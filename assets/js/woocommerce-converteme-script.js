jQuery(document).ready(function(){

    jQuery('.has-dropdown a.header-cart-link').attr('href',window.location.origin + '/carrinho');
    jQuery('.has-dropdown a.header-cart-link').mouseover(function() {
        jQuery('li.has-dropdown ul.nav-dropdown p.woocommerce-mini-cart__buttons.buttons a.button.wc-forward').attr('href',window.location.origin + '/carrinho');
    });
    jQuery('.sticky-add-to-cart button[type="submit"]').click(function(){
        setTimeout(function(){
            window.location.href = window.location.origin + '/carrinho';
        },2000)
      })
    jQuery('a.quick-view').click(function(){
        setTimeout(function(){
            quickview();
        },2000);
    });     

    function quickview(){
        jQuery('button.single_add_to_cart_button').click(function(){
            setTimeout(function(){
                window.location.href = window.location.origin + '/carrinho';
            },2000)
        })
    }
})




