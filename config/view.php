<?php

return [

    /*
    |--------------------------------------------------------------------------
    | View Storage Paths
    |--------------------------------------------------------------------------
    */

    'paths' => [
        resource_path('views'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Compiled View Path
    |--------------------------------------------------------------------------
    |
    | Store compiled Blade templates in the OS temporary directory by default
    | so storage/framework/views remains clean in the repository tree.
    |
    */

    'compiled' => env(
        'VIEW_COMPILED_PATH',
        sys_get_temp_dir()
    ),

];
