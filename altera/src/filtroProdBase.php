<?php
/**
 * Autor: Marluce Almeida
 * Data de criacao: 08/06/15
 * Hora de criacao: 10:00
 */
?>
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
<table xmlns="http://www.w3.org/1999/html">
        <tr><form name="form_busca" method="post" id="form_busca" action="altMiautomec_ProdPBase.php" target="tabIframe2">
                <td><table><tr><td><p style="color:white"><b>Grupo:</b></p></td><td><select name="comboGrupo" maxlength="50" id="comboGrupo">
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
                        <tr><td><p style="color:white"><b>Unidade:</b></p> </td><td><select name="comboUnidade" maxlength="20" id="comboUnidade">
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
                                <p style="color:white"><b>Fornecedor:</b></p> </td><td><select name="comboFornecedor" maxlength="50" id="comboFornecedor">
                                    <?php
                                    $sqlFornecedor= "select f.cdFornecedor, f.Fornecedor, coalesce(f.fantasia,f.fornecedor) fantasia from Fornecedor f where exists (select 1 from produto p where p.cdFornecedor = f.cdFornecedor) order by 2 asc";
                                    $rs = ibase_query($conn , $sqlFornecedor)  or die(ibase_errmsg());

                                    echo "<option value ='--' selected>&nbsp;&nbsp;</option>";
                                    while ($linha = ibase_fetch_assoc($rs)) {
                                        if ((string)$linha["CDFORNECEDOR"] == (string)$FiltraFornecedor) {
                                            $marcaFornecedor = " selected";
                                        } else {
                                            $marcaFornecedor = "";
                                        }
                                        echo "<option value =".$linha["CDFORNECEDOR"].$marcaFornecedor.">".mb_convert_encoding($linha["FANTASIA"], 'UTF-8', 'WINDOWS-1252')."</option>";
                                    }
                                    ?>
                                </select></td></tr>
                        <tr><td><p style="color:white"><b>Produto:</b></p> </td><td><input type="text" autofocus="true" name="prodtxt" id="prodtxt" cols="50" value="<?php echo $FiltraProd;?>">
                            </td></tr></table></td>
            <td>&nbsp;</td><td><p style="color:white"><fieldset>
            <legend style="color:white">Ordenar por:</legend>
            <p style="color:white"><input type="radio" name="ordem" value="G" checked>Grupo<br>
                <input type="radio" name="ordem" value="A" >Alfabética<br></p>
        </fieldset></p></td><td>
                    &nbsp;&nbsp;&nbsp;<button type="button" onclick="document.forms['form_busca'].submit(); return false;"><b>Filtrar</b></button>

                </td></tr>
        </form>
    </table>
