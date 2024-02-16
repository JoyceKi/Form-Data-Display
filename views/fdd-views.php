<?php
if(!defined('ABSPATH')){
    exit;
}

// Affiche les soummissions pour les utilisateurs connectés

function fdd_display_user_submissions() {

    if (!is_user_logged_in()) {
        return 'Connectez-vous pour voir vos messages';
    }

    // Récupère l'utilisateur actuel et son email
    $current_user = wp_get_current_user();
    $current_user_email = $current_user->user_email;

    // Instantiation de FormDataModel pour récupérer les données 
    $model = new FDD_FormDataModel();
    $submissions = $model->get_user_submissions($current_user_email);

    if (!empty($submissions)) {
        $output = '<ul class="user_submissions_list">';
        foreach ($submissions as $submission) { 
            $output .= '<li>';
            $output .= '<strong>Nom : </strong>' . esc_html($submission->name) . '<br>';
            $output .= '<strong>Email : </strong>' . esc_html($submission->email) . '<br>';
            $output .= '<strong>Téléphone : </strong>' . esc_html($submission->phone) . '<br>';
            $output .= '<strong>Sujet : </strong>' . esc_html($submission->subject) . '<br>';
            $output .= '<strong>Message : </strong>' . esc_html($submission->message) . '<br>';
            $output .= '<a href="' . esc_url(add_query_arg('edit', $submission->id, get_permalink())) . '" class="edit-submission-button">Modifier</a>';
            $output .= '</li><br>';
        }
        $output .= '</ul>';
    } else {
        $output = 'Aucun message envoyé.';
    }

    return $output;
}

add_shortcode('display_user_submissions', 'fdd_display_user_submissions');