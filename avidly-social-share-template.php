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
		'<li class="social-share__item share--%s w2"><a href="%s" aria-label="%s %s" %s>%s</a></li>',
		esc_attr( $key ),
		esc_html( $value['url'] ),
		esc_html_x( 'Share in', 'social share UI', 'avidly-social-share' ),
		esc_html( $value['name'] ),
		( 'javascript:void(0);' !== $value['url'] ) ? 'target="_blank" rel="noopener noreferrer"' : 'target="_self"',
		$value['icon']
	);
}
?>

<div class="social-media social__inner-container">
	<div class="social-media-container alignwide flex items-start items-center@md">

		<ul class="social-share margin-top-xs margin-top-xxxxs@md flex@md flex-wrap gap-md reset-list-style">
			<?php echo $string; ?>
		</ul>

	</div>
</div><!-- /.social-media -->