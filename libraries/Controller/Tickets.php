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
class Controller_Tickets extends Controller {


    function indexAction(){
        if(!$this->requireAdmin()){
            return false;
        }

        $session = Session::getInstance();
        $user = $session->get("user");

        $mytickets = $user->owned()->find_many();
        $myinput   = $user->awaitinginput()->find_many();


        $smarty = new SmartyView();
        $smarty->assign('title', 'My Tickets');
        $smarty->assign('mytickets', $mytickets);
        $smarty->assign('myinput',   $myinput);
        $out = $smarty->fetch('tickets/frontpage.tpl.html');
        $this->response->setcontent($out);
    }

    function newAction(){
        if(!$this->requireAdmin()){
            return false;
        }
        
        $session = Session::getInstance();
        $user = $session->get("user");

        $smarty = new SmartyView();
        $smarty->assign('title', 'New Ticket');
        $smarty->assign('date_due', '');
        $smarty->assign('name', '');
        $smarty->assign('reporter', $user->username);
        $smarty->assign('description', "");

        $ownerids = array();
        $ownervalues = array();

        
        $ownerids[] = false;
        $ownervalues[] = "-- Users --";

        $users = Model::factory('Model_User')->find_many();
        foreach($users as $user){
            $ownerids[] = "user_".$user->id;
            $ownervalues[] = $user->username;
        }
        $ownerids[] = false;
        $ownervalues[] = "-- Groups --";

        $groups = Model::factory('Model_Admingroup')->find_many();
        foreach($groups as $group){
            $ownerids[] = "group_".$group->id;
            $ownervalues[] = $group->groupname;
        }
        $smarty->assign('ownerids',    $ownerids);
        $smarty->assign('ownervalues', $ownervalues);

        $out = $smarty->fetch('tickets/new.tpl.html');
        $this->response->setcontent($out);
    }

}
?>
