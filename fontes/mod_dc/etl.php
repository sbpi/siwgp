<?php
header('Expires: '.-1500);
session_start();
$w_dir_volta = '../';
$crlf        = $crlf;
include_once($w_dir_volta.'constants.inc');
include_once($w_dir_volta.'jscript.php');
include_once($w_dir_volta.'funcoes.php');
include_once($w_dir_volta.'classes/db/abreSessao.php');
include_once($w_dir_volta.'classes/sp/db_getMenuCode.php');
include_once($w_dir_volta.'classes/sp/db_getSiwCliModLis.php');
include_once($w_dir_volta.'classes/sp/db_getLinkSubMenu.php');
include_once($w_dir_volta.'classes/sp/db_getMenuData.php');
include_once($w_dir_volta.'classes/sp/db_getEsquema.php');
include_once($w_dir_volta.'classes/sp/db_verificaAssinatura.php');
include_once($w_dir_volta.'classes/sp/db_getEsquemaTabela.php');
include_once($w_dir_volta.'classes/sp/db_getTabela.php');
include_once($w_dir_volta.'classes/sp/db_getColuna.php');
include_once($w_dir_volta.'classes/sp/db_getEsquemaAtributo.php');
include_once($w_dir_volta.'classes/sp/db_getCustomerData.php');
include_once($w_dir_volta.'classes/sp/db_getEsquemaInsert.php');
include_once($w_dir_volta.'classes/sp/db_getEsquemaScript.php');
include_once($w_dir_volta.'classes/sp/db_getLinkData.php');
include_once($w_dir_volta.'classes/sp/dml_putEsquema.php');
include_once($w_dir_volta.'classes/sp/dml_putEsquemaTabela.php');
include_once($w_dir_volta.'classes/sp/dml_putEsquemaAtributo.php');
include_once($w_dir_volta.'classes/sp/dml_putEsquemaInsert.php');
include_once($w_dir_volta.'classes/sp/dml_putEsquemaScript.php');
include_once($w_dir_volta.'funcoes/selecaoFormato.php');
include_once($w_dir_volta.'funcoes/selecaoModulo.php');
include_once($w_dir_volta.'funcoes/selecaoSistema.php');
include_once($w_dir_volta.'funcoes/selecaoUsuario.php');
include_once($w_dir_volta.'funcoes/selecaoTipoTabela.php');
// =========================================================================
// /etl.php
// ------------------------------------------------------------------------
// Nome     : Celso Miguel Lago Filho
// Descricao: Rotinas de importação de dados
// Mail     : celso@sbpi.com.br
// Criacao  : 19/09/2006, 15:30
// Versao   : 1.0.0.0
// Local    : Brasília - DF
// -------------------------------------------------------------------------
// 
// Parâmetros recebidos:
//    R (referência) = usado na rotina de gravação, com conteúdo igual ao parâmetro T
//    O (operação)   = I   : Inclusão
//                   = A   : Alteração
//                   = C   : Cancelamento
//                   = E   : Exclusão
//                   = L   : Listagem
//                   = P   : Pesquisa
//                   = D   : Detalhes
//                   = N   : Nova solicitação de envio
// Verifica se o usuário está autenticado
if ($_SESSION['LOGON']!='Sim') { EncerraSessao(); }
// Declaração de variáveis
$dbms = new abreSessao; $dbms = $dbms->getInstanceOf($_SESSION['DBMS']);
// Carrega variáveis locais com os dados dos parâmetros recebidos
$par        = upper($_REQUEST['par']);
$P1         = nvl($_REQUEST['P1'],0);
$P2         = nvl($_REQUEST['P2'],0);
$P3         = nvl($_REQUEST['P3'],1);
$P4         = nvl($_REQUEST['P4'],$conPageSize);
$TP         = $_REQUEST['TP'];
$SG         = upper($_REQUEST['SG']);
$R          = $_REQUEST['R'];
$O          = upper($_REQUEST['O']);
$w_assinatura   = upper($_REQUEST['w_assinatura']);
$w_pagina       = 'etl.php?par=';
$w_Disabled     = 'ENABLED';
$w_dir          = 'mod_dc/';
$w_troca        = $_REQUEST['w_troca'];
$p_ordena       = lower($_REQUEST['p_ordena']);
// Se receber o código do cliente do SIW, o cliente será determinado por parâmetro;
// caso contrário, o cliente será a empresa ao qual o usuário logado está vinculado.
$w_cliente  = RetornaCliente();
$w_usuario  = RetornaUsuario();
$w_menu     = RetornaMenu($w_cliente,$SG);
$w_ano      = RetornaAno();
$p_nome         = upper($_REQUEST['p_nome']);
$p_tipo         = upper($_REQUEST['p_tipo']);
$p_formato      = upper($_REQUEST['p_formato']);
$p_sq_modulo    = upper($_REQUEST['p_sq_modulo']);
$p_dt_ini       = $_REQUEST['p_dt_ini'];
$p_dt_fim       = $_REQUEST['p_dt_fim'];
$p_ref_ini      = $_REQUEST['p_ref_ini'];
$p_ref_fim      = $_REQUEST['p_ref_fim'];
switch ($O) {
  case 'I': $w_TP=$TP.' - Inclusão';        break;
  case 'A': $w_TP=$TP.' - Alteração';       break;
  case 'E': $w_TP=$TP.' - Exclusão';        break;
  case 'P': $w_TP=$TP.' - Filtragem';       break;
  case 'C': $w_TP=$TP.' - Cópia';           break;
  case 'V': $w_TP=$TP.' - Envio';           break;
  case 'H': $w_TP=$TP.' - Herança';         break;
  case 'O': $w_TP=$TP.' - Geração';         break;
  case 'L': $w_TP=$TP.' - Orientações';     break;
  default:  $w_TP=$TP.' - Listagem';        break;
} 
if($O=='') $O='L';
if ($P1==1) $p_tipo='I'; else $p_tipo='E';
// Verifica se o documento tem sub-menu. Se tiver, agrega no HREF uma chamada para montagem do mesmo.
$sql = new db_getLinkSubMenu; $RS = $sql->getInstanceOf($dbms,$_SESSION['P_CLIENTE'],$SG);
if (count($RS)>0) {
  $w_submenu = 'Existe';
} else {
  $w_submenu = '';
}
// Recupera a configuração do serviço
if ($P2>0) {
  $sql = new db_getMenuData; $RS_Menu = $sql->getInstanceOf($dbms,$P2);
} else {
  $sql = new db_getMenuData; $RS_Menu = $sql->getInstanceOf($dbms,$w_menu);
}
// Se for sub-menu, pega a configuração do pai
if (f($RS_Menu,'ultimo_nivel')=='S') { 
  $sql = new db_getMenuData; $RS_Menu = $sql->getInstanceOf($dbms,f($RS_Menu,'sq_menu_pai'));
} 
Main();
FechaSessao($dbms);
exit;
// =========================================================================
// Rotina de importação de arquivos físicos
// -------------------------------------------------------------------------
function Inicial() {
  extract($GLOBALS);
  $w_sq_esquema = $_REQUEST['w_sq_esquema'];
  if ($w_troca>'') {
    // Se for recarga da página
    $w_sq_esquema       = $_REQUEST['w_sq_esquema'];
    $w_sq_modulo        = $_REQUEST['w_sq_modulo'];
    $w_nome             = $_REQUEST['w_nome'];
    $w_descricao        = $_REQUEST['w_descricao'];
    $w_tipo             = $_REQUEST['w_tipo'];
    $w_ativo            = $_REQUEST['w_ativo'];
    $w_formato          = $_REQUEST['w_formato'];
    $w_ws_servidor      = $_REQUEST['w_ws_servidor'];
    $w_ws_url           = $_REQUEST['w_ws_url'];
    $w_ws_acao          = $_REQUEST['w_ws_acao'];
    $w_ws_mensagem      = $_REQUEST['w_ws_mensagem'];
    $w_no_raiz          = $_REQUEST['w_no_raiz'];
    $w_bd_hostname      = $_REQUEST['w_bd_hostname'];
    $w_bd_username      = $_REQUEST['w_bd_username'];
    $w_bd_password      = $_REQUEST['w_bd_password'];
    $w_tx_delimitador   = $_REQUEST['w_tx_delimitador'];
    $w_ftp_hostname     = $_REQUEST['w_ftp_hostname'];
    $w_ftp_username     = $_REQUEST['w_ftp_username'];
    $w_ftp_password     = $_REQUEST['w_ftp_password'];
    $w_ftp_diretorio    = $_REQUEST['w_ftp_diretorio'];
    $w_tipo_efetivacao  = $_REQUEST['w_tipo_efetivacao'];
    $w_tx_origem_arquivos = $_REQUEST['w_tx_origem_arquivos'];
    $w_envia_mail       = $_REQUEST['w_envia_mail'];
    $w_lista_mail       = $_REQUEST['w_lista_mail'];
    
  } elseif ($O=='L') {
    // Recupera todos os ws_url para a listagem
    $sql = new db_getEsquema; $RS = $sql->getInstanceOf($dbms,$w_cliente,null,null,$w_sq_modulo,$p_nome,$p_tipo,$p_formato,$p_dt_ini,$p_dt_fim,$p_ref_ini,$p_ref_fim);
    if (Nvl($p_ordena,'')>'') {
      $lista = explode(',',str_replace(' ',',',$p_ordena));
      $RS = SortArray($RS,$lista[0],$lista[1]);
    } else {    
      $RS = SortArray($RS,'nome','asc');
    }
  } elseif (!(strpos('AE',$O)===false)) {
    // Recupera todos os ws_url para a listagem
    $sql = new db_getEsquema; $RS = $sql->getInstanceOf($dbms,$w_cliente,null,$w_sq_esquema,null,null,null,null,null,null,null,null);
    foreach($RS as $row){$RS=$row; break;}
    $w_sq_esquema   = f($RS,'sq_esquema');
    $w_sq_modulo    = f($RS,'sq_modulo');
    $w_nome         = f($RS,'nome');
    $w_descricao    = f($RS,'descricao');
    $w_tipo         = f($RS,'tipo');
    $w_ativo        = f($RS,'ativo');
    $w_formato      = f($RS,'formato');
    $w_ws_servidor  = f($RS,'ws_servidor');
    $w_ws_url       = f($RS,'ws_url');
    $w_ws_acao      = f($RS,'ws_acao');
    $w_ws_mensagem  = f($RS,'ws_mensagem');
    $w_no_raiz      = f($RS,'no_raiz');
    $w_bd_hostname  = f($RS,'bd_hostname');
    $w_bd_username  = f($RS,'bd_username');
    $w_bd_password  = f($RS,'bd_password');
    $w_tx_delimitador     = f($RS,'tx_delimitador');
    $w_tipo_efetivacao    = f($RS,'tipo_efetivacao');
    $w_tx_origem_arquivos = f($RS,'tx_origem_arquivos');
    $w_ftp_hostname  = f($RS,'ftp_hostname');
    $w_ftp_username  = f($RS,'ftp_username');
    $w_ftp_password  = f($RS,'ftp_password');
    $w_ftp_diretorio = f($RS,'ftp_diretorio');
    $w_envia_mail = f($RS,'envia_mail');
    $w_lista_mail = f($RS,'lista_mail');

  } 
  Cabecalho();
  head();
  if (!(strpos('IAEP',$O)===false)) {
    ScriptOpen('JavaScript');
    CheckBranco();
    FormataData();
    SaltaCampo();
    ValidateOpen('Validacao');
    if (!(strpos('IAE',$O)===false)) {
      if (!(strpos('IA',$O)===false)) {
        Validate('w_sq_modulo','Módulo','SELECT',1,1,10,'1','1');
        Validate('w_nome','Nome','1','1',3,60,'1','1');
        Validate('w_descricao','Descricao','1','',3,500,'1','1');
        Validate('w_tipo_efetivacao','Tipo de efetivação','SELECT',1,1,10,'1','1');
        if ($P1==1) Validate('w_formato','Formato','SELECT',1,1,10,'1','1');
        if ($w_formato=='W' || $w_formato=='A') {
          Validate('w_no_raiz','Nó raiz','1','1',3,50,'1','1');
          if ($w_formato=='W') {
            Validate('w_ws_servidor','Servidor','1','1',3,100,'1','1');
            Validate('w_ws_url','URL','1','1',3,100,'1','1');
            Validate('w_ws_acao','Ação','1','1',3,100,'1','1');
            Validate('w_ws_mensagem','Mensagem','1','1',3,4000,'1','1');
          }
        } else {
          Validate('w_bd_hostname','Hostname','1','',2,50,'1','1');
          Validate('w_bd_username','Username','1','1',2,50,'1','1');
          Validate('w_bd_password','Password','1','1',2,50,'1','1');
          Validate('w_tx_delimitador','Delimitador','1','1',1,5,'1','1');
          Validate('w_tx_origem_arquivos','Origem dos arquivos','SELECT',1,1,10,'1','1');
          if ($w_tx_origem_arquivos=='1') {
            Validate('w_ftp_hostname','Hostname','1','1',3,50,'1','1');
            Validate('w_ftp_username','Username','1','1',3,50,'1','1');
            Validate('w_ftp_password','Password','1','',3,50,'1','1');
            Validate('w_ftp_diretorio','Diretório','1','',1,100,'1','1');        
          }
        }
        Validate('w_envia_mail','Enviar e-mail','SELECT',1,1,10,'1','1');
        if ($w_envia_mail>0) Validate('w_lista_mail','Lista de e-mails','1','1',1,255,'1','1');      
      }
      Validate('w_assinatura','Assinatura Eletrônica','1','1','6','30','1','1');
    } 
    ShowHTML('  theForm.Botao[0].disabled=true;');
    ShowHTML('  theForm.Botao[1].disabled=true;');
    ValidateClose();
    ScriptClose();
  } 
  ShowHTML('</HEAD>');
  ShowHTML('<BASE HREF="'.$conRootSIW.'">');
  if ($w_troca>'') {
    BodyOpen('onLoad=\'document.Form.'.$w_troca.'.focus()\';');
  } elseif (!(strpos('IA',$O)===false)) {
    BodyOpen('onLoad=\'document.Form.w_nome.focus()\';');
  } elseif (!(strpos('E',$O)===false)) {
    BodyOpen('onLoad=\'document.Form.w_assinatura.focus()\';');
  } else {
    BodyOpen(null);
  } 
  ShowHTML('<B><FONT COLOR="#000000">'.$w_TP.'</FONT></B>');
  ShowHTML('<HR>');
  ShowHTML('<div align=center><center>');
  ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
  if ($O=='L') {
    // Exibe a quantidade de ws_url apresentados na listagem e o cabeçalho da tabela de listagem
    ShowHTML('<tr><td>');
    ShowHTML('        <a accesskey="I" class="SS" href="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=I&w_sq_esquema='.$w_sq_esquema.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'"><u>I</u>ncluir</a>&nbsp;');
    ShowHTML('        <a accesskey="G" class="SS" href="'.$w_dir.$w_pagina.'Grava&R='.$w_pagina.$par.'&O=O&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG=GERAARQ" target="geracao" title="Gera arquivo de configuração para os esquemas ativos." onClick="return(confirm(\'Confirma geração dos arquivos de configuração?\'))"><u>G</u>erar configuração</a>&nbsp;');
    ShowHTML('        <a accesskey="O" class="SS" href="'.$w_dir.$w_pagina.'Help&R='.$w_pagina.$par.'&O=L&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'" target="help"><u>O</u>rientações</a>&nbsp;');
    ShowHTML('    <td align="right"><b>Registros: '.count($RS));
    ShowHTML('<tr><td align="center" colspan=3>');
    ShowHTML('    <TABLE id="tudo" WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
    ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center">');
    ShowHTML('          <td rowspan=2><b>'.LinkOrdena('Módulo','nm_modulo').'</font></td>');
    ShowHTML('          <td rowspan=2><b>'.LinkOrdena('Nome','nome').'</font></td>');
    ShowHTML('          <td rowspan=2><b>'.LinkOrdena('Formato','nm_formato').'</font></td>');
    ShowHTML('          <td rowspan=2><b>'.LinkOrdena('Ativo','nm_ativo').'</font></td>');
    ShowHTML('          <td rowspan=2><b>'.LinkOrdena('Tabelas','qtd_tabela').'</font></td>');
    ShowHTML('          <td colspan=2><b>Data</font></td>');
    ShowHTML('          <td colspan=3><b>Registros</font></td>');
    ShowHTML('          <td rowspan=2><b>Operações</font></td>');
    ShowHTML('        </tr>');
    ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center">');
    ShowHTML('          <td title="Ocorrência"><b>'.LinkOrdena('Ocorr.','data_ocorrencia').'</font></td>');
    ShowHTML('          <td title="Referência"><b>'.LinkOrdena('Ref.','phpdt_data_referencia').'</font></td>');
    ShowHTML('          <td title="Total"><b>Tot.</font></td>');
    ShowHTML('          <td title="Aceitos"><b>Ac.</font></td>');
    ShowHTML('          <td title="Rejeitados"><b>Rej.</font></td>');
    ShowHTML('        </tr>');
    if (count($RS)<=0) {
      // Se não foram selecionados ws_url, exibe mensagem
      ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td colspan=11 align="center"><b>Não foram encontrados registros.</b></td></tr>');
    } else {
      // Lista os ws_url selecionados para listagem
      foreach($RS as $row) {
        $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;
        ShowHTML('      <tr bgcolor="'.$w_cor.'" valign="top">');
        ShowHTML('        <td>'.f($row,'nm_modulo').'</td>');
        if ($P1==1) ShowHTML('        <td>'.f($row,'nome').'</td>');
        else        ShowHTML('        <td>'.LinkArquivo('HL',$w_cliente,f($row,'nome').'.xml','_blank','Exibe os dados do arquivo importado.',f($row,'nome'),null).'&nbsp;</td>');
        ShowHTML('        <td>'.f($row,'nm_formato').'</td>');
        ShowHTML('        <td align="center">'.f($row,'nm_ativo').'</td>');
        ShowHTML('        <td align="center">'.Nvl(f($row,'qtd_tabela'),0).'</td>');
        if (Nvl(f($row,'data_ocorrencia'),'')>'')   ShowHTML('        <td align="center">'.FormataDataEdicao(f($row,'data_ocorrencia')).'</td>');
        else                                        ShowHTML('        <td align="center">---</td>');
        if (Nvl(f($row,'data_referencia'),'')>'')   ShowHTML('        <td align="center">'.substr(FormataDataEdicao(f($row,'phpdt_data_referencia'),3),0,-3).'</td>');
        else                                        ShowHTML('        <td align="center">---</td>');
        if (Nvl(f($row,'processados'),0)>0)         ShowHTML('        <td align="right">'.LinkArquivo('HL',$w_cliente,f($row,'chave_recebido'),'_blank','Exibe os dados do arquivo importado.',Nvl(f($row,'processados'),0),null).'&nbsp;</td>');
        else                                        ShowHTML('        <td align="right">'.Nvl(f($row,'processados'),0).'&nbsp;</td>');
        ShowHTML('        <td align="right">'.(Nvl(f($row,'processados'),0)-Nvl(f($row,'rejeitados'),0)).'&nbsp;</td>');
        if (Nvl(f($row,'rejeitados'),0)>0)          ShowHTML('        <td align="right">'.LinkArquivo('HL',$w_cliente,f($row,'chave_result'),'_blank','Exibe o registro da importação.',Nvl(f($row,'rejeitados'),0),null).'&nbsp;</td>');
        else                                        ShowHTML('        <td align="right">'.Nvl(f($row,'rejeitados'),0).'&nbsp;</td>');
        ShowHTML('        <td align="top" nowrap>');
        ShowHTML('          <A class="hl" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=A&w_sq_esquema='.f($row,'sq_esquema').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'" title="Altera as informações gerais do esquema">AL</A>&nbsp');
        if (Nvl(f($row,'sq_ocorrencia'),'')>'')     ShowHTML('          <A class="hl" onClick="alert(\'Este esquema possui ocorrências, para desabilita-lo, inative-o!\');"title="Exclui o esquema">EX</A>&nbsp');
        else                                        ShowHTML('          <A class="hl" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=E&w_sq_esquema='.f($row,'sq_esquema').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'" title="Exclui o esquema">EX</A>&nbsp');
        ShowHTML('          <A class="hl" HREF="javascript:this.status.value;" onClick="window.open(\''.montaURL_JS(null,$conRootSIW.$w_dir.$w_pagina.'Tabela&R='.$w_dir.$w_pagina.'Tabela&O=L&w_sq_esquema='.f($row,'sq_esquema').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.' - Tabelas&SG=ISSIGTAB&w_menu='.$w_menu.MontaFiltro('GET')).'\',\'Tabelas\',\'toolbar=no,width=780,height=530,top=30,left=10,scrollbars=yes\');" title="Relaciona as tabelas que compõem o esquema">Tabelas</A>&nbsp');
        ShowHTML('          <A class="hl" HREF="javascript:this.status.value;" onClick="window.open(\''.montaURL_JS(null,$conRootSIW.$w_dir.$w_pagina.'Script&R='.$w_dir.$w_pagina.'Script&O=L&w_sq_esquema='.f($row,'sq_esquema').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.' - Scripts&SG=TISCRIPT&w_menu='.$w_menu.MontaFiltro('GET')).'\',\'Script\',\'toolbar=no,width=780,height=530,top=30,left=10,scrollbars=yes\');" title="Faz o upload dos scripts quem devem ser executados no banco de dados na próxima carga">Scripts</A>&nbsp');
        if (Nvl(f($row,'qtd_tabela'),0)>0) {
          if ($P1==1)   ShowHTML('          <A class="hl" HREF="'.$w_dir.$w_pagina.'IMPORTACAO&R='.$w_pagina.$par.'&O=I&w_sq_esquema='.f($row,'sq_esquema').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'&w_menu='.$w_menu.MontaFiltro('GET').'" title="Importa a partir da definição do esquema" onClick="return(confirm(\'A importação usará o último arquivo de configuração gerado.\\nPressione OK apenas se o arquivo de configuração foi atualizado após a última alteração do esquema!\'));">Importar</A>&nbsp');
          else          ShowHTML('          <A class="hl" HREF="'.$w_dir.$w_pagina.'EXPORTACAO&R='.$w_pagina.$par.'&O=I&w_sq_esquema='.f($row,'sq_esquema').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'&w_menu='.$w_menu.MontaFiltro('GET').'" title="Exporta a partir da definição do esquema" onClick="return(confirm(\'Confirma geração do arquivo de exportação?\'))">Exportar</A>&nbsp');
        } 
        ShowHTML('        </td>');
        ShowHTML('      </tr>');
      } 
    } 
    ShowHTML('      </center>');
    ShowHTML('    </table>');
    ShowHTML('  </td>');
    ShowHTML('</tr>');
  } elseif (!(strpos('IAE',$O)===false)) {
    if (!(strpos('E',$O)===false)) $w_Disabled=' DISABLED ';
    AbreForm('Form',$w_dir.$w_pagina.'Grava','POST','return(Validacao(this));',null,$P1,$P2,1,$P4,$TP,$SG,$R,$O);
    ShowHTML('<INPUT type="hidden" name="w_sq_esquema" value="'.$w_sq_esquema.'">');
    ShowHTML('<INPUT type="hidden" name="w_tipo" value="'.$p_tipo.'">');
    ShowHTML('<INPUT type="hidden" name="w_menu" value="'.$w_menu.'">');
    if ($P1==2) ShowHTML('<INPUT type="hidden" name="w_formato" value="A">');
    ShowHTML('<INPUT type="hidden" name="w_troca" value="">');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td align="center">');
    ShowHTML('    <table width="97%" border="0">');
    ShowHTML('      <tr>');
    selecaoModulo('<u>M</u>ódulo:','M',null,$w_sq_modulo,$w_cliente,'w_sq_modulo',null,'title="Selecione na lista o módulo desejado."');
    ShowHTML('      <tr><td><b><u>N</u>ome:<br><INPUT ACCESSKEY="N" TYPE="TEXT" CLASS="sti" NAME="w_nome" SIZE=60 MAXLENGTH=60 VALUE="'.$w_nome.'" '.$w_Disabled.' title="Nome do esquema."></td>');
    ShowHTML('      <tr><td><b><U>D</U>escrição:<br><TEXTAREA ACCESSKEY="D" class="sti" name="w_descricao" rows=3 cols=80 '.$w_Disabled.' title="Descreva sucintamente a finalidade deste esquema.">'.$w_descricao.'</textarea></td>');
    ShowHTML('      <tr><td valign="top"><b><u>T</u>ipo de efetivação:</b><br><SELECT ACCESSKEY="T" CLASS="sts" NAME="w_tipo_efetivacao" '.$w_Disabled.'>');
    ShowHTML('          <option value="">---');
    if (Nvl($w_tipo_efetivacao,'')=='1') {
       ShowHTML('          <option value="0">Efetiva mesmo se achar algum registro errado.');
       ShowHTML('          <option value="1" SELECTED>Efetiva somente se não achar registro errado.');
    } elseif (Nvl($w_tipo_efetivacao,'')=='0') {
      ShowHTML('          <option value="0" SELECTED>Efetiva mesmo se achar algum registro errado.');
      ShowHTML('          <option value="1">Efetiva somente se não achar registro errado.');
   } else {
     ShowHTML('          <option value="0">Efetiva mesmo se achar algum registro errado.');
     ShowHTML('          <option value="1">Efetiva somente se não achar registro errado.');
   }
   ShowHTML('          </select>');    
    ShowHTML('      <tr><td><table border=0 width="100%" cellpadding=0 cellspacing=0><tr valign="top">');
    if ($P1==1) SelecaoFormato('Formato','F',null,$w_formato,null,'w_formato',null,'onChange="document.Form.action=\''.$w_dir.$w_pagina.$par.'\'; document.Form.w_troca.value=\'w_formato\'; document.Form.submit();"');
    if ($w_formato=='A' || $w_formato=='W') {
      ShowHTML('          <td><b>Nó <u>r</u>aiz:<br><INPUT ACCESSKEY="R" TYPE="TEXT" CLASS="sti" NAME="w_no_raiz" SIZE=50 MAXLENGTH=50 VALUE="'.$w_no_raiz.'" '.$w_Disabled.' title="Informe o nome do nó raiz do documento XML."></td>');
      if ($w_formato=='A') {
        ShowHTML('              </table>');
      } else {
        ShowHTML('              </table>');
        ShowHTML('      <tr><td><b><u>S</u>ervidor:<br><INPUT ACCESSKEY="S" TYPE="TEXT" CLASS="sti" NAME="w_ws_servidor" SIZE=70 MAXLENGTH=100 VALUE="'.$w_ws_servidor.'" '.$w_Disabled.' title="Informe o nome do servidor onde o Web Service está instalado."></td>');
        ShowHTML('      <tr><td><b><u>U</u>RL:<br><INPUT ACCESSKEY="U" TYPE="TEXT" CLASS="sti" NAME="w_ws_url" SIZE=70 MAXLENGTH=100 VALUE="'.$w_ws_url.'" '.$w_Disabled.' title="Informe a URL para execução do Web Service."></td>');
        ShowHTML('      <tr><td><b>A<u>ç</u>ão:<br><INPUT ACCESSKEY="C" TYPE="TEXT" CLASS="sti" NAME="w_ws_acao" SIZE=70 MAXLENGTH=100 VALUE="'.$w_ws_acao.'" '.$w_Disabled.' title="Informe a ação que deseja executar no Web Service."></td>');
        ShowHTML('      <tr><td><b><U>M</U>ensagem:<br><TEXTAREA ACCESSKEY="M" class="sti" name="w_ws_mensagem" rows=10 cols=80 '.$w_Disabled.' title="Escreva o envelope da mensagem a ser enviada ao Web Service.">'.$w_ws_mensagem.'</textarea></td>');
      }
    } elseif($w_formato=='T') {
      ShowHTML('      <tr><td><table border=0 width="100%" cellpadding=0 cellspacing=0><tr valign="top">');
      ShowHTML('          <td><b>BD <u>H</u>ostname:<br><INPUT ACCESSKEY="H" TYPE="TEXT" CLASS="sti" NAME="w_bd_hostname" SIZE=20 MAXLENGTH=50 VALUE="'.$w_bd_hostname.'" '.$w_Disabled.' title="Informe o hostname do banco de dados."></td>');
      ShowHTML('          <td><b>BD <u>U</u>sername:<br><INPUT ACCESSKEY="U" TYPE="TEXT" CLASS="sti" NAME="w_bd_username" SIZE=10 MAXLENGTH=50 VALUE="'.$w_bd_username.'" '.$w_Disabled.' title="Informe o login para acesso ao banco de dados."></td>');
      ShowHTML('          <td><b>BD <u>P</u>assword:<br><INPUT ACCESSKEY="P" TYPE="TEXT" CLASS="sti" NAME="w_bd_password" SIZE=10 MAXLENGTH=50 VALUE="'.$w_bd_password.'" '.$w_Disabled.' title="Informe a senha para acesso ao banco de dados."></td>');
      ShowHTML('          <td><b><u>D</u>elimitador:<br><INPUT ACCESSKEY="D" TYPE="TEXT" CLASS="sti" NAME="w_tx_delimitador" SIZE=5 MAXLENGTH=5 VALUE="'.$w_tx_delimitador.'" '.$w_Disabled.' title="Informe o delimitador quer será usado para separar os campos no arquivo TXT."></td>');
      ShowHTML('          </table>');
      ShowHTML('      <tr><td valign="top"><b><u>O</u>rigem dos aquivos TXT:</b><br><SELECT ACCESSKEY="0" CLASS="sts" NAME="w_tx_origem_arquivos" '.$w_Disabled.' onChange="document.Form.action=\''.$w_dir.$w_pagina.$par.'\'; document.Form.w_troca.value=\'w_tx_origem_arquivos\'; document.Form.submit();">');
      ShowHTML('          <option value="">---');
      if (Nvl($w_tx_origem_arquivos,'')=='1') {
         ShowHTML('          <option value="0">Diretório padrão');
         ShowHTML('          <option value="1" SELECTED>Servidor FTP');
      } elseif (Nvl($w_tx_origem_arquivos,'')=='0') {
        ShowHTML('          <option value="0" SELECTED>Diretório padrão');
        ShowHTML('          <option value="1">Servidor FTP');
      } else {
        ShowHTML('          <option value="0">Diretório padrão');
        ShowHTML('          <option value="1">Servidor FTP');
      }
      ShowHTML('          </select>');
      if($w_tx_origem_arquivos=='1') {
        ShowHTML('      <tr><td><table border=0 width="100%" cellpadding=0 cellspacing=0><tr valign="top">');
        ShowHTML('          <td><b>FTP <u>H</u>ostname:<br><INPUT ACCESSKEY="H" TYPE="TEXT" CLASS="sti" NAME="w_ftp_hostname" SIZE=20 MAXLENGTH=50 VALUE="'.$w_ftp_hostname.'" '.$w_Disabled.' title="Informe o hostname do servidor FTP."></td>');
        ShowHTML('          <td><b>FTP <u>U</u>sername:<br><INPUT ACCESSKEY="U" TYPE="TEXT" CLASS="sti" NAME="w_ftp_username" SIZE=10 MAXLENGTH=50 VALUE="'.$w_ftp_username.'" '.$w_Disabled.' title="Informe o login para acesso ao servidor FTP."></td>');
        ShowHTML('          <td><b>FTP <u>P</u>assword:<br><INPUT ACCESSKEY="P" TYPE="TEXT" CLASS="sti" NAME="w_ftp_password" SIZE=10 MAXLENGTH=50 VALUE="'.$w_ftp_password.'" '.$w_Disabled.' title="Informe a senha para acesso ao servidor FTP."></td>');
        ShowHTML('          <td><b>FTP <u>D</u>iretório:<br><INPUT ACCESSKEY="D" TYPE="TEXT" CLASS="sti" NAME="w_ftp_diretorio" SIZE=40 MAXLENGTH=100 VALUE="'.$w_ftp_diretorio.'" '.$w_Disabled.' title="Informe o diretório do servidor FTP."></td>');
        ShowHTML('          </table>');
      }
    }
    ShowHTML('      <tr><td valign="top"><b><u>E</u>nviar e-mail:</b><br><SELECT ACCESSKEY="E" CLASS="sts" NAME="w_envia_mail" '.$w_Disabled.' onChange="document.Form.action=\''.$w_dir.$w_pagina.$par.'\'; document.Form.w_troca.value=\'w_envia_mail\'; document.Form.submit();">');
    ShowHTML('          <option value="">---');
    if (Nvl($w_envia_mail,'')=='0') {
       ShowHTML('          <option value="0" SELECTED>Não enviar');
       ShowHTML('          <option value="1">Sempre enviar');
       ShowHTML('          <option value="2">Enviar somente em caso de insucesso');
    } elseif (Nvl($w_envia_mail,'')=='1') {
       ShowHTML('          <option value="0">Não enviar');
       ShowHTML('          <option value="1" SELECTED>Sempre enviar');
       ShowHTML('          <option value="2">Enviar somente em caso de insucesso');
    } elseif (Nvl($w_envia_mail,'')=='2') {
       ShowHTML('          <option value="0">Não enviar');
       ShowHTML('          <option value="1">Sempre enviar');
       ShowHTML('          <option value="2" SELECTED>Enviar somente em caso de insucesso');
    } else {
       ShowHTML('          <option value="0">Não enviar');
       ShowHTML('          <option value="1">Sempre enviar');
       ShowHTML('          <option value="2">Enviar somente em caso de insucesso');
    }
    ShowHTML('          </select>');  
    if($w_envia_mail>0) ShowHTML('      <tr><td><b><u>L</u>ista de e-mails:<br><INPUT ACCESSKEY="L" TYPE="TEXT" CLASS="sti" NAME="w_lista_mail" SIZE=50 MAXLENGTH=255 VALUE="'.$w_lista_mail.'" '.$w_Disabled.' title="Informe a lista de e-mails os quais o e-mail deverá ser enviado."></td>');  
    ShowHTML('      <tr>');
    MontaRadioSN('<b>Ativo?</b>',$w_ativo,'w_ativo');
    ShowHTML('      <tr><td align="LEFT"><b><U>A</U>ssinatura Eletrônica:<BR> <INPUT ACCESSKEY="A" class="sti" type="PASSWORD" name="w_assinatura" size="30" maxlength="30" value=""></td></tr>');
    ShowHTML('      <tr><td align="center" colspan=2><hr>');
    if ($O=='E')    ShowHTML('          <input class="STB" type="submit" name="Botao" value="Excluir">');
    else            ShowHTML('          <input class="STB" type="submit" name="Botao" value="Gravar">');
    ShowHTML('          <input class="stb" type="button" onClick="location.href=\''.montaURL_JS($w_dir,$w_pagina.$par.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'&O=L').'\';" name="Botao" value="Cancelar">');
    ShowHTML('          </td>');
    ShowHTML('      </tr>');
    ShowHTML('    </table>');
    ShowHTML('    </TD>');
    ShowHTML('</tr>');
    ShowHTML('</FORM>');
  } else {
    ScriptOpen('JavaScript');
    ShowHTML(' alert(\'Opção não disponível\');');
    ScriptClose();
  } 
  ShowHTML('</table>');
  ShowHTML('</center>');
  Rodape();
} 
// =========================================================================
// Rotina de inclusão de tabelas no esquema
// -------------------------------------------------------------------------
function Tabela() {
  extract($GLOBALS);
  $w_sq_esquema_tabela  = $_REQUEST['w_sq_esquema_tabela'];
  $w_sq_esquema         = $_REQUEST['w_sq_esquema'];
  $w_troca              = $_REQUEST['w_troca'];
  $p_nome               = $_REQUEST['p_nome'];
  $p_sq_sistema         = $_REQUEST['p_sq_sistema'];
  $p_sq_usuario         = $_REQUEST['p_sq_usuario'];
  $p_sq_tabela_tipo     = $_REQUEST['p_sq_tabela_tipo'];
  //Recupera os dados do esquema para a montagem do cabeçalho
  $sql = new db_getEsquema; $RS1 = $sql->getInstanceOf($dbms,$w_cliente,null,$w_sq_esquema,null,null,null,null,null,null,null,null);
  foreach($RS1 as $row){$RS1=$row; break;}
  if ($w_troca>'') {
    // Se for recarga da página
    $w_ordem            = $_REQUEST['w_ordem'];
    $w_elemento         = $_REQUEST['w_elemento'];
    $p_nome             = $_REQUEST['p_nome'];
    $p_sq_sistema       = $_REQUEST['p_sq_sistema'];
    $p_sq_usuario       = $_REQUEST['p_sq_usuario'];
    $p_sq_tabela_tipo   = $_REQUEST['p_sq_tabela_tipo'];
  } elseif ($O=='L') {
    // Recupera todos os ws_url para a listagem
    $sql = new db_getEsquemaTabela; $RS = $sql->getInstanceOf($dbms,null,$w_sq_esquema,null);
    if (Nvl($p_ordena,'')>'') {
      $lista = explode(',',str_replace(' ',',',$p_ordena));
      $RS = SortArray($RS,$lista[0],$lista[1]);
    } else {    
      $RS = SortArray($RS,'ordem','asc','nm_tabela','asc','or_coluna','asc');
    }
  } elseif (!(strpos('I',$O)===false)) {
    $sql = new db_getTabela; $RS = $sql->getInstanceOf($dbms,$w_cliente,null,$w_sq_esquema,$p_sq_sistema,$p_sq_usuario,$p_sq_tabela_tipo,$p_nome,$SG);
    $RS = SortArray($RS,'sg_sistema','asc','nm_usuario','asc','nome','asc');
  } elseif (!(strpos('A',$O)===false)) {
    // Recupera todos os ws_url para a listagem
    $sql = new db_getEsquemaTabela; $RS = $sql->getInstanceOf($dbms,null,$w_sq_esquema,$w_sq_esquema_tabela);
    foreach($RS as $row){$RS=$row; break;}
    $w_ordem            = f($RS,'ordem');
    $w_elemento         = f($RS,'elemento');
    $w_remove_registro  = f($RS,'remove_registro');
  } 
  Cabecalho();
  head();
  if (!(strpos('IAPM',$O)===false)) {
    ScriptOpen('JavaScript');
    if (!(strpos('I',$O)===false)) {
      ShowHTML('  function valor(p_indice) {');
      ShowHTML('    if (document.Form["w_sq_tabela[]"][p_indice].checked) { ');
      ShowHTML('       document.Form["w_ordem[]"][p_indice].disabled=false; ');
      ShowHTML('       document.Form["w_elemento[]"][p_indice].disabled=false; ');
      ShowHTML('       document.Form["w_remove_registro[]"][p_indice].disabled=false; ');
      ShowHTML('       document.Form["w_elemento[]"][p_indice].focus(); ');
      ShowHTML('    } else {');
      ShowHTML('       document.Form["w_ordem[]"][p_indice].disabled=true; ');
      ShowHTML('       document.Form["w_elemento[]"][p_indice].disabled=true; ');
      ShowHTML('       document.Form["w_remove_registro[]"][p_indice].disabled=true; ');
      ShowHTML('       document.Form["w_ordem[]"][p_indice].value=\'\'; ');
      ShowHTML('       document.Form["w_elemento[]"][p_indice].value=\'\'; ');
      ShowHTML('    }');
      ShowHTML('  }');
      ShowHTML('  function MarcaTodos() {');
      ShowHTML('    if (document.Form["w_elemento[]"].value==undefined) ');
      ShowHTML('       for (i=1; i < document.Form["w_sq_tabela[]"].length; i++) {');
      ShowHTML('         document.Form["w_sq_tabela[]"][i].checked=true;');
      ShowHTML('         document.Form["w_ordem[]"][i].disabled=false;');
      ShowHTML('         document.Form["w_elemento[]"][i].disabled=false;');
      ShowHTML('         document.Form["w_remove_registro[]"][i].disabled=false;');
      ShowHTML('       } ');
      ShowHTML('    else document.Form["w_sq_tabela[]"].checked=true;');
      ShowHTML('  }');
      ShowHTML('  function DesmarcaTodos() {');
      ShowHTML('    if (document.Form["w_sq_tabela[]"].value==undefined) ');
      ShowHTML('       for (i=1; i < document.Form["w_sq_tabela[]"].length; i++) {');
      ShowHTML('         document.Form["w_sq_tabela[]"][i].checked=false;');
      ShowHTML('         document.Form["w_ordem[]"][i].disabled=true;');
      ShowHTML('         document.Form["w_elemento[]"][i].disabled=true;');
      ShowHTML('         document.Form["w_remove_registro[]"][i].disabled=true;');
      ShowHTML('         document.Form["w_ordem[]"][i].value=\'\'; ');
      ShowHTML('         document.Form["w_elemento[]"][i].value=\'\'; ');
      ShowHTML('       } ');
      ShowHTML('    ');
      ShowHTML('    else document.Form["w_sq_tabela[]"].checked=false;');
      ShowHTML('  }');
    } 
    CheckBranco();
    FormataData();
    SaltaCampo();
    ValidateOpen('Validacao');
    if (!(strpos('IAP',$O)===false)) {
      if (!(strpos('P',$O)===false)) {
        ShowHTML('  if (theForm.p_nome.value==\'\' && theForm.p_sq_sistema.selectedIndex==0 && theForm.p_sq_usuario.selectedIndex==0 && theForm.p_sq_tabela_tipo.selectedIndex==0) {');
        ShowHTML('     alert(\'Você deve escolher pelo menos um critério de filtragem!\');');
        ShowHTML('     return false;');
        ShowHTML('  }');
        Validate('P4','Linhas por página','1','1','1','4','','0123456789');
      } elseif (!(strpos('I',$O)===false)) {
        ShowHTML('  var i; ');
        ShowHTML('  var w_erro=true; ');
        ShowHTML('  if (theForm["w_sq_tabela[]"].value==undefined) {');
        ShowHTML('     for (i=0; i < theForm["w_sq_tabela[]"].length; i++) {');
        ShowHTML('       if (theForm["w_sq_tabela[]"][i].checked) w_erro=false;');
        ShowHTML('     }');
        ShowHTML('  }');
        ShowHTML('  else {');
        ShowHTML('     if (theForm["w_sq_tabela[]"].checked) w_erro=false;');
        ShowHTML('  }');
        ShowHTML('  if (w_erro) {');
        ShowHTML('    alert(\'Você deve informar pelo menos uma tabela!\'); ');
        ShowHTML('    return false;');
        ShowHTML('  }');
        ShowHTML('  for (i=0; i < theForm["w_sq_tabela[]"].length; i++) {');
        ShowHTML('    if((theForm["w_sq_tabela[]"][i].checked)&&(theForm["w_elemento[]"][i].value==\'\')){');
        if(f($RS1,'formato')=='T')  ShowHTML('      alert(\'Para todas as tabelas selecionadas você deve informar o arquivo!\'); ');
        else                        ShowHTML('      alert(\'Para todas as tabelas selecionadas você deve informar o elemento da tabela!\'); ');
        ShowHTML('      return false;');
        ShowHTML('    }');
        ShowHTML('  }');
        ShowHTML('  for (i=0; i < theForm["w_sq_tabela[]"].length; i++) {');
        ShowHTML('    if((theForm["w_sq_tabela[]"][i].checked)&&(theForm["w_ordem[]"][i].value==\'\')){');
        ShowHTML('      alert(\'Para todas as tabelas selecionadas vc deve informar a ordem da tabela para a importação do esquema!\');');
        ShowHTML('      return false;');
        ShowHTML('    }');
        ShowHTML('  }');
      } elseif (!(strpos('A',$O)===false)) {
        Validate('w_elemento','Elemento','1','1',2,50,'1','1');
        Validate('w_ordem','Ordem','1','1',1,18,'','0123456789');
      } 
    } 
    ShowHTML('  theForm.Botao[0].disabled=true;');
    ShowHTML('  theForm.Botao[1].disabled=true;');
    if ($O=='P') ShowHTML('  theForm.Botao[2].disabled=true;');
    ValidateClose();
    ScriptClose();
  } 
  ShowHTML('</HEAD>');
  ShowHTML('<BASE HREF="'.$conRootSIW.'">');
  if ($w_troca>'') {
    BodyOpen('onLoad=\'document.Form.'.$w_troca.'.focus()\';');
  } elseif (!(strpos('P',$O)===false)) {
    BodyOpen('onLoad=\'document.Form.p_sq_sistema.focus()\';');
  } else {
    BodyOpen(null);
  } 
  ShowHTML('<B><FONT COLOR="#000000">'.$w_TP.'</FONT></B>');
  ShowHTML('<HR>');
  ShowHTML('<div align=center><center>');
  ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
  ShowHTML('<tr><td colspan=3 bgcolor="#FAEBD7"><table border=1 width="100%"><tr><td>');
  ShowHTML('    <TABLE WIDTH="100%" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
  ShowHTML('      <tr><td colspan="3">Esquema: <b>'.f($RS1,'nome').'</font></b></td>');
  ShowHTML('      <tr><td colspan="3">Descrição: <b>'.Nvl(f($RS1,'Descricao'),'---').'</font></b></td>');
  ShowHTML('      <tr valign="top">');
  ShowHTML('          <td>Tipo: <b>'.f($RS1,'nm_tipo').'</b></td>');
  ShowHTML('          <td>Formato: <b>'.f($RS1,'nm_formato').'</b></td>');
  ShowHTML('          <td>Ativo: <b>'.f($RS1,'nm_ativo').'</b></td>');
  if (!(strpos('AM',$O)===false)) {
    $sql = new db_getEsquemaTabela; $RS1 = $sql->getInstanceOf($dbms,null,$w_sq_esquema,$w_sq_esquema_tabela);
    foreach($RS1 as $row){$RS1=$row; break;}
    ShowHTML('      <tr><td colspan="3">Tabela: <b>'.Nvl(f($RS1,'nm_tabela'),'---').'</font></b></td>');
  } 
  ShowHTML('    </TABLE>');
  ShowHTML('</TABLE>');
  ShowHTML('</TABLE>');
  ShowHTML('<tr><td>&nbsp');
  ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
  if ($O=='L') {
    //Listagem das tabelas do esquema
    // Exibe a quantidade de ws_url apresentados na listagem e o cabeçalho da tabela de listagem
    ShowHTML('<tr><td>');
    ShowHTML('        <a accesskey="I" class="SS" href="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=P&w_sq_esquema='.$w_sq_esquema.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'&w_menu='.$w_menu.'"><u>I</u>ncluir</a>&nbsp;');
    ShowHTML('        <a accesskey="F" class="ss" href="javascript:window.close(); opener.location.reload(); opener.focus();"><u>F</u>echar</a>&nbsp;');
    ShowHTML('    <td align="right"><b>Registros: '.count($RS));
    ShowHTML('<tr><td align="center" colspan=3>');
    ShowHTML('    <TABLE id="tudo" WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
    ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center">');
    ShowHTML('          <td rowspan="1" colspan="2"><b>Tabelas</font></td>');
    ShowHTML('          <td rowspan="1" colspan="2"><b>Campos</font></td>');
    ShowHTML('          <td rowspan="2"><b>Operações</font></td>');
    ShowHTML('        </tr>');
    ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center">');
    ShowHTML('          <td><b>'.LinkOrdena('Ordem','ordem').'</font></td>');
    ShowHTML('          <td><b>'.LinkOrdena('Nome','nm_tabela').'</font></td>');
    ShowHTML('          <td><b>'.LinkOrdena('Ordem','or_coluna').'</font></td>');
    ShowHTML('          <td><b>'.LinkOrdena('Nome','Campo_externo').'</font></td>');
    ShowHTML('        </tr>');
    if (count($RS)<=0) {
      // Se não foram selecionados ws_url, exibe mensagem
      ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td colspan=5 align="center"><b>Não foram encontrados registros.</b></td></tr>');
    } else {
      // Lista os ws_url selecionados para listagem
      foreach($RS as $row) {
        $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;
        ShowHTML('      <tr bgcolor="'.$w_cor.'" valign="top">');
        if ($w_atual!=f($row,'nm_tabela')) {
          ShowHTML('        <td rowspan="'.f($row,'qtd_coluna').'" align="center">'.f($row,'ordem').'</td>');
          ShowHTML('        <td rowspan="'.f($row,'qtd_coluna').'">'.f($row,'nm_tabela').'</td>');
        } 
        ShowHTML('        <td align="center">'.Nvl(f($row,'or_coluna'),'---').'</td>');
        ShowHTML('        <td>'.Nvl(f($row,'campo_externo'),'---').'</td>');
        if ($w_atual!=f($row,'nm_tabela')) {
          ShowHTML('        <td rowspan="'.f($row,'qtd_coluna').'">');
          ShowHTML('          <A class="hl" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=A&w_sq_esquema='.f($row,'sq_esquema').'&w_sq_esquema_tabela='.f($row,'sq_esquema_tabela').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'&w_menu='.$w_menu.MontaFiltro('GET').'" title="Altera a os dados da tabela deste esquema">AL</A>&nbsp');
          ShowHTML('          <A class="hl" HREF="'.$w_dir.$w_pagina.'Grava'.'&R='.$w_pagina.$par.'&O=E&w_sq_esquema='.f($row,'sq_esquema').'&w_sq_esquema_tabela='.f($row,'sq_esquema_tabela').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'&w_menu='.$w_menu.MontaFiltro('GET').'" title="Exclui a tabela deste esquema" onClick="return confirm(\'Confirma a exclusão do registro?\');">EX</A>&nbsp');
          ShowHTML('          <A class="hl" HREF="'.$w_dir.$w_pagina.'MAPEAMENTO&R='.$w_pagina.$par.'&O=I&w_sq_esquema='.f($row,'sq_esquema').'&w_sq_esquema_tabela='.f($row,'sq_esquema_tabela').'&w_sq_tabela='.f($row,'sq_tabela').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'&w_menu='.$w_menu.MontaFiltro('GET').'" title="Relaciona os campos da tabela">Mapear</A>&nbsp');
          if (f($row,'qtd_coluna')>0) ShowHTML('          <A class="hl" HREF="javascript:this.status.value;" onClick="window.open(\''.montaURL_JS(null,$conRootSIW.$w_dir.$w_pagina.'Registro&R='.$w_dir.$w_pagina.'Registro&O=L&w_sq_esquema='.f($row,'sq_esquema').'&w_sq_esquema_tabela='.f($row,'sq_esquema_tabela').'&w_sq_tabela='.f($row,'sq_tabela').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.' - Registros&SG=TIREGISTRO&w_menu='.$w_menu.MontaFiltro('GET')).'\',\'Registros\',\'toolbar=no,width=780,height=530,top=40,left=20,scrollbars=yes\');" title="Registros a serem inseridos na tabela.">Registros</A>&nbsp');
        } 
        ShowHTML('        </td>');
        ShowHTML('      </tr>');
        $w_atual=f($row,'nm_tabela');
      } 
    }
    ShowHTML('      </center>');
    ShowHTML('    </table>');
    ShowHTML('  </td>');
    ShowHTML('</tr>');
  } elseif (!(strpos('P',$O)===false)) {
    //Filtro para inclusão de um tabela no esquema
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td>');
    ShowHTML('    <table width="100%" border="0">');
    AbreForm('Form',$w_dir.$R,'POST','return(Validacao(this));',null,$P1,$P2,$P3,null,$TP,$SG,$R,'I');
    ShowHTML('<INPUT type="hidden" name="w_sq_esquema" value="'.$w_sq_esquema.'">');
    ShowHTML('<INPUT type="hidden" name="w_menu" value="'.$w_menu.'">');
    ShowHTML('<INPUT type="hidden" name="w_troca" value="">');
    ShowHTML('  <tr bgcolor="'.$conTrBgColor.'"><td colspan=2><div align="justify"><font size=2><b><ul>Instruções</b>:<li>Informe os parâmetros desejados para recuperar a lista de tabelas.<li>Quando a relação de tabelas for exibida, selecione as tabelas desejadas clicando sobre a caixa ao lado do nome.<li>Você pode informar o nome de uma tabela , selecionar as tabelas de um sistema, ou ainda as tabelas de um usuário.<li>Após informar os parâmetros desejados, clique sobre o botão <i>Aplicar filtro</i>.</ul><hr><b>Filtro</b></div>');
    ShowHTML('  <tr bgcolor="'.$conTrBgColor.'"><td colspan=2>');
    ShowHTML('    <table width="100%" border="0">');
    ShowHTML('      <tr>');
    SelecaoSistema('<u>S</u>istema:','S',null,$p_sq_sistema,$w_cliente,'p_sq_sistema',null,'onChange="document.Form.action=\''.$w_dir.$w_pagina.$par.'\'; document.Form.O.value=\''.$O.'\'; document.Form.w_troca.value=\'p_sq_usuario\'; document.Form.submit();"');
    SelecaoUsuario('<u>U</u>suário:','S',null,$w_cliente,$p_sq_usuario,Nvl($p_sq_sistema,0),'p_sq_usuario',null,null);
    ShowHTML('      <tr><td valign="top"><b><u>N</u>ome:</b><br><input '.$w_Disabled.' accesskey="N" type="text" name="p_nome" class="sti" SIZE="30" MAXLENGTH="30" VALUE="'.$p_nome.'"></td>');
    SelecaoTipoTabela('<u>T</u>ipo:','T',null,$p_sq_tabela_tipo,null,'p_sq_tabela_tipo',null,null);
    ShowHTML('      <tr><td valign="top"><b><U>L</U>inhas por página:<br><INPUT ACCESSKEY="L" '.$w_Disabled.' class="sti" type="text" name="P4" size="4" maxlength="4" value="'.$P4.'"></td></tr>');
    ShowHTML('      <tr><td align="center" colspan="3" height="1" bgcolor="#000000">');
    ShowHTML('      <tr><td align="center" colspan="3">');
    ShowHTML('            <input class="STB" type="submit" name="Botao" value="Aplicar filtro">');
    ShowHTML('            <input class="STB" type="button" onClick="location.href=\''.montaURL_JS($w_dir,$w_pagina.$par.'&w_sq_esquema='.$w_sq_esquema.'&R='.$R.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&O=P&SG='.$SG.'&w_menu='.$w_menu).'\';" name="Botao" value="Limpar campos">');
    ShowHTML('            <input class="stb" type="button" onClick="location.href=\''.montaURL_JS($w_dir,$w_pagina.$par.'&w_sq_esquema='.$w_sq_esquema.'&R='.$R.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'&w_menu='.$w_menu.'&O=L').'\';" name="Botao" value="Cancelar">');
    ShowHTML('          </td>');
    ShowHTML('      </tr>');
    ShowHTML('    </table>');
    ShowHTML('    </TD>');
    ShowHTML('</tr>');
    ShowHTML('</form>');
  } elseif (!(strpos('I',$O)===false)) {
    //Rotina de escolha e gravação de tabelas para o esquema
    AbreForm('Form',$w_dir.$w_pagina.'Grava','POST','return(Validacao(this));',null,$P1,$P2,$P3,$P4,$TP,$SG,$R,$O);
    ShowHTML('<INPUT type="hidden" name="w_sq_esquema" value="'.$w_sq_esquema.'">');
    ShowHTML('<INPUT type="hidden" name="w_sq_tabela[]" value="">');
    ShowHTML('<INPUT type="hidden" name="w_ordem[]" value="">');
    ShowHTML('<INPUT type="hidden" name="w_elemento[]" value="">');
    ShowHTML('<INPUT type="hidden" name="w_remove_registro[]" value="">');
    ShowHTML('<INPUT type="hidden" name="w_menu" value="'.$w_menu.'">');
    ShowHTML('<tr><td>');
    ShowHTML('                         <a accesskey="F" class="SS" href="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=P&P1='.$P1.'&P2='.$P2.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'&w_sq_esquema='.$w_sq_esquema.MontaFiltro('GET').'"><u><font color="#BC5100">F</u>iltrar (Ativo)</font></a></font>');
    ShowHTML('    <td align="right"><b>Registros: '.count($RS));
    ShowHTML('<tr><td align="center" colspan=3>');
    ShowHTML('    <TABLE id="tudo" WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
    ShowHTML('          <tr bgcolor="'.$conTrBgColor.'" align="center" valign="top">');
    ShowHTML('            <td width="70"NOWRAP><font size="2"><U ID="INICIO" CLASS="hl" onClick="javascript:MarcaTodos();" TITLE="Marca todos os itens da relação"><IMG SRC="images/NavButton/BookmarkAndPageActivecolor.gif" BORDER="1" width="15" height="15"></U>&nbsp;');
    ShowHTML('                                      <U CLASS="hl" onClick="javascript:DesmarcaTodos();" TITLE="Desmarca todos os itens da relação"><IMG SRC="images/NavButton/BookmarkAndPageInactive.gif" BORDER="1" width="15" height="15"></U>');
    ShowHTML('          <td><b>Sistema</b></font></td>');
    ShowHTML('          <td><b>Tabela</b></font></td>');
    ShowHTML('          <td><b>Descrição</b></font></td>');
    if(f($RS1,'formato')=='T')  ShowHTML('          <td><b>Arquivo (Insira o caminho completo)</b></font></td>');
    else                        ShowHTML('          <td><b>Elemento</b></font></td>');
    ShowHTML('          <td><b>Ordem</b></font></td>');
    ShowHTML('          <td><b>Remove registro</b></font></td>');
    if (count($RS)<=0) {
      // Se não foram selecionados registros, exibe mensagem
      ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td colspan=7 align="center"><b>Não foram encontrados registros.</b></td></tr>');
    } else {
      // Lista os registros selecionados para listagem
      $RS1 = array_slice($RS, (($P3-1)*$P4), $P4);
      $w_cont=0;
      foreach($RS1 as $row) {
        $w_cont+=1;
        $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;        
        ShowHTML('      <tr bgcolor="'.$w_cor.'" valign="top">');
        ShowHTML('        <td align="center"><input type="checkbox" name="w_sq_tabela[]" value="'.f($row,'chave').'" onClick="valor('.$w_cont.');">');
        ShowHTML('        <td>'.f($row,'sg_sistema').'</td>');
        ShowHTML('        <td>'.lower(f($row,'nm_usuario').'.'.f($row,'nome')).'</td>');
        ShowHTML('        <td>'.f($row,'descricao').'</td>');
        ShowHTML('        <td><input disabled type="text" name="w_elemento[]" class="sti" SIZE="20" MAXLENGTH="50" VALUE="'.$w_elemento.'"></td>');
        ShowHTML('        <td><input disabled type="text" name="w_ordem[]" class="sti" SIZE="4" MAXLENGTH="4" VALUE="'.$w_ordem.'"></td>');
        ShowHTML('        <td align="center"><input disabled type="checkbox" name="w_remove_registro[]" value="S" onClick="valor('.$w_cont.');">');
        ShowHTML('      </tr>');
      } 
    } 
    ShowHTML('      </center>');
    ShowHTML('    </table>');
    ShowHTML('<tr><td align="center" colspan="3">');
    ShowHTML('            <input class="stb" type="submit" name="Botao" value="Gravar">');
    ShowHTML('            <input class="stb" type="button" onClick="location.href=\''.montaURL_JS($w_dir,$w_pagina.$par.'&w_sq_esquema='.$w_sq_esquema.'&R='.$R.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'&w_menu='.$w_menu.'&O=L').'\';" name="Botao" value="Cancelar">');
    ShowHTML('  </td>');
    ShowHTML('</FORM>');
    ShowHTML('<tr><td align="center" colspan=3>');
    if ($R>'') {
      MontaBarra($w_dir.$w_pagina.$par.'&R='.$R.'&O='.$O.'&P1='.$P1.'&P2='.$P2.'&TP='.$TP.'&SG='.$SG.'&w_chave='.$w_chave.'&w_sq_esquema='.$w_sq_esquema,ceil(count($RS)/$P4),$P3,$P4,count($RS));
    } else {
      MontaBarra($w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O='.$O.'&P1='.$P1.'&P2='.$P2.'&TP='.$TP.'&SG='.$SG.'&w_chave='.$w_chave.'&w_sq_esquema='.$w_sq_esquema,ceil(count($RS)/$P4),$P3,$P4,count($RS));
    } 
    ShowHTML('</tr>');
  } elseif (!(strpos('A',$O)===false)) {
    //Rotina para alteração do dados da tabela de um esquema
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td>');
    ShowHTML('    <table width="100%" border="0">');
    AbreForm('Form',$w_dir.$w_pagina.'Grava','POST','return(Validacao(this));',null,$P1,$P2,$P3,null,$TP,$SG,$R,$O);
    ShowHTML('<INPUT type="hidden" name="w_sq_esquema" value="'.$w_sq_esquema.'">');
    ShowHTML('<INPUT type="hidden" name="w_sq_esquema_tabela" value="'.$w_sq_esquema_tabela.'">');
    ShowHTML('<INPUT type="hidden" name="w_troca" value="">');
    ShowHTML('<INPUT type="hidden" name="w_menu" value="'.$w_menu.'">');
    ShowHTML('  <tr bgcolor="'.$conTrBgColor.'"><td colspan=2>');
    ShowHTML('    <table width="100%" border="0">');
    ShowHTML('      <tr><td valign="top"><b><u>E</u>lemento:</b><br><input '.$w_Disabled.' accesskey="E" type="text" name="w_elemento" class="sti" SIZE="50" MAXLENGTH="50" VALUE="'.$w_elemento.'"></td>');
    ShowHTML('      <tr><td valign="top"><b><u>O</u>rdem:</b><br><input '.$w_Disabled.' accesskey="O" type="text" name="w_ordem" class="sti" SIZE="4" MAXLENGTH="4" VALUE="'.$w_ordem.'"></td>');
    if($w_remove_registro=='S') ShowHTML('      <tr><td valign="top"><b><u>R</u>emove registro:</b><br><input type="checkbox" name="w_remove_registro" value="S" CHECKED>');
    else                        ShowHTML('      <tr><td valign="top"><input type="checkbox" name="w_remove_registro" value="S"><b> Remove registro</b>');
    ShowHTML('      <tr><td align="center" colspan="3" height="1" bgcolor="#000000">');
    ShowHTML('      <tr><td align="center" colspan="3">');
    ShowHTML('            <input class="STB" type="submit" name="Botao" value="Gravar">');
    ShowHTML('            <input class="stb" type="button" onClick="location.href=\''.montaURL_JS($w_dir,$w_pagina.$par.'&w_sq_esquema='.$w_sq_esquema.'&R='.$R.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'&w_menu='.$w_menu.'&O=L').'\';" name="Botao" value="Cancelar">');
    ShowHTML('          </td>');
    ShowHTML('      </tr>');
    ShowHTML('    </table>');
    ShowHTML('    </TD>');
    ShowHTML('</tr>');
    ShowHTML('</form>');
  } else {
    ScriptOpen('JavaScript');
    ShowHTML(' alert(\'Opção não disponível\');');
    ScriptClose();
  } 
  ShowHTML('</table>');
  ShowHTML('</center>');
  Rodape();
} 
// =========================================================================
// Rotina de inclusão de tabelas no esquema
// -------------------------------------------------------------------------
function Mapeamento() {
  extract($GLOBALS);
  $w_sq_esquema_atributo    = $_REQUEST['w_sq_esquema_atributo'];
  $w_sq_esquema_tabela      = $_REQUEST['w_sq_esquema_tabela'];
  $w_sq_esquema             = $_REQUEST['w_sq_esquema'];
  $w_sq_tabela              = $_REQUEST['w_sq_tabela'];
  $w_sq_coluna              = $_REQUEST['w_sq_coluna'];
  if ($w_troca>'') {
    // Se for recarga da página
    $w_ordem            = $_REQUEST['w_ordem'];
    $w_campo_externo    = $_REQUEST['w_campo_externo'];
    $w_mascara_data     = $_REQUEST['w_mascara_data'];
    $w_valor_default    = $_REQUEST['w_valor_default'];    
  } elseif ($O=='I') {
    // Recupera todos os ws_url para a listagem
    $sql = new db_getColuna; $RS = $sql->getInstanceOf($dbms,$w_cliente,null,$w_sq_tabela,null,null,null,null,$w_sq_esquema_tabela);
    if (Nvl($p_ordena,'')>'') {
      $lista = explode(',',str_replace(' ',',',$p_ordena));
      $RS = SortArray($RS,$lista[0],$lista[1]);
    } else {    
      $RS = SortArray($RS,'ordem','asc','nm_coluna','asc');
    }
  }
  $sql = new db_getEsquema; $RS1 = $sql->getInstanceOf($dbms,$w_cliente,null,$w_sq_esquema,null,null,null,null,null,null,null,null);
  foreach($RS1 as $row){$RS1=$row; break;}
  $w_formato = f($RS1,'formato');
  Cabecalho();
  head();
  if (!(strpos('I',$O)===false)) {
    ScriptOpen('JavaScript');
    ShowHTML('  function valor(p_indice) {');
    ShowHTML('    if (document.Form["w_sq_coluna[]"][p_indice].checked) { ');
    ShowHTML('       document.Form["w_ordem[]"][p_indice].disabled=false; ');
    ShowHTML('       document.Form["w_campo_externo[]"][p_indice].disabled=false; ');
    if($w_formato=='T')  {    
      ShowHTML('       document.Form["w_mascara_data[]"][p_indice].disabled=false; ');
      ShowHTML('       document.Form["w_valor_default[]"][p_indice].disabled=false; ');  
      ShowHTML('       if(document.Form["w_mascara_data[]"][p_indice].value==\'\') document.Form["w_mascara_data[]"][p_indice].value=\'dd/mm/yyyy\';');  
    }
    ShowHTML('       document.Form["w_campo_externo[]"][p_indice].focus(); ');
    ShowHTML('    } else {');
    ShowHTML('       document.Form["w_ordem[]"][p_indice].disabled=true; ');
    ShowHTML('       document.Form["w_campo_externo[]"][p_indice].disabled=true; ');
    if($w_formato=='T')  {
      ShowHTML('       document.Form["w_mascara_data[]"][p_indice].disabled=true; ');
      ShowHTML('       document.Form["w_valor_default[]"][p_indice].disabled=true; ');    
    }
    //ShowHTML('       document.Form["w_ordem[]"][p_indice].value=\'\'; ');
    //ShowHTML('       document.Form["w_campo_externo[]"][p_indice].value=\'\'; ');
    if($w_formato=='T')  {
      //ShowHTML('       document.Form["w_mascara_data[]"][p_indice].value=\'\'; ');
      //ShowHTML('       document.Form["w_valor_default[]"][p_indice].value=\'\'; ');
    }
    ShowHTML('    }');
    ShowHTML('  }');
    ShowHTML('  function MarcaTodos() {');
    ShowHTML('    if (document.Form["w_sq_coluna[]"].value==undefined) ');
    ShowHTML('       for (i=1; i < document.Form["w_sq_coluna[]"].length; i++) {');
    ShowHTML('         document.Form["w_sq_coluna[]"][i].checked=true;');
    ShowHTML('         document.Form["w_ordem[]"][i].disabled=false;');
    ShowHTML('         document.Form["w_campo_externo[]"][i].disabled=false;');
    if($w_formato=='T')  {
      ShowHTML('         document.Form["w_mascara_data[]"][i].disabled=false;');
      ShowHTML('         document.Form["w_valor_default[]"][i].disabled=false;');
      ShowHTML('         if(document.Form["w_mascara_data[]"][i].value==\'\') document.Form["w_mascara_data[]"][i].value=\'dd\\\mm\\\yyyy\';');
    }
    ShowHTML('       } ');
    ShowHTML('    else document.Form["w_sq_coluna[]"].checked=true;');
    ShowHTML('  }');
    ShowHTML('  function DesmarcaTodos() {');
    ShowHTML('    if (document.Form["w_sq_coluna[]"].value==undefined) ');
    ShowHTML('       for (i=1; i < document.Form["w_sq_coluna[]"].length; i++) {');
    ShowHTML('         document.Form["w_sq_coluna[]"][i].checked=false;');
    ShowHTML('         document.Form["w_ordem[]"][i].disabled=true;');
    ShowHTML('         document.Form["w_campo_externo[]"][i].disabled=true;');
    if($w_formato=='T')  {
      ShowHTML('         document.Form["w_mascara_data[]"][i].disabled=true;');
      ShowHTML('         document.Form["w_valor_default[]"][i].disabled=true;');    
    }
    //ShowHTML('         document.Form["w_ordem[]"][i].value=\'\'; ');
    //ShowHTML('         document.Form["w_campo_externo[]"][i].value=\'\'; ');
    if($w_formato=='T')  {
      //ShowHTML('         document.Form["w_mascara_data[]"][i].value=\'\'; ');
      //ShowHTML('         document.Form["w_valor_default[]"][i].value=\'\'; ');
    }
    ShowHTML('       } ');
    ShowHTML('    ');
    ShowHTML('    else document.Form["w_sq_coluna[]"].checked=false;');
    ShowHTML('  }');
    CheckBranco();
    FormataData();
    SaltaCampo();
    ValidateOpen('Validacao');
    ShowHTML('  var i; ');
    ShowHTML('  var w_erro=true; ');
    ShowHTML('  if (theForm["w_sq_coluna[]"].value==undefined) {');
    ShowHTML('     for (i=0; i < theForm["w_sq_coluna[]"].length; i++) {');
    ShowHTML('       if (theForm["w_sq_coluna[]"][i].checked) w_erro=false;');
    ShowHTML('     }');
    ShowHTML('  }');
    ShowHTML('  else {');
    ShowHTML('     if (theForm["w_sq_coluna[]"].checked) w_erro=false;');
    ShowHTML('  }');
    ShowHTML('  if (w_erro) {');
    ShowHTML('    alert(\'Você deve informar pelo menos uma coluna!\'); ');
    ShowHTML('    return false;');
    ShowHTML('  }');
    ShowHTML('  for (i=0; i < theForm["w_sq_coluna[]"].length; i++) {');
    ShowHTML('    if((theForm["w_sq_coluna[]"][i].checked)&&(theForm["w_campo_externo[]"][i].value==\'\')){');
    ShowHTML('      alert(\'Para todas as colunas selecionadas você deve informar o campo externo da coluna!\'); ');
    ShowHTML('      return false;');
    ShowHTML('    }');
    ShowHTML('  }');
    ShowHTML('  for (i=0; i < theForm["w_sq_coluna[]"].length; i++) {');
    ShowHTML('    if((theForm["w_sq_coluna[]"][i].checked)&&(theForm["w_ordem[]"][i].value==\'\')){');
    ShowHTML('      alert(\'Para todas as colunas selecionadas você deve informar a ordem da coluna para a importação do esquema!\'); ');
    ShowHTML('      return false;');
    ShowHTML('    }');
    ShowHTML('  }');
    if($w_formato=='T')  {
      ShowHTML('  for (i=0; i < theForm["w_sq_coluna[]"].length; i++) {');
      ShowHTML('    if((theForm["w_sq_coluna[]"][i].checked)&&(theForm["w_mascara_data[]"][i].value==\'\')){');
      ShowHTML('      alert(\'Para todas as colunas selecionadas do tipo data você deve informar a mascara da data!\'); ');
      ShowHTML('      return false;');
      ShowHTML('    }');
      ShowHTML('  }');      
    }
    ShowHTML('  theForm.Botao[0].disabled=true;');
    ShowHTML('  theForm.Botao[1].disabled=true;');
    ValidateClose();
    ScriptClose();
  } 
  ShowHTML('</HEAD>');
  ShowHTML('<BASE HREF="'.$conRootSIW.'">');
  if ($w_troca>'') {
    BodyOpen('onLoad=\'document.Form.'.$w_troca.'.focus()\';');
  } else {
    BodyOpen(null);
  } 
  ShowHTML('<B><FONT COLOR="#000000">'.$w_TP.'</FONT></B>');
  ShowHTML('<HR>');
  ShowHTML('<div align=center><center>');
  ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
  ShowHTML('<tr><td colspan=3 bgcolor="#FAEBD7"><table border=1 width="100%"><tr><td>');
  ShowHTML('    <TABLE WIDTH="100%" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
  ShowHTML('      <tr><td colspan="3">Esquema: <b>'.f($RS1,'nome').'</font></b></td>');
  ShowHTML('      <tr><td colspan="3">Descrição: <b>'.Nvl(f($RS1,'Descricao'),'---').'</font></b></td>');
  ShowHTML('      <tr valign="top">');
  ShowHTML('          <td>Tipo: <b>'.f($RS1,'nm_tipo').'</b></td>');
  ShowHTML('          <td>Formato: <b>'.f($RS1,'nm_formato').'</b></td>');
  ShowHTML('          <td>Ativo: <b>'.f($RS1,'nm_ativo').'</b></td>');
  $sql = new db_getEsquemaTabela; $RS1 = $sql->getInstanceOf($dbms,null,$w_sq_esquema,$w_sq_esquema_tabela);
  foreach($RS1 as $row){$RS1=$row; break;}
  ShowHTML('      <tr><td colspan="3">Tabela: <b>'.Nvl(f($RS1,'nm_tabela'),'---').'</font></b></td>');
  ShowHTML('    </TABLE>');
  ShowHTML('</TABLE>');
  ShowHTML('</TABLE>');
  ShowHTML('<tr><td>&nbsp');
  ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
  if (!(strpos('I',$O)===false)) {
    //Rotina de escolha e gravação das colunas para a tabela
    AbreForm('Form',$w_dir.$w_pagina.'Grava','POST','return(Validacao(this));',null,$P1,$P2,$P3,$P4,$TP,'ISSIGMAP',$R,$O);
    ShowHTML('<INPUT type="hidden" name="w_menu" value="'.$w_menu.'">');
    ShowHTML('<INPUT type="hidden" name="w_sq_esquema" value="'.$w_sq_esquema.'">');
    ShowHTML('<INPUT type="hidden" name="w_sq_esquema_tabela" value="'.$w_sq_esquema_tabela.'">');
    ShowHTML('<INPUT type="hidden" name="w_sq_coluna[]" value="">');
    ShowHTML('<INPUT type="hidden" name="w_ordem[]" value="">');
    ShowHTML('<INPUT type="hidden" name="w_campo_externo[]" value="">');
    if($w_formato=='T')  {
      ShowHTML('<INPUT type="hidden" name="w_tipo_coluna[]" value="">');
      ShowHTML('<INPUT type="hidden" name="w_mascara_data[]" value="">');
      ShowHTML('<INPUT type="hidden" name="w_valor_default[]" value="">');
    }
    ShowHTML('<tr><td>');
    ShowHTML('    <td align="right"><b>Registros: '.count($RS));
    ShowHTML('<tr><td align="center" colspan=3>');
    ShowHTML('    <TABLE id="tudo" WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
    ShowHTML('          <tr bgcolor="'.$conTrBgColor.'" align="center" valign="top">');
    ShowHTML('            <td width="70"NOWRAP><font size="2"><U ID="INICIO" CLASS="hl" onClick="javascript:MarcaTodos();" TITLE="Marca todos os itens da relação"><IMG SRC="images/NavButton/BookmarkAndPageActivecolor.gif" BORDER="1" width="15" height="15"></U>&nbsp;');
    ShowHTML('                                      <U CLASS="hl" onClick="javascript:DesmarcaTodos();" TITLE="Desmarca todos os itens da relação"><IMG SRC="images/NavButton/BookmarkAndPageInactive.gif" BORDER="1" width="15" height="15"></U>');
    ShowHTML('          <td><b>Coluna</b></font></td>');
    ShowHTML('          <td><b>Tipo</b></font></td>');
    ShowHTML('          <td><b>Campo externo</b></font></td>');
    ShowHTML('          <td><b>Ordem</b></font></td>');
    if($w_formato=='T')  {
      ShowHTML('          <td><b>Máscara data</b></font></td>');
      ShowHTML('          <td><b>Valor default</b></font></td>');
    }
    if (count($RS)<=0) {
      // Se não foram selecionados registros, exibe mensagem
      ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td colspan=6 align="center"><b>Não foram encontrados registros.</b></td></tr>');
    } else {
      // Lista os registros selecionados para listagem
      $w_Disabled = 'DISABLED';
      $w_cont=0;
      foreach($RS as $row) {
        $w_cont+=1;
        $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;
        ShowHTML('      <tr bgcolor="'.$w_cor.'" valign="top">');
        $sql = new db_getEsquemaAtributo; $RS1 = $sql->getInstanceOf($dbms,null,$w_sq_esquema_tabela,null,f($row,'chave'));
        foreach($RS1 as $row1){$RS1=$row1; break;}
        if (count($RS1)>0) {
          ShowHTML('        <td align="center"><input type="checkbox" name="w_sq_coluna[]" value="'.f($row,'chave').'" onClick="valor('.$w_cont.');" CHECKED>');
          $w_ordem            = f($row1,'ordem');
          $w_campo_externo    = f($row1,'campo_externo');
          $w_mascara_data     = f($row1,'mascara_data');
          $w_valor_default    = f($row1,'valor_default');          
          $w_Disabled='';
        } else {
          ShowHTML('        <td align="center"><input type="checkbox" name="w_sq_coluna[]" value="'.f($row,'chave').'" onClick="valor('.$w_cont.');">');
        } 
        if (f($row,'obrigatorio')=='S') $w_title = 'OBRIGATÓRIO';
        else                            $w_title = 'OPICIONAL';
        $w_title .= ' Descrição: '.f($row,'descricao');
        ShowHTML('        <td title="'.$w_title.'">'.f($row,'nm_coluna').'</td>');
        ShowHTML('        <td nowrap>'.f($row,'nm_coluna_tipo').' (');
        if (upper(f($row,'nm_coluna_tipo'))=='NUMERIC') ShowHTML(Nvl(f($row,'precisao'),f($row,'tamanho')).','.Nvl(f($row,'escala'),0));
        else                                                 ShowHTML(f($row,'tamanho'));
        ShowHTML(')</td>');
        ShowHTML('        <td nowrap><input '.$w_Disabled.' type="text" name="w_campo_externo[]" class="sti" SIZE="20" MAXLENGTH="30" VALUE="'.$w_campo_externo.'"></td>');
        ShowHTML('        <td nowrap><input '.$w_Disabled.' type="text" name="w_ordem[]" class="sti" SIZE="4" MAXLENGTH="4" VALUE="'.$w_ordem.'"></td>');
        if($w_formato=='T')  {
          ShowHTML('<INPUT type="hidden" name="w_tipo_coluna[]" value="'.f($row,'nm_coluna_tipo').'">');
          if (upper(f($row,'nm_coluna_tipo'))=='DATE')  ShowHTML('        <td nowrap><input '.$w_Disabled.' type="text" name="w_mascara_data[]" class="sti" SIZE="20" MAXLENGTH="50" VALUE="'.$w_mascara_data.'"></td>');
          else                                               ShowHTML('<td>&nbsp;</td><INPUT type="hidden" name="w_mascara_data[]" value="-1">');
          ShowHTML('        <td nowrap><input '.$w_Disabled.' type="text" name="w_valor_default[]" class="sti" SIZE="20" MAXLENGTH="50" VALUE="'.$w_valor_default.'"></td>');
        }        
        ShowHTML('      </tr>');
        $w_ordem          = '';
        $w_campo_externo  = '';
        $w_mascara_data   = '';
        $w_valor_default  = '';
        $w_Disabled='DISABLED';
      } 
    } 
    ShowHTML('      </center>');
    ShowHTML('    </table>');
    ShowHTML('<tr><td align="center" colspan="3">');
    ShowHTML('            <input class="stb" type="submit" name="Botao" value="Gravar">');
    ShowHTML('            <input class="stb" type="button" onClick="location.href=\''.montaURL_JS($w_dir,$w_pagina.'Tabela'.'&w_sq_esquema='.$w_sq_esquema.'&R='.$R.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'&w_menu='.$w_menu.'&O=L').'\';" name="Botao" value="Cancelar">');
    ShowHTML('  </td>');
    ShowHTML('</FORM>');
  } else {
    ScriptOpen('JavaScript');
    ShowHTML(' alert(\'Opção não disponível\');');
    ScriptClose();
  } 
  ShowHTML('</table>');
  ShowHTML('</center>');
  Rodape();
} 
// =========================================================================
// Rotina de importação de arquivos físicos para atualização
// -------------------------------------------------------------------------
function Importacao() {
  extract($GLOBALS);
  $w_sq_esquema = $_REQUEST['w_sq_esquema'];
  $sql = new db_getCustomerData; $RS = $sql->getInstanceOf($dbms,$w_cliente);
  $w_upload_maximo = f($RS,'upload_maximo');
  if ($O=='I') {
    // Recupera todos os ws_url para a listagem
    $sql = new db_getEsquema; $RS = $sql->getInstanceOf($dbms,$w_cliente,null,$w_sq_esquema,null,null,null,null,null,null,null,null);
    foreach($RS as $row){$RS=$row; break;}
  } elseif (!(strpos('AE',$O)===false)) { } 
  Cabecalho();
  head();
  if (!(strpos('IAEP',$O)===false)) {
    ScriptOpen('JavaScript');
    if (f($RS,'formato')!='T') {
      CheckBranco();
      FormataDataHora();
      FormataData();
      SaltaCampo();
    }
    ValidateOpen('Validacao');
    if (f($RS,'formato')!='T') {
      if (!(strpos('I',$O)===false)) {
        Validate('w_data_arquivo','Data e hora','DATAHORA','1','17','17','','0123456789 /:,');
        Validate('w_caminho','Arquivo de dados','1','1','1','255','1','1');
        Validate('w_assinatura','Assinatura Eletrônica','1','1','6','30','1','1');
      } 
    } else {
      Validate('w_assinatura','Assinatura Eletrônica','1','1','6','30','1','1');
    }
    ShowHTML('  theForm.Botao[0].disabled=true;');
    ShowHTML('  theForm.Botao[1].disabled=true;');
    ValidateClose();
    ScriptClose();
  }
  ShowHTML('</HEAD>');
  ShowHTML('<BASE HREF="'.$conRootSIW.'">');
  if ($w_troca>'') {
    BodyOpen('onLoad=\'document.Form.'.$w_troca.'.focus()\';');
  } elseif (!(strpos('I',$O)===false)) {
    if (f($RS,'formato')=='A') {
      BodyOpen('onLoad=\'document.Form.w_data_arquivo.focus()\';');
    } elseif (f($RS,'formato')=='W' || f($RS,'formato')=='T') {
      BodyOpen('onLoad=\'document.Form.w_assinatura.focus()\';');
    } 
  } elseif (!(strpos('E',$O)===false)) {
    BodyOpen('onLoad=\'document.Form.w_assinatura.focus()\';');
  } else {
    BodyOpen(null);
  } 
  ShowHTML('<B><FONT COLOR="#000000">'.$w_TP.'</FONT></B>');
  ShowHTML('<HR>');
  ShowHTML('<div align=center><center>');
  ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
  if ($O=='L') {
    
  } elseif (!(strpos('I',$O)===false)) {
    if (f($RS,'formato')=='A') {
      ShowHTML('<FORM action="'.$w_dir.$w_pagina.'Grava&SG=IMPARQ&O='.$O.'&w_menu='.$w_menu.'&UploadID='.$UploadID.'" name="Form" onSubmit="return(Validacao(this));" enctype="multipart/form-data" method="POST">');
      ShowHTML('<INPUT type="hidden" name="P1" value="'.$P1.'">');
      ShowHTML('<INPUT type="hidden" name="P2" value="'.$P2.'">');
      ShowHTML('<INPUT type="hidden" name="P3" value="'.$P3.'">');
      ShowHTML('<INPUT type="hidden" name="P4" value="'.$P4.'">');
      ShowHTML('<INPUT type="hidden" name="TP" value="'.$TP.'">');
      ShowHTML('<INPUT type="hidden" name="R" value="'.$R.'">');
    } elseif (f($RS,'formato')=='W') {
      AbreForm('Form',$w_dir.$w_pagina.'Grava','POST','return(Validacao(this));',null,$P1,$P2,1,$P4,$TP,'IMPWEB',$R,$O);
      ShowHTML('<INPUT type="hidden" name="w_menu" value="'.$w_menu.'">');
    } elseif (f($RS,'formato')=='T') {
      AbreForm('Form',$w_dir.$w_pagina.'Grava','POST','return(Validacao(this));',null,$P1,$P2,1,$P4,$TP,'IMPTXT',$R,$O);
      ShowHTML('<INPUT type="hidden" name="w_menu" value="'.$w_menu.'">');
    } 
    ShowHTML('<INPUT type="hidden" name="w_sq_esquema" value="'.$w_sq_esquema.'">');
    ShowHTML('<INPUT type="hidden" name="w_tipo" value="'.$p_tipo.'">');
    ShowHTML('<INPUT type="hidden" name="w_troca" value="">');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td align="center">');
    ShowHTML('    <table width="97%" border="0">');
    ShowHTML('      <tr><td bgcolor="#FAEBD7"><table border=1 width="100%"><tr><td>');
    ShowHTML('      <TABLE WIDTH="100%" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
    ShowHTML('      <tr><td>Nome:<b> '.f($RS,'Nome').'</b></td>');
    ShowHTML('<INPUT type="hidden" name="w_nm_esquema" value="'.f($RS,'Nome').'">');
    ShowHTML('      <tr><td>Descrição:<b> '.Nvl(f($RS,'Descricao'),'---').'</b></td>');
    ShowHTML('      <tr><td><table border=0 width="100%" cellpadding=0 cellspacing=0><tr valign="top">');
    ShowHTML('          <td>Formato:<b> '.f($RS,'nm_formato').'</b></td>');
    ShowHTML('       <td>Ativo:<b> '.f($RS,'nm_ativo').'</b></td>');
    if (f($RS,'formato')=='W' || f($RS,'formato')=='A') {
      ShowHTML('          <td>Nó raiz:<b> '.f($RS,'no_raiz').'</b></td>');
      ShowHTML('              </table>');
      if (f($RS,'formato')=='W') {
        ShowHTML('      <tr><td>Servidor:<b> '.f($RS,'ws_servidor').'</b></td>');
        ShowHTML('      <tr><td>URL:<b> '.f($RS,'ws_url').'</b></td>');
        ShowHTML('      <tr><td>Ação:<b> '.f($RS,'ws_acao').'</b></td>');
        ShowHTML('      <tr><td>Mensagem:<b> '.f($RS,'ws_mensagem').'</b></td>');
      }
    } elseif (f($RS,'formato')=='T') {
      ShowHTML('              </table>');
      ShowHTML('      <tr><td>Hostname:<b> '.f($RS,'bd_hostname').'</b></td>');
      ShowHTML('          <td>Username:<b> '.f($RS,'bd_username').'</b></td>');
      ShowHTML('      <tr><td>Password:<b> '.f($RS,'bd_password').'</b></td>');
      ShowHTML('           <td>Delimitador:<b> '.f($RS,'tx_delimitador').'</b></td>');
    } 
    ShowHTML('    </TABLE>');
    ShowHTML('</TABLE>');
    if (f($RS,'formato')=='A') {
      ShowHTML('      <tr><td align="center" bgcolor="#D0D0D0" style="border: 2px solid rgb(0,0,0);"><font size="2"><b><font color="#BC3131">ATENÇÃO</font>: o tamanho máximo aceito para o arquivo é de '.($w_upload_maximo/1024).' KBytes</b>.</font></td>');
      ShowHTML('<INPUT type="hidden" name="w_upload_maximo" value="'.$w_upload_maximo.'">');
      ShowHTML('      <tr><td><b><u>D</u>ata/hora extração:</b><br><input '.$w_Disabled.' accesskey="D" type="text" name="w_data_arquivo" class="sti" SIZE="17" MAXLENGTH="17" VALUE="'.$w_data_arquivo.'"  onKeyDown="FormataDataHora(this, event);" onKeyUp="SaltaCampo(this.form.name,this,17,event);" title="OBRIGATÓRIO. Informe a data e hora da extração do aquivo. Digite apenas números. O sistema colocará os separadores automaticamente."></td>');
      ShowHTML('      <tr><td><b>A<u>r</u>quivo:</b><br><input accesskey="R" type="file" name="w_caminho" class="STI" SIZE="80" MAXLENGTH="100" VALUE="" title="OBRIGATÓRIO. Clique no botão ao lado para localizar o arquivo. Ele será transferido automaticamente para o servidor.">');
    } elseif (f($RS,'formato')=='T') {
      $w_comando = 'php '.$conDiretorio.'mod_dc/etl_txt.php '.$w_cliente.' '.$_SESSION['DBMS'];
      $w_arquivo = $conDiretorio.'mod_dc/etl_txt.php';
      ShowHTML('      <tr><td align="center" bgcolor="#D0D0D0" style="border: 2px solid rgb(0,0,0);"><font size="2"><b><font color="#BC3131">ATENÇÃO</font>: será executado o comando abaixo no servidor.<br></b><font face="courier" size=1 color="black">'. $w_comando .'</font></font></td>');
      ShowHTML('<INPUT type="hidden" name="w_comando" value="'.$w_comando.'">');
      ShowHTML('<INPUT type="hidden" name="w_arquivo" value="'.$w_arquivo.'">');
    }
    ShowHTML('      <tr><td align="LEFT"><b><U>A</U>ssinatura Eletrônica:<BR> <INPUT ACCESSKEY="A" class="sti" type="PASSWORD" name="w_assinatura" size="30" maxlength="30" value=""></td></tr>');
    ShowHTML('      <tr><td align="center"><hr>');
    ShowHTML('          <input class="STB" type="submit" name="Botao" value="Importar">');
    ShowHTML('          <input class="stb" type="button" onClick="location.href=\''.montaURL_JS($w_dir,$R.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.'TIMPORT'.'&O=L').'\';" name="Botao" value="Cancelar">');
    ShowHTML('          </td>');
    ShowHTML('      </tr>');
    ShowHTML('    </table>');
    ShowHTML('    </TD>');
    ShowHTML('</tr>');
    ShowHTML('</FORM>');
  } else {
    ScriptOpen('JavaScript');
    ShowHTML(' alert(\'Opção não disponível\');');
    ScriptClose();
  } 
  ShowHTML('</table>');
  ShowHTML('</center>');
  Rodape();
}
// =========================================================================
// Rotina de inclusão dos registros das tabelas do esquema, para serem criados os inserts.
// -------------------------------------------------------------------------
function Registro() {
  extract($GLOBALS);
  $w_sq_esquema_tabela  = $_REQUEST['w_sq_esquema_tabela'];
  $w_sq_esquema         = $_REQUEST['w_sq_esquema'];
  $w_sq_tabela          = $_REQUEST['w_sq_tabela'];
  $w_registro           = $_REQUEST['w_registro'];
  //Recupera os dados do esquema para a montagem do cabeçalho
  $sql = new db_getEsquema; $RS1 = $sql->getInstanceOf($dbms,$w_cliente,null,$w_sq_esquema,null,null,null,null,null,null,null,null);
  foreach($RS1 as $row){$RS1=$row; break;}
  if ($w_troca>'') {
    // Se for recarga da página
    $w_valor            = $_REQUEST['w_valor'];
  } elseif ($O=='L') {
    // Recupera todos os ws_url para a listagem
    $sql = new db_getEsquemaInsert; $RS = $sql->getInstanceOf($dbms,null,null,$w_sq_esquema_tabela,null,null);
    $RS = SortArray($RS,'registro','asc','or_coluna','asc');
  } elseif (!(strpos('IA',$O)===false)) {
    $sql = new db_getEsquemaAtributo; $RS = $sql->getInstanceOf($dbms,null,$w_sq_esquema_tabela,null,null);
    $RS = SortArray($RS,'ordem','asc','nm_coluna','asc');
    $sql = new db_getColuna; //$RS = $sql->getInstanceOf($dbms,$w_cliente,null,$w_sq_tabela,null,null,null,null,$w_sq_esquema_tabela);
    //$RS = SortArray($RS,'ordem','asc','nm_coluna','asc');
  } 
  Cabecalho();
  head();
  if (!(strpos('IA',$O)===false)) {
    ScriptOpen('JavaScript');
    ValidateOpen('Validacao');
    ShowHTML('  theForm.Botao[0].disabled=true;');
    ShowHTML('  theForm.Botao[1].disabled=true;');
    ValidateClose();
    ScriptClose();
  } 
  ShowHTML('</HEAD>');
  ShowHTML('<BASE HREF="'.$conRootSIW.'">');
  if ($w_troca>'') {
    BodyOpen('onLoad=\'document.Form.'.$w_troca.'.focus()\';');
  } else {
    BodyOpen(null);
  } 
  ShowHTML('<B><FONT COLOR="#000000">'.$w_TP.'</FONT></B>');
  ShowHTML('<HR>');
  ShowHTML('<div align=center><center>');
  ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
  ShowHTML('<tr><td colspan=3 bgcolor="#FAEBD7"><table border=1 width="100%"><tr><td>');
  ShowHTML('    <TABLE WIDTH="100%" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
  ShowHTML('      <tr><td colspan="3">Esquema: <b>'.f($RS1,'nome').'</font></b></td>');
  ShowHTML('      <tr><td colspan="3">Descrição: <b>'.Nvl(f($RS1,'Descricao'),'---').'</font></b></td>');
  ShowHTML('      <tr valign="top">');
  ShowHTML('          <td>Tipo: <b>'.f($RS1,'nm_tipo').'</b></td>');
  ShowHTML('          <td>Formato: <b>'.f($RS1,'nm_formato').'</b></td>');
  ShowHTML('          <td>Ativo: <b>'.f($RS1,'nm_ativo').'</b></td>');
  $sql = new db_getEsquemaTabela; $RS1 = $sql->getInstanceOf($dbms,null,$w_sq_esquema,$w_sq_esquema_tabela);
  foreach($RS1 as $row){$RS1=$row; break;}
  ShowHTML('      <tr><td colspan="3">Tabela: <b>'.Nvl(f($RS1,'nm_tabela'),'---').'</font></b></td>');
  ShowHTML('    </TABLE>');
  ShowHTML('</TABLE>');
  ShowHTML('</TABLE>');
  ShowHTML('<tr><td>&nbsp');
  ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
  if ($O=='L') {
    //Listagem das tabelas do esquema
    // Exibe a quantidade de ws_url apresentados na listagem e o cabeçalho da tabela de listagem
    ShowHTML('<tr><td>');
    ShowHTML('        <a accesskey="I" class="SS" href="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=I&w_sq_esquema='.$w_sq_esquema.'&w_sq_esquema_tabela='.$w_sq_esquema_tabela.'&w_sq_tabela='.$w_sq_tabela.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'&w_menu='.$w_menu.'"><u>I</u>ncluir</a>&nbsp;');
    ShowHTML('        <a accesskey="F" class="ss" href="javascript:window.close(); opener.location.reload(); opener.focus();"><u>F</u>echar</a>&nbsp;');
    ShowHTML('    <td align="right"><b>Registros: '.count($RS));
    ShowHTML('<tr><td align="center" colspan=3>');
    ShowHTML('    <TABLE id="tudo" WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
    ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center">');
    ShowHTML('          <td rowspan="2"><b>Registro</font></td>');
    ShowHTML('          <td rowspan="1" colspan="3"><b>Campos</font></td>');
    ShowHTML('          <td rowspan="2"><b>Operações</font></td>');
    ShowHTML('        </tr>');
    ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center">');
    ShowHTML('          <td><b>Nome</font></td>');
    ShowHTML('          <td><b>Ordem</font></td>');
    ShowHTML('          <td><b>Valor</font></td>');
    ShowHTML('        </tr>');
    $w_cont=0;
    if (count($RS)<=0) {
      // Se não foram selecionados ws_url, exibe mensagem
      ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td colspan=5 align="center"><b>Não foram encontrados registros.</b></td></tr>');
    } else {
      // Lista os ws_url selecionados para listagem
      foreach($RS as $row) {
        $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;
        ShowHTML('      <tr bgcolor="'.$w_cor.'" valign="top">');
        if ($w_atual!=f($row,'registro')) {
          ShowHTML('        <td rowspan="'.f($row,'qtd_coluna').'" align="center">'.f($row,'registro').'</td>');
        } 
        ShowHTML('        <td>'.Nvl(f($row,'cl_nome'),'---').'</td>');
        ShowHTML('        <td align="center">'.Nvl(f($row,'or_coluna'),'---').'</td>');
        ShowHTML('        <td>'.Nvl(f($row,'valor'),'---').'</td>');
        if ($w_atual!=f($row,'registro')) {
          ShowHTML('        <td rowspan="'.f($row,'qtd_coluna').'">');
          ShowHTML('          <A class="hl" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=A&w_sq_esquema='.f($row,'sq_esquema').'&w_sq_esquema_tabela='.f($row,'sq_esquema_tabela').'&w_registro='.f($row,'registro').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'&w_menu='.$w_menu.MontaFiltro('GET').'" title="Altera a os dados da tabela deste esquema">AL</A>&nbsp');
          ShowHTML('          <A class="hl" HREF="'.$w_dir.$w_pagina.'Grava'.'&R='.$w_pagina.$par.'&O=E&w_sq_esquema='.f($row,'sq_esquema').'&w_sq_esquema_tabela='.f($row,'sq_esquema_tabela').'&w_registro='.f($row,'registro').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'&w_menu='.$w_menu.MontaFiltro('GET').'" title="Exclui a tabela deste esquema" onClick="return confirm(\'Confirma a exclusão do registro?\');">EX</A>&nbsp');
        } 
        ShowHTML('        </td>');
        ShowHTML('      </tr>');
        $w_atual=f($row,'registro');
      } 
    }
    ShowHTML('      </center>');
    ShowHTML('    </table>');
    ShowHTML('  </td>');
    ShowHTML('</tr>');
  } elseif (!(strpos('IA',$O)===false)) {
    //Rotina de escolha e gravação das colunas para a tabela
    AbreForm('Form',$w_dir.$w_pagina.'Grava','POST','return(Validacao(this));',null,$P1,$P2,$P3,$P4,$TP,'TIREGISTRO',$R,$O);
    ShowHTML('<INPUT type="hidden" name="w_menu" value="'.$w_menu.'">');
    ShowHTML('<INPUT type="hidden" name="w_sq_esquema" value="'.$w_sq_esquema.'">');
    ShowHTML('<INPUT type="hidden" name="w_sq_esquema_tabela" value="'.$w_sq_esquema_tabela.'">');
    ShowHTML('<INPUT type="hidden" name="w_sq_tabela" value="'.$w_sq_tabela.'">');
    ShowHTML('<INPUT type="hidden" name="w_registro" value="'.$w_registro.'">');
    ShowHTML('<tr><td>');
    ShowHTML('    <td align="right"><b>Registros: '.count($RS));
    ShowHTML('<tr><td align="center" colspan=3>');
    ShowHTML('    <TABLE id="tudo" WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
    ShowHTML('          <tr bgcolor="'.$conTrBgColor.'" align="center" valign="top">');
    ShowHTML('          <td><b>Coluna</b></font></td>');
    ShowHTML('          <td><b>Tipo</b></font></td>');
    ShowHTML('          <td><b>Ordem</b></font></td>');
    ShowHTML('          <td><b>Valor</b></font></td>');
    if (count($RS)<=0) {
      // Se não foram selecionados registros, exibe mensagem
      ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td colspan=6 align="center"><b>Não foram encontrados registros.</b></td></tr>');
    } else {
      // Lista os registros selecionados para listagem
      foreach($RS as $row) {
        ShowHTML('<INPUT type="hidden" name="w_sq_coluna[]" value="'.f($row,'sq_coluna').'">');
        $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;
        ShowHTML('      <tr bgcolor="'.$w_cor.'" valign="top">');        
        if($O=='A') {
          $sql = new db_getEsquemaInsert; $RS1 = $sql->getInstanceOf($dbms,null,null,$w_sq_esquema_tabela, f($row,'sq_coluna'),$w_registro);
          foreach($RS1 as $row1){$RS1=$row1; break;}
          if (count($RS1)>0) {
            $w_valor = f($RS1,'valor');
            $w_sq_esquema_insert = f($RS1,'sq_esquema_insert');
          }
        }
        if (f($row,'obrigatorio')=='S') $w_title = 'OBRIGATÓRIO';
        else                            $w_title = 'OPICIONAL';
        $w_title .= ' Descrição: '.f($row,'descricao');
        ShowHTML('        <td title="'.$w_title.'">'.f($row,'nm_coluna').'</td>');
        ShowHTML('        <td nowrap>'.f($row,'nm_coluna_tipo').' (');
        if (upper(f($row,'nm_coluna_tipo'))=='NUMERIC') ShowHTML(Nvl(f($row,'precisao'),f($row,'tamanho')).','.Nvl(f($row,'escala'),0));
        else                                                 ShowHTML(f($row,'tamanho'));
        ShowHTML(')</td>');
        ShowHTML('        <td>'.f($row,'or_coluna').'</td>');
        ShowHTML('<INPUT type="hidden" name="w_sq_esquema_insert[]" value="'.$w_sq_esquema_insert.'">');
        ShowHTML('<INPUT type="hidden" name="w_ordem[]" value="'.f($row,'or_coluna').'">');
        ShowHTML('        <td align="center"><input '.$w_Disabled.' type="text" name="w_valor[]" class="sti" SIZE="50" MAXLENGTH="255" VALUE="'.$w_valor.'"></td>');
        ShowHTML('      </tr>');
        $w_valor = '';
      } 
    } 
    ShowHTML('      </center>');
    ShowHTML('    </table>');
    ShowHTML('<tr><td align="center" colspan="3">');
    ShowHTML('            <input class="stb" type="submit" name="Botao" value="Gravar">');
    ShowHTML('            <input class="stb" type="button" onClick="location.href=\''.montaURL_JS($w_dir,$w_pagina.'Registro&w_sq_esquema_tabela='.$w_sq_esquema_tabela.'&w_sq_tabela='.$w_sq_tabela.'&w_sq_esquema='.$w_sq_esquema.'&R='.$R.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'&w_menu='.$w_menu.'&O=L').'\';" name="Botao" value="Cancelar">');
    ShowHTML('  </td>');
    ShowHTML('</FORM>');
  } else {
    ScriptOpen('JavaScript');
    ShowHTML(' alert(\'Opção não disponível\');');
    ScriptClose();
  } 
  ShowHTML('</table>');
  ShowHTML('</center>');
  Rodape();
} 
// ------------------------------------------------------------------------- 
// Rotina de upload dos arquivos com os scripts a serem executados no banco 
// ------------------------------------------------------------------------- 
function Script() {
  extract($GLOBALS);
  global $w_Disabled;
  $w_sq_esquema_script  = $_REQUEST['w_sq_esquema_script'];
  $w_sq_arquivo         = $_REQUEST['w_sq_arquivo'];
  $w_sq_esquema         = $_REQUEST['w_sq_esquema'];
  if ($w_troca>'') {
    // Se for recarga da página 
    $w_nome         = $_REQUEST['w_nome'];
    $w_descricao    = $_REQUEST['w_descricao'];
    $w_caminho      = $_REQUEST['w_caminho'];
  } elseif ($O=='L') {
    // Recupera todos os registros para a listagem 
    $sql = new db_getEsquemaScript; $RS = $sql->getInstanceOf($dbms,$w_sq_esquema,null,$w_cliente);
    $RS = SortArray($RS,'ordem','asc');
  } elseif (!(strpos('AEV',$O)===false) && $w_troca=='') {
    // Recupera os dados do endereço informado 
    $sql = new db_getEsquemaScript; $RS = $sql->getInstanceOf($dbms,$w_sq_esquema,$w_sq_arquivo,$w_cliente);
    foreach($RS as $row) {$RS=$row; break;}
    $w_nome         = f($RS,'nome');
    $w_descricao    = f($RS,'descricao');
    $w_caminho      = f($RS,'caminho');
    $w_ordem        = f($RS,'ordem');
  } 
  Cabecalho();
  head();
  if (!(strpos('IAEP',$O)===false)) {
    ScriptOpen('JavaScript');
    ValidateOpen('Validacao');
    if (!(strpos('IA',$O)===false)) {
      Validate('w_nome','Título','1','1','1','255','1','1'); 
      Validate('w_descricao','Descrição','1','1','1','1000','1','1');
      Validate('w_ordem','Ordem','1','1','1','18','','1');
      if ($O=='I') Validate('w_caminho','Arquivo','','1','5','255','1','1'); 
    } 
    ShowHTML('  theForm.Botao[0].disabled=true;');
    ShowHTML('  theForm.Botao[1].disabled=true;');
    ValidateClose();
    ScriptClose();
  } 
  ShowHTML('</HEAD>');
  ShowHTML('<BASE HREF="'.$conRootSIW.'">');
  if ($w_troca>'') {
    BodyOpen('onLoad=\'document.Form.'.$w_troca.'.focus()\';');
  } elseif ($O=='I') {
   BodyOpen('onLoad=\'document.Form.w_nome.focus()\';');
  } elseif ($O=='A') {
    BodyOpen('onLoad=\'document.Form.w_descricao.focus()\';');
  } else {
    BodyOpen(null);
  } 
  ShowHTML('<B><FONT COLOR="#000000">'.$w_TP.'</FONT></B>');
  ShowHTML('<HR>');
  ShowHTML('<div align=center><center>');
  ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
  $sql = new db_getEsquema; $RS1 = $sql->getInstanceOf($dbms,$w_cliente,null,$w_sq_esquema,null,null,null,null,null,null,null,null);
  foreach($RS1 as $row){$RS1=$row; break;}
  ShowHTML('<tr><td colspan=3 bgcolor="#FAEBD7"><table border=1 width="100%"><tr><td>');
  ShowHTML('    <TABLE WIDTH="100%" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
  ShowHTML('      <tr><td colspan="3">Esquema: <b>'.f($RS1,'nome').'</font></b></td>');
  ShowHTML('      <tr><td colspan="3">Descrição: <b>'.Nvl(f($RS1,'Descricao'),'---').'</font></b></td>');
  ShowHTML('      <tr valign="top">');
  ShowHTML('          <td>Tipo: <b>'.f($RS1,'nm_tipo').'</b></td>');
  ShowHTML('          <td>Formato: <b>'.f($RS1,'nm_formato').'</b></td>');
  ShowHTML('          <td>Ativo: <b>'.f($RS1,'nm_ativo').'</b></td>');
  ShowHTML('    </TABLE>');
  ShowHTML('</TABLE>');
  if ($O=='L') {
    // Exibe a quantidade de registros apresentados na listagem e o cabeçalho da tabela de listagem 
    ShowHTML('<tr><td><a accesskey="I" class="SS" href="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=I&w_sq_esquema='.$w_sq_esquema.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'"><u>I</u>ncluir</a>&nbsp;');
    ShowHTML('        <a accesskey="F" class="ss" href="javascript:window.close(); opener.location.reload(); opener.focus();"><u>F</u>echar</a>&nbsp;');
    ShowHTML('    <td align="right"><b>Registros: '.count($RS));
    ShowHTML('<tr><td align="center" colspan=3>');
    ShowHTML('    <TABLE id="tudo" WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
    ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center">');
    ShowHTML('          <td><b>Título</td>');
    ShowHTML('          <td><b>Descrição</td>');
    ShowHTML('          <td><b>Tipo</td>');
    ShowHTML('          <td><b>Ordem</td>');
    ShowHTML('          <td><b>KB</td>');
    ShowHTML('          <td ><b>Operações</td>');
    ShowHTML('        </tr>');
    if (count($RS)<=0) {
      // Se não foram selecionados registros, exibe mensagem 
      ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td colspan=7 align="center"><b>Não foram encontrados registros.</b></td></tr>');
    } else {
      // Lista os registros selecionados para listagem 
      foreach($RS as $row) {
        $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;
        ShowHTML('      <tr bgcolor="'.$w_cor.'" valign="top">');
        ShowHTML('        <td>'.LinkArquivo('HL',$w_cliente,f($row,'chave_aux'),'_blank','Clique para exibir o arquivo em outra janela.',f($row,'nome'),null).'</td>');
        ShowHTML('        <td>'.Nvl(f($row,'descricao'),'---').'</td>');
        ShowHTML('        <td>'.f($row,'tipo').'</td>');
        ShowHTML('        <td>'.f($row,'ordem').'</td>');
        ShowHTML('        <td align="right">'.round((f($row,'tamanho')/1024),1).'&nbsp;</td>');
        ShowHTML('        <td align="top" nowrap>');
        ShowHTML('          <A class="HL" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=A&w_sq_esquema_script='.f($row,'chave').'&w_sq_arquivo='.f($row,'chave_aux').'&w_sq_esquema='.$w_sq_esquema.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'">AL</A>&nbsp');
        ShowHTML('          <A class="HL" HREF="'.$w_dir.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=E&w_sq_esquema_script='.f($row,'chave').'&w_sq_arquivo='.f($row,'chave_aux').'&w_sq_esquema='.$w_sq_esquema.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'">EX</A>&nbsp');
        ShowHTML('        </td>');
        ShowHTML('      </tr>');
      } 
    }
    ShowHTML('      </center>');
    ShowHTML('    </table>');
    ShowHTML('  </td>');
    ShowHTML('</tr>');
  } elseif (!(strpos('IAEV',$O)===false)) {
    if (!(strpos('EV',$O)===false)) $w_Disabled=' DISABLED ';
    ShowHTML('<FORM action="'.$w_dir.$w_pagina.'Grava&SG='.$SG.'&O='.$O.'" name="Form" onSubmit="return(Validacao(this));" enctype="multipart/form-data" method="POST">');
    ShowHTML('<INPUT type="hidden" name="P1" value="'.$P1.'">');
    ShowHTML('<INPUT type="hidden" name="P2" value="'.$P2.'">');
    ShowHTML('<INPUT type="hidden" name="P3" value="'.$P3.'">');
    ShowHTML('<INPUT type="hidden" name="P4" value="'.$P4.'">');
    ShowHTML('<INPUT type="hidden" name="TP" value="'.$TP.'">');
    ShowHTML('<INPUT type="hidden" name="R" value="'.$R.'">');
    ShowHTML('<INPUT type="hidden" name="w_sq_esquema_script" value="'.$w_sq_esquema_script.'">');
    ShowHTML('<INPUT type="hidden" name="w_sq_arquivo" value="'.$w_sq_arquivo.'">');
    ShowHTML('<INPUT type="hidden" name="w_sq_esquema" value="'.$w_sq_esquema.'">');    
    ShowHTML('<INPUT type="hidden" name="w_atual" value="'.$w_caminho.'">');
    ShowHTML('<INPUT type="hidden" name="w_troca" value="">');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td align="center">');
    ShowHTML('    <table width="97%" border="0">');
    if ($O=='I' || $O=='A') {
      $sql = new db_getCustomerData; $RS = $sql->getInstanceOf($dbms,$w_cliente);
      ShowHTML('      <tr><td align="center" bgcolor="#D0D0D0" style="border: 2px solid rgb(0,0,0);"><font size="2"><b><font color="#BC3131">ATENÇÃO</font>: o tamanho máximo aceito para o arquivo é de '.(f($RS,'upload_maximo')/1024).' KBytes</b>.</font></td>');
      ShowHTML('<INPUT type="hidden" name="w_upload_maximo" value="'.f($RS,'upload_maximo').'">');
    } 
    ShowHTML('      <tr><td><b><u>T</u>ítulo:</b><br><input '.$w_Disabled.' accesskey="T" type="text" name="w_nome" class="STI" SIZE="75" MAXLENGTH="255" VALUE="'.$w_nome.'" title="Informe o tíulo do arquivo."></td>');
    ShowHTML('      <tr><td><b><u>D</u>escrição:</b><br><textarea '.$w_Disabled.' accesskey="D" name="w_descricao" class="STI" ROWS=5 cols=65 title="Descreva o conteúdo do arquivo.">'.$w_descricao.'</TEXTAREA></td>');
    ShowHTML('      <tr><td><b><u>O</u>rdem:</b><br><input '.$w_Disabled.' accesskey="O" type="text" name="w_ordem" class="STI" SIZE="4" MAXLENGTH="18" VALUE="'.$w_ordem.'" title="Informe a ordem de carga dos scripts."></td>');
    ShowHTML('      <tr><td><b>A<u>r</u>quivo:</b><br><input '.$w_Disabled.' accesskey="R" type="file" name="w_caminho" class="STI" SIZE="80" MAXLENGTH="100" VALUE="" title="OBRIGATÓRIO. Clique no botão ao lado para localizar o arquivo. Ele será transferido automaticamente para o servidor.">');
    if ($w_caminho>'') ShowHTML('              <b>'.LinkArquivo('SS',$w_cliente,$w_caminho,'_blank','Clique para exibir o arquivo atual.','Exibir',null).'</b>');
    ShowHTML('      <tr><td align="center"><hr>');
    if ($O=='E') {
      ShowHTML('   <input class="STB" type="submit" name="Botao" value="Excluir" onClick="return confirm(\'Confirma a exclusão do registro?\');">');
    } else {
      if ($O=='I') ShowHTML('            <input class="STB" type="submit" name="Botao" value="Incluir">');
      else         ShowHTML('            <input class="STB" type="submit" name="Botao" value="Atualizar">');
    } 
    ShowHTML('            <input class="STB" type="button" onClick="location.href=\''.montaURL_JS($w_dir,$w_pagina.$par.'&w_sq_esquema='.$w_sq_esquema.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'&O=L').'\';" name="Botao" value="Cancelar">');
    ShowHTML('          </td>');
    ShowHTML('      </tr>');
    ShowHTML('    </table>');
    ShowHTML('    </TD>');
    ShowHTML('</tr>');
    ShowHTML('</FORM>');
  } else {
    ScriptOpen('JavaScript');
    ShowHTML(' alert(\'Opção não disponível\');');
    ShowHTML(' history.back(1);');
    ScriptClose();
  } 
  ShowHTML('</table>');
  ShowHTML('</center>');
  Rodape();
} 

// =========================================================================
// Exibe orientações sobre o processo de importação
// -------------------------------------------------------------------------
function Help() {
  extract($GLOBALS);
  Cabecalho();
  if ($P1==1) {
    $l_infinitivo  = 'Importar';
    $l_gerundio    = 'importando';
    $l_substantivo = 'importação';
  } else {
    $l_infinitivo = 'Exportar';
    $l_gerundio   = 'exportando';
    $l_substantivo = 'exportação';
  }
  ShowHTML('<BASE HREF="'.$conRootSIW.'">');
  BodyOpen(null);
  ShowHTML('<B><FONT COLOR="#000000">'.$w_TP.'</FONT></B>');
  ShowHTML('<HR>');
  ShowHTML('<div align=center><center>');
  ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="90%">');
  ShowHTML('<tr valign="top">');
  ShowHTML('  <td><font size=2>');
  ShowHTML('    <p align="justify">Esta tela tem o objetivo de orientar o usuário na criação, manutenção e execução de esquemas de '.$l_substantivo);
  ShowHTML('        a partir de arquivos texto ou XML, residentes no servidor ou obtidos por Web Service ou FTP.');
  ShowHTML('    <p align="justify"><b>REQUISITOS:</b><br></p>');
  ShowHTML('    <ul>');
  ShowHTML('      <li>Se algum esquema de '.$l_substantivo.' usar transferência de arquivos por FTP, é necessário que: (a) sejam levantados e informados ');
  ShowHTML('           os dados do servidor FTP (endereço, nome de usuário e senha de acesso) e (b) o php seja compilado com a opção <i>-with--ftp</i>. ');
  ShowHTML('           O atendimento deste último requisito só pode ser feito pela equipe técnica da área de TI.');
  if ($P1==1) {
    ShowHTML('      <li>Se o esquema usar aquivos texto para importar dados, é absolutamente necessário que a primeira linha do arquivo indique o nome das colunas.');
    ShowHTML('          A ROTINA DE IMPORTAÇAO IRÁ SEMPRE DESPREZAR A PRIMEIRA LINHA DO ARQUIVO.');
  } else {
    ShowHTML('      <li>A exportação de dados para aquivos texto sempre colocará o nome das colunas na primeira linha do arquivo.');
  }
  ShowHTML('    </ul>');
  ShowHTML('    <p align="justify">O processo relativo a um esquema de '.$l_substantivo.' consiste nas seguintes fases e passos:');
  ShowHTML('    <ol>');
  ShowHTML('    <p align="justify"><b>FASE 1 - Preparando o sistema para utilização de esquemas de '.$l_substantivo.':</b><br></p>');
  ShowHTML('      <li>Para a correta criação de um esquema, é necessário executar a opção de dicionarização automática no owner dos objetos de destino.');
  ShowHTML('          Essa ação só pode ser executada por algum gestor de segurança da SIW. Para saber se o owner dos objetos de destino já foi ');
  ShowHTML('          dicionarizado, verifique se ele aparece na caixa de seleção "Usuário", exibida na inclusão de tabelas do esquema (antes é necessário indicar o sistema).');
  ShowHTML('    <p align="justify"><b>FASE 2 - Criando ou mantendo o esquema de '.$l_substantivo.':</b><br></p>');
  ShowHTML('      <li>Na tela principal de Esquemas, utilize a operaçao <i>Incluir</i> para inserir os dados gerais de um novo esquema.');
  ShowHTML('          Alguns campos, quando informados ou alterados, fazem com que a página seja recarregada para exibir um conjunto adicional de dados.');
  ShowHTML('      <li>Para alterar os dados gerais de um esquema existente, use a operação <i>AL</i>, na tela principal de Esquemas.');
  ShowHTML('      <li>Para excluir um esquema existente, use a operação <i>EX</i>, na tela principal de Esquemas. Se você já executou o esquema alguma vez, ');
  ShowHTML('          nao será possível excluí-lo. Ao invés, use a opção <i>AL</i> e coloque o campo "Ativo" igual a "Não".');
  ShowHTML('      <li> Um esquema de '.$l_substantivo.' precisa de diversos outros dados, além dos que a tela de inclusão ou alteração solicitam. Para ');
  ShowHTML('          complementar os dados, use as operações <i>Tabelas</i> e <i>Scripts</i> para complementar esses dados.');
  ShowHTML('      <li> TABELAS: use as operações exibidas na tela para relacionar as tabelas que deseja '.$l_infinitivo.'.');
  ShowHTML('          <ul>');
  ShowHTML('          <li> Use a operação <i>Mapear</i> para vincular as tabelas do banco de destino com o arquivo de origem de dados.');
  ShowHTML('          <li> Use a operação <i>Registros</i> caso precise inserir algum registro na tabela antes de carregar o arquivo de origem de dados.');
  ShowHTML('          </ul>');
  ShowHTML('      <li> SCRIPTS: se necessário, você pode indicar os scripts que deseja executar durante o processo de '.$l_substantivo.'. É importante ');
  ShowHTML('          saber que os scripts são executados sempre APÓS a '.$l_substantivo.' das tabelas. Cada script tem um número de ordem que define ');
  ShowHTML('          a seqüência de execução, isto é, o script com menor número de ordem será executado primeiro, e assim por diante. ');
  ShowHTML('    <p align="justify"><b>FASE 3 - Gerando um arquivo de configuração atualizado:</b><br></p>');
  ShowHTML('      <li>Na tela principal dos esquemas, clique sobre a operação <i>Gerar configuração</i> e confirme a sua geração. É importante executar essa operaçao ');
  ShowHTML('          sempre que for feita alguma alteração em qualquer dado do esquema, para garantir o correto funcionamento do processo de '.$l_substantivo.'.');
  ShowHTML('    <p align="justify"><b>FASE 4 - Executando um esquema de importação:</b><br></p>');
  ShowHTML('      <li>Após atualizar o arquivo de configuração é possível, a qualquer momento, executar uma nova '.$l_importacao.' do esquema.');
  ShowHTML('          Para tanto, basta clicar sobre a operação <i>'.$l_infinitivo.'</i>, confirmar a execução e aguardar o término do processamento.');
  ShowHTML('    <p align="justify"><b>FASE 5 - Verificação dos arquivos de processamento e de registro:</b><br></p>');
  ShowHTML('      <li>Cada esquema tem três colunas: ');
  ShowHTML('          "Tot." indica o número total de linhas processadas, "Ac." indica o número de linhas que atendeu às condições de '.$l_substantivo);
  ShowHTML('          e "Rej." indica o número de linhas que foram descartadas pela validação. Clique sobre os números que forem exibidos na cor azul para  ');
  ShowHTML('          verificar os arquivos de processamento e de registro.');
  ShowHTML('      <li>Se o esquema indicar o envio de e-mail ao final do processamento, as pessoas que fizerem parte da lista de distribuição também ');
  ShowHTML('          serão comunicadas da execução e do resultado do processamento, inclusive com os arquivos de processamento e/ou de registro. ');
  ShowHTML('    </ol>');
  ShowHTML('    <p align="justify"><b>Observações:</b><br></p>');
  ShowHTML('    <ul>');
  ShowHTML('      <li>Toda importação registra os dados de quem a executou, de quando ela foi executada, bem como os arquivos de processamento e de registro; ');
  if ($P1==1) {
    ShowHTML('      <li>Não há como cancelar uma importação, nem de reverter os valores existentes antes da sua execução. Assim, verifique se o ');
    ShowHTML('          esquema foi corretamente configurado, se o arquivo de configuração foi gerado e se a importação deve realmente ser executada.');
  }
  ShowHTML('    </ul>');
  ShowHTML('  </td>');
  ShowHTML('</tr>');
  ShowHTML('</table>');
  ShowHTML('</center>');
  Rodape();
} 

// =========================================================================
// Procedimento que executa as operações de BD
// -------------------------------------------------------------------------
function Grava() {
  extract($GLOBALS);
  Cabecalho();
  ShowHTML('</HEAD>');
  ShowHTML('<BASE HREF="'.$conRootSIW.'">');  
  BodyOpenClean(null);
  switch ($SG) {
    case 'TIMPORT':
      // Verifica se a Assinatura Eletrônica é válida
      if (verificaAssinaturaEletronica($_SESSION['USERNAME'],upper($_REQUEST['w_assinatura'])) || $w_assinatura=='') {
        $SQL = new dml_putEsquema; $SQL->getInstanceOf($dbms,$O,$w_cliente,$_REQUEST['w_sq_esquema'],$_REQUEST['w_sq_modulo'],$_REQUEST['w_nome'],$_REQUEST['w_descricao'],$_REQUEST['w_tipo'],
                $_REQUEST['w_ativo'],$_REQUEST['w_formato'],$_REQUEST['w_ws_servidor'],$_REQUEST['w_ws_url'],
                $_REQUEST['w_ws_acao'],$_REQUEST['w_ws_mensagem'],$_REQUEST['w_no_raiz'],$_REQUEST['w_bd_hostname'],$_REQUEST['w_bd_username'],
                $_REQUEST['w_bd_password'],$_REQUEST['w_tx_delimitador'],$_REQUEST['w_tipo_efetivacao'],$_REQUEST['w_tx_origem_arquivos'],$_REQUEST['w_ftp_hostname'],$_REQUEST['w_ftp_username'],
                $_REQUEST['w_ftp_password'],$_REQUEST['w_ftp_diretorio'],$_REQUEST['w_envia_mail'],$_REQUEST['w_lista_mail']);
        ScriptOpen('JavaScript');
        ShowHTML('  location.href=\''.montaURL_JS($w_dir,$R.'&O=L&w_sq_esquema='.$_REQUEST['w_sq_esquema'].'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET')).'\';');
        ScriptClose();
      } else {
        ScriptOpen('JavaScript');
        ShowHTML('  alert(\'Assinatura Eletrônica inválida!\');');
        ShowHTML('  history.back(1);');
        ScriptClose();
      }
    break;
    case 'ISSIGTAB':
      // Verifica se a Assinatura Eletrônica é válida
      if (verificaAssinaturaEletronica($_SESSION['USERNAME'],upper($_REQUEST['w_assinatura'])) || $w_assinatura=='') {
        $SQL = new dml_putEsquemaTabela; 
        $SQL1 = new dml_putEsquemaAtributo; 
        if ($O=='I') {
          for ($i=0; $i<=count($_POST['w_sq_tabela'])-1; $i=$i+1) {
            if ($_REQUEST['w_sq_tabela'][$i]>'') {
              $SQL->getInstanceOf($dbms,$O,null,$_REQUEST['w_sq_esquema'],$_REQUEST['w_sq_tabela'][$i],$_REQUEST['w_ordem'][$i],$_REQUEST['w_elemento'][$i],Nvl($_REQUEST['w_remove_registro'][$i],'N'));
            }
          } 
        } elseif ($O=='A') {
          $SQL->getInstanceOf($dbms,$O,$_REQUEST['w_sq_esquema_tabela'],$_REQUEST['w_sq_esquema'],null,$_REQUEST['w_ordem'],$_REQUEST['w_elemento'],Nvl($_REQUEST['w_remove_registro'],'N'));
        } elseif ($O=='E') {
          $SQL1->getInstanceOf($dbms,'E',null,$_REQUEST['w_sq_esquema_tabela'],null,null,null,null,null);
          $SQL->getInstanceOf($dbms,$O,$_REQUEST['w_sq_esquema_tabela'],null,null,null,null,null);
        } 
        ScriptOpen('JavaScript');
        ShowHTML('  location.href=\''.montaURL_JS($w_dir,$R.'&O=L&w_sq_esquema='.$_REQUEST['w_sq_esquema'].'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'&w_menu='.$w_menu.MontaFiltro('GET')).'\';');
        ScriptClose();
      } else {
        ScriptOpen('JavaScript');
        ShowHTML('  alert(\'Assinatura Eletrônica inválida!\');');
        ShowHTML('  history.back(1);');
        ScriptClose();
      } 
    break;
    case 'ISSIGMAP':
      // Verifica se a Assinatura Eletrônica é válida
      if (verificaAssinaturaEletronica($_SESSION['USERNAME'],upper($_REQUEST['w_assinatura'])) || $w_assinatura=='') {
        $SQL = new dml_putEsquemaAtributo; 
        $SQL->getInstanceOf($dbms,'E',null,$_REQUEST['w_sq_esquema_tabela'],null,null,null,null,null);
        if ($O=='I') {
          for ($i=0; $i<=count($_POST['w_sq_coluna'])-1; $i=$i+1) {
            if ($_REQUEST['w_sq_coluna'][$i]>'') {
              if (upper($_REQUEST['w_tipo_coluna'][$i])=='DATE') $w_valor_mascara = $_REQUEST['w_mascara_data'][$i]; else $w_valor_mascara = '';
              $SQL->getInstanceOf($dbms,$O,null,$_REQUEST['w_sq_esquema_tabela'],$_REQUEST['w_sq_coluna'][$i],$_REQUEST['w_ordem'][$i],$_REQUEST['w_campo_externo'][$i],$w_valor_mascara,$_REQUEST['w_valor_default'][$i]);
            }
          }
        } 
        ScriptOpen('JavaScript');
        ShowHTML('  location.href=\''.montaURL_JS($w_dir,$w_pagina.'Tabela'.'&O=L&w_sq_esquema='.$_REQUEST['w_sq_esquema'].'&w_sq_esquema_tabela='.$_REQUEST['w_sq_esquema_tabela'].'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.'ISSIGTAB'.MontaFiltro('GET')).'\';');
        ScriptClose();
      } else {
        ScriptOpen('JavaScript');
        ShowHTML('  alert(\'Assinatura Eletrônica inválida!\');');
        ShowHTML('  history.back(1);');
        ScriptClose();
      } 
    break;
    case 'TIREGISTRO':
      // Verifica se a Assinatura Eletrônica é válida
      if (verificaAssinaturaEletronica($_SESSION['USERNAME'],upper($_REQUEST['w_assinatura'])) || $w_assinatura=='') {
        $SQL = new dml_putEsquemaInsert; 
        if ($O=='I' || $O=='A') {
          for ($i=0; $i<=count($_POST['w_sq_coluna'])-1; $i=$i+1) {
              $SQL->getInstanceOf($dbms,$O,$_REQUEST['w_sq_esquema_insert'][$i],$_REQUEST['w_sq_esquema_tabela'],$_REQUEST['w_sq_coluna'][$i],$_REQUEST['w_ordem'][$i],$_REQUEST['w_valor'][$i],null);
            } 
        } elseif ($O=='E') {
          $SQL->getInstanceOf($dbms,$O,null,$_REQUEST['w_sq_esquema_tabela'],null,null,null,$_REQUEST['w_registro']);
        } 
        ScriptOpen('JavaScript');
        ShowHTML('  location.href=\''.montaURL_JS($w_dir,$R.'&O=L&w_sq_esquema='.$_REQUEST['w_sq_esquema'].'&w_sq_esquema_tabela='.$_REQUEST['w_sq_esquema_tabela'].'&w_sq_tabela='.$_REQUEST['w_sq_tabela'].'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'&w_menu='.$w_menu.MontaFiltro('GET')).'\';');
        ScriptClose();
      } else {
        ScriptOpen('JavaScript');
        ShowHTML('  alert(\'Assinatura Eletrônica inválida!\');');
        ShowHTML('  history.back(1);');
        ScriptClose();
      }
   break;  
   case 'TISCRIPT':
    // Verifica se a Assinatura Eletrônica é válida
    if (verificaAssinaturaEletronica($_SESSION['USERNAME'],upper($_REQUEST['w_assinatura'])) || $w_assinatura=='') {
      if (UPLOAD_ERR_OK==0) {
        $w_maximo = $_REQUEST['w_upload_maximo'];
        foreach ($_FILES as $Chv => $Field) {
          if (!($Field['error']==UPLOAD_ERR_OK || $Field['error']==UPLOAD_ERR_NO_FILE)) {
            // Verifica se o tamanho das fotos está compatível com  o limite de 100KB. 
            ScriptOpen('JavaScript');
            ShowHTML('  alert(\'Atenção: o tamanho máximo do arquivo não pode exceder '.($w_maximo/1024).' KBytes!\');');
            ShowHTML('  history.go(-1);');
            ScriptClose();
            exit();
          }
          if ($Field['size'] > 0) {
            // Verifica se o tamanho das fotos está compatível com  o limite de 100KB. 
            if ($Field['size'] > $w_maximo) {
              ScriptOpen('JavaScript');
              ShowHTML('  alert(\'Atenção: o tamanho máximo do arquivo não pode exceder '.($w_maximo/1024).' KBytes!\');');
              ShowHTML('  history.back(1);');
              ScriptClose();
              exit();
            } 
            // Se já há um nome para o arquivo, mantém 
            if ($_REQUEST['w_atual']>'') {
              $sql = new db_getEsquemaScript; $RS = $sql->getInstanceOf($dbms,$_REQUEST['w_sq_esquema'],$_REQUEST['w_sq_arquivo'],$w_cliente);
              foreach ($RS as $row) {
                if (file_exists($conFilePhysical.$w_cliente.'/'.f($row,'caminho'))) unlink($conFilePhysical.$w_cliente.'/'.f($row,'caminho'));
                if (!(strpos(f($row,'caminho'),'.')===false)) {
                  $w_file = substr(basename(f($row,'caminho')),0,(strpos(basename(f($row,'caminho')),'.') ? strpos(basename(f($row,'caminho')),'.')+1 : 0)-1).substr($Field['name'],(strrpos($Field['name'],'.') ? strrpos($Field['name'],'.')+1 : 0)-1,30);
                } else {
                  $w_file = basename(f($row,'caminho'));
                }
              }
            } else {
              $w_file = str_replace('.tmp','',basename($Field['tmp_name']));
              if (!(strpos($Field['name'],'.')===false)) {
                $w_file = $w_file.substr($Field['name'],(strrpos($Field['name'],'.') ? strrpos($Field['name'],'.')+1 : 0)-1,10);
              }
            }
            $w_tamanho = $Field['size'];
            $w_tipo    = $Field['type'];
            $w_nome    = $Field['name'];
            if ($w_file>'') move_uploaded_file($Field['tmp_name'],DiretorioCliente($w_cliente).'/'.$w_file);
          } 
        } 
        // Se for exclusão e houver um arquivo físico, deve remover o arquivo do disco.  
        if ($O=='E' && $_REQUEST['w_atual']>'') {
          $sql = new db_getEsquemaScript; $RS = $sql->getInstanceOf($dbms,$_REQUEST['w_sq_esquema'],$_REQUEST['w_sq_arquivo'],$w_cliente);
          foreach ($RS as $row) {
            if (file_exists($conFilePhysical.$w_cliente.'/'.f($row,'caminho'))) unlink($conFilePhysical.$w_cliente.'/'.f($row,'caminho'));
          }
        } 
        $SQL = new dml_putEsquemaScript; $SQL->getInstanceOf($dbms,$O,
          $w_cliente,$_REQUEST['w_sq_esquema_script'],$_REQUEST['w_sq_arquivo'],$_REQUEST['w_sq_esquema'],$_REQUEST['w_nome'],$_REQUEST['w_descricao'],
          $w_file,$w_tamanho,$w_tipo,$w_nome,$_REQUEST['w_ordem']);
      } else {
        ScriptOpen('JavaScript');
        ShowHTML('  alert(\'ATENÇÃO: ocorreu um erro na transferência do arquivo. Tente novamente!\');');
        ScriptClose();
        exit();
      } 
      ScriptOpen('JavaScript');
     // Recupera a sigla do serviço pai, para fazer a chamada ao menu 
      $sql = new db_getLinkData; $RS = $sql->getInstanceOf($dbms,$w_cliente,$SG);
      ShowHTML('  location.href=\''.montaURL_JS($w_dir,$R.'&O=L&w_sq_esquema='.$_REQUEST['w_sq_esquema'].'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG).'\';');
      ScriptClose();
    } else {
      ScriptOpen('JavaScript');
      ShowHTML('  alert(\'Assinatura Eletrônica inválida!\');');
      ShowHTML('  history.back(1);');
      ScriptClose();
    }
   break;
   case 'IMPTXT':
     // Verifica se a Assinatura Eletrônica é válida
     if (verificaAssinaturaEletronica($_SESSION['USERNAME'],upper($_REQUEST['w_assinatura'])) || $w_assinatura=='') {

       //$handle = popen($_REQUEST['w_comando'], 'r');
       //pclose($handle);
       $w_sq_esquema = $_REQUEST['w_sq_esquema'];
       $w_nm_esquema = $_REQUEST['w_nm_esquema'];

       Cabecalho();
       ShowHTML('<BASE HREF="'.$conRootSIW.'">');
       BodyOpenClean('onLoad=this.focus();');
       ShowHTML('<B><FONT COLOR="#000000">'.$w_TP.'</FONT></B>');
       ShowHTML('<HR>');
       flush();
       ShowHTML('<div align=center><center><br><br><br><br><br><br><br><br><br><br><img src="images/relogio.gif" align="center"> <b>Aguarde: processamento do esquema '.$w_nm_esquema.' em andamento...</b><br><br><br><br><br><br><br><br><br><br></center></div>');
       Rodape();
       flush();

       include_once($_REQUEST['w_arquivo']);

       ScriptOpen('JavaScript');
       ShowHTML('  alert(\'Importação executada com sucesso!\');');

      // Recupera a sigla do serviço pai, para fazer a chamada ao menu 
       $sql = new db_getLinkData; $RS1 = $sql->getInstanceOf($dbms,$w_cliente,'TIMPORT');
       ShowHTML('  location.href=\''.montaURL_JS($w_dir,f($RS1,'link').'&O=L&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.f($RS1,'sigla').MontaFiltro('GET')).'\';');
       ScriptClose();
     } else {
       ScriptOpen('JavaScript');
       ShowHTML('  alert(\'Assinatura Eletrônica inválida!\');');
       ShowHTML('  history.back(1);');
       ScriptClose();
     }
   break;
   case 'GERAARQ':
      // Recupera informações dos esquemas ativos
      $sql = new db_getEsquema; $RS_Esquema = $sql->getInstanceOf($dbms,$w_cliente,null,$w_esquema,null,null,null,null,null,null,null,null);
      $RS_Esquema = SortArray($RS_Esquema,'nome','asc');

      $w_raiz_conf = $conFilePhysical.$w_cliente.'/etl_conf/';
      $w_raiz_arq  = $conFilePhysical.$w_cliente.'/etl_arquivos/';
      
      // Se os diretórios necessários não existem, tenta criá-los.
      if (!is_dir($w_raiz_conf)) mkdir($w_raiz_conf);
      if (!is_dir($w_raiz_arq))  mkdir($w_raiz_arq);

      // Remove os arquivos de configuração existentes
      if ($dir=@opendir($w_raiz_conf)) { 
        while (($element=readdir($dir))!== false) { 
          if ($element!= '.' && $element!= '..') { 
            unlink($w_raiz_conf.$element);
          } 
        } 
        closedir($dir); 
      } 
      
      // Varre cada um dos esquemas e gera arquivo de script com todos eles
      foreach($RS_Esquema as $row_esquema) {
        if (f($row_esquema,'ativo')=='S') {
          $l_arquivo =  '<?php'.$crlf;
          $l_arquivo .= '/**'.$crlf;
          $l_arquivo .= '* { Description :- '.$crlf;
          $l_arquivo .= '*    '.f($row_esquema,'descricao').$crlf;
          $l_arquivo .= '*/'.$crlf;
          $l_arquivo .= '$esq[\'chave\']        = '.f($row_esquema,'sq_esquema').';'.$crlf;
          $l_arquivo .= '$esq[\'nome\']         = \''.f($row_esquema,'nome').'\';'.$crlf;
          $l_arquivo .= '$esq[\'efetivacao\']   = '.f($row_esquema,'tipo_efetivacao').';'.$crlf;
          $l_arquivo .= '$esq[\'origem_arq\']   = '.f($row_esquema,'tx_origem_arquivos').';'.$crlf;
          $l_arquivo .= ''.$crlf;
          $l_arquivo .= '$mail[\'envia\']       = \''.f($row_esquema,'envia_mail').'\';'.$crlf;
          $l_arquivo .= '$mail[\'lista\']       = \''.f($row_esquema,'lista_mail').'\';'.$crlf;
          $l_arquivo .= ''.$crlf;
          $l_arquivo .= '$ftp[\'hostname\']     = \''.f($row_esquema,'ftp_hostname').'\';'.$crlf;
          $l_arquivo .= '$ftp[\'username\']     = \''.f($row_esquema,'ftp_username').'\';'.$crlf;
          $l_arquivo .= '$ftp[\'password\']     = \''.f($row_esquema,'ftp_password').'\';'.$crlf;
          $l_arquivo .= '$ftp[\'diretorio\']    = \''.f($row_esquema,'ftp_diretorio').'\';'.$crlf;
          $l_arquivo .= ''.$crlf;
          $l_arquivo .= '$bd[\'dbms\']          = '.$_SESSION['DBMS'].';'.$crlf;
          $l_arquivo .= '$bd[\'cliente\']       = '.f($row_esquema,'cliente').';'.$crlf;
          $l_arquivo .= '$bd[\'hostname\']      = \''.f($row_esquema,'bd_hostname').'\';'.$crlf;
          $l_arquivo .= '$bd[\'username\']      = \''.f($row_esquema,'bd_username').'\';'.$crlf;
          $l_arquivo .= '$bd[\'password\']      = \''.f($row_esquema,'bd_password').'\';'.$crlf;
          $l_arquivo .= ''.$crlf;
          $l_arquivo .= '$delimitador           = \''.f($row_esquema,'tx_delimitador').'\';'.$crlf;
          $l_arquivo .= ''.$crlf;
          $i = 0;
          // Recupera informações dos esquemas ativos
          $sql = new db_getEsquemaTabela; $RS_Tabela = $sql->getInstanceOf($dbms,null,f($row_esquema,'sq_esquema'),null);
          $RS_Tabela = SortArray($RS_Tabela,'ordem','asc','nm_tabela','asc','or_coluna','asc');
          $w_atual = '';
          foreach($RS_Tabela as $row_tabela) {
            if ($i==0) {
              $l_arquivo .= '$tabelas = array('.$crlf;
              $i = 1;
            }
            if ($w_atual=='' || $w_atual <> f($row_tabela,'sq_esquema_tabela')) {
              if ($w_atual=='') {
                $l_arquivo .= '  \''.f($row_tabela,'nm_tabela').'\' =>'.$crlf;
              } else {
                $l_arquivo .= '                                   )'.$crlf;
                $sql = new db_getEsquemaInsert; $RS_Insert = $sql->getInstanceOf($dbms,null,null,$w_atual,null,null);
                $RS_Insert = SortArray($RS_Insert,'registro','asc','or_coluna','asc');
                $j = 0;
                $w_reg_atual = '';
                foreach($RS_Insert as $row_insert) {
                  if ($j==0) {
                    $l_arquivo .= '            ,\'insert\'      => array(array(\''.str_pad(f($row_insert,'cl_nome').'\'',44).'=> array('.f($row_insert,'nm_tipo').', \''.f($row_insert,'valor').'\')'.$crlf;
                    $j = 1;
                  } else {
                    if ($w_reg_atual!=f($row_insert,'registro')) {
                      $l_arquivo .= '                                         )'.$crlf;
                      $l_arquivo .= '                                   ,array(\''.str_pad(f($row_insert,'cl_nome').'\'',44).'=> array('.str_pad(f($row_insert,'nm_tipo').',',15).' \''.f($row_insert,'valor').'\')'.$crlf;
                    } else {
                      $l_arquivo .= '                                          ,\''.str_pad(f($row_insert,'cl_nome').'\'',43).'=> array('.str_pad(f($row_insert,'nm_tipo').',',15).' \''.f($row_insert,'valor').'\')'.$crlf;
                    }
                  }
                  $w_reg_atual = f($row_insert,'registro');
                }
                if ($j > 0) {
                  $l_arquivo .= '                                         )'.$crlf;
                  $l_arquivo .= '                                   )'.$crlf;
                }
                $l_arquivo .= '           )'.$crlf;
                $l_arquivo .= '  ,\''.f($row_tabela,'nm_tabela').'\' =>'.$crlf;
              }
              $l_arquivo .= '      array(\'localizacao\' => \''.f($row_tabela,'elemento').'\','.$crlf;
              $l_arquivo .= '            \'remove_registro\' => \''.f($row_tabela,'remove_registro').'\''.$crlf;
              $w_prim  = true;
            }
            if (f($row_tabela,'nm_tipo')=='B_DATE') {
              $l_tamanho = '\''.f($row_tabela,'mascara_data').'\'';
            } else {
              $l_tamanho = f($row_tabela,'cl_tamanho');
            }
            if (nvl(f($row_tabela,'valor_default'),'')!='') {
              $l_default = ', \''.f($row_tabela,'valor_default').'\'';
            } else {
              $l_default = null;
            }
            if ($w_prim) {
              $l_arquivo .= '            ,\'mapeamento\'  => array(\''.str_pad(f($row_tabela,'cl_nome').'\'',50).'=> array('.str_pad(f($row_tabela,'nm_tipo').',',15).str_pad(f($row_tabela,'or_coluna').',',5).$l_tamanho.$l_default.')'.$crlf;
              $w_prim = false;
            } else {
              $l_arquivo .= '                                    ,\''.str_pad(f($row_tabela,'cl_nome').'\'',49).'=> array('.str_pad(f($row_tabela,'nm_tipo').',',15).str_pad(f($row_tabela,'or_coluna').',',5).$l_tamanho.$l_default.')'.$crlf;
            }
            $w_atual = f($row_tabela,'sq_esquema_tabela');
          }
          if ($i > 0) {
            $l_arquivo .= '                                   )'.$crlf;
            $sql = new db_getEsquemaInsert; $RS_Insert = $sql->getInstanceOf($dbms,null,null,$w_atual,null,null);
            $RS_Insert = SortArray($RS_Insert,'registro','asc','or_coluna','asc');
            $j = 0;
            $w_reg_atual = '';
            foreach($RS_Insert as $row_insert) {
              if ($j==0) {
                $l_arquivo .= '            ,\'insert\'      => array(array(\''.str_pad(f($row_insert,'cl_nome').'\'',44).'=> array('.str_pad(f($row_insert,'nm_tipo').',',15).' \''.f($row_insert,'valor').'\')'.$crlf;
                $j = 1;
              } else {
                if ($w_reg_atual!=f($row_insert,'registro')) {
                  $l_arquivo .= '                                         )'.$crlf;
                  $l_arquivo .= '                                   ,array(\''.str_pad(f($row_insert,'cl_nome').'\'',44).'=> array('.str_pad(f($row_insert,'nm_tipo').',',15).' \''.f($row_insert,'valor').'\')'.$crlf;
                } else {
                  $l_arquivo .= '                                          ,\''.str_pad(f($row_insert,'cl_nome').'\'',43).'=> array('.str_pad(f($row_insert,'nm_tipo').',',15).' \''.f($row_insert,'valor').'\')'.$crlf;
                }
              }
              $w_reg_atual = f($row_insert,'registro');
            }
            if ($j > 0) {
              $l_arquivo .= '                                         )'.$crlf;
              $l_arquivo .= '                                   )'.$crlf;
            }
            $l_arquivo .= '           )'.$crlf;
            $l_arquivo .= ');'.$crlf;
          }
          $i = 0;
          $sql = new db_getEsquemaScript; $RS_Script = $sql->getInstanceOf($dbms,f($row_esquema,'sq_esquema'),null,$w_cliente);
          $RS_Script = SortArray($RS_Script,'ordem','asc');
          foreach($RS_Script as $row_script) {
            if ($i==0) {
              $l_arquivo .= '$scripts = array('.$crlf;
              $l_arquivo .= '  \''.f($row_script,'caminho').'\''.$crlf;
              $i = 1;
            } else {
              $l_arquivo .= '  ,\''.f($row_script,'caminho').'\''.$crlf;
            }
          }
          if ($i>0) {
            $l_arquivo .= ');'.$crlf;
          }
          $l_arquivo .= '?>'.$crlf;
        
          // Define o nome do arquivo que contém a configuração do esquema
          $w_arq_conf  = $w_raiz_conf.lower(f($row_esquema,'nome')).'.php';

          // Se o diretório de dados do esquema não existir, tenta criá-lo.
          if (!is_dir($w_raiz_arq.lower(f($row_esquema,'nome'))))  mkdir($w_raiz_arq.lower(f($row_esquema,'nome')));
        
          // Grava o arquivo de configuração para o esquema
          if (!$handle = fopen($w_arq_conf,'w')) {
            ScriptOpen('JavaScript');
            ShowHTML('  alert(\'ATENÇÃO: não foi possível abrir o arquivo para escrita.\\n'.$w_arq_conf.'\');');
            ScriptClose();
            exit;
          } else {
            // Insere o conteúdo no arquivo
            if (!fwrite($handle, $l_arquivo)) {
              ScriptOpen('JavaScript');
              ShowHTML('  alert(\'ATENÇÃO: não foi possível inserir o conteúdo do arquivo.\\n'.$w_arq_conf.'\');');
              ScriptClose();
              fclose($handle);
              exit;
            } else {
              fclose($handle);
            }
          }
        }
      }
      ScriptOpen('JavaScript');
      ShowHTML('  alert(\'Arquivos de configuração gerados com sucesso!\');');
      ShowHTML('  window.close();');
      ScriptClose();
      break;
    default:
      ScriptOpen('JavaScript');
      ShowHTML('  alert(\'Bloco de dados não encontrado: '.$SG.'\');');
      ShowHTML('  history.back(1);');
      ScriptClose();
    break;
  } 
} 
// =========================================================================
// Rotina principal
// -------------------------------------------------------------------------
function Main() {
  extract($GLOBALS);
  switch ($par) {
    case 'INICIAL':     Inicial();      break;
    case 'GRAVA':       Grava();        break;
    case 'TABELA':      Tabela();       break;
    case 'MAPEAMENTO':  Mapeamento();   break;
    case 'IMPORTACAO':  Importacao();   break;
    case 'REGISTRO':    Registro();     break;
    case 'SCRIPT':      Script();       break;    
    case 'HELP':        Help();         break;    
    default:
      cabecalho();
      ShowHTML('<BASE HREF="'.$conRootSIW.'">');      
      BodyOpen('onLoad=this.focus();');
      Estrutura_Topo_Limpo();
      Estrutura_Menu();
      Estrutura_Corpo_Abre();
      Estrutura_Texto_Abre();
      ShowHTML('<div align=center><center><br><br><br><br><br><br><br><br><br><br><img src="images/icone/underc.gif" align="center"> <b>Esta opção está sendo desenvolvida.</b><br><br><br><br><br><br><br><br><br><br></center></div>');
      Estrutura_Texto_Fecha();
      Estrutura_Fecha();
      Estrutura_Fecha();
      Estrutura_Fecha();
      Rodape();
  } 
}
?>