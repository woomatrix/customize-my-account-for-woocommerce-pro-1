<?php

/**
 *  Menu items - Add "Custom sub-menu" in menu item render output
 *  if menu item has class "menu-item-target"
 */
add_filter( 'walker_nav_menu_start_el', 'sysbasics_menu_item_custom_output_premium', 10, 4 );


if ( !class_exists('wcmamtx_nav_metabox')) {
    class wcmamtx_nav_metabox {
        public function add_nav_menu_meta_boxes() {
            add_meta_box(
                'wcmamtx_nav_link_premium',
                __('SysBasics My Account Navigation','customize-my-account-for-woocommerce-pro'),
                array( $this, 'wcmamtx_nav_menu_link'),
                'nav-menus',
                'side',
                'high'
            );

            add_action( 'wp_nav_menu_item_custom_fields', array( $this, 'menu_item_custom_fields' ), 10, 2 );
            add_action( 'wp_update_nav_menu_item', array( $this, 'save_menu_item_custom_fields' ), 10, 2 );
           
        }



        public function save_menu_item_custom_fields( $menu_id, $menu_item_db_id ) {
            if ( isset( $_REQUEST['wcmamtx-currency-nonce'] ) && wp_verify_nonce( sanitize_key( $_REQUEST['wcmamtx-currency-nonce'] ), 'wcmamtx-currency-check-nonce' ) ) {
                $is_show_flag_in_menu_item            = isset( $_POST['show-flag'] ) ? sanitize_text_field( $_POST['show-flag'] ) : 0;
                $is_show_currency_name_in_menu_item   = isset( $_POST['show-currency-name'] ) ? sanitize_text_field( $_POST['show-currency-name'] ) : 0;
                $is_show_currency_symbol_in_menu_item = isset( $_POST['show-currency-symbol'] ) ? sanitize_text_field( $_POST['show-currency-symbol'] ) : 0;
                $is_show_currency_code_in_menu_item   = isset( $_POST['show-currency-code'] ) ? sanitize_text_field( $_POST['show-currency-code'] ) : 0;
                $menu_item_size                       = isset( $_POST['menu-item-size'] ) ? sanitize_text_field( $_POST['menu-item-size'] ) : 'small';

                update_option( 'wcmamtx_currency_show_flag_in_menu_item', $is_show_flag_in_menu_item );
                update_option( 'wcmamtx_currency_show_currency_name_in_menu_item', $is_show_currency_name_in_menu_item );
                update_option( 'wcmamtx_currency_show_currency_symbol_in_menu_item', $is_show_currency_symbol_in_menu_item );
                update_option( 'wcmamtx_currency_show_currency_code_in_menu_item', $is_show_currency_code_in_menu_item );
                update_option( 'wcmamtx_currency_menu_item_size', $menu_item_size );
            }

        }

        public function menu_item_custom_fields( $item_id, $item ) {
            if ( 'wcmamtx_nav_link_premium' === $item->post_name || 'SysBasics My Account Navigation' === $item->post_title ) {

                include 'menuItemCustomFields.php';
            }
        }
        
        public function wcmamtx_nav_menu_link() {

            include 'menuitem.php';
        }
    }
}

$custom_nav = new wcmamtx_nav_metabox;

add_action('admin_init', array($custom_nav, 'add_nav_menu_meta_boxes'));

	

