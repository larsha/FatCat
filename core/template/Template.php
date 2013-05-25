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
				// Replace arrays
				if( is_array( $data ) )
				{
					if( !$this->IsMultidimensionalArray( $data ) )
						$this->ReplaceArray( $key, $data );
				}
				// Replace objects
				elseif( is_object( $data ) )
				{
					$this->ReplaceObject( $key, $data );
				}
				// Replace strings
				else
				{
					$this->ReplaceString( $key, $data );
				}
			}

			$this->ReplaceMultidimensionalArray();

			// Replaces statements
			$this->ReplaceStatements();

			// Clean up code
			$this->CleanUp();

			return $this->content;
		}

		private function CleanUp()
		{
			// Remove unused variables
			$this->content = preg_replace( "/{{[a-z \.=>]+}}/is", "", $this->content );
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
			if( preg_match( "/import (View\\\[a-z\\\]+)|(\"(.+\.tpl)\")/is", $this->content, $matches ) )
			{
				// Get class hierarchy
				$hierarchy = explode( "\\", $matches[1] );

				$file = ( isset( $matches[3] ) ) ? $matches[3] : "../view/".strtolower( $hierarchy[1] )."/".strtolower( $hierarchy[2] ).".tpl";

				// Process template
				$template = new Template( $file );
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
		private function ReplaceArray( $key, array $data )
		{
			foreach( $data AS $itemKey => $item )
				$this->content = str_ireplace( "{{".$key.".".$itemKey."}}", $item, $this->content );
		}

		/**
		 * @param string $key
		 * @param array $data
		 */
		private function ReplaceMultidimensionalArray()
		{
			$re = '% # Match outermost {{FOR}}...{{ENDFOR}} structure.
				{{FOR\ ([a-z]+)\ AS\ ([a-z]+)}}              # Literal start tag.
				(                  # $1: Element contents.
				  (?:              # Zero or more contents alternatives.
					[^{{]*          # Either non-[b]...[/b] stuff...
					(?:            # Begin {(special normal*)*}.
					  {{           # {special} Tag open literal char,
					  (?!ENDFOR|FOR\ [a-z]+\ AS\ [a-z]+}})    # but only if NOT [b] or [/b].
					  [^{{]*        # More {normal*}.
					)*             # Finish {(special normal*)*}.
				  | (?R)           # Or a nested [b]...[/b] structure.
				  )*               # Zero or more contents alternatives.
				)                  # $1: Element contents.
				{{ENDFOR}}            # Literal end tag.
			%x';

			if( !preg_match_all( $re, $this->content, $matches ) )
				return;

			foreach( $matches[1] AS $i => $key )
			{
				if( !array_key_exists( $key, $this->vars ) )
					continue;

				$content = "";
				$replace = array();

				foreach( $this->vars[$key] AS $j => $vars )
				{
					$replace[$j] = $matches[3][$i];

					if( is_array( $vars ) )
						foreach( $vars AS $varKey => $var )
							$replace[$j] = str_ireplace( "{{".$matches[2][$i].".".$varKey."}}", $var, $replace[$j] );
				}

				$this->content = str_replace( $matches[0][$i], implode( "", $replace ), $this->content );
			}
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

		/**
		 * @param array $array
		 * @return bool
		 */
		private function IsMultidimensionalArray( array $array )
		{
			foreach( $array AS $item )
				if( is_array( $item ) )
					return true;

			return false;
		}
	}