<!-- resources/views/oracle_dashboard.blade.php -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Oracle</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800 font-sans">

    <div class="max-w-6xl mx-auto py-10 px-4">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold">Bienvenido, {{ $username }}</h2>
            <form method="POST" action="{{ route('oracle.logout') }}">
                @csrf
                <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded shadow">
                    Cerrar sesión
                </button>
            </form>
        </div>

        {{-- Tablas y Privilegios --}}
         <div class="overflow-x-auto">
            <table class="w-full border border-gray-300 rounded shadow bg-white">
                <thead class="bg-gray-200 text-left">
                    <tr>
                        <th class="px-4 py-2">Objeto</th>
                        <th class="px-4 py-2">Tipo</th>
                        <th class="px-4 py-2">Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($metadatos as $objeto)
                        <tr class="border-t">
                            <td class="px-4 py-2">{{ $objeto->object_name }}</td>
                            <td class="px-4 py-2">{{ $objeto->object_type }}</td>
                            <td class="px-4 py-2">{{ $objeto->status }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-4 py-2 text-center text-gray-500">No se encontraron objetos.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        </div>



    </div>
</body>
</html>
