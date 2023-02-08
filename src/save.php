<?php
require_once 'service/storage.php';


$TEXT_FORMATING = array(
    '[b]' => '<b>',
    '[/b]' => '</b>',
    '[i]' => '<i>',
    '[/i]' => '</i>',
    '[u]' => '<u>',
    '[/u]' => '</u>',
    '[center]' => '<div align=center>',
    '[/center]' => '</div>',
    '[right]' => '<div align=right>',
    '[/right]' => '</div>',
    '[color]' => '<span style="color:',
    '[/color]' => '</span>',
    '[bgcolor]' => '<span style="background-color:',
    '[/bgcolor]' => '</span>',
    '[/rgb]' => '">',
    '[br]' => '<br />',
    '[p]' => '<p>',
    '[/p]' => '</p>',
    '[link]' => '<a href="http://',
    '[/url]' => '">',
    '[/link]' => '</a>',
    "&#13;" => '<br />'
);


$name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS);
$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);

$message = str_replace(
    array_keys($TEXT_FORMATING), 
    array_values($TEXT_FORMATING),  
    filter_input(INPUT_POST, 'entry', FILTER_SANITIZE_SPECIAL_CHARS)
);

if (!empty($name) & !empty($message)) {
    try {
        $db = new Database();
        $db->create_new_entry($name, $email, $message);

        $reply = format_reply(
            'success', 'Thank you very much.<br />Entry is waiting for administrator approvement.'
        );
    } catch (Exception $exc) {
        $reply = format_reply(
            'danger', 'Error during processing occurred.', $exc->getMessage()
        );
    }
} else {
    $details = 'Following items are incorrect: <ul>';

    if (empty($name)) {
        $details .= '<li>Name is empty</li>';
    }

    if (empty($message)) {
        $details .= '<li>Message is empty</li>';
    }
    $details .= '</ul>';

    $reply = format_reply(
        'warning', 'Entry is not valid.', $details
    );
}   

if(filter_input(INPUT_GET, 'ajax', FILTER_SANITIZE_NUMBER_INT)) {
    echo $reply;
    die();
}
?>
<!doctype html>
<html>
  <head>
    <meta name="description" content="">
    <meta name="keywords" content="">
    <meta charset="UTF-8">
    <title>Guestbook</title>
    <link rel="stylesheet" 
          href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css" 
          integrity="sha384-PsH8R72JQ3SOdhVi3uxftmaW6Vc51MKb0q5P2rRUpPvrszuE4W1povHYgTpBfshb" 
          crossorigin="anonymous">
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
  </head>
  <body>
    <?php include_once 'service/navbar.php'; ?>
    <div id="content" class="container col-md-10 col-lg-8 col-xl-7 mt-4 mb-4">
    <?php echo $reply; ?>
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
  </body>
</html>