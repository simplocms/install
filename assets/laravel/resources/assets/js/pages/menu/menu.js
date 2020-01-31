import LocalizationMixin from '../../vue-mixins/localization';
import Multiselect from '../../vue-components/form/multiselect';

const options = window.menuPageOptions();

Vue.component('drop-zone', require('../../vue-components/draggable/drop-zone'));
Vue.component('menu-item', require('./item'));
Vue.component('menu-page', {
    mixins: [LocalizationMixin],

    components: {Multiselect},

    data () {
        return {
            activeMenuId: null,
            pageTree: null,
            categoryTree: null,
            customPage: {
                name: '',
                url: ''
            },
            newMenu: {
                name: '',
                modal: null,
                index: 0
            },
            menuSelect: null,
            menuLocations: Array.isArray(options.menuLocations) ? {} : options.menuLocations,
            menuList: [],
            categoryFlag: 'articles',
            renderItems: true,
            canEdit: options.canEdit,
        };
    },
    methods: {

        removeItem (index) {
            this.activeMenu.items.splice(index, 1);
        },

        /**
         * Modal for new menu
         */
        newMenuModal () {
            this.newMenu.modal.modal();
        },

        /**
         * Delete active menu
         */
        deleteMenu (url) {
            Request.create(url, {
                data: { id: this.activeMenu.id },
                type: 'DELETE',
                dataType: 'json'
            }).done(() => {
                this.menuList = this.menuList.filter(menu => menu.id !== this.activeMenu.id);
                this.activeMenuId = this.menuList.length ? this.menuList[0].id : null;
            });
        },

        /**
         * Create new menu
         */
        createNewMenu () {
            if (!this.newMenu.name.length) {
                alert('Vyplňte prosím název.');
                return;
            }

            var newMenu = {
                id: 0,
                name: this.newMenu.name,
                items: []
            };

            this.menuList.push(newMenu);

            $.post({
                url: options.urls.newMenu,
                data: {
                    name: newMenu.name
                }
            }).done(response => {
                newMenu.id = response.id;
                this.activeMenuId = response.id;
            });

            this.newMenu.index = this.menuList.length - 1;

            this.newMenu.name = '';
            this.newMenu.modal.modal('hide');
        },

        /**
         * Add selected items in specified tree to menu
         * @param {string} treeName
         */
        addSelectedItems (treeName) {
            if (!this.activeMenu) {
                return;
            }

            let tree, idProperty;
            switch (treeName) {
                case 'pages':
                    tree = this.pageTree;
                    idProperty = 'pageId';
                    break;
                case 'categories':
                    tree = this.categoryTree;
                    idProperty = 'categoryId';
                    break;
                default:
                    return;
            }

            const nodes = tree.getSelectedNodes();

            for (const ni in nodes) {
                const node = nodes[ni];
                const hasParent = !node.getParent().isRootNode();
                let pushTo = this.activeMenu.items;

                node.toggleSelected();

                if (this.searchMenuItems(pushTo, idProperty, node.key) !== null) {
                    continue;
                }

                if (hasParent) {
                    const parentItem = this.searchMenuItems(pushTo, idProperty, node.getParent().key);

                    if (parentItem !== null) {
                        pushTo = parentItem.children;
                    }
                }

                const menuItem = {
                    id: 0,
                    name: node.title,
                    class: '',
                    openNewWindow: false,
                    order: pushTo.length + 1,
                    children: []
                };

                menuItem[idProperty] = node.key;
                pushTo.push(menuItem);
            }
        },

        /**
         *
         * @param {Array<Object>} items
         * @param {String} property
         * @param {(String|Number)} key
         * @return {(Object|null)}
         */
        searchMenuItems (items, property, key) {
            for (const index in items) {
                const item = items[index];

                if (Number(item[property]) === Number(key)) {
                    return item;
                }

                const itemFromChildren = this.searchMenuItems(item.children, property, key);

                if (itemFromChildren !== null) {
                    return itemFromChildren;
                }
            }

            return null;
        },

        /**
         * Add selected pages to menu items
         */
        addSelectedPages () {
            this.addSelectedItems('pages');
        },

        /**
         * Add selected pages to menu items
         */
        addSelectedCategories () {
            this.addSelectedItems('categories');
        },

        /**
         * Add custom page
         */
        addCustomPage () {
            if (!this.customPage.name.length) {
                alert('Vyplňte prosím název vlastní stránky.');
                return;
            }

            var customUrl = this.customPage.url;
            if (customUrl.length && customUrl.charAt(0) === '/') {
                customUrl = customUrl.substring(1, customUrl.length)
            }

            this.activeMenu.items.push({
                id: 0,
                name: this.customPage.name,
                url: customUrl,
                class: '',
                openNewWindow: true,
                pageId: 0,
                order: this.activeMenu.items.length + 1,
                children: []
            });

            this.customPage.url = this.customPage.name = '';
        },

        /**
         * Save all changes
         */
        saveChanges (e) {
            const $button = $(e.currentTarget);

            $button.lock({
                spinner: SpinnerType.OVER
            });

            $.post({
                url: options.urls.saveMenu,
                data: {
                    menu: JSON.stringify(this.menuList),
                    menuLocations: JSON.stringify(this.menuLocations)
                }
            }).done(() => {
                $.jGrowl(this.localization.trans('notifications.updated'), {
                    header: this.localization.trans('flash_level.success'),
                    theme: 'bg-teal'
                });
            }).fail(() => {
                $.jGrowl(this.localization.trans('notifications.update_failed'), {
                    header: this.localization.trans('flash_level.danger'),
                    theme: 'bg-danger'
                });
            }).always(function () {
                $button.unlock();
            });
        },

        loadAndInitCategoryTree () {
            if (!this.canEdit) {
                return;
            }

            this.categoryFlag = this.$refs.categoryFlagSelect.value;

            const source = {
                url: options.urls.categoryTree,
                data: {
                    flag: this.categoryFlag
                }
            };

            // If is category tree already created, than only reloads.
            if (this.categoryTree) {
                return this.categoryTree.reload(source);
            }

            // This part is executed only first time.
            this.categoryTree = $("#category-tree").fancytree({
                checkbox: true,
                selectMode: 2,
                autoScroll: true,
                icon: false,
                source: source
            }).fancytree("getTree");
        },
    },

    computed: {
        activeMenu () {
            return this.menuList.find(menu => menu.id === this.activeMenuId);
        },

        selectOptions() {
            return {
                formatNoMatches: (searched) => {
                    if (!searched) {
                        return this.localization.trans('no_menu_created');
                    }

                    return this.localization.trans('search_no_menu_matched');
                }
            };
        },
    },

    watch: {

        menuList () {
            this.newMenu.index = 0;
        },

        activeMenu () {
            // Uniform checkboxes
            $('.uniform').uniform();
        }
    },

    created() {
        this.menuList = [];

        for (const index in options.menuList) {
            const menu = options.menuList[index];
            this.menuList.push(menu);
        }
    },

    mounted () {
        // Fancy tree of pages
        this.pageTree = $("#page-tree").fancytree({
            checkbox: true,
            selectMode: 2,
            autoScroll: true,
            icon: false,
            source: {
                url: options.urls.pageTree
            }
        }).fancytree("getTree");

        $('.uniform').uniform();

        // Fancy tree of categories
        this.loadAndInitCategoryTree();

        // New menu modal
        this.newMenu.modal = $('#add-menu-modal');

        // Load initial categories for current flag.
        this.loadAndInitCategoryTree();
    }
});
