<?php

class NotisendSettings {

	// single instance of settings class object
	static private ?NotisendSettings $_settings = null;

	public string $apiToken;
	public bool $enable;
	public string $address;
	public int $group;

	/** Get or init single instance settings
	 * @return NotisendSettings
	 */
	public static function getSettings(  ) {
		if (self::$_settings===null) {
			self::$_settings = new NotisendSettings();
		}
		return self::$_settings;
	}

	/**
	 * constructor for NotisendSettings
	 */
	public function __construct() {
		$options = get_option("notisend_options");

		$this->apiToken = $options['api_token'] ?? '';
		$this->enable = $options['enable'] ?? false;
		$this->address = 'https://api.notisend.ru/v1/';
		$this->group = $options['group_client'] ?? '' ;
	}

}

