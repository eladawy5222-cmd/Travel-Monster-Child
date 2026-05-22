<?php
/**
 * Gallery V2 - 3-Photo Grid with Lightbox + Video
 * Variables provided by layout-controller.php via extract()
 */
if ( ! defined( 'ABSPATH' ) ) exit;

if ( empty( $all_images ) ) return;

$fts_gallery_mode = isset( $fts_gallery_mode ) ? (string) $fts_gallery_mode : '';
$fts_is_hero = ( $fts_gallery_mode === 'hero' );
?>

<?php if ( $fts_is_hero ) : ?>
<?php
    $hgg_total = count( $all_images );
    $hgg_show_grid = ( $hgg_total >= 3 );
    $hgg_grid_images = $hgg_show_grid ? array_slice( $all_images, 0, 5 ) : array();
    $hgg_side_images = $hgg_show_grid ? array_slice( $hgg_grid_images, 1 ) : array();
    $hgg_side_count  = count( $hgg_side_images );
    $hgg_cls = 'fts-v2-hero-gallery-grid';
    if ( $hgg_side_count <= 2 ) $hgg_cls .= ' fts-v2-hgg--cols1';
    elseif ( $hgg_side_count === 3 ) $hgg_cls .= ' fts-v2-hgg--cols3';
?>
<div class="fts-v2-hero-gallery<?php echo $hgg_show_grid ? '' : ' fts-v2-hero-gallery--slider-only'; ?>" data-count="<?php echo esc_attr( $hgg_total ); ?>">
    <?php if ( $hgg_show_grid ) : ?>
    <div class="<?php echo esc_attr( $hgg_cls ); ?>">
        <?php
            $hgg_main_id  = $hgg_grid_images[0];
            $hgg_main_url = wp_get_attachment_image_url( $hgg_main_id, 'large' );
            $hgg_main_alt = get_post_meta( $hgg_main_id, '_wp_attachment_image_alt', true ) ?: get_the_title();
        ?>
        <div class="fts-v2-hgg-main fts-v2-hgg-cell" data-index="0" data-action="lightbox">
            <img src="<?php echo esc_url( $hgg_main_url ); ?>" alt="<?php echo esc_attr( $hgg_main_alt ); ?>" decoding="async" loading="eager" fetchpriority="high">
            <?php if ( ! empty( $video_url ) ) : ?>
            <div class="fts-v2-video-play" data-video="<?php echo esc_url( $video_url ); ?>">
                <div class="fts-v2-play-circle">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="white"><polygon points="5 3 19 12 5 21 5 3"/></svg>
                </div>
            </div>
            <?php endif; ?>
        </div>
        <div class="fts-v2-hgg-side-grid">
            <?php foreach ( $hgg_side_images as $hgg_si => $hgg_sid ) :
                $hgg_surl = wp_get_attachment_image_url( $hgg_sid, 'medium_large' );
                if ( ! $hgg_surl ) continue;
                $hgg_salt = get_post_meta( $hgg_sid, '_wp_attachment_image_alt', true ) ?: get_the_title();
                $hgg_idx  = $hgg_si + 1;
                $hgg_is_last = ( $hgg_si === $hgg_side_count - 1 );
            ?>
            <div class="fts-v2-hgg-cell<?php echo $hgg_is_last ? ' fts-v2-hgg-cell--last' : ''; ?>" data-index="<?php echo esc_attr( $hgg_idx ); ?>" data-action="lightbox">
                <img src="<?php echo esc_url( $hgg_surl ); ?>" alt="<?php echo esc_attr( $hgg_salt ); ?>" decoding="async" loading="lazy">
                <?php if ( $hgg_is_last && $hgg_total > 5 ) : ?>
                <button type="button" class="fts-v2-hero-gallery-viewall fts-v2-hgg-viewall" data-action="lightbox" aria-label="<?php echo esc_attr__( 'View all photos', 'fts' ); ?>">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                    <span><?php echo esc_html__( 'View all', 'fts' ); ?></span>
                </button>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <div class="fts-v2-gallery-slider" data-count="<?php echo esc_attr( $hgg_total ); ?>">
        <div class="fts-v2-gallery-slider-track">
            <?php foreach ( $all_images as $sidx => $simg_id ) :
                $simg_url = wp_get_attachment_image_url( $simg_id, 'large' );
                if ( ! $simg_url ) continue;
                $salt = get_post_meta( $simg_id, '_wp_attachment_image_alt', true ) ?: get_the_title();
            ?>
            <div class="fts-v2-gallery-slide" data-index="<?php echo esc_attr( $sidx ); ?>">
                <img src="<?php echo esc_url( $simg_url ); ?>" alt="<?php echo esc_attr( $salt ); ?>" decoding="async" loading="<?php echo $sidx === 0 ? 'eager' : 'lazy'; ?>"<?php echo $sidx === 0 ? ' fetchpriority="high"' : ''; ?>>
                <?php if ( $sidx === 0 && ! empty( $video_url ) ) : ?>
                <div class="fts-v2-video-play" data-video="<?php echo esc_url( $video_url ); ?>">
                    <div class="fts-v2-play-circle">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="white"><polygon points="5 3 19 12 5 21 5 3"/></svg>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="fts-v2-gallery-dots" aria-hidden="true"></div>
    </div>
    <?php if ( ! $hgg_show_grid || $hgg_total <= 5 ) : ?>
    <button type="button" class="fts-v2-hero-gallery-viewall" data-action="lightbox" aria-label="<?php echo esc_attr__( 'View all photos', 'fts' ); ?>">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
        <span><?php echo esc_html( sprintf( _n( 'View %d photo', 'View %d photos', $hgg_total, 'fts' ), $hgg_total ) ); ?></span>
    </button>
    <?php endif; ?>
