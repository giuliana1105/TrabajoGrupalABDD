<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\UsuarioOracle;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

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
            // Cambia aquí la redirección:
            return redirect('/procedimiento-anonimo');
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

    public static function ejecutarProcedimientoAnonimo($plsql)
    {
        $conn = DB::connection('oracle')->getPdo();

        // Habilita DBMS_OUTPUT
        $conn->exec("BEGIN DBMS_OUTPUT.ENABLE(NULL); END;");

        // Ejecuta el bloque anónimo
        $stmt = $conn->prepare($plsql);
        $stmt->execute();

        // Recupera la salida de DBMS_OUTPUT
        $lines = [];
        do {
            $line = null;
            $status = null;
            $stmt = $conn->prepare("
                DECLARE
                    line VARCHAR2(32767);
                    status INTEGER;
                BEGIN
                    DBMS_OUTPUT.GET_LINE(line, status);
                    :line := line;
                    :status := status;
                END;
            ");
            $stmt->bindParam(':line', $line, \PDO::PARAM_STR | \PDO::PARAM_INPUT_OUTPUT, 32767);
            $stmt->bindParam(':status', $status, \PDO::PARAM_INT | \PDO::PARAM_INPUT_OUTPUT, 1);
            $stmt->execute();
            if ($status === 0) {
                $lines[] = $line;
            }
        } while ($status === 0);

        // Deshabilita DBMS_OUTPUT
        $conn->exec("BEGIN DBMS_OUTPUT.DISABLE; END;");

        return implode("\n", $lines);
    }
}