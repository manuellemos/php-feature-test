<?php
/*
 * ttf.php
 *
 * @(#) $Id: ttf.php,v 1.3 2021/09/09 05:02:59 mlemos Exp $
 *
 */

class feature_test_ttf_class extends feature_test_driver_class
{
	Function Process()
	{
		if(!function_exists($function = 'ImageTTFBBox')
		|| !function_exists($function = 'ImageTTFText'))
		{
			$this->results = 'DISABLED: The function '.$function.' is not available in this PHP installation as expected.';
		}
		return true;
	}
};

?>
