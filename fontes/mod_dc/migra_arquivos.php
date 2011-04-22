<?php
header('Expires: '.-1500);
session_start();
$w_dir_volta = '../';
include_once($w_dir_volta.'constants.inc');
include_once($w_dir_volta.'jscript.php');
include_once($w_dir_volta.'funcoes.php');

// =========================================================================
//  migra_arquivos.php
// ------------------------------------------------------------------------
// Nome     : Alexandre Vinhadelli Papadópolis
// Descricao: Migra procedures Oracle para PGSQL
// Mail     : alex@sbpi.com.br
// Criacao  : 22/01/11, 17:55
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

$w_assinatura   = upper($_REQUEST['w_assinatura']);
$w_pagina       = 'migra_arquivos.php?par=';
$w_Disabled     = 'ENABLED';
$w_dir          = 'mod_dc/';
$w_troca        = $_REQUEST['w_troca'];

if ($O=='') {
  $O='L';
} 

switch ($O) {
  case 'I': $w_TP=$TP.' - Inclusão';    break;
  case 'A': $w_TP=$TP.' - Alteração';   break;
  case 'E': $w_TP=$TP.' - Exclusão';    break;
  case 'P': $w_TP=$TP.' - Filtragem';   break;
  case 'C': $w_TP=$TP.' - Cópia';       break;
  case 'V': $w_TP=$TP.' - Envio';       break;
  case 'H': $w_TP=$TP.' - Herança';     break;
  default:  $w_TP=$TP.' - Listagem';    break;
} 

if (nvl($_REQUEST['w_raiz'],'') > '') {
  $w_raiz         = str_replace('\\','/',str_replace('//','/',trim($_REQUEST['w_raiz']).'/'));
}

if (nvl($_REQUEST['w_caminho'],'') > '') {
  $w_caminho         = str_replace('\\','/',str_replace('//','/',trim($_REQUEST['w_caminho']).'/'));
}

Main();

exit;

