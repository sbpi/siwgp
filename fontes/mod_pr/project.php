<?php
header('Expires: '.-1500);
session_start();
$w_dir_volta = '../';
include_once($w_dir_volta.'constants.inc');
include_once($w_dir_volta.'jscript.php');
include_once($w_dir_volta.'funcoes.php');
include_once($w_dir_volta.'classes/db/abreSessao.php');
include_once($w_dir_volta.'classes/sp/db_getLinkData.php');
include_once($w_dir_volta.'classes/sp/db_getMenuData.php');
include_once($w_dir_volta.'classes/sp/db_getMenuCode.php');
include_once($w_dir_volta.'classes/sp/db_getCustomerData.php');
include_once($w_dir_volta.'classes/sp/db_verificaAssinatura.php');
include_once($w_dir_volta.'classes/sp/db_getSolicData.php');
include_once($w_dir_volta.'classes/sp/db_getPersonData.php');
include_once($w_dir_volta.'classes/sp/dml_putEtapaProject.php');
include_once($w_dir_volta.'funcoes/selecaoPessoa.php');
include_once($w_dir_volta.'funcoes/selecaoUnidade.php');
include_once($w_dir_volta.'funcoes/selecaoBaseGeografica.php');
include_once($w_dir_volta.'funcoes/selecaoPais.php');
include_once($w_dir_volta.'funcoes/selecaoRegiao.php');
include_once($w_dir_volta.'funcoes/selecaoEstado.php');
include_once($w_dir_volta.'funcoes/selecaoCidade.php');

// =========================================================================
//  /project.php
// ------------------------------------------------------------------------
// Nome     : Alexandre Vinhadelli Papadópolis
// Descricao: Importar estrutura analítica de um arquivo MS-Project
// Mail     : alexvp@sbpi.com.br
// Criacao  : 29/05/2008, 11:43
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

$p_projeto    = $_REQUEST['p_projeto'];
$w_assinatura = upper($_REQUEST['w_assinatura']);
$w_pagina     = 'project.php?par=';
$w_Disabled   = 'ENABLED';
$w_dir        = 'mod_pr/';
$w_troca      = $_REQUEST['w_troca'];
$p_ordena     = $_REQUEST['p_ordena'];

// Verifica se o usuário está autenticado
if ($_SESSION['LOGON']!='Sim') { EncerraSessao(); }

// Declaração de variáveis
$dbms = new abreSessao; $dbms = $dbms->getInstanceOf($_SESSION['DBMS']);

if ($O=='') $O='L';

switch ($O) {
  case 'I': $w_TP=$TP.' - Inclusão';        break;
  case 'A': $w_TP=$TP.' - Alteração';       break;
  case 'E': $w_TP=$TP.' - Exclusão';        break;
  case 'P': $w_TP=$TP.' - Filtragem';       break;
  case 'C': $w_TP=$TP.' - Cópia';           break;
  case 'V': $w_TP=$TP.' - Envio';           break;
  case 'M': $w_TP=$TP.' - Serviços';        break;
  case 'H': $w_TP=$TP.' - Herança';         break;
  case 'T': $w_TP=$TP.' - Ativar';          break;
  case 'D': $w_TP=$TP.' - Desativar';       break;
  default:  $w_TP=$TP.' - Listagem';        break;
}

// Se receber o código do cliente do SIW, o cliente será determinado por parâmetro;
// caso contrário, o cliente será a empresa ao qual o usuário logado está vinculado.
$w_cliente  = RetornaCliente();
$w_usuario  = RetornaUsuario();
$w_menu     = RetornaMenu($w_cliente,$SG);
$w_ano      = RetornaAno();
Main();
FechaSessao($dbms);
exit;


