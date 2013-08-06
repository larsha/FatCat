<?php
	require_once("settings.php");

	use Core\Db\Table;

	// No database defined
	if( !fatcat_db_name )
		throw new ErrorException( "No database defined in settings.php" );

	/**
	 * @param $resource
	 * @return array
	 */
	function getContents( $resource )
	{
		if( !is_resource( $resource ) )
			return array();

		$contents = array();

		while( ( $dir = readdir( $resource ) ) !== false )
		{
			if( $dir == "." || $dir == ".." )
				continue;

			$contents[] = $dir;
		}

		return $contents;
	}

	// Get all model dirs
	$root = fatcat_root_dir."model";

	$models = getContents( opendir( $root ) );

	$instances = array();

	// Loop model dirs
	foreach( $models AS $model )
	{
		if( !is_dir( $root."/".$model ) )
			continue;

		// Get model dirs content
		$files = getContents( opendir( $root."/".$model ) );

		// Loop content
		foreach( $files AS $file )
		{
			// Remove suffix and create namespace string
			$table = str_replace( ".php", "", $file );
			$class = "Model\\$model\\$table";

			/** @var $instance \Core\Model\Model */
			$instances[] = new $class();
		}
	}

	$instances = array_merge( $instances, array(
		new \Core\User\Session()
	) );

	/** @var $instance \Core\Model\Model */
	foreach( $instances AS $instance )
	{
		// Check if table exists
		if( Table::Exists( $instance->GetTableName() ) )
			continue;

		// Create table
		$db = new Table( $instance->GetTableName() );

		// Loop models fields
		foreach( $instance->Fields() AS $field )
		{
			list( $type, $name, $value, $args ) = $field;

			$db->Field( $type, $name, $args );
		}

		// Query database
		$db->Query();
	}