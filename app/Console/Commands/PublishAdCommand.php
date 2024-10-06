<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\Api\AdController;

class PublishAdCommand extends Command
{
    protected $signature = 'ad:publish {tv_id} {order}';

    protected $description = 'Publish the next advertisement';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $tv_id = $this->argument('tv_id');
        $order = $this->argument('order');

        $adController = new AdController();
        $adController->publishNextAd($tv_id, $order);

        $this->info('Ad has been published successfully.');
    }
}
