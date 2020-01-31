## Vlastní build CKeditoru 5 pro CMS

* [Oficiální dokumentace](https://ckeditor.com/docs/ckeditor5/latest/framework/index.html)
* [NPM pluginy pro CKEditor 5](https://www.npmjs.com/search?ranking=optimal&q=ckeditor5)
* [Vytváření vlastních pluginů](https://ckeditor.com/docs/ckeditor5/latest/builds/guides/development/plugins.html)

Z důvodu přizpůsobitelnosti a možnosti aplikovat plugin pro vkládání obsahu z MediaLibrary, je v tomto adresáři připravený build CKeditoru 5, společně s vlastními pluginy.

Build CKEditoru bylo potřeba oddělit od projektu z toho důvodu, že laravel-mix používaný v projektu nejde nastavit tak, aby pro includované SVG soubory používal balíček `raw-loader`, který je potřeba pro správné fungování scriptu.

### Soubory

* `src/index.js` - definice použitých pluginů a jejich úspořádání v CKEditoru.
* `src/media-library-plugin.js` - vlastní plugin pro media library, pomocí kterého je možné vkládat obrázky z media library.
* `src/styles.css` - definice vlastních stylů pro CKEditor.
* `dist/main.js` - zkompilovaný kód určený pro použití v administraci. Tento script se kopíruje do adresáře `public/plugin/js/ckeditor.js` (definováno v `webpack.mix.js` projektu).
* `webpack.config.js` - konfigurace pro build webpackem, převzato z oficiální dokumentace - [scenario 2, method 1](https://ckeditor.com/docs/ckeditor5/latest/builds/guides/integration/advanced-setup.html#scenario-2-building-from-source).

### Build

Pro vývoj lze použít příkaz:

```bash
$ npm run watch
```

Pokud se spustí watch i pro projekt (v adresáři projektu), potom se bude aktualizovaný script automaticky překopírovávat a změny se projeví na webu (po refreshi stránky).

**Před commitem do repozitáře je potřeba vždy provést build pro produkční prostředí**, protože se tento script poté už jenom překopírovává do stránky.

```bash
# Před commitem
$ npm run build
```
