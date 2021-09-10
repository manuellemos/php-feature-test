<?php
/*
 * test.php
 *
 * @(#) $Id: test.php,v 1.12 2021/09/09 10:19:14 mlemos Exp $
 *
 */
	require('feature_test.php');

	/*
	 * Create the feature test main object
	 */
	$feature_test = new feature_test_class;

	/*
	 * Set the configuration file that defines all the supported tests
	 * You can create a new configuration file to perform other tests that
	 * are not yet implemented by the current feature test driver classes.
	 */
	$feature_test->configuration_file = 'feature_test_configuration.json';

	/*
	 * Set the path of the feature test driver classes
	 */
	$feature_test->drivers_path = 'drivers';

	/*
	 * Set the html variable to determine whether you want the test
	 * results to be outputted in plain text or HTML.
	 */
	$feature_test->html = defined('__HTML');

	/*
	 * Setup an options object with custom properties that will be used to
	 * configure the driver classes.
	 */
	$options = new stdClass;
	$options->database_type = 'mysqli';
	$options->database_user = 'root';
	$options->database_password = 'mysql password';
	$options->database_host = 'localhost';
	$options->database_name = 'database name';
	$options->database_options = array();
	$options->cipher = 'bf-ecb';
	$options->ssl_url = 'https://www.phpclasses.org/';
	$options->upload_progress = true;

	$feature_test->options = $options;

	/*
	 * If you want to run this test script from the command line shell,
	 * you can pass the names of the tests that you want to perform,
	 * just in case you just to perform some of the available tests.
	 */
	if(IsSet($_SERVER['argv'])
	&& GetType($_SERVER['argv']) == 'array'
	&& Count($_SERVER['argv']) > 1)
	{
		$feature_test->test_list = $_SERVER['argv'];
		array_shift($feature_test->test_list);
	}
	else
		$feature_test->test_list = array();

	/*
	 * Call the class and output the results depending on whether the
	 * tests failed or not.
	 */
	if(!$feature_test->Initialize()
	|| !$feature_test->Process()
	|| !$feature_test->Finalize())
		echo 'Failed tests: '.$feature_test->error."\n";
	else
		echo $feature_test->Output();
?>