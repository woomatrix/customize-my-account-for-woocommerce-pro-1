<?php 
$download_settings  = (array) get_option('wcmamtx_download_columns');  
$tabs              = wcmamtx_get_account_download_items();
$core_fields       = 'download-product,download-remaining,download-expires,download-file';

$core_fields_array =  array(
      'download-product'   => __( 'Product', 'customize-my-account-for-woocommerce-pro' ),
      'download-remaining' => __( 'Downloads remaining', 'customize-my-account-for-woocommerce-pro' ),
      'download-expires'   => __( 'Expires', 'customize-my-account-for-woocommerce-pro' ),
      'download-file'      => __( 'Download', 'customize-my-account-for-woocommerce-pro' ),
      
    );

if ((sizeof($download_settings) != 1)) {

  foreach ($tabs as $ikey=>$ivalue) {

    $match = wcmtxka_find_string_match_pro($ikey,$download_settings);

    if (!array_key_exists($ikey, $download_settings) && !array_key_exists($ikey, $core_fields_array) && ($match == "notfound")) {

      

      $download_settings[$ikey] = array(
        'show' => 'yes',
        'third_party' => 'yes',
        'endpoint_key' => $ikey,
        'wcmamtx_type' => 'endpoint',
        'parent'       => 'none',
        'endpoint_name'=> $ivalue,
      );           

    }
  }

}
      


      

      if (!isset($download_settings) || (sizeof($download_settings) == 1)) {
        ?>
        <ol class="accordion wcmamtx-accordion" style="display:none;">
            <?php
                $rownum = 0;
                foreach ($tabs as $key=>$value) {

                    if (preg_match('/\b'.$key.'\b/', $core_fields )) { 

                        $third_party = null;

                    } else {

                        $third_party = 'yes';

                    }

                    
                    
                    $this->get_download_content($key,$value,$core_fields,$value,null,$third_party); 
                    $rownum++;

                }
        ?>
        </ol><?php
      
      } else {

        ?>
        <ol class="accordion wcmamtx-accordion" style="display:none;">
            <?php
                $rownum = 0;
                foreach ($download_settings as $key=>$value) {

                    

                    $name = isset($value['endpoint_name']) ? $value['endpoint_name'] : "";

                    $third_party = isset($value['third_party']) ? $value['third_party'] : null;



                    if (isset($value['parent']) && ($value['parent'] == "none") && ($key != "order-actions")) {

                        $this->get_download_content($key,$name,$core_fields,$value,null,$third_party);
                    } 
                
                    $rownum++;

                }
        ?></ol><?php
      }
    ?>
        <script>
            var wmthash=<?php echo $rownum; ?>;
        </script>
        <div id="iconPicker" class="modal fade">
              <div class="modal-dialog">
                    <div class="modal-content">
                          <div class="modal-header">
                              <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                  <h4 class="modal-title"><?php  echo esc_html__('Icon Picker','customize-my-account-for-woocommerce-pro'); ?></h4>
                          </div>
                          <div class="modal-body">
                              <div>
                                  <ul class="icon-picker-list">
                                    <li>
                                      <a data-class="{{item}} {{activeState}}" data-index="{{index}}">
                                        <span class="{{item}}"></span>
                                         <span class="name-class">{{item}}</span>
                                      </a>
                                    </li>
                                  </ul>
                              </div>
                          </div>
                          <div class="modal-footer">
                                <button type="button" id="change-icon" class="btn btn-success">
                                  <span class="fa fa-check-circle-o"></span>
                                  <?php  echo esc_html__('Use Selected Icon','customize-my-account-for-woocommerce-pro'); ?>
                                </button>
                                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                          </div>
                    </div>
              </div>
        </div>