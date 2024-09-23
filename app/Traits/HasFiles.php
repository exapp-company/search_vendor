<?php

namespace App\Traits;

use App\Models\File;
use Illuminate\Database\Eloquent\Model;

trait HasFiles
{
    public static function bootHasFiles()
    {
        static::saved(function (Model $model) {
            $request = request();
            if ($request->has('files')) {
                $images = $request->input('files', []);
                //dd($images);
                $files = File::whereIn('id', $images)->get();
                foreach ($files as $file) {
                    $file->imagable()->associate($model);
                    $file->save();
                }
            }
        });
    }
    public function files()
    {
        return $this->morphMany(File::class, "imagable");
    }
}
