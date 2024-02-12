<?php

if (!defined('ABSPATH')) {
    exit;
}

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

    $model = new FormDataModel();
    $submission = $model->get_submission($id);

    if (!empty($submission)) {

        if ($submission->user_email !== $current_user->user_email) {
            return 'Vous n\'avez pas la permission de modifier les données.';
        }

        $output = '<form method="post" action="' . esc_url(admin_url('admin-post.php')) . '">';
        $output .= wp_nonce_field('update_submission_nonce', '_wpnonce', true, false);
        $output .= '<input type="hidden" name="action" value="update_user_submission">';
        $output .= '<input type="submit" value="Mettre à jour">';
        $output .= '</form>';
    } else {
        return 'Les données ne sont pas disponibles.';
    }

    return $output;
}

add_shortcode('edit_user_submission', 'fdd_edit_user_submission');
