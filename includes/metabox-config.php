<div class="caldera-config-group">
	<label><?php esc_html_e('Post Types', 'caldera-forms-metabox'); ?> </label>
	<div class="post_types_check" style="padding-top: 1px;">
	<?php

	$post_types = get_post_types(array(), 'objects');
	foreach($post_types as $type){
		echo '<label><input type="checkbox" class="field-config" name="{{_name}}[posttypes]['.$type->name.']" value="1" {{#if posttypes/'.$type->name.'}}checked="checked"{{/if}}> ' . $type->label . '</label>';
	}
	?>
	</div>
	<br>
</div>

<div class="caldera-config-group">
	<label><?php esc_html_e('Context', 'caldera-forms-metabox'); ?> </label>
	<div class="caldera-config-field">
		<select class="block-input field-config" name="{{_name}}[context]">
			<option value="normal" {{#is context value="normal"}}selected="selected"{{/is}}>Normal</option>
			<option value="advanced" {{#is context value="advanced"}}selected="selected"{{/is}}>Advanced</option>
			<option value="side" {{#is context value="side"}}selected="selected"{{/is}}>Side</option>
		</select>
	</div>
</div>

<div class="caldera-config-group">
	<label><?php esc_html_e('Priority', 'caldera-forms-metabox'); ?> </label>
	<div class="caldera-config-field">
		<select class="block-input field-config" name="{{_name}}[priority]">
			<option value="default"{{#is priority value="default"}} selected="selected"{{/is}}>Default</option>
			<option value="core"{{#is priority value="core"}} selected="selected"{{/is}}>Core</option>
			<option value="high"{{#is priority value="high"}} selected="selected"{{/is}}>High</option>
			<option value="low"{{#is priority value="low"}} selected="selected"{{/is}}>Low</option>
		</select>
	</div>
</div>
<p class="description"><?php esc_html_e( 'Note: This processor disables the Notification Mailer, Ajax and Internal Entry capture.', 'caldera-forms-metabox'); ?></p>
<input type="hidden" value="{{_id}}" name="config[is_metabox]">
