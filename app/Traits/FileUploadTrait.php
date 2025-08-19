<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

trait FileUploadTrait
{
    public function handleFileUpload(Request $request, string $fieldName, ?string $oldpath = null, string $dir = 'uploads'): ?string
    {
        if (!$request->hasFile($fieldName)) {
            return $oldpath;
        }

        // حذف القديم
        if ($oldpath && File::exists(public_path($oldpath))) {
            File::delete(public_path($oldpath));
        }

        $file = $request->file($fieldName);
        $extension = $file->getClientOriginalExtension();
        $updatedFileName = Str::random(30) . '.' . $extension;
        $file->move(public_path($dir), $updatedFileName);

        return $dir . '/' . $updatedFileName;
    }

    public function deleteFile(string $path): void
    {
        if ($path && File::exists(public_path($path))) {
            File::delete(public_path($path));
        }
    }

    public function handleMultipleFileUpload(Request $request, string $fieldName, string $dir = 'uploads'): array
    {
        $paths = [];

        if (!$request->hasFile($fieldName)) {
            return $paths;
        }

        foreach ($request->file($fieldName) as $file) {
            $extension = $file->getClientOriginalExtension();
            $updatedFileName = Str::random(30) . '.' . $extension;
            $file->move(public_path($dir), $updatedFileName);
            $paths[] = $dir . '/' . $updatedFileName;
        }

        return $paths;
    }

    public function deleteMultipleFiles(array $paths): void
    {
        foreach ($paths as $path) {
            $this->deleteFile($path);
        }
    }

    public function updateFile(?string $oldPath, $newFile, string $dir = 'uploads'): ?string
    {
        if ($newFile) {
            if ($oldPath && File::exists(public_path($oldPath))) {
                File::delete(public_path($oldPath));
            }

            $extension = $newFile->getClientOriginalExtension();
            $updatedFileName = Str::random(30) . '.' . $extension;
            $newFile->move(public_path($dir), $updatedFileName);

            return $dir . '/' . $updatedFileName;
        }

        return $oldPath;
    }

    public function updateMultipleFiles(array $oldPaths, array $newFiles, string $dir = 'uploads'): array
    {
        $resultPaths = [];

        foreach ($oldPaths as $index => $oldPath) {
            $newFile = $newFiles[$index] ?? null;

            if ($newFile) {
                if ($oldPath && File::exists(public_path($oldPath))) {
                    File::delete(public_path($oldPath));
                }

                $extension = $newFile->getClientOriginalExtension();
                $updatedFileName = Str::random(30) . '.' . $extension;
                $newFile->move(public_path($dir), $updatedFileName);

                $resultPaths[$index] = $dir . '/' . $updatedFileName;
            } else {
                $resultPaths[$index] = $oldPath;
            }
        }

        return $resultPaths;
    }
}
