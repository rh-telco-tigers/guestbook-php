<?php
#session_save_path('/tmp/sessions');
session_start();

if(!isset($_SESSION['logged'])) {
    header('Location: ./index.php');
    die();
}

require_once '../service/storage.php';


$message = '';
$db = new Database();


if(filter_has_var(INPUT_POST, 'target')){
    $target = filter_input(INPUT_POST, 'target', FILTER_VALIDATE_INT);

    $ids = array();

    foreach(filter_input_array(INPUT_POST) as $key => $value) {
        if(strstr($key, 'record_') and $value === 'on'){
            array_push($ids, substr($key, 7));
        }
    }

    if(!empty($target)) {
        $db->move_entries($ids, $target);
    } else {
        $db->remove_entries($ids);
    }
    
    $message = 'Administration action finished successfully';
}

if (filter_input(INPUT_GET, 'ajax', FILTER_SANITIZE_NUMBER_INT)) {
    echo $message;
    die();
}

$kind = filter_input(INPUT_GET, 'kind', FILTER_SANITIZE_STRING);
$sites = array(
    'not_approved' => Database::NOT_APPROVED, 
    'approved' => Database::APPROVED, 
    'bin' => Database::BIN
    );

$site = $sites[$kind];

switch ($site) {
    case Database::NOT_APPROVED:
        $head = 'Not approved';
        $options = array(
            array('text' => 'Approve', 'target' => 2),
            array('text' => 'Remove', 'target' => 3),
        );
        break;
    case Database::APPROVED:
        $head = 'Approved';
        $options = array(
            array('text' => 'Remove', 'target' => 3),
        );
        break;
    case Database::BIN:
        $head = 'Bin';
        $options = array(
            array('text' => 'Restore', 'target' => 2),
            array('text' => 'Remove', 'target' => 0)
        );
        break;
}

$records = $db->get_entries($site);

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
    <link href="styles/theme.css" rel="stylesheet">
  </head>
  <body>
      <?php include '../service/navbar.php'; ?>
    <div class="container" id="content">
      <h1>Administration panel - <?php echo $head ?></h1>
      <?php echo $message; ?>
      <div class="container">
        <?php
        echo "<form method=\"POST\" action=\"admin/edit.php?kind=$kind\">\n";
        echo '<div id="form_buttons">';

        echo '<input type="hidden" name="source" value="' . $site . '">';

        foreach ($options as $option) {
            echo '<button class="btn btn-primary mr-2" name="target" value="' . $option['target'] . '" type="submit">' . $option['text'] . '</button>';
        }
        echo '</div>';

        echo '<div id="records">';

        if (empty($records)) {
            echo 'Empty';
        } else {
            foreach ($records as $record) {
                echo '<div class="container record-edit">';
                echo '<div class="row align-items-center">';
                echo '<div class="col-11">';
                echo format_record($record);
                echo '</div>';
                echo '<div class="col"><input type="checkbox" name="record_' . $record['ID'] . '"></div>';
                echo '</div>';
                echo '</div>';
            }
        }

        echo '</div>';

        echo '</form>';
        ?>
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
      <script src="js/admin_records.js"></script>
      <script src="js/sites.js"></script>
      <script>
          $(function() {
              initAdminKit("div.record-edit");
          });
          $(function () {
              divideIntoSites("#records", 'div.record-edit');
          });
      </script>
  </body>
</html>
