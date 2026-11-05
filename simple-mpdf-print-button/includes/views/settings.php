<?php if ( isset($_GET['settings']) ): ?>
    <div class="notice notice-success is-dismissible">
        <p><span class="dashicons dashicons-yes"></span> Settings saved.</p>
    </div>
<?php endif; ?>

<div class="wrap">
    <h1>Simple PDF Print Button — Plugin Settings</h1>




	<div class="postbox" style="padding: 20px;">
    <form method="post">
        <?php wp_nonce_field( 'smppb_settings_action', 'smppb_settings_nonce' ); ?>

		<p class="submit">
            <input type="submit" name="smppb_settings_save" class="button button-primary" value="Save Settings" >
        </p>
		<table class="widefat fixed striped" style="margin-bottom:16px;" role="presentation">
			<thead>
				<tr>
					<th scope="col">Setting</th>
					<th scope="col">Value</th>
					<th scope="col">Info</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<th scope="row">Hide on homepage</th>
					<td>
						<input type="checkbox" name="smppb_disable_on_home" id="smppb_disable_on_home" value="1" <?php checked( get_option('smppb_disable_on_home'), 1 ); ?> />
						<label for="smppb_disable_on_home">Hide the button</label>
					</td>
					<td><p class="description">Use this to disable the PDF button on the homepage only.</p></td>
				</tr>

				<tr>
					<th scope="row">Show template description</th>
					<td>
						<input type="checkbox" name="smppb_show_template_description" id="smppb_show_template_description" value="1" <?php checked( get_option('smppb_show_template_description'), 1 ); ?> />
						<label for="smppb_show_template_description">Show description</label>
					</td>
					<td><p class="description">Displays extra context text below the button when a template is selected.</p></td>
				</tr>

				<tr>
					<th scope="row"><label for="smppb_button_position">Button position</label></th>
					<td>
						<?php $value = get_option('smppb_button_position'); ?>
						<label><input type="radio" name="smppb_button_position" value="before" <?php checked($value, 'before'); ?>> Before</label><br>
						<label><input type="radio" name="smppb_button_position" value="after" <?php checked($value, 'after'); ?>> After</label><br>
						<label><input type="radio" name="smppb_button_position" value="both" <?php checked($value, 'both'); ?>> Both</label><br>
						<label><input type="radio" name="smppb_button_position" value="floating-left" <?php checked($value, 'floating-left'); ?>> Floating bottom left</label><br>
<label><input type="radio" name="smppb_button_position" value="floating-right" <?php checked($value, 'floating-right'); ?>> Floating bottom right</label><br>
<label><input type="radio" name="smppb_button_position" value="none" <?php checked($value, 'none'); ?>> None</label>
					</td>
					<td><p class="description">Control where the button shows relative to your post content.<br><br><br>
					<span class="dashicons dashicons-info-outline"></span> choose 'None' if you use the <strong><a href="admin.php?page=smppb_code_reference">shortcode option</a></strong> on your Pages!				
					</p></td>
				</tr>
				<tr>
					<th scope="row">Shortcodes to ignore</th>
					<td>
						<input type="text" name="smppb_ignored_shortcodes" value="<?php echo esc_attr(get_option('smppb_ignored_shortcodes', '')); ?>" class="regular-text" /></td>
					<td><p class="description">Give shortcodes names , divided by a comma (example: print_pdf_button, betterdocs_search_form)</td>
				</tr>

				<tr>
					<th scope="row">Group by category</th>
					<td>
						<input type="checkbox" name="smppb_group_by_category" id="smppb_group_by_category" value="1" <?php checked(get_option('smppb_group_by_category', 0), 1); ?> />
						<label for="smppb_group_by_category">Enable grouping</label>
					</td>
					<td><p class="description">Adds visual grouping in the dropdown by template category using optgroup.</p></td>
				</tr>

				<tr>
					<th scope="row"><label for="smppb_button_custom_class">Button CSS class</label></th>
					<td>
						<input type="text" name="smppb_button_custom_class" id="smppb_button_custom_class"
							   value="<?php echo esc_attr( get_option('smppb_button_custom_class', '') ); ?>"
							   placeholder="e.g. my-button-class" style="width: 300px;" />
					</td>
					<td><p class="description">Use this to apply your own CSS styling to the button.</p></td>
				</tr>

				<tr>
					<th scope="row"><label for="smppb_select_custom_class">Dropdown CSS class</label></th>
					<td>
						<input type="text" name="smppb_select_custom_class" id="smppb_select_custom_class"
							   value="<?php echo esc_attr( get_option('smppb_select_custom_class', '') ); ?>"
							   placeholder="e.g. my-select-class" style="width: 300px;" />
					</td>
					<td><p class="description">Apply custom styles to the template selector dropdown.</p></td>
				</tr>

				<tr>
					<th scope="row"><label for="smppb_description_custom_class">Description CSS class</label></th>
					<td>
						<input type="text" name="smppb_description_custom_class" id="smppb_description_custom_class"
							   value="<?php echo esc_attr( get_option('smppb_description_custom_class', '') ); ?>"
							   placeholder="e.g. my-description-class" style="width: 300px;" />
					</td>
					<td><p class="description">Customize how the description text is styled.</p></td>
				</tr>

				<tr>
					<th scope="row"><label for="smppb_width_custom">Custom width</label></th>
					<td>
						<input type="text" name="smppb_width_custom" id="smppb_width_custom" 
							   value="<?php echo esc_attr(get_option('smppb_width_custom', '')); ?>" style="width: 200px;">
					</td>
					<td><p class="description">Set a specific width for the button, e.g. "400px" or "80%".</p></td>
				</tr>
				<tr>
					<th scope="row"><label for="smppb_css_framework">Theme Framework</label></th>
					<td>
					<?php $framework = get_option('smppb_css_framework', '');?>
						<select name="smppb_css_framework" id="smppb_css_framework">
							<option value="" <?php selected($framework, ''); ?>>Default</option>
							<option style="background: red; " value="bootstrap" <?php selected($framework, 'bootstrap'); ?>>Bootstrap</option>
							<option value="tailwind" <?php selected($framework, 'tailwind'); ?>>Tailwind</option>
							<option value="bulma" <?php selected($framework, 'bulma'); ?>>Bulma</option>
							<option value="materialize" <?php selected($framework, 'materialize'); ?>>Materialize</option>
							<option value="foundation" <?php selected($framework, 'foundation'); ?>>Foundation</option>
							<option value="uikit" <?php selected($framework, 'uikit'); ?>>UIkit</option>
						</select>

					
					</td>
					<td><p class="description">Choose a CSS framework to style the form elements. if your theme works with one of these framework.</p></td>
				</tr>
			<?php smppb_placement_settings_rows(); ?>
</tbody>
		</table>




        <p class="submit">
            <input type="submit" name="smppb_settings_save" class="button button-primary" value="Save Settings">
        </p>
    </form>
	</div>
</div>
