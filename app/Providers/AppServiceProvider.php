<?php

namespace App\Providers;

use App\Models\Product;
use App\Models\Setting;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cache;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Make sure database table exists (prevents migration errors)
        if (\Schema::hasTable('products') && Cache::get('low_stock_alert')) {
            // Get all low-stock products
            $products = Product::whereColumn('stock', '<=', 'low_stock_level')->get();
            $current_ids = $products->pluck('id')->toArray();
            $l_s_p_count = $products->count();

            // Cache the actual products permanently if needed
            if ($l_s_p_count > 0) {
                Cache::put('low_stock_products_list', $products);
            }

            // Get previous cache or set default
            $cache_low_stock = Cache::get('low_stock_products', [
                'products_ids' => [],
                'products_count' => 0,
                'date_and_time' => now(),
            ]);

            // Check if products have changed
            $dataChanged = $current_ids !== ($cache_low_stock['products_ids'] ?? []);

            // Keep old date if not changed, update if changed
            $dateTime = $dataChanged ? now() : $cache_low_stock['date_and_time'];

            // Update the low stock cache
            Cache::put('low_stock_products', [
                'products_count' => $l_s_p_count,
                'products_ids' => $current_ids,
                'date_and_time' => $dateTime,
            ]);

            // Share with all views
            $low_stock = Cache::get('low_stock_products');
        } else {
            Cache::put('low_stock_products', null);
            $low_stock = Cache::get('low_stock_products');
        }
        $settings = Setting::with('schedules')->first();
        View::share([
            'low_stock' => $low_stock,
            'settings' => $settings,
        ]);
    }
}
