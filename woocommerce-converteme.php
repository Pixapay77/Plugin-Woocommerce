<?php
    /*
    Plugin Name: Woocommerce Converteme
    Plugin URI: https://converte.me/plugin/converteme-checkout
    Description: Descricao provisoria Converteme Checkout.
    Author: Converte
    Version: 1.0.0
    Author URI:
    */


    if (!in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
        add_action( 'admin_notices', function(){
            printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( 'notice notice-error' ), 'Converteme necessita do <a href="http://0.0.0.0:8005/wp-admin/plugin-install.php?tab=plugin-information&amp;plugin=woocommerce&amp;TB_iframe=true&amp;width=772&amp;height=378" class="thickbox open-plugin-details-modal" aria-label="Mais informações sobre WooCommerce1" data-title="WooCommerce"><b>Woocommerce</b></a> para seu funcionamento.' );
        } );
    }elseif (!in_array('woocommerce-correios/woocommerce-correios.php', apply_filters('active_plugins', get_option('active_plugins')))) {
        add_action( 'admin_notices', function(){
            printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( 'notice notice-error' ), 'Converteme necessita do <a href="http://0.0.0.0:8005/wp-admin/plugin-install.php?tab=plugin-information&amp;plugin=woocommerce-correios&amp;TB_iframe=true&amp;width=772&amp;height=378" class="thickbox open-plugin-details-modal" aria-label="Mais informações sobre Claudio Sanches – Correios for WooCommerce" data-title="Claudio Sanches – Correios for WooCommerce"><b>Claudio Sanches - Correios for WooCommerce</b></a> para seu funcionamento.');
        } );
    }else{
        include_once __DIR__ . "/Init.php";
    }
