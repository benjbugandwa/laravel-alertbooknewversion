<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Class Namespace
    |--------------------------------------------------------------------------
    |
    | This value sets the default class namespace for Livewire components.
    |
    */

    'class_namespace' => 'App\\Livewire',

    /*
    |--------------------------------------------------------------------------
    | View Path
    |--------------------------------------------------------------------------
    |
    | This value sets the default directory where Livewire views are stored.
    |
    */

    'view_path' => resource_path('views/livewire'),

    /*
    |--------------------------------------------------------------------------
    | Layout
    |--------------------------------------------------------------------------
    |
    | The default layout view used by Livewire page components.
    |
    */

    'layout' => 'components.layouts.app',

    /*
    |--------------------------------------------------------------------------
    | Lazy Placeholder
    |--------------------------------------------------------------------------
    |
    | The default placeholder view used for lazy-loaded components.
    |
    */

    'lazy_placeholder' => null,

    /*
    |--------------------------------------------------------------------------
    | Temporary File Uploads
    |--------------------------------------------------------------------------
    |
    | Livewire uploads files in two steps:
    | 1. Temporary upload to a temp directory
    | 2. Final storage in your component save() method
    |
    | Since your project uploads:
    | - images for incidents
    | - pdf / word docs for notes and referrals
    |
    | We allow the common types globally here.
    | Then each component must still apply stricter validation rules.
    |
    */

    'temporary_file_upload' => [
        'disk' => 'local',
        'directory' => 'livewire-tmp',

        // Global temporary rules:
        // - images
        // - pdf
        // - word
        // - max 10 MB
        'rules' => 'file|mimes:png,jpg,jpeg,pdf,doc,docx|max:10240',

        'middleware' => 'throttle:60,1',

        // Preview only for image types
        'preview_mimes' => [
            'png',
            'jpg',
            'jpeg',
        ],

        // Max upload time in minutes
        'max_upload_time' => 5,
    ],

    /*
    |--------------------------------------------------------------------------
    | Render On Redirect
    |--------------------------------------------------------------------------
    |
    | Enable or disable rendering before redirecting.
    |
    */

    'render_on_redirect' => false,
];
