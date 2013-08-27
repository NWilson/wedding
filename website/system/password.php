<?php
  header('Vary: Cookie');
  $auth = false;
  $authmsg = '';
  if (array_key_exists('password', $_POST)) {
    if (strtolower($_POST['password']) == strtolower(trim(file_get_contents('../../password-file')))) {
      setcookie('password', sha1('myfairlyrandomsalt:'.$_POST['password']), time()+60*60*24*356);
      $auth = true;
    } else {
      $authmsg = '<p style="color:red">That doesn\'t seem to be the correct password.</p>';
    }
  } else if (array_key_exists('password', $_COOKIE)) {
    if ($_COOKIE['password'] == sha1('myfairlyrandomsalt:'.trim(file_get_contents('../../password-file')))) {
      $auth = true;
    } else {
      $authmsg = '<p style="color:red">Corrupted cookie</p>';
    }
  }
  if (!$auth) {
    header('X-PHP-Response-Code: 403', true, 403);
    echo $authmsg;
?>
    <p>Please enter the password printed on your invitation:</p>
    <form method="POST" action="<?=$lasturlcomp;?>">
      <p><input type="text" name="password"></input></p>
      <p><button style="margin-left: 2em;" type="submit">Done</button></p>
    </form>
<?php
    return;
  }
  return 'OK';
?>
