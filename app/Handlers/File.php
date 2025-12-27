<?php

namespace App\Handlers;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class File
{
    public static function fileStore($hasFile, $file, $requestFileName, $path): string|null
    {
        if ($hasFile) {
            $name = $requestFileName;
            $fileExtension = $file->getClientOriginalExtension();
            $filename = $name . "." . $fileExtension;
            $file->move($path, $filename);
            // $file->move(storage_path('/app/public/galleries'), $filename);
            // Storage::put('avatars/' . $filename, file_get_contents($file));
            $path = url('/') . $path . $filename;
            return $path;
        } else
            return null;
    }
    public static function fileUpdate($requestFileName, $dataFile, $path)
    {
        if ($requestFileName !== $dataFile) {
            $directory = $path;
            $fileName = basename($dataFile);
            $originalFilePath = public_path($directory . $fileName);
            if ($originalFilePath) {
                $newName = $requestFileName;
                $extension = pathinfo($originalFilePath, PATHINFO_EXTENSION); // Get the file extension
                $newFilename = Str::slug($newName) . '.' . $extension; // Create new filename
                $newFilePath = public_path($directory . $newFilename); // Full path to the new file
                rename($originalFilePath, $newFilePath);
                $path = url('/') . $directory . $newFilename;
                return $path;
            }
        } else
            return $dataFile;
    }
    public static function store($request, $file_name, $path, $data)
    {
        if ($request->hasFile($file_name)) {
            if ($data && $data[$file_name])
                Storage::disk('public')->delete(Str::after($data[$file_name], 'storage/'));
            $avatarPath = $request->file($file_name)->store($path, 'public');
            return url("") . Storage::url($avatarPath);
        } else
            if ($data && $data[$file_name])
                return $data[$file_name];
            else
                return null;
    }
    public static function delete($data, $file_name)
    {
        if (!empty($data[$file_name]))
            Storage::disk('public')->delete(Str::after($data[$file_name], 'storage/'));
    }
    public static function text_to_mp3($path, $text, $lang = 'en')
    {
        $fileName = 'tts_' . time() . '.mp3';
        $filePath = $path . $fileName; // will store in storage/app/public/tts

        // Google Translate TTS endpoint
        $url = "https://translate.google.com/translate_tts?ie=UTF-8&q=" . urlencode($text) . "&tl={$lang}&client=tw-ob";

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64)",
            "Referer: https://translate.google.com/",
        ]);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $audio = curl_exec($ch);
        curl_close($ch);

        if (strpos($audio, "<!DOCTYPE html>") !== false) {
            return response()->json(['error' => 'Could not fetch audio from Google TTS.'], 500);
        }

        // Save mp3 using Storage facade
        Storage::disk('public')->put($filePath, $audio);
        return [
            'text' => $text,
            'lang' => $lang,
            'file_path' => url("") . Storage::url($filePath),
        ];
    }
}
