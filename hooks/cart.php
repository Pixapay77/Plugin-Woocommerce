<?php


class HooksCart
{
    protected $Checkout;

    public function __construct()
    {

        add_action( 'seomidia_woocommerce_cart_collaterals', array($this,'seomidia_woocommerce_cart_totals'));
        add_action( 'woocommerce_checkout_billing', array( $this, 'checkout_form_billing' ) );
        add_action( 'woocommerce_checkout_shipping', array( $this, 'checkout_form_shipping' ) );
		add_filter( 'woocommerce_checkout_fields' , array( $this, 'custom_override_checkout_fields') );
//		add_filter( 'woocommerce_form_field', array( $this, 'filter_woocommerce_form_field_radio'), 10, 4);
        add_action( 'seomidia_checkout_order_shipping_options', array( $this, 'seomidia_checkout_order_shipping_options' ) );
        add_action( 'seomidia_checkout_order_payment', array( $this, 'seomidia_checkout_order_payment' ) );
        add_action( 'seomidia_checkout_order_resume', array( $this, 'seomidia_checkout_order_resume' ) );

        $this->Checkout = new WC_Checkout();

    }

	function seomidia_checkout_order_resume(){
    ?>

        <table id="resume" class="shop_table woocommerce-checkout-review-order-table"></table>

	<?php }

	function seomidia_checkout_order_payment(){

		if ( ! wp_doing_ajax() ) {
			do_action( 'woocommerce_review_order_before_payment' );
		}
		?>
<div id="payment" class="woocommerce-checkout-payment">
    <?php if ( WC()->cart->needs_payment() ) : ?>
    <ul class="wc_payment_methods payment_methods methods">
        <?php
					if ( ! empty( $available_gateways ) ) {
						foreach ( $available_gateways as $gateway ) {
							wc_get_template( 'checkout/payment-method.php', array( 'gateway' => $gateway ) );
						}
					} else {
						echo '<li class="woocommerce-notice woocommerce-notice--info woocommerce-info">' . apply_filters( 'woocommerce_no_available_payment_methods_message', WC()->customer->get_billing_country() ? esc_html__( 'Sorry, it seems that there are no available payment methods for your state. Please contact us if you require assistance or wish to make alternate arrangements.', 'woocommerce' ) : esc_html__( 'Please fill in your details above to see available payment methods.', 'woocommerce' ) ) . '</li>'; // @codingStandardsIgnoreLine
					}
					?>
    </ul>
    <?php endif; ?>
    <div class="form-row place-order">
        <noscript>
            <?php
					/* translators: $1 and $2 opening and closing emphasis tags respectively */
					printf( esc_html__( 'Since your browser does not support JavaScript, or it is disabled, please ensure you click the %1$sUpdate Totals%2$s button before placing your order. You may be charged more than the amount stated above if you fail to do so.', 'woocommerce' ), '<em>', '</em>' );
					?>
            <br /><button type="submit" class="button alt" name="woocommerce_checkout_update_totals"
                value="<?php esc_attr_e( 'Update totals', 'woocommerce' ); ?>"><?php esc_html_e( 'Update totals', 'woocommerce' ); ?></button>
        </noscript>

        <?php wc_get_template( 'checkout/terms.php' ); ?>

        <?php do_action( 'woocommerce_review_order_before_submit' ); ?>

        <?php echo apply_filters( 'woocommerce_order_button_html', '<button type="submit" class="button alt" name="woocommerce_checkout_place_order" id="place_order" value="' . esc_attr( $order_button_text ) . '" data-value="' . esc_attr( $order_button_text ) . '">' . esc_html( $order_button_text ) . '</button>' ); // @codingStandardsIgnoreLine ?>

        <?php do_action( 'woocommerce_review_order_after_submit' ); ?>

        <?php wp_nonce_field( 'woocommerce-process_checkout', 'woocommerce-process-checkout-nonce' ); ?>
    </div>
</div>
<?php
		if ( ! wp_doing_ajax() ) {
			do_action( 'woocommerce_review_order_after_payment' );
		}

	}

