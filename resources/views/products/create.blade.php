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
                </div>
            </div>

            <!-- DataTables Table -->
            <div class="row">
                <div class="col-md-12">
                    <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <!-- التصنيف -->
                        <div class="form-group">
                            <label for="category_id">Category</label>
                            <select class="form-control" name="category_id" id="category_id" required>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->translations->first()->id }}">{{ $category->translations->first()->title }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- المخزن -->
                        <div class="form-group">
                            <label for="store_id">Store</label>
                            <select class="form-control" name="store_id" id="store_id" required>
                                @foreach ($stores as $store)
                                    <option value="{{ $store->translations->first()->id }}">{{ $store->translations->first()->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- الصورة الرئيسية -->
                        <div class="form-group">
                            <label for="image">Main Image</label>
                            <input type="file" class="form-control" name="image" id="image" required>
                        </div>

                        <!-- الصور الإضافية -->
                        <div class="form-group">
                            <label for="additional_images">Additional Images</label>
                            <input type="file" class="form-control" name="additional_images[]" id="additional_images"
                                multiple>
                        </div>

                        <!-- السعر والخصم -->
                        <div class="form-group">
                            <label for="price">Price</label>
                            <input type="number" class="form-control" name="price" id="price" step="0.001"
                                required>
                        </div>

                        <div class="form-group">
                            <label for="discount">Discount (%)</label>
                            <input type="number" class="form-control" name="discount" id="discount" step="0.001">
                        </div>

                        <!-- الكمية -->
                        <div class="form-group">
                            <label for="amount">Quantity</label>
                            <input type="number" class="form-control" name="amount" id="amount" min="0"
                                required>
                        </div>

                        <!-- النوع -->
                        <div class="form-group">
                            <label for="type">Type</label>
                            <input type="text" class="form-control" name="type" id="type">
                        </div>

                        <div class="form-group" id="colors-container">
                            <label for="colors">Colors</label>
                            <div class="input-group mb-2">
                                <input type="color" class="form-control" name="colors[]" id="colors">
                                <button type="button" class="btn btn-success" id="add-color">+</button>
                            </div>
                        </div>
                        <!-- الأحجام -->
                        <div class="form-group">
                            <label for="sizes">Sizes</label>

                                <select name="sizes[]" id="sizes" class="form-control" multiple>
                                    <option value="XXS">XXS</option>
                                    <option value="XS">XS</option>
                                    <option value="S">S</option>
                                    <option value="M">M</option>
                                    <option value="L">L</option>
                                    <option value="Xl">Xl</option>
                                    <option value="XXl">XXl</option>
                                    <option value="XXXl">XXXl</option>
                                </select>
                        </div>

                        <!-- الترجمة -->
                        <div class="form-group">
                            <label for="title">Title</label>
                            <input type="text" class="form-control" name="title" id="title" required>
                        </div>

                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea class="form-control" name="description" id="description"></textarea>
                        </div>

                        <div class="form-group">
                            <label for="language">Language</label>

                                <select  class="form-control" name="language" id="language" required>
                                    <option value="en">English</option>
                                    <option value="ar">Arabic</option>
                                </select>
                        </div>

                        <button type="submit" class="btn btn-primary">Add Product</button>
                    </form>
                </div>
            </div>
        </div>



    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        document.getElementById('add-color').addEventListener('click', function () {
    const container = document.getElementById('colors-container');
    const newInputGroup = document.createElement('div');
    newInputGroup.classList.add('input-group', 'mb-2');
    newInputGroup.innerHTML = `
        <input type="color" class="form-control" name="colors[]">
        <button type="button" class="btn btn-danger remove-color">-</button>
    `;
    container.appendChild(newInputGroup);
});

// لإزالة الحقول المضافة
document.getElementById('colors-container').addEventListener('click', function (event) {
    if (event.target.classList.contains('remove-color')) {
        event.target.closest('.input-group').remove();
    }
});

    </script>
@endsection
