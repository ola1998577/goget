@extends('dashboard')
@section('content')
    <div class="page-wrapper">
        <div class="content container-fluid">
            <div class="page-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="page-title">{{ __('translate.Role') }}</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ url('/') }}">{{ __('translate.Dashboard') }}</a></li>
                            <li class="breadcrumb-item active">{{ __('translate.Role') }}</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- DataTables Table -->
            <div class="row">
                <div class="col-md-12">
                    <form action="{{ route('roles.store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label>{{__('translate.Name')}}</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>{{__('translate.Permissions')}}</label>
                            <div class="row">
                                @foreach ($permissions as $permission)
                                <div class="col-3">
                                    <label>
                                        <input type="checkbox" name="permissions[]" value="{{ $permission->id }}">
                                        {{ $permission->name }}
                                    </label><br>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">{{ __('translate.Create Role') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
