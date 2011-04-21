<?php
// Garante que a sess�o ser� reinicializada.
session_start();
if (isset($_SESSION['LOGON1'])) {
    echo '<SCRIPT LANGUAGE="JAVASCRIPT">';
    echo ' alert("J� existe outra sess�o ativa!\nEncerre o sistema na outra janela do navegador ou aguarde alguns instantes.\nUSE SEMPRE A OP��O \"SAIR DO SISTEMA\" para encerrar o uso da aplica��o.");';
    echo ' history.back();';
    echo '</SCRIPT>';
    exit();
}

$_SESSION['DBMS']      = 1;
$_SESSION['P_CLIENTE'] = intVal($_POST['cli']);

if (isset($_POST['p_root'])) {
  $_SESSION['ROOT']=substr($_POST['p_root'],0,255);
}

$w_dir_volta = '../';
include_once($w_dir_volta.'constants.inc');
include_once($w_dir_volta.'jscript.php');
include_once($w_dir_volta.'funcoes.php');
include_once($w_dir_volta.'funcoes_valida.php');
include_once($w_dir_volta.'classes/db/abreSessao.php');
include_once($w_dir_volta.'classes/sp/db_verificausuario.php');
include_once($w_dir_volta.'classes/sp/db_verificasenha.php');
include_once($w_dir_volta.'classes/sp/db_getLinkSubMenu.php');
include_once($w_dir_volta.'classes/sp/db_getLinkData.php');
include_once($w_dir_volta.'classes/sp/db_getUserData.php');
include_once($w_dir_volta.'classes/sp/db_getMenuData.php');
include_once($w_dir_volta.'classes/sp/db_getMenuCode.php');
include_once($w_dir_volta.'classes/sp/db_getCustomerData.php');
include_once($w_dir_volta.'classes/sp/db_getSolicList.php');
include_once($w_dir_volta.'classes/sp/db_getSolicData.php');
include_once($w_dir_volta.'classes/sp/db_getSolicObjetivo.php');
include_once($w_dir_volta.'classes/sp/db_getTramiteList.php');
include_once($w_dir_volta.'classes/sp/db_getTramiteData.php');
include_once($w_dir_volta.'classes/sp/db_getPlanoEstrategico.php');
include_once($w_dir_volta.'classes/sp/db_getObjetivo_PE.php');
include_once($w_dir_volta.'classes/sp/db_getHorizonte_PE.php');
include_once($w_dir_volta.'classes/sp/db_getNatureza_PE.php');
include_once($w_dir_volta.'classes/sp/dml_putProgramaGeral.php');
include_once($w_dir_volta.'classes/sp/dml_putProgramaEnvio.php');
include_once($w_dir_volta.'classes/sp/dml_putProgramaConc.php');
include_once('autenticacao.php');
include_once('visualprograma.php');
// =========================================================================
//  /cadastro.php
// ------------------------------------------------------------------------
// Nome     : Alexandre Vinhadelli Papad�polis 
// Descricao: Cadastra programa estrat�gico atrav�s de interface externa
// Mail     : alex@sbpi.com.br
// Criacao  : 18/08/2010, 14:40
// Versao   : 1.0.0.0
// Local    : Bras�lia - DF
// -------------------------------------------------------------------------
// 
// Par�metros recebidos:
//    cli       = c�digo do cliente a ser utilizado
//    uid       = username para login
//    pwd       = senha para login
//    codigo    = c�digo do programa
//    titulo    = t�tulo do programa 
//    inicio    = data de in�cio prevista para execu��o do programa
//    termino   = data de t�rmino prevista para execu��o do programa
//    valor     = valor estimado para execu��o do programa
//    situacao  = situa��o do programa: N - Normal; C - Cancelado
//
// Retorno:
//    200 A inclus�o de um programa com situa��o normal foi executada com sucesso. Neste caso, apenas uma linha � retornada. 
//    201 A inclus�o de um programa cancelado foi executada com sucesso. Neste caso, apenas uma linha � retornada. 
//    202 A altera��o de um programa com situa��o normal foi executada com sucesso. Neste caso, apenas uma linha � retornada. 
//    203 A altera��o de um programa cancelado foi executada com sucesso. Neste caso, apenas uma linha � retornada. 
//    500 Autentica��o inv�lida. Verificar se os valores das vari�veis uid e pwd est�o corretamente configurados. Neste caso, apenas uma linha � retornada. 
//    501 Erro de preenchimento. Neste caso, a primeira linha ter� o valor 501 e as demais indicar�o o(s) erro(s). 
//        Os erros podem ser causados pelo n�o preenchimento de algum campo ou pelo preenchimento incorreto de um ou mais deles. 

