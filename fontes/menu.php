<?php
header('Expires: '.-1500);
session_start();
$w_dir_volta = '';
include_once('constants.inc');
include_once('jscript.php');
include_once('funcoes.php');
include_once('classes/db/abreSessao.php');
include_once('classes/sp/db_getCustomerData.php');
include_once('classes/sp/db_getLinkData.php');
include_once('classes/sp/db_getLinkDataUser.php');
include_once('classes/sp/db_getCustomerSite.php');
include_once('classes/sp/db_getLinkSubMenu.php');
include_once('classes/sp/db_getLinkDataParent.php');
include_once('classes/sp/db_getUserData.php');
include_once('classes/sp/db_getMenuRelac.php');
include_once('classes/sp/db_verificasenha.php');
include_once('classes/sp/db_verificaAssinatura.php');
include_once('classes/sp/db_updatePassword.php');
include_once('classes/sp/db_getSiwCliModLis.php');
include_once('classes/sp/dml_putMenuRelac.php');
include_once('funcoes/opcaoMenu.php');
include_once('funcoes/selecaoFaseCheck.php');
include_once('funcoes/selecaoServico.php');
// =========================================================================
//  /menu.php
// ------------------------------------------------------------------------
// Nome     : Alexandre Vinhadelli Papadópolis
// Descricao: Monta a estrutura de frames e o menu da aplicação
// Mail     : alex@sbpi.com.br
// Criacao  : 18/03/2005 21:00
// Versao   : 1.0.0.0
// Local    : Brasília - DF
// -------------------------------------------------------------------------

// Carrega variáveis locais com os dados dos parâmetros recebidos
$par        = upper($_REQUEST['par']);
$P1         = $_REQUEST['P1'];
$P3         = $_REQUEST['P3'];
$P4         = $_REQUEST['P4'];
$TP         = $_REQUEST['TP'];
$SG         = upper($_REQUEST['SG']);
$R          = lower($_REQUEST['R']);
$O          = upper($_REQUEST['O']);

// Verifica se o usuário está autenticado
if ($_SESSION['LOGON'] !='Sim') EncerraSessao();

// Declaração de variáveis
$dbms = new abreSessao; $dbms = $dbms->getInstanceOf($_SESSION['DBMS']);

$p_cliente  = $_SESSION['P_CLIENTE'];
$sq_pessoa  = $_SESSION['SQ_PESSOA'];
$w_pagina   = 'menu.php?par=';
$w_ImagemPadrao='images/Folder/SheetLittle.gif';

if ($O=='' && $par=='TROCASENHA') { $O='A'; }

switch ($O) {
  case 'I': $w_TP=$TP.' - Inclusão'; break;
  case 'A': $w_TP=$TP.' - Alteração'; break;
  case 'E': $w_TP=$TP.' - Exclusão'; break;
  case 'V': $w_TP=$TP.' - Envio'; break;
  case 'P': $w_TP=$TP.' - Filtragem'; break;
  default: $w_TP=$TP; 
}

$w_cliente=RetornaCliente();

Main();

FechaSessao($dbms);

// =========================================================================
// Rotina de montagem da estrutura de frames
// -------------------------------------------------------------------------
function Frames() {
  extract($GLOBALS);
  ShowHTML('<HTML> ');
  ShowHTML('  <HEAD> ');
  ShowHTML('  <link rel="shortcut icon" href="'.$conRootSIW.'favicon.ico" type="image/ico" />');
  Estrutura_CSS($w_cliente);
  ShowHTML('  <TITLE>'.$conSgSistema.' - '.$conNmSistema.'</TITLE> ');
  ShowHTML('  </HEAD> ');
  
  $content = '';
  if (nvl($_REQUEST['content'],'')!='') $content = base64_decode($_REQUEST['content']);

  ShowHTML('    <FRAMESET COLS="20%,80%"> ');
  ShowHTML('     <FRAME SRC="menu.php?par=ExibeDocs&$content='.$content.'" SCROLLING="AUTO" FRAMEBORDER="0" FRAMESPACING=0 NAME="menu"> ');
  ShowHTML('     <FRAME SRC="branco.htm" SCROLLING="AUTO" FRAMEBORDER="0" FRAMESPACING=0 NAME="content"> ');
  ShowHTML('    <NOFRAMES> ');
  ShowHTML('     <BODY BGCOLOR="#FFFFFF" BACKGROUND="images/bg.jpg" BGPROPERTIES="FIXED"> ');
  ShowHTML('      <P>Seu navegador não aceita <I>frames</I>. Atualize-o, preferencialmente, para o Microsoft Internet Explorer 5.5 ou superior.</P> ');
  ShowHTML('     </BODY> ');
  ShowHTML('    </FRAMESET> ');
  ShowHTML('</HTML> ');
}

