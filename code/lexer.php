<?php

// How this code works is the Lexer is passed a string containing the code 
// it then is processed when the Lex function is called
// each character type is looked up (in Token.php) and based on what
// character type it is it enters a loop to collect those character and then adds to the list varaible of type Token containing the
// information for the token type (from the character type)

namespace Lex {
include_once 'token.php'; // include token source code

// class for the Lexer
	class Lexer {
		const CHAR_NULL = 255;
		protected $code;
		protected $index;
		protected $tokens;
		public $token_index;
		// known two character operators
		public $op = array("==", "+=", "-=", "*=", "/=", "^=", "!=", "&=", "|=", "++", "--", "||", "&&", "%=", ">>","<<", "::","->", "=>", ".=", "<?", "?>", "//", "/*", "*/");
		public $lex_tokens;
		// known keywords
		public $keywords = array("alignof", "and", "and_eq", "asm",
        "auto", "bitand", "bitor", "bool", "break", "case", "catch", "char",
        "char16_t", "char32_t", "class", "compl", "const", "constexpr",
        "const_cast", "continue", "decltype", "default", "delete", "do",
        "double", "dynamic_cast", "else", "enum", "explicit", "export",
        "extern", "false", "float", "for", "friend", "goto", "if", "inline",
        "int", "long", "mutable", "namespace", "new", "noexcept", "not",
        "not_eq", "nullptr", "operator", "or", "or_eq", "private", "protected",
        "public", "register", "reinterpret_cast", "return", "short", "signed",
        "sizeof", "static", "static_assert", "static_cast", "struct", "switch",
        "template", "this", "thread_local", "throw", "true", "try", "typedef", "typeid", "typename", "union", "unsigned", "using", "virtual", "void", "volatile", "wchar_t", "while", "xor", "xor_eq", "import","abstract","final","interface", "extends", "implements", "function");
		
		// class constructor pass it the code to lex
		function __construct($code) {
			$this->code = $code;
			$this->index = 0;	
			$this->token_index = 0;
			$this->lex_tokens = array();
		}
		
		// grab a single character from the code string
		function getChar() {
			if($this->index < strlen($this->code)) {
				$ch = $this->code[$this->index];
				$this->index ++;
				return $ch;	
			}
			return Lexer::CHAR_NULL;
		}
		
		// get the current char and not increment the position for the next char
		function getCurrentChar() {
			if($this->index < strlen($this->code))  {
				return $this->code[$this->index];	
			}
			return Lexer::CHAR_NULL;
		}
		// function that preforms the lexical code
		function Lex() {
			// while this index (current position) is less the length of the string passed in from constructor
		while($this->index < strlen($this->code)) {
				$ch = $this->getChar(); // grab character					
				if($ch == Lexer::CHAR_NULL) break; // if its te EOF character break loop
				$ch_type = getCharacterType($ch);//grab the character Type
				if($ch_type == Token::TOKEN_WHITESPACE) continue;// if it is whitespace continue (start from top of loop)
				switch($ch_type) {// switch based on what type of character it is
					case Token::TOKEN_CHAR: // if it is a character
					$this->putBack();
					$this->getId();// get Identifier
					break;
					case Token::TOKEN_STRING:// if it is a string
					$this->putBack();
					$this->getString();// grab double quote string
					break;
					case Token::TOKEN_CHSTR: // if it is a quote
					$this->putBack();
					$this->getStringQuote();//grab string quote
					break;
					case Token::TOKEN_NUMERIC:// if it is a numeric (integer or float)
					$this->putBack();
					$this->getNumeric();
					break;
					case Token::TOKEN_SYMBOL:// if is a operator or symbol
					$nch = $this->getChar();
					$ntype = getCharacterType($nch);
					
					if($ch == '-' && $ntype == Token::TOKEN_NUMERIC) {
						$this->putBack();
						$this->putBack();
						$this->getNumeric();
						break;	
					}
					$this->putBack();
					$this->putBack();
					$this->getSymbol();// grab symbol
					break;
					default:
					continue;
				}
			}			
		}
		
		// put back the char grabbed from getChar()
		function putBack() {
			$this->index--;	
		}
		
