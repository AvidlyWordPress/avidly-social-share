# Avidly Social Share

A WordPress pluging to add social share buttons to your WordPress theme or page/post.

## Installation
1. Upload the plugin files to the `/wp-content/plugins/avidly-social-share` directory, or install the plugin through the WordPress plugins screen directly.
1. Activate the plugin through the 'Plugins' screen in WordPress

## Usage
Add social share icons to you theme
```sh
if ( function_exists( 'avidly_social_share' ) ) {
	avidly_social_share();
}
```

Or add social share icons to your page or post by adding Avidly Social Share gutenberg block where ever you want to.

You can choose which icons to show in Settings > Avidly Social Share page.

## Development
**Get packages**
```sh
npm install
```
**Run development** (does not compress the code so it is easier to read)
```sh
npm run start
```

**Run production build** (compresses the code down so it downloads faster)
```sh
npm run build
```
