<div class="caldera-config-group">
	<label>
		<?php esc_html_e( 'Post Type', 'caldera-forms-metabox' ); ?>
	</label>
	<div class="caldera-config-field">
		<select class="block-input field-config required disabled-for-now-ajax-trigger"
		        data-action="cf_post_type_taxonomies" data-target="#type-taxonomies{{_id}}" data-event="change"
		        name="{{_name}}[post_type]">
			<?php
			$post_types = get_post_types( array(), 'objects' );
			foreach ( $post_types as $type ) {
				echo "<option value=\"" . $type->name . "\" {{#is post_type value=\"" . $type->name . "\"}} selected=\"selected\" {{/is}}>" . $type->labels->name . "</option>\r\n";
			}
			?>
		</select>
	</div>
</div>
<div class="caldera-config-group">
	<label>
		<?php esc_html_e( 'Post Status', 'caldera-forms-metabox' ); ?>
	</label>
	<div class="caldera-config-field">
		<input type="text" class="block-input field-config magic-tag-enabled required" name="{{_name}}[post_status]"
		       value="{{post_status}}">
	</div>
</div>
<div class="caldera-config-group">
	<label>
		<?php esc_html_e( 'Post ID', 'caldera-forms-metabox' ); ?>
	</label>
	<div class="caldera-config-field">
		<input type="text" class="block-input field-config magic-tag-enabled" name="{{_name}}[ID]" value="{{ID}}">
		<p class="description">
			<?php esc_html_e( sprintf( 'Leave blank to create a new post. Pass an existing post ID to edit a post. The magic tag %1s is especially useful for this purpose.', '<code>{embed_post:ID}</code>' ), 'caldera-forms-metabox' ); ?>
		</p>
	</div>
</div>
<div class="caldera-config-group">
	<label>
		<?php esc_html_e( 'Post Title', 'caldera-forms-metabox' ); ?>
	</label>
	<div class="caldera-config-field">
		{{{_field slug="post_title" type="text" required="true"}}}
	</div>
</div>
<div class="caldera-config-group">
	<label>
		<?php esc_html_e( 'Post Content', 'caldera-forms-metabox' ); ?>
	</label>
	<div class="caldera-config-field">
		{{{_field slug="post_content" type="paragraph,visual_editor,text,wysiwyg"}}}
	</div>
</div>

<div class="caldera-config-group">
	<label>
		<?php esc_html_e( 'Post Author', 'caldera-forms-metabox' ); ?>
	</label>
	<div class="caldera-config-field">
		<input type="text" class="block-input field-config magic-tag-enabled" name="{{_name}}[post_author]"
		       value="{{post_author}}">
	</div>
</div>

<div class="caldera-config-group">
	<label><?php esc_html_e( 'Post Parent', 'caldera-forms-metabox' ); ?> </label>
	<div class="caldera-config-field">
		<input type="text" class="block-input field-config magic-tag-enabled" name="{{_name}}[post_parent]"
		       value="{{post_parent}}">
	</div>
</div>


<div class="caldera-config-group">
	<label>
		<?php esc_html_e( 'To Ping', 'caldera-forms-metabox' ); ?>
	</label>
	<div class="caldera-config-field">
		<input type="text" class="block-input field-config magic-tag-enabled" name="{{_name}}[to_ping]"
		       value="{{to_ping}}">
	</div>
</div>


<div class="caldera-config-group">
	<label>
		<?php esc_html_e( 'Post Password', 'caldera-forms-metabox' ); ?>
	</label>
	<div class="caldera-config-field">
		<input type="text" class="block-input field-config magic-tag-enabled" name="{{_name}}[post_password]"
		       value="{{post_password}}">
	</div>
</div>

<div class="caldera-config-group">
	<label>
		<?php esc_html_e( 'Post Excerpt', 'caldera-forms-metabox' ); ?>
	</label>
	<div class="caldera-config-field">
		<input type="text" class="block-input field-config magic-tag-enabled" name="{{_name}}[post_excerpt]"
		       value="{{post_excerpt}}">
	</div>
</div>

<div class="caldera-config-group">
	<label>
		<?php esc_html_e( 'Comment Status', 'caldera-forms-metabox' ); ?>
	</label>
	<div class="caldera-config-field">
		<select class="field-config" name="{{_name}}[comment_status]">
			<option value="closed" {{#is comment_status value="closed" }}selected="selected" {{/is}}>
				<?php esc_html_e( 'Closed', 'caldera-forms-metabox' ); ?>
			</option>
			<option value="open" {{#is comment_status value="open" }}selected="selected" {{/is}}>
				<?php esc_html_e( 'Open', 'caldera-forms-metabox' ); ?>
			</option>
		</select>
	</div>
</div>

<div class="caldera-config-group">
	<label><?php _e( 'Featured Image', 'caldera-forms-metabox' ); ?> </label>
	<div class="caldera-config-field">
		{{{_field slug="featured_image" type="file,advanced_file"}}}
		<p class="description">
			<?php esc_html_e( 'File will be uploaded to media library and attached as featured image.', 'caldera-forms-metabox' ); ?>
		</p>
	</div>
</div>

<div id="cf-custom-fields-taxonomies-settings">
	<h3>
		<?php esc_html_e( 'Taxonomies', 'caldera-forms-metabox' ); ?>
	</h3>
	<?php echo cf_custom_fields_taxonomy_ui(); ?>
</div>

<h3><?php esc_html_e( 'Custom Fields', 'caldera-forms-metabox' ); ?></h3>
<p class="description">
	<?php esc_html_e( 'All form fields not set above, will be saved as post-meta/custom fields using the form field slug as the custom field name.', 'caldera-forms-metabox' ); ?>
</p>
<p class="description">
	<?php esc_html_e( 'File fields will be uploaded to the Media Library and attached to post.', 'caldera-forms-metabox' ); ?>
</p>

