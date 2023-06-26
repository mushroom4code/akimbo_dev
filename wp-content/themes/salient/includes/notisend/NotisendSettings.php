<?php

class NotisendSettings {

	// single instance of settings class object
	static private ?NotisendSettings $_settings = null;

	public string $apiToken;
	public bool $enable;
	public string $address;
	public int $group;

	public string $param_city;
	public string $param_phone;
	public string $param_date_registered;

	public string $start_date_registered;

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

		$this->param_city = $options['param_city'] ?? '' ;
		$this->param_phone = $options['param_phone'] ?? '' ;
		$this->param_date_registered = $options['param_date_registered'] ?? '' ;
		$this->start_date_registered = $options['start_date_registered'] ?? '0001-01-01' ;
	}

}

