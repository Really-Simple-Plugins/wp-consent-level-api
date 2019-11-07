<?php
defined('ABSPATH') or die("you do not have acces to this page!");

if (!class_exists("CLAPI_CONFIG")) {
	class CLAPI_CONFIG
	{
		private static $_this;


		function __construct()
		{
			if (isset(self::$_this))
				wp_die(sprintf(__('%s is a singleton class and you cannot create a second instance.', 'complianz-gdpr'), get_class($this)));

			self::$_this = $this;

		}


		static function this()
		{
			return self::$_this;
		}

		/**
		 * Get list if active consenttypes
		 * @return array() $consenttypes
		 */

		public function consenttypes()
		{
			$consenttypes = array(
				'optin',
				'optout',
			);

			return apply_filters('wp_consenttypes', $consenttypes);
		}


		/**
		 * Get list if active consenttypes
		 * @return array() $consenttypes
		 */

		public function consentlevels()
		{
			$consenttypes = array(
				'functional',
				'statistics',
				'anonymous',
			    'statistics',
				'marketing',

			);

			return apply_filters('wp_consentlevels', $consenttypes);
		}


		/**
		 * Get list of possible consentvalues
		 * @return array() $consentvalues
		 */

		public function consentvalues()
		{
			$consentvalues = array(
				'allow',
				'deny',
			);

			return apply_filters('wp_consentvalues', $consentvalues);
		}


		public function expiration(){
			return apply_filters('wp_consent_expiration', DAY_IN_SECONDS * 30);
		}


	}
} //class closure
