<?php
	namespace Core\User;

	use Core\Db\Type;
	use Core\Model\Model;

	class Session extends Model
	{
		/**
		 * @param int $id
		 * @param string $ip
		 * @return mixed
		 */
		public static function Exists( $id, $ip, $timestamp )
		{
			$session = new self();

			$value = $session->select->WhereEquals( Type::Int, "id", $id )
					->WhereEquals( Type::Int, "ip", $ip )
					->WhereEquals( Type::Int, "timestamp", $timestamp )
					->QueryGetValue();

			return ( $value ) ? true : false;
		}

		public function __construct()
		{
			parent::__construct();

			$this->Field( Type::Int, "user_id", NULL, array( "foreign_key" => "Core\\User\\User" ) );
			$this->Field( Type::Int, "ip", NULL );
			$this->Field( Type::Int, "timestamp" );
		}
	}