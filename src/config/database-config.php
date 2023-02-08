<?php

    ## Load the database settings from environment variables
    $dsn = getenv('REMOTE_DSN');
    $user = getenv('REMOTE_DB_USER');
    $password = getenv('REMOTE_DB_PASS');

    ## If you want to configure this application from a single file, 
    ## comment out (or remove) lines 3-6 and uncomment the following
    ## lines and update with the proper information
    # $dsn = "mysql:dbname=yourdbname;host=127.0.0.1";
    # $user = "username";
    # $password = "password";
?>