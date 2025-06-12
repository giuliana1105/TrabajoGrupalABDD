<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Procedimientos Anónimos Oracle</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen font-sans">
    <div class="w-full max-w-xl bg-white p-8 rounded shadow-md">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-2xl font-bold text-gray-800">Ejecutar Procedimiento Anónimo PL/SQL</h2>
            <form method="POST" action="{{ route('oracle.logout') }}">
                @csrf
                <button type="submit" class="ml-4 bg-red-600 hover:bg-red-700 text-white font-semibold py-1 px-3 rounded shadow">
                    Cerrar sesión
                </button>
            </form>
        </div>
        <form method="POST" action="{{ url('/procedimiento-anonimo') }}">
            @csrf
            <label class="block mb-2 font-semibold">Selecciona un procedimiento anónimo:</label>
            <select id="ejemplo-select" class="w-full border rounded p-2 mb-4" onchange="document.getElementById('plsql').value=this.value">
                <option value="">-- Selecciona un procedimiento anónimo --</option>
                @foreach($ejemplos as $nombre => $codigo)
                    <option value="{{ $codigo }}">{{ $nombre }}</option>
                @endforeach
            </select>
            <textarea id="plsql" name="plsql" rows="8" class="w-full border rounded p-2 mb-4" placeholder="BEGIN ... END;">{{ $plsql ?? '' }}</textarea>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded shadow w-full">
                Ejecutar
            </button>
        </form>
        @if(isset($mensaje))
            <div class="mt-4 text-center {{ str_contains($mensaje, 'Error') ? 'text-red-600' : 'text-green-600' }}">
                {{ $mensaje }}
            </div>
        @endif
        @if(isset($salida) && $salida)
            <div class="mt-4 p-4 bg-gray-100 rounded text-gray-800 font-mono whitespace-pre-line">
                <strong>Salida:</strong>
                <br>
                {{ $salida }}
            </div>
        @endif
        <!-- <div class="mt-6 text-sm text-gray-600">
            <strong>Ejemplo:</strong>
            <pre>
BEGIN
    DBMS_OUTPUT.PUT_LINE('Hola desde PL/SQL!');
END;
            </pre>
        </div> -->
    </div>
</body>
</html>