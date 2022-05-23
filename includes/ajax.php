<?php

class SeomidiaCart
{
    public function __construct(){
        add_action( 'wp_ajax_delete_prod', array($this,'product_remove_checkout') );
        add_action( 'wp_ajax_nopriv_delete_prod',  array($this,'product_remove_checkout') );

        add_action( 'wp_ajax_qty_cart', array($this,'warp_ajax_qty_cart') );
        add_action( 'wp_ajax_nopriv_qty_cart',  array($this,'warp_ajax_qty_cart') );

        add_action( 'wp_ajax_get_and_set_shipping', array($this,'calculate_shipping') );
        add_action( 'wp_ajax_nopriv_get_and_set_shipping',  array($this,'calculate_shipping') );

        add_action( 'wp_ajax_get_and_set_shipping_rate', array($this,'shipping_rate') );
        add_action( 'wp_ajax_nopriv_get_and_set_shipping_rate',  array($this,'shipping_rate') );

        add_action( 'wp_ajax_search_email', array($this,'check_email') );
        add_action( 'wp_ajax_nopriv_search_email',  array($this,'check_email') );

        add_action( 'wp_ajax_auth_customer', array($this,'pw_auth_customer') );
        add_action( 'wp_ajax_nopriv_auth_customer',  array($this,'pw_auth_customer') );

        add_action( 'wp_ajax_check_Session', array($this,'check_Session') );
        add_action( 'wp_ajax_nopriv_check_Session',  array($this,'check_Session') );


    }

