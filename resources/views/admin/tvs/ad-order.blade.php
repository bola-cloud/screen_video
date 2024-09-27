@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <h2>{{ __('lang.reorder_ads_for_tv', ['tv_id' => $tv_id]) }}</h2>

    @if (session()->has('message'))
        <div class="alert alert-success">{{ session('message') }}</div>
    @endif

    <!-- Date Search Form -->
    <form id="date-search-form" action="{{ route('tv.ad-order', $tv_id) }}" method="GET">
        <div class="row mb-4">
            <div class="col-md-4">
                <label for="date" class="form-label">{{ __('lang.select_date') }}</label>
                <input type="date" id="date" name="date" class="form-control" value="{{ request('date') }}">
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary mt-4">{{ __('lang.search') }}</button>
            </div>
        </div>
    </form>

    <!-- Ads List (Displayed after searching with a date) -->
    @if(!empty($ads))
        <ul id="sortable-list" class="list-group">
            @foreach ($ads as $ad)
                <li class="list-group-item" data-id="{{ $ad['id'] }}">
                    <div style="cursor:move;">
                        {{ __('lang.ad_title_with_order', ['title' => $ad['advertisement']['title'], 'order' => $ad['order']]) }}
                    </div>
                </li>
            @endforeach
        </ul>

        <!-- Button to save the new order -->
        <button id="confirm-order-btn" class="btn btn-success mt-3">
            {{ __('lang.save_order') }}
        </button>
    @else
        <p>{{ __('lang.no_ads_available') }}</p>
    @endif
</div>
@endsection

@push('js')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>

    <script>
        $(document).ready(function() {
            // Enable sortable functionality on the list
            $('#sortable-list').sortable();

            // Save button click event
            $('#confirm-order-btn').click(function() {
                var orderedAds = [];
                $('#sortable-list li').each(function(index, element) {
                    orderedAds.push({
                        id: $(element).data('id'),
                        order: index + 1 // New order
                    });
                });

                // Send AJAX request to update order
                $.ajax({
                    type: 'POST',
                    url: '{{ route('admin.ads.updateOrder') }}',
                    data: {
                        order: orderedAds,
                        date: '{{ request('date') }}', // Include the selected date
                  		tv_id: {{ $tv_id }}, // Include the TV ID
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if(response.success) {
                            alert(response.message);
                            location.reload(); // Reload the page to reflect changes
                        }
                    },
                    error: function(error) {
                        console.log('Error updating order:', error);
                    }
                });
            });
        });
    </script>
@endpush
