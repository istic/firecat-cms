<?php

class Controller_FrontPage extends Controller {
    function IndexAction(){
        $smarty = new SmartyView();
        $out = $smarty->assign('bodyStyle', "frontpage");
        $out = $smarty->assign('title', "Firecat Maskerade");
        $out = $smarty->fetch('frontpage.tpl.html');
        $this->response->setcontent($out);
    }
}

?>
