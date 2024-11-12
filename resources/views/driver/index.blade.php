@extends('dashboard')
@section('content')
    <div class="page-wrapper">
        <div class="content container-fluid">
            <div class="page-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="page-title">{{ __('translate.Driver') }}</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ url('/') }}">{{ __('translate.Dashboard') }}</a></li>
                            <li class="breadcrumb-item active">{{ __('translate.Driver') }}</li>
                        </ul>
                    </div>
                    <div class="col-auto float-right ml-auto">
                        <a href="#" class="btn add-btn" data-toggle="modal" data-target="#create_user_modal"><i class="fa fa-plus"></i> {{ __('translate.Create Driver') }}</a>
                    </div>
                </div>
            </div>

            <!-- DataTables Table -->
            <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table id="users-table" class="table table-striped custom-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('translate.Name') }}</th>
                                    <th>{{ __('translate.Phone') }}</th>
                                    <th>{{ __('translate.Company') }}</th>
                                    <th>{{ __('translate.Created Date') }}</th>
                                    <th>{{ __('translate.Action') }}</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Create User Modal -->
        <div id="create_user_modal" class="modal custom-modal fade" role="dialog">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('translate.Create Driver') }}</h5>
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <form id="create-user-form">
                            @csrf
                            <div class="form-group">
                                <label>{{ __('translate.Name') }}</label>
                                <input class="form-control" type="text" name="name" required>
                            </div>
                            <div class="form-group">
                                <label>{{ __('translate.Phone') }}</label>
                                <input class="form-control" type="number" min=0 name="phone" required>
                            </div>
                            <div class="form-group">
                                <label>{{ __('translate.Company') }}</label>
                                <select name="deliveryCompany_id" class="form-control">
                                    @foreach ($companies as $company)
                                        <option value="{{ $company->id }}">{{ $company->f_name }}{{ $company->l_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary">{{ __('translate.Create Driver') }}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit User Modal -->
<div id="edit_user_modal" class="modal custom-modal fade" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('translate.Edit User') }}</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <form id="edit-user-form">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="id" id="edit-user-id">
                    <div class="form-group">
                        <label>{{ __('translate.Name') }}</label>
                        <input class="form-control" type="text" name="name" id="edit-f_name" required>
                    </div>
                    <div class="form-group">
                        <label>{{ __('translate.Phone') }}</label>
                        <input class="form-control" type="number" name="phone" min=0 id="edit-phone" required>
                    </div>
                    <div class="form-group">
                        <label>{{ __('translate.Company') }}</label>
                        <select name="deliveryCompany_id" id="edit-role" class="form-control">
                            @foreach ($companies as $company)
                                <option value="{{ $company->id }}">{{ $company->f_name }}{{$company->l_name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">{{ __('translate.Save Changes') }}</button>
                </form>
            </div>
        </div>
    </div>
</div>

    </div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- DataTables CSS و JavaScript -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize DataTable with AJAX
            const table = $('#users-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
            url: "{{ route('drivers.index') }}"

        },                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false }, // عمود الرقم المتسلسل
                    { data: 'name', name: 'name' },
                    { data: 'phone', name: 'phone' },
                    { data: 'company', name: 'company'},
                    { data: 'created_at', name: 'created_at' },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: true,
                        className: 'text-right'
                    }
                ]
            });

            // Create User AJAX
            $('#create-user-form').submit(function(e) {
                e.preventDefault();
                $.ajax({
                    type: "POST",
                    url: "{{ route('drivers.store') }}",
                    data: $(this).serialize(),
                    success: function(response) {
                        $('#create_user_modal').modal('hide');
                        $('#create-user-form')[0].reset();
                        table.ajax.reload();
                    }
                });
            });

            // Open Edit Modal and Load User Data
            $(document).on('click', '.edit', function() {
    const id = $(this).data('id');
    $.get(`/drivers/${id}/edit`, function(user) {
        $('#edit-user-id').val(user.id);
        $('#edit-f_name').val(user.name);
        $('#edit-phone').val(user.phone);
        $('#edit-role').val(user.delivery_company ? user.delivery_company.id : '');

        $('#edit_user_modal').modal('show');
    });
});

            // Update User AJAX
            $('#edit-user-form').submit(function(e) {
                e.preventDefault();
                const id = $('#edit-user-id').val();
                $.ajax({
                    type: "PUT",
                    url: `/drivers/${id}`,
                    data: $(this).serialize(),
                    success: function(response) {
                        $('#edit_user_modal').modal('hide');
                        table.ajax.reload();
                    }
                });
            });

            // Delete User AJAX
            $(document).on('click', '.delete', function() {
    const id = $(this).data('id');
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                type: "DELETE",
                url: `/drivers/${id}`,
                data: { _token: "{{ csrf_token() }}" },
                success: function(response) {
                    // Reload the table or show success message
                    table.ajax.reload();
                    Swal.fire(
                        'Deleted!',
                        'Your file has been deleted.',
                        'success'
                    );
                },
                error: function(xhr) {
                    Swal.fire(
                        'Error!',
                        'Something went wrong.',
                        'error'
                    );
                }
            });
        }
    });
});
        });
    </script>
@endsection
