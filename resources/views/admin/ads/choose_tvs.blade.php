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

                <div class="card-body mt-4">
                    <!-- Search Input for TVs -->
                    <input type="text" id="search-tvs" class="form-control mb-3" placeholder="Search TVs...">

                    <form action="{{ route('ads.storeTvs', $ad->id) }}" method="POST">
                        @csrf

                        <!-- Tabs for Institutions -->
                        <ul class="nav nav-tabs mt-5" id="institutionTabs" role="tablist">
                            @foreach ($institutions as $institution)
                                <li class="nav-item">
                                    <a class="nav-link {{ $loop->first ? 'active' : '' }}" id="tab-{{ $institution->id }}" data-bs-toggle="tab" href="#institution-{{ $institution->id }}" role="tab">{{ $institution->name }}</a>
                                </li>
                            @endforeach
                        </ul>

                        <div class="tab-content mt-3" id="institutionTabContent">
                            @foreach ($institutions as $institution)
                                <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="institution-{{ $institution->id }}" role="tabpanel">
                                    
                                    <!-- Select All / Deselect All Button for each tab -->
                                    <button type="button" class="select-all-btn btn btn-secondary mb-3" data-institution-id="{{ $institution->id }}">{{ __('lang.select_all') }}</button>

                                    <!-- TV Grid Display for this Institution -->
                                    <div class="row" id="tv-list-{{ $institution->id }}">
                                        @foreach ($institution->tvs as $tv)
                                            <div class="col-md-2 tv-item" data-name="{{ $tv->name }}" data-location="{{ $tv->location }}">
                                                <div class="tv-screen">
                                                    <div class="tv-name">{{ $tv->name }}</div>
                                                    <div class="checkbox-wrapper">
                                                        <input type="checkbox" id="tv-{{ $tv->id }}" name="tvs[]" value="{{ $tv->id }}" class="tv-checkbox">
                                                        <label for="tv-{{ $tv->id }}"></label>
                                                    </div>
                                                </div>
                                                <div class="text-center">{{ $tv->location }}</div>
                                                <div class="form-group">
                                                    <label for="turns-{{ $tv->id }}">{{ __('Number of Turns') }}</label>
                                                    <input type="number" name="turns[{{ $tv->id }}]" id="turns-{{ $tv->id }}" class="form-control" placeholder="{{ __('Enter turns') }}">
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
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

<!-- JavaScript to manage tabs, checkboxes, and search -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Preserve TV selections when switching between tabs
    let selectedTvs = {};

    // Function to save selected checkboxes
    function saveSelectedTvs() {
        const checkboxes = document.querySelectorAll('.tv-checkbox');
        checkboxes.forEach(checkbox => {
            if (checkbox.checked) {
                selectedTvs[checkbox.value] = true;
            } else {
                delete selectedTvs[checkbox.value];
            }
        });
    }

    // Restore selected TVs when switching tabs
    function restoreSelectedTvs() {
        const checkboxes = document.querySelectorAll('.tv-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = !!selectedTvs[checkbox.value];
        });
    }

    // Event listener for each tab
    document.querySelectorAll('.nav-link').forEach(tab => {
        tab.addEventListener('click', function () {
            saveSelectedTvs();
            restoreSelectedTvs();
        });
    });

    // Select All / Deselect All functionality for each tab
    document.querySelectorAll('.select-all-btn').forEach(button => {
        button.addEventListener('click', function () {
            const institutionId = this.getAttribute('data-institution-id');
            const checkboxes = document.querySelectorAll(`#tv-list-${institutionId} input[type="checkbox"]`);
            const selectAll = this.textContent.trim() === "{{ __('lang.select_all') }}";
            checkboxes.forEach(checkbox => checkbox.checked = selectAll);
            this.textContent = selectAll ? "{{ __('lang.deselect_all') }}" : "{{ __('lang.select_all') }}";

            // Save the selection after selecting/deselecting
            saveSelectedTvs();
        });
    });

    // Search functionality (can be global or per tab)
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
});
</script>
@endsection

@push('css')
<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Bootstrap JS (necessary for tab toggling) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
@endpush