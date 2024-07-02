<?php


namespace App\Helper\Uploader;


use GuzzleHttp\Client;
use Illuminate\Support\Facades\Storage;

class Uploader
{

    protected static $fileType = [
        'image' => 'image',
        'video' => 'video',
        'voice' => 'voice',
        'file' => 'file',
        'pdf' => 'pdf',
    ];

    protected static $privacy = [
        'private' => 'local',
        'public' => 'public',
    ];

    protected static $path = [
        'private' => 'private\\',
        'public' => ''];


    private static function generateName($file)
    {
        $fileName = uniqid() . uniqid() . time() . '.' . $file->getClientOriginalExtension();
        return $fileName;
    }

    public static function getFileType($type)
    {
        $key = self::$fileType[$type];
        return $key;
    }

    private static function filePath($name, $type, $path)
    {
        if (!is_null($path)) {
            $path = self::getFileType($type) . '/' . $path . '/' . $name;
        } else {
            $path = self::getFileType($type) . '/' . $name;
        }
        return $path;
    }

    /*
     * 0 is private file and 1 is public file
     *  types image video voice file ... .
     */

    public static function uploadToStorage($file, $type, $path = null, $privacy = 'public')
    {
        $fileName = self::generateName($file);

        $filePath = self::filePath('', $type, $path);

        Storage::disk(static::$privacy[$privacy])->putFileAs($filePath, $file, $fileName);

        return ['id' => null, 'path' => $fileName, 'type' => $type];

    }


    public static function deleteFromStorage($name, $type, $path = null, $privacy = 'public')
    {
        if (is_null($name) || empty(trim($name))) {
            return 0;
        }
        $filePath = self::filePath($name, $type, $path);
        Storage::disk(static::$privacy[$privacy])->delete($filePath);
        return true;
    }

    public static function moveFile($name, $type, $originPath, $destPath, $originPrivacy = 'public', $destPrivacy = 'public')
    {

        Storage::move(
            self::$privacy[$originPrivacy] . '/' . static::getFileType($type) . '/' . $originPath . '/' . $name,
            self::$privacy[$destPrivacy] . '/' . static::getFileType($type) . '/' . $destPath . '/' . $name);
    }

    public static function fileExistInStorage($name, $type, $path, $privacy = 'public')
    {
        $filePath = self::filePath($name, $type, $path);
        return Storage::disk(self::$privacy[$privacy])->exists($filePath);
    }

    // public static function showPrivateFile($type, $name)
    // {
    //     $path = storage_path('app' . '/' . $type . '/' . $name);
    //     if (!\Illuminate\Support\Facades\File::exists($path)) {
    //         return response()->make('فایل مورد نظر یافت نشد.', 404);
    //     }
    //     $file = \Illuminate\Support\Facades\File::get($path);
    //     $fileType = \Illuminate\Support\Facades\File::mimeType($path);
    //     $response = response()->make($file, 200);
    //     $response->header("Content-Type", $fileType);
    //     return $response;
    // }
    
    public static function showPrivateFile($type, $name)
    {
        $path = storage_path('app/' . $type . '/' . $name);
        if (!\Illuminate\Support\Facades\File::exists($path)) {
            return response()->make('فایل مورد نظر یافت نشد.', 404);
        }
        $file = \Illuminate\Support\Facades\File::get($path);
        $fileType = \Illuminate\Support\Facades\File::mimeType($path);
        $extension = \Illuminate\Support\Facades\File::extension($path); // Get the file extension
        $response = response()->make($file, 200);
        $response->header("Content-Type", $fileType);
        $response->header("Content-Disposition", "inline; filename=$name.$extension"); // Set the file name with extension
        return $response;
    }

    /*example
     * \App\Helpers\Uploader::changeFilePrivacy(1,1,'5c4bfaabb87b55c4bfaabb87b7piano.jpeg');
     */

    public static function changeFilePrivacy($privacy, $type, $name)
    {
        Storage::move(static::$privacy[(int)$privacy] . '/' . static::getFileType($type) . '/' . $name,
            static::$privacy[(int)!$privacy] . '/' . static::getFileType($type) . '/' . $name);
    }

    public static function storagePublicPath($type, $path)
    {
        $imagePath = storage_path('app/public/' . $type . '/' . $path);
        return $imagePath;
    }

    public static function storagePrivatePath($type, $path)
    {
        $imagePath = storage_path('app/' . $type . '/' . $path);
        return $imagePath;
    }

    public static function getFileFullPath($path, $name)
    {
        return storage_path('app/public/' . 'image' . '/' . $path . '/' . $name);
    }

}
