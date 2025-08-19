@extends('frontend.layouts.master')
@section('title', $product->title)
<!-- Start Setting Metas -->
@section('meta_description', $product->meta_description)
@section('meta_og_title', $product->meta_title)
@section('meta_og_description', $product->meta_description)
@section('meta_og_image', asset($product->main_image->image_path))
@section('meta_tw_title', $product->meta_title)
@section('meta_tw_description', $product->meta_description)
@section('meta_tw_image', asset($product->main_image->image_path))
<!-- End Setting Metas -->
@section('content')
    <section class="py-5">
        <div class="container">
            <div class="row mb-5">
                <div class="col-lg-6">
                    <!-- PRODUCT SLIDER-->
                    <div class="row m-sm-0">
                        <div class="col-sm-2 p-sm-0 order-2 order-sm-1 mt-2 mt-sm-0">
                            <div class="owl-thumbs d-flex flex-row flex-sm-column" data-slider-id="1">
                                @foreach ($product->images as $image)
                                    <div class="owl-thumb-item flex-fill mb-2 mr-2 mr-sm-0">
                                        <img class="w-100" src="{{ asset($image->image_path) }}" alt="...">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="col-sm-10 order-1 order-sm-2">
                            <div class="owl-carousel product-slider" data-slider-id="1">
                                @foreach ($product->images as $image)
                                    <a class="d-block" href="{{ asset($image->image_path) }}" data-lightbox="product"
                                        title="{!! truncate($product->title) !!}"><img class="img-fluid"
                                            src="{{ asset($image->image_path) }}" alt="..."></a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                <!-- PRODUCT DETAILS-->
                <div class="col-lg-6">
                    <ul class="list-inline mb-2">
                        @php
                            $averageRating = round($product->averageRating(), 1); // متوسط التقييم
                        @endphp

                        <ul class="list-inline mb-2">
                            @for ($i = 1; $i <= 5; $i++)
                                <li class="list-inline-item m-0">
                                    <i
                                        class="fas fa-star small {{ $i <= floor($averageRating) ? 'text-warning' : 'text-muted' }}"></i>
                                </li>
                            @endfor
                            <span>({{ $averageRating }}/5)</span>
                        </ul>
                    </ul>
                    <h1>{!! truncate($product->title) !!}</h1>
                    @if ($product->has_discount)
                        <span class="text-muted mr-2">
                            <del>${{ number_format($product->price, 2) }}</del>
                        </span>
                        <span class="text-danger lead">
                            ${{ number_format($product->sale_price, 2) }}
                        </span>
                        <small class="text-success ml-2">
                            (discount {{ $product->discount_percent }}%)
                        </small>
                    @else
                        <p class="text-muted lead">${{ $product->price }}</p>
                    @endif
                    <p class="text-small mb-4">{!! $product->content !!}</p>
                    <form action="{{ route('cart.add') }}" method="POST">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">

                        <div class="row align-items-stretch mb-4">
                            <div class="col-sm-5 pr-sm-0">
                                <div
                                    class="border d-flex align-items-center justify-content-between py-1 px-3 bg-white border-white">
                                    <span class="small text-uppercase text-gray mr-4 no-select">Quantity</span>
                                    <div class="quantity">
                                        <button type="button" class="dec-btn p-0"><i
                                                class="fas fa-caret-left"></i></button>
                                        <input name="quantity" class="form-control border-0 shadow-0 p-0" type="text"
                                            value="1">
                                        <button type="button" class="inc-btn p-0"><i
                                                class="fas fa-caret-right"></i></button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-3 pl-sm-0">
                                <button type="submit"
                                    class="btn btn-dark btn-sm btn-block h-100 d-flex align-items-center justify-content-center px-0">
                                    Add to cart
                                </button>
                            </div>
                        </div>
                    </form>

                    <a class="btn btn-link text-dark p-0 mb-4" href="#"><i class="far fa-heart mr-2"></i>Add to
                        wish list</a><br>
                    <ul class="list-unstyled small d-inline-block">
                        <li class="px-3 py-2 mb-1 bg-white"><strong class="text-uppercase">SKU:</strong><span
                                class="ml-2 text-muted">{{ $product->sku }}</span></li>
                        <li class="px-3 py-2 mb-1 bg-white text-muted"><strong
                                class="text-uppercase text-dark">Category:</strong><a class="reset-anchor ml-2"
                                href="#">{{ $product->category->name }}</a></li>
                        <li class="px-3 py-2 mb-1 bg-white text-muted"><strong
                                class="text-uppercase text-dark">Tags:</strong><a class="reset-anchor ml-2" href="#">
                                @foreach ($product->tags as $tag)
                                    {{ $tag->name }}
                                @endforeach
                            </a></li>

                    </ul>
                </div>
            </div>
            <!-- DETAILS TABS-->
            <ul class="nav nav-tabs border-0" id="myTab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="description-tab" data-toggle="tab" href="#description" role="tab"
                        aria-controls="description" aria-selected="true">Description</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="reviews-tab" data-toggle="tab" href="#reviews" role="tab"
                        aria-controls="reviews" aria-selected="false">Reviews</a>
                </li>
            </ul>

            <div class="tab-content mb-5" id="myTabContent">
                {{-- Description Tab --}}
                <div class="tab-pane fade show active" id="description" role="tabpanel" aria-labelledby="description-tab">
                    <div class="p-4 p-lg-5 bg-white">
                        <h6 class="text-uppercase">Product description</h6>
                        <p class="text-muted text-small mb-0">{!! $product->content !!}</p>
                    </div>
                </div>

                {{-- Reviews Tab --}}
                <div class="tab-pane fade" id="reviews" role="tabpanel" aria-labelledby="reviews-tab">
                    <div class="p-4 p-lg-5 bg-white">
                        <div class="row">
                            <div class="col-lg-8">

                                {{-- عرض المراجعات --}}
                                @foreach ($product->reviews as $review)
                                    <div class="media mb-3">
                                        <img class="rounded-circle"
                                            src="{{ asset('frontend/assets/img/customer-2.png') }}" alt=""
                                            width="50">
                                        <div class="media-body ml-3">
                                            <h6 class="mb-0 text-uppercase">{{ $review->user->name }}</h6>
                                            <p class="small text-muted mb-0 text-uppercase">
                                                {{ date('d M Y H:i', strtotime($review->created_at)) }}
                                            </p>
                                            <ul class="list-inline mb-1 text-xs">
                                                @for ($i = 1; $i <= 5; $i++)
                                                    <li class="list-inline-item m-0">
                                                        <i
                                                            class="fas fa-star{{ $i <= $review->rating ? ' text-warning' : '' }}"></i>
                                                    </li>
                                                @endfor
                                            </ul>
                                            <p class="text-small mb-0 text-muted">{{ $review->comment }}</p>
                                        </div>
                                    </div>
                                @endforeach

                                {{-- فورم إضافة مراجعة --}}
                                <form action="{{ route('product-review') }}" method="POST" class="mt-4">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $product->id }}">

                                    <div class="form-group">
                                        <label>Number of stars</label>
                                        <select name="rating" class="form-control" required>
                                            <option value="">Select Rating</option>
                                            @for ($i = 1; $i <= 5; $i++)
                                                <option value="{{ $i }}">{{ $i }}</option>
                                            @endfor
                                        </select>
                                        @error('rating')
                                            <p class="text-danger">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label>Comment</label>
                                        <textarea name="comment" class="form-control" required></textarea>
                                        @error('comment')
                                            <p class="text-danger">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </form>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- RELATED PRODUCTS-->
            @if (count($relatedProducts) > 0)
                <h2 class="h5 text-uppercase mb-4">Related products</h2>
                <div class="row">
                    <!-- PRODUCT-->
                    @foreach ($relatedProducts as $product)
                        <div class="col-lg-3 col-sm-6">
                            <div class="product text-center skel-loader">
                                <div class="d-block mb-3 position-relative">
                                    @if ($product->badge)
                                        <div class="{{ $product->badge['class'] }}">{{ $product->badge['text'] }}</div>
                                    @endif
                                    <a class="d-block" href="{{ route('product-details', $product->slug) }}">
                                        <img class="img-fluid w-100" src="{{ asset($product->main_image->image_path) }}"
                                            alt="..."></a>
                                    <div class="product-overlay">
                                        <ul class="mb-0 list-inline">
                                            <li class="list-inline-item m-0 p-0"><a class="btn btn-sm btn-outline-dark"
                                                    href="#"><i class="far fa-heart"></i></a></li>
                                            <li class="list-inline-item m-0 p-0"><a class="btn btn-sm btn-dark"
                                                    href="#">Add to cart</a></li>
                                            <li class="list-inline-item mr-0"><a class="btn btn-sm btn-outline-dark"
                                                    href="#productView" data-toggle="modal"><i
                                                        class="fas fa-expand"></i></a>
                                            </li>
                                        </ul>
                                    </div>
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
            @endif
        </div>
    </section>
@endsection