	function seomidia_checkout_order_shipping_options(){

		WC()->cart->calculate_shipping();
		$packages = WC()->shipping()->get_packages();
		$first    = true;


		foreach ( $packages as $i => $package ) {
			$chosen_method = isset( WC()->session->chosen_shipping_methods[ $i ] ) ? WC()->session->chosen_shipping_methods[ $i ] : '';
			$product_names = array();

			if ( count( $packages ) > 1 ) {
				foreach ( $package['contents'] as $item_id => $values ) {
					$product_names[ $item_id ] = $values['data']->get_name() . ' &times;' . $values['quantity'];
				}
				$product_names = apply_filters( 'woocommerce_shipping_package_details_array', $product_names, $package );

			}

			wc_get_template(
				'cart/cart-shipping.php',
				array(
					'package'                  => $package,
					'available_methods'        => $package['rates'],
					'show_package_details'     => count( $packages ) > 1,
					'show_shipping_calculator' => apply_filters( 'woocommerce_shipping_show_shipping_calculator', $first, $i, $package ),
					'package_details'          => implode( ', ', $product_names ),
					/* translators: %d: shipping package number */
					'package_name'             => apply_filters( 'woocommerce_shipping_package_name', ( ( $i + 1 ) > 1 ) ? sprintf( _x( 'Shipping %d', 'shipping packages', 'woocommerce' ), ( $i + 1 ) ) : _x( 'Shipping', 'shipping packages', 'woocommerce' ), $i, $package ),
					'index'                    => $i,
					'chosen_method'            => $chosen_method,
					'formatted_destination'    => WC()->countries->get_formatted_address( $package['destination'], ', ' ),
					'has_calculated_shipping'  => WC()->customer->has_calculated_shipping(),
				)
			);

			$first = false;
		}

	}


	function filter_woocommerce_form_field_radio( $field, $key, $args, $value ) {
    //  Remove the .form-row class from the current field wrapper


	if(!in_array($key,['billing_first_name','billing_last_name','billing_cpf','billing_email']))
        $field = str_replace('form-row', '', $field);

	if(in_array($key,['billing_postcode']))
		$field = str_replace('form-row-wide', 'form-row-first', $field);

	$opendiv = '';

    //  Wrap the field (and its wrapper) in a new custom div, adding .form-row so the reshuffling works as expected, and adding the field priority
		if($key == 'billing_email'){
			if(is_user_logged_in()){
				$style = 'style="color: rgb(9, 113, 45);"';
				$edit  = '<i class="fa fa-edit" aria-hidden="true" style="float: right;"></i>';
			}

			$opendiv = '
			<div id="tab1">
				<h4 '.$style.'></i><i class="fa fa-user" aria-hidden="true"></i> Dados Pessoais '.$edit.'</h4>
				<div class="content">
			';
		}

		if($key == 'billing_cpf')
	      $closediv = '
			  </div>
			  </div>
		  ';


		if($key == 'billing_postcode'){
						if(is_user_logged_in()){
				$style = 'style="color: rgb(9, 113, 45);"';
				$edit  = '<i class="fa fa-edit" aria-hidden="true" style="float: right;"></i>';
			}

			$opendiv = '
			<div id="tab2">
			<h4 '.$style.'><i class="fa fa-map-marker" aria-hidden="true"></i> Endereço '.$edit.'</h4>
			<div class="content">
			';
		}

		if($key == 'billing_country')
			$closediv = '
	- 		 </div>
			</div> 
			';

			if($key == 'account_username')
			$opendiv = '
			<div id="tab3">
			<h4><i class="fa fa-lock" aria-hidden="true"></i> Usuário e senha</h4>
			<div class="content">
			';

		if($key == 'account_password')
			$closediv = '
	- 		 </div>
			</div> 
			';




		$field = $opendiv . $field . $closediv;

    return $field;
	}


    function seomidia_woocommerce_cart_totals() {
		wc_get_template( '../../seomidia-checkout-custom/templates/woocommerce/cart/cart-totals.php' );
	}

	/**
	 * Output the billing form.
	 */
	function checkout_form_billing() {
		wc_get_template( '../../seomidia-checkout-custom/templates/woocommerce/checkout/form-billing.php', array( 'checkout' => $this->Checkout ) );
	}

	public function checkout_form_shipping() {
		wc_get_template( 'checkout/form-shipping.php', array( 'checkout' => $this->Checkout ) );
	}

	public function custom_override_checkout_fields( $fields )
	{
		// unset($fields['billing']['billing_address_2']);
		// unset($fields['billing']['billing_country']);
		return $fields;
	}


}
new HooksCart();
?>