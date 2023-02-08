<?php
session_save_path('/tmp/sessions');
session_start();

if(!isset($_SESSION['logged'])) {
    header('Location: ./index.php');
    die();
}

require_once '../service/storage.php';

$message = '';

$update = filter_input(INPUT_POST, 'change_type', FILTER_SANITIZE_STRING);

function format_message($status, $message, $details = '') {
    return '
    <div class="col-md-10 col-lg-8 col-xl-7">
        <div class="alert alert-' . $status . ' alert-dismissible fade show mt-4 mb-4" role="alert">
          <h4 class="alert-heading">' . $message . '</h4>
          <p>' . $details . '</p>
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
     </div>';
}

function update_user_credits() {
    $user_name = filter_input(INPUT_POST, 'user_name', FILTER_SANITIZE_SPECIAL_CHARS);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
    $repeat = filter_input(INPUT_POST, 'repeat_password', FILTER_SANITIZE_STRING);
    $confirm = filter_input(INPUT_POST, 'confirm_password', FILTER_SANITIZE_STRING);

    if (empty($user_name)) {
        return format_message('warning', 'Invalid input', 'User name is empty');
    } else if (empty($password)) {
        $password = $confirm;  // do not change
    } else if ($repeat != $password) {
        return format_message('warning', 'Invalid input', 'Passwords are different');
    }

    $db = new Database('../');
    if (!$db->check_user($_SESSION['logged'], $confirm)) {
        return format_message('warning', 'Invalid input', 'Current password not match');
    }

    $db->change_user($_SESSION['logged'], $user_name, $password);
    $_SESSION['logged'] = $user_name;
    return format_message('success', 'User credits changed');
}

if ($update == 'user_credits') {
    $message = update_user_credits();
}

if (filter_input(INPUT_GET, 'ajax', FILTER_SANITIZE_NUMBER_INT)) {
    echo $message;
    die();
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
    <link rel="stylesheet" 
          href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css" 
          integrity="sha384-PsH8R72JQ3SOdhVi3uxftmaW6Vc51MKb0q5P2rRUpPvrszuE4W1povHYgTpBfshb" 
          crossorigin="anonymous">
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
  </head>
  <body>
<?php include '../service/navbar.php'; ?>
    <div class="container" id="content">
      <h1>Administration panel - settings</h1>
<?php echo $message; ?>
      <form href="admin/settings.php?update=user_credits" method="POST">
        <input type="hidden" name="change_type" value="user_credits">
        <h3>User credits</h3>
        <div class="container">
          <div class="col-sm-12 col-md-6 pt-2 pb-2 border">
            <div class="form-group row">
              <label for="user_name_input" class="col-sm-4 col-form-label">Name:</label>
              <div class="col-sm-8">
                <input type="text" class="form-control" id="user_name_input" name="user_name" 
                       maxlength="25" placeholder="User" 
                       value="<?php echo $_SESSION['logged']; ?>"
                       >
              </div>
            </div>

            <div class="form-group row">
              <label for="password_input" class="col-sm-4 col-form-label">New password:</label>
              <div class="col-sm-8">
                <input type="password" class="form-control" id="password_input" name="password" 
                       maxlength="25" placeholder="Password" 
                       >
              </div>
            </div>

            <div class="form-group row">
              <label for="repeat_password_input" class="col-sm-4 col-form-label">Repeat password:</label>
              <div class="col-sm-8">
                <input type="password" class="form-control" id="repeat_password_input" name="repeat_password" 
                       maxlength="25" placeholder="Password" 
                       >
              </div>
            </div>

            <div class="form-group row mt-5 align-items-center">
              <label for="confirm_password_input" class="col-sm-4 col-form-label">Confirm with your current password:</label>
              <div class="col-sm-8">
                <input type="password" class="form-control" id="confirm_password_input" name="confirm_password" 
                       maxlength="25" placeholder="Password" 
                       >
              </div>
            </div>
            <div class="row">
              <div class="col-auto ml-auto">
                <button type="submit" class="btn btn-primary ml-auto">Save</button>
              </div>
            </div>
          </div>
        </div>

      </form>
      <script src="https://code.jquery.com/jquery-3.2.1.min.js" 
              integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" 
      crossorigin="anonymous"></script>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js" 
              integrity="sha384-vFJXuSJphROIrBnz7yo7oB41mKfc8JzQZiCq4NCceLEaO4IHwicKwpJf9c9IpFgh" 
      crossorigin="anonymous"></script>
      <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/js/bootstrap.min.js" 
              integrity="sha384-alpBpkh1PFOepccYVYDB4do5UnbKysX5WZXm3XxPqe5iKTfUKjNkCk9SaVuEZflJ" 
      crossorigin="anonymous"></script>
  </body>
</html>