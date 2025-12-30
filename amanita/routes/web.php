<?php

use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return 'OK';
});
Route::view('/rocamora', 'about') ->name('about');
Route::view('/services/example', 'services') ->name('services');
Route::view('/contact', 'contact') ->name('contact');

// Definimos la lógica de servir imágenes en una variable para usarla en ambas rutas
$serveImage = function ($path) {
    // 1. Buscamos el archivo en public o private
    $candidates = [
        storage_path('app/public/' . $path),
        storage_path('app/private/' . $path),
        storage_path('app/public/circles/' . $path),
        storage_path('app/' . $path),
        storage_path('app/circles/' . $path),
    ];

    $file = null;
    foreach ($candidates as $candidate) {
        // Usamos realpath para evitar ataques de "Directory Traversal" (../)
        $realPath = realpath($candidate);
        
        // Verificamos que el archivo exista, no sea directorio y esté dentro de la carpeta storage por seguridad
        if ($realPath && file_exists($realPath) && !is_dir($realPath) && str_starts_with($realPath, storage_path())) {
            $file = $realPath;
            break;
        }
    }

    if (! $file) {
        abort(404);
    }

    // 2. Limpiamos el buffer de salida para evitar corrupción
    if (ob_get_level()) ob_end_clean();

    // 3. Enviamos cabeceras manuales (PHP puro)
    $mime = mime_content_type($file) ?: 'application/octet-stream';
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: ' . $mime);
    header('Content-Length: ' . filesize($file));
    header('Cache-Control: no-cache, no-store, must-revalidate');

    // 4. Leemos el archivo y cortamos la ejecución inmediatamente
    readfile($file);
    exit; 
};
Route::get('/media/{path}', $serveImage)->where('path', '.*');
Route::get('/storage/{path}', $serveImage)->where('path', '.*');
Route::get('/circles/{path}', $serveImage)->where('path', '.*');