<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Admingroup
 *
 * @author Aquarion
 */
class Model_Admingroup extends Model {
    public static $_table = 'admingroup';

    public function members() {
        return $this->has_many_through('Model_User', "Model_UserAdminGroupJoin", "admingroup_id", "user_id");
    }

    public function tickets_groupowned(){
        return $this->has_many('Model_Ticket', "owner_group_id");
    }
}

