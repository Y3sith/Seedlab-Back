<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Exception;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class ImageService
{
    public function procesarImagen(UploadedFile $file, string $folder)
    {
        try {
            $extension = strtolower($file->getClientOriginalExtension());
            $filename = uniqid($folder . '_') . '.webp';
            $path = "public/$folder/$filename";

            $directory = storage_path("app/public/$folder");
            if (!File::isDirectory($directory)) {
                File::makeDirectory($directory, 0755, true, true);
            }

            Log::info("Procesando imagen", ['folder' => $folder, 'filename' => $filename]);

            if ($extension === 'webp') {
                $file->storeAs("public/$folder", $filename);
                Log::info("Archivo WebP movido correctamente");
            } else {
                $sourceImage = $this->createImageFromFile($file->path());
                if ($sourceImage) {
                    $fullPath = storage_path("app/$path");
                    Log::info("Intentando guardar imagen WebP", ['fullPath' => $fullPath]);

                    // Convertir la imagen a true color si es necesario
                    if (!imageistruecolor($sourceImage)) {
                        $trueColorImage = imagecreatetruecolor(imagesx($sourceImage), imagesy($sourceImage));
                        imagecopy($trueColorImage, $sourceImage, 0, 0, 0, 0, imagesx($sourceImage), imagesy($sourceImage));
                        imagedestroy($sourceImage);
                        $sourceImage = $trueColorImage;
                    }

                    if (imagewebp($sourceImage, $fullPath, 80)) {
                        Log::info("Imagen WebP guardada correctamente");
                    } else {
                        Log::error("Error al guardar imagen WebP", ['fullPath' => $fullPath]);
                        throw new Exception("No se pudo guardar la imagen WebP en $fullPath");
                    }
                    imagedestroy($sourceImage);
                } else {
                    throw new Exception("No se pudo procesar la imagen en la carpeta $folder.");
                }
            }

            return "$folder/$filename";
        } catch (Exception $e) {
            Log::error("Error en procesarImagen", ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function procesarRutaMulti($rutaMulti)
    {
        // Verifica si es un archivo subido
        if ($rutaMulti instanceof UploadedFile) {
            $mimeType = $rutaMulti->getMimeType();

            if (strpos($mimeType, 'image') !== false) {
                // Procesar imagen
                $folder = 'imagenes';
                return $this->procesarImagen($rutaMulti, $folder);
            } elseif ($mimeType === 'application/pdf') {
                // Procesar PDF
                $filename = time() . '_' . $rutaMulti->getClientOriginalName();
                $folder = 'documentos';
                $rutaMulti->storeAs("public/$folder", $filename);
                return "$folder/$filename";
            } else {
                throw new Exception('Tipo de archivo no soportado para ruta_multi');
            }
        } elseif (is_string($rutaMulti) && filter_var($rutaMulti, FILTER_VALIDATE_URL)) {
            // Si es una URL válida, simplemente devuélvela
            return $rutaMulti;
        } else {
            throw new Exception('El campo ruta_multi debe ser un archivo válido o una URL.');
        }
    }


    private function createImageFromFile($filePath)
    {
        // Obtiene la información de la imagen
        $imageInfo = getimagesize($filePath);
        if ($imageInfo === false) {
            return false;
        }

        $mimeType = $imageInfo['mime'];

        // Crea la imagen a partir del tipo MIME
        switch ($mimeType) {
            case 'image/jpeg':
                return imagecreatefromjpeg($filePath);
            case 'image/png':
                return imagecreatefrompng($filePath);
            case 'image/gif':
                return imagecreatefromgif($filePath);
            case 'image/bmp':
                return imagecreatefrombmp($filePath);
            default:
                return false;
        }
    }
}
