<?php 


namespace App\Services;

use Illuminate\Support\Facades\Storage;
use InterventionImage;


Class ImageService 
{

    public static function upload($imageFile, $folderName)
    {
        // ファイル名の作成
        $fileName = uniqid(rand().'_');
        $extension = $imageFile->extension();
        $fileNameToStore = $fileName. '.' . $extension;
        $resizedImage = InterventionImage::make($imageFile)
            ->resize(1920, 1080)
            ->encode();

        // 型が違う
        // dd($imageFile, $resizedImage);
        Storage::put('public/'. $folderName . '/' . $fileNameToStore, $resizedImage );

        return $fileNameToStore;
    }

}