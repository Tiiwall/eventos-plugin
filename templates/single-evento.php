<?php
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! wp_style_is( 'bootstrap', 'enqueued' ) ) {
    wp_enqueue_style( 'bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css', array(), '5.3.3' );
}
wp_enqueue_style( 'dashicons' );
wp_enqueue_style( 'lbc-eventos', plugin_dir_url( dirname( __FILE__ ) ) . 'assets/eventos.css', array(), '1.0.0' );

get_header();

while ( have_posts() ) : the_post();

    $data        = get_field( 'evento_data' );
    $local       = get_field( 'evento_local' );
    $organizador = get_field( 'evento_organizador' );
    $data_fmt    = $data ? DateTime::createFromFormat( 'Ymd', $data )->format( 'd/m/Y' ) : '';

?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">

<main id="lbc-single-evento" class="lbc-single-evento py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-9 col-xl-8">

                <nav aria-label="breadcrumb" class="mb-4">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Inicio</a></li>
                        <li class="breadcrumb-item"><a href="<?php echo esc_url( get_post_type_archive_link( 'evento' ) ); ?>">Eventos</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><?php the_title(); ?></li>
                    </ol>
                </nav>

                <h1 class="lbc-single-title display-5 fw-bold mb-4"><?php the_title(); ?></h1>

                <div class="lbc-single-meta d-flex flex-wrap gap-4 mb-5 p-4 rounded-3 bg-light">
                    <?php if ( $data_fmt ) : ?>
                    <div class="lbc-meta-item d-flex align-items-center gap-2">
                        <span class="lbc-meta-icon-wrap d-flex align-items-center justify-content-center rounded-circle">
                            <span class="dashicons dashicons-calendar-alt fs-5" aria-hidden="true"></span>
                        </span>
                        <div>
                            <small class="text-muted d-block lh-1 mb-1">Data</small>
                            <strong><?php echo esc_html( $data_fmt ); ?></strong>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if ( $local ) : ?>
                    <div class="lbc-meta-item d-flex align-items-center gap-2">
                        <span class="lbc-meta-icon-wrap d-flex align-items-center justify-content-center rounded-circle">
                            <span class="dashicons dashicons-location fs-5" aria-hidden="true"></span>
                        </span>
                        <div>
                            <small class="text-muted d-block lh-1 mb-1">Local</small>
                            <strong><?php echo esc_html( $local ); ?></strong>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if ( $organizador ) : ?>
                    <div class="lbc-meta-item d-flex align-items-center gap-2">
                        <span class="lbc-meta-icon-wrap d-flex align-items-center justify-content-center rounded-circle">
                            <span class="dashicons dashicons-admin-users fs-5" aria-hidden="true"></span>
                        </span>
                        <div>
                            <small class="text-muted d-block lh-1 mb-1">Organizador</small>
                            <strong><?php echo esc_html( $organizador ); ?></strong>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <?php if ( has_post_thumbnail() ) : ?>
                <figure class="lbc-single-thumbnail mb-5">
                    <?php the_post_thumbnail( 'large', array(
                        'class' => 'lbc-featured-img img-fluid rounded-3 w-100 object-fit-cover shadow-sm',
                        'alt'   => get_the_title(),
                    ) ); ?>
                </figure>
                <?php endif; ?>

                <div class="lbc-single-content lbc-content">
                    <?php the_content(); ?>
                </div>

                <div class="lbc-single-nav d-flex justify-content-between align-items-center mt-5 pt-4 border-top">
                    <a href="<?php echo esc_url( get_post_type_archive_link( 'evento' ) ); ?>" class="btn btn-outline-secondary">
                        <span class="dashicons dashicons-arrow-left-alt me-1" aria-hidden="true"></span>
                        Todos os Eventos
                    </a>
                    <div class="d-flex gap-2">
                        <?php
                        $prev = get_previous_post();
                        $next = get_next_post();
                        if ( $prev ) : ?>
                        <a href="<?php echo esc_url( get_permalink( $prev ) ); ?>" class="btn btn-outline-primary" title="<?php echo esc_attr( get_the_title( $prev ) ); ?>">
                            <span class="dashicons dashicons-arrow-left-alt2" aria-hidden="true"></span>
                        </a>
                        <?php endif; if ( $next ) : ?>
                        <a href="<?php echo esc_url( get_permalink( $next ) ); ?>" class="btn btn-outline-primary" title="<?php echo esc_attr( get_the_title( $next ) ); ?>">
                            <span class="dashicons dashicons-arrow-right-alt2" aria-hidden="true"></span>
                        </a>
                        <?php endif; ?>
                    </div>
                </div>

            </div>
        </div>
    </div>
</main>

<?php
endwhile;

get_footer();
