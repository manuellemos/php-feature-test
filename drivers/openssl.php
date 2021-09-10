<?php
/*
 * openssl.php
 *
 * @(#) $Id: openssl.php,v 1.6 2021/09/09 05:52:03 mlemos Exp $
 *
 */

class feature_test_openssl_class extends feature_test_driver_class
{
	public $required_options = array(
		'cipher',
	);

	Function Process()
	{
		if(!extension_loaded('openssl'))
		{
			$this->results = 'DISABLED: The OpenSSL extension does not seem to be installed.';
		}
		else
		{
			$cipher = $this->options->cipher;
			$data = 'test';
			$key = 'key';
			$options = true;
			if(($vector_length = openssl_cipher_iv_length($cipher)) === false)
			{
				$this->results = 'It was not possible to get the length of the encryption cipher "'.$cipher.'" .';
			}
			else
			{
				$vector = ($vector_length ? openssl_random_pseudo_bytes($vector_length) : '');
				if(($encrypted = openssl_encrypt($data, $cipher, $key, $options, $vector)))
				{
					if(($decrypted = openssl_decrypt($encrypted, $cipher, $key, $options, $vector)))
					{
						if($decrypted !== $data)
							$this->results = 'The OpenSSL encryption and decryption failed.';
					}
					else
					{
						$this->results = 'The OpenSSL decryption failed.';
					}
				}
				else
				{
					$this->results = 'The OpenSSL encryption failed.';
				}
			}
		}
		return true;
	}
};
	
?>