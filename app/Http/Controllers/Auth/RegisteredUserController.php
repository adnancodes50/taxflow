<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        // ✅ VALIDATE
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'confirmed', 'min:6'],
        ]);

        // ✅ CREATE USER
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // ✅ AUTO LOGIN AFTER REGISTER
        Auth::login($user);

        // 🔥 ATTACH UPLOAD TO USER (YOUR LOGIC)
        if (session()->has('pending_upload_id')) {

            $uploadId = session('pending_upload_id');

            $upload = \App\Models\Upload::find($uploadId);

            if ($upload) {
                $upload->update([
                    'user_id' => auth()->id(),
                    'is_guest' => false
                ]);

                \Log::info('✅ Upload linked after register: ' . $uploadId);

                // ✅ UPDATE REPORT ALSO
                \App\Models\Report::where('upload_id', $uploadId)
                    ->update(['user_id' => auth()->id()]);
            }

            session()->forget('pending_upload_id');

            return redirect('/analyze/' . $uploadId);
        }

        return redirect('/dashboard');
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
