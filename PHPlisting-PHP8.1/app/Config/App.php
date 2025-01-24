<?php

return [
    'license' => '',
    
    'debug' => false,
    'charset' => 'utf-8',
    'url' => '',
    'admin_directory' => 'admin',

    'locale_fallback' => 'en',
    'locale_url_default_exclude' => true,
    'locale_browser' => false,
    'locale_storage' => 'files',
    'locale_path' => ROOT_PATH_PROTECTED . DS . 'I18n',

    'storage_path' => ROOT_PATH_PROTECTED . DS . 'Storage',
    'storage_size_limit_per_ip' => 1024*1024*250, // 250Mb
    'storage_file_limit_per_ip' => 1000,
];
