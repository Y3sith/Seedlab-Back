<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Exception;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ImageService
{
    public function procesarImagen(UploadedFile $file, string $folder)
    {
        try {

            // Verificar el tipo MIME del archivo
            $mimeType = $file->getMimeType();
            
            if ($mimeType !== 'image/png' && $mimeType !== 'image/jpeg' && $mimeType !== 'image/webp') {
                throw new Exception("Formato de imagen no soportado. Solo se permiten PNG, JPEG o WebP.");
            }

            $extension = strtolower($file->getClientOriginalExtension());

            // Generar un nombre base único para las imágenes
            $baseFilename = uniqid($folder . '_');

            $directory = storage_path("app/public/$folder");
            if (!File::isDirectory($directory)) {
                File::makeDirectory($directory, 0755, true, true);
            }

            Log::info("Procesando imagen", ['folder' => $folder, 'filename' => $baseFilename]);

            // Cargar la imagen original
            if ($extension === 'webp') {
                $sourceImage = imagecreatefromwebp($file->path());
            } elseif (in_array($extension, ['jpeg', 'jpg'])) {
                $sourceImage = imagecreatefromjpeg($file->path());
            } elseif ($extension === 'png') {
                $sourceImage = imagecreatefrompng($file->path());
            } else {
                throw new Exception("Formato de imagen no soportado.");
            }

            if (!$sourceImage) {
                throw new Exception("No se pudo procesar la imagen.");
            }

            // Convertir la imagen a true color si es necesario
            if (!imageistruecolor($sourceImage)) {
                $trueColorImage = imagecreatetruecolor(imagesx($sourceImage), imagesy($sourceImage));
                imagecopy($trueColorImage, $sourceImage, 0, 0, 0, 0, imagesx($sourceImage), imagesy($sourceImage));
                imagedestroy($sourceImage);
                $sourceImage = $trueColorImage;
            }

            // Definir los tamaños que queremos generar
            $sizes = [
                'small' => 800,
                'medium' => 1600,
                'large' => 2400,
            ];

            $imageUrls = [];

            foreach ($sizes as $sizeName => $width) {
                // Calcular el alto manteniendo la proporción
                $originalWidth = imagesx($sourceImage);
                $originalHeight = imagesy($sourceImage);
                $ratio = $originalWidth / $originalHeight;
                $height = $width / $ratio;

                // Redimensionar la imagen
                $resizedImage = imagecreatetruecolor($width, $height);
                imagecopyresampled(
                    $resizedImage,
                    $sourceImage,
                    0,
                    0,
                    0,
                    0,
                    $width,
                    $height,
                    $originalWidth,
                    $originalHeight
                );

                // Generar un nombre único para la imagen redimensionada
                $resizedFilename = "{$baseFilename}_{$sizeName}.webp";
                $resizedPath = storage_path("app/public/$folder/$resizedFilename");

                // Guardar la imagen redimensionada en formato WebP
                if (imagewebp($resizedImage, $resizedPath, 80)) {
                    Log::info("Imagen {$sizeName} guardada correctamente");
                } else {
                    Log::error("Error al guardar imagen {$sizeName}", ['fullPath' => $resizedPath]);
                    throw new Exception("No se pudo guardar la imagen {$sizeName} en $resizedPath");
                }

                // Liberar la memoria
                imagedestroy($resizedImage);

                // Guardar la ruta de la imagen
                $imageUrls[$sizeName] = "storage/$folder/$resizedFilename"; // Ruta pública
            }

            // Liberar la imagen original
            imagedestroy($sourceImage);

            // Retornar el array con las rutas de las imágenes redimensionadas
            return $imageUrls;
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
            case 'image/jpg':
                return imagecreatefromjpeg($filePath);
            case 'image/png':
                return imagecreatefrompng($filePath);
            case 'image/gif':
                return imagecreatefromgif($filePath);
            case 'image/bmp':
                return imagecreatefrombmp($filePath);
            case 'image/webp':
                return imagecreatefromwebp($filePath);
            case 'image/x-ms-bmp':  // Algunos sistemas pueden usar este MIME type para BMP
                return imagecreatefrombmp($filePath);
            default:
                return false;
        }
    }
}
