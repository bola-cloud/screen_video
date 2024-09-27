@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card p-5">
                <div class="card-header d-flex justify-content-between">
                    <h1>{{ __('lang.add_new_tv') }}</h1>
                    <a href="{{ route('tvs.index') }}" class="btn btn-primary">{{ __('lang.back_to_list') }}</a>    
                </div>
                
                <div class="card-body">
                    <form action="{{ route('tvs.store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="name">{{ __('lang.tv_name') }}</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                        </div>
                        <div class="form-group">
                            <label for="location">{{ __('lang.location') }}</label>
                            <input type="text" name="location" class="form-control" value="{{ old('location') }}" required>
                        </div>
                        <button type="submit" class="btn btn-success">{{ __('lang.add_tv') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
