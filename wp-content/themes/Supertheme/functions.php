<?php
require_once __DIR__.'/app/bootstrap.php';
require_once __DIR__.'/src/functions.php';

add_filter('timber/context', function($data){
    $data['favicon'] = get_field('favicon', 'option') ?: get_field('mobile_logo', 'option');
    $data['shop_url'] = get_permalink(wc_get_page_id('shop'));
    return $data;
});