@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card p-5">
                <div class="card-header d-flex justify-content-between">
                    <h1>{{ __('lang.edit_ad', ['title' => $ad->title]) }}</h1>
                    <a href="{{ route('ads.index') }}" class="btn btn-primary">{{ __('lang.back_to_list') }}</a>
                </div>
                <div class="card-body">

                    <!-- Edit the basic ad details -->
                    <form action="{{ route('ads.update', $ad->id) }}" method="POST" id="adForm">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label for="title">{{ __('lang.ad_title') }}</label>
                            <input type="text" name="title" class="form-control" value="{{ $ad->title }}" required>
                        </div>
                        <div class="form-group">
                            <label for="brand">{{ __('lang.brand') }}</label>
                            <input type="text" name="brand" class="form-control" value="{{ $ad->brand }}" required>
                        </div>
                        <div class="form-group">
                            <label for="video_link">{{ __('lang.video_link') }}</label>
                            <input type="text" name="video_link" class="form-control" value="{{ $ad->video_link }}" required>
                        </div>
                        <div class="form-group">
                            <label for="video_duration">{{ __('lang.video_duration') }}</label>
                            <input type="text" name="video_duration" class="form-control" value="{{ $ad->video_duration }}" readonly>
                        </div>

                        <!-- Video.js player -->
                        <div id="videoContainer" class="mt-5" style="display: none;">
                            <video id="video_player" class="video-js vjs-default-skin vjs-big-play-centered" controls preload="auto" width="640" height="360"></video>
                        </div>

                        <!-- Select client dropdown -->
                        <div class="form-group">
                            <label for="client_id">{{ __('lang.client') }}</label>
                            <select name="client_id" id="client_id" class="form-control" required>
                                <option value="">{{ __('lang.select_client') }}</option>
                                @foreach($clients as $client)
                                    <option value="{{ $client->id }}" {{ $ad->client_id == $client->id ? 'selected' : '' }}>{{ $client->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-success">{{ __('lang.update_ad') }}</button>
                    </form>

                    <hr>

                    <!-- Show the attached TVs and date ranges -->
                    <h3>{{ __('lang.assigned_tvs') }}</h3>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>{{ __('lang.tv_name') }}</th>
                                <th>{{ __('lang.location') }}</th>
                                <th>{{ __('lang.start_date') }}</th>
                                <th>{{ __('lang.end_date') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($assignedTvs as $schedule)
                                <tr>
                                    <td>{{ $schedule->tv->name }}</td>
                                    <td>{{ $schedule->tv->location }}</td>
                                    <td>{{ $firstDate }}</td>
                                    <td>{{ $lastDate }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <hr>

                    <!-- Reassign TVs if needed -->
                    <h3>{{ __('lang.reassign_tvs') }}</h3>

                    <form action="{{ route('ads.updatetvs', $ad->id) }}" method="POST">
                        @csrf
                        <div class="form-group mt-4">
                            <label for="start_at">{{ __('lang.start_date') }}</label>
                            <input type="date" name="start_at" class="form-control" value="{{ old('start_at', $firstDate) }}" required>
                        </div>
                        <div class="form-group">
                            <label for="end_at">{{ __('lang.end_date') }}</label>
                            <input type="date" name="end_at" class="form-control" value="{{ old('end_at', $lastDate) }}" required>
                        </div>

                        <button type="button" id="select-all" class="btn btn-secondary mb-4">{{ __('lang.select_all') }}</button>

                        <div class="row">
                            @foreach ($tvs as $tv)
                                <div class="col-md-2">
                                    <div class="tv-screen">
                                        <div class="tv-name">{{ $tv->name }}</div>
                                        <div class="checkbox-wrapper">
                                            <input type="checkbox" id="tv-{{ $tv->id }}" name="tvs[]" value="{{ $tv->id }}" {{ in_array($tv->id, $assignedTvs->pluck('tv_id')->toArray()) ? 'checked' : '' }}>
                                            <label for="tv-{{ $tv->id }}"></label>
                                        </div>
                                    </div>
                                    <div class="text-center">{{ $tv->location }}</div>
                                </div>
                            @endforeach
                        </div>

                        <button type="submit" class="btn btn-info mt-4">{{ __('lang.assign_new_tvs') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Initialize the player outside the event
    let player = null;

    // Load video and calculate duration on video_link change
    document.querySelector('input[name="video_link"]').addEventListener('change', function() {
        let videoLink = this.value;

        let isValidYouTubeLink = /^(https?:\/\/)?(www\.youtube\.com|youtu\.?be)\/.+$/gi.test(videoLink);

        if (isValidYouTubeLink) {
            let videoId = videoLink.match(/(?:https?:\/\/)?(?:www\.)?(?:youtube\.com\/(?:[^\/\n\s]+\/|(?:v|embed)\/|.*[?&]v=)|youtu\.be\/)([a-zA-Z0-9_-]{11})/);
            videoId = videoId ? videoId[1] : null;

            if (videoId) {
                document.getElementById('videoContainer').style.display = 'block';

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

                player.on('loadedmetadata', function() {
                    let duration = player.duration();
                    
                    let formattedDuration = new Date(duration * 1000).toISOString().substr(11, 8);

                    document.querySelector('input[name="video_duration"]').value = formattedDuration;
                });

                player.ready(function() {
                    player.play();
                });
            } else {
                alert('{{ __('lang.invalid_youtube_id') }}');
            }
        } else {
            alert('{{ __('lang.invalid_youtube_url') }}');
            document.getElementById('videoContainer').style.display = 'none';
        }
    });

    document.getElementById('select-all').addEventListener('click', function() {
        const checkboxes = document.querySelectorAll('input[type="checkbox"][name="tvs[]"]');
        checkboxes.forEach(checkbox => checkbox.checked = true);
    });
</script>
@endsection

@push('js')
 <!-- Video.js CSS --> <link href="https://vjs.zencdn.net/7.19.2/video-js.css" rel="stylesheet" />
  <!-- Video.js JS --> <script src="https://vjs.zencdn.net/7.19.2/video.js"></script> 
  <!-- YouTube tech for Video.js --> <script src="https://cdn.jsdelivr.net/npm/videojs-youtube@2.6.1/dist/Youtube.min.js"></script> 
  
 @endpush
