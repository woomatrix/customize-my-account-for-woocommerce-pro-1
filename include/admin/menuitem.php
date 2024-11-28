<?php
defined( 'ABSPATH' ) || exit;
?>
<div id="customize-my-account-for-woocommerce-dropdown" class="posttypediv">
		<div id="tabs-panel-customize-my-account-for-woocommerce-dropdown" class="tabs-panel tabs-panel-active">
		<ul id="customize-my-account-for-woocommerce-dropdown-checklist" class="categorychecklist form-no-clear">
			<li>
			<label class="menu-item-title">
				<input type="checkbox" class="menu-item-checkbox" name="menu-item[-1][menu-item-object-id]" value="-1"> <?php echo esc_html_e( 'SysBasics My Account Navigation', 'customize-my-account-for-woocommerce-pro' ); ?>
			</label>
			<input type="hidden" class="menu-item-title" name="menu-item[-1][menu-item-title]" value="<?php echo esc_html_e( 'My Account', 'customize-my-account-for-woocommerce-pro' ); ?>">
			<input type="hidden" class="menu-item-classes" name="menu-item[-1][menu-item-classes]" value="customize-my-account-for-woocommerce-dropdown">
			</li>
		</ul>
		</div>
		<p class="button-controls">
		<span class="add-to-menu">
			<input type="submit" class="button-secondary submit-add-to-menu right" value="Add to Menu" name="add-post-type-menu-item" id="submit-customize-my-account-for-woocommerce-dropdown">
			<span class="spinner"></span>
		</span>
		</p>
</div>
