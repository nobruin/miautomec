<?php
/**
 * Autor: Marluce Almeida
 * Data de criacao: 28/05/18
 */
require_once("conexao_miautomec.php");
?>
<html>
<head>
    <meta http-equiv="content-type" content="text/html;charset=UTF-8" />
    <meta charset="UTF-8" />
    <title>Sistema de Atualização de Preços - Amorim Ferragens</title>
    <script type="text/javascript" src="jquery/jquery-1.3.2.min.js"></script>
    <script src="jquery/jquery.maskMoney.js" type="text/javascript"></script>
    <style type="text/css">


        .fixed_headers td:nth-child(1), th:nth-child(1) { width: 150px;min-width: 150px; }
        .fixed_headers td:nth-child(2), th:nth-child(2) { min-width: 80px; }
        .fixed_headers td:nth-child(3), th:nth-child(3) { width: 300px;min-width: 300px; }
        .fixed_headers td:nth-child(4), th:nth-child(4) { min-width: 45px; }
        .fixed_headers td:nth-child(5), th:nth-child(5) { min-width: 75px; }
        .fixed_headers td:nth-child(6), th:nth-child(6) { min-width: 85px; }
        .fixed_headers td:nth-child(7), th:nth-child(7) { min-width: 75px; }
        .fixed_headers td:nth-child(8), th:nth-child(8) { min-width: 85px; }
        .fixed_headers td:nth-child(9), th:nth-child(9) { min-width: 65px; }
        .fixed_headers td:nth-child(10), th:nth-child(10) { min-width: 85px; }
        .fixed_headers td:nth-child(11), th:nth-child(11) { min-width: 65px; }
        .fixed_headers td:nth-child(12), th:nth-child(12) { min-width: 85px; }
        .fixed_headers td:nth-child(13), th:nth-child(13) { min-width: 75px; }
        .fixed_headers td:nth-child(14), th:nth-child(14) { width: 85px;min-width: 85px; }

        .fixed_headers thead tr {
            display: block;
            position: relative;
        }

        .fixed_headers tbody {
            display: block;
            overflow: auto;
            height: 500px;
        }


    </style>
</head>

<body>

<div>

    <?php
    set_time_limit(60);?>

</div>
<div>

<?php
    if (isset($_POST['ordem'])) {
        if ($_POST['ordem'] == "A") {
            $Ordenacao = " order by 3,1,6";
        } else {
            $Ordenacao = " order by 1,3,6";
        }}
if (isset($_POST['comboGrupo'])) {
    $FiltraGrupo = $_POST['comboGrupo'];
} else {
    $FiltraGrupo = "";
}
if (isset($_POST['comboUnidade'])) {
    $FiltraUnidade = $_POST['comboUnidade'];
} else {
    $FiltraUnidade = "";
}
if (isset($_POST['comboFornecedor'])) {
    $FiltraFornecedor = $_POST['comboFornecedor'];
} else {
    $FiltraFornecedor = "";
}
if (isset($_POST['prodtxt'])) {
    $FiltraProduto = $_POST['prodtxt'];
} else {
    $FiltraProduto = "";
}

