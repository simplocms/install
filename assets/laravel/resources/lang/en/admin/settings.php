<?php

return [

    'header_title' => 'Settings',
    'theme_box_title' => 'Theme',
    'general_box_title' => 'Website',
    'twitter_title' => 'Twitter',
    'open_graph_title' => 'OpenGraph',

    'tabs' => [
        'general' => 'General settings',
        'seo' => 'SEO',
        'og' => 'OpenGraph & Twitter',
        'security_headers' => 'Security headers',
        'search' => 'Search',
    ],

    'notifications' => [
        'theme_unavailable' => 'This theme is not available.',
        'theme_changed' => 'Theme successfully changed.',
        'settings_updated' => 'Settings successfully updated.'
    ],

    'labels' => [
        'site_name' => 'Website name',
        'company_name' => 'Company name',
        'logo' => 'Logo',
        'favicon' => 'Icon',
        'theme_color' => 'Icon background color',
        'active_theme' => 'Active theme',
        'seo_title' => 'Default title',
        'seo_description' => 'Default SEO description',
        'twitter_account' => 'Twitter account name',
    ],

    'btn_change_theme' => 'Change theme',
    'btn_save' => 'Save changes',

    'change_theme_modal' => [
        'title' => 'Change theme',
        'table_columns' => [
            'name' => 'Theme name',
            'activation' => 'Activation',
        ],
        'status_active' => 'Active',
        'btn_activate' => 'Activate'
    ],

    'validation_messages' => [
        'site_name.max' => 'Maximal length of page name is :max characters.',
        'company_name.max' => 'Maximal length of company name is :max characters.',
        'og_title.max' => 'Maximal length of OpenGraph title should be :max characters.',
        'of_description.max' => 'Maximal length of OpenGraph description should be :max characters.',
        'invalid_x_frame_options' => 'If "allow", "deny" or "sameorigin" is not used, a valid URL must be given.',
        'x_xss_protection.in' => 'Invalid value for header "X-Xss-Protection".',
        'referrer_policy.in' => 'Invalid value for header "Referrer-Policy".',
        'hsts_max_age.int' => 'Please enter an integer.',
        'hsts_max_age.min' => 'Minimum value is :min.',
        'search_uri.required_if' => 'Please enter URL slug for search page.',
        'search_uri.max' => 'Maximal length of the URL slug for search page is :max characters.',
        'theme_color.regex' => 'Invalid color. Please insert color in hexadecimal format.',
    ],

    'general' => [
        'title_help' => 'You can use variables <code>%title%</code> and <code>%site_name%</code>. On the page, the <code>%title%</code> variable will be replaced by the title of the currently viewed page, and the <code>%site_name%</code> variable will be replaced by the website name, that is common to the entire site.',
        'logo_help' => 'We recommend to use vector image of type <code>SVG</code>, or bitmap image of type <code>PNG</code>, <code>JPEG</code> or <code>GIF</code> with resolution <code>600x60 px</code>.',
        'favicon_help' => 'We recommend to use vector image of type <code>SVG</code>, or bitmap image of type <code>PNG</code> or <code>JPEG</code> with resolution at least <code>512x512 px</code>.',
    ],

    'security_headers' => [
        'do_not_use' => '-- do not use --',

        'enable' => 'Enable',
        'hsts_include_subdomains' => 'Include sub-domains',

        'btn_test' => 'Test your website on securityheaders.com',

        'x_frame_options_info' => 'Tells the browser whether you want to allow your site to be framed or not. By preventing a browser from framing your site you can defend against attacks like clickjacking. Available values are <kbd>allow</kbd>, <kbd>deny</kbd>, <kbd>sameorigin</kbd> or specific URL. Recommended is <kbd>sameorigin</kbd>.',

        'x_xss_protection_info' => 'Sets the configuration for the cross-site scripting filter built into most browsers. Recommended value is <kbd>1; mode=block</kbd>.',

        'referrer_policy_info' => 'Allows a site to control which referrer information the browser includes with navigations away from a document.',

        'x_content_type_options_info' => 'Stops a browser from trying to MIME-sniff the content type and forces it to stick with the declared content-type. We recommend to enable this header.',

        'hsts_info' => 'Strengthens your implementation of TLS by getting the User Agent to enforce the use of HTTPS. Recommended value of Max-age is <code>31536000</code>.'
    ],

    'search' => [
        'search_enabled' => 'Enable search on website',
        'search_uri' => 'URL slug of the search page',
        'search_in_pages' => 'Search in pages',
        'search_in_articles' => 'Search in articles',
        'search_in_categories' => 'Search in categories',
        'search_in_photogalleries' => 'Search in photogalleries',

        'search_uri_info' => 'Enter URL slug for active language ":language". Final url of the search page will be <code>:url</code>.'
    ],
    
];
