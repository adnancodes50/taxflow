<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
// use Illuminate\Support\Facades\Auth;
use App\Providers\RouteServiceProvider;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */


public function store(LoginRequest $request){
    $request->authenticate();

    $request->session()->regenerate();

    // 🔥 ATTACH UPLOAD TO USER AFTER LOGIN
    if (session()->has('pending_upload_id')) {

        $uploadId = session('pending_upload_id');

        $upload = \App\Models\Upload::find($uploadId);

        if ($upload) {
            $upload->update([
                'user_id' => auth()->id(),
                'is_guest' => false
            ]);

            \Log::info('✅ Upload linked after login: ' . $uploadId);

            // ✅ ALSO UPDATE REPORT IF EXISTS
            \App\Models\Report::where('upload_id', $uploadId)
                ->update(['user_id' => auth()->id()]);
        }

        // ✅ CLEAR SESSION
        session()->forget('pending_upload_id');

        // ✅ REDIRECT BACK TO ANALYZE PAGE
        return redirect('/analyze/' . $uploadId);
    }

    // DEFAULT
return redirect()->intended('/dashboard');}

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
