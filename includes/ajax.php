<?php

class Pixapay
{
    public function __construct(){
        add_action( 'wp_ajax_installments', array($this,'get_installments') );
        add_action( 'wp_ajax_nopriv_installments',  array($this,'get_installments') );

        add_action( 'wp_ajax_methodsSelected', array($this,'get_methodsSelected') );
        add_action( 'wp_ajax_nopriv_methodsSelected',  array($this,'get_methodsSelected') );

    }

    public function get_installments(){
        $settings = get_option( 'woocommerce_pixapay_settings' );

        echo json_encode([
            'success' => true,
            'installments' => $settings['installments']
        ]);
        wp_die();
    }
    public function get_methodsSelected(){
        $settings = get_option( 'woocommerce_pixapay_settings' );

        echo json_encode([
            'success' => true,
            'typepaymts' => $settings['typepaymts']
        ]);
        wp_die();
    }
}

new Pixapay();