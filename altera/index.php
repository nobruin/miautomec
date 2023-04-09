<?php 
require_once("src/conexao_miautomec.php");
session_start();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<script type="text/javascript" src="jquery/tabbed.js" xmlns="http://www.w3.org/1999/html"></script>
<head>
    <script type="text/javascript">
        function synchTab(frameName) {
            var elList, i;
            if (frameName == null)
                return;
            elList = document.getElementsByTagName("A");
            for (i = 0; i < elList.length; i++)
                if (elList[i].target == frameName) {
                    if (elList[i].href == window.frames[frameName].location.href) {
                        elList[i].className += " activeTab";
                        elList[i].blur();
                    }
                    else
                        removeName(elList[i], "activeTab");
                }
        }
        function removeName(el, name) {
            var i, curList, newList;
            if (el.className == null)
                return;
            newList = new Array();
            curList = el.className.split(" ");
            for (i = 0; i < curList.length; i++)
                if (curList[i] != name)
                    newList.push(curList[i]);
            el.className = newList.join(" ");
        }
    </script>
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
    <title>Sistema de Atualização de Preços - Amorim Ferragens</title>
    <style type="text/css">
        div.tabBox {}
        div.tabArea {
            font-family: Arial, Verdana;
            font-size: 98%;
            font-weight: bold;
            padding: 0px 0px 3px 0px;
        }
        a.tab {
            background-color: #ADD8E6; /* cor dos tabs */
            border: 2px solid #000000;
            border-bottom-width: 0px;
            border-color: #B0E0E6 #87CEEB #87CEEB #B0E0E6; /* linha em cima dos tabs, linha direita, linha embaixo, linha esquerda */
            -moz-border-radius: .75em .75em 0em 0em;
            border-radius-topleft: .75em;
            border-radius-topright: .75em;
            padding: 2px 1em 2px 1em;
            position: relative;
            text-decoration: none;
            top: 3px;
            z-index: 100;
        }
        a.tab, a.tab:visited {
            color: #0000FF; /*#1E90FF cor da fonte dos tabs */
        }
        a.tab:hover {
            background-color: #6495ED; /* cor do tab com mouse fundo */
            border-color: #87CEFA #1E90FF #1E90FF #87CEFA; /* cor tab com mouse linha em cima, linha direita, embaixo,  linha esquerda */
            color: #AFEEEE; /* cor tab com mouse fonte */
        }
        a.tab.activeTab, a.tab.activeTab:hover, a.tab.activeTab:visited {
            background-color: #4682B4; /* tab selecionado fundo */
            border-color: #87CEEB #0000FF #0000FF #87CEEB; /* linha do tab selecionado em cima, direita, embaixo, esquerda */
            color: #FFFFFF; /*#AFEEEE; tab selecionado cor da fonte */

        }
        a.tab.activeTab {
            padding-bottom: 4px;
            top: 1px;
            z-index: 102;
        }
        div.tabMain {
            background-color: #4682B4; /* cor do folder ativo background */
            border: 2px solid #000000;
            border-color: #87CEEB #0000FF #0000FF #87CEEB; /* cor do folder ativo linha de cima, direita, embaixo, esquerda*/
            -moz-border-radius: 0em .5em .5em 0em;
            border-radius-topright: .5em;
            border-radius-bottomright: .5em;
            padding: .5em;
            position: relative;
            z-index: 101;
        }
        div.tabIframeWrapper {
            width: 100%;
        }
        iframe.tabContent {
            background-color: #B0C4DE; /* cor do folder ativo conteúdo */
            border: 1px solid #000000;
            border-color: #0000FF #87CEEB #87CEEB #0000FF; /* cor do folder ativo linha de cima, direita, embaixo, esquerda*/       
            width: 100%;
            height: 90ex;
            style: background-color: #B0C4DE;
        }
        h4#title {
            background-color: #000080;
            border: 1px solid #000000;
            border-color: #0000FF #87CEEB #87CEEB #0000FF;
            color: #FFFFFF; /*cor da fonte do título ADD8E6*/
            font-weight: bold;
            margin-top: 0em;
            margin-bottom: .5em;
            padding: 2px .5em 2px .5em;
        }
    </style>
</head>
<body>
<?php
/**
 * Autor: Marluce Almeida
 */
?>
<img src="logo_amorim.JPG"><br><br>
<div class="tabBox" style="clear:both;">
    <div class="tabArea">
        <a class="tab" href="src/altMiautomec_Ubruto.php" target="tabIframe2"  onclick='changeLink("src/filtra_socod.php","none")'>Unitário - Bruto</a>
        <a class="tab" href="src/altMiautomec_ProdPBase.php" target="tabIframe2" onclick='changeLink("src/filtroProdBase.php","block")'>Edita Preço Base</a>
        <a class="tab" href="src/altMiautomec_LCompleta.php" target="tabIframe2" onclick='changeLink("src/filtroCompleto.php","block")'>Lista Completa</a>
        <a class="tab" href="src/altMiautomec_Gautomatico.php" target="tabIframe2" onclick='changeLink("src/filtroCompletoAUT.php","block")'>Grupo - Automático</a>
    </div>
    <div class="tabMain">
        <div class="tabIframeWrapper">
        <div class="filtro" id='divfiltro' name='divfiltro' style="display: none;"><h4 id="title"><iframe id="tabIframe3" name="tabIframe3" src="src/filtra_socod.php" frameborder="0" width="100%" height="120"></iframe></h4></div>
        <div class="tabIframeWrapper"><iframe class="tabContent" id="tabIframe2" name="tabIframe2" onload="synchTab(this.name)"; src="src/altMiautomec_Ubruto.php" marginheight="8" marginwidth="8" frameborder="0"></iframe></div>
        </div>
    </div>
</div>


</body>
</html>

<script type="text/javascript">
    function changeLink(link,mostra) {
        parent.tabIframe3.location=link;
        document.getElementById('divfiltro').style.display = mostra;
    }
</script>