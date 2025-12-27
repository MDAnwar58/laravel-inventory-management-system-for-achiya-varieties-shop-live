<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LowStockAlert;
use App\Models\Schedule;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {

        $setting = null;
        $owner = User::where('role', 'owner')->first();
        $settings = Setting::count();
        if (!$settings > 0) {
            Setting::create([
                'user_id' => $owner->id,
            ]);
        } else
            $setting = Setting::with('schedules')
                ->first();

        $breadcrumbs = [
            ['name' => 'Settings', 'route' => null, 'icon' => null]
        ];
        $auth_user_id = auth()->user()->id;
        $lStockAlert = LowStockAlert::where('user_id', $auth_user_id)->first();

        return view('pages.admin.settings.base', compact('breadcrumbs', 'setting', 'lStockAlert'));
    }
    public function auth_off_or_on(Request $request, $id)
    {
        $setting = Setting::findOrFail(intval($id));
        $setting->update([
            'is_auth_system' => $request->is_auth_system ? true : false,
        ]);
        $auth_status = $setting->is_auth_system ? "On" : "Off";
        return response()->json([
            'msg' => 'Authentication System ' . $auth_status . '.',
            'is_auth_system' => $auth_status
        ], 200);
    }
    public function low_stock_alert_off_or_on(Request $request, $id)
    {
        $low_stock_alert = LowStockAlert::findOrFail(intval($id));
        $low_stock_alert->update([
            'is_alert' => $request->is_alert ? true : false,
        ]);

        $stock_alert_status = $low_stock_alert->is_alert ? "On" : "Off";
        return response()->json([
            'msg' => 'Low Stock Alert System ' . $stock_alert_status . '.',
            'auth_status' => $stock_alert_status
        ], 200);
    }
    public function delete_option(Request $request, $id)
    {
        $setting = Setting::findOrFail(intval($id));
        $setting->update([
            'delete_options' => $request->delete_option ? true : false,
        ]);

        $option = $setting->delete_options ? "Lock" : "Unlock";
        return response()->json([
            'msg' => 'Delete Option ' . $option . '.',
            'option' => $option,
        ], 200);
    }
    public function low_stock_alert_msg_store(Request $request, $id)
    {
        $setting = Setting::findOrFail(intval($id));
        $setting->update([
            'low_stock_alert_msg' => $request->low_stock_alert_msg,
        ]);
        return response()->json(['msg' => 'Low Stock Alert Message Updated.'], 200);
    }
    public function alert_times_store(Request $request, $id)
    {
        $owner = User::where('role', 'owner')->first();
        $setting = Setting::findOrFail(intval($id));
        $times = json_decode($request->input('times'), true);


        if (count($times) > 0) {
            $tIds = array_column($times, 'id');
            Schedule::where('setting_id', $setting->id)
                ->whereNotIn('id', $tIds)
                ->delete();

            foreach ($times as $key => $t) {
                if (!empty($t['id'])) {
                    $schedule = Schedule::findOrFail(intval($t['id']));
                    $schedule->update([
                        'time' => $t['time'],
                    ]);
                } else {
                    $schedule = Schedule::create([
                        'user_id' => $owner->id,
                        'setting_id' => $setting->id,
                        'time' => $t['time'],
                    ]);
                }
            }
            return redirect()->back()->with([
                'status' => 'success',
                'msg' => 'Alert Times Updated!'
            ]);
        }
        return redirect()->back()->with([
            'status' => 'warning',
            'msg' => 'Somethins Went Wrong! Please again try.'
        ]);
    }

}
