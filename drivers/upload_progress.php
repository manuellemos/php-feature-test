<?php
/*
 * upload_progress.php
 *
 * @(#) $Id: upload_progress.php,v 1.6 2021/09/09 05:52:03 mlemos Exp $
 *
 */

class feature_test_upload_progress_class extends feature_test_driver_class
{
	public $required_options = array(
		'upload_progress',
	);

	Function Process()
	{
		if(!$this->options->upload_progress)
		{
			$this->results = 'INFORMATION: The upload progress support is disabled in the current site configuration.';
		}
		elseif(!function_exists('uploadprogress_get_info'))
		{
			$this->results = 'DISABLED: The upload progress extension does not seem to be installed.';
		}
		return true;
	}
};

?>