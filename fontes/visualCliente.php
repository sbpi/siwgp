<?php
// =========================================================================
// Rotina de visualização dos dados do cliente
// -------------------------------------------------------------------------
function visualCliente($w_sq_cliente,$O) {
  extract($GLOBALS);

  $sql = new db_getCustomerData; $RS = $sql->getInstanceOf($dbms,$w_sq_cliente);

  if ($O=='L') {
    // Se for listagem dos dados
    ShowHTML('<div align=center><center>');
    ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td align="center">');
    ShowHTML('    <table width="99%" border="0">');
    ShowHTML('      <tr><td align="center" colspan="2"><font size=3><b>'.f($RS,'nome_resumido').' ('.f($RS,'cnpj').')</font></b></td></tr>');

    // Identificação civil e localização
    ShowHTML('      <tr><td valign="top" colspan="2" align="center" bgcolor="#D0D0D0" style="border: 2px solid rgb(0,0,0);"><b>Identificação Civil e Localização</td>');
    ShowHTML('      <tr><td valign="top">Razão Social:<br><b>'.f($RS,'nome').' </b></td>');
    ShowHTML('      <tr><td valign="top" colspan="2"><table border=0 width="100%" cellspacing=0>');
    ShowHTML('          <td valign="top">Código interno:<br><b>'.f($RS,'sq_pessoa').' </b></td>');
    ShowHTML('          <td valign="top">Segmento:<br><b>'.f($RS,'segmento').' </b></td>');
    ShowHTML('          <tr>');
    ShowHTML('          <td valign="top">Inscrição estadual:<br><b>'.Nvl(f($RS,'inscricao_estadual'),'Não informada').' </b></td>');
    ShowHTML('          <td valign="top">Início das atividades:<br><b>'.FormataDataEdicao(f($RS,'inicio_atividade')).' </b></td>');
    ShowHTML('          <td valign="top">Sede (Matriz)?<br><b>'.str_replace('N','Não',str_replace('S','Sim',f($RS,'sede'))).' </b></td>');
    ShowHTML('          </table>');

    // Cidade e agência padrão
    ShowHTML('      <tr><td valign="top" colspan="2" align="center" bgcolor="#D0D0D0" style="border: 2px solid rgb(0,0,0);"><b>Cidade e Agência Padrão</td>');
    ShowHTML('      <tr><td valign="top" colspan="2"><table border=0 width="100%" cellspacing=0>');
    ShowHTML('          <td valign="top">Cidade:<br><b>'.f($RS,'cidade').' </b></td>');
    ShowHTML('          <td valign="top">Estado:<br><b>'.f($RS,'co_uf').' </b></td>');
    ShowHTML('          <td valign="top">País:<br><b>'.f($RS,'pais').' </b></td>');
    ShowHTML('          </table>');
    ShowHTML('      <tr><td valign="top" colspan="2"><table border=0 width="100%" cellspacing=0>');
    ShowHTML('          <td valign="top">Banco:<br><b>'.f($RS,'banco').' </b></td>');
    ShowHTML('          <td valign="top">Agência:<br><b>'.f($RS,'codigo').' - '.f($RS,'agencia').' </b></td>');
    ShowHTML('          </table>');

    // Parâmetros de segurança
    ShowHTML('      <tr><td valign="top" colspan="2" align="center" bgcolor="#D0D0D0" style="border: 2px solid rgb(0,0,0);"><b>Parâmetros de Segurança</td>');
    ShowHTML('      <tr><td valign="top" colspan="2"><table border=0 width="100%" cellspacing=0>');
    ShowHTML('          <td valign="top">Tamanho mínimo:<br><b>'.f($RS,'TAMANHO_MIN_SENHA').' </b></td>');
    ShowHTML('          <td valign="top">Tamanho máximo:<br><b>'.f($RS,'TAMANHO_MAX_SENHA').' </b></td>');
    ShowHTML('          <td valign="top">Máximo de tentativas:<br><b>'.f($RS,'maximo_tentativas').' </b></td>');
    ShowHTML('          <tr>');
    ShowHTML('          <td valign="top">Limite da vigência:<br><b>'.f($RS,'DIAS_VIG_SENHA').' </b></td>');
    ShowHTML('          <td valign="top">Dias para aviso:<br><b>'.f($RS,'DIAS_AVISO_EXPIR').' </b></td>');
    ShowHTML('          </table>');

    //Endereços de e-mail e internet
    $SQL = new db_getAddressList; $RS = $SQL->getInstanceOf($dbms,$w_sq_cliente,null,'EMAILINTERNET',null);
    $RS = SortArray($RS,'tipo_endereco','asc','padrao','desc','endereco','asc');
    ShowHTML('      <tr><td valign="top" colspan="2" align="center" bgcolor="#D0D0D0" style="border: 2px solid rgb(0,0,0);"><b>Endereços e-Mail e Internet ('.count($RS).')</td>');
    ShowHTML('      <tr><td align="center" colspan="2">');
    ShowHTML('        <TABLE class="tudo" WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
    ShowHTML('          <tr bgcolor="'.$conTrBgColor.'" align="center">');
    ShowHTML('            <td><b>Endereço</td>');
    ShowHTML('            <td><b>Padrão</td>');
    ShowHTML('          </tr>');
    if (count($RS)==0) {
      ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td colspan=2 align="center"><b>Não informado.</b></td></tr>');
    } else {
      foreach ($RS as $row) {
        ShowHTML('      <tr bgcolor="'.$conTrBgColor.'" valign="top">');
        if (f($row,'email')=='S') {
          ShowHTML('        <td><a href="mailto:'.f($row,'logradouro').'">'.f($row,'logradouro').'</a></td>');
        } else {
          ShowHTML('        <td><a href="'.f($row,'logradouro').'" target="_blank">'.f($row,'logradouro').'</a></td>');
        } 
        ShowHTML('        <td align="center">'.str_replace('N','Não',str_replace('S','Sim',f($row,'padrao'))).'</td>');
        ShowHTML('      </tr>');
      }
    } 
    ShowHTML('         </table></td></tr>');
  
    //Endereços físicos
    $SQL = new db_getAddressList; $RS = $SQL->getInstanceOf($dbms,$w_sq_cliente,null,'FISICO',null);
    $RS = SortArray($RS,'padrao','desc','logradouro','asc');
    ShowHTML('      <tr><td valign="top" colspan="2" align="center" bgcolor="#D0D0D0" style="border: 2px solid rgb(0,0,0);"><b>Endereços Físicos ('.count($RS).')</td>');
    if (count($RS)==0) {
      ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><b>Não informado.</b></td></tr>');
    } else {
      foreach ($RS as $row) {
        ShowHTML('      <tr><td align="center" colspan="2"><TABLE WIDTH="100%" BORDER="0" CELLSPACING="0" CELLPADDING="0">');
        ShowHTML('          <tr><td colspan=2><b>'.f($row,'endereco').'</td>');
        ShowHTML('          <tr><td width="5%" rowspan=4><td valign="top">Logradouro:<br><b>'.f($row,'logradouro').'</td></tr>');
        ShowHTML('          <tr><td valign="top"><table border=0 width="100%" cellspacing=0>');
        ShowHTML('              <tr valign="top">');
        ShowHTML('              <td valign="top">Complemento:<br><b>'.Nvl(f($row,'complemento'),'---').' </b></td>');
        ShowHTML('              <td valign="top">Bairro:<br><b>'.Nvl(f($row,'bairro'),'---').' </b></td>');
        ShowHTML('              <td valign="top">CEP:<br><b>'.Nvl(f($row,'cep'),'---').' </b></td>');
        ShowHTML('              <tr valign="top">');
        ShowHTML('              <td valign="top">País:<br><b>'.f($row,'nm_pais').' </b></td>');
        ShowHTML('              <td>Padrão?<br><b>'.str_replace('N','Não',str_replace('S','Sim',f($row,'padrao'))).'</td>');
        ShowHTML('              </table>');
        ShowHTML('          </table></td></tr>');
      } 
    } 

    //Telefones
    $SQL = new db_getFoneList; $RS = $SQL->getInstanceOf($dbms,$w_sq_cliente,null,null,null);
    $RS = SortArray($RS,'tipo_telefone','asc','cidade','asc','padrao','desc','numero','asc');
    ShowHTML('      <tr><td valign="top" colspan="2" align="center" bgcolor="#D0D0D0" style="border: 2px solid rgb(0,0,0);"><b>Telefones ('.count($RS).')</td>');
    ShowHTML('      <tr><td align="center" colspan="2">');
    ShowHTML('        <TABLE class="tudo" WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
    ShowHTML('          <tr bgcolor="'.$conTrBgColor.'" align="center">');
    ShowHTML('            <td><b>Tipo</td>');
    ShowHTML('            <td><b>DDD</td>');
    ShowHTML('            <td><b>Número</td>');
    ShowHTML('            <td><b>Cidade</td>');
    ShowHTML('            <td><b>UF</td>');
    ShowHTML('            <td><b>País</td>');
    ShowHTML('            <td><b>Padrão</td>');
    ShowHTML('          </tr>');
    if (count($RS)==0) {
      ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td colspan=7 align="center"><b>Não informado.</b></td></tr>');
    } else {
      foreach ($RS as $row) {
        ShowHTML('      <tr bgcolor="'.$conTrBgColor.'" valign="top">');
        ShowHTML('        <td>'.f($row,'tipo_telefone').'</td>');
        ShowHTML('        <td align="center">'.f($row,'ddd').'</td>');
        ShowHTML('        <td>'.f($row,'numero').'</td>');
        ShowHTML('        <td>'.Nvl(f($row,'cidade'),'---').'</td>');
        ShowHTML('        <td align="center">'.Nvl(f($row,'co_uf'),'---').'</td>');
        ShowHTML('        <td>'.Nvl(f($row,'pais'),'---').'</td>');
        ShowHTML('        <td align="center">'.str_replace('N','Não',str_replace('S','Sim',f($row,'padrao'))).'</td>');
        ShowHTML('      </tr>');
      } 
    } 
    ShowHTML('         </table></td></tr>');

    //Contas bancárias
    $SQL = new db_getContaBancoList; $RS = $SQL->getInstanceOf($dbms,$w_sq_cliente,null,null);
    $RS = SortArray($RS,'tipo_conta','asc','padrao','desc','banco','asc','numero','asc');
    ShowHTML('      <tr><td valign="top" colspan="2" align="center" bgcolor="#D0D0D0" style="border: 2px solid rgb(0,0,0);"><b>Contas Bancárias ('.count($RS).')</td>');
    ShowHTML('      <tr><td align="center" colspan="2">');
    ShowHTML('        <TABLE class="tudo" WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
    ShowHTML('          <tr bgcolor="'.$conTrBgColor.'" align="center">');
    ShowHTML('            <td><b>Tipo</td>');
    ShowHTML('            <td><b>Banco</td>');
    ShowHTML('            <td><b>Agência</td>');
    ShowHTML('            <td><b>Operação</td>');
    ShowHTML('            <td><b>Número</td>');
    ShowHTML('            <td><b>Ativo</td>');
    ShowHTML('            <td><b>Padrão</td>');
    ShowHTML('          </tr>');
    if (count($RS)==0) {
      ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td colspan=7 align="center"><b>Não informado.</b></td></tr>');
    } else {
      foreach ($RS as $row) {
        ShowHTML('      <tr bgcolor="'.$conTrBgColor.'" valign="top">');
        ShowHTML('        <td>'.f($row,'tipo_conta').'</td>');
        ShowHTML('        <td>'.f($row,'banco').'</td>');
        ShowHTML('        <td>'.f($row,'agencia').'</td>');
        ShowHTML('        <td align="center">'.Nvl(f($row,'operacao'),'---').'</td>');
        ShowHTML('        <td>'.f($row,'numero').'</td>');
        ShowHTML('        <td align="center">'.str_replace('N','Não',str_replace('S','Sim',f($row,'ativo'))).'</td>');
        ShowHTML('        <td align="center">'.str_replace('N','Não',str_replace('S','Sim',f($row,'padrao'))).'</td>');
        ShowHTML('      </tr>');
      } 
    } 
    ShowHTML('         </table></td></tr>');

    //Módulos contratados
    $sql = new db_getSiwCliModLis; $RS = $sql->getInstanceOf($dbms,$w_sq_cliente,null,null);
    ShowHTML('      <tr><td valign="top" colspan="2" align="center" bgcolor="#D0D0D0" style="border: 2px solid rgb(0,0,0);"><b>Módulos Contratados ('.count($RS).')</td>');
    ShowHTML('      <tr><td align="center" colspan="2">');
    ShowHTML('        <TABLE class="tudo" WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
    ShowHTML('          <tr bgcolor="'.$conTrBgColor.'" align="center">');
    ShowHTML('             <td><b>Módulo</td>');
    ShowHTML('             <td><b>Sigla</td>');
    ShowHTML('             <td><b>Objetivo geral</td>');
    ShowHTML('          </tr>');
    if (count($RS)==0) {
      ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td colspan=7 align="center"><b>Não informado.</b></td></tr>');
    } else {
      foreach ($RS as $row) {
        ShowHTML('      <tr bgcolor="'.$conTrBgColor.'" valign="top">');
        ShowHTML('        <td>'.f($row,'nome').'</td>');
        ShowHTML('        <td align="center">'.f($row,'sigla').'</td>');
        ShowHTML('        <td>'.f($row,'objetivo_geral').'</td>');
        ShowHTML('      </tr>');
      } 
    } 
    ShowHTML('         </table></td></tr>');

    //Usuários cadastrados
    $SQL = new db_getUserList; $RS = $SQL->getInstanceOf($dbms,$w_sq_cliente,null,null,null,null,null,null,null,null,null,'S',null,null,null,null,null);
    $RS = SortArray($RS,'nome_resumido_ind','asc');
    ShowHTML('      <tr><td valign="top" colspan="2" align="center" bgcolor="#D0D0D0" style="border: 2px solid rgb(0,0,0);"><b>Usuários Cadastrados ('.count($RS).')</td>');
    ShowHTML('      <tr><td align="center" colspan="2">');
    ShowHTML('        <TABLE class="tudo" WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
    ShowHTML('          <tr bgcolor="'.$conTrBgColor.'" align="center">');
    ShowHTML('            <td><b>Username</td>');
    ShowHTML('            <td><b>Nome</td>');
    ShowHTML('            <td><b>Lotação</td>');
    ShowHTML('            <td><b>Ramal</td>');
    ShowHTML('            <td><b>Vínculo</td>');
    ShowHTML('            <td><b>Ativo</td>');
    ShowHTML('          </tr>');
    if (count($RS)==0) {
      ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td colspan=7 align="center"><b>Não informado.</b></td></tr>');
    } else {
      foreach ($RS as $row) {
        ShowHTML('      <tr bgcolor="'.$conTrBgColor.'" valign="top">');
        ShowHTML('        <td align="center" nowrap>'.f($row,'username').'</td>');
        ShowHTML('        <td title="'.f($row,'nome').'">'.f($row,'nome_resumido').'</td>');
        ShowHTML('        <td>'.f($row,'lotacao').'&nbsp;('.f($row,'localizacao').')</td>');
        ShowHTML('        <td align="center">&nbsp;'.Nvl(f($row,'ramal'),'---').'</td>');
        ShowHTML('        <td>&nbsp;'.Nvl(f($row,'vinculo'),'---').'</td>');
        ShowHTML('        <td align="center">&nbsp;'.str_replace('N','Não',str_replace('S','Sim',f($row,'ativo'))).'</td>');
        ShowHTML('      </tr>');
      } 
    } 
    ShowHTML('         </table></td></tr>');

    //Configuração da aplicação
    $sql = new db_getCustomerData; $RS = $sql->getInstanceOf($dbms,$w_sq_cliente);
    ShowHTML('      <tr><td valign="top" colspan="2" align="center" bgcolor="#D0D0D0" style="border: 2px solid rgb(0,0,0);"><b>Configuração da Aplicação</td>');
    ShowHTML('      <tr><td align="center" colspan="2">');
    ShowHTML('        <TABLE WIDTH="100%">');
    ShowHTML('          <tr bgcolor="'.$conTrBgColor.'" valign="top">');
    ShowHTML('             <td>Servidor SMTP:<br><b>'.f($RS,'smtp_server').'</b></td>');
    ShowHTML('             <td>Nome do remetente:<br><b>'.f($RS,'siw_email_nome').'</b></td>');
    ShowHTML('             <td>Conta do remetente:<br><b>'.f($RS,'siw_email_conta').'</b></td>');
    ShowHTML('          </tr>');
    ShowHTML('         </table></td></tr>');
    ShowHTML('      <tr><td align="center" colspan="2">');
    ShowHTML('        <TABLE WIDTH="100%">');
    ShowHTML('          <tr bgcolor="'.$conTrBgColor.'" valign="top">');
    if (f($RS,'logo')>'') {
      ShowHTML('             <td colspan=3>Logomarca telas e relatórios:<br><b><img src="'.LinkArquivo(null,$w_sq_cliente,'img/logo'.substr(f($RS,'logo'),(strpos(f($RS,'logo'),'.') ? strpos(f($RS,'logo'),'.')+1 : 0)-1,30),null,null,null,'EMBED').'" border=1></b></td>');
    } else {
      ShowHTML('             <td colspan=3>Não informado</td>');
    } 
    if (f($RS,'logo')>'') {
      ShowHTML('             <td colspan=3>Logomarca menu:<br><b><img src="'.LinkArquivo(null,$w_sq_cliente,'img/logo1'.substr(f($RS,'logo1'),(strpos(f($RS,'logo1'),'.') ? strpos(f($RS,'logo1'),'.')+1 : 0)-1,30),null,null,null,'EMBED').'" border=1></b></td>');
    } else {
      ShowHTML('             <td colspan=3>Não informado</td>');
    } 
    ShowHTML('          </tr>');
    ShowHTML('         </table></td></tr>');

    //Funcionalidades
    $w_imagemPadrao='images/Folder/SheetLittle.gif';
    ShowHTML('      <tr><td valign="top" colspan="2" align="center" bgcolor="#D0D0D0" style="border: 2px solid rgb(0,0,0);"><b>Funcionalidades</td>');
    $sql = new db_getLinkDataUser; $RS = $sql->getInstanceOf($dbms,$w_sq_cliente,0,'IS NULL');
    ShowHTML('      <tr><td align="center" colspan="2">');
    ShowHTML('        <TABLE class="tudo" WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
    ShowHTML('          <tr bgcolor="'.$conTrBgColor.'" align="center">');
    ShowHTML('            <td><b>Opção</td>');
    ShowHTML('            <td><b>Link</td>');
    ShowHTML('            <td><b>Sigla</td>');
    ShowHTML('            <td><b>P1</td>');
    ShowHTML('            <td><b>P2</td>');
    ShowHTML('            <td><b>P3</td>');
    ShowHTML('            <td><b>P4</td>');
    ShowHTML('            <td><b>Target</td>');
    ShowHTML('            <td><b>Sub-menu</td>');
    ShowHTML('            <td><b>Ativo</td>');
    ShowHTML('          </tr>');
    if (count($RS)==0) {
      ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td colspan=10 align="center"><b>Não informado.</b></td></tr>');
    } else {
      foreach ($RS as $row) {
        if (f($row,'Filho')>0) {
          ShowHTML('      <tr bgcolor="'.$conTrBgColor.'" valign="top">');
          ShowHTML('        <td colspan=10><img src="images/Folder/FolderClose.gif" border=0 align="center"> <b>'.f($row,'nome'));
          $sql = new db_getLinkDataUser; $RS1 = $sql->getInstanceOf($dbms,$w_sq_cliente,0,f($row,'sq_menu'));
          foreach ($RS1 as $row1) {
            if (f($row1,'Filho')>0) {
              ShowHTML('      <tr bgcolor="'.$conTrBgColor.'" valign="top">');
              ShowHTML('        <td colspan=10 nowrap>&nbsp;&nbsp;&nbsp;&nbsp;<img src="images/Folder/FolderClose.gif" border=0 align="center"> '.f($row1,'nome'));
              $sql = new db_getLinkDataUser; $RS2 = $sql->getInstanceOf($dbms,$w_sq_cliente,0,f($row1,'sq_menu'));
              foreach ($RS2 as $row2) {
                if (f($row2,'Filho')>0) {
                  ShowHTML('      <tr bgcolor="'.$conTrBgColor.'" valign="top">');
                  ShowHTML('        <td colspan=10 nowrap>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src="images/Folder/FolderClose.gif" border=0 align="center"> '.f($row2,'nome'));
                  $sql = new db_getLinkDataUser; $RS3 = $sql->getInstanceOf($dbms,$w_sq_cliente,0,f($row2,'sq_menu'));
                  foreach ($RS3 as $row3) {
                    ShowHTML('      <tr bgcolor="'.$conTrBgColor.'" valign="top">');
                    ShowHTML('        <td nowrap>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src="'.$w_imagem.'" border=0 align="center"> '.f($row3,'nome'));
                    ShowHTML('        <td title="'.f($row3,'link').'"> '.Nvl(substr(f($row3,'link'),0,30),'-'));
                    ShowHTML('        <td> '.Nvl(f($row3,'sigla'),'-'));
                    ShowHTML('        <td align="center"> '.Nvl(f($row3,'p1'),'-'));
                    ShowHTML('        <td align="center"> '.Nvl(f($row3,'p2'),'-'));
                    ShowHTML('        <td align="center"> '.Nvl(f($row3,'p3'),'-'));
                    ShowHTML('        <td align="center"> '.Nvl(f($row3,'p4'),'-'));
                    ShowHTML('        <td align="center"> '.Nvl(f($row3,'target'),'-'));
                    ShowHTML('        <td align="center"> '.str_replace('N','Não',str_replace('S','Sim',f($row3,'ultimo_nivel'))));
                    ShowHTML('        <td align="center"> '.str_replace('N','Não',str_replace('S','Sim',f($row3,'ativo'))));
                  } 
                } else {
                  if (f($row2,'IMAGEM')>'') {
                    $w_imagem=f($row2,'IMAGEM');
                  } else {
                    $w_imagem=$w_imagemPadrao;
                  } 
                  ShowHTML('      <tr bgcolor="'.$conTrBgColor.'" valign="top">');
                  ShowHTML('        <td nowrap>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src="'.$w_imagem.'" border=0 align="center"> '.f($row2,'nome'));
                  ShowHTML('        <td title="'.f($row2,'link').'"> '.Nvl(substr(f($row2,'link'),0,30),'-'));
                  ShowHTML('        <td> '.Nvl(f($row2,'sigla'),'-'));
                  ShowHTML('        <td align="center"> '.Nvl(f($row2,'p1'),'-'));
                  ShowHTML('        <td align="center"> '.Nvl(f($row2,'p2'),'-'));
                  ShowHTML('        <td align="center"> '.Nvl(f($row2,'p3'),'-'));
                  ShowHTML('        <td align="center"> '.Nvl(f($row2,'p4'),'-'));
                  ShowHTML('        <td align="center"> '.Nvl(f($row2,'target'),'-'));
                  ShowHTML('        <td align="center"> '.str_replace('N','Não',str_replace('S','Sim',f($row2,'ultimo_nivel'))));
                  ShowHTML('        <td align="center"> '.str_replace('N','Não',str_replace('S','Sim',f($row2,'ativo'))));
                } 
              } 
              ShowHTML('   </div>');
            } else {
              if (f($row1,'IMAGEM')>'') {
                $w_imagem=f($row1,'IMAGEM');
              } else {
                $w_imagem=$w_imagemPadrao;
              } 
              ShowHTML('      <tr bgcolor="'.$conTrBgColor.'" valign="top">');
              ShowHTML('        <td nowrap>&nbsp;&nbsp;&nbsp;&nbsp;<img src="'.$w_imagem.'" border=0 align="center"> '.f($row1,'nome'));
              ShowHTML('        <td title="'.f($row1,'link').'"> '.Nvl(substr(f($row1,'link'),0,30),'-'));
              ShowHTML('        <td> '.Nvl(f($row1,'sigla'),'-'));
              ShowHTML('        <td align="center"> '.Nvl(f($row1,'p1'),'-'));
              ShowHTML('        <td align="center"> '.Nvl(f($row1,'p2'),'-'));
              ShowHTML('        <td align="center"> '.Nvl(f($row1,'p3'),'-'));
              ShowHTML('        <td align="center"> '.Nvl(f($row1,'p4'),'-'));
              ShowHTML('        <td align="center"> '.Nvl(f($row1,'target'),'-'));
              ShowHTML('        <td align="center"> '.str_replace('N','Não',str_replace('S','Sim',f($row1,'ultimo_nivel'))));
              ShowHTML('        <td align="center"> '.str_replace('N','Não',str_replace('S','Sim',f($row1,'ativo'))));
            }  
          } 
          ShowHTML('   </div>');
        } else {
          if (f($row,'IMAGEM')>'') {
            $w_imagem=f($row,'IMAGEM');
          } else {
            $w_imagem=$w_imagemPadrao;
          } 
          ShowHTML('      <tr bgcolor="'.$conTrBgColor.'" valign="top">');
          ShowHTML('        <td nowrap><img src="'.$w_imagem.'" border=0 align="center"><b> '.f($row,'nome'));
          ShowHTML('        <td title="'.f($row,'link').'"> '.Nvl(substr(f($row,'link'),0,30),'-'));
          ShowHTML('        <td> '.Nvl(f($row,'sigla'),'-'));
          ShowHTML('        <td align="center"> '.Nvl(f($row,'p1'),'-'));
          ShowHTML('        <td align="center"> '.Nvl(f($row,'p2'),'-'));
          ShowHTML('        <td align="center"> '.Nvl(f($row,'p3'),'-'));
          ShowHTML('        <td align="center"> '.Nvl(f($row,'p4'),'-'));
          ShowHTML('        <td align="center"> '.Nvl(f($row,'target'),'-'));
          ShowHTML('        <td align="center"> '.str_replace('N','Não',str_replace('S','Sim',f($row,'ultimo_nivel'))));
          ShowHTML('        <td align="center"> '.str_replace('N','Não',str_replace('S','Sim',f($row,'ativo'))));
        } 
      } 
    } 
    ShowHTML('         </table></td></tr>');
    ShowHTML('     </tr></tr></td></table>');
    ShowHTML('</table>');
  } else {
    ScriptOpen('JavaScript');
    ShowHTML(' alert(\'Opção não disponível\');');
    ShowHTML(' history.back(1);');
    ScriptClose();
  } 
}
?>
