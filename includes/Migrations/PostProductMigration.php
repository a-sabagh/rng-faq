<?php

namespace FAQ\Migrations;

defined('ABSPATH') || exit;

class PostProductMigration {

    public $wpdb;

    const table = "faq_post_product";

    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
        register_activation_hook(FAQ_FILE, array($this, "up"));
    }

    public function up() {
        require_once (trailingslashit(ABSPATH) . 'wp-admin/includes/upgrade.php');
        $post_product = $this->wpdb->prefix . self::table;
        $post = $this->wpdb->prefix . "posts";
        $sql_post_product = "CREATE TABLE IF NOT EXISTS {$post_product} ( "
                . "id BIGINT(20) NOT NULL AUTO_INCREMENT, "
                . "post_id BIGINT(20) UNSIGNED NOT NULL DEFAULT '0', "
                . "product_id BIGINT(20) UNSIGNED NOT NULL DEFAULT '0', "
                . "PRIMARY KEY (id) ,"
                . "FOREIGN KEY (post_id) REFERENCES {$post} (ID) ON DELETE CASCADE ON UPDATE CASCADE "
                . ")"
                . "CHARACTER SET utf8 "
                . "COLLATE utf8_general_ci";
        dbDelta($sql_post_product);
    }

    public function down() {
        
    }

}

new PostProductMigration;
