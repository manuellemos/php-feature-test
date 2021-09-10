<?php
/*
 * zlib.php
 *
 * @(#) $Id: zlib.php,v 1.2 2021/09/09 05:02:59 mlemos Exp $
 *
 */

class feature_test_zlib_class extends feature_test_driver_class
{
	Function Process()
	{
		if(!extension_loaded('zlib'))
		{
			$this->results='DISABLED: The Zlib extension does not seem to be installed.';
		}
		else
		{
			if(!function_exists('gzuncompress'))
			{
				$this->results='The gzuncompress function does not seem to be available.';
			}
		}
		return true;
	}
};

?>