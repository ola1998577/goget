@extends('dashboard')
@section('content')
<div class="page-wrapper">
    <div class="content container-fluid">
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">{{ __('translate.Product') }}</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ url('/') }}">{{ __('translate.Dashboard') }}</a></li>
                        <li class="breadcrumb-item active">{{ __('translate.Product') }}</li>
                    </ul>
                </div>
                <div class="col-auto float-right ml-auto">
                    <a href="{{ route('products.create') }}" class="btn add-btn">
                        <i class="fa fa-plus"></i> {{ __('translate.Create Product') }}
                    </a>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="table-responsive">
                    <table id="products-table" class="table table-striped custom-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ __('translate.Name') }}</th>
                                <th>{{ __('translate.Category') }}</th>
                                <th>{{ __('translate.Stores') }}</th>
                                <th>{{ __('translate.price') }}</th>
                                <th>{{ __('translate.quantity') }}</th>
                                <th>{{ __('translate.Colors & Sizes') }}</th>
                                <th>{{ __('translate.Action') }}</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- DataTables CSS و JavaScript -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script>
    $(document).ready(function() {
        const table = $('#products-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('products.index') }}",
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'name', name: 'translations.title' },
                { data: 'category', name: 'category.translations.title' },
                { data: 'store', name: 'store.translations.name' },
                { data: 'price', name: 'price' },
                { data: 'quantity', name: 'quantity' },
                { data: 'colors_sizes', name: 'colors_sizes', orderable: false, searchable: false },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
    initComplete: function () {
        // تعديل placeholder
        $('input[type="search"]').attr('placeholder', 'title, category, store');
    }
        });

        // Delete product
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
                        url: `/products/${id}`,
                        data: { _token: "{{ csrf_token() }}" },
                        success: function(response) {
                            table.ajax.reload();
                            Swal.fire('Deleted!', 'Product has been deleted.', 'success');
                        },
                        error: function() {
                            Swal.fire('Error!', 'Something went wrong.', 'error');
                        }
                    });
                }
            });
        });
    });
</script>
@endsection
