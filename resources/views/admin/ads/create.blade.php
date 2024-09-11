@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card p-5">
                <div class="card-header d-flex justify-content-between">
                    <h1>Add New Ad</h1>
                    <a href="{{ route('ads.index') }}" class="btn btn-primary">Back to List</a>
                </div>
                <div class="card-body">
                    <form action="{{ route('ads.store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="title">Ad Title</label>
                            <input type="text" name="title" class="form-control" value="{{ old('title') }}" required>
                        </div>
                        <div class="form-group">
                            <label for="brand">Brand</label>
                            <input type="text" name="brand" class="form-control" value="{{ old('brand') }}" required>
                        </div>
                        <div class="form-group">
                            <label for="video_link">Video Link</label>
                            <input type="text" name="video_link" class="form-control" value="{{ old('video_link') }}" required>
                        </div>
                        <div class="form-group">
                            <label for="video_duration">Video Duration (H:i:s)</label>
                            <input type="text" name="video_duration" class="form-control" value="{{ old('video_duration') }}" required>
                        </div>
                        <button type="submit" class="btn btn-success">Add Ad</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
