<?php

use Illuminate\Database\Seeder;
use App\Models\Article\Flag;

class ArticleFlagsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\Article\Flag::unguard();
        foreach (\App\Models\Web\Language::all() as $language) {
            switch ($language->language_code) {
                case 'en':
                    $data = ['url' => 'articles', 'name' => 'Articles'];
                    break;
                case 'de':
                    $data = ['url' => 'artikel', 'name' => 'Artikel'];
                    break;
                default:
                    $data = ['url' => 'clanky', 'name' => 'Články'];
                    break;
            }
            $data['language_id'] = $language->id;

            if (!Flag::where('url', $data['url'])->exists()) {
                \App\Models\Article\Flag::create($data);
            }
        }
        \App\Models\Article\Flag::reguard();
    }
}
