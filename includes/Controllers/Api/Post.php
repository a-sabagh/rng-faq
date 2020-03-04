<?php

namespace FAQ\Controllers\Api;

defined('ABSPATH') || exit;

use FAQ\Models\PostProduct;
use FAQ\Controllers\Web\SettingController;

defined('ABSPATH') || exit;

class Post {

    public $settings;

    public function __construct() {
        $this->settings = SettingController::get_settings();
    }

    public function search() {
        $args['product_id'] = (int) (isset($_POST['product_id'])) ? $_POST['product_id'] : -1;
        $args['posts_per_page'] = (int) (!empty($this->settings['pp'])) ? $this->settings['pp'] : 0;
        $args['search_title'] = (isset($_POST['s'])) ? sanitize_text_field($_POST['s']) : -1;
        if (in_array(-1, $args)) {
            wp_send_json(array('status' => false, 'message' => 'All Params is required'));
            return;
        }
        if (in_array('content', $this->settings['sr'])) {
            $args['search_content'] = sanitize_text_field($_POST['s']);
        } else {
            $args['search_content'] = false;
        }
        $posts = PostProduct::get_posts_by_key($args);
        wp_send_json($posts);
    }

    public function all() {
        $product_id = (int) $_POST['product_id'];
        $posts = PostProduct::get_posts_by_product_id($product_id);
        wp_send_json($posts);
    }

}
