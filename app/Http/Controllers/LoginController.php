<?php

namespace App\Http\Controllers;

use App\Http\Middleware\RedirectIfAuthenticated;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    /**
     * Конструктор
     */
    public function __construct() 
    {
        $this->middleware('guest', ['except' => 'logout']);
    }

    /**
     * Обработка попыток аутентификации.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'name' => ['required'],
            'password' => ['required'],
        ]);        

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            return redirect('/')
                ->with('success', 'Вы вошли в личный кабинет');
        }

        return back()->withErrors([
            'name' => 'Не указано имя пользователя',
            'password' => 'Неправильно указан пароль',
        ]);
    }

    /**
     * Выход пользователя из приложения.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}