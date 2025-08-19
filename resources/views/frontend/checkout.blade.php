@extends('frontend.layouts.master')
@section('content')
    <div class="container">
        <!-- HERO SECTION-->
        <section class="py-5 bg-light">
            <div class="container">
                <div class="row px-4 px-lg-5 py-lg-4 align-items-center">
                    <div class="col-lg-6">
                        <h1 class="h2 text-uppercase mb-0">Checkout</h1>
                    </div>
                    <div class="col-lg-6 text-lg-right">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb justify-content-lg-end mb-0 px-0">
                                <li class="breadcrumb-item"><a href="index.html">Home</a></li>
                                <li class="breadcrumb-item"><a href="cart.html">Cart</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Checkout</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </section>
        <section class="py-5">
            <!-- BILLING ADDRESS-->
            <h2 class="h5 text-uppercase mb-4">Billing details</h2>
            <div class="row">
                <div class="col-lg-8">
                    <form action="{{ route('checkout.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-lg-6 form-group">
                                <label class="text-small text-uppercase" for="firstName">First name</label>
                                <input class="form-control form-control-lg" id="firstName" type="text"
                                    placeholder="Enter your first name" name="first_name">
                                @error('first_name')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-lg-6 form-group">
                                <label class="text-small text-uppercase" for="lastName">Last name</label>
                                <input class="form-control form-control-lg" id="lastName" type="text"
                                    placeholder="Enter your last name" name="last_name">
                                @error('last_name')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-lg-6 form-group">
                                <label class="text-small text-uppercase" for="email">Email address</label>
                                <input class="form-control form-control-lg" id="email" type="email"
                                    placeholder="e.g. Jason@example.com" name="email">
                                @error('email')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-lg-6 form-group">
                                <label class="text-small text-uppercase" for="phone">Phone number</label>
                                <input class="form-control form-control-lg" id="phone" type="tel"
                                    placeholder="e.g. +02 245354745" name="phone">
                                @error('phone')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-lg-6 form-group">
                                <label class="text-small text-uppercase" for="company">Company name (optional)</label>
                                <input class="form-control form-control-lg" id="company" type="text"
                                    placeholder="Your company name" name="company">
                                @error('company')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-lg-6 form-group">
                                <label class="text-small text-uppercase" for="country">Country</label>
                                <select class="selectpicker country" id="country" data-width="fit"
                                    data-style="form-control form-control-lg" data-title="Select your country"
                                    name="country">
                                    <option value="">Select your country</option>
                                    @foreach (config('countries') as $country)
                                        <option value="{{ $country }}">{{ $country }}</option>
                                    @endforeach
                                </select>
                                @error('country')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-lg-12 form-group">
                                <label class="text-small text-uppercase" for="address">Address line 1</label>
                                <input class="form-control form-control-lg" id="address" type="text"
                                    placeholder="House number and street name" name="address_line1">
                                @error('address_line1')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-lg-12 form-group">
                                <label class="text-small text-uppercase" for="address">Address line 2</label>
                                <input class="form-control form-control-lg" id="addressalt" type="text"
                                    placeholder="Apartment, Suite, Unit, etc (optional)" name="address_line2">
                                @error('address_line2')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-lg-6 form-group">
                                <label class="text-small text-uppercase" for="city">Town/City</label>
                                <input class="form-control form-control-lg" id="city" type="text"
                                    name="city">
                                @error('city')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-lg-6 form-group">
                                <label class="text-small text-uppercase" for="state">State/County</label>
                                <input class="form-control form-control-lg" id="state" type="text"
                                    name="state">
                                @error('state')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-lg-12 form-group">
                                <button class="btn btn-dark" type="submit">Place order</button>
                            </div>
                        </div>
                    </form>
                </div>
                <!-- ORDER SUMMARY-->
                <div class="col-lg-4">
                    <div class="card border-0 rounded-0 p-lg-4 bg-light">
                        <div class="card-body">
                            <h5 class="text-uppercase mb-4">Your order</h5>
                            <ul class="list-unstyled mb-0">
                                @foreach ($cart->cartItems as $item)
                                    <li class="d-flex align-items-center justify-content-between"><strong
                                            class="small font-weight-bold">{!! $item->product->title !!}
                                            ({{ $item->quantity }})
                                        </strong><span class="text-muted small">${{ $item->price }}</span></li>
                                    <li class="border-bottom my-2"></li>
                                @endforeach
                                <li class="d-flex align-items-center justify-content-between"><strong
                                        class="text-uppercase small font-weight-bold">Total</strong><span>${{ $cart->cartItems->sum(fn($i) => $i->price * $i->quantity) }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
