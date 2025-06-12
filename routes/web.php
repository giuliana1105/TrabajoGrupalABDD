<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthOracleController;
use Illuminate\Http\Request;


route::get('/', function () {
    return view('oracle_login');
});
Route::get('/oracle/login', [AuthOracleController::class, 'showLoginForm'])->name('oracle.login');
Route::post('/oracle/login', [AuthOracleController::class, 'login']);
Route::post('/oracle/logout', [AuthOracleController::class, 'logout'])->name('oracle.logout');
Route::get('/oracle/dashboard', [AuthOracleController::class, 'dashboard'])->name('oracle.dashboard');

Route::get('/procedimiento-anonimo', function () {
    $ejemplos = [
        'Aumento de salario' => "DECLARE
    v_emp_id   NUMBER := 100;     -- ID del empleado a actualizar
    v_aumento  NUMBER := 500;     -- Monto del aumento
    v_exists   NUMBER;
BEGIN
    -- Verificar si existe el empleado
    SELECT COUNT(*) INTO v_exists
    FROM employees
    WHERE employee_id = v_emp_id;

    IF v_exists > 0 THEN
        UPDATE employees
        SET salary = NVL(salary, 0) + v_aumento
        WHERE employee_id = v_emp_id;

        COMMIT;
        DBMS_OUTPUT.PUT_LINE('Salario actualizado para el empleado ID ' || v_emp_id);
    ELSE
        DBMS_OUTPUT.PUT_LINE('Empleado con ID ' || v_emp_id || ' no existe.');
    END IF;
END;
",
        'Fecha actual' => "BEGIN DBMS_OUTPUT.PUT_LINE(TO_CHAR(SYSDATE)); END;",
        'Aumentar el salario a todos los empleados de un departamento específico' => "DECLARE
    v_dept_id     NUMBER := 50;
    v_porcentaje  NUMBER := 0.05;
BEGIN
    UPDATE employees
    SET salary = salary + (salary * v_porcentaje)
    WHERE department_id = v_dept_id;

    COMMIT;
    DBMS_OUTPUT.PUT_LINE('Salarios actualizados para el departamento ' || v_dept_id);
END;

"
    ];
    return view('procedimiento_anonimo', compact('ejemplos'));
});

Route::post('/procedimiento-anonimo', function (Request $request) {
    $plsql = $request->input('plsql');
    try {
        $salida = \App\Models\UsuarioOracle::ejecutarProcedimientoAnonimo($plsql);
        $mensaje = 'Procedimiento ejecutado correctamente.';
    } catch (\Exception $e) {
        $mensaje = 'Error: ' . $e->getMessage();
        $salida = '';
    }
    $ejemplos = [
                'Aumento de salario' => "DECLARE
    v_emp_id   NUMBER := 100;     -- ID del empleado a actualizar
    v_aumento  NUMBER := 500;     -- Monto del aumento
    v_exists   NUMBER;
BEGIN
    -- Verificar si existe el empleado
    SELECT COUNT(*) INTO v_exists
    FROM employees
    WHERE employee_id = v_emp_id;

    IF v_exists > 0 THEN
        UPDATE employees
        SET salary = NVL(salary, 0) + v_aumento
        WHERE employee_id = v_emp_id;

        COMMIT;
        DBMS_OUTPUT.PUT_LINE('Salario actualizado para el empleado ID ' || v_emp_id);
    ELSE
        DBMS_OUTPUT.PUT_LINE('Empleado con ID ' || v_emp_id || ' no existe.');
    END IF;
END;
",

        'Saludo' => "BEGIN DBMS_OUTPUT.PUT_LINE('Hola mundo!'); END;",
        'Fecha actual' => "BEGIN DBMS_OUTPUT.PUT_LINE(TO_CHAR(SYSDATE)); END;",
        'Aumentar el salario a todos los empleados de un departamento específico' => "DECLARE
    v_dept_id     NUMBER := 50;
    v_porcentaje  NUMBER := 0.05;
BEGIN
    UPDATE employees
    SET salary = salary + (salary * v_porcentaje)
    WHERE department_id = v_dept_id;

    COMMIT;
    DBMS_OUTPUT.PUT_LINE('Salarios actualizados para el departamento ' || v_dept_id);
END;

"
    ];
    return view('procedimiento_anonimo', compact('plsql', 'mensaje', 'salida', 'ejemplos'));
});

