<?php

class Config
{
	private $allowed	= array('db', 'uploads', 'debug', 'salt', 'log', 'require');
	private $required 	= array('db', 'uploads', 'debug', 'salt');
	private $config 	= array();
	private $common		= null;

	function __construct($extract_settings)
	{
		if ( ! is_string($extract_settings) )
		{
			throw new AppConfigException("Invalid configuration string. You are expected to spply the name of the function where I can get your settings");
		}

		if( ! function_exists($extract_settings) )
		{
			throw new AppConfigException("Invalid config function. \"{$extract_settings}\" does not exist");
		}

		$settings = $extract_settings();

		if ( ! is_array($settings) )
		{
			throw new AppConfigException("Your configuration function should return an array, instead a(n) " . get_type($settings) . " was returned.");
		}

		//check that all required config is present
		$missing = array_diff($required, array_keys($settings));

		if ( $missing )
		{
			throw new AppConfigException("Missing required configuration values: [" . implode(',', $missing) . "]");
		}

		//validate all settings
		foreach( $allowed as $option )
		{
			$v_func = 'setup_' . $option;

			if ( method_exists($this, $v_func) )
			{
				$this->{$v_func}();
			}
			else
			{
				$this->config[$option] = false;
			}
		}

		$this->common = Common::getInstance();

	}

	public function getOption($option_name)
	{
		if ( array_key_exists($option_name, $this->config) )
		{
			return $this->config[$option_name];
		}

		return false;
	}

	private function setup_require(array $user_config)
	{
		//This part simply fetches the files, the engine will take care of syntax validation 

		$this->config['require'] = array();

		foreach ( $user_config as $file )
		{
			if ( file_exists($file) && is_dir($file) )
			{
				$this->config['require'][] = glob($file . '/*.php');
			}
			elseif ( file_exists($file) && pathinfo($file, PATHINFO_EXTENSION) == 'php') )
			{
				$this->config['require'][] = $file;
			}
		}

		return true;
	}

	private function setup_log($user_config)
	{
		try{
			$this->common->validate_directory($user_config);
		}catch(Exception $e)
		{
			throw new AppConfigException("Invalid log folder. " . $e->getMessage());
		}

		$this->config['log'] = $user_config;

		return true;
	}

	private function setup_salt($user_config)
	{
		if ( is_string($user_config) )
		{
			throw new AppConfigException("You must supply a string for to salt with. You gave me a " . get_type($user_config));
		}

		$salt_len = strlen($user_config);

		if (  < 32 )
		{
			throw new AppConfigException("It is required that you supply as 32 character salt. This was only {$salt_len} characters long... weeeak!");
		}

		$this->config['salt'] = $user_config;

		return false;
	}

	private function setup_debug($user_config)
	{
		$this->config['debug'] = $user_config ? true : false; //make sure we always have a boolean here

		return true;
	}

	private function setup_uploads($user_config)
	{
		try{
			$this->common->validate_directory($user_config);
		}catch(Exception $e)
		{
			throw new AppConfigException("Invalid path for \"uploads\"." . $e->getMessage());
		}

		$this->config['uploads'] = $user_config;

		return false;
	}

	private function setup_db(array $user_config)
	{
		$req 					= array('host', 'username', 'password', 'database');
		$missing				= array();
		$this->config['db']		= array();

		foreach ( $req as $field )
		{
			if ( ! array_key_exists($field, $user_config) )
			{
				$missing[] = $field;
				continue;
			}

			$this->config['db'][$field]	= $user_config[$field];
		}

		if ( count($missing) )
		{
			throw new AppConfigException("Missing database fields: [" . implode(',', $missing) . "]");
		}

		return true;
	}
}