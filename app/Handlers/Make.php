<?php

namespace App\Handlers;

use Illuminate\Support\Str;

class Make
{
    public static function slug($request, $model, $field, $data)
    {
        if (!$data) {
            $d = $model::where($field, $request->name)->get();
            if ($d->count() > 0) {
                $count = $d->count();
                $slug = Str::slug($request->name) . '-' . $count;
            } else {
                $slug = Str::slug($request->name);
            }
            return $slug;
        } else {
            if ($data->name != $request->name) {
                $d = $model::where($field, $request->name)->get();
                if ($d->count() > 0) {
                    $count = $d->count();
                    $slug = Str::slug($request->name) . '-' . $count;
                } else {
                    $slug = Str::slug($request->name);
                }
                return $slug;
            } else
                return $data->slug;
        }
    }
    public static function sku()
    {
        $sku = 'PROD-' . now()->format('His') . '-' . strtoupper(Str::random(5));
        return $sku;
    }
}

