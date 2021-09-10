<?php
/*
 * zend_opcache.php
 *
 * @(#) $Id: zend_opcache.php,v 1.3 2021/09/09 05:02:59 mlemos Exp $
 *
 */

class feature_test_zend_opcache_class extends feature_test_driver_class
{
	Function Process()
	{
		if(!function_exists('opcache_get_status'))
		{
			echo 'DISABLED: The Zend Opcache extension does not seem to be installed.';
		}
		return true;
	}
};

?>