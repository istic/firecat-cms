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
            $password = sha1($password.$config->get("crypto", "salt"));
            $user->password = $password;
            $user->save();
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
            $password = sha1($password.$config->get("crypto", "salt"));

            $users = Model::Factory('Model_User')
                ->where("username", $this->request->post->username)
                ->where("password", $password)
                ->find_one();

            if($users != 0){

                $session->set("loggedin", true);
                $session->set("user", $users);
               
                if($to = $session->get("afterLogin")){
                    $this->response->redirect($to);
                    return;
                } else {
                    $this->response->redirect("/");
                    return;
                }

            } else {
                $errors[] = "Username & Password didn't work, try again";
                $errors[] = $password;
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
        
        $this->response->setcontent("Hello World");
    }
}
?>
