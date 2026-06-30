<?php

namespace App\Filament\Pages;

use BackedEnum;
use Carbon\Carbon;
use DateTimeInterface;
use Filament\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Collection;
use Laravel\Sanctum\PersonalAccessToken;

/**
 * @property-read Collection<int, PersonalAccessToken> $tokens
 */
class ApiTokens extends Page
{
    protected static bool $isDiscovered = false;

    protected static bool $shouldRegisterNavigation = false;

    protected string $view = 'filament.pages.api-tokens';

    public function getLayout(): string
    {
        return 'filament-panels::components.layout.simple';
    }

    public function getMaxContentWidth(): Width|string|null
    {
        return Width::ScreenLarge;
    }

    protected static ?string $navigationLabel = 'API Tokens';

    protected static ?string $title = 'Personal API Tokens';

    protected static ?int $navigationSort = 99;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentCheck;

    public static function getNavigationGroup(): string|null|\UnitEnum
    {
        return 'Account';
    }

    public ?string $newToken = null;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('createToken')
                ->label('Create token')
                ->icon(Heroicon::OutlinedPlus)
                ->schema([
                    TextInput::make('name')
                        ->required()
                        ->maxLength(255)
                        ->placeholder('Postman token'),
                    TextInput::make('abilities')
                        ->required()
                        ->default('*')
                        ->helperText('Comma-separated abilities. Use * for full access.'),
                    DateTimePicker::make('expires_at')
                        ->label('Expires at')
                        ->seconds(false),
                ])
                ->action(function (array $data): void {
                    $user = auth()->user();
                    if (! $user) {
                        return;
                    }

                    $expiresAt = $data['expires_at'] ?? null;
                    if (is_string($expiresAt) && filled($expiresAt)) {
                        $expiresAt = Carbon::parse($expiresAt);
                    }
                    if (! $expiresAt instanceof DateTimeInterface) {
                        $expiresAt = null;
                    }

                    $token = $user->createToken(
                        $data['name'],
                        $this->normalizeAbilities((string) $data['abilities']),
                        $expiresAt,
                    );

                    $this->newToken = $token->plainTextToken;

                    Notification::make()
                        ->title('Token created')
                        ->body('Copy the token now. It will only be shown once.')
                        ->success()
                        ->send();
                }),
            Action::make('revokeAllTokens')
                ->label('Revoke all')
                ->icon(Heroicon::OutlinedTrash)
                ->color('danger')
                ->requiresConfirmation()
                ->visible(fn (): bool => $this->tokens->isNotEmpty())
                ->action(function (): void {
                    $user = auth()->user();
                    if (! $user) {
                        return;
                    }

                    $user->tokens()->delete();

                    Notification::make()
                        ->title('All tokens revoked')
                        ->success()
                        ->send();
                }),
        ];
    }

    public function getTokensProperty(): Collection
    {
        $user = auth()->user();

        if (! $user) {
            return collect();
        }

        return $user->tokens()->latest()->get();
    }

    public function revokeToken(int $tokenId): void
    {
        $user = auth()->user();
        if (! $user) {
            return;
        }

        $user->tokens()->whereKey($tokenId)->delete();

        Notification::make()
            ->title('Token revoked')
            ->success()
            ->send();
    }

    /**
     * @return array<int, string>
     */
    protected function normalizeAbilities(string $abilities): array
    {
        $parsed = collect(explode(',', $abilities))
            ->map(static fn (string $ability): string => trim($ability))
            ->filter()
            ->values()
            ->all();

        return $parsed === [] ? ['*'] : $parsed;
    }
}
