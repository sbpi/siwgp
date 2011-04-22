<?php
session_start();
include_once('../constants.inc');
$w_dir_volta = $conDiretorio;
include_once($w_dir_volta.'funcoes.php');
include_once($w_dir_volta.'classes/db/abreSessao.php');
include_once($w_dir_volta.'classes/sp/dml_putDcOcorrencia.php');

// =========================================================================
//  etl_txt.php
// ------------------------------------------------------------------------
// Nome     : Alexandre Vinhadelli Papad�polis
// Descricao: Executa procedimentos de um esquema
// Mail     : alex@sbpi.com.br
// Criacao  : 17/08/2006, 12:26
// Versao   : 1.0.0.0
// Local    : Bras�lia - DF
// -------------------------------------------------------------------------
// 
// Par�metros recebidos:
//    R (refer�ncia) = usado na rotina de grava��o, com conte�do igual ao par�metro T
//    O (opera��o)   = I   : Inclus�o
//                   = A   : Altera��o
//                   = C   : Cancelamento
//                   = E   : Exclus�o
//                   = L   : Listagem
//                   = P   : Pesquisa
//                   = D   : Detalhes
//                   = N   : Nova solicita��o de envio

//L� os par�metros de chamada
$w_cliente = nvl($_SESSION['P_CLIENTE'],$argv[1]);
$w_dbms    = nvl($_SESSION['DBMS'],$argv[2]);
$w_esquema = nvl($w_sq_esquema,$argv[3]);
$w_usuario = $_SESSION['SQ_PESSOA'];

// Verifica se os par�metros de chamada est�o corretos
if (nvl($w_cliente,'')=='') {
  echo 'ERRO: � necess�rio informar o c�digo do cliente como primeiro par�metro de chamada.'.$crlf;
  exit();
};
if (nvl($w_dbms,'')=='') {
  echo 'ERRO: � necess�rio informar o banco de dados como segundo par�metro de chamada.'.$crlf;
  exit();
};

// verifica se o diret�rio informado atende aos requisitos de exist�ncia, leitura e escrita
$l_erro = '';
if (!testFile(&$l_erro, $conFilePhysical.$w_cliente.'/etl_conf/', true, true)) {
  echo 'ATEN��O: diret�rio de configura��o '.$l_erro.'!'.$crlf;
  exit();
}

// Se foi disparado da interface Web, guarda os dados para uso futuro
if (nvl($_SESSION['P_CLIENTE'],'')!='') $w_cliente_old  = $_SESSION['P_CLIENTE'];
if (nvl($_SESSION['DBMS'],'')!='')      $w_dbms_old     = $_SESSION['DBMS'];

// Configura par�metros de funcionamento
$_SESSION['P_CLIENTE'] = $w_cliente;
$_SESSION['DBMS']      = $w_dbms;

// Abre conex�o como banco de dados
$dbms = new abreSessao; $dbms = $dbms->getInstanceOf($_SESSION['DBMS']);

Principal();

FechaSessao($dbms);

