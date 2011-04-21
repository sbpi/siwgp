#!/bin/sh
# Script para execuÃ§ da rotina de envio automÃ¡co de e-mails

# ATENÇÃO: O CORRETO FUNCIONAMENTO DEPENDE DA CONSTANTE "_DATABASE_NAME" DO
# ARQUIVO classes/db/db_constants.php TER UM VALOR QUE CORREPONDA
# A UMA ENTRADA EM TNSNAMES.ORA, DEVENDO SER IGUAL ATÉ NAS MAÍUSCULAS E MINÚSCULAS.

# 1 - CONFIGURA VARIÁVEIS DE AMBIENTE DO ORACLE
#     ajustar para a instalação do oracle do servidor onde a rotina for executada
ORACLE_HOME=/home/oracle/orahome/product/9.2.0
ORACLE_BASE=/home/oracle/orahome
NLS_LANG='BRAZILIAN PORTUGUESE_BRAZIL.WE8MSWIN1252'
PATH=$ORACLE_HOME/bin:$PATH
ORA_NLS33=/home/oracle/orahome/product/9.2.0/ocommon/nls/admin/data
ORACLE_SID=XE
export NLS_LANG ORACLE_SID ORACLE_HOME ORACLE_BASE NLS_LANG PATH ORA_NLS33
umask 002 

# 2 - EXECUTA A ROTINA
#     verificar parâmetros conforme orientaçãos contidas no arquivo mail_envio.php

/usr/local/bin/php -c /usr/local/lib/php.ini /var/www/html/siw/mail_envio.php 1 1 SIW GERA
/usr/local/bin/php -c /usr/local/lib/php.ini /var/www/html/siw/mail_envio.php 9634 1 SIW GERA
/usr/local/bin/php -c /usr/local/lib/php.ini /var/www/html/siw/mail_envio.php 14375 1 SIW GERA
