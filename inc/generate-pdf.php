<?php
function add_custom_bulk_action_to_order($actions) {
    global $post;
    if ($post->post_type === 'shop_order') {
        $actions['generate_pdf'] = 'Gerar PDF';
    }
    return $actions;
}
add_filter('bulk_actions-edit-shop_order', 'add_custom_bulk_action_to_order');


function handle_custom_bulk_action_to_order($redirect_to, $action, $post_ids) {
    if ($action === 'generate_pdf') {
        
        foreach ($post_ids as $post_id) {
            $order = wc_get_order($post_id);
            if ($order) {
                generate_pdf($order);
            }
        }
    }

    return $redirect_to;
}
add_filter('handle_bulk_actions-edit-shop_order', 'handle_custom_bulk_action_to_order', 10, 3);

use FPDF as GlobalFPDF;
function generate_pdf($order) {
    require(plugin_dir_path(__FILE__) . '../vendor/setasign/fpdf/fpdf.php');
    ob_clean();
    $logo_path = plugin_dir_url(__FILE__) . '../assets/images/mirantelogo.png';

    $pdf = new FPDF('L','mm','A4');

    $pdf->AddPage();

    $pdf->SetFont('Arial','',12);
    
    $fontSize=12;

    $pdf->Cell(114, 6, $pdf->Image($logo_path, 10, 10, 60), 0, 0);
    $pdf->SetFont('Arial', "B", 10);
    $pdf->Cell(0, 6, utf8_decode("Mirante - Móveis Corporativos"), 0, 1, "R"); 

    $pdf->Cell(114, 6, "", 0, 0);
    $pdf->SetFont('Arial', "", 10);
    $pdf->Cell(0, 6, utf8_decode("Humberto de Campos, 470 A - loja 01 - Jardim Limoeiro, Serra - ES, 29164-034"), 0, 1, "R");

    $pdf->Ln(4);

    $pdf->SetFont('Arial', "B", 16);
    $pdf->Cell(0, 16, utf8_decode("ORÇAMENTO"), 0, 1,);

    $pdf->SetFont('Arial', "", 12);
    $pdf->Cell(0, 6, utf8_decode($order->get_billing_first_name() . ' ' . $order->get_billing_last_name()), 0, 1);

    // $pdf->Cell(189, 6, utf8_decode($order->get_billing_address_1() . ', ' . $order->get_billing_address_2()), 0, 1);

    $pdf->Cell(202, 6, utf8_decode($order->get_billing_address_1() . ', ' . $order->get_billing_address_2()), 0, 0);
    $pdf->Cell($pdf->GetStringWidth("Vendedor:"), 6, utf8_decode("Vendedor:"), 0, 0);
    $pdf->Cell(0, 6, utf8_decode(get_field('vendedor' , $order->get_order_number())), 0, 1, "R");

    $pdf->Cell(202, 6, utf8_decode($order->get_billing_city(). " - Espírito Santo"), 0, 0);
    $pdf->Cell(45, 6, utf8_decode("Data do Orçamento:"), 0, 0); 
    $pdf->Cell(30, 6, utf8_decode(date('d/m/Y')), 0, 1);

    $pdf->Cell(202, 6,utf8_decode($order->get_billing_phone()), 0, 0);
    $pdf->Cell(45, 6, utf8_decode("Número do Pedido:"), 0, 0); 
    $pdf->Cell(30, 6, utf8_decode($order->get_order_number()), 0, 1); 

    $pdf->Cell(202, 6, utf8_decode($order->get_billing_email()), 0, 0);
    $pdf->Cell(45, 6, utf8_decode("Data do Pedido:"), 0, 0); 
    $pdf->Cell(30, 6, utf8_decode($order->get_date_created()->format('d/m/Y')), 0, 1);

    $pdf->Ln(6);

    $pdf->SetFillColor(0,0,0);
    $pdf->SetTextColor(255,255,255);
    $pdf->Cell(25,16,utf8_decode("imagem"),1,0, "C", true); 
    $pdf->Cell(152,16,utf8_decode("Produto"),1, 0, "C", true);
    $pdf->Cell(10,16, utf8_decode("QTD"),1,0, "C", true);

    $yPos_HeadTable = $pdf->GetY();

    $pdf->Cell(60,8, utf8_decode("Preço"),1,1, "C", true);

    $yPos_preco = $pdf->GetY();

    $pdf->SetXY(197, ($yPos_preco));

    $pdf->Cell(30,8, utf8_decode("Preço Un."),1,0, "R", true);
    $pdf->Cell(30,8, utf8_decode("Preço Total"),1,1, "R", true);

    $pdf->SetXY(257, $yPos_HeadTable);
    $pdf->Cell(35,16, utf8_decode("Entrega"),1,1, "C", true);

    $valor_total = 0;
    $pdf->SetTextColor(0,0,0);
    foreach($order->get_items() as $item_id => $item){
        $product = $item->get_product();
        $product_name = $product->get_name();
        $product_quantity = $item->get_quantity();
        $product_id = $product->get_id();
        
        $product_price = $product->get_price();
        $formatted_price_unit = 'R$ ' . number_format($product_price, 2, ',', '.');

        $product_total= ($product_price * $product_quantity);
        $formatted_price_total = 'R$ ' . number_format($product_total, 2, ',', '.');

        
        $valor_total += $product_total;
        $formatted_valor_total = 'R$ ' . number_format($valor_total, 2, ',', '.');

        $delivery_type;
        
        $table_product = wc_get_product( $item["product_id"] );

        if ( $table_product->is_type( 'variable' ) ) {
            // Obtenha todas as variantes do produto
            $variation_id = $item["variation_id"];

            if(get_the_post_thumbnail_url($variation_id)) {
                $product_image_url = get_the_post_thumbnail_url($variation_id);
            } else {
                $product_image_url = plugin_dir_url(__FILE__) . '../assets/images/woocommerce-placeholder.png';
            }

            $meta_data = get_post_meta( $variation_id );
            $valor = $meta_data["_pronta_entrega_encomenda"][0];

            // echo $valor;
            if ($valor === "pronta_entrega") {
                $delivery_type = "Pronta Entrega";
            } else if ($valor === "encomenda") {
                $delivery_type = "Sob Encomenda";
            } else {
                $delivery_type = "Não definido";
            }
        } else {
            if(get_the_post_thumbnail_url($product_id)) {
                $product_image_url = get_the_post_thumbnail_url($product_id);
            } else {
                $product_image_url = plugin_dir_url(__FILE__) . '../assets/images/woocommerce-placeholder.png';
            }

            $meta_data = get_post_meta( $product_id );
            $valor = $meta_data["_yith_wcbm_badge_ids"][0];

            if ($valor === "277") {
                $delivery_type = "Pronta Entrega";
            } else if ($valor === "275") {
                $delivery_type = "Sob Encomenda";
            } else {
                $delivery_type = "Não definido";
            }
        }

        $cellWidth=152;//wrapped cell width
        $cellHeight=20;//normal one-line cell height
        
        //check whether the text is overflowing
        if($pdf->GetStringWidth($product_name) < $cellWidth){
            //if not, then do nothing
            $line=1;
        }else{
            //if it is, then calculate the height needed for wrapped cell
            //by splitting the text to fit the cell width
            //then count how many lines are needed for the text to fit the cell
            
            $textLength=strlen($product_name);	//total text length
            $errMargin=10;		//cell width error margin, just in case
            $startChar=0;		//character start position for each line
            $maxChar=0;			//maximum character in a line, to be incremented later
            $textArray=array();	//to hold the strings for each line
            $tmpString="";		//to hold the string for a line (temporary)
            
            while($startChar < $textLength){ //loop until end of text
                //loop until maximum character reached
                while( 
                $pdf->GetStringWidth( $tmpString ) < ($cellWidth-$errMargin) &&
                ($startChar+$maxChar) < $textLength ) {
                    $maxChar++;
                    $tmpString=substr($product_name,$startChar,$maxChar);
                }
                //move startChar to next line
                $startChar=$startChar+$maxChar;
                //then add it into the array so we know how many line are needed
                array_push($textArray,$tmpString);
                //reset maxChar and tmpString
                $maxChar=0;
                $tmpString='';
                
            }
            //get number of line
            $line=count($textArray);
        }
        
        //write the cells
        $pdf->Cell(25,($line * $cellHeight),utf8_decode(""),1,0, "C"); //adapt height to number of lines

        //use MultiCell instead of Cell
        //but first, because MultiCell is always treated as line ending, we need to 
        //manually set the xy position for the next cell to be next to it.
        //remember the x and y position before writing the multicell
        $xPos=$pdf->GetX();
        $yPos=$pdf->GetY();

        $pdf->MultiCell($cellWidth,$cellHeight,utf8_decode($product_name),1);
        
        //return the position for next cell next to the multicell
        //and offset the x with multicell width
        $pdf->SetXY($xPos + $cellWidth , $yPos);

        $pdf->Image($product_image_url, 16, ($yPos + (($line * $cellHeight) / 2 - 6)), 12, 12);

        $pdf->Cell(10,($line * $cellHeight),utf8_decode($product_quantity),1,0, "C"); //adapt height to number of lines *QUANTIDADE*
        
        $pdf->Cell(30,($line * $cellHeight),utf8_decode($formatted_price_unit),1,0, "R"); //adapt height to number of lines *UNITÁRIO*

        $pdf->Cell(30,($line * $cellHeight),utf8_decode($formatted_price_total),1,0,"R"); //adapt height to number of lines *PREÇO TOTAL*

        $pdf->Cell(35,($line * $cellHeight),utf8_decode($delivery_type),1,1, "C"); //adapt height to number of lines
    }

    $pdf->Ln(6);

    $pdf->SetFont('Arial', "", 14);
    $pdf->Cell(($pdf->GetStringWidth("Valor Total:")), 6,utf8_decode('Valor total:'),0,0);
    $pdf->Cell(0, 6,utf8_decode($formatted_valor_total),0,1);

    $pdf->Ln(4);

    $pdf->SetFont('Arial', "B", 16);
    $pdf->Cell(107, 12, utf8_decode("Detalhes do Pedidos"),0,0);
    $pdf->Cell(0, 12, utf8_decode("Observações"),0,1);

    $pdf->SetFont('Arial', "", 12);
    $pdf->Cell(44, 6, utf8_decode("Validade da proposta:"),0,0);
    $pdf->Cell(63, 6, utf8_decode(get_field('validade_da_proposta' , $order->get_order_number())),0,0);

    $yPos = $pdf->GetY();

    $pdf->MultiCell(0, 6, utf8_decode(get_field('observacoes' , $order->get_order_number())),0);

    $pdf->SetXY(10, ($yPos + 6));
    $pdf->Cell(44, 6, utf8_decode("Forma de Pagamento:"),0,0);
    $pdf->Cell(63, 6, utf8_decode(get_field('metodo_de_pagamento' , $order->get_order_number())),0,1);

    $pdf->Cell(44, 6, utf8_decode("Prazo de Entrega:"),0,0);
    $pdf->Cell(63, 6, utf8_decode(get_field('prazo_de_entrega' , $order->get_order_number())),0,1);

    $filename = 'Orcamento-' . $order->get_date_created()->format('d-m-Y') . '.pdf';
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    $pdf->Output('D', $filename);

    die();
}




add_action('wp_ajax_generate_pdf', 'generate_pdf');
add_action('wp_ajax_nopriv_generate_pdf', 'generate_pdf');
?>