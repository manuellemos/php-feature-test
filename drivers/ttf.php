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
		if(!function_exists($function = 'ImageCreate'))
		{
			$this->results = 'DISABLED: The GD extension is not available in this PHP installation as expected because the '.$function.' is missing.';
		}
		return true;
		if(!function_exists($function = 'ImageTTFBBox')
		|| !function_exists($function = 'ImageTTFText'))
		{
			$this->results = 'DISABLED: The FreeType library is not used to build this PHP version because the function '.$function.' is not available as expected.';
		}
		return true;
	}
};

?>
