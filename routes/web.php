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
'Fecha y creación de base' => "DECLARE
    -- Se declara una variable que tomará el tipo de dato de la columna CREATED de la vista V\$DATABASE
    V_FECHA V\$DATABASE.CREATED%TYPE;
BEGIN
    -- Se obtiene la fecha de creación de la base de datos y se guarda en V_FECHA
    SELECT CREATED INTO V_FECHA FROM V\$DATABASE;

    -- Se compara si la diferencia entre la fecha actual y la fecha de creación es mayor a 30 días
    IF (SYSDATE - V_FECHA > 30) THEN
        -- Si la base tiene más de 30 días, se imprime este mensaje
        DBMS_OUTPUT.PUT_LINE('LA BASE DE DATOS FUE CREADA HACE MÁS DE 30 DÍAS.');
    ELSE
        -- Si la base tiene 30 días o menos, se imprime este otro mensaje
        DBMS_OUTPUT.PUT_LINE('LA BASE DE DATOS FUE CREADA HACE MENOS DE 30 DÍAS.');
    END IF;
     DBMS_OUTPUT.PUT_LINE(V_FECHA);

END;",

        'Bucle loop' => "DECLARE
    -- Declaración e inicialización de la variable numérica V_NUM en 0
    V_NUM NUMBER := 0;
BEGIN
    -- Inicio del bucle LOOP
    LOOP
        -- Incrementa el valor de V_NUM en 1
        V_NUM := V_NUM + 1;

        -- Imprime el número actual en la consola
        DBMS_OUTPUT.PUT_LINE('NUMERO: ' || TO_CHAR(V_NUM));

        -- Condición de salida del bucle: si V_NUM es mayor o igual a 10
        EXIT WHEN V_NUM >= 10;
    END LOOP;
END;
",
        'Guardar números' => "

    DECLARE
    v_ultimo NUMBER := 0;
BEGIN
    -- Obtener el último número insertado, o 0 si la tabla está vacía
    SELECT NVL(MAX(numero), 0) INTO v_ultimo FROM numeros_guardados;

    -- Insertar los siguientes 10 números consecutivos
    FOR i IN 1..10 LOOP
        INSERT INTO numeros_guardados (numero)
        VALUES (v_ultimo + i);
    END LOOP;

    COMMIT;
    DBMS_OUTPUT.PUT_LINE('Insertados los números del ' || (v_ultimo + 1) || ' al ' || (v_ultimo + 10));
END;
",

        'Impresión números pares' => "

BEGIN
    FOR V_NUM IN 0..10 by 2 LOOP
        -- Verifica si el último dígito es 0, 2, 4, 6 u 8 (número par)
            DBMS_OUTPUT.PUT_LINE('NUMERO PAR: ' || V_NUM);
    END LOOP;
END;

",

'Fecha actual' => "BEGIN DBMS_OUTPUT.PUT_LINE(TO_CHAR(SYSDATE)); END;",
         'Mostrar información de un empleado' => "DECLARE
    v_emp_id     NUMBER := 100;
    v_nombre     VARCHAR2(100);
    v_salario    NUMBER;
BEGIN
    SELECT first_name || ' ' || last_name, salary
    INTO v_nombre, v_salario
    FROM employees
    WHERE employee_id = v_emp_id;

    DBMS_OUTPUT.PUT_LINE('Empleado: ' || v_nombre);
    DBMS_OUTPUT.PUT_LINE('Salario: ' || v_salario);
EXCEPTION
    WHEN NO_DATA_FOUND THEN
        DBMS_OUTPUT.PUT_LINE('No existe un empleado con ID ' || v_emp_id);
END;

",
 'Total objetos' => "DECLARE
    -- Se declara una variable numérica para almacenar el total de objetos en la base de datos
    V_TOTAL NUMBER := 0;
BEGIN
    -- Se cuenta el número total de objetos en la base de datos consultando la vista DBA_OBJECTS
    SELECT COUNT(*) INTO V_TOTAL FROM DBA_OBJECTS;

    -- Se evalúa el total de objetos utilizando una estructura CASE
     DBMS_OUTPUT.PUT_LINE(V_TOTAL);

    CASE
        -- Si hay menos de 2000 objetos
        WHEN V_TOTAL < 2000 THEN
            DBMS_OUTPUT.PUT_LINE('LA BASE DE DATOS TIENE MENOS DE 2000 OBJETOS.');

        -- Si hay entre 2001 y 3999 objetos
        WHEN V_TOTAL < 4000 AND V_TOTAL > 2000 THEN
            DBMS_OUTPUT.PUT_LINE('LA BASE DE DATOS TIENE ENTRE 2000 Y 4000 OBJETOS.');

        -- Si hay 4000 objetos o más
        ELSE
            DBMS_OUTPUT.PUT_LINE('LA BASE DE DATOS TIENE MÁS DE 4000 OBJETOS.');
    END CASE;
END;",

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
  'Guardar números' => "DECLARE
    v_ultimo NUMBER := 0;
