<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

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


    public function stock()
    {
        return $this->hasMany(Stock::class);
    }
}
