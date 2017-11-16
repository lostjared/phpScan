
<?php

include 'lexer.php';

	if(isset($_POST["code"])) {
		$str = $_POST["code"];
	} else {
		$str = "";
	}
	$lexer = new Lexer($str);
	$lexer->Lex();
?>

<!DOCTYPE html>
<html>
<head>
<title>
Lexer
</title>

<style>
.linenumber { background-color: rgb(0,100,200); }
.lineindex_color { color: rgb(255,255,255); background-color: #000000; }
.linetype { color: rgb(100,0,255); background-color: rgb(25,255,100); }
.linenum { color: rgb(50,255,50); background-color: #888888; }
.codetext { color: #000000; background-color: rgb(255,254,208);}
.codekeyword { color: rgb(0,0,255); font-weight: bold; }
.codestring { color: rgb(255,0,255);background-color: rgb(200, 200,200); }
.codesymbol { color: rgb(76,255,198); background-color: rgb(100,100,100); }
.codedigit { color: rgb(0,255,186); background-color: rgb(0,0,0); }
.codechar { color: rgb(187,212,249); background-color: rgb(94,0,167); }
</style>
</head>
<body>
<?php 

echo "<form action=\"tokenizer.php\" method=\"POST\">";
echo "<textarea name=\"code\" rows=\"20\" cols=\"100\">" . $str . "</textarea><br>\n";
echo "<input name\"sub\" type=\"submit\" />\n";
echo "</form><br>";


function outputTable() {
	
	global $lexer;
	
	echo "<table border=\"1\" cellspacing=\"4\" cellpadding=\"4\"><tr style=\"background-color: rgb(150, 150, 150);\"><td class=\"linenumber\"><b>Index</b></td><td class=\"linenumber\"><b>Token</b></td><td class=\"linenumber\"><b>Type</b></td></tr>\n";
	for($i = 0; $i < count($lexer->lex_tokens); $i++) {
		$tokenvar = $lexer->lex_tokens[$i];
		$output_type = convertTypeToCSS($tokenvar);
		$output_type_string = $tokenvar->getTypeString();
		if($tokenvar->getType() == Token::TOKEN_ID && $lexer->isKeyword($tokenvar->token)) {
			$output_type = "codekeyword";
			$output_type_string = "Keyword";
		}
		
		echo "<tr><td class=\"lineindex_color\">" . ($i+1) . "</td><td class=\"" . $output_type . "\">" . htmlentities($tokenvar->token) . "</td><td class=\"linetype\">" . $output_type_string . "</td></tr>";
	}
	echo "</table>";
}

if(strlen($str)) {
outputTable();
}

?>
</body>
</html>