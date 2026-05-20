<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function edit()
    {
        $settings = AppSetting::pluck('value', 'key');
        $requiredKeys = ['resort_name', 'email', 'phone', 'address', 'business_hours'];
        $completedRequired = collect($requiredKeys)->filter(fn ($key) => filled($settings[$key] ?? null))->count();
        $summary = [
            'total' => AppSetting::count(),
            'completed_required' => $completedRequired,
            'required_count' => count($requiredKeys),
            'completion' => (int) round(($completedRequired / count($requiredKeys)) * 100),
            'last_updated' => AppSetting::latest('updated_at')->value('updated_at'),
        ];

        return view('admin.settings.edit', compact('settings', 'summary'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'resort_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email'],
            'phone' => ['required', 'string', 'max:50'],
            'address' => ['required', 'string'],
            'business_hours' => ['required', 'string'],
            'booking_rules' => ['nullable', 'string'],
            'notification_settings' => ['nullable', 'string'],
        ]);

        foreach ($data as $key => $value) {
            AppSetting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        return back()->with('success', 'Settings updated.');
    }
}
