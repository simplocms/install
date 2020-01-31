<?php

return [

    'header_title' => 'Nastavení',
    'theme_box_title' => 'Šablona',
    'general_box_title' => 'Webová stránka',
    'twitter_title' => 'Twitter',
    'open_graph_title' => 'OpenGraph',

    'tabs' => [
        'general' => 'Obecná nastavení',
        'seo' => 'SEO',
        'og' => 'OpenGraph & Twitter',
        'security_headers' => 'Bezpečnostní hlavičky',
        'search' => 'Vyhledávání',
    ],

    'notifications' => [
        'theme_unavailable' => 'Tato šablona není k dispozici.',
        'theme_changed' => 'Šablona byla úspěšně změněna.',
        'settings_updated' => 'Nastavení úspěšně uloženo.'
    ],

    'labels' => [
        'site_name' => 'Název webové stránky',
        'company_name' => 'Provozovatel stránky',
        'logo' => 'Logo na webu',
        'favicon' => 'Ikona webu',
        'theme_color' => 'Barva pozadí ikony',
        'active_theme' => 'Aktivní šablona',
        'seo_title' => 'Výchozí titulek',
        'seo_description' => 'Výchozí SEO description',
        'twitter_account' => 'Název twitter účtu',
    ],

    'btn_change_theme' => 'Změnit šablonu',
    'btn_save' => 'Uložit změny',

    'change_theme_modal' => [
        'title' => 'Změna šablony',
        'table_columns' => [
            'name' => 'Název šablony',
            'activation' => 'Aktivace',
        ],
        'status_active' => 'Aktivní',
        'btn_activate' => 'Aktivovat'
    ],

    'validation_messages' => [
        'site_name.max' => 'Maximální délka názvu stránky je :max znaků.',
        'company_name.max' => 'Maximální délka názvu provozovatele je :max znaků.',
        'og_title.max' => 'Maximální délka OpenGraph titulku by měla být :max znaků.',
        'of_description.max' => 'Maximální délka OpenGraph popisku by měla být :max znaků.',
        'invalid_x_frame_options' => 'Není-li použita hodnota "allow", "deny" nebo "sameorigin", musí být zadána platná URL adresa.',
        'x_xss_protection.in' => 'Neplatná hodnota pro hlavičku "X-Xss-Protection".',
        'referrer_policy.in' => 'Neplatná hodnota pro hlavičku "Referrer-Policy".',
        'hsts_max_age.int' => 'Zadejte prosím celé číslo.',
        'hsts_max_age.min' => 'Minimální hodnota je :min.',
        'search_uri.required_if' => 'Zadejte prosím adresu vyhledávání.',
        'search_uri.max' => 'Maximální délka adresy vyhledávání je :max znaků.',
        'theme_color.regex' => 'Neplatná barva. Zadejte prosím barvu v hexadecimální podobě.',
    ],

    'general' => [
        'title_help' => 'Zde můžete použít proměnné <code>%title%</code> a <code>%site_name%</code>. Na stránce pak bude proměnná <code>%title%</code> nahrazena titulkem aktuálně prohlížené stránky a proměnná <code>%site_name%</code> názvem webové stránky, který je společný pro celý web.',
        'logo_help' => 'Doporučujeme použít vektorový obrázek ve formátu <code>SVG</code>, nebo bitmapový obrázek ve formátu <code>PNG</code>, <code>JPEG</code> nebo <code>GIF</code> o rozměrech <code>600x60 px</code>.',
        'favicon_help' => 'Doporučujeme použít vektorový obrázek ve formátu <code>SVG</code>, nebo bitmapový obrázek ve formátu <code>PNG</code> nebo <code>JPEG</code> o rozměrech alespoň <code>512x512 px</code>.',
    ],

    'security_headers' => [
        'do_not_use' => '-- nepoužívat --',

        'enable' => 'Povolit',
        'hsts_include_subdomains' => 'Včetně subdomén',

        'btn_test' => 'Otestovat bezpečnost stránky na securityheaders.com',

        'x_frame_options_info' => 'Informuje prohlížeč, zda chcete povolit, aby vaše stránky mohly být použity v iframe. Tím, že bráníte prohlížení vašeho webu skrze jinou stránku, se můžete ochránit před útoky jako je například  clickjacking. Možné hodnoty jsou <kbd>allow</kbd>, <kbd>deny</kbd>, <kbd>sameorigin</kbd> nebo konrétní URL. Doporučujeme použít hodnotu <kbd>sameorigin</kbd>.',

        'x_xss_protection_info' => 'Nastaví konfiguraci filtru XSS zabudovaného do většiny prohlížečů. Doporučujeme použít hodnotu <kbd>1; mode=block</kbd>.',

        'referrer_policy_info' => 'Umožňuje stránce určit, kdy bude prohlížeč vkládat informace do hlavičky <code>Referrer</code>.',

        'x_content_type_options_info' => 'Zabrání prohlížeči před pokusy o MIME-sniffing a donutí ho držet se deklarovaného typu obsahu. Doporučujeme mít tuto hlavičku povolenou.',

        'hsts_info' => 'Posílí implementaci TLS na stránce tím, že vynutí použítí protokolu HTTPS. Doporučená hodnota Max-age je <code>31536000</code>.'
    ],

    'search' => [
        'search_enabled' => 'Povolit vyhledávání na stránce',
        'search_uri' => 'URL stránky s výsledky vyhledávání',
        'search_in_pages' => 'Prohledávat stránky',
        'search_in_articles' => 'Prohledávat články',
        'search_in_categories' => 'Prohledávat kategorie',
        'search_in_photogalleries' => 'Prohledávat fotogalerie',

        'search_uri_info' => 'Zadejte adresu bez domény (tzv. url slug) pro aktivní jazyk ":language". Výsledná adresa vyhledávání bude <code>:url</code>.'
    ],
    
];
