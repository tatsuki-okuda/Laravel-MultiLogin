<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use InterventionImage;

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
        // dd(Shop::findOrFail($id));
        $shop = Shop::findOrFail($id);
        return view('owner.shops.edit', compact('shop'));
    }

    public function update(Request $request, $id)
    {
        //一時保存
        $imageFile = $request->image;

        // リサイズなしのパターン
        // if(!is_null($imageFile) && $imageFile->isValid() ){
        //     // リサイズする時は型が変わるのでputFileは使えなくなるので注意
        //     Storage::putFile('public/shops', $imageFile);
        // }


        // リサイズパターン
        if(!is_null($imageFile) && $imageFile->isValid() ){
            // ファイル名の作成
            $fileName = uniqid(rand().'_');
            $extension = $imageFile->extension();
            $fileNameToStore = $fileName. '.' . $extension;
            $resizedImage = InterventionImage::make($imageFile)
                ->resize(1920, 1080)
                ->encode();
            // 型が違う
            // dd($imageFile, $resizedImage);
            Storage::put('public/shops/' . $fileNameToStore, $resizedImage );
        }
        return redirect()->route('owner.shops.index');
    }
}
