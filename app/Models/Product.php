<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Product extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'shop_id',
        'name',
        'information',
        'price',
        'is_selling',
        'sort_order',
        'secondary_category_id',
        'image1',
        'image2',
        'image3',
        'image4',
    ];



    // 外部キーと全く同じ名前にメソッド名をできないので変更する。
    // 外日キーの名前もかい得ている時は第三引数で対象のカラムを指定する。
    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    // メソッド名を変更する時は第二引数で¥外部きーのカラムを指定する。
    public function category()
    {
        return $this->belongsTo(SecondaryCategory::class, 'secondary_category_id');
    }

    public function imageFirst()
    {
        return $this->belongsTo(Image::class, 'image1', 'id');
    }

    public function imageSecond()
    {
        return $this->belongsTo(Image::class, 'image2', 'id');
    }

    public function imageThird()
    {
        return $this->belongsTo(Image::class, 'image3', 'id');
    }

    public function imageFourth()
    {
        return $this->belongsTo(Image::class, 'image4', 'id');
    }


    public function stock()
    {
        return $this->hasMany(Stock::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'carts')
        ->withPivot(['id', 'quantity']);
    }


    /**
     * https://readouble.com/laravel/8.x/ja/eloquent.html#:~:text=SecondScope%3A%3Aclass%0A%5D)-%3Eget()%3B-,%E3%83%AD%E3%83%BC%E3%82%AB%E3%83%AB%E3%82%B9%E3%82%B3%E3%83%BC%E3%83%97,-%E3%83%AD%E3%83%BC%E3%82%AB%E3%83%AB%E3%82%B9%E3%82%B3%E3%83%BC%E3%83%97%E3%82%92
     *
     * @param [type] $query
     * @return void
     */
    public function scopeAvailableItems($query)
    {

        $stocks = DB::table('t_stocks')
            ->select('product_id', DB::raw('sum(quantity) as quantity')) 
            ->groupBy('product_id')
            ->having('quantity', '>', 1);

        // 必ずqueryを返す必要がある。
        return $query
            ->joinSub($stocks, 'stock', function($join){
                $join->on('products.id', '=', 'stock.product_id');
            })
            ->join('shops', 'products.shop_id', '=', 'shops.id')
            // eloquantのリレーションが使えなくなるので、関連するテーブルを紐づける必要がある
            ->join('secondary_categories', 'products.secondary_category_id', '=','secondary_categories.id')
            // 同じ名前が使えないので番号をふる
            ->join('images as image1', 'products.image1', '=', 'image1.id')
            ->where('shops.is_selling', true)
            ->where('products.is_selling', true)
            ->select(
                'products.id as id',
                'products.name as name',
                'products.price' ,
                'products.sort_order as sort_order',
                'products.information',
                'secondary_categories.name as category' ,
                'image1.filename as filename'
            );
    }
}
