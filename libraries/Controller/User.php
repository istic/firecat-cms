<?php
/**
 * Description of User
 *
 * @author Aquarion
 */
class Controller_User extends Controller {


    function createAction(){

        $session = Session::getInstance();
        $config = Config::getInstance();

        $user = Model::factory('Model_User')->create();
        $user->username = $this->request->post->username;
        $user->email    = $this->request->post->email;

        $errors = array();

        $users = Model::Factory('Model_User')->where("username", $this->request->post->username);
        if($users->count() != 0){
            $errors[] = "That user already exists";
        }

        $users = Model::Factory('Model_User')->where("email", $this->request->post->email);
        if($users->count() != 0){
            $errors[] = "There is already a user with that email address";
        }

        if($this->request->post->password != $this->request->post->password2){
            $errors[] = "Your passwords matcheth not.";
        }

        if(empty($this->request->post->username)){
            $errors[] = "Username is required";
        }
        if(empty($this->request->post->email)){
            $errors[] = "Email is required";
        }
        if(empty($this->request->post->password)){
            $errors[] = "Password is required";
        }
        if(empty($this->request->post->password2)){
            $errors[] = "Password repeat is required";
        }

        $formid = $this->request->post->formid;
        if (empty($formid) || $formid != $session->get("regform_id") ){
            $errors[] = "This form has already been used";
        }
        $session->set("regform_id", 9);

        if(count($errors) == 0){
            $password = $this->request->post->password;
            $user->setPassword($password);
            $user->save();

            $session->flash("User account created. Please log in.");
            $this->response->redirect("/User/login");


        } else {

            $formid = uniqid();
            $session->set("regform_id", $formid);

            $smarty = new SmartyView();
            $smarty->assign('title', 'Register');
            $smarty->assign('nextpage', '/User/create');
            $smarty->assign('formid', $formid);
            $smarty->assign('user', $user);
            $smarty->assign('errors', $errors);
            $out = $smarty->fetch('user/register.tpl.html');
            $this->response->setcontent($out);

        }
        # Username should be unique
        # Passwords should match
        # Email should be unique



    }

    function registerAction(){

        $session = Session::getInstance();
        $formid = uniqid();
        $session->set("regform_id", $formid);

        $user = new StdClass();
        $user->username = "";
        $user->email = "";

        $smarty = new SmartyView();
        $smarty->assign('title', 'Register');
        $smarty->assign('formid', $formid);
        $smarty->assign('nextpage', '/User/create');
        $smarty->assign('user', $user);
        $out = $smarty->fetch('user/register.tpl.html');
        $this->response->setcontent($out);
    }

    function loginAction(){
        $session = Session::getInstance();
        $formid = uniqid();
        $session->set("loginform_id", $formid);

        $smarty = new SmartyView();
        $smarty->assign('title', 'Login');
        $smarty->assign('formid', $formid);
        $smarty->assign('nextpage', '/User/auth');
        $smarty->assign('username', "");
        $out = $smarty->fetch('user/login.tpl.html');
        $this->response->setcontent($out);
    }

    function logoutAction(){
        $session = Session::getInstance();
        $session->destroy();
        $this->response->redirect("/");
        return;
    }

    function authAction(){
        $session = Session::getInstance();
        $config = Config::getInstance();
        
        
        $errors = array();

        if(empty($this->request->post->username)){
            $errors[] = "Username is required";
        }
        if(empty($this->request->post->password)){
            $errors[] = "Password is required";
        }
        
         if(count($errors) == 0){
            $password = $this->request->post->password;
            $username = $this->request->post->username;


            $user = Model_User::authenticate($username, $password);      
            // </old style passwords>

            if($user){

                $session->set("loggedin", true);
                $session->set("user", $user);
               
                if($to = $session->get("afterLogin")){
                    $this->response->redirect($to);
                    return;
                } else {
                    $this->response->redirect("/User");
                    return;
                }

            } else {
                $errors[] = "Username & Password didn't work, try again";
            }
            

        }


        $formid = uniqid();
        $session->set("loginform_id", $formid);

        $smarty = new SmartyView();
        $smarty->assign('title', 'Login');
        $smarty->assign('nextpage', '/User/auth');
        $smarty->assign('formid', $formid);
        $smarty->assign('username', $this->request->post->username);
        $smarty->assign('errors', $errors);
        $out = $smarty->fetch('user/login.tpl.html');
        $this->response->setcontent($out);


    }

    function IndexAction(){
        $this->requireLogin();

        $session = Session::getInstance();
        $user = $session->get("user");

        $smarty = new SmartyView();
        $smarty->assign('title', 'User Details');
        $smarty->assign('user', $user);

        $out = $smarty->fetch('user/viewCurrent.tpl.html');
        $this->response->setcontent($out);


    }


    function forgotpasswordAction(){

        $smarty = new SmartyView();
        $smarty->assign('title', 'Forgot Password');

        $out = $smarty->fetch('user/forgotpassword.tpl.html');
        $this->response->setcontent($out);
    }

    function changePasswordAction(){
        $this->requireLogin();
        
	$session = Session::getInstance();
        $user = $session->get("user");
        
	$smarty = new SmartyView();
        $smarty->assign('title', 'Forgot Password');
	$errors = array();
	if (isset($this->request->post->oldpassword)){
		if (empty($this->request->post->oldpassword)){
			$errors[] = "Old Password is required";
		}
		if (empty($this->request->post->newpassword)){
			$errors[] = "New Password is required";
		}
		if ($this->request->post->newpassword != $this->request->post->newpassword_repeat){
			$errors[] = "New Passwords don't match";
		}

		if (count($errors) == 0){
			$result = Model_User::check_password($user->username, $this->request->post->oldpassword);
			if ($result['status'] == 1){
				$errors[] = "Your old password did not validate";
			} else {
				$user->setPassword($this->request->post->newpassword);
				$user->force_change_password = 0;
				$user->save();
				$session->flash("Your new password has been saved");
		 		$this->response->redirect("/");
			}
	
		}
		
	}

	if (count($errors) > 0){
		$smarty->assign("errors", $errors);
	}


        $out = $smarty->fetch('user/changePassword.tpl.html');
        $this->response->setcontent($out);
    }
}
?>
