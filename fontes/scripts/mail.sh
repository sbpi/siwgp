#!/bin/sh
# Script para execuç da rotina de envio automáco de e-mails

# ATEN��O: O CORRETO FUNCIONAMENTO DEPENDE DA CONSTANTE "_DATABASE_NAME" DO
# ARQUIVO classes/db/db_constants.php TER UM VALOR QUE CORREPONDA
# A UMA ENTRADA EM TNSNAMES.ORA, DEVENDO SER IGUAL AT� NAS MA�USCULAS E MIN�SCULAS.

# 1 - CONFIGURA VARI�VEIS DE AMBIENTE DO ORACLE
#     ajustar para a instala��o do oracle do servidor onde a rotina for executada
ORACLE_HOME=/home/oracle/orahome/product/9.2.0
ORACLE_BASE=/home/oracle/orahome
NLS_LANG='BRAZILIAN PORTUGUESE_BRAZIL.WE8MSWIN1252'
PATH=$ORACLE_HOME/bin:$PATH
ORA_NLS33=/home/oracle/orahome/product/9.2.0/ocommon/nls/admin/data
ORACLE_SID=XE
export NLS_LANG ORACLE_SID ORACLE_HOME ORACLE_BASE NLS_LANG PATH ORA_NLS33
umask 002 

# 2 - EXECUTA A ROTINA
#     verificar par�metros conforme orienta��os contidas no arquivo mail_envio.php

/usr/local/bin/php -c /usr/local/lib/php.ini /var/www/html/siw/mail_envio.php 1 1 SIW GERA
/usr/local/bin/php -c /usr/local/lib/php.ini /var/www/html/siw/mail_envio.php 9634 1 SIW GERA
/usr/local/bin/php -c /usr/local/lib/php.ini /var/www/html/siw/mail_envio.php 14375 1 SIW GERA
