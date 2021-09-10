<?php
/*
 * mysqli.php
 *
 * @(#) $Id: mysqli.php,v 1.5 2021/09/09 05:52:03 mlemos Exp $
 *
 */

class feature_test_mysqli_class extends feature_test_driver_class
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
		if(function_exists('mysqli_connect'))
		{
			$database_connection = $this->options->database_type.'://'.UrlEncode($this->options->database_user).':'.UrlEncode($this->options->database_password).'@'.$this->options->database_host.'/'.$this->options->database_name;
			
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
				&& $connection['scheme'] === 'mysqli')
				{
						if(IsSet($connection['query']))
							parse_str($connection['query'], $options);
						else
							$options = array();
						$host = $connection['host'];
						$user = $connection['user'];
						$password = $connection['pass'];
						$database = substr($connection['path'], 1);
						$port = (IsSet($options['Options/Port']) ? $options['Options/Port'] : 0);
						$socket = (IsSet($options['Options/Socket']) ? $options['Options/Socket'] : '');
						if(!($db = @mysqli_connect($host, $user, $password, $database, $port, $socket)))
						{
							$error = error_get_last();
							$this->results = 'could not connect to MySQL host '.$host.': '.$error['message'].' ('.$socket.')';
						}
						else
							mysqli_close($db);
				}
			}
		}
		else
			$this->results = 'DISABLED: the MySQLi extension is not available in this PHP installation';
		return true;
	}
};

?>