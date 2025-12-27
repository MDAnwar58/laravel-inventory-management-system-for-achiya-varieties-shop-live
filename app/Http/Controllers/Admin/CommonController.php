<?php

namespace App\Http\Controllers\Admin;

use App\Handlers\File;
use App\Http\Controllers\Controller;
use App\Mail\SendEmailForProductsLowStock;
use App\Models\LowStockAlert;
use App\Models\Product;
use App\Models\Setting;
use App\Models\TtsFile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;

class CommonController extends Controller
{
    public function low_stocks(Request $request)
    {
        $tts_files = TtsFile::all();
        if ($tts_files->count() > 0) {
            foreach ($tts_files as $key => $tts_file) {
                File::delete($tts_file, 'file_path');
                $tts_file->delete();
            }
        }
        $req_text = $request->input('text', 'Available in stock');
        $lang = $request->input('lang', 'bn'); // leng en, bn

        $low_s_products = Product::where('status', 'active')
            ->whereColumn("stock", "<=", "low_stock_level")
            ->select('id', 'name', 'stock')
            ->orderBy("stock", "desc")
            ->get();
        $low_stock_products = [];
        if ($low_s_products->count() > 0) {
            foreach ($low_s_products as $key => $product) {
                $text = $product->name . " " . $product->stock . " " . $req_text;
                $fileMp3 = File::text_to_mp3('tts/', $text, $lang); // leng en, bn
                $tts = TtsFile::create([
                    'text' => $fileMp3['text'],
                    'lang' => $fileMp3['lang'],
                    'file_path' => $fileMp3['file_path'],
                ]);

                $low_stock_products[] = [
                    'id' => $product->id,
                    'name' => $product->name,
                    'stock' => $product->stock,
                    'tts_id' => $tts->id,
                    'voice_alert' => $fileMp3['file_path'],
                ];
            }
        }
        $auth_user_id = auth()->user()->id;
        $low_stock_alert = LowStockAlert::where('user_id', $auth_user_id)->first();
        return $low_stock_alert->is_alert ? $low_stock_products : [];
    }
    public function send_email_for_low_stocks()
    {
        $products = Product::where(function ($q) {
            $q->where('stock_w_type', 'none')
                ->whereColumn('stock', '<=', 'low_stock_level');
        })->orWhere(function ($q) {
            $q->where('stock_w_type', '!=', 'none')
                ->whereColumn('stock_w', '<=', 'low_stock_level');
        })->with(['item_type', 'brand', 'category', 'sub_category'])->get();

        $users = User::whereIn('role', ['admin', 'super_admin', 'owner'])->get();

        if ($users->count() > 0) {
            foreach ($users as $key => $user) {
                Mail::to($user->email)->send(new SendEmailForProductsLowStock($user, $products));
            }
            return response()->json('Email sent successfully');
        } else
            return response()->json(['status' => 'error', 'msg' => 'No user found']);
    }
    public function low_stocks_products(Request $request)
    {
        Cache::forget('low_stock_alert');
        Cache::forget('low_stock_products_list');
        // Cache::forget('low_stock_products');
        return redirect()->route('admin.products');
    }
}
