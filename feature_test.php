<?php
/*
 * feature_test.php
 *
 * @(#) $Id: $
 *
 */

class feature_test_driver_class
{
	public $name = '';
	public $required_options = array();
	public $options;
	public $error = '';
	protected $results = '';

	Function Initialize()
	{
		foreach($this->required_options as $option)
		{
			if(!IsSet($this->options->{$option}))
			{
				$this->error = 'the option "'.$option.'" required to execute the test "'.$this->name.'" is not set';
				return false;
			}
		}
		return true;
	}

	Function Process()
	{
		return true;
	}

	Function Finalize()
	{
		return true;
	}

	Function Output()
	{
		return $this->results;
	}
};
 
class feature_test_class
{
	public $options;
	public $error = '';
	public $results = '';
	public $tests = array();
	public $test_list = array();
	public $html = false;
	public $testing_title = 'Testing the environment';
	public $configuration_file = 'feature_test_configuration.json';
	public $drivers_path = 'drivers';
	public $generated_path = '';
	public $expected_path = '';

	private function EchoOutput($output, $encode = true)
	{
		if($this->html)
		{
			if($encode)
				$output = nl2br(HtmlSpecialChars($output));
			$this->results .= $output;
		}
		else
			echo $output;
	}
	
	private function FlushOutput()
	{
		if(!$this->html)
			flush();
	}

	private function SetError($error)
	{
		$this->error = $error;
		return(false);
	}
	
	private function SetPHPError($error)
	{
		$e = error_get_last();
		if(IsSet($e))
			$error.=": ".$e['message'];
		return($this->SetError($error));
	}

	private function GetPath($directory, $path)
	{
		if($directory !== '')
		{
			if($directory[strlen($directory) - 1])
				$directory .= '/';
			$path = $directory.$path;
		}
		return $path;
	}

	private function GetDriverPath($path)
	{
		return $this->GetPath($this->drivers_path, $path);
	}

	private function GetGeneratedPath($path)
	{
		return $this->GetPath($this->generated_path, $path);
	}

	private function GetExpectedPath($path)
	{
		return $this->GetPath($this->expected_path, $path);
	}

	public function Initialize()
	{
		if(!($json = @file_get_contents($this->configuration_file)))
		{
			if(!file_exists($this->configuration_file))
				return $this->SetError('the feature test configuration file '.$this->configuration_file.' does not exist');
			return $this->SetPHPError('could not read the feature test configuration file '.$this->configuration_file);
		}
		$feature_test = json_decode($json);
		if(!IsSet($feature_test))
			return $this->SetPHPError('It was not possible to decode the feature test configuration file '.$this->configuration_file.' eventually due to incorrect format');
		if(GetType($feature_test) !== 'object')
			return $this->SetError('It was not possible to decode the feature test configuration file '.$this->configuration_file.' because it does not correctly define a JSON object');
		if(!IsSet($feature_test->features)
		|| GetType($feature_test->features) !== 'object')
			return $this->SetError('It was not possible to decode the feature test configuration file '.$this->configuration_file.' because it does not correctly define a JSON object for features');
		foreach($feature_test->features as $name => $properties)
		{
			if(GetType($properties) !== 'object')
				return $this->SetError('Feature test configuration file '.$this->configuration_file.' for the "'.$name.'" feature does not correctly define a JSON object');
			$types = array(
				'class'=>'string',
				'driver'=>'file',
				'nooutput'=>'boolean'
			);
			$required = array(
				'class'=>array(),
				'driver'=>array(),
			);
			$test_properties = array();
			foreach($properties as $property => $value)
			{
				if(!IsSet($types[$property]))
					return $this->SetError($property.' is not a supported property for the "'.$name.'" feature in the feature test configuration file '.$this->configuration_file);
				$type = GetType($value);
				$expected = $types[$property];
				switch($expected)
				{
					case 'file':
						$real_expected = 'string';
						break;
					default:
						$real_expected = $type;
						break;
				}
				if($type !== $real_expected)
					return $this->SetError('the property "'.$property.'" for the "'.$name.'" feature is not of type "'.$expected.'", it is of type "'.$type.'", in the feature test configuration file '.$this->configuration_file);
				switch($expected)
				{
					case 'file':
						switch($property)
						{
							case 'driver':
								$path = $this->GetDriverPath($value);
								break;
							default:
								$path = $value;
						}
						if(!file_exists($path))
							return $this->SetError('the property "'.$property.'" for the "'.$name.'" feature points to a file named "'.$value.'" that does not exist at "'.$path.'", in the feature test configuration file '.$this->configuration_file);
						break;
				}
				$test_properties[$property] = $value;
				UnSet($required[$property]);
			}
			foreach($required as $property => $value)
			{
				if(count($value))
					return $this->SetError('the property "'.$property.'" is not defined for the "'.$name.'" feature in the feature test configuration file '.$this->configuration_file);
			}
			$this->tests[$name] = $test_properties;
		}
		return true;
	}

