<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = [
            'registration_enabled' => setting('registration_enabled', 'true'),
        ];

        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        Setting::set('registration_enabled', $request->has('registration_enabled') ? 'true' : 'false');

        return back()->with('success', '设置已保存');
    }
}