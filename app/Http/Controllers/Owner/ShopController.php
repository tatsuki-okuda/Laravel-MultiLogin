<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Http\Requests\UploadImageRequest;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use InterventionImage;
use App\Services\ImageService;

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

    public function update(UploadImageRequest $request, $id)
    {

        $request->validate([
            'name' => 'required|string|max:50',
            'information' => 'required|string|max:1000',
            'is_selling' => 'required',
        ]);

        //一時保存
        $imageFile = $request->image;

        // リサイズなしのパターン
        // if(!is_null($imageFile) && $imageFile->isValid() ){
        //     // リサイズする時は型が変わるのでputFileは使えなくなるので注意
        //     Storage::putFile('public/shops', $imageFile);
        // }

        // リサイズパターン
        if(!is_null($imageFile) && $imageFile->isValid() ){
            // 独自にサービスを作ってそこに共通化できるメソッドを格納する。
            $fileNameToStore = ImageService::upload($imageFile, 'shops');
        }

        $shop = Shop::findOrFail($id);
        $shop->name = $request->name;
        $shop->information = $request->information;
        $shop->is_selling = $request->is_selling;
        if(!is_null($imageFile) && $imageFile->isValid() ){
            $shop->filename = $fileNameToStore;
        }
        $shop->save();


        return redirect()
            ->route('owner.shops.index')
            ->with([
                'message' => '店舗情報を更新しました。',
                'status' => 'info'
            ]);;
    }

}
