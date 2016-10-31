<?php
require_once __DIR__.'/app/bootstrap.php';
require_once __DIR__.'/src/functions.php';

// register some acf fields
if(function_exists('acf_add_local_field_group')){
    $parser = new \Symfony\Component\Yaml\Parser();
    // header
    $fields = $parser->parse(file_get_contents(__DIR__.'/app/config/header.yml'));
    acf_add_local_field_group($fields);
    // content builder
    $fields = $parser->parse(file_get_contents(__DIR__.'/app/config/content_builder.yml'));
    acf_add_local_field_group($fields);
    // product builder
    $fields = $parser->parse(file_get_contents(__DIR__.'/app/config/product_builder.yml'));
    acf_add_local_field_group($fields);
}

// global twig vars
add_filter('timber/context', function($data){
    $data['favicon'] = get_field('favicon', 'option') ?: get_field('mobile_logo', 'option');
    $data['shop_url'] = get_permalink(wc_get_page_id('shop'));
    // menus
    $data['large_menu'] = wp_nav_menu([
        "location" => is_user_logged_in() ? "loggedin_main_menu" : "loggedout_main_menu",
        "container" => false,
        "menu_class" => "dropdown menu",
        "echo" => false,
        "walker" => new \Supertheme\WordPress\DropDownMenuWalker(),
        'items_wrap' => '<ul id="%1$s" class="%2$s" data-dropdown-menu>%3$s</ul>'
    ]);
    $data['small_menu'] = wp_nav_menu([
        "location" => is_user_logged_in() ? "loggedin_side_menu" : "loggedout_side_menu",
        "container" => false,
        "menu_class" => "vertical menu",
        "echo" => false,
    ]);
    // widgets
    $data['footer_1'] = \Timber::get_widgets('footer_1');
    $data['footer_2'] = \Timber::get_widgets('footer_2');
    $data['footer_3'] = \Timber::get_widgets('footer_3');
    $data['footer_4'] = \Timber::get_widgets('footer_4');
    // follow
    $data['youtube'] = get_field('youtube', 'option');
    $data['facebook'] = get_field('facebook', 'option');
    $data['instagram'] = get_field('instagram', 'option');
    $data['pinterest'] = get_field('pinterest', 'option');
    // copyright
    $data['copyright'] = get_field('copyright', 'option');
    // hero
    $data['hero_background'] = get_field('header_background', 'option');
    // woocommerce
    $data['shop_url'] = get_permalink(wc_get_page_id('shop'));
    return $data;
});

// add menu and cart icons
add_filter('wp_nav_menu_items', function ($items, $args) {
    if(!is_object($args) || !property_exists($args, 'location')){
        return $items;
    }
    if ($args->location == 'loggedin_main_menu' || $args->location == 'loggedout_main_menu') {
        $items = '<li class="menu-toggle"><a data-toggle="offCanvas"><i class="fa fa-bars"></i></a></li>'.$items;
        $items .= '<li class="cart">'
            .'<a href="'.wc_get_cart_url().'" title="'._( 'View your shopping cart' ).'" data-toggle="offCanvas">'
            .'<img src="'.get_stylesheet_directory_uri().'/web/images-min/cart.min.png" alt="cart">'
            .(WC()->cart->get_cart_contents_count()?'<span class="badge">'.WC()->cart->get_cart_contents_count().'</span>':'')
            .'</a>'
            .'</li>';
    }

    return $items;
}, 10, 2 );

// change widget titles to h6
add_filter('widget_title', function($title) {
    return '<h5>'.$title.'</h5>';
});

// add stylesheet to admin pages
add_action('admin_enqueue_scripts', function () {
    wp_enqueue_style('admin-styles', get_template_directory_uri().'/web/stylesheets/admin.css');
});
