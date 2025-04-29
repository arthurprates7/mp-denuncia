<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class PasswordResetController extends Controller
{
    public function showForgotForm()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ], [
            'email.required' => 'O campo email é obrigatório',
            'email.email' => 'Digite um email válido',
            'email.exists' => 'Este email não está cadastrado em nosso sistema'
        ]);

        $token = Str::random(64);

        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        DB::table('password_reset_tokens')->insert([
            'email' => $request->email,
            'token' => $token,
            'created_at' => Carbon::now()
        ]);

        Mail::send('emails.forgot-password', ['token' => $token], function ($message) use ($request) {
            $message->to($request->email);
            $message->subject('Recuperação de Senha');
        });

        return back()->with('success', 'Enviamos um link de recuperação de senha para seu email!');
    }

    public function showResetForm($token)
    {
        return view('auth.reset-password', ['token' => $token]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users',
            'password' => 'required|min:8|confirmed',
            'token' => 'required'
        ]);

        $updatePassword = DB::table('password_reset_tokens')
            ->where([
                'email' => $request->email,
                'token' => $request->token
            ])->first();

        if (!$updatePassword) {
            return back()->withInput()->with('error', 'Token inválido!');
        }

        $user = User::where('email', $request->email)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        DB::table('password_reset_tokens')->where(['email' => $request->email])->delete();

        return redirect()->route('login')->with('success', 'Senha alterada com sucesso!');
    }
}
