<?php 

namespace App\Services;

use App\Models\Product;
use App\Models\Cart;

class cartService
{
    public static function getItemsCart($items)
    {
        $products = array();
        
        foreach($items as $item){
            // 一つの商品を取得
            $p = Product::findOrFail($item->product_id);
            // リレーションからオーナーを取得して、データーを配列にする
            $owner = $p->shop->owner->select('name', 'email')->first()->toArray();
            // nameは商品名と名前かぶるので名前を変える。
            // 先に配列にしたデータの値の方を取得する。
            $values = array_values($owner);
            // 新しくキーを作る。　nameがかぶるので名前を変える
            $keys = ['ownerName', 'email'];
            // 抜き出したキーとバリューをくっつける。
            $ownerInfo = array_combine($keys, $values);
            // dd($ownerInfo);

            // 商品情報を取得して配列にする。
            $product = Product::where('id', $item->product_id)
            ->select('id', 'name', 'price')->get()->toArray();
    
            // 在庫数を取得
            $quantity = Cart::where('product_id', $item->product_id)
            ->select('quantity')->get()->toArray();

            // dd($product,$quantity);

            // それぞれ抜き出したデータを一つの配列にまとめる。
            $result = array_merge($product[0], $ownerInfo, $quantity[0]);

            // dd($result);
            
            array_push($products, $result);
        }

        // dd($products);
        return $products;
    }
}