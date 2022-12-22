<?php

class ZzModuleNicoPagezzModuleFrontController extends ModuleFrontController{

    // public $auth = true;
    // public $guestAllowed = false; Si on doit rendre la page accessible seulement pour les utilisateurs connectés 

    public function __construct(){
        parent::__construct();
    }

    public function initContent(){
        parent::initContent();
        // echo "<pre>";
        // var_dump($this->context); Dans l'objet context on peut aller récupérer n'importe quelle info de Prestashop
        // echo"<pre>";
        $tpl_vars = [
            'title' => Configuration::get('ZZMODULENICO_TITLE'),
            'subtitle' => Configuration::get('ZZMODULENICO_SUBTITLE'),
            'description' => Configuration::get('ZZMODULENICO_DESCRIPTION'),
            'link' => ''
        ];
        $this->context->smarty->assign($tpl_vars);
        $this->setTemplate('module:zzmodulenico/template/views/front/front.tpl');
    }

}