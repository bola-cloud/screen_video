@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card p-5">
                <div class="card-header d-flex justify-content-between">
                    <h1>{{ __('lang.edit_display_time') }}</h1>
                    <a href="{{ route('tv_display_times.index') }}" class="btn btn-primary">{{ __('lang.back_to_list') }}</a>
                </div>

                <form action="{{ route('tv_display_times.update', $displayTime->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <!-- Global Date Inputs for All TVs -->
                    <div class="form-group mt-4">
                        <label for="date">{{ __('lang.display_date') }}</label>
                        <input type="date" name="date" class="form-control" value="{{ $displayTime->date }}" required>
                    </div>

                    <div class="form-group">
                        <label for="start_time">{{ __('lang.start_time') }}</label>
                        <input type="time" name="start_time" class="form-control" value="{{ $displayTime->start_time }}" required>
                    </div>

                    <div class="form-group">
                        <label for="end_time">{{ __('lang.end_time') }}</label>
                        <input type="time" name="end_time" class="form-control" value="{{ $displayTime->end_time }}" required>
                    </div>

                    <!-- Button to select/deselect all TVs -->
                    <button type="button" id="select-all" class="btn btn-secondary mb-4">{{ __('lang.select_all') }}</button>

                    <!-- TV Grid Display -->
                    <div class="row">
                        @foreach ($tvs as $tv)
                            <div class="col-md-2">
                                <div class="tv-screen">
                                    <div class="tv-name">{{ __('lang.tv_name') }}: {{ $tv->name }}</div>
                                    <div class="checkbox-wrapper">
                                        <input type="checkbox" id="tv-{{ $tv->id }}" name="tvs[]" value="{{ $tv->id }}"
                                        {{ in_array($tv->id, $assignedTvs) ? 'checked' : '' }}>
                                        <label for="tv-{{ $tv->id }}"></label>
                                    </div>
                                </div>
                                <div class="text-center">{{ __('lang.tv_location') }}: {{ $tv->location }}</div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-success mt-4">{{ __('lang.update_display_time') }}</button>
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
