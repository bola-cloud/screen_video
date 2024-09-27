@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card p-5">
                <div class="card-header d-flex justify-content-between">
                    <h1>{{ __('lang.assign_tvs_for_ad') }}: {{ $ad->title }}</h1>
                    <a href="{{ route('ads.index') }}" class="btn btn-primary">{{ __('lang.back_to_list') }}</a>
                </div>

                <div class="card-body">
                    <!-- Search Input -->
                    <div class="form-group">
                        <input type="text" id="search-tvs" class="form-control" placeholder="{{ __('lang.search_tvs') }}">
                    </div>

                    <!-- Select All / Deselect All Button -->
                    <button type="button" id="select-all-btn" class="btn btn-secondary mb-3">{{ __('lang.select_all') }}</button>

                    <!-- Form to assign TVs -->
                    <form action="{{ route('ads.storeTvs', $ad->id) }}" method="POST">
                        @csrf

                        <!-- TV Grid Display -->
                        <div class="row" id="tv-list">
                            @foreach ($tvs as $tv)
                                <div class="col-md-2 tv-item" data-name="{{ $tv->name }}" data-location="{{ $tv->location }}">
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

                        <!-- Date Inputs -->
                        <div class="form-group mt-4">
                            <label for="start_at">{{ __('lang.start_date') }}</label>
                            <input type="date" name="start_at" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="end_at">{{ __('lang.end_date') }}</label>
                            <input type="date" name="end_at" class="form-control" required>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="btn btn-success">{{ __('lang.assign_tvs') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript for Select All and Search -->
<script>
    // Select All / Deselect All functionality
    document.getElementById('select-all-btn').addEventListener('click', function () {
        const checkboxes = document.querySelectorAll('#tv-list input[type="checkbox"]');
        const selectAll = this.textContent.trim() === "{{ __('lang.select_all') }}";
        checkboxes.forEach(checkbox => checkbox.checked = selectAll);
        this.textContent = selectAll ? "{{ __('lang.deselect_all') }}" : "{{ __('lang.select_all') }}";
    });

    // Search functionality for filtering TV list
    document.getElementById('search-tvs').addEventListener('keyup', function () {
        const searchTerm = this.value.toLowerCase();
        const tvItems = document.querySelectorAll('.tv-item');
        tvItems.forEach(item => {
            const name = item.getAttribute('data-name').toLowerCase();
            const location = item.getAttribute('data-location').toLowerCase();
            if (name.includes(searchTerm) || location.includes(searchTerm)) {
                item.style.display = '';
            } else {
                item.style.display = 'none';
            }
        });
    });
</script>
@endsection
