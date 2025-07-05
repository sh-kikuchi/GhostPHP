<?php

/**
 * Class UsersTable
 * 
 * This class provides the SQL statement for creating a 'users' table.
 */
class UsersTable {

    /**
     * Get the SQL statement for creating the 'users' table.
     *
     * @return string The SQL statement for creating the 'users' table.
     */
    public function getSql(){
        return 'CREATE TABLE if not exists users (
            id int NOT NULL AUTO_INCREMENT,
            name varchar(255) NOT NULL,
            email varchar(255) NOT NULL,
            password varchar(255) NOT NULL,
            PRIMARY KEY (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;';
    }
}
