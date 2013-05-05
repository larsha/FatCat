<?php
	require_once("__autoload.php");

	/**
	 * The root directory in the file system tree of Ninja catalog.
	 * Example: /var/www/Ninja/
	 */
	define( "ninja_root_dir", "" );

	/**
	 * The base url his Ninja installation is hosting.
	 * Example: http://www.example.com/
	 */
	define( "ninja_site_url", "" );

	/**
	 * Database server to connect to. "localhost" or IP address.
	 */
	define( "ninja_db_server", "localhost" );

	/**
	 * Valid options are: sqlite, mysqli
	 */
	define( "ninja_db_type", "" );

	/**
	 * Database name or, if database is type sqlite, the directory of the database file.
	 */
	define( "ninja_db_name", "" );

	/**
	 * The user to connect to database with (not used with "sqlite").
	 */
	define( "ninja_db_user", "" );

	/**
	 * The password to connect to database with (not used with "sqlite").
	 */
	define( "ninja_db_password", "" );

	/**
	 * Enables or disables admin. Admin is located at http://www.domain.com/admin
	 */
	define( "ninja_enable_admin", false );

	/**
	 * Enables or disables debug mode.
	 */
	define( "ninja_debug_mode", false );

	// Initialize database connection
	if( ninja_db_server )
		Core\Db\Connect::Instance()->Initialize();