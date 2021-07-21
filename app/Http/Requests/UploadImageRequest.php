<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadImageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *認証されているユーザーが使えるか
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'image'=>'image|mimes:jpg,jpeg,png|max:2048',
            // https://readouble.com/laravel/8.x/ja/validation.html#:~:text=%E4%BD%BF%E7%94%A8%E3%81%A7%E3%81%8D%E3%81%BE%E3%81%99%E3%80%82-,%E9%85%8D%E5%88%97%E3%81%AE%E3%83%90%E3%83%AA%E3%83%87%E3%83%BC%E3%82%B7%E3%83%A7%E3%83%B3,-array%E3%83%90%E3%83%AA%E3%83%87%E3%83%BC%E3%82%B7%E3%83%A7%E3%83%B3%E3%83%AB%E3%83%BC%E3%83%AB
            'files.*.image' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ];
    }

    public function messages() {
        return [
        'image' => '指定されたファイルが画像ではありません。',
        'mines' => '指定された拡張子(jpg/jpeg/png)ではありません。',
        'max' => 'ファイルサイズは2MB以内にしてください。',
        ];
    }
}
