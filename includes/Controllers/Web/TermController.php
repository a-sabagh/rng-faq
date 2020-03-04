<?php

namespace FAQ\Controllers\Web;

use FAQ\Models\PostProduct;
use FAQ\Controllers\Web\PostController;

defined('ABSPATH') || exit;

class TermController {

    public $post_controller;

    public function __construct() {
        $this->post_controller = new PostController;
        add_action('category_edit_form_fields', array($this, 'product_id_edit'), 50);
        add_action('edited_category', array($this, 'product_id_update'));
    }

    function product_id_edit($term) {
        $this->post_controller->enqueue_scripts();
        $product_id = get_term_meta($term->term_id, 'faq_product_id', TRUE);
        if(is_numeric($product_id)){
            $url = "https://storina.com/onlinerApi/Product/get/id={$product_id}";
            $response = wp_remote_get($url);
        }
        require_once trailingslashit(FAQ_ADM) . "metabox/category.php";
    }

    function product_id_update($term_id) {
        $product_id = $_POST['faq_product_id'];
        update_term_meta($term_id, 'faq_product_id', $_POST['faq_product_id']);
        PostProduct::group_delete($product_id);
        $post_ids = get_posts(array(
            'numberposts' => -1,
            'tax_query' => array(
                array(
                    'taxonomy' => 'category',
                    'field' => 'id',
                    'terms' => $term_id,
                ),
            ),
            'fields' => 'ids',
        ));
        PostProduct::group_insert($post_ids, $product_id);
    }

}

new TermController;
