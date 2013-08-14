<?php
/*
 * The menu handling. We build up an array of pages which we need to be aware of.
 */

class Menu {
  public $pages = array();
  
  private function processDir() {
    // First load the conf file, if there is one
    //echo "Processing $dir\n";
    $dir = '../pages/menu.conf';
    $conf = @file_get_contents($dir);
    if ($conf === false) {
      error_log("Could not read nor create menu file in $dir");
      return array();
    }
    $dec = json_decode($conf, true);
    if ($dec === null) {
      error_log("Could not decode file $dir");
      return array();
    }
    return $dec;
  }
  
  public function __construct() {
    $this->pages = $this->processDir();
  }
  
  public function output() {
    global $base, $page;
    $pgs = $this->pages;
    echo "<ul>";
    foreach ($pgs as $pgname => $pg) {
      $urlname = preg_replace('/\.php$/', '', $pgname);
      $active = strcmp("$urlname",$page)==0;
      $urlname = preg_replace('/index$/','',$urlname);
      if ($active)
        echo "<li class=\"current\">{$pg["title"]}</li> ";
      else
        echo "<li><a href='$base$urlname'>{$pg["title"]}</a></li> ";
    }
    echo "</ul>";
  }
}
