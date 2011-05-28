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

        $mytickets      = $user->tickets_owned()->find_many();
        $myinput        = $user->tickets_awaitinginput()->find_many();
	$ticketgroups   = array();

	foreach($user->admingroups()->find_many() as $group){

	    $tickets = $group->tickets_groupowned()->find_many();

	    $ticketgroups[] = array(
		"group"   => $group,
		"tickets" => $tickets
	    );
	}

        $smarty = new SmartyView();
        $smarty->assign('title', 'My Tickets');
        $smarty->assign('mytickets', $mytickets);
        $smarty->assign('grouptickets', $ticketgroups);
        $smarty->assign('myinput',   $myinput);
        $out = $smarty->fetch('tickets/frontpage.tpl.html');
        $this->response->setcontent($out);
    }

    function allAction(){

	$tickets =  Model::factory('Model_Ticket')->find_many();

        $smarty = new SmartyView();
        $smarty->assign('title', 'All Tickets');
        $smarty->assign('tickets', $tickets);
        $out = $smarty->fetch('tickets/ticketlist.tpl.html');
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
        $smarty->assign('ownerid', 'user_'.$user->id);
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
        
        $format = "Y-m-d H:i";

        $now = date($format);
        
        $ticket->date_created = $now;

        $ticket->date_due = date($format, strtotime($POST->date_due));

        if(strtotime($POST->date_due) == 0){
	    $ticket->date_due = $POST->date_due;
            $errors['date_due'] = "I've no idea what that date is supposed to be, sorry.";
	    
        }

        list($type, $id) = explode("_", $POST->owner, 2);

        if ($type == "group"){
            $ticket->owner_group_id = $id;
            $ticket->owner_user_id = 0;
        } elseif($type == "user"){
            $ticket->owner_user_id = $id;
            $ticket->owner_group_id = 0;
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


    function editAction(){
        $format = "Y-m-d H:i";

	
        $ticket = Model::Factory('Model_Ticket')
                ->where("id", $this->request->path[2])
                ->find_one();

	$errors = array();

	$POST = $this->request->post;
	if (isset($POST->name)){

	    $ticket->date_due = date($format, strtotime($POST->date_due));

	    if(0 == strtotime($POST->date_due)){
		$ticket->date_due = $POST->date_due;
		$errors['date_due'] = "I've no idea what that date is supposed to be, sorry.";
	    }


	    if (! $POST->owner){
		$errors['owner'] = "Unknown owning entity";

	    } else {

		list($type, $id) = explode("_", $POST->owner, 2);


		if ($type == "group"){
		    $ticket->owner_group_id = $id;
		    $ticket->owner_user_id = 0;
		} elseif($type == "user"){
		    $ticket->owner_user_id = $id;
		    $ticket->owner_group_id = 0;
		} else {
		    $errors['owner'] = "Unknown owning entity";
		}
	    }


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

	}

	$owner = $ticket->getOwner();

        $smarty = new SmartyView();
        $smarty->assign('title', 'Ticket');
        $smarty->assign('ticket', $ticket);
        $smarty->assign('owner', $owner);
        $smarty->assign('reporter', $ticket->getReporter());

	if ($owner->has_user){
	    $smarty->assign('ownerid', "user_".$ticket->owner_user_id);
	} else {
	    $smarty->assign('ownerid', "group_".$ticket->owner_group_id);
	}

        list($ownerids, $ownervalues) = $this->getOwnerDropdown();
        $smarty->assign('ownerids',    $ownerids);
        $smarty->assign('ownervalues', $ownervalues);

        $smarty->assign('errors', $errors);
	
        $out = $smarty->fetch('tickets/edit.tpl.html');
        $this->response->setcontent($out);

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

    function pickupAction(){
	
        $ticket = Model::Factory('Model_Ticket')
                ->where("id", $this->request->path[2])
                ->find_one();

        $session = Session::getInstance();
        $user = $session->get("user");

	$ticket->owner_group_id = 0;
	$ticket->owner_user_id = $user->id;
	$ticket->save();

	$this->response->redirect("/Tickets/view/".$ticket->id);

    }

    function markAction(){
        $ticket = Model::Factory('Model_Ticket')
                ->where("id", $this->request->path[2])
                ->find_one();

        $session = Session::getInstance();
        $user = $session->get("user");

	$newstatus = $this->request->path[3];
	if(null === array_search ($newstatus, $ticket->listStatuses()) ) {
	    $session->flash("Status $newstatus Unknown");

	} else {
	    $ticket->status = $newstatus;
	    if ($newstatus == Model_Ticket::S_COMPLETED){
		$ticket->percentage_done = 100;
	    }
	    $ticket->save();
	    $session->flash("Ticket marked as ".$ticket->humanStatus($newstatus));
	}
	$this->response->redirect("/Tickets/view/".$ticket->id);

    }
    
    function percentAction(){
        $ticket = Model::Factory('Model_Ticket')
                ->where("id", $this->request->path[2])
                ->find_one();

        $session = Session::getInstance();
        $user = $session->get("user");

	$percentage_done = $this->request->path[3];


	if($percentage_done > 100 || $percentage_done < 0 ) {
	    $session->flash("What?");

	} else {
	    $ticket->status = Model_Ticket::S_OPEN;
	    $ticket->percentage_done = $percentage_done;
	    $ticket->save();
	    $session->flash("Ticket marked as ".$percentage_done."% complete");
	}
	$this->response->redirect("/Tickets/view/".$ticket->id);

    }

}
?>
