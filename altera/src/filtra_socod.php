<?php
/**
 * Autor: Marluce Almeida
 * Data de criacao: 08/06/15
 * Hora de criacao: 10:00
 */
?>
<?php
require_once("conexao_miautomec.php");
if (isset($_POST['prodcod'])) {
    $FiltraProd = $_POST['prodcod'];
} else {
    $FiltraProd = "";
}
?>
    <table>
        <tr><form name="form_busca" method="post" id="form_busca" action="altMiautomec_Ubruto.php" target="tabIframe2">
                <td><table><tr><td><p style="color:white"><b>Produto:</b></p> </td><td><input type="text" name="prodcod" id="prodcod" cols="50" value="<?php echo $FiltraProd;?>">
                            </td></tr></table></td>
                <td>
                    &nbsp;&nbsp;&nbsp;<button type="button" onclick="document.forms['form_busca'].submit(); return false;"><b>Filtrar</b></button>

                </td></tr>
        </form>
    </table>
