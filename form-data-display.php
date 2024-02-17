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

require_once(plugin_dir_path(__FILE__) . 'Models/FDD_FormDataModel.php');
require_once(plugin_dir_path(__FILE__) . 'views/fdd-views.php');
require_once(plugin_dir_path(__FILE__) . 'views/fdd-edit-submission.php');
require_once(plugin_dir_path(__FILE__) . 'Controllers/FDD_FormDataController.php');
require_once(plugin_dir_path(__FILE__) . 'Controllers/FDD_FormDataSubmissionController.php');

/**
 * Classe principale du plugin Form Data Display
 */
class FDD_FormDataDisplay
{
    /**
     * @var FDD_FormDataModel Instance du modèle de données
     */
    private $model;

    /**
     * @var FDD_FormDataController Instance du contrôleur
     */
    private $controller;

    /**
     * @var FDD_formDataSubmissionController Instance du contrôleur
     */
    private $submission_controller;

    public function __construct() {
        $this->model = new FDD_FormDataModel();
        $this->controller = new FDD_FormDataController();
        $this->submission_controller = new FDD_FormDataSubmissionController();

        // Hook pour récupérer la soumission du formulaire de contact
        add_action('wpcf7_before_send_mail', array($this->controller, 'capture_form_submission'));
        
        // Hook pour modifier la soumission de l'utilisateur connecté
        add_action('admin_post_update_user_submission', array($this->submission_controller, 'update_submission'));

        // Hook pour vérifier la redirection vers le formulaire de modification
        add_action('template_redirect', array($this, 'check_edit_submission_redirect'));
    }

    public function activate() {
        $this->model->create_submissions_table();
        flush_rewrite_rules();
    }

    public function check_edit_submission_redirect() {
        if (isset($_GET['edit'])) {
            $edit_id = intval($_GET['edit']);
            wp_redirect(home_url('/modifications/?edit=' . $edit_id));
            exit;
        }
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

    FDD_FormDataModel::drop_submissions_table();    
}
    
// Vérifie si la classe existe avant de l'instancier et d'enregistrer les hooks
if (class_exists('FDD_FormDataDisplay')) {
    
    $formDataDisplay = new FDD_FormDataDisplay();

    // activation
    register_activation_hook(__FILE__, array($formDataDisplay, 'activate'));

    // désactivation
    register_deactivation_hook(__FILE__, array($formDataDisplay, 'deactivate'));

    // désinstallation
    register_uninstall_hook(__FILE__, 'form_data_display_uninstall');

}
