<x-filament-panels::page>
    @vite('resources/js/fullcalendar.js')

    <div
        wire:ignore
        x-data="{
            calendar: null,
            events: @js($events),
            init() {
                this.calendar = new FullCalendar.Calendar(this.$refs.cal, {
                    plugins: [
                        FullCalendar.dayGridPlugin,
                        FullCalendar.timeGridPlugin,
                        FullCalendar.listPlugin,
                    ],
                    initialView: 'dayGridMonth',
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth,timeGridWeek,listWeek',
                    },
                    height: 'auto',
                    weekends: true,
                    locale: FullCalendar.enGbLocale,
                    events: this.events,
                    eventClick: (info) => {
                        $wire.mountAction('editTodo', { recordId: info.event.id })
                    },
                })
                this.calendar.render()
                // Force a size recalculation after the page layout has fully painted
                setTimeout(() => this.calendar.updateSize(), 50)
            },
        }"
        @todos-updated.window="
            $wire.call('refreshEvents').then(events => {
                calendar.removeAllEvents()
                calendar.addEventSource(events)
            })
        "
        x-on:livewire:navigated.window="setTimeout(() => calendar && calendar.updateSize(), 50)"
    >
        <div x-ref="cal"></div>
    </div>
</x-filament-panels::page>
