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
		'<li class="social-share__item share--%s"><a href="%s" aria-label="%s %s" %s>%s</a></li>',
		esc_attr( $key ),
		esc_html( $value['url'] ),
		esc_html_x( 'Share in', 'social share UI', 'avidly-social-share' ),
		esc_html( $value['name'] ),
		( 'javascript:void(0);' !== $value['url'] ) ? 'target="_blank" rel="noopener noreferrer"' : 'target="_self"',
		$value['icon']
	);
}
?>

<ul class="social-share list-none flex">
	<?php echo $string; ?>
</ul>


