@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h1>{{ __('lang.client_reports') }}</h1>

            <!-- Search Form for Selecting a Client -->
            <div class="card p-4">
                <h3>{{ __('lang.search_client') }}</h3>
                <form method="GET" action="{{ route('client.reports') }}">
                    <div class="form-row">
                        <div class="form-group col-md-8">
                            <label for="client">{{ __('lang.select_client') }}</label>
                            <select name="client" id="client" class="form-control select2" required>
                                <option value="">{{ __('lang.select_client') }}</option>
                                @foreach ($clients as $client)
                                    <option value="{{ $client->id }}" {{ request('client') == $client->id ? 'selected' : '' }}>
                                        {{ $client->name }} - {{ $client->email }}
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

            <!-- Display Ads for the Selected Client -->
            @if ($selectedClient && $ads->isNotEmpty())
                <div class="card p-4 mt-5">
                    <h4>{{ __('lang.client_name') }}: {{ $selectedClient->name }} - {{ $selectedClient->email }}</h4>
                    <button class="btn btn-primary mb-4" onclick="printReport()">{{ __('lang.print_report') }}</button>
                    
                    <!-- Report Content -->
                    <div id="report-content">
                        @foreach ($ads as $ad)
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h3>{{ __('lang.ad_name') }}: {{ $ad->title }} ({{ $ad->brand }})</h3>
                                </div>
                                <div class="card-body">
                                    <h5>{{ __('lang.tvs_displayed_on') }}</h5>
                                    @if ($ad->schedules->isNotEmpty())
                                        <ul class="list-group mt-3">
                                            @foreach ($ad->schedules->groupBy('tv.id') as $tvSchedules)
                                                <li class="list-group-item mt-5">
                                                    <strong>{{ __('lang.tv_name') }}: {{ $tvSchedules->first()->tv->name }}</strong><br>
                                                    <strong>{{ __('lang.location') }}: {{ $tvSchedules->first()->tv->location }}</strong>
                                                    <ul class="ml-4 mt-2">
                                                        @foreach ($tvSchedules as $schedule)
                                                            <li>
                                                                <strong>{{ __('lang.date') }}: {{ $schedule->date }}</strong><br>
                                                                <ul>
                                                                    @foreach ($schedule->displayTimes as $displayTime)
                                                                        <li>{{ $displayTime->display_time }}</li>
                                                                    @endforeach
                                                                </ul>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <p>{{ __('lang.no_schedules_found') }}</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @elseif($selectedClient)
                <p>{{ __('lang.no_ads_found_for_client') }}</p>
            @endif
        </div>
    </div>
</div>

<script>
    function printReport() {
        var content = document.getElementById('report-content').innerHTML;
        var printWindow = window.open('', '_blank');
        printWindow.document.write('<html><head><title>{{ __('lang.client_report') }}</title>');
        printWindow.document.write('<link rel="stylesheet" href="{{ asset('css/app.css') }}">');
        printWindow.document.write('</head><body>');
        printWindow.document.write(content);
        printWindow.document.write('</body></html>');
        printWindow.document.close();
        printWindow.print();
    }
</script>
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
