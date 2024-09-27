<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class VideoController extends Controller
{
    public function showForm()
    {
        // Show the form to upload files for both processes
        return view('admin.videos.index');
    }

public function processUpload(Request $request)
{
    // Increase maximum execution time for this script
    set_time_limit(300); 

    // Validate the uploaded files based on process type
    $processType = $request->input('process_type');

    if ($processType === 'photo_music') {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'music' => 'required|mimes:mp3,wav|max:10240', // limit size to 10MB
            'duration_photo_music' => 'nullable|regex:/^\d+(:\d{2})?(:\d{2})?$/', // Allow input like 60 or 00:01:00
        ]);

        // Store uploaded files
        $photoPath = $request->file('photo')->store('public/photos');
        $musicPath = $request->file('music')->store('public/music');
      
        $duration = $request->input('duration_photo_music', null); 

        // Generate the video
        $outputVideo = $this->createVideoFromPhotoAndMusic($photoPath, $musicPath, $duration);

    } elseif ($processType === 'repeat_video') {
        $request->validate([
            'video' => 'required|mimes:mp4|max:102400',
            'duration_repeat_video' => 'required|regex:/^\d+(:\d{2})?(:\d{2})?$/',
        ]);

        // Store the uploaded video
        $videoPath = $request->file('video')->store('public/videos');
        $duration = $request->input('duration_repeat_video');

        // Generate the repeated video
        $outputVideo = $this->createRepeatedVideo($videoPath, $duration);
    } else {
        return back()->withErrors(['Invalid process type selected']);
    }

    // Return the video as a downloadable response
    return response()->download(storage_path('app/' . $outputVideo));
}

