/* eslint-disable */
window.$ = jQuery;

var new_widget_id = null;
var new_widget_title = null;

jQuery(function($) {
    if ($('#poststuff').length > 0) {
        var adminbar = $('#wpadminbar').outerHeight();
        var height   = $('#submitdiv').outerHeight();

        // Inject holder
        $('#side-sortables').prepend('<div class="spacer" style="top: ' + adminbar + 'px; height: ' + height + 'px;"></div>');

        $(window).scroll(function() {
            var sidebar  = $('#side-sortables').offset().top - $('#wpadminbar').outerHeight();

            if ($(document).scrollTop() > sidebar) {
                // Set submit div as fixed
                $('#side-sortables').addClass('fixed');
                $('.spacer').css({
                    'height': $('#submitdiv').outerHeight() + 'px',
                    'top': $('#wpadminbar').outerHeight() + 'px'
                });
            } else {
                // Clear fixed position
                $('#side-sortables').removeClass('fixed');
            }
        });
    }

    // Automatically open blocks field
    if ($('div[data-key=field_57222a09e15e1] .edit-field').length > 0) {
        $('div[data-key=field_57222a09e15e1] .edit-field').eq(0).click();
    }
});

if (typeof acf !== 'undefined') {
    acf.addAction('ready', function() {
        // Add Promote to Widget button
        $('#acf-group_572229fc5045c .acf-field-57222a09e15e1 > .acf-input > .acf-flexible-content > .values > .layout').each(function() {
            // Promote button
            if ($(this).data('layout') !== 'widget') {
                $(this).find('.-collapse').eq(0).before('<a class="acf-icon -promote small light acf-js-tooltip" href="#" title="Promote to widget"></a>');
            }

            // Quick jump button
            if ($(this).data('layout') === 'widget') {
                $(this).find('.-collapse').eq(0).before('<a class="acf-icon -visit small light acf-js-tooltip" href="#" title="Go to widget"></a>');
            }
        });

        // Go to Widget functionality
        $('body').on('click', '.acf-icon.-visit', function(e) {
            e.preventDefault();

            var widgetId = $(this).parents('.layout').find('.acf-field-582531244a4aa select').val();

            if (widgetId != '') {
                window.location.href = 'post.php?post=' + widgetId + '&action=edit';
            }
        });

        // Promote to Widget functionality
        $('body').on('click', '.acf-icon.-promote', function(e) {
            e.preventDefault();

            var layout = $(this).parents('.layout');
            var data = 'action=promote_to_widget';
            var title = layout.find(".acf-fc-layout-handle").find('.block-title').text().trim();

            title = window.prompt('Widget Title', $('.editor-post-title__input').val() + ' - ' + title);

            if (title !== null) {
                var array = layout.find(':input');

                array.each(function(index, value) {
                    if ($(this).attr('name')) {
                        if ((['checkbox', 'radio'].indexOf($(this).attr('type')) > 0
                            && $(this).is(':checked'))
                            || ['checkbox', 'radio'].indexOf($(this).attr('type')) === -1) {
                            var fieldLabel = $('[name="' + value.name + '"]').parents('.acf-field');
                            var name = fieldLabel.data('name');
                            var key = fieldLabel.data('key');

                            if (key === 'field_5c903f6d4d821') {
                                var parent = fieldLabel.parents('.acf-field').data('name');
                                data += '&' + value.name + '[parent]=' + parent;
                            }

                            data += '&' + value.name + '[label]=' + name;
                            data += '&' + value.name + '[value]=' + value.value;
                        }
                    }
                });

                data += '&title=' + title;

                layout.find('.acf-icon.-promote').eq(0).removeClass('-promote').addClass('-loading fa-spin');

                $.ajax({
                    url: ajaxurl,
                    method: 'post',
                    cache: false,
                    dataType: 'json',
                    data: data,
                }).done(function(response) {
                    new_widget_id = response.post_id;
                    new_widget_title = title;

                    var mainBlock = acf.getFields({ key: 'field_57222a09e15e1' })[0];

                    mainBlock.add({
                        layout: 'widget',
                        before: layout,
                    });

                    mainBlock.removeLayout(layout);
                });
            }
        });

        // Visibility classes
        if ($('div[data-key=field_57222a09e15e1]').length > 0) {
            $('div[data-key=field_57222a09e15e1]').find('.layout').each(function() {
                $(this).removeClass('disabled scheduled');

                if ($(this).find('.block-visibility').hasClass('disabled')) {
                    $(this).addClass('disabled');
                }

                if ($(this).find('.block-visibility').hasClass('scheduled')) {
                    $(this).addClass('scheduled');
                }
            });
        }

        // Visibility classes
        $('body').on('change', 'div[data-key=field_58a7c3c6e0ffd] select', function() {
            var parent = $(this).closest('.layout');
            var title = parent.find('.block-visibility');

            parent.removeClass('disabled scheduled');
            title.html('');

            if ($(this).val() == 'disable') {
                parent.addClass('disabled');
                title.html(' - <em>Disabled</em>');
            }

            if ($(this).val() == 'schedule') {
                parent.addClass('scheduled');
                title.html(' - <em>Scheduled</em>');
            }
        });

        // Friendly Title (Block)
        $('body').on('keyup', 'div[data-key=field_59486376fc2dd] input', function() {
            var title = $(this).closest('.layout').find('.block-title');

            if ($(this).val().length > 0) {
                title.html($(this).val() + ' (' + title.data('title') + ')');
            } else {
                title.html(title.data('title'));
            }
        });

        // Friendly Title (Widget)
        $('body').on('change', 'div[data-key=field_582531244a4aa] select', function() {
            var title = $(this).closest('.layout').find('.block-title');

            if ($(this).val().length > 0) {
                title.html($(this).find('option:selected').text() + ' (' + title.data('title') + ')');
            } else {
                title.html(title.data('title'));
            }
        });

        // Expand / Collapse
        $('body').on('click', 'a[data-name=acf-fc-collapse]', function(e) {
            e.preventDefault();

            var parent = $(this).closest('.acf-field-setting-fc_layout');
            var id = parent.data('id');
            var container = parent.find('.acf-field-list-wrap').first();
            var count = container.find('.acf-field-list').first().find('.acf-field-object').length;

            if ($(this).text() === 'Collapse') {
                container.slideUp();
                parent.find('.acf-input').first().append('<div class="acf-field-count">' + count + ' Fields Collapsed</div>');
                $(this).text('Expand');
                localStorage.setItem('fc-' + id, 'collapsed');
            } else {
                container.slideDown();
                parent.find('.acf-field-count').first().remove();
                $(this).text('Collapse');
                localStorage.removeItem('fc-' + id);
            }
        });
    });

    acf.addAction('append', function($el) {
        if($el.parents('#acf-group_572229fc5045c').length && $el.parent().closest('.layout').length == 0) {
            // Add Promote to Widget button
            if ($el.data('layout') !== 'widget') {
                $el.find('.-collapse').eq(0).before('<a class="acf-icon -promote small light acf-js-tooltip" href="#" title="Promote to widget"></a>');
            }

            // quick jump button
            if ($el.data('layout') === 'widget') {
                $el.find('.-collapse').before('<a class="acf-icon -visit small light acf-js-tooltip" href="#" title="Go to widget"></a>');

                if (new_widget_id !== null) {
                    $el.find('select').append('<option value="' + new_widget_id + '">' + new_widget_title + '<option>');
                    $el.find('select').val(new_widget_id);
                    new_widget_id = null;
                    new_widget_title = null;
                }
            }
        }
    });
}
