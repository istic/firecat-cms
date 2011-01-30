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
}
?>
