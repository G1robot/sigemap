<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>EMAP - Iniciar Sesión</title>

    <link rel="icon" type="image/png" href="{{ asset('img/logo_navegador.png') }}">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'emap-blue': '#003a78',
                        'emap-green': '#136e37',
                        'emap-gold': '#c29b40'
                    }
                }
            }
        }
    </script>
    
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://rsms.me/inter/inter.css">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-100 flex justify-center items-center min-h-screen relative overflow-hidden">
    
    <div class="absolute inset-0 z-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-[20%] -left-[10%] w-[50%] h-[50%] rounded-full bg-emap-blue/5 blur-3xl"></div>
        <div class="absolute top-[60%] -right-[10%] w-[40%] h-[60%] rounded-full bg-emap-green/5 blur-3xl"></div>
    </div>

    <div class="relative z-10 bg-white rounded-2xl shadow-2xl overflow-hidden flex max-w-4xl w-full mx-4 border border-gray-100 min-h-[500px]">
        
        <div class="hidden md:flex md:w-5/12 bg-gradient-to-br from-emap-blue to-blue-900 flex-col justify-center items-center p-10 text-center relative overflow-hidden">
            
            <div class="absolute -top-10 -left-10 w-40 h-40 bg-white opacity-5 rounded-full"></div>
            <div class="absolute -bottom-10 -right-10 w-40 h-40 bg-emap-gold opacity-20 rounded-full"></div>

            <i class="fa-solid fa-leaf text-6xl text-emap-green bg-white p-4 rounded-2xl mb-6 shadow-lg"></i>
            
            <h2 class="text-3xl font-black text-white mb-2 tracking-wide">SIG-<span class="text-emap-gold">EMAP</span></h2>
            <div class="w-12 h-1 bg-emap-gold mx-auto mb-4 rounded-full"></div>
            <p class="text-blue-100 text-sm font-medium leading-relaxed">
                Sistema Integrado de Gestión de Residuos, Flota Vehicular y Personal Operativo.
            </p>
        </div>

        <div class="w-full md:w-7/12 flex flex-col justify-between p-8 sm:p-12 relative bg-white">
            
            <div class="w-full max-w-sm mx-auto flex-1 flex flex-col justify-center">
                
                <div class="flex flex-col items-center mb-8 animate-fade-in-down">
                    <img src="{{ asset('img/logo_emap.png') }}" alt="Logo EMAP" class="h-20 mb-4 object-contain drop-shadow-sm">
                    <h2 class="text-center text-xl font-bold text-gray-800 tracking-tight">Acceso al Panel</h2>
                    <p class="text-gray-400 text-xs mt-1 font-bold uppercase">Ingresa tus credenciales</p>
                </div>

                @if($errors->any())
                    <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-3 rounded mb-6 flex items-start gap-3" role="alert">
                        <i class="fa-solid fa-circle-exclamation mt-0.5"></i>
                        <div class="text-sm">
                            <p class="font-bold">Acceso denegado</p>
                            <p class="text-xs">{{ $errors->first('login_error') ?? 'Usuario o contraseña incorrectos.' }}</p>
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" autocomplete="off" class="space-y-5">
                    @csrf
                    
                    <div>
                        <label for="usuario" class="block text-gray-600 text-xs font-bold uppercase tracking-wider mb-1.5">Usuario</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fa-solid fa-user text-gray-400"></i>
                            </div>
                            <input type="text" name="usuario" id="usuario" value="{{ old('usuario') }}" autocomplete="new-password"
                                class="w-full bg-gray-50 border border-gray-300 text-gray-900 rounded-lg pl-10 px-4 py-2.5 focus:outline-none focus:border-emap-blue focus:ring-2 focus:ring-emap-blue transition-all font-bold" 
                                placeholder="Tu nombre de usuario" required>
                        </div>
                    </div>

                    <div>
                        <label for="password" class="block text-gray-600 text-xs font-bold uppercase tracking-wider mb-1.5">Contraseña</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fa-solid fa-lock text-gray-400"></i>
                            </div>
                            <input type="password" name="password" id="password" autocomplete="new-password"
                                class="w-full bg-gray-50 border border-gray-300 text-gray-900 rounded-lg pl-10 px-4 py-2.5 focus:outline-none focus:border-emap-blue focus:ring-2 focus:ring-emap-blue transition-all font-bold" 
                                placeholder="••••••••" required>
                        </div>
                    </div>

                    <button type="submit" class="w-full mt-4 bg-emap-blue text-white font-bold py-3 rounded-lg shadow-md hover:bg-blue-900 hover:shadow-lg transition-all active:scale-[0.98] flex justify-center items-center gap-2">
                        <span>Ingresar al Sistema</span>
                        <i class="fa-solid fa-arrow-right-to-bracket"></i>
                    </button>
                </form>

            </div>

            {{-- <div class="mt-8 text-center border-t border-gray-100 pt-4">
                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest flex items-center justify-center gap-1.5">
                    <i class="fa-solid fa-code text-emap-gold"></i>
                    Desarrollado por Imperial Glory
                </p>
            </div> --}}

        </div>
    </div>
</body>
</html>