<?php

/**
 * The template for displaying social share links
 *
 * @package Avidly_Social_Share
 * @since 1.0.0
 */

$medias = apply_filters( 'avidly_social_share', array() );
$string = '';

foreach ( $medias as $key => $value ) {
	$string .= sprintf(
		'<li class="social-share__item share--%s"><a href="%s" aria-label="%s"%s%s>%s</a></li>',
		esc_attr( $key ), // share-- class.
		esc_html( $value['url'] ), // link URL.
		esc_html_x( 'Share in', 'social share UI', 'avidly-social-share' ) . ' ' . esc_html( $value['name'] ), // Aria-Label.
		( 'javascript:void(0);' !== $value['url'] ) ? ' target="_blank" rel="noopener noreferrer"' : ' target="_self"', // Link target.
		' data-click-type="share" data-click-event="' . esc_html( $value['name'] ) . '"', // Click tracking.
		$value['icon'] // Link content.
	);
}
?>

<ul class="social-share list-none flex">
	<?php echo $string; // phpcs:ignore ?>
</ul>
