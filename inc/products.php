<?php

if (!defined('ABSPATH')) {
  die('-1');
}

if (!class_exists('QLWCE_Products')) {

  class QLWCE_Products {

    protected static $instance;

    public static function instance() {
      if (!isset(self::$instance)) {
        self::$instance = new self();
        self::$instance->init();
      }
      return self::$instance;
    }

    function add_section($sections = []) {

      $sections['products'] = __('Products', 'qlwce');

      return $sections;
    }

    function add_fields($fields) {

      global $current_section;

      if ('products' == $current_section) {

        $fields = [
            'section_title' => array(
                'name' => __('Products', 'qlwce'),
                'type' => 'title',
                //'desc' => __('Products', 'qlwce'),
                'id' => 'qlwce_products_section_title'
            ),
            'add_product_ajax' => array(
                'name' => __('Ajax add to cart', 'qlwce'),
                'desc_tip' => __('Ajax add to cart for single products.', 'qlwce'),
                'id' => 'qlwce_add_product_ajax',
                'type' => 'select',
                'class' => 'chosen_select',
                'options' => array(
                    'yes' => __('Yes', 'qlwce'),
                    'no' => __('No', 'qlwce'),
                ),
                'default' => 'no',
            ),
            'add_product_ajax_message' => array(
                'name' => __('Ajax add to cart alert', 'qlwce'),
                'desc_tip' => __('Ajax add to cart alert for single products.', 'qlwce'),
                'id' => 'qlwce_add_product_ajax_message',
                'type' => 'select',
                'class' => 'chosen_select',
                'options' => array(
                    'yes' => __('Yes', 'qlwce'),
                    'no' => __('No', 'qlwce'),
                ),
                'default' => 'no',
            ),
            'add_product_text' => array(
                'name' => __('Replace Add to cart text', 'qlwce'),
                'desc_tip' => __('Replace "Add to cart" text.', 'qlwce'),
                'id' => 'qlwce_add_product_text',
                'type' => 'select',
                'class' => 'chosen_select',
                'options' => array(
                    'yes' => __('Yes', 'qlwce'),
                    'no' => __('No', 'qlwce'),
                ),
                'default' => 'no',
            ),
            'add_product_text_in' => array(
                'name' => __('Replace Add to cart text in', 'qlwce'),
                'desc_tip' => __('Replace "Add to cart" text in product types.', 'qlwce'),
                'id' => 'qlwce_add_product_text_in',
                'type' => 'multiselect',
                'class' => 'chosen_select',
                'options' => array(
                    'simple' => __('Simple Products', 'qlwce'),
                    'grouped' => __('Grouped Products', 'qlwce'),
                    'virtual' => __('Virtual Products', 'qlwce'),
                    'variable' => __('Variable Products', 'qlwce'),
                    'downloadable' => __('Downloadable Products', 'qlwce'),
                ),
                'default' => array('simple'),
            ),
            'add_product_text_content' => array(
                'name' => __('Replace Add to cart text content', 'qlwce'),
                'desc_tip' => __('Replace "Add to cart" text with this text.', 'qlwce'),
                'id' => 'qlwce_add_product_text_content',
                'type' => 'text',
                'default' => esc_html__('Purchase', 'qlwce')
            ),
            'add_product_default_attributes' => array(
                'name' => __('Add default attributes in variable products', 'qlwce'),
                'desc_tip' => __('Add default attributes in all variable products to avoid disabled Add to cart button.', 'qlwce'),
                'id' => 'qlwce_add_product_default_attributes',
                'type' => 'select',
                'class' => 'chosen_select',
                'options' => array(
                    'yes' => __('Yes', 'qlwce'),
                    'no' => __('No', 'qlwce'),
                ),
                'default' => 'no',
            ),
            'section_end' => array(
                'type' => 'sectionend',
                'id' => 'qlwce_products_section_end'
            )
        ];
      }

      return $fields;
    }

    function add_product_text($text, $product) {

      if ('yes' === get_option('qlwce_add_product_text')) {
        if ($product->is_type(get_option('qlwce_add_product_text_in', []))) {
          $text = esc_html__(get_option('qlwce_add_product_text_content'));
        }
      }

      return $text;
    }

    function add_product_default_attributes() {

      if ('yes' === get_option('qlwce_add_product_default_attributes')) {

        global $product;

        if (!count($default_attributes = get_post_meta($product->get_id(), '_default_attributes'))) {

          $new_defaults = array();

          $product_attributes = $product->get_attributes();

          if (count($product_attributes)) {

            foreach ($product_attributes as $key => $attributes) {

              $values = explode(',', $product->get_attribute($key));

              if (isset($values[0]) && !isset($default_attributes[$key])) {
                $new_defaults[$key] = sanitize_key($values[0]);
              }
            }

            update_post_meta($product->get_id(), '_default_attributes', $new_defaults);
          }
        }
      }
    }

    function add_product_ajax() {

      if (!check_ajax_referer('qlwce', 'nonce', false)) {
        wp_send_json_error(esc_html__('Please reload page.', 'qlwce'));
      }

      WC_AJAX::get_refreshed_fragments();

      wp_die();
    }

    function add_product_ajax_message() {

      if (!check_ajax_referer('qlwce', 'nonce', false)) {
        wp_send_json_error(esc_html__('Please reload page.', 'qlwce'));
      }

      ob_start();

      wc_print_notices();

      $data = ob_get_clean();

      wp_send_json($data);
    }

    function init() {
      add_filter('qlwce_add_fields', [$this, 'add_fields']);
      add_filter('woocommerce_get_sections_qlwce', [$this, 'add_section']);
      add_action('wp_ajax_qlwce_add_product_ajax', array($this, 'add_product_ajax'));
      add_action('wp_ajax_nopriv_qlwce_add_product_ajax', array($this, 'add_product_ajax'));
      add_action('wp_ajax_qlwce_add_product_ajax_message', array($this, 'add_product_ajax_message'));
      add_action('wp_ajax_qlwce_nopriv_add_product_ajax_message', array($this, 'add_product_ajax_message'));
      add_filter('woocommerce_product_single_add_to_cart_text', [$this, 'add_product_text'], 10, 2);
      add_action('woocommerce_before_single_product_summary', [$this, 'add_product_default_attributes']);
    }

  }

  QLWCE_Products::instance();
}