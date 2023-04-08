<?php
/**
 * Autor: Marluce Almeida
 * Data de criacao: 26/05/18
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
?>
    <table>
        <tr><form name="form_busca" method="post" id="form_busca" action="altMiautomec_Gautomatico.php" target="tabIframe2">
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
                <td>
                    &nbsp;&nbsp;&nbsp;<button type="button" onclick="document.forms['form_busca'].submit(); return false;"><b>Filtrar</b></button>
                </td>
                </tr>
        </form>
    </table>
