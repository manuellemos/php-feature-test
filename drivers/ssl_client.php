<?php
/*
 * ssl_client.php
 *
 * @(#) $Id: ssl_client.php,v 1.7 2021/09/09 05:52:03 mlemos Exp $
 *
 */

class feature_test_ssl_client_class extends feature_test_driver_class
{
	public $required_options = array(
		'ssl_url',
	);

	Function Process()
	{
		if(!extension_loaded('openssl'))
		{
			$this->results = 'DISABLED: The OpenSSL extension is not available in this PHP installation as expected.';
		}
		else
		{
			$url = str_replace('{account}', 'mlemos', $this->options->ssl_url);
			$context = array(
				'ssl' => array(
					'verify_peer' => false,
					'verify_peer_name' => false
				)
			);  
			$success = file_get_contents($url, false, stream_context_create($context));
			if(!$success)
			{
				$error = error_get_last();
				$this->results = 'Accessing OpenID URL failed ('.$url.') with error: '.$error['message'];
			}
		}
		return true;
	}
};

?>