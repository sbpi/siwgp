<?php
header('Expires: '.-1500);
session_start();
include_once('constants.inc');
include_once('jscript.php');
include_once('funcoes.php');
include_once('classes/db/abreSessao.php');
include_once('classes/sp/db_getUserData.php');
include_once('classes/sp/db_getPersonData.php');
include_once('classes/sp/db_getPersonList.php');
include_once('classes/sp/db_getBenef.php');
include_once('classes/sp/db_getCustomerSite.php');
include_once('classes/sp/db_getCustomerData.php');
include_once('classes/sp/db_getLinkData.php');
include_once('classes/sp/db_verificaAssinatura.php');
include_once('classes/sp/dml_putSiwUsuario.php');
include_once('classes/sp/dml_putPessoa.php');
include_once('funcoes/selecaoSexo.php');
include_once('funcoes/selecaoPais.php');
include_once('funcoes/selecaoEstado.php');
include_once('funcoes/selecaoCidade.php');
include_once('funcoes/selecaoUnidade.php');
include_once('funcoes/selecaoLocalizacao.php');
include_once('funcoes/selecaoVinculo.php');
include_once('funcoes/selecaoTipoAutenticacao.php');

// =========================================================================
//  /pessoa.php
// ------------------------------------------------------------------------
// Nome     : Alexandre Vinhadelli Papadópolis
// Descricao: Gerencia o cadastro de pessoas do sistema
// Mail     : alex@sbpi.com.br
// Criacao  : 25/11/2002 16:17
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
//                   = V   : Envio
//                   = L   : Listagem
//                   = P   : Pesquisa
//                   = D   : Detalhes
//                   = N   : Nova solicitação de envio

// Verifica se o usuário está autenticado
if ($_SESSION['LOGON']!='Sim') { EncerraSessao(); }

// Declaração de variáveis
$dbms = new abreSessao; $dbms = $dbms->getInstanceOf($_SESSION['DBMS']);

// Carrega variáveis locais com os dados dos parâmetros recebidos
$par        = upper($_REQUEST['par']);
$P1         = nvl($_REQUEST['P1'],0);
$P2         = nvl($_REQUEST['P2'],0);
$P3         = nvl($_REQUEST['P3'],0);
$P4         = nvl($_REQUEST['P4'],0);
$TP         = nvl($_REQUEST['TP'],$_REQUEST['p_tp']);
$SG         = upper($_REQUEST['SG']);
$R          = $_REQUEST['R'];
$O          = upper($_REQUEST['O']);
$w_assinatura   = upper($_REQUEST['w_assinatura']);
$w_pagina       = 'pessoa.php?par=';
$w_Disabled     = 'ENABLED';
$w_dir_volta    = '';
if (($par=='DESPESA' || $par=='TRECHO' || $par=='VISUAL') && $O=='A' && $_REQUEST['w_Handle']=='') $O='L';
elseif($par=='FORNECEDORES' && ($O=='' || $O=='I' || $O=='A')) $O='P';

// Configura o valor de O se for a tela de listagem
switch ($O) {
    case 'I':
        if ($SG=='SGUSU' || $SG=='CLUSUARIO') $w_TP=$TP.' - Novo Acesso';
        elseif ($SG=='RHUSU') $w_TP=$TP.' - Nova Pessoa';
        else$w_TP=$TP.' - Inclusão';
        break;
    case 'A': // Se a chamada for para as rotinas de visualização, não concatena nada
    if ($par=='VISUAL' || $par=='ENVIAR') $w_TP=$TP;
    else $w_TP=$TP.' - Alteração';
    break;
    case 'D':
        if ($SG=='SGUSU' || $SG=='CLUSUARIO') $w_TP=$TP.' - Bloqueio de Acesso';
        elseif ($SG=='RHUSU') $w_TP=$TP.' - Desligamento';
        break;
    case 'T': $w_TP=$TP.' - Ativação';  break;
    case 'E': $w_TP=$TP.' - Exclusao';  break;
    case 'V': $w_TP=$TP.' - Envio';     break;
    case 'P': $w_TP=$TP.' - Filtragem'; break;
    default:
        if ($par=='BUSCAUSUARIO') $w_TP=$TP.' - Busca usuário';
        else $w_TP=$TP.' - Listagem';
        break;
}
$w_data_banco = time();

// Se for acesso do módulo de gerenciamento de clientes do SIW, o cliente será determinado por parâmetro;
// caso contrário, o cliente será a empresa ao qual o usuário logado está vinculado.
$w_cliente  = RetornaCliente();
$w_usuario  = RetornaUsuario();

Main();
FechaSessao($dbms);

