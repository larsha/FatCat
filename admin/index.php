<?php
	$ms = microtime();

	require_once("../core/settings.php");

	use Core\Template\Template;
	use Core\Model\Model;

	// Build menu
	$menu = array();
	foreach( Model::GetModelsHierarchy() AS $module => $files )
	{
		foreach( $files AS $file )
		{
			try
			{
				$classname = "Model\\$module\\$file";

				/** @var $class Model */
				$class = new $classname();
				$hierarchy = $class->GetClassHierarchy();

				$menu[$hierarchy[1]][] = array(
					"title" => $hierarchy[2],
					"url" => "/admin/$module/".strtolower( $hierarchy[2] )."/"
				);
			}
			catch( Exception $e ){}
		}
	}

	// Load model
	$url = explode( "?", $_SERVER["REQUEST_URI"] );
	$data = NULL;

	if( $url[0] == "/admin" )
	{
		$controller = new \Controller\Core\Admin();
	}
	else
	{
		$controller = new \Controller\Core\Data();

		// Get requested url
		$model = explode( "/", $url[0] );

		try
		{
			$classname = "Model\\$model[2]\\$model[3]";

			/** @var $class Model */
			$class = new $classname();

			if( $class instanceof Model )
				$data = $class->select->QueryGetData();
		}
		catch( Exception $e ){}
	}

	$headers = array();
	if( is_array( $data ) )
	{
		foreach( $data[0] AS $key => $value )
			$headers[] = $key;
	}

	$content = array(
		"title" => "Admin",
		"menu" => $menu,
		"data" => $data,
		"headers" => $headers
	);

	if( !$data )
		$content["body"] = "This is the admin directory.";

	// Process template
	$template = new Template( $controller->GetView() );
	$template->SetVars( $content );
	echo $template->Process();

	echo "\n<!-- Page loaded in: ".round( microtime() - $ms, 5 )." ms -->";