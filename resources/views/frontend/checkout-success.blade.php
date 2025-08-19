@extends('frontend.layouts.master')

@section('content')
<div class="container my-5">
    <h1 class="mb-3">Payment Successful!</h1>
    <p>Thank you, {{ $order->first_name }}. Your order #{{ $order->id }} has been received.</p>
    <p>Total: ${{ number_format($order->total_amount, 2) }}</p>
    <a href="{{ route('shop') }}" class="btn btn-primary mt-3">Continue Shopping</a>
</div>
@endsection
