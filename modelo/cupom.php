<?php if ( wc_coupons_enabled() ) { ?>
<div class="forms coupon-column summary-coupon-wrap text-center">
    <div class="coupon summary-coupon">
        <fieldset class="coupon-fieldset">
            <div id="cupon-form" style="display: none;">

                <form class="checkout_coupon mb-0" method="post">
                    <div class="coupon">
                        <h3 class="widget-title"><?php echo get_flatsome_icon( 'icon-tag' ); ?>
                            <?php esc_html_e( 'Coupon', 'woocommerce' ); ?></h3>
                        <input type="text" name="coupon_code" class="input-text" id="coupon_code"
                            value=""
                            placeholder="<?php esc_attr_e( 'Coupon code', 'woocommerce' ); ?>" 
                            style="width: 100%;height: 33px;margin: 7px 0px;"
                            />
                        <input type="submit" class="is-form expand" name="apply_coupon"
                            value="<?php esc_attr_e( 'Apply coupon', 'woocommerce' ); ?>" 
                            style="margin-bottom: 10px;width: 100%;display: table;border-radius: 5px;height: 40px;padding: 0 10px;line-height: 40px;display: inline-block;background-color: #43bfe1;border: none;color: #fff;border-radius: 4px;text-align: center;"
                            />
                        <?php do_action( 'woocommerce_cart_coupon' ); ?>
                    </div>
                </form>
            </div>
            <p class="coupon-data"
                style="margin-bottom:10px;width: 100%;display: table;border-radius: 5px;height: 40px;padding: 0 10px;line-height: 40px;display: inline-block;background-color: #5333ed;border: none;color: #fff;border-radius: 4px;text-align: center;">
                <a class="link-coupon-add" href="#" id="cart-link-coupon-add">
                    <span>Adicionar</span>
                    <span>cupom de desconto</span>
                </a>
            </p>
        </fieldset>
    </div>
</div>

<?php } ?>