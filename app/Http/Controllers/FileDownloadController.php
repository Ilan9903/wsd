<?php

namespace App\Http\Controllers;

use App\Models\FileOrFolder;
use App\Models\Link;
use App\Models\User;
use App\Notifications\DownloadLink;
use App\Services\Minio\Bucket;
use Hopla\DownloadManagement\Facades\DownloadManager;
use Illuminate\Support\Carbon;
use STS\ZipStream\Builder;
use STS\ZipStream\Exceptions\UnsupportedSourceDiskException;

class FileDownloadController extends Controller
{
    /**
     * @param  string  $id
     * @return string|Builder
     *
     * @throws UnsupportedSourceDiskException
     * @throws \Exception
     */
    public function download(string $id)
    {
        (new Bucket)->getBucket();

        $fileSystemItem = FileOrFolder::findOrFail($id);
        $link = Link::findOrFail($fileSystemItem->link_id);

        if ($link->expired_at && Carbon::parse($link->expired_at)->endOfDay()->isPast()) {
            return response()->json([
                'message' => 'Lien expiré',
            ], 500);
        }

        if ($link->has_receipt) {
            $user = User::findOrFail($link->user_id);

            $user->notify(new DownloadLink($link));
        }

        return DownloadManager::downloadFolder($fileSystemItem);
    }
}
