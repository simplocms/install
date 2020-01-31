<?php namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\MediaFileRequest;
use App\Models\Media\Directory;
use App\Models\Media\File;
use App\Structures\Enums\SingletonEnum;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class MediaLibraryController extends AdminController
{
    public const MEDIA_LIBRARY_SORT_CACHE_KEY = 'media_library_sort';
    public const MEDIA_LIBRARY_SORT_DIR_CACHE_KEY = 'media_library_sort_dir';

    /**
     * @var \Illuminate\Contracts\Cache\Repository
     */
    private $cache;

    /**
     * @param \Illuminate\Contracts\Cache\Repository $cache
     */
    public function __construct(Repository $cache)
    {
        parent::__construct();

        $this->cache = $cache;
    }

    /**
     * Show page with media library.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showMediaLibrary()
    {
        return view('admin.media-library.index');
    }


    /**
     * GET: directory tree.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDirectoryTree()
    {
        return response()->json([
            'tree' => [
                'name' => trans('admin/media_library.root_directory'),
                'children' => Directory::getTree()->toArray()
            ]
        ]);
    }


    /**
     * GET: file detail.
     *
     * @param \App\Models\Media\File $file
     * @return \Illuminate\Http\JsonResponse
     */
    public function getFileDetail(File $file): JsonResponse
    {
        return response()->json([
            'file' => $file->toArray()
        ]);
    }


    /**
     * GET: directory contents.
     *
     * @param \App\Models\Media\Directory $directory
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDirectoryContents(Request $request, Directory $directory = null)
    {
        $filesQuery = $directory ? $directory->files() : File::ofRoot();
        $directoriesQuery = $directory ? $directory->children() : Directory::ofRoot();

        return $this->getContentResponse($request, $filesQuery, $directoriesQuery);
    }


    /**
     * GET: search contents.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSearchContent(Request $request)
    {
        $searchText = $request->get('query', '');

        if (!strlen(trim($searchText))) {
            return response('', 204);
        }

        $filesQuery = File::query()->search($searchText);
        $directoriesQuery = Directory::query()->search($searchText);

        return $this->getContentResponse($request, $filesQuery, $directoriesQuery);
    }


    /**
     * POST: Upload image and store as new media file.
     *
     * @param \App\Models\Media\Directory $directory
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function uploadAndStoreFile(Request $request, ?Directory $directory = null)
    {
        $uploadedFile = SingletonEnum::mediaLibrary()->receiveFile($request);
        if (!$uploadedFile) {
            return response('', 204);
        }

        $file = SingletonEnum::mediaLibrary()->createFileFromUpload($uploadedFile, $directory);

        return response()->json([
            'file' => $file->toArray()
        ]);
    }


    /**
     * POST: Upload image and override existing media file.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Media\File $file
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function uploadAndOverrideFile(Request $request, File $file)
    {
        $uploadedFile = SingletonEnum::mediaLibrary()->receiveFile($request);
        if (!$uploadedFile) {
            return response('', 204);
        }

        if ($file->overrideFile($uploadedFile)) {
            $file->save();
        }

        return response()->json([
            'file' => $file->toArray()
        ]);
    }


    /**
     * PUT: Update file.
     *
     * @param \App\Http\Requests\Admin\MediaFileRequest $request
     * @param \App\Models\Media\File $file
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function updateFile(MediaFileRequest $request, File $file)
    {
        $file->update($request->getValues());

        if ($request->rotateLeft() || $request->rotateRight()) {
            $file->rotateImage($request->rotateRight());
        } else if ($request->resize()) {
            $file->resize((int)$request->input('width'), (int)$request->input('height'));
        }

        return response()->json([
            'file' => $file->toArray()
        ]);
    }


    /**
     * POST: Store directory.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Media\Directory|null $directory
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeDirectory(Request $request, ?Directory $directory = null)
    {
        $this->validateDirectoryRequest($request);

        $newDirectory = SingletonEnum::mediaLibrary()->createMediaDirectory($request->input('name'), $directory);
        $subTree = is_null($directory) ? Directory::getTree() : $directory->children()->get()->toTree();

        return response()->json([
            'directory' => $newDirectory->toArray(),
            'subTree' => $subTree
        ]);
    }


    /**
     * PUT: Update directory.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Media\Directory $directory
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateDirectory(Request $request, Directory $directory)
    {
        $this->validateDirectoryRequest($request);

        $directory->update([
            'name' => $request->input('name')
        ]);

        return response()->json([
            'directory' => $directory->toArray()
        ]);
    }


    /**
     * DELETE: delete files.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteFiles(Request $request)
    {
        $idsInput = $request->route('files');
        $ids = is_array($idsInput) ? $idsInput : json_decode($idsInput);

        foreach (File::findOrFail($ids) as $file) {
            $file->delete();
        }

        return response('', 204);
    }


    /**
     * DELETE: Delete directory.
     *
     * @param \App\Models\Media\Directory $directory
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function deleteDirectory(Directory $directory)
    {
        $directory->delete();
        return response()->json([
            'parent_id' => $directory->parent_id
        ]);
    }


    /**
     * DELETE: Cancel upload.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function cancelUpload(Request $request)
    {
        SingletonEnum::mediaLibrary()->cancelUpload($request);
        return response('', 204);
    }


    /**
     * Get content response.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Illuminate\Database\Eloquent\Builder $filesQuery
     * @param \Illuminate\Database\Eloquent\Builder $directoriesQuery
     * @return \Illuminate\Http\JsonResponse
     */
    private function getContentResponse(Request $request, $filesQuery, $directoriesQuery)
    {
        $page = $request->get('page');
        $sort = $request->get('sort', $this->cache->get(self::MEDIA_LIBRARY_SORT_CACHE_KEY));
        $type = (array)$request->get('type', []);

        // Sorting
        if ($sort && in_array($sort, ['name', 'updated_at'])) {
            $direction = $request->get('dir', $this->cache->get(self::MEDIA_LIBRARY_SORT_DIR_CACHE_KEY));
            $direction = strtolower($direction) === 'desc' ? 'desc' : 'asc';
            $filesQuery->orderBy($sort, $direction);
            $directoriesQuery->orderBy($sort, $direction);

            $this->cache->forever(self::MEDIA_LIBRARY_SORT_CACHE_KEY, $sort);
            $this->cache->forever(self::MEDIA_LIBRARY_SORT_DIR_CACHE_KEY, $direction);
        }

        if (($key = \array_search('image', $type, true)) !== false) {
            unset($type[$key]);
            $type = \array_unique(\array_merge($type, SingletonEnum::mediaLibrary()->getSelectableImageMimeTypes()));
        }

        if (\count($type)) {
            $filesQuery->whereIn('mime_type', $type);
        }

        return response()->json([
            'directories' => $directoriesQuery->get()->toArray(),
            'files' => $filesQuery->paginate(40, ['*'], 'page', $page)
        ]);
    }


    /**
     * Validate directory request.
     *
     * @param \Illuminate\Http\Request $request
     */
    private function validateDirectoryRequest(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:50'
        ]);
    }
}
