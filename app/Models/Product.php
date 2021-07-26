<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;


    public function Shop()
    {
        return $this->belongsTo(Shop::class);
    }

    // メソッド名を変更する時は第二引数で¥外部きーのカラムを指定する。
    public function Category()
    {
        return $this->belongsTo(SecondaryCategory::class, 'secondary_category_id');
    }


    // 外部キーと全く同じ名前にメソッド名をできないので変更する。
    // 外日キーの名前もかい得ている時は第三引数で対象のカラムを指定する。
    public function ImageFirst()
    {
        return $this->belongsTo(Image::class, 'image1', 'id');
    }
}
