<?php
/**
 * Quick Info V2 - Sticky Tabs Navigation
 * Variables provided by layout-controller.php via extract()
 */
if ( ! defined( 'ABSPATH' ) ) exit;
?>

<!-- Sticky Tabs Navigation -->
<div class="fts-v2-tabs-nav" id="fts-v2-tabs-nav">
    <div class="fts-v2-container">
        <div class="fts-v2-tabs-scroll">
            <?php foreach ( $tab_sections as $id => $label ) : ?>
                <a href="#fts-v2-sec-<?php echo esc_attr( $id ); ?>" class="fts-v2-tab-link" data-section="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $label ); ?></a>
            <?php endforeach; ?>
        </div>
    </div>
</div>
