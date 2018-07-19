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
	$('#pmpro_sws_custom_sale_dates').change(function(){
		if(this.checked)
			$('#pmpro_sws_custom_date_select').show();
		else
			$('#pmpro_sws_custom_date_select').hide();
		});
	$('#pmpro_sws_custom_banner_title').change(function(){
		if(this.checked)
			$('#pmpro_sws_custom_title_select').show();
		else
			$('#pmpro_sws_custom_title_select').hide();
		});
});
