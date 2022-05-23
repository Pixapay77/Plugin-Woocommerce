function validacaoEmail(field) {
    usuario = field.substring(0, field.indexOf("@"));
    dominio = field.substring(field.indexOf("@")+ 1, field.length);
    
    if ((usuario.length >=1) &&
        (dominio.length >=3) &&
        (usuario.search("@")==-1) &&
        (dominio.search("@")==-1) &&
        (usuario.search(" ")==-1) &&
        (dominio.search(" ")==-1) &&
        (dominio.search(".")!=-1) &&
        (dominio.indexOf(".") >=1)&&
        (dominio.lastIndexOf(".") < dominio.length - 1)) {
        return true;
    }else{
        return false;
    }
}
function limpa_formulário_cep() {
        // Limpa valores do formulário de cep.
        jQuery("#billing_address_1").val("");
        jQuery("#billing_neighborhood").val("");
        jQuery("#billing_city").val("");
        jQuery("#billing_state").val("");
    }
function loadshipping(){
    jQuery('.shippingg input[type="radio"]').click(function(){
        var id = jQuery(this).attr('id');
        setTimeout(function(){

            if(localStorage['entraga'] == undefined)
                jQuery('.shippingg a#pagamento').show();

            jQuery('.shippingg label').removeAttr('style');
            jQuery('.shippingg label').removeAttr('id');
            jQuery('#' + id).prop("checked", true); 
            jQuery('label[for="'+id+'"]').css({'font-weight': 'bolder','color': '#254ce8'});
            jQuery('label[for="'+id+'"]').attr('id','entrega-selecionada');

    
            if(Number.isInteger(id)){
            var entrega = jQuery('label[for="'+id+'"]').text();
            entrega = entrega.split(':')[1];
            entrega = entrega.replace('R$','');

            jQuery('.resumo table.shop_table tfoot tr.entrega').remove();
            jQuery('.resumo td#shippingvalue').prepend(entrega);
            }else{
                jQuery('.resumo td#shippingvalue').prepend('00,00');
            }
            localStorage['entraga'] = 'label[for="'+id+'"]';
            },500);

  });

}
function nextBlock(next){
    if(checkinput(next)) {
        jQuery('#' + next.dataset.prev + ' .content-none').hide();
        if(next.dataset.next != ''){
            jQuery('#' + next.dataset.next + ' .content-none').show();
            if(next.dataset.next == 'payment'){
                jQuery('.optionfrete').hide();
                jQuery('#shipping a.next').remove();
                jQuery('.shipping .resumo-box').removeClass('active');
                jQuery('.seomidia_table').removeClass('active');
                jQuery('.payment .seomidia_table').addClass('active');
            }
        }else if(next.dataset.next == 'shipping'){
            jQuery('.optionfrete').show();
        }
    }
}
function checkinput(next){
     var box = next.dataset.prev;

    jQuery('span.error').hide();
    jQuery('input').removeClass('error');


    if(box == 'billing'){
         var inputs = jQuery('#'+box + ' .billing input');
         var total  = inputs.length;

         jQuery('#billing .billing').removeClass('active');
         jQuery('#shipping').addClass('active');



         var id;

        for(var i=0;i<total;i++){
             id = jQuery(inputs[i]).attr('id');
            jQuery('#' + id).removeClass('error');
            jQuery('#' + id).show();

            if(inputs[i].value == ''){
                 jQuery('.' + id + ' .error').show();
                 jQuery('input#' + id).addClass('error');
                 return false;
             }

            if(id == 'nome-completo'){
                jQuery('h3#nome').html(inputs[i].value);
                jQuery('input#billing_first_name').val(inputs[i].value);
            }

            if(id == 'sobrenome'){
                jQuery('h3#nome').append(' ' + inputs[i].value);
                jQuery('input#billing_last_name').val(inputs[i].value);
            }

            if(id == 'cpf'){
                jQuery('p#cpf').html(inputs[i].value);
                jQuery('input#billing_cpf').val(inputs[i].value);
            }
            if(id == 'whatsapp-celular'){
                jQuery('p#celular').html(inputs[i].value);
                jQuery('input#billing_phone').val(inputs[i].value);
            }
            if(id == 'email'){
                jQuery('p#email').html(inputs[i].value);
                jQuery('input#billing_email').val(inputs[i].value);
            }
        }
         jQuery('.billing .resumo-box').show();

     }else if(box == 'shipping'){
         var inputs = jQuery('#'+box + '.shipping input');
         var total  = inputs.length;
         var inputsErros = 0;

         for(var i=0;i<total;i++){
             id = jQuery(inputs[i]).attr('id');
             jQuery('#' + id).removeClass('error');
             jQuery('#' + id).show();
             if(inputs[i].value == '' && jQuery.inArray(id,['cep','city','state','address','numero','neighborhood']) != -1){
                 jQuery('.' + id + ' span.error').show();
                 jQuery('input#' + id).addClass('error');
                 inputsErros++;
                 return false;
             }
             if(id == 'address'){
                 jQuery('h3#ruanumero').html(inputs[i].value+', ' + inputs[i+1].value);
                 jQuery('input#billing_address_1').val(inputs[i].value);
                 jQuery('input#billing_number').val(inputs[i+1].value);
             }

             if(id == 'neighborhood'){
                 var complemento = '';

                 if( inputs[i + 1].value != '') complemento = '-' + inputs[i + 1].value
                 jQuery('p#bairroComplemento').html(inputs[i].value+complemento);
                 jQuery('input#billing_address_2').val(inputs[i + 1].value);
                 jQuery('input#billing_neighborhood').val(inputs[i].value);
             }
             if(id == 'city'){
                 console.log(inputs);
                 jQuery('p#cidadeUFcep').html(inputs[i].value+'-'+inputs[i+1].value+' | CEP '+inputs[4].value);
                 jQuery('select#billing_country').val('BR');
                 jQuery('select#billing_state option[value="'+inputs[i+1].value+'"]').prop("selected", true).change()
                 jQuery('input#billing_city').val(inputs[i].value);
             }
             if(id == 'cep'){
                 jQuery('input#billing_postcode').val(inputs[i].value);
             }
         }
        if(inputsErros == 0)
            jQuery('.shipping .optionfrete').show();

         jQuery('.shipping .resumo-box').show();
         jQuery('.shipping .resumo-box').addClass('active');

     }

     return true;
}
function deleteprod(prod){
    Swal.fire({
        title: 'Atenção',
        text: "Deseja realmente, deletar este produto?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sim',
        cancelButtonText: 'Não'
    }).then((result) => {
        if (result.isConfirmed) {
            var href = jQuery(prod).attr('href');

            var settings = {
                'action' : 'delete_prod',
                'prod_id': href
            }

            jQuery.post({
                url: ajax_object.ajax_url,
                dataType: "json",
                data: settings,
                success: function (response){

                    jQuery('body').trigger('update_checkout');

                    setTimeout(function (){

                    var back = jQuery('a.wc-backward');
                    if(back.length >= 1){
                        var href = jQuery(back).attr('href');
                        window.location.href = href
                    }

                    },1000);

                }
            });
        }
    })

}
jQuery(document).ready(function(){

    var billing = jQuery("#billing");

    var html = jQuery('.optionfrete').html()
    html  = html.replace('Entrega','');
    jQuery('.optionfrete').html(html)


    if(billing.length >= 1){
        jQuery('#billing .billing .content-none').show();
    }

    jQuery('#resumo-pedidos .shop_table tr.woocommerce-shipping-totals').remove();

    jQuery(document.body).on('updated_checkout', function(){
        jQuery('#resumo-pedidos .shop_table tr.woocommerce-shipping-totals').remove();
        jQuery('#resumo-pedidos .shop_table thead tr.woocommerce-shipping-totals').remove();

        jQuery('input.inputcupon').change(function(event){
            var value = jQuery('input.inputcupon').val();
            jQuery('input[name="coupon_code"]').val(value);
        })

    });

    jQuery('.billing .resumo-box').click(function(event){
        event.preventDefault();
        jQuery('.seomidia_table').removeClass('active');
        jQuery('#billing .billing').addClass('active');
        jQuery('.billing .resumo-box').hide();
        jQuery('#billing .billing .content-none').show();
        // jQuery('#shipping').removeClass('my-3');
        // jQuery('#shipping').removeClass('my-md-5');
        // jQuery('#shipping').addClass('my-5');
        // jQuery('#shipping').addClass('my-md-3');
        jQuery('#shipping .content-none').hide();
        jQuery('.shipping .resumo-box').hide();
        jQuery('.shipping .optionfrete').hide();
        jQuery('#shipping .content-none a.next').remove();
        jQuery('#shipping.shipping .content-none').append('<a href="#" data-next="" data-prev="shipping" onclick="nextBlock(this)" class="btn next poscep">CONTINUAR</a>')
    });

    jQuery('.shipping .resumo-box').click(function(event){
        event.preventDefault();
        jQuery('.seomidia_table').removeClass('active');
        jQuery('#shipping').addClass('active');

        jQuery('.shipping .resumo-box').hide();
        jQuery('.shipping .optionfrete').hide();
        jQuery('.shipping .content-none').show();
        jQuery('#shipping.shipping .content-none a.next').remove();
        jQuery('#shipping.shipping').append('<a href="#" data-next="payment" data-prev="shipping" onclick="nextBlock(this)" class="btn next poscep">CONTINUAR</a>')

    });



    jQuery('ul#shipping_method li').click(function(){
        jQuery('ul#shipping_method li').removeClass('active');
        jQuery(this).addClass('active');
        jQuery('#shipping a.next').remove();
        jQuery('#shipping.shipping').append('<a href="#" data-next="payment" data-prev="shipping" onclick="nextBlock(this)" class="btn next poscep">CONTINUAR</a>')
    });


    jQuery("#cep").blur(function() {
        //Nova variável "cep" somente com dígitos.
        var cep = jQuery(this).val().replace(/\D/g, '');

        //Verifica se campo cep possui valor informado.
        if (cep != "") {

            //Expressão regular para validar o CEP.
            var validacep = /^[0-9]{8}$/;

            //Valida o formato do CEP.
            if(validacep.test(cep)) {

                //Preenche os campos com "..." enquanto consulta webservice.
                jQuery("#billing_address_1").val("...");
                jQuery("#billing_neighborhood").val("...");
                jQuery("#billing_city").val("...");
                jQuery("#billing_state").val("...");
                jQuery("#billing_state").val("");


                //Consulta o webservice viacep.com.br/
                jQuery.getJSON("https://viacep.com.br/ws/"+ cep +"/json/?callback=?", function(dados) {

                    if (!("erro" in dados)) {
                        jQuery('.poscep').show();
                        //Atualiza os campos com os valores da consulta.
                        jQuery("#address").val(dados.logradouro);
                        jQuery("#neighborhood").val(dados.bairro);
                        jQuery("#city").val(dados.localidade);
                        jQuery("#state").val(dados.uf);
                        jQuery("#cityState").html(dados.localidade +'/'+dados.uf);


                    } //end if.
                    else {
                        //CEP pesquisado não foi encontrado.
                        limpa_formulário_cep();
                        Swal.fire(
                            'Atenção',
                            "CEP não encontrado.",
                            'warning'
                        )
                
                    }
                });
            } //end if.
            else {
                //cep é inválido.
                limpa_formulário_cep();
                Swal.fire(
                    'Atenção',
                    "Formato de CEP inválido.",
                    'warning'
                )

            }
        } //end if.
        else {
            //cep sem valor, limpa formulário.
            limpa_formulário_cep();
        }
    })
    jQuery('#quantity_6227622e840a2').change(function(){
        jQuery('input[name="update_cart"]').trigger('click');

    })
    jQuery('#cart th.product-remove,th.product-thumbnail').remove();
    jQuery('#cart th.product-name').attr('colspan','3');
    jQuery('.cart-collaterals').addClass('seomidia_table');


    //checkoiut 


    var intervalId = window.setInterval(function(){
        jQuery('.woocommerce-mini-cart__buttons buttons a').attr('href',window.location.origin + '/carrinho');
        jQuery('.wc-proceed-to-checkout a.btn').remove();
        jQuery('.wc-proceed-to-checkout').prepend('<a class="btn btn-link" href="'+window.location.origin+'/loja" style="padding: 0px;"><i class="fa fa-arrow-left" style="padding: 0px 9px 0px 0px;"></i>Voltar a loja</a>');
        jQuery('.cart-collaterals .checkout-button').attr('href','#');
        jQuery('.cart-collaterals a.checkout-button').click(function(event){
            event.preventDefault();
            var dados = {
               'action' : "check_Session",
           };
   
           jQuery.post({
               url: ajax_object.ajax_url,
               dataType: "json",
               data: dados,
               success: function (response){
                   if(response.success ){
                       window.location.href = window.location.origin + "/finalizar-compra";
                   }else{
                       jQuery('.carrinho').hide();
                       jQuery('.auth').show();
                   }
               }
           });
   
        });
    },1000);



    if(window.location.pathname == '/finalizar-compra/'){
        jQuery("#billing_cpf").mask("999.999.999-99");
        jQuery("#billing_postcode").mask("99999-999");
        jQuery("#billing_phone").mask("(99) 9 9999-9999");

        if(localStorage['entraga'] != undefined){
            jQuery('.resumo table.shop_table tfoot').prepend('<tr class="entrega"><th colspan="2">Entrega</th><td id="shippingvalue"></td></tr>');
            jQuery('.shippingg a#pagamento').hide();
        }


        jQuery('#billing_postcode').change(function(){
            jQuery('body').trigger('update_checkout');

            setTimeout(function(){
                jQuery('#shipping').load( window.location.origin +  window.location.pathname + ' #shipping');
            },3000); 
            setTimeout(function(){
                get_endereco_cep();
                loadshipping();
            },4000);        
       
        });


        var largura = window.screen.width;
        if(largura <= 560){

            jQuery('a.checkout-button').click(function(){
                swal.fire({
                    title: 'Aguarde...'
                });        
            });
            jQuery('#billing_cpf').keyup(function(){
                var totalcpf;
                totalcpf = this.value.replace(/\D/g, '').length;
                if(totalcpf == 11){
                    console.log('tal');
                    jQuery('#tab1 h4').css('color','#09712d');
                    jQuery('#tab1 h4 i.fa.fa-edit').remove();
                    jQuery('#tab1 h4').append('<i class="fa fa-edit" aria-hidden="true"  style="float: right;"></i>');
                    jQuery('#tab1 h4 i.fa.fa-check').remove();
                    jQuery('#tab1 h4').prepend('<i class="fa fa-check" aria-hidden="true"></i>');
                
                    jQuery('#tab1 .content').hide();
                    jQuery('#tab2 .content').show();
                    jQuery('#tab3 .content').show();
                }
            })

        }



    jQuery('.shippingg a#pagamento').click(function(event){
        event.preventDefault();
        jQuery('.shippingg-box .payment').css('display','block');
        jQuery(this).hide();
    })

jQuery('.resumo a.finalizar-compra').click(function(event){
    event.preventDefault();
    var entrega = jQuery('.shipping__table--multiple ul li input[type="radio"]:checked').length;
    var opcaopg = jQuery('.payment_methods input[name="juno-credit-card-card-info"]').length;
    
    if(entrega == 0){
        Swal.fire({
            title: 'Atenção',
            text: "Informe uma opção de frete!",
            icon: 'warning',
            confirmButtonColor: '#3085d6',
            confirmButtonText: 'OK!',
          });
          return false;
    }

    if(opcaopg == 1){
        var check = jQuery('.payment_methods input[name="juno-credit-card-card-info"]:checked').length;
        if(check == 0){
            Swal.fire({
                title: 'Atenção',
                text: "Selecione sua compra com cartão de crédito!",
                icon: 'warning',
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'OK!',
              });
              return false;
            }
    }


    if(jQuery('.payment #payment ul input[name="payment_method"]:checked')[0].value == 'juno-credit-card'){
        
    var input = jQuery('.wc-payment-form-fields input[type="text"],.wc-payment-form-fields input[type="tel"]');
    var total = input.length;

    for(var i = 0;i < total;i++){
    if(input[i].value != ''){
        jQuery('.payment #payment button').trigger('click');
    }else{
        Swal.fire({
            title: 'Atenção',
            text: "Verifique os dados do pagamento!",
            icon: 'warning',
            confirmButtonColor: '#3085d6',
            confirmButtonText: 'OK!',
          })
   
    }
    }

}else{
    jQuery('.payment #payment button').trigger('click');

}
})

        var tab1 = jQuery('#tab1 input');
tab1.change(function(){
var tab1total = tab1.length;  
var value = 0;
for(var i = 0;i < tab1total;i++){
  if(tab1[i].value != '')
     value++;
}
  if(tab1total == value){
    jQuery('#tab1 h4').css('color','#09712d');
    jQuery('#tab1 h4 i.fa.fa-edit').remove();
    jQuery('#tab1 h4').append('<i class="fa fa-edit" aria-hidden="true"  style="float: right;"></i>');
    jQuery('#tab1 h4 i.fa.fa-check').remove();
    jQuery('#tab1 h4').prepend('<i class="fa fa-check" aria-hidden="true"></i>');
    jQuery('#tab2 h4').trigger('click');

  }
})

var tab2 = jQuery('#tab2 #billing_number');
tab2.change(function(){
    Swal.fire({
        title: 'Atenção',
        text: "Possue complemento?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sim tenho!',
        cancelButtonText: 'Não tenho'
      }).then((result) => {
        if (!result.isConfirmed) {
            jQuery('#tab2 h4').css('color','#09712d');
            jQuery('#tab2 h4 i.fa.fa-edit').remove();
            jQuery('#tab2 h4').append('<i class="fa fa-edit" aria-hidden="true"  style="float: right;"></i>');
            jQuery('#tab2 h4 i.fa.fa-check').remove();
            jQuery('#tab2 h4').prepend('<i class="fa fa-check" aria-hidden="true"></i>');
            jQuery('#tab3 h4').trigger('click');
        }
    })
})

var tab2 = jQuery('#tab3 #account_password');
tab2.change(function(){
    Swal.fire({
        title: 'Atenção',
        text: "Agora selecione a entrega!",
        icon: 'warning',
        confirmButtonColor: '#3085d6',
        confirmButtonText: 'OK!',
      })
})

jQuery('#billing_address_2').change(function(){
    jQuery('#tab2 h4').css('color','#09712d');
    jQuery('#tab2 h4 i.fa.fa-edit').remove();
    jQuery('#tab2 h4').append('<i class="fa fa-edit" aria-hidden="true"  style="float: right;"></i>');
    jQuery('#tab2 h4 i.fa.fa-check').remove();
    jQuery('#tab2 h4').prepend('<i class="fa fa-check" aria-hidden="true"></i>');
    jQuery('#tab3 h4').trigger('click');
})


var tab3 = jQuery('#tab3 input');
tab3.change(function(){
var tab3total = tab3.length;  
var value = 0;
for(var i = 0;i < tab3total;i++){
  if(tab3[i].value != '')
     value++;
}
  if(tab3total == value){
    jQuery('#tab3 h4').css('color','#09712d');
    jQuery('#tab3 h4 i.fa.fa-edit').remove();
    jQuery('#tab3 h4').append('<i class="fa fa-edit" aria-hidden="true"  style="float: right;"></i>');
    jQuery('#tab3 h4 i.fa.fa-check').remove();
    jQuery('#tab3 h4').prepend('<i class="fa fa-check" aria-hidden="true"></i>');
    jQuery('#tab1 h4').trigger('click');

  }
})



    jQuery('#billing_postcode_field').removeAttr('class');
    // jQuery('.shippingg').addClass('seomidia_table');
    jQuery('.shippingg').css({
        'margin-bottom': '8px'
    });
    

    jQuery('.payment').addClass('seomidia_table');
    jQuery('#billing_phone_field').removeClass('form-row-first');
    jQuery('#billing_phone_field').addClass('form-row-wide');
    jQuery('.woocommerce-billing-fields__field-wrapper').prepend('<p class="text-checkout-aviso">Solicitamos apenas as informações essenciais para a realização da compra.</p>');
    jQuery('.shippingg a#pagamento').click(function(){
        jQuery('.shippingg .payment').show();
    });

    loadshipping();



  
    var intervalId = window.setInterval(function(){
        var entrega;
        var entregatext;

        jQuery('.wc-proceed-to-checkout').prepend('<a class="btn btn-link" href="'+window.location.origin+'/loja" style="padding: 0px;"><i class="fa fa-arrow-left" style="padding: 0px 9px 0px 0px;"></i>Voltar a loja</a>');
        jQuery('.cart-collaterals .checkout-button').attr('href','#');

        loadshipping();

        if(localStorage['entraga'] != undefined){
            classe = '.shippingg ' + localStorage['entraga'];
            jQuery(classe).css({'font-weight': 'bolder','color': '#254ce8'});
            jQuery(classe).attr('id','entrega-selecionada');
            var idinput = jQuery(classe).attr('for');
            jQuery('#'+idinput).prop('checked',true);
            jQuery('.shippingg-box .payment ').show();
            jQuery('.resumo a.finalizar-compra').show();


        }else{
            classe = '#entrega-selecionada';
        }
        entrega = jQuery(classe).text();
        valor = jQuery(classe).val();

        if(entrega != '' ){
                entrega = entrega.split(':')[1];

                if(entrega == undefined){
                    entrega = '0,00'
                }else if(entrega == '00/12'){
                    entrega = '0,00'
                }

                entrega = entrega.replace('R$','');

            jQuery('.resumo table.shop_table tfoot tr.entrega').remove();
            jQuery('.resumo table.shop_table tfoot').prepend('<tr class="entrega"><th colspan="2">Entrega</th><td><span class="woocommerce-Price-amount amount"><bdi><span class="woocommerce-Price-currencySymbol">R$</span>'+entrega+'</bdi></span></td></tr>');
            }
 

    
        jQuery('.resumo #payment').remove();
        jQuery('.resumo td.shipping__inner').remove();

        jQuery('.shippingg th.product-name').remove();
        jQuery('.shippingg th.product-total').remove();
        jQuery('.shippingg tr.cart_item').remove();
        jQuery('.shippingg tr.cart-subtotal').remove();
        jQuery('.shippingg tr.order-total').remove();
        jQuery('.shippingg tr.fee').remove();
        // jQuery('.shippingg .wc_payment_methods').remove();
        jQuery('.shippingg #payment').remove();
        jQuery('.shippingg tr.cupom').remove();

        jQuery('.payment th.product-name').remove();
        jQuery('.payment th.product-total').remove();
        jQuery('.payment tr.cart_item').remove();
        jQuery('.payment tr.cart-subtotal').remove();
        jQuery('.payment tr.order-total').remove();
        jQuery('.payment tr.fee').remove();
        jQuery('.payment td.shipping__inner').remove();
        jQuery('.payment table.woocommerce-checkout-review-order-table').remove();
        jQuery('#payment').removeClass('woocommerce-checkout-payment');

        jQuery('.quantity input[type="button"]').on('click',function(){
            setTimeout(function(){
                jQuery('button[name="update_cart"]').trigger('click');
            },1000)
        })
        

    },1000);


    jQuery('#tab1 h4').click(function(){
        jQuery('.content').hide();
        jQuery('#tab1 .content').toggle();
      })
      jQuery('#tab2 h4').click(function(){
        jQuery('.content').hide();
       jQuery('#tab2 .content').toggle()
     })
     jQuery('#tab3 h4').click(function(){
        jQuery('.content').hide();
       jQuery('#tab3 .content').toggle()
     })

     setTimeout(function(){
        jQuery('#billing_postcode_field').removeAttr('class');
        jQuery('#billing_postcode_field').addClass('form-row-first');
        jQuery('#billing_phone_field').removeAttr('class');
        jQuery('#billing_phone_field').addClass('form-row-last');
   
        jQuery('#billing_address_1_field').removeAttr('class');
        jQuery('#billing_address_1_field').addClass('form-row-first');
        jQuery('#billing_number_field').removeAttr('class');
        jQuery('#billing_number_field').addClass('form-row-last');

        jQuery('#billing_address_2_field').removeAttr('class');
        jQuery('#billing_address_2_field').addClass('form-row-first');
        jQuery('#billing_neighborhood_field').removeAttr('class');
        jQuery('#billing_neighborhood_field').addClass('form-row-last');

        jQuery('#billing_city_field').removeAttr('class');
        jQuery('#billing_city_field').addClass('form-row-first');
        jQuery('#billing_state_field').removeAttr('class');
        jQuery('#billing_state_field').addClass('form-row-last');
        jQuery('#billing_country_field').hide();
        },1000)
    }
     jQuery('.wc-proceed-to-checkout').prepend('<a class="btn btn-link" href="'+window.location.origin+'/loja" style="padding: 0px;"><i class="fa fa-arrow-left" style="padding: 0px 9px 0px 0px;"></i>Voltar a loja</a>');

     jQuery('.cart-collaterals .checkout-button').attr('href','#');

     jQuery('.cart-collaterals a.checkout-button').click(function(event){
         event.preventDefault();
         var dados = {
            'action' : "check_Session",
        };
        var largura = window.screen.width;
        if(largura <= 560){

                swal.fire({
            title: 'Aguarde...'
        });
        swal.showLoading();}


        jQuery.post({
            url: ajax_object.ajax_url,
            dataType: "json",
            data: dados,
            success: function (response){
                if(response.success ){
                    window.location.href = window.location.origin + "/finalizar-compra";
                }else{
                    jQuery('.carrinho').hide();
                    var largura = window.screen.width;
                    if(largura <= 560){
            
                                swal.close();
                    }

                    jQuery('.auth').show();
                    jQuery('#emailInfo').show();
                }
            }
        });

     });

     jQuery('#search-email').click(function(event){
        event.preventDefault();

        swal.fire({
            title: 'Aguarde...'
        });
        
        swal.showLoading();
        var email = jQuery('.busca-email input[name="email"]').val();
        if(email == ''){
            swal.close();
            Swal.fire(
                'Atenção',
                'Informe seu e-mail!',
                'warning'
            );
        }else if(!validacaoEmail(email)){
            swal.close();
            Swal.fire(
                'Atenção',
                'E-mail inválido!',
                'warning'
            );
        }else{

            var dados = {
                'action' : "search_email",
                'email'  : email
            };

            jQuery.post({
                url: ajax_object.ajax_url,
                dataType: "json",
                data: dados,
                success: function (response){
                    swal.close();

                    if(response == null){
                        console.log('usuario nao esta cadastrado');
                        window.location.href = window.location.origin + "/finalizar-compra?email=" + email
                    }else{
                        jQuery('.busca-email').hide();
                        jQuery('#emailInfo').hide();
                        jQuery('.auth-senha').show();
                        swal.close();

                    }
                }
            });
        }

     });

     if(window.location.search.split('=')[0].replace('?','') != ''){
         var email = window.location.search.split('=')[1];
         jQuery('#billing_email').val(email);
     }


     jQuery('form[name="authUser"]').submit(function(event){
        event.preventDefault();
        var user = jQuery('.busca-email input[name="email"]').val();
        var pass = jQuery('.auth-senha input[name="senha"]').val();
        swal.fire({
            title: 'Aguarde...'
        });
        swal.showLoading();

        if(user == ''){
            swal.close();

            Swal.fire(
                'Atenção',
                'Informe seu e-mail!',
                'warning'
            );

        }else if(pass == ''){
            swal.close();

            Swal.fire(
                'Atenção',
                'Informe sua senha!',
                'warning'
            );

        }else{

        var dados = {
            'action' : "auth_customer",
            'user'   : user,
            'password'  : pass
        };

        jQuery.post({
            url: ajax_object.ajax_url,
            dataType: "json",
            data: dados,
            success: function (response){
                swal.close();

                if(response.success){
                    window.location.href = window.location.origin + "/finalizar-compra"
                    localStorage['data_User'] = response.data
                }else{
                    jQuery('.auth-senha p').show();
                    jQuery('.auth-senha p').html('<strong>Erro</strong>: A senha fornecida está incorreta. <a href="'+window.location.origin+'/minha-conta/lost-password/">Perdeu a senha?</a>');
                }
            }
        });
    }
     });





})

