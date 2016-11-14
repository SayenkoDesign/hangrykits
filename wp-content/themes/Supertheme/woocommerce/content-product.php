<?php
global $product, $container;

// Ensure visibility
if ( empty( $product ) || ! $product->is_visible() ) {
	return;
}
use Timber\Timber;

/** @var $timber Timber */
$timber = $container->get('timber');
$context['post'] = $timber::get_post($product->id);
$context['product'] = $product;
$timber::render('teasers/product.html.twig', $context);
?>
