<?php
class error extends PageBasic {  
  public function __construct() {
    $data = <<<EOD
Title: An unfortunate sequence of events

Oops. Something went wrong, and the page you were looking for was not found. We just told the webmaster about it, so he should fix it soon.
EOD;
    parent::__construct($data);
    header('HTTP/1.0 404 Not Found');
  }
}
