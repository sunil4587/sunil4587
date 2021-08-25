(function( $ ) {
	'use strict';

	$(function() {

		// To avoid scope issues, use 'self' instead of 'this'
		// to reference this class from internal events and functions.

		var self = this;

		self.is_admin_preview = $('body').hasClass('wp-admin');

		self.events = {};

		self.init = function() {

			self.bindEvents();
			self.initBadgeSparkles();

			if(!self.is_admin_preview) {
				self.initVariationBadges();
				self.datePickers();
			}
		};

		self.bindEvents = function() {

			if(!self.is_admin_preview) {

				$(document.body).on("click", "input.xt_woopr_apply_discount", self.events.onApplyDiscount);

				$(document.body).on('show_variation', '.variations_form', function () {
					$(this).addClass('xt_woopr-variation-selected');
				});

				$(document.body).on('hide_variation', '.variations_form', function () {
					$(this).removeClass('xt_woopr-variation-selected');
				});
			}

			$( document.body ).on('xtfw_settings_preview_refreshed', function(evt, preview_id) {

				if(preview_id === 'xt_woopr_points_badge_preview') {

					self.initBadgeSparkles();
				}
			});
		};

		self.datePickers = function() {

			var $elements = $('.xt-datepicker, .xt-datepicker-wrap input');

			if($elements.length) {
				$elements.attr("autocomplete", "off").datepicker({
					dateFormat: 'yy-mm-dd'
				});
			}
		};

		self.initBadgeSparkles = function() {

			var $badges = $('.xt_woopr-pbadge-has-sparkles');

			if(!$badges.length) {
				return;
			}

			function randomPosition() {
				return (Math.floor(Math.random() * 90) + 10)+'%';
			}

			function animateSparkles() {

				$badges.each(function() {

					$(this).find('.xt_woopr-pbadge-sparkles').each(function() {

						$(this).css('top', randomPosition());
						$(this).css('left', randomPosition());
					});
				});

				setTimeout(function() {
					animateSparkles();
				}, 3000);
			}

			animateSparkles();
		};

		self.initVariationBadges = function() {

			if ( typeof($.fn.wc_variation_form) === 'function' ) {

				$( document.body).on('found_variation', '.variations_form', self.events.onFoundVariation );
				$( document.body).on('reset_data', '.variations_form', self.events.onResetVariation );
			}
		};

		/* XT Points & Rewards AJAX Apply Points Discount */
		self.events.onApplyDiscount = function(e) {

			e.preventDefault();

			var $container = $(this).closest(".xt_woopr_apply_discount_container");
			var $section = $container.closest(".xt-framework-notice");

			if ( $section.is( ".processing" ) ) {
				return false;
			}

			var discount = $container.find('.xt_woopr_apply_discount_amount').val();

			$section.addClass( "processing" ).block({message: null, overlayCSS: {background: "#fff", opacity: 0.6}});

			var data = {
				action: "xt_woopr_apply_discount",
				xt_woopr_apply_discount: 1,
				discount_amount: discount,
				security: $container.data("apply_coupon_nonce")
			};

			$.ajax({
				type:     "POST",
				url:      woocommerce_params.ajax_url,
				data:     data,
				success:  function() {

					$section.removeClass( "processing" ).unblock().fadeOut();

					$( document.body ).trigger("wc_update_cart");
					$( document.body ).trigger("update_checkout");
				},
				dataType: "html"
			});

			return false;

		};

		self.events.onFoundVariation = function(e, variation) {

			if(variation.points_earned_text) {

				var $badge_points = $(this).closest('.product').find('.xt_woopr-pbadge-points');

				if ($badge_points.length) {
					if($badge_points.data('points') === undefined) {
						$badge_points.data('points', $badge_points.text())
					}
					$badge_points.text(variation.points_earned_text);
				}
			}
		};

		self.events.onResetVariation = function() {

			var $badge_points = $(this).closest('.product').find('.xt_woopr-pbadge-points');

			if($badge_points.length && $badge_points.data('points') !== undefined) {

				var points = $badge_points.data('points');
				$badge_points.text(points);
			}
		};

		self.init();
	});

})( jQuery );