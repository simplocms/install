<?php

namespace App\Structures;

use App\Services\ResponseManager\Link;
use App\Structures\Enums\SingletonEnum;
use Illuminate\Pagination\LengthAwarePaginator;
use \Illuminate\Contracts\Pagination\LengthAwarePaginator as LengthAwarePaginatorContract;
use Illuminate\Support\Collection;

/**
 * Class Paginator - for automatically setting headers when paginating.
 * @package App\Structures
 * @author Patrik Václavek
 * @copyright SIMPLO, s.r.o.
 */
class Paginator extends LengthAwarePaginator implements LengthAwarePaginatorContract
{
    /**
     * @inheritdoc
     */
    public function __construct($items, int $total, int $perPage, ?int $currentPage = null, array $options = [])
    {
        parent::__construct($items, $total, $perPage, $currentPage, $options);

        $pageInQuery = request()->has($this->getPageName()) ? intval(request()->get($this->getPageName())) : null;

        $totalPages = ceil($total / $perPage);

        if ($pageInQuery !== null && $pageInQuery !== 1 && $pageInQuery > $totalPages) {
            abort(404);
        }

        if ($this->currentPage > 1 || $pageInQuery !== null) {
            SingletonEnum::responseManager()->noIndex();
        }

        // Set headers
        if ($this->currentPage > 1) {
            SingletonEnum::responseManager()->addLink(new Link($this->previousPageUrl(), 'prev'));
        }

        if ($this->lastPage > $this->currentPage) {
            SingletonEnum::responseManager()->addLink(new Link($this->nextPageUrl(), 'next'));
        }
    }


    /**
     * Make paginator for given items.
     *
     * @param array|\Illuminate\Support\Collection $items
     * @param int $perPage
     * @param int|string|null $currentPage
     * @param array $options
     * @return \App\Structures\Paginator
     */
    public static function make($items, int $perPage, $currentPage = null, array $options = [])
    {
        $pageName = null;
        if (is_string($currentPage) && !is_numeric($currentPage)) {
            $options['pageName'] = $currentPage;
            $currentPage = self::resolveCurrentPage($currentPage);
        }

        $items = $items instanceof Collection ? $items : Collection::make($items);

        $paginator = new self(
            $items->forPage($currentPage, $perPage), $items->count(), $perPage, $currentPage, $options
        );

        return $paginator;
    }
}
