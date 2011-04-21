<?php
extract($GLOBALS); include_once($w_dir_volta.'classes/db/DatabaseQueriesFactory.php');
/**
* class dml_SiwMenu
*
* { Description :- 
*    Manipula registros de SIW_MENU
* }
*/

class dml_SiwMenu {
   function getInstanceOf($dbms, $operacao, $chave, $sq_menu_pai, $link, $p1, $p2, $p3, $p4, $sigla, 
         $imagem, $target, $emite_os, $consulta_opiniao, $envia_email, $exibe_relatorio, $como_funciona, 
         $vinculacao, $data_hora, $envia_dia_util, $descricao, $justificativa, $finalidade, $cliente, 
         $nome, $acesso_geral, $consulta_geral, $sq_modulo, $sq_unidade_exec, $tramite, $ultimo_nivel, 
         $descentralizado, $externo, $ativo, $ordem, $envio, $controla_ano, $libera_edicao, $numeracao, 
         $numerador, $sequencial, $ano_corrente, $prefixo, $sufixo, $envio_inclusao, $cancela_sem_tramite) {
     extract($GLOBALS,EXTR_PREFIX_SAME,'strchema'); $sql=$strschema.'sp_putSiwMenu';
     $params=array('operacao'           =>array($operacao,              B_VARCHAR,      1),
                   'chave'              =>array($chave,                 B_NUMERIC,     32),
                   'cliente'            =>array($cliente,               B_NUMERIC,     32),
                   'nome'               =>array($nome,                  B_VARCHAR,     40),
                   'acesso_geral'       =>array($acesso_geral,          B_VARCHAR,      1),
                   'consulta_geral'     =>array($consulta_geral,        B_VARCHAR,      1),
                   'sq_modulo'          =>array($sq_modulo,             B_NUMERIC,     32),
                   'tramite'            =>array($tramite,               B_VARCHAR,      1),
                   'ultimo_nivel'       =>array($ultimo_nivel,          B_VARCHAR,      1),
                   'descentralizado'    =>array($descentralizado,       B_VARCHAR,      1),
                   'externo'            =>array($externo,               B_VARCHAR,      1),
                   'ativo'              =>array($ativo,                 B_VARCHAR,      1),
                   'ordem'              =>array($ordem,                 B_NUMERIC,     32),
                   'sq_menu_pai'        =>array($sq_menu_pai,           B_NUMERIC,     32),
                   'link'               =>array($link,                  B_VARCHAR,     60),
                   'p1'                 =>array($p1,                    B_NUMERIC,     32),
                   'p2'                 =>array($p2,                    B_NUMERIC,     32),
                   'p3'                 =>array($p3,                    B_NUMERIC,     32),
                   'p4'                 =>array($p4,                    B_NUMERIC,     32),
                   'sigla'              =>array($sigla,                 B_VARCHAR,     10),
                   'imagem'             =>array($imagem,                B_VARCHAR,     60),
                   'target'             =>array($target,                B_VARCHAR,     15),
                   'sq_unidade_exec'    =>array($sq_unidade_exec,       B_NUMERIC,     32),
                   'emite_os'           =>array($emite_os,              B_VARCHAR,      1),
                   'consulta_opiniao'   =>array($consulta_opiniao,      B_VARCHAR,      1),
                   'envia_email'        =>array($envia_email,           B_VARCHAR,      1),
                   'exibe_relatorio'    =>array($exibe_relatorio,       B_VARCHAR,      1),
                   'como_funciona'      =>array($como_funciona,         B_VARCHAR,   4000),
                   'vinculacao'         =>array($vinculacao,            B_VARCHAR,      1),
                   'data_hora'          =>array($data_hora,             B_VARCHAR,      1),
                   'envia_dia_util'     =>array($envia_dia_util,        B_VARCHAR,      1),
                   'descricao'          =>array($descricao,             B_VARCHAR,      1),
                   'justificativa'      =>array($justificativa,         B_VARCHAR,      1),
                   'finalidade'         =>array($finalidade,            B_VARCHAR,    200),
                   'envio'              =>array($envio,                 B_VARCHAR,      1),
                   'controla_ano'       =>array($controla_ano,          B_VARCHAR,      1),
                   'libera_edicao'      =>array($libera_edicao,         B_VARCHAR,      1),
                   'numeracao'          =>array(tvl($numeracao),        B_INTEGER,     32),
                   'numerador'          =>array(tvl($numerador),        B_INTEGER,     32),
                   'sequencial'         =>array(tvl($sequencial),       B_INTEGER,     32),
                   'ano_corrente'       =>array(tvl($ano_corrente),     B_INTEGER,     32),
                   'prefixo'            =>array(tvl($prefixo),          B_VARCHAR,     10),
                   'sufixo'             =>array(tvl($sufixo),           B_VARCHAR,     10),
                   'envio_inclusao'     =>array($envio_inclusao,        B_VARCHAR,      1),
                   'cancela_sem_tramite'=>array($cancela_sem_tramite,   B_VARCHAR,      1)
                  );
     $lql = new DatabaseQueriesFactory; $l_rs = $lql->getInstanceOf($sql, $dbms, $params, DB_TYPE);
     $l_error_reporting = error_reporting(); 
     error_reporting(0); 
     if(!$l_rs->executeQuery()) { 
       error_reporting($l_error_reporting); 
       TrataErro($sql, $l_rs->getError(), $params, __FILE__, __LINE__, __CLASS__); 
     } else {
       error_reporting($l_error_reporting); 
       return true;
     }
   }
}
?>
