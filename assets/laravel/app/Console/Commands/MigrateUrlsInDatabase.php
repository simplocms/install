<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MigrateUrlsInDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:urls {url} {replacement} {--I|info}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate url addresses in database';

    /**
     * Excluded tables to not search in.
     *
     * @var string[]
     */
    private $excludeTables = [
        'articles_categories', 'article_tags', 'installed_modules', 'jobs',
        'languages', 'media_directories', 'media_directories_closures', 'media_files', 'menu',
        'migrations', 'module_entities', 'password_resets', 'permissions', 'permission_role',
        'roles', 'role_user', 'tags', 'universal_module_entities',
        'universal_module_entity_item', 'urls', 'users', 'widgets'
    ];

    /**
     * Searched URL.
     *
     * @var string
     */
    private $url;

    /**
     * Replacement URL.
     *
     * @var string
     */
    private $replacement;

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->url = $this->argument('url');
        $this->replacement = $this->argument('replacement');

        $excludeMap = array_flip($this->excludeTables);
        $tables = DB::connection()->getDoctrineSchemaManager()->listTableNames();
        foreach ($tables as $tableName) {
            if (isset($excludeMap[$tableName])) {
                continue;
            }

            $this->migrateTable($tableName);
        }

        $this->info('Done.');
    }


    /**
     * Migrate single table.
     *
     * @param string $tableName
     */
    private function migrateTable(string $tableName)
    {
        $isInformative = $this->option('info') ?? false;
        $this->info("Migrating table `{$tableName}`:");

        $data = DB::table($tableName)->get();

        foreach ($data as $rowIndex => $row) {
            $updateValues = [];
            foreach ($row as $column => $value) {
                $result = $this->searchMixedValueAndReplaceMatches($value);

                if ($result === $value) {
                    continue;
                }

                if ($isInformative) {
                    $rowId = isset($row->id) ? "with ID {$row->id}" : $rowIndex;
                    $this->comment(
                        "[INFO] Found on row $rowId in column `{$column}`. Result will be: \"{$result}\"."
                    );
                } else {
                    $updateValues[$column] = $result;
                }
            }

            if ($updateValues) {
                $this->updateTableRow($tableName, $row, $updateValues);
            }
        }
    }


    /**
     * Update table row.
     *
     * @param string $table
     * @param object $row
     * @param string[] $values
     */
    private function updateTableRow(string $table, $row, array $values)
    {
        $query = DB::table($table);

        $primaryKeys = DB::select("SHOW KEYS FROM `$table` WHERE Key_name = 'PRIMARY'");
        $keys = [];
        foreach ($primaryKeys as $primaryKey) {
            $keys[] = "{$primaryKey->Column_name}: {$row->{$primaryKey->Column_name}}";
            $query->where($primaryKey->Column_name, $row->{$primaryKey->Column_name});
        }

        $query->update($values);
        $this->comment("Updated row with key " . join(', ', $keys) . ".");
    }


    /**
     * Search url in text.
     *
     * @param string $text
     * @return string
     */
    private function searchAndReplaceText(string $text): string
    {
        if (strlen($text) < strlen($this->url)) {
            return $text;
        }

        $jsonData = $this->decodeJson($text);
        if ($jsonData !== null) {
            return json_encode($this->searchMixedValueAndReplaceMatches($jsonData));
        }

        return str_replace($this->url, $this->replacement, $text);
    }


    /**
     * @param mixed[] $data
     * @return mixed[]
     */
    private function searchArrayAndReplaceMatches(array $data): array
    {
        return array_map(function ($record) {
            return $this->searchMixedValueAndReplaceMatches($record);
        }, $data);
    }


    /**
     * @param object $data
     * @return object
     */
    private function searchObjectAndReplaceMatches($data)
    {
        $result = new \stdClass();

        foreach ($data as $key => $value) {
            $result->{$key} = $this->searchMixedValueAndReplaceMatches($value);
        }

        return $result;
    }


    /**
     * @param mixed $value
     * @return mixed
     */
    private function searchMixedValueAndReplaceMatches($value)
    {
        switch (true) {
            case is_object($value):
                return $this->searchObjectAndReplaceMatches($value);
            case is_array($value):
                return $this->searchArrayAndReplaceMatches($value);
            case is_string($value):
                return $this->searchAndReplaceText($value);
        }

        return $value;
    }


    /**
     * Decode string JSON object.
     *
     * @param string $text
     * @return mixed|null
     */
    private function decodeJson(string $text)
    {
        if (!Str::startsWith($text, '{') || !Str::endsWith($text, '}')) {
            return null;
        }

        $result = json_decode($text);
        return json_last_error() === JSON_ERROR_NONE ? $result : null;
    }
}
