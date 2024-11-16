@extends('dashboard')
@section('content')
    <div class="page-wrapper">
        <div class="content container-fluid">
            <div class="page-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="page-title">{{ __('translate.Edit_Product') }}</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ url('/') }}">{{ __('translate.Dashboard') }}</a></li>
                            <li class="breadcrumb-item active">{{ __('translate.Edit_Product') }}</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- DataTables Table -->
            <div class="row">
                <div class="col-md-12">
                    <form action="{{ route('products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- التصنيف -->
                        <div class="form-group">
                            <label for="category_id">Category</label>
                            <select class="form-control" name="category_id" id="category_id" required>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" {{ $product->category_id == $category->id ? 'selected' : '' }}>
                                        {{ $category->translations->first()->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- المخزن -->
                        <div class="form-group">
                            <label for="store_id">Store</label>
                            <select class="form-control" name="store_id" id="store_id" required>
                                @foreach ($stores as $store)
                                    <option value="{{ $store->id }}" {{ $product->store_id == $store->id ? 'selected' : '' }}>
                                        {{ $store->translations->first()->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- الصورة الرئيسية -->
                        <div class="form-group">
                            <label for="image">Main Image</label>
                            <input type="file" class="form-control" name="image" id="image">
                            <small>Current Image:</small>
                            <img src="{{ asset('images/products/' . $product->image) }}" alt="Product Image" width="100">
                        </div>

                        <div class="form-group">
                            <label for="additional_images">Additional Images</label>
                            <input type="file" class="form-control" name="additional_images[]" id="additional_images" multiple>
                            @php
                            $images = $product->images; // الحصول على الألوان المرتبطة بالمنتج
                        @endphp
                       @if ($product->images->isNotEmpty())
                       <div class="current-images">
                           @foreach ($product->images as $image)
                               <div class="image-container" style="display: inline-block; position: relative;">
                                   <img src="{{ asset('images/products/' . $image->image) }}" alt="Additional Image" width="50">
                                   <div>
                                       <input type="checkbox" name="remove_images[]" value="{{ $image->id }}">
                                       <label>{{ __('translate.Remove') }}</label>
                                   </div>
                               </div>
                           @endforeach
                       </div>
                   @endif
                        </div>

                        <!-- السعر والخصم -->
                        <div class="form-group">
                            <label for="price">Price</label>
                            <input type="number" class="form-control" name="price" id="price" value="{{ $product->price }}" step="0.001" required>
                        </div>

                        <div class="form-group">
                            <label for="discount">Discount (%)</label>
                            <input type="number" class="form-control" name="discount" id="discount" value="{{ $product->discount }}" step="0.001">
                        </div>

                        <!-- الكمية -->
                        <div class="form-group">
                            <label for="amount">Quantity</label>
                            <input type="number" class="form-control" name="amount" id="amount" value="{{ $product->amount }}" min="0" required>
                        </div>

                        <!-- النوع -->
                        <div class="form-group">
                            <label for="type">Type</label>
                            <input type="text" class="form-control" name="type" id="type" value="{{ $product->type }}">
                        </div>

                        <!-- الألوان -->
                        <div class="form-group" id="colors-container">
                            <label for="colors">Colors</label>
                            @php
                            $colors = $product->colors; // الحصول على الألوان المرتبطة بالمنتج
                        @endphp

                        @if ($colors->isNotEmpty())
                            @foreach ($colors as $color)
                                <div class="input-group mb-2">
                                    <input type="color" class="form-control" name="colors[]" value="{{ $color->color }}">
                                    <button type="button" class="btn btn-danger remove-color">-</button>
                                </div>
                            @endforeach
                        @else
                            <div class="input-group mb-2">
                                <input type="color" class="form-control" name="colors[]" value="#000000"> <!-- لون افتراضي -->
                                <button type="button" class="btn btn-danger remove-color">-</button>
                            </div>
                        @endif
                            <button type="button" class="btn btn-success" id="add-color">+ Add Color</button>
                        </div>

                        <!-- الأحجام -->
                        <div class="form-group">
                            <label for="sizes">Sizes</label>
                            @php
                              $savedSizes = $product->sizes->pluck('size')->toArray();

                            @endphp
                            <select name="sizes[]" id="sizes" class="form-control" multiple>
                                @foreach (['XXS', 'XS', 'S', 'M', 'L', 'XL', 'XXL', 'XXXL'] as $size)
                                <option value="{{ $size }}"
                                    {{ in_array($size, $savedSizes) ? 'selected' : '' }}>
                                    {{ $size }}
                                </option>
                            @endforeach
                            </select>
                        </div>

                        <!-- الترجمة -->
                        <div class="form-group">
                            <label for="title">Title</label>
                            <input type="text" class="form-control" name="title" id="title" value="{{ $product->translations->first()->title }}" required>
                        </div>

                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea class="form-control" name="description" id="description">{{ $product->translations->first()->description }}</textarea>
                        </div>

                        <div class="form-group">
                            <label for="language">Language</label>
                            <select class="form-control" name="language" id="language" required>
                                <option value="en" {{ $product->language == 'en' ? 'selected' : '' }}>English</option>
                                <option value="ar" {{ $product->language == 'ar' ? 'selected' : '' }}>Arabic</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary">Update Product</button>
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

        document.getElementById('colors-container').addEventListener('click', function (event) {
            if (event.target.classList.contains('remove-color')) {
                event.target.closest('.input-group').remove();
            }
        });
    </script>
@endsection
