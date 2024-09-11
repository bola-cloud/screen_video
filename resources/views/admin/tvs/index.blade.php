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
                            <th>Is Active</th>
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
                                    <input type="checkbox" class="is_active_switch" data-id="{{ $tv->id }}" {{ $tv->is_active ? 'checked' : '' }}>
                                </td>
                                <td>
                                    <a href="{{ route('tvs.edit', $tv->id) }}" class="btn btn-warning">Edit</a>
                                    <a href="{{ route('tv.ad-order', $tv->id) }}" class="btn btn-info"> show orders </a>
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

<script>
    document.querySelectorAll('.is_active_switch').forEach(item => {
        item.addEventListener('change', function() {
            const tvId = this.getAttribute('data-id');
            const isActive = this.checked ? 1 : 0;

            fetch(`/tvs/activate/${tvId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ is_active: isActive })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('TV activation status updated successfully.');
                } else {
                    alert('Failed to update activation status.');
                }
            });
        });
    });
</script>

@endsection
