<!DOCTYPE html>
<html lang="pt-br">
<head>
	<meta charset="UTF-8"/>
    <meta HTTP-EQUIV='refresh' CONTENT='1;URL=PaineldeSenha.php'>
	<title>Amorim Ferragens Ltda - Sistema de senhas</title>
	<style>
		body {
			background-color: yellow;
		}
		h1{
			font-family: Arial;
			font-size: 100pt;
			color: Blue;
			text-shadow: 2px 2px 2px black;
			text-align:Center;
			margin-top: 0px;
			margin-bottom: 0px;
		}
		h2{
			font-family: Arial;
			font-size: 500pt;
			color: Red;
			text-shadow: 2px 2px 2px black;
			text-align:Center;
			margin-top: -100px;
			margin-bottom: 0px;
		}
		
	</style>	
</head>
<body>
	<h1>Senha</h1>
	<h2><?php
	$ini = parse_ini_file('PaineldeSenha.ini', true);
	$dir = $ini['DIR']['dir_painelsenha'];

// Abre o Arquivo no Modo r (para leitura)
$arqtexto = fopen ($dir.'senhabalcao.txt', 'r');
if ($arqtexto == false) {
	$arqtexto = fopen ($dir.'senhabalcao.txt', 'x');
	fwrite ($arqtexto,'1');
	echo 1;
} else {
	while(!feof($arqtexto))
	{
		$numsenha = rtrim(fread($arqtexto, 1024));
		echo $numsenha;
	}
}
fclose($arqtexto);
?></h2>
</body>
</html>