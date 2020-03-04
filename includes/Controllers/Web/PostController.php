<?php

namespace FAQ\Controllers\Web;

use FAQ\Models\PostProduct;

defined('ABSPATH') || exit;

class PostController {

    const search_action = "storina_product";

    public function __construct() {
        add_action('save_post', array($this, 'save_post'));
        add_action('add_meta_boxes', array($this, 'metabox_init'));
    }

    public function save_post($post_id) {
        $is_autosave = wp_is_post_autosave($post_id);
        $is_revision = wp_is_post_revision($post_id);
        $is_valid_nonce = (isset($_POST['faq_postmeta_nonce']) && wp_verify_nonce($_POST['faq_postmeta_nonce'], basename(__FILE__))) ? TRUE : FALSE;
        if ($is_autosave || $is_revision || !$is_valid_nonce) {
            return;
        }
        $post_product = PostProduct::get_by_post_id($post_id);
        if (is_numeric($post_product->product_id)) {
            $post_product->product_id = $_POST['faq_product_id'];
            $post_product->update();
        } else {
            PostProduct::create(array("post_id" => $post_id, "product_id" => $_POST['faq_product_id']));
        }
    }

    public function metabox_init() {
        add_meta_box("product-id", "محصول مرتبط", array($this, "metabox_input"), "post", "side", "high");
    }

    public function metabox_input($post) {
        $post_id = $post->ID;
        $this->enqueue_scripts();
        wp_nonce_field(basename(__FILE__), 'faq_postmeta_nonce');
        $post_product = PostProduct::get_by_post_id($post_id);
        if (is_numeric($post_product->product_id)) {
            $product_id = $post_product->product_id;
            $url = "https://storina.com/onlinerApi/Product/get/id={$product_id}";
            $response = wp_remote_get($url);
        }
        require_once trailingslashit(FAQ_ADM) . "metabox/post.php";
    }

    public function enqueue_scripts() {
        wp_enqueue_style("faq-select2-style");
        wp_enqueue_style("faq-admin-style");
        wp_enqueue_script("faq-select2-script");
        wp_enqueue_script("faq-admin-script");
        $data = array(
            "ajaxUrl" => "https://storina.com/onlinerApi/Product/search/",
        );
        wp_localize_script("faq-admin-script", "FAQ_Object", $data);
    }

}
