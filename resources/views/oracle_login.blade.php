<!-- filepath: resources/views/oracle_login.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <title>Login Oracle</title>
</head>
<body>
    <h2>Login Oracle</h2>
    @if($errors->any())
        <div style="color:red;">{{ $errors->first('login') }}</div>
    @endif
    <form method="POST" action="{{ route('oracle.login') }}">
        @csrf
        <label>Usuario:</label>
        <input type="text" name="username" required><br>
        <label>Contraseña:</label>
        <input type="password" name="password" required><br>
        <button type="submit">Ingresar</button>
    </form>
</body>
</html>