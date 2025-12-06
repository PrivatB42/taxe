<?php

namespace App\PhpFx\File;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class XFileManager
{
    /**
     * Uploader un fichier.
     *
     * @param UploadedFile $file
     * @param string $directory
     * @param bool $fullPath
     * @param string $disk
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
     * @param bool $fullPath
     * @param string $disk
     * @return string Le chemin enregistré
     */
    public static function update(UploadedFile $file, ?string $existingFilePath, string $directory, bool $fullPath = false, string $disk = 'public'): string
    {
        if ($existingFilePath) {
            self::delete($existingFilePath, $disk);
        }
        return self::upload($file, $directory, $fullPath, $disk);
    }

    /**
     * Mettre à jour un fichier avec un nom spécifique.
     */
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
     * @param string $disk
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
     * @param string $disk
     * @return string|null
     */
    public static function url(?string $filePath, string $disk = 'public'): ?string
    {
        if (!$filePath || !self::exists($filePath, $disk)) {
            return null;
        }

        return url(Storage::url($filePath));
    }

    /**
     * Télécharger un fichier.
     *
     * @param string $filePath
     * @param string|null $name
     * @param string $disk
     * @return StreamedResponse|null
     */
    public static function download(string $filePath, ?string $name = null, string $disk = 'public'): ?StreamedResponse
    {
        if (!self::exists($filePath, $disk)) {
            return null;
        }

        return Storage::download($filePath, $name, ['disk' => $disk]);
        
    }

    /**
     * Convertir une chaîne base64 en UploadedFile.
     */
    public static function base64ToUploadedFile(string $base64String)
    {
        if (!preg_match('/^data:image\/(\w+);base64,/', $base64String, $matches)) {
            return false;
        }

        $extension = $matches[1];
        $base64Data = substr($base64String, strpos($base64String, ',') + 1);
        $decodedData = base64_decode($base64Data, true);

        if ($decodedData === false) {
            return false;
        }

        $tempFilePath = tempnam(sys_get_temp_dir(), 'laravel_base64_');
        file_put_contents($tempFilePath, $decodedData);

        return new UploadedFile(
            $tempFilePath,
            uniqid() . '.' . $extension,
            'image/' . $extension,
            null,
            true
        );
    }

    /**
     * Vérifier si une chaîne est une image base64 valide.
     */
    public static function isValidBase64Image(string $base64String): bool
    {
        return preg_match('/^data:image\/(\w+);base64,/', $base64String) === 1;
    }

    /**
     * Uploader une image depuis base64.
     */
    public static function uploadImageFromBase64(string $base64String, string $directory, bool $fullPath = false, string $disk = 'public'): ?string
    {
        $uploadedFile = self::base64ToUploadedFile($base64String);
        if ($uploadedFile) {
            return self::upload($uploadedFile, $directory, $fullPath, $disk);
        }
        return null;
    }

    /**
     * Mettre à jour une image depuis base64.
     */
    public static function updateImageFromBase64(string $base64String, ?string $existingFilePath, string $directory, bool $fullPath = false, string $disk = 'public'): ?string
    {
        $uploadedFile = self::base64ToUploadedFile($base64String);
        if ($uploadedFile) {
            return self::update($uploadedFile, $existingFilePath, $directory, $fullPath, $disk);
        }
        return null;
    }

    // ========== NOUVELLES FONCTIONNALITÉS ==========

    /**
     * Vérifier si un fichier existe.
     */
    public static function exists(string $filePath, string $disk = 'public'): bool
    {
        return Storage::disk($disk)->exists($filePath);
    }

    /**
     * Obtenir la taille d'un fichier en octets.
     */
    public static function size(string $filePath, string $disk = 'public'): ?int
    {
        return self::exists($filePath, $disk) ? Storage::disk($disk)->size($filePath) : null;
    }

    /**
     * Obtenir la taille formatée d'un fichier (KB, MB, GB).
     */
    public static function formattedSize(string $filePath, string $disk = 'public'): ?string
    {
        $size = self::size($filePath, $disk);
        if ($size === null) return null;

        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $unitIndex = 0;
        
        while ($size >= 1024 && $unitIndex < count($units) - 1) {
            $size /= 1024;
            $unitIndex++;
        }

        return round($size, 2) . ' ' . $units[$unitIndex];
    }

    /**
     * Obtenir la date de dernière modification d'un fichier.
     */
    public static function lastModified(string $filePath, string $disk = 'public'): ?int
    {
        return self::exists($filePath, $disk) ? Storage::disk($disk)->lastModified($filePath) : null;
    }

    /**
     * Obtenir le type MIME d'un fichier.
     */
    public static function mimeType(string $filePath, string $disk = 'public'): ?string
    {
        return self::exists($filePath, $disk) ? Storage::mimeType($filePath) : null;
    }

