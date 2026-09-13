<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait FileTrait
{
    public function uploadFile($folder, $file)
    {
        // random string 5 character
        $rand = Str::random(5);
        $newFileName = $rand.'_'.time().'.'.$file->getClientOriginalExtension();
        $path = $file->storeAs($folder, $newFileName);

        return $path;
    }

    public function deleteFile($file)
    {
        if ($file && Storage::exists($file)) {
            Storage::delete($file);
        }
    }

    public function updateFile($folder, $old_file, $new_file)
    {
        $this->deleteFile($old_file);

        return $this->uploadFile($folder, $new_file);
    }

    public function getFile($file)
    {
        return Storage::url($file);
    }
}
