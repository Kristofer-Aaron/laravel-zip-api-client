<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class ApiAuthController extends Controller
{
    public function showLoginForm()
    {
        return view('api-login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $apiBase = config('services.api.base_uri');

        $response = Http::post($apiBase . '/login', [
            'email' => $request->email,
            'password' => $request->password,
        ]);

        if ($response->successful()) {
            $data = $response->json();
            Session::put('api_token', $data['token']);
            return redirect()->route('dashboard')->with('message', 'Logged in to API successfully');
        } else {
            return back()->withErrors(['error' => 'Failed to login to API']);
        }
    }

    public function logout()
    {
        Session::forget('api_token');
        return redirect('/')->with('message', 'Logged out from API successfully');
    }

    public function showRegisterForm()
    {
        return view('register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $apiBase = config('services.api.base_uri');

        $response = Http::post($apiBase . '/register', [
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
            'password_confirmation' => $request->password_confirmation,
        ]);

        if ($response->successful()) {
            $data = $response->json();
            Session::put('api_token', $data['token']);
            return redirect()->route('dashboard')->with('message', 'Registered and logged in to API successfully');
        } else {
            $errors = $response->json();
            if (isset($errors['errors'])) {
                return back()->withErrors($errors['errors']);
            }
            return back()->withErrors(['error' => 'Failed to register']);
        }
    }
}