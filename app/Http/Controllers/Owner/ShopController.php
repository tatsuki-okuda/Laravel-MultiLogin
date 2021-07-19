<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShopController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:owners');

        // コントローラーミドルウェア
        // editでパラメータの数値を変えたら他のオーナー情報を見れてしまうので、
        // 自分のオーナーidをチェックして、それ以外の時はエラーにする。
        $this->middleware(function ($request, $next) {
            // リクエストのパラメーターは文字列になる
            // dd($request->route()->parameter('shop'));
            // こっちは数値になる
            // dd(Auth::id());

            //shopのid取得
            $id = $request->route()->parameter('shop');
            // null判定
            // indexの時はパラメータはnullになる。
            if ( !is_null($id) ) {
                $shopsOwnerId = Shop::findOrFail($id)->owner->id;
                // キャスト 文字列→数値に型変換 
                $shopId = (int)$shopsOwnerId;
                $ownerId = Auth::id();
                 // 同じでなかったら
                if ($shopId !== $ownerId) {
                    // 404画面表示 }
                    abort(404);
                }
            }
            return $next($request);
        });
    }

    /**
     * Shop Index
     *
     * @return void
     */
    public function index()
    {
        $ownerId = Auth::id();
        $shops = Shop::where('owner_id', $ownerId)->get();

        return view('owner.shops.index', compact('shops'));
    }


    /**
     * Shop Edit
     *
     * @param [type] $id
     * @return void
     */
    public function edit($id)
    {
        dd(Shop::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        
    }
}
