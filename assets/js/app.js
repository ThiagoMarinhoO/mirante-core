function geraCPF() {
  var n = 9;
  var n1 = Math.floor(Math.random() * n);
  var n2 = Math.floor(Math.random() * n);
  var n3 = Math.floor(Math.random() * n);
  var n4 = Math.floor(Math.random() * n);
  var n5 = Math.floor(Math.random() * n);
  var n6 = Math.floor(Math.random() * n);
  var n7 = Math.floor(Math.random() * n);
  var n8 = Math.floor(Math.random() * n);
  var n9 = Math.floor(Math.random() * n);
  var d1 = n9 * 2 + n8 * 3 + n7 * 4 + n6 * 5 + n5 * 6 + n4 * 7 + n3 * 8 + n2 * 9 + n1 * 10;
  d1 = 11 - (d1 % 11);
  if (d1 >= 10) d1 = 0;
  var d2 = d1 * 2 + n9 * 3 + n8 * 4 + n7 * 5 + n6 * 6 + n5 * 7 + n4 * 8 + n3 * 9 + n2 * 10 + n1 * 11;
  d2 = 11 - (d2 % 11);
  if (d2 >= 10) d2 = 0;

  return '' + n1 + n2 + n3 + n4 + n5 + n6 + n7 + n8 + n9 + d1 + d2;
}

jQuery(document).ready(function($) {
    /* XXXXXXXXXXXXXXXXXX BUTTONS ADITIONALS  XXXXXXXXXXXXXXXXX */    
    $('.single_add_to_cart_button.button').text("Orçar");
    $('.woocommerce-loop-product__buttons .product_type_simple').text("Orçar");
    
    $('#ufSelect').val('')
    $('.checkout-button').addClass('disabled-button');

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

    var cpf = geraCPF();
    $("#billing_cpf").val(cpf);

    /* XXXXXXXXXXXXXXXXXX MEGA MENU XXXXXXXXXXXXXXXXX */
    $('.sub-menu').hide();

    $('.menu-item-has-children').hover(function () {
      $(this).find('.sub-menu').fadeIn('slow');
      $(this).find('.sub-menu').css('display', 'flex');
    },
    function() {
      // Adicione o código que desejar executar quando o mouse sair do elemento aqui.
      $(this).find('.sub-menu').fadeOut();
    });

    $('.sub-menu .childrenLiDiv li').hover(
      function() {
        const selectedMiniatureURL = $(this).attr('categoryimage');
        $('.sub-menu img').attr('src', selectedMiniatureURL);
        // Adicione o código que desejar executar quando o mouse passar por cima do elemento aqui.
      },
      function() {
        // Adicione o código que desejar executar quando o mouse sair do elemento aqui.
        $('.sub-menu img').attr('src', '/wp-content/uploads/2023/05/181568379843805d7b93c37a8d0-1024x603.jpg');
      }
    );
	$(document).on('click' , 'button[name="woocommerce_checkout_place_order"]' , function(e){
    e.preventDefault()
		if($('input#billing_company').val() == ''){
			$('input#billing_company').val($('#razao_social').val())	
		}
    $('form.checkout').submit();
	})
})