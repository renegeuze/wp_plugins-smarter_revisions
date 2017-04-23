<?php
/*
Plugin Name: Smarter revisions
Plugin URI:
Description:
Version: Master
Author: René Geuze
Author URI: http://github.com/renegeuze
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

new Bootstrap();
