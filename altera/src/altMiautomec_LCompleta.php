<?php
/**
 * Autor: Marluce Almeida
 * Data de criacao: 08/06/15
 * Hora de criacao: 10:00
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


        .fixed_headers td:nth-child(1), th:nth-child(1) { width: 150px; }
        .fixed_headers td:nth-child(2), th:nth-child(2) { min-width: 80px; }
        .fixed_headers td:nth-child(3), th:nth-child(3) { width: 300px; }
        .fixed_headers td:nth-child(4), th:nth-child(4) { min-width: 45px; }
        .fixed_headers td:nth-child(5), th:nth-child(5) { min-width: 10px; }
        .fixed_headers td:nth-child(6), th:nth-child(6) { min-width: 60px; }
        .fixed_headers td:nth-child(7), th:nth-child(7) { min-width: 75px; }
        .fixed_headers td:nth-child(8), th:nth-child(8) { min-width: 85px; }
        .fixed_headers td:nth-child(9), th:nth-child(9) { min-width: 75px; }
        .fixed_headers td:nth-child(10), th:nth-child(10) { min-width: 85px; }
        .fixed_headers td:nth-child(11), th:nth-child(11) { min-width: 65px; }
        .fixed_headers td:nth-child(12), th:nth-child(12) { min-width: 85px; }
        .fixed_headers td:nth-child(13), th:nth-child(13) { min-width: 65px; }
        .fixed_headers td:nth-child(14), th:nth-child(14) { min-width: 85px; }
        .fixed_headers td:nth-child(15), th:nth-child(15) { min-width: 65px; }
        .fixed_headers td:nth-child(16), th:nth-child(16) { min-width: 85px; }
        .fixed_headers td:nth-child(17), th:nth-child(17) { min-width: 50px; }
        .fixed_headers td:nth-child(18), th:nth-child(18) { min-width: 50px; }
        .fixed_headers td:nth-child(19), th:nth-child(19) { min-width: 50px; }
        .fixed_headers td:nth-child(20), th:nth-child(20) { min-width: 50px; }
        .fixed_headers td:nth-child(21), th:nth-child(21) { min-width: 50px; }
        .fixed_headers td:nth-child(22), th:nth-child(22) { min-width: 50px; }
        .fixed_headers td:nth-child(23), th:nth-child(23) { min-width: 50px; }

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
        $Unidadetxt = " and pr.cdunidade = '" . mb_convert_encoding($FiltraUnidade, 'WINDOWS-1252', 'UTF-8') . "' ";
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

    $sql = "select distinct g.grupos, p.cdproduto, p.produto, pr.cdunidade, pr.preco,
 		    coalesce(b.bruto,0) as BRUTO, coalesce(b.desconto1,0) as DESC1, coalesce(b.desconto2,0) as DESC2,
            coalesce(b.desconto3,0) as DESC3, coalesce(b.lucro,0) as LUCRO, coalesce(p.ipi,0) as IPI, coalesce(p.frete,0) as frete,
		    coalesce(p.outros,0) as outros, coalesce(pr.percsubsttrib,0) as percsubsttrib,
		    coalesce(pr.vlimpostosdiretos,0) as vlimpostosdiretos,
		    p.ro as RO, pr.idpreco as idpreco, coalesce(pr.fatorconv,0) as fatorconv,
		    coalesce((select multiplicador from fator_ro where ro = p.ro and cdunidade = pr.cdunidade),0) as multiplicador,
		    (select cdunidade from fator_ro where ro = p.ro and multiplicador = 0) as und_princ
    from produto p
    LEFT JOIN produto_codforn pf ON (pf.cdproduto = p.cdproduto)
    LEFT JOIN fornecedor f ON (f.cdFornecedor = pf.cdFornecedor)
    LEFT JOIN grupos g on (p.cdgrupo = g.cdgrupo)
    LEFT JOIN produtopreco pr on (p.cdproduto = pr.cdproduto)
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
        echo '<form name="formPreco" method="post" action="gravaPreco.php">';
        echo '<table style="color:blue;font-family:verdana;font-size:12px" border="0" cellpadding="0" cellspacing="0">';
        echo '<div style="position:fixed; "><tr><td>';
        echo '<input name="todos" id="todos" type="checkbox" value="N" onclick="marcaTodos();"> Todos</td>';
        echo '<td align="right">';
        //<button type="button" onclick="calcula();"><b>Calcular</b></button>&nbsp;&nbsp;
        echo '<button style="display: none;" type="button" onclick="return validaForm();"><b>Gravar</b></button>&nbsp;&nbsp;<button type="button" id="imprimir" name="imprimir" onclick="printDiv(&quot;print&quot;,&quot;Lista de Produtos - Amorim Ferragens&quot;);"><b>Imprimir</b></button></td></tr>';
        echo '<tr><td colspan="2">&nbsp;</td></tr><tr><td colspan="2">';
        echo '<table  class="fixed_headers" style="color:blue;font-family:verdana;font-size:12px" border="1" cellpadding="0" cellspacing="0">';
        echo '<thead><tr style="background: blue;color: white;font-weight: bold"><th align="center">Grupo</th><th align="center">CDProduto</th><th align="center">Produto</th><th align="center">RO</th><th align="center" style="display:none;">Fornecedor</th><th align="center">Unidade</th><th align="center">Bruto</th><th align="center">Bruto NV</th><th align="center">Pre&ccedilo</th><th align="center">Preço NV</th><th align="center">Desc1</th><th align="center">NDesc1</th><th align="center">Desc2</th><th align="center">NDesc2</th><th align="center">Desc3</th><th align="center">NDesc3</th><th align="center">Líq</th><th align="center">Lucro</th><th align="center">ipi</th><th align="center">frete</th><th align="center">outros</th><th align="center">ST</th><th align="center">ID</th></tr></thead><tbody>';
        $codprod = 0;
        echo '<input type="hidden" name="QtdProd" id="QtdProd" value='.count($lista_prod).'>';

        for ($i = 0; $i < count($lista_prod); $i++) {
            $campos = $lista_prod[$i];
            if ($codprod <> $campos["CDPRODUTO"]) {
                echo '<tr id="linha'.$i.'" ';
                echo "onMouseOver='javascript:this.style.backgroundColor=&quot;#D1EEEE&quot;;' ";
                echo "onMouseOut='javascript:this.style.backgroundColor=&quot;#FFFFFF&quot;;' style='background-color: #FFFFFF;'>";

                echo '<td id="coluna1.'.$i.'" ><input name="prodlin'.$i.'" id="prodlin'.$i.'" type="checkbox" value="N" onclick="marcaLinha('.$i.');"> &nbsp;';
                echo mb_convert_encoding($campos["GRUPOS"], 'UTF-8', 'WINDOWS-1252') . '</td>';
                echo '<td id="coluna2.'.$i.'" align="right" ><input readonly id="cdproduto'.$i.'" name="cdproduto'.$i.'" type="text" style="border:0;color:blue;background:transparent;text-align:right;" align="right" size="6" maxlength="6" value="'.mb_convert_encoding($campos["CDPRODUTO"], 'UTF-8', 'WINDOWS-1252').'"></td>';
                echo '<td id="coluna3.'.$i.'" >' . mb_convert_encoding($campos["PRODUTO"], 'UTF-8', 'WINDOWS-1252') . '</td>';
                echo '<td id="coluna4.'.$i.'" ><input readonly id="ro'.$i.'" name="ro'.$i.'" type="text" style="border:0;color:blue;background:transparent;text-align:right;" align="right" size="2" maxlength="2" value="'.$campos["RO"].'"></td>';
                $fab = "&nbsp;";
                echo '<td id="coluna5.'.$i.'" style="display:none;">' . $fab . '</td>';
                $codprod = $campos["CDPRODUTO"];
            } else {
                echo '<tr id="linha'.$i.'" ';
                echo "onMouseOver='javascript:this.style.backgroundColor=&quot;#D1EEEE&quot;;' ";
                echo "onMouseOut='javascript:this.style.backgroundColor=&quot;#FFFFFF&quot;;' style='background-color: #FFFFFF;'>";
                echo '<td id="coluna1.'.$i.'" ><input name="prodlin'.$i.'" id="prodlin'.$i.'" type="checkbox" value="N" onclick="marcaLinha('.$i.');"></td>';
                echo '<td id="coluna2.'.$i.'" align="right" ><input readonly type="hidden" id="cdproduto'.$i.'" name="cdproduto'.$i.'" type="text" style="border:0;color:blue;background:transparent;text-align:right;" align="right" size="2" maxlength="2" value="'.mb_convert_encoding($campos["CDPRODUTO"], 'UTF-8', 'WINDOWS-1252').'"></td>';
                echo '<td id="coluna3.'.$i.'" >&nbsp;</td>';
                echo '<td id="coluna4.'.$i.'" ><input readonly type="hidden" id="ro'.$i.'" name="ro'.$i.'" type="text" style="border:0;color:blue;background:transparent;text-align:right;" align="right" size="2" maxlength="2" value="'.$campos["RO"].'"></td>';
                echo '<td id="coluna5.'.$i.'" style="display:none;" >&nbsp;</td>';
            }
            $calc = doubleval($campos["BRUTO"]) * (1-doubleval($campos["DESC1"])/100)*(1-doubleval($campos["DESC2"])/100)*(1-doubleval($campos["DESC3"])/100);

            $calculado = $calc * (1+doubleval($campos["IPI"])/100) * (1+doubleval($campos["PERCSUBSTTRIB"])/100) * (1+doubleval($campos["FRETE"])/100) * (1+doubleval($campos["OUTROS"])/100) * (1+doubleval($campos["LUCRO"])/100) * (1+doubleval($campos["VLIMPOSTOSDIRETOS"])/100);
            //echo $sql;
            //echo 'calc:' . $calc;
            //echo 'ipi:'. (1+doubleval($campos["IPI"])/100);
            //echo 'st:'.(1+doubleval($campos["PERCSUBSTTRIB"])/100);
            //echo 'st:'.doubleval($campos["PERCSUBSTTRIB"]);
            //echo 'frete:'. (1+doubleval($campos["FRETE"])/100);
            //echo 'outros:'. (1+doubleval($campos["OUTROS"])/100);
            //echo 'lucro:'.(1+doubleval($campos["LUCRO"])/100);
            //echo 'id:'.(1+doubleval($campos["VLIMPOSTOSDIRETOS"])/100);
            //echo 'calculado:'.$calculado;
            if (doubleval($campos["MULTIPLICADOR"]) == 0) {
                $novopreco =   $calculado;
            } else {
                $novopreco = $calculado * doubleval($campos["MULTIPLICADOR"]) * doubleval($campos["FATORCONV"]);
            }

            echo '<td id="coluna6.'.$i.'" ><input readonly id="und'.$i.'" name="und'.$i.'" type="text" style="border:0;color:blue;background:transparent;text-align:right;" align="right" size="3" maxlength="3" value="'.mb_convert_encoding($campos["CDUNIDADE"], 'UTF-8', 'WINDOWS-1252').'"></td>';
            echo '<td id="coluna7.'.$i.'" align="right"><input readonly id="bruto'.$i.'" name="bruto'.$i.'" type="text" class="real" style="border:0;color:blue;background:transparent;text-align:right;" align="right" size="2" maxlength="2" value="'.number_format($campos["BRUTO"], 2, ',', '.').'"></td>';
            echo '<td id="coluna8.'.$i.'"><input id="brutonv'.$i.'" name="brutonv'.$i.'" type="text" class="real" style="border:0;color:blue;background:transparent;text-align:right;" align="right" size="8" maxlength="8" value="'.number_format($campos["BRUTO"], 2, ',', '.').'" onkeyup="trataPreco('.$i.',\'brutonv\',\'bruto\');"></td>';
            echo '<td id="coluna9.'.$i.'" align="right"><input readonly id="preco'.$i.'" name="preco'.$i.'" type="text" class="real" style="border:0;color:blue;background:transparent;text-align:right;" align="right" size="2" maxlength="2" value="'.number_format($campos["PRECO"], 2, ',', '.').'"></td>';
            echo '<td id="coluna10.'.$i.'" align="right"><input id="preconv'.$i.'" name="preconv'.$i.'" type="text" class="real" style="border:0;color:blue;background:transparent;text-align:right;" align="right" size="8" maxlength="8" value="'.number_format($campos["PRECO"], 2, ',', '.').'" onkeyup="trataPreco('.$i.',\'preconv\',\'preco\');"></td>';
            echo '<td id="coluna11.'.$i.'" align="right"><input readonly id="desc1'.$i.'" name="desc1'.$i.'" type="text" class="real" style="border:0;color:blue;background:transparent;text-align:right;" align="right" size="2" maxlength="2" value="'.number_format($campos["DESC1"], 2, ',', '.').'"></td>';
            echo '<td id="coluna12.'.$i.'" align="right"><input id="desc1nv'.$i.'" name="desc1nv'.$i.'" type="text" class="real" style="border:0;color:blue;background:transparent;text-align:right;" align="right" size="8" maxlength="8" value="'.number_format($campos["DESC1"], 2, ',', '.').'" onkeyup="trataPreco('.$i.',\'desc1nv\',\'desc1\');"></td>';
            echo '<td id="coluna13.'.$i.'" align="right"><input readonly id="desc2'.$i.'" name="desc2'.$i.'" type="text" class="real" style="border:0;color:blue;background:transparent;text-align:right;" align="right" size="2" maxlength="2" value="'.number_format($campos["DESC2"], 2, ',', '.').'"></td>';
            echo '<td id="coluna14.'.$i.'" align="right"><input id="desc2nv'.$i.'" name="desc2nv'.$i.'" type="text" class="real" style="border:0;color:blue;background:transparent;text-align:right;" align="right" size="8" maxlength="8" value="'.number_format($campos["DESC2"], 2, ',', '.').'" onkeyup="trataPreco('.$i.',\'desc2nv\',\'desc2\');"></td>';
            echo '<td id="coluna15.'.$i.'" align="right"><input readonly id="desc3'.$i.'" name="desc3'.$i.'" type="text" class="real" style="border:0;color:blue;background:transparent;text-align:right;" align="right" size="2" maxlength="2" value="'.number_format($campos["DESC3"], 2, ',', '.').'"></td>';
            echo '<td id="coluna16.'.$i.'" align="right"><input id="desc3nv'.$i.'" name="desc3nv'.$i.'" type="text" class="real" style="border:0;color:blue;background:transparent;text-align:right;" align="right" size="8" maxlength="8" value="'.number_format($campos["DESC3"], 2, ',', '.').'" onkeyup="trataPreco('.$i.',\'desc3nv\',\'desc3\');"></td>';
            echo '<td id="coluna17.'.$i.'" align="right"><input readonly id="liq'.$i.'" name="liq'.$i.'" type="text" style="border:0;color:blue;background:transparent;text-align:right;" align="right" size="2" maxlength="2" value="'.number_format($calc, 2, ',', '.').'"></td>';
            echo '<td id="coluna18.'.$i.'" align="right"><input readonly id="lucro'.$i.'" name="lucro'.$i.'" type="text" style="border:0;color:blue;background:transparent;text-align:right;" align="right" size="2" maxlength="2" value="'.number_format($campos["LUCRO"], 2, ',', '.').'"></td>';
            echo '<td id="coluna19.'.$i.'" align="right"><input readonly id="ipi'.$i.'" name="ipi'.$i.'" type="text" style="border:0;color:blue;background:transparent;text-align:right;" align="right" size="2" maxlength="2" value="'.number_format($campos["IPI"], 2, ',', '.').'"></td>';
            echo '<td id="coluna20.'.$i.'" align="right"><input readonly id="frete'.$i.'" name="frete'.$i.'" type="text" style="border:0;color:blue;background:transparent;text-align:right;" align="right" size="2" maxlength="2" value="'.number_format($campos["FRETE"], 2, ',', '.').'"></td>';
            echo '<td id="coluna21.'.$i.'" align="right"><input readonly id="outros'.$i.'" name="outros'.$i.'" type="text" style="border:0;color:blue;background:transparent;text-align:right;" align="right" size="2" maxlength="2" value="'.number_format($campos["OUTROS"], 2, ',', '.').'"></td>';
            echo '<td id="coluna22.'.$i.'" align="right"><input readonly id="percsubsttrib'.$i.'" name="percsubsttrib'.$i.'" type="text" style="border:0;color:blue;background:transparent;text-align:right;" align="right" size="2" maxlength="2" value="'.number_format($campos["PERCSUBSTTRIB"], 2, ',', '.').'"></td>';
            echo '<td id="coluna23.'.$i.'" align="right"><input readonly id="vlimpostosdiretos'.$i.'" name="vlimpostosdiretos'.$i.'" type="text" style="border:0;color:blue;background:transparent;text-align:right;" align="right" size="2" maxlength="2" value="'.number_format($campos["VLIMPOSTOSDIRETOS"], 2, ',', '.').'"></td>';
            echo '<td id="coluna24.'.$i.'" style="display:none;" align="right"><input readonly id="idpreco'.$i.'" name="idpreco'.$i.'" type="text" style="border:0;color:blue;background:transparent;text-align:right;" align="right" size="2" maxlength="2" value="'.$campos["IDPRECO"].'"></td></tr>';
        }
        echo '</tbody></table>';
        echo '</td></tr></table></form>';
        echo '<div class="entry" id="print" style="display:none;">';
        echo '<table id="tabprint" name="tabprint" style="color:blue;font-family:verdana;font-size:12px" border="1" cellpadding="0" cellspacing="0">';
        echo '<thead>';
        echo '<tr><th colspan="10">Grupo: '.mb_convert_encoding($campos["GRUPOS"], 'UTF-8', 'WINDOWS-1252').'</th></tr>';
        echo '<tr style="background: blue;color: white;font-weight: bold"><th align="center">Código</th><th align="center">Descrição</th><th align="center">Und</th><th align="center">Pre&ccedilo</th><th align="center">Und</th><th align="center">Pre&ccedilo</th><th align="center">Und</th><th align="center">Pre&ccedilo</th><th align="center">Und</th><th align="center">Pre&ccedilo</th></tr>';
        echo '</thead><tbody>';
        $codprod = 0;
        $qtdund = 0;
        for ($i = 0; $i < count($lista_prod); $i++) {
            $campos = $lista_prod[$i];
            if ($codprod <> $campos["CDPRODUTO"]) {
                if ($qtdund > 0 && $qtdund < 4) {
                    for ($w = $qtdund+1;$w <5;$w++) {
                        echo '<td>&nbsp;</td><td>&nbsp;</td>';
                    }
                    echo '</tr>';
                }
                $qtdund = 0;
                echo '<tr>';
                echo '<td align="right" >' . mb_convert_encoding($campos["CDPRODUTO"], 'UTF-8', 'WINDOWS-1252') . '</td>';
                echo '<td>' . mb_convert_encoding($campos["PRODUTO"], 'UTF-8', 'WINDOWS-1252') . '</td>';
                $codprod = $campos["CDPRODUTO"];
                echo '<td>' . mb_convert_encoding($campos["CDUNIDADE"], 'UTF-8', 'WINDOWS-1252') . '</td>';
                echo '<td align="right">'.number_format($campos["PRECO"], 2, ',', '.').'</td>';
                $qtdund = $qtdund + 1;
            } else {
                echo '<td>' . mb_convert_encoding($campos["CDUNIDADE"], 'UTF-8', 'WINDOWS-1252') . '</td>';
                echo '<td align="right">'.number_format($campos["PRECO"], 2, ',', '.').'</td>';
                $qtdund = $qtdund + 1;
            }
        }
        if ($qtdund > 0 && $qtdund < 4) {
            for ($w = $qtdund+1;$w <5;$w++) {
                echo '<td>&nbsp;</td><td>&nbsp;</td>';
            }
            echo '</tr>';
            $qtdund = 0;
        }
        echo '</tbody></table>';

    }
    ibase_close($conn);
}
?>
</body>

</html>
<script type="text/javascript">
function marcaLinha(chkSel){
    if (document.getElementById('prodlin'+chkSel).checked) {
        document.getElementById('prodlin'+chkSel).checked = true;
        document.getElementById('prodlin'+chkSel).value = 'S';
        for (var x = 1; x < 24; x++) {
            document.getElementById('coluna' + x + '.' + chkSel).style.background = '#CCCCCC';
        }
    }else{
        document.getElementById('todos').checked = false;
        document.getElementById('todos').value = 'N';
        document.getElementById('prodlin'+chkSel).checked = false;
        document.getElementById('prodlin'+chkSel).value = 'N';
        for (var x = 1; x < 24; x++) {
            document.getElementById('coluna'+x + '.' +chkSel).style.background='transparent';
        }
    }
};
function marcaTodos(){
    if (document.getElementById('todos').checked) {
        for (var y = 0; y < document.getElementById('QtdProd').value; y++) {
            if (document.getElementById('prodlin' + y)) {
                document.getElementById('prodlin' + y).checked = true;
                document.getElementById('prodlin' + y).value = 'S';
            }
            for (var x = 1; x < 24; x++) {
                document.getElementById('coluna'+x + '.'  + y).style.background = '#CCCCCC';
            }
        }
    } else {
        for (var y = 0; y < document.getElementById('QtdProd').value; y++) {
            if (document.getElementById('prodlin' + y)) {
                document.getElementById('prodlin' + y).checked = false;
                document.getElementById('prodlin' + y).value = 'N';
            }
            for (var x = 1; x < 24; x++) {
                document.getElementById('coluna' + x + '.'  + y).style.background = 'transparent';
            }
        }
    }
};
function calcula(lin){
    if (document.getElementById('bruto' + lin).value != document.getElementById('brutonv' + lin).value) {
        ro = document.getElementById('ro' + lin).value;
        switch (ro) {
            case 'P1':
                val = Number(document.getElementById('bruto' + lin).value) *
                    (1-Number(document.getElementById('desconto1' + lin).value)/100)*
                    (1-Number(document.getElementById('desconto2' + lin).value)/100)*
                    (1-Number(document.getElementById('desconto3' + lin).value)/100)*
                    (1+Number(document.getElementById('ipi' + lin).value)/100)*
                    (1+Number(document.getElementById('frete' + lin).value)/100)*
                    (1+Number(document.getElementById('outros' + lin).value)/100)*
                    (1+Number(document.getElementById('percsubsttrib' + lin).value)/100)*
                    (1+Number(document.getElementById('vlimpostosdiretos' + lin).value)/100)*
                    (1+Number(document.getElementById('lucro' + lin).value)/100);
                if (val<0.05){val = 0.05}
                document.getElementById('preconv' + lin).value = (val.toFixed(2)).replace(".", ",");
                break;
            case 'R1':
                if (document.getElementById('und'+lin).value == 'PC') {
                    val = Number(document.getElementById('preco_tab' + lin).value) *
                        (1-Number(document.getElementById('vldescfabr' + lin).value)/100)*
                        (1-Number(document.getElementById('largura2' + lin).value)/100)*
                        (1-Number(document.getElementById('largura3' + lin).value)/100)*
                        (1+Number(document.getElementById('ipi' + lin).value)/100)*
                        (1+Number(document.getElementById('frete' + lin).value)/100)*
                        (1+Number(document.getElementById('outros' + lin).value)/100)*
                        (1+Number(document.getElementById('percsubsttrib' + lin).value)/100)*
                        (1+Number(document.getElementById('vlimpostosdiretos' + lin).value)/100)*
                        (1+Number(document.getElementById('percentual' + lin).value)/100);
                    if (val<0.05){val = 0.05}
                    document.getElementById('preconv' + lin).value = (val.toFixed(2)).replace(".", ",");
                }
                if (document.getElementById('und'+lin).value == 'PR') {
                    //Varre pra cima
                    achou = 'N';
                    for (var lin2 =lin-1;lin2>0&&document.getElementById('cdproduto'+ lin2).value == document.getElementById('cdproduto'+lin).value;lin2--) {
                        if (document.getElementById('und'+lin2).value == 'PC') {
                            val = Number(document.getElementById('preco_tab' +lin2).value) *
                                (1-Number(document.getElementById('vldescfabr' +lin2).value)/100)*
                                (1-Number(document.getElementById('largura2' +lin2).value)/100)*
                                (1-Number(document.getElementById('largura3' +lin2).value)/100)*
                                (1+Number(document.getElementById('ipi' +lin2).value)/100)*
                                (1+Number(document.getElementById('frete' +lin2).value)/100)*
                                (1+Number(document.getElementById('outros' +lin2).value)/100)*
                                (1+Number(document.getElementById('percsubsttrib' +lin2).value)/100)*
                                (1+Number(document.getElementById('vlimpostosdiretos' +lin2).value)/100)*
                                (1+Number(document.getElementById('percentual' +lin2).value)/100);
                            val = val * 1;
                            if (val<0.05){val = 0.05}
                            document.getElementById('preconv' +lin).value = (val.toFixed(2)).replace(".", ",");
                            achou = 'S';
                            break;
                        }
                    }
                    if (achou == 'S') { break;}
                    //Varre pra baixo
                    for (var lin2 =lin+1;lin2<document.getElementById('QtdProd').value&&document.getElementById('cdproduto'+ lin2).value == document.getElementById('cdproduto' +lin).value; lin2++) {
                        if (document.getElementById('und'+ lin2).value == 'PC') {
                            val = Number(document.getElementById('preco_tab' +lin2).value) *
                                (1-Number(document.getElementById('vldescfabr' +lin2).value)/100)*
                                (1-Number(document.getElementById('largura2' +lin2).value)/100)*
                                (1-Number(document.getElementById('largura3' +lin2).value)/100)*
                                (1+Number(document.getElementById('ipi' +lin2).value)/100)*
                                (1+Number(document.getElementById('frete' +lin2).value)/100)*
                                (1+Number(document.getElementById('outros' +lin2).value)/100)*
                                (1+Number(document.getElementById('percsubsttrib' +lin2).value)/100)*
                                (1+Number(document.getElementById('vlimpostosdiretos' +lin2).value)/100)*
                                (1+Number(document.getElementById('percentual' +lin2).value)/100);
                            val = val * 1;
                            if (val<0.05){val = 0.05}
                            document.getElementById('preconv' +lin).value = (val.toFixed(2)).replace(".", ",");
                            achou = 'S';
                            break;
                        }
                    }
                }
                break;
            case 'C1':
                if (document.getElementById('und' +lin).value == 'CT') {
                    val = Number(document.getElementById('preco_tab' + lin).value) *
                        (1-Number(document.getElementById('vldescfabr' + lin).value)/100)*
                        (1-Number(document.getElementById('largura2' + lin).value)/100)*
                        (1-Number(document.getElementById('largura3' + lin).value)/100)*
                        (1+Number(document.getElementById('ipi' + lin).value)/100)*
                        (1+Number(document.getElementById('frete' + lin).value)/100)*
                        (1+Number(document.getElementById('outros' + lin).value)/100)*
                        (1+Number(document.getElementById('percsubsttrib' + lin).value)/100)*
                        (1+Number(document.getElementById('vlimpostosdiretos' + lin).value)/100)*
                        (1+Number(document.getElementById('percentual' + lin).value)/100);
                    if (val<0.05){val = 0.05}
                    document.getElementById('preconv' + lin).value = (val.toFixed(2)).replace(".", ",");
                }
                if (document.getElementById('und' +lin).value == 'PC') {
                    //Varre pra cima
                    achou = 'N';
                    for (var lin2 = lin-1; lin2>0&&document.getElementById('cdproduto'+ lin2).value == document.getElementById('cdproduto' +lin).value; lin2--) {
                        if (document.getElementById('und'+ lin2).value == 'CT') {
                            val = Number(document.getElementById('preco_tab' + lin2).value) *
                                (1-Number(document.getElementById('vldescfabr' + lin2).value)/100)*
                                (1-Number(document.getElementById('largura2' + lin2).value)/100)*
                                (1-Number(document.getElementById('largura3' + lin2).value)/100)*
                                (1+Number(document.getElementById('ipi' + lin2).value)/100)*
                                (1+Number(document.getElementById('frete' + lin2).value)/100)*
                                (1+Number(document.getElementById('outros' + lin2).value)/100)*
                                (1+Number(document.getElementById('percsubsttrib' + lin2).value)/100)*
                                (1+Number(document.getElementById('vlimpostosdiretos' + lin2).value)/100)*
                                (1+Number(document.getElementById('percentual' + lin2).value)/100);
                            val = val * 0.02;
                            if (val<0.05){val = 0.05}
                            document.getElementById('preconv' + lin).value = (val.toFixed(2)).replace(".", ",");
                            achou = 'S';
                            break;
                        }
                    }
                    if (achou == 'S') { break;}
                    //Varre pra baixo
                    for (var lin2 = lin+1; lin2<document.getElementById('QtdProd').value&&document.getElementById('cdproduto'+ lin2).value == document.getElementById('cdproduto' +lin).value; lin2++) {
                        if (document.getElementById('und'+ lin2).value == 'CT') {
                            val = Number(document.getElementById('preco_tab' + lin2).value) *
                                (1-Number(document.getElementById('vldescfabr' + lin2).value)/100)*
                                (1-Number(document.getElementById('largura2' + lin2).value)/100)*
                                (1-Number(document.getElementById('largura3' + lin2).value)/100)*
                                (1+Number(document.getElementById('ipi' + lin2).value)/100)*
                                (1+Number(document.getElementById('frete' + lin2).value)/100)*
                                (1+Number(document.getElementById('outros' + lin2).value)/100)*
                                (1+Number(document.getElementById('percsubsttrib' + lin2).value)/100)*
                                (1+Number(document.getElementById('vlimpostosdiretos' + lin2).value)/100)*
                                (1+Number(document.getElementById('percentual' + lin2).value)/100);
                            val = val * 0.02;
                            if (val<0.05){val = 0.05}
                            document.getElementById('preconv' + lin).value = (val.toFixed(2)).replace(".", ",");
                            achou = 'S';
                            break;
                        }
                    }
                }
                break;
            case 'C2':
                if (document.getElementById('und' +lin).value == 'CE') {
                    val = Number(document.getElementById('preco_tab' + lin).value) *
                        (1-Number(document.getElementById('vldescfabr' + lin).value)/100)*
                        (1-Number(document.getElementById('largura2' + lin).value)/100)*
                        (1-Number(document.getElementById('largura3' + lin).value)/100)*
                        (1+Number(document.getElementById('ipi' + lin).value)/100)*
                        (1+Number(document.getElementById('frete' + lin).value)/100)*
                        (1+Number(document.getElementById('outros' + lin).value)/100)*
                        (1+Number(document.getElementById('percsubsttrib' + lin).value)/100)*
                        (1+Number(document.getElementById('vlimpostosdiretos' + lin).value)/100)*
                        (1+Number(document.getElementById('percentual' + lin).value)/100);
                    if (val<0.05){val = 0.05}
                    document.getElementById('preconv' + lin).value = (val.toFixed(2)).replace(".", ",");
                }
                if (document.getElementById('und' +lin).value == 'CT') {
                    //Varre pra cima
                    achou = 'N';
                    for (var lin2 = lin-1; lin2>0&&document.getElementById('cdproduto'+ lin2).value == document.getElementById('cdproduto' +lin).value; lin2--) {
                        if (document.getElementById('und'+ lin2).value == 'CE') {
                            val = Number(document.getElementById('preco_tab' + lin2).value) *
                                (1-Number(document.getElementById('vldescfabr' + lin2).value)/100)*
                                (1-Number(document.getElementById('largura2' + lin2).value)/100)*
                                (1-Number(document.getElementById('largura3' + lin2).value)/100)*
                                (1+Number(document.getElementById('ipi' + lin2).value)/100)*
                                (1+Number(document.getElementById('frete' + lin2).value)/100)*
                                (1+Number(document.getElementById('outros' + lin2).value)/100)*
                                (1+Number(document.getElementById('percsubsttrib' + lin2).value)/100)*
                                (1+Number(document.getElementById('vlimpostosdiretos' + lin2).value)/100)*
                                (1+Number(document.getElementById('percentual' + lin2).value)/100);
                            val = val * 1.3;
                            if (val<0.05){val = 0.05}
                            document.getElementById('preconv' + lin).value = (val.toFixed(2)).replace(".", ",");
                            achou = 'S';
                            break;
                        }
                    }
                    if (achou == 'S') { break;}
                    //Varre pra baixo
                    for (var lin2 = lin+1; lin2<document.getElementById('QtdProd').value&&document.getElementById('cdproduto'+ lin2).value == document.getElementById('cdproduto' +lin).value; lin2++) {
                        if (document.getElementById('und'+ lin2).value == 'CE') {
                            val = Number(document.getElementById('preco_tab' + lin2).value) *
                                (1-Number(document.getElementById('vldescfabr' + lin2).value)/100)*
                                (1-Number(document.getElementById('largura2' + lin2).value)/100)*
                                (1-Number(document.getElementById('largura3' + lin2).value)/100)*
                                (1+Number(document.getElementById('ipi' + lin2).value)/100)*
                                (1+Number(document.getElementById('frete' + lin2).value)/100)*
                                (1+Number(document.getElementById('outros' + lin2).value)/100)*
                                (1+Number(document.getElementById('percsubsttrib' + lin2).value)/100)*
                                (1+Number(document.getElementById('vlimpostosdiretos' + lin2).value)/100)*
                                (1+Number(document.getElementById('percentual' + lin2).value)/100);
                            val = val * 1.3;
                            if (val<0.05){val = 0.05}
                            document.getElementById('preconv' + lin).value = (val.toFixed(2)).replace(".", ",");
                            achou = 'S';
                            break;
                        }
                    }
                }
                if (document.getElementById('und' +lin).value == 'PC') {
                    //Varre pra cima
                    achou = 'N';
                    for (var lin2 = lin-1; lin2>0&&document.getElementById('cdproduto'+ lin2).value == document.getElementById('cdproduto' +lin).value; lin2--) {
                        if (document.getElementById('und'+ lin2).value == 'CE') {
                            val = Number(document.getElementById('preco_tab' + lin2).value) *
                                (1-Number(document.getElementById('vldescfabr' + lin2).value)/100)*
                                (1-Number(document.getElementById('largura2' + lin2).value)/100)*
                                (1-Number(document.getElementById('largura3' + lin2).value)/100)*
                                (1+Number(document.getElementById('ipi' + lin2).value)/100)*
                                (1+Number(document.getElementById('frete' + lin2).value)/100)*
                                (1+Number(document.getElementById('outros' + lin2).value)/100)*
                                (1+Number(document.getElementById('percsubsttrib' + lin2).value)/100)*
                                (1+Number(document.getElementById('vlimpostosdiretos' + lin2).value)/100)*
                                (1+Number(document.getElementById('percentual' + lin2).value)/100);
                            val = val * 0.026;
                            if (val<0.05){val = 0.05}
                            document.getElementById('preconv' + lin).value = (val.toFixed(2)).replace(".", ",");
                            achou = 'S';
                            break;
                        }
                    }
                    if (achou == 'S') { break;}
                    //Varre pra baixo
                    for (var lin2 = lin+1; lin2<document.getElementById('QtdProd').value&&document.getElementById('cdproduto'+ lin2).value == document.getElementById('cdproduto' +lin).value; lin2++) {
                        if (document.getElementById('und'+ lin2).value == 'CE') {
                            val = Number(document.getElementById('preco_tab' + lin2).value) *
                                (1-Number(document.getElementById('vldescfabr' + lin2).value)/100)*
                                (1-Number(document.getElementById('largura2' + lin2).value)/100)*
                                (1-Number(document.getElementById('largura3' + lin2).value)/100)*
                                (1+Number(document.getElementById('ipi' + lin2).value)/100)*
                                (1+Number(document.getElementById('frete' + lin2).value)/100)*
                                (1+Number(document.getElementById('outros' + lin2).value)/100)*
                                (1+Number(document.getElementById('percsubsttrib' + lin2).value)/100)*
                                (1+Number(document.getElementById('vlimpostosdiretos' + lin2).value)/100)*
                                (1+Number(document.getElementById('percentual' + lin2).value)/100);
                            val = val * 0.026;
                            if (val<0.05){val = 0.05}
                            document.getElementById('preconv' + lin).value = (val.toFixed(2)).replace(".", ",");
                            achou = 'S';
                            break;
                        }
                    }
                }
                break;
            case 'C3':
                if (document.getElementById('und' +lin).value == 'CT') {
                    val = Number(document.getElementById('preco_tab' + lin).value) *
                        (1-Number(document.getElementById('vldescfabr' + lin).value)/100)*
                        (1-Number(document.getElementById('largura2' + lin).value)/100)*
                        (1-Number(document.getElementById('largura3' + lin).value)/100)*
                        (1+Number(document.getElementById('ipi' + lin).value)/100)*
                        (1+Number(document.getElementById('frete' + lin).value)/100)*
                        (1+Number(document.getElementById('outros' + lin).value)/100)*
                        (1+Number(document.getElementById('percsubsttrib' + lin).value)/100)*
                        (1+Number(document.getElementById('vlimpostosdiretos' + lin).value)/100)*
                        (1+Number(document.getElementById('percentual' + lin).value)/100);
                    if (val<0.05){val = 0.05}
                    document.getElementById('preconv' + lin).value = (val.toFixed(2)).replace(".", ",");
                }
                if (document.getElementById('und'+lin).value == 'PC') {
                    //Varre pra cima
                    achou = 'N';
                    for (var lin2 = lin-1; lin2>0&&document.getElementById('cdproduto'+ lin2).value == document.getElementById('cdproduto' +lin).value; lin2--) {
                        if (document.getElementById('und'+ lin2).value == 'CT') {
                            val = Number(document.getElementById('preco_tab' + lin2).value) *
                                (1-Number(document.getElementById('vldescfabr' + lin2).value)/100)*
                                (1-Number(document.getElementById('largura2' + lin2).value)/100)*
                                (1-Number(document.getElementById('largura3' + lin2).value)/100)*
                                (1+Number(document.getElementById('ipi' + lin2).value)/100)*
                                (1+Number(document.getElementById('frete' + lin2).value)/100)*
                                (1+Number(document.getElementById('outros' + lin2).value)/100)*
                                (1+Number(document.getElementById('percsubsttrib' + lin2).value)/100)*
                                (1+Number(document.getElementById('vlimpostosdiretos' + lin2).value)/100)*
                                (1+Number(document.getElementById('percentual' + lin2).value)/100);
                            val = val * 0.015;
                            if (val<0.05){val = 0.05}
                            document.getElementById('preconv' + lin).value = (val.toFixed(2)).replace(".", ",");
                            achou = 'S';
                            break;
                        }
                    }
                    if (achou == 'S') { break;}
                    //Varre pra baixo
                    for (var lin2 = lin+1; lin2<document.getElementById('QtdProd').value&&document.getElementById('cdproduto'+ lin2).value == document.getElementById('cdproduto' +lin).value; lin2++) {
                        if (document.getElementById('und'+ lin2).value == 'CT') {
                            val = Number(document.getElementById('preco_tab' + lin2).value) *
                                (1-Number(document.getElementById('vldescfabr' + lin2).value)/100)*
                                (1-Number(document.getElementById('largura2' + lin2).value)/100)*
                                (1-Number(document.getElementById('largura3' + lin2).value)/100)*
                                (1+Number(document.getElementById('ipi' + lin2).value)/100)*
                                (1+Number(document.getElementById('frete' + lin2).value)/100)*
                                (1+Number(document.getElementById('outros' + lin2).value)/100)*
                                (1+Number(document.getElementById('percsubsttrib' + lin2).value)/100)*
                                (1+Number(document.getElementById('vlimpostosdiretos' + lin2).value)/100)*
                                (1+Number(document.getElementById('percentual' + lin2).value)/100);
                            val = val * 0.015;
                            if (val<0.05){val = 0.05}
                            document.getElementById('preconv' + lin).value = (val.toFixed(2)).replace(".", ",");
                            achou = 'S';
                            break;
                        }
                    }
                }
                break;
            case 'C4':
                if (document.getElementById('und' +lin).value == 'CT') {
                    val = Number(document.getElementById('preco_tab' + lin).value) *
                        (1-Number(document.getElementById('vldescfabr' + lin).value)/100)*
                        (1-Number(document.getElementById('largura2' + lin).value)/100)*
                        (1-Number(document.getElementById('largura3' + lin).value)/100)*
                        (1+Number(document.getElementById('ipi' + lin).value)/100)*
                        (1+Number(document.getElementById('frete' + lin).value)/100)*
                        (1+Number(document.getElementById('outros' + lin).value)/100)*
                        (1+Number(document.getElementById('percsubsttrib' + lin).value)/100)*
                        (1+Number(document.getElementById('vlimpostosdiretos' + lin).value)/100)*
                        (1+Number(document.getElementById('percentual' + lin).value)/100);
                    if (val<0.05){val = 0.05}
                    document.getElementById('preconv' + lin).value = (val.toFixed(2)).replace(".", ",");
                }
                if (document.getElementById('und' +lin).value == 'PC') {
                    //Varre pra cima
                    achou = 'N';
                    for (var lin2 = lin-1; lin2>0&&document.getElementById('cdproduto'+ lin2).value == document.getElementById('cdproduto' +lin).value; lin2--) {
                        if (document.getElementById('und'+ lin2).value == 'CT') {
                            val = Number(document.getElementById('preco_tab' + lin2).value) *
                                (1-Number(document.getElementById('vldescfabr' + lin2).value)/100)*
                                (1-Number(document.getElementById('largura2' + lin2).value)/100)*
                                (1-Number(document.getElementById('largura3' + lin2).value)/100)*
                                (1+Number(document.getElementById('ipi' + lin2).value)/100)*
                                (1+Number(document.getElementById('frete' + lin2).value)/100)*
                                (1+Number(document.getElementById('outros' + lin2).value)/100)*
                                (1+Number(document.getElementById('percsubsttrib' + lin2).value)/100)*
                                (1+Number(document.getElementById('vlimpostosdiretos' + lin2).value)/100)*
                                (1+Number(document.getElementById('percentual' + lin2).value)/100);
                            val = val * 0.013;
                            if (val<0.05){val = 0.05}
                            document.getElementById('preconv' + lin).value = (val.toFixed(2)).replace(".", ",");
                            achou = 'S';
                            break;
                        }
                    }
                    if (achou == 'S') { break;}
                    //Varre pra baixo
                    for (var lin2 = lin+1; lin2<document.getElementById('QtdProd').value&&document.getElementById('cdproduto'+ lin2).value == document.getElementById('cdproduto' +lin).value; lin2++) {
                        if (document.getElementById('und'+ lin2).value == 'CT') {
                            val = Number(document.getElementById('preco_tab' + lin2).value) *
                                (1-Number(document.getElementById('vldescfabr' + lin2).value)/100)*
                                (1-Number(document.getElementById('largura2' + lin2).value)/100)*
                                (1-Number(document.getElementById('largura3' + lin2).value)/100)*
                                (1+Number(document.getElementById('ipi' + lin2).value)/100)*
                                (1+Number(document.getElementById('frete' + lin2).value)/100)*
                                (1+Number(document.getElementById('outros' + lin2).value)/100)*
                                (1+Number(document.getElementById('percsubsttrib' + lin2).value)/100)*
                                (1+Number(document.getElementById('vlimpostosdiretos' + lin2).value)/100)*
                                (1+Number(document.getElementById('percentual' + lin2).value)/100);
                            val = val * 0.013;
                            if (val<0.05){val = 0.05}
                            document.getElementById('preconv' + lin).value = (val.toFixed(2)).replace(".", ",");
                            achou = 'S';
                            break;
                        }
                    }
                }
                break;
            case 'C5':
                if (document.getElementById('und' +lin).value == 'PC') {
                    val = Number(document.getElementById('preco_tab' + lin).value) *
                        (1-Number(document.getElementById('vldescfabr' + lin).value)/100)*
                        (1-Number(document.getElementById('largura2' + lin).value)/100)*
                        (1-Number(document.getElementById('largura3' + lin).value)/100)*
                        (1+Number(document.getElementById('ipi' + lin).value)/100)*
                        (1+Number(document.getElementById('frete' + lin).value)/100)*
                        (1+Number(document.getElementById('outros' + lin).value)/100)*
                        (1+Number(document.getElementById('percsubsttrib' + lin).value)/100)*
                        (1+Number(document.getElementById('vlimpostosdiretos' + lin).value)/100)*
                        (1+Number(document.getElementById('percentual' + lin).value)/100);
                    if (val<0.05){val = 0.05}
                    document.getElementById('preconv' + lin).value = (val.toFixed(2)).replace(".", ",");
                }
                if (document.getElementById('und' +lin).value == 'CT') {
                    //Varre pra cima
                    achou = 'N';
                    for (var lin2 = lin-1; lin2>0&&document.getElementById('cdproduto'+ lin2).value == document.getElementById('cdproduto' +lin).value; lin2--) {
                        if (document.getElementById('und'+ lin2).value == 'CT') {
                            val = Number(document.getElementById('preco_tab' + lin2).value) *
                                (1-Number(document.getElementById('vldescfabr' + lin2).value)/100)*
                                (1-Number(document.getElementById('largura2' + lin2).value)/100)*
                                (1-Number(document.getElementById('largura3' + lin2).value)/100)*
                                (1+Number(document.getElementById('ipi' + lin2).value)/100)*
                                (1+Number(document.getElementById('frete' + lin2).value)/100)*
                                (1+Number(document.getElementById('outros' + lin2).value)/100)*
                                (1+Number(document.getElementById('percsubsttrib' + lin2).value)/100)*
                                (1+Number(document.getElementById('vlimpostosdiretos' + lin2).value)/100)*
                                (1+Number(document.getElementById('percentual' + lin2).value)/100);
                            val = val * 80;
                            if (val<0.05){val = 0.05}
                            document.getElementById('preconv' + lin).value = (val.toFixed(2)).replace(".", ",");
                            achou = 'S';
                            break;
                        }
                    }
                    if (achou == 'S') { break;}
                    //Varre pra baixo
                    for (var lin2 = lin+1; lin2<document.getElementById('QtdProd').value&&document.getElementById('cdproduto'+ lin2).value == document.getElementById('cdproduto' +lin).value; lin2++) {
                        if (document.getElementById('und'+ lin2).value == 'CT') {
                            val = Number(document.getElementById('preco_tab' + lin2).value) *
                                (1-Number(document.getElementById('vldescfabr' + lin2).value)/100)*
                                (1-Number(document.getElementById('largura2' + lin2).value)/100)*
                                (1-Number(document.getElementById('largura3' + lin2).value)/100)*
                                (1+Number(document.getElementById('ipi' + lin2).value)/100)*
                                (1+Number(document.getElementById('frete' + lin2).value)/100)*
                                (1+Number(document.getElementById('outros' + lin2).value)/100)*
                                (1+Number(document.getElementById('percsubsttrib' + lin2).value)/100)*
                                (1+Number(document.getElementById('vlimpostosdiretos' + lin2).value)/100)*
                                (1+Number(document.getElementById('percentual' + lin2).value)/100);
                            val = val * 80;
                            if (val<0.05){val = 0.05}
                            document.getElementById('preconv' + lin).value = (val.toFixed(2)).replace(".", ",");
                            achou = 'S';
                            break;
                        }
                    }
                }
                break;
            case 'K1':
                if (document.getElementById('und' +lin).value == 'KG') {
                    val = Number(document.getElementById('preco_tab' + lin).value) *
                        (1-Number(document.getElementById('vldescfabr' + lin).value)/100)*
                        (1-Number(document.getElementById('largura2' + lin).value)/100)*
                        (1-Number(document.getElementById('largura3' + lin).value)/100)*
                        (1+Number(document.getElementById('ipi' + lin).value)/100)*
                        (1+Number(document.getElementById('frete' + lin).value)/100)*
                        (1+Number(document.getElementById('outros' + lin).value)/100)*
                        (1+Number(document.getElementById('percsubsttrib' + lin).value)/100)*
                        (1+Number(document.getElementById('vlimpostosdiretos' + lin).value)/100)*
                        (1+Number(document.getElementById('percentual' + lin).value)/100);
                    if (val<0.05){val = 0.05}
                    document.getElementById('preconv' + lin).value = (val.toFixed(2)).replace(".", ",");
                }
                if (document.getElementById('und' +lin).value == 'GR') {
                    //Varre pra cima
                    achou = 'N';
                    for (var lin2 = lin-1; lin2>0&&document.getElementById('cdproduto'+ lin2).value == document.getElementById('cdproduto' +lin).value; lin2--) {
                        if (document.getElementById('und'+ lin2).value == 'CT') {
                            val = Number(document.getElementById('preco_tab' + lin2).value) *
                                (1-Number(document.getElementById('vldescfabr' + lin2).value)/100)*
                                (1-Number(document.getElementById('largura2' + lin2).value)/100)*
                                (1-Number(document.getElementById('largura3' + lin2).value)/100)*
                                (1+Number(document.getElementById('ipi' + lin2).value)/100)*
                                (1+Number(document.getElementById('frete' + lin2).value)/100)*
                                (1+Number(document.getElementById('outros' + lin2).value)/100)*
                                (1+Number(document.getElementById('percsubsttrib' + lin2).value)/100)*
                                (1+Number(document.getElementById('vlimpostosdiretos' + lin2).value)/100)*
                                (1+Number(document.getElementById('percentual' + lin2).value)/100);
                            val = val * 0.0016;
                            if (val<0.05){val = 0.05}
                            document.getElementById('preconv' + lin).value = (val.toFixed(2)).replace(".", ",");
                            achou = 'S';
                            break;
                        }
                    }
                    if (achou == 'S') { break;}
                    //Varre pra baixo
                    for (var lin2 = lin+1; lin2<document.getElementById('QtdProd').value&&document.getElementById('cdproduto'+ lin2).value == document.getElementById('cdproduto' +lin).value; lin2++) {
                        if (document.getElementById('und'+ lin2).value == 'CT') {
                            val = Number(document.getElementById('preco_tab' + lin2).value) *
                                (1-Number(document.getElementById('vldescfabr' + lin2).value)/100)*
                                (1-Number(document.getElementById('largura2' + lin2).value)/100)*
                                (1-Number(document.getElementById('largura3' + lin2).value)/100)*
                                (1+Number(document.getElementById('ipi' + lin2).value)/100)*
                                (1+Number(document.getElementById('frete' + lin2).value)/100)*
                                (1+Number(document.getElementById('outros' + lin2).value)/100)*
                                (1+Number(document.getElementById('percsubsttrib' + lin2).value)/100)*
                                (1+Number(document.getElementById('vlimpostosdiretos' + lin2).value)/100)*
                                (1+Number(document.getElementById('percentual' + lin2).value)/100);
                            val = val * 0.0016;
                            if (val<0.05){val = 0.05}
                            document.getElementById('preconv' + lin).value = (val.toFixed(2)).replace(".", ",");
                            achou = 'S';
                            break;
                        }
                    }
                }
                break;
            case 'M1':
                if (document.getElementById('und' +lin).value == 'MI') {
                    val = Number(document.getElementById('preco_tab' + lin).value) *
                        (1-Number(document.getElementById('vldescfabr' + lin).value)/100)*
                        (1-Number(document.getElementById('largura2' + lin).value)/100)*
                        (1-Number(document.getElementById('largura3' + lin).value)/100)*
                        (1+Number(document.getElementById('ipi' + lin).value)/100)*
                        (1+Number(document.getElementById('frete' + lin).value)/100)*
                        (1+Number(document.getElementById('outros' + lin).value)/100)*
                        (1+Number(document.getElementById('percsubsttrib' + lin).value)/100)*
                        (1+Number(document.getElementById('vlimpostosdiretos' + lin).value)/100)*
                        (1+Number(document.getElementById('percentual' + lin).value)/100);
                    if (val<0.05){val = 0.05}
                    document.getElementById('preconv' + lin).value = (val.toFixed(2)).replace(".", ",");
                }
                if (document.getElementById('und' +lin).value == 'CT') {
                    //Varre pra cima
                    achou = 'N';
                    for (var lin2 = lin-1; lin2>0&&document.getElementById('cdproduto'+ lin2).value == document.getElementById('cdproduto' +lin).value; lin2--) {
                        if (document.getElementById('und'+ lin2).value == 'MI') {
                            val = Number(document.getElementById('preco_tab' + lin2).value) *
                                (1-Number(document.getElementById('vldescfabr' + lin2).value)/100)*
                                (1-Number(document.getElementById('largura2' + lin2).value)/100)*
                                (1-Number(document.getElementById('largura3' + lin2).value)/100)*
                                (1+Number(document.getElementById('ipi' + lin2).value)/100)*
                                (1+Number(document.getElementById('frete' + lin2).value)/100)*
                                (1+Number(document.getElementById('outros' + lin2).value)/100)*
                                (1+Number(document.getElementById('percsubsttrib' + lin2).value)/100)*
                                (1+Number(document.getElementById('vlimpostosdiretos' + lin2).value)/100)*
                                (1+Number(document.getElementById('percentual' + lin2).value)/100);
                            val = val * 0.015;
                            if (val<0.05){val = 0.015}
                            document.getElementById('preconv' + lin).value = (val.toFixed(2)).replace(".", ",");
                            achou = 'S';
                            break;
                        }
                    }
                    if (achou == 'S') { break;}
                    //Varre pra baixo
                    for (var lin2 = lin+1; lin2<document.getElementById('QtdProd').value&&document.getElementById('cdproduto'+ lin2).value == document.getElementById('cdproduto' +lin).value; lin2++) {
                        if (document.getElementById('und'+ lin2).value == 'MI') {
                            val = Number(document.getElementById('preco_tab' + lin2).value) *
                                (1-Number(document.getElementById('vldescfabr' + lin2).value)/100)*
                                (1-Number(document.getElementById('largura2' + lin2).value)/100)*
                                (1-Number(document.getElementById('largura3' + lin2).value)/100)*
                                (1+Number(document.getElementById('ipi' + lin2).value)/100)*
                                (1+Number(document.getElementById('frete' + lin2).value)/100)*
                                (1+Number(document.getElementById('outros' + lin2).value)/100)*
                                (1+Number(document.getElementById('percsubsttrib' + lin2).value)/100)*
                                (1+Number(document.getElementById('vlimpostosdiretos' + lin2).value)/100)*
                                (1+Number(document.getElementById('percentual' + lin2).value)/100);
                            val = val * 0.015;
                            if (val<0.05){val = 0.015}
                            document.getElementById('preconv' + lin).value = (val.toFixed(2)).replace(".", ",");
                            achou = 'S';
                            break;
                        }
                    }
                }
                if (document.getElementById('und' +lin).value == 'PC') {
                    //Varre pra cima
                    achou = 'N';
                    for (var lin2 = lin-1; lin2>0&&document.getElementById('cdproduto'+ lin2).value == document.getElementById('cdproduto' +lin).value; lin2--) {
                        if (document.getElementById('und'+ lin2).value == 'MI') {
                            val = Number(document.getElementById('preco_tab' + lin2).value) *
                                (1-Number(document.getElementById('vldescfabr' + lin2).value)/100)*
                                (1-Number(document.getElementById('largura2' + lin2).value)/100)*
                                (1-Number(document.getElementById('largura3' + lin2).value)/100)*
                                (1+Number(document.getElementById('ipi' + lin2).value)/100)*
                                (1+Number(document.getElementById('frete' + lin2).value)/100)*
                                (1+Number(document.getElementById('outros' + lin2).value)/100)*
                                (1+Number(document.getElementById('percsubsttrib' + lin2).value)/100)*
                                (1+Number(document.getElementById('vlimpostosdiretos' + lin2).value)/100)*
                                (1+Number(document.getElementById('percentual' + lin2).value)/100);
                            val = val * 0.023;
                            if (val<0.05){val = 0.015}
                            document.getElementById('preconv' + lin).value = (val.toFixed(2)).replace(".", ",");
                            achou = 'S';
                            break;
                        }
                    }
                    if (achou == 'S') { break;}
                    //Varre pra baixo
                    for (var lin2 = lin+1; lin2<document.getElementById('QtdProd').value&&document.getElementById('cdproduto'+ lin2).value == document.getElementById('cdproduto' +lin).value; lin2++) {
                        if (document.getElementById('und'+ lin2).value == 'MI') {
                            val = Number(document.getElementById('preco_tab' + lin2).value) *
                                (1-Number(document.getElementById('vldescfabr' + lin2).value)/100)*
                                (1-Number(document.getElementById('largura2' + lin2).value)/100)*
                                (1-Number(document.getElementById('largura3' + lin2).value)/100)*
                                (1+Number(document.getElementById('ipi' + lin2).value)/100)*
                                (1+Number(document.getElementById('frete' + lin2).value)/100)*
                                (1+Number(document.getElementById('outros' + lin2).value)/100)*
                                (1+Number(document.getElementById('percsubsttrib' + lin2).value)/100)*
                                (1+Number(document.getElementById('vlimpostosdiretos' + lin2).value)/100)*
                                (1+Number(document.getElementById('percentual' + lin2).value)/100);
                            val = val * 0.023;
                            if (val<0.05){val = 0.015}
                            document.getElementById('preconv' + lin).value = (val.toFixed(2)).replace(".", ",");
                            achou = 'S';
                            break;
                        }
                    }
                }
                break;
        }
    } else {
        document.getElementById('preconv' + lin).value = "";
    }
};
function validaForm(){
    for (var y = 0; y < document.getElementById('QtdProd').value; y++) {

        if (document.forms["formPreco"]['prodlin' + y].checked) {
            document.forms["formPreco"]['prodlin' + y].value = "S";
        }else{
            document.forms["formPreco"]['prodlin' + y].value = "N";
        }
        document.forms['formPreco'].submit();

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
