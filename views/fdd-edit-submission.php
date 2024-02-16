<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Affiche et traite le formulaire d'édition de soumission pour l'utilisateur connecté.
 *
 * @return string Le formulaire HTML pour éditer la soumission ou un message d'erreur.
 */
function fdd_edit_user_submission()
{

    if (!is_user_logged_in()) {
        return 'Vous devez être connecté(e) pour modifier vos données.';
    }

    // Récupérez l'ID de la soumission à partir de l'URL
    $id = isset($_GET['edit']) ? intval($_GET['edit']) : 0;

    if (empty($id) || !is_numeric($id)) {
        return 'ID de soumission invalide.';
    }

    $current_user = wp_get_current_user();

    // Crée une instance du modèle et récupère la soumission correspondante à l'ID
    $model = new FDD_FormDataModel();
    $submission = $model->get_submission($id);

    // Vérifie si la soumission existe et appartient à l'utilisateur actuel
    if (!empty($submission) && $submission->email === $current_user->user_email) {

        // Instance du contrôleur
        $controller = new FDD_FormDataSubmissionController();

        // Appelle la méthode de mise à jour du contrôleur et récupère le message de retour
        $message = $controller->update_submission();

        // Construit le formulaire pré-rempli avec les données de la soumission
        $output = '<div class="fdd-form-message">' . esc_html($message) . '</div>';
        $output .= '<form method="post" action="">'; // Sur la même page
        // Sécurité pour la soumission du formulaire
        $output .= wp_nonce_field('update_submission_nonce', '_wpnonce', true, false);
        $output .= '<input type="hidden" name="action" value="update_user_submission">';
        $output .= '<input type="text" name="name" value="' . esc_attr($submission->name) . '">';
        $output .= '<input type="email" name="email" value="' . esc_attr($submission->email) . '">';
        $output .= '<input type="text" name="phone" value="' . esc_attr($submission->phone) . '">';
        $output .= '<input type="text" name="subject" value="' . esc_attr($submission->subject) . '">';
        $output .= '<textarea name="message">' . esc_textarea($submission->message) . '</textarea>';
        $output .= '<input type="submit" value="Mettre à jour">';
        $output .= '</form>';
    } else {
        return 'Les données ne sont pas disponibles.';
    }

    return $output;
}

add_shortcode('edit_user_submission', 'fdd_edit_user_submission');
