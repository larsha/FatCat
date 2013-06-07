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

			$this->ReplaceMultidimensionalArray( $this->content );

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
		 * @param string $content
		 * @return bool|array
		 */
		private function MatchArray( $content )
		{
			$re = '% # Match outermost {{FOR}}...{{ENDFOR}} structure.
				{{FOR\ ([a-z]+)\ AS\ (([a-z]+)\ =>\ )?([a-z]+)}}              # Literal start tag.
				(                  # $1: Element contents.
				  (?:              # Zero or more contents alternatives.
					[^{{]*          # Either non-{{FOR}}...{{ENDFOR}} stuff...
					(?:            # Begin {(special normal*)*}.
					  {{           # {special} Tag open literal char,
					  (?!ENDFOR|FOR\ [a-z]+\ AS\ ([a-z]+ =>\ )?[a-z]+}})    # but only if NOT {{FOR}} or {{ENDFOR}}.
					  [^{{]*        # More {normal*}.
					)*             # Finish {(special normal*)*}.
				  | (?R)           # Or a nested {{FOR}}...{{ENDFOR}} structure.
				  )*               # Zero or more contents alternatives.
				)                  # $1: Element contents.
				{{ENDFOR}}            # Literal end tag.
			%x';

			if( !preg_match_all( $re, $content, $matches ) )
				return false;

			return $matches;
		}

		/**
		 * @param string $content
		 */
		private function ReplaceMultidimensionalArray( $content, $var = "" )
		{
			if( $matches = $this->MatchArray( $content ) )
			{
				foreach( $matches[5] AS $i => $text )
				{
					$var = ( preg_match( "/{{([\w]+)}}.+{{ENDFOR}}/s", $text, $vars ) ) ? $vars[1] : "";
					$key = $matches[1][$i];

					if( array_key_exists( $key, $this->vars ) )
					{
						$body = "";

						if( is_array( $this->vars[$key] ) )
						{
							foreach( $this->vars[$key] AS $items )
							{
								$replace = $text;
								foreach( $items AS $itemKey => $value )
								{
									if( $var )
									{
										$replace .= str_ireplace( "{{".$var."}}", $value, $text );
									}
									else
										$replace = preg_replace( "/{{(".$matches[4][$i].".)?".$itemKey."}}/", $value, $replace );
								}

								$body .= $replace;
							}
						}
						else
							$this->ReplaceString( $key, $this->vars[$key] );

						$this->content = str_ireplace( $matches[0][$i], $body, $this->content );
					}

					$this->ReplaceMultidimensionalArray( $text, $var );
				}
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