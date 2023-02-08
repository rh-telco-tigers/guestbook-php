
CREATE TABLE Users (
    ID INTEGER NOT NULL PRIMARY KEY AUTO_INCREMENT, 
    UserName TEXT NOT NULL UNIQUE, 
    Password TEXT NOT NULL,
    LastValidLoginTime INTEGER NOT NULL,
    LastFailedLoginTime INTEGER
);

INSERT INTO Users
VALUES (NULL, 'admin', '" . sha1('admin') . "', strftime('%s', 'now'), NULL);

CREATE TABLE Entries (
    ID INTEGER NOT NULL PRIMARY KEY AUTO_INCREMENT,
    IP TEXT,
    Kind INTEGER NOT NULL,
    Name TEXT NOT NULL,
    Email TEXT,
    Message TEXT,
    CreationTime INTEGER  NOT NULL,
    ModificationTime INTEGER  NOT NULL
);

CREATE TABLE FailedLogins (
    ID INTEGER NOT NULL PRIMARY KEY AUTO_INCREMENT,
    IP TEXT UNIQUE,
    FailCount INTEGER,
    LastTryTime INTEGER
);