	public function Process()
	{
		if($this->html)
		{
			$this->EchoOutput('<html><head><title>', false);
			$this->EchoOutput($this->testing_title);
			$this->EchoOutput('</title></head><body><pre>', false);
		}
		if(count($this->test_list) > 0)
		{
			$__few = array();
			for($__a = 0; $__a < count($this->test_list); ++$__a)
			{
				$__name = $this->test_list[$__a];
				if(!IsSet($this->tests[$__name]))
				{
					$this->error = $__name." is not a valid test name from the list of available tests: ";
					$first_test = true;
					foreach(array_keys($this->tests) as $test_name)
					{
						$this->error .= ($first_test ? '' : ', ').$test_name;
						$first_test = false;
					}
					return false;
				}
				else
					$__few[$__name] = $this->tests[$__name];
			}
			$this->tests = $__few;
		}
		$__disabled_text = 'DISABLED: ';
		$__information_text = 'INFORMATION: ';
		for($__information = $__disabled = $__different = $__test = $__checked = 0, Reset($this->tests); $__test<count($this->tests); Next($this->tests), $__test++)
		{
			$__name=Key($this->tests);
			$__script=$this->GetDriverPath($this->tests[$__name]['driver']);
			if(!file_exists($__script))
			{
				$this->EchoOutput("\n".'Test driver script '.$__script.' does not exist.'."\n".str_repeat('_',80)."\n");
				continue;
			}
			if(!IsSet($this->tests[$__name]['class']))
			{
				$this->error = 'the class for the test '.$__name.' is missing';
				return false;
			}
			$__class=$this->tests[$__name]['class'];
			$this->EchoOutput('Test "'.$__name.'": ... ');
			$this->FlushOutput();
			if(IsSet($this->tests[$__name]['options']))
				$__test_options=$this->tests[$__name]['options'];
			else
				$__test_options=array();
			if(IsSet($this->tests[$__name]['clear']))
			{
				for($__p=0; $__p<count($this->tests[$__name]['clear']); $__p++)
				{
					$__k=$this->tests[$__name]['clear'][$__p];
					Unset($_POST[$__k]);
					if(IsSet($HTTP_POST_VARS))
						Unset($HTTP_POST_VARS[$__k]);
					if(IsSet($GLOBALS))
						Unset($GLOBALS[$__k]);
					Unset($$__k);
				}
			}
			if(IsSet($this->tests[$__name]['post']))
			{
				$_POST=$HTTP_POST_VARS=$this->tests[$__name]['post'];
				$_GET=$HTTP_GET_VARS=array();
				$_SERVER['REQUEST_METHOD']='POST';
			}
			else
			{
				$_POST=$HTTP_POST_VARS=$_GET=$HTTP_GET_VARS=array();
				$_SERVER['REQUEST_METHOD']='GET';
			}
			if(!class_exists($__class))
				require($__script);
			if(!class_exists($__class))
			{
				$this->error = 'class '.$__class.' is not available to perform test '.$__name;
				return false;
			}
			$driver = new $__class;
			$driver->name = $__name;
			$driver->options = $this->options;
			if(!$driver->Initialize()
			|| !$driver->Process()
			|| !$driver->Finalize(true))
			{
				$this->error = $driver->error;
				return false;
			}
			$output = $driver->Output();
			if(substr($output, 0, strlen($__information_text)) === $__information_text)
			{
				$this->EchoOutput($output."\n");
				$__information++;
			}
			elseif(substr($output, 0, strlen($__disabled_text)) === $__disabled_text)
			{
				$this->EchoOutput($output."\n");
				$__disabled++;
			}
			else
			{
				if(IsSet($this->tests[$__name]['expectedfile']))
					$expected_file = $this->tests[$__name]['expectedfile'];
				else
					$expected_file = null;
				if(IsSet($expected_file))
				{
					$expected=$expected_file;
					if(!file_exists($expected))
					{
						$this->EchoOutput("\n".'Expected output file '.$expected.' does not exist.'."\n".str_repeat('_',80)."\n");
						continue;
					}
				}
				elseif(IsSet($this->tests[$__name]['nooutput'])
				&& $this->tests[$__name]['nooutput'])
				{
					$expected = '';
				}
				else
				{
					$this->EchoOutput("\n".'It was not specified a means to check the test script output.'."\n".str_repeat('_',80)."\n");
					continue;
				}
				if($expected !== $output)
				{
					$generated=$this->GetGeneratedPath($__name);
					if(!($file = fopen($generated, 'wb')))
					{
						$this->error = 'Could not create the generated output file '.$generated."\n";
						return false;
					}
					if((strlen($output)
					&& !fputs($file, $output))
					|| !fclose($file))
					{
						$this->error = 'Could not save the generated output to the file '.$generated."\n";
						return false;
					}
					if(strlen($expected))
					{
						$diff=array();
						exec('diff '.$expected.' '.$generated, $diff);
					}
					else
					{
						$output = file_get_contents($generated);
						if($output === false)
						{
							$this->EchoOutput("\n".'It was not to read the generated file '.$generated."\n".str_repeat('_',80)."\n");
							continue;
						}
						if($output === '')
							$diff = array();
						else
							$diff = preg_split("/\n|(\r\n)|\r/", $output);
					}
					if(count($diff))
					{
						$this->EchoOutput("FAILED\n");
						if(strlen($expected))
							$this->EchoOutput('The output of script '.$__script.' is different from the expected file '.$expected." .\n");
						else
							$this->EchoOutput('The output of script '.$__script.' is not empty as expected.'."\n");
						$this->EchoOutput(str_repeat('_',80)."\n");
						for($line=0; $line<count($diff); $line++)
							$this->EchoOutput($diff[$line]."\n");
						$this->EchoOutput(str_repeat('_',80)."\n");
						$this->FlushOutput();
						$__different++;
					}
					else
						$this->EchoOutput("OK\n");
				}
				else
						$this->EchoOutput("OK\n");
			}
			$__checked++;
		}
		$this->EchoOutput($__checked.' test '.($__checked==1 ? 'was' : 'were').' performed, '.($__checked!=$__test ? (($__test-$__checked==1) ? ' 1 test was skipped, ' : ($__test-$__checked).' tests were skipped, ') : '').($__different ? $__different.' failed, ' : 'none has failed, ').($__disabled ? $__disabled.' disabled' : 'none is disabled').'.'."\n");
		if($this->html)
			$this->EchoOutput('</body></html>', false);
		return true;
	}

	public function Finalize()
	{
		return true;
	}

	public function Output()
	{
		return $this->results;
	}
};
?>