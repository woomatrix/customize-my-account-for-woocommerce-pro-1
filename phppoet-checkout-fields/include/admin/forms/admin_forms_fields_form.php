<?php        
global $woocommerce;


$countries    = new WC_Countries();



$additional_settings  = (array) get_option('syscmafwpl_forms_settings');
$additional_settings  = array_filter($additional_settings);
$core_fields          = '';
$country_fields       = '';
$address2_field       = '';

$requiredadditional_slugs = '';

if (isset($additional_settings) && (sizeof($additional_settings) >= 1)) { 
	$conditional_fields_dropdown = $additional_settings;
} else {
	$conditional_fields_dropdown = array();
}

$noticerowno3 = 1;


?>

<center>

	<div class="alert alert-primary" role="alert">
		<?php echo esc_html__('Click on Add Form Field button to add new field Form.To manage existing fields visit settings tab.To Add new fields visit Edit Account Fields tab','customize-my-account-for-woocommerce-pro'); ?>
	</div>
	<div class="panel-group syscmafwpl-sortable-list" id="accordion" >
		<?php if (isset($additional_settings) && (sizeof($additional_settings) >= 1)) { 
			foreach ($additional_settings as $key =>$field) { 
				$this->show_fields_form2($conditional_fields_dropdown,$key,$field,$noticerowno3,$this->additional_settings_key,$requiredadditional_slugs,$core_fields,$country_fields,$address2_field);
				$noticerowno3++;
			} 
		} 
		?>

	</div>
	<div class="buttondiv">
		<?php 
		global $woocommerce;
		$checkout_url = '#';
		$checkout_url = wc_get_checkout_url();
		?>	  
		<button type="button" href="#" data-etype="additional" id="wcmamtx_add_field2" class="btn btn-primary" >
			<span class="dashicons dashicons-insert"></span>
			<?php echo esc_html__('Add Fields Form','customize-my-account-for-woocommerce-pro'); ?>
		</button>

		

		<?php do_action( 'syscmafwpl_add_author_links' ); ?>
		
	</div>
	</center> <?php
	
$this->show_new_form($conditional_fields_dropdown,$this->additional_settings_key,$country_fields);