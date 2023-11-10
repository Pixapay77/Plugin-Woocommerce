
window.onload = function() {
    get_methodsSelected();
  };
  

function get_installments(param)
{
        var dados = {
            'action' : "installments"
        };

        jQuery.post({
            url: ajax_object.ajaxurl,
            dataType: "json",
            data: dados,
            success: function (response){
                // console.log(response);

                var total = parseInt(response.installments);
                var option = '';
                for (let i = 1; i <= total; i++) {
                    option += '<option value="'+i+'">'+i+'x</option>';
                }

                jQuery(param).html(option);
            }
        });
}

function get_methodsSelected()
{
        var dados = {
            'action' : "methodsSelected"
        };

        
        jQuery.post({
            url: ajax_object.ajaxurl,
            dataType: "json",
            data: dados,
            success: function (response){
                jQuery.each(response.typepaymts,function(index,value){
                    var type = get_type(value);

                    if(jQuery.inArray(type,['pix','boleto','cred_card']) != -1){
                        jQuery.get(window.location.origin + '/wp-content/plugins/woocommerce-pixapay/public/template/'+type+'.html', function(Html) {
                            jQuery('.payment_box.payment_method_pixapay #woocommercerConverteme').append(Html)
                        });
                    }
                })
            }
        });
}

function get_type(param){
    switch (param) {
        case 'pix':
            return 'pix';
        break;
        case 'boleto':
            return 'boleto';
        break;
        case 'creditcart':
            return 'cred_card';
        break;
    }
}

function open_cred_card(a)
{
    jQuery('.content').hide();
    jQuery('fieldset.cred_card').removeAttr('style');
    jQuery('fieldset.cred_card').attr('style','margin:22px 0 !important');
    jQuery('.content.cred_card').toggle();
    jQuery('input[name="payment_type"]').val('credit_card_pixapay');
}

function open_pix(a)
{
    jQuery('.content').hide();
    jQuery('fieldset.pix').removeAttr('style');
    jQuery('fieldset.pix').attr('style','margin:22px 0 !important');
    jQuery('.content.pix').toggle();
    jQuery('input[name="payment_type"]').val('pix_pixapay');
}

function open_boleto(a)
{
    jQuery('.content').hide();
    jQuery('fieldset.boleto').removeAttr('style');
    jQuery('fieldset.boleto').attr('style','margin:22px 0 !important');
    jQuery('.content.boleto').toggle();
    jQuery('input[name="payment_type"]').val('boleto_pixapay');
}

function copy()
{
    var input = jQuery('input[name="copiecole"]');
    input.select(); // Seleciona o texto no input
    document.execCommand("copy"); // Copia o texto selecionado para a área de transferência
    window.getSelection().removeAllRanges(); // Deseleciona o texto
    jQuery(this).text("Texto Copiado!");
}