		// get a string token
		function getString() {
			$this->getChar(); // eat quote character
			$ch = $this->getChar();
			$ttype = getCharacterType($ch);
			$lex_string = "";
			//loop through and grab character until one is found that doesn't fit
			while($ttype != Token::TOKEN_STRING && $ch != Lexer::CHAR_NULL) {
				
				if($ch != Lexer::CHAR_NULL && $ch != "\\") {
					$lex_string .= $ch; 
				} else if($ch == "\\") {
					$ch = $this->getChar();
					if($ch != Lexer::CHAR_NULL)
						$lex_string .= "\\" . $ch;
				}
					
				$ch = $this->getChar();
				$ttype = getCharacterType($ch);
			}
			
			// set next token as new Token with string
			$this->lex_tokens[$this->token_index++] = new Token($lex_string, Token::TOKEN_STRING);
		}
		// get a single quote string
		function getStringQuote() {
			$this->getChar(); // eat quote character
			$ch = $this->getChar();
			$ttype = getCharacterType($ch);
			$lex_string = "";
			//loop through and grab character until one is found that doesn't fit
			while($ttype != Token::TOKEN_CHSTR && $ch != Lexer::CHAR_NULL) {
				
				if($ch != Lexer::CHAR_NULL && $ch != "\\") {
					$lex_string .= $ch; 
				} else if($ch == "\\") {
					$ch = $this->getChar();
					if($ch != Lexer::CHAR_NULL)
						$lex_string .= "\\" . $ch;
				}
					
				$ch = $this->getChar();
				$ttype = getCharacterType($ch);
			}
			// set next token as token of type quote string
			$this->lex_tokens[$this->token_index++] = new Token($lex_string, Token::TOKEN_CHSTR);
		}
		//loop through and grab character until one is found that doesn't fit
		function getSymbol() {
			$ch = $this->getChar();
			$ttype = getCharacterType($ch);
			$lex_string = "";
			if($ttype == Token::TOKEN_SYMBOL && $ch != Lexer::CHAR_NULL) {	
				$lex_string .= $ch;
				$ch = $this->getChar();
				if($ch != Lexer::CHAR_NULL) {
					$temp_string = $lex_string . $ch;
					$found = 0;
					for($i = 0; $i < count($this->op); $i++) {
						if($this->op[$i] == $temp_string) {
							$found = 1;
							break;	
						}
					}
					if($found == 1) {
						$lex_string .= $ch;
					}
					else {
						$this->putBack();
					}
							
			// remove C++ style comments
			if($lex_string == "//") {
				$ch = $this->getChar();
				while($ch != "\n" && $ch != Lexer::CHAR_NULL) {
					$ch = $this->getChar();	
				}
				return;
				// remove C style comments
			} else if($lex_string == "/*") {
				$ch = $this->getChar();
				while(1) {
					$c = $this->getChar();
					$ch .= $c;
					if($ch == "*/" || $ch == Lexer::CHAR_NULL) {
						return;
					}
					 else {
						$ch = $c;	
					}
				}
			} 			
				}
			}	
			// add token (operator) to list of tokens
			$this->lex_tokens[$this->token_index++] = new Token($lex_string, Token::TOKEN_SYMBOL);
		}
		
		// grab numeric token
		function getNumeric() {
			$ch = $this->getChar();
			$ttype = getCharacterType($ch);
			$lex_string = $ch;
			
			// loop until type is not a TOKEN_NUMERIC
			while(($ttype == Token::TOKEN_NUMERIC || $ch == '.' || $ch == '-') &&  $ch != Lexer::CHAR_NULL) {
				$ch = $this->getChar();
				$ttype = getCharacterType($ch);
				if($ch != Lexer::CHAR_NULL && ($ttype == Token::TOKEN_NUMERIC || $ch == '.' || $ch == '-'))
				$lex_string .= $ch;	
			}
					
			// add new token to end of list
			$this->lex_tokens[$this->token_index++] = new Token($lex_string, Token::TOKEN_NUMERIC);
			if($ch != Lexer::CHAR_NULL) $this->putBack();
		}
		
		// get identifier token
		function getId() {
			$ch = $this->getChar();
			$ttype = getCharacterType($ch);
			$string_token = "";	
			
			if($ttype != Token::TOKEN_CHAR) return;
			// loop until type is not a character or a digit
			while(($ttype == Token::TOKEN_CHAR || $ttype == Token::TOKEN_NUMERIC) && $ch != Lexer::CHAR_NULL) {
				$string_token .= $ch;
				$ch = $this->getChar();
				$ttype = getCharacterType($ch);
			}
			// add new token to end of the list
			$this->lex_tokens[$this->token_index++] = new Token($string_token, Token::TOKEN_ID);
			if($ch != Lexer::CHAR_NULL) {
				$this->putBack();
			}
		}
		
		// print out Debug tokens
		function debugTokens() {
			echo "Token count: " . $this->token_index . "<br>";
			for($i = 0; $i < $this->token_index; $i++) 
				$this->lex_tokens[$i]->printToken();		
		}
		// check if string is a keyword
		function isKeyword($v) {
			for ($i = 0; $i < count($this->keywords); $i++) {
				if($this->keywords[$i] == $v)
				return true;	
			}
			return false;
		}
	}
	// convert Code type to CSS id
	function convertTypeToCSS($type) {
			switch($type->token_type) {
				case Token::TOKEN_NOTHING:
				break;
				case Token::TOKEN_CHSTR:
				case Token::TOKEN_STRING:
				return "codestring";
				case Token::TOKEN_ID:
				return "codetext";
				case Token::TOKEN_NUMERIC:
				return "codedigit";
				case Token::TOKEN_CHAR:
				return "codechar";
				break;
			}
	}
	
}
	