<?php

declare(strict_types=1);

namespace Prajwal89\Analytics\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class SvgStorageLinkCommand extends Command
{
    protected $signature = 'analytics:link-svg';

    protected $description = 'Create a symbolic link for SVG flags';

    public function handle(): void
    {
        $target = base_path('modules/analytics/resources/svg/flags');
        $link = resource_path('svg/flags');

        // Remove existing symlink if it exists
        if (File::exists($link) || is_link($link)) {
            File::delete($link);
        }

        // Create the symlink
        symlink($target, $link);

        $this->info('SVG storage link created: ' . $link);
    }
}
