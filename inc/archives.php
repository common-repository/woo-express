<?php

if (!defined('ABSPATH')) {
  die('-1');
}

if (!class_exists('QLWCE_Archives')) {

  class QLWCE_Archives {

    protected static $instance;

    public static function instance() {
      if (!isset(self::$instance)) {
        self::$instance = new self();
        self::$instance->init();
      }
      return self::$instance;
    }

    function add_section($sections) {

      $sections['archives'] = __('Archives', 'qlwce');

      return $sections;
    }

    function add_fields($fields) {

      global $current_section;

      if ('archives' == $current_section) {

        $fields = [
            'qlwce_section_title' => array(
                'name' => __('Archives', 'qlwce'),
                'type' => 'title',
                //'desc' => __('Archives', 'qlwce'),
                'id' => 'qlwce_archives_section_title'
            ),
            'add_archive_ajax' => array(
                'name' => __('Ajax add to cart', 'qlwce'),
                'desc_tip' => __('Ajax add to cart for single products.', 'qlwce'),
                'id' => 'qlwce_add_archive_ajax',
                'type' => 'select',
                'class' => 'chosen_select',
                'options' => array(
                    'yes' => __('Yes', 'qlwce'),
                    'no' => __('No', 'qlwce'),
                ),
                'default' => 'no',
            ),
            /*'add_archive_ajax_message' => array(
                'name' => __('Ajax add to cart alert', 'qlwce'),
                'desc_tip' => __('Ajax add to cart alert for single products.', 'qlwce'),
                'id' => 'qlwce_add_archive_ajax_message',
                'type' => 'select',
                'class' => 'chosen_select',
                'options' => array(
                    'yes' => __('Yes', 'qlwce'),
                    'no' => __('No', 'qlwce'),
                ),
                'default' => 'no',
            ),*/
            'add_archive_text' => array(
                'name' => __('Replace Add to cart text', 'qlwce'),
                'desc_tip' => __('Replace "Add to cart" text.', 'qlwce'),
                'id' => 'qlwce_add_archive_text',
                'type' => 'select',
                'class' => 'chosen_select',
                'options' => array(
                    'yes' => __('Yes', 'qlwce'),
                    'no' => __('No', 'qlwce'),
                ),
                'default' => 'no',
            ),
            'add_archive_text_in' => array(
                'name' => __('Replace Add to cart text in', 'qlwce'),
                'desc_tip' => __('Replace "Add to cart" text in product types.', 'qlwce'),
                'id' => 'qlwce_add_archive_text_in',
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
            'add_archive_text_content' => array(
                'name' => __('Replace Add to cart text content', 'qlwce'),
                'desc_tip' => __('Replace "Add to cart" text with this text.', 'qlwce'),
                'id' => 'qlwce_add_archive_text_content',
                'type' => 'text',
                'default' => esc_html__('Purchase', 'qlwce'),
            ),
            'qlwce_section_end' => array(
                'type' => 'sectionend',
                'id' => 'qlwce_archives_section_end'
            )
        ];
      }

      return $fields;
    }

    function add_settings() {

      global $current_section;

      if ('' == $current_section) {
        woocommerce_admin_fields($this->add_fields());
      }
    }

    function add_archive_text($text, $product) {

      if ('yes' === get_option('qlwce_add_archive_text')) {
        if ($product->is_type(get_option('qlwce_add_archive_text_in', []))) {
          $text = esc_html__(get_option('qlwce_add_archive_text_content'));
        }
      }

      return $text;
    }

    function woocommerce_enable_ajax_add_to_cart($val) {
      return get_option('qlwce_add_archive_ajax');
    }

    function init() {
      add_action('qlwce_add_fields', [$this, 'add_fields']);
      add_filter('woocommerce_get_sections_qlwce', [$this, 'add_section']);
      add_filter('woocommerce_product_add_to_cart_text', [$this, 'add_archive_text'], 10, 2);
      add_filter('option_woocommerce_enable_ajax_add_to_cart', [$this, 'woocommerce_enable_ajax_add_to_cart'], 10, 3);
    }

  }

  QLWCE_Archives::instance();
}