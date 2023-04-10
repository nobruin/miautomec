<?php
/**
 * Autor: Marluce Almeida
 * Data de criacao: 08/06/15
 * Hora de criacao: 10:00
 */
?>
<body>
<?php
require_once("conexao_miautomec.php");
if (isset($_POST['comboGrupo'])) {
    if ($_POST['comboGrupo'] != '--') {
        $FiltraGrupo = $_POST['comboGrupo'];
    } else {
        $FiltraGrupo = "";
    }
} else {
    $FiltraGrupo = "";
}
if (isset($_POST['comboUnidade'])) {
    if ($_POST['comboUnidade'] != '--') {
        $FiltraUnidade = $_POST['comboUnidade'];
    } else {
        $FiltraUnidade = "";
    }
} else {
    $FiltraUnidade = "";
}
if (isset($_POST['comboFornecedor'])) {
    if ($_POST['comboFornecedor'] != '--') {
        $FiltraFornecedor = $_POST['comboFornecedor'];
    } else {
        $FiltraFornecedor = "";
    }
} else {
    $FiltraFornecedor = "";
}
if (isset($_POST['prodtxt'])) {
    $FiltraProd = $_POST['prodtxt'];
} else {
    $FiltraProd = "";
}
?>
<div style="color:#1066c7">
<table>
    <tr><td><img src="logo_amorim.JPG"></td>
<form name="form_busca" method="post" id="form_busca" action="alteraPreco.php">
            <td><table><tr><td><b>Grupo:</b> </td><td><select name="comboGrupo" maxlength="50" id="comboGrupo">
                <?php
                $sqlgrupo= "select g.cdgrupo, g.grupos from grupos g where exists (select 1 from produto p where p.cdgrupo = g.cdgrupo) order by 2 asc";
                $rs = ibase_query($conn , $sqlgrupo)  or die(ibase_errmsg());

                echo "<option value ='--' selected>&nbsp;&nbsp;</option>";
                while ($linha = ibase_fetch_assoc($rs)) {
                    if ((string)$linha["CDGRUPO"] == (string)$FiltraGrupo) {
                        $marcaGrupo = " selected";
                    } else {
                        $marcaGrupo = "";
                    }
                    echo "<option value =".$linha["CDGRUPO"].$marcaGrupo.">".mb_convert_encoding($linha["GRUPOS"], 'UTF-8', 'WINDOWS-1252')."</option>";
                }
                ?>
            </select>
            <tr><td><b>Unidade:</b> </td><td><select name="comboUnidade" maxlength="20" id="comboUnidade">
                <?php
                $sqlUnidade= "select distinct cdunidade from produtopreco order by 1 asc";
                $rs = ibase_query($conn , $sqlUnidade)  or die(ibase_errmsg());

                echo "<option value ='--' selected>&nbsp;&nbsp;</option>";
                while ($linha = ibase_fetch_assoc($rs)) {
                    if ((string)$linha["CDUNIDADE"] == (string)$FiltraUnidade) {
                        $marcaUnidade = " selected";
                    } else {
                        $marcaUnidade = "";
                    }
                    echo "<option value =".$linha["CDUNIDADE"].$marcaUnidade.">".mb_convert_encoding($linha["CDUNIDADE"], 'UTF-8', 'WINDOWS-1252')."</option>";
                }
                ?>
            </select></td></tr>
            <tr><td>
            <b>Fornecedor:</b> </td><td><select name="comboFornecedor" maxlength="50" id="comboFornecedor">
                <?php
                $sqlFornecedor= "select f.cdFornecedor, f.Fornecedor from Fornecedor f where exists (select 1 from produto p where p.cdFornecedor = f.cdFornecedor) order by 2 asc";
                $rs = ibase_query($conn , $sqlFornecedor)  or die(ibase_errmsg());

                echo "<option value ='--' selected>&nbsp;&nbsp;</option>";
                while ($linha = ibase_fetch_assoc($rs)) {
                    if ((string)$linha["CDFORNECEDOR"] == (string)$FiltraFornecedor) {
                        $marcaFornecedor = " selected";
                    } else {
                        $marcaFornecedor = "";
                    }
                    echo "<option value =".$linha["CDFORNECEDOR"].$marcaFornecedor.">".mb_convert_encoding($linha["FORNECEDOR"], 'UTF-8', 'WINDOWS-1252')."</option>";
                }
                ?>
            </select></td></tr>
            <tr><td><b>Produto:</b> </td><td><input type="text" name="prodtxt" id="prodtxt" cols="50" value="<?php echo $FiltraProd;?>">
            </td></tr></table></td>
            <td>
                &nbsp;&nbsp;&nbsp;<button type="button" onclick="document.forms['form_busca'].submit(); return false;"><b>Filtrar</b></button>

            </td><td>
        <table><tr><td>
                    <fieldset>
                        <legend>Tipo de Cálculo</legend>
                        Individual - Preço Bruto <input type="radio" name="tpcalc" value="IB" checked><br>
                        Individual - Desconto <input type="radio" name="tpcalc" value="ID"><br>
                        Grupo - Cálculo Automático <input type="radio" name="tpcalc" value="GC"> <br>
                        Grupo - Desconto <input type="radio" name="tpcalc" value="GD">
                    </fieldset></td></tr>
        </table></td></tr>
</form>
    </table>
    </div>
</body>
