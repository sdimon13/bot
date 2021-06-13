@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">Карточка пользователя {{$user->name}}
                        @if ($user->type_id == 1)
                            <a href="{{ route('user.upgrade', [$user->id]) }}"
                               class="btn btn-xs btn-info me-1">{{ __('user.upgrade') }}</a>
                        @endif
                    </div>
                    <div class="card-header">Добавить обьект</div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('user.object.add') }}">
                            @csrf

                            <select class="form-select form-select-lg mb-3 w-100 p-2"
                                    aria-label="Default select example" name="object_id">
                                @foreach($objects as $object)
                                    <option value="{{$object->ID}}">{{'( '. $object->number . ' ) ' .$object->NAME}}</option>
                                @endforeach
                            </select>

                            <input type="hidden" value="{{$user->id}}" name="user_id">

                            @if ($errors->has('object-id'))
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('object-id') }}</strong>
                                    </span>
                            @endif

                            <div class="form-group row mb-0">
                                <div class="mx-auto">
                                    <button type="submit" class="btn btn-primary">
                                        {{ __('object.add') }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="card-header">Отслеживаемые обьекты</div>
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
                                <th scope="col"></th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($user->objects as $userObject)
                                <tr>
                                    <td> {{$userObject->number}} </td>
                                    <td> {{$userObject->name}} </td>
                                    <td><a href="{{ route('user.object.delete', [$userObject->id]) }}"
                                           class="btn btn-xs btn-danger"
                                           onclick="return confirm({{ __('user.are_you_shure') }})">Delete</a>
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
