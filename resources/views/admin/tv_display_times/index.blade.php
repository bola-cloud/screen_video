@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card p-5">
                <div class="card-header d-flex justify-content-between">
                    <h1>{{ __('lang.tv_display_times') }}</h1>
                    <a href="{{ route('tv_display_times.create') }}" class="btn btn-primary">{{ __('lang.add_new_display_time') }}</a>    
                </div>

                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <!-- Search and Filter Form -->
                <form action="{{ route('tv_display_times.index') }}" method="GET" class="mb-4">
                    <div class="row">
                        <!-- TV Name Search -->
                        <div class="col-md-3">
                            <input type="text" name="tv_name" class="form-control" placeholder="{{ __('lang.search_by_tv_name') }}" value="{{ request()->get('tv_name') }}">
                        </div>

                        <!-- Date Range Filter -->
                        <div class="col-md-3">
                            <input type="date" name="start_date" class="form-control" placeholder="{{ __('lang.start_date') }}" value="{{ request()->get('start_date') }}">
                        </div>
                        <div class="col-md-3">
                            <input type="date" name="end_date" class="form-control" placeholder="{{ __('lang.end_date') }}" value="{{ request()->get('end_date') }}">
                        </div>

                        <!-- Time Filter -->
                        <div class="col-md-3">
                            <input type="time" name="start_time" class="form-control" placeholder="{{ __('lang.start_time') }}" value="{{ request()->get('start_time') }}">
                        </div>
                        <div class="col-md-3 mt-3">
                            <input type="time" name="end_time" class="form-control" placeholder="{{ __('lang.end_time') }}" value="{{ request()->get('end_time') }}">
                        </div>

                        <!-- Search Button -->
                        <div class="col-md-3 mt-3">
                            <button type="submit" class="btn btn-secondary">{{ __('lang.search') }}</button>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>{{ __('lang.id') }}</th>
                                <th>{{ __('lang.tv') }}</th>
                                <th>{{ __('lang.date') }}</th>
                                <th>{{ __('lang.start_time') }}</th>
                                <th>{{ __('lang.end_time') }}</th>
                                <th>{{ __('lang.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($displayTimes as $index => $displayTime)
                                <tr>
                                    <td>{{ $index+1 }}</td>
                                    <td>{{ $displayTime->tv->name }}</td>
                                    <td>{{ $displayTime->date }}</td>
                                    <td>{{ $displayTime->start_time }}</td>
                                    <td>{{ $displayTime->end_time }}</td>
                                    <td class="d-flex">
                                        <a href="{{ route('tv_display_times.edit', $displayTime->id) }}" class="btn btn-warning mr-2 ml-2">{{ __('lang.edit') }}</a>
                                        <form action="{{ route('tv_display_times.destroy', $displayTime->id) }}" method="POST" style="display:inline-block;">
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

                <!-- Pagination Links -->
                {{ $displayTimes->appends(request()->query())->links() }}

            </div>
        </div>
    </div>
</div>
@endsection