// =========================================================================
// Rotina de montagem do menu
// -------------------------------------------------------------------------
function ExibeDocs() {
  extract($GLOBALS);
  // Inclusão do arquivo da classe
  include_once("classes/menu/xPandMenu.php");

  // Instanciando a classe menu
  $root = new XMenu();
  $i    = 1;
  $j    = 1;
  $k    = 1;
  $l    = 1;
  if ($SG=='' || ($SG > '' && ($O=='L'||$O=='P'))) {

    $sql = new db_getLinkDataUser; $RS = $sql->getInstanceOf($dbms, $p_cliente, $sq_pessoa, 'IS NULL');
    foreach ($RS as $row) {
      
      $w_titulo = f($row,'nome');

      if (f($row,'filho') > 0) {

        eval('$node'.i.' = &$root->addItem(new XNode(f($row,\'nome\'),false));');

        $sql = new db_getLinkDataUser; $RS1 = $sql->getInstanceOf($dbms, $p_cliente, $sq_pessoa, f($row,'sq_menu'));
        foreach ($RS1 as $row1) {
          $w_titulo .= ' - '.f($row1,'NOME');
          if (f($row1,'Filho') >0) {

            eval('$node'.i.'_'.j.' = &$node'.i.'->addItem(new XNode(f($row1,\'nome\'),false));');

            $sql = new db_getLinkDataUser; $RS2 = $sql->getInstanceOf($dbms, $p_cliente, $sq_pessoa, f($row1,'sq_menu'));
            foreach ($RS2 as $row2) {

              $w_titulo .= ' - '.f($row2,'NOME');
              if (f($row2,'Filho') > 0) {

                eval('$node'.i.'_'.j.'_'.k.' = &$node'.i.'_'.j.'->addItem(new XNode(f($row2,\'nome\'),false));');

                $sql = new db_getLinkDataUser; $RS3 = $sql->getInstanceOf($dbms, $p_cliente, $sq_pessoa, f($row2,'sq_menu'));
                foreach ($RS3 as $row3) {

                  $w_titulo .= ' - '.f($row3,'NOME');
                  if (f($row3,'IMAGEM') > '') $w_Imagem=f($row3,'IMAGEM'); else $w_Imagem=$w_ImagemPadrao;
                  if (f($row3,'link')>'') {
                    if (f($row3,'externo')=='S') {
                      if (!(strpos(f($row3,'link'),'@file')===false)) {
                        eval('$node'.i.'_'.j.'_'.k.'_'.l.' = &$node'.i.'_'.j.'_'.k.'->addItem(new XNode(null,LinkArquivo(\'hl\',$p_cliente,str_replace(\'@files/\',\'\',f($row3,\'LINK\')),f($row3,\'target\'),null,\'<img src="\'.$w_Imagem.\'" border=0>\'.f($row3,\'nome\'),null),null,null,null));');
                      } else {
                         eval('$node'.i.'_'.j.'_'.k.'_'.l.' = &$node'.i.'_'.j.'_'.k.'->addItem(new XNode(f($row3,\'nome\'),f($row3,\'LINK\'),$w_Imagem,$w_Imagem,f($row3,\'target\')));');
                      }
                    } else {
                       eval('$node'.i.'_'.j.'_'.k.'_'.l.' = &$node'.i.'_'.j.'_'.k.'->addItem(new XNode(f($row3,\'nome\'),f($row3,\'LINK\').\'&P1=\'.f($row3,\'P1\').\'&P2=\'.f($row3,\'P2\').\'&P3=\'.f($row3,\'P3\').\'&P4=\'.f($row3,\'P4\').\'&TP=<img src=\'.$w_Imagem.\' BORDER=0>\'.$w_titulo.\'&SG=\'.f($row3,\'SIGLA\').MontaFiltro(\'GET\'),$w_Imagem,$w_Imagem,f($row3,\'target\')));');
                    }
                  } else {
                    eval('$node'.i.'_'.j.'_'.k.'_'.l.' = &$node'.i.'_'.j.'_'.k.'->addItem(new XNode(null,null,null,null,\'<img src="\'.$w_Imagem.\'" border=0>\'.f($row3,\'nome\'),null,null,null,null));');
                  }
                  $w_titulo=str_replace(' - '.f($row3,'NOME'),'',$w_titulo);
                  $l += 1;
                }
              } else {
                if (f($row2,'IMAGEM')>'') $w_Imagem=f($row2,'IMAGEM'); else $w_Imagem=$w_ImagemPadrao;
                if (f($row2,'link')>'') {
                  if (f($row2,'externo')=='S') {
                    if (!(strpos(f($row2,'link'),'@file')===false)) {
                      eval('$node'.i.'_'.j.'_'.k.' = &$node'.i.'_'.j.'->addItem(new XNode(null,LinkArquivo(\'hl\',$p_cliente,str_replace(\'@files/\',\'\',f($row2,\'LINK\')),f($row2,\'target\'),null,\'<img src="\'.$w_Imagem.\'" border=0>\'.f($row2,\'nome\'),null),null,null,null));');
                    } else {
                       eval('$node'.i.'_'.j.'_'.k.' = &$node'.i.'_'.j.'->addItem(new XNode(f($row2,\'nome\'),f($row2,\'LINK\'),$w_Imagem,$w_Imagem,f($row2,\'target\')));');
                    }
                  } else {
                     eval('$node'.i.'_'.j.'_'.k.' = &$node'.i.'_'.j.'->addItem(new XNode(f($row2,\'nome\'),f($row2,\'LINK\').\'&P1=\'.f($row2,\'P1\').\'&P2=\'.f($row2,\'P2\').\'&P3=\'.f($row2,\'P3\').\'&P4=\'.f($row2,\'P4\').\'&TP=<img src=\'.$w_Imagem.\' BORDER=0>\'.$w_titulo.\'&SG=\'.f($row2,\'SIGLA\').MontaFiltro(\'GET\'),$w_Imagem,$w_Imagem,f($row2,\'target\')));');
                  }
                } else {
                  eval('$node'.i.'_'.j.'_'.k.' = &$node'.i.'_'.j.'->addItem(new XNode(null,null,null,null,\'<img src="\'.$w_Imagem.\'" border=0>\'.f($row2,\'nome\'),null,null,null,null));');
                }
              }
              $w_titulo=str_replace(' - '.f($row2,'NOME'),'',$w_titulo);
              $k += 1;
            }
          } else {
            if (f($row1,'IMAGEM')>'') $w_Imagem=f($row1,'IMAGEM'); else $w_Imagem=$w_ImagemPadrao;
            if (f($row1,'link')>'') {
              if (f($row1,'externo')=='S') {
                if (!(strpos(f($row1,'link'),'@file')===false)) {
                  eval('$node'.i.'_'.j.' = &$node'.i.'->addItem(new XNode(null,LinkArquivo(\'hl\',$p_cliente,str_replace(\'@files/\',\'\',f($row1,\'LINK\')),f($row1,\'target\'),null,\'<img src="\'.$w_Imagem.\'" border=0>\'.f($row1,\'nome\'),null),null,null,null));');
                } else {
                   eval('$node'.i.'_'.j.' = &$node'.i.'->addItem(new XNode(f($row1,\'nome\'),f($row1,\'LINK\'),$w_Imagem,$w_Imagem,f($row1,\'target\')));');
                }
              } else {
                 eval('$node'.i.'_'.j.' = &$node'.i.'->addItem(new XNode(f($row1,\'nome\'),f($row1,\'LINK\').\'&P1=\'.f($row1,\'P1\').\'&P2=\'.f($row1,\'P2\').\'&P3=\'.f($row1,\'P3\').\'&P4=\'.f($row1,\'P4\').\'&TP=<img src=\'.$w_Imagem.\' BORDER=0>\'.$w_titulo.\'&SG=\'.f($row1,\'SIGLA\').MontaFiltro(\'GET\'),$w_Imagem,$w_Imagem,f($row1,\'target\')));');
              }
            } else {
              eval('$node'.i.'_'.j.' = &$node'.i.'->addItem(new XNode(null,null,null,null,\'<img src="\'.$w_Imagem.\'" border=0>\'.f($row1,\'nome\'),null,null,null,null));');
            }
          }
          $w_titulo=str_replace(' - '.f($row1,'NOME'),'',$w_titulo);
          $j += 1;
        }
      } else {
        if (f($row,'IMAGEM')>'') $w_Imagem=f($row,'IMAGEM'); else $w_Imagem=$w_ImagemPadrao;

        if (f($row,'link')>'') {
          if (f($row,'externo')=='S') {
            if (!(strpos(f($row,'link'),'@file')===false)) {
              eval('$node'.i.' = &$root->addItem(new XNode(null,LinkArquivo(\'hl\',$p_cliente,str_replace(\'@files/\',\'\',f($row,\'LINK\')),f($row,\'target\'),null,\'<img src="\'.$w_Imagem.\'" border=0>\'.f($row,\'nome\'),null),null,null,null));');
            } else {
              eval('$node'.i.' = &$root->addItem(new XNode(f($row,\'nome\'),f($row,\'LINK\'),$w_Imagem,$w_Imagem,f($row,\'target\')));');
            }
          } else {
            eval('$node'.i.' = &$root->addItem(new XNode(f($row,\'nome\'),f($row,\'LINK\').\'&P1=\'.f($row,\'P1\').\'&P2=\'.f($row,\'P2\').\'&P3=\'.f($row,\'P3\').\'&P4=\'.f($row,\'P4\').\'&TP=<img src=\'.$w_Imagem.\' BORDER=0>\'.$w_titulo.\'&SG=\'.f($row,\'SIGLA\').MontaFiltro(\'GET\'),$w_Imagem,$w_Imagem,f($row,\'target\')));');
          }
        } else {
          eval('$node'.i.' = &$root->addItem(new XNode(null,null,null,null,\'<img src="\'.$w_Imagem.\'" border=0>\'.f($row,\'nome\'),null,null,null,null));');
        }
      }
      $i += 1;
    }
  } else {
    // Se for montagem de sub-menu para uma opção do menu principal
    // Se for passado o número do documento, ele é apresentado na tela, ao invés da descrição
    if ($_REQUEST['w_documento']>'') 
       $w_descricao=$_REQUEST['w_documento'];
    else {
      $sql = new db_getLinkData; $RS = $sql->getInstanceOf($dbms, $p_cliente, $SG);
      $w_descricao=f($RS,'NOME');
    }
    
    $node1 = &$root->addItem(new XNode($w_descricao,false,null,null,null,null,true));

    $sql = new db_getLinkSubMenu; $RS = $sql->getInstanceOf($dbms, $p_cliente, $SG);
   
    foreach ($RS as $row) {    
      $w_titulo = $TP.' - '.f($row,'nome');
      if (f($row,'imagem') > '') $w_Imagem=f($row,'imagem'); else $w_Imagem=$w_ImagemPadrao; 
      if (f($row,'externo')=='S')
         eval('$node1_'.i.' = &$node1->addItem(new XNode(null,LinkArquivo(\'hl\',$p_cliente,str_replace(\'@files/\',\'\',f($row,\'LINK\')),f($row,\'target\'),null,\'<img src="\'.$w_Imagem.\'" border=0>\'.f($row,\'nome\'),null),null,null,null));');
      else {
        if ($_REQUEST['w_cgccpf']>'')
          eval('$node'.i.' = &$node1->addItem(new XNode(f($row,\'nome\'),f($row,\'LINK\').\'&P1=\'.f($row,\'P1\').\'&P2=\'.f($row,\'P2\').\'&P3=\'.f($row,\'P3\').\'&P4=\'.f($row,\'P4\').\'&TP=\'.$w_titulo.\'&SG=\'.f($row,\'SIGLA\').\'&O=L&w_cgccpf=\'.$_REQUEST[\'w_cgccpf\'].MontaFiltro(\'GET\'),$w_Imagem,$w_Imagem,f($row,\'target\')));');
        elseif ($_REQUEST['w_usuario']>'')
          eval('$node'.i.' = &$node1->addItem(new XNode(f($row,\'nome\'),f($row,\'LINK\').\'&P1=\'.f($row,\'P1\').\'&P2=\'.f($row,\'P2\').\'&P3=\'.f($row,\'P3\').\'&P4=\'.f($row,\'P4\').\'&TP=\'.$w_titulo.\'&SG=\'.f($row,\'SIGLA\').\'&O=L&w_usuario=\'.$_REQUEST[\'w_usuario\'].\'&w_menu=\'.f($row,\'menu_pai\'),$w_Imagem,$w_Imagem,f($row,\'target\')));');
        elseif ($_REQUEST['w_sq_acordo']>'')
          eval('$node'.i.' = &$node1->addItem(new XNode(f($row,\'nome\'),f($row,\'LINK\').\'&P1=\'.f($row,\'P1\').\'&P2=\'.f($row,\'P2\').\'&P3=\'.f($row,\'P3\').\'&P4=\'.f($row,\'P4\').\'&TP=\'.$w_titulo.\'&SG=\'.f($row,\'SIGLA\').\'&O=L&w_sq_acordo=\'.$_REQUEST[\'w_sq_acordo\'].\'&w_menu=\'.f($row,\'menu_pai\'),$w_Imagem,$w_Imagem,f($row,\'target\')));');
        elseif ($_REQUEST['w_sq_pessoa']>'') {
          eval('$node'.i.' = &$node1->addItem(new XNode(f($row,\'nome\'),f($row,\'LINK\').\'&P1=\'.f($row,\'P1\').\'&P2=\'.f($row,\'P2\').\'&P3=\'.f($row,\'P3\').\'&P4=\'.f($row,\'P4\').\'&TP=\'.$w_titulo.\'&SG=\'.f($row,\'SIGLA\').\'&O=L&w_sq_pessoa=\'.$_REQUEST[\'w_sq_pessoa\'].\'&w_menu=\'.f($row,\'menu_pai\').MontaFiltro(\'GET\'),$w_Imagem,$w_Imagem,f($row,\'target\')));');
        } else
          eval('$node'.i.' = &$node1->addItem(new XNode(f($row,\'nome\'),f($row,\'LINK\').\'&P1=\'.f($row,\'P1\').\'&P2=\'.f($row,\'P2\').\'&P3=\'.f($row,\'P3\').\'&P4=\'.f($row,\'P4\').\'&TP=\'.$w_titulo.\'&SG=\'.f($row,\'SIGLA\').\'&O=\'.$O.\'&w_chave=\'.$_REQUEST[\'w_chave\'].\'&w_menu=\'.f($row,\'menu_pai\').MontaFiltro(\'GET\'),$w_Imagem,$w_Imagem,f($row,\'target\')));');
      }

      if ($_REQUEST['O']=='I') break;

      $i = $i +1;
    }
    $sql = new db_getLinkData; $RS = $sql->getInstanceOf($dbms, $p_cliente, $SG);
    $node2 = &$root->addItem(new XNode('Nova consulta',$w_pagina.$par.'&O=L&R='.$R.'&SG='.f($RS,'sigla').'&TP='.RemoveTP($TP).'&P1='.f($RS,'P1').'&P2='.f($RS,'P2').'&P3='.f($RS,'P3').'&P4='.f($RS,'P4').MontaFiltro('GET'),$w_Imagem,$w_Imagem));
    $node3 = &$root->addItem(new XNode('Menu','menu.php?par=ExibeDocs',$w_Imagem,$w_Imagem));
    $i = 4;
  }

  eval('$node'.i.' = &$root->addItem(new XNode(\'Sair do sistema\',\'menu.php?par=Sair\',$w_Imagem,$w_Imagem,\'_top\', \'onClick="return(confirm(\\\'Confirma saída do sistema?\\\'));"\' ));');

  // Quando for concluída a montagem dos nós, chame a função generateTree(), usando o objeto raiz, para gerar o código HTML.
  // Essa função não possui argumentos.
  // No código da função pode ser verificado que há um parâmetro opcional, usado internamente para chamadas recursivas, necessárias à montagem de toda a árvore.
  $menu_html_code = $root->generateTree();

  // A função retornou o código HTML para exibir o menu


  // Montando a página:
  // 3 pontos:
  // - Referencie o arquivo Javascript
  // - Referencie o arquivo CSS
  // - Exiba o código HTML gerado anteriormente
  ShowHTML('<html>');
  head();
  ShowHTML('  <!-- CSS FILE for my tree-view menu -->');
  ShowHTML('  <link rel="stylesheet" type="text/css" href="classes/menu/xPandMenu.css">');
  ShowHTML('  <!-- JS FILE for my tree-view menu -->');
  ShowHTML('  <script src="classes/menu/xPandMenu.js"></script>');
  ShowHTML('</head>');
  ShowHTML('<BASEFONT FACE="Verdana, Helvetica, Sans-Serif" SIZE="2">');
  // Decide se montará o body do menu principal ou o body do sub-menu de uma opção a partir do valor de w_sq_pagina

  $sql = new db_getCustomerData; $RS = $sql->getInstanceOf($dbms, $p_cliente);
  print '<BODY topmargin=0 bgcolor="#FFFFFF" BACKGROUND="'.LinkArquivo(null,$p_cliente,'img/'.f($RS,'fundo'),null,null,null,'EMBED').'" BGPROPERTIES="FIXED" text="#000000" link="#000000" vlink="#000000" alink="#FF0000" ';
  if ($SG=='' && nvl($_REQUEST['content'],'')=='') {
    $sql = new db_getLinkData; $RS = $sql->getInstanceOf($dbms, $p_cliente, 'MESA');
    if (count($RS)>0) {
      if (f($RS,'IMAGEM')>'') {
        ShowHTML('onLoad=\'javascript:top.content.location="'.f($RS,'LINK').'&P1='.f($RS,'P1').'&P2='.f($RS,'P2').'&P3='.f($RS,'P3').'&P4='.f($RS,'P4').'&TP=<img src='.f($RS,'IMAGEM').' BORDER=0>'.f($RS,'nome').'&SG='.f($RS,'SIGLA').'"\'> ');
      } else {
        ShowHTML('onLoad=\'javascript:top.content.location="'.f($RS,'LINK').'&P1='.f($RS,'P1').'&P2='.f($RS,'P2').'&P3='.f($RS,'P3').'&P4='.f($RS,'P4').'&TP=<img src='.$w_ImagemPadrao.' BORDER=0>'.f($RS,'nome').'&SG='.f($RS,'SIGLA').'"\'> ');
      }
    } else {
      ShowHTML('>');
    }
  } else {
    if ($O=='L'||$O=='P') {
      $sql = new db_getLinkData; $RS = $sql->getInstanceOf($dbms, $p_cliente, $SG);
      ShowHTML('onLoad=\'javascript:top.content.location="'.f($RS,'LINK').'&R='.$_REQUEST['R'].'&P1='.f($RS,'P1').'&P2='.f($RS,'P2').'&P3='.f($RS,'P3').'&P4='.f($RS,'P4').'&TP='.$_REQUEST['TP'].' - '.f($RS,'nome').'&SG='.f($RS,'SIGLA').'&O='.$_REQUEST['O'].MontaFiltro('GET').'";\'>');
    } else {
      $sql = new db_getLinkDataParent; $RS = $sql->getInstanceOf($dbms, $p_cliente, $SG);
      $RS = SortArray($RS,'ordem','asc','nome','asc');

      foreach($RS as $row) {
        if ($_REQUEST['w_cgccpf']>'') {
          ShowHTML('onLoad=\'javascript:top.content.location="'.f($row,'LINK').'&R='.$_REQUEST['R'].'&P1='.f($row,'P1').'&P2='.f($row,'P2').'&P3='.f($row,'P3').'&P4='.f($row,'P4').'&TP='.$_REQUEST['TP'].' - '.f($row,'nome').'&SG='.f($row,'SIGLA').'&O='.$_REQUEST['O'].'&w_cgccpf='.$_REQUEST['w_cgccpf'].MontaFiltro('GET').'";\'>');
        } elseif ($_REQUEST['w_usuario']>'') {
          ShowHTML('onLoad=\'javascript:top.content.location="'.f($row,'LINK').'&R='.$_REQUEST['R'].'&P1='.f($row,'P1').'&P2='.f($row,'P2').'&P3='.f($row,'P3').'&P4='.f($row,'P4').'&TP='.$_REQUEST['TP'].' - '.f($row,'nome').'&SG='.f($row,'SIGLA').'&O=L&w_usuario='.$_REQUEST['w_usuario'].MontaFiltro('GET').'";\'>');
        } elseif ($_REQUEST['w_sq_pessoa']>'') {
          ShowHTML('onLoad=\'javascript:top.content.location="'.f($row,'LINK').'&R='.$_REQUEST['R'].'&P1='.f($row,'P1').'&P2='.f($row,'P2').'&P3='.f($row,'P3').'&P4='.f($row,'P4').'&TP='.$_REQUEST['TP'].' - '.f($row,'nome').'&SG='.f($row,'SIGLA').'&O='.$_REQUEST['O'].'&w_sq_pessoa='.$_REQUEST['w_sq_pessoa'].'&w_menu='.f($row,'menu_pai').MontaFiltro('GET').'";\'>');
        } else {
          ShowHTML('onLoad=\'javascript:top.content.location="'.f($row,'LINK').'&R='.$_REQUEST['R'].'&P1='.f($row,'P1').'&P2='.f($row,'P2').'&P3='.f($row,'P3').'&P4='.f($row,'P4').'&TP='.$_REQUEST['TP'].' - '.f($row,'nome').'&SG='.f($row,'SIGLA').'&O='.$_REQUEST['O'].'&w_chave='.$_REQUEST['w_chave'].'&w_menu='.f($row,'menu_pai').MontaFiltro('GET').'";\'>');
        }
        break;
      }
    }
  }

  ShowHTML('  <CENTER><table border=0 cellpadding=0 height="80" width="100%">');
  ShowHTML('      <tr><td width="100%" valign="center" align="center">');
  $sql = new db_getCustomerData; $RS = $sql->getInstanceOf($dbms, $p_cliente);
  ShowHTML('         <img src="'.LinkArquivo(null,$p_cliente,'img/'.f($RS,'logo1'),null,null,null,'EMBED').'" vspace="0" hspace="0" border="1"></td></tr>');
  ShowHTML('      <tr><td height=1><tr><td height=1 bgcolor="#000000">');
  ShowHTML('      <tr><td colspan=2 width="100%"><table border=0 width="100%" cellpadding=0 cellspacing=0><tr valign="top">');
  ShowHTML('          <td>'.$_SESSION['USUARIO'].': <b>'.$_SESSION['NOME_RESUMIDO'].'</b>');
  $sql = new db_getSiwCliModLis; $RS = $sql->getInstanceOf($dbms,$w_cliente,null,'IS');
  if (count($RS)>0) ShowHTML('          <br>Exercício:<b>'.$_SESSION['ANO'].'</b></TD>');
  if($w_cliente!="14014" && $w_cliente!="11134") ShowHTML('          <td align="right"><a class="hl" href="help.php?par=Menu&TP=<img src=images/Folder/hlp.gif border=0> SIW - Visão Geral&SG=MESA&O=L" target="content" title="Exibe informações sobre os módulos do sistema."><img src="images/Folder/hlp.gif" border=0></a></TD>');
  ShowHTML('          </table>');
  ShowHTML('      <tr><td height=1><tr><td height=2 bgcolor="#000000">');
  ShowHTML('  </table></CENTER>');
  ShowHTML('  <table border=0 cellpadding=0 height="80" width="100%"><tr><td nowrap><b>');
  ShowHTML('  <div id="container">');
  echo $menu_html_code;
  ShowHTML('  </div>');
  ShowHTML('  </table>');
  ShowHTML('</body>');
  ShowHTML('</html>');
}

