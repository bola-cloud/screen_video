@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card p-5">
                <div class="card-header d-flex justify-content-between">
                    <h1>Add New Display Time</h1>
                    <a href="{{ route('tv_display_times.index') }}" class="btn btn-primary">Back to List</a>
                </div>

                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('tv_display_times.store') }}" method="POST">
                    @csrf

                    <!-- Global Date Inputs for All TVs -->
                    <div class="form-group mt-4">
                        <label for="date">Display Date</label>
                        <input type="date" name="date" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="start_time">Start Time</label>
                        <input type="time" name="start_time" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="end_time">End Time</label>
                        <input type="time" name="end_time" class="form-control" required>
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
                                        <input type="checkbox" id="tv-{{ $tv->id }}" name="tvs[]" value="{{ $tv->id }}">
                                        <label for="tv-{{ $tv->id }}"></label>
                                    </div>
                                </div>
                                <div class="text-center">{{ $tv->location }}</div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-success mt-4">Save Display Time</button>
                </form>

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
