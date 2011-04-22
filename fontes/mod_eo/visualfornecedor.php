<?php 
// =========================================================================
// Rotina de visualização dos dados do fornecedor
// -------------------------------------------------------------------------
function visualfornecedor($l_sq_pessoa,$O) {
  extract($GLOBALS);
  $sql = new db_getBenef; $l_RS = $sql->getInstanceOf($dbms,$w_cliente,$l_sq_pessoa,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null);
  foreach($l_RS as $row){$l_RS=$row; break;}
  // Se for listagem dos dados
  $w_html.=chr(13).'<table border="0" cellpadding="0" cellspacing="0" width="100%">';
  $w_html.=chr(13).'<tr><td align="center">';
  $w_html.=chr(13).'    <table width="99%" border="0">';
  $w_html.=chr(13).'      <tr><td align="center" colspan="2"><font size=3><b>'.f($l_RS,'nome_resumido').'</font></b></td></tr>';
  $w_html.=chr(13).'      <tr><td colspan="2"><hr NOSHADE color=#000000 size=4></td></tr>';    
  
  // Identificação civil e localização
  $w_html.=chr(13).'      <tr><td colspan="2"><br><font size="2"><b>IDENTIFICAÇÃO<hr NOSHADE color=#000000 SIZE=1></b></font></td></tr>';  
  $w_html.=chr(13).'      <tr><td valign="top" width="20%"><b>Nome:</b></td>';
  $w_html.=chr(13).'          <td>'.f($l_RS,'nm_pessoa').'</td></tr>';
  if(f($l_RS,'sq_tipo_pessoa')=='2') {
    $w_html.=chr(13).'      <tr><td valign="top"><b>CNPJ:</b></td>';
    $w_html.=chr(13).'          <td>'.Nvl(f($l_RS,'cnpj'),'---').'</td></tr>';
    $w_html.=chr(13).'      <tr><td valign="top"><b>Inscrição Estadual:</b></td>';
    $w_html.=chr(13).'          <td>'.nvl(f($l_RS,'inscricao_estadual'),'---').'</td></tr>';
  } else {
    $w_html.=chr(13).'      <tr><td valign="top"><b>CPF:</b></td>';
    $w_html.=chr(13).'          <td>'.Nvl(f($l_RS,'cpf'),'---').'</td></tr>';
    $w_html.=chr(13).'      <tr><td valign="top"><b>Sexo:</b></td>';
    $w_html.=chr(13).'          <td>'.f($l_RS,'nm_sexo').'</td></tr>';
    $w_html.=chr(13).'      <tr><td valign="top"><b>Data de nascimento:</b></td>';
    $w_html.=chr(13).'          <td>'.Nvl(FormataDataEdicao(f($l_RS,'nascimento')),'---').'</td></tr>';
    $w_html.=chr(13).'      <tr><td valign="top"><b>Identidade:</b></td>';
    $w_html.=chr(13).'          <td>'.Nvl(f($l_RS,'rg_numero'),'---').'</td></tr>';
    $w_html.=chr(13).'      <tr><td valign="top"><b>Data de emissão:</b></td>';
    $w_html.=chr(13).'          <td>'.Nvl(FormataDataEdicao(f($l_RS,'rg_emissao')),'---').'</td></tr>';
    $w_html.=chr(13).'      <tr><td valign="top"><b>Órgão emissor:</b></td>';
    $w_html.=chr(13).'          <td>'.Nvl(f($l_RS,'rg_emissor'),'---').'</td></tr>';
    $w_html.=chr(13).'      <tr><td valign="top"><b>Passaporte:</b></td>';
    $w_html.=chr(13).'          <td>'.Nvl(f($l_RS,'passaporte_numero'),'---').'</td></tr>';
    $w_html.=chr(13).'      <tr><td valign="top"><b>País emissor do passaporte:</b></td>';
    $w_html.=chr(13).'          <td>'.Nvl(f($l_RS,'nm_pais_passaporte'),'---').'</td></tr>';
  }

  //Endereços de e-mail e internet
  $sql = new db_getAddressList; $l_RS = $sql->getInstanceOf($dbms,$l_sq_pessoa,null,'EMAILINTERNET',null);
  $l_RS = SortArray($l_RS,'tipo_endereco','asc','padrao','desc','endereco','asc');
  if(count($l_RS)>0) {
    $w_html.=chr(13).'      <tr><td colspan="2"><br><font size="2"><b>ENDEREÇOS DE E-MAIL E INTERNET<hr NOSHADE color=#000000 SIZE=1></b></font></td></tr>';
    $w_html.=chr(13).'      <tr><td align="center" colspan="2">';
    $w_html.=chr(13).'        <TABLE WIDTH="100%" border="1" bordercolor="#00000">';
    $w_html.=chr(13).'          <tr bgcolor="'.$conTrBgColor.'" align="center">';
    $w_html.=chr(13).'            <td><b>Endereço</td>';
    $w_html.=chr(13).'            <td><b>Padrão</td>';
    $w_html.=chr(13).'          </tr>';
    foreach ($l_RS as $row) {
      $w_html.=chr(13).'      <tr bgcolor="'.$conTrBgColor.'" valign="top">';
      if (f($row,'email')=='S') {
        $w_html.=chr(13).'        <td><a href="mailto:'.f($row,'logradouro').'">'.f($row,'logradouro').'</a></td>';
      } else {
        $w_html.=chr(13).'        <td><a href="'.f($row,'logradouro').'" target="_blank">'.f($row,'logradouro').'</a></td>';
      } 
      $w_html.=chr(13).'        <td align="center">'.str_replace('N','Não',str_replace('S','Sim',f($row,'padrao'))).'</td>';
      $w_html.=chr(13).'      </tr>';
    }
    $w_html.=chr(13).'         </table></td></tr>';
  } 

  //Endereços físicos
  $sql = new db_getAddressList; $l_RS = $sql->getInstanceOf($dbms,$l_sq_pessoa,null,'FISICO',null);
  $l_RS = SortArray($l_RS,'padrao','desc','logradouro','asc');
  if (count($l_RS)>0) {
    $w_html.=chr(13).'      <tr><td colspan="2"><br><font size="2"><b>ENDEREÇOS FÍSICOS<hr NOSHADE color=#000000 SIZE=1></b></font></td></tr>';
    foreach ($l_RS as $row) {
      $w_html.=chr(13).'      <tr><td align="center" colspan="2">';
      $w_html.=chr(13).'        <TABLE WIDTH="100%" border="1" bordercolor="#00000">';
      $w_html.=chr(13).'          <tr bgcolor="'.$conTrBgColor.'"><td colspan=2><b>'.f($row,'tipo_endereco').((f($row,'padrao')=='S') ? ' (Padrão)': '').'</td>';
      $w_html.=chr(13).'          <tr><td width="5%" rowspan=4><td valign="top"><b>'.f($row,'endereco').'</td></tr>';
      $w_html.=chr(13).'          <tr><td valign="top"><table border=0 width="100%" cellspacing=0>';
      $w_html.=chr(13).'              <tr valign="top">';
      $w_html.=chr(13).'              <td valign="top">Complemento:<br><b>'.Nvl(f($row,'complemento'),'---').' </b></td>';
      $w_html.=chr(13).'              <td valign="top">Bairro:<br><b>'.Nvl(f($row,'bairro'),'---').' </b></td>';
      $w_html.=chr(13).'              <td valign="top">CEP:<br><b>'.Nvl(f($row,'cep'),'---').' </b></td>';
      $w_html.=chr(13).'              </table>';
      $w_html.=chr(13).'          </table></td></tr>';
    } 
  } 
  
  //Telefones
  $sql = new db_getFoneList; $l_RS = $sql->getInstanceOf($dbms,$l_sq_pessoa,null,null,null);
  $l_RS = SortArray($l_RS,'tipo_telefone','asc','cidade','asc','padrao','desc','numero','asc');
  if (count($l_RS)>0) {
    $w_html.=chr(13).'      <tr><td colspan="2"><br><font size="2"><b>TELEFONES<hr NOSHADE color=#000000 SIZE=1></b></font></td></tr>';
    $w_html.=chr(13).'      <tr><td align="center" colspan="2">';
    $w_html.=chr(13).'        <TABLE WIDTH="100%" border="1" bordercolor="#00000">';
    $w_html.=chr(13).'          <tr bgcolor="'.$conTrBgColor.'" align="center">';
    $w_html.=chr(13).'            <td><b>Tipo</td>';
    $w_html.=chr(13).'            <td><b>DDD</td>';
    $w_html.=chr(13).'            <td><b>Número</td>';
    $w_html.=chr(13).'            <td><b>Cidade</td>';
    $w_html.=chr(13).'            <td><b>UF</td>';
    $w_html.=chr(13).'            <td><b>País</td>';
    $w_html.=chr(13).'            <td><b>Padrão</td>';
    $w_html.=chr(13).'          </tr>';
    foreach ($l_RS as $row) {
      $w_html.=chr(13).'      <tr bgcolor="'.$conTrBgColor.'" valign="top">';
      $w_html.=chr(13).'        <td>'.f($row,'tipo_telefone').'</td>';
      $w_html.=chr(13).'        <td align="center">'.f($row,'ddd').'</td>';
      $w_html.=chr(13).'        <td>'.f($row,'numero').'</td>';
      $w_html.=chr(13).'        <td>'.Nvl(f($row,'cidade'),'---').'</td>';
      $w_html.=chr(13).'        <td align="center">'.Nvl(f($row,'co_uf'),'---').'</td>';
      $w_html.=chr(13).'        <td>'.Nvl(f($row,'pais'),'---').'</td>';
      $w_html.=chr(13).'        <td align="center">'.str_replace('N','Não',str_replace('S','Sim',f($row,'padrao'))).'</td>';
      $w_html.=chr(13).'      </tr>';
    } 
    $w_html.=chr(13).'         </table></td></tr>';
  } 
  //Contas bancárias
  $sql = new db_getContaBancoList; $l_RS = $sql->getInstanceOf($dbms,$l_sq_pessoa,null,null);
  $l_RS = SortArray($l_RS,'tipo_conta','asc','padrao','desc','banco','asc','numero','asc');
  if (count($l_RS)>0) {
    $w_html.=chr(13).'      <tr><td colspan="2"><br><font size="2"><b>CONTAS BANCÁRIAS<hr NOSHADE color=#000000 SIZE=1></b></font></td></tr>';
    $w_html.=chr(13).'      <tr><td align="center" colspan="2">';
    $w_html.=chr(13).'        <TABLE WIDTH="100%" border="1" bordercolor="#00000">';
    $w_html.=chr(13).'          <tr bgcolor="'.$conTrBgColor.'" align="center">';
    $w_html.=chr(13).'            <td><b>Tipo</td>';
    $w_html.=chr(13).'            <td><b>Banco</td>';
    $w_html.=chr(13).'            <td><b>Agência</td>';
    $w_html.=chr(13).'            <td><b>Operação</td>';
    $w_html.=chr(13).'            <td><b>Número</td>';
    $w_html.=chr(13).'            <td><b>Ativo</td>';
    $w_html.=chr(13).'            <td><b>Padrão</td>';
    $w_html.=chr(13).'          </tr>';
    foreach ($l_RS as $row) {
      $w_html.=chr(13).'      <tr bgcolor="'.$conTrBgColor.'" valign="top">';
      $w_html.=chr(13).'        <td>'.f($row,'tipo_conta').'</td>';
      $w_html.=chr(13).'        <td>'.f($row,'banco').'</td>';
      $w_html.=chr(13).'        <td>'.f($row,'agencia').'</td>';
      $w_html.=chr(13).'        <td align="center">'.Nvl(f($row,'operacao'),'---').'</td>';
      $w_html.=chr(13).'        <td>'.f($row,'numero').'</td>';
      $w_html.=chr(13).'        <td align="center">'.str_replace('N','Não',str_replace('S','Sim',f($row,'ativo'))).'</td>';
      $w_html.=chr(13).'        <td align="center">'.str_replace('N','Não',str_replace('S','Sim',f($row,'padrao'))).'</td>';
      $w_html.=chr(13).'      </tr>';
   } 
   $w_html.=chr(13).'         </table></td></tr>';
  } 
  $w_html.=chr(13).'</table>';
  return $w_html;
}
?>
