@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card p-5">
                <div class="card-header d-flex justify-content-between">
                    <h1>Edit Display Time</h1>
                    <a href="{{ route('tv_display_times.index') }}" class="btn btn-primary">Back to List</a>
                </div>

                <form action="{{ route('tv_display_times.update', $displayTime->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <!-- Global Date Inputs for All TVs -->
                    <div class="form-group mt-4">
                        <label for="date">Display Date</label>
                        <input type="date" name="date" class="form-control" value="{{ $displayTime->date }}" required>
                    </div>

                    <div class="form-group">
                        <label for="start_time">Start Time</label>
                        <input type="time" name="start_time" class="form-control" value="{{ $displayTime->start_time }}" required>
                    </div>

                    <div class="form-group">
                        <label for="end_time">End Time</label>
                        <input type="time" name="end_time" class="form-control" value="{{ $displayTime->end_time }}" required>
                    </div>

                    <!-- Tabs for Institutions -->
                    <ul class="nav nav-tabs" id="tvTabs" role="tablist">
                        @foreach ($institutions as $institution)
                            <li class="nav-item">
                                <a class="nav-link {{ $loop->first ? 'active' : '' }}" id="tab-{{ $institution->id }}" data-toggle="tab" href="#institution-{{ $institution->id }}" role="tab">{{ $institution->name }}</a>
                            </li>
                        @endforeach
                    </ul>

                    <!-- TV Display with Checkbox grouped by Institution -->
                    <div class="tab-content mt-3" id="tvTabContent">
                        @foreach ($institutions as $institution)
                            <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="institution-{{ $institution->id }}" role="tabpanel">
                                <button type="button" class="btn btn-sm btn-secondary select-all-institution" data-institution="{{ $institution->id }}">Select All TVs for {{ $institution->name }}</button>
                                <div class="row mt-3">
                                    @foreach ($institution->tvs as $tv)
                                        <div class="col-md-2">
                                            <div class="tv-screen">
                                                <div class="tv-name">{{ $tv->name }}</div>
                                                <div class="checkbox-wrapper">
                                                    <!-- Use a unique name for the Add Single Day TV selection -->
                                                    <input type="checkbox" id="tv-single-day-{{ $tv->id }}" name="tvs[]" value="{{ $tv->id }}"
                                                    {{ in_array($tv->id, $assignedTvs) ? 'checked' : '' }}>
                                                    <label for="tv-single-day-{{ $tv->id }}"></label>
                                                </div>
                                            </div>
                                            <div class="text-center">{{ $tv->location }}</div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-success mt-4">Update Display Time</button>
                </form>

            </div>
        </div>
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
</script>
@endsection
