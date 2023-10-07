<?php
    function mega_menu() {
        ?>
    
        <header class="mega_menu">
    
            <?php
            wp_nav_menu(
                array(
                    'menu'            => 'Menu Cabeçalho Imagem Mirante',
                    'container_id'    => 'primary-menu',
                    'container_class' => 'items-center justify-between hidden w-full md:flex md:w-auto md:order-1',
                    'menu_class'      => 'menuListStyle',
                    'theme_location'  => 'primary',
                    'walker'          => new Custom_Walker_Nav_Menu(), // Adicione o walker personalizado
                )
            );
            ?>
    
        </header>
    
        <?php
    }
    add_shortcode('mega_menu', 'mega_menu');

    class Custom_Walker_Nav_Menu extends Walker_Nav_Menu {
        function start_el(&$output, $item, $depth = 0, $args = null, $id = 0) {
            // Verifique se o item tem sub-itens (children)
            if ($args->walker->has_children) {
                // Adicione sua personalização do item de menu aqui
                
                $output .= '<li class="menu-item-has-children">';
                // Adicione um ícone antes do link
                $output .= '<a href="' . esc_url($item->url) . '">' . esc_html($item->title) . '<i class="fas fa-angle-down premium-dropdown-icon"></i></a>';
            } else {
                // Se não tiver sub-itens, apenas adicione o link
                $output .= '<li>';
                $output .= '<a href="' . esc_url($item->url) . '">' . esc_html($item->title) . '</a>';
            }

        }


        function start_lvl(&$output, $depth = 0, $args = null) {
            // Adicione sua personalização do submenu aqui
            $output .= '<ul class="sub-menu">'; // Personalize a classe da sub-menu conforme necessário

            $output .= '<img src="http://mirante.test/wp-content/uploads/2023/08/cadeira_presidente_alta_relax_com_braco_corsa_courissimo_linha_economica.jpg" alt="Imagem da categoria">';

            $output .= '<div>';
            $output .= '<li></li>';
        }
        
    
        function end_lvl(&$output, $depth = 0, $args = null) {
            // Feche o submenu personalizado
            $output .= '</div>';
            $output .= '</ul>';
        }
    }