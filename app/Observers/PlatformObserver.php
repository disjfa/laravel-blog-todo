<?php

namespace App\Observers;

use App\Models\Platform;
use Illuminate\Support\Str;

class PlatformObserver
{
    public function creating(Platform $platform): void
    {
        $platform->slug = $this->uniqueSlug($platform, $platform->name);
    }

    public function saving(Platform $platform): void
    {
        if (empty($platform->slug)) {
            $platform->slug = $this->uniqueSlug($platform, $platform->name);
        }
    }

    private function uniqueSlug(Platform $platform, string $name): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $i = 1;

        while (Platform::where('slug', $slug)->where('id', '!=', $platform->id ?? '')->exists()) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }
}
