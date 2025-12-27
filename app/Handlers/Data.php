<?php

namespace App\Handlers;
use Illuminate\Pagination\LengthAwarePaginator;

class Data
{
    public static function paginate($combined, $perPage)
    {
        $page = LengthAwarePaginator::resolveCurrentPage();
        $total = $combined->count();
        $items = $combined->slice(($page - 1) * $perPage, $perPage)->values();

        $datas = new LengthAwarePaginator(
            $items,
            $total,
            $perPage,
            $page,
            ['path' => LengthAwarePaginator::resolveCurrentPath()]
        );

        return $datas;
    }
}