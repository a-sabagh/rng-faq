<?php

namespace FAQ;

defined('ABSPATH') || exit;

class Init {

    const first_flush_option = "faq_first_flush_permalinks";

    public $namespace;
    public $version;
    public $api_slug;
    public $web_slug;

    public function __construct($version, $web_slug, $api_slug) {
        $this->version = $version;
        $this->web_slug = $web_slug;
        $this->api_slug = $api_slug;
        $this->load_modules();

        add_action('admin_enqueue_scripts', array($this, "admin_enqueue_scripts"));
        add_action("init", array($this, "add_rewrite_rule"));
        add_action("admin_notices", array($this, "first_flush_notice"));
        add_action("update_option_permalink_structure", function() {
            update_option(self::first_flush_option, true);
        });
        add_action("template_redirect", array($this, "template_redirect"));
    }

    public function admin_enqueue_scripts() {
        wp_register_style("faq-select2-style", trailingslashit(FAQ_PDU) . "admin/assets/css/select2.css");
        wp_register_style("faq-admin-style", trailingslashit(FAQ_PDU) . "admin/assets/css/style.css");
        wp_register_script("faq-select2-script", trailingslashit(FAQ_PDU) . "admin/assets/js/select2.js",array('jquery'),'4.0.6');
        wp_register_script("faq-admin-script", trailingslashit(FAQ_PDU) . "admin/assets/js/script.js", array('jquery'), $this->version);
    }

    public function add_rewrite_rule() {
        add_rewrite_rule("^{$this->api_slug}/([^/]*)/?([^/]*)/?([^/]*)/?$", 'index.php?abp_module=$matches[1]&abp_action=$matches[2]&abp_params=$matches[3]', "top");
        add_rewrite_tag("%abp_module%", "([^/]*)");
        add_rewrite_tag("%abp_action%", "([^/]*)");
        add_rewrite_tag("%abp_params%", "([^/]*)");
    }

    public function first_flush_notice() {
        if (get_option(self::first_flush_option)) {
            return;
        }
        require_once trailingslashit(FAQ_ADM) . "notices/flush-rewrite.php";
    }

    public function template_redirect() {
        $module = get_query_var("abp_module");
        if (empty($module)) {
            return;
        }
        $action = get_query_var("abp_action");
        $params = get_query_var("abp_params");
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        $class = $this->namespace[$module] . $module;
        if (!class_exists($class)) {
            wp_send_json(['error' => "Class {$class} not exist"]);
            return;
        }
        $controller = new $class;
        if (!isset($action)) {
            $controller->index();
            return;
        }
        $controller->{$action}($params);
    }

    public function load_modules() {
        require_once trailingslashit(__DIR__) . 'Models/PostProduct.php';
        require_once trailingslashit(__DIR__) . 'Migrations/PostProductMigration.php';
        require_once trailingslashit(__DIR__) . 'Controllers/Web/PostController.php';
        require_once trailingslashit(__DIR__) . 'Controllers/Web/SettingController.php';
        require_once trailingslashit(__DIR__) . 'Controllers/Web/TermController.php';

        $this->namespace = array(
            "Post" => "FAQ\Controllers\Api\\",
        );
        require_once trailingslashit(__DIR__) . 'Controllers/Api/Post.php';
    }

}
