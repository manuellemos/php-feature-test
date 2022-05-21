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
			if(!IsSet($this->options->ssl_url_user))
			{
				$this->results = 'the ssl_url_user option is not set';
			}
			elseif(!IsSet($this->options->ssl_url))
			{
				$this->results = 'the ssl_url option is not set.';
			}
			else
			{
				$url = str_replace('{account}', $this->options->ssl_url_user, $this->options->ssl_url);
				$parsed = parse_url($url);
				if($parsed === FALSE)
				{
					$this->results = 'the URL to test SSL '.$url.' is not valid.';
				}
				else
				{
					$host = $parsed['host'];
					$ip = gethostbyname($host);
					if($ip === $host)
					{
						$this->results = 'It is not possible to resolve the host name '.$host.' the of the URL to test SSL '.$url.' .';
					}
					else
					{
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
				}
			}
		}
		return true;
	}
};

?>