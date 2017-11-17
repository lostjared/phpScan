<?php
	namespace Lex {

	class Token {
		const TOKEN_NOTHING = 0;
		const TOKEN_ID = 1;
		const TOKEN_STRING = 2;
		const TOKEN_NUMERIC = 3;
		const TOKEN_WHITESPACE = 4;
		const TOKEN_SYMBOL = 5;
		const TOKEN_HEX = 6;
		const TOKEN_CHAR = 7;
		const TOKEN_CHSTR = 8;

		public $token, $token_type;
		public $type_text = array("Nothing", "Token ID", "String", "Numeric", "Whitespace", "Symbol", "Hex", "Char", "Quote");
			
		
		function __construct($token, $token_type) {
			$this->token = $token;
			$this->token_type = $token_type;
		}	
		
		function printToken() {
			echo "[Token: " . $this->token . " ID: " . $this->type_text[$this->token_type] . "]<br>\n";
		}
		
		function getToken() { return $this->token; }
		function getType() { return $this->token_type; }
		function getTypeString() { return $this->type_text[$this->token_type]; }
	}


	function getCharacterType($token) {
		
			if( ($token >= 'a' && $token <= 'z') || ($token >= 'A' && $token <= 'Z') || ($token == '_')) {
				return Token::TOKEN_CHAR; 
			} 
			if($token >= '0' && $token <= '9') {
				return Token::TOKEN_NUMERIC;
			}
			if($token == ' ' || $token == '\n' || $token == '\t' || $token == '\r') {
				return Token::TOKEN_WHITESPACE;	
			}		
			switch($token) {
				case '!':
				case '@':
				case '#':
				case '$':
				case '%':
				case '^':
				case '&':
				case '*':
				case '(':
				case ')':
				case '[':
				case ']':
				case '{':
				case '}':
				case '-':
				case '+':
				case '=':
				case '<':
				case '>':
				case ',':
				case '.':
				case ';':
				case ':';
				case '?':
				case '/':
				case '\\':
				case '~':
				case '`':
				return Token::TOKEN_SYMBOL;
				case "'":
				return Token::TOKEN_CHSTR;
				case "\"":
				return Token::TOKEN_STRING;
				break;			
			}
		}
	}

