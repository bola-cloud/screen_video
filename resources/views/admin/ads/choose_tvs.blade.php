@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card p-5">
                <div class="card-header d-flex justify-content-between">
                    <h1>Assign TVs for Ad: {{ $ad->title }}</h1>
                    <a href="{{ route('ads.index') }}" class="btn btn-primary">Back to List</a>    
                </div>

                <div class="card-body">
                    <!-- Form to assign TVs -->
                    <form action="{{ route('ads.storeTvs', $ad->id) }}" method="POST">
                        @csrf

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

                        <!-- Date Inputs -->
                        <div class="form-group mt-4">
                            <label for="start_at">Start Date</label>
                            <input type="date" name="start_at" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="end_at">End Date</label>
                            <input type="date" name="end_at" class="form-control" required>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="btn btn-success">Assign TVs</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
