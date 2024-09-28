<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessVideoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $photoPath;
    protected $musicPath;
    protected $durationInSeconds;

    public function __construct($photoPath, $musicPath, $durationInSeconds)
    {
        $this->photoPath = $photoPath;
        $this->musicPath = $musicPath;
        $this->durationInSeconds = $durationInSeconds;
    }

    public function handle()
    {
        // Call your function to generate the video from photo and music
        $outputVideo = createVideoFromPhotoAndMusic($this->photoPath, $this->musicPath, $this->durationInSeconds);

        // Logic to notify user that the video is ready (optional)
    }
}