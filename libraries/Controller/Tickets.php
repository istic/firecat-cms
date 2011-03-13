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


    function getOwnerDropdown(){

        $ownerids = array();
        $ownervalues = array();


        $ownerids[] = false;
        $ownervalues[] = "-- Users --";

        $users = Model::factory('Model_User')->where("is_admin", 1)->find_many();
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
        return array($ownerids, $ownervalues);
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

        list($ownerids, $ownervalues) = $this->getOwnerDropdown();

        $smarty->assign('ownerids',    $ownerids);
        $smarty->assign('ownervalues', $ownervalues);

        $out = $smarty->fetch('tickets/new.tpl.html');
        $this->response->setcontent($out);
    }

    function createAction(){
        if(!$this->requireAdmin()){
            return false;
        }
        $session = Session::getInstance();
        $user = $session->get("user");

        $POST = $this->request->post;
        $errors = array();

        $ticket = Model::factory('Model_Ticket')->create();
        
        $format = "Y-M-d H:i";

        $now = date($format);
        
        $ticket->date_created = $now;

        $ticket->date_due = date($format, strtotime($POST->date_due));
        if($ticket->date_due == 0){
            $errors['date_due'] = "Unrecognised format";
        }

        list($type, $id) = explode("_", $POST->owner, 2);

        if ($type == "group"){
            $ticket->owner_group_id = $id;
        } elseif($type == "user"){
            $ticket->owner_user_id = $id;
        } else {
            $errors['owner'] = "Unknown owning entity";
        }

        $ticket->reporter_user_id = $user->id;


        $ticket->name = $POST->name;
        if(empty($ticket->name)){
            $errors['name'] = "Need to name things";
        }

        $ticket->description = $POST->description;
        if(empty($ticket->description)){
            $errors['description'] = "Need to describe things";
        }

        $ticket->status = Model_Ticket::S_OPEN;

        if(count($errors) == 0){
            $ticket->save();
            $this->response->redirect("/Tickets/view/". $ticket->getid());
            return;
        }



        $smarty = new SmartyView();
        $smarty->assign('title', 'Errors in Ticket');
        $smarty->assign('date_due', $ticket->date_due);
        $smarty->assign('name', $ticket->name);
        $smarty->assign('reporter', $user->username);
        $smarty->assign('description', $ticket->description);
        $smarty->assign('ownerid', $POST->owner);

        list($ownerids, $ownervalues) = $this->getOwnerDropdown();

        $smarty->assign('ownerids',    $ownerids);
        $smarty->assign('ownervalues', $ownervalues);

        $smarty->assign('errors', $errors);

        $out = $smarty->fetch('tickets/new.tpl.html');
        $this->response->setcontent($out);
        
        return;
    }

    function viewAction(){

        $ticket = Model::Factory('Model_Ticket')
                ->where("id", $this->request->path[2])
                ->find_one();


        $smarty = new SmartyView();
        $smarty->assign('title', 'Ticket');
        $smarty->assign('ticket', $ticket);
        $smarty->assign('owner', $ticket->getOwner());
        $smarty->assign('reporter', $ticket->getReporter());

        $out = $smarty->fetch('tickets/view.tpl.html');
        $this->response->setcontent($out);

    }

}
?>
