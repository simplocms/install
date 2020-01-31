<?php

namespace Tests\Browser;

use App\Models\Article\Article;
use App\Models\Article\Category;
use App\Models\Article\Content;
use App\Models\Article\Flag;
use App\Models\Entrust\Role;
use App\Models\User;
use App\Models\Web\Url;
use Carbon\Carbon;
use Faker\Factory;
use Tests\Browser\Components\CKEditor;
use Tests\Browser\Components\DatePicker;
use Tests\Browser\Components\FancyTree;
use Tests\Browser\Components\GridEditor;
use Tests\Browser\Components\JGrowl;
use Tests\Browser\Components\SweetAlertModal;
use Tests\Browser\Components\TableActions;
use Tests\Browser\Components\TimePicker;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;

class ArticlesTest extends DuskTestCase
{
    /**
     * Selector for table row.
     *
     * @var string
     */
    private $tableRowSelector = '.datatable > tbody > tr';

    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        parent::setUp();

        Browser::$userResolver = function () {
            return $this->createUserWithPermissions([
                'articles-create', 'articles-edit', 'articles-delete'
            ]);
        };
    }


    /**
     * Clean up the testing environment before the next test.
     *
     * @return void
     */
    public function tearDown()
    {
        Article::getQuery()->delete();
        Category::getQuery()->delete();
        Flag::getQuery()->delete();
        User::getQuery()->delete();
        Role::getQuery()->delete();
        Url::getQuery()->delete();

        parent::tearDown();

        // This line is necessary to avoid problems with asynchronous request,
        // that causes alert, making other test fail. (https://github.com/laravel/dusk/issues/460)
        $this->closeAll();
    }


    /**
     * Test index page as a user with full permissions.
     *
     * @throws \Exception
     * @throws \Throwable
     */
    public function testIndexPageWithFullPermissions()
    {
        $article = $this->createArticle();

        // Full permissions
        $this->browse(function (Browser $browser) use ($article) {
            $browser->login()
                ->visit(route('admin.articles.index', $article->flag->url))
                ->assertUrlIs(route('admin.articles.index', $article->flag->url))
                ->with('.page-title', function (Browser $browser) use ($article) {
                    $browser->assertSee($article->flag->name)
                        ->assertSee(trans('admin/article/general.descriptions.index'));
                })
                ->waitFor($this->tableRow(1))
                ->assertSeeIn('.content > a.btn', trans('admin/article/general.index.btn_create'))
                ->assertSeeIn('.breadcrumb-elements', trans('admin/article/general.index.btn_create'))
                ->within($this->tableRow(1), function (Browser $browser) use ($article) {
                    $browser->assertSeeIn('> td:nth-child(1)', $article->title)
                        ->assertSeeIn(
                            '> td:nth-child(5)',
                            mb_strtoupper(trans('admin/article/general.status.published'))
                        )
                        ->assertVisible('> td:nth-child(6)')
                        ->within(new TableActions, function (Browser $browser) {
                            $browser->click('@toggle')
                                ->assertSeeIn(
                                    '@item:nth-child(1)', trans('admin/article/general.index.btn_edit')
                                )
                                ->assertSeeIn(
                                    '@item:nth-child(2)',
                                    trans('admin/article/general.index.btn_preview')
                                )
                                ->assertSeeIn(
                                    '@item:nth-child(3)',
                                    trans('admin/article/general.index.btn_duplicate')
                                )
                                ->assertSeeIn(
                                    '@item:nth-child(4)', trans('admin/article/general.index.btn_delete')
                                );
                        });
                });
        });
    }


    /**
     * Test index page as a user with no permissions.
     *
     * @throws \Exception
     * @throws \Throwable
     */
    public function testIndexPageWithNoPermissions()
    {
        $this->browse(function (Browser $browser) {
            /** @var \App\Models\User $user */
            $user = factory(\App\Models\User::class)->create();

            /** @var \App\Models\Article\Flag $flag */
            $flag = factory(Flag::class)->create();

            $browser->loginAs($user)
                ->visit(route('admin.articles.index', $flag->url))
                ->assertUrlIs(route('admin.articles.index', $flag->url))
                ->assertSee('403');
        });
    }


    /**
     * Test index page as a user with no permissions.
     *
     * @throws \Exception
     * @throws \Throwable
     */
    public function testIndexPageWithCreatePermissions()
    {
        /** @var \App\Models\User $user */
        $user = $this->createUserWithPermissions(['articles-create']);
        $article = $this->createArticle();

        $this->browse(function (Browser $browser) use ($user, $article) {
            $browser->loginAs($user)
                ->visit(route('admin.articles.index', $article->flag->url))
                ->with('.page-title', function (Browser $browser) use ($article) {
                    $browser->assertSee($article->flag->name)
                        ->assertSee(trans('admin/article/general.descriptions.index'));
                })
                ->assertSeeIn('.content > a.btn', trans('admin/article/general.index.btn_create'))
                ->assertSeeIn('.breadcrumb-elements', trans('admin/article/general.index.btn_create'))
                ->waitFor($this->tableRow(1))
                ->within($this->tableRow(1), function (Browser $browser) {
                    $browser->assertMissing('> td:nth-child(6)');
                });
        });
    }


    /**
     * Test button to create an article.
     *
     * @throws \Exception
     * @throws \Throwable
     */
    public function testButtonCreateArticle()
    {
        /** @var \App\Models\Article\Flag $flag */
        $flag = factory(Flag::class)->create();

        $this->browse(function (Browser $browser) use ($flag) {
            $browser->login()
                ->visit(route('admin.articles.index', $flag->url))
                ->clickLink(trans('admin/article/general.index.btn_create'), '.content > a.btn')
                ->assertUrlIs(route('admin.articles.create', $flag->url))
                ->with('.page-title', function (Browser $browser) use ($flag) {
                    $browser->assertSee($flag->name)
                        ->assertSee(trans('admin/article/general.descriptions.create'));
                });
        });
    }


    /**
     * Test action to edit the article.
     *
     * @throws \Exception
     * @throws \Throwable
     */
    public function testActionEditArticle()
    {
        $article = $this->createArticle();

        $this->browse(function (Browser $browser) use ($article) {
            $browser->login()
                ->visit(route('admin.articles.index', $article->flag->url))
                ->waitFor($this->tableRow(1))
                ->within($this->tableRow(1), function (Browser $browser) use ($article) {
                    $browser
                        ->within(new TableActions, function (Browser $browser) {
                            $browser->clickNthChild(1);
                        })
                        ->assertUrlIs(route('admin.articles.edit', [
                            'flag' => $article->flag->url,
                            'article' => $article->getKey()
                        ]));
                })
                ;
        });
    }


    /**
     * Test action to duplicate the article.
     *
     * @throws \Exception
     * @throws \Throwable
     */
    public function testActionDuplicateArticle()
    {
        $article = $this->createArticle();
        $this->browse(function (Browser $browser) use ($article) {
            $browser->login()
                ->visit(route('admin.articles.index', $article->flag->url))
                ->waitFor($this->tableRow(1))
                ->within($this->tableRow(1), function (Browser $browser) {
                    $browser->within(new TableActions, function (Browser $browser) {
                        $browser->clickNthChild(3);
                    });
                })
                ->waitForReload()
                ->waitFor((new JGrowl())->selector())
                ->within(new JGrowl, function (Browser $browser) {
                    $browser->assertSays(
                        trans('admin/general.flash_level.success'),
                        trans('admin/article/general.notifications.duplicated')
                    );
                });
        });
    }


    /**
     * Test action to delete the article.
     *
     * @throws \Exception
     * @throws \Throwable
     */
    public function testActionDeleteArticle()
    {
        $article = $this->createArticle();

        $this->browse(function (Browser $browser) use ($article) {
            $browser->login()
                ->visit(route('admin.articles.index', $article->flag->url))
                ->waitFor($this->tableRow(1))
                ->within($this->tableRow(1), function (Browser $browser) {
                    $browser->within(new TableActions, function (Browser $browser) {
                        $browser->clickNthChild(4);
                    });
                })
                ->waitFor((new SweetAlertModal)->selector())
                ->within(new SweetAlertModal, function (Browser $browser) {
                    $browser->assertIsDelete(trans('admin/article/general.confirm_delete.title'))->confirm();
                })
                ->waitForReload()
                ->waitFor((new JGrowl())->selector())
                ->within(new JGrowl, function (Browser $browser) {
                    $browser->assertSays(
                        trans('admin/general.flash_level.success'),
                        trans('admin/article/general.notifications.deleted')
                    );
                });
        });

        $article->refresh();
        $this->assertNotNull($article->deleted_at);
    }


    /**
     * Test for creating an article.
     *
     * @throws \Exception
     * @throws \Throwable
     */
    public function testFormCreateArticle()
    {
        $this->browse(function (Browser $browser) {
            /** @var \App\Models\Article\Category $category */
            $category = factory(Category::class)->create();

            /** @var \App\Models\Article\Article $article */
            $article = factory(Article::class)->make([
                'unpublish_at' => Carbon::now()->addMonth(1)->second(0)->minute(0)
            ]);

            $tabSelector = ".content .tabbable > .nav > li";

            $browser->login()
                ->resize(1280, 1280)
                ->visit(route('admin.articles.create', $category->flag->url))
                ->with('.page-title', function (Browser $browser) use ($category) {
                    $browser->assertSee($category->flag->name)
                        ->assertSee(trans('admin/article/general.descriptions.create'));
                })
                ->assertSeeIn("$tabSelector.active", trans('admin/article/form.tabs.general'))
                ->assertMissing("$tabSelector > a[href='#grid']")
                ->assertMissing("input[name='tags']")
                // Fill values
                ->type('title', $article->title)
                ->type('perex', $article->perex)
                ->within(new CKEditor('textarea[name="text"]'), function (Browser $browser) use ($article) {
                    $browser->typeIn($article->text);
                })
                //// Switch to categories tab
                ->within(new FancyTree('.tree-checkbox'), function (Browser $browser) use ($category) {
                    $browser->clickNode($category->name);
                })
                //// Switch to SEO tab
                ->click("$tabSelector > a[href='#seo']")
                // Fill values
                ->type('seo_title', $article->seo_title)
                ->type('seo_description', $article->seo_description)
                //// Switch to OG tags tab
                ->click("$tabSelector > a[href='#open-graph']")
                // Fill values
                ->type('open_graph[title]', $article->open_graph->get('title'))
                ->type('open_graph[type]', $article->open_graph->get('type'))
                ->type('open_graph[url]', $article->open_graph->get('url'))
                ->type('open_graph[description]', $article->open_graph->get('description'))
                //// Submit
                ->click('#articles-form button.btn[type="submit"]')
                ->waitForReload()
                ->assertUrlIs(route('admin.articles.index', $category->flag->url))
                ->waitFor((new JGrowl())->selector())
                ->within(new JGrowl, function (Browser $browser) {
                    $browser->assertSays(
                        trans('admin/general.flash_level.success'),
                        trans('admin/article/general.notifications.created')
                    );
                });

            /** @var \App\Models\Article\Article $newArticle */
            $newArticle = Article::first();
            $this->assertNotNull($newArticle);
            $this->assertEquals($article->title, $newArticle->title);
            $this->assertEquals($article->perex, $newArticle->perex);
            $this->assertNotNull($newArticle->text);

            $this->assertEquals(1, $newArticle->categories()->count());

            $this->assertEquals($article->seo_title, $newArticle->seo_title);
            $this->assertEquals($article->seo_description, $newArticle->seo_description);

            $this->assertEquals($article->open_graph->get('title'), $newArticle->open_graph->get('title'));
            $this->assertEquals($article->open_graph->get('type'), $newArticle->open_graph->get('type'));
            $this->assertEquals($article->open_graph->get('url'), $newArticle->open_graph->get('url'));
            $this->assertEquals(
                $article->open_graph->get('description'), $newArticle->open_graph->get('description')
            );

            $this->assertNull($newArticle->getActiveContent());
        });
    }


    /**
     * Test for creating an article with enabled extensions.
     *
     * @throws \Exception
     * @throws \Throwable
     */
    public function testFormCreateArticleWithExtensions()
    {
        $this->browse(function (Browser $browser) {
            /** @var \App\Models\Article\Flag $flag */
            $flag = factory(Flag::class)->create([
                'use_tags' => true,
                'use_grid_editor' => true,
            ]);

            /** @var \App\Models\Article\Category $category */
            $category = factory(Category::class)->create([
                'flag_id' => $flag->getKey()
            ]);

            /** @var \App\Models\Article\Article $article */
            $article = factory(Article::class)->make([
                'flag_id' => $flag->getKey()
            ]);

            $faker = Factory::create('en_GB');
            $tags = array_unique($faker->words());

            $tabSelector = ".content .tabbable > .nav > li";

            $browser->login()
                ->visit(route('admin.articles.create', $category->flag->url))
                ->assertSeeIn("$tabSelector.active", trans('admin/article/form.tabs.general'))
                ->assertVisible("$tabSelector > a[href='#grid']")
                ->assertMissing('textarea[name="text"]')
                // Fill values
                ->type('title', $article->title)
                ->type('perex', $article->perex)
                ->value('input[name="tags"]', join(',', $tags))
                //// Categories
                ->within(new FancyTree('.tree-checkbox'), function (Browser $browser) use ($category) {
                    $browser->clickNode($category->name);
                })
                //// Switch to GridEditor tab
                ->click("$tabSelector > a[href='#grid']")
//                ->within(new GridEditor, function (Browser $browser) {
//                    $browser->addNewRow('4-4-4');
//                })
                //// Submit
                ->click('#articles-form button.btn[type="submit"]')
                ->waitForReload()
                ->assertUrlIs(route('admin.articles.index', $category->flag->url))
                ->waitFor((new JGrowl())->selector())
                ->within(new JGrowl, function (Browser $browser) {
                    $browser->assertSays(
                        trans('admin/general.flash_level.success'),
                        trans('admin/article/general.notifications.created')
                    );
                });

            /** @var \App\Models\Article\Article $newArticle */
            $newArticle = Article::first();
            $this->assertNotNull($newArticle);
            $this->assertEquals($article->title, $newArticle->title);
            $this->assertEquals($article->perex, $newArticle->perex);
            $this->assertEquals(count($tags), $newArticle->tags->count());
            $this->assertEquals(count($tags), $newArticle->tags->pluck('name')->intersect($tags)->count());
            $this->assertNull($newArticle->text);

            $this->assertEquals(1, $newArticle->categories()->count());

            $this->assertNull($newArticle->seo_title);
            $this->assertNull($newArticle->seo_description);

            $this->assertNull($newArticle->open_graph->get('title'));
            $this->assertNull($newArticle->open_graph->get('type'));
            $this->assertNull($newArticle->open_graph->get('description'));
            $this->assertNull($newArticle->open_graph->get('url'));

            $this->assertNull($newArticle->unpublish_at);

            $this->assertNotNull($newArticle->getActiveContent());
//            $this->assertNotEmpty($newArticle->getActiveContent()->getRaw());
        });
    }


    /**
     * Test for editing the category.
     *
     * @throws \Exception
     * @throws \Throwable
     */
    public function testFormEditArticle()
    {
        $this->browse(function (Browser $browser) {
            $article = $this->createArticle([
                'unpublish_at' => Carbon::now()->addMonth(1)->second(0)->minute(0)
            ]);

            /** @var \App\Models\Article\Category $category */
            $category = factory(Category::class)->create([
                'flag_id' => $article->flag_id
            ]);

            /** @var \App\Models\Article\Article $newValues */
            $newValues = factory(Article::class)->make();

            $tabSelector = ".content .tabbable > .nav > li";

            $browser->login()
                ->resize(1280, 1280)
                ->visit(route('admin.articles.edit', [
                    'flag' => $article->flag->url,
                    'article' => $article->getKey()
                ]))
                ->with('.page-title', function (Browser $browser) use ($article) {
                    $browser->assertSee($article->flag->name)
                        ->assertSee(trans('admin/article/general.descriptions.edit'));
                })
                ->assertSeeIn("$tabSelector.active", trans('admin/article/form.tabs.general'))
                ->assertMissing("$tabSelector > a[href='#grid']")
                ->assertMissing("input[name='tags']")
                // Check fields for important default values:
                ->assertInputValue('title', $article->title)
                ->assertInputValue('url', $article->url)
                ->assertInputValue('perex', $article->perex)
                ->assertInputValue('text', $article->text)
                ->assertInputValue('seo_title', $article->seo_title)
                ->assertInputValue('seo_description', $article->seo_description)
                ->assertInputValue('open_graph[title]', $article->open_graph->get('title'))
                ->assertInputValue('open_graph[type]', $article->open_graph->get('type'))
                ->assertInputValue('open_graph[url]', $article->open_graph->get('url'))
                ->assertInputValue('open_graph[description]', $article->open_graph->get('description'))
                // Fill values
                ->type('title', $newValues->title)
                ->within(new CKEditor('textarea[name="text"]'), function (Browser $browser) use ($newValues) {
                    $browser->typeIn($newValues->text);
                });
                //// Categories
            $browser->within(new FancyTree('.tree-checkbox'), function (Browser $browser) use ($article, $category) {
                    /** @var \App\Models\Article\Category $currentCategory */
                    $currentCategory = $article->categories->first();

                    $browser->assertNodeSelected($currentCategory->name)
                        ->clickNode($currentCategory->name)
                        ->clickNode($category->name);
                });
                //// Switch to SEO tab
             $browser->click("$tabSelector > a[href='#seo']")
                // Fill values
                ->type('seo_title', $newValues->seo_title)
                ->type('seo_description', $newValues->seo_description)
                //// Switch to OG tags tab
                ->click("$tabSelector > a[href='#open-graph']")
                // Fill values
                ->type('input[name="open_graph[title]"]', $newValues->open_graph->get('title'))
                ->type('open_graph[type]', $newValues->open_graph->get('type'))
                ->type('open_graph[url]', $newValues->open_graph->get('url'))
                ->type('open_graph[description]', $newValues->open_graph->get('description'))
                //// Submit
                ->click('#articles-form button.btn[type="submit"]')
                ->waitForReload()
                ->assertUrlIs(route('admin.articles.index', $article->flag->url))
                ->waitFor((new JGrowl())->selector())
                ->within(new JGrowl, function (Browser $browser) {
                    $browser->assertSays(
                        trans('admin/general.flash_level.success'),
                        trans('admin/article/general.notifications.updated')
                    );
                });

            $article->refresh();
            $this->assertEquals($newValues->title, $article->title);

            $this->assertEquals(1, $article->categories->count());
            $this->assertEquals($category->getKey(), $article->categories->first()->getKey());

            $this->assertEquals($newValues->seo_title, $article->seo_title);
            $this->assertEquals($newValues->seo_description, $article->seo_description);

            // TODO: bugged dusk
//            $this->assertEquals($article->open_graph->get('title'), $newValues->open_graph->get('title'));
//            $this->assertEquals($article->open_graph->get('type'), $newValues->open_graph->get('type'));
//            $this->assertEquals($article->open_graph->get('url'), $newValues->open_graph->get('url'));
//            $this->assertEquals(
//                $article->open_graph->get('description'), $newValues->open_graph->get('description')
//            );

            $this->assertNull($article->getActiveContent());
        });
    }


    /**
     * Test for editing the category with existing url.
     *
     * @throws \Exception
     * @throws \Throwable
     */
    public function testFormEditArticleWithUrlCollisionAndExtensions()
    {
        /** @var \App\Models\Article\Flag $flag */
        $flag = factory(Flag::class)->create([
            'use_tags' => true,
            'use_grid_editor' => true,
        ]);

        $articleA = $this->createArticle(['flag_id' => $flag->getKey()]);
        $articleA->syncTags(['a', 'b', 'c']);
        factory(Content::class)->create(['article_id' => $articleA->getKey()]);

        $articleB = $this->createArticle(['flag_id' => $flag->getKey()]);
        $articleB->syncTags(['c', 'e', 'f']);

        $this->browse(function (Browser $browser) use ($articleA, $articleB, $flag) {
            $tabSelector = ".content .tabbable > .nav > li";

            $browser->login()
                ->resize(1280, 1280)
                ->visit(route('admin.articles.edit', [
                    'flag' => $flag->url,
                    'article' => $articleA->getKey()
                ]))
                ->assertVisible("$tabSelector > a[href='#grid']")
                ->assertMissing('textarea[name="text"]')
                // Check fields for important default values:
                ->assertInputValue('tags', $articleA->tags->implode('name', ','))
                ->assertInputValue('url', $articleA->url)
                // Fill
                ->type('url', $articleB->url)
                ->value('input[name="tags"]', $articleB->tags->implode('name', ','))
                //// Switch to GridEditor tab
                ->click("$tabSelector > a[href='#general']")
//                ->within(new GridEditor, function (Browser $browser) {
//                    $browser->addNewRow('4-4-4');
//                })
                //// Submit
                ->click('#articles-form button.btn[type="submit"]')
                ->waitForReload()
                ->assertUrlIs(route('admin.articles.index', $flag->url))
                ->waitFor((new JGrowl())->selector())
                ->within(new JGrowl, function (Browser $browser) {
                    $browser->assertSays(
                        trans('admin/general.flash_level.success'),
                        trans('admin/article/general.notifications.updated')
                    );
                });

            $articleA->refresh();
            $this->assertNotEquals(0, $articleA->tags->count());
            $this->assertEquals($articleA->tags->pluck('name'), $articleB->tags->pluck('name'));

            // Urls can be same, because they are in different categories.
            $this->assertEquals($articleB->url, $articleA->url);

            $this->assertNull($articleA->text);

            $this->assertNotNull($articleA->getActiveContent());
//            $this->assertNotEmpty($articleA->getActiveContent()->getRaw());
        });


    }


    /**
     * Get selector of table row on specified index.
     *
     * @param int $rowIndex
     * @return string
     */
    private function tableRow(int $rowIndex): string
    {
        return "{$this->tableRowSelector}:nth-child($rowIndex):not(.datatable-empty-row)";
    }


    /**
     * Create new article using factory, with assigned categories.
     *
     * @param array $attributes - attributes of article
     * @return \App\Models\Article\Article
     */
    protected function createArticle(array $attributes = [])
    {
        $categoryAttributes = [];
        if (isset($attributes['flag_id'])) {
            $categoryAttributes['flag_id'] = $attributes['flag_id'];
        }

        if (isset($attributes['language_id'])) {
            $categoryAttributes['language_id'] = $attributes['language_id'];
        }

        /** @var \App\Models\Article\Category $category */
        $category = factory(Category::class)->create($categoryAttributes);

        if (!isset($attributes['flag_id'])) {
            $attributes['flag_id'] = $category->flag_id;
        }

        if (!isset($attributes['language_id'])) {
            $attributes['language_id'] = $category->language_id;
        }

        /** @var \App\Models\Article\Article $article */
        $article = factory(Article::class)->make($attributes);
        $article->setCategoriesToSave([$category->getKey()]);
        $article->save();

        return $article;
    }
}
