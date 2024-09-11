@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card p-5">
                <div class="card-header d-flex justify-content-between">
                    <h1>TV Display Times</h1>
                    <a href="{{ route('tv_display_times.create') }}" class="btn btn-primary">Add New Display Time</a>    
                </div>

                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>TV</th>
                            <th>Date</th>
                            <th>Start Time</th>
                            <th>End Time</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($displayTimes as $index=>$displayTime)
                            <tr>
                                <td>{{ $index+1 }}</td>
                                <td>{{ $displayTime->tv->name }}</td>
                                <td>{{ $displayTime->date }}</td>
                                <td>{{ $displayTime->start_time }}</td>
                                <td>{{ $displayTime->end_time }}</td>
                                <td>
                                    <a href="{{ route('tv_display_times.edit', $displayTime->id) }}" class="btn btn-warning">Edit</a>
                                    <form action="{{ route('tv_display_times.destroy', $displayTime->id) }}" method="POST" style="display:inline-block;">
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
