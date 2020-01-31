<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MediaLibraryMigration extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->migrateArticleImages();
        $this->migratePageImages();
        $this->migratePhotogalleryImages();
        $this->migrateUniversalModuleImages();
        $this->migrateImageModuleImages();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // no way back
    }


    /**
     * Migrate images of articles.
     */
    private function migrateArticleImages()
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->media('image_id');
            $table->media('og_image_id');
        });

        Schema::table('article_photos', function (Blueprint $table) {
            $table->media('image_id');
        });

        $mediaLibrary = \App\Structures\Enums\SingletonEnum::mediaLibrary();
        $uploadPath = public_path('media/upload');

        $articles = DB::table("articles")->get();

        foreach ($articles as $article) {
            $photoDirectory = md5($article->created_at . '-' . $article->id);
            $photoPath = $uploadPath . '/articles/' . $photoDirectory;
            $image = null;
            $ogImage = null;

            // Main image
            if ($article->image) {
                try {
                    $image = $mediaLibrary->importFile(
                        "$photoPath/{$article->image}", "img-" . $article->title
                    );
                } catch (\Exception $e) {
                    // go on
                    dump("Article File $photoPath/{$article->image} not found.", $e);
                }
            }

            // OG image
            if ($article->og_image) {
                try {
                    $ogImage = $mediaLibrary->importFile(
                        "$photoPath/{$article->og_image}", "og-{$article->title}"
                    );
                } catch (\Exception $e) {
                    dump("Article file $photoPath/{$article->og_image} not found.", $e);
                    // go on
                }
            }

            // Update if image was imported
            if ($image || $ogImage) {
                \App\Models\Article\Article::where('id', $article->id)->update([
                    'image_id' => optional($image)->getKey(),
                    'og_image_id' => optional($ogImage)->getKey(),
                ]);
            }

            // Migrate article photogallery:

            $photos = DB::table("article_photos")->where('article_id', $article->id)->get();

            foreach ($photos as $photo) {
                try {
                    $image = $mediaLibrary->importFile(
                        "$photoPath/big/{$photo->image}", "gallery-{$article->title}"
                    );

                    DB::table('article_photos')->where('id', $photo->id)->update([
                        'image_id' => $image->getKey()
                    ]);
                } catch (\Exception $e) {
                    dump("Article photo $photoPath/big/{$photo->image} not found.", $e);
                    // go on
                }
            }
        }

        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn('image', 'og_image', 'thumbnail');
        });

        Schema::table('article_photos', function (Blueprint $table) {
            $table->dropColumn('image', 'type', 'size', 'temporary_id');
            $table->dropForeign(['user_id']);
            $table->dropSoftDeletes();
        });
    }


    /**
     * Migrate images of pages.
     */
    private function migratePageImages()
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->media('image_id');
            $table->media('og_image_id');
            $table->renameColumn('published_at', 'publish_at');
            $table->renameColumn('unpublished_at', 'unpublish_at');
        });

        $mediaLibrary = \App\Structures\Enums\SingletonEnum::mediaLibrary();
        $uploadPath = public_path('media/upload');

        $pages = DB::table("pages")->get();

        foreach ($pages as $page) {
            $createdAt = \Carbon\Carbon::createFromTimeString($page->created_at);
            $imageDirectory = md5($createdAt->format('Y-m-d') . '-' . $page->id);
            $imagePath = $uploadPath . '/pages/' . $imageDirectory;
            $image = null;
            $ogImage = null;

            // Main image
            if ($page->image) {
                try {
                    $image = $mediaLibrary->importFile(
                        "$imagePath/{$page->image}", "img-" . $page->name
                    );
                } catch (\Exception $e) {
                    dump("Page file $imagePath/{$page->image} not found.", $e);
                    // go on
                }
            }

            // OG image
            if ($page->og_image) {
                try {
                    $ogImage = $mediaLibrary->importFile(
                        "$imagePath/{$page->og_image}", "og-{$page->name}"
                    );
                } catch (\Exception $e) {
                    dump("Page file $imagePath/{$page->og_image} not found.", $e);
                    // go on
                }
            }

            // Update if image was imported
            if ($image || $ogImage) {
                \App\Models\Page\Page::where('id', $page->id)->update([
                    'image_id' => optional($image)->getKey(),
                    'og_image_id' => optional($ogImage)->getKey(),
                ]);
            }
        }

        Schema::table('pages', function (Blueprint $table) {
            $table->dropColumn('image', 'og_image');
        });
    }


    /**
     * Migrate images of photogalleries.
     */
    private function migratePhotogalleryImages()
    {
        Schema::table('photogallery_photos', function (Blueprint $table) {
            $table->media('image_id');
        });

        $mediaLibrary = \App\Structures\Enums\SingletonEnum::mediaLibrary();
        $uploadPath = public_path('media/upload');

        $photogalleries = DB::table('photogalleries')->get()->keyBy('id');
        $photos = DB::table("photogallery_photos")->get();

        foreach ($photos as $photo) {
            $photogallery = $photogalleries->get($photo->photogallery_id);
            $imageDirectory = md5($photogallery->created_at . '-' . $photo->photogallery_id);
            $imagePath = $uploadPath . '/photogalleries/' . $imageDirectory . '/big';

            try {
                $image = $mediaLibrary->importFile(
                    "$imagePath/{$photo->image}", "img-" . $photogallery->title
                );

                DB::table('photogallery_photos')->where('id', $photo->id)->update([
                    'image_id' => $image->getKey()
                ]);
            } catch (\Exception $e) {
                dump("Photogallery file $imagePath/{$photo->image} not found.", $e);
                // go on
            }
        }

        Schema::table('photogallery_photos', function (Blueprint $table) {
            $table->dropColumn('image', 'type', 'size', 'temporary_id');
            $table->dropForeign(['user_id']);
            $table->dropSoftDeletes();
        });
    }


    /**
     * Migrate images of image module.
     */
    private function migrateImageModuleImages()
    {
        // Migrate Image module if is installed.
        $imageModule = \App\Models\Module\InstalledModule::findNamed('Image');
        if (!$imageModule || !$imageModule->module) {
            return;
        }

        Schema::table('module_image_configurations', function (Blueprint $table) {
            $table->media('image_id');
        });

        $mediaLibrary = \App\Structures\Enums\SingletonEnum::mediaLibrary();
        $uploadPath = public_path('media/upload');

        $configurations = DB::table("module_image_configurations")->get();

        foreach ($configurations as $configuration) {
            $imageUrl = $configuration->image;
            $fileName = basename($imageUrl);
            $filePath = "$uploadPath/Shared/$fileName";

            try {
                $image = $mediaLibrary->importFile($filePath, "shared-" . ($configuration->alt ?? 'image'));

                DB::table("module_image_configurations")
                    ->where('id', $configuration->id)
                    ->update(['image_id' => $image->getKey()]);
            } catch (\Exception $e) {
                // go on
                dump("Shared File $filePath not found.", $e);
            }
        }

        Schema::table('module_image_configurations', function (Blueprint $table) {
            $table->dropColumn('image');
        });
    }


    /**
     * Migrate images of universal modules.
     */
    private function migrateUniversalModuleImages()
    {
        $mediaLibrary = \App\Structures\Enums\SingletonEnum::mediaLibrary();
        $uploadPath = public_path('media/upload');

        $items = DB::table("universal_module_items")->get();

        foreach ($items as $item) {
            $content = unserialize($item->content);
            try {
                $module = \App\Structures\Enums\SingletonEnum::universalModules()->findOrFail($item->prefix);
            } catch (\Exception $e) {
                dump("Module {$item->prefix} not found.", $e);
                continue;
            }

            $filesPath = $uploadPath . '/' . $item->prefix;
            $files = $module['files'] ?? [];

            foreach ($module->getFileFields() as $field) {
                if (!isset($content[$field->getName()])) {
                    continue;
                }

                $fileName = $content[$field->getName()];

                try {
                    $file = $mediaLibrary->importFile(
                        "$filesPath/{$fileName}", $item->prefix . '-' . $field->getName()
                    );

                    $content[$field->getName()] = $file->getKey();
                } catch (\Exception $e) {
                    $content[$field->getName()] = null;
                }
            }

            if ($files) {
                \App\Models\UniversalModule\UniversalModuleItem::where('id', $item->id)
                    ->update([
                        'content' => serialize($content)
                    ]);
            }
        }
    }
}
