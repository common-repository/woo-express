<?php
if (!defined('ABSPATH')) {
  die('-1');
}

if (!class_exists('QLWCE_Checkout')) {

  class QLWCE_Checkout {

    protected static $instance;

    public static function instance() {
      if (!isset(self::$instance)) {
        self::$instance = new self();
        self::$instance->init();
      }
      return self::$instance;
    }

    function add_section($sections) {

      $sections['checkout'] = __('Checkout', 'qlwce');

      return $sections;
    }

    function add_fields($settings) {

      global $current_section;

      if ('checkout' == $current_section) {

        $settings = [
            'section_title' => array(
                'name' => __('Checkout', 'qlwce'),
                'type' => 'title',
                //'desc' => __('Checkout', 'qlwce'),
                'id' => 'section_title'
            ),
            'add_checkout_cart' => array(
                'name' => __('Add cart to checkout', 'qlwce'),
                'desc_tip' => __('Simplifies the checkout process including the shopping cart page inside checkout.', 'qlwce'),
                'id' => 'qlwce_add_checkout_cart',
                'type' => 'select',
                'class' => 'chosen_select',
                'options' => array(
                    'yes' => __('Yes', 'qlwce'),
                    'no' => __('No', 'qlwce'),
                ),
                'default' => 'no',
            ),
            'add_checkout_cart_fields' => array(
                'name' => __('Add cart to checkout fields', 'qlwce'),
                'desc_tip' => __('Include this fields inside the checkout cart.', 'qlwce'),
                'id' => 'qlwce_add_checkout_cart_fields',
                'type' => 'multiselect',
                'class' => 'chosen_select',
                'options' => array(
                    'remove' => __('Remove', 'qlwce'),
                    'thumbnail' => __('Thumbnail', 'qlwce'),
                    'name' => __('Name', 'qlwce'),
                    'price' => __('Price', 'qlwce'),
                    'qty' => __('Quantity', 'qlwce'),
                ),
                'default' => array(
                    0 => 'remove',
                    1 => 'thumbnail',
                    2 => 'price',
                    3 => 'qty',
                )
            ),
            'remove_checkout_fields' => array(
                'name' => __('Remove checkout fields', 'qlwe'),
                'desc_tip' => __('Simplifies the checkout process removing the unnecessary checkout fields.', 'qlwe'),
                'id' => 'qlwce_remove_checkout_fields',
                'type' => 'multiselect',
                'class' => 'chosen_select',
                'options' => array(
                    'country' => __('Country', 'qlwe'),
                    'state' => __('State', 'qlwe'),
                    'city' => __('City', 'qlwe'),
                    'postcode' => __('Postcode', 'qlwe'),
                    'address_1' => __('Address 1', 'qlwe'),
                    'address_2' => __('Address 2', 'qlwe'),
                    'company' => __('Company', 'qlwe'),
                    'phone' => __('Phone', 'qlwe'),
                ),
                'default' => array(
                    0 => 'phone',
                    1 => 'company',
                    2 => 'address_2',
                )
            ),
            'remove_checkout_order_comments' => array(
                'name' => __('Remove checkout order comments', 'qlwce'),
                'desc_tip' => __('Simplifies the checkout process removing the order notes.', 'qlwce'),
                'id' => 'qlwce_remove_checkout_order_comments',
                'type' => 'select',
                'class' => 'chosen_select',
                'options' => array(
                    'yes' => __('Yes', 'qlwce'),
                    'no' => __('No', 'qlwce'),
                ),
                'default' => 'no',
            ),
            'remove_checkout_shipping_address' => array(
                'name' => __('Remove checkout shipping address', 'qlwce'),
                'desc_tip' => __('Simplifies the checkout process removing the shipping address.', 'qlwce'),
                'id' => 'qlwce_remove_checkout_shipping_address',
                'type' => 'select',
                'class' => 'chosen_select',
                'options' => array(
                    'yes' => __('Yes', 'qlwce'),
                    'no' => __('No', 'qlwce'),
                ),
                'default' => 'no',
            ),
            'remove_checkout_coupon_form' => array(
                'name' => __('Remove checkout coupon form', 'qlwce'),
                'desc_tip' => __('Simplifies the checkout process removing the coupon form.', 'qlwce'),
                'id' => 'qlwce_remove_checkout_coupon_form',
                'type' => 'select',
                'class' => 'chosen_select',
                'options' => array(
                    'yes' => __('Yes', 'qlwce'),
                    'no' => __('No', 'qlwce'),
                ),
                'default' => 'no',
            ),
            'remove_checkout_privacy_policy_text' => array(
                'name' => __('Remove checkout policy text', 'qlwce'),
                'desc_tip' => __('Simplifies the checkout process removing the policy text.', 'qlwce'),
                'id' => 'qlwce_remove_checkout_privacy_policy_text',
                'type' => 'select',
                'class' => 'chosen_select',
                'options' => array(
                    'yes' => __('Yes', 'qlwce'),
                    'no' => __('No', 'qlwce'),
                ),
                'default' => 'no',
            ),
            'remove_remove_checkout_terms_and_conditions' => array(
                'name' => __('Remove checkout terms and conditions', 'qlwce'),
                'desc_tip' => __('Simplifies the checkout process removing the terms and conditions.', 'qlwce'),
                'id' => 'qlwce_remove_checkout_terms_and_conditions',
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
                'id' => 'wc_settings_tab_demo_section_end'
            )
        ];
      }

      return $settings;
    }

    function remove_checkout_fields($fields) {

      if ($remove = get_option('qlwce_remove_checkout_fields', [])) {
        foreach ($remove as $id => $key) {
          unset($fields['billing']['billing_' . $key]);
          unset($fields['shipping']['shipping_' . $key]);
        }
      }

      return $fields;
    }

    function remove_checkout_order_commens($return) {

      if ('yes' === get_option('qlwce_remove_checkout_order_comments')) {
        $return = false;
      }

      return $return;
    }

    /*function wc_get_template($located, $template_name, $args, $template_path, $default_path) {

      if ('checkout/review-order.php' == $template_name) {
        if ('yes' === get_option('qlwce_add_checkout_cart') && count(get_option('qlwce_add_checkout_cart_fields', []))) {
          $located = QLWCE_PLUGIN_DIR . 'templates/checkout/review-order.php';
        }
      }

      return $located;
    }*/

    function remove_checkout_shipping_address($val) {

      if ('yes' === get_option('qlwce_remove_checkout_shipping_address')) {
        $val = 'billing_only';
      }

      return $val;
    }

    function update_cart() {

      if (!check_ajax_referer('qlwce', 'nonce', false)) {
        wp_send_json_error(esc_html__('Please reload page.', 'qlwce'));
      }

      $cart_item_key = $_POST['hash'];

      $threeball_product_values = WC()->cart->get_cart_item($cart_item_key);

      $threeball_product_quantity = apply_filters('woocommerce_stock_amount_cart_item', apply_filters('woocommerce_stock_amount', preg_replace("/[^0-9\.]/", '', filter_var($_POST['quantity'], FILTER_SANITIZE_NUMBER_INT))), $cart_item_key);

      $passed_validation = apply_filters('woocommerce_update_cart_validation', true, $cart_item_key, $threeball_product_values, $threeball_product_quantity);

      if ($passed_validation) {
        WC()->cart->set_quantity($cart_item_key, $threeball_product_quantity, true);
      }

      ob_start();
      ?>
      <div id="order_review" class="woocommerce-checkout-review-order">
        <?php do_action('woocommerce_checkout_order_review'); ?>
      </div>
      <?php
      $data = ob_get_clean();

      wp_send_json($data);

      wp_die();
    }

    function init() {

      add_filter('qlwce_add_fields', [$this, 'add_fields']);
      add_filter('woocommerce_get_sections_qlwce', [$this, 'add_section']);

      add_action('wp_ajax_qlwce_update_cart', array($this, 'update_cart'));
      add_action('wp_ajax_qlwce_update_cart', array($this, 'update_cart'));
      add_filter('woocommerce_checkout_fields', [$this, 'remove_checkout_fields']);
      add_filter('woocommerce_enable_order_notes_field', [$this, 'remove_checkout_order_commens']);
      //add_filter('wc_get_template', [$this, 'wc_get_template'], 10, 5);
      add_filter('option_woocommerce_ship_to_destination', [$this, 'remove_checkout_shipping_address'], 10, 3);

      if ('yes' === get_option('qlwce_remove_checkout_coupon_form')) {
        remove_action('woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form', 10);
      }

      if ('yes' === get_option('qlwce_remove_checkout_privacy_policy_text')) {
        remove_action('woocommerce_checkout_terms_and_conditions', 'wc_checkout_privacy_policy_text', 20);
      }

      if ('yes' === get_option('qlwce_remove_checkout_terms_and_conditions')) {
        remove_action('woocommerce_checkout_terms_and_conditions', 'wc_terms_and_conditions_page_content', 30);
      }
    }

  }

  QLWCE_Checkout::instance();
}