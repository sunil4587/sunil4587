<?php
global $product;
$html = 'class="isw-swatches isw-swatches--in-loop"';
if ( has_post_thumbnail() ) {
	$srcset = wp_get_attachment_image_srcset( get_post_thumbnail_id(), 'shop_catalog' );
	$sizes  = wp_get_attachment_image_sizes( get_post_thumbnail_id(), 'shop_catalog' );
	$html   .= ' data-srcset="' . $srcset . '" data-sizes="' . $sizes . '" data-product_id="' . get_the_ID() . '"';
}

echo '<pre>';
  print_r( [
    'attributes'           => $attributes,
    'available_variations' => $available_variations,
    'variation_attributes' => $variation_attributes,
    'selected_attributes'  => $selected_attributes,
  ] );
echo '</pre>';


?>
<div <?php echo( $html ); ?>
        data-product_variations="<?php echo esc_attr( json_encode( $available_variations ) ) ?>">
	<?php
	foreach ( $attributes as $attribute_name => $options ) {
		$attr_id        = wc_attribute_taxonomy_id_by_name( $attribute_name );
		$attr_info      = wc_get_attribute( $attr_id );
		$term_sanitized = Insight_Swatches_Utils::utf8_urldecode( $attribute_name );

    echo '<pre>';
      print_r([
        'attr_id' => $attr_id,
        'attr_info' => $attr_info,
        'term_sanitized' => $term_sanitized,
      ]);
    echo '</pre>';

		$curr['style']  = $attr_info->type;
		$curr['title']  = $attr_info->name;
		if ( taxonomy_exists( $term_sanitized ) ) {
			$curr['terms'] = wp_get_post_terms( $product->get_id(), $term_sanitized, array( 'hide_empty' => false ) );
		}
		?>
        <div class="isw-swatch isw-swatch--isw_<?php echo esc_attr( $curr['style'] ); ?>"
             data-attribute="<?php echo esc_attr( $attribute_name ); ?>">
			<?php
			switch ( $curr['style'] ) {
				case 'text' :
					foreach ( $curr['terms'] as $l => $b ) {
						$val     = get_term_meta( $b->term_id, 'sw_text', true ) ?: $b->name;
						$tooltip = get_term_meta( $b->term_id, 'sw_tooltip', true ) ?: $val;
						?>
                        <span
                                class="isw-term <?php echo apply_filters( 'isw_term_class', '', $b ); ?>"
                                aria-label="<?php echo esc_attr( $tooltip ); ?>"
                                title="<?php echo esc_attr( $tooltip ); ?>"
                                data-term="<?php echo esc_attr( $b->slug ); ?>"><?php echo esc_html( $val ); ?></span>
						<?php
					}
					break;
				case 'color':
					foreach ( $curr['terms'] as $l => $b ) {
						$val     = get_term_meta( $b->term_id, 'sw_color', true ) ?: '#fff';
						$tooltip = get_term_meta( $b->term_id, 'sw_tooltip', true ) ?: $b->name;
						?>
                        <span
                                class="isw-term <?php echo apply_filters( 'isw_term_class', '', $b ); ?>"
                                aria-label="<?php echo esc_attr( $tooltip ); ?>"
                                title="<?php echo esc_attr( $tooltip ); ?>"
                                data-term="<?php echo esc_attr( $b->slug ); ?>"
                                style="background-color: <?php echo esc_attr( $val ); ?>"><?php echo $b->name; ?></span>
						<?php
					}
					break;
				case 'image':
					foreach ( $curr['terms'] as $l => $b ) {
						$val     = get_term_meta( $b->term_id, 'sw_image', true ) ? wp_get_attachment_thumb_url( get_term_meta( $b->term_id, 'sw_image', true ) ) : wc_placeholder_img_src();
						$tooltip = get_term_meta( $b->term_id, 'sw_tooltip', true ) ?: $b->name;
						?>
                        <span
                                class="isw-term <?php echo apply_filters( 'isw_term_class', '', $b ); ?>"
                                aria-label="<?php echo esc_attr( $tooltip ); ?>"
                                title="<?php echo esc_attr( $tooltip ); ?>"
                                data-term="<?php echo esc_attr( $b->slug ); ?>"><img
                                    src="<?php echo esc_url( $val ); ?>"
                                    alt="<?php echo esc_attr( $b->name ); ?>"/></span>
						<?php
					}
					break;
				default:
					break;
			}
			?>
        </div>
		<?php


    $str = 'In My Cart : 11.584 items';
    $int = filter_var($str, FILTER_SANITIZE_NUMBER_INT);

    $num = "100.19MyCart";
    $num = (float)$num;

    echo '<pre>';
    var_dump($int);
    var_dump($num);


    if( $product->is_on_sale() ) {
      echo 'isOnSale';
      $price = $options->get_sale_price();
    }

    $price = $options->get_regular_price();

    print_r([
      'attribute_name' => $attribute_name,
      'options' => $options,
      'price' => $price
    ]);
    echo '</pre>';


	}



	?>

    <a class="reset_variations reset_variations--loop" href="#"
       style="display: none;"><?php esc_html_e( 'Clear', 'insight-swatches' ); ?></a>
</div>
