<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{


    public function download(Document $document)
    {

        $path = $document->path;

        if (!Storage::disk('public')->exists($path)) {
            abort(404, 'File not found');
        }

        return Storage::disk('public')->download(
            $path,
            basename($path) // اسم الملف عند التنزيل
        );
    }
}
