<div class="row-fluid orderform-template span12 inactive">
    <div class="orderform-template-holder span8">
        <div class="row-fluid">
            <div id="client-profile-data" class="span6 client-profile-data">
                <form name="checkUser" class="form-page client-pre-email anim-death anim-current" autocomplete="on"
                    style="display: block;">
                    <p class="link link-cart pull-right">
                        <small><a id="orderform-to-cart" target="_self" href="/finalizar-compra">Voltar para o
                                carrinho</a></small>
                    </p>
                    <fieldset class="pre-email row-fluid">
                        <h3 class="client-pre-email-h">
                            <label for="client-pre-email"><span>Para finalizar a compra, informe seu
                                    e-mail.</span>
                                <small>Rápido. Fácil. Seguro.</small></label>
                        </h3>
                        <div class="client-email input text required span8 offset2">
                            <input type="email" id="client-pre-email" class="input-block-level error" autocomplete="on"
                                name="email" placeholder="seu@email.com">
                            <i class="loading-inline icon-spinner icon-spin" style="display: none;">
                                <span>Carregando...</span>
                            </i>
                            <button id="btn-client-pre-email" class="btn btn-success" type="submit">
                                <span>Continuar</span>
                            </button>
                            <span class="help error" style="">Campo obrigatório.</span>
                        </div>
                    </fieldset>
                    <!-- <div class="emailInfo">
                                <h3>Usamos seu e-mail de forma 100% segura para:</h3>
                                <ul class="unstyled">
                                    <li>
                                        <i class="fa fa-check"></i>
                                        <span>Identificar seu perfil</span>
                                    </li>
                                    <li>
                                        <i class="icon-ok"></i>
                                        <span>Notificar sobre o andamento do seu pedido</span>
                                    </li>
                                    <li>
                                        <i class="icon-ok"></i>
                                        <span>Gerenciar seu histórico de compras</span>
                                    </li>
                                    <li>
                                        <i class="icon-ok"></i>
                                        <span>Acelerar o preenchimento de suas informações</span>
                                    </li>
                                </ul>
                                <i class="icon-lock"></i>
                            </div> -->
                </form>
                <div class="step accordion-group client-profile-data filled">
                    <div class="accordion-heading">
                        <span class="accordion-toggle collapsed">
                            <i class="icon-user"></i>
                            <span>Dados pessoais</span>
                        </span>
                    </div>
                    <div class="accordion-body collapse in">
                        <div class="accordion-inner">
                            <div class="box-step">
                                <div class="form-step box-info" style="display: block;">
                                    <p class="client-profile-email">
                                        <span class="email"></span>
                                    </p>
                                    <div class="client-profile-summary">

                                        billing
                                </div>
                                </div>
                                <!-- FIM INFORMAÇÕES PARA VISUALIZAÇÃO -->
                                <!-- INFORMAÇÕES PARA EDIÇÃO -->
                                <form action="" class="form-step box-edit" style="display: none;">
                                    <div class="box-client-info">
                                        <div class="row-fluid">
                                            <!-- INFORMAÇÕES PARA PESSOA FÍSICA -->
                                            <fieldset class="box-client-info-pf">
                                                <p class="client-notice notice">Solicitamos apenas as
                                                    informações essenciais para a realização da compra.</p>
                                                <p class="client-email input text required">
                                                    <label for="client-email">E-mail</label>
                                                    <input type="email" id="client-email" class="input-xlarge error"
                                                        name="email">
                                                    <span class="help error" style="">Campo
                                                        obrigatório.</span>
                                                </p>
                                                <p style="display: none;">
                                                    <span>Você quis dizer:</span>
                                                    <span> </span>
                                                    <a href="#" id="email-suggestion-address">
                                                        <span></span>
                                                        <strong></strong>
                                                    </a>
                                                </p>
                                                <p class="client-first-name input pull-left text required">
                                                    <label for="client-first-name">Primeiro nome</label>
                                                    <input type="text" id="client-first-name" autocomplete="given-name"
                                                        class="input-small error">
                                                    <span class="help error" style="">Campo
                                                        obrigatório.</span>
                                                </p>
                                                <p class="client-last-name input pull-left text required">
                                                    <label for="client-last-name">Último nome</label>
                                                    <input type="text" id="client-last-name" autocomplete="last-name"
                                                        class="input-small error">
                                                    <span class="help error" style="">Campo
                                                        obrigatório.</span>
                                                </p>
                                                <!-- ko ifnot: showDocumentInCorporate -->
                                                <p class="client-document input pull-left text required mask">
                                                    <label for="client-document">CPF</label>
                                                    <input type="tel" id="client-document" class="input-small error"
                                                        placeholder="999.999.999-99">
                                                    <span class="help error" style="">Campo
                                                        obrigatório.</span>
                                                </p>
                                                <p class="client-document input pull-left text required mask"
                                                    style="display: none;">
                                                    <label for="client-document2">CPF</label>
                                                    <input type="text" id="client-document2" class="input-small"
                                                        disabled="disabled">
                                                </p>
                                                <div>
                                                    <p class="client-phone input pull-left text required mask">
                                                        <label for="client-phone">Telefone</label>
                                                        <input type="tel" id="client-phone" class="input-small error"
                                                            placeholder="11 99999-9999">
                                                        <span class="help error" style="">Campo
                                                            obrigatório.</span>
                                                    </p>
                                                    <p class="client-phone input pull-left text required mask">
                                                    </p>
                                                </div>
                                                <div class="document-box hide document-is-required">
                                                    <h5 class="document-box-title">Documento</h5>
                                                    <ul class="links-other-document links unstyled">
                                                        <li class="link-use-other-document link">
                                                            <a href="#" id="no-document-key">Não tenho
                                                                CPF</a>
                                                        </li>
                                                        <li class="link-use-country-document link"
                                                            style="display: none;">
                                                            <a href="#" id="has-document-key">Tenho CPF</a>
                                                        </li>
                                                    </ul>
                                                    <div class="other-document" style="display: none;">
                                                        <p
                                                            class="client-document-type input pull-left text required mask">
                                                            <label for="client-document-type">Nome do
                                                                documento</label>
                                                            <input type="text" id="client-document-type"
                                                                class="input-small" autocomplete="no-complete">
                                                        </p>
                                                        <p
                                                            class="client-new-document input pull-left text required mask">
                                                            <label for="client-new-document">Documento</label>
                                                            <input type="text" id="client-new-document"
                                                                class="input-small" autocomplete="no-complete">
                                                        </p>
                                                    </div>
                                                </div>
                                                <div class="phone-box">
                                                    <ul class="links-other-phone links unstyled">
                                                        <li class="link-use-other-phone link">
                                                            <a href="#" id="no-phone-key">Não tenho telefone
                                                                do país (Brasil)</a>
                                                        </li>
                                                        <li class="link-use-country-phone link" style="display: none;">
                                                            <a href="#" id="has-phone-key">Tenho telefone do
                                                                país (Brasil)</a>
                                                        </li>
                                                    </ul>
                                                    <div class="other-phone" style="display: none;">
                                                        <p class="client-new-phone input pull-left text required mask">
                                                            <label for="client-new-phone">Telefone
                                                                Internacional</label>
                                                            <input type="text" id="client-new-phone" class="input-small"
                                                                placeholder="+9 999 999 9999">
                                                        </p>
                                                    </div>
                                                </div>
                                            </fieldset>
                                            <fieldset class="box-client-info-pj">
                                                <h5 class="corporate-title">Dados da pessoa jurídica</h5>
                                                <ul class="links unstyled">
                                                    <li class="link pf-pj corporate-hide-link">
                                                        <a href="#" id="is-corporate-client" tabindex="17">Incluir
                                                            dados de pessoa
                                                            jurídica</a>
                                                        <a href="#" id="not-corporate-client"
                                                            style="display: none;">Descartar</a>
                                                    </li>
                                                </ul>
                                                <div class="corporate-info-box" style="display: none;">
                                                    <p class="client-company-name input text required">
                                                        <label for="client-company-name">Razão
                                                            Social</label>
                                                        <input type="text" id="client-company-name"
                                                            class="input-xlarge">
                                                    </p>
                                                    <p class="client-company-nickname input text required">
                                                        <label for="client-company-nickname">Nome
                                                            Fantasia</label>
                                                        <input type="text" id="client-company-nickname"
                                                            class="input-xlarge">
                                                    </p>
                                                    <p class="client-company-ie input text required">
                                                        <label for="client-company-ie">Inscrição
                                                            Estadual</label>
                                                        <input type="text" id="client-company-ie"
                                                            autocomplete="no-complete" class="input-small">
                                                    </p>
                                                    <p class="client-company-document input text required mask">
                                                        <label for="client-company-document">CNPJ</label>
                                                        <input type="text" id="client-company-document"
                                                            class="input-small" placeholder="99.999.999/9999-99">
                                                    </p>
                                                    <p class="client-company-document input pull-left text required mask"
                                                        style="display: none;">
                                                        <label for="client-company-document2">CNPJ</label>
                                                        <input type="text" id="client-company-document2"
                                                            class="input-small" disabled="disabled">
                                                    </p>
                                                    <!-- /ko -->
                                                    <!-- ko if: showDocumentInCorporate -->
                                                    <!-- /ko -->
                                                    <div class="clearfix"></div>
                                                    <div class="state-inscription-box">
                                                        <label class="checkbox exempt-si-label">
                                                            <input type="checkbox" id="state-inscription">
                                                            <span class="exempt-si-text">Isento de Inscrição
                                                                Estadual</span>
                                                        </label>
                                                    </div>
                                                    <div class="company-document-box hide company-document-is-required">
                                                        <ul class="links-other-company-document links unstyled">
                                                            <li class="link-use-other-company-document link">
                                                                <a href="#" id="no-company-document-key">A
                                                                    empresa não é do país (Brasil)</a>
                                                            </li>
                                                            <li class="link-use-country-company-document link"
                                                                style="display: none;">
                                                                <a href="#" id="has-company-document-key">A
                                                                    empresa é do país (Brasil)</a>
                                                            </li>
                                                        </ul>
                                                        <div class="other-company-document" style="display: none;">
                                                            <p
                                                                class="client-new-company-document input pull-left text required mask">
                                                                <label for="client-new-company-document">Documento
                                                                    da Empresa</label>
                                                                <input type="text" id="client-new-company-document"
                                                                    class="input-small">
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </fieldset>
                                            <!-- FIM DE INFORMAÇÕES PARA PESSOA JURÍDICA -->
                                        </div>
                                        <p class="save-data hide">
                                            <label class="checkbox save-data-label">
                                                <input type="checkbox" id="opt-in-save-data" disabled="disabled">
                                                <span class="save-data-text">Autorizo a gravação segura dos
                                                    meus dados apenas para compras futuras.</span>
                                            </label>
                                        </p>
                                        <p class="newsletter">
                                            <label class="checkbox newsletter-label">
                                                <input type="checkbox" id="opt-in-newsletter">
                                                <span class="newsletter-text">Quero receber e-mails com
                                                    promoções.</span>
                                            </label>
                                        </p>
                                    </div>
                                    <p class="submit btn-submit-wrapper">
                                        <button type="submit" id="go-to-shipping"
                                            class="submit btn btn-large btn-success">Ir para a
                                            Entrega</button>
                                        <button type="submit" id="go-to-payment"
                                            class="submit btn btn-large btn-success" style="display: none;">Ir
                                            para o Pagamento</button>
                                    </p>
                                </form>
                                <!-- FIM INFORMAÇÕES PARA EDIÇÃO -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="shipping-data" class="span6 pull-right shipping-data">
                <div class="step accordion-group shipping-data">
                                    <?php do_action( 'woocommerce_checkout_order_review' ); ?>

            </div>
            </div>
        </div>
    </div>
    <div class="cart-template mini-cart span4">
        <div class="cart-fixed affix-top cart-fixed-transition" style="height: 158px;">
            <h2>Resumo do pedido</h2>
            <div class="summary-cart-template-holder" style="display: none; height: auto;">
                <div class="cart">
                    <ul class="cart-items unstyled clearfix">
                        <li class="hproduct item muted">
                            <a href="http://www.shopfacil.com.br/smartphone-apple-iphone-12-64-gb-vermelho-1171755585/p"
                                class="url">
                                <img class="photo"
                                    src="//shopfacil.vteximg.com.br/arquivos/ids/92694799-87-87/7433096_1.jpg?v=637793119057730000"
                                    alt="Smartphone Apple iPhone 12 64 GB Vermelho" id="hproduct-item-7433096"
                                    width="45" height="45">
                            </a>
                            <span class="fn product-name" title="Smartphone Apple iPhone 12 64 GB Vermelho"
                                href="http://www.shopfacil.com.br/smartphone-apple-iphone-12-64-gb-vermelho-1171755585/p">Smartphone
                                Apple iPhone 12 64 GB Vermelho</span>
                            <span class="quantity badge">1</span>
                            <div class="description">
                                <span class="shipping-date pull-left">Em até 4 dias úteis</span>
                                <strong class="item-price pull-right hide">R$ 4.939,05</strong>
                                <strong class="item-price-subtotal pull-right hide">R$ 4.939,05</strong>
                                <strong class="price pull-right">R$ 4.939,05</strong>
                                <strong class="price-subtotal pull-right hide">R$ 4.939,05</strong>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
            <p id="go-to-cart-button" class="link link-cart pull-right">
                <small>
                    <a id="orderform-minicart-to-cart" target="_self" href="#/cart">Voltar para o
                        carrinho</a>
                </small>
            </p>





            <div class="summary-template-holder">
                <div class="row-fluid summary" style="display: block;">
                    <div class="forms coupon-column summary-coupon-wrap span7 pull-left" style="display: none;">
                        <div class="coupon summary-coupon pull-right" style="">
                            <div style="display: none;">
                                <?php require_once "cupom.php";?>
                            </div>
                            <p class="coupon-data pull-right">
                                <a class="link-coupon-add" href="javascript:void(0);" id="cart-link-coupon-add">
                                    <span>Adicionar</span>
                                    <span>cupom de desconto</span>
                                </a>
                            </p>
                        </div>
                    </div>
                    <!-- ko if: checkout.hasShippingPreview() -->
                    <div class="cart-more-options span7">
                        <div class="cart-select-gift-placeholder"></div>
                        <div id="shipping-preview-container" class="srp-container">
                            <div class="srp-content onda-v1">
                                <h2 class="srp-main-title mt0 mb0 f3 black-60 fw4">Entrega</h2>
                                <p class="srp-description mw5">Veja as opções de entrega para seus itens,
                                    com todos os prazos e valores.</p>
                                <div class="srp-data mt4"><button id="shipping-calculate-link"
                                        class="shp-open-options vbw1 ba fw5 ttu br2 fw4 v-mid relative mt3 pv3 ph5 f6 bg-washed-blue b--washed-blue blue hover-bg-light-blue hover-b--light-blue hover-heavy-blue pointer">Calcular</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /ko -->
                    <div class="span5 totalizers summary-totalizers cart-totalizers pull-right">
                        <div>
                            <div class="accordion-group" style="display: block;">
                                <div class="accordion-heading">
                                    <span class="accordion-toggle collapsed">Resumo do pedido</span>
                                </div>
                                <div class="accordion-body collapse in">
                                    <div class="accordion-inner">
                                        <div class="summary-discount-descriptions" style="display: none;">
                                            <h3 class="summary-discount-title">Descontos</h3>
                                            <ul class="all-discount-descriptions unstyled"></ul>
                                        </div>
                                        <table class="table">
                                            <tbody class="totalizers-list">
                                                <tr class="Items">
                                                    <td class="info">Subtotal</td>
                                                    <td class="space"></td>
                                                    <td class="monetary">R$ 4.939,05</td>
                                                    <td class="empty"></td>
                                                </tr>
                                            </tbody>
                                            <tbody class="shipping-reset" style="display: none;">
                                                <tr>
                                                    <td class="info">
                                                        <span class="postal-code-for-sla">
                                                            <span class="shipping-name">Entrega</span>
                                                            <span class="shipping-name-to">para</span>
                                                            <span class="postal-code-value"></span>
                                                            <a href="javascript:void(0);" class="cart-reset-postal-code"
                                                                id="cart-reset-postal-code" title="alterar">
                                                                <i class="icon-remove-sign"></i></a>
                                                            <br>
                                                            <a href="javascript:void(0);" class="cart-reset-postal-code"
                                                                id="cart-reset-postal-code">Informar outro
                                                                CEP</a>
                                                        </span>
                                                    </td>
                                                    <td class="space"></td>
                                                    <td class="monetary shipping-unavailable">indisponível
                                                    </td>
                                                </tr>
                                            </tbody>
                                            <tbody class="shipping-calculate">
                                                <tr>
                                                    <td class="info">
                                                        <span>Entrega</span>
                                                    </td>
                                                    <td class="space"></td>
                                                    <td class="monetary">
                                                        <a id="shipping-calculate-link" class="shipping-calculate-link"
                                                            href="javascript:void(0)">Calcular</a>
                                                    </td>
                                                    <td class="monetary form-postal-code forms" colspan="3"
                                                        style="display: none;">
                                                        <div class="shipping summary-shipping">
                                                            <form class="shipping-form-inline" action="">
                                                                <fieldset class="shipping-fieldset">
                                                                    <div class="shipping-fields">
                                                                        <input type="tel" id="summary-postal-code"
                                                                            class="postal-code input-mini"
                                                                            maxlength="9">
                                                                        <i class="loading-inline icon-spinner icon-spin"
                                                                            style="opacity: 0;">
                                                                            <span>Por favor,
                                                                                aguarde...</span>
                                                                        </i>
                                                                        <button type="submit"
                                                                            id="cart-shipping-calculate"
                                                                            class="btn">Calcular</button>
                                                                    </div>
                                                                    <small
                                                                        class="postal-code-service summary-postal-code-service">
                                                                        <a id="cart-dont-know-postal-code"
                                                                            target="_blank"
                                                                            href="http://www.buscacep.correios.com.br/servicos/dnec/index.do">Não
                                                                            sei meu CEP</a></small>
                                                                </fieldset>
                                                            </form>
                                                        </div>
                                                    </td>
                                                    <td class="empty"></td>
                                                </tr>
                                            </tbody>
                                            <tfoot>
                                                <tr class="">
                                                    <td class="info">Total</td>
                                                    <td class="space"></td>
                                                    <td class="monetary">R$ 4.939,05</td>
                                                    <td class="empty"></td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>



            <div class="payment-confirmation-wrap" style="">
                <div class="note summary-note hide">
                    <p class="note-data" style="display: none;">
                        <a class="link-note-add" href="javascript:void(0);" id="cart-link-note-add">
                            <span>Adicionar observação</span> </a>
                    </p>
                    <div style="display: block;">
                        <p class="note-label">
                            <label for="cart-note">Observação</label>
                        </p>
                        <textarea id="cart-note" class="note-textarea" row="3" maxlength="1000"></textarea>
                    </div>
                </div>
                <p class="payment-submit-wrap">
                    <button id="sac-submit" class="btn btn-default btn-large btn-block" tabindex="200"
                        style="display: none;">
                        <span>Cancelar</span>
                    </button>
                    <button id="payment-data-submit" class="submit btn btn-success btn-large btn-block"
                        style="display: none;">
                        <i class="icon-lock" style=""></i>
                        <i class="icon-spinner icon-spin" style="display: none;"></i>
                        <span>Salvar alterações</span>
                    </button>
                    <button id="payment-data-submit" class="submit btn btn-success btn-large btn-block">
                        <i class="icon-lock" style=""></i>
                        <i class="icon-spinner icon-spin" style="display: none;"></i>
                        <span>Finalizar compra</span>
                    </button>
                </p>
            </div>
        </div>
    </div>
</div>