<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AdminAuthService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(private AdminAuthService $auth) {}

    public function showLogin(Request $request)
    {
        if ($request->session()->get(AdminAuthService::SESSION_KEY)) {
            return redirect()->route('admin.orders');
        }

        return view('admin.login');
    }

    public function login(Request $request)
    {
        $request->validate(['password' => ['required', 'string']]);

        if (! $this->auth->verify($request->input('password'))) {
            return back()->withErrors(['password' => 'ভুল পাসওয়ার্ড']);
        }

        $request->session()->regenerate();
        $request->session()->put(AdminAuthService::SESSION_KEY, true);

        return redirect()->route('admin.orders');
    }

    public function logout(Request $request)
    {
        $request->session()->forget(AdminAuthService::SESSION_KEY);
        $request->session()->regenerate();

        return redirect()->route('admin.login');
    }

    public function changePassword(Request $request)
    {
        $data = $request->validate([
            'current' => ['required', 'string'],
            'next'    => ['required', 'string', 'min:4'],
        ], [
            'next.min' => 'নতুন পাসওয়ার্ড অন্তত ৪ অক্ষরের হতে হবে',
        ]);

        if (! $this->auth->verify($data['current'])) {
            return back()->withErrors(['current' => 'বর্তমান পাসওয়ার্ড ভুল']);
        }

        $this->auth->setPassword($data['next']);

        return back()->with('status', 'পাসওয়ার্ড বদলে গেছে');
    }
}
