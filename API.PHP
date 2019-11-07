<?php
defined('ABSPATH') or die("you do not have acces to this page!");

if (!class_exists("CLAPI_API")) {
    class CLAPI_API
    {
        private static $_this;


        function __construct()
        {
            if (isset(self::$_this))
                wp_die(sprintf(__('%s is a singleton class and you cannot create a second instance.', 'complianz-gdpr'), get_class($this)));

            self::$_this = $this;

            add_action('plugins_loaded', array($this, 'process_save'), 20);
            add_action('cmplz_register_translation', array($this, 'register_translation'), 10, 2);

        }

        static function this()
        {
            return self::$_this;
        }







    }
} //class closure