// =========================================================================
// Rotina de troca de senha ou assinatura eletrônica
// -------------------------------------------------------------------------
function TrocaSenha() {
  extract($GLOBALS);

  // Recupera os dados do cliente
  $sql = new db_getCustomerData; $RS = $sql->getInstanceOf($dbms, $p_cliente);
  $w_minimo     = f($RS,'tamanho_min_senha');
  $w_maximo     = f($RS,'tamanho_max_senha');
  $w_vigencia   = f($RS,'dias_vig_senha');
  $w_aviso      = f($RS,'dias_aviso_expir');

  // Recupera os dados do usuário
  $sql = new db_getUserData; $RS = $sql->getInstanceOf($dbms, $p_cliente, $_SESSION["USERNAME"]);
  $w_tipo_autenticacao = f($RS,'tipo_autenticacao');

  if ($P1==1) { 
    $w_texto='Senha de Acesso';
    $w_dt_troca = f($RS,'dt_ultima_troca_senha');
  } else { 
    $w_texto='Assinatura Eletrônica'; 
    $w_dt_troca = f($RS,'dt_ultima_troca_assin');
  }
  Cabecalho();
  head();
  Estrutura_CSS($w_cliente);

  if ($P1!=1 || ($P1==1 && $w_tipo_autenticacao=='B')) {
    ScriptOpen('JavaScript');
    ValidateOpen('Validacao');

    Validate('w_atual',$w_texto.' atual','1','1',$w_minimo,$w_maximo,'1','1');
    Validate('w_nova','Nova '.$w_texto,'1','1',$w_minimo,$w_maximo,'1','1');
    Validate('w_conf','Confirmação da '.$w_texto.' atual','1','1',$w_minimo,$w_maximo,'1','1');
    ShowHTML('  if (theForm.w_atual.value == theForm.w_nova.value) { ');
    ShowHTML('     alert(\'A nova '.$w_texto.' deve ser diferente da atual!\');');
    ShowHTML('     theForm.w_nova.value=\'\';');
    ShowHTML('     theForm.w_conf.value=\'\';');
    ShowHTML('     theForm.w_nova.focus();');
    ShowHTML('     return false;');
    ShowHTML('  }');
    ShowHTML('  if (theForm.w_nova.value != theForm.w_conf.value) { ');
    ShowHTML('     alert(\'Favor informar dois valores iguais para a nova '.$w_texto.'!\');');
    ShowHTML('     theForm.w_nova.value=\'\';');
    ShowHTML('     theForm.w_conf.value=\'\';');
    ShowHTML('     theForm.w_nova.focus();');
    ShowHTML('     return false;');
    ShowHTML('  }');
    ShowHTML('  var checkStr = theForm.w_nova.value;');
    ShowHTML('  var temLetra = false;');
    ShowHTML('  var temNumero = false;');
    ShowHTML('  var checkOK = \'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz\';');
    ShowHTML('  for (i = 0;  i < checkStr.length;  i++)');
    ShowHTML('  {');
    ShowHTML('    ch = checkStr.charAt(i);');
    ShowHTML('    for (j = 0;  j < checkOK.length;  j++)');
    ShowHTML('      if (ch == checkOK.charAt(j)) temLetra = true;');
    ShowHTML('  }');
    ShowHTML('  var checkOK = \'0123456789\';');
    ShowHTML('  for (i = 0;  i < checkStr.length;  i++)');
    ShowHTML('  {');
    ShowHTML('    ch = checkStr.charAt(i);');
    ShowHTML('    for (j = 0;  j < checkOK.length;  j++)');
    ShowHTML('      if (ch == checkOK.charAt(j)) temNumero = true;');
    ShowHTML('  }');
    ShowHTML('  if (!(temLetra && temNumero))');
    ShowHTML('  {');
    ShowHTML('    alert(\'A nova '.$w_texto.' deve conter letras e números.\');');
    ShowHTML('    theForm.w_nova.value=\'\';');
    ShowHTML('    theForm.w_conf.value=\'\';');
    ShowHTML('    theForm.w_nova.focus();');
    ShowHTML('    return (false);');
    ShowHTML('  }');
    ShowHTML('  theForm.Botao[0].disabled=true;');
    ShowHTML('  theForm.Botao[1].disabled=true;');
    ValidateClose();
    ScriptClose();
  }
  ShowHTML('</HEAD>');
  if ($w_tipo_autenticacao=='B') BodyOpen('onLoad=\'document.Form.w_atual.focus();\''); else BodyOpen('onLoad=\'document.focus();\''); 
  Estrutura_Topo_Limpo();
  Estrutura_Menu();
  Estrutura_Corpo_Abre();
  Estrutura_Texto_Abre();

  ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
  AbreForm('Form',$w_pagina.'Grava','POST','return(Validacao(this));',null,$P1,$P2,$P3,$P4,$TP,$SG,$w_pagina.$par,$O);
  ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td>');
  ShowHTML('    <table width="100%" border="0">');
  ShowHTML('      <tr><td valign="top">'.$_SESSION['USUARIO'].': <br><b>'.$_SESSION["NOME"].' ('.$_SESSION["USERNAME"].')</b></td>');

  if ($P1!=1 || ($P1==1 && $w_tipo_autenticacao=='B')) {
    // Entra se for troca da assinatura ou se for troca da senha e autenticação no banco
    ShowHTML('      <tr><td valign="top">Ultima troca de '.$w_texto.':<br><b>'.date('d/m/Y, H:i:s',toDate($w_dt_troca)).'</b></td>');
    ShowHTML('      <tr><td valign="top">Expiração da '.$w_texto.' atual ocorrerá em:<br><b>'.date('d/m/Y, H:i:s',addDays(toDate($w_dt_troca),$w_vigencia)).'</b></td>');
    ShowHTML('      <tr><td valign="top">Você será convidado a trocar sua '.$w_texto.' a partir de:<br><b>'.date('d/m/Y, H:i:s',addDays(toDate($w_dt_troca),$w_vigencia-$w_aviso)).'</b></td>');

    ShowHTML('      <tr><td align="center" colspan="3" height="1" bgcolor="#000000"></TD></TR>');
    ShowHTML('      <tr><td valign="top"><b>'.$w_texto.' <U>a</U>tual:<br><INPUT ACCESSKEY="A" class="sti" type="password" name="w_atual" size="'.$w_maximo.'" maxlength="'.$w_maximo.'"></td>');
    ShowHTML('      <tr><td valign="top"><b><U>N</U>ova '.$w_texto.':<br><INPUT ACCESSKEY="N" class="sti" type="password" name="w_nova" size="'.$w_maximo.'" maxlength="'.$w_maximo.'"></td>');
    ShowHTML('      <tr><td valign="top"><b><U>R</U>edigite nova '.$w_texto.':<br><INPUT ACCESSKEY="R" class="sti" type="password" name="w_conf" size="'.$w_maximo.'" maxlength="'.$w_maximo.'"></td>');
    ShowHTML('      <tr><td align="center" colspan="3" height="1" bgcolor="#000000"></TD></TR>');

    ShowHTML('      <tr><td align="center" colspan="3">');
    ShowHTML('            <input class="stb" type="submit" name="Botao" value="Grava nova '.$w_texto.'">');
    ShowHTML('            <input class="stb" type="reset" name="Botao" value="Limpar campos" onClick=\'document.Form.w_atual.focus();\'>');
    ShowHTML('          </td>');
    ShowHTML('      </tr>');
  } else {
    ShowHTML('      <tr><td valign="top"><br><b>ATENÇÃO: sua senha de acesso é igual à sua senha na rede. Por questões de segurança, não é permitido alterá-la nesta tela.</b></b></td>');
  }
  ShowHTML('    </table>');
  ShowHTML('    </TD>');
  ShowHTML('</tr>');
  ShowHTML('</FORM>');
  ShowHTML('</table>');
  ShowHTML('</center>');
  Estrutura_Texto_Fecha();
  Estrutura_Fecha();
  Estrutura_Fecha();
  Estrutura_Fecha();
  Rodape();
}
// =========================================================================
// Rotina de inserção dos dados na tabela SIW_MENU_RELAC
// -------------------------------------------------------------------------
function Vinculacao() {
  extract($GLOBALS);
  global $w_Disabled;
  $w_troca          = $_REQUEST['w_troca'];
  $w_sq_menu        = $_REQUEST['w_sq_menu'];
  $w_sq_tramite     = $_REQUEST['w_sq_tramite'];
  $w_sq_menu_fornec = $_REQUEST['w_sq_menu_fornec'];
  // Monta uma string para indicar a opção selecionada
  $w_texto = opcaoMenu($w_sq_menu);
  Cabecalho();
  head();
  Estrutura_CSS($w_cliente);
  ShowHTML('<TITLE>'.$conSgSistema.' - Configuração de vinculações</TITLE>');
  ScriptOpen('JavaScript');
  ValidateOpen('Validacao');
  if (!(strpos('IAE',$O)===false)) {
    if ($O=='I') {
      Validate('w_sq_menu_fornec', 'Serviço', 'SELECT', '1', '1', '18', null, '1');
      ShowHTML('  var i; ');
      ShowHTML('  var w_erro=true; ');
      ShowHTML('  for (i=0; i < theForm["w_sq_tramite[]"].length; i++) {');
      ShowHTML('    if (theForm["w_sq_tramite[]"][i].checked) w_erro=false;');
      ShowHTML('  }');
      ShowHTML('  if (w_erro) {');
      ShowHTML('    alert(\'Você deve informar pelo menos um trâmite!\'); ');
      ShowHTML('    return false;');
      ShowHTML('  }');
    } 
    Validate('w_assinatura', 'Assinatura Eletrônica', '1', '1', '6', '30', '1', '1');
    ShowHTML('  theForm.Botao[0].disabled=true;');
    ShowHTML('  theForm.Botao[1].disabled=true;');
  } 

  ValidateClose();
  ScriptClose();
  ShowHTML('</HEAD>');
  if ($O=='I')      BodyOpen('onLoad=document.Form.w_sq_menu_fornec.focus();');
  elseif ($O=='E')  BodyOpen('onLoad=document.Form.w_assinatura.focus();');
  else              BodyOpen(null);
  Estrutura_Topo_Limpo();
  Estrutura_Menu();
  Estrutura_Corpo_Abre();
  Estrutura_Texto_Abre();
  ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
  ShowHTML('<tr><td colspan=3 bgcolor="#FAEBD7"><table border=1 width="100%"><tr><td>');
  ShowHTML('    <TABLE WIDTH="100%" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
  ShowHTML('        <tr valign="top">');
  ShowHTML('          <td><font size="1">Opção:<br><b><font size=1 class="hl">'.substr($w_texto,0,strlen($w_texto)-4).'</font></b></td>');
  ShowHTML('    </TABLE>');
  ShowHTML('</TABLE>');
  ShowHTML('  <tr><td>&nbsp;');
  if ($O=='L') {
    $sql = new db_getMenuRelac; $RS = $sql->getInstanceOf($dbms, $w_sq_menu, null, null, null, null);
    ShowHTML('<tr><td>');
    ShowHTML('    <a accesskey="I" class="ss" href="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=I&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'&w_sq_menu='.$w_sq_menu.'"><u>I</u>ncluir</a>&nbsp;');
    ShowHTML('    <a class="ss" HREF="javascript:this.status.value;" onClick="opener.focus(); window.close();">Fechar</a>&nbsp;');
    ShowHTML('    <td align="right">'.exportaOffice().'<b>Registros: '.count($RS));
    ShowHTML('<tr><td align="center" colspan=2>');
    ShowHTML('    <TABLE class="tudo" WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
    ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center" valign="top">');
    ShowHTML('          <td><b>Serviço</td>');
    ShowHTML('          <td class="remover"><b>Operações</td>');
    ShowHTML('          <td><b>Trâmites</td>');
    ShowHTML('        </tr>');
    $w_cont='';
    if (count($RS)<=0) {
      ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td colspan=6 align="center"><font size="2"><b>Não foram encontrados registros.</b></td></tr>');
    } else {
      foreach($RS as $row) {
        // Se for quebra de endereço, exibe uma linha com o endereço
        if ($w_cont!=f($row,'nm_servico_fornecedor')) {
          ShowHTML('      <tr bgcolor="'.$conTrBgColor.'" valign="top">');
          ShowHTML('        <td>'.f($row,'nm_servico_fornecedor').' ('.f($row,'nm_modulo_fornecedor').')</td>');
          $w_cont=f($row,'nm_servico_fornecedor');
          ShowHTML('        <td class="remover">');
          ShowHTML('          <A class="hl" HREF="'.$w_pagina.$par.'&R='.$w_pagina.$par.'&O=A&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'&w_sq_menu='.$w_sq_menu.'&w_sq_tramite='.f($row,'sq_siw_tramite').'&w_sq_menu_fornec='.f($row,'servico_fornecedor').'">AL</A>&nbsp');
          ShowHTML('&nbsp');
          ShowHTML('        </td>');
        } else {
          ShowHTML('      <tr bgcolor="'.$conTrBgColor.'" valign="top">');
          ShowHTML('        <td align="center"></td>');
          ShowHTML('        <td align="center"></td>');
        } 
        ShowHTML('        <td>'.f($row,'nm_tramite').'</td>');
        ShowHTML('      </tr>');
      } 
    } 

    ShowHTML('      </center>');
    ShowHTML('    </table>');
    ShowHTML('  </table>');
  } else {
    if($O=='A')$w_Disabled='DISABLED';
    $sql = new db_getMenuRelac; $RS = $sql->getInstanceOf($dbms, $w_sq_menu, null, null, null, null);
    $i=0;
    foreach($RS as $row) {
      if ($i==0) $w_sq_tramite = f($row,'sq_siw_tramite');
      else       $w_sq_tramite .= ','.f($row,'sq_siw_tramite');
      $i=1;
    }
    ShowHTML('      <tr><td align="justify" colspan="2"><font size=2>Informe os serviços e os trâmites aos quais esse serviço poderá ser vinculado.</font></td></tr>');
    ShowHTML('      <tr><td align="center" colspan="2" height="2" bgcolor="#000000">');
    ShowHTML('      <tr><td colspan="2"><font size=2><b>');
    AbreForm('Form',$w_pagina.'Grava', 'POST', 'return(Validacao(this));', null,$P1,$P2,$P3,$P4,$TP,$SG,$R,$O);
    ShowHTML('<INPUT type="hidden" name="w_troca" value="">');
    ShowHTML('<INPUT type="hidden" name="w_sq_menu" value="'.$w_sq_menu.'">');
    if($O=='A') ShowHTML('<INPUT type="hidden" name="w_sq_menu_fornec" value="'.$w_sq_menu_fornec.'">');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td align="center">');
    ShowHTML('    <table width="90%" border="0">');
    ShowHTML('      <tr valign="top">');
    selecaoServico('<U>S</U>erviço:', 'S', null, $w_sq_menu_fornec, $w_sq_menu, null, 'w_sq_menu_fornec', null, 'onChange="document.Form.action=\''.$w_pagina.$par.'\'; document.Form.O.value=\''.$O.'\'; document.Form.w_troca.value=\'w_sq_menu_fornec\'; document.Form.submit();"', null, null, null);
    SelecaoFaseCheck('<u>T</u>râmites','T',null,$w_sq_tramite,$w_sq_menu_fornec,'w_sq_tramite[]','MENURELAC',null);
    ShowHTML('      <tr><td colspan=2><b><U>A</U>ssinatura Eletrônica:<br><INPUT ACCESSKEY="A" class="sti" type="PASSWORD" name="w_assinatura" size="30" maxlength="30" value=""></td>');
    ShowHTML('      </table>');
    ShowHTML('      <tr><td align="center" colspan="2" height="1" bgcolor="#000000">');
    ShowHTML('      <tr><td align="center" colspan="2">');
    if ($O=='E') {
      ShowHTML('            <input class="stb" type="submit" name="Botao" value="Excluir">');
    } else {
      ShowHTML('            <input class="stb" type="submit" name="Botao" value="Gravar">');
    } 

    ShowHTML('            <input class="stb" type="button" onClick="location.href=\''.$w_pagina.$par.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'&w_sq_menu='.$w_sq_menu.'&O=L\';" name="Botao" value="Cancelar">');
    ShowHTML('          </td>');
    ShowHTML('      </tr>');
    ShowHTML('    </table>');
    ShowHTML('    </TD>');
    ShowHTML('</tr>');
    ShowHTML('</FORM>');
  } 

  ShowHTML('  </td>');
  ShowHTML('</tr>');
  ShowHTML('</table>');
  Estrutura_Texto_Fecha();
  Estrutura_Fecha();
  Estrutura_Fecha();
  Estrutura_Fecha();
  Rodape();
} 
// =========================================================================
// Procedimento que executa as operações de BD
// -------------------------------------------------------------------------
function Grava() {
  extract($GLOBALS);

  Cabecalho();
  ShowHTML('</HEAD>');
  BodyOpen('onLoad=this.focus();');

  switch ($SG) {
  case 'SGSENHA':
    if (VerificaSenhaAcesso($_SESSION['USERNAME'],upper($_REQUEST['w_atual']))) {
       $SQL = new db_updatePassword; $SQL->getInstanceOf($dbms,$w_cliente,$_SESSION["SQ_PESSOA"],$_REQUEST["w_nova"],'PASSWORD');
       ScriptOpen('JavaScript');
       ShowHTML('  alert(\'Senha de Acesso alterada com sucesso!\');');
       ScriptClose();
    } else {
       ScriptOpen('JavaScript');
       ShowHTML('  alert(\'Senha de Acesso atual inválida!\');');
       ScriptClose();
       retornaFormulario('w_atual');
    } break;
  case 'SGASSINAT':
    if (VerificaAssinaturaEletronica($_SESSION['USERNAME'],upper($_REQUEST['w_atual']))) {
       $SQL = new db_updatePassword; $SQL->getInstanceOf($dbms,$w_cliente,$_SESSION["SQ_PESSOA"],$_REQUEST["w_nova"],'SIGNATURE');
       ScriptOpen('JavaScript');
       ShowHTML('  alert(\'Assinatura Eletrônica alterada com sucesso!\');');
       ScriptClose();
    } else {
       ScriptOpen('JavaScript');
       ShowHTML('  alert(\'Assinatura Eletrônica atual inválida!\');');
       ScriptClose();
       retornaFormulario('w_atual');
    } break;
  case "SIWMENURELAC":
    // Verifica se a Assinatura Eletrônica é válida
    if (VerificaAssinaturaEletronica($_SESSION['USERNAME'],upper($_REQUEST['w_assinatura'])) || $w_assinatura=='') {
      // Elimina todas as permissões existentes para depois incluir
      $SQL = new dml_PutMenuRelac; 
      $SQL->getInstanceOf($dbms, 'E', $_REQUEST['w_sq_menu'],$_REQUEST['w_sq_menu_fornec'], null);
      for ($i=0; $i<=count($_POST['w_sq_tramite'])-1; $i=$i+1)   {
        $SQL->getInstanceOf($dbms, 'I', $_REQUEST['w_sq_menu'], $_REQUEST['w_sq_menu_fornec'], $_POST['w_sq_tramite'][$i]);
      } 
      ScriptOpen('JavaScript');
      ShowHTML('  location.href=\''.$R.'&O=L&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.'&w_sq_menu='.$_REQUEST['w_sq_menu'].'\';');
      ScriptClose();
    } else {
      ScriptOpen('JavaScript');
      ShowHTML('  alert(\'Assinatura Eletrônica inválida!\');');
      ScriptClose();
     retornaFormulario('w_assinatura');
    } break;
  default:
    ScriptOpen('JavaScript');
    ShowHTML('  alert(\'Bloco de dados não encontrado: '.$SG.'\');');
    ShowHTML('  history.back(1);');
    ScriptClose();
    break;
  }
  // Aqui deve ser usada a variável de sessão para evitar erro na recuperação do link
  $sql = new db_getLinkData; $RS1 = $sql->getInstanceOf($dbms, $_SESSION["P_CLIENTE"], 'MESA');
  ScriptOpen('JavaScript');
  if (f(RS1,'IMAGEM') > '')
     ShowHTML('  location.href=\''.f($RS1,'LINK').'&P1='.f($RS1,'P1').'&P2='.f($RS1,'P2').'&P3='.f($RS1,'P3').'&P4='.f($RS1,'P4').'&TP=<img src='.f($RS1,'IMAGEM').' BORDER=0>'.f($RS1,'NOME').'&SG='.f($RS1,'SIGLA').'\'; ');
  else
     ShowHTML('  location.href=\''.f($RS1,'LINK').'&P1='.f($RS1,'P1').'&P2='.f($RS1,'P2').'&P3='.f($RS1,'P3').'&P4='.f($RS1,'P4').'&TP=<img src='.$w_ImagemPadrao.' BORDER=0>'.f($RS1,'NOME').'&SG='.f($RS1,'SIGLA').'\'; ');

  ScriptClose();
}

// =========================================================================
// Rotina de encerramento da sessão
// -------------------------------------------------------------------------
function Sair() {
  extract($GLOBALS);
  $sql = new db_getCustomerSite; $RS = $sql->getInstanceOf($dbms, $p_cliente);

  // Se a geração de log estiver ativada, registra.
  if ($conLog) {
    // Define o caminho fisico do diretório e do arquivo de log
    $l_caminho = $conLogPath;
    $l_arquivo = $l_caminho.$_SESSION['P_CLIENTE'].'/'.date(Ymd).'.log';

    // Verifica a necessidade de criação dos diretórios de log
    if (!file_exists($l_caminho)) mkdir($l_caminho);
    if (!file_exists($l_caminho.$_SESSION['P_CLIENTE'])) mkdir($l_caminho.$_SESSION['P_CLIENTE']);
      
    // Abre o arquivo de log
    $l_log = @fopen($l_arquivo, 'a');
      
    fwrite($l_log, '['.date(ymd.'_'.Gis.'_'.time()).']'.$crlf);
    fwrite($l_log, $_SESSION['USUARIO'].': '.$_SESSION['NOME_RESUMIDO'].' ('.$_SESSION['SQ_PESSOA'].')'.$crlf);
    fwrite($l_log, 'IP     : '.$_SERVER['REMOTE_ADDR'].$crlf);
    fwrite($l_log, 'Ação   : LOGOUT'.$crlf.$crlf);

    // Fecha o arquivo e o diretório de log
    @fclose($l_log);
    @closedir($l_caminho); 
  }

  // Registra no servidor syslog
  $w_resultado = enviaSyslog('LV','LOGOUT','('.$_SESSION['SQ_PESSOA'].') '.$_SESSION['NOME_RESUMIDO']);
  if ($w_resultado>'') {
    ScriptOpen('JavaScript');
    ShowHTML('  alert(\''.$w_resultado.'\');');
    ScriptClose();
  }

  // Eliminar todas as variáveis de sessão.
  $_SESSION = array();
  // Finalmente, destruição da sessão.
  session_destroy();

  ScriptOpen('JavaScript');
  ShowHTML('  top.location.href=\''.f($RS,'logradouro').'\';');

  ScriptClose();
}

// =========================================================================
// Rotina principal
// -------------------------------------------------------------------------
function Main() {
  extract($GLOBALS);

  switch ($par) {
  case 'GRAVA':         Grava(); break;
  case 'TROCASENHA':    TrocaSenha(); break;
  case 'FRAMES':        Frames(); break;
  case 'EXIBEDOCS':     ExibeDocs(); break;
  case 'SAIR':          Sair(); break;
  case 'VINCULACAO':    Vinculacao(); break;
  default:
    Cabecalho();
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