protected function createVideoFromPhotoAndMusic($photoPath, $musicPath, $duration = null)
{	
  
    $photoFullPath = storage_path('app/' . $photoPath);
    $musicFullPath = storage_path('app/' . $musicPath);
    $uniqueVideoName = 'output_video_' . uniqid() . '.mp4';
    $outputVideoPath = storage_path('app/public/videos/' . $uniqueVideoName);

    // Ensure the videos directory exists
    if (!file_exists(storage_path('app/public/videos'))) {
        mkdir(storage_path('app/public/videos'), 0755, true);
    }

    // Convert the specified duration to seconds, if provided in HH:MM:SS format
    if ($duration) {
        if (strpos($duration, ':') !== false) {
            list($hours, $minutes, $seconds) = explode(':', $duration);
            $durationInSeconds = ($hours * 3600) + ($minutes * 60) + $seconds;
        } else {
            $durationInSeconds = (int) $duration;
        }
    } else {
        $durationInSeconds = null; // Default to the duration of the audio
    }

    // Get the actual duration of the audio file
    $audioDurationCommand = "ffprobe -i $musicFullPath -show_entries format=duration -v quiet -of csv=p=0";
    $audioDurationInSeconds = (float) shell_exec($audioDurationCommand);

    if ($audioDurationInSeconds <= 0) {
        throw new \Exception("Failed to retrieve the audio duration.");
    }

    // If a duration is provided and it's longer than the audio duration, loop the audio
    if ($durationInSeconds && $audioDurationInSeconds < $durationInSeconds) {
        $loopsNeeded = ceil($durationInSeconds / $audioDurationInSeconds);

        // Prepare a temporary file list for concatenation
        $concatListFile = storage_path('app/public/music/concat_list_' . uniqid() . '.txt');
        $concatFileContent = '';

        // Create a list of the audio repeated `loopsNeeded` times
        for ($i = 0; $i < $loopsNeeded; $i++) {
            $concatFileContent .= "file '$musicFullPath'\n";
        }

        // Save the concat list to a file
        file_put_contents($concatListFile, $concatFileContent);

        // Temporary output for concatenated audio
        $loopedAudioFullPath = storage_path('app/public/music/looped_audio_' . uniqid() . '.mp3');

        // FFmpeg command to concatenate the audio and trim to exact duration
        $ffmpegAudioCommand = "ffmpeg -f concat -safe 0 -i $concatListFile -c copy -t $durationInSeconds $loopedAudioFullPath 2>&1";
        exec($ffmpegAudioCommand, $audioOutput, $audioReturnVar);

        // Remove the temporary concat list file
        unlink($concatListFile);

        if ($audioReturnVar !== 0) {
            throw new \Exception("FFmpeg audio concatenation failed: " . implode("\n", $audioOutput));
        }

        // Use the looped audio file as the input for video creation
        $musicFullPath = $loopedAudioFullPath;
    }

    // If no duration is provided, set the video duration to the audio length
    if (!$durationInSeconds) {
        $durationInSeconds = $audioDurationInSeconds;
    }

    // FFmpeg command to create video from the photo and audio with the specified duration
    $ffmpegCommand = "ffmpeg -loop 1 -i $photoFullPath -i $musicFullPath -c:v libx264 -r 25 -c:a aac -b:a 192k -shortest";

    // Enforce the video duration to match the specified duration
    $ffmpegCommand .= " -t $durationInSeconds";

    $ffmpegCommand .= " $outputVideoPath 2>&1";

    // Log FFmpeg command and paths
    \Log::info("Running FFmpeg command: " . $ffmpegCommand);
    \Log::info("Photo Path: " . $photoFullPath);
    \Log::info("Music Path: " . $musicFullPath);

    // Execute the FFmpeg command
    $output = [];
    $returnVar = null;
    exec($ffmpegCommand, $output, $returnVar);

    // Log FFmpeg output
    \Log::info("FFmpeg Output: " . implode("\n", $output));

    if ($returnVar !== 0) {
        // If FFmpeg fails, throw an error
        throw new \Exception("FFmpeg command failed: " . implode("\n", $output));
    }

    return 'public/videos/' . $uniqueVideoName;
}


    
    protected function createRepeatedVideo($videoPath, $duration)
{
    $videoFullPath = storage_path('app/' . $videoPath);
    $uniqueVideoName = 'repeated_video_' . uniqid() . '.mp4';
    $outputVideoPath = storage_path('app/public/videos/' . $uniqueVideoName);

    if (!file_exists(storage_path('app/public/videos'))) {
        mkdir(storage_path('app/public/videos'), 0755, true);
    }

    // Convert the duration to seconds
    if (strpos($duration, ':') !== false) {
        list($hours, $minutes, $seconds) = explode(':', $duration);
        $durationInSeconds = ($hours * 3600) + ($minutes * 60) + $seconds;
    } else {
        $durationInSeconds = (int) $duration;
    }

    // Get the duration of the input video
    $videoDurationCommand = "ffprobe -i $videoFullPath -show_entries format=duration -v quiet -of csv=p=0";
    $videoDurationInSeconds = (float) shell_exec($videoDurationCommand);

    if ($videoDurationInSeconds <= 0) {
        throw new \Exception("Failed to retrieve the video duration.");
    }

    // Calculate how many loops are required
    $loopsNeeded = ceil($durationInSeconds / $videoDurationInSeconds);

    // Prepare a temporary file list for concatenation
    $concatListFile = storage_path('app/public/videos/concat_list_' . uniqid() . '.txt');
    $concatFileContent = '';

    // Create a list of the video repeated `loopsNeeded` times
    for ($i = 0; $i < $loopsNeeded; $i++) {
        $concatFileContent .= "file '$videoFullPath'\n";
    }

    // Save the concat list to a file
    file_put_contents($concatListFile, $concatFileContent);

    // FFmpeg command to concatenate and truncate the video to the exact duration
    $ffmpegCommand = "ffmpeg -f concat -safe 0 -i $concatListFile -c copy -t $durationInSeconds $outputVideoPath 2>&1";
    exec($ffmpegCommand, $output, $returnVar);

    // Remove the temporary concat list file
    unlink($concatListFile);

    if ($returnVar !== 0) {
        throw new \Exception("FFmpeg command failed: " . implode("\n", $output));
    }

    return 'public/videos/' . $uniqueVideoName;
}

}
