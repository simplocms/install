<?php

return [
    'btn_authorize_ga' => 'Authorize Google Analytics',

    'titles' => [
        'index' => 'Dashboard',
        'authorization' => 'Authorization',
        'profile_list' => 'Choose profile',
    ],

    'descriptions' => [
        'index' => 'overview',
        'authorization' => 'access to your GA profile',
        'profile_list' => 'select GA profile for dashboard',
    ],

    'notifications' => [
        'ga_profile_not_selected' => 'Unable to select profile',
        'no_ga_permission' => 'You do not have permission to manage the bulletin board',
        'invalid_ga_token' => 'We were unable to verify the code. Please try again',
        'ga_disconnected' => 'Google Analytics has been disconnected',
    ],

    'graphs' => [
        'title' => 'Website traffic',
        'visits_count' => 'Visits',
        'users_count' => 'Users',
        'pageviews' => 'Page views',
        'views_per_session' => 'Pages per visit',
        'bouncer_rate' => 'Bouncer rate',
        'organic_searches_count' => 'Organic search',
        'new_visitors' => 'New visitors',
        'returning_visitors' => 'Returning visitors',

        'buttons' => [
            'settings' => 'Settings',
            'disconnect_ga' => 'Disconnect Google Analytics',
            'switch_ga_profile' => 'Switch GA profile',
        ],
    ],

    'authorization' => [
        'title' => 'Google Analytics - Authorization',
        'text_for_link' => 'First you need this link: ',
        'token_link_text' => 'get the code',
        'label_token' => 'Code: ',
        'btn_authorize' => 'Authorize'
    ],

    'choose_profile' => [
        'title' => 'Choose profile',
        'help_text' => 'Select the profile whose data you want to see on the dashboard',
        'include_measuring_code' => 'Add a tracking code to your site',
    ],
];
