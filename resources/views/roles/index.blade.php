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
                <div class="col-auto float-right ml-auto">
                    <a href="#" class="btn add-btn" data-toggle="modal" data-target="#create_role_modal"><i class="fa fa-plus"></i> {{ __('translate.Create Role') }}</a>
                </div>
            </div>
        </div>

        <!-- DataTables Table -->
        <div class="row">
            <div class="col-md-12">
                <div class="table-responsive">
                    <table id="roles-table" class="table table-striped custom-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ __('translate.Name') }}</th>
                                <th>{{ __('translate.Created Date') }}</th>
                                <th>{{ __('translate.Action') }}</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Role Modal -->
    <div id="create_role_modal" class="modal custom-modal fade" role="dialog">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('translate.Create Role') }}</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <form id="create-role-form">
                        @csrf
                        <div class="form-group">
                            <label>{{ __('translate.Name') }}</label>
                            <input class="form-control" type="text" name="name" required>
                        </div>
                        <div class="form-group">
                            <label>{{ __('translate.Permissions') }}</label>
                            <select name="permissions[]" class="form-control select2" multiple required>
                                @foreach ($permissions as $permission)
                                    <option value="{{ $permission->id }}">{{ $permission->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">{{ __('translate.Create Role') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Role Modal -->
    <div id="edit_role_modal" class="modal custom-modal fade" role="dialog">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('translate.Edit Role') }}</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <form id="edit-role-form">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="id" id="edit-role-id">
                        <div class="form-group">
                            <label>{{ __('translate.Name') }}</label>
                            <input class="form-control" type="text" name="name" id="edit-role-name" required>
                        </div>
                        <div class="form-group">
                            <label>{{ __('translate.Permissions') }}</label>
                            <select name="permissions[]" id="edit-permissions" class="form-control select2" multiple required>
                                @foreach ($permissions as $permission)
                                    <option value="{{ $permission->id }}">{{ $permission->name }}</option>
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
        const table = $('#roles-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('roles.index') }}"
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'name', name: 'name' },
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

        // Create Role AJAX
        $('#create-role-form').submit(function(e) {
            e.preventDefault();
            $.ajax({
                type: "POST",
                url: "{{ route('roles.store') }}",
                data: $(this).serialize(),
                success: function(response) {
                    $('#create_role_modal').modal('hide');
                    $('#create-role-form')[0].reset();
                    table.ajax.reload();
                }
            });
        });

        // Open Edit Modal and Load Role Data
        $(document).on('click', '.edit', function() {
            const id = $(this).data('id');
            $.get(`/roles/${id}/edit`, function(role) {
                $('#edit-role-id').val(role.id);
                $('#edit-role-name').val(role.name);
                $('#edit-permissions').val(role.permissions.map(permission => permission.id)).trigger('change');
                $('#edit_role_modal').modal('show');
            });
        });

        // Update Role AJAX
        $('#edit-role-form').submit(function(e) {
            e.preventDefault();
            const id = $('#edit-role-id').val();
            $.ajax({
                type: "PUT",
                url: `/roles/${id}`,
                data: $(this).serialize(),
                success: function(response) {
                    $('#edit_role_modal').modal('hide');
                    table.ajax.reload();
                }
            });
        });

        // Delete Role AJAX
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
                        url: `/roles/${id}`,
                        data: { _token: "{{ csrf_token() }}" },
                        success: function(response) {
                            table.ajax.reload();
                            Swal.fire(
                                'Deleted!',
                                'Role has been deleted.',
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
