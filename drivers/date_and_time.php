<?php
/*
 * date_and_time.php
 *
 * @(#) $Id: $
 *
 */

class feature_test_date_and_time_class extends feature_test_driver_class
{
	Function Process()
	{
		$extension = 'Intl';
		if(!IsSet($this->options->intl)
		|| $this->options->intl)
		{
			$v = explode('.', phpversion());
			$version = intval($v[0]) * 1000000 + intval($v[1]) * 1000 + intval($v[2]);
			if(extension_loaded($extension))
			{
				if(!IsSet($this->options->strftime)
				|| $this->options->strftime)
				{
					if($version >= 8001000)
					{
						if($version >= 9000000)
						{
							$this->results = 'INFORMATION: if you use functions gmstrftime or strftime that were removed in PHP 9.0, you need to change your code to use other functions like date_format and date_create of the '.$extension.' extension.';
						}
						else
						{
							$this->results = 'DISABLED: if you use functions gmstrftime or strftime that were deprecated in PHP 8.1, you need to change your code to use other functions like date_format and date_create of the '.$extension.' extension.';
						}
					}
				}
			}
			else
			{
				$this->results = 'DISABLED: the '.$extension.' extension is not installed in this PHP environment';
			}
		}
		return true;
	}
};

?>