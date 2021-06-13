@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="card">
                <div class="card-header">Последние события</div>
                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <table class="table">
                        <thead>
                        <tr>
                            <th scope="col">Время</th>
                            <th scope="col">Обьект</th>
                            <th scope="col">Событие</th>
                            <th scope="col">Зона</th>
                            <th scope="col">Устройство</th>
                            <th scope="col">Адрес</th>
                            <th scope="col">Контактные лица</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($objectEvents as $objectEvent)
                            <tr>
                                <td> {{$objectEvent->event_datetime}} </td>
                                <td> {{$objectEvent->object_number . ' ( ' . $objectEvent->object_name . ' )'}} </td>
                                <td> {{$objectEvent->description_name . ' ( ' . $objectEvent->description_comment . ' )'}} </td>
                                <td> {{$objectEvent->zone_name}} </td>
                                <td> {{$objectEvent->device_type . ' ( ' . $objectEvent->device_name . ' )'}} </td>
                                <td> {{$objectEvent->address}} </td>
                                <td> {{implode(",", ($objectEvent->contacts ?? []))}} </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