// =========================================================================
// Rotina de cadastramento da outra parte
// -------------------------------------------------------------------------
function Principal() {
  extract($GLOBALS);
  // Configura caminhos para recupera��o de arquivos de configura��o e arquivos de dados
  $w_raiz_conf = $conFilePhysical.$w_cliente.'/etl_conf/';
  $w_raiz_scr  = $conFilePhysical.$w_cliente.'/';

  $w_erro = false;
  // Varre cada um dos esquemas e gera arquivo de script com todos eles
  if ($dir = @opendir($w_raiz_conf)) { 
    while (false !== ($element = readdir($dir))) { 
      if ($element!= '.' && $element!= '..' && (strpos(lower($element),'.php')!==false)) { 
        // Defino o nome do arquivo de trabalho
        $w_arq_conf       = $w_raiz_conf.$element;
        $arq_conf['nome'] = $w_arq_conf;

        // Verifica se � poss�vel abrir o arquivo de configura��o
        if (!testFile(&$l_erro, $w_arq_conf, true, true)) {
          $arq_conf['status'] = '[ERRO] Arquivo de configura��o '.$l_erro.'!';;
          $w_erro = true;
          continue;
        } else {
          $arq_conf['status'] = '[OK] Arquivo de configura��o aberto.';;
          if (!include_once($w_arq_conf)) {
            $arq_conf['status'] = '[ERRO] N�o foi poss�vel abrir o arquivo de configura��o!';
            $w_erro = true;
            continue;
          } else {
            $arq_conf['status'] = '[OK] Arquivo de configura��o aberto.';
          
            // Configura o diret�rio raiz dos arquivos de dados
            $w_raiz_arq  = $conFilePhysical.$w_cliente.'/etl_arquivos/'.lower($esq['nome']).'/';

            // Monta o nome do arquivo de execu��o e de log
            $w_arquivo_script = tempnam($w_raiz_scr,'etl_');
            rename($w_arquivo_script,$w_arquivo_script.'.sql');
            $w_arquivo_script = $w_arquivo_script.'.sql';
            $w_arquivo_log    = str_replace('.sql','.log',$w_arquivo_script);
  
            // Inicializa os arquivos de comandos e de log
            $w_destino = @fopen($w_arquivo_script, 'w');
            $w_log     = @fopen($w_arquivo_log, 'w');

            // ******************************************************************************************
            // FASE 0: Se for indicada a origem de dados atrav�s de FTP, tenta obt�-los
            // ******************************************************************************************

            $w_conftp  = true;
            if ($esq['origem_arq']==1) {
              $l_hostname = explode(':',$ftp['hostname']);
              $w_conftp  = false;
              // Tenta estabelecer conex�o com servidor FTP
              if (!($conFtp = @ftp_connect($l_hostname[0],$l_hostname[1]))) {
                fwrite($w_destino, '[CONEX�O AO SERVIDOR FTP]'.$crlf);
                fwrite($w_destino, '  Data e hora: '.DataHora().$crlf);
                fwrite($w_destino, '  Status: [ERRO] N�o foi poss�vel estabelecer a conex�o com o servidor '.$ftp['hostname'].$crlf);
                fwrite($w_destino, '  Observa��o: nenhum arquivo de dados ser� processado!'.$crlf);
                fwrite($w_log, '[CONEX�O AO SERVIDOR FTP]'.$crlf);
                fwrite($w_log, '  Status: [ERRO] N�o foi poss�vel estabelecer a conex�o com o servidor '.$ftp['hostname'].$crlf);
                fwrite($w_log, '  Observa��o: nenhum arquivo de dados ser� processado!'.$crlf);
                $w_erro    = true;
              } else {
                fwrite($w_destino, '[CONEX�O AO SERVIDOR FTP]'.$crlf);
                fwrite($w_destino, '  Data e hora: '.DataHora().$crlf);
                fwrite($w_destino, '  Status: [OK] Conex�o estabelecida com o servidor '.$ftp['hostname'].$crlf);
                // Tenta autenticar no servidor FTP
                if (!($l_ftp_login = @ftp_login($conFtp, $ftp['username'], $ftp['password']))) {
                  fwrite($w_destino, '  ==> Autentica��o de usu�rio'.$crlf);
                  fwrite($w_destino, '  Data e hora: '.DataHora().$crlf);
                  fwrite($w_destino, '  Status: [ERRO] Autentica��o inv�lida para o usu�rio '.$ftp['username'].$crlf);
                  fwrite($w_destino, '  Observa��o: nenhum arquivo de dados ser� processado!'.$crlf);
                  fwrite($w_log, '  ==> Autentica��o de usu�rio'.$crlf);
                  fwrite($w_log, '  Status: [ERRO] Autentica��o inv�lida para o usu�rio '.$ftp['username'].$crlf);
                  fwrite($w_log, '  Observa��o: nenhum arquivo de dados ser� processado!'.$crlf);
                  $w_erro    = true;
                } else {
                  fwrite($w_destino, '  ==> Autentica��o de usu�rio'.$crlf);
                  fwrite($w_destino, '  Data e hora: '.DataHora().$crlf);
                  fwrite($w_destino, '  Status: [OK] Autentica��o efetivada para o usu�rio '.$ftp['username'].$crlf);
                  // Tenta ir para o diret�rio onde est�o os arquivos de dados no servidor FTP
                  if (!@ftp_chdir($conFtp, $ftp['diretorio'])) {
                    fwrite($w_destino, '  ==> Abertura do diret�rio origem de arquivos de dados'.$crlf);
                    fwrite($w_destino, '  Data e hora: '.DataHora().$crlf);
                    fwrite($w_destino, '  Status: [ERRO] N�o foi poss�vel abrir o diret�rio '.$ftp['diretorio'].$crlf);
                    fwrite($w_destino, '  Observa��o: nenhum arquivo de dados ser� processado!'.$crlf);
                    fwrite($w_log, '  ==> Abertura do diret�rio origem de arquivos de dados'.$crlf);
                    fwrite($w_log, '  Status: [ERRO] N�o foi poss�vel abrir o diret�rio '.$ftp['diretorio'].$crlf);
                    fwrite($w_log, '  Observa��o: nenhum arquivo de dados ser� processado!'.$crlf);
                    $w_erro    = true;
                  } else {
                    $l_arquivos = @ftp_nlist($conFtp,'.');
                    
                    // Remove os arquivos de dados existentes no servidor local
                    if (!$l_dir=@opendir($w_raiz_arq)) { 
                      fwrite($w_destino, '  ==> Abertura do diret�rio destino de arquivos de dados'.$crlf);
                      fwrite($w_destino, '  Data e hora: '.DataHora().$crlf);
                      fwrite($w_destino, '  Status: [ERRO] N�o foi poss�vel abrir o diret�rio '.$w_raiz_arq.$crlf);
                      fwrite($w_destino, '  Observa��o: nenhum arquivo de dados ser� processado!'.$crlf);
                      fwrite($w_log, '  ==> Abertura do diret�rio destino de arquivos de dados'.$crlf);
                      fwrite($w_log, '  Status: [ERRO] N�o foi poss�vel abrir o diret�rio '.$w_raiz_arq.$crlf);
                      fwrite($w_log, '  Observa��o: nenhum arquivo de dados ser� processado!'.$crlf);
                      $w_erro    = true;
                    } else {
                      fwrite($w_destino, '  ==> Abertura do diret�rio destino de arquivos de dados'.$crlf);
                      fwrite($w_destino, '  Data e hora: '.DataHora().$crlf);
                      fwrite($w_destino, '  Status: [OK] Foi aberto o diret�rio '.$w_raiz_arq.$crlf);
                      $w_conftp = true;
                      while (($element=readdir($l_dir))!== false) { 
                        if ($element!= '.' && $element!= '..') { 
                          unlink($w_raiz_arq.$element);
                        } 
                      } 
                      closedir($l_dir); 
                    } 
                      
                    // Tenta fazer o download dos arquivos de dados
                    foreach($l_arquivos as $k => $v) {
                      if (!@ftp_get($conFtp, $w_raiz_arq.$v, $v, FTP_ASCII)) {
                        fwrite($w_destino, '  [ERRO] '.$v.$crlf);
                        fwrite($w_log, '  [ERRO] '.$v.$crlf);
                        $w_erro = true;
                      } else {
                        fwrite($w_destino, '  [OK] '.$v.$crlf);
                      }
                    }
                  }
                }
                @ftp_close($conFtp);
              }
            }

            // ******************************************************************************************
            // FASE 1: L� o arquivo de configura��o e monta os comandos SQL necess�rios
            // ******************************************************************************************

            // Inicializa vari�veis de controle do esquema
            $w_lidos      = 0;
            $w_rejeitados = 0;
      
            // Analisa cada uma das tabelas referenciadas pelo esquema
            $j = 0;
            foreach($tabelas as $k_tab => $v_tab) {
              // Recupera o nome da tabela
              $tabela[$j] = $k_tab;

              // Recupera indicador de remo��o dos dados da tabela
              $remove_registro[$j] = $v_tab['remove_registro'];

              // Monta SQL para inser��o pr�via de registros, se necess�rio
              if (is_array($v_tab['insert'])) {
                $k = 0;
                foreach($v_tab['insert'] as $k_ins => $v_ins) {
                  $inserts[$j][$k] = monta_sql_insert($k_tab,$v_ins);
                  $k += 1;
                }
              }

              // Monta SQL para inser��o de registros a partir de arquivo TXT, se necess�rio
              if (is_array($v_tab['mapeamento'])) {
                $w_arq_dados[$j]['nome'] = $w_raiz_arq.$v_tab['localizacao'];
                if (!analisa_arquivo($tabela[$j],$w_raiz_arq.$v_tab['localizacao'], $delimitador, $maps[$j], $v_tab['mapeamento'], $maps[$j])) {
                  $w_arq_dados[$j]['status'] = '[ERRO] N�o foi poss�vel abrir o arquivo de dados '.$w_raiz_arq.$v_tab['localizacao'];
                  $w_erro = true;
                } else {
                  $w_arq_dados[$j]['status'] = '[OK] Arquivo de dados aberto';
                }
              }

              $j += 1;
            }
            
            // Monta SQL para processar os scripts, se necess�rio
            if (is_array($scripts)) {
              foreach($scripts as $k => $v) {
                $w_arq_script[$k]['nome'] = $w_raiz_scr.$v;
                if (!analisa_script($w_arq_script[$k]['nome'], $scr[$k])) {
                  $w_arq_script[$k]['status'] = '[ERRO] N�o foi poss�vel abrir o arquivo de script.';
                  $w_erro = true;
                } else {
                  $w_arq_script[$k]['status'] = '[OK] Arquivo de script aberto';
                }
              }
            }

            // ******************************************************************************************
            // FASE 2: A partir da an�lise do arquivo de configura��o e dos arquivos de dados, executa
            // e grava os logs
            // ******************************************************************************************
            
            // Verifica se � poss�vel estabelecer conex�o com o banco de destino
            if (!($conObj = @oci_connect($bd['username'],$bd['password'],$bd['hostname']))) {
              $error = @oci_error();
              fwrite($w_destino, '[CONEX�O AO BANCO DE DADOS]'.$crlf);
              fwrite($w_destino, '  Data e hora: '.DataHora().$crlf);
              fwrite($w_destino, '  Status: [ERRO]'.$error['message'].$crlf);
              fwrite($w_destino, '  Observa��o: nenhum comando ser� executado!'.$crlf);
              fwrite($w_log, '[CONEX�O AO BANCO DE DADOS]'.$crlf);
              fwrite($w_log, '  Status: [ERRO]'.$error['message'].$crlf);
              fwrite($w_log, '  Observa��o: nenhum comando ser� executado!'.$crlf);
              $w_conexao = false;
              $w_erro    = true;
            } else {
              fwrite($w_destino, '[CONEX�O AO BANCO DE DADOS]'.$crlf);
              fwrite($w_destino, '  Data e hora: '.DataHora().$crlf);
              fwrite($w_destino, '  Status: [OK]'.$crlf);
              $w_conexao = true;
            }

            // Grava informa��es sobre o esquema
            fwrite($w_destino, '[ESQUEMA '.$esq['nome'].']'.$crlf);
            fwrite($w_destino, '  Data e hora: '.DataHora().$crlf);
            fwrite($w_destino, '  Arquivo: '.$arq_conf['nome'].$crlf);
            fwrite($w_destino, '  Status: '.$arq_conf['status'].$crlf);
            if (strpos($arq_conf['status'],'ERRO')!==false) {
              fwrite($w_log, '[ESQUEMA '.$esq['nome'].']'.$crlf);
              fwrite($w_log, '  Arquivo: '.$arq_conf['nome'].$crlf);
              fwrite($w_log, '  Status: '.$arq_conf['status'].$crlf);
              $w_erro = true;
            }
    
            // Apaga o conte�do atual das tabelas se a conex�o estiver ativa
            if ($w_conexao && $w_conftp) {
              for ($x = (count($tabela)-1); $x >= 0; $x--){
                if ($remove_registro[$x]=='S') {
                  fwrite($w_destino, '  ===>Remo��o dos dados da tabela: '.$tabela[$x].$crlf);
                  fwrite($w_destino, '    Data e hora: '.DataHora().$crlf);
                  $sql = 'delete '.$tabela[$x];
                  if (!($stmt = @oci_parse($conObj, $sql))) {
                    $error = @oci_error($stmt);
                    fwrite($w_log, '    '.$sql.$crlf);
                    fwrite($w_log, '      [ERRO]'.$error['message'].$crlf);
                    $w_erro = true;
                  } else {
                    if (!(@oci_execute($stmt, OCI_DEFAULT))) {
                      $error = @oci_error($stmt);
                      fwrite($w_log, '    '.$sql.$crlf);
                      fwrite($w_log, '      [ERRO]'.$error['message'].$crlf);
                      $w_erro = true;
                    }
                    @oci_free_statement($stmt);  
                  }
                }
              }
            }
    
            // Grava informa��es sobre as tabelas
            for ($x = 0; $x < count($tabela); $x++){
              fwrite($w_destino, '  ===>Carga da tabela: '.$tabela[$x].$crlf);
              fwrite($w_destino, '    Data e hora: '.DataHora().$crlf);
              fwrite($w_destino, '    Arquivo: '.$w_arq_dados[$x]['nome'].$crlf);
              fwrite($w_destino, '    Status: '.$w_arq_dados[$x]['status'].$crlf);
              if (strpos($w_arq_dados[$x]['status'],'ERRO')!==false) {
                fwrite($w_log, '  ===>Carga da tabela: '.$tabela[$x].$crlf);
                fwrite($w_log, '    Arquivo: '.$w_arq_dados[$x]['nome'].$crlf);
                fwrite($w_log, '    Status: '.$w_arq_dados[$x]['status'].$crlf);
                $w_erro = true;
              }
              fwrite($w_destino, '    Inserts pr�vios: '.count($inserts[$x]).$crlf);
              for ($y = 0; $y < count($inserts[$x]); $y++){
                if ($w_conexao && $w_conftp) {
                  if (!($stmt = @oci_parse($conObj, substr($inserts[$x][$y],0,-1)))) {
                    $error = @oci_error($stmt);
                    fwrite($w_log, '    '.substr($inserts[$x][$y],0,-1).$crlf);
                    fwrite($w_log, '      [ERRO]'.$error['message'].$crlf);
                    $w_erro = true;
                  } else {
                    $w_lidos += 1;
                    if (!(@oci_execute($stmt, OCI_DEFAULT))) {
                      $error = @oci_error($stmt);
                      fwrite($w_log, '    '.substr($inserts[$x][$y],0,-1).$crlf);
                      fwrite($w_log, '      [ERRO]'.$error['message'].$crlf);
                      $w_erro = true;
                      $w_rejeitados += 1;
                    }
                    @oci_free_statement($stmt);  
                  }
                }
              }
              fwrite($w_destino, '    Registros do arquivo txt: '.count($maps[$x]).$crlf);
              for ($y = 0; $y < count($maps[$x]); $y++){
                if ($w_conexao && $w_conftp) {
                  if (!($stmt = @oci_parse($conObj, substr($maps[$x][$y],0,-1)))) {
                    $error = @oci_error($stmt);
                    fwrite($w_log, '    '.substr($maps[$x][$y],0,-1).$crlf);
                    fwrite($w_log, '      [ERRO]'.$error['message'].$crlf);
                    $w_erro = true;
                  } else {
                    $w_lidos += 1;
                    if (!(@oci_execute($stmt, OCI_DEFAULT))) {
                      $error = @oci_error($stmt);
                      fwrite($w_log, '    '.substr($maps[$x][$y],0,-1).$crlf);
                      fwrite($w_log, '      [ERRO]'.$error['message'].$crlf);
                      $w_erro = true;
                      $w_rejeitados += 1;
                    }
                    @oci_free_statement($stmt);  
                  }
                }
              }
            }

            // Grava informa��es sobre os scripts
            for ($x = 0; $x < count($w_arq_script); $x++){
              fwrite($w_destino, '  ===>Script: '.$w_arq_script[$x]['nome'].$crlf);
              fwrite($w_destino, '    Data e hora: '.DataHora().$crlf);
              fwrite($w_destino, '    Status: '.$w_arq_script[$x]['status'].$crlf);
              if (strpos($w_arq_script[$x]['status'],'ERRO')!==false) {
                fwrite($w_log, '  ===>Script: '.$w_arq_script[$x]['nome'].$crlf);
                fwrite($w_log, '    Status: '.$w_arq_script[$x]['status'].$crlf);
              }
              for ($y = 0; $y < count($scr[$x]); $y++){
                fwrite($w_destino, '    '.$scr[$x][$y].$crlf);
                if ($w_conexao && $w_conftp) {
                  if (!($stmt = @oci_parse($conObj, substr($scr[$x][$y],0,-1)))) {
                    $error = @oci_error($stmt);
                    fwrite($w_log, '    '.substr($scr[$x][$y],0,-1).$crlf);
                    fwrite($w_log, '      [ERRO]'.$error['message'].$crlf);
                    $w_erro = true;
                  } else {
                    $w_lidos += 1;
                    if (!(@oci_execute($stmt, OCI_DEFAULT))) {
                      $error = @oci_error($stmt);
                      fwrite($w_log, '    '.substr($scr[$x][$y],0,-1).$crlf);
                      fwrite($w_log, '      [ERRO]'.$error['message'].$crlf);
                      $w_erro = true;
                      $w_rejeitados += 1;
                    }
                    @oci_free_statement($stmt);  
                  }
                }
              }
            }
  
            if (nvl($w_usuario,'')=='') {
              include_once($w_dir_volta.'classes/sp/db_getBenef.php');
              $sql = new db_getBenef; $RS_Pessoa = $sql->getInstanceOf($dbms,$w_cliente,null,null,null,null,'SBPI SUPORTE',1,null,null,null,null,null,null,null,null,null,null,null);
              foreach($RS_Pessoa as $row) { $RS_Pessoa = $row; break; }
              $w_usuario = f($RS_Pessoa,'sq_pessoa');
            }

            // Grava a ocorr�ncia
            $SQL = new dml_putDcOcorrencia; $SQL->getInstanceOf($dbms,'I',
              $esq['chave'],$w_cliente,$w_usuario,substr(formatadataedicao(time(),3),0,-3),
              basename($w_arquivo_script),basename($w_arquivo_script),filesize($w_arquivo_script),filetype($w_arquivo_script),
              basename($w_arquivo_log),basename($w_arquivo_log),filesize($w_arquivo_log),filetype($w_arquivo_log),
              $w_lidos,$w_rejeitados,basename($w_arquivo_script),basename($w_arquivo_log));

            // Finaliza as opera�oes de banco
            if ($w_conexao && $w_conftp) {
              if ($esq['efetivacao']==0 ||($esq['efetivacao']!=0 && !$w_erro)) {
                // Efetiva a transa��o
                @oci_commit($conObj);
                fwrite($w_destino, '[COMMIT DOS DADOS]'.$crlf);
                fwrite($w_destino, '  Data e hora: '.DataHora().$crlf);
                fwrite($w_destino, '  Status: [OK]'.$crlf);
              } else {
                fwrite($w_destino, '[EFETIVA��O DOS DADOS]'.$crlf);
                fwrite($w_destino, '  Data e hora: '.DataHora().$crlf);
                fwrite($w_destino, '  Status: [N�O EFETIVADO] Vide arquivo de log'.$crlf);
                fwrite($w_log, '[EFETIVA��O DOS DADOS]'.$crlf);
                fwrite($w_log, '  Status: [N�O EFETIVADO]'.$crlf);
              }
            }
            @fclose($w_destino);
            @fclose($w_log);

            // Se necess�rio, envia e-mail sobre a execu��o do esquema
            if ($mail['envia']==1 || ($$mail['envia']==2 and $w_erro)) {
              // Configura os dados necess�rios para envio
              if ($w_erro) {
                $w_subject = 'Esquema '.$esq['nome'].' processado COM erros - vide anexos';
                $w_attachments = $w_arquivo_script.';'.$w_arquivo_log;
              } else {
                $w_subject = 'Esquema '.$esq['nome'].' processado SEM erros - vide anexo';
                $w_attachments = $w_arquivo_script;
              }
              $w_mensagem  ='<HTML>'.$crlf;
              $w_mensagem .= BodyOpenMail(null).$crlf;
              if ($w_erro) {
                $w_mensagem .= 'O esquema '.$esq['nome'].' foi processado mas cont�m erros. Verifique os arquivos de processamento e de log (anexos).';
              } else {
                $w_mensagem .= 'O esquema '.$esq['nome'].' foi processado sem erros. Verifique o arquivo de processamento (anexo).';
              }
              $w_mensagem .= '</BODY>'.$crlf;
              $w_mensagem .= '</HTML>'.$crlf;
              

              // Envia o e-mail
              EnviaMail($w_subject,$w_mensagem,$mail['lista'],$w_attachments);
            }

          }
        }
      }
    }
  }
} 

