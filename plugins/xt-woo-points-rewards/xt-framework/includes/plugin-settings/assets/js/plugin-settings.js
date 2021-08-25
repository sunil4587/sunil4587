;( function( $ ) {

    'use strict';

    var XTFW_Plugin_Settings = {

        init: function() {

            this.initAccordion();
            this.initActionConfirm();
            this.initDatePickers();
            this.initToolTips();
            this.initSelect();
        },

        initAccordion: function() {

            if($( ".xtfw-settings-form" ).length) {

                var $form = $(".xtfw-settings-form");

                var split_url = location.href.split('&sub_id=');

                if(split_url.length > 1) {
                    var sub_id = split_url[1];
                    var url = $form.attr('action').split('?sub_id')[0] + '?sub_id='+sub_id;
                    $form.attr('action', url);
                }

                // Wrap form table
                $form.find('.form-table').each(function() {

                    $(this).wrap('<div class="xtfw-settings-panel"></div>');
                });

                $form.find('h2 + div').each(function(sub_id) {

                    var $h2 = $(this).prev('h2');
                    $h2.replaceWith('<div class="xtfw-settings-title" data-sub_id="'+sub_id+'">' + $h2.html() +'</div>');

                    $(this).appendTo($(this).next('.xtfw-settings-panel'));
                });


                var active = xtfw_plugin_settings.sub_id ? parseInt(xtfw_plugin_settings.sub_id) : 0;

                $form.accordion({
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
                        }
                    }
                });

                $form.find('.ui-accordion-header-icon.ui-icon').removeClass('ui-icon').addClass('dashicons');

                $form.find('.xtfw-settings-title').on('click', function() {

                    var sub_id = $(this).data('sub_id');
                    var url = location.href.split('&sub_id=')[0] + '&sub_id='+sub_id;
                    window.history.replaceState({}, document.title, url);

                    $form.find('a.button').each(function() {
                        url = $(this).attr('href').split('&sub_id=')[0] + '&sub_id='+sub_id;
                        $(this).attr('href', url)
                    });

                    url = $form.attr('action').split('?sub_id=')[0] + '?sub_id='+sub_id;
                    $form.attr('action', url);

                });

            }
        },

        initActionConfirm: function() {

            // action confirm popup
            $( 'a[data-confirm]' ).click( function( e ) {
                if ( ! confirm( $(this).data('confirm') ) ) {
                    e.preventDefault();
                }
            } );
        },

        initDatePickers: function() {

            $( '.xtfw-datepicker' ).datepicker({
                dateFormat: 'yy-mm-dd',
                numberOfMonths: 1,
                showButtonPanel: true,
                showOn: 'button',
                buttonImage: xtfw_plugin_settings.assets_url + '/images/calendar.png',
                buttonImageOnly: true
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

        initSelect: function() {

            $( '.xtfw-select' ).select2();
        }

    };


    $(document).ready(function() {

        XTFW_Plugin_Settings.init();
    });

})( jQuery );	