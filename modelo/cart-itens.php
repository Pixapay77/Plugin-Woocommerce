<?php defined( 'ABSPATH' ) || exit;

$row_classes     = array();
$main_classes    = array();
$sidebar_classes = array();

$auto_refresh  = get_theme_mod( 'cart_auto_refresh' );
$row_classes[] = 'row-large';
$row_classes[] = 'row-divided';

if ( $auto_refresh ) {
	$main_classes[] = 'cart-auto-refresh';
}


$row_classes     = implode( ' ', $row_classes );
$main_classes    = implode( ' ', $main_classes );
$sidebar_classes = implode( ' ', $sidebar_classes );


do_action( 'woocommerce_before_cart' ); ?>

<div class="cart-template full-cart span12 active">
    <div class="cart-template-holder" id="cartLoadedDiv">
        <div class="empty-cart-content" style="display:none">
            <h2 class="empty-cart-title">Seu carrinho está vazio.</h2>
            <div class="empty-cart-message">
                <p>Para continuar comprando, navegue pelas categorias do site ou faça uma busca pelo seu
                    produto.</p>
            </div>
            <div class="clearfix empty-cart-links">
                <a href="/" id="cart-choose-products" class="btn btn-large btn-success link-choose-products">Escolher
                    produtos</a>
            </div>
        </div>
        <div id="cart" class="cart">

            <form class="woocommerce-cart-form" action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post">

                <?php do_action( 'woocommerce_before_cart_table' ); ?>
                <table class="table cart-items">
                    <thead>
                        <tr>
                            <th colspan="2" class="product">Produto</th>
                            <th class="product-price">Preço</th>
                            <th class="quantity">Quantidade</th>
                            <th class="quantity-price">Total</th>
                            <th class="item-remove"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php do_action( 'woocommerce_before_cart_contents' ); ?>

                        <?php
			foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
				$_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
				$product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

				if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
					$product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
					?>
                        <tr
                            class="woocommerce-cart-form__cart-item <?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?>">
                            <td class="product-thumbnail">
                                <?php
						$thumbnail = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key );

						if ( ! $product_permalink ) {
							echo $thumbnail; // PHPCS: XSS ok.
						} else {
							printf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $thumbnail ); // PHPCS: XSS ok.
						}
						?>
                            </td>

                            <td class="product-name" data-title="<?php esc_attr_e( 'Product', 'woocommerce' ); ?>">
                                <?php
						if ( ! $product_permalink ) {
							echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key ) . '&nbsp;' );
						} else {
							echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', sprintf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $_product->get_name() ), $cart_item, $cart_item_key ) );
						}

						do_action( 'woocommerce_after_cart_item_name', $cart_item, $cart_item_key );

						// Meta data.
						echo wc_get_formatted_cart_item_data( $cart_item ); // PHPCS: XSS ok.

						// Backorder notification.
						if ( $_product->backorders_require_notification() && $_product->is_on_backorder( $cart_item['quantity'] ) ) {
							echo wp_kses_post( apply_filters( 'woocommerce_cart_item_backorder_notification', '<p class="backorder_notification">' . esc_html__( 'Available on backorder', 'woocommerce' ) . '</p>', $product_id ) );
						}

						// Mobile price.
						?>
                                <div class="show-for-small mobile-product-price">
                                    <span class="mobile-product-price__qty"><?php echo $cart_item['quantity']; ?> x
                                    </span>
                                    <?php
									echo apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key ); // PHPCS: XSS ok.
								?>
                                </div>
                            </td>

                            <td class="product-price" data-title="<?php esc_attr_e( 'Price', 'woocommerce' ); ?>">
                                <?php
								echo apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key ); // PHPCS: XSS ok.
							?>
                            </td>

                            <td class="quantity">
                                        <a class="decrement" data-action="item-quantity-change-decrement" href="<?php echo $cart_item['product_id'];?>" id="item-quantity-change-decrement-7433096" title="Diminuir a quantidade">         
                                            <i class="icon icon-minus-sign"></i>
                                            <span class="hide item-quantity-change-decrement-text" >Diminuir a quantidade</span>        
                                        </a> 
                                        <input data-product_id="<?php echo $cart_item['product_id'];?>" data-cart_item_key="<?php echo $cart_item_key;?>" type="tel" min="1" class="qty-<?php echo $cart_item['product_id'];?>" value="<?php echo $cart_item['quantity'];?>">
                                        <a  class="increment" data-action="item-quantity-change-increment" href="<?php echo $cart_item['product_id'];?>" id="item-quantity-change-increment-7433096" title="Aumentar a quantidade">          
                                           <i class="icon icon-plus-sign"></i>
                                           <span class="hide item-quantity-change-increment-text">Aumentar a quantidade</span>        
                                        </a>
                                    </td>


                            <td class="product-subtotal" data-title="<?php esc_attr_e( 'Subtotal', 'woocommerce' ); ?>">
                                <?php
								echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key ); // PHPCS: XSS ok.
							?>
                            </td>
                            <td class="product-remove">
                                <?php
								echo apply_filters( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
									'woocommerce_cart_item_remove_link',
									sprintf(
										'<a href="%s" class="remove" aria-label="%s" data-product_id="%s" data-product_sku="%s">&times;</a>',
										esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
										esc_html__( 'Remove this item', 'woocommerce' ),
										esc_attr( $product_id ),
										esc_attr( $_product->get_sku() )
									),
									$cart_item_key
								);
							?>
                            </td>

                        </tr>
                        <?php
				}
			}
			?>

                        <?php do_action( 'woocommerce_cart_contents' ); ?>

                        <tr>
                            <td colspan="6" class="actions clear">

                                <?php do_action( 'woocommerce_cart_actions' ); ?>

                                <button type="submit" class="button primary mt-0 pull-left small" name="update_cart"
                                    value="<?php esc_attr_e( 'Update cart', 'woocommerce' ); ?>"><?php esc_html_e( 'Update cart', 'woocommerce' ); ?></button>

                                <?php wp_nonce_field( 'woocommerce-cart', 'woocommerce-cart-nonce' ); ?>
                            </td>
                        </tr>

                        <?php do_action( 'woocommerce_after_cart_contents' ); ?>
                    </tbody>
                </table>
                <?php do_action( 'woocommerce_after_cart_table' ); ?>
            </form>
        </div>
    </div>

    <?php do_action( 'woocommerce_before_cart_collaterals' ); ?>




    <?php do_action( 'woocommerce_after_cart' ); ?>