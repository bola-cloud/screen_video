<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\AdController;

class PublishNextAd extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'publish:next-ad {tv_id}';

    protected $description = 'Publish the next ad for a TV';


    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tv_id = $this->argument('tv_id');
        $adController = new AdController();
        $adController->publishNextAd($tv_id);
    }
}
