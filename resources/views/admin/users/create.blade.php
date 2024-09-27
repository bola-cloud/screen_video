@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card p-5">
                <div class="card-header">
                    <h1>{{ __('lang.create_user') }}</h1>
                </div>

                <form action="{{ route('users.store') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="name">{{ __('lang.name') }}</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="email">{{ __('lang.email') }}</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="phone">{{ __('lang.phone') }}</label>
                        <input type="text" name="phone" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="company">{{ __('lang.company') }}</label>
                        <input type="text" name="company" class="form-control">
                    </div>

                    <div class="form-group">
                        <label for="category">{{ __('lang.category') }}</label>
                        <select name="category" class="form-control" required>
                            <option value="admin">{{ __('lang.admin') }}</option>
                            <option value="client">{{ __('lang.client') }}</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="password">{{ __('lang.password') }}</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation">{{ __('lang.confirm_password') }}</label>
                        <input type="password" name="password_confirmation" class="form-control" required>
                    </div>

                    <button type="submit" class="btn btn-primary">{{ __('lang.create_user') }}</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