if (count($_POST) > 0) {
  // Recupera par�metros
  $w_cliente   = $_SESSION['P_CLIENTE'];  

  $w_username  = utf8_decode(trim(substr(base64_decode($_POST['uid']),0,20)));
  $w_senha     = utf8_decode(trim(substr(base64_decode($_POST['pwd']),0,20)));
  $w_codigo    = upper(utf8_decode(trim(substr($_POST['codigo'],0,20))));
  $w_titulo    = utf8_decode(trim(substr($_POST['titulo'],0,100)));
  $w_inicio    = utf8_decode(trim(substr($_POST['inicio'],0,10)));
  $w_fim       = utf8_decode(trim(substr($_POST['termino'],0,10)));
  $w_valor     = utf8_decode(trim(substr($_POST['valor'],0,20)));
  $w_situacao  = utf8_decode(trim(substr($_POST['situacao'],0,1)));

  // Abre conex�o com o banco de dados
  $dbms = new abreSessao; $dbms = $dbms->getInstanceOf($_SESSION['DBMS']);

  // Autentica o usu�rio, recupera vari�veis de sess�o e grava log de acesso
  $auth = Valida();
  if ($auth>'') {
    // Erro de autentica��o 
    $response = '500'.$crlf.$auth;
  } else {
    // Configura vari�veis de uso global
    $SG         = 'PEPROCAD';
    $P1         = 5; // Recupera qualquer programa, independentemente da fase ou de estar cancelado
    $w_usuario  = RetornaUsuario();
    $w_menu     = RetornaMenu($w_cliente,$SG);
    $w_ano      = RetornaAno();

    // Retorna os dados do menu
    $sql = new db_getMenuData; $RS_Menu = $sql->getInstanceOf($dbms,$w_menu);

    $w_erro     = '';
    // Testa os dados recebidos
    $w_result = fValidate(1,$w_codigo,'c�digo do programa','',1,1,20,'1','');
    if ($w_result>'') { $w_erro.=$crlf.'C�digo do programa: '.$w_result; } 
    
    $w_result = fValidate(1,$w_inicio,'in�cio do programa','DATA',1,10,10,'','0123456789/');
    if ($w_result>'') { $w_erro.=$crlf.'In�cio do programa: '.$w_result; }  
    
    $w_result = fValidate(1,$w_fim,'fim do programa','DATA',1,10,10,'','0123456789/');
    if ($w_result>'') { $w_erro.=$crlf.'Fim do programa: '.$w_result; }
    
    $w_result = fCompData($w_inicio,'in�cio do programa','<',$w_fim,'fim do programa');
    if ($w_result>'') { $w_erro.=$crlf.'Per�odo do programa: '.$w_result; }
    
    $w_result = fValidate(1,$w_valor,'valor','VALOR',1,4,18,'','0123456789,.');
    if ($w_result>'') { $w_erro.=$crlf.'Valor or�ado para o programa: '.$w_result; } 
    
    $w_result = fValidate(1,$w_situacao,'situacao','',1,1,1,'NEC','');
    if ($w_result>'') { $w_erro.=$crlf.'Situa��o do programa: '.$w_result; }
     
    if ($w_erro>'') {
      // Erro de preenchimento
      $response = '501'.$crlf.substr($w_erro,2);
    } else {
      // Recupera os tr�mites do servi�o de programas estrat�gicos
      $sql = new db_getTramiteList; $RS = $sql->getInstanceOf($dbms, $w_menu, null, null,null);
      $RS = SortArray($RS,'ordem','asc');
      foreach($RS as $row) {
        $tramites[f($row,'sigla')] = f($row,'sq_siw_tramite');
      }

      // Verifica se o programa existe
      $sql = new db_getSolicList; $RS = $sql->getInstanceOf($dbms,$w_menu,$w_usuario,$SG,$P1,
          $p_ini_i,$p_ini_f,$p_fim_i,$p_fim_f,$p_atraso,$p_solicitante,
          $p_unidade,$p_prioridade,$p_ativo,$p_parcerias,
          $p_chave, $p_objeto, $p_pais, $p_regiao, $p_uf, $p_cidade, $p_usu_resp,
          $p_uorg_resp, $w_codigo, $p_prazo, $p_fase, $p_sqcc, $p_projeto, $p_atividade, 
          null, null, $p_empenho, $p_processo);
      
      $w_cont = 0;
      foreach($RS as $row) {
        if (f($row,'codigo_interno')==$w_codigo) $w_cont++;
      }
      if ($w_cont==0) {
        /* Dados recebidos pelo m�todo POST e j� configurados
        $w_codigo           = f($RS,'cd_programa');
        $w_titulo           = f($RS,'titulo');
        $w_inicio           = formataDataEdicao(f($RS,'inicio'));
        $w_fim              = formataDataEdicao(f($RS,'fim'));
        $w_valor            = formatNumber(f($RS,'valor'));
        */
          
        $w_ln_programa      = $w_username;
        $w_solic_pai        = null;
        $w_chave_pai        = null;        
        $w_codigo_atual     = $w_codigo;
        $w_sq_unidade       = $_SESSION['LOTACAO'];
        $w_solicitante      = $w_usuario;
        $w_unid_resp        = $_SESSION['LOTACAO'];
        $w_parcerias        = null;
        $w_aviso            = 'S';
        $w_dias             = 30;
        $w_tramite          = $tramites['CI'];
        $w_novo_tramite     = $tramites['CI'];
          
        // Recupera o plano estrat�gico
        $sql = new db_getPlanoEstrategico; $RS = $sql->getInstanceOf($dbms,$w_cliente,null,null,null,null,null,'S','REGISTROS');
        foreach ($RS as $row) {
          if (nvl(f($row,'codigo_externo'),'')=='INTEGRACAO') {
            $w_plano = f($row,'chave');
            break;
          }
        }
        
        if (nvl($w_plano,'')=='') {
          $response = '501'.$crlf.'N�o h� plano estrat�gico configurado para integra��o com o '.$w_username.'. O c�digo externo do plano deve ser igual a INTEGRACAO.';
        } else {
          // Recupera o objetivo estrat�gico
          $sql = new db_getObjetivo_PE; $RS = $sql->getInstanceOf($dbms,$w_plano,null,$w_cliente,null,'INTEGRACAO','S',null);
          foreach ($RS as $row) {
            $w_objetivo = f($row,'chave');
            break;
          }

          if (nvl($w_objetivo,'')=='') {
            $response = '501'.$crlf.'N�o h� objetivo do plano estrat�gico configurado para integra��o com o '.$w_username.'. A sigla do objetivo deve ser igual a INTEGRACAO.';
          } else {
            $sql = new db_getHorizonte_PE; $RS = $sql->getInstanceOf($dbms,null,$w_cliente,null,'S');
            $RS = SortArray($RS,'nome','asc');
            foreach ($RS as $row) {
              $w_horizonte = f($row,'chave');
              break;
            }

            if (nvl($w_horizonte,'')=='') {
              $response = '501'.$crlf.'N�o h� horizonte temporal cadastrado no '.$conSgSistema.'.';
            } else {
              $sql = new db_getNatureza_PE; $RS = $sql->getInstanceOf($dbms,null,$w_cliente,null,'S');
              $RS = SortArray($RS,'nome','asc');
              foreach ($RS as $row) {
                $w_natureza = f($row,'chave');
                break;
              }

              if (nvl($w_natureza,'')=='') {
                $response = '501'.$crlf.'N�o h� natureza do programa cadastrada no '.$conSgSistema.'.';
              } else {
                // Configura dados para registro da atualiza��o ou envio
                if ($w_situacao=='C') {
                  $response = '201';
                  $w_novo_tramite = $tramites['CI'];
                } elseif ($w_situacao=='E') {
                  $response = '200';
                  $w_novo_tramite = $tramites['AT'];
                } else {
                  $response = '200';
                  $w_novo_tramite = $tramites['EE'];
                }

                $w_destinatario = $w_usuario;
                $w_observacao   = null;
                $w_despacho     = 'Envio autom�tico feito pelo '.$w_username;

                // Atualiza os dados do programa
                $SQL = new dml_putProgramaGeral; $SQL->getInstanceOf($dbms,'I',null,null,$w_menu,
                    $w_plano,$w_objetivo,$w_codigo,$w_titulo,$w_sq_unidade,
                    $w_solicitante,$w_unid_resp,$w_horizonte,$w_natureza,
                    $w_inicio,$w_fim,$w_parcerias,$w_ln_programa,
                    $w_usuario,null,$w_solic_pai,$w_valor,f($RS_Menu,'data_hora'),
                    $w_aviso,$w_dias,&$w_chave);

                if ($w_novo_tramite==$tramites['AT']) {
                  // Envia para execu��o antes de concluir
                  $SQL = new dml_putProgramaEnvio; $SQL->getInstanceOf($dbms,$w_menu,$w_chave,$w_usuario,$w_tramite,$tramites['EE'],'N',$w_observacao,$w_destinatario,$w_despacho,null,null,null,null);

                  // Registra os dados da conclus�o do programa
                  $SQL = new dml_putProgramaConc; $SQL->getInstanceOf($dbms,$w_menu,$w_chave,$w_usuario,$w_tramite,$w_inicio,$w_fim,'Conclus�o autom�tica do programa feita pelo '.$w_username,$w_valor);
                } elseif ($w_situacao=='C') {
                  // Envia para execu��o antes de cancelar
                  $SQL = new dml_putProgramaEnvio; $SQL->getInstanceOf($dbms,$w_menu,$w_chave,$w_usuario,$w_tramite,$tramites['EE'],'N',$w_observacao,$w_destinatario,$w_despacho,null,null,null,null);

                  // Cancela o programa
                  $SQL = new dml_putProgramaGeral; $SQL->getInstanceOf($dbms,'E',$w_chave,null,$w_menu,
                      $w_plano,$w_objetivo,$w_codigo,$w_titulo,$w_sq_unidade,
                      $w_solicitante,$w_unid_resp,$w_horizonte,$w_natureza,
                      $w_inicio,$w_fim,$w_parcerias,$w_ln_programa,
                      $w_usuario,null,$w_solic_pai,$w_valor,f($RS_Menu,'data_hora'),
                      $w_aviso,$w_dias,&$w_chave_nova);
                } else {
                  // Registra a atualiza��o ou envio do programa
                  $SQL = new dml_putProgramaEnvio; $SQL->getInstanceOf($dbms,$w_menu,$w_chave,$w_usuario,$w_tramite,$w_novo_tramite,'N',$w_observacao,$w_destinatario,$w_despacho,null,null,null,null);
                }
              }
            }
          }
        }
      } else {
        $w_cont = 0;
        foreach ($RS as $row) {
          if (nvl(f($row,'ln_programa'),'')==$w_username) {
            $w_chave = f($row,'sq_siw_solicitacao');
            $w_cont++;
          }
        }
        if ($w_cont==0) {
          $response = '501'.$crlf.'C�digo do programa est� vinculado a programa criado fora do '.$w_username;
        } elseif ($w_cont>1) {
          $response = '501'.$crlf.'H� mais de um programa criado pelo '.$w_username.' com o mesmo c�digo';
        } else {
          // Recupera os dados do programa
          $sql = new db_getSolicData; $RS = $sql->getInstanceOf($dbms,$w_chave,$SG);
          /* Dados recebidos pelo m�todo POST e j� configurados
          $w_codigo           = f($RS,'cd_programa');
          $w_titulo           = f($RS,'titulo');
          $w_inicio           = formataDataEdicao(f($RS,'inicio'));
          $w_fim              = formataDataEdicao(f($RS,'fim'));
          $w_valor            = formatNumber(f($RS,'valor'));
          */
          
          $w_ln_programa      = $w_username;
          $w_solic_pai        = f($RS,'sq_solic_pai');
          $w_chave_pai        = f($RS,'sq_solic_pai');
          $w_plano            = f($RS,'sq_plano');
          $w_codigo_atual     = f($RS,'cd_programa');
          $w_sq_unidade       = f($RS,'sq_unidade');
          $w_solicitante      = f($RS,'solicitante');
          $w_unid_resp        = f($RS,'sq_unidade_resp');
          $w_natureza         = f($RS,'sq_penatureza');
          $w_horizonte        = f($RS,'sq_pehorizonte');
          $w_parcerias        = f($RS,'palavra_chave');
          $w_aviso            = f($RS,'aviso_prox_conc');
          $w_dias             = f($RS,'dias_aviso');
          $w_tramite          = f($RS,'sq_siw_tramite');
          $w_novo_tramite     = f($RS,'sq_siw_tramite');

          $sql = new db_getSolicObjetivo; $RS = $sql->getInstanceOf($dbms,$w_chave,null,null);
          $RS = SortArray($RS,'nome','asc');
          $w_objetivo = '';
          foreach($RS as $row) { $w_objetivo .= ','.f($row,'sq_peobjetivo'); }
          $w_objetivo = substr($w_objetivo,1);

          // Configura dados para registro da atualiza��o ou envio
          if ($w_situacao=='C') {
            $response = '203';
            $w_novo_tramite = $w_tramite;
          } elseif ($w_situacao=='E') {
            $response = '202';
            $w_novo_tramite = $tramites['AT'];
          } else {
            $response = '202';
            $w_novo_tramite = $tramites['EE'];
          }
          if($w_tramite!=$w_novo_tramite) {
            $w_destinatario = $w_usuario;
            $w_observacao   = null;
            $w_despacho     = 'Envio autom�tico feito pelo '.$w_username;
          } else {
            $w_destinatario = null;
            $w_observacao   = 'Atualiza��o autom�tica de dados feito pelo '.$w_username;
            $w_despacho     = null;
          }
          
          // Grava vers�o atual
          $w_html = VisualPrograma($w_chave,'T',$w_usuario,3,'WORD','S','S','S','S','S','S','S','S','S','S','S');
          CriaBaseLine($w_chave,$w_html,f($RS_Menu,'nome'),$w_tramite);
          
          // Atualiza os dados do programa
          $SQL = new dml_putProgramaGeral; $SQL->getInstanceOf($dbms,(($w_situacao=='C') ? 'E' : 'A'),$w_chave,null,$w_menu,
              $w_plano,$w_objetivo,$w_codigo,$w_titulo,$w_sq_unidade,
              $w_solicitante,$w_unid_resp,$w_horizonte,$w_natureza,
              $w_inicio,$w_fim,$w_parcerias,$w_ln_programa,
              $w_usuario,null,$w_solic_pai,$w_valor,f($RS_Menu,'data_hora'),
              $w_aviso,$w_dias,&$w_chave_nova);

          
          if ($w_novo_tramite==$tramites['AT'] && $w_tramite!=$w_novo_tramite) {
            // Registra os dados da conclus�o do programa
            $SQL = new dml_putProgramaConc; $SQL->getInstanceOf($dbms,$w_menu,$w_chave,$w_usuario,$w_tramite,$w_inicio,$w_fim,'Conclus�o autom�tica do programa feita pelo '.$w_username,$w_valor);
          } elseif ($w_situacao!='C') {
            // Registra a atualiza��o ou envio do programa
            $SQL = new dml_putProgramaEnvio; $SQL->getInstanceOf($dbms,$w_menu,$w_chave,$w_usuario,$w_tramite,$w_novo_tramite,'N',$w_observacao,$w_destinatario,$w_despacho,null,null,null,null);
          }
        }
      }
    }

    // Se a gera��o de log estiver ativada, registra a sa�da.
    if ($conLog) {
      // Define o caminho fisico do diret�rio e do arquivo de log
      $l_caminho = $conLogPath;
      $l_arquivo = $l_caminho.$_SESSION['P_CLIENTE'].'/'.date(Ymd).'.log';
  
      // Verifica a necessidade de cria��o dos diret�rios de log
      if (!file_exists($l_caminho)) mkdir($l_caminho);
      if (!file_exists($l_caminho.$_SESSION['P_CLIENTE'])) mkdir($l_caminho.$_SESSION['P_CLIENTE']);
        
      // Abre o arquivo de log
      $l_log = @fopen($l_arquivo, 'a');
        
      fwrite($l_log, '['.date(ymd.'_'.Gis.'_'.time()).']'.$crlf);
      fwrite($l_log, $_SESSION['USUARIO'].': '.$_SESSION['NOME_RESUMIDO'].' ('.$_SESSION['SQ_PESSOA'].')'.$crlf);
      fwrite($l_log, 'IP     : '.$_SERVER['REMOTE_ADDR'].$crlf);
      fwrite($l_log, 'A��o   : LOGOUT REMOTO'.$crlf.$crlf);
  
      // Fecha o arquivo e o diret�rio de log
      @fclose($l_log);
      @closedir($l_caminho); 
    }
  }
} else {
  $response = '501'.$crlf.
              'M�todo de chamada inv�lido';
}

echo $response;
flush();

// Eliminar todas as vari�veis de sess�o.
$_SESSION = array();

// Finalmente, destrui��o da sess�o.
session_destroy();
exit;
?>