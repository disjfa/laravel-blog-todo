<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GenerateOpenApiDocs extends Command
{
    protected $signature = 'openapi:generate {--output=public/api-docs.json : Path to write the JSON file}';

    protected $description = 'Generate OpenAPI JSON from PHP attributes and write to disk';

    public function handle(): int
    {
        $output = base_path($this->option('output'));
        $binary = base_path('vendor/bin/openapi');

        $this->info('Scanning for OpenAPI attributes...');

        $command = sprintf(
            '%s %s %s --output %s --format json',
            escapeshellcmd($binary),
            escapeshellarg(app_path('OpenApi')),
            escapeshellarg(app_path('Http/Controllers/Api')),
            escapeshellarg($output),
        );

        passthru($command, $exitCode);

        if ($exitCode !== 0) {
            $this->error('OpenAPI generation failed.');

            return self::FAILURE;
        }

        $this->info("OpenAPI spec written to {$output}");

        return self::SUCCESS;
    }
}
