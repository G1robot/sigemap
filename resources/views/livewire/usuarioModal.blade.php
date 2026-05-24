<div class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50 backdrop-blur-sm z-50 animate-fade-in-down">
    <div class="max-w-4xl w-full mx-4 bg-white rounded-2xl shadow-2xl overflow-hidden">
        
        <div class="bg-gray-50 px-6 py-4 border-b border-gray-100 flex justify-between items-center">
            <h2 class="text-lg font-bold text-gray-800">
                <i class="fa-solid fa-user-shield mr-2 text-orange-500"></i>
                {{ $usuario_id ? 'Editar Personal' : 'Registrar Nuevo Personal' }}
            </h2>
            <button wire:click="closeModal" class="text-gray-400 hover:text-red-500 transition text-xl">&times;</button>
        </div>

        <form wire:submit.prevent="enviarClick" class="p-6" autocomplete="off">
            
            <div class="space-y-4 mb-6">
                <h3 class="text-xs font-black text-gray-400 tracking-wider border-b pb-2">DATOS GENERALES</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="lg:col-span-2">
                        <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Nombre Completo *</label>
                        <input wire:model="nombre_completo" type="text" autocomplete="off"
                            class="w-full border border-gray-300 rounded-lg p-2.5 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-shadow">
                        @error('nombre_completo') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase mb-1">C.I. *</label>
                        <input wire:model="ci" type="text" autocomplete="off"
                            class="w-full border border-gray-300 rounded-lg p-2.5 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-shadow">
                        @error('ci') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Teléfono *</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fa-solid fa-phone text-gray-400 text-xs"></i>
                            </div>
                            <input wire:model="telefono" type="text" autocomplete="off"
                                class="w-full pl-9 border border-gray-300 rounded-lg p-2.5 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-shadow">
                        </div>
                        @error('telefono') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="lg:col-span-2">
                        <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Cargo / Puesto *</label>
                        <select wire:model="cargo_base" 
                            class="w-full border border-gray-300 rounded-lg p-2.5 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-shadow bg-white">
                            <option value="">Seleccione un cargo...</option>
                            <option value="Chofer">Chofer de Compactador</option>
                            <option value="Ayudante">Ayudante (Recolector)</option>
                            <option value="Supervisor">Supervisor de Operaciones</option>
                            <option value="Administrador">Administrador General</option>
                        </select>
                        @error('cargo_base') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 -mx-6 px-6 py-4 border-y border-gray-100 mb-6">
                <div class="flex items-center">
                    <input type="checkbox" id="es_usuario_sistema" wire:model.live="es_usuario_sistema" 
                        class="h-5 w-5 text-orange-600 focus:ring-orange-500 border-gray-300 rounded cursor-pointer transition-colors">
                    <label for="es_usuario_sistema" class="ml-3 block text-sm text-gray-900 font-bold cursor-pointer">
                        Habilitar acceso al sistema SIG-EMAP (Asignar Usuario y Contraseña)
                    </label>
                </div>
            </div>

            @if($es_usuario_sistema)
                <div class="space-y-4 animate-fade-in-down">
                    <h3 class="text-xs font-black text-gray-400 tracking-wider border-b pb-2">CREDENCIALES DE ACCESO</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Nombre de Usuario *</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fa-solid fa-at text-gray-400 text-xs"></i>
                                </div>
                                <input wire:model="usuario" type="text" autocomplete="off" placeholder="Ej: jperez"
                                    class="w-full pl-9 border border-gray-300 rounded-lg p-2.5 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-shadow">
                            </div>
                            @error('usuario') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Nivel de Acceso (Rol) *</label>
                            <select wire:model="rol" 
                                class="w-full border border-gray-300 rounded-lg p-2.5 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-shadow bg-white">
                                <option value="">Seleccione un rol...</option>
                                <option value="Operario">Operario</option>
                                <option value="Supervisor">Supervisor</option>
                                <option value="Administrador">Administrador</option>
                            </select>
                            @error('rol') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-600 uppercase mb-1">
                                Contraseña {{ $usuario_id ? '(Dejar en blanco para no cambiar)' : '*' }}
                            </label>
                            <input wire:model="contrasena" type="password" autocomplete="new-password"
                                class="w-full border border-gray-300 rounded-lg p-2.5 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-shadow">
                            @error('contrasena') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Confirmar Contraseña</label>
                            <input wire:model="contrasena1" type="password" autocomplete="new-password"
                                class="w-full border border-gray-300 rounded-lg p-2.5 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-shadow">
                            @error('contrasena1') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
            @endif

            <div class="mt-8 flex justify-end gap-3 border-t border-gray-100 pt-5">
                <button type="button" wire:click="closeModal" class="px-5 py-2.5 text-sm font-bold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                    Cancelar
                </button>
                
                <button type="submit" 
                    wire:loading.attr="disabled" 
                    wire:target="enviarClick"
                    class="px-5 py-2.5 text-sm font-bold text-white bg-orange-500 hover:bg-orange-600 rounded-lg transition-colors shadow-sm flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                    
                    <span wire:loading.remove wire:target="enviarClick" class="flex items-center gap-2">
                        <i class="fa-solid fa-check"></i> {{ $usuario_id ? 'Guardar Cambios' : 'Registrar' }}
                    </span>

                    <span wire:loading wire:target="enviarClick" class="flex items-center gap-2">
                        <i class="fa-solid fa-spinner fa-spin"></i> Procesando...
                    </span>
                </button>
            </div>
            
        </form>
    </div>
</div>