if ($FiltraGrupo == "" && $FiltraUnidade == "" && $FiltraFornecedor == "" && $FiltraProduto == "") {

}
else {

    $contResult = 0;
    if ($FiltraGrupo != "--") {
        $Grupotxt = " and p.cdgrupo = " . $FiltraGrupo . " ";
    } else {
        $Grupotxt = "";
    }
    if ($FiltraUnidade != "--") {
        $Unidadetxt = " and p.cdunidade = '" . mb_convert_encoding($FiltraUnidade, 'WINDOWS-1252', 'UTF-8') . "' ";
    } else {
        $Unidadetxt = "";
    }
    if ($FiltraFornecedor != "--") {
        $Fornecedortxt = " and pf.cdFornecedor = " . $FiltraFornecedor . " ";
    } else {
        $Fornecedortxt = "";
    }
    if ($FiltraProduto != "") {
        $Produtotxt = " and p.produto like '%" . mb_convert_encoding(strtoupper($FiltraProduto), 'WINDOWS-1252', 'UTF-8') . "%' ";
    } else {
        $Produtotxt = "";
    }

    $sql = "select distinct g.grupos, p.cdproduto, p.produto,
 		    coalesce(b.bruto,0) as BRUTO, coalesce(b.desconto1,0) as DESC1, coalesce(b.desconto2,0) as DESC2,
            coalesce(b.desconto3,0) as DESC3, coalesce(b.lucro,0) as LUCRO,
		    p.ro as RO, p.ipi as ipi, p.frete as frete
    from produto p
    LEFT JOIN produto_codforn pf ON (pf.cdproduto = p.cdproduto)
    LEFT JOIN fornecedor f ON (f.cdFornecedor = pf.cdFornecedor)
    LEFT JOIN grupos g on (p.cdgrupo = g.cdgrupo)
    LEFT JOIN produtobruto_base b on (p.cdproduto = b.cdproduto)
    where p.inativo = 0
    and p.produto not in ('FRETE','') " . $Grupotxt . $Unidadetxt . $Fornecedortxt . $Produtotxt . $Ordenacao;

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
        echo '<form name="formPreco" method="post" action="gravaProdPBase.php">';
        echo '<table style="color:blue;font-family:verdana;font-size:12px" border="0" cellpadding="0" cellspacing="0">';
        echo '<div style="position:fixed; "><tr><td>';
        echo '<button type="button" onclick="return validaForm();"><b>Gravar</b></button>&nbsp;&nbsp;<button style="display: none;" type="button" onclick="return validaForm();"><b>Gravar</b></button>&nbsp;&nbsp;<button type="button" id="imprimir" name="imprimir" onclick="printDiv(&quot;print&quot;,&quot;Lista de Produtos - Amorim Ferragens&quot;);"><b>Imprimir</b></button></td></tr>';
        echo '<tr><td colspan="2">&nbsp;</td></tr><tr><td colspan="2">';
        echo '<table  class="fixed_headers" style="color:blue;font-family:verdana;font-size:12px" border="1" cellpadding="0" cellspacing="0">';
        echo '<thead><tr style="background: blue;color: white;font-weight: bold"><th align="center">Grupo</th><th align="center">CDProduto</th><th align="center">Produto</th><th align="center">RO</th><th align="center">Bruto</th><th align="center">Bruto NV</th><th align="center">Desc1</th><th align="center">NDesc1</th><th align="center">Desc2</th><th align="center">NDesc2</th><th align="center">Desc3</th><th align="center">NDesc3</th><th align="center">Lucro</th><th align="center">Lucro NV</th><th style="visibility: hidden;">ipi</th><th style="visibility: hidden;">frete</th></tr></thead><tbody>';
        $codprod = 0;
        echo '<input type="hidden" name="QtdProd" id="QtdProd" value='.count($lista_prod).'>';

        for ($i = 0; $i < count($lista_prod); $i++) {
            $campos = $lista_prod[$i];
            if ($codprod <> $campos["CDPRODUTO"]) {
                echo '<tr id="linha'.$i.'" ';
                echo "onMouseOver='javascript:this.style.backgroundColor=&quot;#D1EEEE&quot;;' ";
                echo "onMouseOut='javascript:this.style.backgroundColor=&quot;#FFFFFF&quot;;' style='background-color: #FFFFFF;'>";

                echo '<td id="coluna1.'.$i.'" >';
                echo mb_convert_encoding($campos["GRUPOS"], 'UTF-8', 'WINDOWS-1252') . '</td>';
                echo '<td id="coluna2.'.$i.'" align="right" ><input readonly id="cdproduto'.$i.'" name="cdproduto'.$i.'" type="text" style="border:0;color:blue;background:transparent;text-align:right;" align="right" size="6" maxlength="6" value="'.mb_convert_encoding($campos["CDPRODUTO"], 'UTF-8', 'WINDOWS-1252').'"></td>';
                echo '<td id="coluna3.'.$i.'" >' . mb_convert_encoding($campos["PRODUTO"], 'UTF-8', 'WINDOWS-1252') . '</td>';
                echo '<td id="coluna4.'.$i.'" ><input readonly id="ro'.$i.'" name="ro'.$i.'" type="text" style="border:0;color:blue;background:transparent;text-align:right;" align="right" size="2" maxlength="2" value="'.$campos["RO"].'"></td>';
                $codprod = $campos["CDPRODUTO"];
            } else {
                echo '<tr id="linha'.$i.'" ';
                echo "onMouseOver='javascript:this.style.backgroundColor=&quot;#D1EEEE&quot;;' ";
                echo "onMouseOut='javascript:this.style.backgroundColor=&quot;#FFFFFF&quot;;' style='background-color: #FFFFFF;'>";
                echo '<td id="coluna1.'.$i.'" ></td>';
                echo '<td id="coluna2.'.$i.'" align="right" ><input readonly type="hidden" id="cdproduto'.$i.'" name="cdproduto'.$i.'" type="text" style="border:0;color:blue;background:transparent;text-align:right;" align="right" size="2" maxlength="2" value="'.mb_convert_encoding($campos["CDPRODUTO"], 'UTF-8', 'WINDOWS-1252').'"></td>';
                echo '<td id="coluna3.'.$i.'" >&nbsp;</td>';
                echo '<td id="coluna4.'.$i.'" ><input readonly type="hidden" id="ro'.$i.'" name="ro'.$i.'" type="text" style="border:0;color:blue;background:transparent;text-align:right;" align="right" size="2" maxlength="2" value="'.$campos["RO"].'"></td>';
            }

            echo '<td id="coluna5.'.$i.'" align="right"><input readonly id="bruto'.$i.'" name="bruto'.$i.'" type="text" class="real" style="border:0;color:blue;background:transparent;text-align:right;" align="right" size="2" maxlength="2" value="'.number_format($campos["BRUTO"], 2, ',', '.').'"></td>';
            echo '<td id="coluna6.'.$i.'"><input autocomplete="off" id="brutonv'.$i.'" name="brutonv'.$i.'" type="text" class="real" style="border:0;color:blue;background:transparent;text-align:right;" align="right" size="8" maxlength="8" value="'.number_format($campos["BRUTO"], 2, ',', '.').'" onkeyup="trataPreco('.$i.',\'brutonv\',\'bruto\');"></td>';
            echo '<td id="coluna7.'.$i.'" align="right"><input readonly id="desc1'.$i.'" name="desc1'.$i.'" type="text" class="real" style="border:0;color:blue;background:transparent;text-align:right;" align="right" size="2" maxlength="2" value="'.number_format($campos["DESC1"], 2, ',', '.').'"></td>';
            echo '<td id="coluna8.'.$i.'" align="right"><input autocomplete="off" id="desc1nv'.$i.'" name="desc1nv'.$i.'" type="text" class="real" style="border:0;color:blue;background:transparent;text-align:right;" align="right" size="8" maxlength="8" value="'.number_format($campos["DESC1"], 2, ',', '.').'" onkeyup="trataPreco('.$i.',\'desc1nv\',\'desc1\');"></td>';
            echo '<td id="coluna9.'.$i.'" align="right"><input readonly id="desc2'.$i.'" name="desc2'.$i.'" type="text" class="real" style="border:0;color:blue;background:transparent;text-align:right;" align="right" size="2" maxlength="2" value="'.number_format($campos["DESC2"], 2, ',', '.').'"></td>';
            echo '<td id="coluna10.'.$i.'" align="right"><input autocomplete="off" id="desc2nv'.$i.'" name="desc2nv'.$i.'" type="text" class="real" style="border:0;color:blue;background:transparent;text-align:right;" align="right" size="8" maxlength="8" value="'.number_format($campos["DESC2"], 2, ',', '.').'" onkeyup="trataPreco('.$i.',\'desc2nv\',\'desc2\');"></td>';
            echo '<td id="coluna11.'.$i.'" align="right"><input readonly id="desc3'.$i.'" name="desc3'.$i.'" type="text" class="real" style="border:0;color:blue;background:transparent;text-align:right;" align="right" size="2" maxlength="2" value="'.number_format($campos["DESC3"], 2, ',', '.').'"></td>';
            echo '<td id="coluna12.'.$i.'" align="right"><input autocomplete="off" id="desc3nv'.$i.'" name="desc3nv'.$i.'" type="text" class="real" style="border:0;color:blue;background:transparent;text-align:right;" align="right" size="8" maxlength="8" value="'.number_format($campos["DESC3"], 2, ',', '.').'" onkeyup="trataPreco('.$i.',\'desc3nv\',\'desc3\');"></td>';
            echo '<td id="coluna13.'.$i.'" align="right"><input readonly id="lucro'.$i.'" name="lucro'.$i.'" type="text" style="border:0;color:blue;background:transparent;text-align:right;" align="right" size="2" maxlength="2" value="'.number_format($campos["LUCRO"], 2, ',', '.').'"></td>';
            echo '<td id="coluna14.'.$i.'" align="right"><input autocomplete="off" id="lucronv'.$i.'" name="lucronv'.$i.'" type="text" class="real" style="border:0;color:blue;background:transparent;text-align:right;" align="right" size="8" maxlength="8" value="'.number_format($campos["LUCRO"], 2, ',', '.').'" onkeyup="trataPreco('.$i.',\'lucronv\',\'lucro\');"></td>';
            echo '<td style="display:none;" id="coluna15.'.$i.'" align="right"><input id="ipi'.$i.'" name="ipi'.$i.'" type="text" class="real" style="border:0;color:blue;background:transparent;text-align:right;" align="right" size="8" maxlength="8" value="'.number_format($campos["IPI"], 2, ',', '.').'"></td>';
            echo '<td style="display:none;" id="coluna16.'.$i.'" align="right"><input id="frete'.$i.'" name="frete'.$i.'" type="text" class="real" style="border:0;color:blue;background:transparent;text-align:right;" align="right" size="8" maxlength="8" value="'.number_format($campos["FRETE"], 2, ',', '.').'"></td></tr>';
        }
        echo '</tbody></table>';
        echo '</td></tr></table></form>';
        echo '<div class="entry" id="print" style="display:none;">';
        echo '<table id="tabprint" name="tabprint" style="color:blue;font-family:verdana;font-size:12px" border="1" cellpadding="0" cellspacing="0">';
        echo '<thead><tr style="background: blue;color: white;font-weight: bold"><th align="center">Grupo</th><th align="center">CDProduto</th><th align="center">Produto</th><th align="center">RO</th><th align="center">Bruto</th><th align="center">Bruto NV</th><th align="center">Desc1</th><th align="center">NDesc1</th><th align="center">Desc2</th><th align="center">NDesc2</th><th align="center">Desc3</th><th align="center">NDesc3</th><th align="center">Lucro</th><th align="center">Lucro NV</th></tr>';
        echo '</thead><tbody>';
        $codprod = 0;
        $qtdund = 0;
        for ($i = 0; $i < count($lista_prod); $i++) {
            $campos = $lista_prod[$i];
            if ($codprod <> $campos["CDPRODUTO"]) {
                echo '<tr id="linha'.$i.'" ';
                echo " style='background-color: #FFFFFF;'>";

                echo '<td id="coluna1.'.$i.'" >';
                echo mb_convert_encoding($campos["GRUPOS"], 'UTF-8', 'WINDOWS-1252') . '</td>';
                echo '<td id="coluna2.'.$i.'" align="right" ><input readonly id="cdproduto'.$i.'" name="cdproduto'.$i.'" type="text" style="border:0;color:blue;background:transparent;text-align:right;" align="right" size="2" maxlength="2" value="'.mb_convert_encoding($campos["CDPRODUTO"], 'UTF-8', 'WINDOWS-1252').'"></td>';
                echo '<td id="coluna3.'.$i.'" >' . mb_convert_encoding($campos["PRODUTO"], 'UTF-8', 'WINDOWS-1252') . '</td>';
                echo '<td id="coluna4.'.$i.'" ><input readonly id="ro'.$i.'" name="ro'.$i.'" type="text" style="border:0;color:blue;background:transparent;text-align:right;" align="right" size="2" maxlength="2" value="'.$campos["RO"].'"></td>';
                $codprod = $campos["CDPRODUTO"];
            } else {
                echo '<tr id="linha'.$i.'" ';
                echo "onMouseOver='javascript:this.style.backgroundColor=&quot;#D1EEEE&quot;;' ";
                echo "onMouseOut='javascript:this.style.backgroundColor=&quot;#FFFFFF&quot;;' style='background-color: #FFFFFF;'>";
                echo '<td id="coluna1.'.$i.'" ></td>';
                echo '<td id="coluna2.'.$i.'" align="right" ><input readonly type="hidden" id="cdproduto'.$i.'" name="cdproduto'.$i.'" type="text" style="border:0;color:blue;background:transparent;text-align:right;" align="right" size="2" maxlength="2" value="'.mb_convert_encoding($campos["CDPRODUTO"], 'UTF-8', 'WINDOWS-1252').'"></td>';
                echo '<td id="coluna3.'.$i.'" >&nbsp;</td>';
                echo '<td id="coluna4.'.$i.'" ><input readonly type="hidden" id="ro'.$i.'" name="ro'.$i.'" type="text" style="border:0;color:blue;background:transparent;text-align:right;" align="right" size="2" maxlength="2" value="'.$campos["RO"].'"></td>';
            }

            echo '<td id="coluna5.'.$i.'" align="right"><input readonly id="bruto'.$i.'" name="bruto'.$i.'" type="text" class="real" style="border:0;color:blue;background:transparent;text-align:right;" align="right" size="2" maxlength="2" value="'.number_format($campos["BRUTO"], 2, ',', '.').'"></td>';
            echo '<td id="coluna6.'.$i.'"><input id="brutonv'.$i.'" name="brutonv'.$i.'" type="text" class="real" style="border:0;color:blue;background:transparent;text-align:right;" align="right" size="8" maxlength="8" value="'.number_format($campos["BRUTO"], 2, ',', '.').'" onkeyup="trataPreco('.$i.',\'brutonv\',\'bruto\');"></td>';
            echo '<td id="coluna7.'.$i.'" align="right"><input readonly id="desc1'.$i.'" name="desc1'.$i.'" type="text" class="real" style="border:0;color:blue;background:transparent;text-align:right;" align="right" size="2" maxlength="2" value="'.number_format($campos["DESC1"], 2, ',', '.').'"></td>';
            echo '<td id="coluna8.'.$i.'" align="right"><input id="desc1nv'.$i.'" name="desc1nv'.$i.'" type="text" class="real" style="border:0;color:blue;background:transparent;text-align:right;" align="right" size="8" maxlength="8" value="'.number_format($campos["DESC1"], 2, ',', '.').'" onkeyup="trataPreco('.$i.',\'desc1nv\',\'desc1\');"></td>';
            echo '<td id="coluna9.'.$i.'" align="right"><input readonly id="desc2'.$i.'" name="desc2'.$i.'" type="text" class="real" style="border:0;color:blue;background:transparent;text-align:right;" align="right" size="2" maxlength="2" value="'.number_format($campos["DESC2"], 2, ',', '.').'"></td>';
            echo '<td id="coluna10.'.$i.'" align="right"><input id="desc2nv'.$i.'" name="desc2nv'.$i.'" type="text" class="real" style="border:0;color:blue;background:transparent;text-align:right;" align="right" size="8" maxlength="8" value="'.number_format($campos["DESC2"], 2, ',', '.').'" onkeyup="trataPreco('.$i.',\'desc2nv\',\'desc2\');"></td>';
            echo '<td id="coluna11.'.$i.'" align="right"><input readonly id="desc3'.$i.'" name="desc3'.$i.'" type="text" class="real" style="border:0;color:blue;background:transparent;text-align:right;" align="right" size="2" maxlength="2" value="'.number_format($campos["DESC3"], 2, ',', '.').'"></td>';
            echo '<td id="coluna12.'.$i.'" align="right"><input id="desc3nv'.$i.'" name="desc3nv'.$i.'" type="text" class="real" style="border:0;color:blue;background:transparent;text-align:right;" align="right" size="8" maxlength="8" value="'.number_format($campos["DESC3"], 2, ',', '.').'" onkeyup="trataPreco('.$i.',\'desc3nv\',\'desc3\');"></td>';
            echo '<td id="coluna13.'.$i.'" align="right"><input readonly id="lucro'.$i.'" name="lucro'.$i.'" type="text" style="border:0;color:blue;background:transparent;text-align:right;" align="right" size="2" maxlength="2" value="'.number_format($campos["LUCRO"], 2, ',', '.').'"></td>';
            echo '<td id="coluna14.'.$i.'" align="right"><input id="lucronv'.$i.'" name="lucronv'.$i.'" type="text" class="real" style="border:0;color:blue;background:transparent;text-align:right;" align="right" size="8" maxlength="8" value="'.number_format($campos["LUCRO"], 2, ',', '.').'" onkeyup="trataPreco('.$i.',\'lucronv\',\'lucro\');"></td>';
            echo '<td style="display:none;" id="coluna15.'.$i.'" align="right"><input id="ipi'.$i.'" name="ipi'.$i.'" type="text" class="real" style="border:0;color:blue;background:transparent;text-align:right;" align="right" size="8" maxlength="8" value="'.number_format($campos["IPI"], 2, ',', '.').'"></td>';
            echo '<td style="display:none;" id="coluna16.'.$i.'" align="right"><input id="frete'.$i.'" name="frete'.$i.'" type="text" class="real" style="border:0;color:blue;background:transparent;text-align:right;" align="right" size="8" maxlength="8" value="'.number_format($campos["FRETE"], 2, ',', '.').'"></td></tr>';        }
        echo '</tbody></table>';

    }
    ibase_close($conn);
}
?>
</body>

