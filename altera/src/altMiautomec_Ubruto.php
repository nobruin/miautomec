<?php
/**
 * Autor: Marluce Almeida
 * Data de criacao: 08/06/15
 * Hora de criacao: 10:00
 */
require_once("conexao_miautomec.php");
if (isset($_REQUEST['prodcod'])) {
    $FiltraProd = $_REQUEST['prodcod'];
} else {
    $FiltraProd = "";
}
if (isset($_REQUEST['pNovo'])) {
    $NovoPreco = $_REQUEST['pNovo'];
} else {
    $NovoPreco = "0";
}
if (isset($_REQUEST['bNovo'])) {
    $NovoBruto = $_REQUEST['bNovo'];
} else {
    $NovoBruto = "0";
}
if (isset($_REQUEST['jacalc'])) {
    $JaCalc = $_REQUEST['jacalc'];
} else {
    $JaCalc = "N";
}
?>
<html>
<head>
    <meta http-equiv="content-type" content="text/html;charset=UTF-8" />
    <meta charset="UTF-8" />
    <title>Sistema de Atualização de Preços - Amorim Ferragens</title>
    <script type="text/javascript" src="jquery/jquery-1.3.2.min.js"></script>
    <script src="jquery/jquery.maskMoney.js" type="text/javascript"></script>
    <style>
        .textocinza {
            color: gray;
        }
    </style>
</head>

<body onLoad="document.form_busca.prodcod.focus();verifConf();">
<table>
    <tr><form name="form_busca" method="post" id="form_busca" action="altMiautomec_Ubruto.php" target="tabIframe2">
            <td><table><tr><td><b>Código:</b> </td><td><input type="text" autocomplete="off" name="prodcod" id="prodcod" cols="50" value="<?php echo $FiltraProd;?>">
                        </td></tr><tr style="visibility: hidden;"><td><b>Novo Bruto:</b> </td><input type="hidden" name="alinha" id="alinha" value=""></td></tr></table></td>
            <td>
                &nbsp;&nbsp;&nbsp;<button hidden type="button" onclick="document.forms['form_busca'].submit(); return false;"><b>Filtrar</b></button>
                <input type="hidden" name="jaCalc" id="jaCalc" value="<?php echo $JaCalc;?>">
            </td></tr>
    </form>
</table>

<div>

    <?php
    set_time_limit(60);?>

</div>
<div>

