@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <h1>{{ __('lang.dashboard') }}</h1>

            <div class="card p-4">
                <h3>{{ __('lang.search_ad_to_display_tvs') }}</h3>
                <!-- Search Form for Selecting an Advertisement -->
                <form method="GET" action="{{ route('dashboard') }}">
                    <div class="form-row">
                        <div class="form-group col-md-8">
                            <label for="ad">{{ __('lang.select_ad') }}</label>
                            <select name="ad" id="ad" class="form-control select2" required>
                                <option value="">{{ __('lang.select_ad') }}</option>
                                @foreach ($ads as $ad)
                                    <option value="{{ $ad->id }}" {{ request('ad') == $ad->id ? 'selected' : '' }}>
                                        {{ $ad->title }} - {{ $ad->brand }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-4">
                            <label>&nbsp;</label>
                            <button type="submit" class="btn btn-primary btn-block">{{ __('lang.search') }}</button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Display TVs and their schedules for the selected ad -->
            @if ($selectedAd && $selectedAd->schedules->isNotEmpty())
                <div class="card p-4 mt-4">
                    <h4>{{ __('lang.ad_name') }}: {{ $selectedAd->title }} - {{ $selectedAd->brand }}</h4>

                    <!-- TV Tabs -->
                    <ul class="nav nav-tabs" id="tvTabs" role="tablist">
                        @foreach ($selectedTvs as $tv)
                            <li class="nav-item">
                                <a class="nav-link {{ $loop->first ? 'active' : '' }}" id="tv-tab-{{ $tv->id }}" data-bs-toggle="tab" href="#tv-{{ $tv->id }}" role="tab" aria-controls="tv-{{ $tv->id }}" aria-selected="true">{{ $tv->name }}</a>
                            </li>
                        @endforeach
                    </ul>

                    <!-- Tab Content -->
                    <div class="tab-content mt-4" id="tvTabContent">
                        @foreach ($selectedTvs as $tv)
                            <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="tv-{{ $tv->id }}" role="tabpanel" aria-labelledby="tv-tab-{{ $tv->id }}">
                                <h5>{{ __('lang.tv_name') }}: {{ $tv->name }}</h5>
                                <p>{{ __('lang.location') }}: {{ $tv->location }}</p>
                                @if ($tv->schedules->isNotEmpty())
                                    @foreach ($tv->schedules->where('advertisement_id', $selectedAd->id)->groupBy('date') as $date => $schedules)
                                        <h6>{{ __('lang.date') }}: {{ $date }}</h6>
                                        <ul>
                                            @foreach ($schedules as $schedule)
                                                <li>
                                                    {{ __('lang.times') }}:
                                                    <ul>
                                                        @foreach ($schedule->displayTimes as $displayTime)
                                                            <li>{{ $displayTime->display_time }}</li>
                                                        @endforeach
                                                    </ul>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endforeach
                                @else
                                    <p>{{ __('lang.no_schedules_found') }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @elseif($selectedAd)
                <p>{{ __('lang.no_schedules_found_for_ad') }}</p>
            @endif
        </div>
    </div>
</div>
@endsection

@push('css')
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
@endpush

@push('js')
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    $(document).ready(function() {
        $('.select2').select2();
    });
</script>
@endpush