    public function product_remove_checkout(){
        $product_id = $_POST['prod_id'];
        $product_cart_id = WC()->cart->generate_cart_id( $product_id );
        $cart_item_key = WC()->cart->find_product_in_cart( $product_cart_id );
        if ( $cart_item_key ) {
            WC()->cart->remove_cart_item( $cart_item_key );
        }

        echo json_encode([
            'success' => true,
            'message' => ''
        ]);
        wp_die();
    }
    public function warp_ajax_product_remove()
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
    public function warp_ajax_qty_cart()
    {
            // // Set item key as the hash found in input.qty's name
            $cart_item_key = $_POST['hash'];

            // // Get the array of values owned by the product we're updating
            $threeball_product_values = WC()->cart->get_cart_item( $cart_item_key );

            // // Get the quantity of the item in the cart
            $threeball_product_quantity = apply_filters( 'woocommerce_stock_amount_cart_item', apply_filters( 'woocommerce_stock_amount', preg_replace( "/[^0-9\.]/", '', filter_var($_POST['quantity'], FILTER_SANITIZE_NUMBER_INT)) ), $cart_item_key );

            // // Update cart validation
            $passed_validation  = apply_filters( 'woocommerce_update_cart_validation', true, $cart_item_key, $threeball_product_values, $threeball_product_quantity );

            // // Update the quantity of the item in the cart
            if ( $passed_validation ) {
                WC()->cart->set_quantity( $cart_item_key, $threeball_product_quantity, true );
            }
            die();
    }
    public function shipping_rate()
    {

        //    check_ajax_referer( 'shipping-rate', 'security' );


		wc_maybe_define_constant( 'WOOCOMMERCE_CART', true );

		$chosen_shipping_methods = WC()->session->get( 'chosen_shipping_methods' );
		$posted_shipping_methods = isset( $_POST['shipping_method'] ) ? wc_clean( wp_unslash( $_POST['shipping_method'] ) ) : array();

		if ( is_array( $posted_shipping_methods ) ) {
			foreach ( $posted_shipping_methods as $i => $value ) {
				$chosen_shipping_methods[ $i ] = $value;
			}
		}

		WC()->session->set( 'chosen_shipping_methods', $chosen_shipping_methods );

		self::get_cart_totals();
        wp_die();
    }
    public static function get_cart_totals() {
		wc_maybe_define_constant( 'WOOCOMMERCE_CART', true );
		WC()->cart->calculate_totals();
		woocommerce_cart_totals();
		wp_die();
	}
    public function calculate_shipping() {
		try {
			WC()->shipping()->reset_shipping();


			$address = array();

			$address['country']  = isset( $_POST['calc_shipping_country'] ) ? wc_clean( wp_unslash( $_POST['calc_shipping_country'] ) ) : ''; // WPCS: input var ok, CSRF ok, sanitization ok.
			$address['state']    = isset( $_POST['calc_shipping_state'] ) ? wc_clean( wp_unslash( $_POST['calc_shipping_state'] ) ) : ''; // WPCS: input var ok, CSRF ok, sanitization ok.
			$address['postcode'] = isset( $_POST['calc_shipping_postcode'] ) ? wc_clean( wp_unslash( $_POST['calc_shipping_postcode'] ) ) : ''; // WPCS: input var ok, CSRF ok, sanitization ok.
			$address['city']     = isset( $_POST['calc_shipping_city'] ) ? wc_clean( wp_unslash( $_POST['calc_shipping_city'] ) ) : ''; // WPCS: input var ok, CSRF ok, sanitization ok.
			
            $address = apply_filters( 'woocommerce_cart_calculate_shipping_address', $address );


			if ( $address['postcode'] && ! WC_Validation::is_postcode( $address['postcode'], $address['country'] ) ) {
				throw new Exception( __( 'Please enter a valid postcode / ZIP.', 'woocommerce' ) );
			} elseif ( $address['postcode'] ) {
				$address['postcode'] = wc_format_postcode( $address['postcode'], $address['country'] );
			}

			if ( $address['country'] ) {
				if ( ! WC()->customer->get_billing_first_name() ) {
					WC()->customer->set_billing_location( $address['country'], $address['state'], $address['postcode'], $address['city'] );
                }
				WC()->customer->set_shipping_location( $address['country'], $address['state'], $address['postcode'], $address['city'] );
			} else {
				WC()->customer->set_billing_address_to_base();
				WC()->customer->set_shipping_address_to_base();
			}
            var_dump(WC()->customer);

			WC()->customer->set_calculated_shipping( true );
			WC()->customer->save();

			wc_add_notice( __( 'Shipping costs updated.', 'woocommerce' ), 'notice' );

			do_action( 'woocommerce_calculated_shipping' );

		} catch ( Exception $e ) {
			if ( ! empty( $e ) ) {
				wc_add_notice( $e->getMessage(), 'error' );
			}
		}
        wp_die();
	}
    public function check_email()
    {
        global $wpdb;

        extract($_POST);
        $sql = "SELECT * FROM {$wpdb->prefix}users
        WHERE user_email = '{$email}'";
       

        $User = $wpdb->get_results($sql);

                echo json_encode($User[0]);
                wp_die();

    }
    function pw_auth_customer(){
        extract($_POST);
        global $wpdb;

            $sql = "SELECT * FROM {$wpdb->prefix}wc_customer_lookup
            WHERE email = '{$user}'";
            $customer = $wpdb->get_results($sql);
            $dados = json_encode($customer[0]);
            $auth = wp_authenticate($user, $password);
    
            if(isset($auth->errors)){
                echo json_encode([
                    'success' => false,
                    'message' => $auth->errors['incorrect_password'][0]
                ]);
            }else{

                $creds = array(
                    'user_login'    => $user,
                    'user_password' => $password,
                    'remember'      => true
                );
                
                if(!is_user_logged_in()){
                    $user = wp_signon( $creds, false );
                }
                
                echo json_encode([
                    'success' => true,
                ]);
            }

        
        wp_die();
    }
    function check_Session(){
        if(!is_user_logged_in()){
            echo json_encode([
                'success' => false,
            ]);
         }else{
            echo json_encode([
                'success' => true,
            ]);
         }
         wp_die();
        }
}

new SeomidiaCart();