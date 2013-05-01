<?php
	namespace Core\Template;

	use Core\Form\Form;

	class Template
	{
		private $content;
		private $file;
		private $vars;

		/**
		 * @param $file
		 */
		public function __construct( $file )
		{
			if( !file_exists( $file ) )
				throw new \ErrorException( "Template file $file is missing." );

			$this->file = $file;
			$this->vars = array();
		}

		/**
		 * @param array $vars
		 */
		public function SetVars( array $vars )
		{
			$this->vars = $vars;
		}

		public function Process()
		{
			// Load file contents
			$this->content = file_get_contents( $this->file );

			// Include linked template files
			if( $this->IncludeFile() )
				return $this->content;

			// Loop vars and replace content from file
			foreach( $this->vars AS $key => $data )
			{
				if( is_array( $data ) )
					$this->ReplaceArray( $key, $data );
				elseif( is_object( $data ) )
					$this->ReplaceObject( $key, $data );
				else
					$this->ReplaceString( $key, $data );
			}

			// Replaces statements
			$this->ReplaceStatements();

			// Clean up code
			$this->CleanUp();

			return $this->content;
		}

		private function CleanUp()
		{
			// Remove unused variables
			$this->content = preg_replace( "/{{[a-z \.]+}}/is", "", $this->content );
		}

		/**
		 * @return array
		 */
		private function GetReplaces()
		{
			$replaces = array();

			if( preg_match_all( "/{{REPLACE ([a-z]+)}}(.*?){{ENDREPLACE}}/is", $this->content, $matches ) )
			{
				foreach( $matches[1] AS $i => $key )
				{
					$replaces[$key] = $matches[2][$i];
				}
			}

			return $replaces;
		}

		/**
		 * @return bool
		 */
		private function IncludeFile()
		{
			if( preg_match( "/import (View\\\[a-z\\\]+)/is", $this->content, $matches ) )
			{
				// Get class hierarchy
				$hierarchy = explode( "\\", $matches[1] );

				// Process template
				$template = new Template( "../view/".strtolower( $hierarchy[1] )."/".strtolower( $hierarchy[2] ).".tpl" );
				$template->SetVars( array_merge( $this->GetReplaces(), $this->vars ) );
				$this->content = $template->Process();

				return true;
			}

			return false;
		}

		/**
		 * @param string $key
		 * @param array $data
		 */
		private function ReplaceArray( $key, $data )
		{
			// TODO: Add support for keys in regexp

			preg_match( "/{{FOR $key AS ([a-z]+)}}(.*?){{ENDFOR}}/is", $this->content, $matches );

			$body = "";
			foreach( $data AS $row )
			{
				$match = $matches[2];

				if( is_array( $row ) )
				{
					foreach( $row AS $itemKey => $item )
						$match = str_ireplace( "{{".$matches[1].".".$itemKey."}}", $item, $match );
				}
				else
				{
					$match = str_ireplace( "{{".$matches[1]."}}", $row, $match );
				}

				$body .= $match;
			}

			$this->content = str_replace( $matches[0], $body, $this->content );
		}

		/**
		 * @param string $key
		 * @param object $data
		 */
		private function ReplaceObject( $key, $data )
		{
			if( $data instanceof Form )
				$this->content = str_ireplace( "{{".$key."}}", $data->Generate(), $this->content );
		}

		/**
		 * @return void
		 */
		private function ReplaceStatements()
		{
			if( preg_match_all( "/{{IF ([a-z]+)}}(.*?){{ENDIF}}/is", $this->content, $matches ) )
			{
				foreach( $matches[2] AS $key => $match )
				{
					$items = explode( "{{ELSE}}", $match );

					// No else and var exist
					if( count( $items ) == 1 && array_key_exists( $matches[1][$key], $this->vars ) )
					{
						$this->content = str_ireplace( $matches[0][$key], $items[0], $this->content );
					}
					// Else and var exist
					elseif( array_key_exists( $matches[1][$key], $this->vars ) )
					{
						$this->content = str_ireplace( $matches[0][$key], $items[0], $this->content );
					}
					// Else and no var exist
					elseif( !array_key_exists( $matches[1][$key], $this->vars ) )
					{
						$this->content = str_ireplace( $matches[0][$key], $items[1], $this->content );
					}
				}
			}
		}

		/**
		 * @param string $key
		 * @param string $data
		 */
		private function ReplaceString( $key, $data )
		{
			$this->content = str_ireplace( "{{".$key."}}", $data, $this->content );
		}
	}