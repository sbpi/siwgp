<HTML>
<BODY>
Dudu, veja nos INPUT TEXT abaixo o uso do ACESSKEY, do TABINDEX, DISABLE e READONLY<BR>O evento on-click n�o esta funcionando. 
Vamos fazer isso depois.<BR>
Vamos testar o sistema tamb�m com o sistema do DISABLE, onde se mostra uma imagem CINZA!<BR>
<P>
<FONT SIZE=1>Alt-C </FONT><img src=/ztitools/img/arrowr.gif TITLE="Campo obrigat�rio"><BR>
<INPUT TYPE=TEXT NAME="X" VALUE="AAA" ID=Valor ACCESSKEY=C TABINDEX=2 READONLY TITLE="Este � o campo com o valor que ser� passado ao Sistema"><BR>
ALT-A<BR>
<INPUT TYPE=TEXT NAME="X" VALUE="AAA" ID=ValorA ACCESSKEY=A TABINDEX=1 TITLE="Somente teste para o ALT-A"><BR>
ALT-B<BR>
<INPUT TYPE=TEXT NAME="X" VALUE="AAA" ID=ValorB ACCESSKEY=B TABINDEX=3 DISABLED TITLE="Somente teste para o ALT-B"><BR>

<img src=/ztitools/img/tx.gif onclick="alert(document.all.Valor.value);" alt=salvar>
<img src=/ztitools/img/tx.gif onclick="alert(onclickset);" alt=salvar>
 
 
<script> 

onclickset = false;

function ZTIClick(valor) {

  document.all.Valor.value = valor;

  eval("document.all.cmd" + valor + ".src = '/ztitools/img/EditForm/cmd" + valor + "Win-down.gif'");
  
  onclickset = true;
  
  return true
}

function ZTIMouseUp(valor) {
  if (onclickset) {
   eval("document.all.cmd" + valor + ".src = '/ztitools/img/EditForm/cmd" + valor + "Win.gif'");
   
   return true
  }
}
</script> 

<%
Sub ShowImage(ByVal strImagem, ByVal strAlt)

  Response.Write "<BR><input type=image src='/ztitools/img/EditForm/cmd" & strImagem & "Win.gif' onclick=""ZTIClick('" & lcase(strImagem) & "');"" onmouseout=""ZTIMouseUp('" & lcase(strImagem) & "');"" alt='" & strAlt & "' ID=cmd" & lcase(strImagem) & ">"
  
End Sub

ShowImage "Apagar", "Apaga o registro corrente"
ShowImage "Atualizar", "Atualiza a tela"
ShowImage "BOF", "Vai para o in�cio da consulta corrente"
ShowImage "EOF", "Vai para o final da consulta corrente"
ShowImage "Next", "Vai para o pr�ximo registro"
ShowImage "Last", "Vai para o registro anterior"
ShowImage "Localizar", "Ativa o di�logo de localiza��o"
ShowImage "Cancelar", "Cancela a op��o corrente"
ShowImage "Copiar", "Copia o registro corrente"
ShowImage "Sim", "Escolhe Sim"
ShowImage "Nao", "Escolhe N�o"
ShowImage "OK", "Tudo OK!"
ShowImage "Question", "Ativa o sistema de ajuda"
ShowImage "SalvarCopiar", "Salva e inicia uma insers�o, copiando o conteudo do registro corrente"
ShowImage "SalvarIncluir", "Salva e inclui novo registro"


%>





