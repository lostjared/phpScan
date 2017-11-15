<?php

include 'token.php';

	class Lexer {
		protected $code;
		protected $index;
		protected $tokens;
		var $token_index;
		const CHAR_NULL = 255;
		var $op = array("==", "+=", "-=", "*=", "/=", "^=", "!=", "&=", "|=", "++", "--", "||", "&&", "%=", ">>","<<", "::","->", "=>", ".=", "<?", "?>", "//", "/*", "*/");
		var $lex_tokens;
		
		function __construct($code) {
			$this->code = $code;
			$this->index = 0;	
			$this->token_index = 0;
			$this->lex_tokens = array();
		}
		
		function getChar() {
			if($this->index < strlen($this->code)) {
				$ch = $this->code[$this->index];
				$this->index ++;
				return $ch;	
			}
			return Lexer::CHAR_NULL;
		}
		
		function getCurrentChar() {
			if($this->index < strlen($this->code))  {
				return $this->code[$this->index];	
			}
			return Lexer::CHAR_NULL;
		}
		
		function Lex() {
		while($this->index < strlen($this->code)) {
				$ch = $this->getChar();					
				if($ch == Lexer::CHAR_NULL) break;
				$ch_type = getCharacterType($ch);
				if($ch_type == Token::TOKEN_WHITESPACE) continue;
				switch($ch_type) {
					case Token::TOKEN_CHAR:
					$this->putBack();
					$this->getId();
					break;
					case Token::TOKEN_STRING:
					$this->putBack();
					$this->getString();
					break;
					case Token::TOKEN_NUMERIC:
					$this->putBack();
					$this->getNumeric();
					break;
					case Token::TOKEN_SYMBOL:
					$this->putback();
					$this->getSymbol();
					break;
					default:
					continue;
				}
			}			
		}
		
		function putBack() {
			$this->index--;	
		}
		
		function getString() {
			$this->getChar(); // eat quote character
			$ch = $this->getChar();
			$ttype = getCharacterType($ch);
			$lex_string = "";
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
			
			$this->lex_tokens[$this->token_index++] = new Token($lex_string, Token::TOKEN_STRING);
		}
		
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
							
			if($lex_string == "//") {
				$ch = $this->getChar();;
				while($ch != "\n" && $ch != Lexer::CHAR_NULL) {
					$ch = $this->getChar();	
				}
				return;
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
			if($lex_string == "//") {
				$ch = $this->getChar();;
				while($ch != "\n" && $ch != Lexer::CHAR_NULL) {
					$ch = $this->getChar();	
				}
				return;
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
			
			$this->lex_tokens[$this->token_index++] = new Token($lex_string, Token::TOKEN_SYMBOL);
		}
		
		function getNumeric() {
			$ch = $this->getChar();
			$ttype = getCharacterType($ch);
			$lex_string = $ch;
			
			while(($ttype == Token::TOKEN_NUMERIC || $ch == '.') &&  $ch != Lexer::CHAR_NULL) {
				$ch = $this->getChar();
				$ttype = getCharacterType($ch);
				if($ch != Lexer::CHAR_NULL && $ttype == Token::TOKEN_NUMERIC)
				$lex_string .= $ch;	
			}
					
			$this->lex_tokens[$this->token_index++] = new Token($lex_string, Token::TOKEN_NUMERIC);
			if($ch != Lexer::CHAR_NULL) $this->putBack();
		}
		
		function getId() {
			$ch = $this->getChar();
			$ttype = getCharacterType($ch);
			$string_token = "";	
			while($ttype == Token::TOKEN_CHAR && $ch != Lexer::CHAR_NULL) {
				$string_token .= $ch;
				$ch = $this->getChar();
				$ttype = getCharacterType($ch);
			}
			$this->lex_tokens[$this->token_index++] = new Token($string_token, Token::TOKEN_ID);
			if($ch != Lexer::CHAR_NULL) {
				$this->putBack();
			}
		}
		
		function debugTokens() {
			echo "Token count: " . $this->token_index . "<br>";
			for($i = 0; $i < $this->token_index; $i++) 
				$this->lex_tokens[$i]->printToken();		
		}
	}
	
	function convertTypeToCSS($type) {
			switch($type->token_type) {
				case Token::TOKEN_NOTHING:
				break;
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
	
	function convertToHTML($s) {
		$total = "";
		for($i = 0; $i < strlen($s); $i++) {
			if($s[$i] == '<') {
				$total .= "&lt;";
			} else if($s[$i] == '>') {
				$total .= "&gt;";
			} else {
				$total .= $s[$i];
			}	
		}
		return $total;	
	}
?>