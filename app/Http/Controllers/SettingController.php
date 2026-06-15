<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $appName = Setting::getAppName();
        $appLogo = Setting::getAppLogo();

        return view('setting.index', compact('appName', 'appLogo'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'app_name' => 'required|string|max:255',
            'app_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:512', // 512KB = ~500KB
        ]);

        // Update app name
        Setting::setValue('app_name', $request->app_name);

        // Update logo if uploaded
        if ($request->hasFile('app_logo')) {
            $file = $request->file('app_logo');
            $base64 = base64_encode(file_get_contents($file->getRealPath()));
            Setting::setValue('app_logo', $base64);
        }

        // Remove logo
        if ($request->has('remove_logo') && $request->remove_logo == '1') {
            Setting::setValue('app_logo', null);
        }

        return redirect()->route('setting.index')
            ->with('success', 'Pengaturan website berhasil disimpan.');
    }
}