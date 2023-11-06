<?php

class Pixapay
{
    public function __construct(){
        add_action( 'wp_ajax_installments', array($this,'get_installments') );
        add_action( 'wp_ajax_nopriv_installments',  array($this,'get_installments') );

    }

    public function get_installments(){
        $settings = get_option( 'woocommerce_pixapay_settings' );

        echo json_encode([
            'success' => true,
            'installments' => $settings['installments']
        ]);
        wp_die();
    }
}

new Pixapay();