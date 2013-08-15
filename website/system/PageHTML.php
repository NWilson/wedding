<?php
/*
 * The framing class: transparently wraps each page with the top and tails
 */

abstract class PageHTML {
  protected $keywords = '';
  protected $description = '';
  protected $title = '';
  protected $full_title = '';
  
  abstract function handle();
  
  protected function version($file) {
    global $base;
    return $base.$file.':ver='.@filemtime('../'.$file);
  }
  
  public function __construct() {
    header('Content-Type: text/html; charset=UTF-8');
    $expire_seconds = 1;
    header("Cache-Contol: max-age=$expire_seconds;must-revalidate");
    header('Expires: '.date('D, d M Y H:i:s', time()+$expire_seconds).' GMT');
    ob_start();
    $this->frontCover();
  }
  public function __destruct() {
    $this->backCover();
    $page = ob_get_contents();
    ob_end_clean();
    echo $page;
    //Conneg::gzoutput($page);
  }
  
  private function frontCover() {
    global $base;

?><!DOCTYPE html><html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title><?php echo $this->title.($this->title != '' ? ' « ' : '').'Nicholas and Elspeth’s wedding'; ?></title>
        <meta name="keywords" content="<?= $this->keywords ?>">
        <meta name="description" content="<?= $this->description ?>">
        <link rel="shortcut icon" href="<?= $this->version('favicon.png');?>">
        <link rel="stylesheet" href="<?= $this->version('design/normalize.css'); ?>"        type="text/css" media="screen">
        <link rel="stylesheet" href="<?= $this->version('design/main.css'); ?>"        type="text/css" media="screen">
        <meta name="viewport" content="width=device-width">

        <!-- Place favicon.ico and apple-touch-icon.png in the root directory -->
        
<!--         <script src="js/vendor/modernizr-2.6.2.min.js"></script> -->
        <script type="text/javascript" src="<?= $this->version('resources/jquery-1.10.2.min.js'); ?>"></script>
    </head>
    <body>
        <!--[if lt IE 7]>
            <p class="chromeframe">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">activate Google Chrome Frame</a> to improve your experience.</p>
        <![endif]-->

<?php
  }

  private function backCover() {
?>

<?php /* 
<div id="footer">
  © Nicholas Wilson 2011<br>
  <a href="http://www.nicholaswilson.me.uk">Boilerplate by Nicholas Wilson</a>
</div>

*/ ?>

</body>
</html>

<?php
  }
}
