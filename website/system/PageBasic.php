<?php
/*
 * Special pages roll their own layout; most other pages have
 * the same navigation and a basic content area using this class.
 */
class PageBasic extends PageHTML {
  private $data = '';
  protected $full_title = '';
  public function __construct($file = '') {
    // We parse out the contents of our simple data format.
    $exploded = explode("\n", $file);
    $this->full_title = $exploded[0];
    array_shift($exploded);
    $this->data = join("\n",$exploded);
    $this->title = preg_replace('/<[^>]*?>/', '', $this->full_title);
    
    parent::__construct();
  }
  
  final public function handle() {
    global $base, $pages, $page;
?>

       <div id="header" class="<?=$page?>">
            <div id="header-top"><h1><span class="ir"><small>You are invited to celebrate the marriage of</small><br />Nicholas Wilson and Elspeth Pullinger<span></h1>
            <img src="<?= $this->version('resources/sunflower-fffaf4.jpeg')?>" id="sunflower-back" /></div>
            <div id="nav">
                <?= $pages->output(); ?>
            </div>
        </div>

        <div id="column">
            <div class="section">
            <?php $lasturlcomp = $page; eval("?>".$this->data); ?>
            </div>
        </div>

<?php
  }
}
