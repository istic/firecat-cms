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
        return $_SESSION[$index];
    }

    function set($index, $value){
        return $_SESSION[$index] = $value;
    }
    
    function destroy(){
        session_destroy();
    }
}