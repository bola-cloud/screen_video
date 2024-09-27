<?php

return [

    'ffmpeg' => [
        'binaries' => env('FFMPEG_PATH', 'C:/ffmpeg/ffmpeg-7.0.2-essentials_build/bin/ffmpeg.exe'),
        'threads' => 12,
    ],

    'ffprobe' => [
        'binaries' => env('FFPROBE_PATH', 'C:/ffmpeg/ffmpeg-7.0.2-essentials_build/bin/ffprobe.exe'),
    ],

    'timeout' => 3600,

    'log_channel' => env('LOG_CHANNEL', 'stack'),

    'temporary_files_root' => env('FFMPEG_TEMPORARY_FILES_ROOT', sys_get_temp_dir()),

    'temporary_files_encrypted_hls' => env('FFMPEG_TEMPORARY_ENCRYPTED_HLS', env('FFMPEG_TEMPORARY_FILES_ROOT', sys_get_temp_dir())),

    'enable_logging' => true,
];
