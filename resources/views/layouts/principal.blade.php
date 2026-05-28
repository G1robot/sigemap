<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIG-EMAP</title>

    {{-- <link rel="icon" type="image/png" href="{{ asset('img/logo1.png') }}"> --}}

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="https://unpkg.com/flowbite@2.3.0/dist/flowbite.min.js"></script>

    @livewireStyles
    <script src="//unpkg.com/alpinejs" defer></script>
    <style>
        [x-cloak] { display: none !important; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@latest/dist/leaflet-routing-machine.css" />
    <script src="https://unpkg.com/leaflet-routing-machine@latest/dist/leaflet-routing-machine.js"></script>
</head>

<body class="bg-gray-50 font-sans text-gray-800">
    {{-- ========================================== --}}
    {{-- TOP NAVBAR (DARK MODE CON TEXTO RESALTADO) --}}
    {{-- ========================================== --}}
    <nav class="fixed top-0 z-50 w-full bg-gray-900 shadow-lg border-b border-gray-800 h-16">
        <div class="px-5 h-full flex justify-between items-center">

            <a href="{{ route('home') }}" class="flex items-center gap-3">
                <div class="w-8 h-8 bg-orange-600 rounded-lg flex items-center justify-center shadow-md">
                    {{-- <i class="fa-solid fa-utensils text-white text-sm"></i> --}}
                </div>
                <span class="font-black tracking-widest text-white text-xl">SIG<span class="text-orange-500">EMAP</span></span>
            </a>

            @auth('web')
            <div class="relative">
                <button data-dropdown-toggle="dropdown-user"
                    class="flex items-center gap-3 text-gray-300 font-bold hover:text-white transition px-3 py-1.5 rounded-lg hover:bg-gray-800 border border-transparent hover:border-gray-700">
                    
                    @if(Auth::user()->foto)
                        <img src="{{ asset('storage/' . Auth::user()->foto) }}" class="w-8 h-8 rounded-full object-cover shadow-md border border-gray-600">
                    @else
                        <div class="w-8 h-8 rounded-full bg-orange-500 text-white flex items-center justify-center shadow-md">
                            <i class="fa-solid fa-user text-sm"></i>
                        </div>
                    @endif

                    <div class="text-left hidden md:block">
                        <p class="text-sm leading-tight text-white">{{ Auth::guard('web')->user()->nombre_completo }}</p>
                        <p class="text-[10px] text-orange-400 uppercase tracking-widest">{{ Auth::user()->rol }}</p>
                    </div>
                    <i class="fa-solid fa-chevron-down text-xs text-gray-500 ml-1"></i>
                </button>

                <div id="dropdown-user" class="hidden absolute right-0 mt-2 w-56 bg-white shadow-xl rounded-xl border border-gray-100 overflow-hidden z-50">
                    
                    {{-- <div class="px-2 pt-2 border-b border-gray-100 pb-2">
                        <a href="{{ route('perfil') }}" class="w-full flex items-center gap-3 px-3 py-2 text-gray-700 hover:bg-orange-50 hover:text-orange-600 rounded-lg transition font-bold">
                            <i class="fa-solid fa-id-badge"></i> Mi Perfil
                        </a>
                    </div> --}}

                    <ul class="py-2 text-sm">
                        <li class="px-2">
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full flex items-center gap-3 px-3 py-2 text-gray-700 hover:bg-red-50 hover:text-red-600 rounded-lg transition font-bold">
                                    <i class="fa-solid fa-arrow-right-from-bracket"></i> Cerrar sesión
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
            @endauth
        </div>
    </nav>

    {{-- ========================================== --}}
    {{-- SIDEBAR LATERAL                            --}}
    {{-- ========================================== --}}
    <aside class="fixed top-0 left-0 w-64 h-screen bg-white border-r border-gray-200 pt-16 shadow-sm z-40">
        <div class="h-full overflow-y-auto no-scrollbar px-3 py-6">

            <a href="{{ route('home') }}" class="block text-center mb-8 px-4 mt-2 transition-transform hover:scale-105">
                <img src="/img/LOGO_POTOSI_01.png" alt="Logo SIG-EMAP" class="w-full max-w-[150px] mx-auto drop-shadow-sm">
            </a>

            <ul class="space-y-1 text-sm font-medium">

                <li class="text-xs font-black text-gray-400 px-4 mb-2 tracking-wider">OPERACIÓN DIARIA</li>

                <li>
                    <a href="{{ route('asignaciones') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all {{ request()->routeIs('asignaciones') ? 'bg-orange-50 text-orange-600 font-bold border-l-4 border-orange-500' : 'text-gray-600 hover:bg-gray-50 hover:text-orange-500' }}">
                        <i class="fa-solid fa-list w-5 text-center {{ request()->routeIs('asignaciones') ? 'text-orange-500' : 'text-gray-400' }}"></i>
                        <span>Asignaciones</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('rutas.lista') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all {{ request()->routeIs('rutas.lista') ? 'bg-orange-50 text-orange-600 font-bold border-l-4 border-orange-500' : 'text-gray-600 hover:bg-gray-50 hover:text-orange-500' }}">
                        <i class="fa-solid fa-list w-5 text-center {{ request()->routeIs('rutas.lista') ? 'text-orange-500' : 'text-gray-400' }}"></i>
                        <span>Ruta Lista</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('rutas') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all {{ request()->routeIs('rutas') ? 'bg-orange-50 text-orange-600 font-bold border-l-4 border-orange-500' : 'text-gray-600 hover:bg-gray-50 hover:text-orange-500' }}">
                        <i class="fa-solid fa-route w-5 text-center {{ request()->routeIs('rutas') ? 'text-orange-500' : 'text-gray-400' }}"></i>
                        <span>Rutas</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('rutas.zonas') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all {{ request()->routeIs('rutas.zonas') ? 'bg-orange-50 text-orange-600 font-bold border-l-4 border-orange-500' : 'text-gray-600 hover:bg-gray-50 hover:text-orange-500' }}">
                        <i class="fa-solid fa-map w-5 text-center {{ request()->routeIs('rutas.zonas') ? 'text-orange-500' : 'text-gray-400' }}"></i>
                        <span>Zonas</span>
                    </a>
                </li>

                <li class="text-xs font-black text-gray-400 px-4 mt-6 mb-2 tracking-wider border-t pt-4">GESTIÓN RECURSOS</li>

                <li>
                    <a href="{{ route('usuarios') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all {{ request()->routeIs('usuarios') ? 'bg-orange-50 text-orange-600 font-bold border-l-4 border-orange-500' : 'text-gray-600 hover:bg-gray-50 hover:text-orange-500' }}">
                        <i class="fa-solid fa-users w-5 text-center {{ request()->routeIs('usuarios') ? 'text-orange-500' : 'text-gray-400' }}"></i>
                        <span>Usuarios</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('camiones') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all {{ request()->routeIs('camiones') ? 'bg-orange-50 text-orange-600 font-bold border-l-4 border-orange-500' : 'text-gray-600 hover:bg-gray-50 hover:text-orange-500' }}">
                        <i class="fa-solid fa-truck w-5 text-center {{ request()->routeIs('camiones') ? 'text-orange-500' : 'text-gray-400' }}"></i>
                        <span>Camiones</span>
                    </a>
                </li>



                <li class="text-xs font-black text-gray-400 px-4 mt-8 mb-2 tracking-wider border-t pt-4">REPORTES</li>
                {{-- <li>
                    <a href="{{ route('reportes') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all {{ request()->routeIs('estudiantes') ? 'bg-orange-50 text-orange-600 font-bold border-l-4 border-orange-500' : 'text-gray-600 hover:bg-gray-50 hover:text-orange-500' }}">
                        <i class="fa-solid fa-user-graduate w-5 text-center {{ request()->routeIs('estudiantes') ? 'text-orange-500' : 'text-gray-400' }}"></i>
                        <span>Estudiantes</span>
                    </a>
                </li> --}}
            </ul>
            
            <div class="h-20"></div>
        </div>
    </aside>

    <main class="p-4 sm:ml-64 mt-16 min-h-screen bg-gray-50">
        <div class="bg-white shadow-sm border border-gray-100 rounded-2xl p-6 min-h-[80vh]">
            @yield('content')
        </div>
    </main>

    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('toast', (event) => {
                const data = event[0]; 
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.onmouseenter = Swal.stopTimer;
                        toast.onmouseleave = Swal.resumeTimer;
                    }
                });

                Toast.fire({
                    icon: data.icon,
                    title: data.title
                });
            });
        });
    </script>

</body>
</html>