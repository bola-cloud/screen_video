@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card p-5">
                <div class="card-header d-flex justify-content-between">
                    <h1>Add New TV</h1>
                    <a href="{{ route('tvs.index') }}" class="btn btn-primary">Back to List</a>    
                </div>
                
                <div class="card-body">
                    <form action="{{ route('tvs.store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="name">TV Name</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                        </div>
                        <div class="form-group">
                            <label for="location">Location</label>
                            <input type="text" name="location" class="form-control" value="{{ old('location') }}" required>
                        </div>
                        <button type="submit" class="btn btn-success">Add TV</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
