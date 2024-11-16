@extends('dashboard')
@section('content')
    <div class="page-wrapper">
        <div class="content container-fluid">
            <div class="page-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="page-title">{{ __('translate.Child Categories of') }} {{ $category->name }}</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ url('/') }}">{{ __('translate.Dashboard') }}</a></li>
                            <li class="breadcrumb-item active">{{ __('translate.Child Categories of') }}</li>
                        </ul>
                    </div>
                    <div class="col-auto float-right ml-auto">
                        <a href="#" class="btn add-btn" data-toggle="modal" data-target="#create_category_modal">
                            <i class="fa fa-plus"></i> {{ __('translate.Add Child Category') }}
                        </a>
                    </div>
                </div>
            </div>

            <!-- DataTables Table -->
            <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table id="children-table" class="table table-striped custom-table">
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

        <!-- Create Category Modal -->
        <div id="create_category_modal" class="modal custom-modal fade" role="dialog">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('translate.Create Child Category') }}</h5>
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <form id="create-category-form" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="parent_id" value="{{ $category->id }}">
                            <div class="form-group">
                                <label>{{ __('translate.Name') }} (English)</label>
                                <input class="form-control" type="text" name="name[en]" required>
                            </div>
                            <div class="form-group">
                                <label>{{ __('translate.Name') }} (Arabic)</label>
                                <input class="form-control" type="text" name="name[ar]" required>
                            </div>
                            <div class="form-group">
                                <label>{{ __('translate.Image') }}</label>
                                <input type="file" class="form-control" name="image" accept="image/*" required>
                            </div>
                            <button type="submit" class="btn btn-primary">{{ __('translate.Create Category') }}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Category Modal -->
        <div id="edit_category_modal" class="modal custom-modal fade" role="dialog">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('translate.Edit Category') }}</h5>
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <form id="edit-category-form" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="id" id="edit-category-id">
                            <input type="hidden" name="parent_id" value="{{ $category->id }}">

                            <div class="form-group">
                                <label>{{ __('translate.Name') }} (English)</label>
                                <input class="form-control" type="text" name="name[en]" id="edit-category-name-en" required>
                            </div>
                            <div class="form-group">
                                <label>{{ __('translate.Name') }} (Arabic)</label>
                                <input class="form-control" type="text" name="name[ar]" id="edit-category-name-ar" required>
                            </div>
                            <div class="form-group">
                                <label>{{ __('translate.Image') }}</label>
                                <input type="file" class="form-control" name="image" accept="image/*">
                            </div>
                            <div class="form-group">
                                <img id="edit-category-image" src="" alt="Category Image"
                                    style="max-width: 100%; margin-top: 10px; display: none;">
                            </div>
                            <button type="submit" class="btn btn-primary">{{ __('translate.Save Changes') }}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
      <!-- DataTables and AJAX Scripts -->
      <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
      <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
      <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
      <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
      <script>
    $(document).ready(function() {
        // Initialize DataTable
        const table = $('#children-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route("categories.children.data", $category->id) }}',
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'name', name: 'name' },
                { data: 'created_at', name: 'created_at' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ]
        });

        // Create Category AJAX
        $('#create-category-form').submit(function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            $.ajax({
                type: "POST",
                url: "{{ route('categories.store') }}",
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    $('#create_category_modal').modal('hide');
                    $('#create-category-form')[0].reset();
                    table.ajax.reload();
                },
                error: function(xhr) {
                    // Handle errors if needed
                    alert('Error occurred while creating category.');
                }
            });
        });

        // Open Edit Modal and Load Category Data
        $(document).on('click', '.editCategory', function() {
            const id = $(this).data('id');

            $.get(`/categories/${id}/edit`, function(category) {
                $('#edit-category-id').val(category.id);
                $('#edit-category-name-en').val(category.translations.find(t => t.language === 'en').title);
                $('#edit-category-name-ar').val(category.translations.find(t => t.language === 'ar').title);
                if (category.image) {
                    $('#edit-category-image').attr('src', '{{ asset('images') }}/' + category.image).show();
                } else {
                    $('#edit-category-image').hide();
                }
                $('#edit_category_modal').modal('show');
            });
        });

        $('#edit-category-form').submit(function(e) {
                e.preventDefault();
                const id = $('#edit-category-id').val();
                $.ajax({
                    type: "PUT",
                    url: `/categories/${id}`,
                    data: $(this).serialize(),
                    success: function(response) {
                        $('#edit_category_modal').modal('hide');
                        table.ajax.reload();
                    }
                });
            });

        // Delete Category AJAX
        $(document).on('click', '.deleteCategory', function() {
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
                        url: `/categories/${id}`,
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            table.ajax.reload();
                            Swal.fire('Deleted!', 'Category has been deleted.', 'success');
                        },
                        error: function(xhr) {
                            Swal.fire('Error!', 'Something went wrong.', 'error');
                        }
                    });
                }
            });
        });
    });
</script>
@endsection
