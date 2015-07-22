=== Caldera Custom Fields ===
Contributors: Desertsnowman, Shelob9
Tags: Custom metabox, caldera forms, form as metabox, custom metaboxes, custom fields, custom field, form custom field, form to post type, calderawp
Requires at least: 3.9
Tested up to: 4.3
Stable tag: 2.0.0
License: GPLv2

Caldera Forms to post types and custom fields: front-end or back-end

== Description ==
A free addon for [Caldera Forms](http://wordpress.org/plugins/caldera-forms/) to create or edit posts, including custom post types and custom fields. Can be used as a standalone form for front-end editing and as a custom metabox generator in the post editor.
== Installation ==

Other than using the automatic plugin installer from plugins page, You can extract the contents and upload the cf-metaboxes folder to /wp-content/plugins/ directory.

Activate the plugin through the 'Plugins' menu in WordPress

Edit or create a new form. Navigate to Processors tab and click "Add Processor". Click the "Use Processor" button next to the "Form as Metabox" item.

Configure the processor by setting the Post Types you want the metabox to be in, the Context and Priority. Then click Update Form.

You will now see your metabox when creating a new post of the type you specified. All entries are save as custom fields for that post using the field slug as the key.

== Frequently Asked Questions ==
none yet.

== Screenshots ==
1. Metabox processor config options. Simple but effective.
2. Using conditional logic to make great progressive metaboxes.

== Changelog ==

= 2.0.0 =
Added a second processor for using Caldera Forms to create and edit posts and custom fields

= 1.0.5 =
* Support to handle multiple form metaboxes

= 1.0.4 =
* Compatibility for CF 1.9+
* Dont allow ajax based forms for metabox

1.0.3: Compatibility for CF 1.1.0
1.0.2: Multi page compatibility
1.0.1: compatibility with CF 1.0.4
1.0.0: initial release

== Upgrade Notice ==
still new, so nothing to upgrade.
