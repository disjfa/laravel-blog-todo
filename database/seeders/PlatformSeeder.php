<?php

namespace Database\Seeders;

use App\Models\Platform;
use Illuminate\Database\Seeder;

class PlatformSeeder extends Seeder
{
    public function run(): void
    {
        $platforms = [
            ['name' => 'TikTok', 'slug' => 'tiktok'],
            ['name' => 'Facebook', 'slug' => 'facebook'],
            ['name' => 'Instagram', 'slug' => 'instagram'],
            ['name' => 'X', 'slug' => 'x'],
        ];

        foreach ($platforms as $data) {
            Platform::firstOrCreate(
                ['slug' => $data['slug']],
                ['name' => $data['name'], 'is_active' => true]
            );
        }
    }
}
