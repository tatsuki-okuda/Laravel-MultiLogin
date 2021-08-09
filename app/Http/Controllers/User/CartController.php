<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Stock;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    
    public function index()
    {
        $user = User::findOrFail(Auth::id());
        $products = $user->products;
        $totalPrice = 0;

        foreach($products as $product){
            $totalPrice += $product->price * $product->pivot->quantity;
        }
        // dd($products, $totalPrice);

        return view('user.cart', compact('products', 'totalPrice'));
    }


    public function add(Request $request)
    {
        $itemInCart = Cart::where('product_id', $request->product_id)
            ->where('user_id',  Auth::id())
            ->first();

        if($itemInCart){
            // カート内に商品がある場合は既存のカートの商品数と足し合わせる。
            $itemInCart->quantity += $request->quantity;
            $itemInCart->save();
        } else {
            Cart::create([
                'user_id' => Auth::id(),
                'product_id' => $request->product_id,
                'quantity' => $request->quantity
            ]);
        }
        
        return redirect()->route('user.cart.index');
    }


    public function delete($id)
    {
        Cart::where('product_id', $id)
            ->where('user_id', Auth::id())
            ->delete();

        return redirect()->route('user.cart.index');
    }



    public function checkout()
    {
        $user = User::findOrFail(Auth::id());
        $products = $user->products;

        /**
         * https://stripe.com/docs/api/checkout/sessions/create#create_checkout_session-line_items
         */
        $lineItems = array();
        foreach($products as $product){
            // 購入ボタンを押したら在庫を確認して、確保する、
            $quantity = '';
            $quantity = Stock::where('product_id',$product->id)->sum('quantity');

            if($product->pivot->quantity > $quantity){
                // 在庫数が足りない時はリダイレクトで戻す。
                return redirect()->route('user.cart.index');
            } else {
                $linItem = [
                    'name' => $product->name,
                    'description' => $product->information,
                    'amount' => $product->price,
                    'currency' => 'jpy',
                    'quantity'  => $product->pivot->quantity
                ];
                array_push($lineItems, $linItem);
            }
        }
        // dd($lineItems);
        // 　購入可能であれば決済前に在庫を減らす。
        foreach($products as $product){
            Stock::create([
                'product_id' => $product->id,
                'type' => \Constant::PRODUCT_LIST['reduce'],
                'quantity' => $product->pivot->quantity * -1,
            ]);
        }
        // dd('test');


        // https://stripe.com/docs/checkout/integration-builder
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET_KEY'));

        $session = \Stripe\Checkout\Session::create([
            'payment_method_types' => ['card',],
            'line_items' => [$lineItems],
            'mode' => 'payment',
            'success_url' => route('user.cart.success'),
            'cancel_url' => route('user.cart.index'),
        ]);

        $publicKey = env('STRIPE_PUBLIC_KEY');

        return view('user.checkout', compact('session', 'publicKey'));
    }


    public function success()
    {
        Cart::where('user_id', Auth::id())->delete();
        return redirect()->route('user.items.index');
    }
}
