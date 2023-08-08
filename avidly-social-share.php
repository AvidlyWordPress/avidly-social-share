<?php
/**
 * Plugin Name:       Avidly Social Share
 * Description:       Add social share buttons to your page. <a href='/wp-admin/options-general.php?page=avidly_social_share_plugin'>Settings.</a>
 * Requires at least: 5.8
 * Requires PHP:      7.0
 * Version:           1.2.1
 * Author:            Avidly
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       avidly-social-share
 *
 * @package           Avidly_Social_Share
 */

defined( 'ABSPATH' ) || die( 'No script kiddies please!' );

/**
 * Registers the block using the metadata loaded from the `block.json` file.
 * Behind the scenes, it registers also all assets so they can be enqueued
 * through the block editor in the corresponding context.
 *
 * @see https://developer.wordpress.org/block-editor/how-to-guides/block-tutorial/writing-your-first-block-type/
 */
add_action(
	'init',
	function() {
		register_block_type_from_metadata(
			__DIR__,
			array(
				'render_callback' => 'avidly_social_share_callback',
				'description' => _x( 'Add social share buttons to your page.', 'block description', 'avidly-social-share' ),
				'attributes'      => array(
					'className' => array(
					  'default' => '',
					  'type'    => 'string',
					),
					'align' => array(
						'default' => '',
						'type'    => 'string',
					),
					'style' => array(
						'default' => '',
						'type'    => 'object',
					),
				),
			)
		);
	}
);

function avidly_social_share_callback( $attributes ) {
	// Get attributes from block settings.
	$class = 'avidly-social-share-block';
	$class .= ( isset( $attributes['backgroundColor'] ) ) ? ' has-' . $attributes['backgroundColor'] . '-background-color' : '';
	$class .= ( isset( $attributes['textColor'] ) ) ? ' has-' . $attributes['textColor'] . '-color' : '';
	$class .= ( isset( $attributes['align'] ) ) ? ' align' . $attributes['align'] : '';
	$class .= ( isset( $attributes['className'] ) ) ? ' ' . $attributes['className'] : '';

	$style = avidly_social_share_block_inlinestyles( $attributes, 'string' );

	return sprintf(
		'<div%s%s>%s</div>',
		( $class ) ? ' class="' . esc_attr( $class ) . '"' : '',
		( $style ) ? ' style="' . esc_attr( $style ) . '"' : '',
		avidly_get_social_share( 'avidly-social-share-template.php' )
	);
}

/**
 * Create block inline styles for PHP render.
 *
 * @param array  $attributes The block attributes.
 * @param string $format return results in array/string (default: array).
 *
 * @return array $inline_style.
 */
function avidly_social_share_block_inlinestyles( $attributes, $format = 'array' ) {
	// Init array.
	$inline_style = array();
	
	// Return if we do not have styles.
	if ( ! isset( $attributes['style'] ) || ! is_array( $attributes['style'] ) ) {
		return false;
	}

	// Handle spacing styles (mainly margins and paddings, possibilty that there could be some others in future).
	$spacings = isset( $attributes['style']['spacing'] ) ? $attributes['style']['spacing'] : array(); // use empty array as fallback.

	foreach( $spacings as $style => $values ) {
		// Skip values that are not array (example blockGap).
		if( ! is_array( $values ) ) {
			continue;
		}

		foreach( $values as $pos => $val ) {
			$css_var_val = '';

			// Use strpos to detect if value is CSS variable or manually set.
			if ( false !== strpos( $val, 'var' ) ) {
				$css_var = explode( ':', $val ); // Explode the var from value.
				$css_var_val = ( isset( $css_var[1] ) ) ? str_replace( '|', '--', $css_var[1]) : 'unset'; // Format the CSS variable value.
			}

			// Build single CSS rule.
			$inline_style[] = sprintf(
				'%s-%s:%s',
				esc_attr( $style ),
				esc_attr( $pos ),
				( $css_var_val ) ? 'var(--wp--' . esc_attr( $css_var_val ) . ')' : esc_attr( $val )
			);
		}
	}

	// Handle format how results will be return.
	if ( 'string' === $format ) {
		$inline_style = ( is_array( $inline_style ) ) ? implode( ';', $inline_style  ) : ''; // Convert to string.
		return $inline_style;
	} else {
		return $inline_style;
	}
}


