@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card p-5">
                <div class="card-header d-flex justify-content-between">
                    <h1>Advertisements List</h1>
                    <a href="{{ route('ads.create') }}" class="btn btn-primary">Add New Ad</a>    
                </div>

                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Brand</th>
                            <th>Video Link</th>
                            <th>Video Duration</th>
                            <th>Is Active</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($ads as $ad)
                            <tr>
                                <td>{{ $ad->id }}</td>
                                <td>{{ $ad->title }}</td>
                                <td>{{ $ad->brand }}</td>
                                <td>{{ $ad->video_link }}</td>
                                <td>{{ $ad->video_duration }}</td>
                                <td>
                                    <input type="checkbox" role="switch" class="is_active_switch" data-id="{{ $ad->id }}" {{ $ad->is_active ? 'checked' : '' }}>
                                </td>
                                <td>
                                    <a href="{{ route('ads.edit', $ad->id) }}" class="btn btn-warning">Edit</a>
                                    <a href="{{ route('ads.chooseTvs', $ad->id) }}" class="btn btn-info">Assign TVs</a>
                                    <form action="{{ route('ads.destroy', $ad->id) }}" method="POST" style="display:inline-block;">
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
            const adId = this.getAttribute('data-id');
            const isActive = this.checked ? 1 : 0;

            fetch(`/ads/activate/${adId}`, {
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
                    alert('Ad activation status updated successfully.');
                } else {
                    alert('Failed to update activation status.');
                }
            });
        });
    });
</script>

@endsection
