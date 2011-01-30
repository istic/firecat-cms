<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Ticket
 *
 * @author Aquarion
 */
class Model_Ticket {

    public static $_table = 'ticket';

    public function owner(){
        return $this->has_one('Model_User', "owner_user_id");
    }

    public function reporter(){
        return $this->has_one('Model_User', "reporter_user_id");
    }

    public function ownergroup(){
        return $this->has_one('Model_Admingroup', "owner_group_id");
    }

    public function awaitinginput(){
        return $this->has_one('Model_User', "awaiting_input_user_id");
    }

    //id int auto_increment,
    //date_created datetime,
    //date_modified timestamp,
    //date_due datetime,
    //date_closed datetime,
    //percentage_done tinyint,
    //
    //owner_user_id int,
    //reporter_user_id int,
    //owner_group_id int,
    //awaiting_input_user_id int,
    //
    //name varchar(255),
    //description mediumtext,
    //
    //priority int default 5,
    //status tinyint,
    //
    //PRIMARY KEY(id)
}
?>
