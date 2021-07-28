<?php 


namespace App\Services;

use Illuminate\Support\Facades\Storage;
use InterventionImage;


Class ImageService 
{

    public static function upload($imageFile, $folderName)
    {
        // // dd($imageFile);
        // if(is_array($imageFile)){
        //     $file = $imageFile['image'];
        // } else {
        //     $file = $imageFile;
        // }

        // // ファイル名の作成
        // $fileName = uniqid(rand().'_');
        // // 拡張子の取得
        // $extension = $file->extension();
        // $fileNameToStore = $fileName. '.' . $extension;
        // $resizedImage = InterventionImage::make($file)
        //     ->resize(1920, 1080)
        //     ->encode();

       
        // Storage::put('public/'. $folderName . '/' . $fileNameToStore, $resizedImage );

        // return $fileNameToStore;

        //dd($imageFile['image']);
        if(is_array($imageFile))
        {
        $file = $imageFile['image'];
        } else {
        $file = $imageFile;
        }

        // ファイル名の作成
        $fileName = uniqid(rand().'_');
        // 拡張子の取得
        $extension = $file->extension();
        $fileNameToStore = $fileName. '.' . $extension;
        $resizedImage = InterventionImage::make($file)->resize(1920, 1080)->encode();

         // 型が違う
        // dd($imageFile, $resizedImage);
        Storage::put('public/' . $folderName . '/' . $fileNameToStore, $resizedImage );
        
        return $fileNameToStore;
    }

}