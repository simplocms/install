<?php

namespace App\Structures\DataTable;

use Illuminate\Http\Request;

class Configuration
{
    /**
     * @var string
     */
    private $tableName;

    /**
     * @var mixed[]
     */
    private $configuration;

    /**
     * DataTable configuration constructor.
     * @param string $tableName
     */
    public function __construct(string $tableName)
    {
        $this->tableName = $tableName;

        $configuration = resolve('cache')->get($this->getStorageKey());
        $this->configuration = $configuration ? unserialize($configuration) : [];
    }


    /**
     * Get value from configuration under specified key.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        return $this->configuration[$key] ?? $default;
    }


    /**
     * Get sort options.
     *
     * @return \App\Structures\DataTable\FilterOptions
     */
    public function getFilterOptions(): FilterOptions
    {
        $sort = $this->get('sort');
        $searchValue = $this->get('search');
        return new FilterOptions(
            $sort['column'] ?? null,
            $sort['direction'] ?? 'asc',
            $searchValue
        );
    }


    /**
     * Get configuration from request.
     * @param \Illuminate\Http\Request $request
     * @return void
     */
    public function getFromRequest(Request $request): void
    {
        // sorting
        $sortColumn = $request->input('sort_column');
        if ($sortColumn) {
            $direction = strtolower($request->input('sort_direction', 'asc'));
            $realDirection = $direction === 'asc' || $direction === 'desc' ? $direction : null;
            if ($realDirection) {
                $this->configuration['sort'] = [
                    'column' => $sortColumn,
                    'direction' => $realDirection,
                ];
            }
        } elseif ($request->ajax() || $request->wantsJson()) {
            unset($this->configuration['sort']);
        }

        // searching
        if ($request->filled('search_text')) {
            $stringValue = trim(strval($request->input('search_text', '')));
            $this->configuration['search'] = strlen($stringValue) ? $stringValue : null;
        } else {
            unset($this->configuration['search']);
        }

        // page
        if ($request->input('page')) {
            $this->configuration['page'] = intval($request->input('page', ''));
        }

        // row limit
        if ($request->input('row_limit')) {
            $this->configuration['row_limit'] = intval($request->input('row_limit', ''));
        }
    }


    /**
     * Save configuration.
     */
    public function save(): void
    {
        resolve('cache')->forever($this->getStorageKey(), serialize($this->configuration));
    }


    /**
     * Get storage key for datatable.
     *
     * @return string
     */
    private function getStorageKey(): string
    {
        $key = "dt_{$this->tableName}_conf";
        return auth()->check() ? $key . auth()->id() : $key;
    }
}
