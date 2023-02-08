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
    </nav>
    <div id="content">
      <div class="container col-md-10 col-lg-8 col-xl-7">
        <div id="response"></div>
        <div id="response_buttons" style="display: none">
            <a class="btn btn-primary" href="index.php" role="button">OK</a>
        </div>
      </div>
      <div id="form_container" class="container">
        <h1>Cretate new entry</h1>
        <form action="save.php" method="POST">
          <div class="form-group row">
            <label for="name_input" class="col-sm-2 col-lg-1 col-form-label">Name:</label>
            <div class="col-sm-10">
              <input class="form-control" id="name_input" type="text" maxlength="30" name="name"
                     value="" required=""  placeholder="Type your name">
            </div>
          </div>
          <div class="form-group row">
            <label for="email_input" class="col-sm-2 col-lg-1 col-form-label">Email:</label>
            <div class="col-sm-10">
              <input class="form-control" id="email_input" type="emali" maxlength="35" name="email"
                     value="" placeholder="Type your email">
            </div>
          </div>

          <div class="form-group row">
            <label for="entry_input" class="col-sm-2 col-lg-1 col-form-label">Message:</label>
            <div class="col-sm-10">
              <div id="form_options" style="display: none">
                <img src="images/text_options/p.gif" alt="p" />
                <img src="images/text_options/b.gif" alt="b"/>
                <img src="images/text_options/i.gif" alt="i"/>
                <img src="images/text_options/u.gif" alt="u"/>

                <img src="images/text_options/center.gif" alt="center"/>
                <img src="images/text_options/right.gif" alt="right"/>
                <img src="images/text_options/br.gif" alt="br"/>

                <img src="images/text_options/link.gif" alt="link"/>
                <img src="images/text_options/color.gif" alt="color"/>
                <img src="images/text_options/bgcolor.gif" alt="bgcolor"/>
              </div>
              
              <textarea class="form-control" id="entry_input" name="entry"
                        value="" required="" placeholder="Type your message"></textarea>

              <div id="emoticons" style="display: none">
                <img src="images/emoticons/emo1.gif" alt=";|" />
                <img src="images/emoticons/emo2.gif" alt=":|" />
                <img src="images/emoticons/emo3.gif" alt="{no}" />
                <img src="images/emoticons/emo4.gif" alt="{yes}" />

                <img src="images/emoticons/emo5.gif" alt=":)" />
                <img src="images/emoticons/emo6.gif" alt=":}" />
                <img src="images/emoticons/emo7.gif" alt=":]" />
                <img src="images/emoticons/emo8.gif" alt=";)" />

                <img src="images/emoticons/emo9.gif" alt=":O" />
                <img src="images/emoticons/emo10.gif" alt=":?" />
                <img src="images/emoticons/emo11.gif" alt=":[" />
                <img src="images/emoticons/emo12.gif" alt="X|" />

                <img src="images/emoticons/emo13.gif" alt=":(" />
                <img src="images/emoticons/emo14.gif" alt="{|" />
                <img src="images/emoticons/emo15.gif" alt=";(" />
                <img src="images/emoticons/emo16.gif" alt=":{" />
              </div>
            </div>
          </div>
          <button type="submit" class="btn btn-primary">Save</button>
        </form>
      </div>
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
    <script src="js/message_tools.js"></script>
    <script src="js/entry_send.js"></script>
    <script>
        $(installMessageTools);
        $(installAjax);
    </script>
  </body>
</html>
