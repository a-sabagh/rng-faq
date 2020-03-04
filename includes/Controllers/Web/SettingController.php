<?php

namespace FAQ\Controllers\Web;

defined('ABSPATH') || exit;

class SettingController {

    /**
     * @var Array settings array include legal post types and email
     */
    public $settings;
    public $search_resource;

    public function __construct() {
        $this->search_resource = array('title', 'content');
        $this->settings = self::get_settings();
        if (!is_admin()) {
            return;
        }
        add_action("admin_menu", array($this, "admin_menu"));
        add_action("admin_init", array($this, "general_settings_init"));
        add_action("admin_notices", array($this, "configure_notices"));
        add_action("admin_init", array($this, "dismiss_configuration"));
        add_filter('plugin_action_links_' . FAQ_PRU, array($this, 'add_setting_link'));
    }

    /**
     * set plugin settings in settings attribute
     * @return Array
     */
    public static function get_settings() {
        $faq_settings_array = array(
            'sr' => array('title'),
            'pp' => 10,
        );

        $faq_settings = get_option("faq_options");
        if (empty($faq_settings)) {
            return $faq_settings_array;
        }

        $faq_settings_array['sr'] = isset($faq_settings['sr']) ? (array) array_merge($faq_settings['sr'], array('title')) : array('title');
        $faq_settings_array['pp'] = (int) $faq_settings['pp'];
        return $faq_settings_array;
    }

    /**
     * adding general setting of postviews plugin to admin menu
     */
    public function admin_menu() {
        add_submenu_page("options-general.php", esc_html__("وب سرویس پرسش و پاسخ"), esc_html__("پرسش و پاسخ"), "administrator", "faq-settings", array($this, "faq_settings"));
    }

    /**
     * output of setting page for postviews options
     */
    public function faq_settings() {
        include FAQ_ADM . "settings-panel.php";
    }

    /**
     * register setting and section and fields
     */
    public function general_settings_init() {
        register_setting("faq-settings", "faq_options");
        add_settings_section("faq-section-top", esc_html__("تنظیمات جستجو"), array($this, "general_setting_section_top"), "faq-settings");
        add_settings_field("faq-sr", esc_html__("منابع جستجو"), array($this, "general_setting_search_resource"), "faq-settings", "faq-section-top", array("id" => "faq-sr", "name" => "sr"));
        add_settings_field("faq-pp", esc_html__("تعداد پرسش در هر درخواست"), array($this, "general_setting_per_page"), "faq-settings", "faq-section-top", array("id" => "faq-pp", "name" => "pp"));
        add_settings_field("faq-doc", esc_html__("مستندات وب سرویس"), array($this, "general_setting_docs"), "faq-settings", "faq-section-top", array("id" => "faq-doc"));
    }

    /**
     * output of setting field faq-mail
     * @param type $args
     */
    public function general_setting_per_page($args) {
        $settings = (array) $this->settings;
        $mail = (int) $settings['pp'];
        ?>
        <input type="number" id="<?php echo $args['id']; ?>" name="faq_options[<?php echo $args['name']; ?>]" value="<?php echo $mail; ?>">
        <?php
    }

    /**
     * output of setting section faq-section-top
     */
    public function general_setting_section_top() {
        esc_html_e("تنظیمات وب سرویس پرسش و پاسخ استورینا");
    }

    /**
     * output of setting field faq-pt
     * @param Array $args
     */
    public function general_setting_search_resource($args) {
        $settings = $this->settings;
        $active_resource = $settings['sr'];
        $resources = $this->search_resource;
        foreach ($resources as $resource):
            $checked = '';
            if (is_array($active_resource)) {
                $checked = (in_array($resource, $active_resource)) ? 'checked="checked" ' : "";
            }
            $disabled = ($resource == "title") ? "disabled" : "";
            ?>
            <label>
                <?php echo $resource ?>&nbsp;<input id="<?php echo $args['id']; ?>" type="checkbox" name="faq_options[<?php echo $args['name']; ?>][]" <?php echo $checked; ?> value="<?php echo $resource; ?>" <?php echo $disabled; ?> >
            </label>
            <br>
            <?php
        endforeach;
    }

    public function general_setting_docs() {
        require_once trailingslashit(FAQ_ADM) . "webservice-docs.php";
    }

    /**
     * display configuration notice to admin notice after active plugin
     */
    public function configure_notices() {
        $dismiss = get_option("faq_configration_dissmiss");
        if ($dismiss) {
            return;
        }
        $notice = '<div class="updated"><p>' . esc_html__('وب سرویس پرسش و پاسخ روی وب سایت شما نصب شده است. برای شروع تنظیمات آن را انجام دهید.') . ' <a href="' . admin_url('admin.php?page=faq-settings') . '">' . esc_html__('رفتن به تنظیمات') . '</a> &ndash; <a href="' . add_query_arg(array('faq_dismiss_notice' => 'true', 'faq_nonce' => wp_create_nonce("faq_dismiss_nonce"))) . '">' . esc_html__('بی خیالش') . '</a></p></div>';
        echo $notice;
    }

    /**
     * check if configation dismiss or not
     * @param String $verify_nonce
     * @param String $dismiss_notice
     * @param String $dismiss
     * @param String $page
     * @return boolean
     */
    public function check_dismiss_configuration($verify_nonce, $dismiss_notice, $dismiss, $page) {
        if ((isset($verify_nonce, $dismiss, $dismiss_notice) and $dismiss == "true") or $page == "faq-settings") {
            return true;
        }
        return false;
    }

    /**
     * dismiss configuration notice
     */
    public function dismiss_configuration() {
        $verify_nonce = (isset($_GET['faq_nonce'])) ? wp_verify_nonce($_GET['faq_nonce'], 'faq_dismiss_nonce') : false;
        $dismiss_notice = (isset($_GET['faq_dismiss_notice'])) ? $_GET['faq_dismiss_notice'] : false;
        $dismiss = (isset($_GET['faq_dismiss'])) ? $_GET['faq_dismiss'] : false;
        $page = (isset($_GET['page'])) ? $_GET['page'] : false;
        if ($this->check_dismiss_configuration($verify_nonce, $dismiss_notice, $dismiss, $page)) {
            update_option("faq_configration_dissmiss", 1);
        }
    }

    /**
     * adding setting link to rng-postviewes in plugin list
     * @param Array $links
     * @return Array
     */
    public function add_setting_link($links) {
        $mylinks = array(
            '<a href="' . admin_url('options-general.php?page=faq-settings') . '">' . esc_html__("تنظیمات") . '</a>',
        );
        return array_merge($links, $mylinks);
    }

}

new SettingController;
