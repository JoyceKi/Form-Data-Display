<?php
if(!defined('ABSPATH')){
    exit;
}

/**
 * Modèle pour la gestion des soumissions de formulaires.
 *
 * Cette classe fournit des méthodes pour interagir avec la table des soumissions
 * de formulaires dans la base de données WordPress.
 */
class FDD_FormDataModel {
    private $wpdb;
    private $table_name;

    /**
     * Initialise les propriétés de la classe et définit le nom de la table des soumissions.
     */
    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->table_name = $this->wpdb->prefix . 'form_submissions';
    }

     /**
     * Crée la table des soumissions lors de l'activation du plugin.
     */
    public function create_submissions_table() {
        $charset_collate = $this->wpdb->get_charset_collate();

        $sql = "CREATE TABLE $this->table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
            name tinytext NOT NULL,
            email text NOT NULL,
            phone text,
            subject text,
            message text,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    /**
     * Enregistre une nouvelle soumission dans la base de données.
     *
     * @param array $data Les données de la soumission à enregistrer.
     * @return int|WP_Error L'ID de la soumission enregistrée ou un objet WP_Error en cas d'erreur.
     */
    public function save_submission($data) {
        $result = $this->wpdb->insert($this->table_name, array(
            'time' => current_time('mysql'),
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'subject' => $data['subject'],
            'message' => $data['message'],
        ), array(
            '%s',
            '%s',
            '%s',
            '%s',
            '%s',
            '%s'
        ));

        if ($result === false) {
            return new WP_Error('db_insert_error', 'Could not insert submission into the database', $this->wpdb->last_error);
        } else {
            return $this->wpdb->insert_id;
        }
    }

    /**
     * Récupère les soumissions de l'utilisateur actuel par son email.
     *
     * @param string $user_email L'email de l'utilisateur.
     * @return array Un tableau d'objets représentant les soumissions de l'utilisateur.
     */
    public function get_user_submissions($user_email) {
        $query = $this->wpdb->prepare("SELECT * FROM $this->table_name WHERE email = %s", $user_email);
        return $this->wpdb->get_results($query);
    }

     /**
     * Récupère une soumission par son ID.
     *
     * @param int $id L'ID de la soumission à récupérer.
     * @return object|NULL L'objet de la soumission ou NULL si non trouvé.
     */
    public function get_submission($id) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'form_submissions';
        $query = $wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id);
        return $wpdb->get_row($query);
    }

    /**
     * Met à jour une soumission existante dans la base de données.
     *
     * @param int $id L'ID de la soumission à mettre à jour.
     * @param string $name Le nouveau nom.
     * @param string $email Le nouvel email.
     * @param string $phone Le nouveau numéro de téléphone.
     * @param string $subject Le nouveau sujet.
     * @param string $message Le nouveau message.
     * @return int|false Le nombre de lignes mises à jour ou false en cas d'erreur.
     */
    public function update_submission($id, $name, $email, $phone, $subject, $message) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'form_submissions';
    
        // Prépare et exécute la requête de mise à jour
        $updated = $wpdb->update(
            $table_name,
            array(
                'name' => $name,
                'email' => $email,
                'phone' => $phone,
                'subject' => $subject,
                'message' => $message
            ),
            array('id' => $id),
            array(
                '%s', // format de $name
                '%s', // format de $email
                '%s', // format de $phone
                '%s', // format de $subject
                '%s'  // format de $message
            ),
            array('%d') // format de $id
        );
    
        return $updated;
    }

    /**
     * Supprime la table des soumissions lors de la désinstallation du plugin.
     */
    public static function drop_submissions_table() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'form_submissions';
        $wpdb->query("DROP TABLE IF EXISTS $table_name");
    }
}

?>
