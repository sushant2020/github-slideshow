<?php

declare(strict_types = 1);

return [
    'schemas' => [
        'default' => [
            'query' => [
                "getSupplierdata" => \App\GraphQL\Queries\GetSupplier::class,
            ],
            'mutation' => [
                // ExampleMutation::class,
            ],
            // The types only available in this schema
            'types' => [
                "GetSupplier" =>  \App\GraphQL\Types\GetSupplier::class,
            ],

            // Laravel HTTP middleware
            //'middleware' => 'auth:api',
			'middleware' => null,

            // Which HTTP methods to support; must be given in UPPERCASE!
            'method' => ['GET', 'POST'],

            // An array of middlewares, overrides the global ones
            'execution_middleware' => null,
        ],
    ],
];
