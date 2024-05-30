<?php

use \Illuminate\Database\Eloquent\Model;

return [

    /*
    |--------------------------------------------------------------------------
    | Model maker
    |--------------------------------------------------------------------------
    |
    | Define preferences for creating models
    |
    */

    'models' => [
        /* Default implementations of the models */
        'extends' => [Model::class],

        /* Namespace and directory for models */
        'namespace' => 'App\Models',
        'directory' => app_path('Models'),

        /* Which connections may be parsed */
        'connections' => array_keys(config('database.connections')),

        /* Which tables should be ignored while parsing */
        'ignore' => ['migrations'],

        /* Observer settings (https://laravel.com/docs/6.x/eloquent#observers) */
        'observers' => [
            /* Default directory for observers */
            'namespace' => 'App\Observers',
            'directory' => app_path('Observers'),
            /* Directory and namespace for provider */
            'serviceProvider_namespace' => 'App\Providers',
            'serviceProvider_path' => app_path('Providers') . DIRECTORY_SEPARATOR . 'ObserverProvider.php',

            /* Make array empty to create empty classes */
            'events' => [
                'retrieved',
                'creating',
                'created',
                'updating',
                'updated',
                'saving',
                'saved',
                'deleting',
                'deleted',
                'restored'
            ]
        ],

        /* Policies https://laravel.com/docs/6.x/authorization#creating-policies*/
        'policies' => [
            /* Default directory for Policies */
            'namespace' => 'App\Policies',
            'directory' => app_path('Policies'),
            /* Directory and namespace for provider */
            'serviceProvider_namespace' => 'App\Providers',
            'serviceProvider_path' => app_path('Providers') . DIRECTORY_SEPARATOR . 'PolicyProvider.php',

            /*
             * The key is the method name in the policy
             *  - if the value is set to true, the model will be passed as the second argument
             *  - the generator will remove "restore" and "forceDelete" if no SoftDeletes were detected on the model
             *  - The User model is defined in auth.providers.users.model found in config\
             */
            'events' => [
                'viewAny' => null,
                'view' => true,
                'create' => null,
                'update' => true,
                'delete' => true,
                'restore' => true,
                'forceDelete' => true
            ]
        ],

        'misc' => [
            'doctrineMappings' => [
                'enum' => 'string',
                'timestamp' => 'datetime'
            ]
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Repository maker
    |--------------------------------------------------------------------------
    |
    | Define preferences for creating models
    |
    */

    'repositories' => [
        /* Default directory for repositories */
        'directory' => app_path('Repositories'),
        /* Directory and namespace for provider */
        'serviceProvider_namespace' => 'App\Providers',
        'serviceProvider_path' => app_path('Providers') . DIRECTORY_SEPARATOR . 'RepositoryProvider.php'
    ],

    /*
    |--------------------------------------------------------------------------
    | Controller maker
    |--------------------------------------------------------------------------
    |
    | Define preferences for creating controllers
    |
    */

    'controllers' => [
        /* Default directory for controllers */
        'namespace' => 'App\Http\Controllers',
        'directory' => app_path('Http') . DIRECTORY_SEPARATOR . 'Controllers',
        'repositoryProviderNamespace' => 'App\Providers\RepositoryProvider',

        'requests' => [
            'namespace' => 'App\Http\Requests',
            'directory' => app_path('Http') . DIRECTORY_SEPARATOR . 'Requests',
        ]
    ]
];
