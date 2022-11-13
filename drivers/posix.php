<?php
/*
 * posix.php
 *
 * @(#) $Id: $
 *
 */

class feature_test_posix_class extends feature_test_driver_class
{
	Function Process()
	{
		if(!extension_loaded('posix'))
		{
			$this->results = 'DISABLED: The POSIX extension is not available in this PHP installation as expected.';
		}
		return true;
	}
};

?>