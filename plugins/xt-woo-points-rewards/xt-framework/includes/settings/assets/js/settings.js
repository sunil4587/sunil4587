;( function( $ ) {

    'use strict';

    var Settings = {

        form: null,
        stickyPreviews: [],

        init: function() {

            this.form = $(".xtfw-settings-form");

            this.initAccordion();
            this.initActionConfirm();
            this.initDatePickers();
            this.initColorPickers();
            this.initToolTips();
            this.initConditions();
            this.initPreviews();
            this.initAjaxSaveSettings();
        },

        initAccordion: function() {

            var self = this;

            if(self.form.length && self.form.find('.xtfw-settings-title').length) {

                var active = xtfw_settings.sub_id ? parseInt(xtfw_settings.sub_id) : 0;

                self.form.accordion({
                    header: ".xtfw-settings-title",
                    collapsible: true,
                    heightStyle: "content",
                    active: active,
                    icons: { "header": "dashicons-arrow-right-alt2", "activeHeader": "dashicons-arrow-down-alt2" },
                    activate: function( event, ui ) {

                        if(ui.newHeader.length) {
                            $([document.documentElement, document.body]).animate({
                                scrollTop: ui.newHeader.offset().top - 60
                            }, 400);

                            $(document.body).trigger('xtfw_settings_section_changed');
                        }
                    }
                });

                self.form.find('.ui-accordion-header-icon.ui-icon').removeClass('ui-icon').addClass('dashicons');

                self.form.find('.xtfw-settings-title').on('click', function() {

                    var sub_id = $(this).data('sub_id');
                    var url = location.href.split('&sub_id=')[0] + '&sub_id='+sub_id;
                    window.history.replaceState({}, document.title, url);

                    self.form.find('a.button').each(function() {
                        url = $(this).attr('href').split('&sub_id=')[0] + '&sub_id='+sub_id;
                        $(this).attr('href', url)
                    });

                    url = self.form.attr('action').split('?sub_id=')[0] + '?sub_id='+sub_id;
                    self.form.attr('action', url);

                });

            }
        },

        initActionConfirm: function() {

            var self = this;

            // action confirm popup
            $('a[data-confirm]').each( function() {

                var $button = $(this);

                $button.inlineConfirm({
                    message: $button.data('confirm'),
                    preventDefaultEvent: true,
                    showOriginalAction: true,
                    confirmCallback: function(e, button) {

                        self.processAction(button);
                    }
                });

            });
        },

        initDatePickers: function() {

            $( '.xtfw-datepicker' ).datepicker({
                dateFormat: 'yy-mm-dd',
                numberOfMonths: 1,
                showButtonPanel: true,
                showOn: 'button',
                buttonImage: xtfw_settings.assets_url + '/images/calendar.png',
                buttonImageOnly: true
            });
        },

        initColorPickers: function() {

            $('.xtfw-colorpicker').each(function() {

                var $picker = $(this);

                $picker.wpColorPicker({
                    change: function(event, ui) {

                        setTimeout(function() {
                            $(event.target).trigger('change', [ui.color]);
                        }, 20);
                    }
                });

                var $picker_holder = $picker.closest('.wp-picker-container').find('.wp-picker-holder');

                $picker_holder.on('mouseup', function() {
                    $picker.wpColorPicker('close');
                });
            });
        },

        initToolTips: function() {

            $( '.xtfw-help-tip' ).tipTip( {
                'attribute': 'data-tip',
                'fadeIn': 50,
                'fadeOut': 50,
                'delay': 200
            });
        },

        initConditions: function() {

            var self = this;

            self.applyConditions();

            $(document.body).on('change', '.xtfw-settings-form select, .xtfw-settings-form input[type="checkbox"], .xtfw-settings-form input[type="radio"]', function(e) {

                var id = $(this).attr('id');

                if(id) {

                    var field = self.findField(id);

                    if(field) {
                        self.applyConditions(field);
                    }
                }
            });
        },

        findField: function(id) {

            return xtfw_settings.fields.find(function(item) {
                return item.id === id;
            });
        },

        getFieldValue: function(field) {

            var $element = $('#' + field.id);

            if(field.type === 'checkbox') {
                return $element .prop("checked") === true ? 'yes' : 'no';
            }

            if(field.type === 'textarea') {
                return $element.html();
            }

            return $element .val();
        },

        applyConditions: function() {

            var self = this;
            var container;

            xtfw_settings.fields.forEach(function(field) {

                if(field.has_preview) {
                    field = field.has_preview;
                    container = $('#'+field.id).closest('.xtfw-settings-preview-sidebar').find('.xtfw-settings-preview, .xtfw-settings-preview-title');
                }else{
                    container = $('#'+field.id).closest('tr');
                }

                if(container.length) {

                    if (self.isFieldHidden(field)) {

                        container.hide();

                    } else {

                        container.show();
                    }
                }
            });
        },

        isFieldHidden: function(field) {

            var self = this;
            var hidden = false;

            if(field.conditions) {

                var conditions = field.conditions;
                var total = conditions.length;
                var passed = 0;

                conditions.forEach(function (condition) {

                    var targetField = self.findField(condition.id);

                    if(targetField) {

                        var targetFieldValue = self.getFieldValue(targetField);
                        var conditionValue = condition.value;

                        var conditionOperator = condition.operator || '===';

                        // If value is an object and we have a condition on a specific item
                        if(condition.item) {

                            // find the item
                            var valueItem = targetFieldValue.find(function(item) {

                                return item[condition.item.key] === condition.item.value;
                            });

                            // Override target value with the item value based on the condition key
                            targetFieldValue = valueItem[condition.item.conditionKey];
                        }

                        if (
                            (conditionOperator === '===' && targetFieldValue === conditionValue) ||
                            (conditionOperator === '!==' && targetFieldValue !== conditionValue) ||
                            (conditionOperator === '<' && targetFieldValue < conditionValue) ||
                            (conditionOperator === '>' && targetFieldValue > conditionValue) ||
                            (conditionOperator === '<=' && targetFieldValue <= conditionValue) ||
                            (conditionOperator === '>=' && targetFieldValue >= conditionValue) ||
                            (conditionOperator === 'in' && targetFieldValue.includes(conditionValue)) ||
                            (conditionOperator === 'not in' && !targetFieldValue.includes(conditionValue))
                        ) {
                            passed++;
                        }
                    }

                });

                if(total !== passed) {
                    hidden = true;
                }
            }

            return hidden;
        },

        initPreviews: function() {

            var self = this;

            var hasPreviews = !!xtfw_settings.fields.find(function(field) {
                return field.type && field.type === 'title' && field.has_preview;
            });

            if(hasPreviews) {

                self.generatePreviewStyles();
                self.initStickyPreviews();

                $(document.body).on('change', '.xtfw-has-output', function(e) {

                    self.generatePreviewStyles();
                });

                $(document.body).on('change', '[data-preview]', function(e) {

                    var preview = $(this).data('preview');

                    self.refreshPreview(preview);
                });

                $(window).on('resize', function() {

                    self.initStickyPreviews();
                });

                $(document.body).on('xtfw_settings_section_changed', function () {

                    self.initStickyPreviews();
                });


            }
        },

        generatePreviewStyles: function() {

            var self = this;
            var stylesheet_id = 'xtfw_settings-inline-css';
            var fields = xtfw_settings.fields.filter(function(field) {
                return field.type && field.output;
            });

            var css_data = {},
                css = '',
                element,
                property,
                properties,
                value_pattern,
                value;

            fields.forEach(function(field) {

                field.output.forEach(function(item) {

                    element = item.element;
                    property = item.property;
                    value_pattern = item.value_pattern ? item.value_pattern : null;
                    value = self.getFieldValue(field);

                    if(value_pattern && value_pattern !== '') {
                        value = value_pattern.replace('$', value);
                    }

                    if(!css_data.hasOwnProperty(element)) {
                        css_data[element] = {};
                    }

                    css_data[element][property] = value;
                });
            });

            Object.entries(css_data).forEach(function(data) {

                element = data[0];
                properties = data[1];

                css += element+'{';

                Object.entries(properties).forEach(function(item) {

                    property = item[0];
                    value = item[1];

                    if(value !== '') {
                        css += property+':'+value+';';
                    }
                });

                css += '}';
            });

            if ( $( '#'+stylesheet_id ).length ) {
                $( '#'+stylesheet_id ).remove();
            }

            $( 'head' ).append( '<style id="'+stylesheet_id+'">'+css+'</style>' );

        },

        refreshPreview: function(preview_id) {

            var self = this;

            var formData = new FormData(self.form.get(0));
            var $preview = $('#'+preview_id);
            var $spinner = $preview.find('.xtfw-spinner');

            formData.append('action', self.getAjaxAction('refresh_preview'));
            formData.append('preview', preview_id);

            $spinner.addClass('active');

            $.ajax({
                url: ajaxurl,
                enctype: 'multipart/form-data',
                type: 'post',
                data: formData,
                processData: false,  // Important!
                contentType: false,
                cache: false,
                timeout: 600000

            }).done(function(response) {

                if(response.success) {

                    $preview.html(response.preview);

                    $(document.body).trigger('xtfw_settings_preview_refreshed', [preview_id]);

                    self.generatePreviewStyles();
                }

                $spinner.removeClass('active');
            });
        },

        destroyStickyPreviews: function() {

            var self = this;

            self.stickyPreviews.forEach(function(sidebar) {
                sidebar.destroy();
            });

            self.stickyPreviews = [];
        },

        initStickyPreviews: function() {

            var self = this;

            self.destroyStickyPreviews();

            if($(window).width() >= 600) {

                $('.xtfw-settings-preview-sidebar').each(function() {

                    self.stickyPreviews.push(new StickySidebar($(this).get(0), {
                        containerSelector: '.xtfw-settings-preview-section',
                        topSpacing: 50,
                        bottomSpacing: 50
                    }));
                });
            }
        },

        getAjaxAction: function(action) {

            return xtfw_settings.ajax_action.toString().replace( '%%action%%', action );
        },

        eventType: function(id) {

            return xtfw_settings.prefix+'_'+id+'_js_action';
        },

        initAjaxSaveSettings: function() {

            var self = this;

            $(document.body).on('submit', '.xtfw-settings-form', function(evt) {

                evt.preventDefault();
                self.saveSettings(this);
            });
        },

        saveSettings: function(form) {

            var self = this;
            var $form = $(form);
            var $button = $form.find('#xtfw-save-settings');

            if(self.isButtonLoading($button)) {
                return;
            }

            self.removeNotices();
            self.startButtonLoading($button);

            var formData = new FormData(form);

            formData.append('action', self.getAjaxAction('save_settings'));

            $.ajax({
                url: ajaxurl,
                enctype: 'multipart/form-data',
                type: 'post',
                data: formData,
                processData: false,  // Important!
                contentType: false,
                cache: false,
                timeout: 600000

            }).done(function(response) {

                if(response.success) {

                    $(document.body).trigger('xtfw_settings_saved');
                }

                self.showNotices(response.notices);

            }).always(function() {

                self.endButtonLoading($button);
            });
        },

        processAction: function($button) {

            var self = this;

            if(self.isButtonLoading($button)) {
                return;
            }

            self.removeNotices();

            var action_id = $button.attr('id');
            var process_action = self.getAjaxAction('process_action');

            var callback = function() {
                self.endButtonLoading($button);
            };

            self.startButtonLoading($button);

            // Allow XT plugins to override the default action
            if ( true === $( document.body ).triggerHandler( action_id + '_js_action', [ $button, callback ] ) ) {
                return;
            }

            var data = {
                action: process_action,
                action_id: action_id
            };

            if($button.data('data')) {
                data = $.extend(data, $button.data('data'));
            }

            $.ajax({
                url: ajaxurl,
                type: 'post',
                data: data,
                cache: false,
                timeout: 600000

            }).done(function(response) {

                self.showNotices(response.notices);

            }).always(function() {

                self.endButtonLoading($button);
            });
        },

        isButtonLoading: function($button) {
            return $button.hasClass('processing')
        },

        startButtonLoading: function($button) {
            $button.addClass('processing').find('.xtfw-spinner').addClass('active');
        },

        endButtonLoading: function($button) {
            $button.removeClass('processing').find('.xtfw-spinner').removeClass('active');
        },

        removeNotices: function() {

            $('.xt-framework-admin-notice').remove();
        },

        showNotices: function(notices) {

            if(notices !== '') {
                $('.xtfw-admin-tabs-header h1').after($(notices));
                $('html,body').animate({scrollTop: $(notices).offset().top - 20}, 500);
            }
        }

    };

    $(document).ready(function() {

        Settings.init();
    });

})( jQuery );	