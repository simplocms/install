<div v-show="!noCategories">
    <div class="tree-checkbox"></div>
</div>

<em v-if="noCategories">
    {{ trans('admin/article/form.category_tab.no_categories') }}
</em>
