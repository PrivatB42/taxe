<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class FileManager
{
    /**
     * Uploader un fichier.
     *
     * @param UploadedFile $file
     * @param string $directory
     * @return string Le chemin enregistré
     */
    public static function upload(UploadedFile $file, string $directory, bool $fullPath = false, string $disk = 'public'): string
    {
        $file = $file->store($directory, $disk);

        if (!$fullPath) {
            return $file;
        }

        return url(Storage::url($file));
    }

    /**
     * Uploader un fichier avec un nom de fichier donné.
     *
     * @param UploadedFile $file
     * @param string $filename Le nom du fichier
     * @param string $directory
     * @param bool $fullPath Si true, le chemin enregistré est le chemin complet
     * @param string $disk Le disk de stockage
     * @return string Le chemin enregistré
     */
    public static function uploadWithName(UploadedFile $file, string $filename, string $directory, bool $fullPath = false, string $disk = 'public'): string
    {

        $path = $file->storeAs($directory, $filename, $disk);

        if (!$fullPath) {
            return $path;
        }

        return url(Storage::url($path));
    }

    /**
     * Modifier un fichier (supprimer l'ancien et uploader un nouveau).
     *
     * @param UploadedFile $file
     * @param string|null $existingFilePath
     * @param string $directory
     * @return string Le chemin enregistré
     */
    public static function update(UploadedFile $file, ?string $existingFilePath, string $directory, bool $fullPath = false, string $disk = 'public'): string
    {
        if ($existingFilePath) {
            self::delete($existingFilePath, $disk);
        }
        return self::upload($file, $directory, $fullPath, $disk);
    }


    public static function updateWithName(UploadedFile $file, ?string $existingFilePath, string $filename, string $directory, bool $fullPath = false, string $disk = 'public'): string
    {
        if ($existingFilePath) {
            self::delete($existingFilePath, $disk);
        }
        return self::uploadWithName($file, $filename, $directory, $fullPath, $disk);
    }

    /**
     * Supprimer un fichier.
     *
     * @param string|null $filePath
     * @return bool
     */
    public static function delete(?string $filePath, string $disk = 'public'): bool
    {
        if ($filePath) {
            return Storage::disk($disk)->delete($filePath);
        }
        return false;
    }

    /**
     * Générer l'URL d'un fichier stocké.
     *
     * @param string|null $filePath
     * @return string|null
     */
    public static function url(?string $filePath): ?string
    {
        //return $filePath ? Storage::disk('public')->url($filePath) : null;
        return '';
    }

    /**
     * Télécharger un fichier.
     *
     * @param string $filePath
     * @param string|null $name
     * @return \Symfony\Component\HttpFoundation\StreamedResponse|null
     */
    public static function download(string $filePath, ?string $name = null)
    {
        if (Storage::disk('public')->exists($filePath)) {
            // return Storage::disk('public')->download($filePath, $name);
        }
        return null;
    }


    public static function base64ToUploadedFile(string $base64String)
    {
        // Check if the string is a valid base64 image
        if (!preg_match('/^data:image\/(\w+);base64,/', $base64String, $matches)) {
            return false;
        }

        // Extract the MIME type (e.g., 'png', 'jpeg')
        $extension = $matches[1];

        // Remove the base64 prefix
        $base64Data = substr($base64String, strpos($base64String, ',') + 1);

        // Decode the base64 data
        $decodedData = base64_decode($base64Data, true);

        if ($decodedData === false) {
            return false;
        }

        // Generate a temporary file path
        $tempFilePath = tempnam(sys_get_temp_dir(), 'laravel_base64_');
        file_put_contents($tempFilePath, $decodedData);

        // Create an UploadedFile instance
        return new UploadedFile(
            $tempFilePath,
            uniqid() . '.' . $extension, // Filename
            'image/' . $extension, // MIME type
            null, // Error code (null means no error)
            true  // Test mode (avoids moving the file)
        );
    }

    public static function isValidBase64Image(string $base64String): bool
    {
        // Check if the string is a valid base64 image
        return preg_match('/^data:image\/(\w+);base64,/', $base64String) === 1;
    }


    public static function uploadImageFromBase64(string $base64String, string $directory, bool $fullPath = false, string $disk = 'public'): ?string
    {

        $uploadedFile = self::base64ToUploadedFile($base64String);
        if ($uploadedFile) {
            return self::upload($uploadedFile, $directory, $fullPath, $disk);
        }

        return null;
    }

    public static function updateImageFromBase64(string $base64String, ?string $existingFilePath, string $directory, bool $fullPath = false, string $disk = 'public'): ?string
    {
        $uploadedFile = self::base64ToUploadedFile($base64String);
        if ($uploadedFile) {
            return self::update($uploadedFile, $existingFilePath, $directory, $fullPath, $disk);
        }

        return null;
    }
}
