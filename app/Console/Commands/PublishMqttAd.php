<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;

class PublishMqttAd extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'publish:mqtt-ad {tv_id} {ad_id} {video_link}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish an ad to the TV via MQTT';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $tv_id = $this->argument('tv_id');
        $ad_id = $this->argument('ad_id');
        $video_link = $this->argument('video_link');

        // Data to send to the MQTT publisher script
        $adData = json_encode([
            'tv_id' => $tv_id,
            'ad_id' => $ad_id,
            'video_link' => $video_link,
        ]);

        // Path to your MQTT publisher script
        $scriptPath = base_path('node_scripts/mqtt_publisher.js');

        // Execute the Node.js script with the data
        $process = new Process(['node', $scriptPath, $adData]);
        $process->run();

        if (!$process->isSuccessful()) {
            Log::error('Failed to publish MQTT ad: ' . $process->getErrorOutput());
            return Command::FAILURE;
        }

        Log::info("Successfully published MQTT ad for TV ID: $tv_id, Ad ID: $ad_id");
        return Command::SUCCESS;
    }
}
