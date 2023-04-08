<?php
/**
 * Autor: Marluce Almeida
 * Data de criacao: 26/05/18
 */
require_once("conexao_miautomec.php");

$QtdProd = $_POST['QtdProd'];
for ($i = 0; $i < $QtdProd; $i++) {
    $sql = "update produtopreco set preco = " . round(unmask($_POST['preconv'.$i]),2) . ", preco_tab = " . round(unmask($_POST['liqnv'.$i]),2) . ", vlcustoinicial = " . round(unmask($_POST['liqnv'.$i]),2) . ", percentual = " . round(unmask($_POST['lucro'.$i]),2) . ", vlcusto = " . round((unmask($_POST['liqnv'.$i])*(1+unmask($_POST['percsubsttrib'.$i])/100)*(1+unmask($_POST['ipi'.$i])/100)*(1+unmask($_POST['frete'.$i])/100)),2) . ", dtreajustepreco = cast('NOW' as timestamp), dtalter = cast('NOW' as timestamp), dtreajuste = cast('NOW' as timestamp) where cdproduto = " . formata_bd($_POST['cdproduto'.$i]) . " and idpreco = " . $_POST['idpreco'.$i] . " and cdunidade = " . formata_bd($_POST['und'.$i]);
    $rs = ibase_query($conn, $sql)  or die(ibase_errmsg());
    $sql = "update produtobruto_base set desconto1 = " . round(unmask($_POST['nvDesc1']),2) . " where cdproduto = " . formata_bd($_POST['cdproduto'.$i]) ;
    $rs = ibase_query($conn, $sql) or die(ibase_errmsg());
}

echo ("<script type='text/javascript'>alert('Valores atualizados com sucesso!');</script>");
echo "<html><body onload=\"location.href='altMiautomec_Gautomatico.php'\"></body></html>";

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
            return  str_replace(",",".",(str_replace(".","",$valor)));
        }
    }

?>