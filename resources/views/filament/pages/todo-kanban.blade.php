<x-filament-panels::page>
    <div x-data wire:ignore.self class="flex overflow-x-auto overflow-y-hidden gap-4 pb-4 min-h-[70vh]">
        @foreach($statuses as $status)
            <div class="w-80 flex-shrink-0 flex flex-col">
                {{-- Column header --}}
                <div class="flex items-center justify-between px-3 py-2 rounded-t-xl {{ $status['header_color_classes'] }} font-semibold text-sm">
                    <span>{{ $status['title'] }}</span>
                    <span class="text-xs font-normal opacity-70">{{ count($status['records']) }}</span>
                </div>
                {{-- Cards container --}}
                <div
                    data-status-id="{{ $status['id'] }}"
                    class="flex flex-col flex-1 gap-2 p-2 bg-gray-100 dark:bg-gray-800 rounded-b-xl min-h-20"
                >
                    @foreach($status['records'] as $record)
                        <div
                            id="{{ $record->getKey() }}"
                            wire:click.stop="mountAction('editTodo', @js(['recordId' => $record->getKey()]))"
                            class="record bg-white dark:bg-gray-700 rounded-lg px-3 py-2 shadow-sm cursor-grab text-sm"
                            @if($record->timestamps && now()->diffInSeconds($record->updated_at, true) < 3)
                                x-data
                                x-init="
                                    $el.classList.add('ring-2', 'ring-primary-500')
                                    setTimeout(() => $el.classList.remove('ring-2', 'ring-primary-500'), 3000)
                                "
                            @endif
                        >
                            <div class="font-medium text-gray-800 dark:text-gray-100">
                                {{ $record->title }}
                            </div>
                            @if($record->platform)
                                <div class="text-xs text-gray-400 mt-1">{{ $record->platform->name }}</div>
                            @endif
                            @if($record->due_at)
                                <div class="text-xs mt-1 {{ $record->due_at->isPast() ? 'text-red-500' : 'text-gray-400' }}">
                                    {{ $record->due_at->format('d M') }}
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>

    {{-- SortableJS + wiring --}}
    <div wire:ignore>
        <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.3/Sortable.min.js"></script>
        <script>
            function onStart() {
                setTimeout(() => document.body.classList.add('grabbing'))
            }

            function onEnd() {
                document.body.classList.remove('grabbing')
            }

            function setData(dataTransfer, el) {
                dataTransfer.setData('id', el.id)
            }

            function onAdd(e) {
                const recordId = e.item.id
                const status = e.to.dataset.statusId
                const fromOrderedIds = [].slice.call(e.from.children).map(c => c.id)
                const toOrderedIds = [].slice.call(e.to.children).map(c => c.id)
                Livewire.dispatch('status-changed', { recordId, status, fromOrderedIds, toOrderedIds })
            }

            function onUpdate(e) {
                const recordId = e.item.id
                const status = e.from.dataset.statusId
                const orderedIds = [].slice.call(e.from.children).map(c => c.id)
                Livewire.dispatch('sort-changed', { recordId, status, orderedIds })
            }

            function initKanban() {
                const statuses = @js($statuses->pluck('id')->values()->toArray());
                statuses.forEach(status => {
                    const el = document.querySelector(`[data-status-id='${status}']`)
                    if (el) {
                        Sortable.create(el, {
                            group: 'todo-kanban',
                            ghostClass: 'opacity-30',
                            animation: 150,
                            onStart,
                            onEnd,
                            onUpdate,
                            setData,
                            onAdd,
                        })
                    }
                })
            }

            document.addEventListener('livewire:navigated', initKanban)
            document.addEventListener('DOMContentLoaded', initKanban)
        </script>

        <style>
            body.grabbing * { cursor: grabbing !important; }
        </style>
    </div>
</x-filament-panels::page>
