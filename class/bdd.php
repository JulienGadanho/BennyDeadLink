<?php

include dirname(__FILE__) . '/../config.php';

class Bdd
{
    function __construct()
    {
        global $bdd;
        $bdd = new PDO("mysql:host=".HOST.";dbname=".DB.";charset=utf8", USER, PASS);
        return $bdd;
    }
    
    function Off()
    {
        global $bdd;
        $bdd = null;
    }
}