// =========================================================================
// Gera SQL para arquivo de script
// -------------------------------------------------------------------------
function analisa_script($arquivo, &$comandos) {
  global $crlf;
  if (!($w_origem = @fopen($arquivo, 'r'))) { 
    return false; 
  } else {
    $l_arquivo = '';
    // Carrega o arquivo em uma string
    while (!feof($w_origem)) $l_arquivo .= fgets($w_origem);
    @fclose($w_origem);

    // Carrega cada comando SQL do arquivo em um array
    $l_comandos = explode(';', $l_arquivo);
  
    // Carrega os comandos SQL no array, removendo espa�os em branco desnecess�rios
    foreach($l_comandos as $k => $v) { 
      if (nvl($v,'')!='') $comandos[$k] = trim($v).';'; 
    }

    return true;
  }
}

// =========================================================================
// Gera inserts para o arquivo informado
// -------------------------------------------------------------------------
function analisa_arquivo($tabela, $arquivo, $delimitador, $sql_padrao, $campos, &$comandos) {
  global $crlf;
  if (!($w_origem = @fopen($arquivo, 'r'))) { return false; }
  $w_cont   = 0;
  
  // Recupera n�mero de pieces a serem encontrados em cada linha do arquivo de origem
  $pieces = 0;
  foreach($campos as $k => $v) {
    if ($v[1] > $pieces) $pieces = $v[1];
  }  

  while (!feof($w_origem)) {
    // Despreza a primeira linha, que deve sempre conter o nome das colunas
    if ($w_cont==0) $buffer = fgets($w_origem);

    // l� uma linha do arquivo de origem e converte aspas simples para evitar problemas
    // no comando SQL
    $buffer = str_replace($crlf,'',str_replace('\'','\'\'',fgets($w_origem)));
    
    // transforma a linha do arquivo em um array
    $valor = explode($delimitador,$buffer);
    if (nvl($valor[0],'')!='') {
      while (count($valor) < $pieces) {
        $buffer .= str_replace($crlf,'',str_replace('\'','\'\'',fgets($w_origem)));
        $valor = explode($delimitador,$buffer);
      }
    }
    
    if (nvl($valor[0],'')!='') {
      // Recupera o valor de cada campo a partir a linha e do mapeamento
      foreach($campos as $k => $v) {
        foreach($v as $k_col => $v_col) {
          // Prepara array para montagem do SQL
          if ($k_col==1) {
            // Recupera o peda�o do arquivo, tratando o valor default e remove espa�os antes e depois do valor
             $colunas[$k][$k_col] = trim(nvl($valor[$v_col-1],$colunas[$k][3])); 
          } else {
            $colunas[$k][$k_col] = $v_col;
          }
        }
      }
     
      // Gera o SQL e associa ao array de comandos passado por refer�ncia
      $array_sql[$w_cont] = monta_sql_map($tabela, $colunas);
      $w_cont += 1;  
    }
  }
  
  @fclose($w_origem);

  // Devolve o SQL gerado para o array de comandos passado por refer�ncia
  $comandos = $array_sql;
  return true;
}

