<?php 
$wcmamtx_license_settings    = (array) get_option('wcmamtx_license_settings');
$licensing_url               = 'https://api.envato.com/authorization?response_type=code&client_id=sysbasics-license-activation-jvykxxug&redirect_uri=https://www.sysbasics.com/verify-purchase/';
?> 

<table class="widefat wcmamtx_options_table">

	

	<tr>
		<td><label><?php echo esc_html__('Domain Validation Token','customize-my-account-for-woocommerce-pro'); ?></label>
		</td>
		<td>
			<input type="text" class="wcmamtx_license_key_input" data-toggle="toggle" data-size="xs" name="wcmamtx_license_settings[license_key]" value="<?php if (isset($wcmamtx_license_settings['license_key']) ) { echo $wcmamtx_license_settings['license_key']; } ?>" size="80">


			<?php

			$license_key = '';

			if (isset($wcmamtx_license_settings['license_key']) ) { 
				$license_key=$wcmamtx_license_settings['license_key']; 
			}

			
                  $activate_button_visible = 'no';
                  $domain_status = "inactive";
			
                  $input = $_SERVER['SERVER_NAME'];

   
			$input = trim($input, '/');


			if (!preg_match('#^http(s)?://#', $input)) {
				$input = 'http://' . $input;
			}

			$urlParts = parse_url($input);


			$domain_name = preg_replace('/^www\./', '', $urlParts['host']);

			echo '<table class="widefat sysbasics-license-table">';

			

                  echo '<tr><td>'.esc_html__('Domain','customize-my-account-for-woocommerce-pro').'</td><td>'.$domain_name.'</td></tr>';

                  if (!isset($license_key) || ($license_key == "")) {
                  	echo '<tr><td>'.esc_html__('Activate your token to enable auto updates.Please remember that envato item purchase code is different from domain validation token.','customize-my-account-for-woocommerce-pro').'</td></tr>';
                  }


                  $siteurl = wcmamtx_get_siteurl();

			

			$json_url = 'https://www.sysbasics.com/wp-json/wp/v2/check-validation?domain='.$domain_name.'&code='.$license_key.'&tid=31059126&siteurl='. $siteurl.'';

			

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_URL, $json_url);
			$result = curl_exec($ch);
			curl_close($ch);

			$obj = json_decode($result);
			$match_found = $obj->match_found;

			

			if (isset($match_found) && ($match_found == "yes")) {

				$detected_domain = $obj->domain;

				$item_id = $obj->item_id;

				if (($detected_domain == $domain_name) && ($item_id == 31059126)) {

					$supported_until = $obj->supported_until;

					 $usage_validity = $obj->usage_validity;

					 if ($usage_validity == 20500101) {
					 	$usage_validity_txt = esc_html__('Lifetime','customize-my-account-for-woocommerce-pro');
					 } else {
					 	$usage_validity_txt = date('Y-m-d',strtotime($usage_validity));
					 }



					$supported_until = date("d M Y",strtotime($supported_until));

					$domain_status = "active";

					delete_option('wcmamtx_install_e');

					add_option('wcmamtx_install_e','64');

					echo '<tr><td>'.esc_html__('Status','customize-my-account-for-woocommerce-pro').'</td><td>Active</td></tr>';

					echo '<tr><td>'.esc_html__('Support Expiry','customize-my-account-for-woocommerce-pro').'</td><td>'.$supported_until.'</td></tr>';

					echo '<tr><td>'.esc_html__('Usage Valid till','syscmafwpl').'</td><td>'.$usage_validity_txt.'</td></tr>';
				}

			} else {

				delete_option('wcmamtx_install_e');

				add_option('wcmamtx_install_e','32');

				$reason = $obj->reason;
				echo '<tr><td>'.esc_html__('Error','customize-my-account-for-woocommerce-pro').'</td><td>'.$reason.'</td></tr>';

				echo '<tr><td>'.esc_html__('Status','customize-my-account-for-woocommerce-pro').'</td><td>Inactive</td></tr>';
			}

             
             echo '</table>';
			

            $is_domain_validated = get_option("wcmamtx_domain_validated");



            if ((!isset($is_domain_validated) || ($is_domain_validated != "yes")) && ($activate_button_visible != 'yes') || (($domain_status == "inactive") )) {

            	if ($domain_status != "active") {
            		echo '<button href="#" class="btn btn-success wcmamtx_activate_license"><span class="dashicons dashicons-saved"></span>'.esc_html__('Activate','customize-my-account-for-woocommerce-pro').'</button>';
            	}
            	
            }      

			?>

			<a target="_blank" href="https://www.sysbasics.com/my-account/orders" class="sysbasics-token-page btn btn-warning"> 
			<span class="dashicons dashicons-admin-network sysbasics-manage-token"></span>              
				<?php 

				if ($domain_status == "active") {
                    echo esc_html__('Manage Tokens','customize-my-account-for-woocommerce-pro');
				} else {
					echo esc_html__('Get Validation Token','customize-my-account-for-woocommerce-pro');
				}
				?>
					
			</a>
			<?php

			if ((!isset($is_domain_validated) || ($is_domain_validated != "yes")) && ($activate_button_visible != 'yes') || (($domain_status == "inactive") )) {

            	if ($domain_status != "active") {
            		echo '&emsp;<a target="_blank" href="https://sysbasics.com/go/license-doc/" class="btn btn-success sysbasics-license-doc"><span class="dashicons dashicons-admin-settings"></span>&emsp;'.esc_html__('Documentation','customize-my-account-for-woocommerce-pro').'</a>&emsp;';

            		echo '<a href="https://codecanyon.net/user/sysbasics#contact" target="_blank" class="btn btn-primary sysbasics-license-doc"><span class="dashicons  dashicons-admin-tools"></span>&emsp;'.esc_html__('Contact Support','customize-my-account-for-woocommerce-pro').'</a>&emsp;';
            	}
            	
            }
            ?>	
			
			
		</td>
	</tr>

	
</table>
