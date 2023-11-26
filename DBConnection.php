<?php
if(!is_dir(__DIR__.'/db'))
    mkdir(__DIR__.'/db');
if(!defined('db_file')) define('db_file',__DIR__.'./db/lhims.db');
if(!defined('tZone')) define('tZone',"Asia/Manila");
if(!defined('dZone')) define('dZone',ini_get('date.timezone'));
function my_udf_md5($string) {
    return md5($string);
}

Class DBConnection extends SQLite3{
    protected $db;
    function __construct(){
        $this->open(__DIR__ . '/db/lhims.db');
        $this->createFunction('md5', 'my_udf_md5');
        $this->exec("PRAGMA foreign_keys = ON;");

        $this->exec("CREATE TABLE IF NOT EXISTS `user_list` (
            `user_id` INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
            `fullname` INTEGER NOT NULL,
            `username` TEXT NOT NULL,
            `password` TEXT NOT NULL,
            `status` INTEGER NOT NULL Default 1,
            `date_created` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )"); 

        $this->exec("CREATE TABLE IF NOT EXISTS `carrier_list` (
            `carrier_id` INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
            `name` TEXT NOT NULL,
            `contact` TEXT NOT NULL,
            `address` TEXT NOT NULL,
            `status` INTEGER NOT NULL DEFAULT 1,
            `date_added` TIMESTAMP NO NULL DEFAULT CURRENT_TIMESTAMP
        )");

        $this->exec("CREATE TABLE IF NOT EXISTS `parcel_type_list` (
            `parcel_type_id` INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
            `name` TEXT NOT NULL,
            `description` TEXT NOT NULL,
            `status` INTEGER NOT NULL DEFAULT 1
        )");
        $this->exec("CREATE TABLE IF NOT EXISTS `parcel_list` (
            `parcel_id` INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
            `parcel_type_id` INTEGER NOT NULL,
            `code` TEXT NOT NULL,
            `sender_name` TEXT NOT NULL,
            `sender_contact` TEXT NOT NULL,
            `sender_address` TEXT NOT NULL,
            `receiver_name` TEXT NOT NULL,
            `receiver_contact` TEXT NOT NULL,
            `receiver_address` TEXT NOT NULL,
            `remarks` TEXT NOT NULL,
            `status` INTEGER NOT NULL DEFAULT 0,
            `date_added` TIMESTAMP NO NULL DEFAULT CURRENT_TIMESTAMP,
            `date_updated` TIMESTAMP NO NULL DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY('parcel_type_id') references `parcel_type_list`(parcel_type_id) ON DELETE CASCADE
        )");

        $this->exec("CREATE TABLE IF NOT EXISTS `delivery_list` (
            `delivery_id` INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
            `parcel_id` INTEGER NOT NULL,
            `carrier_id` INTEGER NOT NULL,
            `status` INTEGER NOT NULL Default 0,
            `date_added` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY('parcel_id') references `parcel_list`(parcel_id) ON DELETE CASCADE,
            FOREIGN KEY('carrier_id') references `carrier_list`(carrier_id) ON DELETE CASCADE
        )");

        $this->exec("CREATE TABLE IF NOT EXISTS `parcel_tracks` (
            `track_id` INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
            `parcel_id` INTEGER NOT NULL,
            `description` TEXT NOT NULL,
            `date_added` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY('parcel_id') references `parcel_list`(parcel_id) ON DELETE CASCADE
        )");
        
        // $this->exec("CREATE TRIGGER IF NOT EXISTS updatedTime_parcel AFTER UPDATE on `parcel_list`
        // BEGIN
        //     UPDATE `parcel_list` SET date_updated = CURRENT_TIMESTAMP where parcel_id = parcel_id;
        // END
        // ");

        $this->exec("INSERT or IGNORE INTO `user_list` VALUES (1,'Administrator','admin',md5('admin123'),1, CURRENT_TIMESTAMP)");

    }
    function __destruct(){
         $this->close();
    }
}

$conn = new DBConnection();