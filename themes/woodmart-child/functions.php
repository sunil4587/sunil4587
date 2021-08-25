<?php
/**
 * Enqueue script and styles for child theme

 */

function woodmart_child_enqueue_styles() {
    wp_enqueue_style( 'child-style', get_stylesheet_directory_uri() . '/style.css', array( 'woodmart-style' ), woodmart_get_theme_info( 'Version' ) );
    wp_enqueue_script('custom-script', get_stylesheet_directory_uri() . '/js/custom_script.js', array( 'jquery' ));
}
add_action( 'wp_enqueue_scripts', 'woodmart_child_enqueue_styles', 10010 );


// THC data on products grid
function product_attr () {
    global $product;
    return $product->get_attribute( 'pa_thc' );
}

add_shortcode ('thc', 'product_attr');

// Variation price

