jQuery(document).ready(function ($) {
    var data       = (typeof ftsSmartSearchData !== 'undefined') ? ftsSmartSearchData : {};
    var ajaxUrl    = data.ajaxUrl || '';
    var nonce      = data.nonce || '';
    var archiveUrl = data.archiveUrl || '/trips/';
    var currency   = data.currency || '$';
    var i18n       = data.i18n || {};

    var supportsPointer = typeof window !== 'undefined' && !!window.PointerEvent;
    var triggerEvent = supportsPointer ? 'pointerup' : 'click';

    function fmtPrice(v) {
        var n = parseFloat(v);
        if (isNaN(n) || n <= 0) return '';
        return currency + n.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    }

    function getParts($wrapper) {
        return {
            $wrapper: $wrapper,
            $trigger: $wrapper.find('.fts-ss-trigger').first(),
            $tooltip: $wrapper.find('.fts-ss-tooltip').first(),
            $input: $wrapper.find('.fts-ss-input').first(),
            $results: $wrapper.find('.fts-ss-results').first()
        };
    }

    function getState($wrapper) {
        var st = $wrapper.data('ftsSsState');
        if (!st) {
            st = { xhr: null, timer: null };
            $wrapper.data('ftsSsState', st);
        }
        return st;
    }

    function hideResults($wrapper) {
        var parts = getParts($wrapper);
        if (parts.$results.length) parts.$results.removeClass('is-visible').empty();
    }

    function showLoading($wrapper) {
        var parts = getParts($wrapper);
        if (!parts.$results.length) return;
        parts.$results.html(
            '<div class="fts-hero-loading">' +
                '<div class="fts-hero-spinner"></div>' +
                '<span>' + (i18n.searching || 'Searching...') + '</span>' +
            '</div>'
        ).addClass('is-visible');
    }

    function showNoResults($wrapper) {
        var parts = getParts($wrapper);
        if (!parts.$results.length) return;
        parts.$results.html(
            '<div class="fts-hero-no-results">' +
                '<svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/><line x1="8" y1="8" x2="14" y2="14"/><line x1="14" y1="8" x2="8" y2="14"/></svg>' +
                '<p>' + (i18n.no_results || 'No trips found. Try different keywords.') + '</p>' +
            '</div>'
        ).addClass('is-visible');
    }

    function renderTrips($wrapper, data) {
        var parts = getParts($wrapper);
        if (!parts.$results.length) return;

        var trips = (data && data.trips) ? data.trips : [];
        var total = (data && data.total) ? data.total : 0;

        if (!trips.length) {
            showNoResults($wrapper);
            return;
        }

        var html = '';
        for (var i = 0; i < trips.length; i++) {
            var t = trips[i];

            var thumbHtml = t.thumbnail
                ? '<img src="' + t.thumbnail + '" alt="' + t.title + '" loading="lazy">'
                : '<div class="fts-hero-result-thumb-placeholder"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="m21 15-5-5L5 21"/></svg></div>';

            var metaParts = [];
            if (t.destination) {
                metaParts.push(
                    '<span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">' +
                    '<path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>' +
                    t.destination + '</span>'
                );
            }
            if (t.duration_text) {
                metaParts.push(
                    '<span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">' +
                    '<circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>' +
                    t.duration_text + '</span>'
                );
            }
            if (t.rating > 0) {
                metaParts.push(
                    '<span><svg viewBox="0 0 24 24" fill="currentColor" stroke="none">' +
                    '<path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>' +
                    parseFloat(t.rating).toFixed(1) + '</span>'
                );
            }

            var priceHtml = '';
            if (t.price > 0) {
                if (t.old_price > 0 && t.old_price > t.price) {
                    priceHtml += '<span class="fts-hero-result-old-price">' + fmtPrice(t.old_price) + '</span>';
                }
                priceHtml += '<span class="fts-hero-result-current-price">' + fmtPrice(t.price) + '</span>';
                priceHtml += '<span class="fts-hero-result-price-label">' + (i18n.per_person || '/person') + '</span>';
            }

            html +=
                '<a href="' + t.url + '" class="fts-hero-result-card" role="option">' +
                    '<div class="fts-hero-result-thumb">' + thumbHtml + '</div>' +
                    '<div class="fts-hero-result-info">' +
                        '<div class="fts-hero-result-title">' + t.title + '</div>' +
                        '<div class="fts-hero-result-meta">' + metaParts.join('') + '</div>' +
                    '</div>' +
                    (priceHtml ? '<div class="fts-hero-result-price">' + priceHtml + '</div>' : '') +
                '</a>';
        }

        if (total > trips.length) {
            var keyword = $.trim(parts.$input.val());
            var sep     = archiveUrl.indexOf('?') > -1 ? '&' : '?';
            var viewUrl = archiveUrl + sep + 's=' + encodeURIComponent(keyword) + '&post_type=trip';
            var viewAllText = (i18n.view_all || 'View All %s Results').replace('%s', total);

            html +=
                '<a href="' + viewUrl + '" class="fts-hero-view-all">' +
                    viewAllText +
                    ' <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>' +
                '</a>';
        }

        parts.$results.html(html).addClass('is-visible');
    }

    function doSearch($wrapper) {
        var parts = getParts($wrapper);
        if (!parts.$input.length || !parts.$results.length) return;
        if (!ajaxUrl || !nonce) return;

        var st = getState($wrapper);
        var keyword = $.trim(parts.$input.val());

        if (keyword.length < 2) {
            hideResults($wrapper);
            return;
        }

        if (st.xhr) st.xhr.abort();
        showLoading($wrapper);

        st.xhr = $.ajax({
            url: ajaxUrl,
            type: 'POST',
            data: {
                action: 'fts_hero_search',
                nonce: nonce,
                keyword: keyword
            },
            dataType: 'json'
        }).done(function (res) {
            st.xhr = null;
            if (res && res.success) {
                renderTrips($wrapper, res.data);
            } else {
                showNoResults($wrapper);
            }
        }).fail(function (_xhr, status) {
            st.xhr = null;
            if (status !== 'abort') showNoResults($wrapper);
        });
    }

    function setActive($wrapper, isActive) {
        var parts = getParts($wrapper);

        $wrapper.toggleClass('active', !!isActive);

        if (parts.$trigger.length) parts.$trigger.attr('aria-expanded', isActive ? 'true' : 'false');
        if (parts.$tooltip.length) parts.$tooltip.attr('aria-hidden', isActive ? 'false' : 'true');

        if (isActive) {
            setTimeout(function () {
                if (parts.$input.length) parts.$input.trigger('focus');
                if ($.trim(parts.$input.val()).length >= 2) doSearch($wrapper);
            }, 80);
        } else {
            var st = getState($wrapper);
            if (st.timer) {
                clearTimeout(st.timer);
                st.timer = null;
            }
            if (st.xhr) {
                st.xhr.abort();
                st.xhr = null;
            }
            hideResults($wrapper);
        }
    }

    $(document).on(triggerEvent, '.fts-ss-trigger', function (e) {
        e.preventDefault();
        e.stopPropagation();

        var $wrapper = $(this).closest('.fts-smart-search-wrapper');
        $('.fts-smart-search-wrapper').not($wrapper).each(function () {
            setActive($(this), false);
        });

        setActive($wrapper, !$wrapper.hasClass('active'));
    });

    $(document).on(triggerEvent, '.fts-ss-close-btn', function (e) {
        e.preventDefault();
        e.stopPropagation();

        var $wrapper = $(this).closest('.fts-smart-search-wrapper');
        setActive($wrapper, false);

        var $trigger = $wrapper.find('.fts-ss-trigger').first();
        if ($trigger.length) $trigger.trigger('focus');
    });

    $(document).on('click', function (e) {
        if (!$(e.target).closest('.fts-smart-search-wrapper').length) {
            $('.fts-smart-search-wrapper').each(function () {
                setActive($(this), false);
            });
        }
    });

    $(document).on('keydown', function (e) {
        var key = e.key || e.keyCode;
        if (key === 'Escape' || key === 'Esc' || key === 27) {
            $('.fts-smart-search-wrapper.active').each(function () {
                setActive($(this), false);
            });
        }
    });

    $(document).on('click pointerup', '.fts-ss-tooltip', function (e) {
        e.stopPropagation();
    });

    $(document).on('input', '.fts-smart-search-wrapper .fts-ss-input', function () {
        var $wrapper = $(this).closest('.fts-smart-search-wrapper');
        if (!$wrapper.hasClass('active')) return;

        var st = getState($wrapper);
        if (st.timer) clearTimeout(st.timer);
        st.timer = setTimeout(function () {
            doSearch($wrapper);
        }, 300);
    });

    $(document).on('focus', '.fts-smart-search-wrapper .fts-ss-input', function () {
        var $wrapper = $(this).closest('.fts-smart-search-wrapper');
        var parts = getParts($wrapper);
        if (!parts.$results.length) return;
        if ($.trim(parts.$input.val()).length >= 2 && parts.$results.children().length > 0) {
            parts.$results.addClass('is-visible');
        }
    });
});
