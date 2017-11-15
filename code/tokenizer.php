
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
</head>
<body>
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
<?php 

echo "<form action=\"tokenizer.php\" method=\"POST\">";
echo "<textarea name=\"code\" rows=\"20\" cols=\"100\">" . $str . "</textarea><br>\n";
echo "<input name\"sub\" type=\"submit\" />\n";
echo "</form>";


function outputTable() {
	
	global $lexer;
	
	echo "<table border=\"1\" cellspacing=\"4\" cellpadding=\"4\"><tr style=\"background-color: rgb(150, 150, 150);\"><td class=\"linenumber\"><b>Index</b></td><td class=\"linenumber\"><b>Token</b></td><td class=\"linenumber\"><b>Type</b></td></tr>\n";
	for($i = 0; $i < count($lexer->lex_tokens); $i++) {
		$tokenvar = $lexer->lex_tokens[$i];
		echo "<tr><td class=\"lineindex_color\">" . ($i+1) . "</td><td class=\"" . convertTypeToCSS($tokenvar) . "\">" . convertToHTML($tokenvar->token) . "</td><td class=\"linetype\">" . $tokenvar->getTypeString() . "</td></tr>";
	}
	echo "</table>";
}

outputTable();

?>
</body>
</html>