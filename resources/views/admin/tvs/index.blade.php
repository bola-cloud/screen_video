@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card p-5">
                <div class="card-header d-flex justify-content-between">
                    <h1>TV List</h1>
                    <a href="{{ route('tvs.create') }}" class="btn btn-primary">Add New TV</a>    
                </div>

                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
            
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Location</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($tvs as $tv)
                            <tr>
                                <td>{{ $tv->id }}</td>
                                <td>{{ $tv->name }}</td>
                                <td>{{ $tv->location }}</td>
                                <td>
                                    <a href="{{ route('tvs.edit', $tv->id) }}" class="btn btn-warning">Edit</a>
                                    <form action="{{ route('tvs.destroy', $tv->id) }}" method="POST" style="display:inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
   
@endsection
