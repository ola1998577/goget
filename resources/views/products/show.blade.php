@extends('dashboard')
@section('content')
<style>
    .product-title {
    font-size: 2rem;
    font-weight: bold;
}

.main-image img {
    border: 1px solid #ddd;
    padding: 10px;
}

.gallery img {
    cursor: pointer;
    transition: transform 0.3s ease;
}

.gallery img:hover {
    transform: scale(1.1);
}

.badge.bg-primary {
    font-size: 0.9rem;
}

.list-inline-item {
    margin-right: 10px;
}

.text-muted {
    font-size: 1rem;
    color: #6c757d;
}
.main-product-image {
    width: 100%;
    max-height: 400px;
    object-fit: cover;
    border: 2px solid #ddd;
    padding: 5px;
    border-radius: 10px;
}

.gallery {
    gap: 10px;
}

.thumbnail-image {
    width: 80px;
    height: 80px;
    object-fit: cover;
    cursor: pointer;
    transition: transform 0.3s ease;
    border-radius: 5px;
}

.thumbnail-image:hover {
    transform: scale(1.1);
}

</style>
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
        <div class="container mt-5">
            <!-- عنوان المنتج -->
            <div class="row">
                <div class="col-md-8">
                    <h1 class="product-title">{{ $product->translations->first()->title }}</h1>
                    <p class="text-muted">{{ $product->translations->first()->description }}</p>
                </div>
                <div class="col-md-4 text-end">
                    <span class="badge bg-primary p-2">{{ $product->type }}</span>
                </div>
            </div>

            <!-- معرض الصور -->
            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="main-image text-center mb-3">
                        <img src="{{ asset('images/products/' . $product->image) }}" alt="{{ $product->translations->first()->title }}" class="img-fluid rounded main-product-image">
                    </div>
                    <div class="gallery d-flex flex-wrap justify-content-center">
                        @foreach ($product->images as $image)
                            <img src="{{ asset('images/products/' . $image->image) }}" alt="Additional Image" class="img-thumbnail me-2 mb-2 thumbnail-image">
                        @endforeach
                    </div>
                </div>

                <!-- معلومات المنتج -->
                <div class="col-md-6">
                    <h3>Price</h3>
                    <p class="text-success fs-4">
                        ${{ number_format($product->total_price, 2) }}
                        @if($product->discount > 0)
                            <span class="text-danger ms-2"><del>${{ number_format($product->price, 2) }}</del></span>
                        @endif
                    </p>

                    <h3>{{__('translate.Available Stock')}}</h3>
                    <p>{{ $product->amount }} {{__('translate.Units')}}</p>

                    <h3>{{__('translate.Category')}}</h3>
                    <p>{{ $product->category->translations->first()->title }}</p>

                    <h3>{{__('translate.Available Colors')}}</h3>
                    <ul class="list-inline">
                        @foreach ($product->colors as $color)
                            <li class="list-inline-item" style="background-color: {{ $color->color }}; width: 30px; height: 30px; border-radius: 50%; border: 1px solid #ddd;"></li>
                        @endforeach
                    </ul>

                    <h3>{{__('translate.Available Sizes')}}</h3>
                    <ul class="list-inline">
                        @foreach ($product->sizes as $size)
                            <li class="list-inline-item badge bg-secondary p-2">{{ $size->size }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <!-- المراجعات -->
            <div class="row mt-5">
                <div class="col-md-12">
                    <h3>{{__('translate.Customer Reviews')}}</h3>
                    <ul class="list-group">
                        @foreach ($product->reviews as $review)
                            <li class="list-group-item">
                                <strong>{{__('translate.Rating:')}}</strong> {{ $review->rate }}/5
                                <p>{{ $review->review }}</p>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <!-- معلومات المتجر -->
            <div class="row mt-5">
                <div class="col-md-12 text-center">
                    <h4>{{__('translate.Sold By:')}} {{ $product->store->translations->first()->name }}</h4>
                </div>
            </div>
        </div>


    </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

@endsection
