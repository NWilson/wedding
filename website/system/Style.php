<?php
/*
 * A class to pre-process some CSS to handle resource expiry
 *
 * Please don't put quotation marks in your filenames. You're asking for trouble.
 */

class style {
  
  public static function version($match) {
    global $base;
    // FIXME deal properly with strings and quotation marks in filenames
    return 'url('.$match[1].'?ver='.@filemtime('../design/'.$match[1]).')';
  }
  
  public function handle() {    
    global $page_orig;
    
    header('Content-Type: text/css; charset=UTF-8');
    $expire_seconds = 60*60*24*100;
    header("Cache-Contol: max-age=$expire_seconds;must-revalidate");
    header('Expires: '.date('D, d M Y H:i:s', time()+$expire_seconds).' GMT');
    
    $data = file_get_contents('../'.$page_orig);
    
    $data = $data.PHP_EOL.'/* Filtered by PHP */';
    
    $data = preg_replace_callback('/url\([\'"]?(.*?)[\'"]?\)/', 'style::version', $data);
    
    echo $data;//Conneg::gzoutput($data);
  }
}
