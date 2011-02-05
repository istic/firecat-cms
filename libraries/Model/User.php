<?php
/**
 * Description of User
 *
 * @author Aquarion
 */
class Model_User extends Model {

    public static $_table = 'user';

    //id int auto_increment,
    //public $username = "";
    //public $password = "";
    //public $email = "";
    //
    //public $force_change_password;
    //public $is_admin;
    //public $is_npc;
    //public $password_reset_key;

    public function owned(){
        return $this->has_many('Model_Ticket', "owner_user_id");
    }

    public function reported(){
        return $this->has_many('Model_Ticket', "reporter_user_id");
    }

    public function awaitinginput(){
        return $this->has_many('Model_Ticket', "awaiting_input_user_id");
    }


    static public function authenticate($username, $password){
        $session = Session::getInstance();
        $config = Config::getInstance();
        
        $hashed = sha1($password.$config->get("crypto", "salt"));

        $user = Model::Factory('Model_User')
            ->where("username", $username)
            ->where("password", $hashed)
            ->find_one();

        // Code to support login with old-style MD5 passwords,
        // Ideally we want to remove this at some point.
        if ($user == 0){
            $user = Model::Factory('Model_User')
            ->where("username", $username)
            ->where("password", md5($password))
            ->find_one();

            if($user){
                $session->flash("Sorry, due to a security upgrade you need to reset your password");
                $user->force_change_password = 1;
                $user->save();
            }
        }

        return $user;

    }
}
?>
