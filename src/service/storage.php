<?php
require_once(__DIR__.'/../config/database-config.php');

date_default_timezone_set("Europe/London");  # TODO allow to set in settings panel

$EMOTICONS = array(
    ';|' => 'emo1',
    ':|' => 'emo2',
    '{no}' => 'emo3',
    '{yes}' => 'emo4',
    ':)' => 'emo5',
    ':}' => 'emo6',
    ':]' => 'emo7',
    ';)' => 'emo8',
    ':O' => 'emo9',
    ':?' => 'emo10',
    ':[' => 'emo11',
    'X|' => 'emo12',
    ':(' => 'emo13',
    '{|' => 'emo14',
    ';(' => 'emo15',
    ':{' => 'emo16',
);

class Database {
    const NOT_APPROVED = 1;
    const APPROVED = 2;
    const BIN = 3;
    
    private $pdo = null;
    private $location = '';
    
    public function __construct() {

        global $dsn, $user, $password;

        $this->pdo = new PDO($dsn, $user, $password);
        $this->pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
        
        $request = $this->pdo->prepare("SELECT TOP 1 * FROM FailedLogins");
        try {
            $request->execute();
        } catch (Exception $e) {
            // We got an exception (table not found)
            // so we need to build the database structure
            $this->init_db();
        }
    
    }

