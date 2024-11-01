<?php

/**
 * Plugin Name: WooCommerce Express
 * Description: Simplifies the checkout process to improve your sales rate.
 * Version: 1.0.2
 * Author: QuadLayers
 * Author URI: https://quadlayers.com
 * Copyright:   2018 QuadLayers (https://quadlayers.com)
 * Text Domain: qlwce
 */
if (!defined('ABSPATH')) {
  die('-1');
}
if (!defined('QLWCE_PLUGIN_VERSION')) {
  define('QLWCE_PLUGIN_VERSION', '1.0');
}
if (!defined('QLWCE_PLUGIN_FILE')) {
  define('QLWCE_PLUGIN_FILE', __FILE__);
}
if (!defined('QLWCE_PLUGIN_DIR')) {
  define('QLWCE_PLUGIN_DIR', __DIR__ . DIRECTORY_SEPARATOR);
}

if (!class_exists('QLWCE')) {

  class QLWCE {

    protected static $instance;

    public static function instance() {
      if (!isset(self::$instance)) {
        self::$instance = new self();
        self::$instance->includes();
        self::$instance->premium();
        self::$instance->init();
      }
      return self::$instance;
    }

    function add_sections($sections = []) {

      global $current_section;

      $sections = apply_filters('woocommerce_get_sections_qlwce', [
          '' => __('General', 'qlwce')
      ]);

      echo '<ul class="subsubsub">';

      $array_keys = array_keys($sections);

      foreach ($sections as $id => $label) {
        echo '<li><a href="' . admin_url('admin.php?page=wc-settings&tab=qlwce&section=' . sanitize_title($id)) . '" class="' . ( $current_section == $id ? 'current' : '' ) . '">' . $label . '</a> ' . ( end($array_keys) == $id ? '' : '|' ) . ' </li>';
      }

      echo '</ul><br class="clear" />';
    }

    function add_tab($settings_tabs) {
      $settings_tabs['qlwce'] = __('Express', 'qlwce');
      return $settings_tabs;
    }

    function add_settings() {
      woocommerce_admin_fields($this->get_settings());
    }

    function save_settings() {
      woocommerce_update_options($this->get_settings());
    }

    function get_settings() {

      $fields = apply_filters('qlwce_add_fields', [
          'qlwce_section_title' => array(
              'name' => __('General', 'qlwce'),
              'type' => 'title',
              'desc' => __('Simplifies the checkout process.', 'qlwce'),
              'id' => 'qlwce_section_title'
          ),
          'add_to_cart_message' => array(
              'name' => __('Added to cart alert', 'qlwce'),
              'desc_tip' => __('Replace "View Cart" alert with direct checkout.', 'qlwce'),
              'id' => 'qlwce_add_to_cart_message',
              'type' => 'select',
              'class' => 'chosen_select',
              'options' => array(
                  'yes' => __('Yes', 'qlwce'),
                  'no' => __('No', 'qlwce'),
              ),
              'default' => 'no',
          ),
          'add_to_cart_link' => array(
              'name' => __('Added to cart link', 'qlwce'),
              'desc_tip' => __('Replace "View Cart" link with direct checkout.', 'qlwce'),
              'id' => 'qlwce_add_to_cart_link',
              'type' => 'select',
              'class' => 'chosen_select',
              'options' => array(
                  'yes' => __('Yes', 'qlwce'),
                  'no' => __('No', 'qlwce'),
              ),
              'default' => 'no',
          ),
          'add_to_cart_redirect' => array(
              'name' => __('Added to cart redirect', 'qlwce'),
              'desc_tip' => __('Redirect to the checkout page after successful addition.', 'qlwce'),
              'id' => 'qlwce_add_to_cart_redirect',
              'type' => 'select',
              'class' => 'chosen_select',
              'options' => array(
                  'yes' => __('Yes', 'qlwce'),
                  'no' => __('No', 'qlwce'),
              ),
              'default' => 'no',
          ),
          'qlwce_section_end' => array(
              'type' => 'sectionend',
              'id' => 'qlwce_section_end'
          )
      ]);

      return $fields;
    }

    function premium() {
      if (is_file(QLWCE_PLUGIN_DIR . 'inc/premium.php')) {
        require_once( 'inc/premium.php');
      }
    }

    function includes() {
      require_once( 'inc/archives.php');
      require_once( 'inc/products.php');
      require_once( 'inc/checkout.php');
    }

    function init() {
      add_action('plugins_loaded', [$this, 'i18n']);
      add_action('wp_enqueue_scripts', array($this, 'js'));
      add_filter('woocommerce_settings_tabs_array', [$this, 'add_tab'], 50);
      add_filter('woocommerce_sections_qlwce', [$this, 'add_sections']);
      add_filter('woocommerce_sections_qlwce', [$this, 'add_script']);
      add_action('woocommerce_settings_tabs_qlwce', [$this, 'add_settings']);
      add_action('woocommerce_settings_save_qlwce', array($this, 'save_settings'));
      add_filter('wc_add_to_cart_message_html', [$this, 'add_to_cart_message'], 10, 3);
      add_filter('option_woocommerce_cart_redirect_after_add', [$this, 'woocommerce_cart_redirect_after_add'], 10, 3);
      add_filter('woocommerce_add_to_cart_redirect', array($this, 'add_to_cart_redirect'), 99);
      add_filter('woocommerce_get_script_data', [$this, 'add_to_cart_params']);
    }

    function i18n() {
      load_plugin_textdomain('qlwce', false, dirname(plugin_basename(__FILE__)) . '/languages/');
    }

    function js() {
      if (function_exists('is_woocommerce') && (is_woocommerce() || is_checkout())) {

        wp_enqueue_script('woocommerce-express', plugin_dir_url(QLWCE_PLUGIN_FILE) . 'assets/woocommerce-express.js', array('jquery', 'wc-add-to-cart'), QLWCE_PLUGIN_VERSION, true);

        wp_localize_script('woocommerce-express', 'qlwce', array(
            'nonce' => wp_create_nonce('qlwce'),
            'add_product_ajax' => get_option('qlwce_add_product_ajax'),
            'add_product_ajax_message' => get_option('qlwce_add_product_ajax_message')
                )
        );
      }
    }

    function add_to_cart_message($message, $products, $show_qty) {

      if ('yes' === get_option('qlwce_add_to_cart_message')) {

        $message = str_replace(wc_get_page_permalink('cart'), wc_get_page_permalink('checkout'), $message);

        $message = str_replace(esc_html__('View cart', 'woocommerce'), esc_html__('Checkout', 'woocommerce'), $message);
      }

      return $message;
    }

    function add_to_cart_redirect($url) {

      if ('yes' === get_option('qlwce_add_to_cart_redirect')) {
        $url = wc_get_checkout_url();
      }

      return $url;
    }

    function woocommerce_cart_redirect_after_add($val) {
      return get_option('qlwce_add_to_cart_redirect');
    }

    function add_to_cart_params($params) {

      if ('yes' === get_option('qlwce_add_to_cart_link')) {
        $params['cart_url'] = wc_get_checkout_url();
        $params['i18n_view_cart'] = esc_html__('Checkout', 'qlwce');
      }

      return $params;
    }

    function add_script() {

      global $current_section;
      ?>
      <script>
        (function ($) {
          'use strict';
          $(window).on('load', function (e) {
            $('label[for=qlwce_add_checkout_cart]').closest('tr').css({'opacity': '0.5', 'pointer-events': 'none'});
            $('label[for=qlwce_add_checkout_cart_fields]').closest('tr').css({'opacity': '0.5', 'pointer-events': 'none'});
          });
        }(jQuery));
      </script>
      <?php

    }

  }

  add_action('plugins_loaded', ['QLWCE', 'instance']);
}