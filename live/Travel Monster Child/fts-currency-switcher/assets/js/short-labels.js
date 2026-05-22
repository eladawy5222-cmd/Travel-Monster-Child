/* =====================================================
   FTS Currency Switcher - Short codes only
   Overrides the WTE Currency Converter plugin which
   appends full currency names (and "(Default)" label)
   to the dropdown list items. We re-render those list
   items with only the short "symbol + code" string.
   ===================================================== */
(function ($) {
    'use strict';

    if (typeof $ !== 'function') {
        return;
    }

    function ftsApplyShortCurrencyLabels() {
        $('.wte-cc-currency-list-display').each(function () {
            var $select     = $(this);
            var $niceSelect = $select.next('.nice-select');

            if (!$niceSelect.length) {
                return;
            }

            $select.find('option').each(function () {
                var $option     = $(this);
                var optionValue = $option.attr('value');
                var displayText = ($option.data('display') || $option.text() || '').toString().trim();

                if (!displayText) {
                    return;
                }

                var $listItem = $niceSelect.find('.list .option[data-value="' + optionValue + '"]');
                if ($listItem.length && $listItem.text().trim() !== displayText) {
                    $listItem.text(displayText);
                }
            });

            var $selectedOption = $select.find('option:selected');
            var currentText     = ($selectedOption.data('display') || $selectedOption.text() || '').toString().trim();
            if (currentText) {
                var $current = $niceSelect.find('.current');
                if ($current.length && $current.text().trim() !== currentText) {
                    $current.text(currentText);
                }
            }
        });
    }

    $(function () {
        ftsApplyShortCurrencyLabels();
        setTimeout(ftsApplyShortCurrencyLabels, 150);
        setTimeout(ftsApplyShortCurrencyLabels, 600);
        setTimeout(ftsApplyShortCurrencyLabels, 1500);
    });

    $(window).on('load', function () {
        ftsApplyShortCurrencyLabels();
    });

    $(document).on('click', '.wte-cc-currency-list-display + .nice-select', function () {
        setTimeout(ftsApplyShortCurrencyLabels, 0);
    });
})(window.jQuery);
