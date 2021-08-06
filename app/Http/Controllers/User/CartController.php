<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
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
        dd('テスト');
    }
}
