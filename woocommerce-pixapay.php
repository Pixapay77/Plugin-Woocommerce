<?php
    /*
    Plugin Name: Woocommerce Pixapay
    Plugin URI: https://pixapay.com.br/
    Description: Plugin de pagameto para transações de woocommerce para Pixapay.
    Author: SEO Midia soluções
    Version: 1.0.0
    Author URI: http://seomidia.com.br/
    */


    if (!in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
        add_action( 'admin_notices', function(){
            printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( 'notice notice-error' ), 'Pixapay necessita do <a href="/wp-admin/plugin-install.php?tab=plugin-information&amp;plugin=woocommerce&amp;TB_iframe=true&amp;width=772&amp;height=378" class="thickbox open-plugin-details-modal" aria-label="Mais informações sobre WooCommerce1" data-title="WooCommerce"><b>Woocommerce</b></a> para seu funcionamento.' );
        } );
    }else{
        include_once __DIR__ . "/Init.php";
    }
