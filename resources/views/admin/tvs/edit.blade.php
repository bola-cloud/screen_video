@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card p-5">
                <div class="card-header d-flex justify-content-between">
                    <h1>{{ __('lang.edit_tv') }}</h1>
                    <a href="{{ route('tvs.index') }}" class="btn btn-primary">{{ __('lang.back_to_list') }}</a>    
                </div>
                
                <div class="card-body">
                    <form action="{{ route('tvs.update', $tv->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label for="name">{{ __('lang.tv_name') }}</label>
                            <input type="text" name="name" class="form-control" value="{{ $tv->name }}" required>
                        </div>
                        <div class="form-group">
                            <label for="location">{{ __('lang.location') }}</label>
                            <input type="text" name="location" class="form-control" value="{{ $tv->location }}" required>
                        </div>
                        <!-- Institution Select -->
                        <div class="form-group">
                            <label for="institution_id">{{ __('lang.select_institution') }}</label>
                            <select name="institution_id" class="form-control" required>
                                <option value="">{{ __('lang.choose_institution') }}</option>
                                @foreach($institutions as $institution)
                                    <option value="{{ $institution->id }}" {{ $tv->institution_id == $institution->id ? 'selected' : '' }}>
                                        {{ $institution->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-success">{{ __('lang.update_tv') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
