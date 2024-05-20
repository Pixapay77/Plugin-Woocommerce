<?php
    /*
    Plugin Name: Woocommerce Pixapay
    Plugin URI: https://pixapay.com.br/
    Description: Plugin de pagameto para transações de woocommerce para Pixapay.
    Author: SEO Midia soluções
    Version: 1.0.5
    Author URI: http://seomidia.com.br/
    */


    if (!in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
        add_action( 'admin_notices', function(){
            printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( 'notice notice-error' ), 'Pixapay necessita do <a href="/wp-admin/plugin-install.php?tab=plugin-information&amp;plugin=woocommerce&amp;TB_iframe=true&amp;width=772&amp;height=378" class="thickbox open-plugin-details-modal" aria-label="Mais informações sobre WooCommerce1" data-title="WooCommerce"><b>Woocommerce</b></a> para seu funcionamento.' );
        } );
    }else{
        require_once ABSPATH ."/wp-load.php"; // Carregas funçõ nativas do wordpress
        require_once WP_PLUGIN_DIR .'/woocommerce/woocommerce.php'; // Carregas funçõ nativas do woocommerce
        require_once __DIR__ . '/includes/scripts.php'; // incluir scripts css js no front do chekout
        require_once __DIR__ . '/includes/ajax.php'; // executa Ajax

        define('path_plugin',ABSPATH . 'wp-content/plugins/woocommerce-plugin/'); // Path absuloto do plugin

        include_once __DIR__ . "/Init.php"; // Gateway

        add_filter( 'woocommerce_payment_gateways', 'WC_Pixapay_add_gateway_class' );
        function WC_Pixapay_add_gateway_class( $gateways ) {
            $gateways[] = 'WC_Pixapay_Gateway'; // your class name is here
            return $gateways;
        }

        add_action('wp_enqueue_scripts', 'woocommerce_pixapay_style');
        function woocommerce_pixapay_style()
        {
            wp_enqueue_style('woocommerce_converteme_style',plugin_dir_url(__FILE__) . 'public/assets/css/woocommerce_converteme_style.css',[],false,false);
            wp_enqueue_script('woocommerce_converteme_script_imask','https://cdnjs.cloudflare.com/ajax/libs/imask/3.4.0/imask.min.js',['jquery'],false,false);
            wp_enqueue_script('woocommerce_converteme_script_mercadopago','https://sdk.mercadopago.com/js/v2',['jquery'],false,false);
            wp_enqueue_script('woocommerce_converteme_script',plugin_dir_url(__FILE__) . 'public/assets/js/woocommerce_converteme_script.js',['jquery'],false,false);
        }


    }