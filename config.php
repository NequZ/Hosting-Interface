<?php

return [
    'requiredphpversion' => '7.4', // Required PHP Version its just for the Systemcheck and what PHP Version you want to set as required.
     'debug' => false, // Change this to false to disable debug mode -> Debug Mode is for development only and contains sensitive information about your application and database -> Change to false before deploying to production -> Debug looks a bit weird in the browser and is not user friendly -> Debug is for developers only
    'db_host' => 'localhost',
    'db_name' => 'nw_maindb',
    'db_user' => 'nw_user',
    'db_pass' => 'Michelle0105*',

    // The following settings are for the Discord Module
    'discord' => [
        'enabled' => true, // Change this to false to disable the Discord Module
        ]
];

?>
