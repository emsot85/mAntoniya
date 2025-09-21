<x-filament::page>
    <div class="space-y-6">
        <div class="space-y-6">
            <div x-data="{ activeTab: 'export' }" class="bg-white shadow rounded-lg p-6">
                <!-- Вкладки -->
                <div class="flex border-b border-gray-200 mb-6">
                    <button
                        @click="activeTab = 'export'"
                        :class="{
                    'border-blue-500 text-blue-600': activeTab === 'export',
                    'border-transparent text-gray-500 hover:text-gray-700': activeTab !== 'export'
                }"
                        class="whitespace-nowrap py-3 px-4 border-b-2 font-medium text-sm focus:outline-none mr-4">
                        Экспорт данных
                    </button>
                    <button
                        @click="activeTab = 'import'"
                        :class="{
                    'border-green-500 text-green-600': activeTab === 'import',
                    'border-transparent text-gray-500 hover:text-gray-700': activeTab !== 'import'
                }"
                        class="whitespace-nowrap py-3 px-4 border-b-2 font-medium text-sm focus:outline-none">
                        Импорт данных
                    </button>
                </div>

                <!-- Содержимое вкладок -->
                <div class="mt-4">
                    <!-- Вкладка Экспорт -->
                    <div x-show="activeTab === 'export'" class="p-4">
                        <form method="GET" action="{{ route('backup.export') }}">
                            <button type="submit"
                                class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200">
                                Скачать JSON
                            </button>
                        </form>
                    </div>

                    <!-- Вкладка Импорт -->
                    <div x-show="activeTab === 'import'" class="p-4">
                        <h2 class="text-lg font-medium mb-4">Импорт данных</h2>
                        {{ $this->form }}
                        <x-filament::button
                            wire:click="import"
                            type="button"
                            class="mt-4 bg-green-600 hover:bg-green-700"
                            wire:loading.attr="disabled"
                            wire:target="import">
                            <span wire:loading.remove wire:target="import">Импортировать JSON</span>
                            <span wire:loading wire:target="import">Импортируем...</span>
                        </x-filament::button>
                    </div>
                </div>
            </div>
        </div>


        <script>
            window.addEventListener('confirm-import', event => {
                if (confirm(event.detail.message)) {
                    Livewire.emit('confirmedImport');
                }
            });
        </script>
</x-filament::page>