<?php
if ($FiltraProd == "") {

}
else {

    $contResult = 0;
    if ($FiltraProd != "") {
        $Produtotxt = " and p.cdproduto = '" . mb_convert_encoding(strtoupper($FiltraProd), 'WINDOWS-1252', 'UTF-8') . "' ";
    } else {
        $Produtotxt = "";
    }

    $sql = "select distinct p.cdproduto, p.produto, coalesce(b.bruto,0) as BRUTO, pr.preco, pr.cdunidade,p.ro as RO,
            coalesce(b.desconto1,0) as DESC1, coalesce(b.desconto2,0) as DESC2, coalesce(b.desconto3,0) as DESC3,
            coalesce(b.lucro,0) as LUCRO, coalesce(p.ipi,0) as IPI, coalesce(p.frete,0) as frete,
		    coalesce(p.outros,0) as outros, coalesce(pr.percsubsttrib,0) as percsubsttrib,
		    coalesce(pr.vlimpostosdiretos,0) as vlimpostosdiretos, coalesce(pr.FATORCONV,0) as FATORCONV,
		    coalesce((select MULTIPLICADOR from fator_ro where ro = p.ro and cdunidade = pr.cdunidade),0) as MULTIPLICADOR
    from produto p
    LEFT JOIN produtobruto_base b on (p.cdproduto = b.cdproduto)
    LEFT JOIN produtopreco pr on (p.cdproduto = pr.cdproduto)
    where p.inativo = 0
    and p.produto not in ('FRETE','') " . $Produtotxt;

    $rs = ibase_query($conn, $sql) or die(ibase_errmsg());
    $contarray = 0;
    $lista_prod = array();
    $lista_prod[$contarray] = array();
    while ($lista_prod[$contarray] = ibase_fetch_assoc($rs)) {
        $contarray = $contarray + 1;
        $lista_prod[$contarray] = array();
    }

    if ($contarray==0){
        echo '<div style="color:blue;font-family:verdana;font-size:12px">Não foram encontrados produtos com os dados fornecidos</div>';
    } else {
        array_pop($lista_prod);
        $codprod = 0;
        echo '<form name="formPreco" method="post" action="gravaPreco.php">';
        echo '<input type="hidden" name="TpAlt" id="TpAlt" value="UB">';
        for ($i = 0; $i < count($lista_prod); $i++) {
            $campos = $lista_prod[$i];
            if ($codprod <> $campos["CDPRODUTO"]) {
                echo '<table><tr><td><table><tr><td><b>Produto:</b> </td><td><input readonly type="text" name="produto'.$i.'" id="produto'.$i.'" size="100" value="' . htmlspecialchars(mb_convert_encoding($campos["PRODUTO"], 'UTF-8', 'WINDOWS-1252')) . '"></td></tr>';
                echo '<tr><td><b>Bruto:</b> </td><td><input readonly type="text" class="real" name="bruto" id="bruto" value="'.number_format($campos["BRUTO"], 2, ',', '.').'"></td></tr>';
                if ($NovoBruto == "0") {
                    echo '<tr><td><b>Novo Bruto:</b> </td><td><input type="text" autofocus autocomplete="off" class="real" name="brutonv" id="brutonv" size="10" maxlength="10" onfocus="this.select();" value="'.number_format($campos["BRUTO"], 2, ',', '.').'" onkeyup="trataPreco('.$i.');"></td></tr>';
                } else {
                    echo '<tr><td><b>Novo Bruto:</b> </td><td><input type="text" autofocus autocomplete="off" class="real" name="brutonv" id="brutonv" size="10" maxlength="10" onfocus="this.select();" value="'.number_format($NovoBruto, 2, ',', '.').'" onkeyup="trataPreco('.$i.');"></td></tr>';
                }
                echo '<tr><td><b>Preço:</b> </td><td><input type="text" readonly class="real" name="preco" id="preco" value="'.number_format($campos["PRECO"], 2, ',', '.').'"></td></tr>';
                echo '<tr><td><b>Novo Preço:</b> </td><td><input type="text" readonly class="real" name="preconv" id="preconv" value="'.number_format($NovoPreco, 2, ',', '.').'"></td></tr>';

                echo '<tr><td>&nbsp;</td></tr><tr><td>&nbsp;</td></tr><tr><td>&nbsp;</td></tr>';
                echo '<tr class="textocinza"><td>DESC1: </td><td><input type="text" readonly class="real" value="'.number_format($campos["DESC1"], 2, ',', '.').'"></td></tr>';
                echo '<tr class="textocinza"><td>DESC2: </td><td><input type="text" readonly class="real" value="'.number_format($campos["DESC2"], 2, ',', '.').'"></td></tr>';
                echo '<tr class="textocinza"><td>DESC3: </td><td><input type="text" readonly class="real" value="'.number_format($campos["DESC3"], 2, ',', '.').'"></td></tr>';
                echo '<tr class="textocinza"><td>IPI: </td><td><input type="text" readonly class="real" value="'.number_format($campos["IPI"], 2, ',', '.').'"></td></tr>';
                echo '<tr class="textocinza"><td>FRETE: </td><td><input type="text" readonly class="real" value="'.number_format($campos["FRETE"], 2, ',', '.').'"></td></tr>';
                echo '<tr class="textocinza"><td>OUTROS: </td><td><input type="text" readonly class="real" value="'.number_format($campos["OUTROS"], 2, ',', '.').'"></td></tr>';
                echo '<tr class="textocinza"><td>PERCSUBSTTRIB: </td><td><input type="text" readonly class="real" value="'.number_format($campos["PERCSUBSTTRIB"], 2, ',', '.').'"></td></tr>';
                echo '<tr class="textocinza"><td>VLIMPOSTOSDIRETOS: </td><td><input type="text" readonly class="real" value="'.number_format($campos["VLIMPOSTOSDIRETOS"], 2, ',', '.').'"></td></tr>';
                echo '<tr class="textocinza"><td>LUCRO: </td><td><input type="text" readonly class="real" value="'.number_format($campos["LUCRO"], 2, ',', '.').'"></td></tr>';
                echo '<tr class="textocinza"><td>MULTIPLICADOR: </td><td><input type="text" readonly class="real" value="'.number_format($campos["MULTIPLICADOR"], 2, ',', '.').'"></td></tr>';
                echo '<tr class="textocinza"><td>FATOR CONV: </td><td><input type="text" readonly class="real" value="'.number_format($campos["FATORCONV"], 2, ',', '.').'"></td></tr>';
                echo '<tr class="textocinza"><td>CD UNIDADE: </td><td><input type="text" readonly class="real" value="'.$campos["CDUNIDADE"].'"></td></tr>';
                echo '<tr class="textocinza"><td>RO: </td><td><input type="text" readonly class="real" value="'.$campos["RO"].'"></td></tr>';
                echo '<button hidden type="submit" onclick="return validaForm();"><b>Gravar</b></button>';
                $codprod = $campos["CDPRODUTO"];
            }
        }

        echo '<input type="hidden" name="CdProd" id="CdProd" value="'.$campos["CDPRODUTO"].'">';
        echo '<input type="hidden" name="conf" id="conf" value="N">';
        echo '</table>';
        echo '</td></tr></table></form>';
    }
    ibase_close($conn);
}
?>
</body>

</html>
<script type="text/javascript">
function validaForm(){
    if (document.getElementById('bruto').value == document.getElementById('brutonv').value){
       alert('Valor está igual ao anterior');
        return false;
    }
    if (document.getElementById('jaCalc').value == 'N') {
        document.getElementById('conf').value = 'N';
        document.forms['formPreco'].submit();
    } else {
        if (confirm('Tem certeza que deseja gravar?')) {
            document.getElementById('conf').value = 'S';
            document.forms['formPreco'].submit();
        }
        document.getElementById('jaCalc').value = 'N';
    }
};
function verifConf() {
    if (document.getElementById('jaCalc').value == 'G') {
        document.getElementById('jaCalc').value = 'N';
        return false;
    }
    if (document.getElementById('jaCalc').value == 'S') {
        if (confirm('Tem certeza que deseja gravar?')) {
            document.getElementById('conf').value = 'S';
            document.forms['formPreco'].submit();
        }
        document.getElementById('jaCalc').value = 'N';
    }
}
function trataPreco(){
    if (document.getElementById('brutonv').value == document.getElementById('bruto').value) {
        document.getElementById('brutonv').style.color = '#0000FF';
    } else {
        document.getElementById('brutonv').style.color = '#FF0000';
    }
};
$(document).ready(function(){
    // Configuração para campos de Real.
    $('.real').maskMoney({showSymbol:false, symbol:"R$", decimal:",", thousands:"", precision:2});
});
</script>
