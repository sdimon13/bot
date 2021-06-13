@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">{{ __('user.create_user') }}</div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('user.add') }}">
                            @csrf

                            <div class="form-group row">
                                <label for="name"
                                       class="col-md-4 col-form-label text-md-right">{{ __('user.name') }}</label>

                                <div class="col-md-6">
                                    <input id="name" type="text"
                                           class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}"
                                           name="name" value="{{ old('name') }}" required autofocus>

                                    @if ($errors->has('name'))
                                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="telegram-id"
                                       class="col-md-4 col-form-label text-md-right">{{ __('user.telegram_id') }}</label>

                                <div class="col-md-6">
                                    <input id="telegram-id" type="number"
                                           class="form-control{{ $errors->has('telegram_id') ? ' is-invalid' : '' }}"
                                           name="telegram_id" required>

                                    @if ($errors->has('telegram_id'))
                                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('telegram_id') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group row mb-0">
                                <div class="col-md-6 offset-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        {{ __('user.add') }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="card-header">{{ __('user.users') }}</div>
                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        <table class="table">
                            <thead>
                            <tr>
                                <th scope="col">Id</th>
                                <th scope="col">Имя</th>
                                <th scope="col">Telegram Id</th>
                                <th scope="col">Почта</th>
                                <th scope="col">Тип</th>
                                <th scope="col"></th>
                                <th scope="col"></th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($users as $user)
                                <tr>
                                    <td> {{$user->id}} </td>
                                    <td> {{$user->name}} </td>
                                    <td> {{$user->telegram_id}} </td>
                                    <td> {{$user->email}} </td>
                                    <td> {{$user->type_title}} </td>
                                    <td><a href="{{ route('user.get', [$user->id]) }}"
                                           class="btn btn-xs btn-info">{{ __('user.edit') }}</a></td>
                                    <td><a href="{{ route('user.delete', [$user->id]) }}" class="btn btn-xs btn-danger"
                                           onclick="return confirm({{ __('user.are_you_shure') }})">{{ __('user.delete') }}</a>
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