if (!class_exists('wcmamtx_add_settings_page_class')) {

class wcmamtx_add_settings_page_class {
	
	

	private $wcmamtx_plugin_options_key    = 'wcmamtx_plugin_options';
	private $wcmamtx_notices_settings_page = 'wcmamtx_advanced_settings';
	private $wcmamtx_order_settings_page   = 'wcmamtx_order_settings';
	private $wcmamtx_order_actions_page    = 'wcmamtx_order_actions';
    private $wcmamtx_download_columns_page = 'wcmamtx_download_columns';
    private $wcmamtx_avatar_settings_page  = 'wcmamtx_avatar_settings';
    private $wcmamtx_license_settings      = 'wcmamtx_license_settings';
	private $wcmamtx_plugin_settings_tab   = array();
	

	
	public function __construct() {
		
		add_action( 'admin_init', array( $this, 'wcmamtx_register_settings_settings' ) );
		add_action( 'admin_menu', array( $this, 'add_admin_menus' ) ,100);
		add_action( 'admin_enqueue_scripts', array($this, 'wcmamtx_register_admin_scripts'));
		add_action( 'admin_enqueue_scripts', array($this, 'wcmamtx_load_admin_menu_style'));
        add_action( 'wp_ajax_restore_my_account_tabs', array( $this, 'restore_my_account_tabs' ) );
        add_action( 'wp_ajax_restore_my_account_order', array( $this, 'restore_my_account_order' ) );
        add_action( 'wp_ajax_restore_my_account_download', array( $this, 'restore_my_account_download' ) );
        add_action( 'wp_ajax_wcmamtxadmin_add_new_template', array( $this, 'wcmamtxadmin_add_new_template' ) );
        add_action( 'wp_ajax_get_elementor_templates', array( $this, 'wcmamtx_get_posts_ajax_callback' ) );
        add_action( 'admin_post_nds_form_response_endpoint', array( $this, 'add_endpoint_form_response' ));
        add_action( 'admin_post_nds_form_response_column', array( $this, 'add_column_form_response' ));
        add_action( 'admin_post_nds_form_response_download', array( $this, 'add_download_form_response' ));
		add_action( 'admin_post_nds_form_response_action', array( $this, 'add_action_form_response' ));
        add_action( 'wp_ajax_wcmamtxadmin_get_users_ajax', array( $this, 'wcmamtxadmin_get_users_ajax_function' ) );
        add_action( 'wp_ajax_wcmamtx_check_entire_validation', array( $this, 'wcmamtx_check_entire_validation_function' ) );
        add_action( 'admin_notices', array( $this, 'wcmamtx_add_admin_notices' ) );
    }


    public function wcmamtx_add_admin_notices() {
            ?>
            <div class="notice notice-info">
                <p>
                    <?php
                    /* translators: %s for a tag */
                    echo ''.esc_html__( 'You are using unlicensed version of SysBasics Customize My Account Pro', 'pcfme' ).' <a href="https://www.sysbasics.com/product/woocommerce-customize-my-account-pro/" target="_blank">' . esc_html__( 'Buy Pro version', 'pcfme' ) . '</a> '.esc_html__( 'to make it licensed and unlock all features', 'pcfme' ).'';
                    ?>
                </p>
            </div>
            <?php
    }


    public function wcmamtx_check_entire_validation_function() {



        

        $wcmamtx_act_date  = get_option('wcmamtx_act_date');

        $return['force_activation'] = 'no';
        $return['act_date'] = $wcmamtx_act_date;

        $date_today = date("Ymd");

        if (!isset($wcmamtx_act_date) || ($wcmamtx_act_date == "")) {
            update_option('wcmamtx_act_date',$date_today);
        }



        $wcmamtx_act_date  = date('Y-m-d',strtotime($wcmamtx_act_date));

        $tld             = date('Y-m-d');

        $wcmamtx_act_date = new DateTime($wcmamtx_act_date);
        $tld = new DateTime($tld);
        $wcmamtx_interval = $wcmamtx_act_date->diff($tld);

        $days_used = $wcmamtx_interval->days;

        if ($days_used < 03 ) {
            $return['force_activation'] = 'no';

            $return['ruling'] = 01;

             

            die;

        } else {

            $wcmamtx_license_settings    = (array) get_option('wcmamtx_license_settings');



            $license_key = '';

            if (isset($wcmamtx_license_settings['license_key']) ) { 
                $license_key=$wcmamtx_license_settings['license_key']; 
            }

            if ($license_key == "") {

                $return['force_activation'] = 'yes';

                update_option('wcmamtx_install_e','32');

                $return['ruling'] = 02;

                delete_option('wcmamtx_install_e');

                add_option('wcmamtx_install_e','32');

                $return['wcmamtx_install_e'] = get_option('wcmamtx_install_e');

               

                die;

            }

            
           
            $domain_status = "inactive";
            
            $input = $_SERVER['SERVER_NAME'];

   
            $input = trim($input, '/');


            if (!preg_match('#^http(s)?://#', $input)) {
                $input = 'http://' . $input;
            }

            $urlParts = parse_url($input);


            $domain_name = preg_replace('/^www\./', '', $urlParts['host']);



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

            $usg_validity = isset($obj->usage_validity) ? $obj->usage_validity : 20500101;

            $usage_valid_till = $usg_validity;

            

            if (isset($match_found) && ($match_found == "yes")) {

                $date_today = date("Ymd");

                if ($date_today < $usage_valid_till ) {

                    $return['force_activation'] = 'no';

                    $return['usage_valid_till'] = $usg_validity;

                    

                    $return['ruling'] = 03;

                    delete_option('wcmamtx_install_e');

                    add_option('wcmamtx_install_e','64');

                    $return['wcmamtx_install_e'] = get_option('wcmamtx_install_e');


                    die;

                } else {

                    $return['force_activation'] = 'yes';

                    delete_option('wcmamtx_install_e');

                    add_option('wcmamtx_install_e','32');

                    

                    $return['ruling'] = 04;

                    $return['wcmamtx_install_e'] = get_option('wcmamtx_install_e');

                    

                    die;

                }

            } else {

                $return['force_activation'] = 'yes';

                delete_option('wcmamtx_install_e');

                add_option('wcmamtx_install_e','32');

                $return['ruling'] = 05;

                $return['wcmamtx_install_e'] = get_option('wcmamtx_install_e');

                

                die;
            }

        }

        $return['ruling'] = 06;

        $return['wcmamtx_install_e'] = get_option('wcmamtx_install_e');

       
        die;


        

    }


    public function wcmamtxadmin_get_users_ajax_function() {
        $return = array();



        $users = new WP_User_Query( array(
            'search'         => '*'.esc_attr( $_GET['q'] ).'*',
            'search_columns' => array(
                'user_login',
                'user_nicename',
                'user_email',
                'user_url',
            ),
        ) );
        $users_found = $users->get_results();

        

        foreach ($users_found as $ukey=>$uvalue) {
            $return[] = array($uvalue->ID,$uvalue->user_login);
        }

        


        echo json_encode( $return );
        die;
    }



    public function add_action_form_response() {

        if( isset( $_POST['wcmamtx_add_action_nonce'] ) && wp_verify_nonce( $_POST['wcmamtx_add_action_nonce'], 'wcmamtx_nonce_hidden_action') ) {


        
        if (isset($_POST['ndsaction']['label'])) {
            $new_name      = sanitize_text_field($_POST['ndsaction']['label']);

            $random_number  = mt_rand(100000, 999999);
            
            $new_key   = 'custom-action-'.$random_number.'';
        }


    


        $new_row_values    = array();

        $advancedsettings  = (array) get_option('wcmamtx_order_actions');

        

        if ((isset($advancedsettings))  && (sizeof($advancedsettings) >= 1) && (!array_key_exists(0, $advancedsettings))) {

            foreach ($advancedsettings as $key2=>$value2) {

                
                $new_row_values[$key2]['endpoint_key']        = isset($value2['endpoint_key']) ? $value2['endpoint_key'] : $key2;;
                $new_row_values[$key2]['endpoint_name']       = $value2['endpoint_name'];
                $new_row_values[$key2]['wcmamtx_type']        = $value2['wcmamtx_type'];
                $new_row_values[$key2]['parent']              = 'none';



            }

        }
            



        if (isset($new_name) && ($new_name != '')) {
            $new_row_values[$new_key]['endpoint_key']        = $new_key;
            $new_row_values[$new_key]['endpoint_name']       = $new_name;
            $new_row_values[$new_key]['wcmamtx_type']        = $row_type;
            $new_row_values[$new_key]['parent']              = 'none';

        }

        


       
        

        if (($new_row_values != $advancedsettings) && !empty($new_row_values)) {
            update_option('wcmamtx_order_actions',$new_row_values);
        }


        

        // redirect the user to the appropriate page
            wp_redirect('admin.php?page=wcmamtx_advanced_settings&tab=wcmamtx_order_actions');
            exit;
        }           
        else {
            wp_die( __( 'Invalid nonce specified' ), __( 'Error' ), array(
                'response'  => 403,
                'back_link' => 'admin.php?page=wcmamtx_advanced_settings&tab=wcmamtx_order_actions',

            ) );
        }

    }

    public function add_column_form_response() {
        
        if( isset( $_POST['wcmamtx_add_column_nonce'] ) && wp_verify_nonce( $_POST['wcmamtx_add_column_nonce'], 'wcmamtx_nonce_hidden_column') ) {

        
        
            


        
        if (isset($_POST['ndscolumn']['label'])) {
            $new_name      = sanitize_text_field($_POST['ndscolumn']['label']);
        }


        $random_number  = mt_rand(100000, 999999);
        $random_number2 = mt_rand(100000, 999999);

        $row_type = 'endpoint';



        switch($row_type) {
            case "endpoint":
                $new_key   = 'custom-order-'.$random_number.'';
            break;

            
            default:
                $new_key   = 'custom-order-'.$random_number.'';
            break;
        }


        $new_row_values    = array();

        $advancedsettings  = (array) get_option('wcmamtx_order_settings');

        if (!isset($advancedsettings) || (sizeof($advancedsettings) == 1)) {
            $tabs  = wcmamtx_get_account_order_items();

            foreach ($tabs as $key=>$value) {
            
                $new_row_values[$key]['endpoint_key']        = $key;
                $new_row_values[$key]['endpoint_name']       = $value;
                $new_row_values[$key]['wcmamtx_type']        = 'endpoint';
                $new_row_values[$key]['parent']              = 'none';


            }

        } else {
            

            foreach ($advancedsettings as $key2=>$value2) {

                
            
                $new_row_values[$key2]['endpoint_key']        = isset($value2['endpoint_key']) ? $value2['endpoint_key'] : $key2;
                $new_row_values[$key2]['endpoint_name']       = $value2['endpoint_name'];
                $new_row_values[$key2]['wcmamtx_type']        = $value2['wcmamtx_type'];
                $new_row_values[$key2]['parent']              = 'none';
                
                $new_row_values[$key2]['show']                = isset($value2['show']) ? $value2['show'] : "yes";

            }

        }




            if (isset($new_name) && ($new_name != '')) {
                $new_row_values[$new_key]['endpoint_key']        = $new_key;
                $new_row_values[$new_key]['endpoint_name']       = $new_name;
                $new_row_values[$new_key]['wcmamtx_type']        = $row_type;
                $new_row_values[$new_key]['parent']              = 'none';

            }

        



        

        if (($new_row_values != $advancedsettings) && !empty($new_row_values)) {
            update_option('wcmamtx_order_settings',$new_row_values);
        }



        

        // redirect the user to the appropriate page
            wp_redirect('admin.php?page=wcmamtx_advanced_settings&tab=wcmamtx_order_settings');
            exit;
        }           
        else {
            wp_die( __( 'Invalid nonce specified' ), __( 'Error' ), array(
                'response'  => 403,
                'back_link' => 'admin.php?page=wcmamtx_advanced_settings&tab=wcmamtx_order_settings',

            ) );
        }
    }

    public function add_download_form_response() {
        
        if( isset( $_POST['wcmamtx_add_download_nonce'] ) && wp_verify_nonce( $_POST['wcmamtx_add_download_nonce'], 'wcmamtx_nonce_hidden_download') ) {

        

        
        if (isset($_POST['ndscolumn']['label'])) {
            $new_name      = sanitize_text_field($_POST['ndscolumn']['label']);
        }


        $random_number  = mt_rand(100000, 999999);
        $random_number2 = mt_rand(100000, 999999);

        $row_type = 'endpoint';



        switch($row_type) {
            case "endpoint":
                $new_key   = 'custom-download-'.$random_number.'';
            break;

            
            default:
                $new_key   = 'custom-download-'.$random_number.'';
            break;
        }


        $new_row_values    = array();

        $advancedsettings  = (array) get_option('wcmamtx_download_columns');

        if (!isset($advancedsettings) || (sizeof($advancedsettings) == 1)) {
            $tabs  = wcmamtx_get_account_download_items();

            foreach ($tabs as $key=>$value) {
            
                $new_row_values[$key]['endpoint_key']        = $key;
                $new_row_values[$key]['endpoint_name']       = $value;
                $new_row_values[$key]['wcmamtx_type']        = 'endpoint';
                $new_row_values[$key]['parent']              = 'none';


            }

        } else {
            

            foreach ($advancedsettings as $key2=>$value2) {

                
            
                $new_row_values[$key2]['endpoint_key']        = isset($value2['endpoint_key']) ? $value2['endpoint_key'] : $key2;
                $new_row_values[$key2]['endpoint_name']       = $value2['endpoint_name'];
                $new_row_values[$key2]['wcmamtx_type']        = $value2['wcmamtx_type'];
                $new_row_values[$key2]['parent']              = 'none';
                
                $new_row_values[$key2]['show']                = isset($value2['show']) ? $value2['show'] : "yes";

            }

        }




            if (isset($new_name) && ($new_name != '')) {
                $new_row_values[$new_key]['endpoint_key']        = $new_key;
                $new_row_values[$new_key]['endpoint_name']       = $new_name;
                $new_row_values[$new_key]['wcmamtx_type']        = $row_type;
                $new_row_values[$new_key]['parent']              = 'none';

            }

        



        

        if (($new_row_values != $advancedsettings) && !empty($new_row_values)) {
            update_option('wcmamtx_download_columns',$new_row_values);
        }



        

        // redirect the user to the appropriate page
            wp_redirect('admin.php?page=wcmamtx_advanced_settings&tab=wcmamtx_download_columns');
            exit;
        }           
        else {
            wp_die( __( 'Invalid nonce specified' ), __( 'Error' ), array(
                'response'  => 403,
                'back_link' => 'admin.php?page=wcmamtx_advanced_settings&tab=wcmamtx_download_columns',

            ) );
        }
    }


	public function add_endpoint_form_response() {
		
		if( isset( $_POST['wcmamtx_add_endpoint_nonce'] ) && wp_verify_nonce( $_POST['wcmamtx_add_endpoint_nonce'], 'wcmamtx_nonce_hidden') ) {

		
		
			

		if (isset($_POST['nds']['row_type'])) {
			$row_type     = sanitize_text_field($_POST['nds']['row_type']);
		}
		
        if (isset($_POST['nds']['label'])) {
            $new_name      = sanitize_text_field($_POST['nds']['label']);
        }



        $random_number  = mt_rand(100000, 999999);
        $random_number2 = mt_rand(100000, 999999);



        switch($row_type) {
        	case "endpoint":
        	    $new_key   = 'custom-endpoint-'.$random_number.'';
        	break;

        	case "link":
        	    $new_key   = 'custom-link-'.$random_number.'';
            break;

        	case "group":
        	    $new_key   = 'custom-group-'.$random_number.'';
            break;

        	default:
        	    $new_key   = 'custom-endpoint-'.$random_number.'';
            break;
        }


        $new_row_values    = array();

        $advancedsettings  = (array) get_option('wcmamtx_advanced_settings');

        if (!isset($advancedsettings) || (sizeof($advancedsettings) == 1)) {
            $tabs  = wc_get_account_menu_items();

            foreach ($tabs as $key=>$value) {
            
                $new_row_values[$key]['endpoint_key']        = $key;
                $new_row_values[$key]['endpoint_name']       = $value;
                $new_row_values[$key]['wcmamtx_type']        = 'endpoint';
                $new_row_values[$key]['parent']              = 'none';

                $new_row_values[$key]['class']               = isset($value['class']) ? $value['class'] : "";

                
                $new_row_values[$key]['visibleto']           = isset($value['visibleto']) ? $value['visibleto'] : "all";
                $new_row_values[$key]['roles']               = isset($value['roles']) ? $value['roles'] : array();
                $new_row_values[$key]['icon_source']         = isset($value['icon_source']) ? $value['icon_source'] : "default";
                $new_row_values[$key]['icon']                = isset($value['icon']) ? $value['icon'] : "";
                $new_row_values[$key]['content']             = isset($value['content']) ? $value['content'] : "";
                $new_row_values[$key]['show']                = isset($value['show']) ? $value['show'] : "yes";
                $new_row_values[$key]['upload_icon']         = isset($value['upload_icon']) ? $value['upload_icon'] : "";


            }

        } else {
        	

        	foreach ($advancedsettings as $key2=>$value2) {

        		$key2 = isset($value2['endpoint_key']) ? $value2['endpoint_key'] : $key2;
            
                $new_row_values[$key2]['endpoint_key']        = $key2;
                $new_row_values[$key2]['endpoint_name']       = $value2['endpoint_name'];
                $new_row_values[$key2]['wcmamtx_type']        = $value2['wcmamtx_type'];
                $new_row_values[$key2]['parent']              = $value2['parent'];
                
                $new_row_values[$key2]['class']               = isset($value2['class']) ? $value2['class'] : "";
                $new_row_values[$key2]['visibleto']           = isset($value2['visibleto']) ? $value2['visibleto'] : "all";
                $new_row_values[$key2]['roles']               = isset($value2['roles']) ? $value2['roles'] : array();
                $new_row_values[$key2]['icon_source']         = isset($value2['icon_source']) ? $value2['icon_source'] : "default";
                $new_row_values[$key2]['icon']                = isset($value2['icon']) ? $value2['icon'] : "";
                $new_row_values[$key2]['show']                = isset($value2['show']) ? $value2['show'] : "yes";
                $new_row_values[$key2]['upload_icon']         = isset($value2['upload_icon']) ? $value2['upload_icon'] : "";
                

                if (isset($value2['wcmamtx_type']) && ($value2['wcmamtx_type'] == "link")) {
                	$new_row_values[$key2]['link_inputtarget']              = $value2['link_inputtarget'];
                	$new_row_values[$key2]['link_targetblank']              = $value2['link_targetblank'];
                }


                if (isset($value2['wcmamtx_type']) && ($value2['wcmamtx_type'] == "endpoint")) {
                    $new_row_values[$key2]['content']              = isset($value2['content']) ? $value2['content'] : "";
                }



                if (isset($value2['wcmamtx_type']) && ($value2['wcmamtx_type'] == "group")) {

                	$new_row_values[$key2]['group_open_default']   = isset($value2['group_open_default']) ? $value2['group_open_default'] : "no";

                }

                if (isset($value2['wcmamtx_type']) && ($value2['wcmamtx_type'] != "group")) {

                    $new_row_values[$key2]['exclude_wpml_sticky']   = isset($value2['exclude_wpml_sticky']) ? $value2['exclude_wpml_sticky'] : 02;

                }

                if ($key2 == "dashboard") {
                    $new_row_values[$key2]['hide_dashboard_hello']            = isset($value2['hide_dashboard_hello']) ? $value2['hide_dashboard_hello'] : 00;
                    $new_row_values[$key2]['hide_intro_hello']                = isset($value2['hide_intro_hello']) ? $value2['hide_intro_hello'] : 00;
                    $new_row_values[$key2]['content_dash']                    = isset($value2['content_dash']) ? $value2['content_dash'] : "";
                }
                
                
            

            }

        }




        	if (isset($new_name) && ($new_name != '')) {
        	    $new_row_values[$new_key]['endpoint_key']        = $new_key;
                $new_row_values[$new_key]['endpoint_name']       = $new_name;
                $new_row_values[$new_key]['wcmamtx_type']        = $row_type;
                $new_row_values[$new_key]['parent']              = 'none';

                if ($row_type == "endpoint") {
                    $new_row_values[$new_key]['content']              = esc_html__( 'Sample Content' ,'customize-my-account-for-woocommerce-pro');
                }

                if ($row_type == "link") {
                    $new_row_values[$new_key]['link_inputtarget']              = esc_url(site_url());
                }

                if ($row_type != 02) {
                    $new_row_values[$new_key]['exclude_wpml_sticky']              = 02;
                }
                
                

            }

        



        

        if (($new_row_values != $advancedsettings) && !empty($new_row_values)) {
        	update_option($this->wcmamtx_notices_settings_page,$new_row_values);
        }

		// add the admin notice
			$admin_notice = "success";

		// redirect the user to the appropriate page
			wp_redirect('admin.php?page=wcmamtx_advanced_settings');
			exit;
		}			
		else {
			wp_die( __( 'Invalid nonce specified' ), __( 'Error' ), array(
				'response' 	=> 403,
				'back_link' => 'admin.php?page=wcmamtx_advanced_settings',

			) );
		}
	}


	public function wcmamtx_get_posts_ajax_callback(){
 
	
	  $return          = array();
	  
      $post_type_array = array('elementor_library');
	  // you can use WP_Query, query_posts() or get_posts() here - it doesn't matter
	  $search_results  = new WP_Query( array( 
		's'                   => sanitize_text_field($_GET['q']), // the search query
		'post_status'         => 'publish', // if you don't want drafts to be returned
		'ignore_sticky_posts' => 1,
		'post_type'           => $post_type_array
	  ) );
	  



	  if( $search_results->have_posts() ) {
		while( $search_results->have_posts() ) : $search_results->the_post();

		    $product_type = WC_Product_Factory::get_product_type($search_results->post->ID);	
			// shorten the title a little
			

			
				 $finaltitle=''.$search_results->post->post_title.'';
				 $return[] = array( $search_results->post->ID, $finaltitle );
			
			
			  

			 // array( Post ID, Post Title )
		endwhile;
	  } 
	   echo json_encode( $return );
	  die;
    }


	public function wcmamtxadmin_add_new_template() {

		/* First, check nonce */
        check_ajax_referer( 'wcmamtx_nonce', 'security' );
        check_ajax_referer( 'wcmamtx_nonce_hidden', 'nonce' );
		
		if (isset($_POST['row_type'])) {
			$row_type     = sanitize_text_field($_POST['row_type']);
		}
		
        if (isset($_POST['new_row'])) {
            $new_name      = sanitize_text_field($_POST['new_row']);

        }




        $new = array(
            'post_title' => $new_name,
            'post_status' => 'publish',
            'post_type' => 'elementor_library'
        );

        $post_id = wp_insert_post( $new );



        if (isset($post_id) && isset($row_type) && ($row_type != "") ) {
            
            switch($row_type) {

                

                case "myaccount":
                   $content = '[{"id":"a1ab84e","elType":"section","settings":[],"elements":[{"id":"ab99e3e","elType":"column","settings":{"_column_size":100,"_inline_size":null},"elements":[{"id":"f41d127","elType":"widget","settings":{"shortcode":"[woocommerce_my_account]","_margin":{"unit":"px","top":"10","right":"10","bottom":"10","left":"10","isLinked":true},"_padding":{"unit":"px","top":"10","right":"10","bottom":"10","left":"10","isLinked":true}},"elements":[],"widgetType":"shortcode"}],"isInner":false}],"isInner":false}]';
                break;


                case "orders":
                   $content = '[{"id":"06f0109","elType":"section","settings":[],"elements":[{"id":"5c84546","elType":"column","settings":{"_column_size":100,"_inline_size":null},"elements":[{"id":"7bd5aca","elType":"widget","settings":[],"elements":[],"widgetType":"my_orders_widget"}],"isInner":false}],"isInner":false}]';
                break;


                case "login":
                   $content = '[{"id":"b05982c","elType":"container","settings":{"flex_direction":"row","flex_gap":{"unit":"px","size":0,"column":"0","row":"0"}},"elements":[{"id":"9d6a247","elType":"container","settings":{"flex_direction":"column","content_width":"full","width":{"unit":"%","size":"50"}},"elements":[{"id":"3f3a83c","elType":"widget","settings":{"content_width":"full","_padding":{"unit":"px","top":"16","right":"16","bottom":"16","left":"16","isLinked":true}},"elements":[],"widgetType":"my_formloginwidget"}],"isInner":true},{"id":"9fda216","elType":"container","settings":{"flex_direction":"column","content_width":"full","width":{"unit":"%","size":"50"}},"elements":[{"id":"b5990e7","elType":"widget","settings":{"content_width":"full","_padding":{"unit":"px","top":"20","right":"20","bottom":"20","left":"20","isLinked":true}},"elements":[],"widgetType":"my_formregisterwidget"}],"isInner":true}],"isInner":false}]';
                break;


                case "lostpassword":
                   $content = '[{"id":"30c675d","elType":"container","settings":{"flex_direction":"column"},"elements":[{"id":"5b8e858","elType":"widget","settings":{"content_width":"full","_padding":{"unit":"px","top":"23","right":"23","bottom":"23","left":"23","isLinked":true}},"elements":[],"widgetType":"my_formlost_passwordwidget"}],"isInner":false}]';
                break;



                

                default:
                   $content = "";
                break;

            }


            if ($content != "") {

                update_post_meta( $post_id, '_elementor_data', $content );

            }
            

        }

        $elementor_edit_link = ''.admin_url().'post.php?post='.$post_id.'&action=elementor';

        $result = array('redirect_url'=>$elementor_edit_link,'id'=>$post_id,'text'=>get_the_title($post_id));

        echo json_encode( $result );

        die();
	}


	public function wcmamtx_load_admin_menu_style() {

	    wp_enqueue_style( 'woomatrix_admin_menu_css', ''.wcmamtx_PLUGIN_URL.'assets/css/admin_menu.css' );
	    wp_enqueue_script( 'woomatrix_admin_menu_js', ''.wcmamtx_PLUGIN_URL.'assets/js/admin_menu.js' );

	}


	public function restore_my_account_tabs() {
	    if( current_user_can('editor') || current_user_can('administrator') ) {

	    	if ( !wp_verify_nonce($_POST['nonce'], 'wcmamtx_nonce') ){ 
				die(); 
			}
	        delete_option( $this->wcmamtx_notices_settings_page );
        } 
	   die();
	}


	public function restore_my_account_order() {
	    if( current_user_can('editor') || current_user_can('administrator') ) {

	    	if ( !wp_verify_nonce($_POST['nonce'], 'wcmamtx_nonce') ){ 
				die(); 
			}
			
	        delete_option( 'wcmamtx_order_settings' );
        } 
	   die();
	}


    public function restore_my_account_download() {
        if( current_user_can('editor') || current_user_can('administrator') ) {

            if ( !wp_verify_nonce($_POST['nonce'], 'wcmamtx_nonce') ){ 
                die(); 
            }
            
            delete_option( 'wcmamtx_download_columns' );
        } 
       die();
    }
	
	




	
	/*
	 * registers admin scripts via admin enqueue scripts
	 */
	public function wcmamtx_register_admin_scripts($hook) {
	    global $general_wcmamtxsettings_page;



			
		if ( $hook == $general_wcmamtxsettings_page )  {

		    $current_tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : "wcmamtx_advanced_settings";
 
            wp_enqueue_style( 'wcmamtx_fontawesome', ''.wcmamtx_PLUGIN_URL.'assets/css/font-awesome.min.css');

            
            wp_enqueue_script( 'wcmamtx_bootstrap', ''.wcmamtx_PLUGIN_URL.'assets/js/bootstrap.min.js');
            wp_enqueue_script( 'wcmamtx_bootstrap_toggle', ''.wcmamtx_PLUGIN_URL.'assets/js/bootstrap4-toggle.min.js');
            wp_enqueue_style( 'wcmamtx_bootstrap', ''.wcmamtx_PLUGIN_URL.'assets/css/bootstrap.min.css');
            wp_enqueue_style( 'wcmamtx_bootstrap_toggle', ''.wcmamtx_PLUGIN_URL.'assets/css/bootstrap4-toggle.min.css');

		    wp_enqueue_script( 'select2', ''.wcmamtx_PLUGIN_URL.'assets/js/select2.js' );

		    if (isset($current_tab) && ($current_tab == "wcmamtx_advanced_settings")) {

		         wp_enqueue_script( 'wcmamtxadmin', ''.wcmamtx_PLUGIN_URL.'assets/js/admin.js',array('jquery-ui-accordion'), '1.0.0', true );

		    } else if (isset($current_tab) && (($current_tab == "wcmamtx_order_settings") || ($current_tab == "wcmamtx_download_columns") )) {
		    	wp_enqueue_script( 'wcmamtxsortable', ''.wcmamtx_PLUGIN_URL.'assets/js/sortable.js' );
		    	wp_enqueue_script( 'wcmamtxsortable2', ''.wcmamtx_PLUGIN_URL.'assets/js/sortable2.js');

		    	wp_enqueue_script( 'wcmamtxadmin', ''.wcmamtx_PLUGIN_URL.'assets/js/admin2.js',array('jquery-ui-accordion'), '1.0.0', true );

		    } else if (isset($current_tab) && (($current_tab == "wcmamtx_order_actions") || ($current_tab == "wcmamtx_download_actions"))) {

		    	wp_enqueue_script( 'wcmamtxsortable', ''.wcmamtx_PLUGIN_URL.'assets/js/sortable.js' );
		    	wp_enqueue_script( 'wcmamtxsortable2', ''.wcmamtx_PLUGIN_URL.'assets/js/sortable2.js');

		    	wp_enqueue_script( 'wcmamtxadmin', ''.wcmamtx_PLUGIN_URL.'assets/js/admin3.js',array('jquery-ui-accordion'), '1.0.0', true );

		    } else if (isset($current_tab) && ($current_tab == "wcmamtx_plugin_options")) {

		    	wp_enqueue_script( 'wcmamtxadmin', ''.wcmamtx_PLUGIN_URL.'assets/js/admin.js',array('jquery-ui-accordion'), '1.0.0', true );

		    }

		    wp_enqueue_script( 'wcmamtx-dashicons', ''.wcmamtx_PLUGIN_URL.'assets/js/dashicons-picker.js');

		    wp_enqueue_style( 'wcmamtx-dashicons', ''.wcmamtx_PLUGIN_URL.'assets/css/dashicons-picker.css');
		
            wp_enqueue_script( 'wcmamtx-tageditor', ''.wcmamtx_PLUGIN_URL.'assets/js/tageditor.js');
		    wp_enqueue_style( 'wcmamtx-tageditor', ''.wcmamtx_PLUGIN_URL.'assets/css/tageditor.css');

	        wp_enqueue_style( 'jquery-ui-core', ''.wcmamtx_PLUGIN_URL.'assets/css/jquery-ui.css' );
            wp_enqueue_style( 'select2',''.wcmamtx_PLUGIN_URL.'assets/css/select2.css');
		 
		    wp_enqueue_style( 'wcmamtxadmin', ''.wcmamtx_PLUGIN_URL.'assets/css/admin.css' );


		 
		    $wcmamtx_js_array = array(
                'new_row_alert_text'   => esc_html__( 'Enter name for new endpoint' ,'customize-my-account-for-woocommerce-pro'),
                'new_group_alert_text' => esc_html__( 'Enter name for new group' ,'customize-my-account-for-woocommerce-pro'),
                'new_link_alert_text'  => esc_html__( 'Enter name for new link' ,'customize-my-account-for-woocommerce-pro'),
                'group_mixing_text'    => esc_html__( 'Group can not be dropped into group' ,'customize-my-account-for-woocommerce-pro'),
                'restorealert'         => esc_html__( 'Are you sure you want to restore to default my account tabs ? you can not undo this.' ,'customize-my-account-for-woocommerce-pro'),
                'endpoint_remove_alert'   => esc_html__( "Are you sure you want to delete this ?" ,'customize-my-account-for-woocommerce-pro'),
                'core_remove_alert'     => esc_html__( "this group has core endpoints. please move them before removing this group" ,'customize-my-account-for-woocommerce-pro'),
                'dt_type'               => wcmamtx_get_version_type(),
                'pro_notice'            => esc_html__( 'This feature is available in pro version only.' ,'customize-my-account-for-woocommerce-pro'),
                'empty_label_notice'    => esc_html__( 'Label can not be empty.' ,'customize-my-account-for-woocommerce-pro'),
                'nonce'                 => wp_create_nonce( 'wcmamtx_nonce' ),
                'ajax_url'              => admin_url( 'admin-ajax.php' ),
                'wait_text'             => esc_html__( 'Adding....' ,'customize-my-account-for-woocommerce-pro'),
                'order_action_text'     => esc_html__( 'Plugin do not support movement of order actions yet' ,'customize-my-account-for-woocommerce-pro'),
                'chose_template'             => esc_html__( 'Choose Template' ,'customize-my-account-for-woocommerce-pro'),
                'uploadimage'           => esc_html__( 'Choose an image' ,'customize-my-account-for-woocommerce-pro'),
                'useimage'              => esc_html__( 'Use Image' ,'customize-my-account-for-woocommerce-pro'),
                'placeholder'           => wcmamtx_placeholder_img_src()
                
            );

            wp_localize_script( 'wcmamtxadmin', 'wcmamtxadmin', $wcmamtx_js_array );

        }
	}
	
	

	
	
	public function wcmamtx_register_settings_settings() {

		$this->wcmamtx_plugin_settings_tab[$this->wcmamtx_notices_settings_page] = esc_html__( 'Endpoints' ,'customize-my-account-for-woocommerce-pro');

		$this->wcmamtx_plugin_settings_tab[$this->wcmamtx_order_settings_page]   = esc_html__( 'Order Columns' ,'customize-my-account-for-woocommerce-pro');

		$this->wcmamtx_plugin_settings_tab[$this->wcmamtx_order_actions_page]    = esc_html__( 'Order Actions' ,'customize-my-account-for-woocommerce-pro');

        $this->wcmamtx_plugin_settings_tab[$this->wcmamtx_download_columns_page] = esc_html__( 'Download Columns' ,'customize-my-account-for-woocommerce-pro');
        $this->wcmamtx_plugin_settings_tab[$this->wcmamtx_avatar_settings_page] = esc_html__( 'User Avatar' ,'customize-my-account-for-woocommerce-pro');

        $this->wcmamtx_plugin_settings_tab[$this->wcmamtx_plugin_options_key]    = esc_html__( 'Settings' ,'customize-my-account-for-woocommerce-pro');

        $this->wcmamtx_plugin_settings_tab[$this->wcmamtx_license_settings]      = esc_html__( 'License Activation' ,'wcva');
       

		

		register_setting( $this->wcmamtx_notices_settings_page, $this->wcmamtx_notices_settings_page );

		add_settings_section( 'wcmamtx_advance_section', '', '', $this->wcmamtx_notices_settings_page );

		add_settings_field( 'advanced_option', '', array( $this, 'linked_product_swatches_settings' ), $this->wcmamtx_notices_settings_page, 'wcmamtx_advance_section' );


		register_setting( $this->wcmamtx_order_settings_page, $this->wcmamtx_order_settings_page );

		add_settings_section( 'wcmamtx_order_section', '', '', $this->wcmamtx_order_settings_page );

		add_settings_field( 'order_option', '', array( $this, 'linked_product_swatches_order' ), $this->wcmamtx_order_settings_page, 'wcmamtx_order_section' );


		register_setting( $this->wcmamtx_order_actions_page, $this->wcmamtx_order_actions_page );

		add_settings_section( 'wcmamtx_order_actions', '', '', $this->wcmamtx_order_actions_page );

		add_settings_field( 'order_actions', '', array( $this, 'linked_product_swatches_order_actions' ), $this->wcmamtx_order_actions_page, 'wcmamtx_order_actions' );



        register_setting( $this->wcmamtx_avatar_settings_page, $this->wcmamtx_avatar_settings_page );

        add_settings_section( 'wcmamtx_avatar_section', '', '', $this->wcmamtx_avatar_settings_page );

        add_settings_field( 'avatar_option', '', array( $this, 'wcmamtx_avatar_page' ), $this->wcmamtx_avatar_settings_page, 'wcmamtx_avatar_section' );




        register_setting( $this->wcmamtx_download_columns_page, $this->wcmamtx_download_columns_page );

        add_settings_section( 'wcmamtx_download_section', '', '', $this->wcmamtx_download_columns_page );

        add_settings_field( 'download_option', '', array( $this, 'wcmamtx_download_coumns_form' ), $this->wcmamtx_download_columns_page, 'wcmamtx_download_section' );




		register_setting( $this->wcmamtx_plugin_options_key, $this->wcmamtx_plugin_options_key );

		add_settings_section( 'wcmamtx_general_section', '', '', $this->wcmamtx_plugin_options_key );

		add_settings_field( 'general_option', '', array( $this, 'wcmamtx_options_page' ), $this->wcmamtx_plugin_options_key, 'wcmamtx_general_section' );


        register_setting( $this->wcmamtx_license_settings, $this->wcmamtx_license_settings );

        add_settings_section( 'wcmamtx_license_section', '', '', $this->wcmamtx_license_settings );

        add_settings_field( 'wcmamtx_license_option', '', array( $this, 'license_sysbasics_form' ), $this->wcmamtx_license_settings, 'wcmamtx_license_section' );
		

		

	}



    public function license_sysbasics_form() { 

        include ('forms/license_sysbasics_form.php');

    }


     public function wcmamtx_avatar_page() {
        include ('forms/avatar_form.php');
    }




	/**
      * Recursive sanitation for an array
      * 
      * @param $array
      *
      * @return mixed
      */
	public function recursive_sanitize_text_field($array) {
		foreach ( $array as $key => $value ) {

			$value = sanitize_text_field( $value );

		}

		return $array;
	}
	

	

	

	/*
     * Linked product swatached settings
     * includes form field from forms folder
     */
	
	public function linked_product_swatches_settings() { 

        $wcmamtx_act_date  = get_option('wcmamtx_act_date');

        $wcmamtx_install_e = get_option('wcmamtx_install_e');

        $wcmamtx_act_date  = date('Y-m-d',strtotime($wcmamtx_act_date));

        $tld             = date('Y-m-d');

        $wcmamtx_act_date = new DateTime($wcmamtx_act_date);
        $tld = new DateTime($tld);
        $wcmamtx_interval = $wcmamtx_act_date->diff($tld);

        $days_used = $wcmamtx_interval->days;



        if ($days_used < 03 ) {
            include ('forms/settings_form.php');
        } else {
            if (isset($wcmamtx_install_e) && ($wcmamtx_install_e == "64")) {
                include ('forms/settings_form.php');
            } else {
                wcmamtx_load_license_reminder_div();
            }
        }
		   
	}



    /**
     * Linked product swatached settings
     * includes form field from forms folder
     */
    
    public function wcmamtx_download_coumns_form() { 

        $wcmamtx_act_date  = get_option('wcmamtx_act_date');

        $wcmamtx_install_e = get_option('wcmamtx_install_e');

        $wcmamtx_act_date  = date('Y-m-d',strtotime($wcmamtx_act_date));

        $tld             = date('Y-m-d');

        $wcmamtx_act_date = new DateTime($wcmamtx_act_date);
        $tld = new DateTime($tld);
        $wcmamtx_interval = $wcmamtx_act_date->diff($tld);

        $days_used = $wcmamtx_interval->days;

        if ($days_used < 03 ) {
            include ('forms/download_columns_form.php');

            ?>

                    <div class="wcmamtx_add_order_div">
                        <button type="button" href="#" data-toggle="modal" data-target="#wcmamtx_download_modal" data-etype="endpoint" id="wcmamtx_add_download" class="btn btn-sm btn-primary wcmamtx_add_download">
                            <span class="dashicons dashicons-insert"></span>
                            <?php echo esc_html__( 'Add Column' ,'customize-my-account-for-woocommerce-pro'); ?>
                        </button>



                    </div>


            <?php
        } else {
            if (isset($wcmamtx_install_e) && ($wcmamtx_install_e == "64")) {
                include ('forms/download_columns_form.php');

                ?>

                    <div class="wcmamtx_add_order_div">
                        <button type="button" href="#" data-toggle="modal" data-target="#wcmamtx_download_modal" data-etype="endpoint" id="wcmamtx_add_download" class="btn btn-sm btn-primary wcmamtx_add_download">
                            <span class="dashicons dashicons-insert"></span>
                            <?php echo esc_html__( 'Add Column' ,'customize-my-account-for-woocommerce-pro'); ?>
                        </button>



                    </div>


                <?php
            } else {
                wcmamtx_load_license_reminder_div();
            }
        }

          

    }





	/*
     * Linked product swatached settings
     * includes form field from forms folder
     */
	
	public function linked_product_swatches_order() { 

        $wcmamtx_act_date  = get_option('wcmamtx_act_date');

        $wcmamtx_install_e = get_option('wcmamtx_install_e');

        $wcmamtx_act_date  = date('Y-m-d',strtotime($wcmamtx_act_date));

        $tld             = date('Y-m-d');

        $wcmamtx_act_date = new DateTime($wcmamtx_act_date);
        $tld = new DateTime($tld);
        $wcmamtx_interval = $wcmamtx_act_date->diff($tld);

        $days_used = $wcmamtx_interval->days;

        if ($days_used < 03 ) {
            include ('forms/order_form.php');

            ?>

                    <div class="wcmamtx_add_order_div">
                        <button type="button" href="#" data-toggle="modal" data-target="#wcmamtx_order_modal" data-etype="endpoint" id="wcmamtx_add_order" class="btn btn-sm btn-primary wcmamtx_add_order">
                            <span class="dashicons dashicons-insert"></span>
                            <?php echo esc_html__( 'Add Column' ,'customize-my-account-for-woocommerce-pro'); ?>
                        </button>



                    </div>


            <?php
        } else {
            if (isset($wcmamtx_install_e) && ($wcmamtx_install_e == "64")) {
                include ('forms/order_form.php');

                ?>

                    <div class="wcmamtx_add_order_div">
                        <button type="button" href="#" data-toggle="modal" data-target="#wcmamtx_order_modal" data-etype="endpoint" id="wcmamtx_add_order" class="btn btn-sm btn-primary wcmamtx_add_order <?php if ($dt_type == "all") { echo 'wcmamtx_disabled'; } ?>">
                            <span class="dashicons dashicons-insert"></span>
                            <?php echo esc_html__( 'Add Column' ,'customize-my-account-for-woocommerce-pro'); ?>
                        </button>



                    </div>


                <?php
            } else {
                wcmamtx_load_license_reminder_div();
            }
        }

          

    }


	/*
     * Linked product swatached settings
     * includes form field from forms folder
     */
	
	public function linked_product_swatches_order_actions() { 

        $wcmamtx_act_date  = get_option('wcmamtx_act_date');

        $wcmamtx_install_e = get_option('wcmamtx_install_e');

        $wcmamtx_act_date  = date('Y-m-d',strtotime($wcmamtx_act_date));

        $tld             = date('Y-m-d');

        $wcmamtx_act_date = new DateTime($wcmamtx_act_date);
        $tld = new DateTime($tld);
        $wcmamtx_interval = $wcmamtx_act_date->diff($tld);

        $days_used = $wcmamtx_interval->days;

        if ($days_used < 03 ) {
            include ('forms/order_actions_form.php');

            ?>

            <div class="wcmamtx_add_actions_div">
                <button type="button" href="#" data-toggle="modal" data-target="#wcmamtx_actions_modal" data-etype="endpoint" id="wcmamtx_add_actions" class="btn btn-sm btn-primary wcmamtx_add_actions <?php if ($dt_type == "all") { echo 'wcmamtx_disabled'; } ?>">
                    <span class="dashicons dashicons-insert"></span>
                    <?php echo esc_html__( 'Add Custom Order Action' ,'customize-my-account-for-woocommerce-pro'); ?>
                </button>



            </div>


            <?php
        } else {
            if (isset($wcmamtx_install_e) && ($wcmamtx_install_e == "64")) {
                include ('forms/order_actions_form.php');

                ?>

            <div class="wcmamtx_add_actions_div">
                <button type="button" href="#" data-toggle="modal" data-target="#wcmamtx_actions_modal" data-etype="endpoint" id="wcmamtx_add_actions" class="btn btn-sm btn-primary wcmamtx_add_actions <?php if ($dt_type == "all") { echo 'wcmamtx_disabled'; } ?>">
                    <span class="dashicons dashicons-insert"></span>
                    <?php echo esc_html__( 'Add Custom Order Action' ,'customize-my-account-for-woocommerce-pro'); ?>
                </button>



            </div>


            <?php
            } else {
                wcmamtx_load_license_reminder_div();
            }
        }  
		   
	}



	/*
     * Plugin options page
     * 
     */
	
	public function wcmamtx_options_page() { 

	   include ('forms/options_form.php');
		   
	}



	
	
	/*
     * Adds Admin Menu "cart notices"
     * global $general_wcmamtxsettings_page is used to include page specific scripts
     */

	public function add_admin_menus() {
	    global $general_wcmamtxsettings_page;
        
        add_menu_page(
          __( 'SysBasics', 'customize-my-account-for-woocommerce-pro' ),
         'SysBasics',
         'manage_woocommerce',
         'sysbasics',
         array($this,'plugin_options_page'),
         ''.wcmamtx_PLUGIN_URL.'assets/images/icon.png',
         70
        );
	    

        $general_wcmamtxsettings_page = add_submenu_page( 'sysbasics', wcmamtx_PLUGIN_name , wcmamtx_PLUGIN_name , 'manage_woocommerce', esc_html__($this->wcmamtx_notices_settings_page), array($this, 'plugin_options_page'));


	         
	}




	public function plugin_options_page() {
		$tab = isset( $_GET['tab'] ) ? sanitize_text_field($_GET['tab']) : sanitize_text_field($this->wcmamtx_notices_settings_page);
		$current_tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : $tab;
        $dt_type = wcmamtx_get_version_type();

        $wcmamtx_act_date  = get_option('wcmamtx_act_date');

        $wcmamtx_install_e = get_option('wcmamtx_install_e');

        $wcmamtx_act_date  = date('Y-m-d',strtotime($wcmamtx_act_date));

        $tld             = date('Y-m-d');

        $wcmamtx_act_date = new DateTime($wcmamtx_act_date);
        $tld = new DateTime($tld);
        $wcmamtx_interval = $wcmamtx_act_date->diff($tld);

        $days_used = $wcmamtx_interval->days;

        if ($days_used < 03 ) {

            $goahead = 'yes';
            
        } else {
            if (isset($wcmamtx_install_e) && ($wcmamtx_install_e == "64")) {

                $goahead = 'yes';
                
            } else {

                $goahead = 'no';
                
            }
        }
		
		?>
		<div class="wrap">
		   <?php $this->wcmamtx_options_tab_wrap(); ?>
			<form method="post" action="options.php">
				<?php wp_nonce_field( 'update-options' ); ?>
				<?php settings_fields( $tab ); ?>
				<?php do_settings_sections( $tab ); ?>

				<div class="wcmamtx_buttons_section">
				    
				    <?php if (isset($current_tab) && ($current_tab == "wcmamtx_advanced_settings") && ($goahead != "no")) { ?>
				        <div class="wcmamtx_add_section_div">
				            <button type="button" href="#" data-toggle="modal" data-target="#wcmamtx_example_modal" data-etype="endpoint" id="wcmamtx_add_endpoint" class="btn btn-sm btn-primary wcmamtx_add_group <?php if ($dt_type == "all") { echo 'wcmamtx_disabled'; } ?>">
				            	<span class="dashicons dashicons-insert"></span>
				            	<?php echo esc_html__( 'Add Endpoint' ,'customize-my-account-for-woocommerce-pro'); ?>
				            </button>

				            <button type="button" href="#" data-toggle="modal" data-target="#wcmamtx_example_modal" data-etype="link" id="wcmamtx_add_link" class="btn btn-sm btn-primary wcmamtx_add_group">
				            	<span class="dashicons dashicons-insert"></span>
				            	<?php echo esc_html__( 'Add Link' ,'customize-my-account-for-woocommerce-pro'); ?>
				            </button>

				            <button type="button" href="#" data-toggle="modal" data-target="#wcmamtx_example_modal" data-etype="group" id="wcmamtx_add_group" class="btn btn-sm btn-primary wcmamtx_add_group <?php if ($dt_type == "all") { echo 'wcmamtx_disabled'; } ?>">
				            	<span class="dashicons dashicons-insert"></span>
				            	<?php echo esc_html__( 'Add Group' ,'customize-my-account-for-woocommerce-pro'); ?>
				            </button>
				            
				        </div>
				       
				    <?php } ?>

                    <div class="wcmamtx_submit_section_div">


                

                            <input type="submit" name="submit" id="submit" class="btn btn-sm btn-success wcmamtx_submit_button" value="<?php echo esc_html__( 'Save Changes' ,'customize-my-account-for-woocommerce-pro'); ?>">

                     

				        <?php if (isset($current_tab) && ($current_tab == "wcmamtx_advanced_settings")) { ?>

				            <input type="button" href="#" name="submit" id="wcmamtx_reset_tabs_button" class="btn-sm btn btn-danger wcmamtx_reset_tabs_button" value="<?php echo esc_html__( 'Restore Default' ,'customize-my-account-for-woocommerce-pro'); ?>">
                            
                            <?php if (($dt_type == "all") && (pro_url != '')) { ?>
                                  
                            	<a type="button" target="_blank" href="<?php echo pro_url; ?>" name="submit" id="wcmamtx_frontend_link" class="btn btn-sm btn-primary wcmamtx_frontend_link" >
                            		<span class="dashicons dashicons-lock"></span>
                            		<?php echo esc_html__( 'Upgrade to pro' ,'customize-my-account-for-woocommerce-pro'); ?>
                            	</a>

                            <?php } ?>

				            
				        <?php } elseif (isset($current_tab) && ($current_tab == "wcmamtx_order_settings")) {  ?>

				        	    <input type="button" href="#" name="submit" id="wcmamtx_reset_order_button" class="btn btn-sm btn-danger wcmamtx_reset_order_button" value="<?php echo esc_html__( 'Restore Default' ,'customize-my-account-for-woocommerce-pro'); ?>">

				        <?php } elseif (isset($current_tab) && ($current_tab == "wcmamtx_download_columns")) { ?>
                                <input type="button" href="#" name="submit" id="wcmamtx_reset_download_button" class="btn btn-sm btn-danger wcmamtx_reset_download_button" value="<?php echo esc_html__( 'Restore Default' ,'customize-my-account-for-woocommerce-pro'); ?>">
                        
                        <?php } ?>

				        <?php
                         $frontend_url = get_permalink(get_option('woocommerce_myaccount_page_id'));

                         if (($tab == "wcmamtx_order_settings") || ($tab == "wcmamtx_order_actions")) {
                         	 $frontend_url =  wc_get_account_endpoint_url( 'orders' );
                         }

				        ?>

				        <a type="button" target="_blank" href="<?php echo $frontend_url; ?>" name="submit" id="wcmamtx_frontend_link" class="btn btn-sm btn-primary wcmamtx_frontend_link" >
				        	<span class="dashicons dashicons-welcome-view-site"></span>
				        	<?php echo esc_html__( 'Frontend' ,'customize-my-account-for-woocommerce-pro'); ?>
				        </a>

				       

                        <?php do_action( 'wcmamtx_add_author_links' ); ?>
				    </div>

				    
				</div>
				
			</form>

			<div class="modal fade" id="wcmamtx_example_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
				<div class="modal-dialog" role="document">
					<div class="modal-content">

						<div class="modal-body">

							<form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post" id="nds_add_user_meta_form" >			


								<input type="hidden" name="action" value="nds_form_response_endpoint">
								<input type="hidden" name="wcmamtx_add_endpoint_nonce" value="<?php echo wp_create_nonce( 'wcmamtx_nonce_hidden' ); ?>" />			
								<div class="form-group">
									
									
									<input class="form-control" required id="sdfsd-user_meta_key" type="text" name="<?php echo "nds"; ?>[label]" value="" placeholder="<?php echo esc_html__('Enter Label','customize-my-account-for-woocommerce-pro'); ?>" /><br>
									<input type="hidden" class="form-control" nonce="<?php echo wp_create_nonce( 'wcmamtx_nonce_hidden' ); ?>" name="<?php echo "nds"; ?>[row_type]" id="wcmamtx_hidden_endpoint_type" value="">
								</div>

								<button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo esc_html__( 'Close' ,'customize-my-account-for-woocommerce-pro'); ?></button>
								<button type="submit" name="submit"  class="btn btn-primary wcmamtx_new_end_point"><?php echo esc_html__( 'Add' ,'customize-my-account-for-woocommerce-pro'); ?>

                                </button>
                            </form>

                        </div>
                        <div class="modal-footer">

                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="wcmamtx_order_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">

                        <div class="modal-body">

                            <form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post" id="nds_add_column_form" >          


                                <input type="hidden" name="action" value="nds_form_response_column">
                                <input type="hidden" name="wcmamtx_add_column_nonce" value="<?php echo wp_create_nonce( 'wcmamtx_nonce_hidden_column' ); ?>" />          
                                <div class="form-group">
                                    
                                    
                                    <input class="form-control" required id="sdfsd-user_meta_key" type="text" name="<?php echo "ndscolumn"; ?>[label]" value="" placeholder="<?php echo esc_html__('Enter Label','customize-my-account-for-woocommerce-pro'); ?>" /><br>
                                    
                                </div>

                                <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo esc_html__( 'Close' ,'customize-my-account-for-woocommerce-pro'); ?></button>
                                <button type="submit" name="submit"  class="btn btn-primary wcmamtx_new_order"><?php echo esc_html__( 'Add New Column' ,'customize-my-account-for-woocommerce-pro'); ?>

                                </button>
                            </form>

                        </div>
                        <div class="modal-footer">

                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="wcmamtx_download_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">

                        <div class="modal-body">

                            <form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post" id="nds_add_download_form" >          


                                <input type="hidden" name="action" value="nds_form_response_download">
                                <input type="hidden" name="wcmamtx_add_download_nonce" value="<?php echo wp_create_nonce( 'wcmamtx_nonce_hidden_download' ); ?>" />          
                                <div class="form-group">
                                    
                                    
                                    <input class="form-control" required type="text" name="<?php echo "ndscolumn"; ?>[label]" value="" placeholder="<?php echo esc_html__('Enter Label','customize-my-account-for-woocommerce-pro'); ?>" /><br>
                                    
                                </div>

                                <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo esc_html__( 'Close' ,'customize-my-account-for-woocommerce-pro'); ?></button>
                                <button type="submit" name="submit"  class="btn btn-primary wcmamtx_new_download"><?php echo esc_html__( 'Add New Column' ,'customize-my-account-for-woocommerce-pro'); ?>

                                </button>
                            </form>

                        </div>
                        <div class="modal-footer">

                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="wcmamtx_actions_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">

                        <div class="modal-body">

                            <form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post" id="nds_add_action_form" >          


                                <input type="hidden" name="action" value="nds_form_response_action">
                                <input type="hidden" name="wcmamtx_add_action_nonce" value="<?php echo wp_create_nonce( 'wcmamtx_nonce_hidden_action' ); ?>" />          
                                <div class="form-group">
                                    
                                    
                                    <input class="form-control" required id="sdfsd-user_meta_key" type="text" name="<?php echo "ndsaction"; ?>[label]" value="" placeholder="<?php echo esc_html__('Enter Label','customize-my-account-for-woocommerce-pro'); ?>" /><br>
                                    
                                </div>

                                <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo esc_html__( 'Close' ,'customize-my-account-for-woocommerce-pro'); ?></button>
                                <button type="submit" name="submit"  class="btn btn-primary wcmamtx_new_actions"><?php echo esc_html__( 'Add New Action' ,'customize-my-account-for-woocommerce-pro'); ?>

                                </button>
                            </form>

                        </div>
                        <div class="modal-footer">

                        </div>
                    </div>
                </div>
            </div>


        </div>
		<?php
	}


	
	public function wcmamtx_options_tab_wrap() {

		$current_tab = isset( $_GET['tab'] ) ? $_GET['tab'] : sanitize_text_field($this->wcmamtx_notices_settings_page);

        echo '<a target="_blank" class="btn wcmamtx_docs_buton btn-success" href="https://www.sysbasics.com/knowledge-base/category/woocommerce-customize-my-account-pro/"><span class="wcmamtx_docs_icon dashicons dashicons-welcome-learn-more"></span>Documentation</a>';
        echo '<a target="_blank" class="btn wcmamtx_support_buton btn-warning" href="https://www.sysbasics.com/support/"><span class="wcmamtx_docs_icon dashicons dashicons-admin-generic"></span>Support</a>';

        echo '<h2 class="nav-tab-wrapper">';

		foreach ( $this->wcmamtx_plugin_settings_tab as $tab_key => $tab_caption ) {

			$active = $current_tab == $tab_key ? 'nav-tab-active' : '';

			echo '<a class="nav-tab ' . esc_html__($active) . '" href="?page=' . esc_html__($this->wcmamtx_notices_settings_page) . '&tab=' . esc_html__($tab_key) . '">' . esc_html__($tab_caption) . '</a>';	

		}

		echo '</h2>';

	}

    /**
     * render accordion content from $key and $value
     */

	public function get_accordion_content($key,$name,$core_fields,$value = null,$old_value = null,$third_party = null) {
	     
		$third_party = isset($value['third_party']) ? $value['third_party'] : $third_party; 

		if (isset($third_party)) {
			$key = strtolower($key);
			$key = str_replace(' ', '_', $key);
		}
        
        ?>
        <li keyvalue="<?php echo $key; ?>" litype="<?php if (isset($value['wcmamtx_type'])) { echo  $value['wcmamtx_type']; } ?>" class="<?php if (isset($value['show']) && ($value['show'] == "no"))  { echo "wcmamtx_disabled"; } ?> wcmamtx_endpoint <?php echo $key; ?> <?php if (isset($value['wcmamtx_type']) && ($value['wcmamtx_type'] == "group")) { echo 'group'; } ?> <?php if (preg_match('/\b'.$key.'\b/', $core_fields )) { echo "core"; } ?>">

            <?php $this->get_main_li_content($key,$name,$core_fields,$value,$old_value,$third_party); ?>


        </li> <?php
        
    }


    /**
     * render accordion content from $key and $value
     */

	public function get_order_content($key,$name,$core_fields,$value = null,$old_value = null,$third_party = null) {
	     
		$third_party = isset($value['third_party']) ? $value['third_party'] : $third_party; 

		if (isset($third_party)) {
			$key = strtolower($key);
			$key = str_replace(' ', '_', $key);
		}
        
        ?>
        <li keyvalue="<?php echo $key; ?>" litype="<?php if (isset($value['wcmamtx_type'])) { echo  $value['wcmamtx_type']; } ?>" class="<?php if (isset($value['show']) && ($value['show'] == "no"))  { echo "wcmamtx_disabled"; } ?> wcmamtx_endpoint <?php echo $key; ?> <?php if (isset($value['wcmamtx_type']) && ($value['wcmamtx_type'] == "group")) { echo 'group'; } ?> <?php if (preg_match('/\b'.$key.'\b/', $core_fields )) { echo "core"; } ?>">

            <?php $this->get_main_order_content($key,$name,$core_fields,$value,$old_value,$third_party); ?>


        </li> <?php
        
    }


    /**
     * render accordion content from $key and $value
     */

    public function get_download_content($key,$name,$core_fields,$value = null,$old_value = null,$third_party = null) {
         
        $third_party = isset($value['third_party']) ? $value['third_party'] : $third_party; 

        if (isset($third_party)) {
            $key = strtolower($key);
            $key = str_replace(' ', '_', $key);
        }
        
        ?>
        <li keyvalue="<?php echo $key; ?>" litype="<?php if (isset($value['wcmamtx_type'])) { echo  $value['wcmamtx_type']; } ?>" class="<?php if (isset($value['show']) && ($value['show'] == "no"))  { echo "wcmamtx_disabled"; } ?> wcmamtx_endpoint <?php echo $key; ?> <?php if (isset($value['wcmamtx_type']) && ($value['wcmamtx_type'] == "group")) { echo 'group'; } ?> <?php if (preg_match('/\b'.$key.'\b/', $core_fields )) { echo "core"; } ?>">

            <?php $this->get_main_download_content($key,$name,$core_fields,$value,$old_value,$third_party); ?>


        </li> <?php
        
    }


    /**
     * render accordion content from $key and $value
     */

	public function get_order_actions($key,$name,$core_fields,$value = null,$old_value = null,$third_party = null) {
	     
		$third_party = isset($value['third_party']) ? $value['third_party'] : $third_party; 

		if (isset($third_party)) {
			$key = strtolower($key);
			$key = str_replace(' ', '_', $key);
		}
        
        ?>
        <li keyvalue="<?php echo $key; ?>" litype="<?php if (isset($value['wcmamtx_type'])) { echo  $value['wcmamtx_type']; } ?>" class="<?php if (isset($value['show']) && ($value['show'] == "no"))  { echo "wcmamtx_disabled"; } ?> wcmamtx_endpoint <?php echo $key; ?> <?php if (isset($value['wcmamtx_type']) && ($value['wcmamtx_type'] == "group")) { echo 'group'; } ?> <?php if (preg_match('/\b'.$key.'\b/', $core_fields )) { echo "core"; } ?>">

            <?php $this->get_main_order_actions($key,$name,$core_fields,$value,$old_value,$third_party); ?>


        </li> <?php
        
    }








    public function get_main_order_actions($key,$name,$core_fields,$value = null,$old_value = null,$third_party = null) { 
         
        global $wp_roles;

        $extra_content_core_fields = 'downloads,edit-address,edit-account';
        $exclude_content_core_fields       = 'dashboard,orders,customer-logout';

        
        $wcmamtx_type = 'endpoint';
       
        


        if (isset($value['parent']) && ($value['parent'] != "")) {

        	$wcmamtx_parent = $value['parent'];
        	
        } else {

        	$wcmamtx_parent = 'none';
       
        }



        if ( ! isset( $wp_roles ) ) { 
        	$wp_roles = new WP_Roles();  

        }

        $roles    = $wp_roles->roles;

        $third_party = isset($value['third_party']) ? $value['third_party'] : $third_party;

	    
    	?>

    	<h3>
    		<div class="wcmamtx_accordion_handler">
    			<?php if (preg_match('/\b'.$key.'\b/', $core_fields )) { ?>

    				
    				<input type="checkbox" <?php if ($key == "order-actions") { echo 'disabled="disabled"'; } ?> class="wcmamtx_accordion_onoff" parentkey="<?php echo $key; ?>"  <?php if (isset($value['show']) && ($value['show'] != "no"))  { echo "checked"; } elseif (!isset($value['show'])) { echo 'checked';} ?>>

    			    

    				<input type="hidden" class="<?php echo $key; ?>_hidden_checkbox" value='<?php if (isset($value['show']) && ($value['show'] == "no")) { echo "no"; } else { echo 'yes';} ?>' name='wcmamtx_order_actions[<?php echo $key; ?>][show]'>

    			<?php } else { 

                      if (isset($third_party)) {
                      	 $key = strtolower($key);
                      	 $key = str_replace(' ', '_', $key);
                      }

    				?>
    				<span type="removeicon" parentkey="<?php echo $key; ?>" class="dashicons dashicons-trash wcmamtx_accordion_remove"></span>
    			<?php } ?>
    		</div>

    		<span class="dashicons dashicons-menu-alt "></span><?php if (isset($name) && ($name != "")) { echo $name; } else if ($key == "order-actions") { echo esc_html__('Actions','customize-my-account-for-woocommerce-pro'); } ?>
    		<span class="wcmamtx_type_label">
    			<?php echo esc_html__('Order Action','customize-my-account-for-woocommerce-pro'); ?>
    		</span>

    	</h3>

        <div class="<?php echo $wcmamtx_type; ?>_accordion_content">

        	<table class="wcmamtx_table widefat">

        		<?php if (isset($third_party)) { ?>

        			<tr>
        				<td>
                        
        				</td>
        				<td>
        					<p><?php  echo esc_html__('This is third party endpoint.Some features may not work.','customize-my-account-for-woocommerce-pro'); ?></p>
        					<input type="hidden" name="wcmamtx_order_actions[<?php echo $key; ?>][third_party]" value="yes">
        					<input type="hidden" name="wcmamtx_order_actions[<?php echo $key; ?>][endpoint_name]" value="<?php if (isset($name)) { echo $name; } ?>">
        				</td>

        			</tr>

        		<?php } ?>

                

            	<input type="hidden" class="wcmamtx_accordion_input" name="wcmamtx_order_actions[<?php echo $key; ?>][endpoint_key]" value="<?php if (isset($value['endpoint_key'])) { echo $value['endpoint_key']; } else { echo $key; } ?>">


                

        
                <input type="hidden" name="wcmamtx_order_actions[<?php echo $key; ?>][wcmamtx_type]" value="<?php echo $wcmamtx_type; ?>">

                <input type="hidden" name="wcmamtx_order_actions[<?php echo $key; ?>][parent]" class="wcmamtx_parent_field" value="<?php echo $wcmamtx_parent; ?>">

                <?php if (!isset($third_party) && ($key != "order-actions")) { ?>

                <tr>
                    <td>
                        <label class="wcmamtx_accordion_label"><?php  echo esc_html__('Label','customize-my-account-for-woocommerce-pro'); ?></label>
                    </td>
                    <td>
                       
                        <input type="text" class="wcmamtx_accordion_input" name="wcmamtx_order_actions[<?php echo $key; ?>][endpoint_name]" value="<?php if (isset($value['endpoint_name'])) { echo $value['endpoint_name']; } elseif ($key != "order-actions") { echo $value; } ?>">
                    </td>
            
                </tr>

                <?php } else { ?>

                <tr>

                	<td>
                        <label class="wcmamtx_accordion_label"><?php  echo esc_html__('Label','customize-my-account-for-woocommerce-pro'); ?></label>
                    </td>
                    <td>
                       
                          <label class="wcmamtx_accordion_label"><?php  echo esc_html__('Actions','customize-my-account-for-woocommerce-pro'); ?></label>
                    </td>
                </tr>

                <?php } ?>

                <tr>

                	<td>
                        <label class="wcmamtx_accordion_label">
                        	<?php  echo esc_html__('Url','customize-my-account-for-woocommerce-pro'); ?>
                        </label>
                    </td>
                    <td>
                       
                        <input type="text" class="wcmamtx_accordion_input" name="wcmamtx_order_actions[<?php echo $key; ?>][action_url]" value="<?php if (isset($value['action_url'])) { echo $value['action_url']; } else {
                            echo ''.site_url().'/?order_id={orderid}&trekking={your_custom_meta_key}';
                        } ?>" size="100">
                    </td>
                </tr>

                <tr>
                	<td>
                        
                    </td>
                	<td>
                        <p><?php  echo esc_html__('Example :- '.site_url().'/?order_id={orderid}&trekking={your_custom_meta_key}','customize-my-account-for-woocommerce-pro'); ?></p>
                        <p><?php  echo esc_html__('You can use following variables inside url','customize-my-account-for-woocommerce-pro'); ?></p>
                        <ul>
                        	<li><?php  echo esc_html__('{orderid} = Order ID','customize-my-account-for-woocommerce-pro'); ?></li>
                        	<li><?php  echo esc_html__('{your_custom_meta_key} = Order Custom Field','customize-my-account-for-woocommerce-pro'); ?></li>
                        </ul></td>
                </tr>

                                <tr>
                    <td>
                        <label class="wcmamtx_accordion_label"><?php  echo esc_html__('Icon Settings','customize-my-account-for-woocommerce-pro'); ?></label>
                    </td>
                    <td>
                        <?php 
                             if (isset($value['icon_source']) && ($value['icon_source'] != '')) {
                                $icon_source = $value['icon_source'];
                             } else {
                                $icon_source = 'default';
                             }
                        ?>

                        <div class="wcmamtx_icon_settings_div">
                            
                            <div class="form-check wcmamtx_icon_checkbox">
                                <input class="form-check-input wcmamtx_icon_source_radio" type="radio" name="wcmamtx_order_actions[<?php echo $key; ?>][icon_source]"  value="noicon" <?php if (($icon_source != "custom") || ($icon_source == "dashicon")) { echo 'checked'; } ?>>
                                <label class="form-check-label wcmamtx_icon_checkbox_label">
                                    <?php  echo esc_html__('No Icon','customize-my-account-for-woocommerce-pro'); ?>
                                </label>
                            </div>
                            <div class="form-check wcmamtx_icon_checkbox">
                                <input class="form-check-input wcmamtx_icon_source_radio" type="radio" name="wcmamtx_order_actions[<?php echo $key; ?>][icon_source]"  value="custom" <?php if ($icon_source == "custom") { echo 'checked'; } ?>>
                                <label class="form-check-label wcmamtx_icon_checkbox_label">
                                    <?php  echo esc_html__('Font Awesome Icon','customize-my-account-for-woocommerce-pro'); ?>
                                </label>
                            </div>

                            <div class="form-check wcmamtx_icon_checkbox">
                                <input class="form-check-input wcmamtx_icon_source_radio" type="radio" name="wcmamtx_order_actions[<?php echo $key; ?>][icon_source]"  value="dashicon" <?php if ($icon_source == "dashicon") { echo 'checked'; } ?>>
                                <label class="form-check-label wcmamtx_icon_checkbox_label">
                                    <?php  echo esc_html__('Dashicon','customize-my-account-for-woocommerce-pro'); ?>
                                </label>
                            </div>
                        </div>
                    </td>
            
                </tr>

                <tr class="fa_icon_tr" style= "<?php if ($icon_source == "custom") { echo 'display:table-row;'; } else { echo 'display:none;'; } ?>">
                    <td>
                        <label class="wcmamtx_accordion_label"><?php  echo esc_html__('Icon','customize-my-account-for-woocommerce-pro'); ?></label>
                    </td>
                    <td>

                        <input type="text" class="wcmamtx_iconpicker icon-class-input" name="wcmamtx_order_actions[<?php echo $key; ?>][icon]" value="<?php if (isset($value['icon'])) { echo $value['icon']; } ?>">
                        <button type="button" class="btn btn-primary picker-button"><?php  echo esc_html__('Chose Font Awesome Icon','customize-my-account-for-woocommerce-pro'); ?></button>
                    </td>
            
                </tr>

                <tr class="show_dashicon_tr" style= "<?php if ($icon_source == "dashicon") { echo 'display:table-row;'; } else { echo 'display:none;'; } ?>">
                    <td>
                        <label class="wcmamtx_accordion_label"><?php  echo esc_html__('Icon','customize-my-account-for-woocommerce-pro'); ?></label>
                    </td>
                    <td>

                        <input class="regular-text " id="dashicons_picker_example_<?php echo $key; ?>" type="text" name="wcmamtx_order_actions[<?php echo $key; ?>][dashicon]" value="<?php if (isset($value['dashicon'])) { echo $value['dashicon']; } ?>" />
                        <input class="button dashicons-picker" type="button" value="<?php  echo esc_html__('Chose Dashicon','customize-my-account-for-woocommerce-pro'); ?>" data-target="#dashicons_picker_example_<?php echo $key; ?>" />

                    </td>
            
                </tr>


                
            </table>

        </div>




    <?php 
    
    }


    public function get_main_download_content($key,$name,$core_fields,$value = null,$old_value = null,$third_party = null) { 
         
        global $wp_roles;

        $extra_content_core_fields = 'downloads,edit-address,edit-account';
        $exclude_content_core_fields       = 'dashboard,orders,customer-logout';

        
        $wcmamtx_type = 'endpoint';
       
        


        if (isset($value['parent']) && ($value['parent'] != "")) {

            $wcmamtx_parent = $value['parent'];
            
        } else {

            $wcmamtx_parent = 'none';
       
        }



        if ( ! isset( $wp_roles ) ) { 
            $wp_roles = new WP_Roles();  

        }

        $roles    = $wp_roles->roles;

        $third_party = isset($value['third_party']) ? $value['third_party'] : $third_party;

        
        ?>

        <h3>
            <div class="wcmamtx_accordion_handler">
                <?php if (preg_match('/\b'.$key.'\b/', $core_fields )) { ?>

                    
                    <input type="checkbox" <?php if ($key == "order-actions") { echo 'disabled="disabled"'; } ?> class="wcmamtx_accordion_onoff" parentkey="<?php echo $key; ?>"  <?php if (isset($value['show']) && ($value['show'] != "no"))  { echo "checked"; } elseif (!isset($value['show'])) { echo 'checked';} ?>>

                    

                    <input type="hidden" class="<?php echo $key; ?>_hidden_checkbox" value='<?php if (isset($value['show']) && ($value['show'] == "no")) { echo "no"; } else { echo 'yes';} ?>' name='wcmamtx_download_columns[<?php echo $key; ?>][show]'>

                <?php } else { 

                      if (isset($third_party)) {
                         $key = strtolower($key);
                         $key = str_replace(' ', '_', $key);
                      }

                    ?>
                    <span type="removeicon" parentkey="<?php echo $key; ?>" class="dashicons dashicons-trash wcmamtx_accordion_remove"></span>
                <?php } ?>
            </div>

            <span class="dashicons dashicons-menu-alt "></span><?php if (isset($name) && ($name != "")) { echo $name; } else if ($key == "order-actions") { echo esc_html__('Actions','customize-my-account-for-woocommerce-pro'); } ?>
            <span class="wcmamtx_type_label">
                <?php echo esc_html__('Column','customize-my-account-for-woocommerce-pro'); ?>
            </span>

        </h3>

        <div class="<?php echo $wcmamtx_type; ?>_accordion_content">

            <table class="wcmamtx_table widefat">

                <?php if (isset($third_party)) { ?>

                    <tr>
                        <td>
                        
                        </td>
                        <td>
                            <p><?php  echo esc_html__('This is third party endpoint.Some features may not work.','customize-my-account-for-woocommerce-pro'); ?></p>
                            <input type="hidden" name="wcmamtx_download_columns[<?php echo $key; ?>][third_party]" value="yes">
                            <input type="hidden" name="wcmamtx_download_columns[<?php echo $key; ?>][endpoint_name]" value="<?php if (isset($name)) { echo $name; } ?>">
                        </td>

                    </tr>

                <?php } ?>

                

                <input type="hidden" class="wcmamtx_accordion_input" name="wcmamtx_download_columns[<?php echo $key; ?>][endpoint_key]" value="<?php if (isset($value['endpoint_key'])) { echo $value['endpoint_key']; } else { echo $key; } ?>">


               

        
                <input type="hidden" name="wcmamtx_download_columns[<?php echo $key; ?>][wcmamtx_type]" value="<?php echo $wcmamtx_type; ?>">

                <input type="hidden" name="wcmamtx_download_columns[<?php echo $key; ?>][parent]" class="wcmamtx_parent_field" value="<?php echo $wcmamtx_parent; ?>">

                <?php if (!isset($third_party) && ($key != "order-actions")) { ?>

                <tr>
                    <td>
                        <label class="wcmamtx_accordion_label"><?php  echo esc_html__('Label','customize-my-account-for-woocommerce-pro'); ?></label>
                    </td>
                    <td>
                       
                        <input type="text" class="wcmamtx_accordion_input" name="wcmamtx_download_columns[<?php echo $key; ?>][endpoint_name]" value="<?php if (isset($value['endpoint_name'])) { echo $value['endpoint_name']; } elseif ($key != "order-actions") { echo $value; } ?>">
                    </td>
            
                </tr>

                <?php } else { ?>

                <tr>

                    <td>
                        <label class="wcmamtx_accordion_label"><?php  echo esc_html__('Label','customize-my-account-for-woocommerce-pro'); ?></label>
                    </td>
                    <td>
                       
                          <label class="wcmamtx_accordion_label"><?php  echo esc_html__('Actions','customize-my-account-for-woocommerce-pro'); ?></label>
                    </td>
                </tr>

                <?php } ?>

                <?php if ((!preg_match('/\b'.$key.'\b/', $core_fields ) && ($wcmamtx_type == 'endpoint')) && (!isset($third_party))) { 
                    
                    $ordervalues = wcmamtx_get_meta_values();
                    
                    

                    ?>   

                    <tr>

                        <td>
                            <label class="wcmamtx_accordion_label">
                                <?php  echo esc_html__('Value','customize-my-account-for-woocommerce-pro'); ?>
                                    
                            </label>
                        </td>
                        <td>

                            <select class="wcmamtx_value_select" name="wcmamtx_download_columns[<?php echo $key; ?>][value]">
                                <option value="">
                                    <?php  echo esc_html__('Chose an Option','customize-my-account-for-woocommerce-pro'); ?>
                                        
                                </option>
                                <option value="checkoutfield" <?php if (isset($value['value']) && ($value['value'] == "checkoutfield" )) { echo 'selected'; } ?>>
                                    <?php  echo esc_html__('Checkout Field','customize-my-account-for-woocommerce-pro'); ?>
                                        
                                </option>
                                <option value="orderid" <?php if (isset($value['value']) && ($value['value'] == "orderid" )) { echo 'selected'; } ?>>
                                    <?php  echo esc_html__('Order ID','customize-my-account-for-woocommerce-pro'); ?>
                                        
                                </option>
                                <option value="customkey" <?php if (isset($value['value']) && ($value['value'] == "customkey" )) { echo 'selected'; } ?>>
                                    <?php  echo esc_html__('Use new custom meta key','customize-my-account-for-woocommerce-pro'); ?>
                                        
                                </option>

                            </select>
                        </td>
                    </tr>

                    <tr class="wcmamtx_customkey_tr" style="<?php if (isset($value['value']) && ($value['value'] == "customkey" )) { echo 'display:;'; } else { echo 'display:none;'; } ?>">
                        <td>
                           <label class="wcmamtx_accordion_label"><?php  echo esc_html__('Key','customize-my-account-for-woocommerce-pro'); ?></label>
                       </td>
                       <td>


                        <input type="text" class="wcmamtx_accordion_input" name="wcmamtx_download_columns[<?php echo $key; ?>][custom_key]" value="<?php if (isset($value['custom_key'])) { echo $value['custom_key']; } else { echo $key; } ?>">
                        </td>

                    </tr>

                    <tr class="wcmamtx_checkoutfield_tr" style="<?php if (isset($value['value']) && ($value['value'] == "checkoutfield" )) { echo 'display:;'; } else { echo 'display:none;'; } ?>">
                        <td>
                           <label class="wcmamtx_accordion_label"><?php  echo esc_html__('Checkout Field','customize-my-account-for-woocommerce-pro'); ?></label>
                        </td>
                        <td>
                            <select class="checkout_field_rule_parentfield" name="wcmamtx_download_columns[<?php echo $key; ?>][custom_key]">
                                
                                
                                <optgroup label="<?php echo esc_html__( 'Billing Fields' ,'wcmamtx'); ?>">
                                    <?php

                                    $billing_settings = (array) get_option('wcmamtx_billing_settings');
                                    

                                    if (sysbasics_checkout_mode =="on") { 
                                      
                                        $billing_settings = $billing_settings;

                                        

                                    } else {
                                        global $woocommerce;
                                        $countries     = new WC_Countries();

                                        $billing_settings  = $countries->get_address_fields( $countries->get_base_country(),'billing_');
                                        
                                    }

                                    foreach ($billing_settings as $optionkey=>$optionvalue) { 

                                        if ( (isset ($optionvalue['type']) && ($optionvalue['type'] == 'email'))) { 

                                        } else { 

                                            if (isset($optionvalue['label']))  { 

                                                $optionlabel = $optionvalue['label']; 

                                            } else { 

                                                $optionlabel = $optionkey; 
                                            }
                                            ?> 

                                            <option value="<?php echo $optionkey; ?>" <?php if (isset($value['custom_key']) && ($value['custom_key'] == $optionkey)) { echo 'selected';} ?> >
                                                <?php echo $optionlabel; ?>
                                                
                                            </option>

                                            <?php
                                            
                                        } 
                                    } 
                                    ?>
                                </optgroup>

                                <optgroup label="<?php echo esc_html__( 'Shipping Fields' ,'wcmamtx'); ?>">
                                    <?php
                                    $shipping_settings = (array) get_option('wcmamtx_shipping_settings');

                                    if (sysbasics_checkout_mode == "on") { 
                                      
                                        $shipping_settings = $shipping_settings;

                                    } else {
                                        global $woocommerce;
                                        $countries     = new WC_Countries();

                                        $shipping_settings              = $countries->get_address_fields( $countries->get_base_country(),'shipping_');
                                    }

                                    foreach ($shipping_settings as $optionkey=>$optionvalue) { 

                                        if ( (isset ($optionvalue['type']) && ($optionvalue['type'] == 'email'))) { 

                                        } else { 

                                            if (isset($optionvalue['label']))  { 

                                                $optionlabel = $optionvalue['label']; 

                                            } else { 

                                                $optionlabel = $optionkey; 
                                            }
                                            ?> 

                                            <option value="<?php echo $optionkey; ?>" <?php if (isset($value['custom_key']) && ($value['custom_key'] == $optionkey)) { echo 'selected';} ?>>
                                                <?php echo $optionlabel; ?>
                                                
                                            </option>

                                            <?php
                                            
                                        } 
                                    } 
                                    ?>
                                </optgroup>

                                <?php

                                $additional_settings  = (array) get_option('wcmamtx_additional_settings');
                                $additional_settings  = array_filter($additional_settings);

                                if (isset($additional_settings) && (sizeof($additional_settings) >= 1)) { 
                                    $conditional_fields_dropdown = $additional_settings;
                                } else {
                                    $conditional_fields_dropdown = array();
                                }




                                if (count($conditional_fields_dropdown) != 0) { ?>

                                    <optgroup label="<?php echo esc_html__( 'Additional Fields' ,'wcmamtx'); ?>">

                                        <?php 

                                        


                                        foreach ($conditional_fields_dropdown as $optionkey=>$optionvalue) { 

                                            if ( (isset ($optionvalue['type']) && ($optionvalue['type'] == 'email')) || (preg_match('/\b'.$optionkey.'\b/', $country_fields ))) { 

                                            } else { 

                                                if (isset($optionvalue['label']))  { 

                                                    $optionlabel = $optionvalue['label']; 

                                                } else { 

                                                    $optionlabel = $optionkey; 
                                                }
                                                ?> 

                                                <option value="<?php echo $optionkey; ?>" <?php if (isset($value['custom_key']) && ($value['custom_key'] == $optionkey)) { echo 'selected';} ?>>
                                                    <?php echo $optionlabel; ?>
                                                    
                                                </option>

                                                <?php
                                                
                                            } 
                                        } 
                                        

                                        
                                        ?>

                                    </optgroup>

                                <?php } ?>
                                

                            </select>
                        </td>

                    </tr>

                <?php } ?>
                
            </table>

        </div>




    <?php 
    
    }


    public function get_main_order_content($key,$name,$core_fields,$value = null,$old_value = null,$third_party = null) { 
         
        global $wp_roles;

        $extra_content_core_fields = 'downloads,edit-address,edit-account';
        $exclude_content_core_fields       = 'dashboard,orders,customer-logout';

        
        $wcmamtx_type = 'endpoint';
       
        


        if (isset($value['parent']) && ($value['parent'] != "")) {

        	$wcmamtx_parent = $value['parent'];
        	
        } else {

        	$wcmamtx_parent = 'none';
       
        }



        if ( ! isset( $wp_roles ) ) { 
        	$wp_roles = new WP_Roles();  

        }

        $roles    = $wp_roles->roles;

        $third_party = isset($value['third_party']) ? $value['third_party'] : $third_party;

	    
    	?>

    	<h3>
    		<div class="wcmamtx_accordion_handler">
    			<?php if (preg_match('/\b'.$key.'\b/', $core_fields )) { ?>

    				
    				<input type="checkbox" <?php if ($key == "order-actions") { echo 'disabled="disabled"'; } ?> class="wcmamtx_accordion_onoff" parentkey="<?php echo $key; ?>"  <?php if (isset($value['show']) && ($value['show'] != "no"))  { echo "checked"; } elseif (!isset($value['show'])) { echo 'checked';} ?>>

    			    

    				<input type="hidden" class="<?php echo $key; ?>_hidden_checkbox" value='<?php if (isset($value['show']) && ($value['show'] == "no")) { echo "no"; } else { echo 'yes';} ?>' name='wcmamtx_order_settings[<?php echo $key; ?>][show]'>

    			<?php } else { 

                      if (isset($third_party)) {
                      	 $key = strtolower($key);
                      	 $key = str_replace(' ', '_', $key);
                      }

    				?>
    				<span type="removeicon" parentkey="<?php echo $key; ?>" class="dashicons dashicons-trash wcmamtx_accordion_remove"></span>
    			<?php } ?>
    		</div>

    		<span class="dashicons dashicons-menu-alt "></span><?php if (isset($name) && ($name != "")) { echo $name; } else if ($key == "order-actions") { echo esc_html__('Actions','customize-my-account-for-woocommerce-pro'); } ?>
    		<span class="wcmamtx_type_label">
    			<?php echo esc_html__('Column','customize-my-account-for-woocommerce-pro'); ?>
    		</span>

    	</h3>

        <div class="<?php echo $wcmamtx_type; ?>_accordion_content">

        	<table class="wcmamtx_table widefat">

        		<?php if (isset($third_party)) { ?>

        			<tr>
        				<td>
                        
        				</td>
        				<td>
        					<p><?php  echo esc_html__('This is third party endpoint.Some features may not work.','customize-my-account-for-woocommerce-pro'); ?></p>
        					<input type="hidden" name="wcmamtx_order_settings[<?php echo $key; ?>][third_party]" value="yes">
        					<input type="hidden" name="wcmamtx_order_settings[<?php echo $key; ?>][endpoint_name]" value="<?php if (isset($name)) { echo $name; } ?>">
        				</td>

        			</tr>

        		<?php } ?>

                

            	<input type="hidden" class="wcmamtx_accordion_input" name="wcmamtx_order_settings[<?php echo $key; ?>][endpoint_key]" value="<?php if (isset($value['endpoint_key'])) { echo $value['endpoint_key']; } else { echo $key; } ?>">


               

        
                <input type="hidden" name="wcmamtx_order_settings[<?php echo $key; ?>][wcmamtx_type]" value="<?php echo $wcmamtx_type; ?>">

                <input type="hidden" name="wcmamtx_order_settings[<?php echo $key; ?>][parent]" class="wcmamtx_parent_field" value="<?php echo $wcmamtx_parent; ?>">

                <?php if (!isset($third_party) && ($key != "order-actions")) { ?>

                <tr>
                    <td>
                        <label class="wcmamtx_accordion_label"><?php  echo esc_html__('Label','customize-my-account-for-woocommerce-pro'); ?></label>
                    </td>
                    <td>
                       
                        <input type="text" class="wcmamtx_accordion_input" name="wcmamtx_order_settings[<?php echo $key; ?>][endpoint_name]" value="<?php if (isset($value['endpoint_name'])) { echo $value['endpoint_name']; } elseif ($key != "order-actions") { echo $value; } ?>">
                    </td>
            
                </tr>

                <?php } else { ?>

                <tr>

                	<td>
                        <label class="wcmamtx_accordion_label"><?php  echo esc_html__('Label','customize-my-account-for-woocommerce-pro'); ?></label>
                    </td>
                    <td>
                       
                          <label class="wcmamtx_accordion_label"><?php  echo esc_html__('Actions','customize-my-account-for-woocommerce-pro'); ?></label>
                    </td>
                </tr>

                <?php } ?>

                <?php if ((!preg_match('/\b'.$key.'\b/', $core_fields ) && ($wcmamtx_type == 'endpoint')) && (!isset($third_party))) { 
                    
                    $ordervalues = wcmamtx_get_meta_values();
                    
                    

                	?>   

                	<tr>

                		<td>
                			<label class="wcmamtx_accordion_label">
                				<?php  echo esc_html__('Value','customize-my-account-for-woocommerce-pro'); ?>
                					
                			</label>
                		</td>
                		<td>

                			<select class="wcmamtx_value_select" name="wcmamtx_order_settings[<?php echo $key; ?>][value]">
                				<option value="">
                					<?php  echo esc_html__('Chose an Option','customize-my-account-for-woocommerce-pro'); ?>
                						
                				</option>
                                <option value="checkoutfield" <?php if (isset($value['value']) && ($value['value'] == "checkoutfield" )) { echo 'selected'; } ?>>
                                    <?php  echo esc_html__('Checkout Field','customize-my-account-for-woocommerce-pro'); ?>
                                        
                                </option>
                				<option value="orderid" <?php if (isset($value['value']) && ($value['value'] == "orderid" )) { echo 'selected'; } ?>>
                					<?php  echo esc_html__('Order ID','customize-my-account-for-woocommerce-pro'); ?>
                						
                				</option>
                                <option value="customkey" <?php if (isset($value['value']) && ($value['value'] == "customkey" )) { echo 'selected'; } ?>>
                					<?php  echo esc_html__('Use new custom meta key','customize-my-account-for-woocommerce-pro'); ?>
                						
                				</option>

                			</select>
                		</td>
                	</tr>

                	<tr class="wcmamtx_customkey_tr" style="<?php if (isset($value['value']) && ($value['value'] == "customkey" )) { echo 'display:;'; } else { echo 'display:none;'; } ?>">
                        <td>
                           <label class="wcmamtx_accordion_label"><?php  echo esc_html__('Key','customize-my-account-for-woocommerce-pro'); ?></label>
                       </td>
                       <td>

                        <input type="text" default_key="<?php echo $key; ?>" class="wcmamtx_accordion_input_custom_key" name="wcmamtx_order_settings[<?php echo $key; ?>][custom_key]" value="<?php if (isset($value['custom_key'])) { echo $value['custom_key']; } ?>">
                        </td>

                    </tr>

                    <tr class="wcmamtx_checkoutfield_tr" style="<?php if (isset($value['value']) && ($value['value'] == "checkoutfield" )) { echo 'display:;'; } else { echo 'display:none;'; } ?>">
                        <td>
                           <label class="wcmamtx_accordion_label"><?php  echo esc_html__('Checkout Field','customize-my-account-for-woocommerce-pro'); ?></label>
                        </td>
                        <td>
                            <select class="checkout_field_rule_parentfield" name="wcmamtx_order_settings[<?php echo $key; ?>][custom_key2]">
                                
                                
                                <optgroup label="<?php echo esc_html__( 'Billing Fields' ,'wcmamtx'); ?>">
                                    <?php

                                    $billing_settings = (array) get_option('wcmamtx_billing_settings');
                                    

                                    if (sysbasics_checkout_mode =="on") { 
                                      
                                        $billing_settings = $billing_settings;

                                        

                                    } else {
                                        global $woocommerce;
                                        $countries     = new WC_Countries();

                                        $billing_settings  = $countries->get_address_fields( $countries->get_base_country(),'billing_');
                                        
                                    }

                                    foreach ($billing_settings as $optionkey=>$optionvalue) { 

                                        if ( (isset ($optionvalue['type']) && ($optionvalue['type'] == 'email'))) { 

                                        } else { 

                                            if (isset($optionvalue['label']))  { 

                                                $optionlabel = $optionvalue['label']; 

                                            } else { 

                                                $optionlabel = $optionkey; 
                                            }
                                            ?> 

                                            <option value="<?php echo $optionkey; ?>" <?php if (isset($value['custom_key2']) && ($value['custom_key2'] == $optionkey)) { echo 'selected';} ?> >
                                                <?php echo $optionlabel; ?>
                                                
                                            </option>

                                            <?php
                                            
                                        } 
                                    } 
                                    ?>
                                </optgroup>

                                <optgroup label="<?php echo esc_html__( 'Shipping Fields' ,'wcmamtx'); ?>">
                                    <?php
                                    $shipping_settings = (array) get_option('wcmamtx_shipping_settings');

                                    if (sysbasics_checkout_mode == "on") { 
                                      
                                        $shipping_settings = $shipping_settings;

                                    } else {
                                        global $woocommerce;
                                        $countries     = new WC_Countries();

                                        $shipping_settings              = $countries->get_address_fields( $countries->get_base_country(),'shipping_');
                                    }

                                    foreach ($shipping_settings as $optionkey=>$optionvalue) { 

                                        if ( (isset ($optionvalue['type']) && ($optionvalue['type'] == 'email'))) { 

                                        } else { 

                                            if (isset($optionvalue['label']))  { 

                                                $optionlabel = $optionvalue['label']; 

                                            } else { 

                                                $optionlabel = $optionkey; 
                                            }
                                            ?> 

                                            <option value="<?php echo $optionkey; ?>" <?php if (isset($value['custom_key2']) && ($value['custom_key2'] == $optionkey)) { echo 'selected';} ?>>
                                                <?php echo $optionlabel; ?>
                                                
                                            </option>

                                            <?php
                                            
                                        } 
                                    } 
                                    ?>
                                </optgroup>

                                <?php

                                $additional_settings  = (array) get_option('wcmamtx_additional_settings');
                                $additional_settings  = array_filter($additional_settings);

                                if (isset($additional_settings) && (sizeof($additional_settings) >= 1)) { 
                                    $conditional_fields_dropdown = $additional_settings;
                                } else {
                                    $conditional_fields_dropdown = array();
                                }




                                if (count($conditional_fields_dropdown) != 0) { ?>

                                    <optgroup label="<?php echo esc_html__( 'Additional Fields' ,'wcmamtx'); ?>">

                                        <?php 

                                        


                                        foreach ($conditional_fields_dropdown as $optionkey=>$optionvalue) { 

                                            if ( (isset ($optionvalue['type']) && ($optionvalue['type'] == 'email')) || (preg_match('/\b'.$optionkey.'\b/', $country_fields ))) { 

                                            } else { 

                                                if (isset($optionvalue['label']))  { 

                                                    $optionlabel = $optionvalue['label']; 

                                                } else { 

                                                    $optionlabel = $optionkey; 
                                                }
                                                ?> 

                                                <option value="<?php echo $optionkey; ?>" <?php if (isset($value['custom_key2']) && ($value['custom_key2'] == $optionkey)) { echo 'selected';} ?>>
                                                    <?php echo $optionlabel; ?>
                                                    
                                                </option>

                                                <?php
                                                
                                            } 
                                        } 
                                        

                                        
                                        ?>

                                    </optgroup>

                                <?php } ?>
                                

                            </select>
                        </td>

                    </tr>

                <?php } ?>
                
            </table>

        </div>




    <?php 
    
    }

    public function get_main_li_content($key,$name,$core_fields,$value = null,$old_value = null,$third_party = null) { 
         
        global $wp_roles;

        $extra_content_core_fields = 'downloads,edit-address,edit-account';
        $exclude_content_core_fields       = 'dashboard,orders,customer-logout';

        if (isset($value['wcmamtx_type'])) {

        	$wcmamtx_type = $value['wcmamtx_type'];

        } else {
        	$wcmamtx_type = 'endpoint';
       
        }


        if (isset($value['parent']) && ($value['parent'] != "")) {

        	$wcmamtx_parent = $value['parent'];
        	
        } else {

        	$wcmamtx_parent = 'none';
       
        }



        if ( ! isset( $wp_roles ) ) { 
        	$wp_roles = new WP_Roles();  

        }

        $roles    = $wp_roles->roles;

        $third_party = isset($value['third_party']) ? $value['third_party'] : $third_party;

	    
    	?>

    	<h3>
    		<div class="wcmamtx_accordion_handler">
    			
    				<input type="checkbox" class="wcmamtx_accordion_onoff" parentkey="<?php echo $key; ?>"  <?php if (isset($value['show']) && ($value['show'] != "no"))  { echo "checked"; } elseif (!isset($value['show'])) { echo 'checked';} ?>>
    				<input type="hidden" class="<?php echo $key; ?>_hidden_checkbox" value='<?php if (isset($value['show']) && ($value['show'] == "no")) { echo "no"; } else { echo 'yes';} ?>' name='<?php  echo esc_html__($this->wcmamtx_notices_settings_page); ?>[<?php echo $key; ?>][show]'>
                
    		</div>

    		<span class="dashicons dashicons-menu-alt "></span>
    		
            <?php if (preg_match('/\b'.$key.'\b/', $core_fields )) { ?>
                <?php } else { 

                      if (isset($third_party)) {
                         $key = strtolower($key);
                         $key = str_replace(' ', '_', $key);
                      }

                    ?>
                    <span type="removeicon" parentkey="<?php echo $key; ?>" class="dashicons dashicons-trash wcmamtx_accordion_remove"></span>
            <?php } ?>
            <?php if (isset($name)) { echo $name; } ?>
            <span class="wcmamtx_type_label">
                <?php echo ucfirst($wcmamtx_type); ?>
            </span>
    	</h3>

        <div class="<?php echo $wcmamtx_type; ?>_accordion_content">

        	<table class="wcmamtx_table widefat">

        		<?php if (isset($third_party)) { ?>

        			<tr>
        				<td>
                        
        				</td>
        				<td>
        					<p><?php  echo esc_html__('This is third party endpoint.Some features may not work.','customize-my-account-for-woocommerce-pro'); ?></p>
        					<input type="hidden" name="<?php  echo esc_html__($this->wcmamtx_notices_settings_page); ?>[<?php echo $key; ?>][third_party]" value="yes">
        					<input type="hidden" name="<?php  echo esc_html__($this->wcmamtx_notices_settings_page); ?>[<?php echo $key; ?>][endpoint_name]" value="<?php if (isset($name)) { echo $name; } ?>">
        				</td>

        			</tr>

        		<?php } ?>

                <?php if ((!preg_match('/\b'.$key.'\b/', $core_fields ) && ($wcmamtx_type == 'endpoint')) && (!isset($third_party))) { ?>   

                <tr>
                    <td>
                    	<label class="wcmamtx_accordion_label"><?php  echo esc_html__('Key','customize-my-account-for-woocommerce-pro'); ?></label>
                    </td>
                    <td>
                        <input type="text" class="wcmamtx_accordion_input" name="<?php  echo esc_html__($this->wcmamtx_notices_settings_page); ?>[<?php echo $key; ?>][endpoint_key]" value="<?php if (isset($value['endpoint_key'])) { echo $value['endpoint_key']; } else { echo $key; } ?>">
                    </td>
            
                </tr>
                <?php } else { ?>

            	    <input type="hidden" class="wcmamtx_accordion_input" name="<?php  echo esc_html__($this->wcmamtx_notices_settings_page); ?>[<?php echo $key; ?>][endpoint_key]" value="<?php if (isset($value['endpoint_key'])) { echo $value['endpoint_key']; } else { echo $key; } ?>">


                <?php  } ?>

        
                <input type="hidden" name="<?php  echo esc_html__($this->wcmamtx_notices_settings_page); ?>[<?php echo $key; ?>][wcmamtx_type]" value="<?php echo $wcmamtx_type; ?>">

                <input type="hidden" name="<?php  echo esc_html__($this->wcmamtx_notices_settings_page); ?>[<?php echo $key; ?>][parent]" class="wcmamtx_parent_field" value="<?php echo $wcmamtx_parent; ?>">

                <?php if (!isset($third_party)) { ?>

                <tr>
                    <td>
                        <label class="wcmamtx_accordion_label"><?php  echo esc_html__('Label','customize-my-account-for-woocommerce-pro'); ?></label>
                    </td>
                    <td>

                        <input type="text" class="wcmamtx_accordion_input" name="<?php  echo esc_html__($this->wcmamtx_notices_settings_page); ?>[<?php echo $key; ?>][endpoint_name]" value="<?php if (isset($value['endpoint_name'])) { echo $value['endpoint_name']; } else { if (preg_match('/\b'.$key.'\b/', $core_fields ) ) { echo $value; } } ?>">
                    </td>
            
                </tr>

                <?php } ?>
                

                <tr>
                    <td>
                        <label class="wcmamtx_accordion_label"><?php  echo esc_html__('Icon Settings','customize-my-account-for-woocommerce-pro'); ?></label>
                    </td>
                    <td>
                    	<?php 
                             if (isset($value['icon_source']) && ($value['icon_source'] != '')) {
                             	$icon_source = $value['icon_source'];
                             } else {
                             	$icon_source = 'default';
                             }
                    	?>

                    	<div class="wcmamtx_icon_settings_div">
                    		<div class="form-check wcmamtx_icon_checkbox">
                    			<input class="form-check-input wcmamtx_icon_source_radio" type="radio" name="<?php  echo esc_html__($this->wcmamtx_notices_settings_page); ?>[<?php echo $key; ?>][icon_source]"  value="default" <?php if ($icon_source == "default") { echo 'checked'; } ?>>
                    			<label class="form-check-label wcmamtx_icon_checkbox_label" >
                    				<?php  echo esc_html__('Default Icon','customize-my-account-for-woocommerce-pro'); ?>
                    			</label>
                    		</div>
                    		<div class="form-check wcmamtx_icon_checkbox">
                    			<input class="form-check-input wcmamtx_icon_source_radio" type="radio" name="<?php  echo esc_html__($this->wcmamtx_notices_settings_page); ?>[<?php echo $key; ?>][icon_source]"  value="noicon" <?php if ($icon_source == "noicon") { echo 'checked'; } ?>>
                    			<label class="form-check-label wcmamtx_icon_checkbox_label">
                    				<?php  echo esc_html__('No Icon','customize-my-account-for-woocommerce-pro'); ?>
                    			</label>
                    		</div>
                    		<div class="form-check wcmamtx_icon_checkbox">
                    			<input class="form-check-input wcmamtx_icon_source_radio" type="radio" name="<?php  echo esc_html__($this->wcmamtx_notices_settings_page); ?>[<?php echo $key; ?>][icon_source]"  value="custom" <?php if ($icon_source == "custom") { echo 'checked'; } ?>>
                    			<label class="form-check-label wcmamtx_icon_checkbox_label">
                    				<?php  echo esc_html__('Font Awesome Icon','customize-my-account-for-woocommerce-pro'); ?>
                    			</label>
                    		</div>

                    		<div class="form-check wcmamtx_icon_checkbox">
                    			<input class="form-check-input wcmamtx_icon_source_radio" type="radio" name="<?php  echo esc_html__($this->wcmamtx_notices_settings_page); ?>[<?php echo $key; ?>][icon_source]"  value="dashicon" <?php if ($icon_source == "dashicon") { echo 'checked'; } ?>>
                    			<label class="form-check-label wcmamtx_icon_checkbox_label">
                    				<?php  echo esc_html__('Dashicon','customize-my-account-for-woocommerce-pro'); ?>
                    			</label>
                    		</div>

                            <div class="form-check wcmamtx_icon_checkbox">
                                <input class="form-check-input wcmamtx_icon_source_radio" type="radio" name="<?php  echo esc_html__($this->wcmamtx_notices_settings_page); ?>[<?php echo $key; ?>][icon_source]"  value="upload" <?php if ($icon_source == "upload") { echo 'checked'; } ?>>
                                <label class="form-check-label wcmamtx_icon_checkbox_label">
                                    <?php  echo esc_html__('Uplod Icon','customize-my-account-for-woocommerce-pro'); ?>
                                </label>
                            </div>
                    	</div>
                    </td>
            
                </tr>

                <tr class="fa_icon_tr" style= "<?php if ($icon_source == "custom") { echo 'display:table-row;'; } else { echo 'display:none;'; } ?>">
                    <td>
                        <label class="wcmamtx_accordion_label"><?php  echo esc_html__('Icon','customize-my-account-for-woocommerce-pro'); ?></label>
                    </td>
                    <td>

                        <input type="text" class="wcmamtx_iconpicker icon-class-input" name="<?php  echo esc_html__($this->wcmamtx_notices_settings_page); ?>[<?php echo $key; ?>][icon]" value="<?php if (isset($value['icon'])) { echo $value['icon']; } ?>">
                        <button type="button" class="btn btn-primary picker-button"><?php  echo esc_html__('Chose Font Awesome Icon','customize-my-account-for-woocommerce-pro'); ?></button>
                    </td>
            
                </tr>

                <tr class="show_dashicon_tr" style= "<?php if ($icon_source == "dashicon") { echo 'display:table-row;'; } else { echo 'display:none;'; } ?>">
                    <td>
                        <label class="wcmamtx_accordion_label"><?php  echo esc_html__('Icon','customize-my-account-for-woocommerce-pro'); ?></label>
                    </td>
                    <td>

                        <input class="regular-text " id="dashicons_picker_example_<?php echo $key; ?>" type="text" name="<?php  echo esc_html__($this->wcmamtx_notices_settings_page); ?>[<?php echo $key; ?>][dashicon]" value="<?php if (isset($value['dashicon'])) { echo $value['dashicon']; } ?>" />
                        <input class="button dashicons-picker" type="button" value="<?php  echo esc_html__('Chose Dashicon','customize-my-account-for-woocommerce-pro'); ?>" data-target="#dashicons_picker_example_<?php echo $key; ?>" />

                    </td>
            
                </tr>

                <tr class="show_upload_tr" style= "<?php if ($icon_source == "upload") { echo 'display:table-row;'; } else { echo 'display:none;'; } ?>">
                    <td>
                        <label class="wcmamtx_accordion_label"><?php  echo esc_html__('Upload Icon','customize-my-account-for-woocommerce-pro'); ?></label>
                    </td>
                    <td>
                        <?php

                        $swatchimage = isset($value['upload_icon']) ? $value['upload_icon'] : "";

                         if (isset($swatchimage)) {
                            $swatchurl     = wp_get_attachment_thumb_url( $swatchimage );
                         } 
                        ?>
                        <div class="facility_thumbnail" id="facility_thumbnail_<?php echo $key; ?>" style="float:left;">
                            <img src="<?php if (isset($swatchurl) && ($swatchurl != '')) { echo $swatchurl; } else { echo wcmamtx_placeholder_img_src(); }  ?>" width="60px" height="60px" />
                            <div  class="image-upload-div" idval="<?php echo $key; ?>" >
                                <input type="hidden" class="facility_thumbnail_id_<?php echo $key; ?>" name="<?php  echo esc_html__($this->wcmamtx_notices_settings_page); ?>[<?php echo $key; ?>][upload_icon]" value="<?php if (isset($swatchimage)) { echo $swatchimage; } ?>"/>
                                <button type="submit" class="upload_image_button_<?php echo $key; ?> button"><?php echo esc_html__( 'Upload/Add image', 'wcva' ); ?></button>
                                <button type="submit" class="remove_image_button_<?php echo $key; ?> button"><?php echo esc_html__( 'Remove image', 'wcva' ); ?></button>
                            </div>
                        </div>


                    </td>

                </tr>

                <?php if  ((wcmamtx_wpmlsticky_mode == "on") && ($wcmamtx_type != 'group')) { ?>

                     <tr>
                        <td>
                            <label class="wcmamtx_accordion_label"><?php  echo esc_html__('WPML Sticky Links','customize-my-account-for-woocommerce-pro'); ?></label>
                        </td>
                        <td>    
                            <input data-toggle="toggle" data-size="small" class="wcmamtx_accordion_input wcmamtx_accordion_checkbox form-check-input" type="checkbox" name="wcmamtx_advanced_settings[<?php echo $key; ?>][exclude_wpml_sticky]" <?php if (isset($value['exclude_wpml_sticky']) && ($value['exclude_wpml_sticky'] == "01")) { echo 'checked'; } ?> value="01">

                            <p class="wpml_sticky_para"><?php  echo esc_html__('Exclude from WPML Sticky Url to avoid transforming into PageID','customize-my-account-for-woocommerce-pro'); ?></p>
                        </td>
                    </tr>
                <?php } else { ?>
                    
                     <input type="hidden" name="wcmamtx_advanced_settings[<?php echo $key; ?>][exclude_wpml_sticky]"  value="<?php if (isset($value['exclude_wpml_sticky']) ) { echo $value['exclude_wpml_sticky']; } ?>">

                <?php } 

                if ($wcmamtx_type == 'link') {     
                ?>
                

                <tr>
                    <td>
                        <label class="wcmamtx_accordion_label"><?php  echo esc_html__('Link url','customize-my-account-for-woocommerce-pro'); ?></label>
                    </td>
                    <td>
                         <input class="wcmamtx_accordion_input" type="text" name="wcmamtx_advanced_settings[<?php echo $key; ?>][link_inputtarget]" value="<?php if (isset($value['link_inputtarget']) && ($value['link_inputtarget'] != '')) { echo ($value['link_inputtarget']); } else { echo '#';} ?>" size="70">
                    </td>
            
                </tr>

                <tr>
                    <td>
                    	<label class="wcmamtx_accordion_label"><?php  echo esc_html__('Open in new tab','customize-my-account-for-woocommerce-pro'); ?></label>
                    </td>
                    <td>    
                        <input data-toggle="toggle" data-size="sm" class="wcmamtx_accordion_input wcmamtx_accordion_checkbox checkmark" type="checkbox" name="wcmamtx_advanced_settings[<?php echo $key; ?>][link_targetblank]" value="01" <?php if (isset($value['link_targetblank']) && ($value['link_targetblank'] == "01")) { echo 'checked'; } ?>>
                    </td>
                </tr>

                <?php } ?>

                <tr>
                    <td>
                        <label class=" wcmamtx_accordion_label"><?php echo esc_html__('Hide in My Account Navigation','customize-my-account-for-woocommerce-pro'); ?></label>
                    </td>
                    <td>
                        <input type="checkbox" data-toggle="toggle" data-on="<?php  echo esc_html__('Yes','customize-my-account-for-woocommerce-pro'); ?>" data-off="<?php  echo esc_html__('No','customize-my-account-for-woocommerce-pro'); ?>" data-size="sm" class="wcmamtx_accordion_input wcmamtx_accordion_checkbox checkmark" ype="checkbox" name="wcmamtx_advanced_settings[<?php echo $key; ?>][hide_in_navigation]" value="01" <?php if (isset($value['hide_in_navigation']) && ($value['hide_in_navigation'] == "01")) { echo 'checked'; } ?>>
               
                    </td>
                </tr>

                <tr>
                    <td>
                        <label class=" wcmamtx_accordion_label"><?php echo esc_html__('Hide in dashboard links','customize-my-account-for-woocommerce-pro'); ?></label>
                    </td>
                    <td>
                        <input type="checkbox" data-toggle="toggle" data-on="<?php  echo esc_html__('Yes','customize-my-account-for-woocommerce-pro'); ?>" data-off="<?php  echo esc_html__('No','customize-my-account-for-woocommerce-pro'); ?>" data-size="sm" class="wcmamtx_accordion_input wcmamtx_accordion_checkbox checkmark" ype="checkbox" name="wcmamtx_advanced_settings[<?php echo $key; ?>][hide_dashboard_links]" value="01" <?php if (isset($value['hide_dashboard_links']) && ($value['hide_dashboard_links'] == "01")) { echo 'checked'; } ?>>
               
                    </td>
                </tr>


                <tr>
                    <td>
                        <label class=" wcmamtx_accordion_label"><?php echo esc_html__('Hide in My Account Menu widget','customize-my-account-for-woocommerce-pro'); ?></label>
                    </td>
                    <td>
                        <input type="checkbox" data-toggle="toggle" data-on="<?php  echo esc_html__('Yes','customize-my-account-for-woocommerce-pro'); ?>" data-off="<?php  echo esc_html__('No','customize-my-account-for-woocommerce-pro'); ?>" data-size="sm" class="wcmamtx_accordion_input wcmamtx_accordion_checkbox checkmark" ype="checkbox" name="wcmamtx_advanced_settings[<?php echo $key; ?>][hide_myaccount_widget]" value="01" <?php if (isset($value['hide_myaccount_widget']) && ($value['hide_myaccount_widget'] == "01")) { echo 'checked'; } ?>>
               
                    </td>
                </tr>

                                <?php if ($key == "dashboard") { ?>

                    <tr>
                        <td>
                            <label class=" wcmamtx_accordion_label"><?php echo esc_html__('Hide hello, Username text','customize-my-account-for-woocommerce-pro'); ?></label>
                        </td>
                        <td>
                            <input type="checkbox" data-toggle="toggle" data-on="<?php  echo esc_html__('Yes','customize-my-account-for-woocommerce-pro'); ?>" data-off="<?php  echo esc_html__('No','customize-my-account-for-woocommerce-pro'); ?>" data-size="sm" class="wcmamtx_accordion_input wcmamtx_accordion_checkbox checkmark" ype="checkbox" name="wcmamtx_advanced_settings[<?php echo $key; ?>][hide_dashboard_hello]" value="01" <?php if (isset($value['hide_dashboard_hello']) && ($value['hide_dashboard_hello'] == "01")) { echo 'checked'; } ?>>

                        </td>
                    </tr>

                    <tr>
                        <td>
                            <label class=" wcmamtx_accordion_label"><?php echo esc_html__('Hide introductory text','customize-my-account-for-woocommerce-pro'); ?></label>
                        </td>
                        <td>
                            <input type="checkbox" data-toggle="toggle" data-on="<?php  echo esc_html__('Yes','customize-my-account-for-woocommerce-pro'); ?>" data-off="<?php  echo esc_html__('No','customize-my-account-for-woocommerce-pro'); ?>" data-size="sm" class="wcmamtx_accordion_input wcmamtx_accordion_checkbox checkmark" ype="checkbox" name="wcmamtx_advanced_settings[<?php echo $key; ?>][hide_intro_hello]" value="01" <?php if (isset($value['hide_intro_hello']) && ($value['hide_intro_hello'] == "01")) { echo 'checked'; } ?>>
                            
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <label class="wcmamtx_accordion_label wcmamtx_custom_content_label"><?php  echo esc_html__('Custom Content before dashboard links','customize-my-account-for-woocommerce-pro'); ?></label>
                        </td>
                        <td>    

                            <?php 
                            $editor_content = isset($value['content_dash']) ? $value['content_dash'] : "";

                            

                            $editor_id      = 'wcmamtx_content_'.$key.'';
                            $editor_name    = ''.esc_html__($this->wcmamtx_notices_settings_page).'['.$key.'][content_dash]';

                            wp_editor( $editor_content, $editor_id, $settings = array(
                                'textarea_name' => $editor_name,
                                'editor_height' => 180, // In pixels, takes precedence and has no default value
                                'textarea_rows' => 16
                            ) ); 
                            ?>
                        </td>
                    </tr>

                <?php } ?>


                <tr>
                    <td>
                        <label class="wcmamtxvisibleto wcmamtx_accordion_label"><?php echo esc_html__('Visible to','customize-my-account-for-woocommerce-pro'); ?></label>
                    </td>
                    <td>
                        <select mkey="<?php echo $key; ?>" class="wcmamtxvisibleto" name="wcmamtx_advanced_settings[<?php echo $key; ?>][visibleto]">
                            <option value="all" <?php if ((isset($value['visibleto'])) && ($value['visibleto'] == "all")) { echo "selected"; } ?>><?php echo esc_html__('All roles','customize-my-account-for-woocommerce-pro'); ?></option>
                            
                            <option value="specific_exclude" <?php if ((isset($value['visibleto'])) && ($value['visibleto'] == "specific_exclude")) { echo "selected"; } ?>><?php echo esc_html__('All roles except specified','customize-my-account-for-woocommerce-pro'); ?></option>
                            <option value="specific" <?php if ((isset($value['visibleto'])) && ($value['visibleto'] == "specific")) { echo "selected"; } ?>><?php echo esc_html__('Only specified roles','customize-my-account-for-woocommerce-pro'); ?></option>

                            <option value="specific_exclude_user" <?php if ((isset($value['visibleto'])) && ($value['visibleto'] == "specific_exclude_user")) { echo "selected"; } ?>><?php echo esc_html__('All users except specified','customize-my-account-for-woocommerce-pro'); ?></option>
                            <option value="specific_user" <?php if ((isset($value['visibleto'])) && ($value['visibleto'] == "specific_user")) { echo "selected"; } ?>><?php echo esc_html__('Only specified users','customize-my-account-for-woocommerce-pro'); ?></option>
                        </select>
               
                    </td>
                </tr>

                <?php 

                if (!empty($value['roles'])) { 
                    $chosenrolls = implode(',', $value['roles']); 
                } else { 
                    $chosenrolls=''; 
                } 

                ?>
              
                <tr style="<?php if ((isset($value['visibleto'])) && (($value['visibleto'] == "specific") || ($value['visibleto'] == "specific_exclude"))) { echo "display:table-row;"; } else { echo "display:none;"; } ?>" class="wcmamtxroles_<?php echo $key; ?>">
                    <td>
                        <label class="wcmamtx_roles wcmamtx_accordion_label"><?php echo esc_html__('Select roles','customize-my-account-for-woocommerce-pro'); ?></label>
                    </td>
                    <td>
                        <select data-placeholder="<?php echo esc_html__('Choose Roles','customize-my-account-for-woocommerce-pro'); ?>" name="wcmamtx_advanced_settings[<?php echo $key; ?>][roles][]" class="wcmamtx_roleselect" multiple>
                            <?php foreach ($roles as $rkey => $role) { ?>
                                <option value="<?php echo $rkey; ?>" <?php if (preg_match('/\b'.$rkey.'\b/', $chosenrolls )) { echo 'selected';}?>><?php echo $role['name']; ?></option>
                            <?php } ?>
                        </select>
                    </td>
                </tr>

                <?php 

                if (!empty($value['users'])) { 
                    $chosenusers = $value['users']; 
                } else { 
                    $chosenusers= array(); 
                } 

                

                ?>
              
                <tr style="<?php if ((isset($value['visibleto'])) && (($value['visibleto'] == "specific_exclude_user") || ($value['visibleto'] == "specific_user"))) { echo "display:table-row;"; } else { echo "display:none;"; } ?>" class="wcmamtxusers_<?php echo $key; ?>">
                    <td>
                        <label class="wcmamtx_roles wcmamtx_accordion_label"><?php echo esc_html__('Select users','customize-my-account-for-woocommerce-pro'); ?></label>
                    </td>
                    <td>
                        <select data-placeholder="<?php echo esc_html__('Choose Users','customize-my-account-for-woocommerce-pro'); ?>" name="wcmamtx_advanced_settings[<?php echo $key; ?>][users][]" class="wcmamtx_userselect" multiple>
                            <?php foreach ($chosenusers as $ukey => $uvalue) { 
                                $user = get_user_by( 'id', $uvalue );

                                ?>
                                <option value="<?php echo $uvalue; ?>" selected><?php echo $user->user_login; ?></option>
                            <?php } ?>
                        </select>
                    </td>
                </tr>


			    <?php if (($wcmamtx_type == 'endpoint') && (!preg_match('/\b'.$key.'\b/', $exclude_content_core_fields )) && (!isset($third_party))) { ?>

			    <tr>
                    <td>
                        <label class="wcmamtx_accordion_label wcmamtx_custom_content_label"><?php  echo esc_html__('Custom Content','customize-my-account-for-woocommerce-pro'); ?></label>
                    </td>
                    <td>    
                        
                        <?php 
                            $editor_content = isset($value['content']) ? $value['content'] : "";

                            

                            $editor_id      = 'wcmamtx_content_'.$key.'';
                            $editor_name    = ''.esc_html__($this->wcmamtx_notices_settings_page).'['.$key.'][content]';

                            wp_editor( $editor_content, $editor_id, $settings = array(
                            	'textarea_name' => $editor_name,
                            	'editor_height' => 180, // In pixels, takes precedence and has no default value
                                'textarea_rows' => 16
                            ) ); 
                        ?>
                    </td>
                </tr>

                <?php } ?>


                <?php if (($wcmamtx_type == 'endpoint') && (preg_match('/\b'.$key.'\b/', $extra_content_core_fields ))) { ?>

                	<tr>
                		<td>
                			<label class="wcmamtx_accordion_label"><?php  echo esc_html__('Content Settings','customize-my-account-for-woocommerce-pro'); ?></label>
                		</td>
                		<td>
                			<?php 
                			if (isset($value['content_settings']) && ($value['content_settings'] != '')) {
                				$content_settings = $value['content_settings'];
                			} else {
                				$content_settings = 'after';
                			}
                			?>

                			<div class="wcmamtx_content_settings_div">
                				<div class="form-check wcmamtx_content_checkbox">
                					<input class="form-check-input wcmamtx_content_source_radio" type="radio" name="<?php  echo esc_html__($this->wcmamtx_notices_settings_page); ?>[<?php echo $key; ?>][content_settings]"  value="after" <?php if ($content_settings == "after") { echo 'checked'; } ?>>
                					<label class="form-check-label wcmamtx_icon_checkbox_label" >
                						<?php  echo esc_html__('After Existing Content','customize-my-account-for-woocommerce-pro'); ?>
                					</label>
                				</div>
                				<div class="form-check wcmamtx_content_checkbox">
                					<input class="form-check-input wcmamtx_content_source_radio" type="radio" name="<?php  echo esc_html__($this->wcmamtx_notices_settings_page); ?>[<?php echo $key; ?>][content_settings]"  value="before" <?php if ($content_settings == "before") { echo 'checked'; } ?>>
                					<label class="form-check-label wcmamtx_icon_checkbox_label">
                						<?php  echo esc_html__('Before Existing Content','customize-my-account-for-woocommerce-pro'); ?>
                					</label>
                				</div>
                			</div>
                		</td>

                	</tr>

                <?php } ?>


                <?php if ($wcmamtx_type == 'group') { ?>

                	<tr>
                		<td>
                			<label class="wcmamtx_accordion_label"><?php  echo esc_html__('Open by default','customize-my-account-for-woocommerce-pro'); ?></label>
                		</td>
                		<td>    
                			<input class="wcmamtx_accordion_input wcmamtx_accordion_checkbox form-check-input" type="checkbox" name="wcmamtx_advanced_settings[<?php echo $key; ?>][group_open_default]" <?php if (isset($value['group_open_default']) && ($value['group_open_default'] == "01")) { echo 'checked'; } ?> value="01">
                		</td>
                	</tr>

                <?php } ?>

                <tr>
                    <td>
                        <label class="wcmamtx_accordion_label"><?php  echo esc_html__('Classes','customize-my-account-for-woocommerce-pro'); ?></label>
                    </td>
                    <td>    
                        <input type="text" class="wcmamtx_accordion_input wcmamtx_class_input" name="<?php  echo esc_html__($this->wcmamtx_notices_settings_page); ?>[<?php echo $key; ?>][class]" value="<?php if (isset($value['class'])) { echo $value['class']; } ?>">
                    </td>
                </tr>

                <?php if ($wcmamtx_type != 'group') { ?>

                <?php } ?>

                
            </table>

        </div>

            <?php if (($wcmamtx_type == 'group') && ($value['parent'] == "none")) {

            	$this->get_group_content($name,$key,$value);

            } ?>


    <?php 
    
    }


        public function get_group_content($name,$key,$value) {

        	    $all_keys  = (array) get_option('wcmamtx_advanced_settings');  
                
                $matches   = $this->wcmamtx_search($all_keys, $key);

         
    	    ?>

            	<ol class="wcmamtx_group_items">

                    <?php 
                        foreach($matches as $mkey=>$mvalue) {
                        	$mname             = $mvalue['endpoint_name'];
                        	$core_fields       = 'dashboard,orders,downloads,edit-address,edit-account,customer-logout';


                            $this->get_accordion_content($mkey,$mname,$core_fields,$mvalue,null);
                        }
                    ?>
                
                </ol>
            <?php
                
        }






        public function wcmamtx_search($array, $key) {
          
            $results = array();

            
        
                foreach ($array as $subkey=>$subvalue) {

                	if (isset($subvalue['parent'])) {

                		if ($subvalue['parent'] == $key) {
                    	    $results[$subkey] = $subvalue;
                        }
                	}
                    
                }
            
            return $results;
        }
    


    }
}


new wcmamtx_add_settings_page_class();
?>