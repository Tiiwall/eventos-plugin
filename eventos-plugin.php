<?php
/**
 * Plugin Name: Eventos Plugin
 * Plugin URI:  https://lbc-global.com
 * Description: Cria e apresenta eventos com Custom Post Type, campos ACF e shortcode [eventos_futuros].
 * Version:     1.0.0
 * Author:      Tiago Bernardo
 * Text Domain: eventos-plugin
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/* 
   CUSTOM POST TYPE
*/
function ep_register_post_type() {
    $labels = array(
        'name'               => __( 'Eventos', 'eventos-plugin' ),
        'singular_name'      => __( 'Evento', 'eventos-plugin' ),
        'menu_name'          => __( 'Eventos', 'eventos-plugin' ),
        'add_new'            => __( 'Adicionar Evento', 'eventos-plugin' ),
        'add_new_item'       => __( 'Adicionar Novo Evento', 'eventos-plugin' ),
        'edit_item'          => __( 'Editar Evento', 'eventos-plugin' ),
        'new_item'           => __( 'Novo Evento', 'eventos-plugin' ),
        'view_item'          => __( 'Ver Evento', 'eventos-plugin' ),
        'search_items'       => __( 'Pesquisar Eventos', 'eventos-plugin' ),
        'not_found'          => __( 'Nenhum evento encontrado', 'eventos-plugin' ),
        'not_found_in_trash' => __( 'Nenhum evento no lixo', 'eventos-plugin' ),
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'has_archive'        => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array( 'slug' => 'eventos' ),
        'capability_type'    => 'post',
        'supports'           => array( 'title', 'editor', 'thumbnail' ),
        'menu_icon'          => 'dashicons-calendar-alt',
        'menu_position'      => 5,
    );

    register_post_type( 'evento', $args );
}
add_action( 'init', 'ep_register_post_type' );


/* 
   2. ACF eventos
   */
function ep_register_acf_fields() {
    if ( ! function_exists( 'acf_add_local_field_group' ) ) {
        return;
    }

    acf_add_local_field_group( array(
        'key'      => 'group_evento_detalhes',
        'title'    => 'Detalhes do Evento',
        'fields'   => array(
            array(
                'key'          => 'field_evento_data',
                'label'        => 'Data do Evento',
                'name'         => 'evento_data',
                'type'         => 'date_picker',
                'display_format' => 'd/m/Y',
                'return_format'  => 'Ymd',
                'first_day'      => 1,
                'required'       => 1,
            ),
            array(
                'key'      => 'field_evento_local',
                'label'    => 'Local',
                'name'     => 'evento_local',
                'type'     => 'text',
                'required' => 1,
            ),
            array(
                'key'      => 'field_evento_organizador',
                'label'    => 'Organizador',
                'name'     => 'evento_organizador',
                'type'     => 'text',
                'required' => 1,
            ),
        ),
        'location' => array(
            array(
                array(
                    'param'    => 'post_type',
                    'operator' => '==',
                    'value'    => 'evento',
                ),
            ),
        ),
        'menu_order'            => 0,
        'position'              => 'normal',
        'style'                 => 'default',
        'label_placement'       => 'top',
        'instruction_placement' => 'label',
        'active'                => true,
    ) );
}
add_action( 'acf/init', 'ep_register_acf_fields' );


/* 
   SHORTCODE  [eventos_futuros limite=X]
  */