    private function init_db() {
        $this->pdo->exec("
            CREATE TABLE Users (
                ID int NOT NULL IDENTITY(1, 1) PRIMARY KEY, 
                UserName varchar(255) NOT NULL UNIQUE, 
                Password varchar(255) NOT NULL,
                LastValidLoginTime INTEGER NOT NULL,
                LastFailedLoginTime INTEGER
            );");
        
        $this->pdo->exec("
            INSERT INTO Users (UserName, Password, LastValidLoginTime) 
            VALUES ('admin', CONVERT(char(48),HASHBYTES('SHA1', 'admin'),2), DATEDIFF(SECOND,'1970-01-01', GETUTCDATE())
            );");

        $this->pdo->exec("
            CREATE TABLE Entries (
                ID int NOT NULL IDENTITY(1, 1) PRIMARY KEY,
                IP varchar(255),
                Kind INTEGER NOT NULL,
                Name varchar(255) NOT NULL,
                Email varchar(255),
                Message varchar(255),
                CreationTime INTEGER  NOT NULL,
                ModificationTime INTEGER  NOT NULL
            );");
        
        $this->pdo->exec("
            CREATE TABLE FailedLogins (
                ID int NOT NULL IDENTITY(1, 1) PRIMARY KEY,
                IP varchar(255) UNIQUE,
                FailCount INTEGER,
                LastTryTime INTEGER
            );");
        
    }
    
    public function check_user($user_name, $password){
        $request = $this->pdo->prepare("SELECT Password FROM Users WHERE UserName=:user_name;");
        $request->bindParam(':user_name', $user_name );
        $request->execute();

        $valid_password = $request->fetch()['Password'];
        $valid_password_lower = trim(strtolower($valid_password));
        $password_lower = strtolower(sha1($password));
        if (!empty($valid_password)) {
            return $valid_password_lower === $password_lower;
        } else {
            echo "password empty";
            return false;
        }
    }
    
    public function change_user($user_name, $new_user_name, $new_password) {
        $sha_password = sha1($new_password);
        $request = $this->pdo->prepare("UPDATE Users SET UserName=:user_name, Password=:password WHERE UserName=:prev_name;");
        $request->bindParam(':user_name', $new_user_name);
        $request->bindParam(':password', $sha_password);
        $request->bindParam(':prev_name', $user_name);
        $request->execute();
    }
    
    public function get_next_login_try_time() {
        $ip = $this->get_ip();
        
        $request = $this->pdo->prepare("
            SELECT FailCount, LastTryTime
            FROM FailedLogins
            WHERE IP=:ip;
            ");
        $request->bindParam(':ip', $ip);
        $request->execute();
        
        $data = $request->fetch();
        
        if ($data) {
            return $data['FailCount'] > 3? 
                $data['LastTryTime'] + ($data['FailCount'] - 3) * 5 * 60: 0;
        }
    }
    
    public function get_last_login_time($user_name) {
        $request = $this->pdo->prepare("
            SELECT LastValidLoginTime, LastFailedLoginTime
            FROM Users 
            WHERE UserName=:user;");
        $request->bindParam(':user', $user_name);
        $request->execute();
        
        return $request->fetch();
    }
    
    public function register_login_success($user_name) {
        $ip = $this->get_ip();
        
        $request = $this->pdo->prepare("
            DELETE FROM FailedLogins
            WHERE IP=:ip;
            ");
        $request->bindParam(':ip', $ip);
        $request->execute();
        
        $request = $this->pdo->prepare("
            UPDATE Users 
            SET LastValidLoginTime=DATEDIFF(SECOND,'1970-01-01', GETUTCDATE())
            WHERE UserName=:user;
            ");
        $request->bindParam(':user', $user_name);
        $request->execute();
    }
    
    public function register_login_fail($user_name) {
        $ip = $this->get_ip();

        $request = $this->pdo->prepare("
            UPDATE Users 
            SET LastFailedLoginTime=DATEDIFF(SECOND,'1970-01-01', GETUTCDATE())
            WHERE UserName=:user;
            ");
        $request->bindParam(':user', $user_name);
        $request->execute();
        
        $request = $this->pdo->prepare("
            SELECT FailCount
            FROM FailedLogins 
            WHERE IP=:ip;");
        $request->bindParam(':ip', $ip);
        $request->execute();
        
        $fail_count = $request->fetch()['FailCount'];
            
        if(empty($fail_count)) {
            $request = $this->pdo->prepare("
                INSERT INTO FailedLogins (IP, FailCount, LastTryTime)
                VALUES ( :ip, 1, DATEDIFF(SECOND,'1970-01-01', GETUTCDATE()));
                ");
            $request->bindParam(':ip', $ip);
            $request->execute();
        } else {
            $request = $this->pdo->prepare("
                UPDATE FailedLogins 
                SET FailCount=:fail_count, LastTryTime=DATEDIFF(SECOND,'1970-01-01', GETUTCDATE())
                WHERE IP=:ip;
                ");
            $request->bindValue(':fail_count', $fail_count + 1, PDO::PARAM_INT);
            $request->bindParam(':ip', $ip);
            $request->execute();
        }
    }
    
    public function create_new_entry($name, $email, $message) {
        $this->pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING );
        $request = $this->pdo->prepare("
            INSERT INTO Entries (IP, Kind, Name, Email, Message, CreationTime, ModificationTime)
            VALUES ( :ip, :kind, :name, :email, :message, DATEDIFF(SECOND,'1970-01-01', GETUTCDATE()), DATEDIFF(SECOND,'1970-01-01', GETUTCDATE()));
            ");
        $request->bindValue(':kind', $this::NOT_APPROVED, PDO::PARAM_INT);
        $request->bindValue(':ip', $this->get_ip());
        $request->bindParam(':name', $name);
        $request->bindParam(':email', $email);
        $request->bindParam(':message', $message);
        $result = $request->execute();
    }
    
    public function get_entries($kind=Database::APPROVED) {
        $request = $this->pdo->prepare("
            SELECT ID, Name, Email, Message, CreationTime, ModificationTime 
            FROM Entries 
            WHERE Kind=:kind
            ORDER BY CreationTime DESC;
            ");
        $request->bindParam(':kind', $kind, PDO::PARAM_INT);
        $request->execute();
        
        return $request->fetchAll();
    }
    
    public function move_entries($ids, $target) {
        $ids_str = '(' . join(', ', $ids) . ')';
        $request = $this->pdo->prepare("
            UPDATE Entries 
            SET Kind=:kind, ModificationTime=DATEDIFF(SECOND,'1970-01-01', GETUTCDATE())
            WHERE ID IN $ids_str;
            ");
        $request->bindParam(':kind', $target, PDO::PARAM_INT);
        $request->execute();
    }
    
    public function remove_entries($ids) {
        $ids_str = '(' . join(', ', $ids) . ')';
        $this->pdo->exec("
            DELETE FROM Entries
            WHERE ID IN $ids_str;
            ");
    }
    
    private function get_ip(){
        return filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_SANITIZE_SPECIAL_CHARS);
    }
}


function format_reply($status, $message, $details='') {
    return '
    <div id="status" class="alert alert-' . $status . ' alert-dismissible fade show mt-4 mb-4" role="alert">
      <h4 class="alert-heading">' . $message . '</h4>
      <p>' . $details . '</p>
      <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
    </div>';
}

function format_record(&$record) {
    return '<div class="container record">
        <div class="row">
            <div class="col-12">' . replace_emoticons($record['Message']) . '</div>
        </div>
        <div class="row">
            <div class="col-sm-5 col-md-4">' . date('d.m.y H:i:s', $record['CreationTime']) . '</div>
            <div class="col-sm-5 col-md-4">' . $record['Name'] . (empty($record['Email']) ? '' : ' (' . $record['Email'] . ')') . '</div>
        </div>
    </div>';
}


function replace_emoticons($text) {
    global $EMOTICONS;
    
    $emoticon_path = function($v)  {
        return '<img src="images/emoticons/' . $v . '.gif" />';
    };

    return str_replace(
        array_keys($EMOTICONS), array_map($emoticon_path, array_values($EMOTICONS)), $text
    );
}
