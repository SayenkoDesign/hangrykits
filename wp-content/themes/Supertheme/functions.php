<?php
require_once __DIR__.'/app/bootstrap.php';
require_once __DIR__.'/src/functions.php';
use Timber\Timber;
use Timber\Post;

/** @var $timber Timber */
$timber = $container->get('timber');

// add woocommerce support
add_action('after_setup_theme', function (){
    add_theme_support('woocommerce');
});

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
    // product header
    $fields = $parser->parse(file_get_contents(__DIR__.'/app/config/product_header.yml'));
    acf_add_local_field_group($fields);
}

// global twig vars
add_filter('timber/context', function($data){
    $data['favicon'] = get_field('favicon', 'option') ?: get_field('mobile_logo', 'option');
    $data['shop_url'] = get_permalink(wc_get_page_id('shop'));
    // menus
    $data['large_menu'] = wp_nav_menu([
        "theme_location" => is_user_logged_in() ? "loggedin_main_menu" : "loggedout_main_menu",
        "container" => false,
        "menu_class" => "dropdown menu",
        "echo" => false,
        "walker" => new \Supertheme\WordPress\DropDownMenuWalker(),
        'items_wrap' => '<ul id="%1$s" class="%2$s" data-dropdown-menu>%3$s</ul>'
    ]);
    $data['small_menu'] = wp_nav_menu([
        "theme_location" => is_user_logged_in() ? "loggedin_side_menu" : "loggedout_side_menu",
        "container" => false,
        "menu_class" => "vertical menu",
        "echo" => false,
    ]);
    // highlights section
    $data['highlights_header'] = get_field('highlights_heading', 'option');
    $data['highlights_box'] = get_field('highlights_box', 'option');
    $data['highlights'] = get_field('highlights', 'option');
    //var_dump(get_field('highlights', 'option'));
    // widgets
    $data['sidebar'] = \Timber::get_widgets('sidebar');
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
    $data['shop_background'] = get_field('Shop_background', 'option');
    return $data;
});

// add timber extensions
add_filter('get_twig', function ($twig) {
    /* this is where you can add your own fuctions to twig */
    $twig->addExtension(new \Snilius\Twig\SortByFieldExtension());
    return $twig;
});

// add menu and cart icons
add_filter('wp_nav_menu_items', function ($items, $args) {
    if(!is_object($args) || !property_exists($args, 'theme_location')){
        return $items;
    }
    if ($args->theme_location == 'loggedin_main_menu' || $args->theme_location == 'loggedout_main_menu') {
        $items = '<li class="menu-toggle"><a data-toggle="offCanvas"><i class="fa fa-bars"></i></a></li>'.$items;
        $items .= '<li class="cart">'
            .'<a href="'.wc_get_cart_url().'" title="'._( 'View your shopping cart' ).'">'
            .'<img src="'.get_stylesheet_directory_uri().'/web/images-min/cart.min.png" alt="cart">'
            .(WC()->cart->get_cart_contents_count()?'<span class="badge">'.WC()->cart->get_cart_contents_count().'</span>':'')
            .'</a>'
            .'</li>';
    }

    return $items;
}, 10, 2 );

// change widget titles to h6
add_filter('widget_title', function($title) {
    return '<h6>'.$title.'</h6>';
});

// add stylesheet to admin pages
add_action('admin_enqueue_scripts', function () {
    wp_enqueue_style('admin-styles', get_template_directory_uri().'/web/stylesheets/admin.css');
});

add_filter('woocommerce_add_to_cart_redirect', function ( $url ) {
    $url = WC()->cart->get_cart_url();
    // $url = wc_get_checkout_url(); // since WC 2.5.0
    return $url;
});

// move yoast down
add_filter( 'wpseo_metabox_prio', function(){
    return 'low';
});

// add comments query vars
add_action('init',function () use($wp) {
    $wp->add_query_var('comments_page', 1);
    $wp->add_query_var('comments_sort', get_option('comment_order'));
});

// shortcode for lightbox
add_shortcode('fancybox', function($atts, $content){
    $settings = shortcode_atts([
        'id' => 'fancybox_'.time(),
        'title' => 'shortcode missing title="title text"',
        'content' => 'Missing content between opening and closing shortcodes',
    ], $atts);
    return <<<HTML
    <a href="#{$settings['id']}" class="fancybox">{$settings['title']}</a>
    <div style="display: none;" id="{$settings['id']}">$content</div>
HTML;
});

// shortcode for lightbox
add_shortcode('reviews', function() use($timber) {
    $context['posts'] = $timber::get_posts(['post_type' => 'product']);
    $timber::render('shortcodes/reviews.html.twig', $context);
});

remove_action( 'woocommerce_cart_collaterals', 'woocommerce_cross_sell_display' );