function ep_shortcode_eventos_futuros( $atts ) {
    $atts = shortcode_atts(
        array( 'limite' => -1 ),
        $atts,
        'eventos_futuros'
    );

    $hoje = date( 'Ymd' );

    $query_args = array(
        'post_type'      => 'evento',
        'post_status'    => 'publish',
        'posts_per_page' => intval( $atts['limite'] ),
        'meta_key'       => 'evento_data',
        'orderby'        => 'meta_value',
        'order'          => 'ASC',
        'meta_query'     => array(
            array(
                'key'     => 'evento_data',
                'value'   => $hoje,
                'compare' => '>=',
                'type'    => 'DATE',
            ),
        ),
    );

    $eventos = new WP_Query( $query_args );

    if ( ! $eventos->have_posts() ) {
        return '<p class="lbc-no-events">Não existem eventos futuros de momento.</p>';
    }

    // Enqueue Bootstrap 
    if ( ! wp_style_is( 'bootstrap', 'enqueued' ) ) {
        wp_enqueue_style( 'bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css', array(), '5.3.3' );
    }
    wp_enqueue_style( 'lbc-eventos', plugin_dir_url( __FILE__ ) . 'assets/eventos.css', array( 'bootstrap' ), '1.0.0' );

    ob_start();
    ?>
    <div class="lbc-eventos-grid container-fluid px-0">
        <div class="row g-4">
        <?php while ( $eventos->have_posts() ) : $eventos->the_post();
            $data        = get_field( 'evento_data' );
            $local       = get_field( 'evento_local' );
            $organizador = get_field( 'evento_organizador' );
            $data_fmt    = $data ? DateTime::createFromFormat( 'Ymd', $data )->format( 'd/m/Y' ) : '';
        ?>
            <div class="col-12 col-md-6 col-lg-4">
                <a href="<?php the_permalink(); ?>" class="lbc-card-link text-decoration-none">
                    <div class="lbc-card card h-100 shadow-sm border-0">
                        <?php if ( has_post_thumbnail() ) : ?>
                            <div class="lbc-card-img-wrap overflow-hidden">
                                <?php the_post_thumbnail( 'medium_large', array( 'class' => 'lbc-card-img card-img-top w-100 object-fit-cover' ) ); ?>
                            </div>
                        <?php endif; ?>
                        <div class="card-body d-flex flex-column p-4">
                            <h3 class="lbc-card-title h5 mb-3"><?php the_title(); ?></h3>
                            <ul class="lbc-card-meta list-unstyled mb-0 mt-auto">
                                <?php if ( $data_fmt ) : ?>
                                <li class="lbc-meta-item d-flex align-items-center gap-2 mb-2">
                                    <span class="lbc-meta-icon dashicons dashicons-calendar-alt" aria-hidden="true"></span>
                                    <span><?php echo esc_html( $data_fmt ); ?></span>
                                </li>
                                <?php endif; ?>
                                <?php if ( $local ) : ?>
                                <li class="lbc-meta-item d-flex align-items-center gap-2 mb-2">
                                    <span class="lbc-meta-icon dashicons dashicons-location" aria-hidden="true"></span>
                                    <span><?php echo esc_html( $local ); ?></span>
                                </li>
                                <?php endif; ?>
                                <?php if ( $organizador ) : ?>
                                <li class="lbc-meta-item d-flex align-items-center gap-2">
                                    <span class="lbc-meta-icon dashicons dashicons-admin-users" aria-hidden="true"></span>
                                    <span><?php echo esc_html( $organizador ); ?></span>
                                </li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>
                </a>
            </div>
        <?php endwhile; wp_reset_postdata(); ?>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode( 'eventos_futuros', 'ep_shortcode_eventos_futuros' );


/* 
   Template para eventos
 */
function ep_single_template( $template ) {
    if ( is_singular( 'evento' ) ) {
        $plugin_template = plugin_dir_path( __FILE__ ) . 'templates/single-evento.php';
        if ( file_exists( $plugin_template ) ) {
            return $plugin_template;
        }
    }
    return $template;
}
add_filter( 'single_template', 'ep_single_template' );


/* 
   5. Enqueue Dashicons
   */
function ep_enqueue_assets() {
    if ( is_singular( 'evento' ) || has_shortcode( get_post()->post_content ?? '', 'eventos_futuros' ) ) {
        wp_enqueue_style( 'dashicons' );
    }
}
add_action( 'wp_enqueue_scripts', 'ep_enqueue_assets' );


/* 
   6. Flush rewrite rules -- prevenir erros 404
   */
function ep_activate() {
    ep_register_post_type();
    flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'ep_activate' );

function ep_deactivate() {
    flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'ep_deactivate' );
