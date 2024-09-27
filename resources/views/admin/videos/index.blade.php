@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card p-5">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <h1>{{ __('lang.upload_video_create') }}</h1>

                <form id="durationForm" action="{{ route('processUpload') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="process_type">{{ __('lang.select_process_type') }}</label>
                        <select name="process_type" id="process_type" class="form-control" required>
                            <option value="photo_music">{{ __('lang.create_video_photo_music') }}</option>
                            <option value="repeat_video">{{ __('lang.repeat_existing_video') }}</option>
                        </select>
                    </div>

                    <!-- Photo and Music Inputs -->
                    <div id="photoMusicInputs">
                        <div class="form-group">
                            <label for="photo">{{ __('lang.upload_photo') }}</label>
                            <input type="file" name="photo" class="form-control" accept="image/*">
                        </div>

                        <div class="form-group">
                            <label for="music">{{ __('lang.upload_music') }}</label>
                            <input type="file" name="music" class="form-control">
                        </div>

                        <div class="form-group">
                            <label for="duration_photo_music">{{ __('lang.specify_duration') }}</label>
                            <div class="row">
                                <div class="col-md-4">
                                    <select id="hours_photo_music" class="form-control">
                                        <!-- Options will be populated by JS -->
                                    </select>
                                    <small>{{ __('Hours') }}</small>
                                </div>
                                <div class="col-md-4">
                                    <select id="minutes_photo_music" class="form-control">
                                        <!-- Options will be populated by JS -->
                                    </select>
                                    <small>{{ __('Minutes') }}</small>
                                </div>
                                <div class="col-md-4">
                                    <select id="seconds_photo_music" class="form-control">
                                        <!-- Options will be populated by JS -->
                                    </select>
                                    <small>{{ __('Seconds') }}</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Repeat Video Inputs -->
                    <div id="repeatVideoInputs" style="display: none;">
                        <div class="form-group">
                            <label for="video">{{ __('lang.upload_video') }}</label>
                            <input type="file" name="video" class="form-control">
                        </div>

                        <div class="form-group">
                            <label for="duration_repeat_video">{{ __('lang.specify_duration') }}</label>
                            <div class="row">
                                <div class="col-md-4">
                                    <select id="hours_repeat_video" class="form-control">
                                        <!-- Options will be populated by JS -->
                                    </select>
                                    <small>{{ __('Hours') }}</small>
                                </div>
                                <div class="col-md-4">
                                    <select id="minutes_repeat_video" class="form-control">
                                        <!-- Options will be populated by JS -->
                                    </select>
                                    <small>{{ __('Minutes') }}</small>
                                </div>
                                <div class="col-md-4">
                                    <select id="seconds_repeat_video" class="form-control">
                                        <!-- Options will be populated by JS -->
                                    </select>
                                    <small>{{ __('Seconds') }}</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- This will be the field named 'duration' that gets submitted -->
                    <input type="hidden" name="duration" id="duration">

                    <button type="submit" class="btn btn-primary">{{ __('lang.process_video') }}</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    // Populate the time options dynamically for hours, minutes, and seconds
    function populateTimeOptions(id, max) {
        for (var i = 0; i <= max; i++) {
            var value = i < 10 ? '0' + i : i;
            $(id).append('<option value="'+ value +'">'+ value +'</option>');
        }
    }

    $(document).ready(function() {
        // Populate hours (0-23), minutes and seconds (0-59) for both sections
        populateTimeOptions('#hours_photo_music, #hours_repeat_video', 23);  // Hours
        populateTimeOptions('#minutes_photo_music, #minutes_repeat_video', 59);  // Minutes
        populateTimeOptions('#seconds_photo_music, #seconds_repeat_video', 59);  // Seconds

        // Switch between photo/music inputs and repeat video inputs
        $('#process_type').change(function () {
            var processType = $(this).val();
            if (processType === 'photo_music') {
                $('#photoMusicInputs').show();
                $('#repeatVideoInputs').hide();
            } else if (processType === 'repeat_video') {
                $('#photoMusicInputs').hide();
                $('#repeatVideoInputs').show();
            }
        }).trigger('change'); // Trigger the change event on page load

        // Format the duration in hh:mm:ss and store it in the 'duration' input before form submission
        $('#durationForm').submit(function (event) {
            var processType = $('#process_type').val();

            var hours, minutes, seconds;

            if (processType === 'photo_music') {
                hours = $('#hours_photo_music').val();
                minutes = $('#minutes_photo_music').val();
                seconds = $('#seconds_photo_music').val();
            } else if (processType === 'repeat_video') {
                hours = $('#hours_repeat_video').val();
                minutes = $('#minutes_repeat_video').val();
                seconds = $('#seconds_repeat_video').val();
            }

            // Combine hours, minutes, and seconds into hh:mm:ss format
            var formattedDuration = hours + ':' + minutes + ':' + seconds;

            // Set the hidden input value to the formatted duration
            $('#duration').val(formattedDuration);

            // Optionally, log the result (for debugging)
            console.log('Formatted Duration: ' + formattedDuration);
        });
    });
</script>
@endpush
