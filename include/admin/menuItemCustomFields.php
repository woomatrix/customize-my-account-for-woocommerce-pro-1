<?php

$is_show_flag_in_menu_item            = get_option( 'wcmamtx_currency_show_flag_in_menu_item', 1 );
$is_show_currency_name_in_menu_item   = get_option( 'wcmamtx_currency_show_currency_name_in_menu_item', 1 );
$is_show_currency_symbol_in_menu_item = get_option( 'wcmamtx_currency_show_currency_symbol_in_menu_item', 1 );
$is_show_currency_code_in_menu_item   = get_option( 'wcmamtx_currency_show_currency_code_in_menu_item', 1 );
$menu_item_size                       = get_option( 'wcmamtx_currency_menu_item_size', 'small' );
?>
<div class="wcmamtx-currency-menu-item-custom-fields">
	<span class="wcmamtx-currency-menu-item-custom-fields__title"><?php echo esc_html__( 'Switcher elements:', 'wcmamtx-currency' ); ?></span>
	<div class="wcmamtx-currency-menu-item-custom-fields__field">
		<input class="wcmamtx-currency-menu-item-custom-fields__field--checkbox" type="checkbox" id="show-flag" name="show-flag" value="1" <?php echo $is_show_flag_in_menu_item ? 'checked' : null; ?> />
		<label for="show-flag"><?php echo esc_html__( 'Show flag', 'wcmamtx-currency' ); ?></label>
	</div>
	<div class="wcmamtx-currency-menu-item-custom-fields__field">
		<input class="wcmamtx-currency-menu-item-custom-fields__field--checkbox" type="checkbox" id="show-currency-name" name="show-currency-name" value="1" <?php echo $is_show_currency_name_in_menu_item ? 'checked' : null; ?> />
		<label for="show-currency-name"><?php echo esc_html__( 'Show currency name', 'wcmamtx-currency' ); ?></label>
	</div>
	<div class="wcmamtx-currency-menu-item-custom-fields__field">
		<input class="wcmamtx-currency-menu-item-custom-fields__field--checkbox" type="checkbox" id="show-currency-symbol" name="show-currency-symbol" value="1" <?php echo $is_show_currency_symbol_in_menu_item ? 'checked' : null; ?> />
		<label for="show-currency-symbol"><?php echo esc_html__( 'Show currency symbol', 'wcmamtx-currency' ); ?></label>
	</div>
	<div class="wcmamtx-currency-menu-item-custom-fields__field">
		<input class="wcmamtx-currency-menu-item-custom-fields__field--checkbox" type="checkbox" id="show-currency-code" name="show-currency-code" value="1" <?php echo $is_show_currency_code_in_menu_item ? 'checked' : null; ?> />
		<label for="show-currency-code"><?php echo esc_html__( 'Show currency code', 'wcmamtx-currency' ); ?></label>
	</div>
	<div class="wcmamtx-currency-menu-item-custom-fields">
		<span class="wcmamtx-currency-menu-item-custom-fields__title"><?php echo esc_html__( 'Switcher size:', 'wcmamtx-currency' ); ?></span>
		<div class="wcmamtx-currency-menu-item-custom-field__field-group">
			<div class="wcmamtx-currency-menu-item-custom-field__field">
				<input class="wcmamtx-currency-menu-item-custom-fields__field--radio" type="radio" id="menu-item-size-small" name="menu-item-size" value="small" <?php echo 'small' === $menu_item_size ? 'checked' : null; ?> />
				<label for="menu-item-size"><?php echo esc_html__( 'Small', 'wcmamtx-currency' ); ?></label>
			</div>
			<div class="wcmamtx-currency-menu-item-custom-field__field">
				<input class="wcmamtx-currency-menu-item-custom-fields__field--radio" type="radio" id="menu-item-size-medium" name="menu-item-size" value="medium" <?php echo 'medium' === $menu_item_size ? 'checked' : null; ?> />
				<label for="menu-item-size"><?php echo esc_html__( 'Medium', 'wcmamtx-currency' ); ?></label>
			</div>
		</div>
	</div>
</div>