// =========================================================================
// Retorna string com uma DML de inser��o de registro
// -------------------------------------------------------------------------
function monta_sql_insert($p_tabela, $p_registro) {
  // Parte inicial do comando
  $l_sql = 'insert into '.$p_tabela. ' (';
  
  // Recupera a lista dos nomes de campos
  foreach($p_registro as $k => $v) $l_sql .= $k.', ';
  
  // Parte intermedi�ria do comando
  $l_sql = substr($l_sql,0,-2).') values (';

  // Recupera a lista dos nomes de campos
  foreach($p_registro as $k => $v) $l_sql .= monta_valor_insert($v).', ';

  // Parte final do comando
  $l_sql = substr($l_sql,0,-2).');';
  return $l_sql; 
}

// =========================================================================
// Retorna valor a ser colocado na DML de inser��o de registro
// -------------------------------------------------------------------------
function monta_valor_insert($p_valor) {
  $l_tipo    = $p_valor[0];
  $l_valor   = $p_valor[1];
  $l_formato = $p_valor[2];
  
  if (nvl($p_valor,'')=='') {
    $l_valor = 'null';
  } else {
    switch ($l_tipo) {
      case B_VARCHAR: $l_valor = '\''.$l_valor.'\''; break;
      case B_DATE:    $l_valor = 'to_date(\''.$l_valor.'\',\''.$l_formato.'\')'; break;
      default:        $l_valor = $l_valor;
    } 
  }

  return $l_valor; 
}

