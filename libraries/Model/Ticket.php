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
class Model_Ticket  extends Model {

    const S_DELETED = 0;
    const S_OPEN = 5;
    const S_COMPLETED = 100;
    const S_CLOSED = 125;


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

    public function humanStatus($status){

            switch ($status){
                case Model_Ticket::S_DELETED:
                    return "Deleted";

                case Model_Ticket::S_OPEN:
                    return "Open";

                case Model_Ticket::S_COMPLETED:
                    return "Completed!";

                case Model_Ticket::S_CLOSED:
                    return "Closed uncompleted";

            }

            return "Unknown";
    }

    public function myHumanStatus(){
	return $this->humanStatus($this->status);
    }

    public function listStatuses(){
        return array(
            Model_Ticket::S_DELETED,
            Model_Ticket::S_OPEN,
            Model_Ticket::S_COMPLETED,
            Model_Ticket::S_CLOSED
           );
    }

    function getid(){
        return $this->id();
    }

    function getOwner(){
        if($this->owner_user_id){
            $owner = Model::Factory('Model_User')
                ->where("id", $this->owner_user_id)
                ->find_one();
	    $owner->name = $owner->username;
	    $owner->has_user = true;

        } else {
            $owner = Model::Factory('Model_Admingroup')
                ->where("id", $this->owner_group_id)
                ->find_one();
	    $owner->name = $owner->groupname;
	    $owner->has_user = false;
        }


        
        return $owner;

    }

    function getReporter(){

            return Model::Factory('Model_User')
                ->where("id", $this->reporter_user_id)
                ->find_one();
    }

    function due(){
	return $this->date_due;
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
