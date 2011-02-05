<?php

class Session {
    # Begin Singleton Zen
    static private $_instance;

    static function getInstance(){
        if(empty(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    function __construct(){
        session_start();
    }
    # End Singleton Zen


    function get($index){
        if(!isset($_SESSION[$index])){
            return null;
        }
        return $_SESSION[$index];
    }

    function set($index, $value){
        return $_SESSION[$index] = $value;
    }
    
    function destroy(){
        session_destroy();
    }

    // A flash is a one-time message to be displayed on the next page you see.
    function flash($message){
        if(!isset($_SESSION['flashes'])){
            $_SESSION['flashes'] = array();
        }

        $_SESSION['flashes'][] = $message;
    }

    function getFlashes(){
        if(!isset($_SESSION['flashes'])){
            return array();
        }
        $flashes = $_SESSION['flashes'];
        $_SESSION['flashes'] = array();
        return $flashes;
    }

    function checkForFlashes(){
        if(!isset($_SESSION['flashes'])){
            return false;
        }
        $count = count($_SESSION['flashes']);
        if($count > 0){
            return true;
        } else {
            return false;
        }
    }
}