    /**
     * Obtenir l'extension d'un fichier.
     */
    public static function extension(string $filePath): string
    {
        return pathinfo($filePath, PATHINFO_EXTENSION);
    }

    /**
     * Obtenir le nom de fichier sans l'extension.
     */
    public static function filename(string $filePath): string
    {
        return pathinfo($filePath, PATHINFO_FILENAME);
    }

    /**
     * Obtenir le nom complet du fichier (avec extension).
     */
    public static function basename(string $filePath): string
    {
        return pathinfo($filePath, PATHINFO_BASENAME);
    }

    /**
     * Copier un fichier.
     */
    public static function copy(string $from, string $to, string $disk = 'public'): bool
    {
        if (!self::exists($from, $disk)) {
            return false;
        }

        return Storage::disk($disk)->copy($from, $to);
    }

    /**
     * Déplacer un fichier.
     */
    public static function move(string $from, string $to, string $disk = 'public'): bool
    {
        if (!self::exists($from, $disk)) {
            return false;
        }

        return Storage::disk($disk)->move($from, $to);
    }

    /**
     * Lister tous les fichiers d'un répertoire.
     */
    public static function listFiles(string $directory = '', string $disk = 'public', bool $recursive = false): Collection
    {
        $method = $recursive ? 'allFiles' : 'files';
        $files = Storage::disk($disk)->$method($directory);

        return collect($files)->map(function ($filePath) use ($disk) {
            return [
                'path' => $filePath,
                'name' => self::basename($filePath),
                'filename' => self::filename($filePath),
                'extension' => self::extension($filePath),
                'size' => self::size($filePath, $disk),
                'formatted_size' => self::formattedSize($filePath, $disk),
                'mime_type' => self::mimeType($filePath, $disk),
                'last_modified' => self::lastModified($filePath, $disk),
                'url' => self::url($filePath, $disk),
            ];
        });
    }

    /**
     * Lister les répertoires.
     */
    public static function listDirectories(string $directory = '', string $disk = 'public', bool $recursive = false): Collection
    {
        $method = $recursive ? 'allDirectories' : 'directories';
        return collect(Storage::disk($disk)->$method($directory));
    }

    /**
     * Filtrer les fichiers par extension.
     */
    public static function filterByExtension(Collection $files, array|string $extensions): Collection
    {
        $extensions = is_array($extensions) ? $extensions : [$extensions];
        $extensions = array_map('strtolower', $extensions);

        return $files->filter(function ($file) use ($extensions) {
            return in_array(strtolower($file['extension']), $extensions);
        });
    }

