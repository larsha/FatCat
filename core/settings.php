<?php
	require_once("__autoload.php");

	/**
	 * The root directory in the file system tree of FatCat catalog.
	 * Example: /var/www/FatCat/
	 */
	define( "fatcat_root_dir", "/Users/rasmusbrandberg/FatCat/" );

	/**
	 * The base url this Fat Cat installation is hosting.
	 * Example: http://www.example.com/
	 */
	define( "fatcat_site_url", "" );

	/**
	 * Database server to connect to. "localhost" or IP address.
	 */
	define( "fatcat_db_server", "localhost" );

	/**
	 * Valid options are: sqlite, mysqli
	 */
	define( "fatcat_db_type", "mysqli" );

	/**
	 * Database name or, if database is type sqlite, the directory of the database file.
	 */
	define( "fatcat_db_name", "" );

	/**
	 * The user to connect to database with (not used with "sqlite").
	 */
	define( "fatcat_db_user", "root" );

	/**
	 * The password to connect to database with (not used with "sqlite").
	 */
	define( "fatcat_db_password", "" );

	/**
	 * Enables or disables admin. Admin is located at http://www.domain.com/admin
	 */
	define( "fatcat_enable_admin", false );

	/**
	 * Enables or disables debug mode.
	 */
	define( "fatcat_debug_mode", false );

	// Initialize database connection
	if( fatcat_db_server )
		Core\Db\Connect::Instance()->Initialize();