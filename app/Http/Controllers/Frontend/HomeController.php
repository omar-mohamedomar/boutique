<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\CheckoutStoreRequest;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {
        $categories = Category::where([
            'status' => 1,
            'show_at_home' => 1,
        ])->get();

        $products = Product::activeEntries()->with('images')->orderBy('id', 'DESC')->take(8)->get();

        return view('frontend.home', compact('categories', 'products'));
    }

    public function shop(Request $request)
    {

        $products = Product::query();

        if ($request->has('category')) {
            $products->where('category_id', $request->category);
        }

        $minPrice = $request->get('min_price', 100);
        $maxPrice = $request->get('max_price', 1000);

        if ($request->filled('min_price') && $request->filled('max_price')) {
            $products->whereBetween('price', [$minPrice, $maxPrice]);
        }

        if (request('sorting') == 'low-high') {
            $products->orderBy('price', 'ASC');
        } elseif (request('sorting') == 'high-low') {
            $products->orderBy('price', 'DESC');
        } else {
            $products->orderBy('id', 'DESC');
        }

        $products = $products->activeEntries()->with('images')->orderBy('id', 'DESC')->paginate(6);

        $categories = Category::all();

        return view('frontend.shop', compact('products', 'categories', 'minPrice', 'maxPrice'));
    }



    public function showProduct(string $slug)
    {
        $product = Product::with(['tags', 'images'])->where('slug', $slug)->activeEntries()->first();

        $relatedProducts = Product::where('slug', '!=', $product->slug)
            ->where('category_id', $product->category_id)
            ->activeEntries()
            ->take(4)
            ->get();

        return view('frontend.product-details', compact('product', 'relatedProducts'));
    }

    public function handleReview(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'rating'     => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:1000',
        ]);

        if (!Auth::check()) {
            // توجه المستخدم لصفحة التسجيل
            return redirect()->route('register')->with('message', 'You need to register first.');
        }

        Review::create([
            'product_id' => $request->product_id,
            'user_id' => Auth::user()->id,
            'rating' => $request->rating,
            'comment' => $request->comment
        ]);

        toast('Comment added successfully!', 'success');
        return redirect()->back();
    }

    public function addCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'required|integer|min:1'
        ]);

        $product = Product::findOrFail($request->product_id);

        $cart = Cart::firstOrCreate([
            'user_id' => Auth::user()->id,
        ]);

        $cartItem = $cart->CartItems()->where('product_id', $product->id)->first();

        if ($cartItem) {
            $cartItem->quantity += $request->quantity;
            $cartItem->save();
        } else {
            $cart->cartItems()->create([
                'product_id' => $product->id,
                'quantity'   => $request->quantity,
                'price'      => $product->sale_price ?? $product->price
            ]);
        }

        return redirect()->route('cart')->with('success', 'The product has been added to the cart successfully!');
    }

    public function cart()
    {
        $cart = Cart::with('cartItems.product')
            ->where('user_id', Auth::user()->id)
            ->first();

        return view('frontend.cart', compact('cart'));
    }

    public function updateCart(Request $request)
    {
        $request->validate([
            'item_id' => 'required|integer',
            'quantity' => 'required|integer|min:1'
        ]);

        $cartItem = CartItem::where('id', $request->item_id)
            ->whereHas('cart', function ($q) {
                $q->where('user_id', Auth::user()->id);
            })
            ->firstOrFail();

        $cartItem->update([
            'quantity' => $request->quantity,
            'price'    => $cartItem->product->sale_price ?? $cartItem->product->price,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Cart updated successfully!',
            'item_total' => $cartItem->price * $cartItem->quantity,
            'cart_total' => $cartItem->cart->cartItems->sum(fn($i) => $i->price * $i->quantity),
        ]);
    }

    public function removeCart($id)
    {
        $cartItem = CartItem::findOrFail($id);
        $cartItem->delete();

        return response()->json([
            'success' => true,
            'message' => 'Item removed successfully!',
        ]);
    }

    public function checkout()
    {
        $cart = Cart::with('cartItems.product')
            ->where('user_id', Auth::user()->id)
            ->first();

        return view('frontend.checkout', compact('cart'));
    }

    public function checkoutStore(CheckoutStoreRequest $request)
    {
        $cart = Cart::with('cartItems.product')->where('user_id', Auth::user()->id)->first();

        if (!$cart || $cart->cartItems->isEmpty()) {
            return redirect()->route('cart')->with('error', 'Your cart is empty!');
        }

        $totalAmount = $cart->cartItems->sum(fn($item) => $item->price * $item->quantity);

        $order = Order::create([
            'user_id' => Auth::user()->id,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'company_name' => $request->company,
            'country' => $request->country,
            'address_line1' => $request->address_line1,
            'address_line2' => $request->address_line2,
            'city' => $request->city,
            'state' => $request->state,
            'total_amount' => $totalAmount,
            'status'     => 'pending',
        ]);

        foreach ($cart->cartItems as $item) {
            $order->orderItems()->create([
                'product_id' => $item->product_id,
                'quantity'   => $item->quantity,
                'price'      => $item->price,
            ]);
        }

        $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));

        $shippingRate = $stripe->shippingRates->create([
            'display_name' => 'Ground shipping',
            'type' => 'fixed_amount',
            'fixed_amount' => [
                'amount' => 500,
                'currency' => 'usd',
            ],
            'delivery_estimate' => [
                'minimum' => ['unit' => 'business_day', 'value' => 5],
                'maximum' => ['unit' => 'business_day', 'value' => 7],
            ],
        ]);

        $session = $stripe->checkout->sessions->create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => ['name' => 'Order #' . $order->id],
                    'unit_amount' => intval($order->total_amount * 100),
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'shipping_options' => [['shipping_rate' => $shippingRate->id]],
            'success_url' => route('checkout.success', $order->id),
            'cancel_url' => route('checkout.cancel', $order->id),
        ]);

        return redirect($session->url);
    }



    public function checkoutSuccess(Order $order)
    {
        if (!$order) {
            return redirect()->route('shop')->with('error', 'Order not found!');
        }

        $order->update(['status' => 'paid']);

        $cart = Cart::where('user_id', $order->user_id)->first();
        if ($cart) {
            $cart->cartItems()->delete();
        }

        return view('frontend.checkout-success', compact('order'));
    }

    public function checkoutCancel(Order $order)
    {
        $order->update(['status' => 'cancelled']);
        return view('checkout.cancel', compact('order'));
    }
}
