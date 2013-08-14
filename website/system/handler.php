<?php
/*
 * The niceify URLs file
 */

require 'config.php';

// Some set-up for sites served on multiple domains (like www.example.org/mysite/,
// and www.real-address.org as well as localhost/sites/mysite/ for testing).
// The method is simple: $crop = 'mysite' above, and everything just works.
$crop      = pathinfo(realpath('..'), PATHINFO_BASENAME);
$page_orig = preg_replace( "#^(.*?$crop/)?#", '', @$_GET['q']);
$base      = preg_replace("#^((.*?$crop)?).*?$#", '$1/', @$_GET['q']);
// NB: $base always has start and end slash (either '/' or '/.../')

function __autoload($class_name) {require_once "$class_name.php";}

$pages = new Menu();

// We apply a small number of URL re-mappings here
$page_map = array(
  '/^$/'        => 'index',
 // '/\/$/'       => '/index',
  '/^.*\.css$/' => '../system/Style'
  );
$page = preg_replace(array_keys($page_map), array_values($page_map), $page_orig);

//echo '<pre>';
//echo "page: '$page'\n";
// First try for a PHP file for the page:
if (($file = @file_get_contents('../pages/'.$page.'.php')) !== false && preg_match('/^\s*<\?php/', $file)) {
  //echo "Got a PHP: '".'../pages/'.$page.'.php\''.PHP_EOL;
  require '../pages/'.$page.'.php';
  preg_match('/[^\/]*$/', $page, $handler);
  $handler = @$handler[0];
  //echo "loading class '$handler'\n";
  $handler = new $handler;
} elseif (($file = @file_get_contents('../pages/'.$page)) !== false) {
  //echo "Got a Basic\n";
  $handler = new PageBasic($file);
} else {
  //echo "Not found\n";
  error_log('Page not found: '.var_export(@$_GET['q'], true).', with referrer '.@$_SERVER['HTTP_REFERER']);
  $handler = new error();
}

$handler->handle();
