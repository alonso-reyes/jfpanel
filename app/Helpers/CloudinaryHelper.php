<?php

// use Illuminate\Support\Facades\Log;

// Log::info('CloudinaryHelper cargado correctamente');
//return;

use Cloudinary\Cloudinary;

// class CloudinaryHelper
// {
//     public static function getClient()
//     {
//         return new Cloudinary([
//             'cloud' => [
//                 'cloud_name' => config('cloudinary.cloud_name'),
//                 'api_key' => config('cloudinary.api_key'),
//                 'api_secret' => config('cloudinary.api_secret'),
//             ],
//             'url' => [
//                 'secure' => true
//             ]
//         ]);
//     }
// }

if (!function_exists('cloudinary_url')) {
    function cloudinary_url($public_id)
    {
        $cloud_name = config('cloudinary.cloud_name');
        return "https://res.cloudinary.com/{$cloud_name}/image/upload/{$public_id}";
    }
}
