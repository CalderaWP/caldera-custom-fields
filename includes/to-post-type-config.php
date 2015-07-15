<div class="caldera-config-group">
	<label>
		<?php _e('Post Type', 'caldera-custom-fields'); ?>
	</label>
	<div class="caldera-config-field">
		<select class="block-input field-config required disabled-for-now-ajax-trigger" data-action="cf_post_type_taxonomies" data-target="#type-taxonomies{{_id}}" data-event="change" name="{{_name}}[post_type]">
		<?php
		$post_types = get_post_types(array(), 'objects');
    	foreach($post_types as $type){
    		echo "<option value=\"" . $type->name . "\" {{#is post_type value=\"" . $type->name . "\"}}selected=\"selected\"{{/is}}>" . $type->labels->name . "</option>\r\n";
    	}
    	?>
    	</select>
	</div>
</div>
<div class="caldera-config-group">
	<label>
		<?php _e('Post Status', 'caldera-custom-fields'); ?>
	</label>
	<div class="caldera-config-field">
		<input type="text" class="block-input field-config magic-tag-enabled required" name="{{_name}}[post_status]" value="{{post_status}}">
	</div>
</div>
<div class="caldera-config-group">
	<label>
		<?php _e('Post ID', 'caldera-custom-fields'); ?>
	</label>
	<div class="caldera-config-field">
		<input type="text" class="block-input field-config magic-tag-enabled" name="{{_name}}[ID]" value="{{ID}}">
		<p class="description">
			<?php _e( sprintf( 'Leave blank to create a new post. Pass an existing post ID to edit a post. The magic tag %1s is especially useful for this purpose.', '<code>{embed_post:ID}</code>' ), 'caldera-custom-fields'); ?>
		</p>
	</div>
</div>
<div class="caldera-config-group">
	<label>
		<?php _e('Post Title', 'caldera-custom-fields'); ?>
	</label>
	<div class="caldera-config-field">
		{{{_field slug="post_title" type="text" required="true"}}}
	</div>
</div>
<div class="caldera-config-group">
	<label>
		<?php _e('Post Content', 'caldera-custom-fields'); ?>
	</label>
	<div class="caldera-config-field">
		{{{_field slug="post_content" type="paragraph"}}}
	</div>
</div>

<div class="caldera-config-group">
	<label>
		<?php _e('Post Author', 'caldera-custom-fields'); ?>
	</label>
	<div class="caldera-config-field">
		<input type="text" class="block-input field-config magic-tag-enabled" name="{{_name}}[post_author]" value="{{post_author}}">
	</div>
</div>

<div class="caldera-config-group">
	<label><?php _e('Post Parent', 'caldera-custom-fields'); ?> </label>
	<div class="caldera-config-field">
		<input type="text" class="block-input field-config magic-tag-enabled" name="{{_name}}[post_parent]" value="{{post_parent}}">
	</div>
</div>


<div class="caldera-config-group">
	<label>
		<?php _e('To Ping', 'caldera-custom-fields'); ?>
	</label>
	<div class="caldera-config-field">
		<input type="text" class="block-input field-config magic-tag-enabled" name="{{_name}}[to_ping]" value="{{to_ping}}">
	</div>
</div>


<div class="caldera-config-group">
	<label>
		<?php _e('Post Password', 'caldera-custom-fields'); ?>
	</label>
	<div class="caldera-config-field">
		<input type="text" class="block-input field-config magic-tag-enabled" name="{{_name}}[post_password]" value="{{post_password}}">
	</div>
</div>

<div class="caldera-config-group">
	<label>
		<?php _e('Post Excerpt', 'caldera-custom-fields'); ?>
	</label>
	<div class="caldera-config-field">
		<input type="text" class="block-input field-config magic-tag-enabled" name="{{_name}}[post_excerpt]" value="{{post_excerpt}}">
	</div>
</div>

<div class="caldera-config-group">
	<label>
		<?php _e('Comment Status', 'caldera-custom-fields'); ?>
	</label>
	<div class="caldera-config-field">
		<select class="field-config" name="{{_name}}[comment_status]">
			<option value="closed" {{#is comment_status value="closed"}}selected="selected"{{/is}}>
				<?php _e('Closed', 'caldera-custom-fields'); ?>
			</option>
			<option value="open" {{#is comment_status value="open"}}selected="selected"{{/is}}>
				<?php _e('Open', 'caldera-custom-fields'); ?>
			</option>
		</select>
	</div>
</div>

<div class="caldera-config-group">
	<label><?php _e('Featured Image', 'caldera-custom-fields'); ?> </label>
	<div class="caldera-config-field">
		{{{_field slug="featured_image" type="file"}}}
		<p class="description">
			<?php _e('File will be uploaded to media library and attached as featured image.', 'caldera-custom-fields' ); ?>
		</p>
	</div>
</div>

<h3><?php _e( 'Custom Fields', 'caldera-custom-fields' ); ?></h3>
<p class="description">
	<?php _e('All form fields not set above, will be saved as post-meta/custom fields using the form field slug as the custom field name.', 'caldera-custom-fields' ); ?>
</p>
<p class="description">
	<?php _e('File fields will be uploaded to the Media Library and attached to post.', 'caldera-custom-fields'); ?>
</p>

