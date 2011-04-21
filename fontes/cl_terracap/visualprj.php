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
include_once($w_dir_volta.'classes/sp/dml_putSiwUsuario.php');
include_once($w_dir_volta.'classes/sp/db_updatePassword.php');
include_once('autenticacao.php');
include_once('visualprograma.php');
// =========================================================================
//  /visualprj.php
// ------------------------------------------------------------------------
// Nome     : Alexandre Vinhadelli Papadópolis 
// Descricao: Visualiza detalhamento de projeto através de interface externa
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
//    codigo    = código do projeto
//
// Retorno:
//    Página HTML com o relatório executivo informado ou com mensagem informando que o programa não existe, ou ainda. 
//    500 Autenticação inválida. Verificar se os valores das variáveis uid e pwd estão corretamente configurados. Neste caso, apenas uma linha é retornada. 
//    501 Erro de preenchimento. Neste caso, a primeira linha terá o valor 501 e as demais indicarão o(s) erro(s). 
//        Os erros podem ser causados pelo não preenchimento de algum campo ou pelo preenchimento incorreto de um ou mais deles. 

if (count($_POST) > 0) {
  // Recupera parâmetros
  $w_cliente   = $_SESSION['P_CLIENTE'];  

  $w_username  = utf8_decode(trim(substr(base64_decode($_POST['uid']),0,20)));
  $w_senha     = utf8_decode(upper(trim(substr(base64_decode($_POST['pwd']),0,20))));
  $w_nome      = utf8_decode(trim(substr(base64_decode($_POST['nome']),0,60)));
  $w_mail      = utf8_decode(trim(substr(base64_decode($_POST['mail']),0,60)));
  $w_codigo    = upper(utf8_decode(trim(substr($_POST['codigo'],0,20))));
  
  // Abre conexão com o banco de dados
  $dbms = new abreSessao; $dbms = $dbms->getInstanceOf($_SESSION['DBMS']);
  
  // Verifica se o usuário existe
  $sql = new DB_GetUserData; $RS = $sql->getInstanceOf($dbms, $_SESSION['P_CLIENTE'], $w_username);
  $O = 'I';
  if (count($RS)>0) {
    $O                  = 'A';
    $w_chave            = f($RS,'SQ_PESSOA');
    $w_nome_resumido    = f($RS,'NOME_RESUMIDO');
    $w_cpf              = f($RS,'CPF');
    $w_sexo             = f($RS,'SEXO');
    $w_mail             = f($RS,'email');
    $w_sq_tipo_vinculo  = f($RS,'SQ_TIPO_VINCULO');
    $w_tipo_pessoa      = f($RS,'SQ_TIPO_PESSOA');
    $w_unidade          = f($RS,'SQ_UNIDADE');
    $w_localizacao      = f($RS,'SQ_LOCALIZACAO');
    $w_gestor_seguranca = f($RS,'GESTOR_SEGURANCA');
    $w_gestor_sistema   = f($RS,'GESTOR_SISTEMA');
    $w_tipo             = f($RS,'TIPO_AUTENTICACAO');
    $w_gestor_portal    = f($RS,'gestor_portal');
    $w_gestor_dashboard = f($RS,'gestor_dashbord');
    $w_gestor_conteudo  = f($RS,'gestor_conteudo');
  }        

  // Cria ou atualiza o usuário
  $SQL = new dml_putSiwUsuario; $SQL->getInstanceOf($dbms, $O, $w_chave, $w_cliente, $w_nome, $w_nome_resumido, $w_cpf, $w_sexo,
        $w_sq_tipo_vinculo, $w_tipo_pessoa, $w_unidade, $w_localizacao, $w_username, $w_mail, $w_gestor_seguranca, 
        $w_gestor_sistema, $w_tipo, $w_gestor_portal, $w_gestor_dashboard, $w_gestor_conteudo);

  // Recupera a chave do usuário
  $sql = new DB_GetUserData; $RS = $sql->getInstanceOf($dbms, $_SESSION['P_CLIENTE'], $w_username);
  $w_chave  = f($RS,'SQ_PESSOA');
  
  // Atualiza a senha e a assinatura eletrônica do usuário
  $sql = new db_updatePassword; $sql->getInstanceOf($dbms,$w_cliente,$w_chave,$w_senha,'PASSWORD');
  $sql = new db_updatePassword; $sql->getInstanceOf($dbms,$w_cliente,$w_chave,$w_senha,'SIGNATURE');
        
  //Autentica o usuário, recupera variáveis de sessão e grava log de acesso
  $auth = Valida();
  if ($auth>'') {
    $response = '500'.$crlf.$auth;
  } else {
    
    // Configura variáveis de uso global
    $SG         = 'PJCAD';
    $P1         = 5; // Recupera o projeto indicado, independentemente da fase ou de estar cancelado
    $w_usuario  = RetornaUsuario();
    $w_menu     = RetornaMenu($w_cliente,$SG);
    $w_ano      = RetornaAno();
    
    // Retorna os dados do menu
    $sql = new db_getMenuData; $RS_Menu = $sql->getInstanceOf($dbms,$w_menu);
      
    $w_erro     = '';
    // Testa os dados recebidos
    $w_result = fValidate(1,$w_codigo,'código do projeto','','',1,20,'1','');
    if ($w_result>'') { $w_erro.=$crlf.'Código do projeto: '.$w_result; } 
      
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
        if (f($row,'sigla')!='CA') $w_fase.=','.f($row,'sq_siw_tramite');
      }
      $w_fase = substr($w_fase,1);
        
      // Verifica se o programa existe
      $sql = new db_getSolicList; $RS = $sql->getInstanceOf($dbms,$w_menu,$w_usuario,$SG,$P1,
            $p_ini_i,$p_ini_f,$p_fim_i,$p_fim_f,$p_atraso,$p_solicitante,
            $p_unidade,$p_prioridade,$p_ativo,$p_parcerias,
            $p_chave, $p_objeto, $p_pais, $p_regiao, $p_uf, $p_cidade, $p_usu_resp,
            $p_uorg_resp, $p_codigo, $p_prazo, $w_fase, $p_sqcc, $p_projeto, $p_atividade, 
            null, null, $p_empenho, $p_processo);
      $RS = SortArray($RS,'codigo_interno','asc', 'inicio', 'desc');
            
      $w_cont = 0;
      foreach ($RS as $row) {
        if (nvl($w_codigo,f($row,'codigo_interno'))==f($row,'codigo_interno') && f($row,'sg_tramite')!='CA' && f($row,'sg_tramite')!='AT') {
          $w_cont++;
          $p_plano    = f($row,'sq_plano');
          if ($w_cont==1) $p_projeto  = f($row,'sq_siw_solicitacao'); else $p_projeto  .= ','.f($row,'sq_siw_solicitacao'); 
        }
      }
      if ($w_cont==0) {
        $SG = 'MESA';
        $content='';
      } else {
        // Se o programa não foi informado, recupera todo o relatorio
        if ($w_cont > 1) $p_projeto = '';
         
        $SG = 'RELDSPROJ';
        $sql = new db_getLinkData; $RS1 = $sql->getInstanceOf($dbms,$_SESSION['P_CLIENTE'],$SG);
        $id = session_id();
        $content = base64_encode(montaURL_JS($w_dir,f($RS1,'link').'&O=L&p_plano='.$p_plano.'&p_projeto='.$p_projeto.'&p_legenda=S&p_geral=S&p_qualit=S&p_os=S&p_oe=S&p_ee=S&p_pr=S&p_re=S&p_ob=S&p_rubrica=S&p_etapa=S&p_tr=S&p_indicador=S&p_meta=S&p_recurso=S&p_risco=S&p_cf=S&p_tf=S&p_resp=S&p_partes=S&p_ca=S&p_anexo=S&p_tramite=S&p_sinal=S&w_menu='.f($RS1,'sq_menu').'&P1='.f($RS1,'p1').'&P2='.f($RS1,'p2').'&P3='.f($RS1,'p3').'&P4='.f($RS1,'p4').'&TP='.f($RS1,'nome').'&SG='.f($RS1,'sigla').'&id='.$id));
      }
        
      // Abre SIW-GP em nova janela
      ScriptOpen('JavaScript');
      ShowHTML('  location.href="'.montaURL_JS(null,$conRootSIW.'menu.php?par=Frames&content='.$content).'";');
      ScriptClose();
      exit();
    }
  }
} else {
  $response = '501'.$crlf.
              'Método de chamada inválido';
}

if ($response=='200') {
  echo $relatorio;
} else {
  echo $response;
}
flush();

// Eliminar todas as variáveis de sessão.
$_SESSION = array();

// Finalmente, destruição da sessão.
session_destroy();
exit;
?>