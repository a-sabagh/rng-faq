<?php

namespace FAQ\Models;

defined('ABSPATH') || exit;

class PostProduct {

    public $id;
    public $post_id;
    public $product_id;

    const format = array("%d", "%d");
    const table = "faq_post_product";

    public function __construct($args = array()) {
        settype($args, "array");
        foreach ($args as $key => $value) {
            $this->$key = $value;
        }
    }

    public static function create($args) {
        settype($args, "array");
        global $wpdb;
        $table = $wpdb->prefix . self::table;
        $data = array(
            "post_id" => $args['post_id'],
            "product_id" => $args['product_id'],
        );
        $format = self::format;
        $wpdb->insert($table, $data, $format);
        return $wpdb->insert_id;
    }

    public static function get($id) {
        global $wpdb;
        $table = $wpdb->prefix . self::table;
        $query = "SELECT * FROM {$table} WHERE id=%d LIMIT 1";
        $prepared_query = $wpdb->prepare($query, $id);
        $post_product_row = $wpdb->get_row($prepared_query, ARRAY_A);
        return new PostProduct($post_product_row);
    }

    public static function group_insert($posts_ids, $product_id) {
        global $wpdb;
        $table = $wpdb->prefix . self::table;
        $sql_insert = "INSERT INTO {$table} (post_id,product_id) VALUES ";
        $place_holders = $values = array();
        foreach ($posts_ids as $post_id) {
            $sql_insert .= "({$post_id},{$product_id}),";
        }
        
        $result = $wpdb->query(rtrim($sql_insert,','));
    }

    public static function group_delete($product_id) {
        global $wpdb;
        $table = $wpdb->prefix . self::table;
        $sql_delete = "DELETE FROM {$table} WHERE product_id = {$product_id}";
        $wpdb->query($sql_delete);
    }

    public static function get_by_post_id($post_id) {
        global $wpdb;
        $table = $wpdb->prefix . self::table;
        $query = "SELECT * FROM {$table} WHERE post_id=%d LIMIT 1";
        $prepared_query = $wpdb->prepare($query, $post_id);
        $post_product_row = $wpdb->get_row($prepared_query, ARRAY_A);
        return new PostProduct($post_product_row);
    }

    public static function get_posts_by_key($args) {
        settype($args, "array");
        global $wpdb;
        $posts = $wpdb->prefix . "posts";
        $post_product = $wpdb->prefix . self::table;
        $sql_search = "SELECT posts.ID , posts.post_title , posts.guid "
                . "FROM {$post_product} AS post_product "
                . "INNER JOIN {$posts} as posts "
                . "ON post_id = posts.ID "
                . "WHERE post_product.product_id = {$args['product_id']} "
                . "AND posts.post_title LIKE '%{$args['search_title']}%' AND posts.post_status = 'publish' ";
        if ($args['search_content']) {
            $sql_search .= "OR posts.post_content LIKE '%{$args['search_content']}%' ";
        }
        if (0 !== $args['posts_per_page']) {
            $sql_search .= "LIMIT {$args['posts_per_page']} OFFSET 0";
        }
        return $wpdb->get_results($sql_search, ARRAY_A);
    }
    
    public static function get_posts_by_product_id($product_id){
        global $wpdb;
        $posts = $wpdb->prefix . "posts";
        $post_product = $wpdb->prefix . self::table;
        $sql_all = "SELECT posts.ID , posts.post_title , posts.post_content , posts.guid "
                . "FROM {$post_product} AS post_product "
                . "INNER JOIN {$posts} as posts "
                . "ON post_id = posts.ID "
                . "WHERE post_product.product_id = {$product_id} ";
        return $wpdb->get_results($sql_all, ARRAY_A);
    }

    public function save() {
        global $wpdb;
        $table = $wpdb->prefix . self::table;
        $data = array(
            "post_id" => $this->post_id,
            "product_id" => $this->product_id,
        );
        $format = self::format;
        $wpdb->insert($table, $data, $format);
    }

    public function update() {
        global $wpdb;
        $table = $table = $wpdb->prefix . self::table;
        $data = array(
            "post_id" => $this->post_id,
            "product_id" => $this->product_id,
        );
        $where = array('id' => $this->id);
        $format = self::format;
        $where_format = array("%d");
        return $wpdb->update($table, $data, $where, $format, $where_format);
    }

    public function delete() {
        global $wpdb;
        $table = $table = $wpdb->prefix . self::table;
        $query = "DELETE FROM {$table} WHERE id=%d";
        $prepared_query = $wpdb->prepare($query, $this->id);
        return $wpdb->query($prepared_query);
    }

}
