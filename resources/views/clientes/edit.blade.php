<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Editar Cliente') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">

                    <h2 class="mb-6 text-lg font-bold text-gray-700">
                        {{ __('Actualizar Información del Cliente') }}
                    </h2>

                    <form method="POST" action="{{ route('clientes.update', $cliente) }}">
                        @csrf
                        @method('PUT')

                        <!-- Nombre -->
                        <div class="mb-4">
                            <x-input-label for="nombre" :value="__('Nombre')" />
                            <x-text-input id="nombre" name="nombre" type="text" class="block w-full mt-1"
                                :value="old('nombre', $cliente->nombre)" required autofocus />
                            <x-input-error :messages="$errors->get('nombre')" class="mt-2" />
                        </div>

                        <!-- Teléfono -->
                        <div class="mb-4">
                            <x-input-label for="telefono" :value="__('Teléfono')" />
                            <x-text-input id="telefono" name="telefono" type="text" class="block w-full mt-1"
                                :value="old('telefono', $cliente->telefono)" />
                            <x-input-error :messages="$errors->get('telefono')" class="mt-2" />
                        </div>

                        <!-- Calle -->
                        <div class="mb-4">
                            <x-input-label for="calle" :value="__('Calle')" />
                            <x-text-input id="calle" name="calle" type="text" class="block w-full mt-1"
                                :value="old('calle', $cliente->calle)" />
                            <x-input-error :messages="$errors->get('calle')" class="mt-2" />
                        </div>

                        <!-- Colonia -->
                        <div class="mb-4">
                            <x-input-label for="colonia" :value="__('Colonia')" />
                            <x-text-input id="colonia" name="colonia" type="text" class="block w-full mt-1"
                                :value="old('colonia', $cliente->colonia)" />
                            <x-input-error :messages="$errors->get('colonia')" class="mt-2" />
                        </div>

                        <!-- Código Postal -->
                        <div class="mb-4">
                            <x-input-label for="codigo_postal" :value="__('Código Postal')" />
                            <x-text-input id="codigo_postal" name="codigo_postal" type="text" class="block w-full mt-1"
                                :value="old('codigo_postal', $cliente->codigo_postal)" />
                            <x-input-error :messages="$errors->get('codigo_postal')" class="mt-2" />
                        </div>

                        <!-- Municipio -->
                        <div class="mb-4">
                            <x-input-label for="municipio" :value="__('Municipio')" />
                            <x-text-input id="municipio" name="municipio" type="text" class="block w-full mt-1"
                                :value="old('municipio', $cliente->municipio)" />
                            <x-input-error :messages="$errors->get('municipio')" class="mt-2" />
                        </div>

                        <!-- Estado -->
                        <div class="mb-4">
                            <x-input-label for="estado" :value="__('Estado')" />
                            <x-text-input id="estado" name="estado" type="text" class="block w-full mt-1"
                                :value="old('estado', $cliente->estado)" />
                            <x-input-error :messages="$errors->get('estado')" class="mt-2" />
                        </div>

                        <!-- Asignado a -->
                        <div class="mb-4">
                            <x-input-label for="asignado_a" :value="__('Asignado a Vendedor')" />
                            <select name="asignado_a" id="asignado_a"
                                class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                <option value="">-- Seleccione --</option>
                                @foreach ($vendedores as $vendedor)
                                    <option value="{{ $vendedor->id }}"
                                        {{ old('asignado_a', $cliente->asignado_a) == $vendedor->id ? 'selected' : '' }}>
                                        {{ $vendedor->name }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('asignado_a')" class="mt-2" />
                        </div>
                        <!-- Nivel de Precio -->
                        <div class="mb-4">
                            <x-input-label for="nivel_precio_id" value="Nivel de Precio" />
                            <select name="nivel_precio_id" id="nivel_precio_id" class="block w-full mt-1 rounded-md border-gray-300 shadow-sm">
                                <option value="">-- Selecciona un nivel --</option>
                                @foreach ($niveles as $nivel)
                                    <option value="{{ $nivel->id }}" {{ old('nivel_precio_id', $cliente->nivel_precio_id) == $nivel->id ? 'selected' : '' }}>
                                        {{ $nivel->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('nivel_precio_id')" class="mt-2" />
                        </div>


                        <!-- Botón -->
                        <div class="flex justify-end mt-4">
                            <x-primary-button class="ml-4">
                                {{ __('Actualizar Cliente') }}
                            </x-primary-button>
                        </div>

                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
