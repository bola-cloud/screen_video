@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card p-5">
                <div class="card-header d-flex justify-content-between">
                    <h1>{{ __('lang.add_new_display_time') }}</h1>
                    <a href="{{ route('tv_display_times.index') }}" class="btn btn-primary">{{ __('lang.back_to_list') }}</a>
                </div>

                @if($errors->any())
                    <div class="alert alert-danger">
                        <strong>{{ __('lang.error_heading') }}</strong>
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
                        <label for="date">{{ __('lang.display_date') }}</label>
                        <input type="date" name="date" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="start_time">{{ __('lang.start_time') }}</label>
                        <input type="time" name="start_time" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="end_time">{{ __('lang.end_time') }}</label>
                        <input type="time" name="end_time" class="form-control" required>
                    </div>

                    <!-- Search Input for Filtering TVs -->
                    <div class="form-group">
                        <input type="text" id="search-tvs" class="form-control mb-3" placeholder="{{ __('lang.search_tv_placeholder') }}">
                    </div>

                    <!-- Button to select/deselect all TVs -->
                    <button type="button" id="select-all" class="btn btn-secondary mb-4">{{ __('lang.select_all') }}</button>

                    <!-- TV Grid Display -->
                    <div class="row" id="tv-list">
                        @foreach ($tvs as $tv)
                            <div class="col-md-2 tv-item" data-name="{{ $tv->name }}" data-location="{{ $tv->location }}">
                                <div class="tv-screen">
                                    <div class="tv-name">{{ __('lang.tv_name') }}: {{ $tv->name }}</div>
                                    <div class="checkbox-wrapper">
                                        <input type="checkbox" id="tv-{{ $tv->id }}" name="tvs[]" value="{{ $tv->id }}">
                                        <label for="tv-{{ $tv->id }}"></label>
                                    </div>
                                </div>
                                <div class="text-center">{{ __('lang.tv_location') }}: {{ $tv->location }}</div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-success mt-4">{{ __('lang.save_display_time') }}</button>
                </form>

            </div>
        </div>
    </div>
</div>

<script>
    // Select All Checkboxes
    document.getElementById('select-all').addEventListener('click', function() {
        const checkboxes = document.querySelectorAll('input[type="checkbox"][name="tvs[]"]');
        checkboxes.forEach(checkbox => checkbox.checked = true);
    });

    // Search Functionality
    document.getElementById('search-tvs').addEventListener('keyup', function() {
        const searchTerm = this.value.toLowerCase();
        const tvItems = document.querySelectorAll('.tv-item');

        tvItems.forEach(item => {
            const name = item.getAttribute('data-name').toLowerCase();
            const location = item.getAttribute('data-location').toLowerCase();
            
            // Check if the TV name or location contains the search term
            if (name.includes(searchTerm) || location.includes(searchTerm)) {
                item.style.display = 'block'; // Show item if it matches
            } else {
                item.style.display = 'none'; // Hide item if it doesn't match
            }
        });
    });
</script>
@endsection
