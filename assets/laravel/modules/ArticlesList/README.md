## CMS MODULE - List článků

> Modul je určený pro použití v GridEditoru.

### Použití

- View pro tento modul vkládejte do adresáře šablony `view/modules/articles_list`.
- Při renderování dostane view proměnné:
  - `$articles` typu `\Illuminate\Support\Collection` 
  - `$configuration` typu `Modules\ArticlesList\Models\Configuration` 

### Build skriptu

Pokud je potřeba upravit skript modulu, je potřeba nainstalovat npm knihovny příkazem `npm i`.

Modul používá knihovnu `laravel-mix`, takže je možné používat pro něj typické příkazy pro build.

**Před commitem do repozitáře je potřeba provést build pro produkci příkazem `npm run prod`!**
