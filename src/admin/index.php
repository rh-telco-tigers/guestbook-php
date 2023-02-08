<?php
require_once './../service/storage.php';

# session_save_path('/tmp/sessions');
session_start();

define('LOG_IP', 0);
define('LOG_DATE', 1);
define('LOG_SUCCESS', 2);

$reply = '';
$reload = false;

$db = new Database();

if (!isset($_SESSION['logged']) && filter_has_var(INPUT_POST, 'login')) {
    $user_name = filter_input(INPUT_POST, 'login', FILTER_SANITIZE_SPECIAL_CHARS);
    $password = filter_input(INPUT_POST, 'password', FILTER_UNSAFE_RAW);

    $login_try_time = $db->get_next_login_try_time();

    if ($login_try_time > time()) {
        $reply = format_reply(
            'warning', 'Panel temporary blocked', 'Next login try allowed at ' . date('H:i:s', $login_try_time)
        );
    } elseif ($db->check_user($user_name, $password)) {
        $_SESSION['logged'] = $user_name;
        $_SESSION['login_time'] = $db->get_last_login_time($user_name);
        setcookie('is_logged', 1, 0, '/');  # for UI on public sites
        $db->register_login_success($user_name);
        $reply = format_reply('success', 'You was successfully login!');
    } else {
        $db->register_login_fail($user_name);
        $reply = format_reply('warning', 'Invalid login data.');
    }
}

if (isset($_SESSION['logged']) && filter_input(INPUT_GET, 'action', FILTER_UNSAFE_RAW) == 'logout') {
    unset($_SESSION['logeed']);
    setcookie('is_logged', null, 0, '/');
    session_destroy();

    $reload = true;
    $reply = format_reply('success', "You was successfully log out.");
}
?>
<!doctype html>
<html>
  <head>
    <meta name="description" content="">
    <meta name="keywords" content="">
    <meta charset="UTF-8">
    <base href="./../" target="_self">
    <title>Guestbook - administration panel</title>
    <?php if ($reload) { ?>
        <meta http-equiv="refresh" content="1; url=admin/index.php">
    <?php } ?>

    <link rel="stylesheet" 
          href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css" 
          integrity="sha384-PsH8R72JQ3SOdhVi3uxftmaW6Vc51MKb0q5P2rRUpPvrszuE4W1povHYgTpBfshb" 
          crossorigin="anonymous">
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
  </head>
  <body>
      <?php include '../service/navbar.php'; ?>
    <div class="container" id="content">
      <h1>Welcome in administration panel</h1>
      <?php
      echo $reply;

      if (!isset($_SESSION['logged'])) {
          ?>
          <div id="login_form">
            <form action="admin/index.php" method="post">
              <div class="form-group row">
                <label for="login_input" class="col-sm-2 col-lg-1 col-form-label">Name</label>
                <div class="col-sm-8 col-md-6 col-lg-4">
                  <input type="text" class="form-control" id="login_input" name="login" maxlength="25" placeholder="User">
                </div>
              </div>
              <div class="form-group row">
                <label for="password_input" class="col-sm-2 col-lg-1 col-form-label">Password</label>
                <div class="col-sm-8 col-md-6 col-lg-4">
                  <input type="password" class="form-control" id="password_input" 
                         placeholder="Password" name="password" maxlength="25">
                </div>
              </div>
              <button type="submit" class="btn btn-primary">Sign in</button>
            </form>
          </div>
      <?php } elseif (!$reload) { ?>
          <div id="rewiew">
            <h3>Prevous logs</h3>
            <p>Time of last failed login: <strong>
                <?php 
                    $failed_time = $_SESSION['login_time']['LastFailedLoginTime'];
                    echo $failed_time? date('d.m.y H:i:s', $failed_time) : 'Never';
                ?></strong></p>
            <p>Time of last success login: <strong><?php echo date('d.m.y H:i:s', $_SESSION['login_time']['LastValidLoginTime']) ?></strong></p>
          </div>
      <?php } ?>
    </div>
    <script src="https://code.jquery.com/jquery-3.2.1.min.js" 
            integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" 
    crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js" 
            integrity="sha384-vFJXuSJphROIrBnz7yo7oB41mKfc8JzQZiCq4NCceLEaO4IHwicKwpJf9c9IpFgh" 
    crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/js/bootstrap.min.js" 
            integrity="sha384-alpBpkh1PFOepccYVYDB4do5UnbKysX5WZXm3XxPqe5iKTfUKjNkCk9SaVuEZflJ" 
    crossorigin="anonymous"></script>
    <script src="../js/admin_records.js"></script>
    <script src="../js/sites.js"></script>
    <script src="../js/textkit.js"></script>
    <script>
        $(initAdminKit);
        $(function () {
            divideIntoSites("#records");
        });
    </script>
  </body>
</html>
