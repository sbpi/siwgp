<?php
// Garante que a sessão será reinicializada.
session_start();
if (isset($_SESSION['LOGON1'])) {
    echo '<SCRIPT LANGUAGE="JAVASCRIPT">';
    echo ' alert("Já existe outra sessão ativa!\nEncerre o sistema na outra janela do navegador ou aguarde alguns instantes.\nUSE SEMPRE A OPÇÃO \"SAIR DO SISTEMA\" para encerrar o uso da aplicação.");';
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
//  /indicador.php
// ------------------------------------------------------------------------
// Nome     : Alexandre Vinhadelli Papadópolis 
// Descricao: Retorna quantitativos de programa estratégico através de interface externa
// Mail     : alex@sbpi.com.br
// Criacao  : 18/08/2010, 14:40
// Versao   : 1.0.0.0
// Local    : Brasília - DF
// -------------------------------------------------------------------------
// 
// Parâmetros recebidos:
//    cli       = código do cliente a ser utilizado
//    uid       = username para login
//    pwd       = senha para login
//    codigo    = código do programa
//
// Retorno:
//    200 A chamada foi executada com sucesso. Neste caso, retorna uma linha para cada programa vinculado ao sistema externo ou uma linha para o programa informado. 
//    500 Autenticação inválida. Verificar se os valores das variáveis uid e pwd estão corretamente configurados. Neste caso, apenas uma linha é retornada. 
//    501 Erro de preenchimento. Neste caso, a primeira linha terá o valor 501 e as demais indicarão o(s) erro(s). 
//        Os erros podem ser causados pelo não preenchimento de algum campo ou pelo preenchimento incorreto de um ou mais deles. 

if (count($_POST) > 0) {
  // Recupera parâmetros
  $w_cliente   = $_SESSION['P_CLIENTE'];  

  $w_username  = utf8_decode(trim(substr(base64_decode($_POST['uid']),0,20)));
  $w_senha     = utf8_decode(trim(substr(base64_decode($_POST['pwd']),0,20)));
  $w_codigo    = upper(utf8_decode(trim(substr($_POST['codigo'],0,20))));

  // Abre conexão com o banco de dados
  $dbms = new abreSessao; $dbms = $dbms->getInstanceOf($_SESSION['DBMS']);
  
  // Autentica o usuário, recupera variáveis de sessão e grava log de acesso
  $auth = Valida();
  if ($auth>'') {
    // Erro de autenticação 
    $response = '500'.$crlf.$auth;
  } else {
    // Configura variáveis de uso global
    $SG         = 'PEPROCAD';
    $P1         = 5; // Recupera qualquer programa, independentemente da fase ou de estar cancelado
    $w_usuario  = RetornaUsuario();
    $w_menu     = RetornaMenu($w_cliente,$SG);
    $w_ano      = RetornaAno();

    // Retorna os dados do menu
    $sql = new db_getMenuData; $RS_Menu = $sql->getInstanceOf($dbms,$w_menu);

    $w_erro     = '';
    // Testa os dados recebidos
    $w_result = fValidate(1,$w_codigo,'código do programa','','',1,20,'1','');
    if ($w_result>'') { $w_erro.=$crlf.'Código do programa: '.$w_result; } 
     
    if ($w_erro>'') {
      // Erro de preenchimento
      $response = '501'.$crlf.substr($w_erro,2);
    } else {
      // Recupera os trâmites do serviço de programas estratégicos
      $sql = new db_getTramiteList; $RS = $sql->getInstanceOf($dbms, $w_menu, null, null,null);
      $RS = SortArray($RS,'ordem','asc');
      $w_fase = '';
      foreach($RS as $row) {
        $tramites[f($row,'sigla')] = f($row,'sq_siw_tramite');
        if (f($row,'sigla')!='CA' && f($row,'sigla')!='AT') $w_fase.=','.f($row,'sq_siw_tramite');
      }
      $w_fase = substr($w_fase,1);
      

      // Verifica se o programa existe
      $sql = new db_getSolicList; $RS = $sql->getInstanceOf($dbms,$w_menu,$w_usuario,$SG,$P1,
          $p_ini_i,$p_ini_f,$p_fim_i,$p_fim_f,$p_atraso,$p_solicitante,
          $p_unidade,$p_prioridade,$p_ativo,$p_parcerias,
          $p_chave, $p_objeto, $p_pais, $p_regiao, $p_uf, $p_cidade, $p_usu_resp,
          $p_uorg_resp, $w_codigo, $p_prazo, $w_fase, $p_sqcc, $p_projeto, $p_atividade, 
          null, null, $p_empenho, $p_processo);
      $RS = SortArray($RS,'codigo_interno','asc', 'inicio', 'desc');

      $w_cont = 0;
      foreach ($RS as $row) {
        if (nvl(f($row,'ln_programa'),'')==$w_username && nvl($w_codigo,f($row,'codigo_interno'))==f($row,'codigo_interno')) {
          $w_cont++;
        }
      }
      
      if ($w_cont==0) {
        if (nvl($w_codigo,'')=='') {
          $response = '501'.$crlf.'Nenhum programa vinculado ao ['.$w_username.'] foi encontrado na base de dados';
        } else {
          $response = '501'.$crlf.'Não existe programa ['.$w_codigo.'] criado pelo ['.$w_username.']';
        }
      } else {
        $response = '200';
        $w_cont = 0;
        reset($RS);
        foreach ($RS as $row) {
          if ((nvl(f($row,'ln_programa'),'')==$w_username && nvl($w_codigo,f($row,'codigo_interno'))==f($row,'codigo_interno'))) {
            $w_chave = f($row,'sq_siw_solicitacao');
            $w_cont++;
            
            $response.=$crlf.f($row,'codigo_interno');
            $w_normal = 0;
            $w_aviso  = 0;
            $w_atraso  = 0;
            $w_conc   = 0;
            // Recupera os projetos do programa
            $sql = new db_getLinkData; $RS1 = $sql->getInstanceOf($dbms,$w_cliente,'PJCAD');
            $sql = new db_getSolicList; $RS1 = $sql->getInstanceOf($dbms,f($RS1,'sq_menu'),$w_usuario,f($RS1,'sigla'),5,
                $p_ini_i,$p_ini_f,$p_fim_i,$p_fim_f,$p_atraso,$p_solicitante,
                $p_unidade,$p_prioridade,$p_ativo,$p_parcerias,
                $p_chave, $p_objeto, $p_pais, $p_regiao, $p_uf, $p_cidade, $p_usu_resp,
                $p_uorg_resp, null, $p_prazo, $p_fase, $p_sqcc, $w_chave, $p_atividade, 
                null, null, $p_empenho, $p_processo);
            foreach($RS1 as $row1) {
              // Verifica a situação do projeto
              if (f($row1,'sg_tramite')=='AT') {
                $w_conc++;
              } else {
                if (f($row1,'fim')<addDays(time(),-1)) {
                  $w_atraso++;
                } elseif (f($row1,'aviso_prox_conc')=='S' && (f($row1,'aviso')<=addDays(time(),-1))) {
                  $w_aviso++;
                } else {
                  $w_normal++;
                }
              }
            }
            $response.=';'.$w_normal.';'.$w_aviso.';'.$w_atraso.';'.$w_conc;
          }
        }
      }
    }
    
    // Se a geração de log estiver ativada, registra a saída.
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
      fwrite($l_log, 'Ação   : LOGOUT REMOTO'.$crlf.$crlf);
  
      // Fecha o arquivo e o diretório de log
      @fclose($l_log);
      @closedir($l_caminho); 
    }
  }
  
} else {
  $response = '501'.$crlf.
              'Método de chamada inválido';
}

echo $response;
flush();

// Eliminar todas as variáveis de sessão.
$_SESSION = array();

// Finalmente, destruição da sessão.
session_destroy();
exit;
?>