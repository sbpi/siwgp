<?php
extract($GLOBALS); include_once($w_dir_volta.'classes/db/DatabaseQueriesFactory.php');
/**
* class dml_sIWCliConf
*
* { Description :- 
*    Manipula registros de siw_cliente
* }
*/

class dml_sIWCliConf {
   function getInstanceOf($dbms, $chave, $tamanho_minimo_senha, $tamanho_maximo_senha, $maximo_tentativas, $dias_vigencia_senha, $dias_aviso_expiracao, $smtp_server, $siw_email_nome, $siw_email_conta, $siw_email_senha, $logo, $logo1, $fundo, $tipo, $upload_maximo) {
     extract($GLOBALS,EXTR_PREFIX_SAME,'strchema'); $sql=$strschema.'SP_PUTSIWCLICONF';
     $params=array('p_chave'                     =>array($chave,                                           B_INTEGER,        32),
                   'p_tamanho_minimo_senha'      =>array(tvl($tamanho_minimo_senha),                       B_INTEGER,        32),
                   'p_tamanho_maximo_senha'      =>array(tvl($tamanho_maximo_senha),                       B_INTEGER,        32),
                   'p_maximo_tentativas'         =>array(tvl($maximo_tentativas),                          B_INTEGER,        32),
                   'p_dias_vigencia_senha'       =>array(tvl($dias_vigencia_senha),                        B_INTEGER,        32),
                   'p_dias_aviso_expiracao'      =>array(tvl($dias_aviso_expiracao),                       B_INTEGER,        32),
                   'p_smtp_server'               =>array(tvl($smtp_server),                                B_VARCHAR,        60),
                   'p_siw_emaip_nome'            =>array(tvl($siw_email_nome),                             B_VARCHAR,        60),
                   'p_siw_emaip_conta'           =>array(tvl($siw_email_conta),                            B_VARCHAR,        60),
                   'p_siw_emaip_senha'           =>array(tvl($siw_email_senha),                            B_VARCHAR,        60),
                   'p_logo'                      =>array(tvl($logo),                                       B_VARCHAR,        60),
                   'p_logo1'                     =>array(tvl($logo1),                                      B_VARCHAR,        60),
                   'p_fundo'                     =>array(tvl($fundo),                                      B_VARCHAR,        60),
                   'p_tipo'                      =>array($tipo,                                            B_VARCHAR,        15),
                   'p_upload_maximo'             =>array($upload_maximo,                                   B_INTEGER,        32)
                  );
     $lql = new DatabaseQueriesFactory; $l_rs = $lql->getInstanceOf($sql, $dbms, $params, DB_TYPE);
     $l_error_reporting = error_reporting(); error_reporting(0); 
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
