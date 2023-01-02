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
        if (isset($_POST['code'])) {
            echo("<h1 class='text-danger'>salut</h1>");
            echo("<h1 class='text-danger'>".$_POST['code']."</h1>");
            $this->generateCartRule($_POST['code']);
        }
        $tpl_vars = [
            'reduc' => Configuration::get('ZZMODULENICO_REDUC'),
            'maxReduc' => Configuration::get('ZZMODULENICO_MAXREDUC'),
            'code' => $this->generateCode(),
            'cartRules' => var_dump($this->displaySponsorship()),
            'link' => ''
        ];
        $this->context->smarty->assign($tpl_vars);
        $this->setTemplate('module:zzmodulenico/template/views/front/front.tpl');
    }

    public function generateCartRule($code) {

        $promo = new CartRule();
        $promo->name[1] = "Réduction de parrainage";
        $promo->id_customer = $this->context->customer->id;
        $promo->date_from = date("Y-m-d h:i:s");
        $promo->date_to = "2030-01-01 00:00:00";
        $promo->code = $code;
        $promo->reduction_percent = Configuration::get('ZZMODULENICO_REDUC');
        $promo->active = 0;
        $promo->save();

    }

    public function displaySponsorship() {
        return $this->context->cart->getCartRules();
    }

    public function generateCode() {
        $monString = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789'; //62 characters
        $monCode = [];
        for ($a = 0; $a < 30; $a++) {
            $monRand = rand(0, 61);
            array_push($monCode, $monString[$monRand]);
        }
        return implode($monCode);
    }
}