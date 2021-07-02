<?php

namespace App\Helpers;

/**
 * Gera a paginação dos itens de um array ou collection.
 *
 * @param array|Collection      $items
 * @param int   $perPage
 * @param int  $page
 * @param array $options
 *
 * @return LengthAwarePaginator
 */

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;

class Paginate {

    public static function paginate($items, $perPage = 15, $page = null, $options = []): LengthAwarePaginator
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);

        $items = $items instanceof Collection ? $items : Collection::make($items);

        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }
}


