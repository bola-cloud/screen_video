@extends('layouts.admin')

@section('content')
    <h1>Edit TV</h1>

    <form action="{{ route('tvs.update', $tv->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="name">TV Name</label>
            <input type="text" name="name" class="form-control" value="{{ $tv->name }}" required>
        </div>
        <div class="form-group">
            <label for="screen_id">Screen ID</label>
            <input type="number" name="screen_id" class="form-control" value="{{ $tv->screen_id }}" required>
        </div>
        <div class="form-group">
            <label for="location">Location</label>
            <input type="text" name="location" class="form-control" value="{{ $tv->location }}" required>
        </div>
        <button type="submit" class="btn btn-success">Update TV</button>
    </form>
@endsection
