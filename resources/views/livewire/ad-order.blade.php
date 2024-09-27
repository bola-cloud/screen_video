<div class="container-fluid">
    <h2>Reorder Ads for TV #{{ $tv_id }}</h2>

    @if (session()->has('message'))
        <div class="alert alert-success">{{ session('message') }}</div>
    @endif

    <ul id="sortable-list" class="list-group">
        @foreach ($ads as $ad)
            <li wire:key="ad-{{ $ad['id'] }}" class="list-group-item" data-id="{{ $ad['id'] }}">
                <div style="cursor:move;">
                    Ad: {{ $ad['advertisement_id'] }} (Order: {{ $ad['order'] }})
                </div>
            </li>
        @endforeach
    </ul>

    <!-- Button to save the new order -->
    <button id="confirm-order-btn" class="btn btn-success mt-3" wire:click="saveOrder">
        Save Order
    </button>
</div>

@push('js')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>

    <script>
        document.addEventListener('livewire:load', function () {
            $('#sortable-list').sortable({
                update: function(event, ui) {
                    var orderedAds = [];
                    $('#sortable-list li').each(function(index, element) {
                        orderedAds.push({
                            id: $(element).data('id'),
                            order: index + 1 // Update order based on the position in the list
                        });
                    });

                    // Send reordered ads to the Livewire component
                    @this.set('ads', orderedAds);
                }
            });
        });
    </script>
@endpush
