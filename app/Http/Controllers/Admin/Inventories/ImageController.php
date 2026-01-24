<?php

namespace App\Http\Controllers\Admin\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Image;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{
    public function destroy(Image $image)
    {
        Storage::delete($image->path);

        $image->delete();
    }
}
