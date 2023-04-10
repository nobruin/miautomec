<?php
/**
 * Autor: Marluce Almeida
 * Data de criacao: 01/09/15
 * Hora de criacao: 19:00
 */
require_once("conexao_miautomec.php");

$TpAlt = $_POST['TpAlt'];
$Conf = $_POST['conf'];

switch ($TpAlt) {
    case 'UB':

        $valBrutoNv = unmask($_POST['brutonv']);
        $codProd = $_POST['CdProd'];

        $sql = "select distinct pr.cdunidade, pr.preco,
 		        coalesce(b.bruto,0) as BRUTO, coalesce(b.desconto1,0) as DESC1, coalesce(b.desconto2,0) as DESC2,
                coalesce(b.desconto3,0) as DESC3, coalesce(b.lucro,0) as LUCRO, coalesce(p.ipi,0) as IPI, coalesce(p.frete,0) as frete,
		        coalesce(p.outros,0) as outros, coalesce(pr.percsubsttrib,0) as percsubsttrib,
		        coalesce(pr.vlimpostosdiretos,0) as vlimpostosdiretos,
		        p.ro as RO, pr.idpreco as idpreco, coalesce(pr.FATORCONV,0) as FATORCONV,
		        coalesce((select MULTIPLICADOR from fator_ro where ro = p.ro and cdunidade = pr.cdunidade),0) as MULTIPLICADOR,
		        (select cdunidade from fator_ro where ro = p.ro and MULTIPLICADOR = 0) as und_princ
                from produto p
                LEFT JOIN produto_codforn pf ON (pf.cdproduto = p.cdproduto)
                LEFT JOIN fornecedor f ON (f.cdFornecedor = pf.cdFornecedor)
                LEFT JOIN grupos g on (p.cdgrupo = g.cdgrupo)
                LEFT JOIN produtopreco pr on (p.cdproduto = pr.cdproduto)
                LEFT JOIN produtobruto_base b on (p.cdproduto = b.cdproduto)
                where p.cdproduto = '".$codProd."'";

        $rs = ibase_query($conn, $sql) or die(ibase_errmsg());
        $contarray = 0;
        $lista_prod = array();
        $lista_prod[$contarray] = array();
        while ($lista_prod[$contarray] = ibase_fetch_assoc($rs)) {
            $contarray = $contarray + 1;
            $lista_prod[$contarray] = array();
        }

        array_pop($lista_prod);
        for ($i = 0; $i < count($lista_prod); $i++) {
            $campos = $lista_prod[$i];

            $ro = $campos['RO'];
            //Valor principal calculado com o novo preço bruto
            $val = $valBrutoNv *
                (1-($campos['DESC1']/100))*
                (1-($campos['DESC2']/100))*
                (1-($campos['DESC3']/100))*
                (1+($campos['IPI']/100))*
                (1+($campos['FRETE']/100))*
                (1+($campos['OUTROS']/100))*
                (1+($campos['PERCSUBSTTRIB']/100))*
                (1+($campos['VLIMPOSTOSDIRETOS']/100))*
                (1+($campos['LUCRO']/100));
            $calc = $valBrutoNv * (1-doubleval($campos["DESC1"])/100)*(1-doubleval($campos["DESC2"])/100)*(1-doubleval($campos["DESC3"])/100);
            $lucro = doubleval($campos['LUCRO']);
            $calc2 = $calc * (1+doubleval($campos["PERCSUBSTTRIB"])/100)* (1+doubleval($campos["IPI"])/100)*(1+doubleval($campos["FRETE"])/100);
//echo 'brutonv:'.$valBrutoNv."<br>";
//echo 'desc1:'.$campos['DESC1']."<br>";
//echo 'desc2:'.$campos['DESC2']."<br>";
//echo 'desc3:'.$campos['DESC3']."<br>";
//echo 'ipi:'.$campos['IPI']."<br>";
//echo 'frete:'.$campos['FRETE']."<br>";
//echo 'outros:'.$campos['OUTROS']."<br>";
//echo 'perctrib:'.$campos['PERCSUBSTTRIB']."<br>";
//echo 'imposto:'.$campos['VLIMPOSTOSDIRETOS']."<br>";
//echo 'lucro:'.$campos['LUCRO']."<br>";
//echo 'val:'.$val."<br>";
//echo 'MULTIPLICADOR:'.$campos['MULTIPLICADOR']."<br>";
//echo 'FATORCONV:'.$campos['FATORCONV']."<br>";

            switch ($ro) {
                case 'P1':
                    $preco_novo = $val;
                    break;
                case 'C1':
                    if ($campos['CDUNIDADE'] == 'CT') {
                        $preco_novo = $val;
                    } else {
                        $preco_novo = $val * $campos['MULTIPLICADOR'] * $campos['FATORCONV'] ;
                    }
                    break;
                case 'C2':
                    if ($campos['CDUNIDADE'] == 'CE') {
                        $preco_novo = $val;
                    } else {
                        $preco_novo = $val * $campos['MULTIPLICADOR'] * $campos['FATORCONV'] ;
                    }
                    break;
                case 'C3':
                    if ($campos['CDUNIDADE'] == 'CT') {
                        $preco_novo = $val;
                    } else {
                        $preco_novo = $val * $campos['MULTIPLICADOR'] * $campos['FATORCONV'] ;
                    }
                    break;
                case 'C4':
                    if ($campos['CDUNIDADE'] == 'CT') {
                        $preco_novo = $val;
                    } else {
                        $preco_novo = $val * $campos['MULTIPLICADOR'] * $campos['FATORCONV'] ;
                    }
                    break;
                case 'C5':
                    if ($campos['CDUNIDADE'] == 'PC') {
                        $preco_novo = $val;
                    } else {
                        $preco_novo = $val * $campos['MULTIPLICADOR'] * $campos['FATORCONV'] ;
                    }
                    break;
                case 'K1':
                    if ($campos['CDUNIDADE'] == 'KG') {
                        $preco_novo = $val;
                    } else {
                        $preco_novo = $val * $campos['MULTIPLICADOR'] * $campos['FATORCONV'] ;
                    }
                    break;
                case 'M1':
                    if ($campos['CDUNIDADE'] == 'MI') {
                        $preco_novo = $val;
                    } else {
                        $preco_novo = $val * $campos['MULTIPLICADOR'] * $campos['FATORCONV'] ;
                    }
                    break;
                case 'M2':
                    if ($campos['CDUNIDADE'] == 'MI') {
                        $preco_novo = $val;
                    } else {
                        $preco_novo = $val * $campos['MULTIPLICADOR'] * $campos['FATORCONV'] ;
                    }
                    break;
                case 'Q1':
                    if ($campos['CDUNIDADE'] == 'KG') {
                        $preco_novo = $val;
                    } else {
                        $preco_novo = $val * $campos['MULTIPLICADOR'] * $campos['FATORCONV'] ;
                    }
                    break;
                case 'R1':
                    if ($campos['CDUNIDADE'] == 'PC') {
                        $preco_novo = $val;
                    } else {
                        $preco_novo = $val * $campos['MULTIPLICADOR'] * $campos['FATORCONV'] ;
                    }
                    break;
                default:
                    echo ("<script type='text/javascript'>alert('Fator RO ".$ro." Inválido no produto!');</script>");
                    echo "<html><body onload=\"location.href='altMiautomec_Ubruto.php'\"></body></html>";
                    return;
                    break;
            }
            if ($preco_novo<0.05){
                $preco_novo = 0.05;
            }
            if ($Conf == 'S') {
                //echo '$preco_novo:'.$preco_novo."<br>";
                $sql = "update produtopreco set preco = " . $preco_novo . ", preco_tab = " . $calc . ", vlcustoinicial = " . $calc . ", percentual = " . $lucro . ", vlcusto = " . $calc2 . ", dtreajustepreco = cast('NOW' as timestamp), dtalter = cast('NOW' as timestamp), dtreajuste = cast('NOW' as timestamp) where cdproduto = " . formata_bd($codProd) . " and idpreco = " . $campos['IDPRECO'] . " and cdunidade = " . formata_bd($campos['CDUNIDADE']);
                //echo '$sql:'.$sql."<br>";
                $rs = ibase_query($conn, $sql)  or die(ibase_errmsg());
                $sql = "update produtobruto_base set bruto = " . $valBrutoNv . " where cdproduto = " . formata_bd($codProd) ;
                $rs = ibase_query($conn, $sql) or die(ibase_errmsg());
                //$sql = "insert into produtopreco_historico (cdproduto,idpreco,cdunidade,preco) values (".formata_bd($codProd) . ", " . unmask($campos['IDPRECO']) . ", " . formata_bd($campos['CDUNIDADE']).", ".round(unmask($campos['PRECO']),2).")";
                //$rs = ibase_query($conn, $sql) or die(ibase_errmsg());
            }
        }

        if ($Conf == 'S') {
            echo ("<script type='text/javascript'>alert('Valores atualizados com sucesso!');</script>");
            echo "<html><body onload=\"location.href='altMiautomec_Ubruto.php'\"></body></html>";
        } else {
            echo "<html><body onload=\"location.href='altMiautomec_Ubruto.php?prodcod=".$codProd."&pNovo=".$preco_novo."&bNovo=".$valBrutoNv."&jacalc=S'\"></body></html>";
        }


        break;
    }
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
    function dismask($valor){
    if (empty($valor)||trim($valor) == ''||is_null($valor)) {
        return "0";
    }
    else {
        return  str_replace(".",",",(str_replace(",","",$valor)));
    }
}

?>