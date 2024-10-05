@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card p-5">
                <div class="card-header d-flex justify-content-between">
                    <h1>Add Single Day to TV for Ad: {{ $ad->title }}</h1>
                    <a href="{{ route('ads.index') }}" class="btn btn-primary">Back to List</a>
                </div>
                <div class="card-body">
                    <form action="{{ route('ads.addsingleday', $ad->id) }}" method="POST">
                        @csrf
                        
                        <div class="form-group mt-4">
                            <label for="date">Select Date</label>
                            <input type="date" name="date" class="form-control" required>
                        </div>

                        <!-- Tabs for TVs -->
                        <ul class="nav nav-tabs mt-5" id="tvTabs" role="tablist">
                            @foreach ($institutions as $institution)
                                <li class="nav-item">
                                    <a class="nav-link {{ $loop->first ? 'active' : '' }}" id="tab-{{ $institution->id }}" data-bs-toggle="tab" href="#institution-{{ $institution->id }}" role="tab">{{ $institution->name }}</a>
                                </li>
                            @endforeach
                        </ul>

                        <!-- Tab content -->
                        <div class="tab-content mt-3" id="tvTabContent">
                            @foreach ($institutions as $institution)
                                <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="institution-{{ $institution->id }}" role="tabpanel">
                                    
                                    <div class="row">
                                        @foreach ($institution->tvs as $tv)
                                            <div class="col-md-2">
                                                <div class="tv-screen">
                                                    <div class="tv-name">{{ $tv->name }}</div>
                                                    <div class="checkbox-wrapper">
                                                        <input type="checkbox" id="tv-{{ $tv->id }}" name="tvs[]" value="{{ $tv->id }}">
                                                        <label for="tv-{{ $tv->id }}"></label>
                                                    </div>
                                                </div>
                                                <div class="text-center">{{ $tv->location }}</div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="btn btn-success mt-4">Add Single Day</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Select/Deselect all TVs in an institution tab
        document.querySelectorAll('.select-all-btn').forEach(button => {
            button.addEventListener('click', function () {
                const institutionId = this.getAttribute('data-institution-id');
                const checkboxes = document.querySelectorAll(`#institution-${institutionId} input[type="checkbox"]`);
                const selectAll = this.textContent.trim() === "Select All";
                checkboxes.forEach(checkbox => checkbox.checked = selectAll);
                this.textContent = selectAll ? "Deselect All" : "Select All";
            });
        });
    });
</script>
@endsection
