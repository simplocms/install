<?php

namespace App\Http\Controllers\Admin;

use App\Models\Article\Category;
use App\Models\Menu\Menu;
use App\Models\Page\Page;
use App\Models\Web\Theme;
use Illuminate\Http\Request;
use App\Models\Article\Flag;

class MenuController extends AdminController
{
    /**
     * MenuController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->middleware('permission:menu-show')->only('index');
        $this->middleware('permission:menu-create')->only(['create', 'store']);
        $this->middleware('permission:menu-edit')->only(['edit', 'update']);
        $this->middleware('permission:menu-delete')->only('delete');
    }


    /**
     * Request: show menus
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $this->setTitleDescription(trans('admin/menu.header_title'), trans('admin/menu.header_description'));
        $menuList = Menu::toJsonStructure($this->getLanguage());
        $defaultTheme = Theme::getDefault();
        $flags = Flag::whereLanguage($this->getLanguage())->pluck('name', 'id');
        $menuLocations = $defaultTheme->config('menu_locations');
        $maxDepth = config('admin.max_menu_depth');

        return view('admin.menu.index',
            compact('menuList', 'defaultTheme', 'flags', 'menuLocations', 'maxDepth')
        );
    }


    /**
     * Request: Get page tree
     *
     * @return mixed
     */
    public function pagesTree()
    {
        return Page::getTree(Page::whereLanguage($this->getLanguage()));
    }


    /**
     * Request: Get categories tree
     *
     * @return mixed
     */
    public function categoriesTree()
    {
        $flag = request()->get('flag');
        if (!$flag) {
            return [];
        }

        return Category::getTree(
            Category::whereLanguage($this->getLanguage())->whereFlag($flag)
        );
    }


    /**
     * Request: create new menu
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
        ]);

        $menu = new Menu($request->only(['name']));
        $menu->language_id = $this->getLanguage()->getKey();
        $menu->save();

        return response()->json(['id' => $menu->getKey()]);
    }


    /**
     * Request: Save menus
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {
        $menuListInput = $request->input('menu');
        if (!$menuListInput) {
            return abort(400);
        }

        $menuList = json_decode($menuListInput);

        foreach ($menuList as $menuObject) {
            /** @var \App\Models\Menu\Menu $menu */
            $menu = Menu::findOrFail($menuObject->id);
            $preserveIds = $this->updateMenu($menu, $menuObject->items);

            $menu->items()->whereLanguage($this->getLanguage())
                ->whereNotIn('id', $preserveIds)->delete();
        }

        // Set theme menu locations
        if ($menuLocationsInput = $request->input('menuLocations')) {
            $menuLocations = json_decode($menuLocationsInput, true);
            $defaultTheme = Theme::getDefault();

            foreach ($menuLocations as $location => $menuId) {
                $defaultTheme->setMenuLocation($location, $menuId);
            }
        }

        return response()->noContent();
    }


    /**
     * Updates menu items and options
     *
     * @param \App\Models\Menu\Menu $menu
     * @param $items
     * @param null $parentId
     * @return array
     */
    private function updateMenu(Menu $menu, $items, $parentId = null)
    {
        $order = 1;
        $itemIdsPreserve = [];

        foreach ($items as $itemObject) {

            $itemData = [
                'name' => $itemObject->name,
                'language_id' => $this->getLanguage()->id,
                'url' => isset($itemObject->url) ? $itemObject->url : null,
                'order' => $order,
                'parent_id' => $parentId,
                'class' => isset($itemObject->class) ? $itemObject->class : null,
                'open_new_window' => isset($itemObject->openNewWindow) ? !!$itemObject->openNewWindow : false,
                'page_id' => $itemObject->pageId ?? null ? $itemObject->pageId : null,
                'category_id' => $itemObject->categoryId ?? null
            ];

            // Editing existing item
            if ($itemObject->id) {
                /** @var \App\Models\Menu\Item $item */
                $item = $menu->items->find($itemObject->id);

                if (!$item) {
                    continue;
                }

                $item->update($itemData);
            } else {
                // Creating new item
                $item = $menu->items()->create($itemData);
            }

            $itemIdsPreserve[] = $item->getKey();
            if ($itemObject->children) {
                $preserveFromChildren = $this->updateMenu($menu, $itemObject->children, $item->getKey());
                $itemIdsPreserve = array_merge($itemIdsPreserve, $preserveFromChildren);
            }

            $order++;
        }

        return $itemIdsPreserve;
    }


    /**
     * Delete menu.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Request $request)
    {
        $menuId = $request->input('id');
        if (!$menuId) {
            abort(404);
        }

        Menu::findOrFail($menuId)->delete();
        return response()->noContent();
    }
}
