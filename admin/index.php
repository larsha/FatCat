<?php
	$ms = microtime();

	require_once("../core/settings.php");

	use Core\Template\Template;
	use Core\Model\Model;
	use Core\Db\Type;
	use Core\User\Auth;

	$url = explode( "?", $_SERVER["REQUEST_URI"] );

	// Logout user
	if( $url[0] == "/admin/logout" )
	{
		Auth::DestroySession();

		header( "Location: /admin" );
		exit();
	}

	// Check for login
	if( !Auth::UserIsLoggedIn( $_COOKIE["auth"] ) )
	{
		if( isset( $_POST["username"] ) && isset( $_POST["password"] ) )
		{
			if( $_POST["username"] == fatcat_user_name && $_POST["password"] == fatcat_user_password )
			{
				Auth::CreateSession( 0 );

				header( "Location: /admin" );
				exit();
			}
		}

		$template = new Template( fatcat_root_dir."view/admin/login.tpl" );
		echo $template->Process();
		exit();
	}

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
	$data = NULL;

	if( $url[0] == "/admin" )
	{
		$controller = new \Controller\Core\Admin();
	}
	else
	{
		// Get requested url
		$model = explode( "/", $url[0] );

		try
		{
			$classname = "Model\\$model[2]\\$model[3]";

			/** @var $class Model */
			$class = new $classname();

			if( $class instanceof Model )
			{
				if( isset( $model[4] ) && intval( $model[4] ) > 0 )
					$class->select->WhereEquals( Type::Int, "id", $model[4] );
			}
		}
		catch( Exception $e ){}

		// Load proper controller
		if( isset( $model[4] ) && $model[4] != "" )
		{
			if( intval( $model[4] ) > 0 )
			{
				$controller = new \Controller\Core\Edit( array_merge( array( "id" => $model[4] ), $_REQUEST ), $class );
			}
			elseif( $model[4] == "new" )
			{
				$controller = new \Controller\Core\Edit( array_merge( array(), $_REQUEST ), $class );
			}
		}
		else
		{
			$controller = new \Controller\Core\Data( array(), $class );
		}
	}

	$content = array(
		"title" => "Admin",
		"menu" => $menu
	);

	$content = array_merge( $content, $controller->GetData() );

	if( $url[0] == "/admin" )
		$content["body"] = "This is the admin directory.";

	// Process template
	$template = new Template( $controller->GetView() );
	$template->SetVars( $content );
	echo $template->Process();

	echo "\n<!-- Page loaded in: ".round( microtime() - $ms, 5 )." ms -->";