@extends('frontend.layouts.master')
@section('content')
    <div class="container">
        <!-- HERO SECTION-->
        <section class="py-5 bg-light">
            <div class="container">
                <div class="row px-4 px-lg-5 py-lg-4 align-items-center">
                    <div class="col-lg-6">
                        <h1 class="h2 text-uppercase mb-0">Shop</h1>
                    </div>
                    <div class="col-lg-6 text-lg-right">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb justify-content-lg-end mb-0 px-0">
                                <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Shop</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </section>
        <section class="py-5">
            <div class="container p-0">
                <div class="row">
                    <!-- SHOP SIDEBAR-->
                    <div class="col-lg-3 order-2 order-lg-1">
                        <h5 class="text-uppercase mb-4">Categories</h5>
                        @foreach ($categories as $category)
                            <ul class="list-unstyled small text-muted pl-lg-4 font-weight-normal">
                                <li class="mb-2">
                                    <a class="reset-anchor" href="{{ route('shop', ['category' => $category->id]) }}">
                                        {{ $category->name }}
                                    </a>
                                </li>
                            </ul>
                        @endforeach

                        <form method="GET" action="{{ route('shop') }}">
                            <h6 class="text-uppercase mb-4">Price range</h6>
                            <div class="price-range pt-4 mb-5">
                                <div id="range"></div>
                                <div class="row pt-2">
                                    <div class="col-6">
                                        <strong class="small font-weight-bold text-uppercase">
                                            From: <span id="min_price_val">${{ $minPrice }}</span>
                                        </strong>
                                    </div>
                                    <div class="col-6 text-right">
                                        <strong class="small font-weight-bold text-uppercase">
                                            To: <span id="max_price_val">${{ $maxPrice }}</span>
                                        </strong>
                                    </div>
                                </div>
                            </div>

                            <input type="hidden" id="min_price" name="min_price" value="{{ $minPrice }}">
                            <input type="hidden" id="max_price" name="max_price" value="{{ $maxPrice }}">

                            <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                        </form>


                    </div>
                    <!-- SHOP LISTING-->
                    <div class="col-lg-9 order-1 order-lg-2 mb-5 mb-lg-0">
                        <div class="row mb-3 align-items-center">
                            <div class="col-lg-6 mb-2 mb-lg-0">
                                <p class="text-small text-muted mb-0">
                                    Showing {{ $products->firstItem() }}–{{ $products->lastItem() }} of
                                    {{ $products->total() }} results
                                </p>
                            </div>

                            <div class="col-lg-6">
                                <ul class="list-inline d-flex align-items-center justify-content-lg-end mb-0">
                                    <li class="list-inline-item">
                                        <form method="GET" id="filterForm">
                                            <input type="hidden" name="category_id" value="{{ request('category_id') }}">
                                            <input type="hidden" name="min_price" value="{{ request('min_price') }}">
                                            <input type="hidden" name="max_price" value="{{ request('max_price') }}">

                                            <select class="selectpicker ml-auto" name="sorting" data-width="200"
                                                data-style="bs-select-form-control" data-title="Default sorting"
                                                onchange="document.getElementById('filterForm').submit();">
                                                <option value="default"
                                                    {{ request('sorting') == 'default' ? 'selected' : '' }}>Default sorting
                                                </option>
                                                <option value="low-high"
                                                    {{ request('sorting') == 'low-high' ? 'selected' : '' }}>Price: Low to
                                                    High</option>
                                                <option value="high-low"
                                                    {{ request('sorting') == 'high-low' ? 'selected' : '' }}>Price: High to
                                                    Low</option>
                                            </select>
                                        </form>

                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="row">
                            <!-- PRODUCT-->
                            @foreach ($products as $product)
                                <div class="col-lg-4 col-sm-6">
                                    <div class="product text-center">
                                        <div class="mb-3 position-relative">
                                            @if ($product->badge)
                                                <div class="{{ $product->badge['class'] }}">{{ $product->badge['text'] }}
                                                </div>
                                            @endif
                                            <a class="d-block" href="{{ route('product-details', $product->slug) }}">
                                                <img class="img-fluid w-100"
                                                    src="{{ asset($product->main_image->image_path) }}" alt="..."></a>
                                        </div>
                                        <h6> <a class="reset-anchor"
                                                href="{{ route('product-details', $product->slug) }}">{!! truncate($product->title) !!}</a>
                                        </h6>
                                        @if ($product->has_discount)
                                            <span class="small text-muted">
                                                <del>${{ number_format($product->price, 2) }}</del>
                                            </span>
                                            <span class="small text-danger">
                                                ${{ number_format($product->sale_price, 2) }}
                                            </span>
                                        @else
                                            <p class="small text-muted">${{ $product->price }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach

                        </div>
                        <!-- PAGINATION-->
                        {{ $products->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
@push('scripts')
    <script>
        var range = document.getElementById('range');

        var startMin = parseInt(document.getElementById('min_price').value);
        var startMax = parseInt(document.getElementById('max_price').value);

        noUiSlider.create(range, {
            range: {
                'min': 0,
                'max': 2000
            },
            step: 5,
            start: [startMin, startMax],
            margin: 300,
            connect: true,
            tooltips: true,
            format: {
                to: function(value) {
                    return '$' + Math.round(value);
                },
                from: function(value) {
                    return Number(value.replace('$', ''));
                }
            }
        });

        range.noUiSlider.on('update', function(values) {
            document.getElementById('min_price_val').textContent = values[0];
            document.getElementById('max_price_val').textContent = values[1];
            document.getElementById('min_price').value = values[0].replace('$', '');
            document.getElementById('max_price').value = values[1].replace('$', '');
        });
    </script>
@endpush
