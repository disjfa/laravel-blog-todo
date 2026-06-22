<x-filament-panels::page>
    <div class="mb-4">
        <x-filament::link href="{{ url()->previous(url('/admin')) }}" icon="heroicon-o-arrow-left">
            Back to admin
        </x-filament::link>
    </div>
    @if ($newToken)
        <x-filament::section heading="Token generated" icon="heroicon-o-key">
            <p class="text-sm text-gray-600 dark:text-gray-300">
                Copy this bearer token now. It will not be shown again.
            </p>

            <div class="mt-3 rounded-lg bg-gray-50 p-3 dark:bg-gray-900">
                <code class="block break-all text-xs text-gray-900 dark:text-gray-100">{{ $newToken }}</code>
            </div>
        </x-filament::section>
    @endif

    <x-filament::section heading="Active tokens" icon="heroicon-o-shield-check">
        @if ($this->tokens->isEmpty())
            <p class="text-sm text-gray-600 dark:text-gray-300">No personal access tokens created yet.</p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700 text-left">
                            <th class="py-2 pr-4 font-medium">Name</th>
                            <th class="py-2 pr-4 font-medium">Abilities</th>
                            <th class="py-2 pr-4 font-medium">Last used</th>
                            <th class="py-2 pr-4 font-medium">Expires</th>
                            <th class="py-2 pr-4 font-medium">Created</th>
                            <th class="py-2 pr-4 font-medium">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($this->tokens as $token)
                            <tr class="border-b border-gray-100 dark:border-gray-800">
                                <td class="py-2 pr-4">{{ $token->name }}</td>
                                <td class="py-2 pr-4">
                                    @php
                                        $abilities = $token->abilities ?? [];
                                    @endphp
                                    {{ $abilities === ['*'] ? '*' : implode(', ', $abilities) }}
                                </td>
                                <td class="py-2 pr-4">{{ $token->last_used_at?->diffForHumans() ?? 'Never' }}</td>
                                <td class="py-2 pr-4">{{ $token->expires_at?->toDateTimeString() ?? 'No expiry' }}</td>
                                <td class="py-2 pr-4">{{ $token->created_at->toDateTimeString() }}</td>
                                <td class="py-2 pr-4">
                                    <x-filament::button
                                        color="danger"
                                        size="xs"
                                        wire:click="revokeToken({{ $token->id }})"
                                        wire:confirm="Revoke this token?"
                                    >
                                        Revoke
                                    </x-filament::button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </x-filament::section>
</x-filament-panels::page>

