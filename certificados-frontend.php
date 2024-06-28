<?php

// Generar el código QR en el contenido del certificado
function generar_qr_certificado($content) {
    if (get_post_type() == 'certificado') {
        $laboratorio = get_post_meta(get_the_ID(), 'laboratorio', true);
        $acreditacion = get_post_meta(get_the_ID(), 'acreditacion', true);
        $fecha_emision = get_post_meta(get_the_ID(), 'fecha_emision', true);
        $folio_certificado = get_post_meta(get_the_ID(), 'folio_certificado', true);
        $tipo_instrumento = get_post_meta(get_the_ID(), 'tipo_instrumento', true);
        $marca = get_post_meta(get_the_ID(), 'marca', true);
        $modelo = get_post_meta(get_the_ID(), 'modelo', true);
        $numero_serie = get_post_meta(get_the_ID(), 'numero_serie', true);
        $numero_inventario = get_post_meta(get_the_ID(), 'numero_inventario', true);

        $url = get_permalink();
        $upload_dir = wp_upload_dir();
        $qr_code_url = $upload_dir['url'] . "/qr_" . get_the_ID() . ".png";

        $search_form = '<div class="search-certificado-form">';
        $search_form .= '<input type="text" id="search-folio" placeholder="Ingresa el folio del certificado">';
        $search_form .= '<button id="search-folio-btn">Buscar</button>';
        $search_form .= '</div>';

        $content = $search_form . $content;

        $content .= '<div class="certificado-qr">';
        $content .= '<img src="' . esc_url($qr_code_url) . '" alt="QR Code">';
        $content .= '</div>';

        $content .= '<h3>Información del Certificado</h3>';
        $content .= '<p><strong>Laboratorio:</strong> ' . esc_html($laboratorio) . '</p>';
        $content .= '<p><strong>Acreditación:</strong> ' . esc_html($acreditacion) . '</p>';
        $content .= '<p><strong>Fecha de Emisión:</strong> ' . esc_html($fecha_emision) . '</p>';
        $content .= '<p><strong>Folio de Certificado:</strong> ' . esc_html($folio_certificado) . '</p>';
        $content .= '<p><strong>Tipo de Instrumento:</strong> ' . esc_html($tipo_instrumento) . '</p>';
        $content .= '<p><strong>Marca:</strong> ' . esc_html($marca) . '</p>';
        $content .= '<p><strong>Modelo:</strong> ' . esc_html($modelo) . '</p>';
        $content .= '<p><strong>No. Serie:</strong> ' . esc_html($numero_serie) . '</p>';
        $content .= '<p><strong>No. Inventario:</strong> ' . esc_html($numero_inventario) . '</p>';
    }

    return $content;
}
add_filter('the_content', 'generar_qr_certificado');

// Incluir CSS y JavaScript para la búsqueda por folio
function incluir_css_js_busqueda() {
    wp_enqueue_style('frontend-styles', plugin_dir_url(__FILE__) . 'css/frontend-styles.css');
    wp_enqueue_script('frontend-scripts', plugin_dir_url(__FILE__) . 'js/frontend-scripts.js', array('jquery'), null, true);
}
add_action('wp_enqueue_scripts', 'incluir_css_js_busqueda');
?>
