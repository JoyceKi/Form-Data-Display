<?php
if(!defined('ABSPATH')){
    exit;
}

class FormDataController {
    protected $model;

    public function __construct() {
        $this->model = new FormDataModel();
    }

    // fonction qui "nettoie" les n° de téléphone (n'existe pas dans wp)
    public function sanitize_phone($phone) {
        $sanitized_phone = preg_replace('/[^0-9+()\-.\s]/', '', $phone);
        return $sanitized_phone;
    }

    // Récupère les données du formulaire de contact et les enregistre grâce à FormDataModel
    public function capture_form_submission($contact_form) {
        $submission = WPCF7_Submission::get_instance();
        if ($submission) {
            $posted_data = $submission->get_posted_data();

            // Vérifie que les champs requis sont bien présents
            if (isset($posted_data['your-name'], $posted_data['your-email'], $posted_data['your-message'])) {
                // Les champs optionnels
                $phone = isset($posted_data['tel-6']) ? $this->sanitize_phone($posted_data['tel-6']) : '';
                $subject = isset($posted_data['your-subject']) ? sanitize_text_field($posted_data['your-subject']) : '';

                $data = array(
                    'name'    => sanitize_text_field($posted_data['your-name']),
                    'email'   => sanitize_email($posted_data['your-email']),
                    'phone'   => $phone,
                    'subject' => $subject,
                    'message' => sanitize_textarea_field($posted_data['your-message']),
                );

                $post_id = $this->model->save_submission($data);

                // Gestion des erreurs en interne pour ne pas interférer avec Contact Form 7
                if (is_wp_error($post_id)) {
                    error_log('Erreur lors de l\'enregistrement de la soumission : ' . $post_id->get_error_message());
                }
            }
        }
    }

    public function edit_submission() {
        $submission_id = isset($_GET['edit']) ? intval($_GET['edit']) : 0;
        $submission = $this->model->get_submission($submission_id);
        
        if (!$submission) {
            
            return;
        }

        if (!current_user_can('edit_submission', $submission_id)) {
            return;
        }

        include('views/edit-submission.php');
    }

    /**
     *  public function update_submission() {
     *    vérifier nonce et permissions et màj
     * }  
    */
}