</html>
<script type="text/javascript">
function validaForm(){
    var temalt = 0;
    for (var y = 0; y < document.getElementById('QtdProd').value; y++) {
        if (document.getElementById('brutonv'+y).value != document.getElementById('bruto'+y).value) {
            temalt = 1;
            break;
        }
        if (document.getElementById('desc1nv'+y).value != document.getElementById('desc1'+y).value) {
            temalt = 1;
            break;
        }
        if (document.getElementById('desc2nv'+y).value != document.getElementById('desc2'+y).value) {
            temalt = 1;
            break;
        }
        if (document.getElementById('desc3nv'+y).value != document.getElementById('desc3'+y).value) {
            temalt = 1;
            break;
        }
        if (document.getElementById('lucronv'+y).value != document.getElementById('lucro'+y).value) {
            temalt = 1;
            break;
        }
    }
    if (temalt == 1) {
        document.forms['formPreco'].submit();
    } else {
        alert ("Não foram feitas alterações!");
    }

};
function trataPreco(prcSel, camponv, campo){
    if (document.getElementById(camponv+prcSel).value == document.getElementById(campo+prcSel).value) {
        document.getElementById(camponv+prcSel).style.color = '#0000FF';
    } else {
        document.getElementById(camponv+prcSel).style.color = '#FF0000';
    }
    if(event.which == 13) {
        document.getElementById(camponv+(prcSel+1)).focus();
        document.getElementById(camponv+(prcSel+1)).select();
    }
};
function printDiv(id, pg) {
    var oPrint, oJan;
    oPrint = window.document.getElementById(id).innerHTML;
    oJan = window.open(pg);
    oJan.document.write(oPrint);
    oJan.window.print();
    oJan.document.close();
    oJan.focus();
}
$(document).ready(function(){
    // Configuração para campos de Real.
    $('.real').maskMoney({showSymbol:false, symbol:"R$", decimal:",", thousands:"", precision:2});
});
</script>
