@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card p-5">
                <div class="card-header d-flex justify-content-between">
                    <h1>{{ __('lang.hospital_list') }}</h1>
                    <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createModal">{{ __('lang.add_new_hospital') }}</a>    
                </div>

                @if(session('success'))
                    <div class="alert alert-success">{{ __('lang.success_message') }}</div>
                @endif

                <!-- Search Form -->
                <form action="{{ route('institutions.index') }}" method="GET" class="mb-4">
                    <div class="form-group">
                        <input type="text" name="search" class="form-control" value="{{ request()->get('search') }}" placeholder="{{ __('lang.search') }}">
                    </div>
                    <button type="submit" class="btn btn-secondary">{{ __('lang.search') }}</button>
                </form>

                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>{{ __('lang.id') }}</th>
                                <th>{{ __('lang.name') }}</th>
                                <th>{{ __('lang.description') }}</th>
                                <th>{{ __('lang.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($institutions as $institution)
                                <tr>
                                    <td>{{ $institution->id }}</td>
                                    <td>{{ $institution->name }}</td>
                                    <td>{{ $institution->description }}</td>
                                    <td class="d-flex">
                                        <!-- Edit Button -->
                                        <a href="javascript:void(0)" onclick="editInstitution({{ $institution->id }})" class="btn btn-warning ms-2 me-2">{{ __('lang.edit') }}</a>
                                        
                                        <!-- Delete Button -->
                                        <form action="{{ route('institutions.destroy', $institution->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirmDelete()">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger">{{ __('lang.delete') }}</button>
                                        </form>
                                    </td>
                                </tr>

                                <!-- Edit Modal for this specific institution -->
                                <div class="modal fade" id="editModal{{$institution->id}}" tabindex="-1" aria-labelledby="editModalLabel{{$institution->id}}" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form id="editForm{{$institution->id}}" method="POST" action="{{ route('institutions.update', $institution->id) }}">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="editModalLabel{{$institution->id}}">{{ __('lang.edit_hospital') }}</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        <label for="editName{{ $institution->id }}">{{ __('lang.name') }}</label>
                                                        <input type="text" class="form-control" id="editName{{$institution->id}}" name="name" value="{{ $institution->name }}" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="editDescription{{ $institution->id }}">{{ __('lang.description') }}</label>
                                                        <textarea class="form-control" id="editDescription{{$institution->id}}" name="description" required>{{ $institution->description }}</textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('lang.close') }}</button>
                                                    <button type="submit" class="btn btn-primary">{{ __('lang.save') }}</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination links -->
                {{ $institutions->appends(['search' => request()->get('search')])->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Create Modal -->
<div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('institutions.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="createModalLabel">{{ __('lang.add_new_hospital') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="name">{{ __('lang.name') }}</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="description">{{ __('lang.description') }}</label>
                        <textarea class="form-control" name="description" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('lang.close') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('lang.save') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('css')
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
@endpush

@push('js')
<!-- Bootstrap JS and optional JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    function editInstitution(id) {
        const modalId = '#editModal' + id;
        const url = "{{ url('institutions') }}/" + id + "/edit";
        
        $.get(url, function(data) {
            // Fill the modal fields with the returned data
            $(modalId + ' #editName' + id).val(data.name);
            $(modalId + ' #editDescription' + id).val(data.description);
            
            // Show the specific modal for editing this institution
            $(modalId).modal('show');
        });
    }

    function confirmDelete() {
        return confirm('{{ __('lang.are_you_sure') }}');
    }
</script>
@endpush