</div>
<?php else : ?>
<div class="fts-v2-gallery-section">
    <div class="fts-v2-container">
        <?php if ( count( $all_images ) > 1 ) : ?>
        <div class="fts-v2-gallery-slider" data-count="<?php echo esc_attr( count( $all_images ) ); ?>">
            <div class="fts-v2-gallery-slider-track">
                <?php foreach ( $all_images as $sidx => $simg_id ) :
                    $simg_url = wp_get_attachment_image_url( $simg_id, 'large' );
                    if ( ! $simg_url ) continue;
                    $salt = get_post_meta( $simg_id, '_wp_attachment_image_alt', true ) ?: get_the_title();
                ?>
                <div class="fts-v2-gallery-slide" data-index="<?php echo esc_attr( $sidx ); ?>">
                    <img src="<?php echo esc_url( $simg_url ); ?>" alt="<?php echo esc_attr( $salt ); ?>" decoding="async" loading="eager"<?php echo $sidx === 0 ? ' fetchpriority="high"' : ''; ?>>
                    <?php if ( $sidx === 0 && ! empty( $video_url ) ) : ?>
                    <div class="fts-v2-video-play" data-video="<?php echo esc_url( $video_url ); ?>">
                        <div class="fts-v2-play-circle">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="white"><polygon points="5 3 19 12 5 21 5 3"/></svg>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="fts-v2-gallery-dots" aria-hidden="true"></div>
        </div>
        <?php endif; ?>

        <div class="fts-v2-gallery-grid fts-v2-gallery-count-<?php echo intval( $grid_count ); ?>">
            <?php foreach ( $grid_images as $idx => $img_id ) :
                $img_url = wp_get_attachment_image_url( $img_id, 'large' );
                if ( ! $img_url ) continue;
                $alt = get_post_meta( $img_id, '_wp_attachment_image_alt', true ) ?: get_the_title();
                $cls = $idx === 0 ? 'fts-v2-gallery-main' : 'fts-v2-gallery-side';
            ?>
            <div class="fts-v2-gallery-cell <?php echo esc_attr( $cls ); ?>" data-index="<?php echo esc_attr( $idx ); ?>">
                <img src="<?php echo esc_url( $img_url ); ?>" alt="<?php echo esc_attr( $alt ); ?>" decoding="async" loading="<?php echo $idx === 0 ? 'eager' : 'lazy'; ?>">

                <?php if ( $idx === 0 && ! empty( $video_url ) ) : ?>
                <div class="fts-v2-video-play" data-video="<?php echo esc_url( $video_url ); ?>">
                    <div class="fts-v2-play-circle">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="white"><polygon points="5 3 19 12 5 21 5 3"/></svg>
                    </div>
                </div>
                <?php endif; ?>

                <?php if ( $extra_photos > 0 && $idx === $grid_count - 1 ) : ?>
                <div class="fts-v2-gallery-more" data-action="lightbox">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                    <span><?php echo esc_html( sprintf( _n( '+%d photo', '+%d photos', $extra_photos, 'fts' ), $extra_photos ) ); ?></span>
                </div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Lightbox -->
<div id="fts-v2-lightbox" class="fts-v2-lightbox">
    <div class="fts-v2-lb-close" role="button" tabindex="0" aria-label="<?php echo esc_attr__( 'Close', 'fts' ); ?>">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2.5" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
    </div>
    <div class="fts-v2-lb-prev" role="button" tabindex="0" aria-label="<?php echo esc_attr__( 'Previous', 'fts' ); ?>">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
    </div>
    <div class="fts-v2-lb-next" role="button" tabindex="0" aria-label="<?php echo esc_attr__( 'Next', 'fts' ); ?>">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
    </div>
    <div class="fts-v2-lb-stage">
        <img id="fts-v2-lb-img" src="" alt="">
    </div>
    <div class="fts-v2-lb-footer">
        <div class="fts-v2-lb-title" id="fts-v2-lb-title"></div>
        <div class="fts-v2-lb-counter" id="fts-v2-lb-counter"></div>
        <div class="fts-v2-lb-thumbs" id="fts-v2-lb-thumbs"></div>
    </div>
</div>

<!-- Video Modal -->
<div id="fts-v2-video-modal" class="fts-v2-video-modal">
    <button class="fts-v2-video-close">&times;</button>
    <div class="fts-v2-video-wrap">
        <iframe id="fts-v2-video-iframe" src="" allowfullscreen title="<?php echo esc_attr__( 'Trip video', 'fts' ); ?>"></iframe>
    </div>
</div>