// =========================================================================
// Retorna string com uma DML padr�o para inser��o de registro oriundo de TXT
// -------------------------------------------------------------------------
function monta_sql_map($p_tabela, $p_mapeamento) {
  
  // Parte inicial do comando
  $l_sql = 'insert into '.$p_tabela. ' (';
  
  // Recupera a lista dos nomes de campos
  foreach($p_mapeamento as $k => $v) $l_sql .= $k.', ';
  
  // Parte intermedi�ria do comando
  $l_sql = substr($l_sql,0,-2).') values (';

  // Recupera a lista dos nomes de campos
  foreach($p_mapeamento as $k => $v) {
    $l_sql .= monta_valor_map($v,$p_linha, $p_delimitador).', ';
  }

  // Parte final do comando
  $l_sql = substr($l_sql,0,-2).');';
  return $l_sql; 
}

// =========================================================================
// Retorna formato do valor a ser colocado na DML de inser��o de registro
// -------------------------------------------------------------------------
function monta_valor_map($p_valor, $p_linha) {

  $l_tipo    = $p_valor[0];
  $l_valor   = $p_valor[1];
  $l_tamanho = $p_valor[2];
  $l_formato = $p_valor[2];
  $l_default = $p_valor[3];
  
  if (nvl($l_valor,'')=='') {
    $l_valor = 'null';
  } else {
    switch ($l_tipo) {
      case B_VARCHAR: $l_valor = 'substr(\''.$l_valor.'\',1,'.$l_tamanho.')'; break;
      case B_DATE:    
        if (strpos(upper($l_formato),'HH24')!==false) $l_tam = strlen($l_tamanho) - 2; else $l_tam = strlen($l_tamanho);
        $l_valor = 'to_date('.'substr(\''.$l_valor.'\',0,'.$l_tam.')'.',\''.$l_formato.'\')';
        break;
      default:        $l_valor = $l_valor;
    } 
  }

  return $l_valor; 
}

?>
