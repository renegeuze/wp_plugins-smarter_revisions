<?php
/*
Plugin Name: Smarter revisions
Plugin URI: https://github.com/renegeuze/wp_plugins-smarter_revisions
Description: Keep your revision list from flooding while maintaining old revisions
Version: Master
Author: René Geuze
Author URI: https://github.com/renegeuze
Copyright: Ask René Geuze
*/
use ReneGeuze\SmarterRevisions\Bootstrap;

// Prioritize local autoload - usually during development
if (true === file_exists(__DIR__ . '/vendor/autoload.php')) {
	require __DIR__ . '/vendor/autoload.php';
} elseif (class_exists('\ReneGeuze\SmarterRevisions\Bootstrap')) {
	// Assume nicely loaded autoloader
} else {
	require __DIR__ . '/src/Bootstrap.php';
}

do_action('smarter_revisions/bootstrap', new Bootstrap());
