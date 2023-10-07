jQuery(document).ready(function($) {
    $('.single_add_to_cart_button.button').text("Orçar");

    $('#ufSelect').on("change", function() {
        const selectedValue = $('#ufSelect').val();
        if (selectedValue !== 'ES') {
          $('.modalDelivery').css('display', 'flex');
          $('.checkout-button').addClass('disabled-button');
        } else {
            $('.modalDelivery').css('display', 'none');
            $('.wc-proceed-to-checkout .checkout-button').removeClass('disabled-button');
        }
      });
  
    // Fechar o modal de erro quando o botão "Fechar" for clicado
    $('#closeModal').click(function(e) {
        e.preventDefault();
        $('.modalDelivery').css('display', 'none');
    });

    // Fechar o modal de erro se o usuário clicar fora dele
    // $(window).click(function(event) {
    //     event.preventDefault();
    //     if (event.target === $('#myModal')[0]) {
    //         $('.modalDelivery').css('display', 'none');
    //     }
    // });

    /* XXXXXXXXXXXXXXXXXX WOOCOMMERCE CHECKOUT FIELDS XXXXXXXXXXXXXXXXX */    
    // Inicialmente, ocultar todos os campos relacionados
    $('campo-pessoa-juridica').hide();

    // Adicionar um evento de mudança ao campo de seleção
    $('#billing_persontype').on("change", function() {
        const selectedValue = $(this).val();
        // Ocultar todos os campos relacionados
        // $('.campo-pessoa-fisica, .campo-pessoa-juridica').hide();
        $('.campo-pessoa-juridica').hide();

        
        if (selectedValue === "2") {
            // Mostrar os campos relacionados a Pessoa Física
            $('.campo-pessoa-juridica').show();
            var div = $('.woocommerce-billing-fields__field-wrapper');
            var divNumberFive = div.find("p:eq(5)")
            console.log(divNumberFive);
            $('.campo-pessoa-juridica').insertBefore(divNumberFive);
            // var elements = div.find('.campo-pessoa-juridica');
            // elements.each(function(element) {
            //   element.insertBefore(div.find("p:eq(5)"));
            // })
          }
    });

})