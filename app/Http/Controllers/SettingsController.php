<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;

class SettingsController extends Controller
{
    public function index()
    {

        $settings = Setting::first();

        return view('admin.settings', compact('settings'));
    }

    public function save(Request $request)
{
    $request->validate([
        'stripe_public_key' => 'nullable|string',
        'stripe_secret_key' => 'nullable|string',
        'per_page_price' => 'nullable|numeric',
        'ai_prompt' => 'nullable|string',
        'ai_key' => 'nullable|string',
    ]);

    // Always use first row OR create one
    $settings = Setting::first();

    if (!$settings) {
        $settings = new Setting();
    }

    $settings->stripe_public_key = $request->stripe_public_key;
    $settings->stripe_secret_key = $request->stripe_secret_key;
    $settings->per_page_price = $request->per_page_price;
    $settings->ai_prompt = $request->ai_prompt;
    $settings->ai_key = $request->ai_key;

    $settings->save();

    return back()->with('success', 'Settings saved successfully');
}
}