// =========================================================================
// Rotina de importação das etapas
// -------------------------------------------------------------------------
function Etapa() {
  extract($GLOBALS);
  global $w_Disabled;

  $sql = new db_getSolicData; $RS = $sql->getInstanceOf($dbms,$p_projeto,'PJGERAL');
  $w_cabecalho  = f($RS,'titulo').' ('.$p_projeto.')';

  $w_sq_pessoa    = nvl($_REQUEST['w_sq_pessoa'],$w_usuario);
  $w_sq_unidade   = nvl($_REQUEST['w_sq_unidade'],$_SESSION['LOTACAO']);
  $w_base         = nvl($_REQUEST['w_base'],5);

  if ($w_troca>'' && $O!='E') {
    // Se for recarga da página
    $w_chave        = $_REQUEST['w_chave'];
    $w_nome         = $_REQUEST['w_nome'];
    $w_codigo       = $_REQUEST['w_codigo'];
    $w_ativo        = $_REQUEST['w_ativo'];
    $w_pais         = $_REQUEST['w_pais'];
    $w_regiao       = $_REQUEST['w_regiao'];
    $w_uf           = $_REQUEST['w_uf'];
    $w_cidade       = $_REQUEST['w_cidade'];
    $w_pais         = $_REQUEST['w_pais'];
    $w_regiao       = $_REQUEST['w_regiao'];
    $w_uf           = $_REQUEST['w_uf'];
    $w_cidade       = $_REQUEST['w_cidade'];
    $w_peso         = $_REQUEST['w_peso'];
  } elseif (!(strpos('AEV',$O)===false)) {
    // Recupera os dados do endereço informado
    $sql = new db_getTipoRestricao; $RS = $sql->getInstanceOf($dbms,$w_chave,$w_cliente,null,null,null,null);
    foreach ($RS as $row) {$RS = $row; break;}
    $w_chave    = f($RS,'chave');
    $w_cliente  = f($RS,'cliente');
    $w_nome     = f($RS,'nome');
    $w_codigo   = f($RS,'codigo_externo');
    $w_ativo    = f($RS,'ativo');
  } 
  Cabecalho();
  head();
  ShowHTML('<TITLE>'.$conSgSistema.' - Importação MS-Project</TITLE>');
  if (!(strpos('LIAEP',$O)===false)) {
    ScriptOpen('JavaScript');
    ValidateOpen('Validacao');
    if ($O=='L') {
      Validate('w_peso','Peso','1','1','1','2','','0123456789');
      CompValor('w_peso','Peso da etapa','>=',1,'1');
      Validate('w_sq_pessoa','Responsável','HIDDEN','1','1','10','','1');
      Validate('w_sq_unidade','Setor responsável','HIDDEN','1','1','10','','1');
      Validate('w_base','Base geográfica','SELECT','1','1','18','','1');
      if (nvl($w_base,5)!=5) {
        Validate('w_pais','País','SELECT','1','1','18','','1');
        if ($w_base==2) Validate('w_regiao','Região','SELECT','1','1','18','','1');
        if ($w_base==3 || $w_base==4) Validate('w_uf','Estado','SELECT','1','1','18','1','1');
        if ($w_base==4) Validate('w_cidade','Cidade','SELECT','1','1','18','','1');
      }
      Validate('w_caminho','Arquivo','','1','5','255','1','1');
      ShowHTML('    if (theForm.w_caminho.value.toUpperCase().lastIndexOf("XML")==-1) {');
      ShowHTML('       alert(\'Só é possível escolher arquivos com a extensão ".xml"!\');');
      ShowHTML('       theForm.w_caminho.focus();');
      ShowHTML('       return false;');
      ShowHTML('    }');
      ShowHTML('  theForm.Botao.disabled=true;');
    } elseif (!(strpos('IA',$O)===false)) {
      Validate('w_nome','Nome','1','1','4','30','1','1');
      Validate('w_codigo','Código externo','1','','1','30','1','1');
      Validate('w_assinatura','Assinatura Eletrônica','1','1','6','30','1','1');
      ShowHTML('  theForm.Botao[0].disabled=true;');
      ShowHTML('  theForm.Botao[1].disabled=true;');
    } elseif ($O=='E') {
      Validate('w_assinatura','Assinatura Eletrônica','1','1','6','30','1','1');
      ShowHTML('  if (confirm(\'Confirma a exclusão deste registro?\')) ');
      ShowHTML('     { return (true); }; ');
      ShowHTML('     { return (false); }; ');
      ShowHTML('  theForm.Botao[0].disabled=true;');
      ShowHTML('  theForm.Botao[1].disabled=true;');
    } 
    ValidateClose();
    ScriptClose();
  } 
  ShowHTML('</HEAD>');
  ShowHTML('<BASE HREF="'.$conRootSIW.'">');
  if ($w_troca>'') {
    BodyOpen('onLoad=\'document.Form.'.$w_troca.'.focus()\';');
  } elseif (!(strpos('IA',$O)===false)) {
    BodyOpen('onLoad=\'document.Form.w_nome.focus()\';');
  } elseif ($O=='E') {
    BodyOpen('onLoad=\'document.Form.w_assinatura.focus()\';');
  } else {
    BodyOpen('onLoad=\'this.focus()\';');
  } 
  ShowHTML('<B><FONT COLOR="#000000">'.$w_TP.'</FONT></B>');
  ShowHTML('<HR>');
  ShowHTML('<div align=center><center>');
  ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
  ShowHTML('<tr><td colspan="2"><hr NOSHADE color=#000000 size=4></td></tr>');
  ShowHTML('<tr><td colspan="2"  bgcolor="#f0f0f0"><div align=justify><font size="2"><b>PROJETO: '.$w_cabecalho.'</b></font></div></td></tr>');
  ShowHTML('<tr><td colspan="2"><hr NOSHADE color=#000000 size=4></td></tr>');
  if ($O=='L') {
    ShowHTML('    ');
    ShowHTML('<tr><td align="center"><table width="97%" border="0">');
    ShowHTML('<tr><td>');
    ShowHTML('        <a accesskey="F" class="ss" HREF="javascript:this.status.value;" onClick="window.close(); opener.focus();"><u>F</u>echar</a>&nbsp;');
    ShowHTML('<tr><td colspan="3" bgcolor="'.$conTrBgColorLightBlue2.'"" style="border: 2px solid rgb(0,0,0);"><ol>');
    ShowHTML('  A finalidade desta tela é importar as etapas de um arquivo MS-Project para este projeto. O procedimento consiste em:');
    ShowHTML('  <li>Salve seu arquivo MS-Project como arquivo XML.');
    ShowHTML('  <li>Informe os dados do formulário para definir os valores a serem assumidos pelas etapas/pacotes.');
    ShowHTML('  <li>Por último, clique no botão "Procurar..." e localize o arquivo XML no seu computador.');
    ShowHTML('  <li>Clique no botão "Importar" para executar o procedimento de importação.');
    ShowHTML('  <li>Após o término da importação pode ser necessário ajustar os dados das etapas, tais como pesos, responsáveis etc.');
    ShowHTML('  </td>');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td align="center">');
    $sql = new db_getCustomerData; $RS = $sql->getInstanceOf($dbms,$w_cliente);
    ShowHTML('<tr><td colspan="3" bgcolor="#D0D0D0" style="border: 2px solid rgb(0,0,0);"><ul><b><font color="#BC3131">ATENÇÃO:</font></b>');
    ShowHTML('  <li>Este procedimento irá recriar todas as etapas. O início e o fim do projeto será ajustado conforme os prazos das etapas.');
    ShowHTML('  <li>Vínculos entre as etapas e recursos, interessados ou restrições serão AUTOMATICAMENTE APAGADOS, sendo necessários recriá-los após a importação.');
    ShowHTML('  <li>Outros vínculos com a etapa, tais como comentários e arquivos NÃO SÃO apagados de forma automática. Remova-os manualmente antes de executar a importação.');
    ShowHTML('  <li>A importação NÃO SERÁ POSSÍVEL se alguma etapa tiver tarefas, demandas, contratos ou quaisquer outros documentos vinculados.');
    ShowHTML('  <li>O TAMANHO MÁXIMO aceito para o arquivo é de '.(f($RS,'upload_maximo')/1024).' KBytes');
    ShowHTML('</tr>');
    ShowHTML('<FORM action="'.$w_dir.$w_pagina.'Grava&SG='.$SG.'&O='.$O.'" name="Form" enctype="multipart/form-data" onSubmit="return(Validacao(this));" method="POST">');
    ShowHTML('<INPUT type="hidden" name="P1" value="'.$P1.'">');
    ShowHTML('<INPUT type="hidden" name="P2" value="'.$P2.'">');
    ShowHTML('<INPUT type="hidden" name="P3" value="'.$P3.'">');
    ShowHTML('<INPUT type="hidden" name="P4" value="'.$P4.'">');
    ShowHTML('<INPUT type="hidden" name="TP" value="'.$TP.'">');
    ShowHTML('<INPUT type="hidden" name="R" value="'.$R.'">');
    ShowHTML('<INPUT type="hidden" name="p_projeto" value="'.$p_projeto.'">');
    ShowHTML('<INPUT type="hidden" name="w_atual" value="'.$w_caminho.'">');
    ShowHTML('<INPUT type="hidden" name="w_troca" value="">');
    ShowHTML('<INPUT type="hidden" name="w_upload_maximo" value="'.f($RS,'upload_maximo').'">');
    ShowHTML('      <tr valign="top">');
    ShowHTML('        <td><b><u>P</u>eso dos pacotes de trabalho:<br><INPUT ACCESSKEY="O" TYPE="TEXT" CLASS="STI" NAME="w_peso" SIZE=2 MAXLENGTH=2 VALUE="'.nvl($w_peso,1).'" '.$w_Disabled.' title="Informe o peso da etapa no cálculo do percentual de execução."></td>');
    selecaoBaseGeografica('<U>B</U>ase geográfica:','B','Selecione a base geográfica da atuação, execução, entrega ou impacto.',$w_base,null,null,'w_base',null,'onChange="document.Form.action=\''.$w_dir.$w_pagina.$par.'&O=L&SG='.$SG.'\'; document.Form.w_troca.value=\'w_base\'; document.Form.submit();"');
    if (nvl($w_base,-1)!=5) {
      ShowHTML('      <tr valign="top">');
      if ($w_base==1) SelecaoPais('<u>P</u>aís:','P',null,$w_pais,null,'w_pais',null,null);
      if ($w_base==2) {
        SelecaoPais('<u>P</u>aís:','P',null,$w_pais,null,'w_pais',null,'onChange="document.Form.action=\''.$w_dir.$w_pagina.$par.'&O=L&SG='.$SG.'\'; document.Form.w_troca.value=\'w_regiao\'; document.Form.submit();"');
        SelecaoRegiao('<u>R</u>egião:','R',null,$w_regiao,$w_pais,'w_regiao',null,null);
      }
      if ($w_base==3) {
        SelecaoPais('<u>P</u>aís:','P',null,$w_pais,null,'w_pais',null,'onChange="document.Form.action=\''.$w_dir.$w_pagina.$par.'&O=L&SG='.$SG.'\'; document.Form.w_troca.value=\'w_uf\'; document.Form.submit();"');
        SelecaoEstado('E<u>s</u>tado:','S',null,$w_uf,$w_pais,null,'w_uf',null,null);
      }
      if ($w_base==4) {
        SelecaoPais('<u>P</u>aís:','P',null,$w_pais,null,'w_pais',null,'onChange="document.Form.action=\''.$w_dir.$w_pagina.$par.'&O=L&SG='.$SG.'\'; document.Form.w_troca.value=\'w_uf\'; document.Form.submit();"');
        SelecaoEstado('E<u>s</u>tado:','S',null,$w_uf,$w_pais,null,'w_uf',null,'onChange="document.Form.action=\''.$w_dir.$w_pagina.$par.'&O=L&SG='.$SG.'\'; document.Form.w_troca.value=\'w_cidade\'; document.Form.submit();"');
        SelecaoCidade('<u>C</u>idade:','C',null,$w_cidade,$w_pais,$w_uf,'w_cidade',null,null);
      }
    }
    ShowHTML('      <tr valign="top">');
    SelecaoPessoa('Respo<u>n</u>sável pelas etapas:','N','Selecione o responsável pelas etapas na relação.',$w_sq_pessoa,null,'w_sq_pessoa','USUARIOS','onChange="document.Form.action=\''.$w_dir.$w_pagina.$par.'&O=L&SG='.$SG.'\'; document.Form.w_troca.value=\'w_sq_unidade\'; document.Form.submit();"');
    $sql = new db_getPersonData; $RS_Pessoa = $sql->getInstanceOf($dbms, $w_cliente, $w_sq_pessoa, null, null);
    $w_sq_unidade = f($RS_Pessoa,'sq_unidade');
    ShowHTML('              <td colspan=3><table border=0 cellpadding=0 cellspacing=0 width="100%"><tr>');
    SelecaoUnidade('<U>S</U>etor responsável pelas etapas:','S','Selecione o setor responsável pela execução das etapas',$w_sq_unidade,null,'w_sq_unidade',null,null);    ShowHTML('                  </table>');
    ShowHTML('          <tr>');
    ShowHTML('      <tr><td colspan=3><b>A<u>r</u>quivo:</b><br><input '.$w_Disabled.' accesskey="R" type="file" name="w_caminho" class="STI" SIZE="80" MAXLENGTH="100" VALUE="" title="OBRIGATÓRIO. Clique no botão ao lado para localizar o arquivo. Ele será transferido automaticamente para o servidor.">');
    if ($w_caminho>'') {
      ShowHTML('              <b>'.LinkArquivo('SS',$w_cliente,$w_caminho,'_blank','Clique para exibir o arquivo atual.','Exibir',null).'</b>');
    } 
    ShowHTML('      <tr><td align="center" colspan=3 ><hr>');
    ShowHTML('            <input class="STB" type="submit" name="Botao" value="Importar">');
    ShowHTML('          </td>');
    ShowHTML('      </tr>');
    ShowHTML('    </table>');
    ShowHTML('    </TD>');
    ShowHTML('</tr>');
    ShowHTML('</FORM>');
  } elseif (!(strpos('IAEV',$O)===false)) {
    if (!(strpos('EV',$O)===false)) {
      $w_Disabled=' DISABLED ';
    } 
    AbreForm('Form',$w_dir.$w_pagina.'Grava','POST','return(Validacao(this));',null,$P1,$P2,$P3,$P4,$TP,$SG,$R,$O);
    ShowHTML('<INPUT type="hidden" name="w_chave" value="'.$w_chave.'">');
    ShowHTML('<INPUT type="hidden" name="w_troca" value="">');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td align="center">');
    ShowHTML('    <table width="97%" border="0">');
    ShowHTML('      <tr><td><table border=0 width="100%" cellspacing=0 cellpadding=0><tr valign="top">');
    ShowHTML('           <td><b><u>N</u>ome:</b><br><input '.$w_Disabled.' accesskey="N" type="text" name="w_nome" class="sti" SIZE="30" MAXLENGTH="30" VALUE="'.$w_nome.'"></td>'); 
    ShowHTML('           <td title="OPCIONAL. Código desse registro em outro sistema"><b><u>C</u>ódigo externo:</b><br><input '.$w_Disabled.' accesskey="C" type="text" name="w_codigo" class="sti" SIZE="30" MAXLENGTH="30" VALUE="'.$w_codigo.'"></td>'); 
    ShowHTML('        <tr valign="top">');
    MontaRadioSN('<b>Ativo?</b>',$w_ativo,'w_ativo');
    ShowHTML('           </table>');
    ShowHTML('      <tr><td align="LEFT"><b><U>A</U>ssinatura Eletrônica:<BR> <INPUT ACCESSKEY="A" class="sti" type="PASSWORD" name="w_assinatura" size="30" maxlength="30" value=""></td></tr>');
    ShowHTML('      <tr><td align="center"><hr>');
    if ($O=='E') {
      ShowHTML('   <input class="STB" type="submit" name="Botao" value="Excluir">');
    } else {
      if ($O=='I') {
        ShowHTML('            <input class="STB" type="submit" name="Botao" value="Incluir">');
      } else {
        ShowHTML('            <input class="STB" type="submit" name="Botao" value="Atualizar">');
      } 
    } 
    ShowHTML('            <input class="stb" type="button" onClick="location.href=\''.montaURL_JS($w_dir,$R.'&O=L&w_cliente='.$w_cliente.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG).'\';" name="Botao" value="Cancelar">');
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
// Procedimento que executa as operações de BD
// -------------------------------------------------------------------------
function Grava() {
  extract($GLOBALS);
  global $w_Disabled;
  Cabecalho();
  ShowHTML('</HEAD>');
  ShowHTML('<BASE HREF="'.$conRootSIW.'">');
  BodyOpen('onLoad=this.focus();');
  switch ($SG) {
  case 'IMPPROJ':
    // Verifica se a Assinatura Eletrônica é válida
    if (verificaAssinaturaEletronica($_SESSION['USERNAME'],upper($_REQUEST['w_assinatura'])) || $w_assinatura=='') {
      // Trata o recebimento de upload ou dados 
      ShowHTML('<div align=center><center><br><br><br><br><br><br><br><br><br><br><img src="images/relogio.gif" align="center"> <b>Aguarde: Importação em andamento...</b><br><br><br><br><br><br><br><br><br><br></center></div>');
      flush();
      if ((false!==(strpos(upper($_SERVER['HTTP_CONTENT_TYPE']),'MULTIPART/FORM-DATA'))) || (false!==(strpos(upper($_SERVER['CONTENT_TYPE']),'MULTIPART/FORM-DATA')))) {
        // Se foi feito o upload de um arquivo 
        if (UPLOAD_ERR_OK==0) {
          $w_maximo = $_REQUEST['w_upload_maximo'];
          $i        = 0; // Variável para saber se processou algum registro.
          foreach ($_FILES as $Chv => $Field) {
            if (!($Field['error']==UPLOAD_ERR_OK || $Field['error']==UPLOAD_ERR_NO_FILE)) {
              // Verifica se o tamanho das fotos está compatível com  o limite de 100KB. 
              ScriptOpen('JavaScript');
              ShowHTML('  alert(\'Atenção: o tamanho máximo do arquivo não pode exceder '.($w_maximo/1024).' KBytes!\');');
              ScriptClose();
              retornaFormulario('w_observacao');
              exit();
            }
            if ($Field['size'] > 0) {
              // Verifica se o tamanho das fotos está compatível com  o limite de 100KB. 
              if ($Field['size'] > $w_maximo) {
                ScriptOpen('JavaScript');
                ShowHTML('  alert(\'Atenção: o tamanho máximo do arquivo não pode exceder '.($w_maximo/1024).' KBytes!\');');
                ScriptClose();
                retornaFormulario('w_observacao');
                exit();
              } 
              // Agrega a extensão XML ao nome do arquivo temporário
              $w_file = basename($Field['tmp_name']).'.xml';
              $w_tamanho = $Field['size'];
              $w_tipo    = $Field['type'];
              $w_nome    = $Field['name'];
              if ($w_file>'') move_uploaded_file($Field['tmp_name'],DiretorioCliente($w_cliente).'/'.$w_file);
            } 
          } 
          ShowHTML('<br><br><b>Fase 1/4 - Upload do arquivo: completada');
          flush();
          // Carrega array com os dados do arquivo
          $xml = simplexml_load_file(DiretorioCliente($w_cliente).'/'.$w_file);
          // Procura pelas tarefas
          $result = $xml->xpath('Tasks/Task');
          foreach($xml->Tasks->Task as $row) {
            if ($row->ID!='0') {
              $w_titulo     = substr(utf8_decode(str_replace('â€“','-',str_replace('  ','  ',$row->Name))),0,100);
              $w_inicio     = utf8_decode($row->Start);
              $w_inicio     = substr($w_inicio,8,2).'/'.substr($w_inicio,5,2).'/'.substr($w_inicio,0,4);
              $w_fim        = utf8_decode($row->Finish);
              $w_fim        = substr($w_fim,8,2).'/'.substr($w_fim,5,2).'/'.substr($w_fim,0,4);
              $w_perc       = utf8_decode($row->PercentComplete);
              $w_descricao  = trim(substr(utf8_decode(str_replace('â€“','-',str_replace('  ','  ',$row->Notes))),0,2000));
              $w_codigo     = utf8_decode($row->OutlineNumber);
              $w_ordem      = utf8_decode($row->OutlineNumber);
              if (strpos($w_ordem,'.')===false) {
                $w_pai = null;
              } else {
                $w_pai = '';
                while(strpos($w_ordem,'.')!==false) {
                  $w_pai   .= substr($w_ordem,0,strpos($w_ordem,'.')+1);
                  $w_ordem = substr($w_ordem,strpos($w_ordem,'.')+1);
                }
                $w_chave_pai = $w_chave[substr($w_pai,0,strlen($w_pai)-1)];
              }
              if ($i==0) {
                // Se for gravar etapa, apaga todas as etapas existentes
                $i = 1;
                $SQL = new dml_putEtapaProject; $SQL->getInstanceOf($dbms,'E',$_REQUEST['p_projeto'],null,
                    null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,&$w_chave_nova);
                ShowHTML('<br><br><b>Fase 2/4 - Remoção das etapas do projeto: completada');
                flush();
              }
              $SQL = new dml_putEtapaProject; $SQL->getInstanceOf($dbms,'I',$_REQUEST['p_projeto'],$w_chave_pai,
                  $w_titulo,nvl($w_descricao,$w_titulo),$w_ordem,$w_inicio,$w_fim,$w_perc,
                  $_REQUEST['w_sq_pessoa'],$_REQUEST['w_sq_unidade'],$w_usuario,
                  $_REQUEST['w_base'],$_REQUEST['w_pais'],$_REQUEST['w_regiao'],$_REQUEST['w_uf'],$_REQUEST['w_cidade'],
                  null,&$w_chave_nova);
            
              $w_chave[$w_codigo] = $w_chave_nova;
            }
          }
          if ($i>0) {
            ShowHTML('<br><br><b>Fase 3/4 - Carga das etapas do projeto a partir do arquivo: completada');
            flush();
            // Se processou algum registro, ajusta os pacotes de trabalho
            $SQL = new dml_putEtapaProject; $SQL->getInstanceOf($dbms,'A',$_REQUEST['p_projeto'],null,
                null,null,null,null,null,null,null,null,null,null,null,null,null,null,$_REQUEST['w_peso'],&$w_chave_nova);
            ShowHTML('<br><br><b>Fase 4/4 - Indicação dos pacotes de trbalho: completada');
            flush();
          }
        } else {
          ScriptOpen('JavaScript');
          ShowHTML('  alert(\'ATENÇÃO: ocorreu um erro na transferência do arquivo. Tente novamente!\');');
          ScriptClose();
        } 
        ScriptOpen('JavaScript');
        ShowHTML('  alert("Importação concluída com sucesso!");');
        ShowHTML('  window.close();');
        ShowHTML('  opener.location.reload();');
        ShowHTML('  opener.focus();');
        ScriptClose();
      } 
    } else {
      ScriptOpen('JavaScript');
      ShowHTML('  alert(\'Assinatura Eletrônica inválida!\');');
      ScriptClose();
      retornaFormulario('w_assinatura');
    } 
    break;
  default:
    exibevariaveis();
    ScriptOpen('JavaScript');
    ShowHTML('  alert(\'Bloco de dados não encontrado: '.$SG.'\');');
    ScriptClose();
    break;
  } 
  Rodape(); 
} 

// =========================================================================
// Rotina principal
// -------------------------------------------------------------------------
function Main() {
  extract($GLOBALS);
  global $w_Disabled;
  switch ($par) {
    case 'ETAPA':              Etapa();             break;
    case 'GRAVA':              Grava();             break;
    default:
    Cabecalho();
    ShowHTML('<BASE HREF="'.$conRootSIW.'">');
    BodyOpen('onLoad=this.focus();');
    ShowHTML('<B><FONT COLOR="#000000">'.$w_TP.'</FONT></B>');
    ShowHTML('<HR>');
    ShowHTML('<div align=center><center><br><br><br><br><br><br><br><br><br><br><img src="images/icone/underc.gif" align="center"> <b>Esta opção está sendo desenvolvida.</b><br><br><br><br><br><br><br><br><br><br></center></div>');
    Rodape();
    exibevariaveis();
  break;
  } 
} 
?>