    /**
     * Obtenir les fichiers images d'un répertoire.
     */
    public static function getImages(string $directory = '', string $disk = 'public', bool $recursive = false): Collection
    {
        $files = self::listFiles($directory, $disk, $recursive);
        return self::filterByExtension($files, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp']);
    }

    /**
     * Obtenir les fichiers documents d'un répertoire.
     */
    public static function getDocuments(string $directory = '', string $disk = 'public', bool $recursive = false): Collection
    {
        $files = self::listFiles($directory, $disk, $recursive);
        return self::filterByExtension($files, ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt']);
    }

    /**
     * Obtenir les fichiers vidéos d'un répertoire.
     */
    public static function getVideos(string $directory = '', string $disk = 'public', bool $recursive = false): Collection
    {
        $files = self::listFiles($directory, $disk, $recursive);
        return self::filterByExtension($files, ['mp4', 'avi', 'mov', 'wmv', 'flv', 'webm', 'mkv']);
    }

    /**
     * Obtenir les fichiers audios d'un répertoire.
     */
    public static function getAudios(string $directory = '', string $disk = 'public', bool $recursive = false): Collection
    {
        $files = self::listFiles($directory, $disk, $recursive);
        return self::filterByExtension($files, ['mp3', 'wav', 'ogg', 'flac', 'aac', 'm4a']);
    }

    /**
     * Trier les fichiers.
     */
    public static function sortFiles(Collection $files, string $sortBy = 'name', string $direction = 'asc'): Collection
    {
        $direction = strtolower($direction) === 'desc' ? 'sortByDesc' : 'sortBy';
        return $files->$direction($sortBy)->values();
    }

    /**
     * Créer un répertoire.
     */
    public static function createDirectory(string $directory, string $disk = 'public'): bool
    {
        return Storage::disk($disk)->makeDirectory($directory);
    }

    /**
     * Supprimer un répertoire.
     */
    public static function deleteDirectory(string $directory, string $disk = 'public'): bool
    {
        return Storage::disk($disk)->deleteDirectory($directory);
    }

    /**
     * Vérifier si un répertoire existe.
     */
    public static function directoryExists(string $directory, string $disk = 'public'): bool
    {
        return Storage::disk($disk)->exists($directory);
    }

    /**
     * Streamer un fichier.
     */
    public static function stream(string $filePath, string $disk = 'public'): ?StreamedResponse
    {
        if (!self::exists($filePath, $disk)) {
            return null;
        }

        $stream = Storage::disk($disk)->readStream($filePath);
        $mimeType = self::mimeType($filePath, $disk) ?? 'application/octet-stream';

        return new StreamedResponse(function () use ($stream) {
            fpassthru($stream);
            if (is_resource($stream)) {
                fclose($stream);
            }
        }, 200, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . self::basename($filePath) . '"'
        ]);
    }

    /**
     * Générer un nom de fichier unique.
     */
    public static function generateUniqueFilename(string $originalFilename, string $directory = '', string $disk = 'public'): string
    {
        $extension = self::extension($originalFilename);
        $filename = self::filename($originalFilename);
        $counter = 1;
        $newFilename = $originalFilename;

        while (self::exists($directory . '/' . $newFilename, $disk)) {
            $newFilename = $filename . '_' . $counter . '.' . $extension;
            $counter++;
        }

        return $newFilename;
    }

    /**
     * Obtenir le contenu d'un fichier texte.
     */
    public static function getContent(string $filePath, string $disk = 'public'): ?string
    {
        return self::exists($filePath, $disk) ? Storage::disk($disk)->get($filePath) : null;
    }

    /**
     * Écrire du contenu dans un fichier.
     */
    public static function putContent(string $filePath, string $content, string $disk = 'public'): bool
    {
        return Storage::disk($disk)->put($filePath, $content);
    }

    /**
     * Ajouter du contenu à la fin d'un fichier.
     */
    public static function appendContent(string $filePath, string $content, string $disk = 'public'): bool
    {
        $existingContent = self::getContent($filePath, $disk) ?? '';
        return self::putContent($filePath, $existingContent . $content, $disk);
    }

    /**
     * Rechercher des fichiers par nom (avec wildcards).
     */
    public static function search(string $pattern, string $directory = '', string $disk = 'public', bool $recursive = false): Collection
    {
        $files = self::listFiles($directory, $disk, $recursive);
        
        return $files->filter(function ($file) use ($pattern) {
            return fnmatch($pattern, $file['name']);
        });
    }

    /**
     * Obtenir des statistiques sur un répertoire.
     */
    public static function getDirectoryStats(string $directory = '', string $disk = 'public'): array
    {
        $files = self::listFiles($directory, $disk, true);
        $directories = self::listDirectories($directory, $disk, true);
        
        $totalSize = $files->sum('size');
        $filesByExtension = $files->groupBy('extension');
        
        return [
            'total_files' => $files->count(),
            'total_directories' => $directories->count(),
            'total_size' => $totalSize,
            'formatted_total_size' => self::formatBytes($totalSize),
            'files_by_extension' => $filesByExtension->map->count(),
            'largest_file' => $files->sortByDesc('size')->first(),
            'newest_file' => $files->sortByDesc('last_modified')->first(),
        ];
    }

    /**
     * Formater les octets en unités lisibles.
     */
    private static function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $unitIndex = 0;
        
        while ($bytes >= 1024 && $unitIndex < count($units) - 1) {
            $bytes /= 1024;
            $unitIndex++;
        }

        return round($bytes, 2) . ' ' . $units[$unitIndex];
    }

    /**
     * Nettoyer un répertoire (supprimer les fichiers anciens).
     */
    public static function cleanDirectory(string $directory, int $daysOld, string $disk = 'public'): int
    {
        $files = self::listFiles($directory, $disk, true);
        $cutoffTime = now()->subDays($daysOld)->timestamp;
        $deletedCount = 0;

        foreach ($files as $file) {
            if ($file['last_modified'] < $cutoffTime) {
                if (self::delete($file['path'], $disk)) {
                    $deletedCount++;
                }
            }
        }

        return $deletedCount;
    }

    /**
     * Valider un fichier uploadé.
     */
    public static function validateFile(UploadedFile $file, array $rules = []): array
    {
        $errors = [];
        
        // Taille maximale (en MB)
        if (isset($rules['max_size']) && $file->getSize() > $rules['max_size'] * 1024 * 1024) {
            $errors[] = "Le fichier dépasse la taille maximale de {$rules['max_size']}MB";
        }

        // Extensions autorisées
        if (isset($rules['extensions']) && !in_array(strtolower($file->getClientOriginalExtension()), $rules['extensions'])) {
            $errors[] = "L'extension du fichier n'est pas autorisée";
        }

        // Types MIME autorisés
        if (isset($rules['mime_types']) && !in_array($file->getMimeType(), $rules['mime_types'])) {
            $errors[] = "Le type de fichier n'est pas autorisé";
        }

        return $errors;
    }
}