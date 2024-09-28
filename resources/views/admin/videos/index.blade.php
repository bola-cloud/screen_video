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

                <form action="{{ route('processUpload') }}" method="POST" enctype="multipart/form-data">
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

                        <!-- Select dropdowns for hours, minutes, and seconds -->
                        <div class="form-group">
                            <label>{{ __('lang.specify_duration') }}</label>
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="hours">{{ __('lang.hours') }}</label>
                                    <select name="hours" id="hours" class="form-control">
                                        @for ($i = 0; $i <= 23; $i++)
                                            <option value="{{ $i }}">{{ $i }}</option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="minutes">{{ __('lang.minutes') }}</label>
                                    <select name="minutes" id="minutes" class="form-control">
                                        @for ($i = 0; $i <= 59; $i++)
                                            <option value="{{ $i }}">{{ $i }}</option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="seconds">{{ __('lang.seconds') }}</label>
                                    <select name="seconds" id="seconds" class="form-control">
                                        @for ($i = 0; $i <= 59; $i++)
                                            <option value="{{ $i }}">{{ $i }}</option>
                                        @endfor
                                    </select>
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

                        <!-- Select dropdowns for hours, minutes, and seconds -->
                        <div class="form-group">
                            <label>{{ __('lang.specify_duration') }}</label>
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="hours_repeat">{{ __('lang.hours') }}</label>
                                    <select name="hours_repeat" id="hours_repeat" class="form-control">
                                        @for ($i = 0; $i <= 23; $i++)
                                            <option value="{{ $i }}">{{ $i }}</option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="minutes_repeat">{{ __('lang.minutes') }}</label>
                                    <select name="minutes_repeat" id="minutes_repeat" class="form-control">
                                        @for ($i = 0; $i <= 59; $i++)
                                            <option value="{{ $i }}">{{ $i }}</option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="seconds_repeat">{{ __('lang.seconds') }}</label>
                                    <select name="seconds_repeat" id="seconds_repeat" class="form-control">
                                        @for ($i = 0; $i <= 59; $i++)
                                            <option value="{{ $i }}">{{ $i }}</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">{{ __('lang.process_video') }}</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    document.getElementById('process_type').addEventListener('change', function () {
        var processType = this.value;
        var photoMusicInputs = document.getElementById('photoMusicInputs');
        var repeatVideoInputs = document.getElementById('repeatVideoInputs');

        if (processType === 'photo_music') {
            photoMusicInputs.style.display = 'block';
            repeatVideoInputs.style.display = 'none';
        } else if (processType === 'repeat_video') {
            photoMusicInputs.style.display = 'none';
            repeatVideoInputs.style.display = 'block';
        }
    });

    // Trigger change event on page load to show/hide appropriate fields
    document.getElementById('process_type').dispatchEvent(new Event('change'));
</script>    
@endpush
