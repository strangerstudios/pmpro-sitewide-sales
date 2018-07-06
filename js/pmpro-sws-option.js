jQuery( document ).ready(function($) {
	$("#pmpro_sws_discount_code_select").selectWoo();
	$("#pmpro_sws_landing_page_select").selectWoo();
	$("#pmpro_sws_use_banner_select").selectWoo();
	$("#pmpro_sws_hide_levels_select").selectWoo();

	$(".pmpro_sws_option").change(function() {
		window.onbeforeunload = function() {
    	return true;
		};
	});
	$("#pmpro_sws_options").submit(function() {
		window.onbeforeunload = null;
	});
});