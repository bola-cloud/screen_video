@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <h1>{{ __('lang.dashboard') }}</h1>
            <div class="card p-4">
                <h3>{{ __('lang.statistics') }}</h3>
                <p><strong>{{ __('lang.total_tvs') }}:</strong> {{ $tvsCount }}</p>
                <p><strong>{{ __('lang.total_ads') }}:</strong> {{ $adsCount }}</p>
            </div>

            <div class="card p-4 mt-4">
                <h3>{{ __('lang.search_ads') }}</h3>
                <form method="GET" action="{{ route('dashboard') }}">
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label for="start_date">{{ __('lang.start_date') }}</label>
                            <input type="date" class="form-control" name="start_date" id="start_date" value="{{ request('start_date') }}">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="end_date">{{ __('lang.end_date') }}</label>
                            <input type="date" class="form-control" name="end_date" id="end_date" value="{{ request('end_date') }}">
                        </div>
                        <div class="form-group col-md-4">
                            <label>&nbsp;</label>
                            <button type="submit" class="btn btn-primary btn-block">{{ __('lang.search') }}</button>
                        </div>
                    </div>
                </form>

                @if($filteredAds->isNotEmpty())
                <h4>{{ __('lang.filtered_ads') }}</h4>
                <table class="table">
                    <thead>
                        <tr>
                            <th>{{ __('lang.id') }}</th>
                            <th>{{ __('lang.title') }}</th>
                            <th>{{ __('lang.brand') }}</th>
                            <th>{{ __('lang.date') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($filteredAds as $ad)
                            <tr>
                                <td>{{ $ad->id }}</td>
                                <td>{{ $ad->title }}</td>
                                <td>{{ $ad->brand }}</td>
                                <td>
                                    @foreach ($ad->schedules as $schedule)
                                        {{ $schedule->date }}<br>
                                    @endforeach
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p>{{ __('lang.no_ads_found') }}</p>
            @endif
            
            </div>
        </div>
    </div>
</div>
@endsection
