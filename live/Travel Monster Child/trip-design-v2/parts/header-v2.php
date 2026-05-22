<?php
/**
 * Header V2 - Slim page header: Breadcrumb + Title + Rating + Share
 * Variables provided by layout-controller.php via extract()
 */
if ( ! defined( 'ABSPATH' ) ) exit;
?>

<div class="fts-v2-page-header">
    <div class="fts-v2-container">

        <nav class="fts-v2-breadcrumbs">
            <a href="<?php echo esc_url( home_url() ); ?>"><?php echo esc_html__( 'Home', 'fts' ); ?></a>
            <?php foreach ( $dest_chain as $dc ) : ?>
                <span class="fts-v2-bc-sep">/</span>
                <a href="<?php echo esc_url( $dc['url'] ); ?>"><?php echo esc_html( $dc['name'] ); ?></a>
            <?php endforeach; ?>
            <?php if ( $last_crumb ) : ?>
                <span class="fts-v2-bc-sep">/</span>
                <span class="fts-v2-bc-current"><?php echo esc_html( $last_crumb ); ?></span>
            <?php endif; ?>
        </nav>

        <div class="fts-v2-ph-title-row">
            <h1 class="fts-v2-trip-title"><?php the_title(); ?></h1>

            <div class="fts-v2-ph-meta-row">
                <?php if ( $avg_rating > 0 ) : ?>
                <div class="fts-v2-hero-rating" aria-label="<?php echo esc_attr__( 'Customer rating', 'fts' ); ?>">
                    <div class="fts-v2-stars-inline" aria-hidden="true">
                        <?php for ( $i = 1; $i <= 5; $i++ ) : ?>
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="<?php echo $i <= round( $avg_rating ) ? '#FF8C00' : 'none'; ?>" stroke="#FF8C00" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                        <?php endfor; ?>
                    </div>
                    <strong><?php echo number_format( $avg_rating, 1 ); ?></strong>
                    <?php if ( $review_count > 0 ) : ?>
                    <a href="#fts-v2-sec-reviews" class="fts-v2-hero-reviews-link"><?php echo esc_html( sprintf( _n( '%s review', '%s reviews', $review_count, 'fts' ), number_format_i18n( $review_count ) ) ); ?></a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>

    </div>
</div>
