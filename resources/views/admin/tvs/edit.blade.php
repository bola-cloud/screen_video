@extends('layouts.admin')

@section('content')
    <h1>{{ __('lang.edit_tv') }}</h1>

    @if(session('error'))
        <div class="alert alert-danger">{{ __('lang.error_message') }}</div>
    @endif
    <form action="{{ route('tvs.update', $tv->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="name">{{ __('lang.tv_name') }}</label>
            <input type="text" name="name" class="form-control" value="{{ $tv->name }}" required>
        </div>
        <div class="form-group">
            <label for="screen_id">{{ __('lang.screen_id') }}</label>
            <input type="number" name="screen_id" class="form-control" value="{{ $tv->screen_id }}" required>
        </div>
        <div class="form-group">
            <label for="location">{{ __('lang.location') }}</label>
            <input type="text" name="location" class="form-control" value="{{ $tv->location }}" required>
        </div>
        <button type="submit" class="btn btn-success">{{ __('lang.update_tv') }}</button>
    </form>
@endsection