BEGIN
    -- Obtener el último número insertado, o 0 si la tabla está vacía
    SELECT NVL(MAX(numero), 0) INTO v_ultimo FROM numeros_guardados;

    -- Insertar los siguientes 10 números consecutivos
    FOR i IN 1..10 LOOP
        INSERT INTO numeros_guardados (numero)
        VALUES (v_ultimo + i);
    END LOOP;

    COMMIT;
    DBMS_OUTPUT.PUT_LINE('Insertados los números del ' || (v_ultimo + 1) || ' al ' || (v_ultimo + 10));
END;",
                'Fecha y creación de base' => "DECLARE
    -- Se declara una variable que tomará el tipo de dato de la columna CREATED de la vista V\$DATABASE
    V_FECHA V\$DATABASE.CREATED%TYPE;
BEGIN
    -- Se obtiene la fecha de creación de la base de datos y se guarda en V_FECHA
    SELECT CREATED INTO V_FECHA FROM V\$DATABASE;

    -- Se compara si la diferencia entre la fecha actual y la fecha de creación es mayor a 30 días
    IF (SYSDATE - V_FECHA > 30) THEN
        -- Si la base tiene más de 30 días, se imprime este mensaje
        DBMS_OUTPUT.PUT_LINE('LA BASE DE DATOS FUE CREADA HACE MÁS DE 30 DÍAS.');
    ELSE
        -- Si la base tiene 30 días o menos, se imprime este otro mensaje
        DBMS_OUTPUT.PUT_LINE('LA BASE DE DATOS FUE CREADA HACE MENOS DE 30 DÍAS.');
    END IF;
     DBMS_OUTPUT.PUT_LINE(V_FECHA);

END;",
      'Bucle loop' => "DECLARE
    -- Declaración e inicialización de la variable numérica V_NUM en 0
    V_NUM NUMBER := 0;
BEGIN
    -- Inicio del bucle LOOP
    LOOP
        -- Incrementa el valor de V_NUM en 1
        V_NUM := V_NUM + 1;

        -- Imprime el número actual en la consola
        DBMS_OUTPUT.PUT_LINE('NUMERO: ' || TO_CHAR(V_NUM));

        -- Condición de salida del bucle: si V_NUM es mayor o igual a 10
        EXIT WHEN V_NUM >= 10;
    END LOOP;
END;
",

        'Impresión números pares' => "

BEGIN
    FOR V_NUM IN 0..10 by 2 LOOP
        -- Verifica si el último dígito es 0, 2, 4, 6 u 8 (número par)
            DBMS_OUTPUT.PUT_LINE('NUMERO PAR: ' || V_NUM);
    END LOOP;
END;

",


        'Total objetos' => "DECLARE
    -- Se declara una variable numérica para almacenar el total de objetos en la base de datos
    V_TOTAL NUMBER := 0;
BEGIN
    -- Se cuenta el número total de objetos en la base de datos consultando la vista DBA_OBJECTS
    SELECT COUNT(*) INTO V_TOTAL FROM DBA_OBJECTS;

    -- Se evalúa el total de objetos utilizando una estructura CASE
     DBMS_OUTPUT.PUT_LINE(V_TOTAL);

    CASE
        -- Si hay menos de 2000 objetos
        WHEN V_TOTAL < 2000 THEN
            DBMS_OUTPUT.PUT_LINE('LA BASE DE DATOS TIENE MENOS DE 2000 OBJETOS.');

        -- Si hay entre 2001 y 3999 objetos
        WHEN V_TOTAL < 4000 AND V_TOTAL > 2000 THEN
            DBMS_OUTPUT.PUT_LINE('LA BASE DE DATOS TIENE ENTRE 2000 Y 4000 OBJETOS.');

        -- Si hay 4000 objetos o más
        ELSE
            DBMS_OUTPUT.PUT_LINE('LA BASE DE DATOS TIENE MÁS DE 4000 OBJETOS.');
    END CASE;
END;",



        'Saludo' => "BEGIN DBMS_OUTPUT.PUT_LINE('Hola mundo!'); END;",
        'Fecha actual' => "BEGIN DBMS_OUTPUT.PUT_LINE(TO_CHAR(SYSDATE)); END;",
        'Mostrar información de un empleado' => "DECLARE
    v_emp_id     NUMBER := 100;
    v_nombre     VARCHAR2(100);
    v_salario    NUMBER;
BEGIN
    SELECT first_name || ' ' || last_name, salary
    INTO v_nombre, v_salario
    FROM employees
    WHERE employee_id = v_emp_id;

    DBMS_OUTPUT.PUT_LINE('Empleado: ' || v_nombre);
    DBMS_OUTPUT.PUT_LINE('Salario: ' || v_salario);
EXCEPTION
    WHEN NO_DATA_FOUND THEN
        DBMS_OUTPUT.PUT_LINE('No existe un empleado con ID ' || v_emp_id);
END;

",
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

