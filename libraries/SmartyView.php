<?php
/**
 * Description of SmartyView
 *
 * @author Aquarion
 */
class SmartyView extends Smarty {
   function __construct()
   {

        // Class Constructor.
        // These automatically get set with each new instance.

        parent::__construct();

        $this->template_dir = PATH."/../templates";
        $this->compile_dir  = PATH."/../cache/smarty_compile";
        $this->config_dir   = PATH."/../templates/config";
        $this->cache_dir    = PATH."/../cache/smarty_cache";

        //$this->caching = Smarty::CACHING_LIFETIME_CURRENT;
        $this->assign('app_name', 'Firecat');
   }
}
?>