// =========================================================================
// Rotina de beneficiário
// -------------------------------------------------------------------------
function Benef() {
    extract($GLOBALS);
    global $w_Disabled;
    // Nesta rotina, P1 = 0 indica que não pode haver troca do beneficiário
    //                  = 1 indica que pode haver troca de beneficiário
    //               P2 = 0 indica que não pegará os dados bancários, nem da forma de pagamento
    //                  = 1 indica que pegará os dados bancários, mas não da forma de pagamento
    //                  = 2 indica que pegará os dados bancários e também da forma de pagamento
    $w_readonly       = '';
    $w_erro           = '';
    $w_troca          = $_REQUEST['w_troca'];
    $w_sq_pessoa      = $_REQUEST['w_sq_pessoa'];
    $p_data_inicio    = upper($_REQUEST['p_data_inicio']);
    $p_data_fim       = upper($_REQUEST['p_data_fim']);
    $p_solicitante    = upper($_REQUEST['p_solicitante']);
    $p_numero         = upper($_REQUEST['p_numero']);
    $p_ordena         = $_REQUEST['p_ordena'];
    $p_localizacao    = upper($_REQUEST['p_localizacao']);
    $p_lotacao        = upper($_REQUEST['p_lotacao']);
    $p_nome           = upper($_REQUEST['p_nome']);
    $p_gestor         = upper($_REQUEST['p_gestor']);
    $w_sq_solicitacao = $_REQUEST['w_sq_solicitacao'];
    $w_cpf            = $_REQUEST['w_cpf'];
    // Verifica se há necessidade de recarregar os dados da tela a partir
    // da própria tela (se for recarga da tela) ou do banco de dados (se não for inclusão)
    if ($w_troca>'' && $O!='E') {
        // Se for recarga da página
        $w_username_adm         = $_REQUEST['w_username_adm'];
        $w_senha_adm            = $_REQUEST['w_senha_adm'];
        $w_username             = $_REQUEST['w_username'];
        $w_nome                 = $_REQUEST['w_nome'];
        $w_nome_resumido        = $_REQUEST['w_nome_resumido'];
        $w_sexo                 = $_REQUEST['w_sexo'];
        $w_rg                   = $_REQUEST['w_rg'];
        $w_passaporte           = $_REQUEST['w_passaporte'];
        $w_nascimento           = $_REQUEST['w_nascimento'];
        $w_end                  = $_REQUEST['w_end'];
        $w_comple               = $_REQUEST['w_comple'];
        $w_pais                 = $_REQUEST['w_pais'];
        $w_uf                   = $_REQUEST['w_uf'];
        $w_cidade               = $_REQUEST['w_cidade'];
        $w_cep                  = $_REQUEST['w_cep'];
        $w_telefone             = $_REQUEST['w_telefone'];
        $w_fax                  = $_REQUEST['w_fax'];
        $w_email                = $_REQUEST['w_email'];
        $w_sq_unidade_lotacao   = $_REQUEST['w_sq_unidade_lotacao'];
        $w_sq_localizacao       = $_REQUEST['w_sq_localizacao'];
        $w_projeto              = $_REQUEST['w_projeto'];
        $w_entrada              = $_REQUEST['w_entrada'];
        $w_saldo_ferias         = $_REQUEST['w_saldo_ferias'];
        $w_limite_emprestimo    = $_REQUEST['w_limite_emprestimo'];
        $w_sq_tipo_vinculo      = $_REQUEST['w_sq_tipo_vinculo'];
        $w_gestor_seguranca     = $_REQUEST['w_gestor_seguranca'];
        $w_gestor_sistema       = $_REQUEST['w_gestor_sistema'];
        $w_gestor_portal        = $_REQUEST['w_gestor_portal'];
        $w_gestor_dashboard     = $_REQUEST['w_gestor_dashboard'];
        $w_gestor_conteudo      = $_REQUEST['w_gestor_conteudo'];
        $w_tipo_autenticacao    = $_REQUEST['w_tipo_autenticacao'];
        $w_username_ant         = $_REQUEST['w_username_ant'];
    } else {
        if ($O=='I' && $w_sq_pessoa=='' && $w_cpf>'' && $SG=='SGUSU') {
          $SQL = new db_getPersonData; $RS = $SQL->getInstanceOf($dbms,$w_cliente,null,$w_cpf,null);
          if (count($RS)>0) {
            if (nvl(f($RS,'username'),'')!='') {
              ScriptOpen('JavaScript');
              ShowHTML('  alert(\'CPF já associado a outro usuário!\');');
              ShowHTML('  history.back(1);');
              ScriptClose();
              exit;
            } else {
              $w_sq_pessoa = f($RS,'sq_pessoa');
            }
          }
        }
        if (strpos('IATDEV',$O)!==false) {
            if (nvl($w_sq_pessoa,'')!='') {
                // Recupera os dados do beneficiário em co_pessoa
                $SQL = new db_getPersonData; $RS = $SQL->getInstanceOf($dbms,$w_cliente,$w_sq_pessoa,null,null);
                if (count($RS)) {
                    $w_cpf                  = f($RS,'cpf');
                    $w_username             = f($RS,'username');
                    $w_nome                 = f($RS,'Nome');
                    $w_nome_resumido        = f($RS,'Nome_Resumido');
                    $w_sexo                 = f($RS,'sexo');
                    $w_email                = f($RS,'Email');
                    $w_sq_unidade_lotacao   = f($RS,'sq_unidade');
                    $w_sq_localizacao       = f($RS,'sq_localizacao');
                    $w_sq_tipo_vinculo      = f($RS,'sq_tipo_vinculo');
                    $w_gestor_seguranca     = f($RS,'gestor_seguranca');
                    $w_gestor_sistema       = f($RS,'gestor_sistema');
                    $w_gestor_portal        = f($RS,'gestor_portal');
                    $w_gestor_dashboard     = f($RS,'gestor_dashbord');
                    $w_gestor_conteudo      = f($RS,'gestor_conteudo');
                    $w_tipo_autenticacao    = f($RS,'tipo_autenticacao');
                    $w_username_ant         = f($RS,'username');
                }
            } elseif (nvl($w_username,'')>'') {
                // Recupera os dados do beneficiário em co_pessoa
                $SQL = new db_getPersonData; $RS = $SQL->getInstanceOf($dbms,$w_cliente,null,$w_username,null);
                if (count($RS)) {
                    $w_sq_pessoa            = f($RS,'sq_pessoa');
                    $w_nome                 = f($RS,'Nome');
                    $w_nome_resumido        = f($RS,'Nome_Resumido');
                    $w_cpf                  = f($RS,'cpf');
                    $w_sexo                 = f($RS,'sexo');
                    $w_email                = f($RS,'Email');
                    $w_sq_unidade_lotacao   = f($RS,'sq_unidade');
                    $w_sq_localizacao       = f($RS,'sq_localizacao');
                    $w_sq_tipo_vinculo      = f($RS,'sq_tipo_vinculo');
                    $w_gestor_seguranca     = f($RS,'gestor_seguranca');
                    $w_gestor_sistema       = f($RS,'gestor_sistema');
                    $w_gestor_portal        = f($RS,'gestor_portal');
                    $w_gestor_dashboard     = f($RS,'gestor_dashbord');
                    $w_gestor_conteudo      = f($RS,'gestor_conteudo');
                    $w_tipo_autenticacao    = f($RS,'tipo_autenticacao');
                    $w_username_ant         = f($RS,'username');
                }
            }
        }
        // O bloco abaixo recupera os dados bancários e a forma de pagamento,
        // dependendo do valor de P1 e se não for inclusão
        // O local onde os dados bancários e a forma de pagamento serão recuperados
        // depende do tipo de documento.
        if ($O!='I' && ($P2==1 || $P2==2)) {
            // Vide finalidade do parâmetro no cabeçalho da rotina
        }
    }
    Cabecalho();
    head();
    Estrutura_CSS($w_cliente);
    // Monta o código JavaScript necessário para validação de campos e preenchimento automático de máscara,
    // tratando as particularidades de cada serviço
    ScriptOpen('JavaScript');
    Modulo();
    FormataCPF();
    FormataCEP();
    CheckBranco();
    FormataValor();
    FormataData();
    SaltaCampo();
    FormataDataHora();
    ValidateOpen('Validacao');
    if ($w_cpf=="" || (!(strpos($_REQUEST['botao'],"Procurar")===false)) || (!(strpos($_REQUEST['botao'],"Troca")===false))) {
        // Se o beneficiário ainda não foi selecionado
        ShowHTML('  if (theForm.Botao.value == \'Procurar\') {');
        Validate('w_nome','Nome','','1','4','20','1','');
        ShowHTML('  theForm.Botao.value = \'Procurar\';');
        ShowHTML('}');
        ShowHTML('else {');
        Validate('w_cpf','CPF','CPF','1','14','14','','0123456789-.');
        if ($P2==2) {
            Validate('w_frm_pag','Forma de pagamento','SELECT','1','1','10','','1');
        }
        ShowHTML('}');
    } elseif ($O=='I' || $O=='A') {
        ShowHTML('  if (theForm.Botao.value == \'Troca\') { return true; }');
        Validate('w_nome','Nome','1',1,5,60,'1','1');
        Validate('w_nome_resumido','Nome resumido','1',1,2,21,'1','1');
        Validate('w_sexo','Sexo','SELECT',1,1,1,'MF','');
        if ($SG=='RHUSU') {
            if (strlen($w_cpf)!=10) {
                Validate('w_rg','RG','1',1,5,80,'1','1');
                Validate('w_passaporte','Passaporte','1','',1,15,'1','1');
            } else {
                Validate('w_passaporte','Passaporte','1',1,1,15,'1','1');
            }
            Validate('w_nascimento','Data de Nascimento','DATA',1,10,10,'',1);
            Validate('w_end','Endereço','1',1,4,50,'1','1');
            Validate('w_pais','País','SELECT',1,1,10,'1','1');
            Validate('w_uf','UF','SELECT',1,1,10,'1','1');
            Validate('w_cidade','Cidade','SELECT',1,1,10,'','1');
            if ($w_pais=='' || $w_pais==1) {
                Validate('w_cep','CEP','1','',1,10,'','1');
            } else {
                Validate('w_cep','CEP','1',1,6,10,'','1');
            }
            Validate('w_telefone','Telefone','1',1,7,40,'1','1');
            Validate('w_fax','Fax','1','',4,20,'1','1');
        } elseif ($SG=='SGUSU' || $SG=='CLUSUARIO') {
            Validate('w_username','Username','1',1,2,60,'1','1');
            Validate('w_email','E-Mail','1','1',4,60,'1','1');
        }
        Validate('w_sq_unidade_lotacao','Unidade de lotação','HIDDEN',1,1,10,'','1');
        Validate('w_sq_localizacao','Localização','SELECT',1,1,10,'','1');
        Validate('w_sq_tipo_vinculo','Vínculo com a organização','SELECT',1,1,10,'','1');
        if (($O=='I' || $w_username_ant!= $w_username) && strpos('AO',$w_tipo_autenticacao)!==false) {
          Validate('w_username_adm','Usuário da rede','1',1,2,60,'1','1');
          Validate('w_senha_adm','Senha do usuário da rede','1','1','2','30','1','1');
        }
        if ($SG=='SGUSU' || $SG=='CLUSUARIO') {
            Validate('w_assinatura','Assinatura Eletrônica','1','1','6','30','1','1');
        }
    } elseif ($O=='E' || $O=='T' || $O=='D') {
        Validate('w_assinatura','Assinatura Eletrônica','1','1','6','30','1','1');
    }
    ValidateClose();
    ScriptClose();
    ShowHTML('</HEAD>');
    if ($P1!=0 && ($w_cpf=='' || (isset($_REQUEST['Botao']) && ((!(strpos($_REQUEST['Botao'],'Troca')===false)) || (!(strpos($_REQUEST['Botao'],'Procurar')===false)))))) {
        // Se o beneficiário ainda não foi selecionado
        if (isset($_REQUEST['Botao']) && (!(strpos($_REQUEST['botao'],'Procurar'))===false)) {
            // Se está sendo feita busca por nome
            if ($w_troca!='w_sq_localizacao') { BodyOpen('onLoad=\'this.focus()\';'); }
        } else {
            BodyOpen('onLoad=\'document.Form.w_cpf.focus()\';');
        }
    } elseif ($w_troca>'') {
        BodyOpen('onLoad=\'document.Form.'.$w_troca.'.focus()\';');
    } elseif (!(strpos('ETDV',$O)===false)) {
        BodyOpen('onLoad=\'document.Form.w_assinatura.focus()\';');
    } else {
        BodyOpen('onLoad=\'document.Form.w_nome.focus()\';');
    }
    Estrutura_Topo_Limpo();
    Estrutura_Menu();
    Estrutura_Corpo_Abre();
    Estrutura_Texto_Abre();
    ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
    if (!(strpos('IAETDV',$O)===false)) {
        if (!(strpos('ETDV',$O)===false)) {
            $w_Disabled=' DISABLED ';
            if ($O=='V') $w_Erro = Validacao($w_sq_solicitacao, $SG);
        }
        if ($w_cpf=='' || (isset($_REQUEST['Botao']) && ((!(strpos($_REQUEST['Botao'],'Troca')===false)) || (!(strpos($_REQUEST['Botao'],'Procurar')===false))))) {
            // Se o beneficiário ainda não foi selecionado
            AbreForm('Form',$w_pagina.$par,'POST','return(Validacao(this))',null,$P1,$P2,$P3,$P4,$TP,$SG,$R,$O);
        } else {
            AbreForm('Form',$w_pagina.'Grava','POST','return(Validacao(this))',null,$P1,$P2,$P3,$P4,$TP,$SG,$w_pagina.$par,$O);
        }
        ShowHTML('<INPUT type="hidden" name="w_troca" value="">');
        ShowHTML('<INPUT type="hidden" name="w_sq_solicitacao" value="'.$w_sq_solicitacao.'">');
        ShowHTML('<INPUT type="hidden" name="w_sq_pessoa" value="'.$w_sq_pessoa.'">');
        ShowHTML('<INPUT type="hidden" name="w_username_ant" value="'.$w_username_ant.'">');
        ShowHTML('<INPUT type="hidden" name="w_cliente" value="'.$w_cliente.'">');
        if (!(strpos('ETDV',$O)===false)) {
          ShowHTML('<INPUT type="hidden" name="w_tipo_autenticacao" value="'.$w_tipo_autenticacao.'">');
        }
        ShowHTML(MontaFiltro('POST'));
        if ($P1!=0 && ($w_cpf=='' || (isset($_REQUEST['Botao']) && ((!(strpos($_REQUEST['Botao'],'Troca')===false)) || (!(strpos($_REQUEST['Botao'],'Procurar')===false)))))) {
            $w_frm_pag = $_REQUEST['w_frm_pag'];
            $w_nome    = $_REQUEST['w_nome'];
            ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td>');
            ShowHTML('    <table border="0">');
            ShowHTML('        <tr><td colspan=3><font size=2>Informe os dados abaixo e clique no botão "Selecionar" para continuar.</TD>');
            ShowHTML('        <tr><td><font size=1><b><u>C</u>PF:<br><INPUT ACCESSKEY="C" TYPE="text" Class="sti" NAME="w_cpf" VALUE="'.$w_cpf.'" SIZE="14" MaxLength="14" onKeyDown="FormataCPF(this, event);">');
            ShowHTML('            <td valign="bottom"><INPUT class="stb" TYPE="submit" NAME="Botao" VALUE="Selecionar" onClick="Botao.value=this.value; document.Form.action=\''.$w_pagina.$par.'\'">');
            if ($SG=='SGUSU' || $SG=='RHUSU' || $SG=='CLUSUARIO') { // Tela de usuários do SG ou RH
                ShowHTML('            <input class="stb" type="button" onClick="location.href=\''.$R.'&w_cliente='.$_REQUEST['w_cliente'].'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'\';" name="Botao" value="Cancelar">');
            }
            ShowHTML('        <tr><td colspan=3><p>&nbsp</p>');
            ShowHTML('        <tr><td colspan=3 heigth=1 bgcolor="#000000">');
            ShowHTML('        <tr><td colspan=3>');
            ShowHTML('             <font size=1><b><u>P</u>rocurar pelo nome:</b> (Informe qualquer parte do nome SEM ACENTOS)<br><INPUT ACCESSKEY="P" TYPE="text" Class="sti" NAME="w_nome" VALUE="'.$w_nome.'" SIZE="20" MaxLength="20">');
            ShowHTML('              <INPUT class="stb" TYPE="submit" NAME="Botao" VALUE="Procurar" onClick="Botao.value=this.value; document.Form.action=\''.$w_pagina.$par.'\'">');
            ShowHTML('      </table>');
            if ($_REQUEST['w_nome']>"") {
                $SQL = new db_getPersonList; $RS = $SQL->getInstanceOf($dbms,$w_cliente,null,"PESSOA",$_REQUEST['w_nome'],null,null,null);
                ShowHTML('<tr><td align="center" colspan=3>');
                ShowHTML('    <TABLE WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
                ShowHTML('        <tr bgcolor="'.$conTrBgColor.'" align="center">');
                ShowHTML('          <td><b>Nome</font></td>');
                ShowHTML('          <td><b>Nome resumido</font></td>');
                ShowHTML('          <td><b>CPF</font></td>');
                ShowHTML('          <td><b>Operações</font></td>');
                ShowHTML('        </tr>');
                if (count($RS)==0) {
                    ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td colspan=4 align="center"><font  size="2"><b>Não há pessoas (não usuárias) que contenham o texto informado.</b></td></tr>');
                } else {
                    foreach($RS as $row) {
                        ShowHTML('      <tr bgcolor="'.$conTrBgColor.'" valign="top">');
                        ShowHTML('        <td><font  size="1">'.f($row,'nome').'</td>');
                        ShowHTML('        <td><font  size="1">'.f($row,'nome_resumido').'</td>');
                        ShowHTML('        <td align="center"><font  size="1">'.nvl(f($row,'cpf'),"---").'</td>');
                        ShowHTML('        <td nowrap>');
                        ShowHTML('          <A class="hl" HREF="pessoa.php?par=BENEF&R='.$R.'&O=I&w_cpf='.f($row,'cpf').'&w_sq_pessoa='.f($row,'sq_pessoa').'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'">Selecionar</A>&nbsp');
                        ShowHTML('        </td>');
                        ShowHTML('      </tr>');
                    }
                }
                ShowHTML('      </center>');
                ShowHTML('    </table>');
                ShowHTML('  </td>');
                ShowHTML('</tr>');
                DesConectaBD();
            }
        } else {
            ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td>');
            ShowHTML('    <table width="97%" border="0">');
            ShowHTML('      <tr><td><table border="0" width="100%">');
            ShowHTML('       <tr><td><font size=1>CPF:</font><br><b><font size=2>'.$w_cpf);
            ShowHTML('                   <INPUT type="hidden" name="w_cpf" value="'.$w_cpf.'">');
            ShowHTML('       <tr><td><b><u>N</u>ome completo:</b><br><input '.$w_Disabled.' accesskey="N" type="text" name="w_nome" class="sti" SIZE="45" MAXLENGTH="60" VALUE="'.$w_nome.'"></td>');
            ShowHTML('                <td><b><u>N</u>ome resumido:</b><br><input '.$w_Disabled.' accesskey="N" type="text" name="w_nome_resumido" class="sti" SIZE="15" MAXLENGTH="21" VALUE="'.$w_nome_resumido.'"></td>');
            SelecaoSexo('Se<u>x</u>o:','X',null,$w_sexo,null,'w_sexo',null,null);
            ShowHTML('          </table>');
            if ($SG=="RHUSU") {
                ShowHTML('      <tr><td colspan="2"><table border=0 width="100%" cellspacing=0>');
                ShowHTML('          <tr><td><b><u>I</u>dentidade:</b><br><input '.$w_Disabled.' accesskey="I" type="text" name="w_rg" class="sti" SIZE="14" MAXLENGTH="80" VALUE="'.$w_rg.'"></td>');
                ShowHTML('              <td><b>Passapo<u>r</u>te:</b><br><input '.$w_Disabled.' accesskey="R" type="text" name="w_passaporte" class="sti" SIZE="15" MAXLENGTH="15" VALUE="'.$w_passaporte.'"></td>');
                ShowHTML('              <td><b>Da<u>t</u>a de nascimento:</b><br><input '.$w_Disabled.' accesskey="T" type="text" name="w_nascimento" class="sti" SIZE="10" MAXLENGTH="10" VALUE="'.$w_nascimento.'" onKeyDown="FormataData(this,event);" onKeyUp="SaltaCampo(this.form.name,this,10,event);"></td>');
                ShowHTML('          </table>');
                ShowHTML('      <tr><td colspan="2"><table border=0 width="100%" cellspacing=0>');
                ShowHTML('          <tr><td><b>En<u>d</u>ereço:</b><br><input '.$w_Disabled.' accesskey="D" type="text" name="w_end" class="sti" SIZE="35" MAXLENGTH="50" VALUE="'.$w_end.'"></td>');
                ShowHTML('              <td><b>C<u>o</u>mplemento:</b><br><input '.$w_Disabled.' accesskey="O" type="text" name="w_comple" class="sti" SIZE="30" MAXLENGTH="50" VALUE="'.$w_comple.'"></td>');
                ShowHTML('          </table>');
                ShowHTML('      <tr><td colspan="2"><table border=0 width="100%" cellspacing=0>');
                ShowHTML('      <tr>');
                selecaoPais('<u>P</u>aís:','P',null,$w_pais,null,'w_pais',null,'onChange="document.Form.action=\''.$w_pagina.$par.'\'; document.Form.w_troca.value=\'w_uf\'; document.Form.submit();"');
                selecaoEstado('E<u>s</u>tado:','S',null,$w_uf,$w_pais,null,'w_uf',null,'onChange=\'document.Form.action=\''.$w_pagina.$par.'\'; document.Form.w_troca.value=\'w_cidade\'; document.Form.submit();"');
                selecaoCidade('<u>C</u>idade:','C',null,$w_cidade,$w_pais,$w_uf,'w_cidade',null,null);
                ShowHTML('          </table>');
                ShowHTML('          <tr><td><b>C<u>E</u>P:</b><br><input '.$w_Disabled.' accesskey="E" type="text" name="w_cep" class="sti" SIZE="10" MAXLENGTH="10" VALUE="'.$w_cep.'" onKeyDown="FormataCEP(this,event);"></td>');
                ShowHTML('      <tr><td colspan="2"><table border=0 width="100%" cellspacing=0>');
                ShowHTML('          <tr><td><b>Te<u>l</u>efone:</b><br><input '.$w_Disabled.' accesskey="L" type="text" name="w_telefone" class="sti" SIZE="20" MAXLENGTH="40" VALUE="'.$w_telefone.'"> '.consultaTelefone($w_cliente).'</td>');
                ShowHTML('              <td><b>Fa<u>x</u>:</b><br><input '.$w_Disabled.' accesskey="X" type="text" name="w_fax" class="sti" SIZE="20" MAXLENGTH="20" VALUE="'.$w_fax.'"></td>');
                if ($w_Disabled==' DISABLED ') {
                    ShowHTML('              <td><b>e-<u>M</u>ail:</b><br><input '.$w_Disabled.' accesskey="M" type="text" name="w_email1" class="sti" SIZE="40" MAXLENGTH="50" VALUE="'.$w_email.'"></td>');
                    ShowHTML('                   <INPUT type="hidden" name="w_email" value="'.$w_email.'">');
                } else {
                    ShowHTML('              <td><b>e-<u>M</u>ail:</b><br><input '.$w_Disabled.' accesskey="M" type="text" name="w_email" class="sti" SIZE="40" MAXLENGTH="50" VALUE="'.$w_email.'"></td>');
                }
                ShowHTML('          </table>');
            } elseif ($SG=='SGUSU' || $SG=='CLUSUARIO') {
                ShowHTML('        <tr><td><font size=1><b><u>U</u>sername:<br><INPUT ACCESSKEY="C" TYPE="text" Class="sti" NAME="w_username" VALUE="'.nvl($w_username,$w_cpf).'" SIZE="30" MaxLength="60" onBlur="document.Form.action=\''.$w_pagina.$par.'\'; document.Form.w_troca.value=\'w_email\'; document.Form.submit();">');
                if ($w_Disabled==' DISABLED ') {
                    ShowHTML('          <tr><td><b>e-<u>M</u>ail:</b><br><input '.$w_Disabled.' accesskey="M" type="text" name="w_email1" class="sti" SIZE="40" MAXLENGTH="60" VALUE="'.$w_email.'"></td>');
                    ShowHTML('                   <INPUT type="hidden" name="w_email" value="'.$w_email.'">');
                } else {
                    ShowHTML('          <tr><td><b>e-<u>M</u>ail:</b><br><input '.$w_Disabled.' accesskey="M" type="text" name="w_email" class="sti" SIZE="40" MAXLENGTH="60" VALUE="'.$w_email.'"></td>');
                }
            }
            ShowHTML('      <tr><td colspan="2"><table border=0 width="100%" cellspacing=0>');
            ShowHTML('          <tr>');
            selecaoUnidade('<U>U</U>nidade de lotação:','U','Selecione a unidade de lotação e aguarde a recarga da página para selecionar sua localização.',$w_sq_unidade_lotacao,null,'w_sq_unidade_lotacao',null,'onChange="document.Form.action=\''.$w_pagina.$par.'\'; document.Form.w_troca.value=\'w_sq_localizacao\'; document.Form.submit();"');
            ShowHTML('          <tr>');
            selecaoLocalizacao('Locali<u>z</u>ação:','Z',null,$w_sq_localizacao,nvl($w_sq_unidade_lotacao,0),'w_sq_localizacao',null);
            ShowHTML('          </table>');
            ShowHTML('      <tr><td colspan="2"><table border=0 width="100%" cellspacing=0>');
            ShowHTML('      <tr>');
            if ($SG=='RHUSU') {
                selecaoVinculo('<u>M</u>odalidade de contratação:','M',null,$w_sq_tipo_vinculo,null,'w_sq_tipo_vinculo','S','Física','S');
            } else {
                selecaoVinculo('<u>V</u>ínculo com a organização:','V',null,$w_sq_tipo_vinculo,null,'w_sq_tipo_vinculo','S','Física',null);
            }

            ShowHTML('      </tr>');
            ShowHTML('          </table>');
            if ($SG=='RHUSU') { // Tela de usuários do RH
                if ($O=='A') $w_readonly='READONLY'; // Se for alteração, bloqueia a edição dos campos
                ShowHTML('      <tr><td colspan="2"><table border=0 width="100%" cellspacing=0>');
                ShowHTML('          <tr><td><b>Da<u>t</u>a de entrada:</b><br><input '.$w_Disabled.' '.$w_readonly.' accesskey="T" type="text" name="w_entrada" class="sti" SIZE="10" MAXLENGTH="10" VALUE="'.$w_entrada.'" onKeyDown="FormataData(this,event);" onKeyUp="SaltaCampo(this.form.name,this,10,event);"></td>');
                ShowHTML('              <td><b><u>L</u>imite para empréstimo:</b><br><input '.$w_Disabled.' '.$w_readonly.' accesskey="L" type="text" name="w_limite_emprestimo" class="sti" SIZE="10" MAXLENGTH="10" VALUE="'.$w_limite_emprestimo.'" style="text-align:right;" onKeyDown="FormataValor(this,11,2,event)"></td>');
                ShowHTML('              <td><b>Saldo de <u>f</u>érias:</b><br><input '.$w_Disabled.' '.$w_readonly.' accesskey="F" type="text" name="w_saldo_ferias" class="sti" SIZE="5" MAXLENGTH="5" VALUE="'.$w_saldo_ferias.'" style="text-align:right;" onKeyDown="FormataValor(this,6,1,event)"></td>');
                if ($O=='I') { // Se for inclusão de funcionário, pergunta se deseja enviar e-mail
                    ShowHTML('          <tr><td><input type="checkbox" name="w_envia_mail" class="STC" VALUE="S" CHECKED> Enviar mensagem comunicando admissão de novo funcionário.</td>');
                } elseif ($O=='E') { // Se for remoção de funcionário, pergunta se deseja enviar e-mail
                    ShowHTML('          <tr><td><input type="checkbox" name="w_envia_mail" class="STC" VALUE="S" CHECKED> Enviar mensagem comunicando rescisão do contrato de funcionário.</td>');
                }
                ShowHTML('          </table>');
            } elseif ($SG=='SGUSU' || $SG=='CLUSUARIO') { // Tela de cadastramento de usuários
              ShowHTML('      <tr><td colspan="2"><table border=0 width="100%" cellspacing=0>');
              ShowHTML('          <tr><td><b>Gestor segurança?</b><br>');
              if ($w_gestor_seguranca=='S') {
                ShowHTML('              <input '.$w_Disabled.' type="RADIO" name="w_gestor_seguranca" class="str" VALUE="S" CHECKED> Sim<input '.$w_Disabled.' type="RADIO" name="w_gestor_seguranca" class="str" VALUE="N"> Não</td>');
              } else {
                ShowHTML('              <input '.$w_Disabled.' type="RADIO" name="w_gestor_seguranca" class="str" VALUE="S"> Sim<input '.$w_Disabled.' type="RADIO" name="w_gestor_seguranca" class="str" VALUE="N" CHECKED> Não</td>');
              }
              ShowHTML('              <td><b>Gestor sistema?</b><br>');
              if ($w_gestor_sistema=='S') {
                ShowHTML('              <input '.$w_Disabled.' type="RADIO" name="w_gestor_sistema" class="str" VALUE="S" CHECKED> Sim<input '.$w_Disabled.' type="RADIO" name="w_gestor_sistema" class="str" VALUE="N"> Não</td>');
              } else {
                ShowHTML('              <input '.$w_Disabled.' type="RADIO" name="w_gestor_sistema" class="str" VALUE="S"> Sim<input '.$w_Disabled.' type="RADIO" name="w_gestor_sistema" class="str" VALUE="N" CHECKED> Não</td>');
              }
              ShowHTML('          <tr valign="top">');
              MontaRadioNS('<b>Gestor portal?</b>',$w_gestor_portal,'w_gestor_portal');
              MontaRadioNS('<b>Gestor dashboard?</b>',$w_gestor_dashboard,'w_gestor_dashboard');
              MontaRadioNS('<b>Gestor conteúdo?</b>',$w_gestor_conteudo,'w_gestor_conteudo');
              ShowHTML('      <tr valign="top">');
              selecaoTipoAutenticacao('<u>T</u>ipo de autenticação:','t','Indique o tipo de autenticação para este usuário',$w_tipo_autenticacao,$w_cliente,'w_tipo_autenticacao',null,'onChange="document.Form.action=\''.$w_pagina.$par.'\'; document.Form.w_troca.value=\'w_tipo_autenticacao\'; document.Form.submit();"');
              if (($O=='I' || $w_username_ant!= $w_username) && strpos('AO',$w_tipo_autenticacao)!==false) {
                ShowHTML('        <td><font size=1><b><u>U</u>suário de rede:<br><INPUT ACCESSKEY="U" TYPE="text" Class="sti" NAME="w_username_adm" VALUE="'.$w_username_adm.'" SIZE="30" MaxLength="60">');
                ShowHTML('        <td><b><U>S</U>enha:<br><INPUT ACCESSKEY="S" class="sti" type="PASSWORD" name="w_senha_adm" size="30" maxlength="60" value=""></td>');
              }

              if ($O=='I') { // Se for inclusão de funcionário, pergunta se deseja enviar e-mail
                ShowHTML('          <tr><td><input type="checkbox" name="w_envia_mail" class="STC" VALUE="S" CHECKED> Comunica ao usuário a criação do acesso</td>');
              } elseif ($O=='E') { // Se for remoção de funcionário, pergunta se deseja enviar e-mail
                ShowHTML('          <tr><td><input type="checkbox" name="w_envia_mail" class="STC" VALUE="S" CHECKED> Comunica ao usuário sua exclusão</td>');
              } elseif ($O=='T') { // Se for remoção de funcionário, pergunta se deseja enviar e-mail
                ShowHTML('          <tr><td><input type="checkbox" name="w_envia_mail" class="STC" VALUE="S" CHECKED> Comunica ao usuário a ativação do seu acesso</td>');
              } elseif ($O=='D') { // Se for remoção de funcionário, pergunta se deseja enviar e-mail
                ShowHTML('          <tr><td><input type="checkbox" name="w_envia_mail" class="STC" VALUE="S" CHECKED> Comunica ao usuário o bloqueio do seu acesso</td>');
              }
              ShowHTML('          </table>');
            }
            if ($SG=='RHUSU' || $SG=='SGUSU' || $SG=='CLUSUARIO') { // Tela de usuários do RH e do SG
              ShowHTML('      <tr><td><b><U>A</U>ssinatura Eletrônica:<br><INPUT ACCESSKEY="A" class="sti" type="PASSWORD" name="w_assinatura" size="30" maxlength="30" value=""></td>');
            }
            ShowHTML('      <tr><td align="center" colspan="3" height="1" bgcolor="#000000"></TD></TR>');
            // Verifica se poderá ser feito o envio da solicitação, a partir do resultado da validação
            ShowHTML('      <tr><td align="center" colspan="3">');
            if ($O=='E') {
                ShowHTML('            <input class="stb" type="submit" name="Botao" value="Excluir">');
            } elseif ($O=='T') {
                ShowHTML('            <input class="stb" type="submit" name="Botao" value="Desbloquear Acesso" onClick="return(confirm(\'Confirma a ativação do acesso ao sistema para este usuário?\'));">');
            } elseif ($O=='D') {
                if ($SG=='SGUSU' || $SG=='CLUSUARIO') { // Tela de usuários do SG
                    ShowHTML('            <input class="stb" type="submit" name="Botao" value="Bloquear Acesso" onClick="return(confirm(\'Confirma bloqueio do acesso ao sistema para este usuário?\'));">');
                } elseif ($SG=='RHUSU') { // Tela de usuários do RH
                    ShowHTML('            <input class="stb" type="submit" name="Botao" value="Remover do quadro" onClick="return(confirm(\'Confirma remoção do quadro de funcionários e bloqueio do acesso ao sistema para esta pessoa?\'));">');
                } else {
                    ShowHTML('            <input class="stb" type="submit" name="Botao" value="Excluir">');
                }
            } else {
                ShowHTML('            <input class="stb" type="submit" name="Botao" value="Gravar">');
            }
            $sql = new db_getLinkData; $RS = $sql->getInstanceOf($dbms,$w_cliente,'SGUSU');
            ShowHTML('            <input class="stb" type="button" onClick="location.href=\''.f($RS,'link').'&w_cliente='.$_REQUEST['w_cliente'].'&P1='.f($RS,'P1').'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.f($RS,'sigla').MontaFiltro('GET').'\';" name="Botao" value="Cancelar">');
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
// Rotina de cadastramento de pessoas físicas e jurídicas
// -------------------------------------------------------------------------
function CadastraPessoa() {
    extract($GLOBALS);
    global $w_Disabled;
    $w_erro           = '';
    $w_troca          = $_REQUEST['w_troca'];
    $w_sq_pessoa      = $_REQUEST['w_sq_pessoa'];
    $w_tipo_pessoa    = $_REQUEST['w_tipo_pessoa'];

    $p_cpf            = $_REQUEST['p_cpf'];
    $p_cnpj           = $_REQUEST['p_cnpj'];
    $p_nome           = $_REQUEST['p_nome'];
    $p_tipo_pessoa    = $_REQUEST['p_tipo_pessoa'];
    $p_mandatory      = $_REQUEST['p_mandatory'];
    if(strpos($p_mandatory,'w_nascimento')!==false) $w_exige_nascimento = true; else $w_exige_nascimento = false;
    if(strpos($p_mandatory,'w_email')!==false)      $w_exige_email = true;      else $w_exige_email = false;
    
    // Verifica se há necessidade de recarregar os dados da tela a partir
    // da própria tela (se for recarga da tela) ou do banco de dados (se não for inclusão)
    if ($w_troca>'') {
        // Se for recarga da página
        $w_chave                = $_REQUEST['w_chave'];
        $w_chave_aux            = $_REQUEST['w_chave_aux'];
        $w_cpf                  = $_REQUEST['w_cpf'];
        $w_cnpj                 = $_REQUEST['w_cnpj'];
        $w_nome                 = $_REQUEST['w_nome'];
        $w_nome_resumido        = $_REQUEST['w_nome_resumido'];
        $w_sq_pessoa_pai        = $_REQUEST['w_sq_pessoa_pai'];
        $w_nm_tipo_pessoa       = $_REQUEST['w_nm_tipo_pessoa'];
        $w_sq_tipo_vinculo      = $_REQUEST['w_sq_tipo_vinculo'];
        $w_nm_tipo_vinculo      = $_REQUEST['w_nm_tipo_vinculo'];
        $w_interno              = $_REQUEST['w_interno'];
        $w_vinculo_ativo        = $_REQUEST['w_vinculo_ativo'];
        $w_sq_pessoa_telefone   = $_REQUEST['w_sq_pessoa_telefone'];
        $w_ddd                  = $_REQUEST['w_ddd'];
        $w_nr_telefone          = $_REQUEST['w_nr_telefone'];
        $w_sq_pessoa_celular    = $_REQUEST['w_sq_pessoa_celular'];
        $w_nr_celular           = $_REQUEST['w_nr_celular'];
        $w_sq_pessoa_fax        = $_REQUEST['w_sq_pessoa_fax'];
        $w_nr_fax               = $_REQUEST['w_nr_fax'];
        $w_email                = $_REQUEST['w_email'];
        $w_sq_pessoa_endereco   = $_REQUEST['w_sq_pessoa_endereco'];
        $w_logradouro           = $_REQUEST['w_logradouro'];
        $w_complemento          = $_REQUEST['w_complemento'];
        $w_bairro               = $_REQUEST['w_bairro'];
        $w_cep                  = $_REQUEST['w_cep'];
        $w_sq_cidade            = $_REQUEST['w_sq_cidade'];
        $w_co_uf                = $_REQUEST['w_co_uf'];
        $w_sq_pais              = $_REQUEST['w_sq_pais'];
        $w_pd_pais              = $_REQUEST['w_pd_pais'];
        $w_nascimento           = $_REQUEST['w_nascimento'];
        $w_rg_numero            = $_REQUEST['w_rg_numero'];
        $w_rg_emissor           = $_REQUEST['w_rg_emissor'];
        $w_rg_emissao           = $_REQUEST['w_rg_emissao'];
        $w_passaporte_numero    = $_REQUEST['w_passaporte_numero'];
        $w_sq_pais_passaporte   = $_REQUEST['w_sq_pais_passaporte'];
        $w_sexo                 = $_REQUEST['w_sexo'];
        $w_inscricao_estadual   = $_REQUEST['w_inscricao_estadual'];
    } elseif ($O=='A' || $w_sq_pessoa>'') {
        // Recupera os dados do beneficiário em co_pessoa
        $SQL = new db_getBenef; $RS = $SQL->getInstanceOf($dbms,$w_cliente,$w_sq_pessoa,null,$w_cpf,$w_cnpj,null,null,null,null,null,null,null,null,null, null, null, null, null);
        if (count($RS)>0) {
            foreach($RS as $row) {
                $w_sq_pessoa            = f($row,'sq_pessoa');
                $w_username             = f($row,'username');
                $w_nome                 = f($row,'nm_pessoa');
                $w_nome_resumido        = f($row,'nome_resumido');
                $w_sq_pessoa_pai        = f($row,'sq_pessoa_pai');
                $w_nm_tipo_pessoa       = f($row,'nm_tipo_pessoa');
                $w_sq_tipo_vinculo      = f($row,'sq_tipo_vinculo');
                $w_nm_tipo_vinculo      = f($row,'nm_tipo_vinculo');
                $w_interno              = f($row,'interno');
                $w_vinculo_ativo        = f($row,'vinculo_ativo');
                $w_sq_pessoa_telefone   = f($row,'sq_pessoa_telefone');
                $w_ddd                  = f($row,'ddd');
                $w_nr_telefone          = f($row,'nr_telefone');
                $w_sq_pessoa_celular    = f($row,'sq_pessoa_celular');
                $w_nr_celular           = f($row,'nr_celular');
                $w_sq_pessoa_fax        = f($row,'sq_pessoa_fax');
                $w_nr_fax               = f($row,'nr_fax');
                $w_email                = f($row,'email');
                $w_sq_pessoa_endereco   = f($row,'sq_pessoa_endereco');
                $w_logradouro           = f($row,'logradouro');
                $w_complemento          = f($row,'complemento');
                $w_bairro               = f($row,'bairro');
                $w_cep                  = f($row,'cep');
                $w_sq_cidade            = f($row,'sq_cidade');
                $w_co_uf                = f($row,'co_uf');
                $w_sq_pais              = f($row,'sq_pais');
                $w_pd_pais              = f($row,'pd_pais');
                $w_cpf                  = f($row,'cpf');
                $w_nascimento           = FormataDataEdicao(f($row,'nascimento'));
                $w_rg_numero            = f($row,'rg_numero');
                $w_rg_emissor           = f($row,'rg_emissor');
                $w_rg_emissao           = FormataDataEdicao(f($row,'rg_emissao'));
                $w_passaporte_numero    = f($row,'passaporte_numero');
                $w_sq_pais_passaporte   = f($row,'sq_pais_passaporte');
                $w_sexo                 = f($row,'sexo');
                $w_cnpj                 = f($row,'cnpj');
                $w_inscricao_estadual   = f($row,'inscricao_estadual');
                break;
            }
        }
    }
    Cabecalho();
    head();
    Estrutura_CSS($w_cliente);
    // Monta o código JavaScript necessário para validação de campos e preenchimento automático de máscara,
    // tratando as particularidades de cada serviço
    ScriptOpen('JavaScript');
    Modulo();
    FormataCPF();
    FormataCNPJ();
    FormataCEP();
    CheckBranco();
    FormataData();
    SaltaCampo();
    ValidateOpen('Validacao');
    Validate('w_nome','Nome','1',1,5,60,'1','1');
    if ($w_tipo_pessoa==1) {
        if(substr($_REQUEST['p_objeto'],0,2)=='PD' ){
          Validate('w_cpf','CPF','CPF','1','14','14','','0123456789-.');
        }else{
          Validate('w_cpf','CPF','CPF','','14','14','','0123456789-.');
        }
    } elseif ($w_tipo_pessoa==2) {
        Validate('w_cnpj','CNPJ','CNPJ','','18','18','','0123456789/-.');
    }
    Validate('w_nome_resumido','Nome resumido','1',1,2,21,'1','1');
    if ($w_tipo_pessoa==1 or $w_tipo_pessoa==3) {
        Validate('w_nascimento','Data de Nascimento','DATA',(($w_exige_nascimento)?'1':''),10,10,'',1);
        Validate('w_sexo','Sexo','SELECT',1,1,1,'MF','');
        Validate('w_rg_numero','Identidade','1','',2,30,'1','1');
        Validate('w_rg_emissao','Data de emissão','DATA','',10,10,'','0123456789/');
        Validate('w_rg_emissor','Órgão expedidor','1','',2,30,'1','1');
        if ($w_tipo_pessoa==1) {
          Validate('w_passaporte_numero','Passaporte','1','',1,20,'1','1');
          Validate('w_sq_pais_passaporte','País emissor','SELECT','',1,10,'1','1');
        } else {
          Validate('w_passaporte_numero','Passaporte','1','1',1,20,'1','1');
          Validate('w_sq_pais_passaporte','País emissor','SELECT','1',1,10,'1','1');
        }
        ShowHTML('  if ((theForm.w_rg_numero.value+theForm.w_rg_emissao.value+theForm.w_rg_emissor.value)!="" && (theForm.w_rg_numero.value=="" || theForm.w_rg_emissor.value=="")) {');
        ShowHTML('     alert(\'Os campos identidade, data de emissão e órgão emissor devem ser informados em conjunto!\\nDos três, apenas a data de emissão é opcional.\');');
        ShowHTML('     theForm.w_rg_numero.focus();');
        ShowHTML('     return false;');
        ShowHTML('  }');
        ShowHTML('  if ((theForm.w_passaporte_numero.value+theForm.w_sq_pais_passaporte[theForm.w_sq_pais_passaporte.selectedIndex].value)!="" && (theForm.w_passaporte_numero.value=="" || theForm.w_sq_pais_passaporte.selectedIndex==0)) {');
        ShowHTML('     alert(\'Os campos passaporte e país emissor devem ser informados em conjunto!\');');
        ShowHTML('     theForm.w_passaporte_numero.focus();');
        ShowHTML('     return false;');
        ShowHTML('  }');
    } else {
        Validate('w_inscricao_estadual','Inscrição estadual','1','',2,20,'1','1');
    }
    Validate('w_ddd','DDD','1','',2,4,'','0123456789');
    Validate('w_nr_telefone','Telefone','1','',7,25,'1','1');
    Validate('w_nr_fax','Fax','1','',7,25,'1','1');
    Validate('w_nr_celular','Celular','1','',7,25,'1','1');
    Validate('w_logradouro','Endereço','1','',4,60,'1','1');
    Validate('w_complemento','Complemento','1','',2,20,'1','1');
    Validate('w_bairro','Bairro','1','',2,30,'1','1');
    Validate('w_sq_pais','País','SELECT',(($w_exige_email)?'1':''),1,10,'1','1');
    Validate('w_co_uf','UF','SELECT',(($w_exige_email)?'1':''),1,10,'1','1');
    Validate('w_sq_cidade','Cidade','SELECT',(($w_exige_email)?'1':''),1,10,'','1');
    if (Nvl($w_pd_pais,'S')=='S') {
        Validate('w_cep','CEP','1','',9,9,'','0123456789-');
    } else {
        Validate('w_cep','CEP','1','',5,9,'','0123456789');
    }
    ShowHTML('  if ((theForm.w_nr_telefone.value+theForm.w_nr_fax.value+theForm.w_nr_celular.value)!="" && theForm.w_ddd.value=="") {');
    ShowHTML('     alert(\'O campo DDD é obrigatório quando informar telefone, fax ou celular!\');');
    ShowHTML('     theForm.w_ddd.focus();');
    ShowHTML('     return false;');
    ShowHTML('  }');
    ShowHTML('  if (theForm.w_ddd.value!="" && theForm.w_nr_telefone.value=="") {');
    ShowHTML('     alert(\'Se informar o DDD, então informe obrigatoriamente o telefone!\\nFax e celular são opcionais.\');');
    ShowHTML('     theForm.w_nr_telefone.focus();');
    ShowHTML('     return false;');
    ShowHTML('  }');
    ShowHTML('  if (theForm.w_ddd.value!="" && (theForm.w_sq_pais.value=="" || theForm.w_co_uf.value=="" || theForm.w_sq_cidade.value=="")) {');
    ShowHTML('     alert(\'Se informar telefone, fax ou celular, então informe o país, estado e cidade!\');');
    ShowHTML('     theForm.w_sq_pais.focus();');
    ShowHTML('     return false;');
    ShowHTML('  }');
    ShowHTML('  if ((theForm.w_complemento.value+theForm.w_bairro.value+theForm.w_cep.value)!="" && theForm.w_logradouro.value=="") {');
    ShowHTML('     alert(\'O campo logradouro é obrigatório quando informar os campos complemento, bairro ou CEP!\');');
    ShowHTML('     theForm.w_logradouro.focus();');
    ShowHTML('     return false;');
    ShowHTML('  }');
    ShowHTML('  if (theForm.w_logradouro.value!="" && theForm.w_cep.value=="") {');
    ShowHTML('     alert(\'O campo CEP é obrigatório quando informar o endereço da pessoa!\');');
    ShowHTML('     theForm.w_cep.focus();');
    ShowHTML('     return false;');
    ShowHTML('  }');
    Validate('w_email','E-Mail','1',(($w_exige_email)?'1':''),4,60,'1','1');
    ShowHTML('  if ((theForm.w_ddd.value+theForm.w_logradouro.value+theForm.w_email.value)!="" && (theForm.w_sq_pais.value=="" || theForm.w_co_uf.value=="" || theForm.w_sq_cidade.value=="")) {');
    ShowHTML('     alert(\'Se informar algum telefone, o endereço ou o e-mail da pessoa, então informe o país, estado e cidade!\');');
    ShowHTML('     theForm.w_sq_pais.focus();');
    ShowHTML('     return false;');
    ShowHTML('  }');
    Validate('w_assinatura','Assinatura Eletrônica','1','1','6','30','1','1');
    ShowHTML('  theForm.Botao[0].disabled=true;');
    ShowHTML('  theForm.Botao[1].disabled=true;');
    ValidateClose();
    ScriptClose();
    ShowHTML('</HEAD>');
    ShowHTML('<BASE HREF="'.$conRootSIW.'">');
    if (nvl($w_troca,'')!='') {
        BodyOpenClean('onLoad=\'document.Form.'.$w_troca.'.focus()\';');
    } else {
        BodyOpenClean('onLoad=\'document.Form.w_nome.focus()\';');
    }
    Estrutura_Topo_Limpo();
    Estrutura_Menu();
    Estrutura_Corpo_Abre();
    Estrutura_Texto_Abre();
    ShowHTML('<table align="center" border="0" cellpadding="0" cellspacing="0" width="100%">');
    if (strpos('IA',$O)!==false) {
        AbreForm('Form',$w_dir.$w_pagina.'Grava','POST','return(Validacao(this));',null,$P1,$P2,$P3,$P4,$TP,'PESSOA',$R,$O);
        ShowHTML(MontaFiltro('POST'));
        ShowHTML('<INPUT type="hidden" name="w_troca" value="">');
        ShowHTML('<INPUT type="hidden" name="w_chave" value="'.$w_chave.'">');
        ShowHTML('<INPUT type="hidden" name="w_sq_pessoa" value="'.$w_sq_pessoa.'">');
        ShowHTML('<INPUT type="hidden" name="w_tipo_pessoa" value="'.$w_tipo_pessoa.'">');
        ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td>');
        ShowHTML('    <table width="97%" border="0">');
        ShowHTML('      <tr><td colspan="2" align="center" height="2" bgcolor="#000000"></td></tr>');
        ShowHTML('      <tr><td colspan="2" align="center" height="1" bgcolor="#000000"></td></tr>');
        ShowHTML('      <tr><td colspan="2" align="center" bgcolor="#D0D0D0"><b>Identificação</td></td></tr>');
        ShowHTML('      <tr><td colspan="2" align="center" height="1" bgcolor="#000000"></td></tr>');
        ShowHTML('      <tr><td colspan="2"><table border="0" width="100%">');
        ShowHTML('          <tr valign="top">');
        ShowHTML('             <td><b><u>N</u>ome completo:</b><br><input '.$w_Disabled.' accesskey="N" type="text" name="w_nome" class="sti" SIZE="45" MAXLENGTH="60" VALUE="'.$w_nome.'"></td>');
        if ($w_tipo_pessoa==1) {
            ShowHTML('             <td><b><u>C</u>PF:<br><INPUT ACCESSKEY="C" TYPE="text" class="sti" NAME="w_cpf" VALUE="'.$w_cpf.'" SIZE="14" MaxLength="14" onKeyDown="FormataCPF(this, event);">');
        } elseif ($w_tipo_pessoa==2) {
            ShowHTML('             <td><b><u>C</u>NPJ:<br><INPUT ACCESSKEY="C" TYPE="text" class="sti" NAME="w_cnpj" VALUE="'.$w_cnpj.'" SIZE="18" MaxLength="18" onKeyDown="FormataCNPJ(this, event);">');
        }
        ShowHTML('          <tr valign="top">');
        ShowHTML('             <td><b><u>N</u>ome resumido:</b><br><input '.$w_Disabled.' accesskey="N" type="text" name="w_nome_resumido" class="sti" SIZE="15" MAXLENGTH="21" VALUE="'.$w_nome_resumido.'"></td>');
        if ($w_tipo_pessoa==1 or $w_tipo_pessoa==3) {
            SelecaoSexo('Se<u>x</u>o:','X',null,$w_sexo,null,'w_sexo',null,null);
            ShowHTML('          <td><b>Da<u>t</u>a de nascimento:</b><br><input '.$w_Disabled.' accesskey="T" type="text" name="w_nascimento" class="sti" SIZE="10" MAXLENGTH="10" VALUE="'.$w_nascimento.'" onKeyDown="FormataData(this,event);" onKeyUp="SaltaCampo(this.form.name,this,10,event);"></td>');
            ShowHTML('        <tr valign="top">');
            ShowHTML('          <td><b><u>I</u>dentidade:</b><br><input '.$w_Disabled.' accesskey="I" type="text" name="w_rg_numero" class="sti" SIZE="14" MAXLENGTH="80" VALUE="'.$w_rg_numero.'"></td>');
            ShowHTML('          <td><b>Data de <u>e</u>missão:</b><br><input '.$w_Disabled.' accesskey="E" type="text" name="w_rg_emissao" class="sti" SIZE="10" MAXLENGTH="10" VALUE="'.$w_rg_emissao.'" onKeyDown="FormataData(this,event);" onKeyUp="SaltaCampo(this.form.name,this,10,event);"></td>');
            ShowHTML('          <td><b>Ór<u>g</u>ão emissor:</b><br><input '.$w_Disabled.' accesskey="G" type="text" name="w_rg_emissor" class="sti" SIZE="30" MAXLENGTH="30" VALUE="'.$w_rg_emissor.'"></td>');
            ShowHTML('        <tr valign="top">');
            ShowHTML('          <td><b>Passapo<u>r</u>te:</b><br><input '.$w_Disabled.' accesskey="R" type="text" name="w_passaporte_numero" class="sti" SIZE="15" MAXLENGTH="15" VALUE="'.$w_passaporte_numero.'"></td>');
            SelecaoPais('<u>P</u>aís emissor do passaporte:','P',null,$w_sq_pais_passaporte,null,'w_sq_pais_passaporte',null,null);
        } else {
            ShowHTML('          <td><b><u>I</u>nscrição estadual:</b><br><input '.$w_Disabled.' accesskey="I" type="text" name="w_inscricao_estadual" class="sti" SIZE="20" MAXLENGTH="20" VALUE="'.$w_inscricao_estadual.'"></td>');
        }
        ShowHTML('          </table>');
        ShowHTML('      <tr><td colspan="2" align="center" height="2" bgcolor="#000000"></td></tr>');
        ShowHTML('      <tr><td colspan="2" align="center" height="1" bgcolor="#000000"></td></tr>');
        if ($w_tipo_pessoa==1 or $w_tipo_pessoa==3) {
            ShowHTML('      <tr><td colspan="2" align="center" bgcolor="#D0D0D0"><b>Endereço comercial, Telefones e e-Mail</td></td></tr>');
        } else {
            ShowHTML('      <tr><td colspan="2" align="center" bgcolor="#D0D0D0"><b>Endereço principal, Telefones e e-Mail</td></td></tr>');
        }
        ShowHTML('      <tr><td colspan="2" align="center" height="1" bgcolor="#000000"></td></tr>');
        ShowHTML('      <tr><td colspan="2"><table border=0 width="100%" cellspacing=0>');
        ShowHTML('          <tr valign="top">');
        ShowHTML('          <td><b><u>D</u>DD:</b><br><input '.$w_Disabled.' accesskey="D" type="text" name="w_ddd" class="sti" SIZE="4" MAXLENGTH="4" VALUE="'.$w_ddd.'"></td>');
        ShowHTML('          <td><b>Te<u>l</u>efone:</b><br><input '.$w_Disabled.' accesskey="L" type="text" name="w_nr_telefone" class="sti" SIZE="20" MAXLENGTH="40" VALUE="'.$w_nr_telefone.'"> '.consultaTelefone($w_cliente).'</td>');
        ShowHTML('          <td title="Se informar um número de fax, informe-o neste campo."><b>Fa<u>x</u>:</b><br><input '.$w_Disabled.' accesskey="X" type="text" name="w_nr_fax" class="sti" SIZE="20" MAXLENGTH="20" VALUE="'.$w_nr_fax.'"></td>');
        ShowHTML('          <td title="Se informar um celular institucional, informe-o neste campo."><b>C<u>e</u>lular:</b><br><input '.$w_Disabled.' accesskey="E" type="text" name="w_nr_celular" class="sti" SIZE="20" MAXLENGTH="20" VALUE="'.$w_nr_celular.'"></td>');
        ShowHTML('          <tr valign="top">');
        ShowHTML('          <td colspan=2><b>En<u>d</u>ereço:</b><br><input '.$w_Disabled.' accesskey="D" type="text" name="w_logradouro" class="sti" SIZE="50" MAXLENGTH="50" VALUE="'.$w_logradouro.'"></td>');
        ShowHTML('          <td><b>C<u>o</u>mplemento:</b><br><input '.$w_Disabled.' accesskey="O" type="text" name="w_complemento" class="sti" SIZE="20" MAXLENGTH="20" VALUE="'.$w_complemento.'"></td>');
        ShowHTML('          <td><b><u>B</u>airro:</b><br><input '.$w_Disabled.' accesskey="B" type="text" name="w_bairro" class="sti" SIZE="30" MAXLENGTH="30" VALUE="'.$w_bairro.'"></td>');
        ShowHTML('          <tr valign="top">');
        SelecaoPais('<u>P</u>aís:','P',null,$w_sq_pais,null,'w_sq_pais',null,'onChange="document.Form.action=\''.$w_dir.$w_pagina.$par.'\'; document.Form.w_troca.value=\'w_co_uf\'; document.Form.submit();"');
        ShowHTML('          <td>');
        SelecaoEstado('E<u>s</u>tado:','S',null,$w_co_uf,$w_sq_pais,null,'w_co_uf',null,'onChange="document.Form.action=\''.$w_dir.$w_pagina.$par.'\'; document.Form.w_troca.value=\'w_sq_cidade\'; document.Form.submit();"');
        SelecaoCidade('<u>C</u>idade:','C',null,$w_sq_cidade,$w_sq_pais,$w_co_uf,'w_sq_cidade',null,null);
        ShowHTML('          <tr valign="top">');
        if (Nvl($w_pd_pais,'S')=='S') {
            ShowHTML('              <td><b>C<u>E</u>P:</b><br><input '.$w_Disabled.' accesskey="E" type="text" name="w_cep" class="sti" SIZE="9" MAXLENGTH="9" VALUE="'.$w_cep.'" onKeyDown="FormataCEP(this,event);"></td>');
        } else {
            ShowHTML('              <td><b>C<u>E</u>P:</b><br><input '.$w_Disabled.' accesskey="E" type="text" name="w_cep" class="sti" SIZE="9" MAXLENGTH="9" VALUE="'.$w_cep.'"></td>');
        }
        ShowHTML('              <td colspan=3 title="Se informar um e-mail institucional, informe-o neste campo."><b>e-<u>M</u>ail:</b><br><input '.$w_Disabled.' accesskey="M" type="text" name="w_email" class="sti" SIZE="50" MAXLENGTH="60" VALUE="'.$w_email.'"></td>');
        ShowHTML('          </table>');
        ShowHTML('      <tr><td colspan=3><b><U>A</U>ssinatura Eletrônica:<BR> <INPUT ACCESSKEY="A" class="STI" type="PASSWORD" name="w_assinatura" size="30" maxlength="30" value=""></td></tr>');
        ShowHTML('      <tr><td align="center" colspan="3" height="1" bgcolor="#000000"></TD></TR>');
        ShowHTML('      <tr><td align="center" colspan="3">');
        ShowHTML('            <input class="stb" type="submit" name="Botao" value="Gravar" onClick="Botao.value=this.value;">');
        ShowHTML('            <input class="stb" type="button" onClick="location.href=\''.Nvl($_REQUEST['p_volta'],$R.'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG).montaFiltro('GET').'\';" name="Botao" value="Cancelar">');
        ShowHTML('          </td>');
        ShowHTML('      </tr>');
        ShowHTML('    </table>');
        ShowHTML('    </TD>');
        ShowHTML('</tr>');
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
// Rotina de busca dos usuários
// -------------------------------------------------------------------------
function BuscaUsuario() {
    extract($GLOBALS);
    global $w_Disabled;
    $w_nome       = upper($_REQUEST['w_nome']);
    $w_sg_unidade = upper($_REQUEST['w_sg_unidade']);
    $w_cliente    = $_REQUEST['w_cliente'];
    $ChaveAux     = $_REQUEST['ChaveAux'];
    $restricao    = $_REQUEST['restricao'];
    $campo        = $_REQUEST['campo'];
    $SQL = new db_getPersonList; $RS = $SQL->getInstanceOf($dbms,$_SESSION['P_CLIENTE'],$ChaveAux,$restricao,$w_nome,$w_sg_unidade,null,null);
    Cabecalho();
    ShowHTML('<TITLE>Seleção de pessoa</TITLE>');
    head();
    Estrutura_CSS($w_cliente);
    ScriptOpen('JavaScript');
    ShowHTML('  function volta(l_nome, l_chave) {');
    ShowHTML('     opener.document.Form.'.$campo.'_nm.value=l_nome;');
    ShowHTML('     opener.document.Form.'.$campo.'.value=l_chave;');
    ShowHTML('     opener.document.Form.'.$campo.'_nm.focus();');
    ShowHTML('     window.close();');
    ShowHTML('     opener.focus();');
    ShowHTML('   }');
    ValidateOpen('Validacao');
    Validate('w_nome','Nome','1','','4','100','1','1');
    Validate('w_sg_unidade','Sigla da unidade de lotação','1','','2','20','1','1');
    ShowHTML('  if (theForm.w_nome.value=="" && theForm.w_sg_unidade.value=="") {');
    ShowHTML('     alert (\'Informe um valor para o nome ou para a sigla da unidade!\');');
    ShowHTML('     theForm.w_nome.focus();');
    ShowHTML('     return false;');
    ShowHTML('  }');
    ShowHTML('  theForm.Botao[0].disabled=true;');
    ShowHTML('  theForm.Botao[1].disabled=true;');
    ValidateClose();
    ScriptClose();
    ShowHTML('</HEAD>');
    ShowHTML('<BASE HREF="//'.$_SERVER['server_name'].'/siw/">');
    BodyOpen('onLoad=\'document.Form.w_nome.focus();\'');
    Estrutura_Texto_Abre();
    ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td>');
    ShowHTML('    <table width="100%" border="0">');
    AbreForm('Form',$w_dir.$w_pagina.'BuscaUsuario','POST','return(Validacao(this))',null,$P1,$P2,$P3,$P4,$TP,$SG,null,null);
    ShowHTML('<INPUT type="hidden" name="restricao" value="'.$restricao.'">');
    ShowHTML('<INPUT type="hidden" name="campo" value="'.$campo.'">');
    ShowHTML(MontaFiltro('POST'));
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td><div align="justify"><font size=2><b><ul>Instruções</b>:<li>Informe parte do nome da ação ou o código da ação.<li>Quando a relação for exibida, selecione a ação desejada clicando sobre o link <i>Selecionar</i>.<li>Após informar o nome da ação ou o código da ação, clique sobre o botão <i>Aplicar filtro</i>. Clicando sobre o botão <i>Cancelar</i>, a procura é cancelada.</ul></div>');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td>');
    ShowHTML('    <table width="100%" border="0">');
    ShowHTML('      <tr><td><b>Parte do <U>n</U>ome da pessoa:<br><INPUT ACCESSKEY="N" '.$w_Disabled.' class="sti" type="text" name="w_nome" size="50" maxlength="100" value="'.$w_nome.'">');
    ShowHTML('      <tr><td><b><U>S</U>igla da unidade de lotação:<br><INPUT ACCESSKEY="S" '.$w_Disabled.' class="sti" type="text" name="w_sg_unidade" size="6" maxlength="20" value="'.$w_sg_unidade.'">');
    ShowHTML('      <tr><td align="center" colspan="3" height="1" bgcolor="#000000">');
    ShowHTML('      <tr><td align="center" colspan="3">');
    ShowHTML('            <input class="stb" type="submit" name="Botao" value="Aplicar filtro">');
    ShowHTML('            <input class="stb" type="button" name="Botao" value="Cancelar" onClick="window.close(); opener.focus();">');
    ShowHTML('          </td>');
    ShowHTML('      </tr>');
    ShowHTML('    </table>');
    ShowHTML('    </TD>');
    ShowHTML('</tr>');
    ShowHTML('</form>');
    if ($w_nome>'' || $w_sg_unidade>'') {
        ShowHTML('<tr><td align="right"><b>Registros: '.count($RS));
        ShowHTML('<tr><td>');
        ShowHTML('    <TABLE WIDTH="100%" border=0>');
        if (count($RS)==0) {
            ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td colspan=5 align="center"><b>Não foram encontrados registros.</b></td></tr>');
        } else {
            ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td>');
            ShowHTML('        <TABLE WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
            ShowHTML('          <tr bgcolor="'.$conTrBgColor.'" align="center">');
            ShowHTML('            <td><b>Nome resumido</font></td>');
            ShowHTML('            <td><b>Nome</font></td>');
            ShowHTML('            <td><b>Lotação</font></td>');
            ShowHTML('            <td><b>Operações</font></td>');
            ShowHTML('          </tr>');
            foreach($RS as $row) {
                $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;
                ShowHTML('      <tr bgcolor="'.$w_cor.'" valign="top">');
                ShowHTML('            <td>'.f($row,'nome_resumido').'</td>');
                ShowHTML('            <td>'.f($row,'nome').'</td>');
                ShowHTML('            <td>'.f($row,'sg_unidade').' ('.f($row,'nm_local').')</td>');
                ShowHTML('            <td><a class="ss" HREF="javascript:this.status.value;" onClick="javascript:volta(\''.f($row,'sq_pessoa').'\');">Selecionar</a>');
            }
            ShowHTML('        </table></tr>');
            ShowHTML('      </center>');
            ShowHTML('    </table>');
            ShowHTML('  </td>');
            ShowHTML('</tr>');
        }
    }
    ShowHTML('    </table>');
    ShowHTML('    </TD>');
    ShowHTML('</tr>');
    ShowHTML('</FORM>');
    ShowHTML('</table>');
    ShowHTML('</center>');
    Estrutura_Texto_Fecha();
}

// =========================================================================
// Rotina de busca de pessoas
// -------------------------------------------------------------------------
function BuscaPessoa() {
    extract($GLOBALS);
    global $w_Disabled;
    $p_mandatory   = nvl($_REQUEST['mandatory'],$_REQUEST['p_mandatory']);
    $p_nome        = upper($_REQUEST['p_nome']);
    $p_cpf         = $_REQUEST['p_cpf'];
    $p_cnpj        = $_REQUEST['p_cnpj'];
    $p_tipo_pessoa = nvl($_REQUEST['p_tipo_pessoa'],'NF,NJ,EF,EJ');
    $w_pessoa      = $_REQUEST['w_pessoa'];
    $p_restricao   = nvl($_REQUEST['restricao'],$_REQUEST['p_restricao']);
    $p_campo       = nvl($_REQUEST['campo'],$_REQUEST['p_campo']);
    $p_objeto      = nvl($SG,$_REQUEST['p_objeto']);
    $SQL = new db_getBenef; $RS = $SQL->getInstanceOf($dbms,$w_cliente,$w_pessoa,null,$p_cpf,$p_cnpj,$p_nome,null,null,null,null,null,null,null,null, null, null, null, null);
    Cabecalho();
    ShowHTML('<TITLE>Seleção de pessoa</TITLE>');
    head();
    Estrutura_CSS($w_cliente);
    ScriptOpen('JavaScript');
    ShowHTML('  function volta(l_nome, l_chave) {');
    ShowHTML('     opener.document.Form.'.$p_campo.'_nm.value=l_nome;');
    ShowHTML('     opener.document.Form.'.$p_campo.'.value=l_chave;');
    ShowHTML('     opener.document.Form.'.$p_campo.'_nm.focus();');
    ShowHTML('     window.close();');
    ShowHTML('     opener.focus();');
    ShowHTML('   }');
    ShowHTML('  function cadastra(l_tipo_pessoa) {');
    ShowHTML('     location.href="'.$w_pagina.'CadastraPessoa&O=I&p_tp='.$TP.'&TP=Cadastro de pessoas&w_tipo_pessoa="+l_tipo_pessoa+"&p_volta='.$w_pagina.$par.montaFiltro('GET').'&p_objeto='.$p_objeto.'";');
    ShowHTML('   }');
    Modulo();
    FormataCPF();
    FormataCNPJ();
    FormataCEP();
    CheckBranco();
    ValidateOpen('Validacao');
    Validate('p_nome','Nome','1','','3','100','1','1');
    if (strpos($p_tipo_pessoa,'NF')!==false) Validate('p_cpf','CPF','CPF','','14','14','','0123456789.-');
    if (strpos($p_tipo_pessoa,'NJ')!==false) Validate('p_cnpj','CNPJ','CNPJ','','18','18','','0123456789.-/');
    ShowHTML('  if (theForm.p_nome.value=="" && theForm.p_cpf.value=="" && theForm.p_cnpj.value=="") {');
    ShowHTML('     alert (\'Informe um critério para busca!\');');
    ShowHTML('     theForm.p_nome.focus();');
    ShowHTML('     return false;');
    ShowHTML('  }');
    ShowHTML('  theForm.Botao[0].disabled=true;');
    ShowHTML('  theForm.Botao[1].disabled=true;');
    ValidateClose();
    ScriptClose();
    ShowHTML('</HEAD>');
    BodyOpen('onLoad=\'document.Form.p_nome.focus();\'');
    Estrutura_Texto_Abre();
    ShowHTML('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td>');
    ShowHTML('    <table width="100%" border="0">');
    AbreForm('Form',$w_dir.$w_pagina.$par,'POST','return(Validacao(this))',null,$P1,$P2,$P3,$P4,$TP,$SG,null,null);
    ShowHTML('<INPUT type="hidden" name="p_restricao" value="'.$p_restricao.'">');
    ShowHTML('<INPUT type="hidden" name="p_campo" value="'.$p_campo.'">');
    ShowHTML('<INPUT type="hidden" name="p_objeto" value="'.$p_objeto.'">');
    ShowHTML('<INPUT type="hidden" name="p_mandatory" value="'.$p_mandatory.'">');
    ShowHTML('<INPUT type="hidden" name="p_tipo_pessoa" value="'.$p_tipo_pessoa.'">');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td align="justify"><b><ul>Instruções</b>:');
    ShowHTML('  <li>Informe pelos menos um critério de busca.');
    ShowHTML('  <li>Quando a relação for exibida, selecione a ação desejada clicando sobre o link <i>Selecionar</i>.');
    ShowHTML('  <li>Após informar os critérios de busca, clique sobre o botão <i>Aplicar filtro</i>. Clicando sobre o botão <i>Cancelar</i>, a procura é cancelada.');
    ShowHTML('  <li>Se a pessoa desejada não for encontrada, clique no botão <i>Cadastrar nova pessoa</i>, exibido abaixo da listagem.');
    ShowHTML('  <li><b>Evite cadastrar pessoas que já existem. Procure-a de diversas formas antes de cadastrá-la.</b>');
    ShowHTML('  <li><b>Se precisar alterar os dados de uma pessoa, entre em contato com os gestores do módulo.</b>');
    ShowHTML('  </ul>');
    ShowHTML('<tr bgcolor="'.$conTrBgColor.'"><td>');
    ShowHTML('    <table width="100%" border="0">');
    ShowHTML('      <tr><td colspan=2><b>Parte do <U>n</U>ome da pessoa:<br><INPUT ACCESSKEY="N" '.$w_Disabled.' class="sti" type="text" name="p_nome" size="50" maxlength="100" value="'.$p_nome.'">');
    ShowHTML('      <tr valign="top">');
    ShowHTML('        <td '.((strpos($p_tipo_pessoa,'NF')!==false) ? '' : 'style="display:none;"').'><b><u>C</u>PF:<br><INPUT ACCESSKEY="C" TYPE="text" class="sti" NAME="p_cpf" VALUE="'.$p_cpf.'" SIZE="14" MaxLength="14" onKeyDown="FormataCPF(this, event);">');
    ShowHTML('        <td '.((strpos($p_tipo_pessoa,'NJ')!==false) ? '' : 'style="display:none;"').'><b><u>C</u>NPJ:<br><INPUT ACCESSKEY="C" TYPE="text" class="sti" NAME="p_cnpj" VALUE="'.$p_cnpj.'" SIZE="18" MaxLength="18" onKeyDown="FormataCNPJ(this, event);">');
    ShowHTML('      <tr><td align="center" colspan="3" height="1" bgcolor="#000000">');
    ShowHTML('      <tr><td align="center" colspan="3">');
    ShowHTML('            <input class="stb" type="submit" name="Botao" value="Aplicar filtro">');
    ShowHTML('            <input class="stb" type="button" name="Botao" value="Cancelar" onClick="window.close(); opener.focus();">');
    ShowHTML('          </td>');
    ShowHTML('      </tr>');
    ShowHTML('    </table>');
    ShowHTML('    </TD>');
    ShowHTML('</tr>');
    ShowHTML('</form>');
    if ($p_nome!='' || $p_cpf!='' || $p_cnpj!='') {
        ShowHTML('<tr><td align="right"><b>Registros: '.count($RS));
        ShowHTML('<tr><td>');
        ShowHTML('    <TABLE WIDTH="100%" border=0>');
        if (count($RS)==0) {
            ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td colspan=5 align="center"><b>Não foram encontrados registros.</b></td></tr>');
        } else {
            ShowHTML('      <tr bgcolor="'.$conTrBgColor.'"><td>');
            ShowHTML('        <TABLE WIDTH="100%" bgcolor="'.$conTableBgColor.'" BORDER="'.$conTableBorder.'" CELLSPACING="'.$conTableCellSpacing.'" CELLPADDING="'.$conTableCellPadding.'" BorderColorDark="'.$conTableBorderColorDark.'" BorderColorLight="'.$conTableBorderColorLight.'">');
            ShowHTML('          <tr bgcolor="'.$conTrBgColor.'" align="center">');
            ShowHTML('            <td><b>CPF/CNPJ</font></td>');
            ShowHTML('            <td><b>Nome</font></td>');
            ShowHTML('            <td><b>Operações</font></td>');
            ShowHTML('          </tr>');
            foreach($RS as $row) {
                $w_cor = ($w_cor==$conTrBgColor || $w_cor=='') ? $w_cor=$conTrAlternateBgColor : $w_cor=$conTrBgColor;
                ShowHTML('      <tr bgcolor="'.$w_cor.'" valign="top">');
                ShowHTML('            <td align="center" width="1%" nowrap>'.nvl(f($row,'identificador_primario'),'---').'</td>');
                ShowHTML('            <td>'.f($row,'nm_pessoa').'</td>');
                ShowHTML('            <td><a class="ss" HREF="javascript:this.status.value;" onClick="javascript:volta(\''.f($row,'nm_pessoa').'\', '.f($row,'sq_pessoa').');">Selecionar</a>');
            }
            ShowHTML('        </table></tr>');
            ShowHTML('      </center>');
            ShowHTML('    </table>');
            ShowHTML('  </td>');
            ShowHTML('</tr>');
        }
        if (strpos($p_tipo_pessoa,'NF')!==false || strpos($p_tipo_pessoa,'NJ')!==false) {
          ShowHTML('      <tr><td align="center" colspan="3">');
          if (strpos($p_tipo_pessoa,'NF')!==false) ShowHTML('            <input lenght="150" class="stb" type="Button" name="BotaoNF" value="Cadastrar pessoa física BRASILEIRA" onClick="javascript:cadastra(1);">');
          if (strpos($p_tipo_pessoa,'NJ')!==false) ShowHTML('            <input class="stb" type="Button" name="BotaoNJ" value="Cadastrar pessoa jurídica BRASILEIRA" onClick="javascript:cadastra(2);">');
        }
        if (strpos($p_tipo_pessoa,'NF')!==false || strpos($p_tipo_pessoa,'NJ')!==false) {
          ShowHTML('      <tr><td align="center" colspan="3">');
          if (strpos($p_tipo_pessoa,'EF')!==false) ShowHTML('            <input class="stb" type="Button" name="BotaoEF" value="Cadastrar pessoa física ESTRANGEIRA" onClick="javascript:cadastra(3);">');
          if (strpos($p_tipo_pessoa,'EJ')!==false) ShowHTML('            <input class="stb" type="Button" name="BotaoEJ" value="Cadastrar pessoa jurídica ESTRANGEIRA" onClick="javascript:cadastra(4);">');
        }
    }
    ShowHTML('    </table>');
    ShowHTML('    </TD>');
    ShowHTML('</tr>');
    ShowHTML('</FORM>');
    ShowHTML('</table>');
    ShowHTML('</center>');
    Estrutura_Texto_Fecha();
}

// =========================================================================
// Procedimento que executa as operações de BD
// -------------------------------------------------------------------------
function Grava() {
    extract($GLOBALS);
    Cabecalho();
    ShowHTML('</HEAD>');
    BodyOpen('onLoad=this.focus();');
    if ($SG=='SGUSU' || $SG=='CLUSUARIO') { // Identifica, a partir do tamanho da variável w_username, se é pessoa física, jurídica ou estrangeiro
        // Verifica se a Assinatura Eletrônica é válida
        if (VerificaAssinaturaEletronica($_SESSION['USERNAME'],upper($_REQUEST['w_assinatura'])) || $w_assinatura=='') {
            if (strlen($_REQUEST['w_username'])<=14 || $SG=='SGUSU') $w_tipo='Física'; else $w_tipo='Jurídica';
            if (strpos('ED',$O)===false) {
              // Se não for exclusão nem desativação de usuários, verifica se o nome de usuário já existe
              $SQL = new db_getUserData; $RS = $SQL->getInstanceOf($dbms,$w_cliente,$_REQUEST['w_username']);
              $w_sq_pessoa = f($RS,'sq_pessoa');
              if (count($RS) > 0 && ($O=='I' || ($O!='I' && $w_sq_pessoa!=$_REQUEST['w_sq_pessoa']))) {
                ScriptOpen('JavaScript');
                ShowHTML('  alert(\'Nome de usuário já associado a outra pessoa!\');');
                ScriptClose();
                retornaFormulario('w_username');
                exit;
              }
              // Se a autenticação não for na aplicação, o nome de usuário deve existir no repositório indicado
              if (($O=='I' || $_REQUEST['w_username_ant']!= $_REQUEST['w_username']) && strpos('AO',$_REQUEST['w_tipo_autenticacao'])!==false) {
                include_once('classes/ldap/ldap.php');
                $sql = new db_getCustomerData; $RS1 = $sql->getInstanceOf($dbms, $_SESSION['P_CLIENTE']);   
                        
                if ($_REQUEST['w_tipo_autenticacao']=='A') {
                  // Recupera dados para conexão ao MS-AD
                  $w_label = 'Active Directory';
                  $array = array(            
                      'domain_controllers'    => f($RS1,'ad_domain_controlers'),
                      'base_dn'               => f($RS1,'ad_base_dn')          ,
                      'account_suffix'        => f($RS1,'ad_account_sufix')    ,               
                  );
                } else {
                  // Recupera dados para conexão ao Open LDAP
                  $w_label = 'Open LDAP';
                  $array = array(            
                      'domain_controllers'    => f($RS1,'ol_domain_controlers'),
                      'base_dn'               => f($RS1,'ol_base_dn')          ,
                      'account_suffix'        => f($RS1,'ol_account_sufix')    ,               
                  );
                }
                $adldap = new adLDAP($array);
                                                                                                                                                           
                if(!$adldap->authenticate($_REQUEST['w_username_adm'],$_REQUEST['w_senha_adm'])) {
                  // Autenticação fora da aplicação exige usuário válido e autenticado na rede.
                  ScriptOpen('JavaScript');
                  ShowHTML('  alert(\'Usuário de rede inexistente ou senha inválida!\');');
                  ScriptClose();
                  retornaFormulario('w_username_adm');
                  exit;
                } else {
                  // Testa se o usuário de rede existe e se não está bloqueado.
                  $user = $adldap->user_info($_REQUEST['w_username_adm'],array("userAccountControl"));
                  $user_attrib = $adldap->account_attrib($user[0]['useraccountcontrol'][0]);
                  if (in_array('ACCOUNTDISABLE',$user_attrib)) {
                    // Usuário de rede não pode estar bloqueado.
                    ScriptOpen('JavaScript');
                    ShowHTML('  alert(\'Usuário de rede bloqueado no '.$w_label.'!\');');
                    ScriptClose();
                    retornaFormulario('w_username_adm');
                    exit;
                  }

                  // Testa se o usuário em inclusão/edição existe e se não está bloqueado.
                  $user = $adldap->user_info($_REQUEST['w_username'],array('cn'));
                  if ($user[0]['dn']==NULL){
                    // Autenticação fora da aplicação exige que o nome do usuário seja criado previamente.
                    ScriptOpen('JavaScript');
                    ShowHTML('  alert(\'Nome de usuário não existe no '.$w_label.'!\nEntre em contato com o administrador da rede para criá-lo.\');');
                    ScriptClose();
                    retornaFormulario('w_username');
                    exit;
                  } else {
                    $user = $adldap->user_info($_REQUEST['w_username'],array("userAccountControl"));
                    $user_attrib = $adldap->account_attrib($user[0]['useraccountcontrol'][0]);
                    if (in_array('ACCOUNTDISABLE',$user_attrib)) {
                      // Autenticação fora da aplicação não permite criar usuários com contas bloqueadas.
                      ScriptOpen('JavaScript');
                      ShowHTML('  alert(\'Usuário bloqueado no '.$w_label.'!\');');
                      ScriptClose();
                      retornaFormulario('w_username');
                      exit;
                    }
                  }
                }
              }
            }

            // Executa a operação no banco de dados
            $SQL = new dml_putSiwUsuario; $SQL->getInstanceOf($dbms, $O,
                 $_REQUEST['w_sq_pessoa'],$_REQUEST['w_cliente'],$_REQUEST['w_nome'],$_REQUEST['w_nome_resumido'],
                 $_REQUEST['w_cpf'],$_REQUEST['w_sexo'],
                 $_REQUEST['w_sq_tipo_vinculo'],$w_tipo,$_REQUEST['w_sq_unidade_lotacao'],$_REQUEST['w_sq_localizacao'],
                 $_REQUEST['w_username'],$_REQUEST['w_email'],$_REQUEST['w_gestor_seguranca'],$_REQUEST['w_gestor_sistema'],
                 $_REQUEST['w_tipo_autenticacao'],$_REQUEST['w_gestor_portal'],$_REQUEST['w_gestor_dashboard'],
                 $_REQUEST['w_gestor_conteudo']);

            // Se o usuário logado deseja comunicar a ocorrência ao usuário em edição, configura e envia mensagem automática.
            if ($_REQUEST['w_envia_mail']>'') { // Configuração do texto da mensagem
              $w_html = '<HTML>'.$crlf;
              $w_html .= BodyOpenMail().$crlf;
              $w_html .= '<table border="0" cellpadding="0" cellspacing="0" width="100%">'.$crlf;
              $w_html .= '<tr bgcolor="'.$conTrBgColor.'"><td align="center">'.$crlf;
              $w_html .= '    <table width="97%" border="0">'.$crlf;
              $w_html .= '      <tr valign="top"><td><font size=2><b><font color="#BC3131">ATENÇÃO</font>: Esta é uma mensagem de envio automático. Não responda esta mensagem.</b></font><br><br><td></tr>'.$crlf;
              if (strpos('IT',$O)!==false) {
                if ($O=='I') {
                  $w_html .= '      <tr valign="top"><td align="center"><font size=2><b>CRIAÇÃO DE USUÁRIO</b></font><br><br><td></tr>'.$crlf;
                } elseif ($O=='T') {
                  $w_html .= '      <tr valign="top"><td align="center"><font size=2><b>DESBLOQUEIO DE USUÁRIO</b></font><br><br><td></tr>'.$crlf;
                }
                $w_html .= '      <tr valign="top"><td><font size=2>'.$crlf;
                if ($O=='I') {
                  $w_html .= '         Seu acesso ao sistema foi criado. Utilize os dados informados abaixo:<br>'.$crlf;
                } elseif ($O=='T') {
                  $w_html .= '         Seu acesso ao sistema foi desbloqueado. Utilize os dados informados abaixo:<br>'.$crlf;
                }
                $w_html .= '         <ul>'.$crlf;
                $SQL = new db_getCustomerSite; $RS = $SQL->getInstanceOf($dbms,$w_cliente);
                $w_html .= '         <li>Endereço de acesso ao sistema: <b><a class="ss" href="'.f($RS,'logradouro').'" target="_blank">'.f($RS,'Logradouro').'</a></b></li>'.$crlf;
                $w_html .= '         <li>Nome de '.((nvl($_REQUEST['w_sexo'],'M')=='M') ? 'usuário' : 'usuária').': <b>'.$_REQUEST['w_username'].'</b></li>'.$crlf;
                if (strpos('AO',$_REQUEST['w_tipo_autenticacao'])===false){
                  $w_html .= '         <li>Senha de acesso: <b>'.$_REQUEST['w_username'].'</b></li>'.$crlf;
                } else {
                  $w_html .= '         <li>Senha de acesso: <b>igual à senha de rede</b></li>'.$crlf;
                }
                $w_html .= '         <li>Assinatura eletrônica: <b>'.$_REQUEST['w_cpf'].'</b></li>'.$crlf;
                $w_html .= '         </ul>'.$crlf;
                $w_html .= '      </font></td></tr>'.$crlf;
                $w_html .= '      <tr valign="top"><td><font size=2>'.$crlf;
                $w_html .= '         Orientações e observações:<br>'.$crlf;
                $w_html .= '         <ol>'.$crlf;
                $sql = new db_getCustomerData; $RS = $sql->getInstanceOf($dbms,$w_cliente);
                if (strpos('AO',$_REQUEST['w_tipo_autenticacao'])===false){
                  $w_html .= '         <li>Troque sua senha de acesso e assinatura eletrônica no primeiro acesso que fizer ao sistema.</li>'.$crlf;
                  $w_html .= '         <li>Para trocar sua senha de acesso, localize no menu a opção <b>Troca senha</b> e clique sobre ela, seguindo as orientações apresentadas.</li>'.$crlf;
                  $w_html .= '         <li>Para trocar sua assinatura eletrônica, localize no menu a opção <b>Assinatura eletrônica</b> e clique sobre ela, seguindo as orientações apresentadas.</li>'.$crlf;
                  $w_html .= '         <li>Você pode fazer com que a senha de acesso e a assinatura eletrônica tenham o mesmo valor ou valores diferentes. A decisão é sua.</li>'.$crlf;
                  $w_html .= '         <li>Tanto a senha quanto a assinatura eletrônica têm tempo de vida máximo de <b>'.f($RS,'dias_vig_senha').'</b> dias. O sistema irá recomendar a troca <b>'.f($RS,'dias_aviso_expir').'</b> dias antes da expiração do tempo de vida.</li>'.$crlf;
                  $w_html .= '         <li>O sistema irá bloquear seu acesso se você errar sua senha de acesso ou sua assinatura eletrônica <b>'.f($RS,'maximo_tentativas').'</b> vezes consecutivas. Se você tiver dúvidas ou não lembrar sua senha de acesso ou assinatura eletrônica, utilize a opção "Lembrar senha" na tela de autenticação do sistema.</li>'.$crlf;
                  $w_html .= '         <li>Se sua senha de acesso ou assinatura eletrônica for bloqueada, entre em contato com o gestor de segurança do sistema.</li>'.$crlf;
                } else {
                  $w_html .= '         <li>Sua senha de acesso na aplicação será sempre igual à senha da rede. Se ela expirar ou for bloqueada, você não terá mais acesso ao sistema. Neste caso, entre em contato com os administradores de sua rede local.</li>'.$crlf;
                  $w_html .= '         <li>Troque sua assinatura eletrônica no primeiro acesso que fizer ao sistema. Para tanto, clique sobre a opção <b>Assinatura eletrônica</b>, localizada no menu principal, e siga as orientações apresentadas.</li>'.$crlf;
                  $w_html .= '         <li>Você pode fazer com que a senha de acesso e a assinatura eletrônica tenham o mesmo valor ou valores diferentes. A decisão é sua.</li>'.$crlf;
                  $w_html .= '         <li>A assinatura eletrônica têm tempo de vida máximo de <b>'.f($RS,'dias_vig_senha').'</b> dias. O sistema irá recomendar a troca <b>'.f($RS,'dias_aviso_expir').'</b> dias antes da expiração do tempo de vida.</li>'.$crlf;
                  $w_html .= '         <li>O sistema irá bloquear seu acesso se você errar sua assinatura eletrônica <b>'.f($RS,'maximo_tentativas').'</b> vezes consecutivas. Se você tiver dúvidas ou não lembrá-la, utilize a opção "Recriar senha" na tela de autenticação do sistema.</li>'.$crlf;
                }
                $w_html .= '         </ol>'.$crlf;
                $w_html .= '      </font></td></tr>'.$crlf;
              } elseif (strpos("ED",$O)!==false) {
                if ($O=='E') {
                  $w_html .= '      <tr valign="top"><td align="center"><font size=2><b>EXCLUSÃO DE USUÁRIO</b></font><br><br><td></tr>'.$crlf;
                } elseif ($O=='D') {
                  $w_html .= '      <tr valign="top"><td align="center"><font size=2><b>BLOQUEIO DE USUÁRIO</b></font><br><br><td></tr>'.$crlf;
                }
                $w_html .= '      <tr valign="top"><td><font size=2>'.$crlf;
                $SQL = new db_getCustomerSite; $RS = $SQL->getInstanceOf($dbms,$w_cliente);
                if ($O=='E') {
                  $w_html .= '         Seus dados foram excluídos do sistema existente no endereço '.f($RS,'logradouro').'. A partir de agora você não poderá mais acessá-lo.<br>'.$crlf;
                } elseif ($O=='D') {
                  $w_html .= '         Seu acesso ao sistema existente no endereço '.f($RS,'logradouro').' foi bloqueado pelo gestor de segurança. A partir de agora você não poderá mais acessá-lo.<br>'.$crlf;
                }
                $w_html .= '         Em caso de dúvidas, entre em contato com o gestor:'.$crlf;
                $w_html .= '         <ul>'.$crlf;
                $w_html .= '         <li>Nome: <b>'.$_SESSION['NOME'].'</b></li>'.$crlf;
                $w_html .= '         <li>e-Mail: <b><a class="ss" href="mailto:'.$_SESSION['EMAIL'].'">'.$_SESSION['EMAIL'].'</a></b></li>'.$crlf;
                $w_html .= '         </ul>'.$crlf;
                $w_html .= '      </font></td></tr>'.$crlf;
              }
              $w_html .= '      <tr valign="top"><td><font size=2>'.$crlf;
              $w_html .= '         Dados da ocorrência:<br>'.$crlf;
              $w_html .= '         <ul>'.$crlf;
              $w_html .= '         <li>Data do servidor: <b>'.date('d/m/Y, H:i:s').'</b></li>'.$crlf;
              $w_html .= '         <li>IP de origem: <b>'.$_SERVER['REMOTE_ADDR'].'</b></li>'.$crlf;
              $w_html .= '         </ul>'.$crlf;
              $w_html .= '      </font></td></tr>'.$crlf;
              $w_html .= '    </table>'.$crlf;
              $w_html .= '</td></tr>'.$crlf;
              $w_html .= '</table>'.$crlf;
              $w_html .= '</BODY>'.$crlf;
              $w_html .= '</HTML>'.$crlf;

              // Executa a função de envio de e-mail
              $w_resultado = '';
              if ($O=='I') {
                $w_resultado=EnviaMail('Aviso de criação de usuário',$w_html,$_REQUEST['w_email']);
              } elseif ($O=='E') {
                $w_resultado=EnviaMail('Aviso de exclusão de usuário',$w_html,$_REQUEST['w_email']);
              } elseif ($O=='D') {
                $w_resultado=EnviaMail('Aviso de bloqueio de acesso',$w_html,$_REQUEST['w_email']);
              } elseif ($O=='T') {
                $w_resultado=EnviaMail('Aviso de desbloqueio de acesso',$w_html,$_REQUEST['w_email']);
              }
            }
            // Aqui deve ser usada a variável de sessão para evitar erro na recuperação do link
            $sql = new db_getLinkData; $RS = $sql->getInstanceOf($dbms,$_SESSION['P_CLIENTE'],$SG);
            ScriptOpen('JavaScript');
            if ($SG=='SGUSU' || $SG=='RHUSU' || $SG=='CLUSUARIO') {
              if ($w_resultado>'') {
                ShowHTML('  alert(\'ATENÇÃO: operação executada mas não foi possível proceder o envio do e-mail.\n'.$w_resultado.'\');');
              }
              ShowHTML('  location.href=\''.f($RS,'link').'&O=L&w_cliente='.$_REQUEST['w_cliente'].'&w_sq_solicitacao='.$_REQUEST['w_sq_solicitacao'].'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'\';');
            } else {
              ShowHTML('  location.href=\''.f($RS,'link').'&O='.$O.'&w_cliente='.$_REQUEST['w_cliente'].'&w_sq_solicitacao='.$_REQUEST['w_sq_solicitacao'].'&P1='.$P1.'&P2='.$P2.'&P3='.$P3.'&P4='.$P4.'&TP='.$TP.'&SG='.$SG.MontaFiltro('GET').'\';');
            }
            ScriptClose();
            DesconectaBD();
        } else {
            ScriptOpen('JavaScript');
            ShowHTML('  alert(\'Assinatura Eletrônica inválida!\');');
            ScriptClose();
            retornaFormulario('w_assinatura');
        }
    } elseif ($SG=='PESSOA') {
        // Verifica se a Assinatura Eletrônica é válida
        if (verificaAssinaturaEletronica($_SESSION['USERNAME'],upper($_REQUEST['w_assinatura'])) || $w_assinatura=='') {
            if ($O=='I' || $O=='A') {
                if ($_REQUEST['w_tipo_pessoa']==1 || $_REQUEST['w_tipo_pessoa']==3) {
                    // Verifica se já existe pessoa física com o CPF informado
                    $SQL = new db_getBenef; $RS = $SQL->getInstanceOf($dbms,$w_cliente,$w_pessoa,null,nvl($_REQUEST['w_cpf'],'0'),null,null,$_REQUEST['w_tipo_pessoa'],null,null,null,null,null,null,null, null, null, null, null);
                    if (count($RS)>0) {
                        ScriptOpen('JavaScript');
                        ShowHTML('  alert(\'Já existe pessoa cadastrada com o CPF informado!\\nVerifique os dados.\');');
                        ScriptClose();
                        retornaFormulario('w_cpf');
                        exit;
                    }
                    // Verifica se já existe pessoa física com o mesmo nome.
                    $SQL = new db_getBenef; $RS = $SQL->getInstanceOf($dbms,$w_cliente,$w_pessoa,null,null,null,nvl($_REQUEST['w_nome'],'0'),$_REQUEST['w_tipo_pessoa'],null,null,null,null,null,null,null, null, null, null, null,'EXISTE');
                    if (count($RS)>0) {
                        foreach ($RS as $row) {
                            if (strlen(f($row,'nm_pessoa'))==strlen($_REQUEST['w_nome']) && (nvl(f($row,'identificador_primario'),'')=='' || nvl($_REQUEST['w_cpf'],'')=='')) {
                                ScriptOpen('JavaScript');
                                if (nvl(f($row,'identificador_primario'),'')=='') {
                                    ShowHTML('  alert(\'Já existe pessoa cadastrada com o nome informado!\\nVerifique os dados e, se necessário, solicite ao gestor a alteração dos dados da pessoa já cadastrada.\');');
                                } else {
                                    ShowHTML('  alert(\'Já existe pessoa cadastrada com o nome informado!\\nNeste caso é obrigatório informar o CPF.\');');
                                }
                                ScriptClose();
                                retornaFormulario('w_cpf');
                                exit;
                            }
                        }
                    }
                } else {
                    // Verifica se já existe pessoa jurídica com o CNPJ informado
                    $SQL = new db_getBenef; $RS = $SQL->getInstanceOf($dbms,$w_cliente,$w_pessoa,null,null,nvl($_REQUEST['w_cnpj'],'0'),null,$_REQUEST['w_tipo_pessoa'],null,null,null,null,null,null,null, null, null, null, null,'EXISTE');
                    if (count($RS)>0) {
                        ScriptOpen('JavaScript');
                        ShowHTML('  alert(\'Já existe pessoa jurídica cadastrada com o CNPJ informado!\\nVerifique os dados.\');');
                        ScriptClose();
                        retornaFormulario('w_cnpj');
                        exit;
                    }

                    // Verifica se já existe pessoa jurídica com o mesmo nome. Se existir, é obrigatório informar o CNPJ.
                    $SQL = new db_getBenef; $RS = $SQL->getInstanceOf($dbms,$w_cliente,$w_pessoa,null,null,null,nvl($_REQUEST['w_nome'],'0'),$_REQUEST['w_tipo_pessoa'],null,null,null,null,null,null,null, null, null, null, null,'EXISTE');
                    if (count($RS)>0) {
                        foreach ($RS as $row) {
                            if (strlen(f($row,'nm_pessoa'))==strlen($_REQUEST['w_nome']) && (nvl(f($row,'identificador_primario'),'')=='' || nvl($_REQUEST['w_cnpj'],'')=='')) {
                                ScriptOpen('JavaScript');
                                if (nvl(f($row,'identificador_primario'),'')=='') {
                                    ShowHTML('  alert(\'Já existe pessoa cadastrada com o nome informado!\\nVerifique os dados e, se necessário, solicite ao gestor a alteração dos dados da pessoa já cadastrada.\');');
                                } else {
                                    ShowHTML('  alert(\'Já existe pessoa cadastrada com o nome informado!\\nNeste caso é obrigatório informar o CNPJ.\');');
                                }
                                ScriptClose();
                                retornaFormulario('w_cnpj');
                                exit;
                            }
                        }
                    }
                }
            }

            $SQL = new dml_putPessoa; $SQL->getInstanceOf($dbms,$_REQUEST['O'],$w_cliente,Nvl($_REQUEST['p_restricao'],$SG),
                $_REQUEST['w_tipo_pessoa'],$_REQUEST['w_tipo_vinculo'],$_REQUEST['w_sq_pessoa'],$_REQUEST['w_cpf'],
                $_REQUEST['w_cnpj'],$_REQUEST['w_nome'],$_REQUEST['w_nome_resumido'],
                $_REQUEST['w_sexo'],$_REQUEST['w_nascimento'],$_REQUEST['w_rg_numero'],
                $_REQUEST['w_rg_emissao'],$_REQUEST['w_rg_emissor'],$_REQUEST['w_passaporte_numero'],
                $_REQUEST['w_sq_pais_passaporte'],$_REQUEST['w_inscricao_estadual'],$_REQUEST['w_logradouro'],
                $_REQUEST['w_complemento'],$_REQUEST['w_bairro'],$_REQUEST['w_sq_cidade'],
                $_REQUEST['w_cep'],$_REQUEST['w_ddd'],$_REQUEST['w_nr_telefone'],
                $_REQUEST['w_nr_fax'],$_REQUEST['w_nr_celular'],$_REQUEST['w_email'],&$w_chave_nova);

            ScriptOpen('JavaScript');
            ShowHTML('  location.href=\''.$_REQUEST['p_volta'].'&p_tp='.$_REQUEST['p_tp'].'&p_campo='.$_REQUEST['p_campo'].'&p_nome='.$_REQUEST['w_nome'].'&p_cpf='.$_REQUEST['w_cpf'].'&p_cnpj='.$_REQUEST['w_cnpj'].'\';');
            ScriptClose();
        } else {
            ScriptOpen('JavaScript');
            ShowHTML('  alert(\'Assinatura Eletrônica inválida!\');');
            ScriptClose();
            retornaFormulario('w_assinatura');
        }
    } else {
        ScriptOpen('JavaScript');
        ShowHTML('  alert(\'Bloco de dados não encontrado: '.$SG.'\');');
        ScriptClose();
        exibeVariaveis();
    }
}
// =========================================================================
// Rotina principal
// -------------------------------------------------------------------------
function Main() {
    extract($GLOBALS);
    switch ($par) {
        case "BENEF":           Benef();        break;
        case "BUSCAUSUARIO":    BuscaUsuario(); break;
        case "BUSCAPESSOA":     BuscaPessoa();  break;
        case "CADASTRAPESSOA":  CadastraPessoa();  break;
        case "GRAVA":           Grava();        break;
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