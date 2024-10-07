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

                <!-- Search Form with Institution Filter -->
                <form id="filterForm" method="GET" class="mb-4 d-flex mt-4 row">
                    <div class="form-group me-2 col-md-4">
                        <input type="text" name="search" id="search" class="form-control" value="{{ request()->get('search') }}" placeholder="{{ __('lang.search') }}">
                    </div>

                    <div class="form-group me-2 col-md-4">
                        <select name="institution_id" id="institution_id" class="form-control">
                            <option value="">{{ __('lang.all_institutions') }}</option>
                            @foreach($institutions as $institution)
                                <option value="{{ $institution->id }}" {{ request()->get('institution_id') == $institution->id ? 'selected' : '' }}>
                                    {{ $institution->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-secondary">{{ __('lang.search') }}</button>
                    </div>
                </form>


                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>{{ __('lang.id') }}</th>
                                <th>{{ __('lang.name') }}</th>
                                <th>{{ __('lang.location') }}</th>
                                <th>{{ __('lang.institution') }}</th>
                                <th>{{ __('lang.is_active') }}</th>
                                <th>{{ __('lang.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody id="tvTableBody">
                            @foreach ($tvs as $tv)
                                <tr>
                                    <td>{{ $tv->id }}</td>
                                    <td>{{ $tv->name }}</td>
                                    <td>{{ $tv->location }}</td>
                                    <td>{{ $tv->institution->name ?? 'N/A' }}</td>
                                    <td>
                                        @if ($tv->status == 1)
                                            <span class="badge bg-success">{{ __('lang.online') }}</span>
                                        @else
                                            <span class="badge bg-danger">{{ __('lang.offline') }}</span>
                                        @endif
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

                <!-- Pagination links -->
                <div id="paginationLinks">
                    {{ $tvs->appends(['search' => request()->get('search'), 'institution_id' => request()->get('institution_id')])->links() }}
                </div>
            </div>
        </div>
    </div>
</div>


@endsection

@push('css')
   <!-- Bootstrap CSS -->
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
@endpush

@push('js')
   <!-- Bootstrap JS -->
   <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
@endpush
