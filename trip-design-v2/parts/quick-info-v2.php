<?php
/**
 * Quick Info V2 - Sticky Tabs Navigation
 * Variables provided by layout-controller.php via extract()
 *
 * @var bool                              $use_frontend_view_model
 * @var array<int,array<string,mixed>>    $vm_quick_info
 * @var array<int,array<string,mixed>>    $trip_facts_items
 * @var array<string,string>              $tab_sections
 */
if ( ! defined( 'ABSPATH' ) ) exit;

$fts_use_frontend_vm = ! empty( $use_frontend_view_model );
$fts_vm_quick_info = ( isset( $vm_quick_info ) && is_array( $vm_quick_info ) ) ? $vm_quick_info : array();
$fts_quick_info_items = array();

if ( $fts_use_frontend_vm && ! empty( $fts_vm_quick_info ) ) {
    foreach ( $fts_vm_quick_info as $fts_qi_index => $fts_qi_item ) {
        if ( ! is_array( $fts_qi_item ) ) {
            continue;
        }

        $fts_qi_label = isset( $fts_qi_item['label'] ) && is_scalar( $fts_qi_item['label'] ) ? trim( (string) $fts_qi_item['label'] ) : '';
        $fts_qi_value = isset( $fts_qi_item['value'] ) && is_scalar( $fts_qi_item['value'] ) ? trim( (string) $fts_qi_item['value'] ) : '';
        if ( $fts_qi_label === '' || $fts_qi_value === '' ) {
            continue;
        }

        $fts_qi_icon_key = isset( $fts_qi_item['icon'] ) && is_scalar( $fts_qi_item['icon'] ) ? strtolower( trim( (string) $fts_qi_item['icon'] ) ) : '';
        $fts_qi_icon_map = array(
            'clock'    => 'fa-clock-o',
            'pickup'   => 'fa-map-marker',
            'language' => 'fa-language',
            'meal'     => 'fa-cutlery',
            'vehicle'  => 'fa-car',
            'car'      => 'fa-car',
            'users'    => 'fa-users',
            'group'    => 'fa-users',
            'map-pin'  => 'fa-map-marker',
            'location' => 'fa-map-marker',
            'calendar' => 'fa-calendar',
            'shield'   => 'fa-shield',
            'check'    => 'fa-check-circle',
            'landmark' => 'fa-university',
            'ticket'   => 'fa-ticket',
            'info'     => 'fa-info-circle',
        );
        $fts_qi_icon = isset( $fts_qi_icon_map[ $fts_qi_icon_key ] ) ? $fts_qi_icon_map[ $fts_qi_icon_key ] : 'fa-info-circle';

        $fts_qi_has_priority = isset( $fts_qi_item['priority'] ) && is_numeric( $fts_qi_item['priority'] );
        $fts_quick_info_items[] = array(
            'label'        => $fts_qi_label,
            'value'        => $fts_qi_value,
            'icon'         => $fts_qi_icon,
            '_has_priority'=> $fts_qi_has_priority,
            '_priority'    => $fts_qi_has_priority ? intval( $fts_qi_item['priority'] ) : PHP_INT_MAX,
            '_order'       => intval( $fts_qi_index ),
        );
    }

    if ( ! empty( $fts_quick_info_items ) ) {
        usort( $fts_quick_info_items, function( $a, $b ) {
            $a_has_priority = ! empty( $a['_has_priority'] );
            $b_has_priority = ! empty( $b['_has_priority'] );

            if ( $a_has_priority && $b_has_priority ) {
                if ( $a['_priority'] !== $b['_priority'] ) {
                    return $a['_priority'] <=> $b['_priority'];
                }
            } elseif ( $a_has_priority !== $b_has_priority ) {
                return $a_has_priority ? -1 : 1;
            }

            return $a['_order'] <=> $b['_order'];
        } );
    }
}

if ( empty( $fts_quick_info_items ) && isset( $trip_facts_items ) && is_array( $trip_facts_items ) ) {
    foreach ( $trip_facts_items as $fts_fact_item ) {
        if ( ! is_array( $fts_fact_item ) ) {
            continue;
        }

        $fts_fact_label = isset( $fts_fact_item['label'] ) && is_scalar( $fts_fact_item['label'] ) ? trim( (string) $fts_fact_item['label'] ) : '';
        $fts_fact_value = isset( $fts_fact_item['value'] ) && is_scalar( $fts_fact_item['value'] ) ? trim( (string) $fts_fact_item['value'] ) : '';
        if ( $fts_fact_label === '' || $fts_fact_value === '' ) {
            continue;
        }

        $fts_fact_icon = isset( $fts_fact_item['icon'] ) && is_scalar( $fts_fact_item['icon'] ) ? trim( (string) $fts_fact_item['icon'] ) : 'fa-info-circle';
        if ( $fts_fact_icon === '' ) {
            $fts_fact_icon = 'fa-info-circle';
        }

        $fts_quick_info_items[] = array(
            'label' => $fts_fact_label,
            'value' => $fts_fact_value,
            'icon'  => $fts_fact_icon,
        );
    }
}
?>

<?php if ( ! empty( $fts_quick_info_items ) ) : ?>
<div class="fts-v2-quick-bar">
    <div class="fts-v2-container">
        <div class="fts-v2-quick-bar-inner">
            <div class="fts-v2-quick-text">
                <ul class="fts-v2-facts-list">
                    <?php foreach ( $fts_quick_info_items as $fts_qi_render_item ) : ?>
                    <li class="fts-v2-fact">
                        <span class="fts-v2-fact-icon" aria-hidden="true"><i class="fa <?php echo esc_attr( $fts_qi_render_item['icon'] ); ?>"></i></span>
                        <span class="fts-v2-fact-text">
                            <span class="fts-v2-fact-label"><?php echo esc_html( $fts_qi_render_item['label'] ); ?></span>
                            <span class="fts-v2-fact-value"><?php echo esc_html( $fts_qi_render_item['value'] ); ?></span>
                        </span>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

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
