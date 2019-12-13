(function ($) {
    var wpYes = {
        init: function () {
            $('.wp-yes--color-picker').wpColorPicker();

            $('.wp-yes--browse-media').on('click', wpYes.onBrowseMedia);
            $('.wp-yes--remove-media').on('click', wpYes.onRemoveMedia);
            $('.wp-yes--nav-tab-wrapper a').on('click', wpYes.onClickTab);

            var getActiveTab = wpYes.getActiveTab();

            if (getActiveTab && $('.wp-yes--nav-tab-wrapper a' + getActiveTab + '-tab').length) {
                $('.wp-yes--nav-tab-wrapper a' + getActiveTab + '-tab').click();
            } else {
                $('.wp-yes--nav-tab-wrapper a:first').click();
            }

            var fields = _.reduce(wpYesVar.fields, function (fields, field) {
                return _.union(fields, _.keys(field.conditional));
            });

            _.each(fields, function (field) {
                $('.wp-yes--field[name ="' + field + '"]').off('change', wpYes.onFieldChange);
                $('.wp-yes--field[name ="' + field + '"]').on('change', wpYes.onFieldChange);
                $('.wp-yes--field[name ="' + field + '"]').change();
            });
        },
        onFieldChange: function (event) {
            var fields = _.filter(wpYesVar.fields, function (field) {
                return field.conditional && _.has(field.conditional, $(event.target).attr('name'));
            });

            _.each(fields, function (field) {
                var isHidden = false;

                _.each(field.conditional, function (fieldDependencyCompare, fieldDependency) {
                    var $fieldDependency = $('.wp-yes--field[name ="' + fieldDependency + '"]:checked');

                    if (!$fieldDependency.length) {
                        $fieldDependency = $('.wp-yes--field[name ="' + fieldDependency + '"]');
                    }

                    if (_.isArray(fieldDependencyCompare) && _.indexOf(fieldDependencyCompare, $fieldDependency.val()) === -1) {
                        isHidden = true;
                    }

                    if (_.isString(fieldDependencyCompare) && fieldDependencyCompare !== $fieldDependency.val()) {
                        isHidden = true;
                    }

                    if (isHidden) {
                        return true;
                    }
                });

                if (isHidden) {
                    $field = $('.wp-yes--field--wrap[data-id="' + field.name + '"]').closest('tr').hide();
                } else {
                    $field = $('.wp-yes--field--wrap[data-id="' + field.name + '"]').closest('tr').fadeIn();
                }
            });
        },
        onBrowseMedia: function (event) {
            event.preventDefault();

            var self = $(this);

            // Create the media frame.
            var mediaModal = (wp.media.frames.file_frame = wp.media({
                multiple: false
            }));

            mediaModal.on('select', function () {
                var attachment = mediaModal.state().get('selection').first().toJSON();

                self.closest('td').find('input[type="text"]').val(attachment.url);
            });

            // Finally, open the modal
            mediaModal.open();
        },
        onRemoveMedia: function (event) {
            event.preventDefault();

            $(this).closest('td').find('input[type="text"]').val('');
        },
        onClickTab: function (event) {
            event.preventDefault();

            $('.wp-yes--nav-tab-wrapper a').removeClass('nav-tab-active');

            $(this).addClass('nav-tab-active').blur();

            $('.wp-yes--tab-group').hide();

            $($(this).attr('href')).fadeIn();

            wpYes.setActiveTab($(this).attr('href'));
        },
        getActiveTab: function () {
            if (typeof localStorage !== 'undefined') {
                return localStorage.getItem('wp-yes--active-tab-' + wpYesVar.menuSlug);
            }

            return '';
        },
        setActiveTab: function (tab) {
            if (typeof localStorage !== 'undefined') {
                localStorage.setItem('wp-yes--active-tab-' + wpYesVar.menuSlug, tab);
            }
        }
    };

    $(document).ready(wpYes.init);
})(jQuery);