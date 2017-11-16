=== Caldera Custom Fields ===
Contributors: Desertsnowman, Shelob9, nahuelmahe
Tags: Custom metabox, caldera forms, form as metabox, custom metaboxes, custom fields, custom field, form custom field, form to post type, calderawp, front-end editor, front end editor
Requires at least: 4.5
Tested up to: 4.8.3
Stable tag: 2.2.1
License: GPLv2

Caldera Forms to post types and custom fields: front-end or back-end

== Description ==
A free addon for [Caldera Forms](https://CalderaForms.com/) to create or edit posts, including custom post types and custom fields. Can be used as a standalone form for front-end editing and as a custom metabox generator in the post editor.
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

= 2.2.1 =
FIXED: Save simple file field file as Pods custom field and use magic tags as pods custom field ( along the Paypal Processor )

= 2.2.0 =
SEE: [https://calderaforms.com/updates/caldera-custom-fields-2-2-0/]*https://calderaforms.com/updates/caldera-custom-fields-2-2-0/)
ADDED: Better support for Pods file fields

= 2.1.4 =
SEE: [https://calderaforms.com/updates/caldera-custom-fields-2-1-4)(https://calderaforms.com/updates/caldera-custom-fields-2-1-4)
* Fixed nonce check for metabox save
* Improved handling for featured images.

= 2.1.2 =
Fix minor bug fixes
= 2.1.1 =
Minor bug fixes

= 2.1.0 =
Make metabox compatible with CF 1.3.5+
Add support for taxonomies

= 2.0.4.1 =
Fixed a bug that caused saving of said fields not work.

= 2.0.4 =
Added visual_editor as accepted type for content area in save to post type
Added better icons to the processor selector

= 2.0.3 =
Corrected a bug that prevented saving new posts with CF 1.2+

= 2.0.2 =
Added permalink magic tag for using the posts permalink in another processor.

= 2.0.1 =
Fix a broken include for post type config in admin.

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
