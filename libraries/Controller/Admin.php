<?php
/**
 * Description of Admin
 *
 * @author Aquarion
 */
class Controller_Admin extends Controller { 


    function IndexAction(){
        $this->requireAdmin();

        $smarty = new SmartyView();
        $smarty->assign('title', 'User Details');

        $out = $smarty->fetch('admin/index.tpl.html');
        $this->response->setcontent($out);

    }

    function BecomeAction(){
        $this->requireAdmin();
        $session = Session::getInstance();
	$who = $this->request->path[2];

	$user = Model::Factory('Model_User')
	    ->where("username", $who)
	    ->find_one();


	$session->set("loggedin", true);
	$session->set("user", $user);

	$to = $session->get("afterLogin");

	if($to){
	    $this->response->redirect($to);
	    return;
	} else {
	    $this->response->redirect("/User");
	    return;
	}
    }

    function AdminGroupsAction(){

        $this->requireAdmin();
        $session = Session::getInstance();

	if (isset($this->request->get->join) & isset($this->request->get->user)){
	    $user = Model::Factory('Model_User')
		->where("username", $this->request->get->user)
		->find_one();


	    $gid = $this->request->get->join;
	    $group = Model::Factory('Model_Admingroup')->find_one($gid);

	    $membership = Model::factory('Model_UserAdminGroupJoin')
		->where("user_id", $user->id)
		->where("admingroup_id", $group->id)
		->find_one();
	    
	    if($membership){
		$session->flash("Already a member.");
	    } else {
		$membership = Model::factory('Model_UserAdminGroupJoin')->create();
		$membership->user_id = $user->id;
		$membership->admingroup_id = $this->request->get->join;
		$membership->save();

		$session->flash("Membership to ".$group->groupname." Added.");
	    }
	}

	if (isset($this->request->get->leave) & isset($this->request->get->user)){
	    $user = Model::Factory('Model_User')
		->where("username", $this->request->get->user)
		->find_one();

	    $gid = $this->request->get->leave;
	    $group = Model::Factory('Model_Admingroup')->find_one($gid);

	    $membership = Model::factory('Model_UserAdminGroupJoin')
		->where("user_id", $user->id)
		->where("admingroup_id", $group->id)
		->find_one();

	    if ($membership){
		$membership->delete();
		$session->flash("Membership to ".$group->groupname." Removed.");
	    }
            
	}

	if (isset($this->request->get->user)){
	    $user = Model::Factory('Model_User')
		->where("username", $this->request->get->user)
		->find_one();

	    $smarty = new SmartyView();
	    $smarty->assign('title', 'User Details');
	    $smarty->assign('thisuser', $user);

	    $groups = Model::Factory('Model_Admingroup')->find_many();
	    $mygroups = array();
	    $othergroups = array();

	    foreach ($groups as $group){
		 $is = $group->members()->where("user_id", $user->id)->find_one();
		 if($is){
		     $mygroups[] = $group;
		 } else {
		     $othergroups[] = $group;
		 }
	    }

	    $smarty->assign('mygroups', $mygroups);
	    $smarty->assign('othergroups', $othergroups);

	    

	    $out = $smarty->fetch('admin/admin_groups_user.tpl.html');
	    $this->response->setcontent($out);
	    return;
	}

	$users = Model::Factory('Model_User')->where("is_admin", 1)->find_many();

	
        $smarty = new SmartyView();
        $smarty->assign('title', 'User Details');
        $smarty->assign('users', $users);

        $out = $smarty->fetch('admin/admin_groups_select_user.tpl.html');
        $this->response->setcontent($out);

    }


}