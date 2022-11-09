<?php

namespace ASENHA\Classes;

/**
 * Class related to rendering of settings fields on the admin page
 *
 * @since 2.2.0
 */
class Settings_Fields_Render {

/**
 * Render checkbox field as a toggle/switcher
 *
 * @since 1.0.0
 */
function render_checkbox_toggle( $args ) {

	$options = get_option( ASENHA_SLUG_U );

	$field_name = $args['field_name'];
	$field_description = $args['field_description'];
	$field_option_value = ( array_key_exists( $args['field_id'], $options ) ) ? $options[$args['field_id']] : false;

	echo '<input type="checkbox" id="' . esc_attr( $field_name ) . '" class="asenha-field-checkbox" name="' . esc_attr( $field_name ) . '" ' . checked( $field_option_value, true, false ) . '>';
	echo '<label for="' . esc_attr( $field_name ) . '"></label>';

	// For field with additional options / sub-fields, we add a wrapper to enclose field descriptions
	if ( array_key_exists( 'field_options_wrapper', $args ) && $args['field_options_wrapper'] ) {
		// For when the options / sub-fields occupy lengthy vertical space, we add show all / less toggler
		if ( array_key_exists( 'field_options_moreless', $args ) && $args['field_options_moreless'] ) {
			echo '<div class="asenha-field-with-options field-show-more">';
			echo '<a id="' . $args['field_slug'] . '-show-moreless" class="show-more-less show-more" href="#">Expand &#9660;</a>';
			echo '<div class="asenha-field-options-wrapper wrapper-show-more">';
		} else {
			echo '<div class="asenha-field-with-options">';
			echo '<div class="asenha-field-options-wrapper">';
		}

	}

	echo '<div class="asenha-field-description">' . wp_kses_post( $field_description ) . '</div>';

	// For field with additional options / sub-fields, we add wrapper for them
	if ( array_key_exists( 'field_options_wrapper', $args ) && $args['field_options_wrapper'] ) {
		echo '<div class="asenha-subfields" style="display:none"></div>';
	}


	// For field with additional options / sub-fields, we add a wrapper to enclose field descriptions
	if ( array_key_exists( 'field_options_wrapper', $args ) && $args['field_options_wrapper'] ) {
		echo '</div>';
		echo '</div>';
	}

}

/**
 * Render checkbox field as sub-field of a toggle/switcher checkbox
 *
 * @since 1.9.0
 */
function render_checkbox_plain( $args ) {

	$options = get_option( ASENHA_SLUG_U );

	$field_name = $args['field_name'];
	$field_label = $args['field_label'];
	$field_option_value = ( isset( $options[$args['field_id']] ) ) ? $options[$args['field_id']] : false;

	echo '<input type="checkbox" id="' . esc_attr( $field_name ) . '" class="asenha-subfield-checkbox" name="' . esc_attr( $field_name ) . '" ' . checked( $field_option_value, true, false ) . '>';
	echo '<label for="' . esc_attr( $field_name ) . '" class="asenha-subfield-checkbox-label">' . wp_kses_post( $field_label ) . '</label>';

}

/**
 * Render checkbox field as sub-field of a toggle/switcher checkbox
 *
 * @since 1.3.0
 */
function render_checkbox_subfield( $args ) {

	$options = get_option( ASENHA_SLUG_U );

	$field_name = $args['field_name'];
	$field_label = $args['field_label'];
	$field_option_value = ( isset( $options[$args['parent_field_id']][$args['field_id']] ) ) ? $options[$args['parent_field_id']][$args['field_id']] : false;

	echo '<input type="checkbox" id="' . esc_attr( $field_name ) . '" class="asenha-subfield-checkbox" name="' . esc_attr( $field_name ) . '" ' . checked( $field_option_value, true, false ) . '>';
	echo '<label for="' . esc_attr( $field_name ) . '" class="asenha-subfield-checkbox-label">' . wp_kses_post( $field_label ) . '</label>';

}

/**
 * Render text field as sub-field of a toggle/switcher checkbox
 *
 * @since 1.4.0
 */
function render_text_subfield( $args ) {

	$options = get_option( ASENHA_SLUG_U );

	$field_id = $args['field_id'];
	$field_name = $args['field_name'];
	$field_type = $args['field_type'];
	$field_prefix = $args['field_prefix'];
	$field_suffix = $args['field_suffix'];
	$field_description = $args['field_description'];
	$field_option_value = ( isset( $options[$args['field_id']] ) ) ? $options[$args['field_id']] : '';

	if ( $field_id == 'custom_login_slug' ) {
		$placeholder = 'e.g. backend';
	} elseif ( $field_id == 'redirect_after_login_to_slug' ) {
		$placeholder = 'e.g. my-account';
	} elseif ( $field_id == 'redirect_after_logout_to_slug' ) {
		$placeholder = 'e.g. come-visit-again';
	} else {}

	echo $field_prefix . '<input type="text" id="' . esc_attr( $field_name ) . '" class="asenha-subfield-text" name="' . esc_attr( $field_name ) . '" placeholder="' . esc_attr( $placeholder ) . '" value="' . esc_attr( $field_option_value ) . '">' . $field_suffix;
	echo '<label for="' . esc_attr( $field_name ) . '" class="asenha-subfield-checkbox-label">' . esc_html( $field_description ) . '</label>';

}

/**
 * Render textarea field as sub-field of a toggle/switcher checkbox
 *
 * @since 2.3.0
 */
function render_textarea_subfield( $args ) {

	$options = get_option( ASENHA_SLUG_U );

	$field_id = $args['field_id'];
	$field_slug = $args['field_slug'];
	$field_name = $args['field_name'];
	$field_type = $args['field_type'];
	$field_prefix = $args['field_prefix'];
	$field_suffix = $args['field_suffix'];
	$field_description = $args['field_description'];
	$field_option_value = ( isset( $options[$args['field_id']] ) ) ? $options[$args['field_id']] : '';

	echo '<textarea rows="30" class="asenha-subfield-textarea" id="' . esc_attr( $field_name ) . '" name="' . esc_attr( $field_name ) . '">' . esc_textarea( $field_option_value ) . '</textarea>';
}

/**
 * Render sortable menu field
 *
 * @since 2.0.0
 */
function render_sortable_menu( $args ) {

	?>
	<div class="subfield-description">Drag and drop menu items to the desired position and optionally hide them until "Show All" at the bottom of the admin menu is clicked.</div>
	<ul id="custom-admin-menu" class="menu ui-sortable">
	<?php

	global $menu;
	global $submenu;
	$options = get_option( ASENHA_SLUG_U );

	// Get hidden menu items
	if ( array_key_exists( 'custom_menu_hidden', $options ) ) {
		$hidden_menu = $options['custom_menu_hidden'];
		$hidden_menu = explode( ',', $hidden_menu );
	} else {
		$hidden_menu = array();
	}

	$i = 1;

	// Check if there's an existing custom menu order data stored in options

	if ( array_key_exists( 'custom_menu_order', $options ) ) {

		$custom_menu = $options['custom_menu_order'];
		$custom_menu = explode( ',', $custom_menu );

		// Render sortables with data in custom menu order

		foreach ( $custom_menu as $custom_menu_item ) {

			foreach ( $menu as $menu_key => $menu_info ) {

				if ( false !== strpos( $menu_info[4], 'wp-menu-separator' ) ) {
					$menu_item_id = $menu_info[2];
				} else {
					$menu_item_id = $menu_info[5];
				}

				if ( $custom_menu_item == $menu_item_id ) {

					?>
					<li id="<?php echo wp_kses_post( $menu_item_id ); ?>" class="menu-item menu-item-depth-0">
						<div class="menu-item-bar">
							<div class="menu-item-handle ui-sortable-handle">
								<div class="item-title">
									<span class="menu-item-title">
					<?php

					if ( false !== strpos( $menu_info[4], 'wp-menu-separator' ) ) {
						$separator_name = $menu_info[2];
						$separator_name = str_replace( 'separator', 'Separator-', $separator_name );
						$separator_name = str_replace( '--last', '-Last', $separator_name );
						echo '~~ ' . wp_kses_post( $separator_name ) . ' ~~';
					} else {
						echo wp_kses_post( $menu_info[0] );
					}

					?>
									</span>
									<label class="menu-item-checkbox-label">
										<?php
											if ( in_array( $custom_menu_item, $hidden_menu ) ) {
											?>
										<input type="checkbox" class="menu-item-checkbox" data-menu-item-id="<?php echo wp_kses_post( $menu_item_id ); ?>" checked>
										<span>Hide</span>
											<?php
											} else {
											?>
										<input type="checkbox" class="menu-item-checkbox" data-menu-item-id="<?php echo wp_kses_post( $menu_item_id ); ?>">
										<span>Hide</span>
											<?php
											}
										?>
									</label>
								</div>
							</div>
						</div>
					<?php

					$i = 1;

					if ( array_key_exists( $menu_info[2], $submenu ) && @is_countable( $submenu[$menu_info[2]] ) && @sizeof( $submenu[$menu_info[2]] ) > 0 ) {
						?>
						<div class="menu-item-settings wp-clearfix" style="display:none;">
						<?php

						foreach ( $submenu[$menu_info[2]] as $submenu_item ) {

							$i++;

							// echo $submenu_item[0];

						}
						?>
						</div>
						<?php

					}
					?>
					</li>

					<?php

				}

			}

		}

	} else {

		// Render sortables with existing items in the admin menu

		foreach ( $menu as $menu_key => $menu_info ) {

			if ( false !== strpos( $menu_info[4], 'wp-menu-separator' ) ) {
				$menu_item_id = $menu_info[2];
			} else {
				$menu_item_id = $menu_info[5];
			}

			?>
			<li id="<?php echo wp_kses_post( $menu_item_id ); ?>" class="menu-item menu-item-depth-0">
				<div class="menu-item-bar">
					<div class="menu-item-handle ui-sortable-handle">
						<div class="item-title">
							<span class="menu-item-title">
			<?php

			if ( false !== strpos( $menu_info[4], 'wp-menu-separator' ) ) {
				$separator_name = $menu_info[2];
				$separator_name = str_replace( 'separator', 'Separator-', $separator_name );
				$separator_name = str_replace( '--last', '-Last', $separator_name );
				echo '~~ ' . wp_kses_post( $separator_name ) . ' ~~';
			} else {
				echo wp_kses_post( $menu_info[0] );
			}

			?>
							</span>
							<label class="menu-item-checkbox-label">
								<input type="checkbox" class="menu-item-checkbox" data-menu-item-id="<?php echo wp_kses_post( $menu_item_id ); ?>">
								<span>Hide</span>
							</label>
						</div>
					</div>
				</div>
			<?php

			$i = 1;

			if ( array_key_exists( $menu_info[2], $submenu ) && @is_countable( $submenu[$menu_info[2]] ) && @sizeof( $submenu[$menu_info[2]] ) > 0 ) {
				?>
				<div class="menu-item-settings wp-clearfix" style="display:none;">
				<?php

				foreach ( $submenu[$menu_info[2]] as $submenu_item ) {

					$i++;

					// echo $submenu_item[0];

				}
				?>
				</div>
				<?php

			}
			?>
			</li>

			<?php

		}


	}


	?>
	</ul>
	<?php

	$field_id = $args['field_id'];
	$field_name = $args['field_name'];
	$field_description = $args['field_description'];
	$field_option_value = ( isset( $options[$args['field_id']] ) ) ? $options[$args['field_id']] : '';

	// Hidden input field to store custom menu order (from options as is, or sortupdate) upon clicking Save Changes. 
	echo '<input type="hidden" id="' . esc_attr( $field_name ) . '" class="asenha-subfield-text" name="' . esc_attr( $field_name ) . '" value="' . esc_attr( $field_option_value ) . '">';

	$field_id = 'custom_menu_hidden';
	$field_name = ASENHA_SLUG_U . '['. $field_id .']';
	$field_option_value = ( isset( $options[$field_id] ) ) ? $options[$field_id] : '';

	// Hidden input field to store hidden menu itmes (from options as is, or 'Hide' checkbox clicks) upon clicking Save Changes.
	echo '<input type="hidden" id="' . esc_attr( $field_name ) . '" class="asenha-subfield-text" name="' . esc_attr( $field_name ) . '" value="' . esc_attr( $field_option_value ) . '">';

}

}