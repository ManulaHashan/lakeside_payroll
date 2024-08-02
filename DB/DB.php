<?php

class Database
{

    private static $Connection;

    function getConnection()
    {
        if (!isset(Database::$Connection)) {
            $dbhost = "localhost";
            $dbuser = "root";
            $dbpass = "root";
            $dbname = "lakeside_payroll";

            Database::$Connection = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);

            return Database::$Connection;
        } else {
            return Database::$Connection;
        }

        if (mysqli_connect_errno()) {
            echo ("Database Connectin failed : " . mysqli_connect_error() . "( " . mysqli_connect_errno() . " )");
        }
    }
}

function SUD($query)
{
    $DB = new Database();
    mysqli_query($DB->getConnection(), $query);
    if (mysqli_errno($DB->getConnection())) {
        return mysqli_error($DB->getConnection());
    } else {
        return "1";
    }
}

function SUDwithKeys($query)
{
    $DB = new Database();
    mysqli_query($DB->getConnection(), $query);

    if (mysqli_errno($DB->getConnection())) {
        return mysqli_error($DB->getConnection());
    } else {
        // Get the last inserted ID and return it
        return mysqli_insert_id($DB->getConnection());
    }
}

function Search($query)
{
    $DB = new Database();
    $res = mysqli_query($DB->getConnection(), $query);
    if (mysqli_errno($DB->getConnection())) {
        return mysqli_connect_error();
    } else {
        return $res;
    }
}