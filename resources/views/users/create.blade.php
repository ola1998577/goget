@extends('dashboard')
@section('content')
    <div class="page-wrapper">
        <div class="content container-fluid">
            <div class="page-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="page-title">{{ __('translate.users') }}</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ url('/') }}">{{ __('translate.Dashboard') }}</a></li>
                            <li class="breadcrumb-item active">{{ __('translate.users') }}</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- DataTables Table -->
            <div class="row">
                <div class="col-md-12">
                    <form id="create-user-form">
                        @csrf
                        <div class="form-group">
                            <label>{{ __('translate.First Name') }}</label>
                            <input class="form-control" type="text" name="f_name" required>
                        </div>
                        <div class="form-group">
                            <label>{{ __('translate.Last Name') }}</label>
                            <input class="form-control" type="text" name="l_name" required>
                        </div>
                        <div class="form-group">
                            <label>{{ __('translate.Email') }}</label>
                            <input class="form-control" type="email" name="email" required>
                        </div>
                        <div class="form-group">
                            <label>{{ __('translate.Phone') }}</label>
                            <input class="form-control" type="number" min=0 name="phone" required>
                        </div>
                        <div class="form-group">
                            <label>{{ __('translate.Role') }}</label>
                            <select name="role" class="form-control">
                                @foreach ($roles as $role)
                                    <option value="{{ $role->name }}">{{ $role->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>{{ __('translate.password') }}</label>
                            <input class="form-control" type="password" name="password" required>
                        </div>
                        <button type="submit" class="btn btn-primary">{{ __('translate.Create User') }}</button>
                    </form>
                </div>
            </div>
        </div>



    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
           $(document).ready(function() {
             $('#create-user-form').submit(function(e) {
                e.preventDefault();
                $.ajax({
                    type: "POST",
                    url: "{{ route('users.store') }}",
                    data: $(this).serialize(),
                    success: function(response) {
                        $('#create-user-form')[0].reset();
                    }
                });
            });
        });
    </script>
@endsection
