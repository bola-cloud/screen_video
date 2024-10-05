@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card p-5">
                <div class="card-header d-flex justify-content-between">
                    <h1>@lang('lang.edit_ad', ['title' => $ad->title])</h1>
                    <a href="{{ route('ads.index') }}" class="btn btn-primary">@lang('lang.back_to_list')</a>
                </div>
                <div class="row">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
                <div class="card-body">

                    <!-- Edit the basic ad details -->
                    <form action="{{ route('ads.update', $ad->id) }}" method="POST" id="adForm">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label for="title">@lang('lang.ad_title')</label>
                            <input type="text" name="title" class="form-control" value="{{ $ad->title }}" required>
                        </div>
                        <div class="form-group">
                            <label for="brand">@lang('lang.brand')</label>
                            <input type="text" name="brand" class="form-control" value="{{ $ad->brand }}" required>
                        </div>
                        <div class="form-group">
                            <label for="video_link">@lang('lang.video_link')</label>
                            <input type="text" name="video_link" class="form-control" value="{{ $ad->video_link }}" required>
                        </div>
                        <div class="form-group">
                            <label for="video_duration">@lang('lang.video_duration')</label>
                            <input type="text" name="video_duration" class="form-control" value="{{ $ad->video_duration }}" readonly>
                        </div>

                        <!-- Video.js player -->
                        <div id="videoContainer" class="mt-5" style="display: none;">
                            <video id="video_player" class="video-js vjs-default-skin vjs-big-play-centered" controls preload="auto" width="640" height="360"></video>
                        </div>

                        <!-- Select client dropdown -->
                        <div class="form-group">
                            <label for="client_id">@lang('lang.client')</label>
                            <select name="client_id" id="client_id" class="form-control" required>
                                <option value="">@lang('lang.select_client')</option>
                                @foreach($clients as $client)
                                    <option value="{{ $client->id }}" {{ $ad->client_id == $client->id ? 'selected' : '' }}>{{ $client->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-success">@lang('lang.update_ad')</button>
                    </form>

                    <hr>

                    <!-- Show the attached TVs and date ranges -->
                    <h3 class="mt-5">
                        @lang('lang.assigned_tvs_dates')
                        <!-- Button to open Add Single Day functionality -->
                        <a href="#addSingleDayModal" class="btn btn-sm btn-success float-right" data-toggle="modal">@lang('lang.add_single_day_to_tv')</a>
                    </h3>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>@lang('lang.tv_name')</th>
                                <th>@lang('lang.location')</th>
                                <th>@lang('lang.date')</th>
                                <th>@lang('lang.actions')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($assignedTvs as $schedule)
                                <tr>
                                    <td>{{ $schedule->tv->name }}</td>
                                    <td>{{ $schedule->tv->location }}</td>
                                    <td>{{ $schedule->date }}</td>
                                    <td>
                                        <!-- Delete button for the schedule -->
                                        <form action="{{ route('ads.deleteschedule', [$ad->id, $schedule->id]) }}" method="POST" onsubmit="return confirm('@lang('lang.confirm_delete')');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">@lang('lang.delete')</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <hr>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Add Single Day -->
<div class="modal fade bd-example-modal-lg" id="addSingleDayModal" tabindex="-1" role="dialog" aria-labelledby="addSingleDayModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document"> <!-- Makes the modal larger -->
        <form action="{{ route('ads.addsingleday', $ad->id) }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addSingleDayModalLabel">@lang('lang.add_single_day_to_tv')</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Date Selection -->
                    <div class="form-group">
                        <label for="date">@lang('lang.select_date')</label>
                        <input type="date" name="date" class="form-control" required>
                    </div>

                    <!-- TV Selection with Tabs -->
                    <ul class="nav nav-tabs" id="tvTabs" role="tablist">
                        @foreach ($institutions as $institution)
                            <li class="nav-item">
                                <a class="nav-link {{ $loop->first ? 'active' : '' }}" id="tab-{{ $institution->id }}" data-toggle="tab" href="#institution-{{ $institution->id }}" role="tab">{{ $institution->name }}</a>
                            </li>
                        @endforeach
                    </ul>
                    <div class="tab-content mt-3" id="tvTabContent">
                        @foreach ($institutions as $institution)
                            <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="institution-{{ $institution->id }}" role="tabpanel">
                                <button type="button" class="btn btn-sm btn-secondary select-all-institution" data-institution="{{ $institution->id }}">@lang('lang.select_all_tvs_institution', ['institution' => $institution->name])</button>
                                <div class="row mt-3">
                                    @foreach ($institution->tvs as $tv)
                                        <div class="col-md-2 mt-3">
                                            <div class="tv-screen">
                                                <div class="tv-name">{{ $tv->name }}</div>
                                                <div class="checkbox-wrapper">
                                                    <!-- Use a unique name for the Add Single Day TV selection -->
                                                    <input type="checkbox" id="tv-single-day-{{ $tv->id }}" name="tvs_single_day[]" value="{{ $tv->id }}">
                                                    <label for="tv-single-day-{{ $tv->id }}"></label>
                                                </div>
                                               
                                            </div>
                                            <div class="text-center">{{ $tv->location }}</div>
                                             <!-- Add a field for turns number -->
                                             <div class="form-group">
                                                <label for="turns-{{ $tv->id }}">@lang('lang.turns')</label>
                                                <input type="number" name="turns[{{ $tv->id }}]" class="form-control" value="1" min="1">
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">@lang('lang.close')</button>
                    <button type="submit" class="btn btn-primary">@lang('lang.add_day')</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    // Select all checkboxes for an institution
    document.querySelectorAll('.select-all-institution').forEach(function(button) {
        button.addEventListener('click', function() {
            const institutionId = this.getAttribute('data-institution');
            const checkboxes = document.querySelectorAll('#institution-' + institutionId + ' input[type="checkbox"]');
            checkboxes.forEach(function(checkbox) {
                checkbox.checked = true;
            });
        });
    });

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
                    sources: [{ type: 'video/youtube', src: 'https://www.youtube.com/watch?v=' + videoId }]
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
                alert('@lang('lang.invalid_youtube_url')');
            }
        } else {
            alert('@lang('lang.invalid_youtube_url')');
            document.getElementById('videoContainer').style.display = 'none';
        }
    });
</script>
@endsection

@push('js')
 <!-- Video.js CSS --> <link href="https://vjs.zencdn.net/7.19.2/video-js.css" rel="stylesheet" />
 <!-- Video.js JS --> <script src="https://vjs.zencdn.net/7.19.2/video.js"></script> 
 <!-- YouTube tech for Video.js --> <script src="https://cdn.jsdelivr.net/npm/videojs-youtube@2.6.1/dist/Youtube.min.js"></script> 
@endpush