// =========================================================================
// Rotina de cadastramento da outra parte
// -------------------------------------------------------------------------
function Inicial() {
  extract($GLOBALS);
  global $w_Disabled;

  if ($O=='') $O='P';

  $w_erro           = '';
  $w_chave          = $_REQUEST['w_chave'];
  $w_chave_aux      = $_REQUEST['w_chave_aux'];

  if ($w_raiz=='' && (strpos($_REQUEST['botao'],'Selecionar')===false)) {
    $w_raiz    = f($RS,'sq_prop');
    $w_pessoa_atual = f($RS,'sq_prop');
  } elseif (strpos($_REQUEST['botao'],'Selecionar')===false) {
    $w_diretorio       = f($RS,'w_diretorio');
  } 
  if (Nvl($w_raiz,0)==0) $O='I'; else $O='A';
  // Verifica se há necessidade de recarregar os dados da tela a partir
  // da própria tela (se for recarga da tela) ou do banco de dados (se não for inclusão)
  if ($w_troca>'') {
    // Se for recarga da página
    $w_diretorio            = $_REQUEST['w_diretorio'];
  } 
  Cabecalho();
  head();
  ShowHTML('  <!-- CSS FILE for my tree-view menu -->');
  ShowHTML('  <link rel="stylesheet" type="text/css" href="'.$w_dir_volta.'classes/menu/xPandMenu.css">');
  ShowHTML('  <!-- JS FILE for my tree-view menu -->');
  ShowHTML('  <script src="'.$w_dir_volta.'classes/menu/xPandMenu.js"></script>');
  Estrutura_CSS($w_cliente);
  // Monta o código JavaScript necessário para validação de campos e preenchimento automático de máscara,
  // tratando as particularidades de cada serviço
  ScriptOpen('JavaScript');
  ValidateOpen('Validacao');
  if ($w_raiz=='' || !(strpos($_REQUEST['botao'],'Alterar')===false)) {
    Validate('w_raiz','Diretório raiz','1','1','4','80','1','1');
    ShowHTML('  theForm.Botao.disabled=true;');
  } else {
    Validate('w_caminho','Diretório destino','1','1','4','80','1','1');
    Validate('w_extensao','Extensão','1','1','2','20','ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890, ',null);
    ShowHTML('  theForm.Botao[0].disabled=true;');
    ShowHTML('  theForm.Botao[1].disabled=true;');
  } 
  ValidateClose();
  ScriptClose();
  ShowHTML('</HEAD>');
  ShowHTML('<BASE HREF="'.$conRootSIW.'">');
  if ($w_raiz=='' || !(strpos($_REQUEST['botao'],'Procurar')===false)) {
    // Se o diretório raiz de busca
    if (!(strpos($_REQUEST['botao'],'Procurar')===false)) {
      // Se está sendo feita busca por nome
      BodyOpen('onLoad=\'this.focus()\';');
    } else {
      BodyOpen('onLoad=\'document.Form.w_raiz.focus()\';');
    } 
  } elseif ($w_troca>'') {
    BodyOpen('onLoad=\'document.Form.'.$w_troca.'.focus()\';');
  } else {
    BodyOpen('onLoad=\'this.focus()\';');
  } 
  Estrutura_Topo_Limpo();
  Estrutura_Menu();
  Estrutura_Corpo_Abre();
  Estrutura_Texto_Abre();
  ShowHTML('<table align="center" border="0" cellpadding="0" cellspacing="0" width="100%">');
  if (!(strpos('IA',$O)===false)) {
    if ($w_raiz=='') {
      // Se o beneficiário ainda não foi selecionado
      ShowHTML('<FORM action="'.$w_dir.$w_pagina.$par.'" method="POST" name="Form" onSubmit="return(Validacao(this));">');
    } else {
      ShowHTML('<FORM action="'.$w_dir.$w_pagina.'Grava" method="POST" name="Form" onSubmit="return(Validacao(this));">');
    } 
    ShowHTML('<INPUT type="hidden" name="P1" value="'.$P1.'">');
    ShowHTML('<INPUT type="hidden" name="P2" value="'.$P2.'">');
    ShowHTML('<INPUT type="hidden" name="P3" value="'.$P3.'">');
    ShowHTML('<INPUT type="hidden" name="P4" value="'.$P4.'">');
    ShowHTML('<INPUT type="hidden" name="TP" value="'.$TP.'">');
    ShowHTML('<INPUT type="hidden" name="SG" value="MIGRA">');
    ShowHTML('<INPUT type="hidden" name="R" value="'.$w_pagina.$par.'">');
    ShowHTML('<INPUT type="hidden" name="O" value="'.$O.'">');
    ShowHTML('<INPUT type="hidden" name="w_troca" value="">');
    if ($w_raiz=='') {
      ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td>');
      ShowHTML('    <table border="0">');
      ShowHTML('        <tr><td>Informe o diretório raiz de busca e clique no botão "Selecionar" para continuar.</TD>');
      ShowHTML('        <tr><td><b><u>D</u>iretório raiz:<br><INPUT ACCESSKEY="D" TYPE="text" class="sti" NAME="w_raiz" VALUE="'.$w_raiz.'" SIZE="80" MaxLength="80">');
      ShowHTML('        <tr><td><INPUT class="STB" TYPE="submit" NAME="Botao" VALUE="Selecionar">');
      ShowHTML('      </table>');
    } else {
      ShowHTML('<INPUT type="hidden" name="w_raiz" value="'.$w_raiz.'">');
      ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td>');
      ShowHTML('    <table width="100%" border="0">');
      ShowHTML('      <tr><td colspan="2" align="center" height="2" bgcolor="#000000"></td></tr>');
      ShowHTML('      <tr><td colspan="2" align="center" height="1" bgcolor="#000000"></td></tr>');
      ShowHTML('      <tr><td colspan="2" align="center" bgcolor="#D0D0D0"><b>Configuração dos parâmetros de execução</td></td></tr>');
      ShowHTML('      <tr><td colspan="2" align="center" height="1" bgcolor="#000000"></td></tr>');
      ShowHTML('      <tr><td colspan="2"><b>Diretório onde deverão ser gerados os arquivos:</b><br><INPUT TYPE="text" class="STI" NAME="w_caminho" VALUE="d:\temp" SIZE="80" MaxLength="80">');
      ShowHTML('      <tr><td colspan="2"><b>Aplicar busca a arquivos com a extensão:</b> (informe as extensões com letras maiúsculas e separe-as com vírgulas)<br><INPUT TYPE="text" class="STI" NAME="w_extensao" VALUE="'.nvl($w_extensao,'PRC, FNC, SQL, TRG').'" SIZE="80" MaxLength="20">');

      ShowHTML('      <tr><td colspan="2" align="center" height="2" bgcolor="#000000"></td></tr>');
      ShowHTML('      <tr><td colspan="2" align="center" height="1" bgcolor="#000000"></td></tr>');
      ShowHTML('      <tr><td colspan="2" align="center" bgcolor="#D0D0D0"><b>Seleção de diretórios</td></td></tr>');
      ShowHTML('      <tr><td colspan="2" align="center" height="1" bgcolor="#000000"></td></tr>');
      ShowHTML('      <tr><td colspan="2"><table border="0" width="100%">');
      ShowHTML('          <tr valign="top"><td>');
      
      // verifica se o diretório informado atende aos requisitos de existência, leitura e escrita
      if (!testFile(&$l_erro, $w_raiz, true, true)) {
        ScriptOpen('JavaScript');
        ShowHTML('  alert(\'ATENÇÃO: diretório '.$l_erro.'!\');');
        ShowHTML('  location.href=\''.montaURL_JS($w_dir,$R.'&O='.$O.'&SG='.$SG.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.MontaFiltro('GET')).'\';');
        ScriptClose();
        exit();
      }
      
      // Recupera a estrutura do diretório informado
      $estrutura = retrieveTree($w_raiz, true);
      
      if (is_array($estrutura)) {
        // Inclusão do arquivo da classe
        include_once($w_dir_volta.'classes/menu/xPandMenu.php');
        $w_ImagemPadrao='images/Folder/SheetLittle.gif';

        // Instanciando a classe menu
        $root = new XMenu();

        // Inicializa contadores para identificação dos níveis da árvore
        $n1 = 0;
        $n2 = 0;
        $n3 = 0;
        $n4 = 0;
        $n5 = 0;
        $n6 = 0;
        $n7 = 0;
        $n8 = 0;
        $n9 = 0;

        // Cria o nó raiz
        eval('$noderaiz = &$root->addItem(new XNode(\'<input type="checkbox" class="STC" name="w_dir[]" value="\'.$w_raiz.\'" checked>\'. $w_raiz.\' (\'.count($estrutura, COUNT_RECURSIVE).\')\',false,LEAF_DEFAULT_IMG,LEAF_DEFAULT_ALT_IMG,null,null,true));');
        
        // Monta a árvore da estrutura
        foreach ($estrutura as $k1 => $v1) {
          if (is_array($v1)) {
            $n1 += 1;
            // se tiver subdiretórios, não coloca checkbox
            if (count($v1) == count($v1, COUNT_RECURSIVE)) {
              $w_valor = $w_raiz.$k1;
              eval('$node'.$n1.' = &$noderaiz->addItem(new XNode(\'<input type="checkbox" class="STC" name="w_dir[]" value="\'.$w_valor.\'" checked>\'. $k1.\' (\'.count($v1, COUNT_RECURSIVE).\')\',false,LEAF_DEFAULT_IMG,LEAF_DEFAULT_ALT_IMG,null,null,true));');
            } else {
              eval('$node'.$n1.' = &$noderaiz->addItem(new XNode($k1.\' (\'.count($v1, COUNT_RECURSIVE).\')\',false,LEAF_DEFAULT_IMG,LEAF_DEFAULT_ALT_IMG,null,null,true));');
            }
            foreach ($v1 as $k2 => $v2) {
              if (is_array($v2)) {
                $n2 += 1;
                if (count($v2) == count($v2, COUNT_RECURSIVE)) {
                  $w_valor = $w_raiz.$k1.'/'.$k2;
                  eval('$node'.$n1.'_'.$n2.' = &$node'.$n1.'->addItem(new XNode(\'<input type="checkbox" class="STC" name="w_dir[]" value="\'.$w_valor.\'" checked>\'. $k2.\' (\'.count($v2, COUNT_RECURSIVE).\')\',false,LEAF_DEFAULT_IMG,LEAF_DEFAULT_ALT_IMG,null,null,true));');
                } else {
                  eval('$node'.$n1.'_'.$n2.' = &$node'.$n1.'->addItem(new XNode($k2.\' (\'.count($v2, COUNT_RECURSIVE).\')\',false,LEAF_DEFAULT_IMG,LEAF_DEFAULT_ALT_IMG,null,null,true));');
                }
                foreach ($v2 as $k3 => $v3) {
                  if (is_array($v3)) {
                    $n3 += 1;
                    if (count($v3) == count($v3, COUNT_RECURSIVE)) {
                      $w_valor = $w_raiz.$k1.'/'.$k2.'/'.$k3;
                      eval('$node'.$n1.'_'.$n2.'_'.$n3.' = &$node'.$n1.'_'.$n2.'->addItem(new XNode(\'<input type="checkbox" class="STC" name="w_dir[]" value="\'.$w_valor.\'" checked>\'. $k3.\' (\'.count($v3, COUNT_RECURSIVE).\')\',false,LEAF_DEFAULT_IMG,LEAF_DEFAULT_ALT_IMG,null,null,true));');
                    } else {
                      eval('$node'.$n1.'_'.$n2.'_'.$n3.' = &$node'.$n1.'_'.$n2.'->addItem(new XNode($k3.\' (\'.count($v3, COUNT_RECURSIVE).\')\',false,LEAF_DEFAULT_IMG,LEAF_DEFAULT_ALT_IMG,null,null,true));');
                    }
                    foreach ($v3 as $k4 => $v4) {
                      if (is_array($v4)) {
                        $n4 += 1;
                        if (count($v4) == count($v4, COUNT_RECURSIVE)) {
                          $w_valor = $w_raiz.$k1.'/'.$k2.'/'.$k3.'/'.$k4;
                          eval('$node'.$n1.'_'.$n2.'_'.$n3.'_'.$n4.' = &$node'.$n1.'_'.$n2.'_'.$n3.'->addItem(new XNode(\'<input type="checkbox" class="STC" name="w_dir[]" value="\'.$w_valor.\'" checked>\'. $k4.\' (\'.count($v4, COUNT_RECURSIVE).\')\',false,LEAF_DEFAULT_IMG,LEAF_DEFAULT_ALT_IMG,null,null,true));');
                        } else {
                          eval('$node'.$n1.'_'.$n2.'_'.$n3.'_'.$n4.' = &$node'.$n1.'_'.$n2.'_'.$n3.'->addItem(new XNode($k4.\' (\'.count($v4, COUNT_RECURSIVE).\')\',false,LEAF_DEFAULT_IMG,LEAF_DEFAULT_ALT_IMG,null,null,true));');
                        }
                        foreach ($v4 as $k5 => $v5) {
                          if (is_array($v5)) {
                            $n5 += 1;
                            if (count($v5) == count($v5, COUNT_RECURSIVE)) {
                              $w_valor = $w_raiz.$k1.'/'.$k2.'/'.$k3.'/'.$k4.'/'.$k5;
                              eval('$node'.$n1.'_'.$n2.'_'.$n3.'_'.$n4.'_'.$n5.' = &$node'.$n1.'_'.$n2.'_'.$n3.'_'.$n4.'->addItem(new XNode(\'<input type="checkbox" class="STC" name="w_dir[]" value="\'.$w_valor.\'" checked>\'. $k5.\' (\'.count($v5, COUNT_RECURSIVE).\')\',false,LEAF_DEFAULT_IMG,LEAF_DEFAULT_ALT_IMG,null,null,true));');
                            } else {
                              eval('$node'.$n1.'_'.$n2.'_'.$n3.'_'.$n4.'_'.$n5.' = &$node'.$n1.'_'.$n2.'_'.$n3.'_'.$n4.'->addItem(new XNode($k5.\' (\'.count($v5, COUNT_RECURSIVE).\')\',false,LEAF_DEFAULT_IMG,LEAF_DEFAULT_ALT_IMG,null,null,true));');
                            }
                            foreach ($v5 as $k6 => $v6) {
                              if (is_array($v6)) {
                                $n6 += 1;
                                if (count($v6) == count($v6, COUNT_RECURSIVE)) {
                                  $w_valor = $w_raiz.$k1.'/'.$k2.'/'.$k3.'/'.$k4.'/'.$k5.'/'.$k6;
                                  eval('$node'.$n1.'_'.$n2.'_'.$n3.'_'.$n4.'_'.$n5.'_'.$n6.' = &$node'.$n1.'_'.$n2.'_'.$n3.'_'.$n4.'_'.$n5.'->addItem(new XNode(\'<input type="checkbox" class="STC" name="w_dir[]" value="\'.$w_valor.\'" checked>\'. $k6.\' (\'.count($v6, COUNT_RECURSIVE).\')\',false,LEAF_DEFAULT_IMG,LEAF_DEFAULT_ALT_IMG,null,null,true));');
                                } else {
                                  eval('$node'.$n1.'_'.$n2.'_'.$n3.'_'.$n4.'_'.$n5.'_'.$n6.' = &$node'.$n1.'_'.$n2.'_'.$n3.'_'.$n4.'_'.$n5.'->addItem(new XNode($k6.\' (\'.count($v6, COUNT_RECURSIVE).\')\',false,LEAF_DEFAULT_IMG,LEAF_DEFAULT_ALT_IMG,null,null,true));');
                                }
                                foreach ($v6 as $k7 => $v7) {
                                  if (is_array($v7)) {
                                    $n7 += 1;
                                    if (count($v7) == count($v7, COUNT_RECURSIVE)) {
                                      $w_valor = $w_raiz.$k1.'/'.$k2.'/'.$k3.'/'.$k4.'/'.$k5.'/'.$k6.'/'.$k7;
                                      eval('$node'.$n1.'_'.$n2.'_'.$n3.'_'.$n4.'_'.$n5.'_'.$n6.'_'.$n7.' = &$node'.$n1.'_'.$n2.'_'.$n3.'_'.$n4.'_'.$n5.'_'.$n6.'->addItem(new XNode(\'<input type="checkbox" class="STC" name="w_dir[]" value="\'.$w_valor.\'" checked>\'. $k7.\' (\'.count($v7, COUNT_RECURSIVE).\')\',false,LEAF_DEFAULT_IMG,LEAF_DEFAULT_ALT_IMG,null,null,true));');
                                    } else {
                                      eval('$node'.$n1.'_'.$n2.'_'.$n3.'_'.$n4.'_'.$n5.'_'.$n6.'_'.$n7.' = &$node'.$n1.'_'.$n2.'_'.$n3.'_'.$n4.'_'.$n5.'_'.$n6.'->addItem(new XNode($k7.\' (\'.count($v7, COUNT_RECURSIVE).\')\',false,LEAF_DEFAULT_IMG,LEAF_DEFAULT_ALT_IMG,null,null,true));');
                                    }
                                    foreach ($v7 as $k8 => $v8) {
                                      if (is_array($v8)) {
                                        $n8 += 1;
                                        if (count($v8) == count($v8, COUNT_RECURSIVE)) {
                                          $w_valor = $w_raiz.$k1.'/'.$k2.'/'.$k3.'/'.$k4.'/'.$k5.'/'.$k6.'/'.$k7.'/'.$k8;
                                          eval('$node'.$n1.'_'.$n2.'_'.$n3.'_'.$n4.'_'.$n5.'_'.$n6.'_'.$n7.'_'.$n8.' = &$node'.$n1.'_'.$n2.'_'.$n3.'_'.$n4.'_'.$n5.'_'.$n6.'_'.$n7.'->addItem(new XNode(\'<input type="checkbox" class="STC" name="w_dir[]" value="\'.$w_valor.\'" checked>\'. $k8.\' (\'.count($v8, COUNT_RECURSIVE).\')\',false,LEAF_DEFAULT_IMG,LEAF_DEFAULT_ALT_IMG,null,null,true));');
                                        } else {
                                          eval('$node'.$n1.'_'.$n2.'_'.$n3.'_'.$n4.'_'.$n5.'_'.$n6.'_'.$n7.'_'.$n8.' = &$node'.$n1.'_'.$n2.'_'.$n3.'_'.$n4.'_'.$n5.'_'.$n6.'_'.$n7.'->addItem(new XNode($k8.\' (\'.count($v8, COUNT_RECURSIVE).\')\',false,LEAF_DEFAULT_IMG,LEAF_DEFAULT_ALT_IMG,null,null,true));');
                                        }
                                        foreach ($v8 as $k9 => $v9) {
                                          if (is_array($v9)) {
                                            $n9 += 1;
                                            $w_valor = $w_raiz.$k1.'/'.$k2.'/'.$k3.'/'.$k4.'/'.$k5.'/'.$k6.'/'.$k7.'/'.$k8.'/'.$k9;
                                            eval('$node'.$n1.'_'.$n2.'_'.$n3.'_'.$n4.'_'.$n5.'_'.$n6.'_'.$n7.'_'.$n8.'_'.$n9.' = &$node'.$n1.'_'.$n2.'_'.$n3.'_'.$n4.'_'.$n5.'_'.$n6.'_'.$n7.'_'.$n8.'->addItem(new XNode(\'<input type="checkbox" class="STC" name="w_dir[]" value="\'.$w_valor.\'" checked>\'. $k9.\' (\'.count($v9, COUNT_RECURSIVE).\')\',false,LEAF_DEFAULT_IMG,LEAF_DEFAULT_ALT_IMG,null,null,true));');
                                          }
                                        }
                                      }
                                    }
                                  }
                                }
                              }
                            }
                          }
                        }
                      }
                    }
                  }
                }
              }
            }
          }
        }
        ShowHTML($root->generateTree());
      }

      ShowHTML('          </table>');
      ShowHTML('      <tr><td align="center" colspan="3" height="1" bgcolor="#000000"></TD></TR>');
      ShowHTML('      <tr><td align="center" colspan="3">');
      ShowHTML('            <input class="stb" type="submit" name="Botao" value="Executar" onClick="Botao.value=this.value;">');
      ShowHTML('            <input class="stb" type="submit" name="Botao" value="Alterar raiz" onClick="Botao.value=this.value; document.Form.w_raiz.value=\'\'; document.Form.action=\''.$w_dir.$w_pagina.$par.'\'; document.Form.submit();">');
      ShowHTML('          </td>');
      ShowHTML('      </tr>');
      ShowHTML('    </table>');
      ShowHTML('    </TD>');
      ShowHTML('</tr>');
    } 
    ShowHTML('</FORM>');
  } else {
    ScriptOpen('JavaScript');
    ShowHTML(' alert(\'Opção não disponível\');');
    ShowHTML(' history.back(1);');
    ScriptClose();
  } 
  ShowHTML('</table>');
  ShowHTML('</center>');
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

  $w_file       = '';
  $w_tamanho    = '';
  $w_tipo       = '';
  $w_nome       = '';
  $w_opcao      = nvl($_REQUEST['w_opcao'],'N');
  Cabecalho();
  ShowHTML('</HEAD>');
  ShowHTML('<BASE HREF="'.$conRootSIW.'">');
  BodyOpen('onLoad=this.focus();');
  switch ($SG) {
    case 'MIGRA':
      // Verifica se a Assinatura Eletrônica é válida
      $cont = 0;
      // Para cada diretório indicado, verifica e trata os arquivos
      for ($i=0; $i<=count($_REQUEST['w_dir'])-1; $i += 1)   {
        // Informa o diretório em execução
        if (!$i) {
          if ($w_opcao=='nomes') {
          // Carrega array com todos os arquivos do diretório raiz, para poder testar nomes inválidos
          $lista = retrieveTree($_REQUEST['w_raiz'],true, true);
          array_walk_recursive($lista,'flatArray',&$base);
          }
          ShowHTML('<b>Processando a lista de diretórios selecionados</b>');
          ShowHTML('<DL>');
        }
        ShowHTML('<DT><b>'.$_REQUEST['w_dir'][$i].'</b>');
        // Recupera a lista de arquivos do diretório
        $arquivos = retrieveTree($_REQUEST['w_dir'][$i]);
        foreach($arquivos as $k => $v) {
          // verifica se a extensão do arquivo é uma das selecionadas
          if ((false!==strpos($v,'.')) && (false!==strpos($_REQUEST['w_extensao'],upper(substr($v,strpos($v,'.')+1))))) {
            $cont += 1; 
            $arquivo = $v;
            $file = analisa_arquivo($arquivo, $w_opcao, $base, $w_caminho);
            if ($file['gerado']=='S') { $desta = '<b>'; $destf = '</b>'; } else { $desta = ''; $destf = ''; }
            ShowHTML('<DD>'.$cont.' - '.$desta.basename($v).$destf);
            //ShowHTML('<br><pre>'.$file['descricao'].'</pre>');

            // Se foi indicada a atualização do banco de dados, verifica se o arquivo já existe e grava.
            if ($w_opcao=='atualiza' && strlen($file['nome'])<=40) {
              $w_diretorio  = nvl((substr(strrev($file['diretorio']),0,1)=='/') ? substr($file['diretorio'],0,-1) : $file['diretorio'],'/');
              if ($w_diretorio=='/') $w_diretorio = null;
            }
          }
          flush();
        }
        if ($i==count($_REQUEST['w_dir'])-1) ShowHTML('</DL>');
      }
      ShowHTML('Processamento concluído. Clique <a class="hl" href="'.$w_dir.$R.'&O=L&SG='.$SG.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.MontaFiltro('GET').'">aqui</a> para processar outro diretório.');
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
// Retorna array com a estrutura de diretórios e arquivos
// -------------------------------------------------------------------------
function retrieveTree($l_raiz, $recursive = false) {
  if ($dir=@opendir($l_raiz)) { 
      while (($element=readdir($dir))!== false) { 
        $l_atual = str_replace('//','/',$l_raiz.'/'.$element);
        if (is_dir($l_atual) && $element!= '.' && $element!= '..' &&$recursive) { 
          $l_array[$element] = retrieveTree($l_atual, true); 
        } elseif ($element!= '.' && $element!= '..') { 
            $l_array[] = $l_atual; 
        } 
    } 
    closedir($dir); 
  } 
  return (isset($l_array) ? $l_array : false); 
}

// =========================================================================
// Retorna array unidimensional a partir de array multidimensional
// -------------------------------------------------------------------------
function flatArray($item, $key, $base) {
  global $w_raiz;
  $base[str_replace($w_raiz,'',$item)] = '';
}

// =========================================================================
// Analisa o código fonte do arquivo informado
// -------------------------------------------------------------------------
function analisa_arquivo($arquivo, $opcao, &$lista, $dir_destino) {
  global $w_raiz;
  $l_diretorio          = str_replace($w_raiz,'',str_replace(basename($arquivo),'',$arquivo));
  $l_diretorio          = (strlen($l_diretorio)>1) ? $l_diretorio : null;
  $destino              = $dir_destino.substr(basename($arquivo),0,strpos(basename($arquivo),'.')).'.sql';
  $i                    = 1;
  $l_array['nome']      = basename($arquivo);
  $l_array['diretorio'] = $l_diretorio;
  $l_array['tipo']      = null;
  $l_array['descricao'] ='';
  $l_array['gerado']    = 'N';
  $existe               = false; // indica se o arquivo de destino existe
  $atualiza             = false; // indica se o arquivo deve ser atualizado
  $return               = true; // Indica se foi encontrado a string RETURN, existente após a declaração de parâmetros de uma função 
  $cursor               = false; // Indica se foi encontrada a declaração de um cursor antes do $return  
  $begin                = false; // Indica se foi encontrada a string BEGIN
  //$select               = false; // Indica se deve ser feita a extração dos nomes recuperados em um select
  $recordset            = false; // Indica se a procedure retorna um record set, ou seja, se encontrar a string SYS_REF
  $procedure            = false; // Indica se foi encontrada a string PROCEDURE na primeira linha do arquivo
  $function             = false; // Indica se foi encontrada a string FUNCTION na primeira linha do arquivo
  $view                 = false; // Indica se foi encontrada a string VIEW na primeira linha do arquivo
  //unset($campos); // Array com os campos de um cursor

  if (!file_exists($destino)) {
    $atualiza = true;
    $existe   = false;
  } else {
    $existe = true;
    if (filemtime($arquivo)>filemtime($destino)) {
      $atualiza = true;
    }
  }
  $w_origem = fopen($arquivo, 'r');
  while (!feof($w_origem)) {
    // lê uma linha do arquivo de origem
    $buffer = fgets($w_origem);
    /*
    $buffer = str_Ireplace(' NUMBER', ' numeric', $buffer);
    $buffer = str_Ireplace(' VARCHAR2', ' varchar', $buffer);
    $buffer = str_Ireplace(' DATE', ' date', $buffer);
    $buffer = str_Ireplace(' DEFAULT NULL', ' ', $buffer);
    $buffer = str_Ireplace('DELETE ', 'DELETE FROM ', $buffer);
    $buffer = str_Ireplace('DELETE FROM FROM', 'DELETE FROM', $buffer);
    $buffer = str_Ireplace('FROM DUAL', '', $buffer);
    if (stripos($buffer,'loop')===FALSE) {
      $buffer = str_Ireplace(' IN ', ' ', $buffer);
    }
    $buffer = str_Ireplace(' OUT ', ' ', $buffer);
    $buffer = str_Ireplace('begin', 'BEGIN', $buffer);
    $buffer = str_Ireplace('sysdate', 'now()', $buffer);
    while (strpos($buffer,' ,')) $buffer = str_Ireplace(' ,', ',', $buffer);
    */
    if (stripos($buffer,'BEGIN')!==FALSE) {
      $begin = true;
    }
    if (!$return) {
      if (stripos($buffer,'SYS_REF')!==FALSE) {
        $recordset = true;
        $buffer = str_Ireplace(' SYS_REFCURSOR', ' REFCURSOR', $buffer);
      }
      if (stripos($buffer,' FUNCTION ')!==FALSE) {
        $function = true;
      }
      if (stripos($buffer,' VIEW ')!==FALSE) {
        $view = true;
      }
      if (stripos($buffer,'START WITH')!==FALSE) {
        // não atualiza se tiver select recursivo
        $atualiza = false;
      }
      if (stripos($buffer,' PROCEDURE ')!==FALSE) {
        $procedure = true;
        $buffer = str_Ireplace(' PROCEDURE ', ' FUNCTION ', $buffer);
      }
      
      if (($function && stripos($buffer,'RETURN')!==false) || ($procedure && stripos($buffer,') IS')!==false)) {
        if ($function) {
          $buffer = str_Ireplace('RETURN', ' RETURNS', $buffer);
          $buffer = str_Ireplace(' IS', ' AS $$', $buffer);
        } else {
          $buffer = str_Ireplace(') IS', ') RETURNS '.(($recordset) ? 'REFCURSOR' : 'VOID').' AS $$', $buffer);
        }
        $buffer .= $crlf.'DECLARE'."\r\n";
        $return = true;
      }
    } else {
      /*
      if (stripos($buffer,'cursor')!==false) {
        $buffer = str_Ireplace('cursor', '', $buffer);
        if (stripos($buffer,'(')!==false) {
          $buffer = str_Ireplace('(', 'CURSOR (', $buffer);
          $buffer = str_Ireplace(' IS', ' FOR', $buffer);
        } else {
          $buffer = str_Ireplace(' IS', ' CURSOR FOR', $buffer);
        }
      }
      */

      if (stripos($buffer,'.nextval')!==false) {
        $temp = substr($buffer,0,stripos($buffer,'.nextval'));
        $sequence = substr($temp,strrpos($temp,' ')+1);
        $sequence = str_replace('(','',str_replace(')','',$sequence));
        $buffer = str_Ireplace($sequence.'.nextval','nextVal(\''.$sequence.'\')',$buffer);
      }
    }

    $l_array['descricao'] .= $crlf.$buffer;
  }
/*
  if (!$view) {
    $pos = strripos($l_array['descricao'],'end')-1;
  } else {
    $pos = strlen($l_array['descricao']);
  }
  $l_array['descricao'] = substr($l_array['descricao'],0,$pos).
                          (($recordset) ? "\r\n  return p_result;\r\n" : '').
                          (($procedure || $function) ? 'END; $$ LANGUAGE \'PLPGSQL\' VOLATILE;' : '');
*/
    $i += 1;  
  fclose($w_origem);
  
  if (!$existe || $atualiza) {
    $w_destino = fopen($destino, 'w');
    if (is_writable($destino)) {
      if (fwrite($w_destino, ($l_array['descricao'])) === FALSE) {
          echo "Não foi possível escrever no arquivo ($destino)";
          exit;
      }
    } else {
      echo "O arquivo ($destino) não permite escrita!";
      exit;
    }
    fclose($w_destino);
    $l_array['gerado'] = 'S';
  }

  return $l_array; 
}

// =========================================================================
// Rotina principal
// -------------------------------------------------------------------------
function Main() {
  extract($GLOBALS);

  switch ($par) {
  case 'GRAVA':             Grava(); break; 
  default:
    Inicial();
  break;
  } 
} 
?>
