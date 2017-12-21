/* Custom Shoping Cart in the top */
    function theme_wc_print_mini_cart() {
        ?>
        <div class="tr-dropdown-menu">
            <?php if ( sizeof( WC()->cart->get_cart() ) > 0 ) : ?>
                <ul class="tr-list">
                    <?php foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) :
                    $_product = $cart_item['data'];
                    // Only display if allowed
                    if ( ! apply_filters('woocommerce_widget_cart_item_visible', true, $cart_item, $cart_item_key ) || ! $_product->exists() || $cart_item['quantity'] == 0 ) continue;
                    // Get price
                    $product_price = get_option( 'woocommerce_tax_display_cart' ) == 'excl' ? $_product->get_price_excluding_tax() : $_product->get_price_including_tax();
                    $product_price = apply_filters( 'woocommerce_cart_item_price_html', woocommerce_price( $product_price ), $cart_item, $cart_item_key );
                    ?>
                    <li class="remove-item">
                    	<?php echo apply_filters( 'woocommerce_cart_item_remove_link', sprintf(
					    '<a href="%s" class="remove remove-icon" aria-label="%s" data-product_id="%s" data-product_sku="%s" data-cart_item_key="%s"><i class="fa fa-times" aria-hidden="true"></i></a>',
					    esc_url( WC()->cart->get_remove_url( $cart_item_key ) ),
					    esc_html__( 'Remove this item', 'carrito' ),
					    esc_attr( $cart_item['product_id'] ),
					    esc_attr( $_product->get_sku() ),
					    esc_attr( $cart_item_key )
					), $cart_item_key ); ?>
						<div class="tr-product">
							<a href="<?php echo get_permalink( $cart_item['product_id'] ); ?>">
	                        	<span class="product-image">
	                            	<?php echo $_product->get_image('carrito-icon-thumb'); ?>
	                        	</span>
                            	<span class="product-title"><?php echo  $_product->get_title(); ?></span>
	                            <?php echo apply_filters( 'woocommerce_widget_cart_item_price', '<span class="price">' . $product_price . '</span>', $cart_item, $cart_item_key ); ?>
                            </a>
                    	</div>
                    </li>
                    <?php endforeach; ?>
                </ul><!-- end .carrito-mini-cart-products -->
            <?php else : ?>
                <p class="carrito-mini-cart-product-empty"><?php _e( 'No products in the cart.', 'carrito' ); ?></p>
            <?php endif; ?>
            <?php if (sizeof( WC()->cart->get_cart()) > 0) : ?>
                <div class="total-price">
                	 <span><strong><?php _e( 'Cart Subtotal', 'carrito' ); ?></strong>: <?php echo WC()->cart->get_cart_subtotal(); ?>
                	</span> 	
           		</div>
                <div class="buttons">
                    <a href="<?php echo WC()->cart->get_cart_url(); ?>" class="btn btn-primary cart-button">
                    	<?php _e( 'Cart', 'carrito' ); ?>
                    </a>
                    <a href="<?php echo WC()->cart->get_checkout_url(); ?>" class="btn btn-primary"><?php _e( 'Checkout', 'carrito' ); ?>
                    </a>
                </div>
            <?php endif; ?>
        </div>
        <?php
    }

// Remove product in the cart using ajax
function warp_ajax_product_remove()
{
    // Get mini cart
    ob_start();

    foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item)
    {
        if($cart_item['product_id'] == $_POST['product_id'] && $cart_item_key == $_POST['cart_item_key'] )
        {
            WC()->cart->remove_cart_item($cart_item_key);
        }
    }

    WC()->cart->calculate_totals();
    WC()->cart->maybe_set_cart_cookies();

    woocommerce_mini_cart();

    $mini_cart = ob_get_clean();

    // Fragments and mini cart are returned
    $data = array(
        'fragments' => apply_filters( 'woocommerce_add_to_cart_fragments', array(
                'div.widget_shopping_cart_content' => '<div class="widget_shopping_cart_content">' . $mini_cart . '</div>'
            )
        ),
        'cart_hash' => apply_filters( 'woocommerce_add_to_cart_hash', WC()->cart->get_cart_for_session() ? md5( json_encode( WC()->cart->get_cart_for_session() ) ) : '', WC()->cart->get_cart_for_session() )
    );

    wp_send_json( $data );

    die();
}

add_action( 'wp_ajax_product_remove', 'warp_ajax_product_remove' );
add_action( 'wp_ajax_nopriv_product_remove', 'warp_ajax_product_remove' );