/**
 * Plugin translations.
 */
add_action(
	'init',
	function() {
		load_plugin_textdomain( 'avidly-social-share', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	}
);

/**
 * Create output and support for 
 */
function avidly_set_social_medias() {

	// Detect correct share content for page for posts, term archives and single content (example posts and pages).
	if ( is_home() ) {
		$post_id   = get_option( 'page_for_posts' );
		$title_raw = rawurlencode( esc_attr( get_option( 'blogname' ) . ' | ' . get_the_title( $post_id ) ) );
		$link      = get_the_permalink( $post_id );
	} elseif ( is_archive() ) {
		global $wp_query;
		$cat_obj   = $wp_query->get_queried_object();
		$title_raw = rawurlencode( get_option( 'blogname' ) . ' | ' . esc_attr( $cat_obj->name ) );
		$link      = get_term_link( $cat_obj->term_id );
	} else {
		$post_id   = get_the_ID();
		$title_raw = rawurlencode( esc_attr( get_the_title( $post_id ) ) );
		$link      = get_the_permalink( $post_id );
	}

	$medias = array(
		'facebook' => array(
			'name' => 'Facebook',
			'url'  => 'https://www.facebook.com/sharer/sharer.php?u=' . esc_url( $link ) . '&amp;t=' . esc_attr( $title_raw ),
			'icon' => '<svg class="svg-icon icon-ui" aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" width="32" height="32" fill="currentColor"><g><path d="m22.69 18.24.84-5.46h-5.24V9.24a2.726 2.726 0 0 1 3.07-2.95h2.38V1.65c-1.4-.23-2.81-.35-4.23-.37-4.31 0-7.13 2.61-7.13 7.34v4.16H7.6v5.46h4.79v13.19h5.9V18.24h4.4z"/></g></svg>',
		),
		'twitter' => array(
			'name' => 'Twitter',
			'url'  => 'https://twitter.com/intent/tweet?text=' . esc_attr( $title_raw ) . '&amp;url=' . esc_url( $link ),
			'icon' => '<svg class="svg-icon icon-ui" aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" width="32" height="32" fill="currentColor"><g><path d="M28.03 10.82c.02.27.02.54.02.8 0 8.17-6.22 17.58-17.58 17.58-3.36.01-6.66-.95-9.49-2.77.5.05.99.08 1.49.08 2.78.01 5.48-.92 7.67-2.64a6.208 6.208 0 0 1-5.78-4.28c.39.06.78.1 1.17.1.55 0 1.09-.07 1.63-.21a6.17 6.17 0 0 1-4.95-6.06v-.08c.85.46 1.81.73 2.79.77a6.186 6.186 0 0 1-1.92-8.27 17.61 17.61 0 0 0 12.74 6.47c-.1-.47-.15-.94-.15-1.41a6.178 6.178 0 0 1 10.69-4.22c1.38-.27 2.71-.77 3.92-1.49a6.158 6.158 0 0 1-2.72 3.4 12.2 12.2 0 0 0 3.56-.96 13.56 13.56 0 0 1-3.09 3.19z"/></g></svg>',
		),
		'whatsapp' => array(
			'name' => 'WhatsApp',
			'url'  => 'https://api.whatsapp.com/send?phone=&amp;text=' . esc_attr( $title_raw ) . ' ' . esc_url( $link ),
			'icon' => '<svg class="svg-icon icon-ui" aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" width="32" height="32" fill="currentColor"><path d="M25.69 6.55a13.532 13.532 0 0 0-9.63-3.99c-7.51 0-13.61 6.11-13.62 13.62 0 2.4.63 4.74 1.82 6.81l-1.93 7.06 7.22-1.89c1.99 1.08 4.23 1.66 6.51 1.66h.01c7.5 0 13.61-6.11 13.62-13.62-.02-3.66-1.43-7.08-4-9.65zM16.06 27.5c-2.04 0-4.03-.55-5.77-1.58l-.41-.25-4.29 1.13 1.14-4.18-.27-.43c-1.13-1.8-1.73-3.88-1.73-6.02 0-6.24 5.08-11.32 11.32-11.32 3.02 0 5.86 1.18 8 3.32 2.14 2.14 3.31 4.98 3.31 8.01.01 6.24-5.07 11.32-11.3 11.32zm6.2-8.48c-.34-.17-2.01-.99-2.32-1.11-.31-.11-.54-.17-.77.17-.23.34-.88 1.11-1.08 1.33-.2.23-.4.26-.74.09-.34-.17-1.44-.53-2.74-1.69-1.01-.9-1.69-2.02-1.89-2.36-.2-.34-.02-.52.15-.69.15-.15.34-.4.51-.6.17-.2.23-.34.34-.57.11-.23.06-.43-.03-.6-.09-.17-.77-1.85-1.05-2.53-.28-.66-.56-.57-.77-.58-.2-.01-.43-.01-.65-.01-.23 0-.6.09-.91.43-.31.34-1.19 1.16-1.19 2.84 0 1.67 1.22 3.29 1.39 3.52.17.23 2.4 3.66 5.81 5.14.81.35 1.45.56 1.94.72.81.26 1.56.22 2.14.13.65-.1 2.01-.82 2.3-1.62.28-.79.28-1.48.2-1.62-.07-.13-.3-.22-.64-.39z" style="fill-rule:evenodd;clip-rule:evenodd;" /></svg>',
		),
		'linkedin' => array(
			'name' => 'LinkedIn',
			'url'  => 'https://www.linkedin.com/shareArticle?mini=true&amp;url=' . esc_url( $link ),
			'icon' => '<svg class="svg-icon icon-ui" aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" width="32" height="32" fill="currentColor"><g><path d="M9.11 28.94H3.64V11.33H9.1v17.61zM6.37 8.93c-1.76.01-3.19-1.41-3.2-3.17s1.41-3.19 3.17-3.2a3.182 3.182 0 0 1 3.2 3.16c0 1.77-1.41 3.2-3.17 3.21zm23.21 20.01h-5.46v-8.57c0-2.04-.04-4.66-2.84-4.66-2.84 0-3.28 2.22-3.28 4.52v8.72h-5.46V11.33h5.24v2.4h.08a5.74 5.74 0 0 1 5.17-2.84c5.53 0 6.55 3.65 6.55 8.38v9.67z"/></g></svg>',
		),
		'email' => array(
			'name' => 'Email',
			'url'  => 'mailto:?subject=' . esc_html_x( 'I want to share this site with you', 'social share UI', 'avidly-social-share' ) . ': ' . esc_attr( $title_raw ) . '&amp;body=' . esc_attr( $title_raw ) . ' ' . esc_url( $link ),
			'icon' => '<svg class="svg-icon icon-ui" aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" width="32" height="32" fill="currentColor"><path d="M28.5 5.26h-25c-.69 0-1.25.56-1.25 1.25v17.55c0 .69.56 1.25 1.25 1.25h25c.69 0 1.25-.56 1.25-1.25V6.51c0-.69-.56-1.25-1.25-1.25zm-3.66 2.5L16 14.6 7.16 7.76h17.68zM4.75 22.82V9.06l10.48 8.11c.23.17.5.26.76.26s.54-.09.76-.26l10.49-8.11v13.76H4.75z" /></svg>',
		),
	);

	return $medias;
}

/**
 * Define default social share channels.
 *
 * @param array $medias set the media specific details.
 * @param int $post_id
 */
add_filter(
	'avidly_social_share',
	function ( $output ) {
		$medias    = avidly_set_social_medias();
		$output    = array();
		$myoptions = get_option( 'avidly_social_share_plugin_options' );

		// Output icons if media is set to visible.
		foreach( $medias as $id => $array ) {
			if ( isset( $myoptions[$id] ) && $myoptions[$id] === '1' ) {
				array_push( $output, $medias[$id] );
			}
		}

		// Return array of medias.
		return $output;
	},
	10,
	2
);


/*
* Output (based on https://10up.github.io/wp-component-library/component/social-links/index.html).
* Use OB to render the HTL output as string.
*/
function avidly_get_social_share( $file_name = 'avidly-social-share-template.php' ) {

	// Check if there is atleast default value added to filename.
	if ( ! $file_name ) {
		return;
	}

	ob_start();
	include plugin_dir_path( __FILE__ ) . $file_name;
	$content = ob_get_contents();
	ob_end_clean();

	return $content;
}


/*
* Add settings page.
*/

function avidly_social_share_add_settings_page() {
	add_options_page(
		esc_html__( 'Select social services to share', 'avidly-social-share'),
		esc_html__( 'Avidly Social Share', 'avidly-social-share'),
		'manage_options',
		'avidly_social_share_plugin',
		'avidly_social_share_render_plugin_settings_page'
	);
}
add_action( 'admin_menu', 'avidly_social_share_add_settings_page' );

/**
 * Generate form for options page.
 *
 * @return void 
 */
function avidly_social_share_render_plugin_settings_page() {
	?>
	<h2><?php esc_html_e( 'Select social services to share', 'avidly-social-share' ); ?></h2>
	<form action="options.php" method="post">
		<?php
		settings_fields( 'avidly_social_share_plugin_options' );
		do_settings_sections( 'avidly_social_share_plugin' );
		?>
		<input name="submit" class="button button-primary" type="submit" value="<?php esc_attr_e( 'Save' ); ?>" />
	</form>
	<?php
}

/**
 * Add add_settings_field for options page.
 *
 * @return void 
 */
function avidly_social_share_register_settings() {
	register_setting(
		'avidly_social_share_plugin_options',
		'avidly_social_share_plugin_options',
		'avidly_social_share_plugin_options_validate'
	);

	add_settings_section(
		'some_settings',
		'',
		'',
		'avidly_social_share_plugin'
	);

	// Create array to generate input fields for each social media.
	$medias = avidly_set_social_medias();

	// Loop thru array ad set $id and Name for social mediat that can be checked.
	foreach ( $medias as $id => $val ) {
		add_settings_field(
			$id,
			$val['name'],
			'avidly_social_share_checkbox_callback',
			'avidly_social_share_plugin',
			'some_settings',
			$id
		);
	}
}
add_action( 'admin_init', 'avidly_social_share_register_settings' );

/**
 * Create function to render checkboxes for social medias.
 * 
 * @param string $social_id for name of the cosial media (lower caps)
 * 
 * @return void
 */
function avidly_social_share_checkbox_callback( $social_id = '' ) {

	// Return if there is no social media ID set.
	if ( ! $social_id ) {
		return;
	}

	// Get selected options.
	$options = get_option( 'avidly_social_share_plugin_options' );

	// Check if social media ID if found from options and set the value for it.
	if ( isset( $options[$social_id] ) ) {
		$html = sprintf(
			'<input type="checkbox" id="checkbox_%1$s" name="avidly_social_share_plugin_options[%1$s]" value="1" %2$s/>',
			$social_id,
			checked( 1, $options[$social_id], false )
		);
	} else {
		$html = sprintf(
			'<input type="checkbox" id="checkbox_%1$s" name="avidly_social_share_plugin_options[%1$s]" value="1" />',
			$social_id
		);
	}


	// Output input field.
	echo $html;
}
