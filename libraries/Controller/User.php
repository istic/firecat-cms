<?php
/**
 * Description of User
 *
 * @author Aquarion
 */
class Controller_User extends Controller {

    function registerAction(){
        $smarty = new SmartyView();
        $smarty->assign('title', 'Register');
        $smarty->assign('content', 'Hello World');
        $out = $smarty->fetch('user/register.tpl.html');
        $this->response->setcontent($out);
    }

    function IndexAction(){
        
        $this->response->setcontent("Hello World");
    }
}
?>
