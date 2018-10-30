jQuery( document ).ready(function($) {
	// multiselects
	$("#pmpro_sws_discount_code_select").selectWoo();
	$("#pmpro_sws_landing_page_select").selectWoo();
	$("#pmpro_sws_use_banner_select").selectWoo();
	$("#pmpro_sws_hide_levels_select").selectWoo();
	$("#pmpro_sws_upsell_levels").selectWoo();

	// removing some buttons from the edit post page for our CPT
	jQuery('.wp-editor-tabs').remove();
	jQuery('#insert-media-button').remove();

	// make sure save all settings buttons don't prompt the leave site alert
	$('input[type=submit]').click(function(){
		$(window).off( 'beforeunload.edit-post' );
	});

	// toggling the discount code input layout
	$('#pmpro_sws_discount_code_select').change(function(){
		$('#pmpro_sws_after_discount_code_select').hide();
	});

	// toggling the landing page input layout
	$('#pmpro_sws_landing_page_select').change(function(){
		$('#pmpro_sws_after_landing_page_select').hide();
	});

	// toggling the banner settings and banner CSS hint
	function pmpro_sws_toggle_banner_settings() {
		var banner = $('#pmpro_sws_use_banner_select').val();
		if(banner.length < 1 || banner == 'no') {
			$('#pmpro_sws_banner_options').hide();
			$('#pmpro_sws_css_selectors_description').hide();
			$('.pmpro_sws_banner_css_selectors').hide();
		} else {
			$('#pmpro_sws_css_selectors_description').show();
			$('.pmpro_sws_banner_css_selectors').hide();
			$('.pmpro_sws_banner_css_selectors[data-pmprosws-banner='+banner+']').show();
			$('#pmpro_sws_banner_options').show();
		}
	}
	$('#pmpro_sws_use_banner_select').change(function(){
		pmpro_sws_toggle_banner_settings();
	});
	pmpro_sws_toggle_banner_settings();

	// toggling the upsell settings
	$('#pmpro_sws_upsell_enabled').change(function(){
		if(this.checked)
			$('.pmpro_sws_upsell_settings').show();
		else
			$('.pmpro_sws_upsell_settings').hide();
		});
});
