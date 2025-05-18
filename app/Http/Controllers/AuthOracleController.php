<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\UsuarioOracle;

class AuthOracleController extends Controller
{
    public function showLoginForm()
    {
        return view('oracle_login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);
        $username = $request->input('username');
        $password = $request->input('password');

        if (UsuarioOracle::validarCredenciales($username, $password)) {
            Session::put('oracle_user', $username);
            Session::put('oracle_pass', $password);
            return redirect()->route('oracle.dashboard');
        } else {
            return back()->withErrors(['login' => 'Credenciales inválidas']);
        }
    }

    public function logout()
    {
        Session::forget('oracle_user');
        Session::forget('oracle_pass');
        return redirect()->route('oracle.login');
    }

    public function dashboard()
    {
        $username = Session::get('oracle_user');
        if (!$username) {
            return redirect()->route('oracle.login');
        }
        $tablas = \App\Models\UsuarioOracle::obtenerTablasYPrivilegios($username);
        $privilegiosSistema = \App\Models\UsuarioOracle::obtenerPrivilegiosSistema($username);
        $roles = \App\Models\UsuarioOracle::obtenerRoles($username);
        $dbaSysPrivs = \App\Models\UsuarioOracle::obtenerDBASysPrivs($username);
        $dbaRolePrivs = \App\Models\UsuarioOracle::obtenerDBARolePrivs($username);

        return view('oracle_dashboard', compact(
            'username', 'tablas', 'privilegiosSistema', 'roles', 'dbaSysPrivs', 'dbaRolePrivs'
        ));
    }
}