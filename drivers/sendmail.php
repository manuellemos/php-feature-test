<?php
/*
 * sendmail.php
 *
 * @(#) $Id: sendmail.php,v 1.4 2021/09/09 05:02:59 mlemos Exp $
 *
 */

class feature_test_sendmail_class extends feature_test_driver_class
{
	Function Process()
	{
		$sendmail_paths = array(
			'/usr/lib/sendmail',
			'/usr/sbin/sendmail'
		);
		foreach($sendmail_paths as $path)
		{
			if(!file_exists($path))
				$this->results = 'DISABLED: The sendmail program does not exist at '.$path.' .';
			elseif(!is_executable($path))
				$this->results = 'The sendmail program exists with at '.$path.' but it is not executable.';
		}
		return true;
	}
};
	
?>