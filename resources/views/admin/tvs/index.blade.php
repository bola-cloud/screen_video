@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card p-5">
                <div class="card-header d-flex justify-content-between">
                    <h1>{{ __('lang.tv_list') }}</h1>
                    <a href="{{ route('tvs.create') }}" class="btn btn-primary">{{ __('lang.add_new_tv') }}</a>    
                </div>

                @if(session('success'))
                    <div class="alert alert-success">{{ __('lang.success_message') }}</div>
                @endif

                <!-- Search Form -->
                <form action="{{ route('tvs.index') }}" method="GET" class="mb-4">
                    <div class="form-group">
                        <input type="text" name="search" class="form-control" value="{{ request()->get('search') }}" placeholder="{{ __('lang.search') }}">
                    </div>
                    <button type="submit" class="btn btn-secondary">{{ __('lang.search') }}</button>
                </form>

                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>{{ __('lang.id') }}</th>
                                <th>{{ __('lang.name') }}</th>
                                <th>{{ __('lang.location') }}</th>
                                <th>{{ __('lang.is_active') }}</th>
                                <th>{{ __('lang.actions') }}</th>
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
                                    <td class="d-flex">
                                        <a href="{{ route('tvs.edit', $tv->id) }}" class="btn btn-warning">{{ __('lang.edit') }}</a>
                                        <a href="{{ route('tv.ad-order', $tv->id) }}" class="btn btn-info mr-2 ml-2">{{ __('lang.show_orders') }}</a>
                                        <form action="{{ route('tvs.destroy', $tv->id) }}" method="POST" style="display:inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger">{{ __('lang.delete') }}</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination links (if using pagination) -->
                {{ $tvs->appends(['search' => request()->get('search')])->links() }}
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
                    alert('{{ __('lang.activation_status_success') }}');
                } else {
                    alert('{{ __('lang.activation_status_failure') }}');
                }
            });
        });
    });
</script>

@endsection
@push('css')
   <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Optional JavaScript and Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        a{
            text-decoration: none !important;
        }
    </style>
@endpush