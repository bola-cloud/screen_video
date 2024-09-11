@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card p-5">
                <div class="card-header d-flex justify-content-between">
                    <h1>Edit Ad: {{ $ad->title }}</h1>
                    <a href="{{ route('ads.index') }}" class="btn btn-primary">Back to List</a>
                </div>
                <div class="card-body">

                    <!-- Edit the basic ad details -->
                    <form action="{{ route('ads.update', $ad->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label for="title">Ad Title</label>
                            <input type="text" name="title" class="form-control" value="{{ $ad->title }}" required>
                        </div>
                        <div class="form-group">
                            <label for="brand">Brand</label>
                            <input type="text" name="brand" class="form-control" value="{{ $ad->brand }}" required>
                        </div>
                        <div class="form-group">
                            <label for="video_link">Video Link</label>
                            <input type="text" name="video_link" class="form-control" value="{{ $ad->video_link }}" required>
                        </div>
                        <div class="form-group">
                            <label for="video_duration">Video Duration (H:i:s)</label>
                            <input type="text" name="video_duration" class="form-control" value="{{ $ad->video_duration }}" required>
                        </div>
                        <button type="submit" class="btn btn-success">Update Ad</button>
                    </form>

                    <hr>

                    <!-- Show the attached TVs and durations -->
                    <h3>Assigned TVs and Duration</h3>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>TV Name</th>
                                <th>Location</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($assignedTvs as $schedule)
                                <tr>
                                    <td>{{ $schedule->tv->name }}</td>
                                    <td>{{ $schedule->tv->location }}</td>
                                    <td>{{ $schedule->start_at }}</td>
                                    <td>{{ $schedule->end_at }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <hr>

                    <!-- Reassign TVs if needed -->
                    <h3>Reassign TVs</h3>

                    <!-- Global Date Inputs for All TVs -->
                    <form action="{{ route('ads.storeTvs', $ad->id) }}" method="POST">
                        @csrf

                        @php
                            // Use the first schedule to pre-fill the start and end dates if available
                            $startAt = $assignedTvs->first() ? $assignedTvs->first()->start_at : '';
                            $endAt = $assignedTvs->first() ? $assignedTvs->first()->end_at : '';
                        @endphp

                        <div class="form-group mt-4">
                            <label for="start_at">Start Date</label>
                            <input type="date" name="start_at" class="form-control" value="{{ old('start_at', $startAt) }}" required>
                        </div>
                        <div class="form-group">
                            <label for="end_at">End Date</label>
                            <input type="date" name="end_at" class="form-control" value="{{ old('end_at', $endAt) }}" required>
                        </div>

                        <!-- Button to select/deselect all TVs -->
                        <button type="button" id="select-all" class="btn btn-secondary mb-4">Select All</button>

                        <!-- TV Grid Display -->
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

                        <!-- Submit Button -->
                        <button type="submit" class="btn btn-info mt-4">Assign New TVs</button>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('select-all').addEventListener('click', function() {
        const checkboxes = document.querySelectorAll('input[type="checkbox"][name="tvs[]"]');
        checkboxes.forEach(checkbox => checkbox.checked = true);
    });
</script>
@endsection
