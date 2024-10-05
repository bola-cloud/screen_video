<div class="container-fluid">
    <h2>Reorder Ads for TV #{{ $tv_id }}</h2>

    @if (session()->has('message'))
        <div class="alert alert-success">{{ session('message') }}</div>
    @endif

    <ul id="sortable-list" class="list-group">
        @foreach ($ads as $ad)
            <li wire:key="ad-{{ $ad['id'] }}" class="list-group-item">
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
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.13.0/Sortable.min.js"></script>

    <script>
        document.addEventListener('livewire:load', function () {
            let sortableList = document.getElementById('sortable-list');
            let updatedOrder = [];

            // Initialize SortableJS manually
            new Sortable(sortableList, {
                animation: 150,
                handle: 'div',
                onEnd: function () {
                    // Capture the new order when sorting is done
                    updatedOrder = Array.from(sortableList.children).map((el, index) => ({
                        id: el.getAttribute('wire:key').split('-')[1],
                        order: index + 1
                    }));

                    // Assign the updatedOrder to the Livewire property
                    Livewire.emit('setOrder', updatedOrder);
                }
            });
        });
    </script>
@endpush
