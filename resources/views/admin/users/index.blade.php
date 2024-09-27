@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card p-5">
                <div class="card-header d-flex justify-content-between">
                    <h1>{{ __('lang.user_list') }}</h1>
                    <a href="{{ route('users.create') }}" class="btn btn-primary">{{ __('lang.add_new_user') }}</a>    
                </div>

                @if(session('success'))
                    <div class="alert alert-success">{{ __('lang.success_message') }}</div>
                @endif
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>{{ __('lang.id') }}</th>
                                <th>{{ __('lang.name') }}</th>
                                <th>{{ __('lang.email') }}</th>
                                <th>{{ __('lang.phone') }}</th>
                                <th>{{ __('lang.category') }}</th>
                                <th>{{ __('lang.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $user)
                                <tr>
                                    <td>{{ $user->id }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->phone }}</td>
                                    <td>{{ ucfirst($user->category) }}</td>
                                    <td class="d-flex">
                                        <a href="{{ route('users.edit', $user->id) }}" class="btn btn-warning">{{ __('lang.edit') }}</a>
                                        <form action="{{ route('users.destroy', $user->id) }}" method="POST" style="display:inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger ml-2">{{ __('lang.delete') }}</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
