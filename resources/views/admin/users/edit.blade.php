@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card p-5">
                <div class="card-header">
                    <h1>{{ __('lang.edit_user') }}</h1>
                </div>

                <form action="{{ route('users.update', $user->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label for="name">{{ __('lang.name') }}</label>
                        <input type="text" name="name" class="form-control" value="{{ $user->name }}" required>
                    </div>

                    <div class="form-group">
                        <label for="email">{{ __('lang.email') }}</label>
                        <input type="email" name="email" class="form-control" value="{{ $user->email }}" required>
                    </div>

                    <div class="form-group">
                        <label for="phone">{{ __('lang.phone') }}</label>
                        <input type="text" name="phone" class="form-control" value="{{ $user->phone }}" required>
                    </div>

                    <div class="form-group">
                        <label for="company">{{ __('lang.company') }}</label>
                        <input type="text" name="company" class="form-control" value="{{ $user->company }}">
                    </div>

                    <div class="form-group">
                        <label for="category">{{ __('lang.category') }}</label>
                        <select name="category" class="form-control" required>
                            <option value="admin" {{ $user->category === 'admin' ? 'selected' : '' }}>{{ __('lang.admin') }}</option>
                            <option value="client" {{ $user->category === 'client' ? 'selected' : '' }}>{{ __('lang.client') }}</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="password">{{ __('lang.password') }}</label>
                        <input type="password" name="password" class="form-control">
                        <small class="text-muted">{{ __('lang.leave_blank') }}</small>
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation">{{ __('lang.confirm_password') }}</label>
                        <input type="password" name="password_confirmation" class="form-control">
                    </div>

                    <button type="submit" class="btn btn-primary">{{ __('lang.update_user') }}</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
