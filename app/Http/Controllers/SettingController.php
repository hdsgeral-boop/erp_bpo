<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->groupBy('group');
        return view('admin.settings.index', compact('settings'));
    }

    public function updateBulk(Request $request)
    {
        $settingsInput = $request->except(['_token', '_method']);

        foreach ($settingsInput as $key => $value) {
            Setting::where('key', $key)->update(['value' => $value]);
        }
        
        Cache::forget('system_settings'); 

        return redirect()->route('admin.settings.index')->with('success', 'Configurações atualizadas com sucesso.');
    }
}
