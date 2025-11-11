<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// 👇 OJO: importa BinaryFileResponse (no StreamedResponse)
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AppDownloadController extends Controller
{
    // 👇 cambia el tipo de retorno a BinaryFileResponse (o quítalo)
    public function apk(Request $request): BinaryFileResponse
    {
        $absolute = storage_path('app/private/apk/LeroLero-v1.apk');

        abort_unless(file_exists($absolute), 404, 'APK no disponible.');

        $filename = 'LeroLero-v1.apk';
        $mime     = 'application/vnd.android.package-archive';

        return response()->download($absolute, $filename, [
            'Content-Type'           => $mime,
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control'          => 'public, max-age=3600',
        ]);
    }
}
