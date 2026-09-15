<?php

declare(strict_types=1);

$moduleControllerDirectories = array_reduce(
    glob(base_path('modules/*/src/Transport/Http/Controller')) ?: [],
    static function (array $directories, string $directory): array {
        $moduleName = basename(dirname(dirname(dirname(dirname($directory)))));

        $directories[$directory] = [
            'namespace' => 'Module\\' . $moduleName . '\\Transport\\Http\\Controller',
            'base_path' => $directory,
            'prefix' => 'api',
            'middleware' => 'api',
        ];

        return $directories;
    },
    []
);

return [
    /*
     *  Automatic registration of routes will only happen if this setting is `true`
     */
    'enabled' => true,

    /*
     * Controllers in these directories that have routing attributes
     * will automatically be registered.
     *
     * Optionally, you can specify group configuration by using key/values
     */
    'directories' => array_merge(
        [
            app_path('Transport/Http/Controller') => [
                'prefix' => 'api',
                'middleware' => 'api',
            ],
        ],
        $moduleControllerDirectories,
    ),

    /*
     * This middleware will be applied to all routes.
     */
    'middleware' => [
        \Illuminate\Routing\Middleware\SubstituteBindings::class,
    ],

    /*
     * When enabled, implicitly scoped bindings will be enabled by default.
     * You can override this behaviour by using the `ScopeBindings` attribute, and passing `false` to it.
     *
     * Possible values:
     *  - null: use the default behaviour
     *  - true: enable implicitly scoped bindings for all routes
     *  - false: disable implicitly scoped bindings for all routes
     */
    'scope-bindings' => null,
];
