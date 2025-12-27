<?php

namespace App\Http\Controllers;

use App\Handlers\File;
use App\Models\TtsFile;
use Illuminate\Http\Request;

class TestController extends Controller
{
    // i want to make in the function text to audio .mp3
    public function test(Request $request)
    {
        $text = $request->input('text', 'Your stock is very low');
        $lang = $request->input('lang', 'en'); // default English

        $fileMp3 = File::text_to_mp3($text, $lang);
        // Save record in DB
        $tts = TtsFile::create([
            'text' => $fileMp3['text'],
            'lang' => $fileMp3['lang'],
            'file_path' => $fileMp3['file_path'],
        ]);

        return response()->json([
            'message' => 'TTS generated successfully!',
            'tts_id' => $tts->id,
            'file_path' => $fileMp3['file_path'],
        ]);
    }
    //  that to workable
    // public function test(Request $request)
    // {
    //     $text = $request->input('text', 'Your stock is very low');
    //     $lang = $request->input('lang', 'en'); // default English

    //     $fileName = 'tts_' . time() . '.mp3';
    //     $filePath = 'tts/' . $fileName; // store in storage/app/public/tts
    //     $fullPath = storage_path('app/public/' . $filePath);

    //     // Ensure directory exists
    //     if (!file_exists(storage_path('app/public/tts'))) {
    //         mkdir(storage_path('app/public/tts'), 0777, true);
    //     }

    //     // Google Translate TTS endpoint
    //     $url = "https://translate.google.com/translate_tts?ie=UTF-8&q=" . urlencode($text) . "&tl={$lang}&client=tw-ob";

    //     $ch = curl_init($url);
    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //     curl_setopt($ch, CURLOPT_HTTPHEADER, [
    //         "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64)",
    //         "Referer: https://translate.google.com/",
    //     ]);
    //     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    //     $audio = curl_exec($ch);
    //     curl_close($ch);

    //     if (strpos($audio, "<!DOCTYPE html>") !== false) {
    //         return response()->json(['error' => 'Could not fetch audio from Google TTS.'], 500);
    //     }

    //     // Save mp3 file
    //     file_put_contents($fullPath, $audio);

    //     // Save record in DB
    //     $tts = TssFile::create([
    //         'text' => $text,
    //         'lang' => $lang,
    //         'file_path' => $filePath,
    //     ]);

    //     return response()->json([
    //         'message' => 'TTS generated successfully!',
    //         'tts_id' => $tts->id,
    //         'file_path' => asset('storage/' . $filePath),
    //     ]);
    // }
    // public function test(Request $request)
    // {
    //     $text = $request->input('text', 'Your stock is very low');
    //     $lang = $request->input('lang', 'en'); // default English
    //     $fileName = 'tts_' . time() . '.mp3';
    //     $filePath = storage_path('app/public/' . $fileName);

    //     // Google Translate TTS endpoint
    //     $url = "https://translate.google.com/translate_tts?ie=UTF-8&q=" . urlencode($text) . "&tl={$lang}&client=tw-ob";

    //     $ch = curl_init($url);
    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    //     // **Important headers**
    //     curl_setopt($ch, CURLOPT_HTTPHEADER, [
    //         "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64)",
    //         "Referer: https://translate.google.com/",
    //     ]);

    //     // Optional: some systems need this to avoid SSL errors
    //     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    //     $audio = curl_exec($ch);
    //     curl_close($ch);

    //     // **Check if audio returned correctly**
    //     if (strpos($audio, "<!DOCTYPE html>") !== false) {
    //         return "Error: Could not fetch audio. Google may have blocked request.";
    //     }

    //     file_put_contents($filePath, $audio);

    //     return response()->download($filePath)->deleteFileAfterSend(true);
    // }
}
