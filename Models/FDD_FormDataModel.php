<?php
if(!defined('ABSPATH')){
    exit;
}

class FDD_FormDataModel {
    private $wpdb;
    private $table_name;

    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->table_name = $this->wpdb->prefix . 'form_submissions';
    }

    // Crée la table personnalisée lors de l'activation du plugin
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

    // Enregistre une soumission dans la table personnalisée
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

    // Récupère les soumissions de l'utilisateur actuel
    public function get_user_submissions($user_email) {
        $query = $this->wpdb->prepare("SELECT * FROM $this->table_name WHERE email = %s", $user_email);
        return $this->wpdb->get_results($query);
    }

    public function get_submission($id) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'form_submissions';
        $query = $wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id);
        return $wpdb->get_row($query);
    }

    public function update_submission($id, $data) {
        // màj de la soumission dans bdd
    }

    // Méthode de désinstallation
    public static function drop_submissions_table() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'form_submissions';
        $wpdb->query("DROP TABLE IF EXISTS $table_name");
    }
}

?>
