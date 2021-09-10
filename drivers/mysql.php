<?php
/*
 * mysql.php
 *
 * @(#) $Id: mysql.php,v 1.6 2021/09/09 05:52:03 mlemos Exp $
 *
 */

class feature_test_mysql_class extends feature_test_driver_class
{
	public $required_options = array(
		'database_type',
		'database_user',
		'database_password',
		'database_host',
		'database_name',
	);

	Function Process()
	{
		if(function_exists('mysql_connect'))
		{
			$database_connection = $this->options->database_type.'://'.UrlEncode($this->options->database_user).':'.UrlEncode($this->options->database_password).'@'.$this->options->database_host.'/'.$this->options->database_name;
			$first = true;
			foreach($this->options->database_options as $option => $value)
			{
				$database_connection .= ($first ? '?' : '&').'Options/'.$option.'='.UrlEncode($value);
				$first = false;
			}
			if(GetType($connection = @parse_url($database_connection)) != 'array')
			{
				$this->results = 'it was not specified a valid database connection: '.$database_connection;
			}
			else
			{
				if(IsSet($connection['scheme'])
				&& $connection['scheme'] === 'mysql')
				{
					if(IsSet($connection['query']))
						parse_str($connection['query'], $options);
					else
						$options = array();
					$host = $connection['host'];
					$user = $connection['user'];
					$password = $connection['pass'];
					$database = substr($connection['path'], 1);
					$port = (IsSet($options['Options/Port']) ? $options['Options/Port'] : '');
					$socket = (IsSet($options['Options/Socket']) ? $options['Options/Socket'] : '');
					if(!($db = @mysql_connect($host.(strlen($port) ? ':'.$port : '').(strlen($socket) ? ':'.$socket : ''), $user, $password)))
					{
						$error = error_get_last();
						$this->results = 'could not connect to MySQL host '.$host.': '.$error['message'].' ('.$database_connection.')';
					}
					else
					{
						if(!mysql_select_db($database, $db))
						{
							$this->results = 'could not access to MySQL database '.$database.': '.mysql_error($db);
						}
						mysql_close($db);
					}
				}
			}
		}
		else
			$this->results = 'INFORMATION: the MySQL extension is not available in this PHP installation';
		return true;
	}
};

?>