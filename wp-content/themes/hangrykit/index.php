<?php
require_once __DIR__.'/app/bootstrap.php';
use Timber\Timber;

/** @var $timber Timber */
$timber = $container->get('timber.environment');
$context = $timber::get_context();
$context['post'] = Timber::query_post();
$context['favicon'] = get_field('favicon', 'options')?:get_field('mobile_logo', 'options');
$context['youtube'] = get_field('youtube', 'options');
$context['facebook'] = get_field('facebook', 'options');
$context['instagram'] = get_field('instagram', 'options');
$context['pinterest'] = get_field('pinterest', 'options');
$context['twitter'] = get_field('twitter', 'options');
$context['copyright'] = get_field('copyright', 'options');

if(is_singular() && is_front_page()) {
    $template = 'single.html.twig';
} elseif(is_home()) {
    $template = 'posts.html.twig';
} else {
    $template = 'basic.html.twig';
}

$context['panels'] = '';
if(have_rows('content_builder')){
    ob_start();
    while(have_rows('content_builder')) {
        the_row();
        switch(get_row_layout()) {
            case 'hero':
                $timber::render('panels/hero.html.twig', $context);
                break;
            default:
                break;
        }
    }
    $context['panels'] = ob_get_clean();
}


$timber->render($template, $context);
