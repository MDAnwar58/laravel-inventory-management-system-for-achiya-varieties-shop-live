<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TtsFile extends Model
{
    use HasFactory;
    protected $table = 'tts_files';
    protected $fillable = ['text', 'lang', 'file_path'];
}
