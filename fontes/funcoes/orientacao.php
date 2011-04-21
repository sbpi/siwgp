<?php
include_once('../constants.inc');
include_once('../funcoes.php');
$p_tipo = upper($_REQUEST['p_tipo']);
header("Cache-Control: no-cache, must-revalidate",false);

ShowHTML('<script src="'.$conRootSIW.'js/jquery.js"></script>');
ShowHTML('<script src="'.$conRootSIW.'js/funcoes.js"></script>');
?>
<html>
<head>
<style>
.vertical{
    text-decoration: none;
}
.vertical:hover{
    text-decoration: underline;
}
</style>

<style>

</style>
</head>
<body>
    <form name="frm">
       <div id="conteudo"> 
       <div style="float:right"><img src="images/close.gif" style="cursor:pointer" onclick="closeMessage();"></div>
      <table align="center">
        <tr>
          <td>  
            <label class="vertical" for="rvert" id="rvert" onclick="gerar('PORTRAIT');" onmouseover="document.getElementById('vert').style.display = 'block';document.getElementById('hori').style.display = 'none';" value="retrato" >Retrato</label>
            <p>
            <label class="vertical" for="rhori" id="rhori" onclick="gerar('LANDSCAPE');" onmouseover="document.getElementById('hori').style.display = 'block';document.getElementById('vert').style.display = 'none';" value="paisagem" >Paisagem</label>
          </td>  
          <td>
            <div style="padding-left:60px">
              <img id="vert" src="images/vertical.gif" alt="Vertical" />
    
              <img style="display:none" id="hori" src="images/horizontal.gif" alt="Horizontal"/>
            <div>
          </td>
        </tr>              
      </table>
      </div>
      <div id="carregando" style="display:none">
         <center>
             <img src="images/load.gif" alt="Carregando">
             <br />
             <b> Carregando... </b>
         </center>
      </div> 
  </form>  
</body>
</html>
<script>  
    function gerar(orientacao){
        
        document.getElementById('conteudo').style.display     = 'none';
        document.getElementById('carregando').style.display   = '';
                       
       
        if (document.temp.opcao.value=='W') {
          window.location.href =  $("#word").val()+"&orientacao=" + orientacao;
        } else {
          window.open( $("#pdf").val()+"&orientacao=" + orientacao ,'pdf','resizable=yes,width=700,height=500');
        }
        closeMessage();
    }        
</script>
