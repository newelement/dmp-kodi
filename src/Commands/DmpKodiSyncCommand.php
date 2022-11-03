<?php

namespace Newelement\DmpKodi\Commands;

use Illuminate\Console\Command;
use Newelement\DmpKodi\Services\KodiMediaSyncService;

class DmpKodiSyncCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'dmp-kodi:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'DMP Kodi poster sync';


    public function handle()
    {
        $service = new KodiMediaSyncService();
        $service->syncMedia();
    }
}
