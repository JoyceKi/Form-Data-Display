<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Gère la mise à jour des soumissions de formulaires par les utilisateurs.
 */
class FDD_FormDataSubmissionController
{
    /**
     * vérifie les données POST, les nettoie et met à jour la soumission
     * dans la base de données en utilisant le modèle FDD_FormDataModel.
     *
     * @return string Un message indiquant le résultat de la mise à jour.
     */
    public function update_submission()
    {
        $message = ""; // initialise le message de retour

        if (isset($_POST['action']) && $_POST['action'] === 'update_user_submission') {

            // Vérifie le nonce pour la sécurité
            check_admin_referer('update_submission_nonce');

            // Récupère et nettoie les données POST, avec entre autres la méthode statique sanitize_phone 
            $id = isset($_POST['submission_id']) ? intval($_POST['submission_id']) : 0;
            $name = isset($_POST['name']) ? FDD_FormDataController::sanitize_phone($_POST['name']) : '';
            $email = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
            $phone = isset($_POST['phone']) ? sanitize_text_field($_POST['phone']) : ''; //facultatif
            $subject = isset($_POST['subject']) ? sanitize_text_field($_POST['subject']) : '';
            $message = isset($_POST['message']) ? sanitize_textarea_field($_POST['message']) : '';

            // Vérifie que toutes les variables nécessaires sont définies 
            if ($id && $name && $email && $subject && $message) {
                $model = new FDD_FormDataModel();
                $success = $model->update_submission($id, $name, $email, $phone, $subject, $message);

                if ($success) {

                    $message = 'Votre soumission a été mise à jour avec succès.';
                } else {

                    $message = 'Une erreur est survenue lors de la mise à jour de votre soumission.';
                }
            } else {

                $message = 'Veuillez vous assurer que tous les champs obligatoires sont remplis correctement.';
            }
        }
        return $message;
    }
}
