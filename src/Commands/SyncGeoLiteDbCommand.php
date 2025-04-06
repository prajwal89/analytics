<?php

declare(strict_types=1);

namespace Prajwal89\Analytics\Commands;

use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;

/**
 * php artisan analytics:sync-geolite-db-command
 *
 * @see https://github.com/P3TERX/GeoLite.mmdb
 */
class SyncGeoLiteDbCommand extends Command
{
    protected $signature = 'analytics:sync-geolite-db-command';

    protected $description = 'Download and synchronize the GeoLite2 City database file';

    public function handle(): int
    {
        $dbPath = config('analytics.geolite_db_path');
        $downloadUrl = 'https://raw.githubusercontent.com/P3TERX/GeoLite.mmdb/download/GeoLite2-City.mmdb';
        $tempPath = storage_path('app/temp_geolite_' . Carbon::now()->timestamp . '.mmdb');

        try {
            // Create directory if it doesn't exist
            $directory = dirname($dbPath);
            if (!File::exists($directory)) {
                File::makeDirectory($directory, 0755, true);
                $this->info("Created directory: {$directory}");
            }

            // Download file to temp location with progress bar
            $this->info("Downloading GeoLite2 database from {$downloadUrl}");
            $this->info('This may take a while...');

            $response = Http::withOptions([
                'sink' => $tempPath,
                'progress' => function ($downloadTotal, $downloadedBytes): void {
                    if ($downloadTotal > 0) {
                        $progress = round(($downloadedBytes / $downloadTotal) * 100);
                        $this->output->write("\rProgress: {$progress}% (" . $this->formatBytes($downloadedBytes) . ' / ' . $this->formatBytes($downloadTotal) . ')');
                    }
                },
            ])->get($downloadUrl);

            if (!$response->successful()) {
                throw new Exception('Failed to download file: HTTP status ' . $response->status());
            }

            // Verify the downloaded file exists and has content
            if (!File::exists($tempPath) || File::size($tempPath) === 0) {
                throw new Exception('Downloaded file is empty or does not exist');
            }

            // Replace the old database with the new one
            if (File::exists($dbPath)) {
                File::delete($dbPath);
                $this->info("\nRemoved old database file");
            }

            File::move($tempPath, $dbPath);

            $fileSize = File::size($dbPath);
            $this->newLine();
            $this->info('✓ Successfully downloaded and installed GeoLite2 database');
            $this->info("Location: {$dbPath}");
            $this->info('Size: ' . $this->formatBytes($fileSize));

            return Command::SUCCESS;
        } catch (Exception $e) {
            $this->newLine();
            $this->error('Failed to sync GeoLite database: ' . $e->getMessage());

            // Clean up temp file if it exists
            if (File::exists($tempPath)) {
                File::delete($tempPath);
                $this->info('Cleaned up temporary file');
            }

            return Command::FAILURE;
        }
    }

    /**
     * Format bytes to human-readable format
     *
     * @param  int  $bytes
     * @param  int  $precision
     */
    private function formatBytes($bytes, $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
