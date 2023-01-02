<?php

use PrestaShop\PrestaShop\Core\Module\WidgetInterface;

class Zzmodulenico extends Module implements WidgetInterface{

    private $templateFile;//fichier de template

    public function __construct(){
        $this-> name = 'zzmodulenico';// nom du module
        $this->tab; //catégorie du module, si rien va dans Autres
        $this->version = '1.0.0';//version
        $this->author = 'Jean-michel Dévelopeur';//auteur
        $this->need_instance = 1;//est-ce que le module a besoin d'une instance de la classe
        $this->bootstrap = true;//bootstrap activé ou non
        parent::__construct();

        $this->displayName = $this->trans('Module de parrainage', [],'Module.Zzmodulenico.Admin');//affichage du non et on traduit
        $this->description = $this->trans('Module permettant de generer un lien d\'inscription unique, qui donne au parain une reduction de panier si il est utilisé.', [],'Module.Zzmodulenico.Admin');//on récupère la descritpion et on la passe dans trans pour la traduction
        $this->confirmUninstall = 'Vous désinstallez mon super module. Êtes-vous sûr?';//on peut ne pas traduire

        $this->templateFile = 'module:zzmodulenico/template/views/front.tpl';//chemin pour aller chercher le template via le chemin
    }

    public function install(){
        return parent::install()
        && $this->registerHook('displayHome')
        && $this->registerHook('header')//permet de rajouter des fichiers js ou css
        && Configuration::updateValue('ZZMODULENICO_REDUC', 'Réduction')
        && Configuration::updateValue('ZZMODULENICO_MAXREDUC', 'Montant maximum éligible à la réduction');
    }

    public function uninstall(){
        return parent::uninstall()
        && $this->unregisterHook('displayHome');
    }

    public function hookHeader(){
        $this->context->controller->registerStylesheet(
            'module-zzmodulenico-style',
            'modules/'.$this->name.'/template/views/assets/main.css',
            [
                'media' => 'all',
                'priority' => 200
            ]
        );
    }

    public function getWidgetVariables($hookName, array $configuration){
        return [
            'reduc' => Configuration::get('ZZMODULENICO_REDUC'),
            'maxReduc' => Configuration::get('ZZMODULENICO_MAXREDUC'),
            'link' => Context::getContext()->link->getModuleLink('zzmodulenico','pagezz')
        ];
    }

    public function renderWidget($hookName, array $configuration){//Envoie les variables récupérées par getWidgetVariables dans le template
        $templateVars = $this->getWidgetVariables($hookName, $configuration);
        $this->smarty->assign($templateVars);
        return $this->fetch($this->templateFile);
    } 

    public function getContent(){ //son rôle est de rajouter un bouton configurer pour accéder à un formulaire pour paramétrer le module
        $output = $this->post_validate();
        return $output.$this->renderForm();
    }

    private function post_validate(){//Check de validation pour le formulaire
        $output = '';
        $errors = [];
        if(Tools::isSubmit('submitZZ')){//Check si le formulaire a été submit
            $reduc = Tools::getValue('zzreduc');//récupère la valeur du champ du formulaire, de manière générale la fonction va chercher la valeur par rapport à la clé en paramètre dans le $_GET ou $_POST
            $maxReduc = Tools::getValue('zzmaxreduc');
            if($reduc === ''){
                $errors[] = 'Le champs réduction est obligatoire';
            }
            if($maxReduc === ''){
                $errors[] = 'Le champs montant maximum éligible à la réduction est obligatoire';
            }
            if(count($errors) > 0){
                $output = $this->displayError(implode('<br>',$errors));//Affiche un message d'erreur
            }
            else{
                Configuration::updateValue('ZZMODULENICO_REDUC',$reduc);//l'envoie en BDD
                Configuration::updateValue('ZZMODULENICO_MAXREDUC',$maxReduc);
                $output = $this->displayConfirmation('Le formulaire est enregistré');//Affiche un message de confirmation
            }
        }
        return $output;
    }

    private function renderForm(){//Construction du formulaire de configuration
        $fields_form = [
            'form' =>[
                'legend' => [
                    'title' => $this->trans('Settings', [], 'Admin.Global'),
                    'icon' => 'icon.org'
                ],
                'description' => $this->trans('Display a title in front page',[], 'Modules.Zzmodulenico.Admin'),
                'input' => [
                    [
                        'type' => 'text',
                        'name' => 'zzreduc',
                        'label' => $this->trans('Reduction', [], 'Modules.Zzmodulenico.Admin'),
                        'required' => 1
                    ],
                    [
                        'type' => 'text',
                        'name' => 'zzmaxreduc',
                        'label' => $this->trans('Montant maximum éligible à la réduction', [], 'Modules.Zzmodulenico.Admin'),
                        'required' => 1
                    ],
                ],
                'submit' => [
                    'title' => $this->trans('Save',[],'Admin.Actions'),
                ]
            ]
        ];

        $lang = new Language((int) Configuration::get('PS_LANG_DEFAULT'));

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitZZ';//Important => donner un nom au submit_action
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false) . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = [
            'fields_value' => $this->getConfigFieldsValue(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        ];

        return $helper->generateForm([$fields_form]);
    }

    private function getConfigFieldsValue(){//Va chercher les valeurs de config sur la BDD
        return [
            'zzreduc' => Tools::getValue('zzreduc', Configuration::get('ZZMODULENICO_REDUC')),
            'zzmaxreduc' => Tools::getValue('zzmaxreduc', Configuration::get('ZZMODULENICO_MAXREDUC'))
        ];
    }
}