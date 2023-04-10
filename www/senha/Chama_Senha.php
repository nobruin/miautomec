<?php
	$habilita = "readonly";
	if (isset($_POST["acaoSenha"]) && ($_POST["acaoSenha"]!='')) {
		if ($_POST['acaoSenha'] == '+')
		{
		$ini = parse_ini_file('PaineldeSenha.ini', true);
		$dir = $ini['DIR']['dir_painelsenha'];

		$arqtexto = fopen ($dir.'senhabalcao.txt', 'r+');
		if ($arqtexto == false) {
			$senha = '1';
			} else {
				while(!feof($arqtexto))
				{
					$numsenha = rtrim(fread($arqtexto, 1024));
					$senha = (string)(((int)$numsenha) + 1);
					if ($senha == 1000)
					{
						$senha = '0';
					}
				}
			}
		fclose($arqtexto);
		$arqtexto = fopen ($dir.'senhabalcao.txt', 'w');
		fwrite($arqtexto, $senha);
		fclose($arqtexto);
		}
		else if ($_POST['acaoSenha'] == '-')
		{
		$ini = parse_ini_file('PaineldeSenha.ini', true);
		$dir = $ini['DIR']['dir_painelsenha'];

		$arqtexto = fopen ($dir.'senhabalcao.txt', 'r+');
		if ($arqtexto == false) {
			$senha = '1';
			} else {
				while(!feof($arqtexto))
				{
					$numsenha = rtrim(fread($arqtexto, 1024));
					$senha = (string)(((int)$numsenha) - 1);
					if ($senha == -1)
					{
						$senha = '0';
					}
				}
			}
		fclose($arqtexto);
		$arqtexto = fopen ($dir.'senhabalcao.txt', 'w');
		fwrite($arqtexto, $senha);
		fclose($arqtexto);
		}
		else if ($_POST['acaoSenha'] == 'o')
		{
		$senha = $_POST['senha'];
		$habilita = "readonly";
		$ini = parse_ini_file('PaineldeSenha.ini', true);
		$dir = $ini['DIR']['dir_painelsenha'];

		$arqtexto = fopen ($dir.'senhabalcao.txt', 'w');
		fwrite($arqtexto, $senha);	
		fclose($arqtexto);
		} else if ($_POST['acaoSenha'] == 'x')
		{
		$habilita = "";
		$senha = $_POST['senha'];
		$ini = parse_ini_file('PaineldeSenha.ini', true);
		$dir = $ini['DIR']['dir_painelsenha'];

		$arqtexto = fopen ($dir.'senhabalcao.txt', 'r');
		if ($arqtexto == false) {
			$arqtexto = fopen ($dir.'senhabalcao.txt', 'x');
			fwrite ($arqtexto,'1');
			$senha = '1';
		} else {
			while(!feof($arqtexto))
			{
				$numsenha = rtrim(fread($arqtexto, 1024));
				$senha = $numsenha;
			}
		}
		fclose($arqtexto);
		}
		}
		else
		{
		$ini = parse_ini_file('PaineldeSenha.ini', true);
		$dir = $ini['DIR']['dir_painelsenha'];

		$arqtexto = fopen ($dir.'senhabalcao.txt', 'r');
		if ($arqtexto == false) {
			$arqtexto = fopen ($dir.'senhabalcao.txt', 'x');
			fwrite ($arqtexto,'1');
			$senha = '1';
		} else {
			while(!feof($arqtexto))
			{
				$numsenha = rtrim(fread($arqtexto, 1024));
				$senha = $numsenha;
			}
		}
		fclose($arqtexto);
		}

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
	<meta charset="UTF-8"/>
<title>Chama senha</title>
	<style>
		body {
			//background-color: yellow;
		}
		h1{
			font-family: Arial;
			font-size: 20pt;
			color: Blue;
			text-shadow: 2px 2px 2px black;
			text-align:Left;
			margin-top: 0px;
			margin-bottom: 0px;
		}
		h2{
			font-family: Arial;
			font-size: 100pt;
			color: Red;
			text-shadow: 2px 2px 2px black;
			text-align:Left;
			margin-top: 0px;
			margin-bottom: 0px;
		}
		
	</style>	
</head>
<body>
	<form name="form_senha" id="form_senha" method="post" action="Chama_Senha.php">
	<h1>Senha</h1>
	<h2><input type="text" <?php echo $habilita;?> name="senha" id="senha" autocomplete="off" value="<?php echo $senha; ?>" style="border:0;font-family: Arial;font-size: 100pt;color: Red;text-shadow: 2px 2px 2px black;text-align:Left;margin-top: 0px;margin-bottom: 0px;"></h2>
	<input type="hidden" id="acaoSenha" name="acaoSenha" value="">
		<?php if (!(isset($_POST["acaoSenha"])) || ($_POST['acaoSenha'] != 'x'))
		{
			echo '<input type="submit" name="avanca" id="avanca" value="Avança" onclick="document.getElementById(&quot;acaoSenha&quot;).value=&quot;+&quot;;document.forms[&quot;form_senha&quot;].submit();return false;">&nbsp;&nbsp;&nbsp;'; 
			echo '<input type="button" name="volta" id="volta" value="Volta" onclick="document.getElementById(&quot;acaoSenha&quot;).value=&quot;-&quot;;document.forms[&quot;form_senha&quot;].submit();return false;">&nbsp;&nbsp;&nbsp;';
			echo '<input type="button" name="edita" id="edita" value="Edita" onclick="document.getElementById(&quot;acaoSenha&quot;).value=&quot;x&quot;;document.forms[&quot;form_senha&quot;].submit();return false;">';
		} else {
			echo '<input type="submit" name="ok" id="ok" value="OK" onclick="document.getElementById(&quot;acaoSenha&quot;).value=&quot;o&quot;;document.forms[&quot;form_senha&quot;].submit();return false;">&nbsp;&nbsp;&nbsp;';
			echo '<input type="button" name="cancela" id="cancela" value="Cancela" onclick="document.getElementById(&quot;acaoSenha&quot;).value=&quot;&quot;;document.forms[&quot;form_senha&quot;].submit();return false;">';
		} ?>
	</form>
</body>
</html>
<?php if ((isset($_POST["acaoSenha"])) && ($_POST['acaoSenha'] == 'x')) {
	echo '<script>document.forms["form_senha"]["senha"].focus();</script>';	
	echo '<script>document.forms["form_senha"]["senha"].select();</script>';	
} else {
	echo '<script>document.forms["form_senha"]["avanca"].focus();</script>';	
}
?>