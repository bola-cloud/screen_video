@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card p-5">
                <div class="card-header d-flex justify-content-between">
                    <h1>{{ __('lang.advertisements_list') }}</h1>
                    <a href="{{ route('ads.create') }}" class="btn btn-primary">{{ __('lang.add_new_ad') }}</a>    
                </div>

                <!-- Search Form -->
                <form action="{{ route('ads.index') }}" method="GET" class="mb-4">
                    <div class="form-group">
                        <input type="text" name="search" class="form-control" value="{{ request()->get('search') }}" placeholder="{{ __('lang.search_ads') }}">
                    </div>
                    <button type="submit" class="btn btn-secondary">{{ __('lang.search') }}</button>
                </form>

                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>{{ __('lang.id') }}</th>
                                <th>{{ __('lang.title') }}</th>
                                <th>{{ __('lang.brand') }}</th>
                                <th>{{ __('lang.video_link') }}</th>
                                <th>{{ __('lang.video_duration') }}</th>
                                <th>{{ __('lang.is_active') }}</th>
                                <th>{{ __('lang.actions') }}</th>
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
                                    <td class="d-flex">
                                        <a href="{{ route('ads.edit', $ad->id) }}" class="btn btn-warning mr-2 ml-2">{{ __('lang.edit') }}</a>
                                        {{--<a href="{{ route('ads.chooseTvs', $ad->id) }}" class="btn btn-info">{{ __('lang.assign_tvs') }}</a>--}}
                                        <form action="{{ route('ads.destroy', $ad->id) }}" method="POST" style="display:inline-block;">
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
                    alert('{{ __('lang.success_message') }}');
                } else {
                    alert('{{ __('lang.error_message') }}');
                }
            });
        });
    });
</script>

@endsection
