<?php
/*
 * sapi.php
 *
 * @(#) $Id: sapi.php,v 1.2 2021/09/09 05:02:59 mlemos Exp $
 *
 */

class feature_test_sapi_class extends feature_test_driver_class
{
	Function Process()
	{
		$this->results = 'INFORMATION: Currently PHP is using Server API '.php_sapi_name();
		return true;
	}
};

?>