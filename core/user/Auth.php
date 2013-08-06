<?php
	namespace Core\User;

	use Core\Db\Core;
	use Core\Db\Type;

	class Auth
	{
		/**
		 * @param int $userId
		 */
		public static function CreateSession( $userId )
		{
			$ip = preg_replace( '/[^0-9]/', "", $_SERVER["REMOTE_ADDR"] );
			$timestamp = time();

			$session = new Session();
			$session->insert->Field( Type::Int, "ip", $ip )
					->Field( Type::Int, "timestamp", $timestamp )
					->Field( Type::Int, "user_id", $userId )
					->Query();

			$auth = array(
				Core::LastInsertId(), 	// Session ID
				$ip, 					// Users IP-address
				$timestamp 				// Login timestamp
			);

			setcookie( "auth", base64_encode(
				implode( "-", $auth )."-".
				sha1( implode( "-", $auth )."-".fatcat_site_random ) 	// Validation hash with som parameters and something unique to this site.
			) );
		}

		public static function DestroySession()
		{
			// TODO: Check, doesn't work.
			setcookie( "auth", false, 0 );
		}

		/**
		 * @param mixed $session $_COOKIE["auth"]
		 * @return bool
		 */
		public static function UserIsLoggedIn( $session )
		{
			$auth = explode( "-", base64_decode( $session ) );

			// Unique string does not match. Someone tried to create their own cookie.
			if( $auth[3] != sha1( $auth[0]."-".$auth[1]."-".$auth[2]."-".fatcat_site_random ) )
				return false;

			// Session exists on server with session ID, IP and timestamp.
			if( Session::Exists( $auth[0], $auth[1], $auth[2] ) )
				return true;

			return false;
		}

		public static function RequireLogin()
		{
			if( !self::UserIsLoggedIn( $_COOKIE ) )
			{
				header( "Location: /login" );
				exit();
			}
		}
	}