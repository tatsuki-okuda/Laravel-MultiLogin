<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Image;
use App\Models\Owner;
use App\Models\PrimaryCategory;
use App\Models\Product;
use App\Models\Shop;
use App\Models\Stock;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ProductRequest;

class ProductController extends Controller
{

    /**
     * construct
     */
    public function __construct()
    {
        $this->middleware('auth:owners');
        $this->middleware(function ($request, $next) {
            $id = $request->route()->parameter('product');
            if ( !is_null($id) ) {
                $productOwnerId = Product::findOrFail($id)->shop->owner->id;
                $iproductId = (int)$productOwnerId;
                if ( $iproductId !== Auth::id() ) {
                    abort(404);
                }
            }
            return $next($request);
        });
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // ログインユーザーの🆔からオーナーを取得して、リレーションを介してproductを取得する、。
        $products = Owner::findOrFail(Auth::id())->shop->product;

        // N+!問題　eagerロード
        $ownerInfo = Owner::with('shop.product.imageFirst')
            ->where('id', Auth::id())
            ->get();

        return view('owner.products.index', compact('ownerInfo'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $shops = Shop::where('owner_id', Auth::id())
                ->select('id', 'name')
                ->get();
        $images = Image::where('owner_id', Auth::id())
                ->select('id', 'title', 'filename')
                ->orderby('updated_at', 'desc')
                ->get();
        $categories = PrimaryCategory::with('secondary')
                ->get();
        return view('owner.products.create', compact('shops', 'images', 'categories'));
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProductRequest $request)
    {
        // storeでエラニーになる。
        // dd($request);

        try {
            DB::transaction(function ()  use($request) {
                $product = Product::create([
                    'name' => $request->name,
                    'information' => $request->information,
                    'price' => $request->price,
                    'sort_order' => $request->sort_order,
                    'shop_id' => $request->shop_id,
                    'secondary_category_id' => $request->category,
                    'image1' => $request->image1,
                    'image2' => $request->image2,
                    'image3' => $request->image3,
                    'image4' => $request->image4,
                    'is_selling' => $request->is_selling
                ]);

                Stock::create([
                    'product_id' => $product->id,
                    'type' => 1,
                    'quantity' => $request->quantity
                ]);

            }, 2);

            // PHPの機能使う時は頭にバックスラッシュか、useで読み込む
        } catch (\Throwable $e) {
            Log::error($e);
            throw $e;
        }

        return redirect()
        ->route('owner.products.index')
        ->with([
            'message' => '商品を登録を実施しました。',
            'status' => 'info'
        ]);

    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $product = Product::find($id);
        $quantity = Stock::where('product_id', $product->id)
            ->sum('quantity');
        
        $shops = Shop::where('owner_id', Auth::id())
            ->select('id', 'name')
            ->get();
        $images = Image::where('owner_id', Auth::id())
            ->select('id', 'title', 'filename')
            ->orderby('updated_at', 'desc')
            ->get();
        $categories = PrimaryCategory::with('secondary')
            ->get();

        return view('owner.products.edit', compact('quantity','product', 'shops', 'images', 'categories'));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ProductRequest $request, $id)
    {
        $request->validate([
            'current_quantity' => 'required|integer',
        ]);

        $product = Product::finfOrFail($id);
        $quantity = Stock::where('product_id', $product->id)
            ->sum('quantity');
        
        // 在庫数に変動がないか、更新まえにデータを引っ張って確認する。
        // 楽観的ロック
        if($request->current_quantity !== $quantity){
            // ルートパラメータの🆔を取得する。
            $id = $request->route()->parameter('product');
            return redirect()
                ->route('owner.products.edit', ['product' => $id])
                ->with([
                    'message' => '在庫数が変更されています。再度確認してください。',
                    'status' => 'alert'
                ]);
        } else {
            try {
                DB::transaction(function ()  use($request, $product) {
                    
                    $product->name = $request->name;
                    $product->information = $request->information;
                    $product->price = $request->price;
                    $product->sort_order = $request->sort_order;
                    $product->shop_id = $request->shop_id;
                    $product->secondary_category_id = $request->category;
                    $product->image1 = $request->image1;
                    $product->image2 = $request->image2;
                    $product->image3 = $request->image3;
                    $product->image4 = $request->image4;
                    $product->is_selling = $request->is_selling;
                    $product->save();

                    // 在庫は追加か現状か
                    if($request->type === '1'){
                        $newQuantity =  $request->quantity;
                    }
                    if($request->type === '2'){
                        $newQuantity =  $request->quantity * -1;
                    }
    
                    Stock::create([
                        'product_id' => $product->id,
                        'type' => $request->type,
                        'quantity' => $newQuantity
                    ]);
    
                }, 2);
    
                // PHPの機能使う時は頭にバックスラッシュか、useで読み込む
            } catch (\Throwable $e) {
                Log::error($e);
                throw $e;
            }
    
            return redirect()
            ->route('owner.products.index')
            ->with([
                'message' => '商品情報をを更新しました。',
                'status' => 'info'
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
