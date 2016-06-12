<?php

GFForms::include_addon_framework();

class GFIPRestrict extends GFAddOn {

    protected $_version = GF_IP_RESTRICT_VERSION;
    protected $_min_gravityforms_version = '1.9';
    protected $_slug = 'iprestrict';
    protected $_path = 'gravityforms-iprestrict/gravityforms-iprestrict.php';
    protected $_full_path = __FILE__;
    protected $_title = 'Gravity Forms IP Restrict Add-On';
    protected $_short_title = 'IP Restrict';

    private static $_instance = null;

    public static function get_instance() {
        if ( self::$_instance == null ) {
            self::$_instance = new GFIPRestrict();
        }

        return self::$_instance;
    }

    public function init() {
        parent::init();
        add_filter( 'gform_pre_render', array( $this, 'form_apply_iprestrict' ));
        add_filter( 'gform_pre_submission', array( $this, 'form_apply_iprestrict' ));
    }

    public function form_apply_iprestrict( $form ) {
        $settings = $this->get_form_settings( $form );

        // If IP's have been specified, ensure the client IP matches before displaying the form
        if ( !empty( $settings['ipwhitelist'] ) ) {
            // Get IP's from the settings field, splitting on one or more of any character other than a digit or period
            $ips = preg_split('/[^0-9\.]+/',$settings['ipwhitelist']);

            // Get the client's IP
            $myip = empty($_SERVER['REMOTE_ADDR']) ? null : $_SERVER['REMOTE_ADDR'];

            // If there's no match, disallow the form from being displayed
            if(!empty($ips) && !in_array($myip, $ips)) {
                add_filter('gform_validation_message', array($this, 'iprestricted_validation_message'), 10, 2);
                return false;
            }
        }

        return $form;
    }

    public function iprestricted_validation_message($message, $form)
    {
        return "<div class='validation_error'><strong>Oops!</strong> It looks like you are trying to view a restricted form.</div>";
    }

    public function form_settings_fields( $form ) {
        return array(
            array(
                'title'  => esc_html__( 'IP Restrict Form Settings', 'gfiprestrict' ),
                'fields' => array(
                    array(
                        'label'   => esc_html__( 'IP Whitelist', 'gfiprestrict' ),
                        'type'    => 'textarea',
                        'name'    => 'ipwhitelist',
                        'tooltip' => esc_html__( 'Comma-separated list of IP addresses allowed to view and submit this form; leave blank for unrestricted access.', 'gfiprestrict' ),
                        'class'   => 'medium',
                    ),
                ),
            ),
        );
    }

    public function settings_my_custom_field_type( $field, $echo = true ) {
        echo '<div>' . esc_html__( 'My custom field contains a few settings:', 'gfiprestrict' ) . '</div>';

        // get the text field settings from the main field and then render the text field
        $text_field = $field['args']['text'];
        $this->settings_text( $text_field );

        // get the checkbox field settings from the main field and then render the checkbox field
        $checkbox_field = $field['args']['checkbox'];
        $this->settings_checkbox( $checkbox_field );
    }

    public function is_valid_setting( $value ) {
        return strlen( $value ) < 10;
    }

}