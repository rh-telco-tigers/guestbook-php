<?php
$_URI = filter_input(INPUT_SERVER, 'REQUEST_URI');

if(substr($_URI, -1) == '/') {
    $_URI .= 'index.php';
}

if(strstr($_URI, 'admin/index.php')) {
    $_URI = ''; // no path should be active
}
    
$_print_item = function($href, $text, $icon=null) use (&$_URI) {
    echo '<li class="nav-item' . (strstr($_URI, $href) ? ' active' : '') . '">';
    echo '<a class="nav-link" href="' . $href . '">';
    if(!empty($icon)){
        echo '<span class="fa fa-' . $icon . '" aria-hidden="true"></span> ';
    }
    echo $text . '</a>';
    echo '</li>';
}
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <span class="navbar-brand mb-0 h1">Guestbook</span>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarNav">
    <ul class="navbar-nav">
      <?php
      $_print_item('form.php', 'Write', 'pencil');
      $_print_item('index.php', 'Review', 'book');
      echo '</ul>';

      if (isset($_SESSION['logged']) || filter_input(INPUT_COOKIE, 'is_logged', FILTER_SANITIZE_NUMBER_INT)) {
          echo '<ul class="nav navbar-nav ml-auto">';
          $_print_item('admin/edit.php?kind=not_approved', 'Not approved', 'inbox');
          $_print_item('admin/edit.php?kind=approved', 'Approved', 'desktop');
          $_print_item('admin/edit.php?kind=bin', 'Bin', 'trash');
          $_print_item('admin/settings.php', 'Settings', 'cogs');
          $_print_item('admin/index.php?action=logout', 'Sign out', 'sign-out');
          echo '</ul>';
      }
      ?>
  </div>
</nav>
<?php
unset($_print_item);
unset($_URI);
