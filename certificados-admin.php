<?php

// Incluir el autoload de Composer
require_once plugin_dir_path(__FILE__) . 'vendor/autoload.php';

use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

// Registrar el Custom Post Type 'certificado'
function crear_certificado_post_type() {
    $labels = array(
        'name' => 'Certificados',
        'singular_name' => 'Certificado',
        'add_new' => 'Añadir Nuevo',
        'add_new_item' => 'Añadir Nuevo Certificado',
        'edit_item' => 'Editar Certificado',
        'new_item' => 'Nuevo Certificado',
        'view_item' => 'Ver Certificado',
        'search_items' => 'Buscar Certificados',
        'not_found' => 'No se encontraron certificados',
        'not_found_in_trash' => 'No se encontraron certificados en la papelera',
    );

    $args = array(
        'labels' => $labels,
        'public' => true,
        'has_archive' => true,
        'supports' => array('custom-fields', 'thumbnail'),
        'menu_position' => 5,
        'menu_icon' => 'dashicons-awards',
    );

    register_post_type('certificado', $args);
}
add_action('init', 'crear_certificado_post_type');

// Añadir metaboxes personalizados para la información del certificado
function agregar_metaboxes_certificado() {
    add_meta_box(
        'info_certificado',
        'Información del Certificado',
        'mostrar_metaboxes_certificado',
        'certificado',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'agregar_metaboxes_certificado');

function mostrar_metaboxes_certificado($post) {
    $laboratorio = get_post_meta($post->ID, 'laboratorio', true);
    $acreditacion = get_post_meta($post->ID, 'acreditacion', true);
    $fecha_emision = get_post_meta($post->ID, 'fecha_emision', true);
    $folio_certificado = get_post_meta($post->ID, 'folio_certificado', true);
    $tipo_instrumento = get_post_meta($post->ID, 'tipo_instrumento', true);
    $marca = get_post_meta($post->ID, 'marca', true);
    $modelo = get_post_meta($post->ID, 'modelo', true);
    $numero_serie = get_post_meta($post->ID, 'numero_serie', true);
    $numero_inventario = get_post_meta($post->ID, 'numero_inventario', true);

    echo '<label for="laboratorio">Laboratorio:</label>';
    echo '<input type="text" name="laboratorio" value="' . esc_attr($laboratorio) . '" class="widefat"><br>';

    echo '<label for="acreditacion">Acreditación:</label>';
    echo '<input type="text" name="acreditacion" value="' . esc_attr($acreditacion) . '" class="widefat"><br>';

    echo '<label for="fecha_emision">Fecha de Emisión:</label>';
    echo '<input type="date" name="fecha_emision" value="' . esc_attr($fecha_emision) . '" class="widefat"><br>';

    echo '<label for="folio_certificado">Folio de Certificado:</label>';
    echo '<input type="text" name="folio_certificado" value="' . esc_attr($folio_certificado) . '" class="widefat"><br>';

    echo '<label for="tipo_instrumento">Tipo de Instrumento:</label>';
    echo '<input type="text" name="tipo_instrumento" value="' . esc_attr($tipo_instrumento) . '" class="widefat"><br>';

    echo '<label for="marca">Marca:</label>';
    echo '<input type="text" name="marca" value="' . esc_attr($marca) . '" class="widefat"><br>';

    echo '<label for="modelo">Modelo:</label>';
    echo '<input type="text" name="modelo" value="' . esc_attr($modelo) . '" class="widefat"><br>';

    echo '<label for="numero_serie">No. Serie:</label>';
    echo '<input type="text" name="numero_serie" value="' . esc_attr($numero_serie) . '" class="widefat"><br>';

    echo '<label for="numero_inventario">No. Inventario:</label>';
    echo '<input type="text" name="numero_inventario" value="' . esc_attr($numero_inventario) . '" class="widefat"><br>';
}

// Guardar la información del certificado y configurar el título a partir del folio del certificado
function guardar_info_certificado($post_id) {
    if (array_key_exists('laboratorio', $_POST)) {
        update_post_meta($post_id, 'laboratorio', sanitize_text_field($_POST['laboratorio']));
    }

    if (array_key_exists('acreditacion', $_POST)) {
        update_post_meta($post_id, 'acreditacion', sanitize_text_field($_POST['acreditacion']));
    }

    if (array_key_exists('fecha_emision', $_POST)) {
        update_post_meta($post_id, 'fecha_emision', sanitize_text_field($_POST['fecha_emision']));
    }

    if (array_key_exists('folio_certificado', $_POST)) {
        $folio_certificado = sanitize_text_field($_POST['folio_certificado']);
        update_post_meta($post_id, 'folio_certificado', $folio_certificado);
    } else {
        $folio_certificado = get_post_meta($post_id, 'folio_certificado', true);
    }

    if (array_key_exists('tipo_instrumento', $_POST)) {
        update_post_meta($post_id, 'tipo_instrumento', sanitize_text_field($_POST['tipo_instrumento']));
    }

    if (array_key_exists('marca', $_POST)) {
        update_post_meta($post_id, 'marca', sanitize_text_field($_POST['marca']));
    }

    if (array_key_exists('modelo', $_POST)) {
        update_post_meta($post_id, 'modelo', sanitize_text_field($_POST['modelo']));
    }

    if (array_key_exists('numero_serie', $_POST)) {
        update_post_meta($post_id, 'numero_serie', sanitize_text_field($_POST['numero_serie']));
    }

    if (array_key_exists('numero_inventario', $_POST)) {
        update_post_meta($post_id, 'numero_inventario', sanitize_text_field($_POST['numero_inventario']));
    }

    // Configurar el título y el slug a partir del folio del certificado
    if (!empty($folio_certificado)) {
        $post_data = array(
            'ID' => $post_id,
            'post_title' => $folio_certificado,
            'post_name' => sanitize_title($folio_certificado),
        );
        remove_action('save_post', 'guardar_info_certificado'); // Evitar bucles infinitos
        wp_update_post($post_data);
        add_action('save_post', 'guardar_info_certificado');
    }

    // Eliminar el QR anterior si existe
    $thumbnail_id = get_post_thumbnail_id($post_id);
    if ($thumbnail_id) {
        wp_delete_attachment($thumbnail_id, true);
    }

    // Generar y guardar el nuevo código QR como imagen destacada
    $url = get_permalink($post_id);
    $upload_dir = wp_upload_dir();
    $qr_code_path = $upload_dir['path'] . "/qr_$post_id.png";

    // Opciones para el QR
    $options = new QROptions([
        'outputType' => QRCode::OUTPUT_IMAGE_PNG,
        'eccLevel' => QRCode::ECC_L,
        'scale' => 5,
    ]);

    // Generar el código QR
    $qrcode = new QRCode($options);
    $qrcode->render($url, $qr_code_path);

    $filetype = wp_check_filetype($qr_code_path, null);
    $attachment = array(
        'post_mime_type' => $filetype['type'],
        'post_title' => "QR Code for $folio_certificado",
        'post_content' => '',
        'post_status' => 'inherit'
    );

    $attach_id = wp_insert_attachment($attachment, $qr_code_path, $post_id);
    require_once(ABSPATH . 'wp-admin/includes/image.php');
    $attach_data = wp_generate_attachment_metadata($attach_id, $qr_code_path);
    wp_update_attachment_metadata($attach_id, $attach_data);

    // Establecer la imagen destacada
    set_post_thumbnail($post_id, $attach_id);
}
add_action('save_post', 'guardar_info_certificado');

// Añadir columnas personalizadas en el listado de certificados en el admin
function agregar_columnas_certificado($columns) {
    $new_columns = array(
        'cb' => $columns['cb'],
        'title' => 'Folio',
        'numero_inventario' => 'No. de Inventario',
        'fecha_emision' => 'Fecha de Emisión',
        'numero_serie' => 'No. de Serie',
        'laboratorio' => 'Laboratorio',
        'qr_code' => 'Código QR'
    );
    return $new_columns;
}
add_filter('manage_certificado_posts_columns', 'agregar_columnas_certificado');

function mostrar_columnas_certificado($column, $post_id) {
    switch ($column) {
        case 'numero_inventario':
            echo esc_html(get_post_meta($post_id, 'numero_inventario', true));
            break;
        case 'fecha_emision':
            echo esc_html(get_post_meta($post_id, 'fecha_emision', true));
            break;
        case 'numero_serie':
            echo esc_html(get_post_meta($post_id, 'numero_serie', true));
            break;
        case 'laboratorio':
            echo esc_html(get_post_meta($post_id, 'laboratorio', true));
            break;
        case 'qr_code':
            $thumbnail_id = get_post_thumbnail_id($post_id);
            if ($thumbnail_id) {
                $thumbnail_url = wp_get_attachment_url($thumbnail_id);
                echo '<a href="#" class="open-modal" data-url="' . esc_url($thumbnail_url) . '">';
                echo wp_get_attachment_image($thumbnail_id, array(50, 50));
                echo '</a>';
            }
            break;
    }
}
add_action('manage_certificado_posts_custom_column', 'mostrar_columnas_certificado', 10, 2);

// Ocultar el editor y el título en el tipo de post 'certificado'
function ocultar_editor_titulo_certificado() {
    remove_post_type_support('certificado', 'editor');
    remove_post_type_support('certificado', 'title');
}
add_action('init', 'ocultar_editor_titulo_certificado');

// Incluir CSS y JavaScript para el modal
function incluir_css_js_modal() {
    wp_enqueue_style('admin-styles', plugin_dir_url(__FILE__) . 'css/admin-styles.css');
    wp_enqueue_script('admin-scripts', plugin_dir_url(__FILE__) . 'js/admin-scripts.js', array('jquery'), null, true);
}
add_action('admin_enqueue_scripts', 'incluir_css_js_modal');
?>
