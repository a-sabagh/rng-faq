<?php

/*
 * Plugin Name: FAQ
 * Description: Frequently Question Answer
 * Version: 1.0
 * Author: Abolfazl Sabagh
 * Author URI: http://asabagh.ir
 */

if (!defined('ABSPATH')) {
    exit;
}

define("FAQ_FILE", __FILE__);
define("FAQ_PRU", plugin_basename(__FILE__));
define("FAQ_PDU", plugin_dir_url(__FILE__));
define("FAQ_PRT", basename(__DIR__));
define("FAQ_PDP", plugin_dir_path(__FILE__));
define("FAQ_TMP", FAQ_PDP . "public/");
define("FAQ_ADM", FAQ_PDP . "admin/");

require_once trailingslashit(__DIR__) . "includes/Init.php";
$init = new FAQ\Init(1.0, 'rng-faq', 'FAQApi');
