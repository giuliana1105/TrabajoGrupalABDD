<!-- filepath: resources/views/oracle_dashboard.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard Oracle</title>
</head>
<body>
    <h2>Bienvenido, {{ $username }}</h2>
    <form method="POST" action="{{ route('oracle.logout') }}">
        @csrf
        <button type="submit">Cerrar sesión</button>
    </form>
    <h3>Tablas y privilegios</h3>
    <table border="1">
        <tr>
            <th>Tabla</th>
            <th>Privilegio</th>
        </tr>
        @forelse($tablas as $tabla)
            <tr>
                <td>{{ $tabla->table_name }}</td>
                <td>{{ $tabla->privilege }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="2">No hay tablas o privilegios asignados.</td>
            </tr>
        @endforelse
    </table>
    
    <h3>Privilegios DBA (dba_sys_privs)</h3>
    <table border="1">
        <tr>
            <th>Privilegio</th>
        </tr>
        @forelse($dbaSysPrivs as $priv)
            <tr>
                <td>{{ $priv->privilege }}</td>
            </tr>
        @empty
            <tr>
                <td>No tiene privilegios DBA asignados.</td>
            </tr>
        @endforelse
    </table>

    <h3>Roles DBA (dba_role_privs)</h3>
    <table border="1">
        <tr>
            <th>Rol</th>
        </tr>
        @forelse($dbaRolePrivs as $rol)
            <tr>
                <td>{{ $rol->granted_role }}</td>
            </tr>
        @empty
            <tr>
                <td>No tiene roles DBA asignados.</td>
            </tr>
        @endforelse
    </table>
</body>
</html>