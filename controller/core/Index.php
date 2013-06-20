<?php
	namespace Controller\Core;

	use Core\Controller\Controller;

	class Index extends Controller
	{
		public function GetData()
		{
			return array(
				"title" => "Fat Cat",
				"body" => "Lightweight, easy to use and fast progress with your projects."
			);
		}
	}