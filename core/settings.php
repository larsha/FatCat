<?php
	require_once("__autoload.php");

	/**
	 * The root directory in the file system tree of Ninja catalog.
	 * Example: /var/www/Ninja/
	 */
	define( "ninja_root_dir", "" );

	/**
	 * Database server to connect to. "localhost" or IP address.
	 */
	define( "ninja_db_server", "" );

	/**
	 * Valid options are: sqlite, mysqli
	 */
	define( "ninja_db_type", "sqlite" );

	/**
	 * Database name or, if database is type sqlite, the directory of the database file.
	 */
	define( "ninja_db_name", "" );

	/**
	 * Enables or disables admin. Admin is located at http://www.domain.com/admin
	 */
	define( "ninja_enable_admin", false );

	// Initialize database connection
	if( ninja_db_server )
		Core\Db\Connect::Instance()->Initialize();