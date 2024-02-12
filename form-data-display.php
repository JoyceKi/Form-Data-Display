<?php
/**
* Plugin Name: Form Data Display
* Description: Enregistre les soumissions de Contact Form 7 sous forme de Custom Post Type et les affiche aux utilisateurs.
* Author: Joyce KIM
* Version: 1.0.0
*/

if(!defined('ABSPATH')){
    exit;
}

require_once(plugin_dir_path(__FILE__) . 'Models/FormDataModel.php');
require_once(plugin_dir_path(__FILE__) . 'views/fdd-views.php');
require_once(plugin_dir_path(__FILE__) . 'Controllers/FormDataController.php');

/**
 * Classe principale du plugin Form Data Display
 */
class FormDataDisplay
{
    /**
     * @var FormDataModel Instance du modèle de données
     */
    private $model;

    /**
     * @var FormDataController Instance du contrôleur
     */
    private $controller;

    public function __construct() {
        $this->model = new FormDataModel();
        $this->controller = new FormDataController();

        // Hook
        add_action('wpcf7_before_send_mail', array($this->controller, 'capture_form_submission'));
    }

    public function activate() {
        $this->model->create_submissions_table();
        flush_rewrite_rules();
    }

    public function deactivate() {
        flush_rewrite_rules();
    }

}    

// Fonction indépendante pour la désinstallation
function form_data_display_uninstall() {
    if (!current_user_can('activate_plugins')) {
        return;
    }

    if (!defined('WP_UNINSTALL_PLUGIN')) {
        exit;
    }

    FormDataModel::drop_submissions_table();    
}
    
// Vérifie si la classe existe avant de l'instancier et d'enregistrer les hooks
if (class_exists('FormDataDisplay')) {
    
    $formDataDisplay = new FormDataDisplay();

    // activation
    register_activation_hook(__FILE__, array($formDataDisplay, 'activate'));

    // désactivation
    register_deactivation_hook(__FILE__, array($formDataDisplay, 'deactivate'));

    // désinstallation
    register_uninstall_hook(__FILE__, 'form_data_display_uninstall');

}
