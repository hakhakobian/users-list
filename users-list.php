<?php
/**
 * Plugin Name: Users list
 * Description: Show WordPress users in separate admin page with sort functionality.
 * Version: 1.0.0
 * Requires at least: 4.6
 * Requires PHP: 7.0
 * Author: Hakob Hakobyan
 * License: http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: hh_users_list
 */

final class HHUsersList {
  protected static $_instance = null;
  public string $prefix = "hhul";
  public string $nicename = "";
  public string $version = "1.0.0";
  public string $plugin_url = '';
  public string $plugin_dir = '';
  public $nonce = 'hhul_nonce';

  public $options;

  /**
   * Ensures only one instance is loaded or can be loaded.
   *
   * @return  self|null
   */
  public static function instance() {
    if ( is_null( self::$_instance ) ) {
      self::$_instance = new self();
    }
    return self::$_instance;
  }

  public function __construct() {
    $this->nicename = __("Users list", "hhul");
    $this->plugin_url = plugins_url(plugin_basename(dirname(__FILE__)));
    $this->plugin_dir = WP_PLUGIN_DIR . "/" . plugin_basename(dirname(__FILE__));

    add_action('admin_enqueue_scripts', array($this, 'register_admin_scripts'));

    require_once($this->plugin_dir . '/includes/admin_page.php');
    new HHUsersList_ADMIN_PAGE($this);
  }

  /**
   * Register styles to enqueue later.
   *
   * @return void
   */
  public function register_admin_scripts() {
    wp_register_style($this->prefix . '_admin', $this->plugin_url . '/assets/css/admin.css', [], $this->version);
    wp_register_script($this->prefix . '_admin', $this->plugin_url . '/assets/js/admin.js', ['jquery'], $this->version);
  }
}

/**
 * Main instance of HHUsersList.
 *
 * @return HHUsersList The main instance to prevent the need to use globals.
 */
function HHUsersList() {
  return HHUsersList::instance();
}

HHUsersList();
