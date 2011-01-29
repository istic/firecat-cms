<?php

class Controller_FrontPage extends Controller {
    function IndexAction(){
        $this->response->setcontent("Hello World");
    }
}

?>
