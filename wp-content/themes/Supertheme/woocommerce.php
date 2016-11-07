<?php
require_once __DIR__.'/app/bootstrap.php';
use Timber\Timber;

/** @var $timber Timber */
$timber = $container->get('timber');
$context            = $timber::get_context();
$context['sidebar'] = $timber::get_widgets('shop-sidebar');

if (is_singular('product')) {
    $context['post']    = $timber::get_post();
    $product            = wc_get_product( $context['post']->ID );
    $context['product'] = $product;
    $context["acf"] = get_field_objects($context["post"]->ID);
    $context['related'] = [];
    foreach($product->get_related(3) as $k=>$v) {
        $context['related'][] = Timber::get_post($v);
    }

    if ( get_option( 'woocommerce_review_rating_verification_required' ) === 'no' || wc_customer_bought_product( '', get_current_user_id(), $product->id ) ) {
        $commenter = wp_get_current_commenter();
        $comment_form = array(
            'title_reply' => __('Add a review', 'woocommerce'),
            'title_reply_to' => __('Leave a Reply to %s', 'woocommerce'),
            'title_reply_before' => '<h3>',
            'title_reply_after' => '</h3>',
            'comment_notes_after' => '',
            'label_submit' => __('Add Review', 'woocommerce'),
            'logged_in_as' => '',
            'comment_field' => '',
        );
        if ($account_page_url = wc_get_page_permalink('myaccount')) {
            $comment_form['must_log_in'] = '<p class="must-log-in">'
                .sprintf(__('You must be <a href="%s">logged in</a> to post a review.', 'woocommerce'), esc_url($account_page_url))
                .'</p>';
        }
        if (get_option('woocommerce_enable_review_rating') === 'yes') {
            $comment_form['comment_field'] = '<p class="comment-form-rating"><label for="rating">' . __('Your rating', 'woocommerce') . '</label><select name="rating" id="rating" aria-required="true" required>
							<option value="">' . __('Rate&hellip;', 'woocommerce') . '</option>
							<option value="5">' . __('Perfect', 'woocommerce') . '</option>
							<option value="4">' . __('Good', 'woocommerce') . '</option>
							<option value="3">' . __('Average', 'woocommerce') . '</option>
							<option value="2">' . __('Not that bad', 'woocommerce') . '</option>
							<option value="1">' . __('Very poor', 'woocommerce') . '</option>
						</select></p>';
        }
        $comment_form['comment_field'] .= '<p class="comment-form-comment"><label for="comment">' . __('Your review', 'woocommerce') . ' <span class="required">*</span></label><textarea id="comment" name="comment" cols="45" rows="8" aria-required="true" required></textarea></p>';
        ob_start();
        comment_form(apply_filters('woocommerce_product_review_comment_form_args', $comment_form));
        $context['review'] = ob_get_clean();
    }



    $timber::render('woocommerce/single-product.html.twig', $context);
}
else {
    $posts = $timber::get_posts();
    $context['products'] = $posts;

    if ( is_product_category() ) {
        $queried_object = get_queried_object();
        $term_id = $queried_object->term_id;
        $context['category'] = get_term( $term_id, 'product_cat' );
        $context['title'] = single_term_title('', false);
    }
    $timber::render('woocommerce/archive.html.twig', $context);
}
