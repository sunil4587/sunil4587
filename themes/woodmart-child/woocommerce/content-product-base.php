<?php 
global $product;

$action_classes  = '';
$add_btn_classes = '';


if ( 'carousel' === woodmart_loop_prop('products_view') ) {
	$action_classes  .= ' woodmart-buttons wd-pos-r-t';
	$add_btn_classes .= ' wd-action-btn wd-add-cart-btn wd-style-icon';
} else {
	$action_classes  .= ' wd-bottom-actions';
}

do_action( 'woocommerce_before_shop_loop_item' ); ?>

<?php 

	$terms = wp_get_post_terms( get_the_id(), '_product_color' );

	// echo '<pre>';
	// print_r( $terms );
	// echo '</pre>';

	$inlineColor = "";
	$borderColor = "";
	$termName = "";
    $productIcon = "";

	foreach( $terms as $term){
		// if( strtolower($term->name) === strtolower('Featured') ){
		// 	continue;
		// }
		$productColor = get_field('color', $term->taxonomy . '_' . $term->term_id );
		$productIcon  = get_field('icon', $term->taxonomy . '_' . $term->term_id );
		$inlineColor  = "background: {$productColor};border-color: {$productColor};";
		$borderColor  = "border-bottom-color: {$productColor};";
		$termName     = $term->name;
		break;
	}

    # Adding color for reward page product
	$productBgColor = get_field( "purchasable_by_points_only", get_the_id() ) === 'yes' ? get_field( "product_color", get_the_id() ) : "";
	$productCss = !empty($productBgColor) ? "background-color:{$productBgColor}" : "";
?>


<div class="product-wrapper" xmlns="http://www.w3.org/1999/html" style="<?php echo $borderColor; ?>">
	<div class="content-product-imagin"></div>
	<div class="product-element-top">
		<a href="<?php echo esc_url( get_permalink() ); ?>" class="product-image-link" style="<?php echo $productCss; ?>">
			<?php
				/**
				 * woocommerce_before_shop_loop_item_title hook
				 *
				 * @hooked woocommerce_show_product_loop_sale_flash - 10
				 * @hooked woodmart_template_loop_product_thumbnail - 10
				 */
				do_action( 'woocommerce_before_shop_loop_item_title' );
			?>
		</a>
        <div class="thc">
            <span class="thc-label">THC </span>
            <span class="thc-info">
            <?php
            echo do_shortcode('[thc]');
            ?>
            </span>
        </div>

		<?php woodmart_hover_image(); ?>



		<?php woodmart_quick_shop_wrapper(); ?>
	</div>

	<div class="product-information">
        <?php
			/**
			 * woocommerce_shop_loop_item_title hook
			 *
			 * @hooked woocommerce_template_loop_product_title - 10
			 */
			
			do_action( 'woocommerce_shop_loop_item_title' );
		?>
		<?php
			woodmart_product_categories();
			woodmart_product_brands_links();
		?>
		<div class="product-rating-price">
			<div class="wrapp-product-price">
				<?php
					/**
					 * woocommerce_after_shop_loop_item_title hook
					 *
					 * @hooked woocommerce_template_loop_rating - 5
					 * @hooked woocommerce_template_loop_price - 10
					 */
					do_action( 'woocommerce_after_shop_loop_item_title' );
				?>
			</div>
		</div>
		<div class="fade-in-block">
			<div class="hover-content woodmart-more-desc">
				<div class="hover-content-inner woodmart-more-desc-inner">
					<?php 
						if ( woodmart_get_opt( 'base_hover_content' ) == 'excerpt' ) {
							echo do_shortcode( get_the_excerpt() );
						}else if ( woodmart_get_opt( 'base_hover_content' ) == 'additional_info' ){
							wc_display_product_attributes( $product );
						}
					?>
				</div>
			</div>




			<?php if ( woodmart_loop_prop( 'progress_bar' ) ): ?>
				<?php woodmart_stock_progress_bar(); ?>
			<?php endif ?>
			
			<?php if ( woodmart_loop_prop( 'timer' ) ): ?>
				<?php woodmart_product_sale_countdown(); ?>
			<?php endif ?>


			<div class="custom-wrapping">

				<div class="wrapp-swatches-">
					<?php echo woodmart_swatches_list();?>
					<?php woodmart_add_to_compare_loop_btn(); ?>
				</div>

				<div class="<?php echo esc_attr( $action_classes ); ?>">
					<div class="wrap-wishlist-button"><?php do_action( 'woodmart_product_action_buttons' ); ?></div>
					<div class="woodmart-add-btn<?php echo esc_attr( $add_btn_classes ); ?>"><?php do_action( 'woocommerce_after_shop_loop_item' ); ?></div>
					<div class="wrap-quickview-button"><?php woodmart_quick_view_btn( get_the_ID() ); ?></div>
				</div>


				<span class="product-strain product-strain-<?php echo $productIcon; ?>" style="<?php echo $inlineColor; ?>">
					 <span class="product-strain__text"><?php echo $termName; ?></span>
				</span>

			</div>
			
		</div>
	</div>
</div>
