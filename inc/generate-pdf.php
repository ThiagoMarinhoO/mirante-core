<?php
function add_custom_bulk_action_to_order($actions) {
    global $post;
    if ($post->post_type === 'shop_order') {
        $actions['generate_pdf'] = 'Gerar PDF';
    }
    return $actions;
}
add_filter('bulk_actions-edit-shop_order', 'add_custom_bulk_action_to_order');

// Lide com a ação em massa quando ela for executada
function handle_custom_bulk_action_to_order($redirect_to, $action, $post_ids) {
    if ($action === 'generate_pdf') {
        // Lógica para a ação "Gerar PDF"
        foreach ($post_ids as $post_id) {
            // Obtenha o pedido individual e realize as ações necessárias para gerar o PDF
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

    $pdf = new FPDF('P','mm','A4');

    $pdf->AddPage();

    $pdf->SetFont('Arial','',12);
    //define standard font size
    $fontSize=12;

    $pdf->Cell(114, 6, $pdf->Image($logo_path, 10, 10, 60), 0, 0);
    $pdf->SetFont('Arial', "B", 10);
    $pdf->Cell(0, 6, utf8_decode("Mirante - Móveis Corporativos"), 0, 1, "R"); //fim da linha

    $pdf->Cell(114, 6, "", 0, 0);
    $pdf->SetFont('Arial', "", 10);
    $pdf->Cell(0, 6, utf8_decode("Humberto de Campos, 470 A - loja 01"), 0, 1, "R"); //fim da linha

    $pdf->Cell(114, 6, "", 0, 0);
    $pdf->Cell(0, 6, utf8_decode("Jardim Limoeiro, Serra - ES, 29164-034"), 0, 1, "R");//fim da linha

    $pdf->Cell(0, 6, "", 0, 1); //Linha em branco acima da Fatura

    $pdf->SetFont('Arial', "B", 16);
    $pdf->Cell(189, 20, utf8_decode("FATURA"), 0, 1,);

    $pdf->SetFont('Arial', "", 12);
    $pdf->Cell(189, 6, utf8_decode($order->get_billing_first_name() . ' ' . $order->get_billing_last_name()), 0, 1);

    $pdf->Cell(189, 6, utf8_decode($order->get_billing_address_1() . ', ' . $order->get_billing_address_2()), 0, 1);

    $pdf->Cell(114, 6, utf8_decode($order->get_billing_city()), 0, 0);
    $pdf->Cell(50, 6, utf8_decode("Número da Fatura:"), 0, 0);
    $pdf->Cell(25, 6, utf8_decode("8"), 0, 1); //fim da linha

    $pdf->Cell(114, 6, utf8_decode("Espírito Santo"), 0, 0);
    $pdf->Cell(50, 6, utf8_decode("Data da Fatura:"), 0, 0); //fim da linha
    $pdf->Cell(25, 6, utf8_decode("dd/mm/yyyy"), 0, 1); //fim da linha

    $pdf->Cell(114, 6, utf8_decode("XXXXX-XXX"), 0, 0);
    $pdf->Cell(50, 6, utf8_decode("Número do Pedido:"), 0, 0); //fim da linha
    $pdf->Cell(25, 6, utf8_decode("2658"), 0, 1); //fim da linha

    $pdf->Cell(114, 6, utf8_decode($order->get_billing_email()), 0, 0);
    $pdf->Cell(50, 6, utf8_decode("Data do Pedido:"), 0, 0); //fim da linha
    $pdf->Cell(25, 6, utf8_decode("dd/mm/yyyy"), 0, 1); //fim da linha

    $pdf->Cell(114, 6,utf8_decode($order->get_billing_phone()), 0, 0);
    $pdf->Cell(50, 6, utf8_decode("Método de Pagamento:"), 0, 0); //fim da linha
    $pdf->Cell(25, 6, utf8_decode("-"), 0, 1, "C"); //fim da linha

    $pdf->Cell(0, 20, "", 0, 1); //Linha falsa acima da Tabela
    

    foreach($order->get_items() as $item_id => $item){
        $product = $item->get_product();
        $product_name = $product->get_name();
        $product_quantity = $item->get_quantity();
        $product_price = $product->get_price();

        $cellWidth=80;//wrapped cell width
        $cellHeight=5;//normal one-line cell height
        
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

        $pdf->SetFillColor(0,0,0);
        $pdf->SetTextColor(255,255,255);
        //write the cells
        $pdf->Cell(40,($line * $cellHeight),utf8_decode("imagem"),1,0, "C", true); //adapt height to number of lines
        
        //use MultiCell instead of Cell
        //but first, because MultiCell is always treated as line ending, we need to 
        //manually set the xy position for the next cell to be next to it.
        //remember the x and y position before writing the multicell
        $xPos=$pdf->GetX();
        $yPos=$pdf->GetY();
        $pdf->MultiCell($cellWidth,($line * $cellHeight),utf8_decode("Produto"),1, "C", true);
        
        //return the position for next cell next to the multicell
        //and offset the x with multicell width
        $pdf->SetXY($xPos + $cellWidth , $yPos);

        $pdf->Cell(30,($line * $cellHeight), utf8_decode("Quantidade"),1,0, "C", true); //adapt height to number of lines
        
        $pdf->Cell(40,($line * $cellHeight), utf8_decode("Preço"),1,1, "C", true); //adapt height to number of lines

        // $pdf->Ln();
        

        $pdf->SetTextColor(0,0,0);
        //write the cells
        $pdf->Cell(40,($line * $cellHeight),utf8_decode("imagem"),1,0, "C"); //adapt height to number of lines
        
        //use MultiCell instead of Cell
        //but first, because MultiCell is always treated as line ending, we need to 
        //manually set the xy position for the next cell to be next to it.
        //remember the x and y position before writing the multicell
        $xPos=$pdf->GetX();
        $yPos=$pdf->GetY();
        $pdf->MultiCell($cellWidth,$cellHeight,utf8_decode($product_name),1, "C");
        
        //return the position for next cell next to the multicell
        //and offset the x with multicell width
        $pdf->SetXY($xPos + $cellWidth , $yPos);

        $pdf->Cell(30,($line * $cellHeight), utf8_decode($product_quantity),1,0, "C"); //adapt height to number of lines
        
        $pdf->Cell(40,($line * $cellHeight),utf8_decode($product_price),1,1, "C"); //adapt height to number of lines
        
    }

    $filename = 'Orcamento-' . $order->get_date_created()->format('d-m-Y') . '-' . date('d-m-Y') . '.pdf';
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    $pdf->Output('D', $filename);

    die();
}




add_action('wp_ajax_generate_pdf', 'generate_pdf');
add_action('wp_ajax_nopriv_generate_pdf', 'generate_pdf');
?>