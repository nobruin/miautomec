<?php
/**
 * Autor: Marluce Almeida
 * Data de criacao: 28/05/18
 */
require_once("conexao_miautomec.php");

$QtdProd = $_POST['QtdProd'];
for ($i = 0; $i < $QtdProd; $i++) {
    $sqlalt = "";
    if ($_POST['bruto'.$i] != $_POST['brutonv'.$i]) {
        $sqlalt = " bruto = " . round(unmask($_POST['brutonv'.$i]),2);
        $camposins = '(cdproduto,bruto';
        $camposval = '('.formata_bd($_POST['cdproduto'.$i]).','.round(unmask($_POST['brutonv'.$i]),2);
    }
    if ($_POST['desc1'.$i] != $_POST['desc1nv'.$i]) {
        if ($sqlalt != "") {
            $sqlalt = $sqlalt. ", desconto1 = " . round(unmask($_POST['desc1nv'.$i]),2);
            $camposins = $camposins.',desconto1';
            $camposval = $camposval.','.round(unmask($_POST['desc1nv'.$i]),2);
        } else {
            $sqlalt = " desconto1 = " . round(unmask($_POST['desc1nv'.$i]),2);
            $camposins = '(cdproduto,desconto1';
            $camposval = '('.formata_bd($_POST['cdproduto'.$i]).','.round(unmask($_POST['desc1nv'.$i]),2);
        }
    }
    if ($_POST['desc2'.$i] != $_POST['desc2nv'.$i]) {
        if ($sqlalt != "") {
            $sqlalt = $sqlalt. ", desconto2 = " . round(unmask($_POST['desc2nv'.$i]),2);
            $camposins = $camposins.',desconto2';
            $camposval = $camposval.','.round(unmask($_POST['desc2nv'.$i]),2);
        } else {
            $sqlalt = " desconto2 = " . round(unmask($_POST['desc2nv'.$i]),2);
            $camposins = '(cdproduto,desconto2';
            $camposval = '('.formata_bd($_POST['cdproduto'.$i]).','.round(unmask($_POST['desc2nv'.$i]),2);
        }
    }
    if ($_POST['desc3'.$i] != $_POST['desc3nv'.$i]) {
        if ($sqlalt != "") {
            $sqlalt = $sqlalt. ", desconto3 = " . round(unmask($_POST['desc3nv'.$i]),2);
            $camposins = $camposins.',desconto3';
            $camposval = $camposval.','.round(unmask($_POST['desc3nv'.$i]),2);
        } else {
            $sqlalt = " desconto3 = " . round(unmask($_POST['desc3nv'.$i]),2);
            $camposins = '(cdproduto,desconto3';
            $camposval = '('.formata_bd($_POST['cdproduto'.$i]).','.round(unmask($_POST['desc3nv'.$i]),2);
        }
    }
    if ($_POST['lucro'.$i] != $_POST['lucronv'.$i]) {
        if ($sqlalt != "") {
            $sqlalt = $sqlalt. ", lucro = " . round(unmask($_POST['lucronv'.$i]),2);
            $camposins = $camposins.',lucro';
            $camposval = $camposval.','.round(unmask($_POST['lucronv'.$i]),2);
        } else {
            $sqlalt = " lucro = " . round(unmask($_POST['lucronv'.$i]),2);
            $camposins = '(cdproduto,lucro';
            $camposval = '('.formata_bd($_POST['cdproduto'.$i]).','.round(unmask($_POST['lucronv'.$i]),2);
        }
    }
    if ($sqlalt != "" ) {
        $sql = "select cdproduto from PRODUTOBRUTO_BASE where cdproduto = " . formata_bd($_POST['cdproduto'.$i]);
        $rs = ibase_query($conn, $sql)  or die(ibase_errmsg());
        $count = 0;
        while ($row[$count] = ibase_fetch_assoc($rs))
            $count++;
        if ($count == 0) {
            $sql = "insert into produtobruto_base " .$camposins .") VALUES " . $camposval .")";
            $rs = ibase_query($conn, $sql)  or die(ibase_errmsg());
        } else {
            $sql = "update produtobruto_base set " .$sqlalt ." where cdproduto = " . formata_bd($_POST['cdproduto'.$i]) ;
            $rs = ibase_query($conn, $sql)  or die(ibase_errmsg());
            $calc = doubleval(round(unmask($_POST['brutonv'.$i]),2)) * (1-doubleval(round(unmask($_POST['desc1nv'.$i]),2))/100)*(1-doubleval(round(unmask($_POST['desc2nv'.$i]),2))/100)*(1-doubleval(round(unmask($_POST['desc3nv'.$i]),2))/100);
            $lucro = doubleval(round(unmask($_POST['lucronv'.$i]),2));
            $sql = "update produtopreco set preco_tab = " . $calc . ", vlcustoinicial = " . $calc . ", percentual = " . $lucro . ", vlcusto = (".$calc."*(1+percsubsttrib/100)*".(1+doubleval(round(unmask($_POST['ipi'.$i]),2))/100)."+".(1+doubleval(round(unmask($_POST['frete'.$i]),2))/100)."), dtreajustepreco = cast('NOW' as timestamp), dtalter = cast('NOW' as timestamp), dtreajuste = cast('NOW' as timestamp) where cdproduto = " . formata_bd($_POST['cdproduto'.$i]);
            $rs = ibase_query($conn, $sql)  or die(ibase_errmsg());

        }

    }
}

echo ("<script type='text/javascript'>alert('Valores atualizados com sucesso!');</script>");
echo "<html><body onload=\"location.href='altMiautomec_ProdPBase.php'\"></body></html>";

    function formata_bd($txtcampo)
    {
        if (empty($txtcampo)||trim($txtcampo) == ''||is_null($txtcampo)) {
            return "NULL";
        } else {
            return "'".mb_convert_encoding($txtcampo, 'WINDOWS-1252','UTF-8')."'";
        }
    }

    function unmask($valor){
        if (empty($valor)||trim($valor) == ''||is_null($valor)) {
            return "0";
        }
        else {
            return str_replace(",",".",(str_replace(".","",$valor)));
        }
    }

?>