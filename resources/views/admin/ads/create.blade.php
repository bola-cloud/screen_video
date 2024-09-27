@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card p-5">
                <div class="card-header d-flex justify-content-between">
                    <h1>{{ __('lang.add_new_ad') }}</h1>
                    <a href="{{ route('ads.index') }}" class="btn btn-primary">{{ __('lang.back_to_list') }}</a>
                </div>
                <div class="card-body">
                    <form action="{{ route('ads.store') }}" method="POST" id="adForm">
                        @csrf
                        <div class="form-group">
                            <label for="title">{{ __('lang.ad_title') }}</label>
                            <input type="text" name="title" class="form-control" value="{{ old('title') }}" required>
                        </div>
                        <div class="form-group">
                            <label for="brand">{{ __('lang.brand') }}</label>
                            <input type="text" name="brand" class="form-control" value="{{ old('brand') }}" required>
                        </div>
                        <div class="form-group">
                            <label for="video_link">{{ __('lang.video_link') }}</label>
                            <input type="text" name="video_link" class="form-control" value="{{ old('video_link') }}" required>
                        </div>
                        <div class="form-group">
                            <label for="video_duration">{{ __('lang.video_duration') }}</label>
                            <input type="text" name="video_duration" class="form-control" value="{{ old('video_duration') }}" readonly>
                        </div>

                        <!-- Select client dropdown -->
                        <div class="form-group">
                            <label for="client_id">{{ __('lang.client') }}</label>
                            <select name="client_id" id="client_id" class="form-control" required>
                                <option value="">{{ __('lang.select_client') }}</option>
                                @foreach($clients as $client)
                                    <option value="{{ $client->id }}">{{ $client->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <!-- Submit button -->
                        <button type="submit" class="btn btn-success" id="submitButton">{{ __('lang.add_ad') }}</button>
                    </form>

                    <!-- Video.js player -->
                    <div id="videoContainer" class="mt-5" style="display: none;">
                        <video id="video_player" class="video-js vjs-default-skin vjs-big-play-centered" controls preload="auto" width="640" height="360"></video>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<!-- Video.js CSS -->
<link href="https://vjs.zencdn.net/7.19.2/video-js.css" rel="stylesheet" />

<!-- Video.js JS -->
<script src="https://vjs.zencdn.net/7.19.2/video.js"></script>

<!-- YouTube tech for Video.js -->
<script src="https://cdn.jsdelivr.net/npm/videojs-youtube@2.6.1/dist/Youtube.min.js"></script>

<script>
    // Initialize the player outside the event
    let player = null;
    let isSubmitting = false;  // To track if the form has already been submitted

    document.querySelector('input[name="video_link"]').addEventListener('change', function() {
        // Get the video link value
        let videoLink = this.value;

        // Validate if the link is a valid YouTube URL
        let isValidYouTubeLink = /^(https?:\/\/)?(www\.youtube\.com|youtu\.?be)\/.+$/gi.test(videoLink);

        if (isValidYouTubeLink) {
            // Extract the YouTube video ID from the URL
            let videoId = videoLink.match(/(?:https?:\/\/)?(?:www\.)?(?:youtube\.com\/(?:[^\/\n\s]+\/|(?:v|embed)\/|.*[?&]v=)|youtu\.be\/)([a-zA-Z0-9_-]{11})/);
            videoId = videoId ? videoId[1] : null;

            if (videoId) {
                // Show the video container
                document.getElementById('videoContainer').style.display = 'block';

                // Initialize or update the Video.js player
                if (player) {
                    player.dispose();
                }
                player = videojs('video_player', {
                    techOrder: ['youtube'],
                    sources: [{
                        type: 'video/youtube',
                        src: 'https://www.youtube.com/watch?v=' + videoId
                    }]
                });

                // Wait for the metadata to be loaded before retrieving the duration
                player.on('loadedmetadata', function() {
                    let duration = player.duration();
                    
                    // Format duration to H:i:s
                    let formattedDuration = new Date(duration * 1000).toISOString().substr(11, 8);

                    // Set the duration in the form field
                    document.querySelector('input[name="video_duration"]').value = formattedDuration;

                    // Re-enable the submit button
                    document.getElementById('submitButton').disabled = false;
                });

                player.ready(function() {
                    player.play();
                });
            } else {
                alert('{{ __('lang.invalid_youtube_video_id') }}');
            }
        } else {
            // Display an error or hide the player if it's not a valid YouTube URL
            alert('{{ __('lang.please_enter_valid_youtube_url') }}');
            document.getElementById('videoContainer').style.display = 'none';
        }
    });
</script>
@endpush
