<?php

class WC_CONVERTEME_Init
{

    public static function woocommerce_converteme_init()
    {
        include_once WC_CONVERTEME_PATH . '/partals/partials.php';
        include_once WC_CONVERTEME_PATH . '/includes/scripts.php';
        include_once WC_CONVERTEME_PATH . '/includes/gateway.php';
//        include_once WC_CONVERTEME_PATH . '/includes/PathTemplate.php';
//        include_once WC_CONVERTEME_PATH . '/includes/hooks.php';
    }
}