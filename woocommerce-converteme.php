<?php
    /*
    Plugin Name: Converteme Checkout
    Plugin URI: https://converte.me/plugin/converteme-checkout
    Description: Descricao provisoria Converteme Checkout.
    Author: Converte
    Version: 1.0.0
    Author URI:
    */


    add_action( 'plugins_loaded', 'Converteme_init' );
    function Converteme_init()
    {
        class WC_Converteme_Gateway extends WC_Payment_Gateway
        {
            public function __construct()
            {
                $this->id = 'converteme'; // payment gateway plugin ID
                $this->icon = ''; // URL of the icon that will be displayed on checkout page near your gateway name
                $this->has_fields = true; // in case you need a custom credit card form
                $this->method_title = 'Converteme Gateway';
                $this->method_description = 'Descricao do servico de pagamento'; // will be displayed on the options page

                // gateways can support subscriptions, refunds, saved payment methods,
                // but in this tutorial we begin with simple payments
                $this->supports = array(
                    'products'
                );

                // Method with all the options fields
                $this->init_form_fields();

                // Load the settings.
                $this->init_settings();
                $this->title = $this->get_option( 'title' );
                $this->description = $this->get_option( 'description' );
                $this->enabled = $this->get_option( 'enabled' );
                $this->testmode = 'yes' === $this->get_option( 'testmode' );
                $this->clientid = $this->testmode ? $this->get_option( 'test_client_id' ) : $this->get_option( 'client_id' );
                $this->clientsecret = $this->testmode ? $this->get_option( 'test_client_secret' ) : $this->get_option( 'client_secret' );

                // This action hook saves the settings
                add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );

                // We need custom JavaScript to obtain a token
                add_action( 'wp_enqueue_scripts', array( $this, 'payment_scripts' ) );

                // You can also register a webhook here
                // add_action( 'woocommerce_api_{webhook name}', array( $this, 'webhook' ) );
            }

            public function init_form_fields()
            {
                $this->form_fields = array(
                    'enabled' => array(
                        'title'       => 'Ativar/Desativar',
                        'label'       => 'Ativar Converteme Gateway',
                        'type'        => 'checkbox',
                        'description' => '',
                        'default'     => 'no'
                    ),
                    'title' => array(
                        'title'       => 'Titulo',
                        'type'        => 'text',
                        'description' => 'Nome do metodo de pagamento.',
                        'default'     => 'Converteme',
                        'desc_tip'    => true,
                    ),
                    'description' => array(
                        'title'       => 'Descrição',
                        'type'        => 'textarea',
                        'description' => 'Descricao do servico de pagamento.',
                        'default'     => 'Descricao do servico de pagamento.',
                    ),
                    'testmode' => array(
                        'title'       => 'Modo de teste',
                        'label'       => 'Ativar modo teste',
                        'type'        => 'checkbox',
                        'description' => 'Ativar o modo teste para homologacao.',
                        'default'     => 'yes',
                        'desc_tip'    => true,
                    ),
                    'test_client_id' => array(
                        'title'       => 'Client id',
                        'type'        => 'text'
                    ),
                    'test_client_secret' => array(
                        'title'       => 'Client secret',
                        'type'        => 'password',
                    ),
                    'client_id' => array(
                        'title'       => 'Client id',
                        'type'        => 'text'
                    ),
                    'client_secret' => array(
                        'title'       => 'Client secret',
                        'type'        => 'password'
                    )
                );
            }

            public function payment_fields()
            {

                // ok, let's display some description before the payment form
                if ( $this->description ) {
                    // you can instructions for test mode, I mean test card numbers etc.
                    if ( $this->testmode ) {
                        $this->description .= ' TEST MODE ENABLED. In test mode, you can use the card numbers listed in <a href="#">documentation</a>.';
                        $this->description  = trim( $this->description );
                    }
                    // display the description with <p> tags etc.
                    echo wpautop( wp_kses_post( $this->description ) );
                }

                // I will echo() the form, but you can close PHP tags and print it directly in HTML
                echo '<fieldset id="wc-' . esc_attr( $this->id ) . '-cc-form" class="wc-credit-card-form wc-payment-form" style="background:transparent;">';

                // Add this action hook if you want your custom payment gateway to support it
                do_action( 'woocommerce_credit_card_form_start', $this->id );

                // I recommend to use inique IDs, because other gateways could already use #ccNo, #expdate, #cvc
//                $html = file_get_contents(__FILE__ . 'public/template/methods.html');
//                echo $html;
                do_action( 'woocommerce_credit_card_form_end', $this->id );

                echo '<div class="clear"></div></fieldset>';
            }

            public function payment_scripts()
            {
                // we need JavaScript to process a token only on cart/checkout pages, right?
                if ( ! is_cart() && ! is_checkout() && ! isset( $_GET['pay_for_order'] ) ) {
                    return;
                }

                // if our payment gateway is disabled, we do not have to enqueue JS too
                if ( 'no' === $this->enabled ) {
                    return;
                }

                // no reason to enqueue JavaScript if API keys are not set
                if ( empty( $this->clientid ) || empty( $this->clientsecret ) ) {
                    return;
                }

                // do not work with card detailes without SSL unless your website is in a test mode
                if ( ! $this->testmode && ! is_ssl() ) {
                    return;
                }

                // let's suppose it is our payment processor JavaScript that allows to obtain a token
                wp_enqueue_script( 'misha_js', 'https://www.mishapayments.com/api/token.js' );

                // and this is our custom JS in your plugin directory that works with token.js
                wp_register_script( 'woocommerce_misha', plugins_url( 'misha.js', __FILE__ ), array( 'jquery', 'misha_js' ) );

                // in most payment processors you have to use PUBLIC KEY to obtain a token
                wp_localize_script( 'woocommerce_misha', 'misha_params', array(
                    'publishableKey' => $this->publishable_key
                ) );

                wp_enqueue_script( 'woocommerce_misha' );
            }

            public function validate_fields()
            {
                if( empty( $_POST[ 'billing_first_name' ]) ) {
                    wc_add_notice(  'First name is required!', 'error' );
                    return false;
                }
                return true;
            }

            public function process_payment( $order_id )
            {
                global $woocommerce;

                // we need it to get any order detailes
                $order = wc_get_order( $order_id );


                /*
                  * Array with parameters for API interaction
                 */
                $args = array();

                /*
                 * Your API interaction could be built with wp_remote_post()
                  */
                $response = wp_remote_post( '{payment processor endpoint}', $args );


                if( !is_wp_error( $response ) ) {

                    $body = json_decode( $response['body'], true );

                    // it could be different depending on your payment processor
                    if ( $body['response']['responseCode'] == 'APPROVED' ) {

                        // we received the payment
                        $order->payment_complete();
                        $order->reduce_order_stock();

                        // some notes to customer (replace true with false to make it private)
                        $order->add_order_note( 'Hey, your order is paid! Thank you!', true );

                        // Empty cart
                        $woocommerce->cart->empty_cart();

                        // Redirect to the thank you page
                        return array(
                            'result' => 'success',
                            'redirect' => $this->get_return_url( $order )
                        );

                    } else {
                        wc_add_notice(  'Please try again.', 'error' );
                        return;
                    }

                } else {
                    wc_add_notice(  'Connection error.', 'error' );
                    return;
                }
            }

            public function webhook()
            {
            }
        }
    }

    add_filter( 'woocommerce_payment_gateways', 'WC_Converteme_add_gateway_class' );
    function WC_Converteme_add_gateway_class( $gateways ) {
        $gateways[] = 'WC_Converteme_Gateway'; // your class name is here
        return $gateways;
    }
