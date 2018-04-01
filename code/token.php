<?php

// This file contains the Token class
// declares the different types of tokens
// and function to get what type of a token it is based off the character type
// then a instance of this class is added to the end of the List of tokens in the Lexer

	namespace Lex {

	// class to Hold each Indiividual Token
	class Token {
		// Token types see getCharacterType()
		const TOKEN_NOTHING = 0; // value nothing
		const TOKEN_ID = 1; // Token is a Identifier
		const TOKEN_STRING = 2; // Token is a string
		const TOKEN_NUMERIC = 3; // token is numerical (number)
		const TOKEN_WHITESPACE = 4;// Token is whitespace
		const TOKEN_SYMBOL = 5; // Token is a symbol or operator
		const TOKEN_HEX = 6; // Token is a hex value
		const TOKEN_CHAR = 7;// Token is a character string
		const TOKEN_CHSTR = 8; // Token is a single quoted string

		public $token, $token_type; // the token and its type
		// token type string id to printing out
		public $type_text = array("Nothing", "Token ID", "String", "Numeric", "Whitespace", "Symbol", "Hex", "Char", "Quote");
			
		// class constructor takes token and token type
		function __construct($token, $token_type) {
			$this->token = $token;
			$this->token_type = $token_type;
		}	
		
		// print the token 
		function printToken() {
			echo "[Token: " . $this->token . " ID: " . $this->type_text[$this->token_type] . "]<br>\n";
		}
		
		// get the token
		function getToken() { return $this->token; }
		// get its type
		function getType() { return $this->token_type; }
		// get its type as string
		function getTypeString() { return $this->type_text[$this->token_type]; }
	}

	// this function witll identify what type of token a character is and return its Token Type
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

