<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Jobs\SendThanksMail;
use App\Mail\TestMail;
use App\Models\PrimaryCategory;
use App\Models\Product;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

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
        //メールの送信
        // 同期的に送信
        // Mail::to('test@exsample.com')
        //     ->send(new TestMail());

        // 非同期で送信
        // これでキューにjobを入れるだけなので
        // 別途workerを発火させる必要あり。
        // SendThanksMail::dispatch();


        $categories = PrimaryCategory::with('secondary')->get();
        // モデルにscopeとしてqueryを登録する.
        $products = Product::availableItems()
            // 選んでなかったら初期値を入れる
            ->selectCategory($request->category ?? '0')
            ->serchKeyword($request->keyword)
            ->sortOrder($request->sort)
            ->paginate($request->pagination ?? '20');
        // dd($stocks,$products);
        // $products = Product::all();
        return view('user.index', compact('products', 'categories'));
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
