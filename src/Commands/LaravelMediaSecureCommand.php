<?php

namespace Bekwoh\LaravelMediaSecure\Commands;

use Illuminate\Console\Command;

class LaravelMediaSecureCommand extends Command
{
    public $signature = 'laravel-media-secure';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
