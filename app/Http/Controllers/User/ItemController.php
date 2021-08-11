<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ItemController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:users');
        $this->middleware(function ($request, $next) {
            // コントローラーでparamsは設定している。
            $id = $request->route()->parameter('item');
            if(!is_null($id)){
            $itemId = Product::availableItems()->where('products.id', $id)->exists();
                if(!$itemId){
                    abort(404);
                }
            }
            return $next($request);
        });
    }

    public function index(Request $request)
    {
        // モデルにscopeとしてqueryを登録する.
        $products = Product::availableItems()
            ->sortOrder($request->sort)
            ->get();
        // dd($stocks,$products);
        // $products = Product::all();
        return view('user.index', compact('products'));
    }


    public function show($id)
    {
        $product = Product::findOrFail($id);
        $quantity = Stock::where('product_id', $product->id)
            ->sum('quantity');
        if($quantity > 9){
            $quantity = 9;
        }
        return view('user.show', compact('product', 'quantity'));
    }
}
