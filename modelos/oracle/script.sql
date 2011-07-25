/*==============================================================*/
/* DBMS name:      ORACLE Version 9i                            */
/* Created on:     25/07/2011 14:15:58                          */
/*==============================================================*/


create sequence SQ_AGENCIA;

create sequence SQ_AREA_ATUACAO;

create sequence SQ_ARQUIVO;

create sequence SQ_BANCO
increment by 1
start with 1;

create sequence SQ_CIDADE
increment by 1
start with 1;

create sequence SQ_COLUNA;

create sequence SQ_CPF_ESPECIAL;

create sequence SQ_DADO_TIPO;

create sequence SQ_DATA_ESPECIAL;

create sequence SQ_DEMANDA_LOG;

create sequence SQ_DEMANDA_TIPO;

create sequence SQ_EOINDICADOR;

create sequence SQ_EOINDICADOR_AFERICAO;

create sequence SQ_EOINDICADOR_AFERIDOR;

create sequence SQ_EOINDICADOR_AGENDA;

create sequence SQ_ESQUEMA;

create sequence SQ_ESQUEMA_ATRIBUTO;

create sequence SQ_ESQUEMA_INSERT;

create sequence SQ_ESQUEMA_SCRIPT;

create sequence SQ_ESQUEMA_TABELA;

create sequence SQ_ETAPA_COMENTARIO;

create sequence SQ_ETAPA_CONTRATO;

create sequence SQ_ETAPA_DEMANDA;

create sequence SQ_EVENTO;

create sequence SQ_INDICE;

create sequence SQ_INDICE_TIPO;

create sequence SQ_LOCALIZACAO;

create sequence SQ_MAIL;

create sequence SQ_MAIL_ANEXO;

create sequence SQ_MAIL_DESTINATARIO;

create sequence SQ_MENU
increment by 1
start with 1;

create sequence SQ_META_CRONOGRAMA;

create sequence SQ_MODULO
increment by 1
start with 1;

create sequence SQ_OCORRENCIA;

create sequence SQ_PAIS
increment by 1
start with 1;

create sequence SQ_PARAM;

create sequence SQ_PEHORIZONTE;

create sequence SQ_PENATUREZA;

create sequence SQ_PEOBJETIVO;

create sequence SQ_PESSOA
increment by 1
start with 1;

create sequence SQ_PESSOA_CONTA_BANCARIA;

create sequence SQ_PESSOA_ENDERECO
increment by 1
start with 1;

create sequence SQ_PESSOA_MAIL;

create sequence SQ_PESSOA_TELEFONE
increment by 1
start with 1;

create sequence SQ_PLANO;

create sequence SQ_PLANO_INDICADOR;

create sequence SQ_PROCEDURE;

create sequence SQ_PROGRAMA_LOG;

create sequence SQ_PROJETO_ETAPA;

create sequence SQ_PROJETO_LOG;

create sequence SQ_PROJETO_RECURSO;

create sequence SQ_PROJETO_RUBRICA;

create sequence SQ_PT_CAMPO;

create sequence SQ_PT_CONTEUDO;

create sequence SQ_PT_EIXO;

create sequence SQ_PT_EXIBICAO_CONTEUDO;

create sequence SQ_PT_FILTRO;

create sequence SQ_PT_MENU;

create sequence SQ_PT_OPERADOR;

create sequence SQ_PT_PESQUISA;

create sequence SQ_RECURSO;

create sequence SQ_RECURSO_DISPONIVEL;

create sequence SQ_RECURSO_INDISPONIVEL;

create sequence SQ_REGIAO
increment by 1
start with 1;

create sequence SQ_RELACIONAMENTO;

create sequence SQ_RUBRICA_CRONOGRAMA;

create sequence SQ_SEGMENTO
increment by 1
start with 1;

create sequence SQ_SEGMENTO_MENU
increment by 1
start with 1;

create sequence SQ_SEGMENTO_VINCULO
increment by 1
start with 1;

create sequence SQ_SISTEMA;

create sequence SQ_SIW_ARQUIVO;

create sequence SQ_SIW_COORDENADA;

create sequence SQ_SIW_RESTRICAO;

create sequence SQ_SIW_SOLICITACAO;

create sequence SQ_SIW_SOLIC_LOG;

create sequence SQ_SIW_TRAMITE;

create sequence SQ_SOLICITACAO_INTERESSADO;

create sequence SQ_SOLIC_APOIO;

create sequence SQ_SOLIC_INDICADOR;

create sequence SQ_SOLIC_META;

create sequence SQ_SOLIC_RECURSO;

create sequence SQ_SOLIC_RECURSO_ALOCACAO;

create sequence SQ_SOLIC_RECURSO_LOG;

create sequence SQ_SOLIC_SITUACAO;

create sequence SQ_SP_PARAM;

create sequence SQ_SP_TIPO;

create sequence SQ_STORED_PROC;

create sequence SQ_TABELA;

create sequence SQ_TABELA_TIPO;

create sequence SQ_TIPO_APOIO;

create sequence SQ_TIPO_ARQUIVO;

create sequence SQ_TIPO_ENDERECO
increment by 1
start with 1;

create sequence SQ_TIPO_EVENTO;

create sequence SQ_TIPO_INDICADOR;

create sequence SQ_TIPO_INTERESSADO;

create sequence SQ_TIPO_LOG;

create sequence SQ_TIPO_PESSOA
increment by 1
start with 1;

create sequence SQ_TIPO_POSTO;

create sequence SQ_TIPO_RECURSO;

create sequence SQ_TIPO_RESTRICAO;

create sequence SQ_TIPO_TELEFONE
increment by 1
start with 1;

create sequence SQ_TIPO_UNIDADE;

create sequence SQ_TIPO_VINCULO;

create sequence SQ_TRIGGER;

create sequence SQ_UNIDADE;

create sequence SQ_UNIDADE_MEDIDA;

create sequence SQ_UNIDADE_RESPONSAVEL;

create sequence SQ_USUARIO;

/*==============================================================*/
/* Table: CO_AGENCIA                                            */
/*==============================================================*/
create table CO_AGENCIA  (
   SQ_AGENCIA           NUMBER(18)                      not null,
   SQ_BANCO             NUMBER(18)                      not null,
   CODIGO               VARCHAR2(30)                    not null,
   ATIVO                VARCHAR2(1)                    default 'S' not null
      constraint CKC_ATIVO_CO_AGENC check (ATIVO in ('S','N') and ATIVO = upper(ATIVO)),
   PADRAO               VARCHAR2(1)                    default 'N' not null
      constraint CKC_PADRAO_COAGE check (PADRAO in ('S','N') and PADRAO = upper(PADRAO)),
   NOME                 VARCHAR2(43)                    not null,
   constraint PK_CO_AGENCIA primary key (SQ_AGENCIA)
);

comment on table CO_AGENCIA is
'Armazena a tabela de agências';

comment on column CO_AGENCIA.SQ_AGENCIA is
'Chave de CO_AGENCIA.';

comment on column CO_AGENCIA.SQ_BANCO is
'Chave de CO_BANCO. Indica a que banco o registro está ligado.';

comment on column CO_AGENCIA.CODIGO is
'Código da agência bancária. Está com tamanho acima do normal para aceitar agências no exterior.';

comment on column CO_AGENCIA.ATIVO is
'Indica se o registro deve ou não ser exibido.';

comment on column CO_AGENCIA.PADRAO is
'Indica se este registro deve ser apresentado como padrão para o usuário.';

comment on column CO_AGENCIA.NOME is
'Nome da agência.';

/*==============================================================*/
/* Index: IN_COAGE_SQBANCO                                      */
/*==============================================================*/
create unique index IN_COAGE_SQBANCO on CO_AGENCIA (
   SQ_BANCO ASC,
   SQ_AGENCIA ASC
);

/*==============================================================*/
/* Index: IN_COAGE_ATIVO                                        */
/*==============================================================*/
create index IN_COAGE_ATIVO on CO_AGENCIA (
   SQ_BANCO ASC,
   ATIVO ASC
);

/*==============================================================*/
/* Index: IN_COAGE_PADRAO                                       */
/*==============================================================*/
create index IN_COAGE_PADRAO on CO_AGENCIA (
   SQ_BANCO ASC,
   PADRAO ASC
);

/*==============================================================*/
/* Index: IN_COAGE_NOME                                         */
/*==============================================================*/
create index IN_COAGE_NOME on CO_AGENCIA (
   NOME ASC,
   SQ_AGENCIA ASC
);

/*==============================================================*/
/* Index: IN_COAGE_UNICO                                        */
/*==============================================================*/
create unique index IN_COAGE_UNICO on CO_AGENCIA (
   SQ_BANCO ASC,
   CODIGO ASC
);

/*==============================================================*/
/* Table: CO_BANCO                                              */
/*==============================================================*/
create table CO_BANCO  (
   SQ_BANCO             NUMBER(18)                      not null,
   CODIGO               VARCHAR2(30)                    not null,
   NOME                 VARCHAR2(31)                    not null,
   ATIVO                VARCHAR2(1)                    default 'S' not null
      constraint CKC_ATIVO_CO_BANCO check (ATIVO in ('S','N') and ATIVO = upper(ATIVO)),
   PADRAO               VARCHAR2(1)                    default 'N' not null
      constraint CKC_PADRAO_COBAN check (PADRAO in ('S','N') and PADRAO = upper(PADRAO)),
   EXIGE_OPERACAO       VARCHAR2(1)                    default 'N' not null
      constraint CKC_EXIGE_OPERACAO_CO_BANCO check (EXIGE_OPERACAO in ('S','N') and EXIGE_OPERACAO = upper(EXIGE_OPERACAO)),
   constraint PK_CO_BANCO primary key (SQ_BANCO)
);

comment on table CO_BANCO is
'Armazena a tabela de bancos';

comment on column CO_BANCO.SQ_BANCO is
'Chave de CO_BANCO.';

comment on column CO_BANCO.CODIGO is
'Código do banco. Está com tamanho acima do normal para aceitar bancos do exterior.';

comment on column CO_BANCO.NOME is
'Nome do banco.';

comment on column CO_BANCO.ATIVO is
'Indica se o registro deve ou não ser exibido.';

comment on column CO_BANCO.PADRAO is
'Indica se este registro deve ser apresentado como padrão para o usuário.';

comment on column CO_BANCO.EXIGE_OPERACAO is
'Indica se os dados bancários do banco exigem o campo Operação.';

/*==============================================================*/
/* Index: IN_COBANCO_CODIGO                                     */
/*==============================================================*/
create unique index IN_COBANCO_CODIGO on CO_BANCO (
   CODIGO ASC
);

/*==============================================================*/
/* Index: IN_COBANCO_NOME                                       */
/*==============================================================*/
create unique index IN_COBANCO_NOME on CO_BANCO (
   NOME ASC
);

/*==============================================================*/
/* Index: IN_COBANCO_ATIVO                                      */
/*==============================================================*/
create index IN_COBANCO_ATIVO on CO_BANCO (
   ATIVO ASC
);

/*==============================================================*/
/* Index: IN_COBANCO_PADRAO                                     */
/*==============================================================*/
create index IN_COBANCO_PADRAO on CO_BANCO (
   PADRAO ASC
);

/*==============================================================*/
/* Table: CO_CIDADE                                             */
/*==============================================================*/
create table CO_CIDADE  (
   SQ_CIDADE            NUMBER(18)                      not null,
   SQ_PAIS              NUMBER(18)                      not null,
   SQ_REGIAO            NUMBER(18)                      not null,
   CO_UF                VARCHAR2(3)                     not null,
   NOME                 VARCHAR2(60)                    not null,
   DDD                  VARCHAR2(4),
   CODIGO_IBGE          VARCHAR2(20),
   CAPITAL              VARCHAR2(1)                    default 'N' not null
      constraint CKC_COCID_CAP
               check (CAPITAL in ('S','N') and CAPITAL = upper(CAPITAL)),
   CODIGO_EXTERNO       VARCHAR2(60),
   AEROPORTOS           NUMBER(1)                      default 1 not null,
   constraint PK_CO_CIDADE primary key (SQ_CIDADE)
);

comment on table CO_CIDADE is
'Armazena a tabela de cidades';

comment on column CO_CIDADE.SQ_CIDADE is
'Chave de CO_CIDADE.';

comment on column CO_CIDADE.SQ_PAIS is
'Chave de CO_PAIS. Indica a que país o registro está ligado.';

comment on column CO_CIDADE.SQ_REGIAO is
'Chave de CO_REGIAO. Indica a que região o registro está ligado.';

comment on column CO_CIDADE.CO_UF is
'Chave de CO_UF. Indica a que estado o registro está ligado.';

comment on column CO_CIDADE.NOME is
'Nome da cidade.';

comment on column CO_CIDADE.DDD is
'DDD da cidade.';

comment on column CO_CIDADE.CODIGO_IBGE is
'Código IBGE da cidade.';

comment on column CO_CIDADE.CAPITAL is
'Indica se a cidade é capital do estado. Apenas uma cidade por estado pode ter valor igual a ''S''.';

comment on column CO_CIDADE.CODIGO_EXTERNO is
'Código desse registro num sistema externo.';

comment on column CO_CIDADE.AEROPORTOS is
'Indica o número de aeroportos da cidade.';

/*==============================================================*/
/* Index: IN_COCID_NOME                                         */
/*==============================================================*/
create unique index IN_COCID_NOME on CO_CIDADE (
   NOME ASC,
   CO_UF ASC,
   SQ_PAIS ASC
);

/*==============================================================*/
/* Index: IN_COCID_PAISUF                                       */
/*==============================================================*/
create index IN_COCID_PAISUF on CO_CIDADE (
   CO_UF ASC
);

/*==============================================================*/
/* Index: IN_COCID_PAISREG                                      */
/*==============================================================*/
create index IN_COCID_PAISREG on CO_CIDADE (
   SQ_REGIAO ASC
);

/*==============================================================*/
/* Index: IN_COCID_CODIBGE                                      */
/*==============================================================*/
create index IN_COCID_CODIBGE on CO_CIDADE (
   CODIGO_IBGE ASC
);

/*==============================================================*/
/* Index: IN_COCID_EXTERNO                                      */
/*==============================================================*/
create index IN_COCID_EXTERNO on CO_CIDADE (
   CODIGO_EXTERNO ASC,
   SQ_CIDADE ASC
);

/*==============================================================*/
/* Table: CO_PAIS                                               */
/*==============================================================*/
create table CO_PAIS  (
   SQ_PAIS              NUMBER(18)                      not null,
   NOME                 VARCHAR2(60)                    not null,
   ATIVO                VARCHAR2(1)                    default 'S' not null
      constraint CKC_ATIVO_CO_PAIS check (ATIVO in ('S','N') and ATIVO = upper(ATIVO)),
   PADRAO               VARCHAR2(1)                    default 'N' not null
      constraint CKC_PADRAO_COPAI check (PADRAO in ('S','N') and PADRAO = upper(PADRAO)),
   DDI                  VARCHAR2(10),
   SIGLA                VARCHAR2(3),
   CODIGO_EXTERNO       VARCHAR2(60),
   CONTINENTE           NUMBER(1)                      default 1 not null,
   constraint PK_CO_PAIS primary key (SQ_PAIS)
);

comment on table CO_PAIS is
'Armazena a tabela de países';

comment on column CO_PAIS.SQ_PAIS is
'Chave de CO_PAIS.';

comment on column CO_PAIS.NOME is
'Nome do país.';

comment on column CO_PAIS.ATIVO is
'Indica se o registro deve ou não ser exibido.';

comment on column CO_PAIS.PADRAO is
'Indica se este registro deve ser apresentado como padrão para o usuário.';

comment on column CO_PAIS.DDI is
'DDI do país.';

comment on column CO_PAIS.SIGLA is
'Sigla do país, usada em relatórios para facilitar a exibição com largura pequena.';

comment on column CO_PAIS.CODIGO_EXTERNO is
'Código desse registroo para um sistema externo.';

comment on column CO_PAIS.CONTINENTE is
'Continente do país: 1 - América, 2 - Europa, 3 - Ásia, 4 - África, 5 - Oceania.';

/*==============================================================*/
/* Index: IN_COPAIS_NOME                                        */
/*==============================================================*/
create unique index IN_COPAIS_NOME on CO_PAIS (
   NOME ASC
);

/*==============================================================*/
/* Index: IN_COPAIS_ATIVO                                       */
/*==============================================================*/
create index IN_COPAIS_ATIVO on CO_PAIS (
   ATIVO ASC
);

/*==============================================================*/
/* Index: IN_COPAIS_PADRAO                                      */
/*==============================================================*/
create index IN_COPAIS_PADRAO on CO_PAIS (
   PADRAO ASC
);

/*==============================================================*/
/* Index: IN_COPAIS_EXTERNO                                     */
/*==============================================================*/
create index IN_COPAIS_EXTERNO on CO_PAIS (
   CODIGO_EXTERNO ASC,
   SQ_PAIS ASC
);

/*==============================================================*/
/* Table: CO_PESSOA                                             */
/*==============================================================*/
create table CO_PESSOA  (
   SQ_PESSOA            NUMBER(18)                      not null,
   SQ_PESSOA_PAI        number(18),
   SQ_TIPO_VINCULO      number(18),
   SQ_TIPO_PESSOA       NUMBER(10),
   SQ_RECURSO           NUMBER(18),
   NOME                 varchar2(63)                    not null,
   NOME_RESUMIDO        varchar2(21),
   NOME_INDICE          VARCHAR2(63)                    not null,
   NOME_RESUMIDO_IND    VARCHAR2(21),
   CLIENTE              VARCHAR2(1)                    default 'N' not null
      constraint CKC_CLIENTE_CO_PESSO check (CLIENTE in ('S','N') and CLIENTE = upper(CLIENTE)),
   FORNECEDOR           VARCHAR2(1)                    default 'N' not null
      constraint CKC_FORNECEDOR_CO_PESSO check (FORNECEDOR in ('S','N') and FORNECEDOR = upper(FORNECEDOR)),
   ENTIDADE             VARCHAR2(1)                    default 'N' not null
      constraint CKC_ENTIDADE_CO_PESSO check (ENTIDADE in ('S','N') and ENTIDADE = upper(ENTIDADE)),
   PARCEIRO             VARCHAR2(1)                    default 'N' not null
      constraint CKC_PARCEIRO_CO_PESSO check (PARCEIRO in ('S','N') and PARCEIRO = upper(PARCEIRO)),
   FUNCIONARIO          VARCHAR2(1)                    default 'N' not null
      constraint CKC_FUNCIONARIO_CO_PESSO check (FUNCIONARIO in ('S','N') and FUNCIONARIO = upper(FUNCIONARIO)),
   DEPENDENTE           VARCHAR2(1)                    default 'N' not null
      constraint CKC_DEPENDENTE_CO_PESSO check (DEPENDENTE in ('S','N') and DEPENDENTE = upper(DEPENDENTE)),
   CODIGO_EXTERNO       VARCHAR2(60),
   INCLUSAO             DATE                           default sysdate not null,
   constraint PK_CO_PESSOA primary key (SQ_PESSOA)
);

comment on table CO_PESSOA is
'Armazena pessoas físicas e jurídicas';

comment on column CO_PESSOA.SQ_PESSOA is
'Chave de CO_PESSOA.';

comment on column CO_PESSOA.SQ_PESSOA_PAI is
'Chave de CO_PESSOA. Auto-relacionamento da tabela.';

comment on column CO_PESSOA.SQ_TIPO_VINCULO is
'Chave de CO_TIPO_VINCULO. Indica a que tipo de vínculo o registro está ligado.';

comment on column CO_PESSOA.SQ_TIPO_PESSOA is
'Chave de CO_TIPO_PESSOA. Indica a que tipo de pessoa o registro está ligado.';

comment on column CO_PESSOA.SQ_RECURSO is
'Chave de EO_RECURSO. Indica a que recurso a pessoa está ligada.';

comment on column CO_PESSOA.NOME is
'Nome da pessoa.';

comment on column CO_PESSOA.NOME_RESUMIDO is
'Nome pelo qual a pessoa é conhecida (apelido, cognome etc.)';

comment on column CO_PESSOA.NOME_INDICE is
'Este campo é alimentado por uma trigger nos eventos insert e update, com a finalidade de facilitar a busca por nome. Seu conteúdo é igual al nome, mas em maiúsculas e sem acentos.';

comment on column CO_PESSOA.NOME_RESUMIDO_IND is
'Igual a NOME_INDICE';

comment on column CO_PESSOA.CLIENTE is
'Indica se a pessoa é cliente da organização.';

comment on column CO_PESSOA.FORNECEDOR is
'Indica se a pessoa é fornecedora da organização.';

comment on column CO_PESSOA.ENTIDADE is
'Indica se a pessoa é uma entidade de interesse da organização.';

comment on column CO_PESSOA.PARCEIRO is
'Indica se a pessoa é parceira da organização.';

comment on column CO_PESSOA.FUNCIONARIO is
'Indica se a pessoa é funcionária da organização.';

comment on column CO_PESSOA.DEPENDENTE is
'Indica se a pessoa é dependente de um funcionário da organização.';

comment on column CO_PESSOA.CODIGO_EXTERNO is
'Código desse registro num sistema externo.';

comment on column CO_PESSOA.INCLUSAO is
'Data de inclusão do registro na tabela.';

/*==============================================================*/
/* Index: IN_COPES_NMIND                                        */
/*==============================================================*/
create index IN_COPES_NMIND on CO_PESSOA (
   NOME_INDICE ASC
);

/*==============================================================*/
/* Index: IN_COPES_NMRESIND                                     */
/*==============================================================*/
create index IN_COPES_NMRESIND on CO_PESSOA (
   NOME_RESUMIDO_IND ASC
);

/*==============================================================*/
/* Index: IN_COPES_SQPESPAI                                     */
/*==============================================================*/
create index IN_COPES_SQPESPAI on CO_PESSOA (
   SQ_PESSOA_PAI ASC,
   SQ_PESSOA ASC
);

/*==============================================================*/
/* Index: IN_COPES_SQTPVINC                                     */
/*==============================================================*/
create index IN_COPES_SQTPVINC on CO_PESSOA (
   SQ_PESSOA_PAI ASC,
   SQ_TIPO_VINCULO ASC,
   SQ_PESSOA ASC
);

/*==============================================================*/
/* Index: IN_COPES_CLIENTE                                      */
/*==============================================================*/
create index IN_COPES_CLIENTE on CO_PESSOA (
   SQ_PESSOA_PAI ASC,
   CLIENTE ASC,
   SQ_PESSOA ASC
);

/*==============================================================*/
/* Index: IN_COPES_FORNEC                                       */
/*==============================================================*/
create index IN_COPES_FORNEC on CO_PESSOA (
   SQ_PESSOA_PAI ASC,
   FORNECEDOR ASC,
   SQ_PESSOA ASC
);

/*==============================================================*/
/* Index: IN_COPES_ENTIDADE                                     */
/*==============================================================*/
create index IN_COPES_ENTIDADE on CO_PESSOA (
   SQ_PESSOA_PAI ASC,
   ENTIDADE ASC,
   SQ_PESSOA ASC
);

/*==============================================================*/
/* Index: IN_COPES_PARCEIRO                                     */
/*==============================================================*/
create index IN_COPES_PARCEIRO on CO_PESSOA (
   SQ_PESSOA_PAI ASC,
   PARCEIRO ASC,
   SQ_PESSOA ASC
);

/*==============================================================*/
/* Index: IN_COPES_FUNCION                                      */
/*==============================================================*/
create index IN_COPES_FUNCION on CO_PESSOA (
   SQ_PESSOA_PAI ASC,
   FUNCIONARIO ASC,
   SQ_PESSOA ASC
);

/*==============================================================*/
/* Index: IN_COPES_DEPEND                                       */
/*==============================================================*/
create index IN_COPES_DEPEND on CO_PESSOA (
   SQ_PESSOA_PAI ASC,
   DEPENDENTE ASC,
   SQ_PESSOA ASC
);

/*==============================================================*/
/* Index: IN_COPES_EXTERNO                                      */
/*==============================================================*/
create index IN_COPES_EXTERNO on CO_PESSOA (
   CODIGO_EXTERNO ASC,
   SQ_PESSOA ASC
);

/*==============================================================*/
/* Index: IN_COPES_REC                                          */
/*==============================================================*/
create index IN_COPES_REC on CO_PESSOA (
   SQ_RECURSO ASC,
   SQ_PESSOA ASC
);

/*==============================================================*/
/* Table: CO_PESSOA_CONTA                                       */
/*==============================================================*/
create table CO_PESSOA_CONTA  (
   SQ_PESSOA_CONTA      NUMBER(18)                      not null,
   SQ_PESSOA            number(18)                      not null,
   SQ_AGENCIA           NUMBER(18)                      not null,
   OPERACAO             VARCHAR2(6),
   NUMERO               VARCHAR2(30)                    not null,
   ATIVO                VARCHAR2(1)                    default 'S' not null
      constraint CKC_ATIVO_CO_PESSO check (ATIVO in ('S','N') and ATIVO = upper(ATIVO)),
   PADRAO               VARCHAR2(1)                    default 'N' not null
      constraint CKC_COPESCONBAN_PD
               check (PADRAO in ('S','N') and PADRAO = upper(PADRAO)),
   TIPO_CONTA           VARCHAR2(1)                     not null
      constraint CKC_COPESCONBAN_TC
               check (TIPO_CONTA in ('1','2')),
   INVALIDA             VARCHAR2(1)                    default 'N' not null
      constraint CKC_COPESCONBAN_IV
               check (INVALIDA in ('S','N') and INVALIDA = upper(INVALIDA)),
   DEVOLUCAO_VALOR      VARCHAR2(1)                    default 'N' not null
      constraint CKC_DEVOLUCAO_VALOR_CO_PESSO check (DEVOLUCAO_VALOR in ('S','N') and DEVOLUCAO_VALOR = upper(DEVOLUCAO_VALOR)),
   SALDO_INICIAL        NUMBER(18,2)                   default 0 not null,
   constraint PK_CO_PESSOA_CONTA primary key (SQ_PESSOA_CONTA)
);

comment on table CO_PESSOA_CONTA is
'Armazena a conta bancária das pessoas';

comment on column CO_PESSOA_CONTA.SQ_PESSOA_CONTA is
'Chave de CO_PESSOA_CONTA.';

comment on column CO_PESSOA_CONTA.SQ_PESSOA is
'Chave de CO_PESSOA. Indica a que pessoa o registro está ligado.';

comment on column CO_PESSOA_CONTA.SQ_AGENCIA is
'Chave de CO_AGENCIA. Indica a que agência o registro está ligado.';

comment on column CO_PESSOA_CONTA.OPERACAO is
'Armazena a operação da conta, utilizada por bancos como Caixa Econômica e Bradesco.';

comment on column CO_PESSOA_CONTA.NUMERO is
'Número da conta bancária.';

comment on column CO_PESSOA_CONTA.ATIVO is
'Indica se o registro deve ou não ser exibido.';

comment on column CO_PESSOA_CONTA.PADRAO is
'Indica se este registro deve ser apresentado como padrão para o usuário.';

comment on column CO_PESSOA_CONTA.TIPO_CONTA is
'Armazena o tipo da conta corrente';

comment on column CO_PESSOA_CONTA.INVALIDA is
'Indica se a conta é inválida ou não.';

comment on column CO_PESSOA_CONTA.DEVOLUCAO_VALOR is
'Indica se a conta pode ser usada para devolução de valores.';

comment on column CO_PESSOA_CONTA.SALDO_INICIAL is
'Saldo inicial da conta bancária. Utilizado na geração de relatórios financeiros.';

/*==============================================================*/
/* Index: IN_COPESCONBAN_PES                                    */
/*==============================================================*/
create index IN_COPESCONBAN_PES on CO_PESSOA_CONTA (
   SQ_PESSOA ASC,
   TIPO_CONTA ASC
);

/*==============================================================*/
/* Table: CO_PESSOA_ENDERECO                                    */
/*==============================================================*/
create table CO_PESSOA_ENDERECO  (
   SQ_PESSOA_ENDERECO   NUMBER(18)                      not null,
   SQ_PESSOA            number(18),
   SQ_TIPO_ENDERECO     NUMBER(18)                      not null,
   LOGRADOURO           VARCHAR2(65)                    not null,
   COMPLEMENTO          VARCHAR2(21),
   BAIRRO               VARCHAR2(40),
   SQ_CIDADE            NUMBER(18)                      not null,
   CEP                  VARCHAR2(9),
   PADRAO               VARCHAR2(1)                    default 'N' not null
      constraint CKC_COPESEND_PAD
               check (PADRAO in ('S','N') and PADRAO = upper(PADRAO)),
   CODIGO_EXTERNO       VARCHAR2(60),
   constraint PK_CO_PESSOA_ENDERECO primary key (SQ_PESSOA_ENDERECO)
);

comment on table CO_PESSOA_ENDERECO is
'Armazena os endereços da pessoa';

comment on column CO_PESSOA_ENDERECO.SQ_PESSOA_ENDERECO is
'Chave de CO_PESSOA_ENDERECO.';

comment on column CO_PESSOA_ENDERECO.SQ_PESSOA is
'Chave de CO_PESSOA. Indica a que pessoa o registro está ligado.';

comment on column CO_PESSOA_ENDERECO.SQ_TIPO_ENDERECO is
'Chave de CO_TIPO_ENDERECO. Indica a que tipo de endereço o registro está ligado.';

comment on column CO_PESSOA_ENDERECO.LOGRADOURO is
'Logradouro do endereço.';

comment on column CO_PESSOA_ENDERECO.COMPLEMENTO is
'Complemento do endereço.';

comment on column CO_PESSOA_ENDERECO.BAIRRO is
'Nome do bairro.';

comment on column CO_PESSOA_ENDERECO.SQ_CIDADE is
'Chave de CO_CIDADE. Indica a que cidade o registro está ligado.';

comment on column CO_PESSOA_ENDERECO.CEP is
'Código de endereçamento postal.';

comment on column CO_PESSOA_ENDERECO.PADRAO is
'Indica se este registro deve ser apresentado como padrão para o usuário.';

comment on column CO_PESSOA_ENDERECO.CODIGO_EXTERNO is
'Código desse registro num sistema externo.';

/*==============================================================*/
/* Index: IN_COPESEND_SQPES                                     */
/*==============================================================*/
create index IN_COPESEND_SQPES on CO_PESSOA_ENDERECO (
   SQ_PESSOA ASC
);

/*==============================================================*/
/* Index: IN_COPESEND_CIDADE                                    */
/*==============================================================*/
create index IN_COPESEND_CIDADE on CO_PESSOA_ENDERECO (
   SQ_CIDADE ASC
);

/*==============================================================*/
/* Index: IN_COPESEND_BAIRRO                                    */
/*==============================================================*/
create index IN_COPESEND_BAIRRO on CO_PESSOA_ENDERECO (
   BAIRRO ASC
);

/*==============================================================*/
/* Index: IN_COPESEND_PADRAO                                    */
/*==============================================================*/
create index IN_COPESEND_PADRAO on CO_PESSOA_ENDERECO (
   PADRAO ASC
);

/*==============================================================*/
/* Index: IN_COPESEND_EXTERNO                                   */
/*==============================================================*/
create index IN_COPESEND_EXTERNO on CO_PESSOA_ENDERECO (
   CODIGO_EXTERNO ASC,
   SQ_PESSOA ASC,
   SQ_PESSOA_ENDERECO ASC
);

/*==============================================================*/
/* Table: CO_PESSOA_FISICA                                      */
/*==============================================================*/
create table CO_PESSOA_FISICA  (
   SQ_PESSOA            number(18)                      not null,
   CLIENTE              number(18)                      not null,
   NASCIMENTO           DATE,
   RG_NUMERO            VARCHAR2(30),
   RG_EMISSOR           VARCHAR2(32),
   RG_EMISSAO           DATE,
   CPF                  VARCHAR2(20),
   SQ_CIDADE_NASC       NUMBER(18),
   PASSAPORTE_NUMERO    VARCHAR2(20),
   SQ_PAIS_PASSAPORTE   NUMBER(18),
   SEXO                 VARCHAR2(1)                    default 'M' not null
      constraint CKC_SEXO_CO_PESSO check (SEXO in ('M','F')),
   INCLUSAO             DATE                           default sysdate not null,
   MATRICULA            VARCHAR2(30),
   constraint PK_CO_PESSOA_FISICA primary key (SQ_PESSOA)
);

comment on table CO_PESSOA_FISICA is
'Armazena dados das pessoas físicas';

comment on column CO_PESSOA_FISICA.SQ_PESSOA is
'Chave de CO_PESSOA. Indica a que pessoa o registro está ligado.';

comment on column CO_PESSOA_FISICA.CLIENTE is
'Chave de CO_PESSOA. Indica a que cliente o registro está ligado.';

comment on column CO_PESSOA_FISICA.NASCIMENTO is
'Data de nascimento da pessoa física';

comment on column CO_PESSOA_FISICA.RG_NUMERO is
'Número do rg';

comment on column CO_PESSOA_FISICA.RG_EMISSOR is
'Órgão emissor do rg';

comment on column CO_PESSOA_FISICA.RG_EMISSAO is
'Data de emissão do registro geral (Identidade)';

comment on column CO_PESSOA_FISICA.CPF is
'CPF da pessoa física.';

comment on column CO_PESSOA_FISICA.SQ_CIDADE_NASC is
'Chave de CO_CIDADE. Indica a cidade de nascimento da pessoa.';

comment on column CO_PESSOA_FISICA.PASSAPORTE_NUMERO is
'Número do passaporte.';

comment on column CO_PESSOA_FISICA.SQ_PAIS_PASSAPORTE is
'Chave de CO_PAIS. Indica o país emissor do passaporte.';

comment on column CO_PESSOA_FISICA.SEXO is
'Indica se esta pessoa jurídica é a sede (matriz).';

comment on column CO_PESSOA_FISICA.INCLUSAO is
'Data de inclusão do registro na tabela.
';

comment on column CO_PESSOA_FISICA.MATRICULA is
'Registro funcional da pessoa na organização.';

/*==============================================================*/
/* Index: IN_COPESFIS_NASC                                      */
/*==============================================================*/
create index IN_COPESFIS_NASC on CO_PESSOA_FISICA (
   NASCIMENTO ASC
);

/*==============================================================*/
/* Index: IN_COPESFIS_SEXO                                      */
/*==============================================================*/
create index IN_COPESFIS_SEXO on CO_PESSOA_FISICA (
   SEXO ASC
);

/*==============================================================*/
/* Index: IN_COPESFIS_CPF                                       */
/*==============================================================*/
create index IN_COPESFIS_CPF on CO_PESSOA_FISICA (
   CPF ASC,
   CLIENTE ASC
);

/*==============================================================*/
/* Index: IN_COPESFIS_CLI                                       */
/*==============================================================*/
create index IN_COPESFIS_CLI on CO_PESSOA_FISICA (
   CLIENTE ASC,
   SQ_PESSOA ASC
);

/*==============================================================*/
/* Table: CO_PESSOA_JURIDICA                                    */
/*==============================================================*/
create table CO_PESSOA_JURIDICA  (
   SQ_PESSOA            number(18)                      not null,
   CLIENTE              number(18)                      not null,
   INICIO_ATIVIDADE     DATE,
   CNPJ                 VARCHAR2(20),
   INSCRICAO_ESTADUAL   VARCHAR2(20),
   SEDE                 VARCHAR2(1)                    default 'S' not null
      constraint CKC_SEDE_CO_PESSO check (SEDE in ('S','N') and SEDE = upper(SEDE)),
   INCLUSAO             DATE                           default sysdate not null,
   constraint PK_CO_PESSOA_JURIDICA primary key (SQ_PESSOA)
);

comment on table CO_PESSOA_JURIDICA is
'Armazena os dados específicos de pessoa jurídica';

comment on column CO_PESSOA_JURIDICA.SQ_PESSOA is
'Chave de CO_PESSOA. Indica a que pessoa o registro está ligado.';

comment on column CO_PESSOA_JURIDICA.CLIENTE is
'Chave de CO_PESSOA. Indica a que cliente o registro está ligado.';

comment on column CO_PESSOA_JURIDICA.INICIO_ATIVIDADE is
'Inicio das atividades da pessoa jurídica';

comment on column CO_PESSOA_JURIDICA.CNPJ is
'CNPJ da pessoa jurídica.';

comment on column CO_PESSOA_JURIDICA.INSCRICAO_ESTADUAL is
'Inscrição estadual da pessoa jurídica.';

comment on column CO_PESSOA_JURIDICA.SEDE is
'Indica se esta pessoa é a sede da empresa';

comment on column CO_PESSOA_JURIDICA.INCLUSAO is
'Data de inclusão do registro na tabela.
';

/*==============================================================*/
/* Index: IN_COPESJUR_CNPJ                                      */
/*==============================================================*/
create index IN_COPESJUR_CNPJ on CO_PESSOA_JURIDICA (
   CNPJ ASC,
   CLIENTE ASC
);

/*==============================================================*/
/* Index: IN_COPESJUR_SEDE                                      */
/*==============================================================*/
create index IN_COPESJUR_SEDE on CO_PESSOA_JURIDICA (
   SEDE ASC,
   CNPJ ASC
);

/*==============================================================*/
/* Index: IN_COPESJUR_INIATI                                    */
/*==============================================================*/
create index IN_COPESJUR_INIATI on CO_PESSOA_JURIDICA (
   INICIO_ATIVIDADE ASC
);

/*==============================================================*/
/* Index: IN_COPESJUR_IE                                        */
/*==============================================================*/
create index IN_COPESJUR_IE on CO_PESSOA_JURIDICA (
   INSCRICAO_ESTADUAL ASC,
   CNPJ ASC
);

/*==============================================================*/
/* Index: IN_COPESJUR_CLI                                       */
/*==============================================================*/
create index IN_COPESJUR_CLI on CO_PESSOA_JURIDICA (
   CLIENTE ASC,
   SQ_PESSOA ASC
);

/*==============================================================*/
/* Table: CO_PESSOA_SEGMENTO                                    */
/*==============================================================*/
create table CO_PESSOA_SEGMENTO  (
   SQ_PESSOA            number(18)                      not null,
   SQ_SEGMENTO          NUMBER(18)                      not null,
   constraint PK_CO_PESSOA_SEGMENTO primary key (SQ_PESSOA, SQ_SEGMENTO)
);

comment on table CO_PESSOA_SEGMENTO is
'Armazena o segmento em que a pessoa se enquadra. É utilizado para definir as regras de negócio do SIW. Só pode haver um registro para cada pessoa.';

comment on column CO_PESSOA_SEGMENTO.SQ_PESSOA is
'Chave de CO_PESSOA. Indica a que pessoa o registro está ligado.';

comment on column CO_PESSOA_SEGMENTO.SQ_SEGMENTO is
'Chave de CO_SEGMENTO. Indica a que segmento o registro está ligado.';

/*==============================================================*/
/* Index: IN_COPESSEG_SQSEG                                     */
/*==============================================================*/
create index IN_COPESSEG_SQSEG on CO_PESSOA_SEGMENTO (
   SQ_SEGMENTO ASC
);

/*==============================================================*/
/* Table: CO_PESSOA_TELEFONE                                    */
/*==============================================================*/
create table CO_PESSOA_TELEFONE  (
   SQ_PESSOA_TELEFONE   NUMBER(18)                      not null,
   SQ_PESSOA            number(18)                      not null,
   SQ_TIPO_TELEFONE     NUMBER(18)                      not null,
   SQ_CIDADE            NUMBER(18)                      not null,
   DDD                  VARCHAR2(4)                     not null,
   NUMERO               VARCHAR2(25)                    not null,
   PADRAO               VARCHAR2(1)                    default 'N' not null
      constraint CKC_PADRAO_COPES check (PADRAO in ('S','N') and PADRAO = upper(PADRAO)),
   constraint PK_CO_PESSOA_TELEFONE primary key (SQ_PESSOA_TELEFONE)
);

comment on table CO_PESSOA_TELEFONE is
'Armazena os endereços da pessoa';

comment on column CO_PESSOA_TELEFONE.SQ_PESSOA_TELEFONE is
'Chave de CO_PESSOA_TELEFONE.';

comment on column CO_PESSOA_TELEFONE.SQ_PESSOA is
'Chave de CO_PESSOA. Indica a que pessoa o registro está ligado.';

comment on column CO_PESSOA_TELEFONE.SQ_TIPO_TELEFONE is
'Chave de CO_TIPO_TELEFONE. Indica a que tipo de telefone o registro está ligado.';

comment on column CO_PESSOA_TELEFONE.SQ_CIDADE is
'Chave de CO_CIDADE. Indica a que cidade o registro está ligado.';

comment on column CO_PESSOA_TELEFONE.DDD is
'DDD do telefone.';

comment on column CO_PESSOA_TELEFONE.NUMERO is
'Número do telefone e ramal.';

comment on column CO_PESSOA_TELEFONE.PADRAO is
'Indica se este registro deve ser apresentado como padrão para o usuário.';

/*==============================================================*/
/* Index: IN_COPESTEL_SQPES                                     */
/*==============================================================*/
create index IN_COPESTEL_SQPES on CO_PESSOA_TELEFONE (
   SQ_PESSOA ASC
);

/*==============================================================*/
/* Index: IN_COPESTEL_TPFONE                                    */
/*==============================================================*/
create index IN_COPESTEL_TPFONE on CO_PESSOA_TELEFONE (
   SQ_TIPO_TELEFONE ASC
);

/*==============================================================*/
/* Index: IN_COPESTEL_SQCID                                     */
/*==============================================================*/
create index IN_COPESTEL_SQCID on CO_PESSOA_TELEFONE (
   SQ_CIDADE ASC
);

/*==============================================================*/
/* Index: IN_COPESTEL_NUMERO                                    */
/*==============================================================*/
create index IN_COPESTEL_NUMERO on CO_PESSOA_TELEFONE (
   NUMERO ASC
);

/*==============================================================*/
/* Table: CO_REGIAO                                             */
/*==============================================================*/
create table CO_REGIAO  (
   SQ_REGIAO            NUMBER(18)                      not null,
   SQ_PAIS              NUMBER(18)                      not null,
   NOME                 VARCHAR2(20)                    not null,
   SIGLA                VARCHAR2(2)                     not null,
   ORDEM                NUMBER(4)                       not null,
   constraint PK_CO_REGIAO primary key (SQ_REGIAO)
);

comment on table CO_REGIAO is
'Armazena a tabela de regiões';

comment on column CO_REGIAO.SQ_REGIAO is
'Chave de CO_REGIAO.';

comment on column CO_REGIAO.SQ_PAIS is
'Chave de CO_PAIS. Indica a que país o registro está ligado.';

comment on column CO_REGIAO.NOME is
'Nome da região.';

comment on column CO_REGIAO.SIGLA is
'Sigla da região.';

comment on column CO_REGIAO.ORDEM is
'Indica a ordem do registro nas listagens.';

/*==============================================================*/
/* Index: IN_COREGIAO_PAIS                                      */
/*==============================================================*/
create index IN_COREGIAO_PAIS on CO_REGIAO (
   SQ_PAIS ASC
);

/*==============================================================*/
/* Index: IN_COREGIAO_NOME                                      */
/*==============================================================*/
create unique index IN_COREGIAO_NOME on CO_REGIAO (
   NOME ASC,
   SQ_PAIS ASC
);

/*==============================================================*/
/* Index: IN_COREGIAO_SIGLA                                     */
/*==============================================================*/
create unique index IN_COREGIAO_SIGLA on CO_REGIAO (
   SIGLA ASC,
   SQ_PAIS ASC
);

/*==============================================================*/
/* Table: CO_SEGMENTO                                           */
/*==============================================================*/
create table CO_SEGMENTO  (
   SQ_SEGMENTO          NUMBER(18)                      not null,
   NOME                 VARCHAR2(40)                    not null,
   ATIVO                VARCHAR2(1)                    default 'S' not null
      constraint CKC_ATIVO_CO_SEGME check (ATIVO in ('S','N') and ATIVO = upper(ATIVO)),
   PADRAO               VARCHAR2(1)                    default 'N' not null
      constraint CKC_PADRAO_COSEG check (PADRAO in ('S','N') and PADRAO = upper(PADRAO)),
   constraint PK_CO_SEGMENTO primary key (SQ_SEGMENTO)
);

comment on table CO_SEGMENTO is
'Armazena a tabela de segmentos onde as pessoas jurídicas se enquadram. Pode ser organismo internacional, órgão público, comércio varejista, franquias, associações etc.';

comment on column CO_SEGMENTO.SQ_SEGMENTO is
'Chave de CO_SEGMENTO.';

comment on column CO_SEGMENTO.NOME is
'Nome do segmento.';

comment on column CO_SEGMENTO.ATIVO is
'Indica se o registro deve ou não ser exibido.';

comment on column CO_SEGMENTO.PADRAO is
'Indica se este registro deve ser apresentado como padrão para o usuário.';

/*==============================================================*/
/* Index: IN_COSEG_NOME                                         */
/*==============================================================*/
create unique index IN_COSEG_NOME on CO_SEGMENTO (
   NOME ASC
);

/*==============================================================*/
/* Index: IN_COSEG_ATIVO                                        */
/*==============================================================*/
create index IN_COSEG_ATIVO on CO_SEGMENTO (
   ATIVO ASC
);

/*==============================================================*/
/* Index: IN_COSEG_PADRAO                                       */
/*==============================================================*/
create index IN_COSEG_PADRAO on CO_SEGMENTO (
   PADRAO ASC
);

/*==============================================================*/
/* Table: CO_TIPO_ENDERECO                                      */
/*==============================================================*/
create table CO_TIPO_ENDERECO  (
   SQ_TIPO_ENDERECO     NUMBER(18)                      not null,
   SQ_TIPO_PESSOA       NUMBER(18)                      not null,
   NOME                 VARCHAR2(30)                    not null,
   ATIVO                VARCHAR2(1)                    default 'S' not null
      constraint CKC_ATIVO_COTIPEND check (ATIVO in ('S','N') and ATIVO = upper(ATIVO)),
   PADRAO               VARCHAR2(1)                    default 'N' not null
      constraint CKC_COTIPEND_PAD
               check (PADRAO in ('S','N') and PADRAO = upper(PADRAO)),
   EMAIL                VARCHAR2(1)                    default 'N' not null
      constraint CKC_EMAIL_CO_TIPO_ check (EMAIL in ('S','N') and EMAIL = upper(EMAIL)),
   INTERNET             VARCHAR2(1)                    default 'N' not null
      constraint CKC_COTIPEND_WEB
               check (INTERNET in ('S','N') and INTERNET = upper(INTERNET)),
   CODIGO_EXTERNO       VARCHAR2(60),
   constraint PK_CO_TIPO_ENDERECO primary key (SQ_TIPO_ENDERECO)
);

comment on table CO_TIPO_ENDERECO is
'Armazena os tipos de endereço';

comment on column CO_TIPO_ENDERECO.SQ_TIPO_ENDERECO is
'Chave de CO_TIPO_ENDERECO.';

comment on column CO_TIPO_ENDERECO.SQ_TIPO_PESSOA is
'Chave de CO_TIPO_PESSOA. Indica a que tipo de pessoa o registro está ligado.';

comment on column CO_TIPO_ENDERECO.NOME is
'Nome do tipo de endereço.';

comment on column CO_TIPO_ENDERECO.ATIVO is
'Indica se o registro deve ou não ser exibido.';

comment on column CO_TIPO_ENDERECO.PADRAO is
'Indica se este registro deve ser apresentado como padrão para o usuário.';

comment on column CO_TIPO_ENDERECO.EMAIL is
'Indica se o endereço é de e-mail.';

comment on column CO_TIPO_ENDERECO.INTERNET is
'Indica se o endereço é de Internet.';

comment on column CO_TIPO_ENDERECO.CODIGO_EXTERNO is
'Código desse registro num sistema externo.';

/*==============================================================*/
/* Index: IN_COTIPEND_TPPES                                     */
/*==============================================================*/
create index IN_COTIPEND_TPPES on CO_TIPO_ENDERECO (
   SQ_TIPO_PESSOA ASC
);

/*==============================================================*/
/* Index: IN_COTIPEND_ATIVO                                     */
/*==============================================================*/
create index IN_COTIPEND_ATIVO on CO_TIPO_ENDERECO (
   ATIVO ASC
);

/*==============================================================*/
/* Index: IN_COTIPEND_PADRAO                                    */
/*==============================================================*/
create index IN_COTIPEND_PADRAO on CO_TIPO_ENDERECO (
   PADRAO ASC
);

/*==============================================================*/
/* Index: IN_COTIPEND_EMAIL                                     */
/*==============================================================*/
create index IN_COTIPEND_EMAIL on CO_TIPO_ENDERECO (
   EMAIL ASC
);

/*==============================================================*/
/* Index: IN_COTIPEND_WEB                                       */
/*==============================================================*/
create index IN_COTIPEND_WEB on CO_TIPO_ENDERECO (
   INTERNET ASC
);

/*==============================================================*/
/* Index: IN_COTIPEND_EXTERNO                                   */
/*==============================================================*/
create index IN_COTIPEND_EXTERNO on CO_TIPO_ENDERECO (
   CODIGO_EXTERNO ASC,
   SQ_TIPO_ENDERECO ASC
);

/*==============================================================*/
/* Table: CO_TIPO_PESSOA                                        */
/*==============================================================*/
create table CO_TIPO_PESSOA  (
   SQ_TIPO_PESSOA       NUMBER(18)                      not null,
   NOME                 VARCHAR2(60),
   ATIVO                VARCHAR2(1)                    default 'S' not null
      constraint CKC_ATIVO_COTIPPES check (ATIVO in ('S','N') and ATIVO = upper(ATIVO)),
   PADRAO               VARCHAR2(1)                    default 'N' not null
      constraint CKC_COTIPPES_PAD
               check (PADRAO in ('S','N') and PADRAO = upper(PADRAO)),
   constraint PK_CO_TIPO_PESSOA primary key (SQ_TIPO_PESSOA)
);

comment on table CO_TIPO_PESSOA is
'Armazena os tipos de pessoa';

comment on column CO_TIPO_PESSOA.SQ_TIPO_PESSOA is
'Chave de CO_TIPO_PESSOA.';

comment on column CO_TIPO_PESSOA.NOME is
'Nome do tipo de pessoa.';

comment on column CO_TIPO_PESSOA.ATIVO is
'Indica se o registro deve ou não ser exibido.';

comment on column CO_TIPO_PESSOA.PADRAO is
'Indica se este registro deve ser apresentado como padrão para o usuário.';

/*==============================================================*/
/* Index: IN_COTIPPES_ATIVO                                     */
/*==============================================================*/
create index IN_COTIPPES_ATIVO on CO_TIPO_PESSOA (
   ATIVO ASC
);

/*==============================================================*/
/* Index: IN_COTIPPES_PADRAO                                    */
/*==============================================================*/
create index IN_COTIPPES_PADRAO on CO_TIPO_PESSOA (
   PADRAO ASC
);

/*==============================================================*/
/* Table: CO_TIPO_TELEFONE                                      */
/*==============================================================*/
create table CO_TIPO_TELEFONE  (
   SQ_TIPO_TELEFONE     NUMBER(18)                      not null,
   SQ_TIPO_PESSOA       NUMBER(18)                      not null,
   NOME                 VARCHAR2(25)                    not null,
   ATIVO                VARCHAR2(1)                    default 'S' not null
      constraint CKC_ATIVO_COTIPTEL check (ATIVO in ('S','N') and ATIVO = upper(ATIVO)),
   PADRAO               VARCHAR2(1)                    default 'N' not null
      constraint CKC_COTIPTEL_PAD
               check (PADRAO in ('S','N') and PADRAO = upper(PADRAO)),
   constraint PK_CO_TIPO_TELEFONE primary key (SQ_TIPO_TELEFONE)
);

comment on table CO_TIPO_TELEFONE is
'Armazena os tipos de telefone';

comment on column CO_TIPO_TELEFONE.SQ_TIPO_TELEFONE is
'Chave de CO_TIPO_TELEFONE.';

comment on column CO_TIPO_TELEFONE.SQ_TIPO_PESSOA is
'Chave de CO_TIPO_PESSOA. Indica a que tipo de pessoa o registro está ligado.';

comment on column CO_TIPO_TELEFONE.NOME is
'Nome do tipo de telefone.';

comment on column CO_TIPO_TELEFONE.ATIVO is
'Indica se o registro deve ou não ser exibido.';

comment on column CO_TIPO_TELEFONE.PADRAO is
'Indica se este registro deve ser apresentado como padrão para o usuário.';

/*==============================================================*/
/* Index: IN_COTIPTEL_TPPES                                     */
/*==============================================================*/
create index IN_COTIPTEL_TPPES on CO_TIPO_TELEFONE (
   SQ_TIPO_PESSOA ASC
);

/*==============================================================*/
/* Table: CO_TIPO_VINCULO                                       */
/*==============================================================*/
create table CO_TIPO_VINCULO  (
   SQ_TIPO_VINCULO      number(18)                      not null,
   SQ_TIPO_PESSOA       NUMBER(18)                      not null,
   CLIENTE              number(18),
   NOME                 VARCHAR2(21)                    not null,
   INTERNO              VARCHAR2(1)                    default 'N' not null
      constraint CKC_COTIPVIN_INT
               check (INTERNO in ('S','N') and INTERNO = upper(INTERNO)),
   ATIVO                VARCHAR2(1)                    default 'S' not null
      constraint CKC_ATIVO_COTIPVIN check (ATIVO in ('S','N') and ATIVO = upper(ATIVO)),
   PADRAO               VARCHAR2(1)                    default 'N' not null
      constraint CKC_COTIPVIN_PAD
               check (PADRAO in ('S','N') and PADRAO = upper(PADRAO)),
   CONTRATADO           VARCHAR2(1)                    default 'N' not null
      constraint CKC_COTIPVIN_CONT
               check (CONTRATADO in ('S','N') and CONTRATADO = upper(CONTRATADO)),
   ORDEM                NUMBER(6),
   CODIGO_EXTERNO       VARCHAR2(60),
   ENVIA_MAIL_TRAMITE   VARCHAR2(1)                    default 'S' not null
      constraint CKC_ENVIA_MAIL_TRAMIT_CO_TIPO_ check (ENVIA_MAIL_TRAMITE in ('S','N') and ENVIA_MAIL_TRAMITE = upper(ENVIA_MAIL_TRAMITE)),
   ENVIA_MAIL_ALERTA    VARCHAR2(1)                    default 'S' not null
      constraint CKC_ENVIA_MAIL_ALERTA_CO_TIPO_ check (ENVIA_MAIL_ALERTA in ('S','N') and ENVIA_MAIL_ALERTA = upper(ENVIA_MAIL_ALERTA)),
   constraint PK_CO_TIPO_VINCULO primary key (SQ_TIPO_VINCULO)
);

comment on table CO_TIPO_VINCULO is
'Armazena os tipos de vinculo entre pessoas físicas e jurídicas';

comment on column CO_TIPO_VINCULO.SQ_TIPO_VINCULO is
'Chave de CO_TIPO_VINCULO.';

comment on column CO_TIPO_VINCULO.SQ_TIPO_PESSOA is
'Chave de CO_TIPO_PESSOA. Indica a que tipo de pessoa o registro está ligado.';

comment on column CO_TIPO_VINCULO.CLIENTE is
'Chave de CO_PESSOA. Indica a que cliente o registro está ligado.';

comment on column CO_TIPO_VINCULO.NOME is
'Nome do tipo de vínculo.';

comment on column CO_TIPO_VINCULO.INTERNO is
'Indica se o vínculo é interno à organização.';

comment on column CO_TIPO_VINCULO.ATIVO is
'Indica se o registro deve ou não ser exibido.';

comment on column CO_TIPO_VINCULO.PADRAO is
'Indica se este registro deve ser apresentado como padrão para o usuário.';

comment on column CO_TIPO_VINCULO.CONTRATADO is
'Indica se a pessoa é contratada ou não pela organização.';

comment on column CO_TIPO_VINCULO.ORDEM is
'Indica a ordem do registro nas listagens.';

comment on column CO_TIPO_VINCULO.CODIGO_EXTERNO is
'Código desse registro num sistema externo.';

comment on column CO_TIPO_VINCULO.ENVIA_MAIL_TRAMITE is
'Indica se usuários desse vínculo devem ser receber e-mail de alerta quando houver tramitação ou conclusão das solicitações.';

comment on column CO_TIPO_VINCULO.ENVIA_MAIL_ALERTA is
'Indica se usuários desse vínculo devem receber e-mail de alerta de atraso ou proximidade.';

/*==============================================================*/
/* Index: IN_COTIPVIN_TPPES                                     */
/*==============================================================*/
create index IN_COTIPVIN_TPPES on CO_TIPO_VINCULO (
   CLIENTE ASC,
   SQ_TIPO_PESSOA ASC,
   SQ_TIPO_VINCULO ASC
);

/*==============================================================*/
/* Index: IN_COTIPVIN_ATIVO                                     */
/*==============================================================*/
create index IN_COTIPVIN_ATIVO on CO_TIPO_VINCULO (
   CLIENTE ASC,
   ATIVO ASC,
   SQ_TIPO_VINCULO ASC
);

/*==============================================================*/
/* Index: IN_COTIPVIN_PADRAO                                    */
/*==============================================================*/
create index IN_COTIPVIN_PADRAO on CO_TIPO_VINCULO (
   CLIENTE ASC,
   PADRAO ASC,
   SQ_TIPO_VINCULO ASC
);

/*==============================================================*/
/* Index: IN_COTIPVIN_INT                                       */
/*==============================================================*/
create index IN_COTIPVIN_INT on CO_TIPO_VINCULO (
   CLIENTE ASC,
   INTERNO ASC,
   SQ_TIPO_VINCULO ASC
);

/*==============================================================*/
/* Index: IN_COTIPVIN_CONTR                                     */
/*==============================================================*/
create index IN_COTIPVIN_CONTR on CO_TIPO_VINCULO (
   CLIENTE ASC,
   CONTRATADO ASC,
   SQ_TIPO_VINCULO ASC
);

/*==============================================================*/
/* Index: IN_COTIPVIN_EXTERNO                                   */
/*==============================================================*/
create index IN_COTIPVIN_EXTERNO on CO_TIPO_VINCULO (
   CLIENTE ASC,
   CODIGO_EXTERNO ASC,
   SQ_TIPO_VINCULO ASC
);

/*==============================================================*/
/* Table: CO_UF                                                 */
/*==============================================================*/
create table CO_UF  (
   CO_UF                VARCHAR2(3)                     not null,
   SQ_PAIS              NUMBER(18)                      not null,
   SQ_REGIAO            NUMBER(18)                      not null,
   NOME                 VARCHAR2(30)                    not null,
   ATIVO                VARCHAR2(1)                    default 'S' not null
      constraint CKC_ATIVO_CO_UF check (ATIVO in ('S','N') and ATIVO = upper(ATIVO)),
   PADRAO               VARCHAR2(1)                    default 'N' not null
      constraint CKC_PADRAO_COUF check (PADRAO in ('S','N') and PADRAO = upper(PADRAO)),
   CODIGO_IBGE          VARCHAR2(2),
   ORDEM                NUMBER(5),
   constraint PK_CO_UF primary key (CO_UF, SQ_PAIS)
);

comment on table CO_UF is
'Armazena a tabela de estados';

comment on column CO_UF.CO_UF is
'Chave de CO_UF.';

comment on column CO_UF.SQ_PAIS is
'Chave de CO_PAIS. Indica a que país o registro está ligado.';

comment on column CO_UF.SQ_REGIAO is
'Chave de CO_REGIAO. Indica a que região o registro está ligado.';

comment on column CO_UF.NOME is
'Nome da unidade da federação.';

comment on column CO_UF.ATIVO is
'Indica se o registro deve ou não ser exibido.';

comment on column CO_UF.PADRAO is
'Indica se este registro deve ser apresentado como padrão para o usuário.';

comment on column CO_UF.CODIGO_IBGE is
'Código IBGE da UF. Este código abrange a região e a UF.';

comment on column CO_UF.ORDEM is
'Indica a ordem do registro nas listagens.';

/*==============================================================*/
/* Index: IN_COUF_PAISREGIAO                                    */
/*==============================================================*/
create index IN_COUF_PAISREGIAO on CO_UF (
   SQ_REGIAO ASC,
   SQ_PAIS ASC
);

/*==============================================================*/
/* Index: IN_COUF_NOME                                          */
/*==============================================================*/
create unique index IN_COUF_NOME on CO_UF (
   NOME ASC,
   SQ_PAIS ASC
);

/*==============================================================*/
/* Table: CO_UNIDADE_MEDIDA                                     */
/*==============================================================*/
create table CO_UNIDADE_MEDIDA  (
   SQ_UNIDADE_MEDIDA    NUMBER(18)                      not null,
   CLIENTE              NUMBER(18)                      not null,
   NOME                 VARCHAR2(30)                    not null,
   SIGLA                VARCHAR2(10)                    not null,
   ATIVO                VARCHAR2(1)                     not null,
   constraint PK_CO_UNIDADE_MEDIDA primary key (SQ_UNIDADE_MEDIDA)
);

comment on table CO_UNIDADE_MEDIDA is
'Registra as unidades de medida.';

comment on column CO_UNIDADE_MEDIDA.SQ_UNIDADE_MEDIDA is
'Chave de CO_UNIDADE_MEDIDA.';

comment on column CO_UNIDADE_MEDIDA.CLIENTE is
'Chave de CO_PESSOA. Indica a que cliente a unidade de medida está vinculada.';

comment on column CO_UNIDADE_MEDIDA.NOME is
'Nome da unidade de medida.';

comment on column CO_UNIDADE_MEDIDA.SIGLA is
'Sigla da unidade de medida.';

comment on column CO_UNIDADE_MEDIDA.ATIVO is
'Indica se a unidade de medida pode ser vinculaao a novos registros.';

/*==============================================================*/
/* Index: IN_COUNIMED_CLIENTE                                   */
/*==============================================================*/
create index IN_COUNIMED_CLIENTE on CO_UNIDADE_MEDIDA (
   CLIENTE ASC,
   SQ_UNIDADE_MEDIDA ASC
);

/*==============================================================*/
/* Index: IN_COUNIMED_NOME                                      */
/*==============================================================*/
create index IN_COUNIMED_NOME on CO_UNIDADE_MEDIDA (
   CLIENTE ASC,
   NOME ASC,
   SQ_UNIDADE_MEDIDA ASC
);

/*==============================================================*/
/* Index: IN_COUNIMED_SIGLA                                     */
/*==============================================================*/
create index IN_COUNIMED_SIGLA on CO_UNIDADE_MEDIDA (
   CLIENTE ASC,
   SIGLA ASC,
   SQ_UNIDADE_MEDIDA ASC
);

/*==============================================================*/
/* Index: IN_COUNIMED_ATIVO                                     */
/*==============================================================*/
create index IN_COUNIMED_ATIVO on CO_UNIDADE_MEDIDA (
   CLIENTE ASC,
   ATIVO ASC,
   SQ_UNIDADE_MEDIDA ASC
);

/*==============================================================*/
/* Table: DC_ARQUIVO                                            */
/*==============================================================*/
create table DC_ARQUIVO  (
   SQ_ARQUIVO           NUMBER(18)                      not null,
   SQ_SISTEMA           NUMBER(18)                      not null,
   NOME                 VARCHAR2(40)                    not null,
   DESCRICAO            VARCHAR2(4000)                  not null,
   TIPO                 VARCHAR2(1)                    default 'G' not null
      constraint CKC_TIPO_DC_ARQUI check (TIPO in ('G','I','C','R')),
   DIRETORIO            VARCHAR2(100),
   constraint PK_DC_ARQUIVO primary key (SQ_ARQUIVO)
);

comment on table DC_ARQUIVO is
'Armazena os arquivos de um sistema.';

comment on column DC_ARQUIVO.SQ_ARQUIVO is
'Chave de SIW_ARQUIVO. Indica a que arquivo o registro está ligado.';

comment on column DC_ARQUIVO.SQ_SISTEMA is
'Chave de DC_SISTEMA. Indica a que sistema o registro está ligado.';

comment on column DC_ARQUIVO.NOME is
'Nome do arquivo.';

comment on column DC_ARQUIVO.DESCRICAO is
'Descrição do arquivo.';

comment on column DC_ARQUIVO.TIPO is
'Armazena o tipo do arquivo (G - rotinas genéricas; I - inclusão; C - configuração; R - requisitos)';

comment on column DC_ARQUIVO.DIRETORIO is
'Diretório onde o arquivo encontra-se.';

/*==============================================================*/
/* Index: IN_DCARQ_SISTEMA                                      */
/*==============================================================*/
create index IN_DCARQ_SISTEMA on DC_ARQUIVO (
   SQ_SISTEMA ASC,
   SQ_ARQUIVO ASC
);

/*==============================================================*/
/* Index: IN_DCARQ_NOME                                         */
/*==============================================================*/
create unique index IN_DCARQ_NOME on DC_ARQUIVO (
   NOME ASC,
   DIRETORIO ASC,
   SQ_SISTEMA ASC
);

/*==============================================================*/
/* Table: DC_COLUNA                                             */
/*==============================================================*/
create table DC_COLUNA  (
   SQ_COLUNA            NUMBER(18)                      not null,
   SQ_TABELA            NUMBER(18)                      not null,
   SQ_DADO_TIPO         NUMBER(18)                      not null,
   NOME                 VARCHAR2(30)                    not null,
   DESCRICAO            VARCHAR2(4000)                  not null,
   ORDEM                NUMBER(18),
   TAMANHO              NUMBER(18)                      not null,
   PRECISAO             NUMBER(18),
   ESCALA               NUMBER(18),
   OBRIGATORIO          VARCHAR2(1)                    default 'N' not null
      constraint CKC_OBRIGATORIO_DC_COLUN check (OBRIGATORIO in ('S','N') and OBRIGATORIO = upper(OBRIGATORIO)),
   VALOR_PADRAO         VARCHAR2(255),
   constraint PK_DC_COLUNA primary key (SQ_COLUNA)
);

comment on table DC_COLUNA is
'Armazena dados das colunas.';

comment on column DC_COLUNA.SQ_COLUNA is
'Chave de DC_COLUNA.';

comment on column DC_COLUNA.SQ_TABELA is
'Chave de DC_TABELA. Indica a que tabela o registro está ligado.';

comment on column DC_COLUNA.SQ_DADO_TIPO is
'Chave de DC_DADO_TIPO. Indica a que tipo de dado o registro está ligado.';

comment on column DC_COLUNA.NOME is
'Nome da coluna.';

comment on column DC_COLUNA.DESCRICAO is
'Finalidade da coluna.';

comment on column DC_COLUNA.ORDEM is
'Número de ordem da coluna na tabela.';

comment on column DC_COLUNA.TAMANHO is
'Tamanho da coluna, em bytes.';

comment on column DC_COLUNA.PRECISAO is
'Número de casas decimais quando for a coluna for do tipo numérico';

comment on column DC_COLUNA.ESCALA is
'Número de dígitos à direita da vírgula decimal, quando a coluna for do tipo numérico.';

comment on column DC_COLUNA.OBRIGATORIO is
'Indica se o campo é de preenchimento obrigqatório.';

comment on column DC_COLUNA.VALOR_PADRAO is
'Valor da coluna, caso não seja especificado um.';

/*==============================================================*/
/* Index: IN_DCCOL_TIPO                                         */
/*==============================================================*/
create index IN_DCCOL_TIPO on DC_COLUNA (
   SQ_DADO_TIPO ASC,
   SQ_COLUNA ASC
);

/*==============================================================*/
/* Index: IN_DCCOL_TABELA                                       */
/*==============================================================*/
create index IN_DCCOL_TABELA on DC_COLUNA (
   SQ_TABELA ASC,
   SQ_COLUNA ASC
);

/*==============================================================*/
/* Index: IN_DCCOL_NOME                                         */
/*==============================================================*/
create unique index IN_DCCOL_NOME on DC_COLUNA (
   NOME ASC,
   SQ_TABELA ASC
);

/*==============================================================*/
/* Table: DC_DADO_TIPO                                          */
/*==============================================================*/
create table DC_DADO_TIPO  (
   SQ_DADO_TIPO         NUMBER(18)                      not null,
   NOME                 VARCHAR(30)                     not null,
   DESCRICAO            VARCHAR2(4000)                  not null,
   constraint PK_DC_DADO_TIPO primary key (SQ_DADO_TIPO)
);

comment on table DC_DADO_TIPO is
'Armazena os tipos de dado válidos.';

comment on column DC_DADO_TIPO.SQ_DADO_TIPO is
'Chave de DC_DADO_TIPO.';

comment on column DC_DADO_TIPO.NOME is
'Nome do tipo de dado.';

comment on column DC_DADO_TIPO.DESCRICAO is
'Descrição do tipo de dado.';

/*==============================================================*/
/* Index: IN_DCDADTIP_NOME                                      */
/*==============================================================*/
create unique index IN_DCDADTIP_NOME on DC_DADO_TIPO (
   NOME ASC
);

/*==============================================================*/
/* Table: DC_ESQUEMA                                            */
/*==============================================================*/
create table DC_ESQUEMA  (
   SQ_ESQUEMA           NUMBER(18)                      not null,
   CLIENTE              NUMBER(18)                      not null,
   SQ_MODULO            NUMBER(18)                      not null,
   NOME                 VARCHAR(60)                     not null,
   DESCRICAO            VARCHAR(500),
   TIPO                 VARCHAR2(1)                    default 'I' not null
      constraint CKC_TIPO_DC_ESQUE check (TIPO in ('I','E')),
   ATIVO                VARCHAR2(1)                    default 'S' not null
      constraint CKC_ATIVO_DC_ESQUE check (ATIVO in ('S','N') and ATIVO = upper(ATIVO)),
   FORMATO              VARCHAR2(1)                    default 'A' not null
      constraint CKC_FORMATO_DC_ESQUE check (FORMATO in ('A','W','T')),
   WS_SERVIDOR          VARCHAR2(100),
   WS_URL               VARCHAR2(100),
   WS_ACAO              VARCHAR2(100),
   WS_MENSAGEM          VARCHAR2(4000),
   NO_RAIZ              VARCHAR2(50),
   BD_HOSTNAME          VARCHAR2(50),
   BD_USERNAME          VARCHAR2(50),
   BD_PASSWORD          VARCHAR2(50),
   TX_DELIMITADOR       VARCHAR2(5),
   TIPO_EFETIVACAO      NUMBER(1)                      default 0 not null
      constraint CKC_TIPO_EFETIVACAO_DC_ESQUE check (TIPO_EFETIVACAO in (0,1)),
   TX_ORIGEM_ARQUIVOS   NUMBER(1)                      default 0
      constraint CKC_TX_ORIGEM_ARQUIVO_DC_ESQUE check (TX_ORIGEM_ARQUIVOS is null or (TX_ORIGEM_ARQUIVOS in (0,1))),
   FTP_HOSTNAME         VARCHAR2(50),
   FTP_USERNAME         VARCHAR2(50),
   FTP_PASSWORD         VARCHAR2(50),
   FTP_DIRETORIO        VARCHAR2(100),
   ENVIA_MAIL           NUMBER(1)                      default 0
      constraint CKC_ENVIA_MAIL_DC_ESQUE check (ENVIA_MAIL is null or (ENVIA_MAIL in (0,1,2))),
   LISTA_MAIL           VARCHAR2(255),
   constraint PK_DC_ESQUEMA primary key (SQ_ESQUEMA)
);

comment on table DC_ESQUEMA is
'Registra os esquemas de importação e exprotação.';

comment on column DC_ESQUEMA.SQ_ESQUEMA is
'Chave de DC_ESQUEMA.';

comment on column DC_ESQUEMA.CLIENTE is
'Chave de CO_PESSOA. Indica a que cliente o esquema pertence.';

comment on column DC_ESQUEMA.SQ_MODULO is
'Chave de SIW_MODULO. Indica a que módulo o esquema está vinculado.';

comment on column DC_ESQUEMA.NOME is
'Nome do esquema.';

comment on column DC_ESQUEMA.DESCRICAO is
'Descrição do esquema.';

comment on column DC_ESQUEMA.TIPO is
'Indica o tipo do esquema. I para importação, E para exportação.';

comment on column DC_ESQUEMA.ATIVO is
'Indica se esta tabela deve ser tratada em novas rotinas de importação ou exportação.';

comment on column DC_ESQUEMA.FORMATO is
'Indica o formato do esquema: Arquivo (A), web service (W) ou TXT delimitado (T).';

comment on column DC_ESQUEMA.WS_SERVIDOR is
'Servidor onde o Web Service está instalado.';

comment on column DC_ESQUEMA.WS_URL is
'URL do web service.';

comment on column DC_ESQUEMA.WS_ACAO is
'Ação SOAP a ser executada.';

comment on column DC_ESQUEMA.WS_MENSAGEM is
'Mensagem SOAP a ser enviada para o Web Service.';

comment on column DC_ESQUEMA.NO_RAIZ is
'Tag do esquema no arquivo XML.';

comment on column DC_ESQUEMA.BD_HOSTNAME is
'Servidor de destino para importação de dados.';

comment on column DC_ESQUEMA.BD_USERNAME is
'Usuário de destino para importação de dados.';

comment on column DC_ESQUEMA.BD_PASSWORD is
'Senha do usuário de destino para importação de dados.';

comment on column DC_ESQUEMA.TX_DELIMITADOR is
'Delimitador a ser usado nos arquivos de importação.';

comment on column DC_ESQUEMA.TIPO_EFETIVACAO is
'(0) efetiva mesmo que haja algum registro errado (1) efetiva somente se não achar registro errado.';

comment on column DC_ESQUEMA.TX_ORIGEM_ARQUIVOS is
'Indica como os arquivos TXT devem ser obtidos. (0) Do diretório padrão (1) De servidor FTP.';

comment on column DC_ESQUEMA.FTP_HOSTNAME is
'Endereço do servidor FTP a ser usado para obter os arquivos TXT.';

comment on column DC_ESQUEMA.FTP_USERNAME is
'Username do servidor FTP a ser usado para obter os arquivos TXT.';

comment on column DC_ESQUEMA.FTP_PASSWORD is
'Senha do servidor FTP a ser usado para obter os arquivos TXT.';

comment on column DC_ESQUEMA.FTP_DIRETORIO is
'Diretório do servidor FTP a ser usado para obter os arquivos TXT.';

comment on column DC_ESQUEMA.ENVIA_MAIL is
'Define forma de envio de e-mails. (0) Não envia (1) Envia sempre (2) Envia somente em caso de insucesso';

comment on column DC_ESQUEMA.LISTA_MAIL is
'Endereços para envio de e-mails.';

/*==============================================================*/
/* Index: IN_DCESQ_CLIENTE                                      */
/*==============================================================*/
create index IN_DCESQ_CLIENTE on DC_ESQUEMA (
   CLIENTE ASC,
   SQ_ESQUEMA ASC
);

/*==============================================================*/
/* Index: IN_DCESQ_MODULO                                       */
/*==============================================================*/
create index IN_DCESQ_MODULO on DC_ESQUEMA (
   CLIENTE ASC,
   SQ_MODULO ASC,
   SQ_ESQUEMA ASC
);

/*==============================================================*/
/* Index: IN_DCESQ_NOME                                         */
/*==============================================================*/
create unique index IN_DCESQ_NOME on DC_ESQUEMA (
   CLIENTE ASC,
   NOME ASC,
   SQ_MODULO ASC
);

/*==============================================================*/
/* Table: DC_ESQUEMA_ATRIBUTO                                   */
/*==============================================================*/
create table DC_ESQUEMA_ATRIBUTO  (
   SQ_ESQUEMA_ATRIBUTO  NUMBER(18)                      not null,
   SQ_ESQUEMA_TABELA    NUMBER(18)                      not null,
   SQ_COLUNA            NUMBER(18)                      not null,
   ORDEM                NUMBER(4)                      default 0 not null,
   CAMPO_EXTERNO        VARCHAR2(30)                    not null,
   MASCARA_DATA         VARCHAR2(50),
   VALOR_DEFAULT        VARCHAR2(50),
   constraint PK_DC_ESQUEMA_ATRIBUTO primary key (SQ_ESQUEMA_ATRIBUTO)
);

comment on table DC_ESQUEMA_ATRIBUTO is
'Registra o mapeamento entre o atributo do arquivo XML e o campo.';

comment on column DC_ESQUEMA_ATRIBUTO.SQ_ESQUEMA_ATRIBUTO is
'Chave de DC_ESQUEMA_ATRIBUTO.';

comment on column DC_ESQUEMA_ATRIBUTO.SQ_ESQUEMA_TABELA is
'Chave de DC_ESQUEMA_TABELA. Indica a que tabela refere-se este mapeamento.';

comment on column DC_ESQUEMA_ATRIBUTO.SQ_COLUNA is
'Chave de DC_COLUNA. Indica a que coluna da tabela refere-se este mapeamento.';

comment on column DC_ESQUEMA_ATRIBUTO.ORDEM is
'Número de ordem do campo, utilizado para exportação. Para importação, a seqüência será igual a DC_COLUNA.ORDEM.';

comment on column DC_ESQUEMA_ATRIBUTO.CAMPO_EXTERNO is
'Nome do campo no arquivo de inclusão ou exclusão.';

comment on column DC_ESQUEMA_ATRIBUTO.MASCARA_DATA is
'Indica a máscara para campos data.';

comment on column DC_ESQUEMA_ATRIBUTO.VALOR_DEFAULT is
'Indica o valor a ser inserido caso o campo de origem seja nulo.';

/*==============================================================*/
/* Index: IN_DCESQATR_TABELA                                    */
/*==============================================================*/
create index IN_DCESQATR_TABELA on DC_ESQUEMA_ATRIBUTO (
   SQ_ESQUEMA_TABELA ASC,
   SQ_COLUNA ASC,
   SQ_ESQUEMA_ATRIBUTO ASC
);

/*==============================================================*/
/* Index: IN_DCESQATR_COLUNA                                    */
/*==============================================================*/
create unique index IN_DCESQATR_COLUNA on DC_ESQUEMA_ATRIBUTO (
   SQ_COLUNA ASC,
   SQ_ESQUEMA_TABELA ASC
);

/*==============================================================*/
/* Table: DC_ESQUEMA_INSERT                                     */
/*==============================================================*/
create table DC_ESQUEMA_INSERT  (
   SQ_ESQUEMA_INSERT    NUMBER(18)                      not null,
   SQ_ESQUEMA_TABELA    NUMBER(18),
   REGISTRO             NUMBER(4)                       not null,
   SQ_COLUNA            NUMBER(18),
   ORDEM                NUMBER(4)                      default 0 not null,
   VALOR                VARCHAR2(255),
   constraint PK_DC_ESQUEMA_INSERT primary key (SQ_ESQUEMA_INSERT)
);

comment on table DC_ESQUEMA_INSERT is
'Registra inserções de registro numa tabela.';

comment on column DC_ESQUEMA_INSERT.SQ_ESQUEMA_INSERT is
'Chave de DC_ESQUEMA_INSERT.';

comment on column DC_ESQUEMA_INSERT.SQ_ESQUEMA_TABELA is
'Chave de DC_ESQUEMA_TABELA. Indica a tabela que receberá o registro.';

comment on column DC_ESQUEMA_INSERT.REGISTRO is
'Número do registro. Define a ordem de inserção.';

comment on column DC_ESQUEMA_INSERT.SQ_COLUNA is
'Chave de DC_COLUNA. Indica o campo que receberá o valor.';

comment on column DC_ESQUEMA_INSERT.ORDEM is
'Número de ordem do campo, utilizado para montagem do comando de inserção.';

comment on column DC_ESQUEMA_INSERT.VALOR is
'Valor a ser inserido no campo.';

/*==============================================================*/
/* Index: IN_DCESQINS_TAB                                       */
/*==============================================================*/
create index IN_DCESQINS_TAB on DC_ESQUEMA_INSERT (
   SQ_ESQUEMA_TABELA ASC,
   SQ_ESQUEMA_INSERT ASC
);

/*==============================================================*/
/* Index: IN_DCESQINS_COL                                       */
/*==============================================================*/
create index IN_DCESQINS_COL on DC_ESQUEMA_INSERT (
   SQ_COLUNA ASC,
   SQ_ESQUEMA_INSERT ASC
);

/*==============================================================*/
/* Table: DC_ESQUEMA_SCRIPT                                     */
/*==============================================================*/
create table DC_ESQUEMA_SCRIPT  (
   SQ_ESQUEMA_SCRIPT    NUMBER(18)                      not null,
   SQ_ESQUEMA           NUMBER(18)                      not null,
   SQ_SIW_ARQUIVO       NUMBER(18)                      not null,
   ORDEM                NUMBER(4)                      default 0 not null,
   constraint PK_DC_ESQUEMA_SCRIPT primary key (SQ_ESQUEMA_SCRIPT)
);

comment on table DC_ESQUEMA_SCRIPT is
'Registra scripts a serem disparados pelo esquema.';

comment on column DC_ESQUEMA_SCRIPT.SQ_ESQUEMA_SCRIPT is
'Chave de DC_ESQUEMA_SCRIPT.';

comment on column DC_ESQUEMA_SCRIPT.SQ_ESQUEMA is
'Chave de DC_ESQUEMA. Indica o esquema ao qual o script está ligado.';

comment on column DC_ESQUEMA_SCRIPT.SQ_SIW_ARQUIVO is
'chave de SIW_ARQUIVO, indicando o arquivo de upload do script.';

comment on column DC_ESQUEMA_SCRIPT.ORDEM is
'Informa posição deste script na execução do programa de carga.';

/*==============================================================*/
/* Index: IN_DCESQSCR_ESQUEMA                                   */
/*==============================================================*/
create index IN_DCESQSCR_ESQUEMA on DC_ESQUEMA_SCRIPT (
   SQ_ESQUEMA ASC,
   SQ_ESQUEMA_SCRIPT ASC
);

/*==============================================================*/
/* Table: DC_ESQUEMA_TABELA                                     */
/*==============================================================*/
create table DC_ESQUEMA_TABELA  (
   SQ_ESQUEMA_TABELA    NUMBER(18)                      not null,
   SQ_ESQUEMA           NUMBER(18)                      not null,
   SQ_TABELA            NUMBER(18)                      not null,
   ORDEM                NUMBER(4)                      default 0 not null,
   ELEMENTO             VARCHAR2(255)                   not null,
   REMOVE_REGISTRO      VARCHAR2(1)                    default 'N' not null
      constraint CKC_REMOVE_REGISTRO_DC_ESQUE check (REMOVE_REGISTRO in ('S','N') and REMOVE_REGISTRO = upper(REMOVE_REGISTRO)),
   constraint PK_DC_ESQUEMA_TABELA primary key (SQ_ESQUEMA_TABELA)
);

comment on table DC_ESQUEMA_TABELA is
'Registra as tabelas do dicionário que serão integradas a sistemas externos.';

comment on column DC_ESQUEMA_TABELA.SQ_ESQUEMA_TABELA is
'Chave de DC_ESQUEMA_TABELA.';

comment on column DC_ESQUEMA_TABELA.SQ_ESQUEMA is
'Chave de DC_ESQUEMA. Indica a que esquema o registro está ligado.';

comment on column DC_ESQUEMA_TABELA.SQ_TABELA is
'Chave de DC_TABELA. Indica a que tabela o registro está ligado.';

comment on column DC_ESQUEMA_TABELA.ORDEM is
'Informa posição desta tabela na lista de importação ou exportação.';

comment on column DC_ESQUEMA_TABELA.ELEMENTO is
'Indica o elemento do arquivo XML que contém os dados da tabela. Para arquivos TXT indica o caminho físico do arquivo a ser importado.';

comment on column DC_ESQUEMA_TABELA.REMOVE_REGISTRO is
'Indica se o conteúdo da tabela deve ser removido antes de iniciar a carga dos dados.';

/*==============================================================*/
/* Index: IN_DCESQTAB_TABELA                                    */
/*==============================================================*/
create unique index IN_DCESQTAB_TABELA on DC_ESQUEMA_TABELA (
   SQ_TABELA ASC,
   SQ_ESQUEMA ASC
);

/*==============================================================*/
/* Table: DC_EVENTO                                             */
/*==============================================================*/
create table DC_EVENTO  (
   SQ_EVENTO            NUMBER(18)                      not null,
   NOME                 VARCHAR2(30)                    not null,
   DESCRICAO            VARCHAR2(4000)                  not null,
   constraint PK_DC_EVENTO primary key (SQ_EVENTO)
);

comment on table DC_EVENTO is
'Armazena os eventos possíveis para uma trigger.';

comment on column DC_EVENTO.SQ_EVENTO is
'Chave de DC_EVENTO.';

comment on column DC_EVENTO.NOME is
'Nome do evento.';

comment on column DC_EVENTO.DESCRICAO is
'Descrição do evento.';

/*==============================================================*/
/* Index: IN_DCEVE_NOME                                         */
/*==============================================================*/
create unique index IN_DCEVE_NOME on DC_EVENTO (
   NOME ASC
);

/*==============================================================*/
/* Table: DC_INDICE                                             */
/*==============================================================*/
create table DC_INDICE  (
   SQ_INDICE            NUMBER(18)                      not null,
   SQ_INDICE_TIPO       NUMBER(18)                      not null,
   SQ_USUARIO           NUMBER(18)                      not null,
   SQ_SISTEMA           NUMBER(18)                      not null,
   NOME                 VARCHAR2(30)                    not null,
   DESCRICAO            VARCHAR2(4000)                  not null,
   constraint PK_DC_INDICE primary key (SQ_INDICE)
);

comment on table DC_INDICE is
'Armazena os índices das tabelas.';

comment on column DC_INDICE.SQ_INDICE is
'Chave de DC_INDICE.';

comment on column DC_INDICE.SQ_INDICE_TIPO is
'Chave de DC_INDICE_TIPO. Indica a que tipo o registro está ligado.';

comment on column DC_INDICE.SQ_USUARIO is
'Chave de DC_USUARIO. Indica a que usuário o do banco de dados o registro está ligado.';

comment on column DC_INDICE.SQ_SISTEMA is
'Chave de DC_SISTEMA. Indica a que sistema o registro está ligado.';

comment on column DC_INDICE.NOME is
'Nome do índice.';

comment on column DC_INDICE.DESCRICAO is
'Descrição do índice.';

/*==============================================================*/
/* Index: IN_DCIND_TIPO                                         */
/*==============================================================*/
create index IN_DCIND_TIPO on DC_INDICE (
   SQ_INDICE_TIPO ASC,
   SQ_INDICE ASC
);

/*==============================================================*/
/* Index: IN_DCIND_SISTEMA                                      */
/*==============================================================*/
create index IN_DCIND_SISTEMA on DC_INDICE (
   SQ_SISTEMA ASC,
   SQ_INDICE ASC
);

/*==============================================================*/
/* Index: IN_DCIND_NOME                                         */
/*==============================================================*/
create unique index IN_DCIND_NOME on DC_INDICE (
   NOME ASC,
   SQ_USUARIO ASC,
   SQ_SISTEMA ASC
);

/*==============================================================*/
/* Table: DC_INDICE_COLS                                        */
/*==============================================================*/
create table DC_INDICE_COLS  (
   SQ_INDICE            NUMBER(18)                      not null,
   SQ_COLUNA            NUMBER(18)                      not null,
   ORDEM                NUMBER(18)                      not null,
   ORDENACAO            VARCHAR2(1)                    default 'D' not null
      constraint CKC_ORDENACAO_DC_INDIC check (ORDENACAO in ('A','D')),
   constraint PK_DC_INDICE_COLS primary key (SQ_INDICE, SQ_COLUNA)
);

comment on table DC_INDICE_COLS is
'Armazena as colunas de um índice.';

comment on column DC_INDICE_COLS.SQ_INDICE is
'Chave de DC_INDICE. Indica a que índice o registro está ligado.';

comment on column DC_INDICE_COLS.SQ_COLUNA is
'Chave de DC_COLUNA. Indica a que coluna da tabela refere-se este índice.';

comment on column DC_INDICE_COLS.ORDEM is
'Número de ordem da coluna no índice.';

comment on column DC_INDICE_COLS.ORDENACAO is
'Modo de ordenação da coluna (A - ascendente; D - descendente).';

/*==============================================================*/
/* Index: IN_DCINDCOL_INVERSA                                   */
/*==============================================================*/
create index IN_DCINDCOL_INVERSA on DC_INDICE_COLS (
   SQ_COLUNA ASC,
   SQ_INDICE ASC
);

/*==============================================================*/
/* Table: DC_INDICE_TIPO                                        */
/*==============================================================*/
create table DC_INDICE_TIPO  (
   SQ_INDICE_TIPO       NUMBER(18)                      not null,
   NOME                 VARCHAR2(30)                    not null,
   DESCRICAO            VARCHAR2(4000)                  not null,
   constraint PK_DC_INDICE_TIPO primary key (SQ_INDICE_TIPO)
);

comment on table DC_INDICE_TIPO is
'Armazena os tipos possíveis de índice.';

comment on column DC_INDICE_TIPO.SQ_INDICE_TIPO is
'Chave de DC_INDICE_TIPO.';

comment on column DC_INDICE_TIPO.NOME is
'Nome do tipo de índice.';

comment on column DC_INDICE_TIPO.DESCRICAO is
'Descrição do tipo de índice.';

/*==============================================================*/
/* Table: DC_OCORRENCIA                                         */
/*==============================================================*/
create table DC_OCORRENCIA  (
   SQ_OCORRENCIA        NUMBER(18)                      not null,
   SQ_ESQUEMA           NUMBER(18)                      not null,
   SQ_PESSOA            NUMBER(18)                      not null,
   DATA_OCORRENCIA      DATE                           default sysdate not null,
   DATA_REFERENCIA      DATE                           default sysdate not null,
   PROCESSADOS          NUMBER(18)                      not null,
   REJEITADOS           NUMBER(18)                      not null,
   ARQUIVO_PROCESSAMENTO NUMBER(18)                      not null,
   ARQUIVO_REJEICAO     NUMBER(18),
   constraint PK_DC_OCORRENCIA primary key (SQ_OCORRENCIA)
);

comment on table DC_OCORRENCIA is
'Registra as ocorrências de importação ou exportação.';

comment on column DC_OCORRENCIA.SQ_OCORRENCIA is
'Chave de DC_OCORRENCIA.';

comment on column DC_OCORRENCIA.SQ_ESQUEMA is
'Chave de DC_ESQUEMA. Indica a que esquema a ocorrência refere-se.';

comment on column DC_OCORRENCIA.SQ_PESSOA is
'Chave de CO_PESSOA. Indica o usuário responsável pela ocorrência.';

comment on column DC_OCORRENCIA.DATA_OCORRENCIA is
'Data de processamento da importação ou exportação.';

comment on column DC_OCORRENCIA.DATA_REFERENCIA is
'Data de referência dos dados importados ou exportados.';

comment on column DC_OCORRENCIA.PROCESSADOS is
'Quantidade de registros processados.';

comment on column DC_OCORRENCIA.REJEITADOS is
'Quantidade de registros rejeitados.';

comment on column DC_OCORRENCIA.ARQUIVO_PROCESSAMENTO is
'Chave de SIW_ARQUIVO, que contém os dados do arquivo processado.';

comment on column DC_OCORRENCIA.ARQUIVO_REJEICAO is
'Chave de SIW_ARQUIVO, que contém dados do arquivo com os registros rejeitados no processamento.';

/*==============================================================*/
/* Index: IN_DCOCO_DATA                                         */
/*==============================================================*/
create index IN_DCOCO_DATA on DC_OCORRENCIA (
   DATA_OCORRENCIA ASC,
   SQ_ESQUEMA ASC,
   SQ_OCORRENCIA ASC
);

/*==============================================================*/
/* Table: DC_PROCEDURE                                          */
/*==============================================================*/
create table DC_PROCEDURE  (
   SQ_PROCEDURE         NUMBER(18)                      not null,
   SQ_ARQUIVO           NUMBER(18)                      not null,
   SQ_SISTEMA           NUMBER(18)                      not null,
   SQ_SP_TIPO           NUMBER(18)                      not null,
   NOME                 VARCHAR2(30)                    not null,
   DESCRICAO            VARCHAR2(4000)                  not null,
   constraint PK_DC_PROCEDURE primary key (SQ_PROCEDURE)
);

comment on table DC_PROCEDURE is
'Armazena as rotinas e funções da aplicação.';

comment on column DC_PROCEDURE.SQ_PROCEDURE is
'Chave de DC_PROCEDURE.';

comment on column DC_PROCEDURE.SQ_ARQUIVO is
'Chave de SIW_ARQUIVO. Indica a que arquivo o registro está ligado.';

comment on column DC_PROCEDURE.SQ_SISTEMA is
'Chave de DC_SISTEMA. Indica a que sistema o registro está ligado.';

comment on column DC_PROCEDURE.SQ_SP_TIPO is
'Chave de DC_SP_TIPO. Indica a que tipo de SP o registro está ligado.';

comment on column DC_PROCEDURE.NOME is
'Nome da procedure.';

comment on column DC_PROCEDURE.DESCRICAO is
'Descrição da rotina.';

/*==============================================================*/
/* Index: IN_DCPRO_SISTEMA                                      */
/*==============================================================*/
create index IN_DCPRO_SISTEMA on DC_PROCEDURE (
   SQ_SISTEMA ASC,
   SQ_PROCEDURE ASC
);

/*==============================================================*/
/* Index: IN_DCPRO_ARQUIVO                                      */
/*==============================================================*/
create index IN_DCPRO_ARQUIVO on DC_PROCEDURE (
   SQ_ARQUIVO ASC,
   SQ_PROCEDURE ASC
);

/*==============================================================*/
/* Index: IN_DCPRO_TIPO                                         */
/*==============================================================*/
create index IN_DCPRO_TIPO on DC_PROCEDURE (
   SQ_SP_TIPO ASC,
   SQ_PROCEDURE ASC
);

/*==============================================================*/
/* Index: IN_DCPRO_NOME                                         */
/*==============================================================*/
create index IN_DCPRO_NOME on DC_PROCEDURE (
   NOME ASC,
   SQ_PROCEDURE ASC
);

/*==============================================================*/
/* Table: DC_PROC_PARAM                                         */
/*==============================================================*/
create table DC_PROC_PARAM  (
   SQ_PARAM             NUMBER(18)                      not null,
   SQ_PROCEDURE         NUMBER(18)                      not null,
   SQ_DADO_TIPO         NUMBER(18)                      not null,
   NOME                 VARCHAR2(30)                    not null,
   DESCRICAO            VARCHAR2(4000)                  not null,
   TIPO                 VARCHAR2(1)                    default 'E' not null
      constraint CKC_TIPO_DC_PROC_ check (TIPO in ('E','S','A')),
   ORDEM                NUMBER(18)                      not null,
   constraint PK_DC_PROC_PARAM primary key (SQ_PARAM)
);

comment on table DC_PROC_PARAM is
'Armazena os parâmetros de procedures.';

comment on column DC_PROC_PARAM.SQ_PARAM is
'Chave de DC_PROC_PARAM.';

comment on column DC_PROC_PARAM.SQ_PROCEDURE is
'Chave de DC_PROCEDURE. Indica a que procedure o registro está ligado.';

comment on column DC_PROC_PARAM.SQ_DADO_TIPO is
'Chave de DC_DADO_TIPO. Indica a que tipo de dado o registro está ligado.';

comment on column DC_PROC_PARAM.NOME is
'Nome do parâmetro.';

comment on column DC_PROC_PARAM.DESCRICAO is
'Descrição do parâmetro.';

comment on column DC_PROC_PARAM.TIPO is
'Tipo do parâmetro (E - entrada; S - saída; A - ambos)';

comment on column DC_PROC_PARAM.ORDEM is
'Número de ordem do parâmetro.';

/*==============================================================*/
/* Index: IN_DCPROPAR_PROCEDURE                                 */
/*==============================================================*/
create index IN_DCPROPAR_PROCEDURE on DC_PROC_PARAM (
   SQ_PROCEDURE ASC,
   SQ_PARAM ASC
);

/*==============================================================*/
/* Index: IN_DCPROPAR_NOME                                      */
/*==============================================================*/
create index IN_DCPROPAR_NOME on DC_PROC_PARAM (
   NOME ASC,
   SQ_PROCEDURE ASC
);

/*==============================================================*/
/* Table: DC_PROC_SP                                            */
/*==============================================================*/
create table DC_PROC_SP  (
   SQ_PROCEDURE         NUMBER(18)                      not null,
   SQ_STORED_PROC       NUMBER(18)                      not null,
   constraint PK_DC_PROC_SP primary key (SQ_PROCEDURE, SQ_STORED_PROC)
);

comment on table DC_PROC_SP is
'Armazena as storage procedures chamadas por uma função ou rotina do sistema.';

comment on column DC_PROC_SP.SQ_PROCEDURE is
'Chave de DC_PROCEDURE. Indica a que procedure o registro está ligado.';

comment on column DC_PROC_SP.SQ_STORED_PROC is
'Chave de DC_STORED_PROC. Indica a que SP o registro está lgado.';

/*==============================================================*/
/* Index: IN_DCPROSP_INVERSA                                    */
/*==============================================================*/
create index IN_DCPROSP_INVERSA on DC_PROC_SP (
   SQ_STORED_PROC ASC,
   SQ_PROCEDURE ASC
);

/*==============================================================*/
/* Table: DC_PROC_TABELA                                        */
/*==============================================================*/
create table DC_PROC_TABELA  (
   SQ_PROCEDURE         NUMBER(18)                      not null,
   SQ_TABELA            NUMBER(18)                      not null,
   DESCRICAO            VARCHAR2(4000)                  not null,
   constraint PK_DC_PROC_TABELA primary key (SQ_PROCEDURE, SQ_TABELA)
);

comment on table DC_PROC_TABELA is
'Armazena as tabelas referenciadas por uma função ou procedure do sistema.';

comment on column DC_PROC_TABELA.SQ_PROCEDURE is
'Chave de DC_PROCEDURE. Indica a que procedure o registro está ligado.';

comment on column DC_PROC_TABELA.SQ_TABELA is
'Chave de DC_TABELA. Indica a que tabela o registro está ligado.';

comment on column DC_PROC_TABELA.DESCRICAO is
'Descrição das operações que a função ou rotina executa sobre a tabela.';

/*==============================================================*/
/* Index: IN_DCPROTAB_INVERSA                                   */
/*==============================================================*/
create index IN_DCPROTAB_INVERSA on DC_PROC_TABELA (
   SQ_TABELA ASC,
   SQ_PROCEDURE ASC
);

/*==============================================================*/
/* Table: DC_RELACIONAMENTO                                     */
/*==============================================================*/
create table DC_RELACIONAMENTO  (
   SQ_RELACIONAMENTO    NUMBER(18)                      not null,
   NOME                 VARCHAR2(30)                    not null,
   DESCRICAO            VARCHAR2(4000)                  not null,
   TABELA_PAI           NUMBER(18)                      not null,
   TABELA_FILHA         NUMBER(18)                      not null,
   SQ_SISTEMA           NUMBER(18)                      not null,
   constraint PK_DC_RELACIONAMENTO primary key (SQ_RELACIONAMENTO)
);

comment on column DC_RELACIONAMENTO.SQ_RELACIONAMENTO is
'Chave de DC_RELACIONAMENTO.';

comment on column DC_RELACIONAMENTO.NOME is
'Nome do relacionamento.';

comment on column DC_RELACIONAMENTO.DESCRICAO is
'Descrição  do relacionamento.';

comment on column DC_RELACIONAMENTO.TABELA_PAI is
'Sequence';

comment on column DC_RELACIONAMENTO.TABELA_FILHA is
'Sequence';

comment on column DC_RELACIONAMENTO.SQ_SISTEMA is
'Chave de DC_SISTEMA. Indica a que sistema o registro está ligado.';

/*==============================================================*/
/* Index: IN_DCREL_PAI                                          */
/*==============================================================*/
create index IN_DCREL_PAI on DC_RELACIONAMENTO (
   TABELA_PAI ASC,
   SQ_RELACIONAMENTO ASC
);

/*==============================================================*/
/* Index: IN_DCREL_FILHA                                        */
/*==============================================================*/
create index IN_DCREL_FILHA on DC_RELACIONAMENTO (
   TABELA_FILHA ASC,
   SQ_RELACIONAMENTO ASC
);

/*==============================================================*/
/* Index: IN_DCREL_NOME                                         */
/*==============================================================*/
create unique index IN_DCREL_NOME on DC_RELACIONAMENTO (
   NOME ASC,
   SQ_SISTEMA ASC
);

/*==============================================================*/
/* Index: IN_DCREL_SISTEMA                                      */
/*==============================================================*/
create index IN_DCREL_SISTEMA on DC_RELACIONAMENTO (
   SQ_SISTEMA ASC,
   SQ_RELACIONAMENTO ASC
);

/*==============================================================*/
/* Table: DC_RELAC_COLS                                         */
/*==============================================================*/
create table DC_RELAC_COLS  (
   SQ_RELACIONAMENTO    NUMBER(18)                      not null,
   COLUNA_PAI           NUMBER(18)                      not null,
   COLUNA_FILHA         NUMBER(18)                      not null,
   constraint PK_DC_RELAC_COLS primary key (SQ_RELACIONAMENTO, COLUNA_PAI, COLUNA_FILHA)
);

comment on table DC_RELAC_COLS is
'Armazena as colunas de ligação.';

comment on column DC_RELAC_COLS.SQ_RELACIONAMENTO is
'Chave de DC_RELACIONAMENTO. Indica a que relacionamento o registro está ligado.';

comment on column DC_RELAC_COLS.COLUNA_PAI is
'Sequence.';

comment on column DC_RELAC_COLS.COLUNA_FILHA is
'Sequence.';

/*==============================================================*/
/* Index: IN_DCRELCOL_PAI                                       */
/*==============================================================*/
create index IN_DCRELCOL_PAI on DC_RELAC_COLS (
   COLUNA_PAI ASC,
   SQ_RELACIONAMENTO ASC
);

/*==============================================================*/
/* Index: IN_DCRELCOL_FILHA                                     */
/*==============================================================*/
create index IN_DCRELCOL_FILHA on DC_RELAC_COLS (
   COLUNA_FILHA ASC,
   SQ_RELACIONAMENTO ASC
);

/*==============================================================*/
/* Table: DC_SISTEMA                                            */
/*==============================================================*/
create table DC_SISTEMA  (
   SQ_SISTEMA           NUMBER(18)                      not null,
   CLIENTE              NUMBER(18)                      not null,
   NOME                 VARCHAR2(31)                    not null,
   SIGLA                VARCHAR2(10)                    not null,
   DESCRICAO            VARCHAR2(4000)                  not null,
   constraint PK_DC_SISTEMA primary key (SQ_SISTEMA)
);

comment on table DC_SISTEMA is
'Armazena os dados do sistema.';

comment on column DC_SISTEMA.SQ_SISTEMA is
'Chave de DC_SISTEMA.';

comment on column DC_SISTEMA.CLIENTE is
'Chave de CO_PESSOA. Indica a que cliente o registro está ligado.';

comment on column DC_SISTEMA.NOME is
'Nome do sistema.';

comment on column DC_SISTEMA.SIGLA is
'Sigla do sistema.';

comment on column DC_SISTEMA.DESCRICAO is
'Descrição do sistema: finalidade, objetivos, características etc.';

/*==============================================================*/
/* Index: IN_DCSIS_CLIENTE                                      */
/*==============================================================*/
create index IN_DCSIS_CLIENTE on DC_SISTEMA (
   CLIENTE ASC,
   SQ_SISTEMA ASC
);

/*==============================================================*/
/* Table: DC_SP_PARAM                                           */
/*==============================================================*/
create table DC_SP_PARAM  (
   SQ_SP_PARAM          NUMBER(18)                      not null,
   SQ_STORED_PROC       NUMBER(18)                      not null,
   SQ_DADO_TIPO         NUMBER(18)                      not null,
   NOME                 VARCHAR2(30)                    not null,
   DESCRICAO            VARCHAR2(4000)                  not null,
   TIPO                 VARCHAR2(1)                    default 'E' not null
      constraint CKC_TIPO_DC_SP_PA check (TIPO in ('E','S','A')),
   ORDEM                NUMBER(18)                      not null,
   TAMANHO              NUMBER(18)                     default 0 not null,
   PRECISAO             NUMBER(18),
   ESCALA               NUMBER(18),
   OBRIGATORIO          VARCHAR2(1)                    default 'N' not null
      constraint CKC_OBRIGATORIO_DC_SP_PA check (OBRIGATORIO in ('S','N') and OBRIGATORIO = upper(OBRIGATORIO)),
   VALOR_PADRAO         VARCHAR2(255),
   constraint PK_DC_SP_PARAM primary key (SQ_SP_PARAM)
);

comment on table DC_SP_PARAM is
'Armazena os parâmetros da stored procedure.';

comment on column DC_SP_PARAM.SQ_SP_PARAM is
'Chave de DC_SP_PARAM.';

comment on column DC_SP_PARAM.SQ_STORED_PROC is
'Chave de DC_STORED_PROC. Indica a que SP o registro está lgado.';

comment on column DC_SP_PARAM.SQ_DADO_TIPO is
'Chave de DC_DADO_TIPO. Indica a que tipo de dado o registro está ligado.';

comment on column DC_SP_PARAM.NOME is
'Nome do parâmetro.';

comment on column DC_SP_PARAM.DESCRICAO is
'Descrição do parâmetro.';

comment on column DC_SP_PARAM.TIPO is
'Tipo do parâmetro (E - entrada; S - saída; A - ambos)';

comment on column DC_SP_PARAM.ORDEM is
'Número de ordem do parâmetro.';

comment on column DC_SP_PARAM.TAMANHO is
'Tamanho do parâmetro, em bytes.';

comment on column DC_SP_PARAM.PRECISAO is
'Número de casas decimais quando for o parâmetro for do tipo numérico';

comment on column DC_SP_PARAM.ESCALA is
'Número de dígitos à direita da vírgula decimal, quando o parâmetro for do tipo numérico.';

comment on column DC_SP_PARAM.OBRIGATORIO is
'Indica se o parâmetro é obrigqatório.';

comment on column DC_SP_PARAM.VALOR_PADRAO is
'Valor do parâmetro, caso não seja especificado um.';

/*==============================================================*/
/* Index: IN_DCSPPAR_SP                                         */
/*==============================================================*/
create index IN_DCSPPAR_SP on DC_SP_PARAM (
   SQ_STORED_PROC ASC,
   SQ_SP_PARAM ASC
);

/*==============================================================*/
/* Index: IN_DCSPPAR_NOME                                       */
/*==============================================================*/
create index IN_DCSPPAR_NOME on DC_SP_PARAM (
   NOME ASC,
   SQ_STORED_PROC ASC
);

/*==============================================================*/
/* Table: DC_SP_SP                                              */
/*==============================================================*/
create table DC_SP_SP  (
   SP_PAI               NUMBER(18)                      not null,
   SP_FILHA             NUMBER(18)                      not null,
   constraint PK_DC_SP_SP primary key (SP_PAI, SP_FILHA)
);

comment on table DC_SP_SP is
'Armazena as stored procedures chamadas por outras stored procedures.';

comment on column DC_SP_SP.SP_PAI is
'Chave de DC_STORED_PROC. Indica a SP que é referenciada pela SP filha.';

comment on column DC_SP_SP.SP_FILHA is
'Chave de DC_STORED_PROC. Indica a SP que referencia a SP pai.';

/*==============================================================*/
/* Index: IN_DCSPSP_INVERSA                                     */
/*==============================================================*/
create index IN_DCSPSP_INVERSA on DC_SP_SP (
   SP_FILHA ASC,
   SP_PAI ASC
);

/*==============================================================*/
/* Table: DC_SP_TABS                                            */
/*==============================================================*/
create table DC_SP_TABS  (
   SQ_STORED_PROC       NUMBER(18)                      not null,
   SQ_TABELA            NUMBER(18)                      not null,
   constraint PK_DC_SP_TABS primary key (SQ_STORED_PROC, SQ_TABELA)
);

comment on table DC_SP_TABS is
'Armazena as tabelas referenciadas por uma stored procedure.';

comment on column DC_SP_TABS.SQ_STORED_PROC is
'Chave de DC_STORED_PROC. Indica a que SP o registro está lgado.';

comment on column DC_SP_TABS.SQ_TABELA is
'Chave de DC_TABELA. Indica a que tabela o registro está ligado.';

/*==============================================================*/
/* Index: IN_DCSPTAB_INVERSA                                    */
/*==============================================================*/
create index IN_DCSPTAB_INVERSA on DC_SP_TABS (
   SQ_TABELA ASC,
   SQ_STORED_PROC ASC
);

/*==============================================================*/
/* Table: DC_SP_TIPO                                            */
/*==============================================================*/
create table DC_SP_TIPO  (
   SQ_SP_TIPO           NUMBER(18)                      not null,
   NOME                 VARCHAR2(30)                    not null,
   DESCRICAO            VARCHAR2(4000)                  not null,
   constraint PK_DC_SP_TIPO primary key (SQ_SP_TIPO)
);

comment on table DC_SP_TIPO is
'Armazena os tipos possíveis de stored procedures.';

comment on column DC_SP_TIPO.SQ_SP_TIPO is
'Chave de DC_SP_TIPO.';

comment on column DC_SP_TIPO.NOME is
'Nome da stored procedure.';

comment on column DC_SP_TIPO.DESCRICAO is
'Descrição do tipo de stored procedure.';

/*==============================================================*/
/* Index: IN_DCSPTIP_NOME                                       */
/*==============================================================*/
create unique index IN_DCSPTIP_NOME on DC_SP_TIPO (
   NOME ASC
);

/*==============================================================*/
/* Table: DC_STORED_PROC                                        */
/*==============================================================*/
create table DC_STORED_PROC  (
   SQ_STORED_PROC       NUMBER(18)                      not null,
   SQ_SP_TIPO           NUMBER(18)                      not null,
   SQ_USUARIO           NUMBER(18)                      not null,
   SQ_SISTEMA           NUMBER(18)                      not null,
   NOME                 VARCHAR2(30)                    not null,
   DESCRICAO            VARCHAR2(4000)                  not null,
   constraint PK_DC_STORED_PROC primary key (SQ_STORED_PROC)
);

comment on table DC_STORED_PROC is
'Armazena dados das stored procedures do sistema.';

comment on column DC_STORED_PROC.SQ_STORED_PROC is
'Chave de DC_STORED_PROC.';

comment on column DC_STORED_PROC.SQ_SP_TIPO is
'Chave de DC_SP_TIPO. Indica a que tipo de SP o registro está ligado.';

comment on column DC_STORED_PROC.SQ_USUARIO is
'Chave de DC_USUARIO. Indica a que usuário o do banco de dados o registro está ligado.';

comment on column DC_STORED_PROC.SQ_SISTEMA is
'Chave de DC_SISTEMA. Indica a que sistema o registro está ligado.';

comment on column DC_STORED_PROC.NOME is
'Nome da stored procedure.';

comment on column DC_STORED_PROC.DESCRICAO is
'Descrição da stored procedure.';

/*==============================================================*/
/* Index: IN_DCSTOPRO_TIPO                                      */
/*==============================================================*/
create index IN_DCSTOPRO_TIPO on DC_STORED_PROC (
   SQ_SP_TIPO ASC,
   SQ_STORED_PROC ASC
);

/*==============================================================*/
/* Index: IN_DCSTOPRO_SISTEMA                                   */
/*==============================================================*/
create index IN_DCSTOPRO_SISTEMA on DC_STORED_PROC (
   SQ_SISTEMA ASC,
   SQ_STORED_PROC ASC
);

/*==============================================================*/
/* Index: IN_DCSTOPRO_NOME                                      */
/*==============================================================*/
create unique index IN_DCSTOPRO_NOME on DC_STORED_PROC (
   NOME ASC,
   SQ_USUARIO ASC,
   SQ_SISTEMA ASC
);

/*==============================================================*/
/* Table: DC_TABELA                                             */
/*==============================================================*/
create table DC_TABELA  (
   SQ_TABELA            NUMBER(18)                      not null,
   SQ_TABELA_TIPO       NUMBER(18)                      not null,
   SQ_USUARIO           NUMBER(18)                      not null,
   SQ_SISTEMA           NUMBER(18)                      not null,
   NOME                 VARCHAR2(30)                    not null,
   DESCRICAO            VARCHAR2(4000)                  not null,
   constraint PK_DC_TABELA primary key (SQ_TABELA)
);

comment on table DC_TABELA is
'Armazena dados das tabelas do sistema.';

comment on column DC_TABELA.SQ_TABELA is
'Chave de DC_TABELA.';

comment on column DC_TABELA.SQ_TABELA_TIPO is
'Chave de DC_TABELA_TIPO. Indica a que tipo de tabela o registro está ligado.';

comment on column DC_TABELA.SQ_USUARIO is
'Chave de DC_USUARIO. Indica a que usuário o do banco de dados o registro está ligado.';

comment on column DC_TABELA.SQ_SISTEMA is
'Chave de DC_SISTEMA. Indica a que sistema o registro está ligado.';

comment on column DC_TABELA.NOME is
'Nome da tabela.';

comment on column DC_TABELA.DESCRICAO is
'Descrição da tabela: finalidade, objetivos, tipos de dados armazenados etc.';

/*==============================================================*/
/* Index: IN_DCTAB_SISTEMA                                      */
/*==============================================================*/
create index IN_DCTAB_SISTEMA on DC_TABELA (
   SQ_SISTEMA ASC,
   SQ_TABELA ASC
);

/*==============================================================*/
/* Index: IN_DCTAB_TIPO                                         */
/*==============================================================*/
create index IN_DCTAB_TIPO on DC_TABELA (
   SQ_TABELA_TIPO ASC,
   SQ_TABELA ASC
);

/*==============================================================*/
/* Index: IN_DCTAB_NOME                                         */
/*==============================================================*/
create unique index IN_DCTAB_NOME on DC_TABELA (
   NOME ASC,
   SQ_USUARIO ASC,
   SQ_SISTEMA ASC
);

/*==============================================================*/
/* Table: DC_TABELA_TIPO                                        */
/*==============================================================*/
create table DC_TABELA_TIPO  (
   SQ_TABELA_TIPO       NUMBER(18)                      not null,
   NOME                 VARCHAR2(30)                    not null,
   DESCRICAO            VARCHAR2(4000)                  not null,
   constraint PK_DC_TABELA_TIPO primary key (SQ_TABELA_TIPO)
);

comment on table DC_TABELA_TIPO is
'Armazena os tipos possíveis de tabelas (física, materializada, view etc.).';

comment on column DC_TABELA_TIPO.SQ_TABELA_TIPO is
'Chave de DC_TABELA_TIPO.';

comment on column DC_TABELA_TIPO.NOME is
'Nome do tipo de tabela.';

comment on column DC_TABELA_TIPO.DESCRICAO is
'Descrição do tipo de tabela.';

/*==============================================================*/
/* Index: IN_DCTABTIP_NOME                                      */
/*==============================================================*/
create unique index IN_DCTABTIP_NOME on DC_TABELA_TIPO (
   NOME ASC
);

/*==============================================================*/
/* Table: DC_TRIGGER                                            */
/*==============================================================*/
create table DC_TRIGGER  (
   SQ_TRIGGER           NUMBER(18)                      not null,
   SQ_TABELA            NUMBER(18)                      not null,
   SQ_USUARIO           NUMBER(18)                      not null,
   SQ_SISTEMA           NUMBER(18)                      not null,
   NOME                 VARCHAR2(30)                    not null,
   DESCRICAO            VARCHAR2(4000)                  not null,
   constraint PK_DC_TRIGGER primary key (SQ_TRIGGER)
);

comment on table DC_TRIGGER is
'Armazena as triggers do sistema.';

comment on column DC_TRIGGER.SQ_TRIGGER is
'Chave de DC_TRIGGER.';

comment on column DC_TRIGGER.SQ_TABELA is
'Chave de DC_TABELA. Indica a que tabela o registro está ligado.';

comment on column DC_TRIGGER.SQ_USUARIO is
'Chave de DC_USUARIO. Indica a que usuário o do banco de dados o registro está ligado.';

comment on column DC_TRIGGER.SQ_SISTEMA is
'Chave de DC_SISTEMA. Indica a que sistema o registro está ligado.';

comment on column DC_TRIGGER.NOME is
'Nome da trigger.';

comment on column DC_TRIGGER.DESCRICAO is
'Descrição da trigger: finalidade, objetivos etc.';

/*==============================================================*/
/* Index: IN_DCTRI_TABELA                                       */
/*==============================================================*/
create index IN_DCTRI_TABELA on DC_TRIGGER (
   SQ_TABELA ASC,
   SQ_TRIGGER ASC
);

/*==============================================================*/
/* Index: IN_DCTRI_NOME                                         */
/*==============================================================*/
create unique index IN_DCTRI_NOME on DC_TRIGGER (
   NOME ASC,
   SQ_SISTEMA ASC
);

/*==============================================================*/
/* Index: IN_DCTRI_SISTEMA                                      */
/*==============================================================*/
create index IN_DCTRI_SISTEMA on DC_TRIGGER (
   SQ_SISTEMA ASC,
   SQ_TRIGGER ASC
);

/*==============================================================*/
/* Table: DC_TRIGGER_EVENTO                                     */
/*==============================================================*/
create table DC_TRIGGER_EVENTO  (
   SQ_TRIGGER           NUMBER(18)                      not null,
   SQ_EVENTO            NUMBER(18)                      not null,
   constraint PK_DC_TRIGGER_EVENTO primary key (SQ_TRIGGER, SQ_EVENTO)
);

comment on table DC_TRIGGER_EVENTO is
'Armazena os eventos que disparam uma trigger.';

comment on column DC_TRIGGER_EVENTO.SQ_TRIGGER is
'Chave de DC_TRIGGER. Indica a que trigger o evento está ligado.';

comment on column DC_TRIGGER_EVENTO.SQ_EVENTO is
'Chave de DC_EVENTO. Indica a que evento o registro está ligado.';

/*==============================================================*/
/* Table: DC_USUARIO                                            */
/*==============================================================*/
create table DC_USUARIO  (
   SQ_USUARIO           NUMBER(18)                      not null,
   SQ_SISTEMA           NUMBER(18)                      not null,
   NOME                 VARCHAR2(30)                    not null,
   DESCRICAO            VARCHAR2(4000)                  not null,
   constraint PK_DC_USUARIO primary key (SQ_USUARIO)
);

comment on table DC_USUARIO is
'Armazena usuários do sistema.';

comment on column DC_USUARIO.SQ_USUARIO is
'Chave de DC_USUARIO.';

comment on column DC_USUARIO.SQ_SISTEMA is
'Chave de DC_SISTEMA. Indica a que sistema o registro está ligado.';

comment on column DC_USUARIO.NOME is
'Nome do usuário.';

comment on column DC_USUARIO.DESCRICAO is
'Descrição do usuário: finalidade, objetos por ele armazenados etc.';

/*==============================================================*/
/* Index: IN_DCUSU_SISTEMA                                      */
/*==============================================================*/
create index IN_DCUSU_SISTEMA on DC_USUARIO (
   SQ_SISTEMA ASC,
   SQ_USUARIO ASC
);

/*==============================================================*/
/* Index: IN_DCUSU_NOME                                         */
/*==============================================================*/
create unique index IN_DCUSU_NOME on DC_USUARIO (
   NOME ASC,
   SQ_SISTEMA ASC
);

/*==============================================================*/
/* Table: DM_SEGMENTO_MENU                                      */
/*==============================================================*/
create table DM_SEGMENTO_MENU  (
   SQ_SEGMENTO_MENU     NUMBER(18)                      not null,
   SQ_MODULO            NUMBER(18)                      not null,
   SQ_SEGMENTO          NUMBER(18)                      not null,
   SQ_SEG_MENU_PAI      NUMBER(18),
   NOME                 VARCHAR2(40)                    not null,
   FINALIDADE           VARCHAR2(200)                  default 'A ser inserido.' not null,
   LINK                 VARCHAR2(60),
   SQ_UNID_EXECUTORA    number(10),
   TRAMITE              VARCHAR2(1)                    default 'N' not null
      constraint CKC_DMSEGMEN_TRAM
               check (TRAMITE in ('S','N') and TRAMITE = upper(TRAMITE)),
   ORDEM                NUMBER(4)                       not null,
   ULTIMO_NIVEL         VARCHAR2(1)                    default 'N' not null
      constraint CKC_DMSEGMEN_ULT
               check (ULTIMO_NIVEL in ('S','N') and ULTIMO_NIVEL = upper(ULTIMO_NIVEL)),
   P1                   NUMBER(18),
   P2                   NUMBER(18),
   P3                   NUMBER(18),
   P4                   NUMBER(18),
   SIGLA                VARCHAR2(10),
   IMAGEM               VARCHAR2(60),
   ACESSO_GERAL         VARCHAR2(1)                    default 'N' not null
      constraint CKC_DMSEGMEN_ACGER
               check (ACESSO_GERAL in ('S','N') and ACESSO_GERAL = upper(ACESSO_GERAL)),
   DESCENTRALIZADO      VARCHAR2(1)                    default 'S' not null
      constraint CKC_DMSEGMEN_DESC
               check (DESCENTRALIZADO in ('S','N') and DESCENTRALIZADO = upper(DESCENTRALIZADO)),
   EXTERNO              VARCHAR2(1)                    default 'N' not null
      constraint CKC_DMSEGMEN_EXT
               check (EXTERNO in ('S','N') and EXTERNO = upper(EXTERNO)),
   TARGET               VARCHAR2(15),
   EMITE_OS             VARCHAR2(1)                    
      constraint CKC_DMSEGMEN_OS
               check (EMITE_OS is null or (EMITE_OS in ('S','N') and EMITE_OS = upper(EMITE_OS))),
   CONSULTA_OPINIAO     VARCHAR2(1)                    
      constraint CKC_DMSEGMEN_OPI
               check (CONSULTA_OPINIAO is null or (CONSULTA_OPINIAO in ('S','N') and CONSULTA_OPINIAO = upper(CONSULTA_OPINIAO))),
   ENVIA_EMAIL          VARCHAR2(1)                    
      constraint CKC_DMSEGMEN_MAIL
               check (ENVIA_EMAIL is null or (ENVIA_EMAIL in ('S','N') and ENVIA_EMAIL = upper(ENVIA_EMAIL))),
   EXIBE_RELATORIO      VARCHAR2(1)                    
      constraint CKC_DMSEGMEN_REL
               check (EXIBE_RELATORIO is null or (EXIBE_RELATORIO in ('S','N') and EXIBE_RELATORIO = upper(EXIBE_RELATORIO))),
   COMO_FUNCIONA        VARCHAR2(1000),
   ARQUIVO_PROCED       VARCHAR2(60),
   VINCULACAO           VARCHAR2(1)                    
      constraint CKC_DMSEGMEN_VIN
               check (VINCULACAO is null or (VINCULACAO in ('P','U') and VINCULACAO = upper(VINCULACAO))),
   DATA_HORA            VARCHAR2(1)                    
      constraint CKC_DATA_HORA_DM_SEGME check (DATA_HORA is null or (DATA_HORA = upper(DATA_HORA))),
   ENVIA_DIA_UTIL       VARCHAR2(1)                    
      constraint CKC_DMSEGMEN_UTIL
               check (ENVIA_DIA_UTIL is null or (ENVIA_DIA_UTIL in ('S','N') and ENVIA_DIA_UTIL = upper(ENVIA_DIA_UTIL))),
   DESCRICAO            VARCHAR2(1)                    
      constraint CKC_DMSEGMEN_DESCR
               check (DESCRICAO is null or (DESCRICAO in ('S','N') and DESCRICAO = upper(DESCRICAO))),
   JUSTIFICATIVA        VARCHAR2(1)                    
      constraint CKC_DMSEGMEN_JUST
               check (JUSTIFICATIVA is null or (JUSTIFICATIVA in ('S','N') and JUSTIFICATIVA = upper(JUSTIFICATIVA))),
   ATIVO                VARCHAR2(1)                    default 'S' not null
      constraint CKC_ATIVO_DMSEGMEN check (ATIVO in ('S','N') and ATIVO = upper(ATIVO)),
   CONTROLA_ANO         VARCHAR2(1)                    default 'N' not null
      constraint CKC_CONTROLA_ANO_DM_SEGME check (CONTROLA_ANO in ('S','N') and CONTROLA_ANO = upper(CONTROLA_ANO)),
   LIBERA_EDICAO        VARCHAR2(1)                    default 'S' not null
      constraint CKC_LIBERA_EDICAO_DM_SEGME check (LIBERA_EDICAO in ('S','N') and LIBERA_EDICAO = upper(LIBERA_EDICAO)),
   constraint PK_DM_SEGMENTO_MENU primary key (SQ_SEGMENTO_MENU)
);

comment on table DM_SEGMENTO_MENU is
'Armazena as opções padrão do menu para um segmento';

comment on column DM_SEGMENTO_MENU.SQ_SEGMENTO_MENU is
'Chave de DM_SEGMENTO_MENU.';

comment on column DM_SEGMENTO_MENU.SQ_MODULO is
'Chave de SIW_MODULO. Indica a que módulo o registro está ligado.';

comment on column DM_SEGMENTO_MENU.SQ_SEGMENTO is
'Chave de CO_SEGMENTO. Indica a que segmento o registro está ligado.';

comment on column DM_SEGMENTO_MENU.SQ_SEG_MENU_PAI is
'Chave de DM_SEGMENTO_MENU. Se preenchido, informa a subordinação da opção.';

comment on column DM_SEGMENTO_MENU.NOME is
'Informa o texto a ser apresentado no menu.';

comment on column DM_SEGMENTO_MENU.FINALIDADE is
'Informa a finalidade da opção.';

comment on column DM_SEGMENTO_MENU.LINK is
'Informa o link a ser chamado quando a opção for clicada.';

comment on column DM_SEGMENTO_MENU.SQ_UNID_EXECUTORA is
'Chave de EO_UNIDADE. Unidade responsável pela execução do serviço.';

comment on column DM_SEGMENTO_MENU.TRAMITE is
'Indica se a opção deve ter controle de trâmites (work-flow).';

comment on column DM_SEGMENTO_MENU.ORDEM is
'Informa a ordem em que a opção deve ser apresentada, em relação a outras opções de mesma subordinação.';

comment on column DM_SEGMENTO_MENU.ULTIMO_NIVEL is
'Indica se a opção deve ser apresentada num sub-menu (S) ou na montagem do menu principal (N)';

comment on column DM_SEGMENTO_MENU.P1 is
'Parâmetro de uso geral pela aplicação.';

comment on column DM_SEGMENTO_MENU.P2 is
'Parâmetro de uso geral pela aplicação.';

comment on column DM_SEGMENTO_MENU.P3 is
'Parâmetro de uso geral pela aplicação.';

comment on column DM_SEGMENTO_MENU.P4 is
'Parâmetro de uso geral pela aplicação.';

comment on column DM_SEGMENTO_MENU.SIGLA is
'Informa a sigla da opção, usada para controle interno da aplicação.';

comment on column DM_SEGMENTO_MENU.IMAGEM is
'Informa qual ícone deve ser colocado ao lado da opção. Se for nulo, a imagem será a padrão.';

comment on column DM_SEGMENTO_MENU.ACESSO_GERAL is
'Indica que a opção deve ser acessada por todos os usuários.';

comment on column DM_SEGMENTO_MENU.DESCENTRALIZADO is
'Indica se a opção deve ser controlada por endereço.';

comment on column DM_SEGMENTO_MENU.EXTERNO is
'Indica se o link da opção aponta para um endereço externo ao sistema.';

comment on column DM_SEGMENTO_MENU.TARGET is
'Se preenchido, informa o nome da janela a ser aberta quando a opção for clicada.';

comment on column DM_SEGMENTO_MENU.EMITE_OS is
'Indica se o serviço terá emissão de ordem de serviço';

comment on column DM_SEGMENTO_MENU.CONSULTA_OPINIAO is
'Indica se o serviço deverá consultar a opinião do solicitante quanto ao atendimento';

comment on column DM_SEGMENTO_MENU.ENVIA_EMAIL is
'Indica se deve ser enviado e-mail para o solicitante a cada trâmite';

comment on column DM_SEGMENTO_MENU.EXIBE_RELATORIO is
'Indica se o serviço deve ser exibido no relatório gerencial';

comment on column DM_SEGMENTO_MENU.COMO_FUNCIONA is
'Texto de apresentação do serviço, inclusive com as regras de negócio a serem respeitadas.';

comment on column DM_SEGMENTO_MENU.ARQUIVO_PROCED is
'Arquivo que contém descrição dos procedimentos relacionados à opção.';

comment on column DM_SEGMENTO_MENU.VINCULACAO is
'Este campo determina se a solicitação do serviço é vinculada ao beneficiário ou à unidade solicitante. Se for ao beneficiário, outras pessoas da unidade, que não sejam titular ou substituto, não poderão vê-la. Além disso, se o beneficiário for para outra unidade, a solicitação deve ser vista pelos novos chefes. Se for à unidade, todos as pessoas da unidade poderão consultar a solicitação, mesmo que não sejam chefes. Mesmo que o solicitante vá para outra unidade, a solicitação é consultada pela unidade que cadastrou a solicitação.';

comment on column DM_SEGMENTO_MENU.DATA_HORA is
'Indica como o sistema deve tratar a questão de horas. (0) Não pede data; (1) Pede apenas uma data; (2) Pede apenas uma data/hora; (3) Pede data início e fim; (4) Pede data/hora início e fim.';

comment on column DM_SEGMENTO_MENU.ENVIA_DIA_UTIL is
'Indica se a solicitação só pode ser atendida em dia útil.';

comment on column DM_SEGMENTO_MENU.DESCRICAO is
'Indica se deve ser informada uma descrição na solicitação';

comment on column DM_SEGMENTO_MENU.JUSTIFICATIVA is
'Indica se deve ser informada uma justificativa na solicitação';

comment on column DM_SEGMENTO_MENU.ATIVO is
'Indica se o registro deve ou não ser exibido.';

comment on column DM_SEGMENTO_MENU.CONTROLA_ANO is
'Indica se a opção do menu deve ter seu controle por ano.';

comment on column DM_SEGMENTO_MENU.LIBERA_EDICAO is
'Indica se pode haver inclusão, alteração ou exclusão dos registros.';

/*==============================================================*/
/* Index: IN_DMSEGMEN_SIGLA                                     */
/*==============================================================*/
create index IN_DMSEGMEN_SIGLA on DM_SEGMENTO_MENU (
   SIGLA ASC,
   SQ_SEGMENTO ASC
);

/*==============================================================*/
/* Index: IN_DMSEGMEN_ULT                                       */
/*==============================================================*/
create unique index IN_DMSEGMEN_ULT on DM_SEGMENTO_MENU (
   ULTIMO_NIVEL ASC,
   SQ_SEGMENTO_MENU ASC
);

/*==============================================================*/
/* Index: IN_DMSEGMEN_ATIVO                                     */
/*==============================================================*/
create unique index IN_DMSEGMEN_ATIVO on DM_SEGMENTO_MENU (
   ATIVO ASC,
   SQ_SEGMENTO_MENU ASC
);

/*==============================================================*/
/* Index: IN_DMSEGMEN_PAI                                       */
/*==============================================================*/
create index IN_DMSEGMEN_PAI on DM_SEGMENTO_MENU (
   SQ_SEG_MENU_PAI ASC,
   SQ_SEGMENTO_MENU ASC
);

/*==============================================================*/
/* Index: IN_DMSEGMEN_SEG                                       */
/*==============================================================*/
create index IN_DMSEGMEN_SEG on DM_SEGMENTO_MENU (
   SQ_SEGMENTO ASC,
   SQ_MODULO ASC,
   SQ_SEGMENTO_MENU ASC
);

/*==============================================================*/
/* Table: DM_SEG_VINCULO                                        */
/*==============================================================*/
create table DM_SEG_VINCULO  (
   SQ_SEG_VINCULO       NUMBER(18)                      not null,
   SQ_SEGMENTO          NUMBER(18)                      not null,
   SQ_TIPO_PESSOA       NUMBER(18)                      not null,
   NOME                 VARCHAR2(21)                    not null,
   INTERNO              VARCHAR2(1)                    default 'N' not null
      constraint CKC_DMSEGVIN_INT
               check (INTERNO in ('S','N') and INTERNO = upper(INTERNO)),
   CONTRATADO           VARCHAR2(1)                    default 'N' not null
      constraint CKC_DMSEGVIN_CONT
               check (CONTRATADO in ('S','N') and CONTRATADO = upper(CONTRATADO)),
   ORDEM                NUMBER(6),
   ATIVO                VARCHAR2(1)                    default 'S' not null
      constraint CKC_ATIVO_DMSEG check (ATIVO in ('S','N') and ATIVO = upper(ATIVO)),
   PADRAO               VARCHAR2(1)                    default 'N' not null
      constraint CKC_DMSEGVIN_PAD
               check (PADRAO in ('S','N') and PADRAO = upper(PADRAO)),
   constraint PK_DM_SEG_VINCULO primary key (SQ_SEG_VINCULO)
);

comment on table DM_SEG_VINCULO is
'Armazena os vínculos padrão para o segmento da pessoa jurídica e tipo de pessoa';

comment on column DM_SEG_VINCULO.SQ_SEG_VINCULO is
'Chave de DM_SEG_VINCULO.';

comment on column DM_SEG_VINCULO.SQ_SEGMENTO is
'Chave de CO_SEGMENTO. Indica a que segmento o registro está ligado.';

comment on column DM_SEG_VINCULO.SQ_TIPO_PESSOA is
'Chave de CO_TIPO_PESSOA. Indica a que tipo de pessoa o registro está ligado.';

comment on column DM_SEG_VINCULO.NOME is
'Nome do vínculo.';

comment on column DM_SEG_VINCULO.INTERNO is
'Indica se o vínculo é interno à organização.';

comment on column DM_SEG_VINCULO.CONTRATADO is
'Indica se a pessoa é contratada ou não pela organização.';

comment on column DM_SEG_VINCULO.ORDEM is
'Indica a ordem do registro nas listagens.';

comment on column DM_SEG_VINCULO.ATIVO is
'Indica se o registro deve ou não ser exibido.';

comment on column DM_SEG_VINCULO.PADRAO is
'Indica se este registro deve ser apresentado como padrão para o usuário.';

/*==============================================================*/
/* Index: IN_DMSEGVIN_SEG                                       */
/*==============================================================*/
create index IN_DMSEGVIN_SEG on DM_SEG_VINCULO (
   SQ_SEGMENTO ASC,
   SQ_TIPO_PESSOA ASC
);

/*==============================================================*/
/* Table: EO_AREA_ATUACAO                                       */
/*==============================================================*/
create table EO_AREA_ATUACAO  (
   SQ_AREA_ATUACAO      NUMBER(10)                      not null,
   SQ_PESSOA            NUMBER(18)                      not null,
   NOME                 VARCHAR2(29)                    not null,
   ATIVO                VARCHAR2(1)                    default 'S' not null
      constraint CKC_ATIVO_EO_AREA_ check (ATIVO in ('S','N') and ATIVO = upper(ATIVO)),
   CODIGO_EXTERNO       VARCHAR2(60),
   constraint PK_EO_AREA_ATUACAO primary key (SQ_AREA_ATUACAO)
);

comment on table EO_AREA_ATUACAO is
'Armazena a tabela de áreas de atuação das unidades organizacionais';

comment on column EO_AREA_ATUACAO.SQ_AREA_ATUACAO is
'Chave de EO_AREA_ATUACAO.';

comment on column EO_AREA_ATUACAO.SQ_PESSOA is
'Chave de CO_PESSOA. Indica a que cliente o registro está ligado.';

comment on column EO_AREA_ATUACAO.NOME is
'Nome da área  de atuação.';

comment on column EO_AREA_ATUACAO.ATIVO is
'Indica se este registro está disponível para ligação a outras tabelas.';

comment on column EO_AREA_ATUACAO.CODIGO_EXTERNO is
'Código desse registro num sistema externo.';

/*==============================================================*/
/* Index: IN_EOAREATU_PESSOA                                    */
/*==============================================================*/
create index IN_EOAREATU_PESSOA on EO_AREA_ATUACAO (
   SQ_PESSOA ASC,
   SQ_AREA_ATUACAO ASC
);

/*==============================================================*/
/* Index: IN_EOAREATU_ATIVO                                     */
/*==============================================================*/
create index IN_EOAREATU_ATIVO on EO_AREA_ATUACAO (
   ATIVO ASC,
   SQ_AREA_ATUACAO ASC
);

/*==============================================================*/
/* Index: IN_EOAREATU_EXTERNO                                   */
/*==============================================================*/
create index IN_EOAREATU_EXTERNO on EO_AREA_ATUACAO (
   SQ_PESSOA ASC,
   CODIGO_EXTERNO ASC,
   SQ_AREA_ATUACAO ASC
);

/*==============================================================*/
/* Table: EO_DATA_ESPECIAL                                      */
/*==============================================================*/
create table EO_DATA_ESPECIAL  (
   SQ_DATA_ESPECIAL     NUMBER(18)                      not null,
   CLIENTE              NUMBER(18)                      not null,
   SQ_PAIS              NUMBER(18),
   CO_UF                VARCHAR2(3),
   SQ_CIDADE            NUMBER(18),
   TIPO                 VARCHAR2(1)                     not null
      constraint CKC_TIPO_EO_DATA_ check (TIPO in ('I','E','S','C','Q','P','D','H')),
   DATA_ESPECIAL        VARCHAR(10),
   NOME                 VARCHAR(60)                     not null,
   ABRANGENCIA          VARCHAR2(1)                     not null
      constraint CKC_ABRANGENCIA_EO_DATA_ check (ABRANGENCIA in ('I','N','E','M','O')),
   EXPEDIENTE           VARCHAR2(1)                     not null
      constraint CKC_EXPEDIENTE_EO_DATA_ check (EXPEDIENTE in ('S','N','M','T')),
   ATIVO                VARCHAR2(1)                    default 'S' not null
      constraint CKC_ATIVO_EO_DATA_ check (ATIVO in ('S','N') and ATIVO = upper(ATIVO)),
   constraint PK_EO_DATA_ESPECIAL primary key (SQ_DATA_ESPECIAL)
);

comment on table EO_DATA_ESPECIAL is
'Registra datas especiais da organização, tais como feriados, pontos facultativos e outras.';

comment on column EO_DATA_ESPECIAL.SQ_DATA_ESPECIAL is
'Chave de EO_DATA_ESPECIAL.';

comment on column EO_DATA_ESPECIAL.CLIENTE is
'Chave de CO_PESSOA. Indica a que cliente a data está ligada.';

comment on column EO_DATA_ESPECIAL.SQ_PAIS is
'Se a abrangência for nacional ou estadual, indica a que país ela refere-se.';

comment on column EO_DATA_ESPECIAL.CO_UF is
'Se a abrangência for estadual, indica a que estado ela refere-se.';

comment on column EO_DATA_ESPECIAL.SQ_CIDADE is
'Chave de CO_CIDADE. Se a abrangência for estadual, indica a que cidade ela refere-se.';

comment on column EO_DATA_ESPECIAL.TIPO is
'Tipo da data: I - Invariável, E - Específica, S - Segunda de carnaval, C - Carnaval, Q - Cinzas, P - Paixão, D - Páscoa, H - Corpus Christi.';

comment on column EO_DATA_ESPECIAL.DATA_ESPECIAL is
'Contém dia e mês (DD/MM) quando o tipo for I (Invariável) ou dia, mês e ano (DD/MM/AAAA) se tipo for E (Específico). Nos outros casos, recebe nulo.';

comment on column EO_DATA_ESPECIAL.NOME is
'Nome da ocorrência que torna a data especial.';

comment on column EO_DATA_ESPECIAL.ABRANGENCIA is
'I - Internacional, N - Nacional, E - Estadual, M - Municipal, O - Organização';

comment on column EO_DATA_ESPECIAL.EXPEDIENTE is
'Indica se há expediente na data. S - Sim, N - Não, M - Somente manhã, T - Somente tarde.';

comment on column EO_DATA_ESPECIAL.ATIVO is
'Indica se a data deve ser tratada para novos registros.';

/*==============================================================*/
/* Index: IN_EODATESP_ATIVO                                     */
/*==============================================================*/
create index IN_EODATESP_ATIVO on EO_DATA_ESPECIAL (
   CLIENTE ASC,
   ATIVO ASC,
   SQ_DATA_ESPECIAL ASC
);

/*==============================================================*/
/* Table: EO_INDICADOR                                          */
/*==============================================================*/
create table EO_INDICADOR  (
   SQ_EOINDICADOR       NUMBER(18)                      not null,
   CLIENTE              NUMBER(18)                      not null,
   SQ_TIPO_INDICADOR    NUMBER(18)                      not null,
   SQ_UNIDADE_MEDIDA    NUMBER(18)                      not null,
   NOME                 VARCHAR2(60)                    not null,
   SIGLA                VARCHAR2(15)                    not null,
   DESCRICAO            VARCHAR2(2000)                  not null,
   FORMA_AFERICAO       VARCHAR2(2000)                  not null,
   FONTE_COMPROVACAO    VARCHAR2(2000)                  not null,
   CICLO_AFERICAO       VARCHAR2(2000)                  not null,
   ATIVO                VARCHAR2(1)                    default 'S' not null
      constraint CKC_ATIVO_EO_INDIC check (ATIVO in ('S','N') and ATIVO = upper(ATIVO)),
   VINCULA_META         VARCHAR2(1)                    default 'N' not null
      constraint CKC_VINCULA_META_EO_INDIC check (VINCULA_META in ('S','N') and VINCULA_META = upper(VINCULA_META)),
   EXIBE_MESA           VARCHAR2(1)                    default 'S' not null
      constraint CKC_EXIBE_MESA_EO_INDIC check (EXIBE_MESA in ('S','N') and EXIBE_MESA = upper(EXIBE_MESA)),
   constraint PK_EO_INDICADOR primary key (SQ_EOINDICADOR)
);

comment on table EO_INDICADOR is
'Registra dos dados de um indicador da organização.';

comment on column EO_INDICADOR.SQ_EOINDICADOR is
'Chave de EO_INDICADOR.';

comment on column EO_INDICADOR.CLIENTE is
'Chave de CO_PESSOA. Indica a que cliente o registro está ligado.';

comment on column EO_INDICADOR.SQ_TIPO_INDICADOR is
'Chave de EO_TIPO_INDICADOR. Referencia para o tipo do indicador ligado ao registro.';

comment on column EO_INDICADOR.SQ_UNIDADE_MEDIDA is
'Chave de PE_UNIDADE_MEDIDA. Indica a unidade de medida do indicador.';

comment on column EO_INDICADOR.NOME is
'Nome do indicador.';

comment on column EO_INDICADOR.SIGLA is
'Sigla do indicador.';

comment on column EO_INDICADOR.DESCRICAO is
'Definição do indicador (o quê pretende medir).';

comment on column EO_INDICADOR.FORMA_AFERICAO is
'Forma de aferição do indicador.';

comment on column EO_INDICADOR.FONTE_COMPROVACAO is
'Fonte de comprovação do indicador.';

comment on column EO_INDICADOR.CICLO_AFERICAO is
'Ciclo de aferição sugerido para o indicador.';

comment on column EO_INDICADOR.ATIVO is
'Indica se o indicador pode ser vinculado a novos registros.';

comment on column EO_INDICADOR.VINCULA_META is
'Indica se o registro pode ser vinculado a uma meta.';

comment on column EO_INDICADOR.EXIBE_MESA is
'Indica se o registro deve ser exibido na mesa de trabalho.';

/*==============================================================*/
/* Index: IN_EOIND_NOME                                         */
/*==============================================================*/
create index IN_EOIND_NOME on EO_INDICADOR (
   CLIENTE ASC,
   NOME ASC,
   SQ_EOINDICADOR ASC
);

/*==============================================================*/
/* Index: IN_EOIND_SIGLA                                        */
/*==============================================================*/
create index IN_EOIND_SIGLA on EO_INDICADOR (
   CLIENTE ASC,
   SIGLA ASC,
   SQ_EOINDICADOR ASC
);

/*==============================================================*/
/* Index: IN_EOIND_ATIVO                                        */
/*==============================================================*/
create index IN_EOIND_ATIVO on EO_INDICADOR (
   CLIENTE ASC,
   ATIVO ASC,
   SQ_EOINDICADOR ASC
);

/*==============================================================*/
/* Index: IN_EOIND_META                                         */
/*==============================================================*/
create index IN_EOIND_META on EO_INDICADOR (
   CLIENTE ASC,
   VINCULA_META ASC,
   SQ_EOINDICADOR ASC
);

/*==============================================================*/
/* Index: IN_EOIND_MESA                                         */
/*==============================================================*/
create index IN_EOIND_MESA on EO_INDICADOR (
   CLIENTE ASC,
   EXIBE_MESA ASC,
   SQ_EOINDICADOR ASC
);

/*==============================================================*/
/* Table: EO_INDICADOR_AFERICAO                                 */
/*==============================================================*/
create table EO_INDICADOR_AFERICAO  (
   SQ_EOINDICADOR_AFERICAO NUMBER(18)                      not null,
   SQ_EOINDICADOR       NUMBER(18)                      not null,
   DATA_AFERICAO        DATE                            not null,
   REFERENCIA_INICIO    DATE                            not null,
   REFERENCIA_FIM       DATE                            not null,
   SQ_PAIS              NUMBER(18),
   SQ_REGIAO            NUMBER(18),
   CO_UF                VARCHAR2(3),
   SQ_CIDADE            NUMBER(18),
   CADASTRADOR          NUMBER(18)                      not null,
   BASE_GEOGRAFICA      NUMBER(1)                       not null,
   FONTE                VARCHAR2(60),
   VALOR                NUMBER(18,4)                   default 0 not null,
   INCLUSAO             DATE                           default sysdate not null,
   ULTIMA_ALTERACAO     DATE                           default sysdate,
   PREVISAO             VARCHAR2(1)                    default 'N' not null
      constraint CKC_PREVISAO_EO_INDIC check (PREVISAO in ('S','N') and PREVISAO = upper(PREVISAO)),
   OBSERVACAO           VARCHAR2(255),
   constraint PK_EO_INDICADOR_AFERICAO primary key (SQ_EOINDICADOR_AFERICAO)
);

comment on table EO_INDICADOR_AFERICAO is
'Registra as aferições do indicador.';

comment on column EO_INDICADOR_AFERICAO.SQ_EOINDICADOR_AFERICAO is
'Chave de EO_INDICADOR_AFERICAO.';

comment on column EO_INDICADOR_AFERICAO.SQ_EOINDICADOR is
'Chave de EO_INDICADOR. Indica a que indicador o registro está ligado.';

comment on column EO_INDICADOR_AFERICAO.DATA_AFERICAO is
'Data de aferição do indicador.';

comment on column EO_INDICADOR_AFERICAO.REFERENCIA_INICIO is
'Início do período de referência da aferição.';

comment on column EO_INDICADOR_AFERICAO.REFERENCIA_FIM is
'Término do período de referência da aferição.';

comment on column EO_INDICADOR_AFERICAO.SQ_PAIS is
'Chave de CO_PAIS. Tem valor apenas quando a aferição é a nivel nacional.';

comment on column EO_INDICADOR_AFERICAO.SQ_REGIAO is
'Chave de CO_REGIAO. Tem valor apenas quando a aferição é a nivel regional.';

comment on column EO_INDICADOR_AFERICAO.CO_UF is
'Chave de CO_UF. Tem valor apenas quando a aferição é a nivel estadual.';

comment on column EO_INDICADOR_AFERICAO.SQ_CIDADE is
'Chave de CO_CIDADE. Tem valor apenas quando a aferição é a nivel municipal.';

comment on column EO_INDICADOR_AFERICAO.CADASTRADOR is
'Chave de CO_PESSOA. Indica o responsável pelo cadastramento ou pela última alteração no registro.';

comment on column EO_INDICADOR_AFERICAO.BASE_GEOGRAFICA is
'Indica a que base geográfica a aferição aplica-se. 1 - Nacional, 2 - Regional, 3 - Estadual, 4 - Municipal, 5 - Organizacional.';

comment on column EO_INDICADOR_AFERICAO.FONTE is
'Fonte de aferição do indicador.';

comment on column EO_INDICADOR_AFERICAO.VALOR is
'Valor aferido.';

comment on column EO_INDICADOR_AFERICAO.INCLUSAO is
'Data de inclusão do registro.';

comment on column EO_INDICADOR_AFERICAO.ULTIMA_ALTERACAO is
'Data da última alteração no registro.';

comment on column EO_INDICADOR_AFERICAO.PREVISAO is
'Indica se o valor registrado é uma previsão.';

comment on column EO_INDICADOR_AFERICAO.OBSERVACAO is
'Observações quaisquer, julgadas relevantes pelo usuário.';

/*==============================================================*/
/* Index: IN_EOINDAFE_DATA                                      */
/*==============================================================*/
create index IN_EOINDAFE_DATA on EO_INDICADOR_AFERICAO (
   SQ_EOINDICADOR ASC,
   DATA_AFERICAO ASC,
   SQ_EOINDICADOR_AFERICAO ASC
);

/*==============================================================*/
/* Index: IN_EOINDAFE_INICIO                                    */
/*==============================================================*/
create index IN_EOINDAFE_INICIO on EO_INDICADOR_AFERICAO (
   SQ_EOINDICADOR ASC,
   REFERENCIA_INICIO ASC,
   SQ_EOINDICADOR_AFERICAO ASC
);

/*==============================================================*/
/* Index: IN_EOINDAFE_FIM                                       */
/*==============================================================*/
create index IN_EOINDAFE_FIM on EO_INDICADOR_AFERICAO (
   SQ_EOINDICADOR ASC,
   REFERENCIA_FIM ASC,
   SQ_EOINDICADOR_AFERICAO ASC
);

/*==============================================================*/
/* Index: IN_EOINDAFE_UNICO                                     */
/*==============================================================*/
create unique index IN_EOINDAFE_UNICO on EO_INDICADOR_AFERICAO (
   SQ_EOINDICADOR ASC,
   DATA_AFERICAO ASC,
   BASE_GEOGRAFICA ASC
);

/*==============================================================*/
/* Table: EO_INDICADOR_AFERIDOR                                 */
/*==============================================================*/
create table EO_INDICADOR_AFERIDOR  (
   SQ_EOINDICADOR_AFERIDOR NUMBER(18)                      not null,
   SQ_EOINDICADOR       NUMBER(18)                      not null,
   SQ_PESSOA            NUMBER(18)                      not null,
   PRAZO_DEFINIDO       VARCHAR2(1)                    default 'N' not null
      constraint CKC_PRAZO_DEFINIDO_EO_INDIC check (PRAZO_DEFINIDO in ('S','N') and PRAZO_DEFINIDO = upper(PRAZO_DEFINIDO)),
   INICIO               DATE                            not null,
   FIM                  DATE                            not null,
   constraint PK_EO_INDICADOR_AFERIDOR primary key (SQ_EOINDICADOR_AFERIDOR)
);

comment on table EO_INDICADOR_AFERIDOR is
'Registra as pessoas responsáveis pela aferição do indicador.';

comment on column EO_INDICADOR_AFERIDOR.SQ_EOINDICADOR_AFERIDOR is
'Chave de EO_INDICADOR_AFERIDOR.';

comment on column EO_INDICADOR_AFERIDOR.SQ_EOINDICADOR is
'Chave de EO_INDICADOR. Indica a que indicador o registro está ligado.';

comment on column EO_INDICADOR_AFERIDOR.SQ_PESSOA is
'Chave de CO_PESSOA. Indica a que pessoa o registro está ligado.';

comment on column EO_INDICADOR_AFERIDOR.PRAZO_DEFINIDO is
'Indica se a responsabilidade tem prazo definido.';

comment on column EO_INDICADOR_AFERIDOR.INICIO is
'Início da responsabilidade pela aferição.';

comment on column EO_INDICADOR_AFERIDOR.FIM is
'Se o prazo de responsabilidade é definido, indica o término da responsabilidade. Se for indefinido, armazena 31/12/2100.';

/*==============================================================*/
/* Index: IN_EOINDAFR_INDICADOR                                 */
/*==============================================================*/
create index IN_EOINDAFR_INDICADOR on EO_INDICADOR_AFERIDOR (
   SQ_EOINDICADOR ASC,
   SQ_EOINDICADOR_AFERIDOR ASC
);

/*==============================================================*/
/* Index: IN_EOINDAFR_PESSOA                                    */
/*==============================================================*/
create index IN_EOINDAFR_PESSOA on EO_INDICADOR_AFERIDOR (
   SQ_PESSOA ASC,
   SQ_EOINDICADOR_AFERIDOR ASC
);

/*==============================================================*/
/* Index: IN_EOINDAFR_INICIO                                    */
/*==============================================================*/
create index IN_EOINDAFR_INICIO on EO_INDICADOR_AFERIDOR (
   INICIO ASC,
   SQ_EOINDICADOR_AFERIDOR ASC
);

/*==============================================================*/
/* Index: IN_EOINDAFR_FIM                                       */
/*==============================================================*/
create index IN_EOINDAFR_FIM on EO_INDICADOR_AFERIDOR (
   FIM ASC,
   SQ_EOINDICADOR_AFERIDOR ASC
);

/*==============================================================*/
/* Table: EO_INDICADOR_AGENDA                                   */
/*==============================================================*/
create table EO_INDICADOR_AGENDA  (
   SQ_EOINDICADOR_AGENDA NUMBER(18)                      not null,
   SQ_EOINDICADOR       NUMBER(18)                      not null,
   PADRAO_RECORRENCIA   NUMBER(1)                       not null,
   TIPO_RECORRENCIA     NUMBER(2)                       not null,
   PRAZO_DEFINIDO       VARCHAR2(1)                    default 'N' not null
      constraint CKC_PRAZO_DEFINIDO_EOINDAGE check (PRAZO_DEFINIDO in ('S','N') and PRAZO_DEFINIDO = upper(PRAZO_DEFINIDO)),
   INICIO               DATE                            not null,
   FIM                  DATE                            not null,
   LIMITE_OCORRENCIAS   NUMBER(4),
   INTERVALO_OCORRENCIAS NUMBER(4),
   DIA_UTIL             VARCHAR2(1)                    default 'S' not null
      constraint CKC_DIA_UTIL_EOINDAGE check (DIA_UTIL in ('S','N') and DIA_UTIL = upper(DIA_UTIL)),
   DIA_MES              NUMBER(2),
   DIA_SEMANA           NUMBER(2),
   ORDINAL_SEMANA       NUMBER(1),
   MES                  NUMBER(2),
   ATIVO                VARCHAR2(1)                    default 'S' not null
      constraint CKC_ATIVO_EOINDAGE check (ATIVO in ('S','N') and ATIVO = upper(ATIVO)),
   constraint PK_EO_INDICADOR_AGENDA primary key (SQ_EOINDICADOR_AGENDA)
);

comment on table EO_INDICADOR_AGENDA is
'Registra o agendamento de aferições de um indicador.';

comment on column EO_INDICADOR_AGENDA.SQ_EOINDICADOR_AGENDA is
'Chave de EO_INDICADOR_AGENDA.';

comment on column EO_INDICADOR_AGENDA.SQ_EOINDICADOR is
'Chave de EO_INDICADOR. Indica a que indicador o registro está ligado.';

comment on column EO_INDICADOR_AGENDA.PADRAO_RECORRENCIA is
'Indica o padrão de recorrência da agenda: 1 - Diário, 2 - Semanal, 3 - Mensal, 4 - Anual.';

comment on column EO_INDICADOR_AGENDA.TIPO_RECORRENCIA is
'Indica o tipo de recorrência: 1 - A cada x dias/semanas/meses; 2 - Dia específico; 3 - Dia relativo.';

comment on column EO_INDICADOR_AGENDA.PRAZO_DEFINIDO is
'Indica se o prazo de agendamento tem término definido.';

comment on column EO_INDICADOR_AGENDA.INICIO is
'Data de início do agendamento.';

comment on column EO_INDICADOR_AGENDA.FIM is
'Se o prazo de agendamento for definido e baseado em datas, registra a data de término. Se for indefinido, armazena 31/12/2100.';

comment on column EO_INDICADOR_AGENDA.LIMITE_OCORRENCIAS is
'Se o prazo de agendamento for definido e baseado em número de ocorrências, registra esse número.';

comment on column EO_INDICADOR_AGENDA.INTERVALO_OCORRENCIAS is
'Informa o intervalo entre os agendamentos (dias, semanas, meses ou anos).';

comment on column EO_INDICADOR_AGENDA.DIA_UTIL is
'Indica se o agendamento deve ser feito apenas em dias úteis.';

comment on column EO_INDICADOR_AGENDA.DIA_MES is
'Dia do mês em que deve ocorrer o agendamento.';

comment on column EO_INDICADOR_AGENDA.DIA_SEMANA is
'Dia da semana do agendamento: 1 - Domingo ... 7 - Sábado, 8 - Dia, 9 - Dia da semana, 10 - Final de semana.';

comment on column EO_INDICADOR_AGENDA.ORDINAL_SEMANA is
'Indica o ordinal da semana: 1 - Primeiro, 2 - Segundo, 3 - Terceiro, 4 - Quarto, 5 - Último.';

comment on column EO_INDICADOR_AGENDA.MES is
'Indica o mês em que deve ocorrer o agendamento. Apenas para PADRAO=4.';

comment on column EO_INDICADOR_AGENDA.ATIVO is
'Indica se o agendamento está ativo.';

/*==============================================================*/
/* Index: IN_EOINDAGE_INICIO                                    */
/*==============================================================*/
create index IN_EOINDAGE_INICIO on EO_INDICADOR_AGENDA (
   SQ_EOINDICADOR ASC,
   INICIO ASC,
   SQ_EOINDICADOR_AGENDA ASC
);

/*==============================================================*/
/* Index: IN_EOINDAGE_FIM                                       */
/*==============================================================*/
create index IN_EOINDAGE_FIM on EO_INDICADOR_AGENDA (
   SQ_EOINDICADOR ASC,
   FIM ASC,
   SQ_EOINDICADOR_AGENDA ASC
);

/*==============================================================*/
/* Table: EO_LOCALIZACAO                                        */
/*==============================================================*/
create table EO_LOCALIZACAO  (
   SQ_LOCALIZACAO       NUMBER(10)                      not null,
   SQ_UNIDADE           number(10)                      not null,
   SQ_PESSOA_ENDERECO   NUMBER(18),
   CLIENTE              NUMBER(18)                      not null,
   NOME                 VARCHAR2(30)                    not null,
   TELEFONE             VARCHAR2(12),
   TELEFONE2            VARCHAR2(12),
   RAMAL                VARCHAR2(6),
   FAX                  VARCHAR2(12),
   ATIVO                VARCHAR2(1)                    default 'S' not null
      constraint CKC_ATIVO_EO_LOCAL check (ATIVO in ('S','N') and ATIVO = upper(ATIVO)),
   CODIGO_EXTERNO       VARCHAR2(60),
   ALMOXARIFADO_CONSUMO VARCHAR2(1)                    default 'N' not null
      constraint CKC_ALMOXARIFADO_CONS_EO_LOCAL check (ALMOXARIFADO_CONSUMO in ('S','N') and ALMOXARIFADO_CONSUMO = upper(ALMOXARIFADO_CONSUMO)),
   DEPOSITO_PERMANENTE  VARCHAR2(1)                    default 'N' not null
      constraint CKC_DEPOSITO_PERMANEN_EO_LOCAL check (DEPOSITO_PERMANENTE in ('S','N') and DEPOSITO_PERMANENTE = upper(DEPOSITO_PERMANENTE)),
   ARQUIVO_SETORIAL     VARCHAR2(1)                    default 'N' not null
      constraint CKC_ARQUIVO_SETORIAL_EO_LOCAL check (ARQUIVO_SETORIAL in ('S','N') and ARQUIVO_SETORIAL = upper(ARQUIVO_SETORIAL)),
   ARQUIVO_CENTRAL      VARCHAR2(1)                    default 'N' not null
      constraint CKC_ARQUIVO_CENTRAL_EO_LOCAL check (ARQUIVO_CENTRAL in ('S','N') and ARQUIVO_CENTRAL = upper(ARQUIVO_CENTRAL)),
   constraint PK_EO_LOCALIZACAO primary key (SQ_LOCALIZACAO)
);

comment on table EO_LOCALIZACAO is
'Armazena a tabela de localizações de unidades';

comment on column EO_LOCALIZACAO.SQ_LOCALIZACAO is
'Chave de EO_LOCALIZACAO.';

comment on column EO_LOCALIZACAO.SQ_UNIDADE is
'Chave de EO_UNIDADE. Indica a que unidade o registro está ligado.';

comment on column EO_LOCALIZACAO.SQ_PESSOA_ENDERECO is
'Chave de CO_PESSOA_ENDERECO. Indica a que endereço o registro está ligado.';

comment on column EO_LOCALIZACAO.CLIENTE is
'Chave de CO_PESSOA. Indica a que cliente o registro está ligado.';

comment on column EO_LOCALIZACAO.NOME is
'Nome da localização.';

comment on column EO_LOCALIZACAO.TELEFONE is
'Telefone da localização';

comment on column EO_LOCALIZACAO.TELEFONE2 is
'Outro telefone da localização';

comment on column EO_LOCALIZACAO.RAMAL is
'Ramal da localização.';

comment on column EO_LOCALIZACAO.FAX is
'Fax do local.';

comment on column EO_LOCALIZACAO.ATIVO is
'Indica se este registro está disponível para ligação a outras tabelas.';

comment on column EO_LOCALIZACAO.CODIGO_EXTERNO is
'Código desse registro num sistema externo.';

comment on column EO_LOCALIZACAO.ALMOXARIFADO_CONSUMO is
'Indica se o local é almoxarifado de materiais de consumo.';

comment on column EO_LOCALIZACAO.DEPOSITO_PERMANENTE is
'Indica se o local é depósito de materiais permanentes.';

comment on column EO_LOCALIZACAO.ARQUIVO_SETORIAL is
'Indica se o local é arquivo setorial de protocolo.';

comment on column EO_LOCALIZACAO.ARQUIVO_CENTRAL is
'Indica se o local é arquivo central de protocolo.';

/*==============================================================*/
/* Index: IN_EOLOC_UNIDADE                                      */
/*==============================================================*/
create index IN_EOLOC_UNIDADE on EO_LOCALIZACAO (
   CLIENTE ASC,
   SQ_UNIDADE ASC,
   SQ_LOCALIZACAO ASC
);

/*==============================================================*/
/* Index: IN_EOLOC_ENDERECO                                     */
/*==============================================================*/
create index IN_EOLOC_ENDERECO on EO_LOCALIZACAO (
   CLIENTE ASC,
   SQ_PESSOA_ENDERECO ASC,
   SQ_LOCALIZACAO ASC
);

/*==============================================================*/
/* Index: IN_EOLOC_ATIVO                                        */
/*==============================================================*/
create index IN_EOLOC_ATIVO on EO_LOCALIZACAO (
   CLIENTE ASC,
   ATIVO ASC,
   SQ_LOCALIZACAO ASC
);

/*==============================================================*/
/* Index: IN_EOLOC_EXTERNO                                      */
/*==============================================================*/
create index IN_EOLOC_EXTERNO on EO_LOCALIZACAO (
   CLIENTE ASC,
   CODIGO_EXTERNO ASC,
   SQ_LOCALIZACAO ASC
);

/*==============================================================*/
/* Index: IN_EOLOC_ALMOXARIFADO                                 */
/*==============================================================*/
create index IN_EOLOC_ALMOXARIFADO on EO_LOCALIZACAO (
   CLIENTE ASC,
   ALMOXARIFADO_CONSUMO ASC,
   SQ_LOCALIZACAO ASC
);

/*==============================================================*/
/* Index: IN_EOLOC_DEPOSITO                                     */
/*==============================================================*/
create index IN_EOLOC_DEPOSITO on EO_LOCALIZACAO (
   CLIENTE ASC,
   DEPOSITO_PERMANENTE ASC,
   SQ_LOCALIZACAO ASC
);

/*==============================================================*/
/* Index: IN_EOLOC_SETORIAL                                     */
/*==============================================================*/
create index IN_EOLOC_SETORIAL on EO_LOCALIZACAO (
   CLIENTE ASC,
   ARQUIVO_SETORIAL ASC,
   SQ_LOCALIZACAO ASC
);

/*==============================================================*/
/* Index: IN_EOLOC_CENTRAL                                      */
/*==============================================================*/
create index IN_EOLOC_CENTRAL on EO_LOCALIZACAO (
   CLIENTE ASC,
   ARQUIVO_CENTRAL ASC,
   SQ_LOCALIZACAO ASC
);

/*==============================================================*/
/* Table: EO_RECURSO                                            */
/*==============================================================*/
create table EO_RECURSO  (
   SQ_RECURSO           NUMBER(18)                      not null,
   CLIENTE              NUMBER(18)                      not null,
   SQ_TIPO_RECURSO      NUMBER(18)                      not null,
   SQ_UNIDADE_MEDIDA    NUMBER(18),
   UNIDADE_GESTORA      NUMBER(10)                      not null,
   NOME                 VARCHAR2(100)                   not null,
   CODIGO               VARCHAR2(40),
   DESCRICAO            VARCHAR2(2000),
   FINALIDADE           VARCHAR2(2000),
   DISPONIBILIDADE_TIPO NUMBER(1),
   ATIVO                VARCHAR2(1)                    default 'S' not null
      constraint CKC_ATIVO_EO_RECUR check (ATIVO in ('S','N') and ATIVO = upper(ATIVO)),
   EXIBE_MESA           VARCHAR2(1)                    default 'N' not null
      constraint CKC_EXIBE_MESA_EO_RECUR check (EXIBE_MESA in ('S','N') and EXIBE_MESA = upper(EXIBE_MESA)),
   constraint PK_EO_RECURSO primary key (SQ_RECURSO)
);

comment on table EO_RECURSO is
'Registra informações sobre o pool de recursos.';

comment on column EO_RECURSO.SQ_RECURSO is
'Chave de EO_RECURSO.';

comment on column EO_RECURSO.CLIENTE is
'Chave de CO_PESSOA. Indica a que cliente o registro está ligado.';

comment on column EO_RECURSO.SQ_TIPO_RECURSO is
'Chave de EO_TIPO_RECURSO. Indica o tipo do recurso.';

comment on column EO_RECURSO.SQ_UNIDADE_MEDIDA is
'Chave de PE_UNIDADE_MEDIDA.';

comment on column EO_RECURSO.UNIDADE_GESTORA is
'Chave de EO_UNIDADE. Indica a unidade gestora do recurso.';

comment on column EO_RECURSO.NOME is
'Nome do recurso.';

comment on column EO_RECURSO.CODIGO is
'Código de identificação única do recurso (RGP, matrícula, CPF, placa etc.)';

comment on column EO_RECURSO.DESCRICAO is
'Descrição do recurso.';

comment on column EO_RECURSO.FINALIDADE is
'Finalidade do recurso.';

comment on column EO_RECURSO.DISPONIBILIDADE_TIPO is
'Indica a disponibilidade do recurso. 1 - Indefinida; 2 - Definida com limite de unidades; 3 - Definida sem limite de unidades.';

comment on column EO_RECURSO.ATIVO is
'Indica se o recurso pode ser ligado a novos registros.';

comment on column EO_RECURSO.EXIBE_MESA is
'Indica se o recurso deve ser exibido na mesa de trabalho.';

/*==============================================================*/
/* Index: IN_EOREC_GESTORA                                      */
/*==============================================================*/
create index IN_EOREC_GESTORA on EO_RECURSO (
   CLIENTE ASC,
   UNIDADE_GESTORA ASC,
   SQ_RECURSO ASC
);

/*==============================================================*/
/* Index: IN_EOREC_TIPO                                         */
/*==============================================================*/
create index IN_EOREC_TIPO on EO_RECURSO (
   CLIENTE ASC,
   SQ_TIPO_RECURSO ASC,
   SQ_RECURSO ASC
);

/*==============================================================*/
/* Index: IN_EOREC_ATIVO                                        */
/*==============================================================*/
create index IN_EOREC_ATIVO on EO_RECURSO (
   CLIENTE ASC,
   ATIVO ASC,
   SQ_RECURSO ASC
);

/*==============================================================*/
/* Index: IN_EOREC_MESA                                         */
/*==============================================================*/
create index IN_EOREC_MESA on EO_RECURSO (
   CLIENTE ASC,
   EXIBE_MESA ASC,
   SQ_RECURSO ASC
);

/*==============================================================*/
/* Table: EO_RECURSO_DISPONIVEL                                 */
/*==============================================================*/
create table EO_RECURSO_DISPONIVEL  (
   SQ_RECURSO_DISPONIVEL NUMBER(18)                      not null,
   SQ_RECURSO           NUMBER(18)                      not null,
   INICIO               DATE,
   FIM                  DATE,
   VALOR                NUMBER(18,2)                    not null,
   UNIDADES             NUMBER(18,2),
   LIMITE_DIARIO        NUMBER(18,2),
   DIA_UTIL             VARCHAR2(1)                    default 'S' not null
      constraint CKC_DIA_UTIL_EO_RECUR check (DIA_UTIL in ('S','N') and DIA_UTIL = upper(DIA_UTIL)),
   constraint PK_EO_RECURSO_DISPONIVEL primary key (SQ_RECURSO_DISPONIVEL)
);

comment on table EO_RECURSO_DISPONIVEL is
'Registra períodos de disponibilidade do recurso.';

comment on column EO_RECURSO_DISPONIVEL.SQ_RECURSO_DISPONIVEL is
'Chave de EO_RECURSO_DISPONIVEL.';

comment on column EO_RECURSO_DISPONIVEL.SQ_RECURSO is
'Chave de EO_RECURSO. Indica a que recurso o registro está vinculado.';

comment on column EO_RECURSO_DISPONIVEL.INICIO is
'Registra o inicio da disponibilidade, quando ela for do tipo 2 ou 3.';

comment on column EO_RECURSO_DISPONIVEL.FIM is
'Registra o fim da disponibilidade, quando ela for do tipo 2 ou 3.';

comment on column EO_RECURSO_DISPONIVEL.VALOR is
'Valor da unidade definida para o recurso.';

comment on column EO_RECURSO_DISPONIVEL.UNIDADES is
'Quantidade disponível de unidades do recurso no período.';

comment on column EO_RECURSO_DISPONIVEL.LIMITE_DIARIO is
'Registra o limite de unidades que podem ser consumidas diariamente, se o tipo de disponibilidade for 2 ou 3.';

comment on column EO_RECURSO_DISPONIVEL.DIA_UTIL is
'Indica se o recurso pode ser alocado apenas em dias úteis. Se for igual a não, permite a alocação em qualquer dia.';

/*==============================================================*/
/* Index: IN_EORECDIS_INICIO                                    */
/*==============================================================*/
create index IN_EORECDIS_INICIO on EO_RECURSO_DISPONIVEL (
   SQ_RECURSO ASC,
   INICIO ASC,
   SQ_RECURSO_DISPONIVEL ASC
);

/*==============================================================*/
/* Index: IN_EORECDIS_FIM                                       */
/*==============================================================*/
create index IN_EORECDIS_FIM on EO_RECURSO_DISPONIVEL (
   SQ_RECURSO ASC,
   FIM ASC,
   SQ_RECURSO_DISPONIVEL ASC
);

/*==============================================================*/
/* Table: EO_RECURSO_INDISPONIVEL                               */
/*==============================================================*/
create table EO_RECURSO_INDISPONIVEL  (
   SQ_RECURSO_INDISPONIVEL NUMBER(18)                      not null,
   SQ_RECURSO           NUMBER(18)                      not null,
   INICIO               DATE                            not null,
   FIM                  DATE                            not null,
   JUSTIFICATIVA        VARCHAR2(2000)                  not null,
   constraint PK_EO_RECURSO_INDISPONIVEL primary key (SQ_RECURSO_INDISPONIVEL)
);

comment on table EO_RECURSO_INDISPONIVEL is
'Registra períodos de indisponibilidade do recurso.';

comment on column EO_RECURSO_INDISPONIVEL.SQ_RECURSO_INDISPONIVEL is
'Chave de EO_RECURSO_INDISPONIVEL.';

comment on column EO_RECURSO_INDISPONIVEL.SQ_RECURSO is
'Chave de EO_RECURSO. Indica a que recurso o registro está vinculado.';

comment on column EO_RECURSO_INDISPONIVEL.INICIO is
'Data de início da indisponibilidade do recurso.';

comment on column EO_RECURSO_INDISPONIVEL.FIM is
'Data de término da indisponibilidade do recurso.';

comment on column EO_RECURSO_INDISPONIVEL.JUSTIFICATIVA is
'Justificativa para a indisponibilidade do recurso.';

/*==============================================================*/
/* Index: IN_EORECIND_INICIO                                    */
/*==============================================================*/
create index IN_EORECIND_INICIO on EO_RECURSO_INDISPONIVEL (
   SQ_RECURSO ASC,
   INICIO ASC,
   SQ_RECURSO_INDISPONIVEL ASC
);

/*==============================================================*/
/* Index: IN_EORECIND_FIM                                       */
/*==============================================================*/
create index IN_EORECIND_FIM on EO_RECURSO_INDISPONIVEL (
   SQ_RECURSO ASC,
   FIM ASC,
   SQ_RECURSO_INDISPONIVEL ASC
);

/*==============================================================*/
/* Table: EO_RECURSO_MENU                                       */
/*==============================================================*/
create table EO_RECURSO_MENU  (
   SQ_RECURSO           NUMBER(18)                      not null,
   SQ_MENU              NUMBER(18)                      not null,
   constraint PK_EO_RECURSO_MENU primary key (SQ_RECURSO, SQ_MENU)
);

comment on table EO_RECURSO_MENU is
'Registra que recursos estão disponíveis para cada opção do menu.';

comment on column EO_RECURSO_MENU.SQ_RECURSO is
'Chave de EO_RECURSO. Indica a que recurso o registro está ligado.';

comment on column EO_RECURSO_MENU.SQ_MENU is
'Chave de SIW_MENU. Indica a que opção do menu o registro está ligado.';

/*==============================================================*/
/* Index: IN_EORECMEN_INVERSA                                   */
/*==============================================================*/
create index IN_EORECMEN_INVERSA on EO_RECURSO_MENU (
   SQ_MENU ASC,
   SQ_RECURSO ASC
);

/*==============================================================*/
/* Table: EO_TIPO_INDICADOR                                     */
/*==============================================================*/
create table EO_TIPO_INDICADOR  (
   SQ_TIPO_INDICADOR    NUMBER(18)                      not null,
   CLIENTE              NUMBER(18)                      not null,
   NOME                 VARCHAR2(25)                    not null,
   ATIVO                VARCHAR2(1)                    default 'S' not null
      constraint CKC_ATIVO_EOTIPIND check (ATIVO in ('S','N') and ATIVO = upper(ATIVO)),
   constraint PK_EO_TIPO_INDICADOR primary key (SQ_TIPO_INDICADOR)
);

comment on table EO_TIPO_INDICADOR is
'Registra os tipos de indicador.';

comment on column EO_TIPO_INDICADOR.SQ_TIPO_INDICADOR is
'Chave de EO_TIPO_INDICADOR.';

comment on column EO_TIPO_INDICADOR.CLIENTE is
'Chave de CO_PESSOA. Indica a que cliente o registro está ligado.';

comment on column EO_TIPO_INDICADOR.NOME is
'Nome do tipo de indicador.';

comment on column EO_TIPO_INDICADOR.ATIVO is
'Indica se o tipo do indicador pode ser vinculado a novos registros.';

/*==============================================================*/
/* Index: IN_EOTIPIND_ATIVO                                     */
/*==============================================================*/
create index IN_EOTIPIND_ATIVO on EO_TIPO_INDICADOR (
   CLIENTE ASC,
   ATIVO ASC,
   SQ_TIPO_INDICADOR ASC
);

/*==============================================================*/
/* Index: IN_EOTIPIND_NOME                                      */
/*==============================================================*/
create index IN_EOTIPIND_NOME on EO_TIPO_INDICADOR (
   CLIENTE ASC,
   NOME ASC,
   SQ_TIPO_INDICADOR ASC
);

/*==============================================================*/
/* Table: EO_TIPO_RECURSO                                       */
/*==============================================================*/
create table EO_TIPO_RECURSO  (
   SQ_TIPO_RECURSO      NUMBER(18)                      not null,
   CLIENTE              NUMBER(18)                      not null,
   SQ_TIPO_PAI          NUMBER(18),
   UNIDADE_GESTORA      NUMBER(10)                      not null,
   NOME                 VARCHAR2(30)                    not null,
   SIGLA                VARCHAR2(10)                    not null,
   DESCRICAO            VARCHAR2(2000)                  not null,
   ATIVO                VARCHAR2(1)                    default 'S' not null
      constraint CKC_ATIVO_PETIPREC check (ATIVO in ('S','N') and ATIVO = upper(ATIVO)),
   constraint PK_EO_TIPO_RECURSO primary key (SQ_TIPO_RECURSO)
);

comment on table EO_TIPO_RECURSO is
'Registra os tipos de recurso.';

comment on column EO_TIPO_RECURSO.SQ_TIPO_RECURSO is
'Chave de EO_TIPO_RECURSO.';

comment on column EO_TIPO_RECURSO.CLIENTE is
'Chave de CO_PESSOA. Indica a que cliente o registro está ligado.';

comment on column EO_TIPO_RECURSO.SQ_TIPO_PAI is
'Chave de EO_TIPO_RECURSO. Indica a que tipo de recurso o registro está ligado.';

comment on column EO_TIPO_RECURSO.UNIDADE_GESTORA is
'Chave de EO_UNIDADE. Indica a unidade responsável pela gestão dos recursos deste tipo.';

comment on column EO_TIPO_RECURSO.NOME is
'Nome do tipo de recurso.';

comment on column EO_TIPO_RECURSO.SIGLA is
'Sigla do tipo de recurso.';

comment on column EO_TIPO_RECURSO.DESCRICAO is
'Descrição do tipo de recurso.';

comment on column EO_TIPO_RECURSO.ATIVO is
'Indica se este tipo de recurso pode ser vinculado a novos registros.';

/*==============================================================*/
/* Index: IN_EOTIPREC_CLIENTE                                   */
/*==============================================================*/
create index IN_EOTIPREC_CLIENTE on EO_TIPO_RECURSO (
   CLIENTE ASC,
   SQ_TIPO_RECURSO ASC
);

/*==============================================================*/
/* Index: IN_EOTIPREC_NOME                                      */
/*==============================================================*/
create index IN_EOTIPREC_NOME on EO_TIPO_RECURSO (
   CLIENTE ASC,
   NOME ASC,
   SQ_TIPO_RECURSO ASC
);

/*==============================================================*/
/* Index: IN_EOTIPREC_SIGLA                                     */
/*==============================================================*/
create index IN_EOTIPREC_SIGLA on EO_TIPO_RECURSO (
   CLIENTE ASC,
   SIGLA ASC,
   SQ_TIPO_RECURSO ASC
);

/*==============================================================*/
/* Index: IN_EOTIPREC_ATIVO                                     */
/*==============================================================*/
create index IN_EOTIPREC_ATIVO on EO_TIPO_RECURSO (
   CLIENTE ASC,
   ATIVO ASC,
   SQ_TIPO_RECURSO ASC
);

/*==============================================================*/
/* Index: IN_EOTIPREC_PAI                                       */
/*==============================================================*/
create index IN_EOTIPREC_PAI on EO_TIPO_RECURSO (
   CLIENTE ASC,
   SQ_TIPO_PAI ASC,
   SQ_TIPO_RECURSO ASC
);

/*==============================================================*/
/* Index: IN_EOTIPREC_GESTORA                                   */
/*==============================================================*/
create index IN_EOTIPREC_GESTORA on EO_TIPO_RECURSO (
   CLIENTE ASC,
   UNIDADE_GESTORA ASC,
   SQ_TIPO_RECURSO ASC
);

/*==============================================================*/
/* Table: EO_TIPO_UNIDADE                                       */
/*==============================================================*/
create table EO_TIPO_UNIDADE  (
   SQ_TIPO_UNIDADE      NUMBER(10)                      not null,
   SQ_PESSOA            NUMBER(18)                      not null,
   NOME                 VARCHAR2(25)                    not null,
   ATIVO                VARCHAR2(1)                    default 'S' not null
      constraint CKC_ATIVO_EOTIPPOSTO check (ATIVO in ('S','N') and ATIVO = upper(ATIVO)),
   CODIGO_EXTERNO       VARCHAR2(60),
   constraint PK_EO_TIPO_UNIDADE primary key (SQ_TIPO_UNIDADE)
);

comment on table EO_TIPO_UNIDADE is
'Armazena os tipos de unidades organizacionais';

comment on column EO_TIPO_UNIDADE.SQ_TIPO_UNIDADE is
'Chave de EO_TIPO_UNIDADE.';

comment on column EO_TIPO_UNIDADE.SQ_PESSOA is
'Chave de CO_PESSOA. Indica a que cliente o registro está ligado.';

comment on column EO_TIPO_UNIDADE.NOME is
'Nome do tipo de unidade.';

comment on column EO_TIPO_UNIDADE.ATIVO is
'Indica se este registro está disponível para ligação a outras tabelas.';

comment on column EO_TIPO_UNIDADE.CODIGO_EXTERNO is
'Código desse registro num sistema externo.';

/*==============================================================*/
/* Index: IN_EOTIPUNI_PESSOA                                    */
/*==============================================================*/
create index IN_EOTIPUNI_PESSOA on EO_TIPO_UNIDADE (
   SQ_TIPO_UNIDADE ASC,
   SQ_PESSOA ASC
);

/*==============================================================*/
/* Index: IN_EOTIPUNI_ATIVO                                     */
/*==============================================================*/
create index IN_EOTIPUNI_ATIVO on EO_TIPO_UNIDADE (
   ATIVO ASC,
   SQ_TIPO_UNIDADE ASC
);

/*==============================================================*/
/* Index: IN_TOTIPUNI_EXTERNO                                   */
/*==============================================================*/
create index IN_TOTIPUNI_EXTERNO on EO_TIPO_UNIDADE (
   SQ_PESSOA ASC,
   CODIGO_EXTERNO ASC,
   SQ_TIPO_UNIDADE ASC
);

/*==============================================================*/
/* Table: EO_UNIDADE                                            */
/*==============================================================*/
create table EO_UNIDADE  (
   SQ_UNIDADE           NUMBER(10)                      not null,
   SQ_UNIDADE_PAI       number(10),
   SQ_UNIDADE_GESTORA   number(10),
   SQ_UNID_PAGADORA     number(10),
   SQ_AREA_ATUACAO      NUMBER(10),
   SQ_TIPO_UNIDADE      NUMBER(10),
   SQ_PESSOA_ENDERECO   NUMBER(18),
   SQ_PESSOA            NUMBER(18)                      not null,
   NOME                 VARCHAR2(60)                    not null,
   SIGLA                VARCHAR2(20)                    not null,
   ORDEM                NUMBER(2)                       not null,
   INFORMAL             VARCHAR2(1)                    default 'N' not null
      constraint CKC_EOUNI_INFORM
               check (INFORMAL in ('S','N') and INFORMAL = upper(INFORMAL)),
   VINCULADA            VARCHAR2(1)                    default 'N' not null
      constraint CKC_EOUNI_VINC
               check (VINCULADA in ('S','N') and VINCULADA = upper(VINCULADA)),
   ADM_CENTRAL          VARCHAR2(1)                    default 'N' not null
      constraint CKC_EOUNI_ADMCEN
               check (ADM_CENTRAL in ('S','N') and ADM_CENTRAL = upper(ADM_CENTRAL)),
   UNIDADE_GESTORA      VARCHAR2(1)                    default 'N' not null
      constraint CKC_EOUNI_GEST
               check (UNIDADE_GESTORA in ('S','N') and UNIDADE_GESTORA = upper(UNIDADE_GESTORA)),
   UNIDADE_PAGADORA     VARCHAR2(1)                    default 'N' not null
      constraint CKC_EOUNI_PAG
               check (UNIDADE_PAGADORA in ('S','N') and UNIDADE_PAGADORA = upper(UNIDADE_PAGADORA)),
   CODIGO               VARCHAR2(15),
   EMAIL                VARCHAR2(60),
   ATIVO                VARCHAR2(1)                    default 'S' not null
      constraint CKC_ATIVO_EO_UNIDA check (ATIVO in ('S','N') and ATIVO = upper(ATIVO)),
   EXTERNO              VARCHAR2(1)                    default 'N' not null
      constraint CKC_EXTERNO_EO_UNIDA check (EXTERNO in ('S','N') and EXTERNO = upper(EXTERNO)),
   constraint PK_EO_UNIDADE primary key (SQ_UNIDADE)
);

comment on table EO_UNIDADE is
'Unidades organizacionais';

comment on column EO_UNIDADE.SQ_UNIDADE is
'Chave de EO_UNIDADE. Indica a que unidade o registro está ligado.';

comment on column EO_UNIDADE.SQ_UNIDADE_PAI is
'Chave de EO_UNIDADE. Auto-relacionamento da tabela.';

comment on column EO_UNIDADE.SQ_UNIDADE_GESTORA is
'Chave de EO_UNIDADE. Unidade gestora dos bens patrimoniais da unidade. Auto-relacionamento.';

comment on column EO_UNIDADE.SQ_UNID_PAGADORA is
'Chave de EO_UNIDADE. Unidade responsável pelas despesas da unidade. Auto-relacionamento.';

comment on column EO_UNIDADE.SQ_AREA_ATUACAO is
'Chave de EO_AREA_ATUACAO. Área de atuação da unidade.';

comment on column EO_UNIDADE.SQ_TIPO_UNIDADE is
'Chave de EO_TIPO_UNIDADE. Indica a que tipo de unidade o registro está ligado.';

comment on column EO_UNIDADE.SQ_PESSOA_ENDERECO is
'Chave de CO_PESSOA_ENDERECO. Endereço da organização à qual a unidade está vinculada. Chave de CO_PESSOA_ENDERECO.';

comment on column EO_UNIDADE.SQ_PESSOA is
'Chave de CO_PESSOA. Indica a que cliente o registro está ligado.';

comment on column EO_UNIDADE.NOME is
'Nome da unidade.';

comment on column EO_UNIDADE.SIGLA is
'Sigla da unidade.';

comment on column EO_UNIDADE.ORDEM is
'Número de ordem da unidade para listagem.';

comment on column EO_UNIDADE.INFORMAL is
'Indica se a unidade faz parte da estrutura formal da organização.';

comment on column EO_UNIDADE.VINCULADA is
'Indica se a unidade é, na verdade, um órgão vinculado à organização.';

comment on column EO_UNIDADE.ADM_CENTRAL is
'Indica se a unidade faz parte da administração central da organização.';

comment on column EO_UNIDADE.UNIDADE_GESTORA is
'Indica se a unidade é responsável por bens patrimoniais da organização (depósito).';

comment on column EO_UNIDADE.UNIDADE_PAGADORA is
'Indica se a unidade é um centro de custo da organização.';

comment on column EO_UNIDADE.CODIGO is
'Código livre utilizado pela organização para identificar a unidade.';

comment on column EO_UNIDADE.EMAIL is
'e-Mail da unidade.';

comment on column EO_UNIDADE.ATIVO is
'Indica se a unidade está ativa.';

comment on column EO_UNIDADE.EXTERNO is
'Indica se a unidade é externa à organização. Só aparece em listagens do protocolo.';

/*==============================================================*/
/* Index: IN_EOUNI_PAI                                          */
/*==============================================================*/
create index IN_EOUNI_PAI on EO_UNIDADE (
   SQ_UNIDADE_PAI ASC,
   SQ_UNIDADE ASC
);

/*==============================================================*/
/* Index: IN_EOUNI_GESTORA                                      */
/*==============================================================*/
create index IN_EOUNI_GESTORA on EO_UNIDADE (
   SQ_UNIDADE_GESTORA ASC,
   SQ_UNIDADE ASC
);

/*==============================================================*/
/* Index: IN_EOUNI_PAGADORA                                     */
/*==============================================================*/
create index IN_EOUNI_PAGADORA on EO_UNIDADE (
   SQ_UNID_PAGADORA ASC,
   SQ_UNIDADE ASC
);

/*==============================================================*/
/* Index: IN_EOUNI_ENDERECO                                     */
/*==============================================================*/
create index IN_EOUNI_ENDERECO on EO_UNIDADE (
   SQ_PESSOA_ENDERECO ASC,
   SQ_UNIDADE ASC
);

/*==============================================================*/
/* Index: IN_EOUNI_AREA                                         */
/*==============================================================*/
create index IN_EOUNI_AREA on EO_UNIDADE (
   SQ_AREA_ATUACAO ASC,
   SQ_UNIDADE ASC
);

/*==============================================================*/
/* Index: IN_EOUNI_TIPO_UNID                                    */
/*==============================================================*/
create index IN_EOUNI_TIPO_UNID on EO_UNIDADE (
   SQ_TIPO_UNIDADE ASC,
   SQ_UNIDADE ASC
);

/*==============================================================*/
/* Index: IN_EOUNI_NOME                                         */
/*==============================================================*/
create index IN_EOUNI_NOME on EO_UNIDADE (
   NOME ASC,
   SQ_UNIDADE ASC
);

/*==============================================================*/
/* Index: IN_EOUNI_SIGLA                                        */
/*==============================================================*/
create index IN_EOUNI_SIGLA on EO_UNIDADE (
   SIGLA ASC,
   SQ_UNIDADE ASC
);

/*==============================================================*/
/* Index: IN_EOUNI_ORDEM                                        */
/*==============================================================*/
create index IN_EOUNI_ORDEM on EO_UNIDADE (
   ORDEM ASC,
   SQ_UNIDADE ASC
);

/*==============================================================*/
/* Index: IN_EOUNI_ATIVO                                        */
/*==============================================================*/
create index IN_EOUNI_ATIVO on EO_UNIDADE (
   ATIVO ASC,
   SQ_UNIDADE ASC
);

/*==============================================================*/
/* Index: IN_EOUNI_CLIENTE                                      */
/*==============================================================*/
create index IN_EOUNI_CLIENTE on EO_UNIDADE (
   SQ_PESSOA ASC,
   SQ_UNIDADE ASC
);

/*==============================================================*/
/* Index: IN_EOUNI_EXTERNO                                      */
/*==============================================================*/
create index IN_EOUNI_EXTERNO on EO_UNIDADE (
   SQ_PESSOA ASC,
   EXTERNO ASC,
   SQ_UNIDADE ASC
);

/*==============================================================*/
/* Table: EO_UNIDADE_ARQUIVO                                    */
/*==============================================================*/
create table EO_UNIDADE_ARQUIVO  (
   SQ_UNIDADE           NUMBER(10)                      not null,
   SQ_SIW_ARQUIVO       NUMBER(18)                      not null,
   ORDEM                NUMBER(4)                      default 0 not null,
   constraint PK_EO_UNIDADE_ARQUIVO primary key (SQ_UNIDADE, SQ_SIW_ARQUIVO)
);

comment on table EO_UNIDADE_ARQUIVO is
'Registra os arquivos anexados para a unidade.';

comment on column EO_UNIDADE_ARQUIVO.SQ_UNIDADE is
'Chave de EO_UNIDADE. Indica a que unidade o registro está ligado.';

comment on column EO_UNIDADE_ARQUIVO.SQ_SIW_ARQUIVO is
'Chave de SIW_ARQUIVO. Indica a que arquivo o registro está ligado.';

comment on column EO_UNIDADE_ARQUIVO.ORDEM is
'Número de ordem do registro nas listagens.';

/*==============================================================*/
/* Index: IN_EOUNIARQ_INV                                       */
/*==============================================================*/
create index IN_EOUNIARQ_INV on EO_UNIDADE_ARQUIVO (
   SQ_SIW_ARQUIVO ASC,
   SQ_UNIDADE ASC
);

/*==============================================================*/
/* Table: EO_UNIDADE_RESP                                       */
/*==============================================================*/
create table EO_UNIDADE_RESP  (
   SQ_UNIDADE_RESP      NUMBER(18)                      not null,
   SQ_UNIDADE           number(10)                      not null,
   SQ_PESSOA            NUMBER(18)                      not null,
   TIPO_RESPONS         VARCHAR2(1)                    default 'T' not null
      constraint CKC_EOUNIRES_TPRES
               check (TIPO_RESPONS in ('T','S') and TIPO_RESPONS = upper(TIPO_RESPONS)),
   INICIO               DATE                            not null,
   FIM                  DATE,
   constraint PK_EO_UNIDADE_RESP primary key (SQ_UNIDADE_RESP)
);

comment on table EO_UNIDADE_RESP is
'Armazena o histórico de ocupação da chefia titular e substituta de uma unidade.';

comment on column EO_UNIDADE_RESP.SQ_UNIDADE_RESP is
'Chave de EO_UNIDADE_RESP.';

comment on column EO_UNIDADE_RESP.SQ_UNIDADE is
'Chave de EO_UNIDADE. Indica a que unidade o registro está ligado.';

comment on column EO_UNIDADE_RESP.SQ_PESSOA is
'Chave de CO_PESSOA. Indica a que pessoa o registro está ligado.';

comment on column EO_UNIDADE_RESP.TIPO_RESPONS is
'Indica se a pessoa é titular ou substituto da unidade.';

comment on column EO_UNIDADE_RESP.INICIO is
'Início da responsabilidade.';

comment on column EO_UNIDADE_RESP.FIM is
'Fim da responsabilidade.';

/*==============================================================*/
/* Index: IN_EOUNIRES_PESSOA                                    */
/*==============================================================*/
create index IN_EOUNIRES_PESSOA on EO_UNIDADE_RESP (
   SQ_PESSOA ASC,
   SQ_UNIDADE ASC
);

/*==============================================================*/
/* Index: IN_EOUNIRES_INICIO                                    */
/*==============================================================*/
create index IN_EOUNIRES_INICIO on EO_UNIDADE_RESP (
   INICIO ASC,
   SQ_UNIDADE ASC
);

/*==============================================================*/
/* Index: IN_EOUNIRES_FIM                                       */
/*==============================================================*/
create index IN_EOUNIRES_FIM on EO_UNIDADE_RESP (
   FIM ASC,
   SQ_UNIDADE ASC
);

/*==============================================================*/
/* Index: IN_EOUNIRES_TIPO                                      */
/*==============================================================*/
create index IN_EOUNIRES_TIPO on EO_UNIDADE_RESP (
   TIPO_RESPONS ASC,
   SQ_UNIDADE ASC,
   SQ_PESSOA ASC
);

/*==============================================================*/
/* Index: IN_EOUNIRES_UNID                                      */
/*==============================================================*/
create index IN_EOUNIRES_UNID on EO_UNIDADE_RESP (
   SQ_UNIDADE ASC,
   SQ_UNIDADE_RESP ASC
);

/*==============================================================*/
/* Table: GD_DEMANDA                                            */
/*==============================================================*/
create table GD_DEMANDA  (
   SQ_SIW_SOLICITACAO   NUMBER(18)                      not null,
   SQ_UNIDADE_RESP      NUMBER(10),
   SQ_DEMANDA_PAI       NUMBER(18),
   SQ_SIW_RESTRICAO     NUMBER(18),
   SQ_DEMANDA_TIPO      NUMBER(18),
   RESPONSAVEL          NUMBER(18),
   ASSUNTO              VARCHAR2(2096),
   PRIORIDADE           NUMBER(2),
   AVISO_PROX_CONC      VARCHAR2(1)                    default 'N' not null
      constraint CKC_GDDEM_AVISO check (AVISO_PROX_CONC in ('S','N') and AVISO_PROX_CONC = upper(AVISO_PROX_CONC)),
   DIAS_AVISO           NUMBER(3)                      default 0 not null,
   INICIO_REAL          DATE,
   FIM_REAL             DATE,
   CONCLUIDA            VARCHAR2(1)                    default 'N' not null
      constraint CKC_GDDEM_CONC check (CONCLUIDA in ('S','N') and CONCLUIDA = upper(CONCLUIDA)),
   DATA_CONCLUSAO       DATE,
   NOTA_CONCLUSAO       VARCHAR2(2005),
   CUSTO_REAL           NUMBER(18,2)                   default 0 not null,
   PROPONENTE           VARCHAR2(90),
   ORDEM                NUMBER(3)                      default 0 not null,
   RECEBIMENTO          DATE,
   LIMITE_CONCLUSAO     DATE,
   constraint PK_GD_DEMANDA primary key (SQ_SIW_SOLICITACAO)
);

comment on table GD_DEMANDA is
'Registra informações cadastrais da demanda.';

comment on column GD_DEMANDA.SQ_SIW_SOLICITACAO is
'Chave de SIW_SOLICITACAO indicando a que solicitação o registro está ligado.';

comment on column GD_DEMANDA.SQ_UNIDADE_RESP is
'Chave de EO_UNIDADE. Indica a unidade responsável pelo monitoramento da demanda.';

comment on column GD_DEMANDA.SQ_DEMANDA_PAI is
'Chave de GD_DEMANDA. Usado APENAS para vinculação de um atividade a outra.';

comment on column GD_DEMANDA.SQ_SIW_RESTRICAO is
'Chave de SIW_RESTRICAO. Indica a que risco a tarefa está vinculada.';

comment on column GD_DEMANDA.SQ_DEMANDA_TIPO is
'Chave de GD_DEMANDA_TIPO. Indica o tipo da demanda eventual.';

comment on column GD_DEMANDA.RESPONSAVEL is
'Chave de CO_PESSOA. Indica o responsável pela execução da demanda.';

comment on column GD_DEMANDA.ASSUNTO is
'Assunto ou ementa da demanda. Será usado para recuperação textual.';

comment on column GD_DEMANDA.PRIORIDADE is
'Registra a prioridade da demanda. Quanto menor o número, mais alta a prioridade.';

comment on column GD_DEMANDA.AVISO_PROX_CONC is
'Indica se  necessrio avisar a proximidade da data limite para conclusão da demanda.';

comment on column GD_DEMANDA.DIAS_AVISO is
'Se o campo AVISO_PROX_CONC igual a S, indica o número de dias a partir do qual devem ser enviados os avisos por e-mail.';

comment on column GD_DEMANDA.INICIO_REAL is
'Início real da demanda.';

comment on column GD_DEMANDA.FIM_REAL is
'Fim real da demanda.';

comment on column GD_DEMANDA.CONCLUIDA is
'Indica se a demanda está concluída ou não.';

comment on column GD_DEMANDA.DATA_CONCLUSAO is
'Data informada pelo usuário.';

comment on column GD_DEMANDA.NOTA_CONCLUSAO is
'Observações relativas à conclusão da demanda.';

comment on column GD_DEMANDA.CUSTO_REAL is
'Custo real dispendido com o atendimento da demanda.';

comment on column GD_DEMANDA.PROPONENTE is
'Proponente da demanda. Texto livre.';

comment on column GD_DEMANDA.ORDEM is
'Indica o número de ordem a ser utilizado pelas rotinas de visualização.';

comment on column GD_DEMANDA.RECEBIMENTO is
'Data de recebimento da demanda.';

comment on column GD_DEMANDA.LIMITE_CONCLUSAO is
'Limite para conclusão da demanda.';

/*==============================================================*/
/* Index: IN_GDDEM_UNID                                         */
/*==============================================================*/
create index IN_GDDEM_UNID on GD_DEMANDA (
   SQ_UNIDADE_RESP ASC,
   SQ_SIW_SOLICITACAO ASC
);

/*==============================================================*/
/* Index: IN_GDDEM_PRIOR                                        */
/*==============================================================*/
create index IN_GDDEM_PRIOR on GD_DEMANDA (
   PRIORIDADE ASC,
   SQ_SIW_SOLICITACAO ASC
);

/*==============================================================*/
/* Index: IN_GDDEM_INI                                          */
/*==============================================================*/
create index IN_GDDEM_INI on GD_DEMANDA (
   INICIO_REAL ASC,
   SQ_SIW_SOLICITACAO ASC
);

/*==============================================================*/
/* Index: IN_GDDEM_FIM                                          */
/*==============================================================*/
create index IN_GDDEM_FIM on GD_DEMANDA (
   FIM_REAL ASC,
   SQ_SIW_SOLICITACAO ASC
);

/*==============================================================*/
/* Index: IN_GDDEM_CONC                                         */
/*==============================================================*/
create index IN_GDDEM_CONC on GD_DEMANDA (
   CONCLUIDA ASC,
   SQ_SIW_SOLICITACAO ASC
);

/*==============================================================*/
/* Index: IN_GDDEM_CUSTO                                        */
/*==============================================================*/
create index IN_GDDEM_CUSTO on GD_DEMANDA (
   CUSTO_REAL ASC,
   SQ_SIW_SOLICITACAO ASC
);

/*==============================================================*/
/* Index: IN_GDDEM_DTCONC                                       */
/*==============================================================*/
create index IN_GDDEM_DTCONC on GD_DEMANDA (
   DATA_CONCLUSAO ASC,
   SQ_SIW_SOLICITACAO ASC
);

/*==============================================================*/
/* Index: IN_GDDEM_PROPON                                       */
/*==============================================================*/
create index IN_GDDEM_PROPON on GD_DEMANDA (
   PROPONENTE ASC,
   SQ_SIW_SOLICITACAO ASC
);

/*==============================================================*/
/* Index: IN_GDDEM_PAI                                          */
/*==============================================================*/
create index IN_GDDEM_PAI on GD_DEMANDA (
   SQ_DEMANDA_PAI ASC,
   SQ_SIW_SOLICITACAO ASC
);

/*==============================================================*/
/* Index: IN_GDDEM_RESTRICAO                                    */
/*==============================================================*/
create index IN_GDDEM_RESTRICAO on GD_DEMANDA (
   SQ_SIW_RESTRICAO ASC,
   SQ_SIW_SOLICITACAO ASC
);

/*==============================================================*/
/* Index: IN_GDDEM_TIPO                                         */
/*==============================================================*/
create index IN_GDDEM_TIPO on GD_DEMANDA (
   SQ_DEMANDA_TIPO ASC,
   SQ_SIW_SOLICITACAO ASC
);

/*==============================================================*/
/* Index: IN_GDDEM_RESP                                         */
/*==============================================================*/
create index IN_GDDEM_RESP on GD_DEMANDA (
   RESPONSAVEL ASC,
   SQ_SIW_SOLICITACAO ASC
);

/*==============================================================*/
/* Index: IN_GDDEM_LIMITE                                       */
/*==============================================================*/
create index IN_GDDEM_LIMITE on GD_DEMANDA (
   LIMITE_CONCLUSAO ASC,
   SQ_SIW_SOLICITACAO ASC
);

/*==============================================================*/
/* Index: IN_GDDEM_RECEB                                        */
/*==============================================================*/
create index IN_GDDEM_RECEB on GD_DEMANDA (
   RECEBIMENTO ASC,
   SQ_SIW_SOLICITACAO ASC
);

/*==============================================================*/
/* Table: GD_DEMANDA_ENVOLV                                     */
/*==============================================================*/
create table GD_DEMANDA_ENVOLV  (
   SQ_UNIDADE           number(10)                      not null,
   SQ_SIW_SOLICITACAO   NUMBER(18)                      not null,
   PAPEL                VARCHAR2(2000)                  not null,
   constraint PK_GD_DEMANDA_ENVOLV primary key (SQ_UNIDADE, SQ_SIW_SOLICITACAO)
);

comment on table GD_DEMANDA_ENVOLV is
'Registra as unidades envolvidas no atendimento da demanda.';

comment on column GD_DEMANDA_ENVOLV.SQ_UNIDADE is
'Chave de EO_UNIDADE. Indica a que unidade o registro está ligado.';

comment on column GD_DEMANDA_ENVOLV.SQ_SIW_SOLICITACAO is
'Chave de SIW_SOLICITACAO indicando a que solicitação o registro está ligado.';

comment on column GD_DEMANDA_ENVOLV.PAPEL is
'Papel cumprido pela unidade envolvida.';

/*==============================================================*/
/* Index: IN_GDDEMENV_INVERSA                                   */
/*==============================================================*/
create index IN_GDDEMENV_INVERSA on GD_DEMANDA_ENVOLV (
   SQ_UNIDADE ASC
);

/*==============================================================*/
/* Table: GD_DEMANDA_INTERES                                    */
/*==============================================================*/
create table GD_DEMANDA_INTERES  (
   SQ_PESSOA            NUMBER(18)                      not null,
   SQ_SIW_SOLICITACAO   NUMBER(18)                      not null,
   TIPO_VISAO           NUMBER(1)                       not null
      constraint CKC_TIPO_VISAO_GD_DEMAN check (TIPO_VISAO in (0,1,2)),
   ENVIA_EMAIL          VARCHAR2(1)                    default 'N' not null
      constraint CKC_GDDEMINT_MAIL check (ENVIA_EMAIL in ('S','N') and ENVIA_EMAIL = upper(ENVIA_EMAIL)),
   constraint PK_GD_DEMANDA_INTERES primary key (SQ_PESSOA, SQ_SIW_SOLICITACAO)
);

comment on table GD_DEMANDA_INTERES is
'Registra os interessados pela demanda e que tipo de informações eles podem receber ou visualizar.';

comment on column GD_DEMANDA_INTERES.SQ_PESSOA is
'Chave de CO_PESSOA. Indica a que pessoa o registro está ligado.';

comment on column GD_DEMANDA_INTERES.SQ_SIW_SOLICITACAO is
'Chave de SIW_SOLICITACAO indicando a que solicitação o registro está ligado.';

comment on column GD_DEMANDA_INTERES.TIPO_VISAO is
'Indica a visão que a pessoa pode ter dessa demanda.';

comment on column GD_DEMANDA_INTERES.ENVIA_EMAIL is
'Indica se deve ser enviado e-mail ao interessado quando houver alguma ocorrência na demanda.';

/*==============================================================*/
/* Index: IN_GDDEMINT_INVERSA                                   */
/*==============================================================*/
create index IN_GDDEMINT_INVERSA on GD_DEMANDA_INTERES (
   SQ_SIW_SOLICITACAO ASC,
   SQ_PESSOA ASC
);

/*==============================================================*/
/* Index: IN_GDDEMINT_MAIL                                      */
/*==============================================================*/
create index IN_GDDEMINT_MAIL on GD_DEMANDA_INTERES (
   ENVIA_EMAIL ASC,
   SQ_PESSOA ASC,
   SQ_SIW_SOLICITACAO ASC
);

/*==============================================================*/
/* Table: GD_DEMANDA_LOG                                        */
/*==============================================================*/
create table GD_DEMANDA_LOG  (
   SQ_DEMANDA_LOG       NUMBER(18)                      not null,
   SQ_SIW_SOLICITACAO   NUMBER(18)                      not null,
   SQ_SIW_SOLIC_LOG     NUMBER(18),
   CADASTRADOR          NUMBER(18)                      not null,
   DESTINATARIO         NUMBER(18),
   DATA_INCLUSAO        DATE                            not null,
   OBSERVACAO           VARCHAR2(2088),
   DESPACHO             VARCHAR2(2000),
   constraint PK_GD_DEMANDA_LOG primary key (SQ_DEMANDA_LOG)
);

comment on table GD_DEMANDA_LOG is
'Registra o histórico da demanda';

comment on column GD_DEMANDA_LOG.SQ_DEMANDA_LOG is
'Chave de GD_DEMANDA_LOG.';

comment on column GD_DEMANDA_LOG.SQ_SIW_SOLICITACAO is
'Chave de SIW_SOLICITACAO indicando a que solicitação o registro está ligado.';

comment on column GD_DEMANDA_LOG.SQ_SIW_SOLIC_LOG is
'Chave do log da solicitação, informada apenas quando for envio entre fases.';

comment on column GD_DEMANDA_LOG.CADASTRADOR is
'Responsável pela inserção do histórico.';

comment on column GD_DEMANDA_LOG.DESTINATARIO is
'Pessoa à qual a demanda está sendo encaminhada.';

comment on column GD_DEMANDA_LOG.DATA_INCLUSAO is
'Data de inclusão do registro, gerado pelo sistema.';

comment on column GD_DEMANDA_LOG.OBSERVACAO is
'Observações inseridas pelo usuário.';

comment on column GD_DEMANDA_LOG.DESPACHO is
'Orientação ao destinatário sobre as ações necessárias.';

/*==============================================================*/
/* Index: IN_GDDEMLOG_DEM                                       */
/*==============================================================*/
create index IN_GDDEMLOG_DEM on GD_DEMANDA_LOG (
   SQ_SIW_SOLICITACAO ASC,
   SQ_DEMANDA_LOG ASC
);

/*==============================================================*/
/* Index: IN_GDDEMLOG_DATA                                      */
/*==============================================================*/
create index IN_GDDEMLOG_DATA on GD_DEMANDA_LOG (
   DATA_INCLUSAO ASC,
   SQ_DEMANDA_LOG ASC
);

/*==============================================================*/
/* Index: IN_GDDEMLOG_CADAST                                    */
/*==============================================================*/
create index IN_GDDEMLOG_CADAST on GD_DEMANDA_LOG (
   CADASTRADOR ASC,
   SQ_DEMANDA_LOG ASC
);

/*==============================================================*/
/* Index: IN_GDDEMLOG_DEST                                      */
/*==============================================================*/
create index IN_GDDEMLOG_DEST on GD_DEMANDA_LOG (
   DESTINATARIO ASC,
   SQ_DEMANDA_LOG ASC
);

/*==============================================================*/
/* Index: IN_GDDEMLOG_SIWLOG                                    */
/*==============================================================*/
create index IN_GDDEMLOG_SIWLOG on GD_DEMANDA_LOG (
   SQ_SIW_SOLIC_LOG ASC,
   SQ_DEMANDA_LOG ASC
);

/*==============================================================*/
/* Table: GD_DEMANDA_LOG_ARQ                                    */
/*==============================================================*/
create table GD_DEMANDA_LOG_ARQ  (
   SQ_DEMANDA_LOG       NUMBER(18)                      not null,
   SQ_SIW_ARQUIVO       NUMBER(18)                      not null,
   constraint PK_GD_DEMANDA_LOG_ARQ primary key (SQ_DEMANDA_LOG, SQ_SIW_ARQUIVO)
);

comment on table GD_DEMANDA_LOG_ARQ is
'Vincula arquivos a logs de demanda.';

comment on column GD_DEMANDA_LOG_ARQ.SQ_DEMANDA_LOG is
'Chave de GD_DEMANDA_LOG. Indica a que registro o arquivo está ligado.';

comment on column GD_DEMANDA_LOG_ARQ.SQ_SIW_ARQUIVO is
'Chave de SIW_ARQUIVO. Indica a que arquivo o registro está ligado.';

/*==============================================================*/
/* Index: IN_GDDEMLOGARQ_INV                                    */
/*==============================================================*/
create index IN_GDDEMLOGARQ_INV on GD_DEMANDA_LOG_ARQ (
   SQ_SIW_ARQUIVO ASC,
   SQ_DEMANDA_LOG ASC
);

/*==============================================================*/
/* Table: GD_DEMANDA_TIPO                                       */
/*==============================================================*/
create table GD_DEMANDA_TIPO  (
   SQ_DEMANDA_TIPO      NUMBER(18)                      not null,
   CLIENTE              NUMBER(18)                      not null,
   NOME                 VARCHAR2(60)                    not null,
   SIGLA                VARCHAR2(20)                    not null,
   DESCRICAO            VARCHAR2(500),
   SQ_UNIDADE           NUMBER(10),
   ATIVO                VARCHAR2(1)                    default 'S' not null
      constraint CKC_ATIVO_GD_DEMAN check (ATIVO in ('S','N') and ATIVO = upper(ATIVO)),
   REUNIAO              VARCHAR2(1)                    default 'N' not null
      constraint CKC_REUNIAO_GD_DEMAN check (REUNIAO in ('S','N') and REUNIAO = upper(REUNIAO)),
   constraint PK_GD_DEMANDA_TIPO primary key (SQ_DEMANDA_TIPO)
);

comment on table GD_DEMANDA_TIPO is
'Registra os tipos de demandas eventuais.';

comment on column GD_DEMANDA_TIPO.SQ_DEMANDA_TIPO is
'Chave de GD_DEMANDA_TIPO.';

comment on column GD_DEMANDA_TIPO.CLIENTE is
'Chave de CO_PESSOA. Indica a que cliente o registro pertence.';

comment on column GD_DEMANDA_TIPO.NOME is
'Nome do tipo da demanda.';

comment on column GD_DEMANDA_TIPO.SIGLA is
'Sigla do tipo da demanda.';

comment on column GD_DEMANDA_TIPO.DESCRICAO is
'Descrição do tipo da demanda.';

comment on column GD_DEMANDA_TIPO.SQ_UNIDADE is
'Chave de EO_UNIDADE. Indica a unidade responsável pela execução de demandas deste tipo.';

comment on column GD_DEMANDA_TIPO.ATIVO is
'Indica se este tipo pode ser vinculado a novos registros.';

comment on column GD_DEMANDA_TIPO.REUNIAO is
'Indica se o tipo deve ter tratamento de reunião: S - Sim, N - Não.';

/*==============================================================*/
/* Index: IN_GDDEMTIP_NOME                                      */
/*==============================================================*/
create unique index IN_GDDEMTIP_NOME on GD_DEMANDA_TIPO (
   CLIENTE ASC,
   NOME ASC
);

/*==============================================================*/
/* Index: IN_GDDEMTIP_SIGLA                                     */
/*==============================================================*/
create unique index IN_GDDEMTIP_SIGLA on GD_DEMANDA_TIPO (
   CLIENTE ASC,
   SIGLA ASC
);

/*==============================================================*/
/* Index: IN_GDDEMTIP_CLIENTE                                   */
/*==============================================================*/
create index IN_GDDEMTIP_CLIENTE on GD_DEMANDA_TIPO (
   CLIENTE ASC,
   SQ_DEMANDA_TIPO ASC
);

/*==============================================================*/
/* Index: IN_GDDEMTIP_ATIVO                                     */
/*==============================================================*/
create index IN_GDDEMTIP_ATIVO on GD_DEMANDA_TIPO (
   CLIENTE ASC,
   ATIVO ASC,
   SQ_DEMANDA_TIPO ASC
);

/*==============================================================*/
/* Table: PE_HORIZONTE                                          */
/*==============================================================*/
create table PE_HORIZONTE  (
   SQ_PEHORIZONTE       NUMBER(18)                      not null,
   CLIENTE              NUMBER(18)                      not null,
   NOME                 VARCHAR2(30)                    not null,
   ATIVO                VARCHAR2(1)                    default 'S' not null
      constraint CKC_PEHOR_ATIVO check (ATIVO in ('S','N') and ATIVO = upper(ATIVO)),
   constraint PK_PE_HORIZONTE primary key (SQ_PEHORIZONTE)
);

comment on table PE_HORIZONTE is
'Domínio de valores para o horizonte temporal de um programa.';

comment on column PE_HORIZONTE.SQ_PEHORIZONTE is
'Chave de PE_HORIZONTE.';

comment on column PE_HORIZONTE.CLIENTE is
'Chave de CO_PESSOA. Indica a que cliente o horizonte está ligado.';

comment on column PE_HORIZONTE.NOME is
'Nome do horizonte do programa.';

comment on column PE_HORIZONTE.ATIVO is
'Indica se o registro pode ser associado a novos registros.';

/*==============================================================*/
/* Index: IN_PEHOR_CLIENTE                                      */
/*==============================================================*/
create index IN_PEHOR_CLIENTE on PE_HORIZONTE (
   CLIENTE ASC,
   SQ_PEHORIZONTE ASC
);

/*==============================================================*/
/* Table: PE_NATUREZA                                           */
/*==============================================================*/
create table PE_NATUREZA  (
   SQ_PENATUREZA        NUMBER(18)                      not null,
   CLIENTE              NUMBER(18)                      not null,
   NOME                 VARCHAR2(30)                    not null,
   ATIVO                VARCHAR2(1)                    default 'S' not null
      constraint CKC_PENAT_ATIVO check (ATIVO in ('S','N') and ATIVO = upper(ATIVO)),
   constraint PK_PE_NATUREZA primary key (SQ_PENATUREZA)
);

comment on table PE_NATUREZA is
'Domnio de valores para a natureza do programa';

comment on column PE_NATUREZA.SQ_PENATUREZA is
'Chave de PE_NATUREZA.';

comment on column PE_NATUREZA.CLIENTE is
'Chave de CO_PESSOA. Indica a que cliente o registro está ligado.';

comment on column PE_NATUREZA.NOME is
'Nome da natureza do programa.';

comment on column PE_NATUREZA.ATIVO is
'Indica se o registro pode ser associado a novos registros.';

/*==============================================================*/
/* Index: IN_PENAT_CLIENTE                                      */
/*==============================================================*/
create index IN_PENAT_CLIENTE on PE_NATUREZA (
   CLIENTE ASC,
   SQ_PENATUREZA ASC
);

/*==============================================================*/
/* Table: PE_OBJETIVO                                           */
/*==============================================================*/
create table PE_OBJETIVO  (
   SQ_PEOBJETIVO        NUMBER(18)                      not null,
   CLIENTE              NUMBER(18)                      not null,
   SQ_PLANO             NUMBER(18)                      not null,
   NOME                 VARCHAR2(100)                   not null,
   SIGLA                VARCHAR2(10)                    not null,
   DESCRICAO            VARCHAR2(4000)                  not null,
   ATIVO                VARCHAR2(1)                    default 'S' not null
      constraint CKC_ATIVO_PE_OBJET check (ATIVO in ('S','N') and ATIVO = upper(ATIVO)),
   CODIGO_EXTERNO       VARCHAR2(30),
   constraint PK_PE_OBJETIVO primary key (SQ_PEOBJETIVO)
);

comment on table PE_OBJETIVO is
'Registra os objetivos do planejamento estratégico, sendo vinculado apenas ao nivel folha da tabela PE_GERAL.';

comment on column PE_OBJETIVO.SQ_PEOBJETIVO is
'Chave de SQ_PEOBJETIVO.';

comment on column PE_OBJETIVO.CLIENTE is
'Chave de CO_PESSOA. Indica a que cliente o objetivo está ligado.';

comment on column PE_OBJETIVO.SQ_PLANO is
'Chave de PE_PLANO. Indica a que plano estratégico o objetivo está ligado.';

comment on column PE_OBJETIVO.NOME is
'Nome do objetivo estratégico.';

comment on column PE_OBJETIVO.SIGLA is
'Sigla do objetivo estratégico.';

comment on column PE_OBJETIVO.DESCRICAO is
'Descrição do objetivo estratégico.';

comment on column PE_OBJETIVO.ATIVO is
'Indica se o objetivo estratégico está ativo.';

comment on column PE_OBJETIVO.CODIGO_EXTERNO is
'Código desse registro num sistema externo.';

/*==============================================================*/
/* Index: IN_PEOBJ_CLIENTE                                      */
/*==============================================================*/
create index IN_PEOBJ_CLIENTE on PE_OBJETIVO (
   CLIENTE ASC,
   SQ_PEOBJETIVO ASC
);

/*==============================================================*/
/* Index: IN_PEOBJ_PLANO                                        */
/*==============================================================*/
create index IN_PEOBJ_PLANO on PE_OBJETIVO (
   CLIENTE ASC,
   SQ_PLANO ASC,
   SQ_PEOBJETIVO ASC
);

/*==============================================================*/
/* Index: IN_PEOBJ_NOME                                         */
/*==============================================================*/
create index IN_PEOBJ_NOME on PE_OBJETIVO (
   CLIENTE ASC,
   NOME ASC,
   SQ_PEOBJETIVO ASC
);

/*==============================================================*/
/* Index: IN_PEOBJ_SIGLA                                        */
/*==============================================================*/
create index IN_PEOBJ_SIGLA on PE_OBJETIVO (
   CLIENTE ASC,
   SIGLA ASC,
   SQ_PEOBJETIVO ASC
);

/*==============================================================*/
/* Index: IN_PEOBJ_ATIVO                                        */
/*==============================================================*/
create index IN_PEOBJ_ATIVO on PE_OBJETIVO (
   CLIENTE ASC,
   ATIVO ASC,
   SQ_PEOBJETIVO ASC
);

/*==============================================================*/
/* Table: PE_PLANO                                              */
/*==============================================================*/
create table PE_PLANO  (
   SQ_PLANO             NUMBER(18)                      not null,
   CLIENTE              NUMBER(18)                      not null,
   SQ_PLANO_PAI         NUMBER(18),
   TITULO               VARCHAR2(100)                   not null,
   MISSAO               VARCHAR2(2000)                  not null,
   VALORES              VARCHAR2(2000)                  not null,
   VISAO_PRESENTE       VARCHAR2(2000)                  not null,
   VISAO_FUTURO         VARCHAR2(2000)                  not null,
   INICIO               DATE                            not null,
   FIM                  DATE                            not null,
   ATIVO                VARCHAR2(1)                    default 'S' not null
      constraint CKC_ATIVO_PE_PLANO check (ATIVO in ('S','N') and ATIVO = upper(ATIVO)),
   CODIGO_EXTERNO       VARCHAR2(30),
   constraint PK_PE_PLANO primary key (SQ_PLANO)
);

comment on table PE_PLANO is
'Registra os dados gerais do planejamento estratégico.';

comment on column PE_PLANO.SQ_PLANO is
'Chave de PE_PLANO.';

comment on column PE_PLANO.CLIENTE is
'Chave de CO_PESSOA. Indica a que cliente o planejamento está ligado.';

comment on column PE_PLANO.SQ_PLANO_PAI is
'Chave de PE_PLANO. Auto-relacionamento da tabela.';

comment on column PE_PLANO.TITULO is
'Título do planejamento estratégico.';

comment on column PE_PLANO.MISSAO is
'Missão (negócio) da organização durante a execução do planejamento estratégico.';

comment on column PE_PLANO.VALORES is
'Valores (crenças) da organização que o planejamento estratégico deve respeitar e ter como diretrizes.';

comment on column PE_PLANO.VISAO_PRESENTE is
'Visão presente da organização no momento da elaboração do planejamento estratégico.';

comment on column PE_PLANO.VISAO_FUTURO is
'Visão do futuro desejado para a organização ao final da execução do planejamento estratégico.';

comment on column PE_PLANO.INICIO is
'Data inicial do planejamento estratégico.';

comment on column PE_PLANO.FIM is
'Data final do planejamento estratégico.';

comment on column PE_PLANO.ATIVO is
'Indica se o item do planejamento estratégico está ativo.';

comment on column PE_PLANO.CODIGO_EXTERNO is
'Código desse registro num sistema externo.';

/*==============================================================*/
/* Index: IN_PEPLA_CLIENTE                                      */
/*==============================================================*/
create index IN_PEPLA_CLIENTE on PE_PLANO (
   CLIENTE ASC,
   SQ_PLANO ASC
);

/*==============================================================*/
/* Index: IN_PEPLA_PAI                                          */
/*==============================================================*/
create index IN_PEPLA_PAI on PE_PLANO (
   CLIENTE ASC,
   SQ_PLANO_PAI ASC,
   SQ_PLANO ASC
);

/*==============================================================*/
/* Index: IN_PEPLA_INICIO                                       */
/*==============================================================*/
create index IN_PEPLA_INICIO on PE_PLANO (
   CLIENTE ASC,
   INICIO ASC,
   SQ_PLANO ASC
);

/*==============================================================*/
/* Index: IN_PEPLA_FIM                                          */
/*==============================================================*/
create index IN_PEPLA_FIM on PE_PLANO (
   CLIENTE ASC,
   FIM ASC,
   SQ_PLANO ASC
);

/*==============================================================*/
/* Index: IN_PEPLA_TITULO                                       */
/*==============================================================*/
create index IN_PEPLA_TITULO on PE_PLANO (
   CLIENTE ASC,
   TITULO ASC,
   SQ_PLANO ASC
);

/*==============================================================*/
/* Table: PE_PLANO_ARQ                                          */
/*==============================================================*/
create table PE_PLANO_ARQ  (
   SQ_SIW_ARQUIVO       NUMBER(18)                      not null,
   SQ_PLANO             NUMBER(18)                      not null,
   constraint PK_PE_PLANO_ARQ primary key (SQ_SIW_ARQUIVO, SQ_PLANO)
);

comment on table PE_PLANO_ARQ is
'Registra os arquivos ligados a um item do planejamento estratégico';

comment on column PE_PLANO_ARQ.SQ_SIW_ARQUIVO is
'Chave de SIW_ARQUIVO. Indica a que arquivo o registro está ligado.';

comment on column PE_PLANO_ARQ.SQ_PLANO is
'Chave de PE_PLANO. Indica a que item do planejamento estratégico o registro está ligado.';

/*==============================================================*/
/* Index: IN_PEPLAARQ_INVERSA                                   */
/*==============================================================*/
create index IN_PEPLAARQ_INVERSA on PE_PLANO_ARQ (
   SQ_PLANO ASC,
   SQ_SIW_ARQUIVO ASC
);

/*==============================================================*/
/* Table: PE_PLANO_INDICADOR                                    */
/*==============================================================*/
create table PE_PLANO_INDICADOR  (
   SQ_PLANO_INDICADOR   NUMBER(18)                      not null,
   SQ_PLANO             NUMBER(18)                      not null,
   SQ_EOINDICADOR       NUMBER(18)                      not null,
   constraint PK_PE_PLANO_INDICADOR primary key (SQ_PLANO_INDICADOR)
);

comment on table PE_PLANO_INDICADOR is
'Registra os indicadores de um plano estratégico.';

comment on column PE_PLANO_INDICADOR.SQ_PLANO_INDICADOR is
'Chave de PE_PLANO_INDICADOR.';

comment on column PE_PLANO_INDICADOR.SQ_PLANO is
'Chave de PE_PLANO. Indica a que plano estratégico o registro está ligado.';

comment on column PE_PLANO_INDICADOR.SQ_EOINDICADOR is
'Chave de EO_INDICADOR. Indica a que indicador o registro está ligado.';

/*==============================================================*/
/* Index: IN_PEPLANIND_PLANO                                    */
/*==============================================================*/
create index IN_PEPLANIND_PLANO on PE_PLANO_INDICADOR (
   SQ_PLANO ASC,
   SQ_PLANO_INDICADOR ASC
);

/*==============================================================*/
/* Index: IN_PEPLANIND_INDICADOR                                */
/*==============================================================*/
create index IN_PEPLANIND_INDICADOR on PE_PLANO_INDICADOR (
   SQ_EOINDICADOR ASC,
   SQ_PLANO_INDICADOR ASC
);

/*==============================================================*/
/* Table: PE_PLANO_MENU                                         */
/*==============================================================*/
create table PE_PLANO_MENU  (
   SQ_PLANO             NUMBER(18)                      not null,
   SQ_MENU              NUMBER(18)                      not null,
   constraint PK_PE_PLANO_MENU primary key (SQ_PLANO, SQ_MENU)
);

comment on table PE_PLANO_MENU is
'Reqistra os serviços que podem vincular-se a planos estratégicos.';

comment on column PE_PLANO_MENU.SQ_PLANO is
'Chave de PE_PLANO. Indica a que plano o registro está ligado';

comment on column PE_PLANO_MENU.SQ_MENU is
'Chave de SIW_MENU. Indica a que serviço o registro está ligado.';

/*==============================================================*/
/* Index: IN_PEPLANMEN_INVERSA                                  */
/*==============================================================*/
create index IN_PEPLANMEN_INVERSA on PE_PLANO_MENU (
   SQ_MENU ASC,
   SQ_PLANO ASC
);

/*==============================================================*/
/* Table: PE_PROGRAMA                                           */
/*==============================================================*/
create table PE_PROGRAMA  (
   SQ_SIW_SOLICITACAO   NUMBER(18)                      not null,
   CLIENTE              NUMBER(18)                      not null,
   SQ_PEHORIZONTE       NUMBER(18)                      not null,
   SQ_PENATUREZA        NUMBER(18)                      not null,
   SQ_UNIDADE_RESP      NUMBER(10)                      not null,
   PUBLICO_ALVO         VARCHAR2(2000),
   ESTRATEGIA           VARCHAR2(2000),
   LN_PROGRAMA          VARCHAR2(120),
   SITUACAO_ATUAL       VARCHAR2(2000),
   EXEQUIVEL            VARCHAR2(1)                    default 'S' not null
      constraint CKC_EXEQUIVEL_PE_PROGR check (EXEQUIVEL in ('S','N') and EXEQUIVEL = upper(EXEQUIVEL)),
   JUSTIFICATIVA_INEXEQUIVEL VARCHAR2(2000),
   OUTRAS_MEDIDAS       VARCHAR2(2000),
   INICIO_REAL          DATE,
   FIM_REAL             DATE,
   CUSTO_REAL           NUMBER(18,2),
   NOTA_CONCLUSAO       VARCHAR2(2005),
   AVISO_PROX_CONC      VARCHAR2(1)                    default 'N' not null
      constraint CKC_PEPRO_AVISO check (AVISO_PROX_CONC in ('S','N') and AVISO_PROX_CONC = upper(AVISO_PROX_CONC)),
   DIAS_AVISO           NUMBER(3)                      default 0 not null,
   constraint PK_PE_PROGRAMA primary key (SQ_SIW_SOLICITACAO)
);

comment on table PE_PROGRAMA is
'Registra os programas do planejamento estratégico.';

comment on column PE_PROGRAMA.SQ_SIW_SOLICITACAO is
'Chave de SIW_SOLICITACAO. Indica a que solicitação o programa está ligado.';

comment on column PE_PROGRAMA.CLIENTE is
'Chave de CO_PESSOA. Indica a que cliente o programa est[a vinculado.';

comment on column PE_PROGRAMA.SQ_PEHORIZONTE is
'Chave de PE_PROGRAMA. Indica o horizonte temporal do programa.';

comment on column PE_PROGRAMA.SQ_PENATUREZA is
'Chave de PE_NATUREZA. Indica a natureza do programa.';

comment on column PE_PROGRAMA.SQ_UNIDADE_RESP is
'Chave de EO_UNIDADE. Indica a unidade responsável pelo monitoramento do programa.';

comment on column PE_PROGRAMA.PUBLICO_ALVO is
'Descrição do Público alvo do Programa';

comment on column PE_PROGRAMA.ESTRATEGIA is
'Estratégia de execuçao do programa.';

comment on column PE_PROGRAMA.LN_PROGRAMA is
'Link para um site específico do programa, se existir.';

comment on column PE_PROGRAMA.SITUACAO_ATUAL is
'Texto detalhando a situação atual do programa.';

comment on column PE_PROGRAMA.EXEQUIVEL is
'Indica se o programa está avaliado como passível de cumprimento ou não.';

comment on column PE_PROGRAMA.JUSTIFICATIVA_INEXEQUIVEL is
'Motivos que justificam o não atingimento dos objetivos do programa.';

comment on column PE_PROGRAMA.OUTRAS_MEDIDAS is
'Descrição das medidas necessárias ao cumprimento do objetivo.';

comment on column PE_PROGRAMA.INICIO_REAL is
'Início real do programa, informado na conclusão do programa.';

comment on column PE_PROGRAMA.FIM_REAL is
'Fim real do programa, informado na conclusão do programa.';

comment on column PE_PROGRAMA.CUSTO_REAL is
'Custo real do programa, informado na conclusão do programa.';

comment on column PE_PROGRAMA.NOTA_CONCLUSAO is
'Avaliação final do programa.';

comment on column PE_PROGRAMA.AVISO_PROX_CONC is
'Indica se é necessário avisar a proximidade da data final do programa.';

comment on column PE_PROGRAMA.DIAS_AVISO is
'Se o campo AVISO_PROX_CONC igual a S, indica o número de dias a partir do qual devem ser enviados os avisos por e-mail.';

/*==============================================================*/
/* Index: IN_PEPRO_UNIDADE                                      */
/*==============================================================*/
create index IN_PEPRO_UNIDADE on PE_PROGRAMA (
   SQ_UNIDADE_RESP ASC,
   SQ_SIW_SOLICITACAO ASC
);

/*==============================================================*/
/* Table: PE_PROGRAMA_LOG                                       */
/*==============================================================*/
create table PE_PROGRAMA_LOG  (
   SQ_PROGRAMA_LOG      NUMBER(18)                      not null,
   SQ_SIW_SOLICITACAO   NUMBER(18)                      not null,
   SQ_SIW_SOLIC_LOG     NUMBER(18),
   CADASTRADOR          NUMBER(18)                      not null,
   DESTINATARIO         NUMBER(18),
   DATA_INCLUSAO        DATE                           default sysdate not null,
   OBSERVACAO           VARCHAR2(2000),
   DESPACHO             VARCHAR2(2000),
   constraint PK_PE_PROGRAMA_LOG primary key (SQ_PROGRAMA_LOG)
);

comment on table PE_PROGRAMA_LOG is
'Registra o histórico da tramitação do programa.';

comment on column PE_PROGRAMA_LOG.SQ_PROGRAMA_LOG is
'Chave de PE_PROGRAMA_LOG.';

comment on column PE_PROGRAMA_LOG.SQ_SIW_SOLICITACAO is
'Chave de SIW_SOLICITACAO. Indica a que programa o registro está ligado.';

comment on column PE_PROGRAMA_LOG.SQ_SIW_SOLIC_LOG is
'Chave do log do acordo, informada apenas quando for envio entre fases.';

comment on column PE_PROGRAMA_LOG.CADASTRADOR is
'Chave de CO_PESSOA. Responsável pela inserção do histórico.';

comment on column PE_PROGRAMA_LOG.DESTINATARIO is
'Chave de CO_PESSOA. Pessoa à qual o acordo está sendo encaminhado.';

comment on column PE_PROGRAMA_LOG.DATA_INCLUSAO is
'Data de inclusão do registro, gerado pelo sistema.';

comment on column PE_PROGRAMA_LOG.OBSERVACAO is
'Observações inseridas pelo usuário.';

comment on column PE_PROGRAMA_LOG.DESPACHO is
'Orientação ao destinatário sobre as ações necessárias.';

/*==============================================================*/
/* Index: IN_PEPROLOG_PROGRAMA                                  */
/*==============================================================*/
create index IN_PEPROLOG_PROGRAMA on PE_PROGRAMA_LOG (
   SQ_SIW_SOLICITACAO ASC,
   SQ_PROGRAMA_LOG ASC
);

/*==============================================================*/
/* Index: IN_PEPROLOG_DATA                                      */
/*==============================================================*/
create index IN_PEPROLOG_DATA on PE_PROGRAMA_LOG (
   SQ_SIW_SOLICITACAO ASC,
   DATA_INCLUSAO ASC,
   SQ_PROGRAMA_LOG ASC
);

/*==============================================================*/
/* Index: IN_PEPROLOG_CADAST                                    */
/*==============================================================*/
create index IN_PEPROLOG_CADAST on PE_PROGRAMA_LOG (
   CADASTRADOR ASC,
   SQ_PROGRAMA_LOG ASC
);

/*==============================================================*/
/* Index: IN_PEPROLOG_DEST                                      */
/*==============================================================*/
create index IN_PEPROLOG_DEST on PE_PROGRAMA_LOG (
   DESTINATARIO ASC,
   SQ_PROGRAMA_LOG ASC
);

/*==============================================================*/
/* Index: IN_PEPROLOG_SIWLOG                                    */
/*==============================================================*/
create index IN_PEPROLOG_SIWLOG on PE_PROGRAMA_LOG (
   SQ_SIW_SOLIC_LOG ASC,
   SQ_PROGRAMA_LOG ASC
);

/*==============================================================*/
/* Table: PE_PROGRAMA_LOG_ARQ                                   */
/*==============================================================*/
create table PE_PROGRAMA_LOG_ARQ  (
   SQ_PROGRAMA_LOG      NUMBER(18)                      not null,
   SQ_SIW_ARQUIVO       NUMBER(18)                      not null,
   constraint PK_PE_PROGRAMA_LOG_ARQ primary key (SQ_PROGRAMA_LOG, SQ_SIW_ARQUIVO)
);

comment on table PE_PROGRAMA_LOG_ARQ is
'Vincula logs de solicitação a arquivos físicos.';

comment on column PE_PROGRAMA_LOG_ARQ.SQ_PROGRAMA_LOG is
'Chave de PE_PROGRAMA_LOG. Indica a que log o registro está ligado.';

comment on column PE_PROGRAMA_LOG_ARQ.SQ_SIW_ARQUIVO is
'Chave de SIW_ARQUIVO. Indica a que arquivo o registro está ligado.';

/*==============================================================*/
/* Index: IN_PEPROLOGARQ_INV                                    */
/*==============================================================*/
create index IN_PEPROLOGARQ_INV on PE_PROGRAMA_LOG_ARQ (
   SQ_SIW_ARQUIVO ASC,
   SQ_PROGRAMA_LOG ASC
);

/*==============================================================*/
/* Table: PE_UNIDADE                                            */
/*==============================================================*/
create table PE_UNIDADE  (
   SQ_UNIDADE           NUMBER(10)                      not null,
   CLIENTE              NUMBER(18)                      not null,
   DESCRICAO            VARCHAR2(2000)                  not null,
   PLANEJAMENTO         VARCHAR2(1)                    default 'S' not null
      constraint CKC_PLANEJAMENTO_PE_UNIDA check (PLANEJAMENTO in ('S','N') and PLANEJAMENTO = upper(PLANEJAMENTO)),
   EXECUCAO             VARCHAR2(1)                    default 'S' not null
      constraint CKC_EXECUCAO_PE_UNIDA check (EXECUCAO in ('S','N') and EXECUCAO = upper(EXECUCAO)),
   GESTAO_RECURSOS      VARCHAR2(1)                    default 'S' not null
      constraint CKC_GESTAO_RECURSOS_PE_UNIDA check (GESTAO_RECURSOS in ('S','N') and GESTAO_RECURSOS = upper(GESTAO_RECURSOS)),
   ATIVO                VARCHAR2(1)                    default 'S' not null
      constraint CKC_ATIVO_PE_UNIDA check (ATIVO in ('S','N') and ATIVO = upper(ATIVO)),
   constraint PK_PE_UNIDADE primary key (SQ_UNIDADE)
);

comment on table PE_UNIDADE is
'Registra as unidades responsáveis pelo monitoramento do planejamento estratégico.';

comment on column PE_UNIDADE.SQ_UNIDADE is
'Chave de EO_UNIDADE. Indica a que unidade o registro está vinculado.';

comment on column PE_UNIDADE.CLIENTE is
'Chave de EO_UNIDADE. Indica a que unidade o registro está ligado.';

comment on column PE_UNIDADE.DESCRICAO is
'Descrição do papel que a unidade cumpre no monitoramento do planejamento estratégico.';

comment on column PE_UNIDADE.PLANEJAMENTO is
'Indica se a unidade pode monitorar o planejamento estratégico.';

comment on column PE_UNIDADE.EXECUCAO is
'Indica se a unidade pode executar o planejamento estratégico.';

comment on column PE_UNIDADE.GESTAO_RECURSOS is
'Indica se a unidade é gestora de recursos.';

comment on column PE_UNIDADE.ATIVO is
'Indica se a unidade pode ser vinculada a novos registros.';

/*==============================================================*/
/* Index: IN_PEUNI_CLIENTE                                      */
/*==============================================================*/
create index IN_PEUNI_CLIENTE on PE_UNIDADE (
   CLIENTE ASC,
   ATIVO ASC,
   SQ_UNIDADE ASC
);

/*==============================================================*/
/* Table: PJ_COMENTARIO_ARQ                                     */
/*==============================================================*/
create table PJ_COMENTARIO_ARQ  (
   SQ_ETAPA_COMENTARIO  NUMBER(18)                      not null,
   SQ_SIW_ARQUIVO       NUMBER(18)                      not null,
   constraint PK_PJ_COMENTARIO_ARQ primary key (SQ_ETAPA_COMENTARIO, SQ_SIW_ARQUIVO)
);

comment on table PJ_COMENTARIO_ARQ is
'Registra os arquivos vinculados a um comentário';

comment on column PJ_COMENTARIO_ARQ.SQ_ETAPA_COMENTARIO is
'Chave de PJ_ETAPA_COMENTARIO.';

comment on column PJ_COMENTARIO_ARQ.SQ_SIW_ARQUIVO is
'Chave de SIW_ARQUIVO. Indica a que arquivo o registro está ligado.';

/*==============================================================*/
/* Index: IN_PJCOMARQ_INV                                       */
/*==============================================================*/
create index IN_PJCOMARQ_INV on PJ_COMENTARIO_ARQ (
   SQ_SIW_ARQUIVO ASC,
   SQ_ETAPA_COMENTARIO ASC
);

/*==============================================================*/
/* Table: PJ_ETAPA_COMENTARIO                                   */
/*==============================================================*/
create table PJ_ETAPA_COMENTARIO  (
   SQ_ETAPA_COMENTARIO  NUMBER(18)                      not null,
   SQ_PROJETO_ETAPA     NUMBER(18)                      not null,
   SQ_PESSOA_INCLUSAO   NUMBER(18)                      not null,
   COMENTARIO           VARCHAR2(4000)                  not null,
   INCLUSAO             DATE                           default sysdate not null,
   ENVIA_MAIL           VARCHAR2(1)                    default 'S' not null
      constraint CKC_ENVIA_MAIL_PJ_ETAPA check (ENVIA_MAIL in ('S','N') and ENVIA_MAIL = upper(ENVIA_MAIL)),
   REGISTRADO           VARCHAR2(1)                    default 'N' not null
      constraint CKC_REGISTRADO_PJ_ETAPA check (REGISTRADO in ('S','N') and REGISTRADO = upper(REGISTRADO)),
   REGISTRO             DATE,
   constraint PK_PJ_ETAPA_COMENTARIO primary key (SQ_ETAPA_COMENTARIO)
);

comment on table PJ_ETAPA_COMENTARIO is
'Registra comentários dos usuários a respeito de uma etapa do projeto.';

comment on column PJ_ETAPA_COMENTARIO.SQ_ETAPA_COMENTARIO is
'Chave de PJ_ETAPA_COMENTARIO.';

comment on column PJ_ETAPA_COMENTARIO.SQ_PROJETO_ETAPA is
'Chave de PJ_PROJETO_ETAPA. Indica a que etapa do projeto o registro está ligado.';

comment on column PJ_ETAPA_COMENTARIO.SQ_PESSOA_INCLUSAO is
'Chave de CO_PESSOA. Indica a pessoa responsável pela inclusão do registro.';

comment on column PJ_ETAPA_COMENTARIO.COMENTARIO is
'Detalhamento do comentário.';

comment on column PJ_ETAPA_COMENTARIO.INCLUSAO is
'Data de inclusão do comentário.';

comment on column PJ_ETAPA_COMENTARIO.ENVIA_MAIL is
'Indica se deve ser enviado e-mail aos responsáveis pela etapa ao final da gravação.';

comment on column PJ_ETAPA_COMENTARIO.REGISTRADO is
'Indica se o comentário está em fase de edição ou já foi registrado.';

comment on column PJ_ETAPA_COMENTARIO.REGISTRO is
'Data de registro do comentário. Preenchido apenas para comentários registrados.';

/*==============================================================*/
/* Index: IN_PJETAPACOM_ETAPA                                   */
/*==============================================================*/
create index IN_PJETAPACOM_ETAPA on PJ_ETAPA_COMENTARIO (
   SQ_PROJETO_ETAPA ASC,
   SQ_ETAPA_COMENTARIO ASC
);

/*==============================================================*/
/* Index: IN_PJETAPACOM_INCLUSAO                                */
/*==============================================================*/
create index IN_PJETAPACOM_INCLUSAO on PJ_ETAPA_COMENTARIO (
   INCLUSAO ASC,
   SQ_ETAPA_COMENTARIO ASC
);

/*==============================================================*/
/* Index: IN_PJETAPACOM_PESSOA                                  */
/*==============================================================*/
create index IN_PJETAPACOM_PESSOA on PJ_ETAPA_COMENTARIO (
   SQ_PESSOA_INCLUSAO ASC,
   SQ_ETAPA_COMENTARIO ASC
);

/*==============================================================*/
/* Index: IN_PJETACOM_REGISTRO                                  */
/*==============================================================*/
create index IN_PJETACOM_REGISTRO on PJ_ETAPA_COMENTARIO (
   REGISTRADO ASC,
   SQ_ETAPA_COMENTARIO ASC
);

/*==============================================================*/
/* Table: PJ_ETAPA_CONTRATO                                     */
/*==============================================================*/
create table PJ_ETAPA_CONTRATO  (
   SQ_ETAPA_CONTRATO    NUMBER(18)                      not null,
   SQ_PROJETO_ETAPA     NUMBER(18),
   SQ_SIW_SOLICITACAO   NUMBER(18),
   constraint PK_PJ_ETAPA_CONTRATO primary key (SQ_ETAPA_CONTRATO)
);

comment on table PJ_ETAPA_CONTRATO is
'Relaciona os contratos vinculados à etapa.';

comment on column PJ_ETAPA_CONTRATO.SQ_ETAPA_CONTRATO is
'Chave de PJ_ETAPA_CONTRATO. Indica a que etapa do projeto o contrato está ligado.';

comment on column PJ_ETAPA_CONTRATO.SQ_PROJETO_ETAPA is
'Chave de PJ_PROJETO_ETAPA. Indica a que etapa do projeto o registro está ligado.';

comment on column PJ_ETAPA_CONTRATO.SQ_SIW_SOLICITACAO is
'Chave de SIW_SOLICITACAO indicando a que solicitação o registro está ligado.';

/*==============================================================*/
/* Index: IN_PJETACON_ETAPA                                     */
/*==============================================================*/
create index IN_PJETACON_ETAPA on PJ_ETAPA_CONTRATO (
   SQ_PROJETO_ETAPA ASC,
   SQ_ETAPA_CONTRATO ASC
);

/*==============================================================*/
/* Index: IN_PJETACON_SOLIC                                     */
/*==============================================================*/
create index IN_PJETACON_SOLIC on PJ_ETAPA_CONTRATO (
   SQ_SIW_SOLICITACAO ASC,
   SQ_ETAPA_CONTRATO ASC
);

/*==============================================================*/
/* Table: PJ_ETAPA_DEMANDA                                      */
/*==============================================================*/
create table PJ_ETAPA_DEMANDA  (
   SQ_ETAPA_DEMANDA     NUMBER(18)                      not null,
   SQ_PROJETO_ETAPA     NUMBER(18)                      not null,
   SQ_SIW_SOLICITACAO   NUMBER(18)                      not null,
   constraint PK_PJ_ETAPA_DEMANDA primary key (SQ_ETAPA_DEMANDA)
);

comment on table PJ_ETAPA_DEMANDA is
'Relaciona as demandas necessárias ao cumprimento da etapa.';

comment on column PJ_ETAPA_DEMANDA.SQ_ETAPA_DEMANDA is
'Chave de PJ_ETAPA_DEMANDA. Indica a que etapa do projeto a demanda está ligada.';

comment on column PJ_ETAPA_DEMANDA.SQ_PROJETO_ETAPA is
'Chave de PJ_PROJETO_ETAPA. Indica a que etapa do projeto o registro está ligado.';

comment on column PJ_ETAPA_DEMANDA.SQ_SIW_SOLICITACAO is
'Chave de SIW_SOLICITACAO indicando a que solicitação o registro está ligado.';

/*==============================================================*/
/* Index: IN_PJETADEM_ETAPA                                     */
/*==============================================================*/
create index IN_PJETADEM_ETAPA on PJ_ETAPA_DEMANDA (
   SQ_PROJETO_ETAPA ASC,
   SQ_ETAPA_DEMANDA ASC
);

/*==============================================================*/
/* Index: IN_PJETADEM_SOLIC                                     */
/*==============================================================*/
create index IN_PJETADEM_SOLIC on PJ_ETAPA_DEMANDA (
   SQ_SIW_SOLICITACAO ASC,
   SQ_ETAPA_DEMANDA ASC
);

/*==============================================================*/
/* Table: PJ_ETAPA_MENSAL                                       */
/*==============================================================*/
create table PJ_ETAPA_MENSAL  (
   SQ_PROJETO_ETAPA     NUMBER(18)                      not null,
   REFERENCIA           DATE                            not null,
   EXECUCAO_FISICA      NUMBER(18,2)                    not null,
   EXECUCAO_FINANCEIRA  NUMBER(18,2)                    not null,
   constraint PK_PJ_ETAPA_MENSAL primary key (SQ_PROJETO_ETAPA, REFERENCIA)
);

comment on table PJ_ETAPA_MENSAL is
'Registra quantitativos mensais de execução da etapa.';

comment on column PJ_ETAPA_MENSAL.SQ_PROJETO_ETAPA is
'Chave de PJ_PROJETO_ETAPA. Indica a que etapa do projeto o registro está ligado.';

comment on column PJ_ETAPA_MENSAL.REFERENCIA is
'Mês de referência da informação. Será informado sempre o último dia do mês.';

comment on column PJ_ETAPA_MENSAL.EXECUCAO_FISICA is
'Quantitativo físico executado no mês de referência.';

comment on column PJ_ETAPA_MENSAL.EXECUCAO_FINANCEIRA is
'Valor financeiro executado no mês de referência.';

/*==============================================================*/
/* Index: IN_PJETAMEN_INVERSA                                   */
/*==============================================================*/
create index IN_PJETAMEN_INVERSA on PJ_ETAPA_MENSAL (
   REFERENCIA ASC,
   SQ_PROJETO_ETAPA ASC
);

/*==============================================================*/
/* Table: PJ_PROJETO                                            */
/*==============================================================*/
create table PJ_PROJETO  (
   SQ_SIW_SOLICITACAO   NUMBER(18)                      not null,
   SQ_UNIDADE_RESP      NUMBER(10)                      not null,
   OUTRA_PARTE          NUMBER(18),
   PREPOSTO             NUMBER(18),
   SQ_TIPO_PESSOA       NUMBER(18),
   PRIORIDADE           NUMBER(2),
   DIAS_AVISO           NUMBER(3)                      default 0 not null,
   PROPONENTE           VARCHAR2(90),
   INICIO_REAL          DATE,
   FIM_REAL             DATE,
   CONCLUIDA            VARCHAR2(1)                    default 'N' not null
      constraint CKC_PJPRO_CONC check (CONCLUIDA in ('S','N') and CONCLUIDA = upper(CONCLUIDA)),
   DATA_CONCLUSAO       DATE,
   NOTA_CONCLUSAO       VARCHAR2(2005),
   CUSTO_REAL           NUMBER(18,2)                   default 0 not null,
   VINCULA_CONTRATO     VARCHAR2(1)                    default 'N' not null
      constraint CKC_PJPRO_CONTRATO check (VINCULA_CONTRATO in ('S','N') and VINCULA_CONTRATO = upper(VINCULA_CONTRATO)),
   VINCULA_VIAGEM       VARCHAR2(1)                    default 'N' not null
      constraint CKC_PJPRO_VIAGEM check (VINCULA_VIAGEM in ('S','N') and VINCULA_VIAGEM = upper(VINCULA_VIAGEM)),
   AVISO_PROX_CONC      VARCHAR2(1)                    default 'N' not null
      constraint CKC_PJPRO_AVISO check (AVISO_PROX_CONC in ('S','N') and AVISO_PROX_CONC = upper(AVISO_PROX_CONC)),
   SQ_CIDADE            NUMBER(18),
   LIMITE_PASSAGEM      NUMBER(18),
   OBJETIVO_SUPERIOR    VARCHAR2(2000),
   EXCLUSOES            VARCHAR2(2000),
   PREMISSAS            VARCHAR2(2000),
   RESTRICOES           VARCHAR2(2000),
   AVISO_PROX_CONC_PACOTE VARCHAR2(1)                    default 'N' not null
      constraint CKC_AVISO_PROX_CONC_P_PJ_PROJE check (AVISO_PROX_CONC_PACOTE in ('S','N') and AVISO_PROX_CONC_PACOTE = upper(AVISO_PROX_CONC_PACOTE)),
   PERC_DIAS_AVISO_PACOTE NUMBER(3)                      default 0 not null,
   INSTANCIA_ARTICULACAO VARCHAR2(500),
   COMPOSICAO_INSTANCIA VARCHAR2(500),
   ESTUDOS              VARCHAR2(2000),
   ANALISE1             VARCHAR2(2000),
   ANALISE2             VARCHAR2(2000),
   ANALISE3             VARCHAR2(2000),
   ANALISE4             VARCHAR2(2000),
   EXIBE_RELATORIO      VARCHAR2(1)                    default 'N' not null
      constraint CKC_EXIBE_RELATORIO_PJ_PROJE check (EXIBE_RELATORIO in ('S','N') and EXIBE_RELATORIO = upper(EXIBE_RELATORIO)),
   constraint PK_PJ_PROJETO primary key (SQ_SIW_SOLICITACAO)
);

comment on table PJ_PROJETO is
'Registra as informações cadastrais do projeto';

comment on column PJ_PROJETO.SQ_SIW_SOLICITACAO is
'Chave de SIW_SOLICITACAO indicando a que solicitação o registro está ligado.';

comment on column PJ_PROJETO.SQ_UNIDADE_RESP is
'Chave de EO_UNIDADE. Indica a unidade responsável pelo monitoramento do projeto.';

comment on column PJ_PROJETO.OUTRA_PARTE is
'Chave de CO_PESSOA, indicando a outra parte do projeto, se existir.';

comment on column PJ_PROJETO.PREPOSTO is
'Chave de CO_PESSOA, indicando o preposto se a outra parte for pessoa jurídica.';

comment on column PJ_PROJETO.SQ_TIPO_PESSOA is
'Chave de CO_TIPO_PESSOA. Quando o projeto está associado a outra parte, indica se ela é pessoa física ou jurídica.';

comment on column PJ_PROJETO.PRIORIDADE is
'Registra a prioridade do projeto. Quanto menor o número, mais alta a prioridade.';

comment on column PJ_PROJETO.DIAS_AVISO is
'Se o campo AVISO_PROX_CONC igual a S, indica o número de dias a partir do qual devem ser enviados os avisos por e-mail.';

comment on column PJ_PROJETO.PROPONENTE is
'Proponente da demanda. Texto livre.';

comment on column PJ_PROJETO.INICIO_REAL is
'Início real do projeto.';

comment on column PJ_PROJETO.FIM_REAL is
'Fim real do projeto.';

comment on column PJ_PROJETO.CONCLUIDA is
'Indica se a demanda está concluída ou não.';

comment on column PJ_PROJETO.DATA_CONCLUSAO is
'Data informada pelo usuário.';

comment on column PJ_PROJETO.NOTA_CONCLUSAO is
'Observações relativas à conclusão da demanda.';

comment on column PJ_PROJETO.CUSTO_REAL is
'Custo real para execução do projeto.';

comment on column PJ_PROJETO.VINCULA_CONTRATO is
'Indica se é possível a vinculação de contratos ao projeto.';

comment on column PJ_PROJETO.VINCULA_VIAGEM is
'Indica se é possível a vinculação de passagens e diárias ao projeto.';

comment on column PJ_PROJETO.AVISO_PROX_CONC is
'Indica se é necessário avisar a proximidade da data limite para conclusão da demanda.';

comment on column PJ_PROJETO.SQ_CIDADE is
'Chave de CO_CIDADE indicando a cidade de realização do projeto.';

comment on column PJ_PROJETO.LIMITE_PASSAGEM is
'Indica a quantidade máxima de passagens permitidas para este projeto.';

comment on column PJ_PROJETO.OBJETIVO_SUPERIOR is
'Objetivo superior do projeto.';

comment on column PJ_PROJETO.EXCLUSOES is
'Objetivo específicas, ligadas ao projeto. ';

comment on column PJ_PROJETO.PREMISSAS is
'Premissas para a execução do projeto.';

comment on column PJ_PROJETO.RESTRICOES is
'Restrições do projeto.';

comment on column PJ_PROJETO.AVISO_PROX_CONC_PACOTE is
'Indica se é necessário avisar a proximidade da data limite para conclusão dos pacotes de trabalho.';

comment on column PJ_PROJETO.PERC_DIAS_AVISO_PACOTE is
'Se o campo AVISO_PROX_CONC_PACOTE igual a S, indica o percentual de dias a partir do qual devem ser enviados os avisos por e-mail.';

comment on column PJ_PROJETO.INSTANCIA_ARTICULACAO is
'Instância de articulação público-privada.';

comment on column PJ_PROJETO.COMPOSICAO_INSTANCIA is
'Composição da instância de articulação público-privada.';

comment on column PJ_PROJETO.ESTUDOS is
'Estudos.';

comment on column PJ_PROJETO.ANALISE1 is
'Texto  1 de análise. Utilizado para registro da análise de um perfil da aplicação.';

comment on column PJ_PROJETO.ANALISE2 is
'Texto  2 de análise. Utilizado para registro da análise de um perfil da aplicação.';

comment on column PJ_PROJETO.ANALISE3 is
'Texto  3 de análise. Utilizado para registro da análise de um perfil da aplicação.';

comment on column PJ_PROJETO.ANALISE4 is
'Texto  4 de análise. Utilizado para registro da análise de um perfil da aplicação.';

comment on column PJ_PROJETO.EXIBE_RELATORIO is
'Se o projeto já foi concluído, indica se deve ser exibido nos relatórios gerenciais.';

/*==============================================================*/
/* Index: IN_PJPRO_UNID                                         */
/*==============================================================*/
create index IN_PJPRO_UNID on PJ_PROJETO (
   SQ_UNIDADE_RESP ASC,
   SQ_SIW_SOLICITACAO ASC
);

/*==============================================================*/
/* Index: IN_PJPRO_INI                                          */
/*==============================================================*/
create index IN_PJPRO_INI on PJ_PROJETO (
   INICIO_REAL ASC,
   SQ_SIW_SOLICITACAO ASC
);

/*==============================================================*/
/* Index: IN_PJPRO_FIM                                          */
/*==============================================================*/
create index IN_PJPRO_FIM on PJ_PROJETO (
   FIM_REAL ASC,
   SQ_SIW_SOLICITACAO ASC
);

/*==============================================================*/
/* Index: IN_PJPRO_PRIOR                                        */
/*==============================================================*/
create index IN_PJPRO_PRIOR on PJ_PROJETO (
   PRIORIDADE ASC,
   SQ_SIW_SOLICITACAO ASC
);

/*==============================================================*/
/* Index: IN_PJPRO_CONC                                         */
/*==============================================================*/
create index IN_PJPRO_CONC on PJ_PROJETO (
   CONCLUIDA ASC,
   SQ_SIW_SOLICITACAO ASC
);

/*==============================================================*/
/* Index: IN_PJPRO_CUSTO                                        */
/*==============================================================*/
create index IN_PJPRO_CUSTO on PJ_PROJETO (
   CUSTO_REAL ASC,
   SQ_SIW_SOLICITACAO ASC
);

/*==============================================================*/
/* Index: IN_PJPRO_DTCONC                                       */
/*==============================================================*/
create index IN_PJPRO_DTCONC on PJ_PROJETO (
   DATA_CONCLUSAO ASC,
   SQ_SIW_SOLICITACAO ASC
);

/*==============================================================*/
/* Index: IN_PJPRO_PROPON                                       */
/*==============================================================*/
create index IN_PJPRO_PROPON on PJ_PROJETO (
   PROPONENTE ASC,
   SQ_SIW_SOLICITACAO ASC
);

/*==============================================================*/
/* Index: IN_PJPRO_OUTRA                                        */
/*==============================================================*/
create index IN_PJPRO_OUTRA on PJ_PROJETO (
   OUTRA_PARTE ASC,
   SQ_SIW_SOLICITACAO ASC
);

/*==============================================================*/
/* Index: IN_PJPRO_REPRES                                       */
/*==============================================================*/
create index IN_PJPRO_REPRES on PJ_PROJETO (
   PREPOSTO ASC,
   SQ_SIW_SOLICITACAO ASC
);

/*==============================================================*/
/* Index: IN_PJPRO_TIPOPESSOA                                   */
/*==============================================================*/
create index IN_PJPRO_TIPOPESSOA on PJ_PROJETO (
   SQ_TIPO_PESSOA ASC,
   SQ_SIW_SOLICITACAO ASC
);

/*==============================================================*/
/* Table: PJ_PROJETO_ENVOLV                                     */
/*==============================================================*/
create table PJ_PROJETO_ENVOLV  (
   SQ_UNIDADE           number(10)                      not null,
   SQ_SIW_SOLICITACAO   NUMBER(18)                      not null,
   PAPEL                VARCHAR2(2000)                  not null,
   INTERESSE_POSITIVO   VARCHAR2(1)                    default 'S' not null
      constraint CKC_INTERESSE_POSITIV_PJ_PROJE check (INTERESSE_POSITIVO in ('S','N') and INTERESSE_POSITIVO = upper(INTERESSE_POSITIVO)),
   INFLUENCIA           NUMBER(2),
   constraint PK_PJ_PROJETO_ENVOLV primary key (SQ_UNIDADE, SQ_SIW_SOLICITACAO)
);

comment on table PJ_PROJETO_ENVOLV is
'Registra as unidades envolvidas na execução do projeto.';

comment on column PJ_PROJETO_ENVOLV.SQ_UNIDADE is
'Chave de EO_UNIDADE. Indica a que unidade o registro está ligado.';

comment on column PJ_PROJETO_ENVOLV.SQ_SIW_SOLICITACAO is
'Chave de SIW_SOLICITACAO indicando a que solicitação o registro está ligado.';

comment on column PJ_PROJETO_ENVOLV.PAPEL is
'Papel cumprido pela área envolvida.';

comment on column PJ_PROJETO_ENVOLV.INTERESSE_POSITIVO is
'Indica se o interesse da área é positivo ou negativo.';

comment on column PJ_PROJETO_ENVOLV.INFLUENCIA is
'Registra a influência da área no projeto. 0 - Alta, 1 - Média, 2 - Baixa.';

/*==============================================================*/
/* Index: IN_PJPROENV_INVERSA                                   */
/*==============================================================*/
create index IN_PJPROENV_INVERSA on PJ_PROJETO_ENVOLV (
   SQ_SIW_SOLICITACAO ASC,
   SQ_UNIDADE ASC
);

/*==============================================================*/
/* Table: PJ_PROJETO_ETAPA                                      */
/*==============================================================*/
create table PJ_PROJETO_ETAPA  (
   SQ_PROJETO_ETAPA     NUMBER(18)                      not null,
   SQ_SIW_SOLICITACAO   NUMBER(18)                      not null,
   SQ_ETAPA_PAI         NUMBER(18),
   SQ_PESSOA            NUMBER(18)                      not null,
   SQ_PESSOA_ATUALIZACAO NUMBER(18)                      not null,
   SQ_UNIDADE           NUMBER(10)                      not null,
   ORDEM                NUMBER(3)                       not null,
   TITULO               VARCHAR2(150)                   not null,
   DESCRICAO            VARCHAR2(2000)                  not null,
   INICIO_PREVISTO      DATE                            not null,
   FIM_PREVISTO         DATE                            not null,
   INICIO_REAL          DATE,
   FIM_REAL             DATE,
   PERC_CONCLUSAO       NUMBER(18,2)                   default 0 not null,
   ORCAMENTO            NUMBER(18,2)                   default 0 not null,
   VINCULA_ATIVIDADE    VARCHAR2(1)                    default 'S' not null
      constraint CKC_VINCULA_ATIVIDADE_PJ_PROJE check (VINCULA_ATIVIDADE in ('S','N') and VINCULA_ATIVIDADE = upper(VINCULA_ATIVIDADE)),
   ULTIMA_ATUALIZACAO   DATE                           default SYSDATE not null,
   SITUACAO_ATUAL       VARCHAR2(4000),
   UNIDADE_MEDIDA       VARCHAR2(30),
   QUANTIDADE           NUMBER(18,2)                   default 0 not null,
   CUMULATIVA           VARCHAR2(1)                    default 'N' not null
      constraint CKC_CUMULATIVA_PJ_PROJE check (CUMULATIVA in ('S','N') and CUMULATIVA = upper(CUMULATIVA)),
   PROGRAMADA           VARCHAR2(1)                    default 'N' not null
      constraint CKC_PROGRAMADA_PJ_PROJE check (PROGRAMADA in ('S','N') and PROGRAMADA = upper(PROGRAMADA)),
   EXEQUIVEL            VARCHAR2(1)                    default 'S' not null
      constraint CKC_EXEQUIVEL_PJ_PROJE check (EXEQUIVEL in ('S','N') and EXEQUIVEL = upper(EXEQUIVEL)),
   JUSTIFICATIVA_INEXEQUIVEL VARCHAR2(1000),
   OUTRAS_MEDIDAS       VARCHAR2(1000),
   VINCULA_CONTRATO     VARCHAR2(1)                    default 'N' not null
      constraint CKC_VINCULA_CONTRATO_PJ_PROJE check (VINCULA_CONTRATO in ('S','N') and VINCULA_CONTRATO = upper(VINCULA_CONTRATO)),
   PACOTE_TRABALHO      VARCHAR2(1)                    default 'N' not null
      constraint CKC_PACOTE_PJPROETA check (PACOTE_TRABALHO in ('S','N') and PACOTE_TRABALHO = upper(PACOTE_TRABALHO)),
   BASE_GEOGRAFICA      NUMBER(1),
   SQ_PAIS              NUMBER(18),
   SQ_REGIAO            NUMBER(18),
   CO_UF                VARCHAR2(3),
   SQ_CIDADE            NUMBER(18),
   PESO                 NUMBER(2)                      default 1 not null,
   PESO_PAI             NUMBER(18,15)                  default 0 not null,
   PESO_PROJETO         NUMBER(18,15)                  default 0 not null,
   constraint PK_PJ_PROJETO_ETAPA primary key (SQ_PROJETO_ETAPA)
);

comment on table PJ_PROJETO_ETAPA is
'Registra as etapas do projeto.';

comment on column PJ_PROJETO_ETAPA.SQ_PROJETO_ETAPA is
'Chave de PJ_PROJETO_ETAPA.';

comment on column PJ_PROJETO_ETAPA.SQ_SIW_SOLICITACAO is
'Chave de SIW_SOLICITACAO indicando a que solicitação o registro está ligado.';

comment on column PJ_PROJETO_ETAPA.SQ_ETAPA_PAI is
'Chave de PJ_PROJETO_ETAPA. Auto-relacionamento da tabela.';

comment on column PJ_PROJETO_ETAPA.SQ_PESSOA is
'Chave de CO_PESSOA. Responsável pela etapa.';

comment on column PJ_PROJETO_ETAPA.SQ_PESSOA_ATUALIZACAO is
'Chave de CO_PESSOA. Usuário responsável pela criação ou última atualização da etapa.';

comment on column PJ_PROJETO_ETAPA.SQ_UNIDADE is
'Chave de EO_UNIDADE. Indica a unidade responsável pela etapa.';

comment on column PJ_PROJETO_ETAPA.ORDEM is
'Ordem de execução da etapa.';

comment on column PJ_PROJETO_ETAPA.TITULO is
'Título da etapa.';

comment on column PJ_PROJETO_ETAPA.DESCRICAO is
'Descrição da etapa.';

comment on column PJ_PROJETO_ETAPA.INICIO_PREVISTO is
'Início previsto da etapa.';

comment on column PJ_PROJETO_ETAPA.FIM_PREVISTO is
'Fim previsto para a etapa.';

comment on column PJ_PROJETO_ETAPA.INICIO_REAL is
'Início real da etapa.';

comment on column PJ_PROJETO_ETAPA.FIM_REAL is
'Fim real da etapa.';

comment on column PJ_PROJETO_ETAPA.PERC_CONCLUSAO is
'Percentual de concluso da etapa.';

comment on column PJ_PROJETO_ETAPA.ORCAMENTO is
'Orçamento disponível para cumprimento da etapa.';

comment on column PJ_PROJETO_ETAPA.VINCULA_ATIVIDADE is
'Indica se atividades podem ser vinculadas a esta etapa ou se ele existe apenas para agrupamento.';

comment on column PJ_PROJETO_ETAPA.ULTIMA_ATUALIZACAO is
'Registra a data da criação ou última atualização da etapa.';

comment on column PJ_PROJETO_ETAPA.SITUACAO_ATUAL is
'Texto detalhando a situação atual da etapa.';

comment on column PJ_PROJETO_ETAPA.UNIDADE_MEDIDA is
'Unidade de medida a ser realizada.';

comment on column PJ_PROJETO_ETAPA.QUANTIDADE is
'Quantidade prevista para a unidade de medida informada.';

comment on column PJ_PROJETO_ETAPA.CUMULATIVA is
'Indica se a realização da etapa é cumulativa ou não.';

comment on column PJ_PROJETO_ETAPA.PROGRAMADA is
'Indica se a etapa está vinculada ao planejamento estratégico.';

comment on column PJ_PROJETO_ETAPA.EXEQUIVEL is
'Indica se a etapa está avaliada como passível de cumprimento ou não.';

comment on column PJ_PROJETO_ETAPA.JUSTIFICATIVA_INEXEQUIVEL is
'Motivos que justificam o não cumprimento da etapa.';

comment on column PJ_PROJETO_ETAPA.OUTRAS_MEDIDAS is
'Descrição das medidas necessárias ao cumprimento da etapa.';

comment on column PJ_PROJETO_ETAPA.VINCULA_CONTRATO is
'Indica se contratos podem ser vinculados a esta etapa.';

comment on column PJ_PROJETO_ETAPA.PACOTE_TRABALHO is
'Indica se a etapa é um pacote de trabalho. Se for, tem que ser último nível.';

comment on column PJ_PROJETO_ETAPA.BASE_GEOGRAFICA is
'Indica a que base geográfica da etapa. 1 - Nacional, 2 - Regional, 3 - Estadual, 4 - Municipal, 5 - Organizacional.';

comment on column PJ_PROJETO_ETAPA.SQ_PAIS is
'Chave de CO_PAIS. Tem valor apenas quando a base geográfica é a nivel nacional.';

comment on column PJ_PROJETO_ETAPA.SQ_REGIAO is
'Chave de CO_REGIAO. Tem valor apenas quando a base geográfica é a nivel regional.';

comment on column PJ_PROJETO_ETAPA.CO_UF is
'Chave de CO_UF. Tem valor apenas quando a base geográfica é a nivel estadual.';

comment on column PJ_PROJETO_ETAPA.SQ_CIDADE is
'Chave de CO_CIDADE. Tem valor apenas quando a base geográfica é a nivel municipal.';

comment on column PJ_PROJETO_ETAPA.PESO is
'Peso da etapa para cálculo do percentual de execução.';

comment on column PJ_PROJETO_ETAPA.PESO_PAI is
'Peso relativo da etapa em relação ao pai.';

comment on column PJ_PROJETO_ETAPA.PESO_PROJETO is
'Peso relativo da etapa em relação ao projeto.';

/*==============================================================*/
/* Index: IN_PJPROETA_PAI                                       */
/*==============================================================*/
create index IN_PJPROETA_PAI on PJ_PROJETO_ETAPA (
   SQ_ETAPA_PAI ASC,
   SQ_PROJETO_ETAPA ASC
);

/*==============================================================*/
/* Index: IN_PJPROETA_PROJ                                      */
/*==============================================================*/
create index IN_PJPROETA_PROJ on PJ_PROJETO_ETAPA (
   SQ_SIW_SOLICITACAO ASC,
   SQ_PROJETO_ETAPA ASC
);

/*==============================================================*/
/* Index: IN_PJPROETA_INI                                       */
/*==============================================================*/
create index IN_PJPROETA_INI on PJ_PROJETO_ETAPA (
   INICIO_PREVISTO ASC,
   SQ_PROJETO_ETAPA ASC
);

/*==============================================================*/
/* Index: IN_PJPROETA_INIRE                                     */
/*==============================================================*/
create index IN_PJPROETA_INIRE on PJ_PROJETO_ETAPA (
   INICIO_REAL ASC,
   SQ_PROJETO_ETAPA ASC
);

/*==============================================================*/
/* Index: IN_PJPROETA_FIM                                       */
/*==============================================================*/
create index IN_PJPROETA_FIM on PJ_PROJETO_ETAPA (
   FIM_PREVISTO ASC,
   SQ_PROJETO_ETAPA ASC
);

/*==============================================================*/
/* Index: IN_PJPROETA_ORDEM                                     */
/*==============================================================*/
create index IN_PJPROETA_ORDEM on PJ_PROJETO_ETAPA (
   ORDEM ASC,
   SQ_PROJETO_ETAPA ASC
);

/*==============================================================*/
/* Index: IN_PJPROETA_FIMRE                                     */
/*==============================================================*/
create index IN_PJPROETA_FIMRE on PJ_PROJETO_ETAPA (
   FIM_REAL ASC,
   SQ_PROJETO_ETAPA ASC
);

/*==============================================================*/
/* Index: IN_PJPROETA_RESP                                      */
/*==============================================================*/
create index IN_PJPROETA_RESP on PJ_PROJETO_ETAPA (
   SQ_PESSOA ASC,
   SQ_PROJETO_ETAPA ASC
);

/*==============================================================*/
/* Index: IN_PJPROETA_SETOR                                     */
/*==============================================================*/
create index IN_PJPROETA_SETOR on PJ_PROJETO_ETAPA (
   SQ_UNIDADE ASC,
   SQ_SIW_SOLICITACAO ASC,
   SQ_PROJETO_ETAPA ASC
);

/*==============================================================*/
/* Index: IN_PJPROETA_PAIS                                      */
/*==============================================================*/
create index IN_PJPROETA_PAIS on PJ_PROJETO_ETAPA (
   SQ_PAIS ASC,
   SQ_SIW_SOLICITACAO ASC,
   SQ_PROJETO_ETAPA ASC
);

/*==============================================================*/
/* Index: IN_PJPROETA_REGIAO                                    */
/*==============================================================*/
create index IN_PJPROETA_REGIAO on PJ_PROJETO_ETAPA (
   SQ_REGIAO ASC,
   SQ_SIW_SOLICITACAO ASC,
   SQ_PROJETO_ETAPA ASC
);

/*==============================================================*/
/* Index: IN_PJPROETA_UF                                        */
/*==============================================================*/
create index IN_PJPROETA_UF on PJ_PROJETO_ETAPA (
   SQ_PAIS ASC,
   CO_UF ASC,
   SQ_SIW_SOLICITACAO ASC,
   SQ_PROJETO_ETAPA ASC
);

/*==============================================================*/
/* Index: IN_PJPROETA_CIDADE                                    */
/*==============================================================*/
create index IN_PJPROETA_CIDADE on PJ_PROJETO_ETAPA (
   SQ_CIDADE ASC,
   SQ_SIW_SOLICITACAO ASC,
   SQ_PROJETO_ETAPA ASC
);

/*==============================================================*/
/* Index: IN_PJPROETA_BASE                                      */
/*==============================================================*/
create index IN_PJPROETA_BASE on PJ_PROJETO_ETAPA (
   BASE_GEOGRAFICA ASC,
   SQ_SIW_SOLICITACAO ASC,
   SQ_PROJETO_ETAPA ASC
);

/*==============================================================*/
/* Table: PJ_PROJETO_ETAPA_ARQ                                  */
/*==============================================================*/
create table PJ_PROJETO_ETAPA_ARQ  (
   SQ_PROJETO_ETAPA     NUMBER(18)                      not null,
   SQ_SIW_ARQUIVO       NUMBER(18)                      not null,
   constraint PK_PJ_PROJETO_ETAPA_ARQ primary key (SQ_PROJETO_ETAPA, SQ_SIW_ARQUIVO)
);

comment on table PJ_PROJETO_ETAPA_ARQ is
'Registra os anexos de etapas.';

comment on column PJ_PROJETO_ETAPA_ARQ.SQ_PROJETO_ETAPA is
'Chave de PJ_PROJETO_ETAPA. Indica a que etapa do projeto o registro está ligado.';

comment on column PJ_PROJETO_ETAPA_ARQ.SQ_SIW_ARQUIVO is
'Chave de SIW_ARQUIVO. Indica a que arquivo o registro está ligado.';

/*==============================================================*/
/* Index: IN_PROETAARQ_INV                                      */
/*==============================================================*/
create index IN_PROETAARQ_INV on PJ_PROJETO_ETAPA_ARQ (
   SQ_SIW_ARQUIVO ASC,
   SQ_PROJETO_ETAPA ASC
);

/*==============================================================*/
/* Table: PJ_PROJETO_INTERES                                    */
/*==============================================================*/
create table PJ_PROJETO_INTERES  (
   SQ_PESSOA            NUMBER(18)                      not null,
   SQ_SIW_SOLICITACAO   NUMBER(18)                      not null,
   TIPO_VISAO           NUMBER(1)                       not null
      constraint CKC_TIPO_VISAO_PJ_PROJE check (TIPO_VISAO in (0,1,2)),
   ENVIA_EMAIL          VARCHAR2(1)                    default 'N' not null
      constraint CKC_ENVIA_EMAIL_PJ_PROJE check (ENVIA_EMAIL in ('S','N') and ENVIA_EMAIL = upper(ENVIA_EMAIL)),
   constraint PK_PJ_PROJETO_INTERES primary key (SQ_PESSOA, SQ_SIW_SOLICITACAO)
);

comment on table PJ_PROJETO_INTERES is
'Registra os interessados pelo projeto e que tipo de informações eles podem receber ou visualizar.';

comment on column PJ_PROJETO_INTERES.SQ_PESSOA is
'Chave de CO_PESSOA. Indica a que pessoa o registro está ligado.';

comment on column PJ_PROJETO_INTERES.SQ_SIW_SOLICITACAO is
'Chave de SIW_SOLICITACAO indicando a que solicitação o registro está ligado.';

comment on column PJ_PROJETO_INTERES.TIPO_VISAO is
'Indica a visão que a pessoa pode ter dessa demanda.';

comment on column PJ_PROJETO_INTERES.ENVIA_EMAIL is
'Indica se deve ser enviado e-mail ao interessado quando houver alguma ocorrência no projeto.';

/*==============================================================*/
/* Index: IN_PJPROINT_INVERSA                                   */
/*==============================================================*/
create index IN_PJPROINT_INVERSA on PJ_PROJETO_INTERES (
   SQ_SIW_SOLICITACAO ASC,
   SQ_PESSOA ASC
);

/*==============================================================*/
/* Index: IN_PJPROINT_EMAIL                                     */
/*==============================================================*/
create index IN_PJPROINT_EMAIL on PJ_PROJETO_INTERES (
   ENVIA_EMAIL ASC,
   SQ_PESSOA ASC,
   SQ_SIW_SOLICITACAO ASC
);

/*==============================================================*/
/* Table: PJ_PROJETO_LOG                                        */
/*==============================================================*/
create table PJ_PROJETO_LOG  (
   SQ_PROJETO_LOG       NUMBER(18)                      not null,
   SQ_SIW_SOLICITACAO   NUMBER(18)                      not null,
   SQ_SIW_SOLIC_LOG     NUMBER(18),
   CADASTRADOR          NUMBER(18)                      not null,
   DESTINATARIO         NUMBER(18),
   DATA_INCLUSAO        DATE                            not null,
   OBSERVACAO           VARCHAR2(2000),
   DESPACHO             VARCHAR2(2000),
   constraint PK_PJ_PROJETO_LOG primary key (SQ_PROJETO_LOG)
);

comment on table PJ_PROJETO_LOG is
'Registra o histórico do projjeto';

comment on column PJ_PROJETO_LOG.SQ_PROJETO_LOG is
'Chave de PJ_PROJETO_LOG.';

comment on column PJ_PROJETO_LOG.SQ_SIW_SOLICITACAO is
'Chave de SIW_SOLICITACAO indicando a que solicitação o registro está ligado.';

comment on column PJ_PROJETO_LOG.SQ_SIW_SOLIC_LOG is
'Chave de SIW_SOLIC_LOG.';

comment on column PJ_PROJETO_LOG.CADASTRADOR is
'Chave de CO_PESSOA.';

comment on column PJ_PROJETO_LOG.DESTINATARIO is
'Chave de CO_PESSOA.';

comment on column PJ_PROJETO_LOG.DATA_INCLUSAO is
'Data de inclusão do registro, gerado pelo sistema.';

comment on column PJ_PROJETO_LOG.OBSERVACAO is
'Observações inseridas pelo usuário.';

comment on column PJ_PROJETO_LOG.DESPACHO is
'Orientação ao destinatário sobre as ações necessárias.';

/*==============================================================*/
/* Index: IN_PJPROLOG_PRJ                                       */
/*==============================================================*/
create index IN_PJPROLOG_PRJ on PJ_PROJETO_LOG (
   SQ_SIW_SOLICITACAO ASC,
   SQ_PROJETO_LOG ASC
);

/*==============================================================*/
/* Index: IN_PRJPROLOG_DATA                                     */
/*==============================================================*/
create index IN_PRJPROLOG_DATA on PJ_PROJETO_LOG (
   SQ_SIW_SOLICITACAO ASC,
   DATA_INCLUSAO ASC,
   SQ_PROJETO_LOG ASC
);

/*==============================================================*/
/* Index: IN_PJPROLOG_CADAST                                    */
/*==============================================================*/
create index IN_PJPROLOG_CADAST on PJ_PROJETO_LOG (
   SQ_SIW_SOLICITACAO ASC,
   CADASTRADOR ASC,
   SQ_PROJETO_LOG ASC
);

/*==============================================================*/
/* Index: IN_PJPROLOG_DEST                                      */
/*==============================================================*/
create index IN_PJPROLOG_DEST on PJ_PROJETO_LOG (
   SQ_SIW_SOLICITACAO ASC,
   DESTINATARIO ASC,
   SQ_PROJETO_LOG ASC
);

/*==============================================================*/
/* Index: IN_PJLOG_SIWLOG                                       */
/*==============================================================*/
create index IN_PJLOG_SIWLOG on PJ_PROJETO_LOG (
   SQ_SIW_SOLIC_LOG ASC,
   SQ_PROJETO_LOG ASC
);

/*==============================================================*/
/* Table: PJ_PROJETO_LOG_ARQ                                    */
/*==============================================================*/
create table PJ_PROJETO_LOG_ARQ  (
   SQ_PROJETO_LOG       NUMBER(18)                      not null,
   SQ_SIW_ARQUIVO       NUMBER(18)                      not null,
   constraint PK_PJ_PROJETO_LOG_ARQ primary key (SQ_PROJETO_LOG, SQ_SIW_ARQUIVO)
);

comment on table PJ_PROJETO_LOG_ARQ is
'Vincula arquivos a logs de projeto.';

comment on column PJ_PROJETO_LOG_ARQ.SQ_PROJETO_LOG is
'Chave de PJ_PROJETO_LOG. Indica a que registro o arquivo está ligado.';

comment on column PJ_PROJETO_LOG_ARQ.SQ_SIW_ARQUIVO is
'Chave de SIW_ARQUIVO. Indica a que arquivo o registro está ligado.';

/*==============================================================*/
/* Index: IN_PJPROLOGARQ_INVERSA                                */
/*==============================================================*/
create index IN_PJPROLOGARQ_INVERSA on PJ_PROJETO_LOG_ARQ (
   SQ_SIW_ARQUIVO ASC,
   SQ_PROJETO_LOG ASC
);

/*==============================================================*/
/* Table: PJ_PROJETO_RECURSO                                    */
/*==============================================================*/
create table PJ_PROJETO_RECURSO  (
   SQ_PROJETO_RECURSO   NUMBER(18)                      not null,
   SQ_SIW_SOLICITACAO   NUMBER(18)                      not null,
   NOME                 VARCHAR2(100)                   not null,
   TIPO                 NUMBER(2)                       not null,
   DESCRICAO            VARCHAR2(2000),
   FINALIDADE           VARCHAR2(2000),
   constraint PK_PJ_PROJETO_RECURSO primary key (SQ_PROJETO_RECURSO)
);

comment on table PJ_PROJETO_RECURSO is
'Registra informações sobre os recursos alocados ao projeto.';

comment on column PJ_PROJETO_RECURSO.SQ_PROJETO_RECURSO is
'Chave de PJ_PROJETO_RECURSO.';

comment on column PJ_PROJETO_RECURSO.SQ_SIW_SOLICITACAO is
'Chave de SIW_SOLICITACAO indicando a que solicitação o registro está ligado.';

comment on column PJ_PROJETO_RECURSO.NOME is
'Nome do recurso.';

comment on column PJ_PROJETO_RECURSO.TIPO is
'Tipo do recurso (Humano, Material, Financeiro etc)';

comment on column PJ_PROJETO_RECURSO.DESCRICAO is
'Descrição do recurso';

comment on column PJ_PROJETO_RECURSO.FINALIDADE is
'Finalidade cumprida pelo recurso.';

/*==============================================================*/
/* Index: IN_PJPROREC_PROJ                                      */
/*==============================================================*/
create index IN_PJPROREC_PROJ on PJ_PROJETO_RECURSO (
   SQ_SIW_SOLICITACAO ASC,
   SQ_PROJETO_RECURSO ASC
);

/*==============================================================*/
/* Index: IN_PJPROREC_TIPO                                      */
/*==============================================================*/
create index IN_PJPROREC_TIPO on PJ_PROJETO_RECURSO (
   TIPO ASC,
   SQ_PROJETO_RECURSO ASC
);

/*==============================================================*/
/* Table: PJ_PROJETO_REPRESENTANTE                              */
/*==============================================================*/
create table PJ_PROJETO_REPRESENTANTE  (
   SQ_PESSOA            number(18)                      not null,
   SQ_SIW_SOLICITACAO   NUMBER(18)                      not null,
   constraint PK_PJ_PROJETO_REPRESENTANTE primary key (SQ_PESSOA, SQ_SIW_SOLICITACAO)
);

comment on table PJ_PROJETO_REPRESENTANTE is
'Registra os representantes da outra parte do projeto, se for pessoa jurídica.';

comment on column PJ_PROJETO_REPRESENTANTE.SQ_PESSOA is
'Chave de CO_PESSOA. Indica a que pessoa o registro está ligado.';

comment on column PJ_PROJETO_REPRESENTANTE.SQ_SIW_SOLICITACAO is
'Chave de SIW_SOLICITACAO indicando a que solicitação o registro está ligado.';

/*==============================================================*/
/* Index: IN_PJPROREP_INV                                       */
/*==============================================================*/
create unique index IN_PJPROREP_INV on PJ_PROJETO_REPRESENTANTE (
   SQ_SIW_SOLICITACAO ASC,
   SQ_PESSOA ASC
);

/*==============================================================*/
/* Table: PJ_RECURSO_ETAPA                                      */
/*==============================================================*/
create table PJ_RECURSO_ETAPA  (
   SQ_PROJETO_ETAPA     NUMBER(18)                      not null,
   SQ_PROJETO_RECURSO   NUMBER(18)                      not null,
   OBSERVACAO           VARCHAR2(500),
   constraint PK_PJ_RECURSO_ETAPA primary key (SQ_PROJETO_ETAPA, SQ_PROJETO_RECURSO)
);

comment on table PJ_RECURSO_ETAPA is
'Relaciona os recursos do projeto alocados a essa etapa.';

comment on column PJ_RECURSO_ETAPA.SQ_PROJETO_ETAPA is
'Chave de PJ_PROJETO_ETAPA. Indica a que etapa do projeto o registro está ligado.';

comment on column PJ_RECURSO_ETAPA.SQ_PROJETO_RECURSO is
'Chave de PJ_PROJETO_RECURSO. Indica a que recurso do projeto o registro está ligado.';

comment on column PJ_RECURSO_ETAPA.OBSERVACAO is
'Observações sobre a participação do recurso no cumprimento da etapa.';

/*==============================================================*/
/* Index: IN_PJRECETA_INVERSA                                   */
/*==============================================================*/
create index IN_PJRECETA_INVERSA on PJ_RECURSO_ETAPA (
   SQ_PROJETO_RECURSO ASC,
   SQ_PROJETO_ETAPA ASC
);

/*==============================================================*/
/* Table: PJ_RUBRICA                                            */
/*==============================================================*/
create table PJ_RUBRICA  (
   SQ_PROJETO_RUBRICA   NUMBER(18)                      not null,
   SQ_SIW_SOLICITACAO   NUMBER(18)                      not null,
   CODIGO               VARCHAR2(20)                    not null,
   NOME                 VARCHAR2(60)                    not null,
   DESCRICAO            VARCHAR2(500)                   not null,
   ATIVO                VARCHAR2(1)                    default 'S' not null
      constraint CKC_ATIVO_PJ_RUBRI check (ATIVO in ('S','N') and ATIVO = upper(ATIVO)),
   VALOR_INICIAL        NUMBER(18,2)                   default 0 not null,
   ENTRADA_PREVISTA     NUMBER(18,2)                   default 0 not null,
   ENTRADA_REAL         NUMBER(18,2)                   default 0 not null,
   SAIDA_PREVISTA       NUMBER(18,2)                   default 0 not null,
   SAIDA_REAL           NUMBER(18,2)                   default 0 not null,
   APLICACAO_FINANCEIRA VARCHAR2(1)                    default 'N' not null
      constraint CKC_APLICACAO_FINANCE_PJ_RUBRI check (APLICACAO_FINANCEIRA in ('S','N') and APLICACAO_FINANCEIRA = upper(APLICACAO_FINANCEIRA)),
   constraint PK_PJ_RUBRICA primary key (SQ_PROJETO_RUBRICA)
);

comment on table PJ_RUBRICA is
'Registra as rubricas do projeto.';

comment on column PJ_RUBRICA.SQ_PROJETO_RUBRICA is
'Chave de PJ_PROJETO_RUBRICA.';

comment on column PJ_RUBRICA.SQ_SIW_SOLICITACAO is
'Chave de SIW_SOLICITACAO. Indica a que projeto a rubrica está ligada.';

comment on column PJ_RUBRICA.CODIGO is
'Código da rubrica.';

comment on column PJ_RUBRICA.NOME is
'Nome da rubrica.';

comment on column PJ_RUBRICA.DESCRICAO is
'Descrição da rubrica.';

comment on column PJ_RUBRICA.ATIVO is
'Indica se a rubrica pode ser associada a novos lancamentos financeiros.';

comment on column PJ_RUBRICA.VALOR_INICIAL is
'Valor da dotação inicial para a rubrica. Atribuído por trigger a partir dos recebimentos.';

comment on column PJ_RUBRICA.ENTRADA_PREVISTA is
'Somatório das receitas não liquidadas. Calculado por trigger a partir dos recebimentos.';

comment on column PJ_RUBRICA.ENTRADA_REAL is
'Somatório das receitas liquidadas. Calculado por trigger a partir dos recebimentos.';

comment on column PJ_RUBRICA.SAIDA_PREVISTA is
'Somatório das despesas não liquidadas. Calculado por trigger a partir dos pagamentos.';

comment on column PJ_RUBRICA.SAIDA_REAL is
'Somatório das despesas liquidadas. Calculado por trigger a partir dos pagamentos.';

comment on column PJ_RUBRICA.APLICACAO_FINANCEIRA is
'Indica se a rubrica é de relativa a aplicações financeiras.';

/*==============================================================*/
/* Index: IN_PJRUB_SOLIC                                        */
/*==============================================================*/
create index IN_PJRUB_SOLIC on PJ_RUBRICA (
   SQ_SIW_SOLICITACAO ASC,
   SQ_PROJETO_RUBRICA ASC
);

/*==============================================================*/
/* Table: PJ_RUBRICA_CRONOGRAMA                                 */
/*==============================================================*/
create table PJ_RUBRICA_CRONOGRAMA  (
   SQ_RUBRICA_CRONOGRAMA NUMBER(18)                      not null,
   SQ_PROJETO_RUBRICA   NUMBER(18)                      not null,
   INICIO               DATE                            not null,
   FIM                  DATE                            not null,
   VALOR_PREVISTO       NUMBER(18,2)                   default 0 not null,
   VALOR_REAL           NUMBER(18,2)                   default 0 not null,
   constraint PK_PJ_RUBRICA_CRONOGRAMA primary key (SQ_RUBRICA_CRONOGRAMA)
);

comment on table PJ_RUBRICA_CRONOGRAMA is
'Registra o cronograma desembolso da rubrica.';

comment on column PJ_RUBRICA_CRONOGRAMA.SQ_RUBRICA_CRONOGRAMA is
'Chave de PJ_RUBRICA_CRONOGRAMA.';

comment on column PJ_RUBRICA_CRONOGRAMA.SQ_PROJETO_RUBRICA is
'Chave de PJ_PROJETO_RUBRICA. Indica a que rubrica o registro está ligado.';

comment on column PJ_RUBRICA_CRONOGRAMA.INICIO is
'Início do período de referência do cronograma.';

comment on column PJ_RUBRICA_CRONOGRAMA.FIM is
'Término do período de referência do cronograma.';

comment on column PJ_RUBRICA_CRONOGRAMA.VALOR_PREVISTO is
'Valor previsto para a rubrica no período.';

comment on column PJ_RUBRICA_CRONOGRAMA.VALOR_REAL is
'Valor executado da rubrica no período.';

/*==============================================================*/
/* Index: IN_PJRUBCRO_INICIO                                    */
/*==============================================================*/
create index IN_PJRUBCRO_INICIO on PJ_RUBRICA_CRONOGRAMA (
   INICIO ASC,
   SQ_PROJETO_RUBRICA ASC,
   SQ_RUBRICA_CRONOGRAMA ASC
);

/*==============================================================*/
/* Index: IN_PJRUBCRO_FIM                                       */
/*==============================================================*/
create index IN_PJRUBCRO_FIM on PJ_RUBRICA_CRONOGRAMA (
   FIM ASC,
   SQ_PROJETO_RUBRICA ASC,
   SQ_RUBRICA_CRONOGRAMA ASC
);

/*==============================================================*/
/* Index: IN_PJRUBCRO_RUBRICA                                   */
/*==============================================================*/
create index IN_PJRUBCRO_RUBRICA on PJ_RUBRICA_CRONOGRAMA (
   SQ_PROJETO_RUBRICA ASC,
   INICIO ASC,
   FIM ASC,
   SQ_RUBRICA_CRONOGRAMA ASC
);

/*==============================================================*/
/* Table: PT_CAMPO                                              */
/*==============================================================*/
create table PT_CAMPO  (
   SQ_CAMPO             NUMBER(11)                      not null,
   CLIENTE              NUMBER(18)                      not null,
   NOME_CAMPO           VARCHAR2(100)                   not null,
   NOME_CAMPO_TABELA    VARCHAR2(50),
   NOME_TABELA          VARCHAR2(45),
   DAT_CADASTRO         DATE,
   SITUACAO_CAMPO       NUMBER(11)                      not null,
   constraint PK_PT_CAMPO primary key (SQ_CAMPO)
);

comment on table PT_CAMPO is
'Campos das tabelas do portal.';

comment on column PT_CAMPO.SQ_CAMPO is
'Chave de PT_FILTRO.';

comment on column PT_CAMPO.CLIENTE is
'Chave de CO_PESSOA. Indica a que cliente o registro está ligado.';

comment on column PT_CAMPO.DAT_CADASTRO is
'Data de cadastramento do registro.';

/*==============================================================*/
/* Table: PT_CONTEUDO                                           */
/*==============================================================*/
create table PT_CONTEUDO  (
   SQ_CONTEUDO          NUMBER(11)                      not null,
   CLIENTE              NUMBER(18)                      not null,
   TITULO_CONTEUDO      VARCHAR2(200),
   TEXTO_CONTEUDO       CLOB,
   EXIBE_PO             NUMBER(11),
   EXIBE_BANNER         NUMBER(11),
   SQ_USUARIO           NUMBER(11),
   DATA_CRIACAO         DATE,
   SITUACAO_CONTEUDO    VARCHAR2(1),
   constraint PK_PT_CONTEUDO primary key (SQ_CONTEUDO)
);

comment on table PT_CONTEUDO is
'Conteúdo a ser exibido.';

comment on column PT_CONTEUDO.SQ_CONTEUDO is
'Chave de PT_CONTEUDO.';

comment on column PT_CONTEUDO.CLIENTE is
'Chave de CO_PESSOA. Indica a que cliente o registro está ligado.';

comment on column PT_CONTEUDO.TITULO_CONTEUDO is
'Título do conteúdo.';

comment on column PT_CONTEUDO.TEXTO_CONTEUDO is
'Texto que compõe o conteúdo.';

comment on column PT_CONTEUDO.SQ_USUARIO is
'Chave de SG_AUTENTICACAO. Indica a que usuário o registro está ligado.';

comment on column PT_CONTEUDO.DATA_CRIACAO is
'Data de cadastramento do registro.';

/*==============================================================*/
/* Table: PT_EIXO                                               */
/*==============================================================*/
create table PT_EIXO  (
   SQ_EIXO              NUMBER(11)                      not null,
   CLIENTE              NUMBER(18)                      not null,
   NOME_EIXO            VARCHAR2(100)                   not null,
   SITUACAO_EIXO        NUMBER(11)                      not null,
   TIPO_EIXO            NUMBER(11)                      not null,
   DAT_CADASTRO         DATE,
   constraint PK_PT_EIXO primary key (SQ_EIXO)
);

comment on table PT_EIXO is
'Títulos dos eixos utilizados na montagem de gráficos.';

comment on column PT_EIXO.SQ_EIXO is
'Chave de PT_EIXO.';

comment on column PT_EIXO.CLIENTE is
'Chave de CO_PESSOA. Indica a que cliente o registro está ligado.';

comment on column PT_EIXO.TIPO_EIXO is
'Tipo do eixo: 1 e 2 - Eixo X; 3, 4, 5 e 6 - Eixo Y.';

comment on column PT_EIXO.DAT_CADASTRO is
'Data de cadastramento do registro.';

/*==============================================================*/
/* Table: PT_EXIBICAO_CONTEUDO                                  */
/*==============================================================*/
create table PT_EXIBICAO_CONTEUDO  (
   SQ_EXIBICAO_CONTEUDO NUMBER(11)                      not null,
   CLIENTE              NUMBER(18)                      not null,
   SQ_CONTEUDO          NUMBER(11)                      not null,
   SQ_MENU              NUMBER(11)                      not null,
   DATA_EXIBICAO        VARCHAR2(45),
   SITUACAO_EXIBICAO    NUMBER(11)                      not null,
   PAGINA_INICIAL       VARCHAR2(1)                    default 'N' not null,
   constraint PK_PT_EXIBICAO_CONTEUDO primary key (SQ_EXIBICAO_CONTEUDO)
);

comment on table PT_EXIBICAO_CONTEUDO is
'Indica se o conteúdo deve ser exibido.';

comment on column PT_EXIBICAO_CONTEUDO.SQ_EXIBICAO_CONTEUDO is
'Chave de PT_EXIBICAO_CONTEUDO.';

comment on column PT_EXIBICAO_CONTEUDO.CLIENTE is
'Chave de CO_PESSOA. Indica a que cliente o registro está ligado.';

comment on column PT_EXIBICAO_CONTEUDO.SQ_CONTEUDO is
'Chave de PT_CONTEUDO. Indica a que conteúdo o registro está ligado.';

comment on column PT_EXIBICAO_CONTEUDO.SQ_MENU is
'Chave de PT_MENU. Indica a que menu o registro está ligado.';

/*==============================================================*/
/* Index: IN_PTEXICON_CONTEUDO                                  */
/*==============================================================*/
create index IN_PTEXICON_CONTEUDO on PT_EXIBICAO_CONTEUDO (
   SQ_CONTEUDO ASC
);

/*==============================================================*/
/* Index: IN_PTEXICON_MENU                                      */
/*==============================================================*/
create index IN_PTEXICON_MENU on PT_EXIBICAO_CONTEUDO (
   SQ_MENU ASC
);

/*==============================================================*/
/* Table: PT_FILTRO                                             */
/*==============================================================*/
create table PT_FILTRO  (
   SQ_FILTRO            NUMBER(11)                      not null,
   CLIENTE              NUMBER(18)                      not null,
   VALOR_FILTRO         VARCHAR2(45),
   DAT_CADASTRO         VARCHAR2(45),
   SQ_OPERADOR          NUMBER(11)                      not null,
   SQ_CAMPO             NUMBER(11)                      not null,
   SQ_PESQUISA          NUMBER(11)                      not null,
   constraint PK_PT_FILTRO primary key (SQ_FILTRO)
);

comment on table PT_FILTRO is
'Filtro de pesquisa no portal.';

comment on column PT_FILTRO.SQ_FILTRO is
'Chave de PT_FILTRO.';

comment on column PT_FILTRO.CLIENTE is
'Chave de CO_PESSOA. Indica a que cliente o registro está ligado.';

comment on column PT_FILTRO.VALOR_FILTRO is
'Valor a ser aplicado ao filtro.';

comment on column PT_FILTRO.DAT_CADASTRO is
'Data de cadastramento do registro.';

comment on column PT_FILTRO.SQ_OPERADOR is
'Chave de PT_OPERADOR. Indica a que operador o registro está ligado.';

comment on column PT_FILTRO.SQ_CAMPO is
'Chave de PT_FILTRO. Indica a que campo o registro está ligado.';

comment on column PT_FILTRO.SQ_PESQUISA is
'Chave d ePT_PESQUISA. Indica a que pesquisa o registro está ligado.';

/*==============================================================*/
/* Index: IN_PTFIL_CAMPO                                        */
/*==============================================================*/
create index IN_PTFIL_CAMPO on PT_FILTRO (
   SQ_CAMPO ASC
);

/*==============================================================*/
/* Index: IN_PTFIL_OPERADOR                                     */
/*==============================================================*/
create index IN_PTFIL_OPERADOR on PT_FILTRO (
   SQ_OPERADOR ASC
);

/*==============================================================*/
/* Index: IN_PTFIL_PESQUISA                                     */
/*==============================================================*/
create index IN_PTFIL_PESQUISA on PT_FILTRO (
   SQ_PESQUISA ASC
);

/*==============================================================*/
/* Table: PT_MENU                                               */
/*==============================================================*/
create table PT_MENU  (
   SQ_MENU              NUMBER(11)                      not null,
   CLIENTE              NUMBER(18)                      not null,
   NOME_MENU            VARCHAR2(50),
   MENU_SQ_MENU         NUMBER(11),
   SITUACAO_MENU        NUMBER(11),
   CONTEUDO_RESTRITO    CHAR                           default 'N' not null,
   constraint PK_PT_MENU primary key (SQ_MENU)
);

comment on table PT_MENU is
'Opções de menu disponíveis no portal.';

comment on column PT_MENU.SQ_MENU is
'Chave de PT_MENU. Indica a que menu o registro está ligado.';

comment on column PT_MENU.CLIENTE is
'Chave de CO_PESSOA. Indica a que cliente o registro está ligado.';

comment on column PT_MENU.NOME_MENU is
'Nome a ser exibido no menu.';

comment on column PT_MENU.MENU_SQ_MENU is
'Chave de PT_MENU. Autorrelacionamento.';

comment on column PT_MENU.SITUACAO_MENU is
'Situação do menu quanto à exibição no portal: 0 - não exibe; 1 - exibe.';

comment on column PT_MENU.CONTEUDO_RESTRITO is
'Indica se o conteúdo só pode ser visto por gestores de conteúdo.';

/*==============================================================*/
/* Index: IN_PTMEN_PTMEN_PAI                                    */
/*==============================================================*/
create index IN_PTMEN_PTMEN_PAI on PT_MENU (
   MENU_SQ_MENU ASC
);

/*==============================================================*/
/* Table: PT_OPERADOR                                           */
/*==============================================================*/
create table PT_OPERADOR  (
   SQ_OPERADOR          NUMBER(11)                      not null,
   CLIENTE              NUMBER(18)                      not null,
   NOME_OPERACAO        VARCHAR2(50)                    not null,
   OPERADOR_RELACIONAL  VARCHAR2(5)                     not null,
   SITUACAO_OPERADOR    NUMBER(11)                      not null,
   DAT_CADASTRO         DATE,
   EXPRESSAO_RELACIONAL VARCHAR2(45)                    not null,
   constraint PK_PT_OPERADOR primary key (SQ_OPERADOR)
);

comment on table PT_OPERADOR is
'Operador relacional disponível para montagem de filtros.';

comment on column PT_OPERADOR.SQ_OPERADOR is
'Chave de PT_OPERADOR.';

comment on column PT_OPERADOR.CLIENTE is
'Chave de CO_PESSOA. Indica a que cliente o registro está ligado.';

comment on column PT_OPERADOR.DAT_CADASTRO is
'Data de cadastramento do registro.';

/*==============================================================*/
/* Table: PT_PESQUISA                                           */
/*==============================================================*/
create table PT_PESQUISA  (
   SQ_PESQUISA          NUMBER(11)                      not null,
   CLIENTE              NUMBER(18)                      not null,
   NOME_PESQUISA        VARCHAR2(100)                   not null,
   SITUACAO_PESQUISA    NUMBER(11)                      not null,
   SQ_PESSOA            NUMBER(11),
   PESQUISA_PADRAO      NUMBER(11)                      not null,
   SQ_EIXO_X            NUMBER(11)                      not null,
   SQ_EIXO_Y            NUMBER(11)                      not null,
   constraint PK_PT_PESQUISA primary key (SQ_PESQUISA)
);

comment on table PT_PESQUISA is
'Pesquisas gravadas.';

comment on column PT_PESQUISA.SQ_PESQUISA is
'Chave d ePT_PESQUISA.';

comment on column PT_PESQUISA.CLIENTE is
'Chave de CO_PESSOA. Indica a que cliente o registro está ligado.';

comment on column PT_PESQUISA.SQ_PESSOA is
'Chave de CO_PESSOA. Indica a que pessoa o registro está ligado.';

comment on column PT_PESQUISA.SQ_EIXO_X is
'Chave de PT_EIXO. Indica a que eixo o registro está ligado.';

comment on column PT_PESQUISA.SQ_EIXO_Y is
'Chave de PT_EIXO. Indica a que eixo o registro está ligado.';

/*==============================================================*/
/* Index: IN_PTPES_EIXOX                                        */
/*==============================================================*/
create index IN_PTPES_EIXOX on PT_PESQUISA (
   SQ_EIXO_X ASC
);

/*==============================================================*/
/* Index: IN_PTPES_EIXOY                                        */
/*==============================================================*/
create index IN_PTPES_EIXOY on PT_PESQUISA (
   SQ_EIXO_Y ASC
);

/*==============================================================*/
/* Table: SG_AUTENTICACAO                                       */
/*==============================================================*/
create table SG_AUTENTICACAO  (
   SQ_PESSOA            number(18)                      not null,
   SQ_UNIDADE           number(10)                      not null,
   SQ_LOCALIZACAO       NUMBER(10)                      not null,
   CLIENTE              number(18)                      not null,
   USERNAME             VARCHAR2(60)                    not null,
   EMAIL                VARCHAR2(60)                    not null,
   SENHA                VARCHAR2(255)                   not null,
   ASSINATURA           VARCHAR2(255),
   GESTOR_SEGURANCA     VARCHAR2(1)                    default 'N' not null
      constraint CKC_SGAUT_GESSEG
               check (GESTOR_SEGURANCA in ('S','N') and GESTOR_SEGURANCA = upper(GESTOR_SEGURANCA)),
   GESTOR_SISTEMA       VARCHAR2(1)                    default 'N' not null
      constraint CKC_SGAUT_GESSIS
               check (GESTOR_SISTEMA in ('S','N') and GESTOR_SISTEMA = upper(GESTOR_SISTEMA)),
   ULTIMA_TROCA_SENHA   DATE                           default SYSDATE not null,
   ULTIMA_TROCA_ASSIN   DATE                           default SYSDATE not null,
   ATIVO                VARCHAR2(1)                    default 'S' not null
      constraint CKC_ATIVO_SG_AUTEN check (ATIVO in ('S','N') and ATIVO = upper(ATIVO)),
   TENTATIVAS_SENHA     NUMBER(2)                      default 0 not null,
   TENTATIVAS_ASSIN     NUMBER(2)                      default 0 not null,
   TIPO_AUTENTICACAO    VARCHAR2(1)                    default 'B' not null,
   GESTOR_CONTEUDO      VARCHAR2(1)                    default 'N' not null
      constraint CKC_SGAUT_GESCON check (GESTOR_CONTEUDO in ('S','N') and GESTOR_CONTEUDO = upper(GESTOR_CONTEUDO)),
   GESTOR_DASHBOARD     VARCHAR2(1)                    default 'N' not null
      constraint CKC_SGAUT_GESDAS check (GESTOR_DASHBOARD in ('S','N') and GESTOR_DASHBOARD = upper(GESTOR_DASHBOARD)),
   GESTOR_PORTAL        VARCHAR2(1)                    default 'N' not null
      constraint CKC_SGAUT_GESPOR check (GESTOR_PORTAL in ('S','N') and GESTOR_PORTAL = upper(GESTOR_PORTAL)),
   constraint PK_SG_AUTENTICACAO primary key (SQ_PESSOA)
);

comment on table SG_AUTENTICACAO is
'Armazena os dados necessários para o usuário autenticar-se na aplicação.';

comment on column SG_AUTENTICACAO.SQ_PESSOA is
'Chave de SG_AUTENTICACAO, importada de CO_PESSOA.';

comment on column SG_AUTENTICACAO.SQ_UNIDADE is
'Chave de EO_UNIDADE. Indica a que unidade o registro está ligado.';

comment on column SG_AUTENTICACAO.SQ_LOCALIZACAO is
'Chave de EO_LOCALIZACAO. Indica a que localização o registro está ligado.';

comment on column SG_AUTENTICACAO.CLIENTE is
'Chave de CO_PESSOA. Indica a que cliente o registro está ligado.';

comment on column SG_AUTENTICACAO.USERNAME is
'Nome de usuário vinculado à pessoa.';

comment on column SG_AUTENTICACAO.EMAIL is
'e-Mail do usuário.';

comment on column SG_AUTENTICACAO.SENHA is
'Senha do usuário.';

comment on column SG_AUTENTICACAO.ASSINATURA is
'Assinatura eletrônica do usuário.';

comment on column SG_AUTENTICACAO.GESTOR_SEGURANCA is
'Indica se o usuário tem acesso geral nas funcionalidades do módulo de controle.';

comment on column SG_AUTENTICACAO.GESTOR_SISTEMA is
'Indica se o usuário tem acesso geral a funcionalidades e dados, exeto as de controle.';

comment on column SG_AUTENTICACAO.ULTIMA_TROCA_SENHA is
'Data da última alteração da assinatura senha.';

comment on column SG_AUTENTICACAO.ULTIMA_TROCA_ASSIN is
'Data da última alteração da assinatura eletrônica.';

comment on column SG_AUTENTICACAO.ATIVO is
'Indica se este registro está disponível para ligação a outras tabelas.';

comment on column SG_AUTENTICACAO.TENTATIVAS_SENHA is
'Número de vezes que a senha de acesso foi informada incorretamente';

comment on column SG_AUTENTICACAO.TENTATIVAS_ASSIN is
'Número de vezes que a assinatura eletrônica foi informada incorretamente';

comment on column SG_AUTENTICACAO.TIPO_AUTENTICACAO is
'Tipo da autenticação do usuário: A - MS Active Directory; O - Open LDAP; B - Banco de dados.';

comment on column SG_AUTENTICACAO.GESTOR_CONTEUDO is
'Indica se o usuário é gestor de conteúdo do portal.';

comment on column SG_AUTENTICACAO.GESTOR_DASHBOARD is
'Indica se o usuário é gestor de dashboard.';

comment on column SG_AUTENTICACAO.GESTOR_PORTAL is
'Indica se o usuário tem acesso geral como gestor do portal.';

/*==============================================================*/
/* Index: IN_SGAUT_USERNAME                                     */
/*==============================================================*/
create index IN_SGAUT_USERNAME on SG_AUTENTICACAO (
   USERNAME ASC
);

/*==============================================================*/
/* Index: IN_SGAUT_UNIDADE                                      */
/*==============================================================*/
create index IN_SGAUT_UNIDADE on SG_AUTENTICACAO (
   SQ_UNIDADE ASC,
   SQ_PESSOA ASC
);

/*==============================================================*/
/* Index: IN_SGAUT_LOCAL                                        */
/*==============================================================*/
create index IN_SGAUT_LOCAL on SG_AUTENTICACAO (
   SQ_LOCALIZACAO ASC,
   SQ_PESSOA ASC
);

/*==============================================================*/
/* Index: IN_SGAUT_CLIENTE                                      */
/*==============================================================*/
create index IN_SGAUT_CLIENTE on SG_AUTENTICACAO (
   CLIENTE ASC,
   USERNAME ASC
);

/*==============================================================*/
/* Index: IN_SGAUT_ATIVO                                        */
/*==============================================================*/
create index IN_SGAUT_ATIVO on SG_AUTENTICACAO (
   SQ_PESSOA ASC,
   ATIVO ASC
);

/*==============================================================*/
/* Table: SG_AUTENTICACAO_TEMP                                  */
/*==============================================================*/
create table SG_AUTENTICACAO_TEMP  (
   CLIENTE              number(18)                      not null,
   CPF                  VARCHAR2(14)                    not null,
   NOME                 varchar2(60)                    not null,
   NOME_RESUMIDO        varchar2(15)                    not null,
   SEXO                 VARCHAR2(1)                     not null
      constraint CKC_SEXO_SG_AUTEN check (SEXO in ('M','F')),
   EMAIL                VARCHAR2(60)                    not null,
   VINCULO              NUMBER(1)                       not null,
   UNIDADE              VARCHAR2(40)                    not null,
   SALA                 VARCHAR2(20)                    not null,
   RAMAL                VARCHAR2(20)                    not null,
   EFETIVAR             VARCHAR2(1)                    default 'N' not null
      constraint CKC_ATIVO_SGAUTTEM check (EFETIVAR in ('S','N') and EFETIVAR = upper(EFETIVAR)),
   EFETIVADO            VARCHAR2(1)                    default 'N' not null
      constraint CKC_EFETIVADO_SG_AUTEN check (EFETIVADO in ('S','N') and EFETIVADO = upper(EFETIVADO)),
   EFETIVACAO           DATE,
   constraint PK_SG_AUTENTICACAO_TEMP primary key (CLIENTE, CPF)
);

comment on table SG_AUTENTICACAO_TEMP is
'Armazena dados temporários de usuários, para posterior inserção na tabela definitiva.';

comment on column SG_AUTENTICACAO_TEMP.CLIENTE is
'Indica o cliente a que pertence o usuário.';

comment on column SG_AUTENTICACAO_TEMP.CPF is
'CPF do usuário, a ser convertido em username caso sua criação seja efetivada.';

comment on column SG_AUTENTICACAO_TEMP.NOME is
'Nome do usuário.';

comment on column SG_AUTENTICACAO_TEMP.NOME_RESUMIDO is
'Nome pelo qual a pessoa é conhecida (apelido, cognome etc.)';

comment on column SG_AUTENTICACAO_TEMP.SEXO is
'Sexo do usuário (F) Feminino (M) Masculino.';

comment on column SG_AUTENTICACAO_TEMP.EMAIL is
'e-Mail do usuário.';

comment on column SG_AUTENTICACAO_TEMP.VINCULO is
'Vínculo que o usuário mantém com a organização.';

comment on column SG_AUTENTICACAO_TEMP.UNIDADE is
'Unidade de exercício do usuário.';

comment on column SG_AUTENTICACAO_TEMP.SALA is
'Sala (localização) do usuário.';

comment on column SG_AUTENTICACAO_TEMP.RAMAL is
'Ramal do usuário.';

comment on column SG_AUTENTICACAO_TEMP.EFETIVAR is
'Indica se o usuário deve ser efetivado na tabela definitiva de usuários do sistema.';

comment on column SG_AUTENTICACAO_TEMP.EFETIVADO is
'Indica se o registro foi efetivado na base de usuários.';

comment on column SG_AUTENTICACAO_TEMP.EFETIVACAO is
'Data da efetivação do usuário na tabela definitiva.';

/*==============================================================*/
/* Table: SG_PERFIL_MENU                                        */
/*==============================================================*/
create table SG_PERFIL_MENU  (
   SQ_TIPO_VINCULO      number(18)                      not null,
   SQ_MENU              NUMBER(18)                      not null,
   SQ_PESSOA_ENDERECO   NUMBER(18)                      not null,
   constraint PK_SG_PERFIL_MENU primary key (SQ_TIPO_VINCULO, SQ_MENU, SQ_PESSOA_ENDERECO)
);

comment on table SG_PERFIL_MENU is
'Registra as permissões de perfis às opções do menu.';

comment on column SG_PERFIL_MENU.SQ_TIPO_VINCULO is
'Chave de CO_TIPO_VINCULO. Indica a que tipo de vínculo o registro está ligado.';

comment on column SG_PERFIL_MENU.SQ_MENU is
'Chave de SIW_MENU_ENDERECO.';

comment on column SG_PERFIL_MENU.SQ_PESSOA_ENDERECO is
'Chave de CO_PESSOA_ENDERECO. Indica a que endereço o registro está ligado.';

/*==============================================================*/
/* Index: IN_SGPERMEN_SQMENU                                    */
/*==============================================================*/
create index IN_SGPERMEN_SQMENU on SG_PERFIL_MENU (
   SQ_MENU ASC,
   SQ_PESSOA_ENDERECO ASC,
   SQ_TIPO_VINCULO ASC
);

/*==============================================================*/
/* Index: IN_SGPERMENU_SQEND                                    */
/*==============================================================*/
create index IN_SGPERMENU_SQEND on SG_PERFIL_MENU (
   SQ_PESSOA_ENDERECO ASC,
   SQ_MENU ASC,
   SQ_TIPO_VINCULO ASC
);

/*==============================================================*/
/* Table: SG_PESSOA_MAIL                                        */
/*==============================================================*/
create table SG_PESSOA_MAIL  (
   SQ_PESSOA_MAIL       NUMBER(18)                      not null,
   SQ_PESSOA            number(18)                      not null,
   SQ_MENU              NUMBER(18)                      not null,
   ALERTA_DIARIO        VARCHAR2(1)                    default 'S' not null
      constraint CKC_ALERTA_DIARIO_SG_PESSO check (ALERTA_DIARIO in ('S','N') and ALERTA_DIARIO = upper(ALERTA_DIARIO)),
   TRAMITACAO           VARCHAR2(1)                    default 'S' not null
      constraint CKC_TRAMITACAO_SG_PESSO check (TRAMITACAO in ('S','N') and TRAMITACAO = upper(TRAMITACAO)),
   CONCLUSAO            VARCHAR2(1)                    default 'S' not null
      constraint CKC_CONCLUSAO_SG_PESSO check (CONCLUSAO in ('S','N') and CONCLUSAO = upper(CONCLUSAO)),
   RESPONSABILIDADE     VARCHAR2(1)                    default 'S' not null
      constraint CKC_RESPONSABILIDADE_SG_PESSO check (RESPONSABILIDADE in ('S','N') and RESPONSABILIDADE = upper(RESPONSABILIDADE)),
   constraint PK_SG_PESSOA_MAIL primary key (SQ_PESSOA_MAIL)
);

comment on table SG_PESSOA_MAIL is
'Registra as configurações de envio de e-mail para o usuário.';

comment on column SG_PESSOA_MAIL.SQ_PESSOA_MAIL is
'Chave de SG_PESSOA_MAIL.';

comment on column SG_PESSOA_MAIL.SQ_PESSOA is
'Chave de SG_AUTENTICACAO. Indica a que usuário o registro está ligado.';

comment on column SG_PESSOA_MAIL.SQ_MENU is
'Chave de SIW_MENU. Indica a que serviço o registro está ligado.';

comment on column SG_PESSOA_MAIL.ALERTA_DIARIO is
'Indica se o usuário deve receber e-mail de alerta diário para o serviço indicado.';

comment on column SG_PESSOA_MAIL.TRAMITACAO is
'Indica se o usuário deve receber e-mail de tramitação para o serviço indicado.';

comment on column SG_PESSOA_MAIL.CONCLUSAO is
'Indica se o usuário deve receber e-mail de conclusão  para o serviço indicado.';

comment on column SG_PESSOA_MAIL.RESPONSABILIDADE is
'Indica se o usuário deve receber e-mail comunicando responsabilidade para o serviço indicado. No caso de projetos, refere-se à responsabilidade por etapa.';

/*==============================================================*/
/* Index: IN_SGPESMAI_PESSOA                                    */
/*==============================================================*/
create index IN_SGPESMAI_PESSOA on SG_PESSOA_MAIL (
   SQ_PESSOA ASC,
   SQ_PESSOA_MAIL ASC
);

/*==============================================================*/
/* Index: IN_SGPESMAI_MENU                                      */
/*==============================================================*/
create index IN_SGPESMAI_MENU on SG_PESSOA_MAIL (
   SQ_MENU ASC,
   SQ_PESSOA_MAIL ASC
);

/*==============================================================*/
/* Table: SG_PESSOA_MENU                                        */
/*==============================================================*/
create table SG_PESSOA_MENU  (
   SQ_PESSOA            number(18)                      not null,
   SQ_MENU              NUMBER(18)                      not null,
   SQ_PESSOA_ENDERECO   NUMBER(18)                      not null,
   constraint PK_SG_PESSOA_MENU primary key (SQ_PESSOA, SQ_MENU, SQ_PESSOA_ENDERECO)
);

comment on table SG_PESSOA_MENU is
'Permissões que a pessoa têm às opções do menu';

comment on column SG_PESSOA_MENU.SQ_PESSOA is
'Chave de CO_PESSOA. Indica a que pessoa o registro está ligado.';

comment on column SG_PESSOA_MENU.SQ_MENU is
'Chave de SIW_MENU. Indica a que opção do menu o registro está ligado.';

comment on column SG_PESSOA_MENU.SQ_PESSOA_ENDERECO is
'Chave de CO_PESSOA_ENDERECO. Indica a que endereço o registro está ligado.';

/*==============================================================*/
/* Index: IN_SGPESMEN_SQMEN                                     */
/*==============================================================*/
create index IN_SGPESMEN_SQMEN on SG_PESSOA_MENU (
   SQ_MENU ASC,
   SQ_PESSOA ASC
);

/*==============================================================*/
/* Index: IN_SGPESMEN_END                                       */
/*==============================================================*/
create index IN_SGPESMEN_END on SG_PESSOA_MENU (
   SQ_PESSOA_ENDERECO ASC,
   SQ_MENU ASC
);

/*==============================================================*/
/* Table: SG_PESSOA_MODULO                                      */
/*==============================================================*/
create table SG_PESSOA_MODULO  (
   SQ_PESSOA            NUMBER(18)                      not null,
   CLIENTE              number(18)                      not null,
   SQ_MODULO            NUMBER(18)                      not null,
   SQ_PESSOA_ENDERECO   NUMBER(18)                      not null,
   constraint PK_SG_PESSOA_MODULO primary key (SQ_PESSOA, CLIENTE, SQ_MODULO, SQ_PESSOA_ENDERECO)
);

comment on table SG_PESSOA_MODULO is
'Registra os gestores de módulo, por endereço da organização.';

comment on column SG_PESSOA_MODULO.SQ_PESSOA is
'Chave de CO_PESSOA. Indica a que pessoa o registro está ligado.';

comment on column SG_PESSOA_MODULO.CLIENTE is
'Cliente. Chave de SIW_CLIENTE_MODULO.';

comment on column SG_PESSOA_MODULO.SQ_MODULO is
'Chave de SIW_MODULO. Indica a que módulo o registro está ligado.';

comment on column SG_PESSOA_MODULO.SQ_PESSOA_ENDERECO is
'Chave de CO_PESSOA_ENDERECO. Indica a que endereço o registro está ligado.';

/*==============================================================*/
/* Index: IN_SGPESMOD_END                                       */
/*==============================================================*/
create index IN_SGPESMOD_END on SG_PESSOA_MODULO (
   CLIENTE ASC,
   SQ_PESSOA_ENDERECO ASC,
   SQ_MODULO ASC,
   SQ_PESSOA ASC
);

/*==============================================================*/
/* Index: IN_SGPESMOD_MODULO                                    */
/*==============================================================*/
create index IN_SGPESMOD_MODULO on SG_PESSOA_MODULO (
   CLIENTE ASC,
   SQ_MODULO ASC,
   SQ_PESSOA_ENDERECO ASC,
   SQ_PESSOA ASC
);

/*==============================================================*/
/* Index: IN_SGPESMOD_CLI                                       */
/*==============================================================*/
create index IN_SGPESMOD_CLI on SG_PESSOA_MODULO (
   CLIENTE ASC,
   SQ_PESSOA ASC,
   SQ_MODULO ASC,
   SQ_PESSOA_ENDERECO ASC
);

/*==============================================================*/
/* Table: SG_PESSOA_UNIDADE                                     */
/*==============================================================*/
create table SG_PESSOA_UNIDADE  (
   SQ_PESSOA            number(18),
   SQ_UNIDADE           NUMBER(10)
);

comment on table SG_PESSOA_UNIDADE is
'Registra as unidades que o usuário tem acesso. ';

comment on column SG_PESSOA_UNIDADE.SQ_PESSOA is
'Chave de SG_AUTENTICACAO, importada de CO_PESSOA.';

comment on column SG_PESSOA_UNIDADE.SQ_UNIDADE is
'Chave de EO_UNIDADE. Indica a que unidade o registro está ligado.';

/*==============================================================*/
/* Index: IN_SGPESUNI_INVERSA                                   */
/*==============================================================*/
create index IN_SGPESUNI_INVERSA on SG_PESSOA_UNIDADE (
   SQ_UNIDADE ASC,
   SQ_PESSOA ASC
);

/*==============================================================*/
/* Table: SG_TRAMITE_PESSOA                                     */
/*==============================================================*/
create table SG_TRAMITE_PESSOA  (
   SQ_PESSOA            number(18)                      not null,
   SQ_SIW_TRAMITE       NUMBER(18)                      not null,
   SQ_PESSOA_ENDERECO   NUMBER(18)                      not null,
   constraint PK_SG_TRAMITE_PESSOA primary key (SQ_PESSOA, SQ_SIW_TRAMITE, SQ_PESSOA_ENDERECO)
);

comment on table SG_TRAMITE_PESSOA is
'Permissões da pessoa a um trâmite de serviço';

comment on column SG_TRAMITE_PESSOA.SQ_PESSOA is
'Chave de CO_PESSOA. Indica a que pessoa o registro está ligado.';

comment on column SG_TRAMITE_PESSOA.SQ_SIW_TRAMITE is
'Chave de SIW_TRAMITE. Indica a que trâmite o registro está ligado.';

comment on column SG_TRAMITE_PESSOA.SQ_PESSOA_ENDERECO is
'Chave de CO_PESSOA_ENDERECO. Indica a que endereço o registro está ligado.';

/*==============================================================*/
/* Index: IN_SGTRAPES_END                                       */
/*==============================================================*/
create index IN_SGTRAPES_END on SG_TRAMITE_PESSOA (
   SQ_PESSOA_ENDERECO ASC,
   SQ_PESSOA ASC,
   SQ_SIW_TRAMITE ASC
);

/*==============================================================*/
/* Table: SIW_ARQUIVO                                           */
/*==============================================================*/
create table SIW_ARQUIVO  (
   SQ_SIW_ARQUIVO       NUMBER(18)                      not null,
   CLIENTE              NUMBER(18)                      not null,
   SQ_TIPO_ARQUIVO      NUMBER(18),
   NOME                 VARCHAR2(255)                   not null,
   DESCRICAO            VARCHAR2(1000),
   INCLUSAO             DATE                           default sysdate not null,
   TAMANHO              NUMBER(18)                     default 0 not null,
   TIPO                 VARCHAR2(100),
   CAMINHO              VARCHAR2(255),
   NOME_ORIGINAL        VARCHAR2(255)                   not null,
   constraint PK_SIW_ARQUIVO primary key (SQ_SIW_ARQUIVO)
);

comment on table SIW_ARQUIVO is
'Registra o link para os arquivos físicos recebidos por upload.';

comment on column SIW_ARQUIVO.SQ_SIW_ARQUIVO is
'Chave de SIW_ARQUIVO. Indica a que arquivo o registro está ligado.';

comment on column SIW_ARQUIVO.CLIENTE is
'Chave de CO_PESSOA. Indica a que cliente o registro está ligado.';

comment on column SIW_ARQUIVO.SQ_TIPO_ARQUIVO is
'Chave de SIW_TIPO_ARQUIVO. Indica a que tipo de arquivo o registro está ligado.';

comment on column SIW_ARQUIVO.NOME is
'Nome original do arquivo.';

comment on column SIW_ARQUIVO.DESCRICAO is
'Descrição do conteúdo do arquivo.';

comment on column SIW_ARQUIVO.INCLUSAO is
'Data da inclusão do arquivo.';

comment on column SIW_ARQUIVO.TAMANHO is
'Tamanho do arquivo em bytes.';

comment on column SIW_ARQUIVO.TIPO is
'Tipo do arquivo, a ser usado na visualização.';

comment on column SIW_ARQUIVO.CAMINHO is
'Caminho físico do arquivo.';

comment on column SIW_ARQUIVO.NOME_ORIGINAL is
'Nome original do arquivo.';

/*==============================================================*/
/* Index: IN_SIWARQ_CLIENTE                                     */
/*==============================================================*/
create index IN_SIWARQ_CLIENTE on SIW_ARQUIVO (
   CLIENTE ASC,
   SQ_SIW_ARQUIVO ASC
);

/*==============================================================*/
/* Table: SIW_CLIENTE                                           */
/*==============================================================*/
create table SIW_CLIENTE  (
   SQ_PESSOA            number(18)                      not null,
   SQ_CIDADE_PADRAO     NUMBER(18)                      not null,
   SQ_AGENCIA_PADRAO    NUMBER(18),
   ATIVACAO             DATE                            not null,
   BLOQUEIO             DATE,
   DESATIVACAO          DATE,
   TIPO_AUTENTICACAO    NUMBER(1)                       not null
      constraint CKC_SIWCLI_TPAUT
               check (TIPO_AUTENTICACAO in (1,2)),
   SMTP_SERVER          VARCHAR2(60),
   SIW_EMAIL_NOME       VARCHAR2(60),
   SIW_EMAIL_CONTA      VARCHAR2(60),
   SIW_EMAIL_SENHA      VARCHAR2(60),
   LOGO                 VARCHAR2(60),
   LOGO1                VARCHAR2(60),
   TAMANHO_MIN_SENHA    NUMBER(2)                      default 6 not null,
   TAMANHO_MAX_SENHA    NUMBER(2)                      default 15 not null,
   DIAS_VIG_SENHA       NUMBER(3)                      default 90 not null,
   DIAS_AVISO_EXPIR     NUMBER(3)                      default 10 not null,
   MAXIMO_TENTATIVAS    NUMBER(2)                      default 4 not null,
   FUNDO                VARCHAR2(60),
   UPLOAD_MAXIMO        NUMBER(18)                     default 0 not null,
   ENVIA_MAIL_TRAMITE   VARCHAR2(1)                    default 'S' not null
      constraint CKC_ENVIA_MAIL_TRAMIT_SIW_CLIE check (ENVIA_MAIL_TRAMITE in ('S','N') and ENVIA_MAIL_TRAMITE = upper(ENVIA_MAIL_TRAMITE)),
   ENVIA_MAIL_ALERTA    VARCHAR2(1)                    default 'S' not null
      constraint CKC_ENVIA_MAIL_ALERTA_SIW_CLIE check (ENVIA_MAIL_ALERTA in ('S','N') and ENVIA_MAIL_ALERTA = upper(ENVIA_MAIL_ALERTA)),
   GEOREFERENCIA        VARCHAR2(1)                    default 'N' not null
      constraint CKC_GEOREFERENCIA_SIW_CLIE check (GEOREFERENCIA in ('S','N') and GEOREFERENCIA = upper(GEOREFERENCIA)),
   GOOGLEMAPS_KEY       VARCHAR2(2000),
   ATA_REGISTRO_PRECO   VARCHAR2(1)                    default 'S' not null
      constraint CKC_ATA_REGISTRO_PREC_SIW_CLIE check (ATA_REGISTRO_PRECO in ('S','N') and ATA_REGISTRO_PRECO = upper(ATA_REGISTRO_PRECO)),
   AD_ACCOUNT_SUFIX     VARCHAR2(40),
   AD_BASE_DN           VARCHAR2(40),
   AD_DOMAIN_CONTROLERS VARCHAR2(40),
   OL_ACCOUNT_SUFIX     VARCHAR2(40),
   OL_BASE_DN           VARCHAR2(40),
   OL_DOMAIN_CONTROLERS VARCHAR2(40),
   SYSLOG_SERVER_NAME   VARCHAR2(30),
   SYSLOG_SERVER_PROTOCOL VARCHAR2(10),
   SYSLOG_SERVER_PORT   NUMBER(5),
   SYSLOG_FACILITY      NUMBER(2),
   SYSLOG_FQDN          VARCHAR2(30),
   SYSLOG_TIMEOUT       NUMBER(2),
   SYSLOG_LEVEL_PASS_OK NUMBER(2),
   SYSLOG_LEVEL_PASS_ER NUMBER(2),
   SYSLOG_LEVEL_SIGN_ER NUMBER(2),
   SYSLOG_LEVEL_WRITE_OK NUMBER(2),
   SYSLOG_LEVEL_WRITE_ER NUMBER(2),
   SYSLOG_LEVEL_RES_ER  NUMBER(2),
   constraint PK_SIW_CLIENTE primary key (SQ_PESSOA)
);

comment on table SIW_CLIENTE is
'Armazena os clientes do SIW';

comment on column SIW_CLIENTE.SQ_PESSOA is
'Chave de CO_PESSOA. Indica a que pessoa o registro está ligado.';

comment on column SIW_CLIENTE.SQ_CIDADE_PADRAO is
'Chave de CO_CIDADE. Indica a que cidade padrão do cliente, a ser usada nas telas onde a cidade é solicitada.';

comment on column SIW_CLIENTE.SQ_AGENCIA_PADRAO is
'Chave de CO_AGENCIA. Indica a agência padrao do cliente, a ser sugerida nas telas onde a agência é solicitada.';

comment on column SIW_CLIENTE.ATIVACAO is
'Data de ativação do cliente no SIW';

comment on column SIW_CLIENTE.BLOQUEIO is
'Data de bloqueio do cliente no SIW';

comment on column SIW_CLIENTE.DESATIVACAO is
'Data de desativação do cliente no SIW.';

comment on column SIW_CLIENTE.TIPO_AUTENTICACAO is
'Armazena o tipo de autenticação desejado pelo cliente.';

comment on column SIW_CLIENTE.SMTP_SERVER is
'Endereço eletrônico do servidor SMTP.';

comment on column SIW_CLIENTE.SIW_EMAIL_NOME is
'Nome a ser usado como sender de uma mensagem automática de e-mail.';

comment on column SIW_CLIENTE.SIW_EMAIL_CONTA is
'Conta de e-mail para conexão ao servidor SMTP.';

comment on column SIW_CLIENTE.SIW_EMAIL_SENHA is
'Senha da conta de e-mail para conexão ao servidor SMTP.';

comment on column SIW_CLIENTE.LOGO is
'Nome do arquivo a ser usado como logotipo em relatórios.';

comment on column SIW_CLIENTE.LOGO1 is
'Nome do arquivo a ser usado como logotipo no menu.';

comment on column SIW_CLIENTE.TAMANHO_MIN_SENHA is
'Tamanho mínimo aceito pelo sistema para a senha de acesso e assinatura eletrônica';

comment on column SIW_CLIENTE.TAMANHO_MAX_SENHA is
'Tamanho máximo aceito pelo sistema para a senha de acesso e assinatura eletrônica';

comment on column SIW_CLIENTE.DIAS_VIG_SENHA is
'Dias de vigência da senha de acesso/assinatura eletrônica antes que o sistema bloqueie automaticamente';

comment on column SIW_CLIENTE.DIAS_AVISO_EXPIR is
'Dias antes da expiração da senha de acesso/assinatura eletrônica que o sistema avisará o usuário';

comment on column SIW_CLIENTE.MAXIMO_TENTATIVAS is
'Número de tentativas inválidas de uso da senha ou assinatura antes do sistema bloquear o acesso.';

comment on column SIW_CLIENTE.FUNDO is
'Nome do arquivo que contém a imagem de fundo do menu.';

comment on column SIW_CLIENTE.UPLOAD_MAXIMO is
'Tamanho máximo, em bytes, que um upload pode aceitar.';

comment on column SIW_CLIENTE.ENVIA_MAIL_TRAMITE is
'Indica se devem ser encaminhados e-mail de alerta quando houver tramitação ou conclusão das solicitações do cliente.';

comment on column SIW_CLIENTE.ENVIA_MAIL_ALERTA is
'Indica se devem ser encaminhados e-mail de alerta de proximidade das solicitações do cliente.';

comment on column SIW_CLIENTE.GEOREFERENCIA is
'Indica se o cliente tem acesso às funcionalidades de geo-referenciamento.';

comment on column SIW_CLIENTE.GOOGLEMAPS_KEY is
'Chave de acesso ao Web Service do Google Maps.';

comment on column SIW_CLIENTE.ATA_REGISTRO_PRECO is
'Se o cliente tiver módulo de licitações, indica se há controle de ARP.';

comment on column SIW_CLIENTE.AD_ACCOUNT_SUFIX is
'Sufixo das contas de usuário para autenticação no microsoft active directory.';

comment on column SIW_CLIENTE.AD_BASE_DN is
'Nome base do domínio para autenticação no microsoft active directory.';

comment on column SIW_CLIENTE.AD_DOMAIN_CONTROLERS is
'Lista de controladores active directory, separados por vírgula, sem espaços.';

comment on column SIW_CLIENTE.OL_ACCOUNT_SUFIX is
'Sufixo das contas de usuário para autenticação no Open LDAP.';

comment on column SIW_CLIENTE.OL_BASE_DN is
'Nome base do domínio para autenticação no Open LDAP.';

comment on column SIW_CLIENTE.OL_DOMAIN_CONTROLERS is
'Lista de controladores Open LDAP, separados por vírgula, sem espaços.';

comment on column SIW_CLIENTE.SYSLOG_SERVER_NAME is
'Endereço do servidor syslog. IP ou nome.';

comment on column SIW_CLIENTE.SYSLOG_SERVER_PROTOCOL is
'Protocolo do servidor syslog. Default UDP.';

comment on column SIW_CLIENTE.SYSLOG_SERVER_PORT is
'Porta do servidor syslog. Default 514.';

comment on column SIW_CLIENTE.SYSLOG_FACILITY is
'Categoria do evento.';

comment on column SIW_CLIENTE.SYSLOG_FQDN is
'Nome base do domínio para syslog.';

comment on column SIW_CLIENTE.SYSLOG_TIMEOUT is
'Tempo limite para a conexão (segundos).';

comment on column SIW_CLIENTE.SYSLOG_LEVEL_PASS_OK is
'Nível de erro para login correto.';

comment on column SIW_CLIENTE.SYSLOG_LEVEL_PASS_ER is
'Nível de erro para login incorreto.';

comment on column SIW_CLIENTE.SYSLOG_LEVEL_SIGN_ER is
'Nível de erro para assinatura eletrônica incorreta.';

comment on column SIW_CLIENTE.SYSLOG_LEVEL_WRITE_OK is
'Nível de erro para insert, update e delete correto.';

comment on column SIW_CLIENTE.SYSLOG_LEVEL_WRITE_ER is
'Nível de erro para insert, update e delete incorreto.';

comment on column SIW_CLIENTE.SYSLOG_LEVEL_RES_ER is
'Nível de erro para problema no acesso a recursos (banco de dados, servidor de e-mail etc).';

/*==============================================================*/
/* Index: IN_SIWCLI_CIDPD                                       */
/*==============================================================*/
create index IN_SIWCLI_CIDPD on SIW_CLIENTE (
   SQ_CIDADE_PADRAO ASC
);

/*==============================================================*/
/* Index: IN_SIWCLI_AGEPD                                       */
/*==============================================================*/
create index IN_SIWCLI_AGEPD on SIW_CLIENTE (
   SQ_AGENCIA_PADRAO ASC
);

/*==============================================================*/
/* Index: IN_SIWCLI_ATIVACAO                                    */
/*==============================================================*/
create index IN_SIWCLI_ATIVACAO on SIW_CLIENTE (
   ATIVACAO ASC
);

/*==============================================================*/
/* Index: IN_SIWCLI_BLOQUEIO                                    */
/*==============================================================*/
create index IN_SIWCLI_BLOQUEIO on SIW_CLIENTE (
   BLOQUEIO ASC
);

/*==============================================================*/
/* Index: IN_SIWCLI_DESAT                                       */
/*==============================================================*/
create index IN_SIWCLI_DESAT on SIW_CLIENTE (
   DESATIVACAO ASC
);

/*==============================================================*/
/* Index: IN_SIWCLI_AUTENT                                      */
/*==============================================================*/
create index IN_SIWCLI_AUTENT on SIW_CLIENTE (
   TIPO_AUTENTICACAO ASC
);

/*==============================================================*/
/* Table: SIW_CLIENTE_MODULO                                    */
/*==============================================================*/
create table SIW_CLIENTE_MODULO  (
   SQ_PESSOA            number(18)                      not null,
   SQ_MODULO            NUMBER(18)                      not null,
   constraint PK_SIW_CLIENTE_MODULO primary key (SQ_PESSOA, SQ_MODULO)
);

comment on table SIW_CLIENTE_MODULO is
'Armazena os módulos contratados pelos clientes';

comment on column SIW_CLIENTE_MODULO.SQ_PESSOA is
'Chave de CO_PESSOA. Indica a que pessoa o registro está ligado.';

comment on column SIW_CLIENTE_MODULO.SQ_MODULO is
'Chave de SIW_MODULO. Indica a que módulo o registro está ligado.';

/*==============================================================*/
/* Index: IN_SIWCLIMOD_MOD                                      */
/*==============================================================*/
create index IN_SIWCLIMOD_MOD on SIW_CLIENTE_MODULO (
   SQ_MODULO ASC,
   SQ_PESSOA ASC
);

/*==============================================================*/
/* Table: SIW_COORDENADA                                        */
/*==============================================================*/
create table SIW_COORDENADA  (
   SQ_SIW_COORDENADA    NUMBER(18)                      not null,
   CLIENTE              NUMBER(18)                      not null,
   NOME                 VARCHAR2(100)                   not null,
   LATITUDE             NUMBER(18,10)                   not null,
   LONGITUDE            NUMBER(18,10)                   not null,
   ICONE                VARCHAR2(30)                    not null,
   TIPO                 VARCHAR2(30)                    not null,
   constraint PK_SIW_COORDENADA primary key (SQ_SIW_COORDENADA)
);

comment on table SIW_COORDENADA is
'Registras as coordenadas geográficas de um ponto.';

comment on column SIW_COORDENADA.SQ_SIW_COORDENADA is
'Chave de SIW_COORDENADA.';

comment on column SIW_COORDENADA.CLIENTE is
'Chave de CO_PESSOA. Indica a que cliente o registro está ligado.';

comment on column SIW_COORDENADA.NOME is
'Nome para exibição da coordenada no mapa.';

comment on column SIW_COORDENADA.LATITUDE is
'Latitude do ponto.';

comment on column SIW_COORDENADA.LONGITUDE is
'Longitude do ponto.';

comment on column SIW_COORDENADA.ICONE is
'Icone a ser exibido.';

comment on column SIW_COORDENADA.TIPO is
'Indica a que objeto a coordenada está ligada. ENDERECO, PROJETO, ETAPA...';

/*==============================================================*/
/* Index: IN_SIWCOO_CLIENTE                                     */
/*==============================================================*/
create index IN_SIWCOO_CLIENTE on SIW_COORDENADA (
   CLIENTE ASC,
   SQ_SIW_COORDENADA ASC
);

/*==============================================================*/
/* Table: SIW_COORDENADA_ENDERECO                               */
/*==============================================================*/
create table SIW_COORDENADA_ENDERECO  (
   SQ_SIW_COORDENADA    NUMBER(18)                      not null,
   SQ_PESSOA_ENDERECO   NUMBER(18)                      not null,
   constraint PK_SIW_COORDENADA_ENDERECO primary key (SQ_SIW_COORDENADA, SQ_PESSOA_ENDERECO)
);

comment on table SIW_COORDENADA_ENDERECO is
'Vincula uma coordenada geográfica a um endereço físico.';

comment on column SIW_COORDENADA_ENDERECO.SQ_SIW_COORDENADA is
'Chave de SIW_COORDENADA. Indica a que coordenada o registro está ligado.';

comment on column SIW_COORDENADA_ENDERECO.SQ_PESSOA_ENDERECO is
'Chave de CO_PESSOA_ENDERECO. Indica a que endereço o registro está ligado.';

/*==============================================================*/
/* Index: IN_SIWCOOEND_INV                                      */
/*==============================================================*/
create index IN_SIWCOOEND_INV on SIW_COORDENADA_ENDERECO (
   SQ_PESSOA_ENDERECO ASC,
   SQ_SIW_COORDENADA ASC
);

/*==============================================================*/
/* Table: SIW_COORDENADA_SOLICITACAO                            */
/*==============================================================*/
create table SIW_COORDENADA_SOLICITACAO  (
   SQ_SIW_COORDENADA    NUMBER(18)                      not null,
   SQ_SIW_SOLICITACAO   NUMBER(18)                      not null,
   constraint PK_SIW_COORDENADA_SOLICITACAO primary key (SQ_SIW_COORDENADA, SQ_SIW_SOLICITACAO)
);

comment on table SIW_COORDENADA_SOLICITACAO is
'Vincula coordenadas geográficas a uma solicitação.';

comment on column SIW_COORDENADA_SOLICITACAO.SQ_SIW_COORDENADA is
'Chave de SIW_COORDENADA. Indica a que coordenada o registro está ligado.';

comment on column SIW_COORDENADA_SOLICITACAO.SQ_SIW_SOLICITACAO is
'Chave de SIW_SOLICITACAO. Indica a que solicitação o registro está ligado.';

/*==============================================================*/
/* Index: IN_SIWCOOSOL_INVERSA                                  */
/*==============================================================*/
create index IN_SIWCOOSOL_INVERSA on SIW_COORDENADA_SOLICITACAO (
   SQ_SIW_SOLICITACAO ASC,
   SQ_SIW_COORDENADA ASC
);

/*==============================================================*/
/* Table: SIW_ETAPA_INTERESSADO                                 */
/*==============================================================*/
create table SIW_ETAPA_INTERESSADO  (
   SQ_UNIDADE           NUMBER(10)                      not null,
   SQ_PROJETO_ETAPA     NUMBER(18)                      not null,
   constraint PK_SIW_ETAPA_INTERESSADO primary key (SQ_UNIDADE, SQ_PROJETO_ETAPA)
);

comment on table SIW_ETAPA_INTERESSADO is
'Registra as vinculações entre partes interessadas e pacotes de trabalho de um projeto.';

comment on column SIW_ETAPA_INTERESSADO.SQ_UNIDADE is
'Chave de EO_UNIDADE. Indica a que unidade o registro está ligado.';

comment on column SIW_ETAPA_INTERESSADO.SQ_PROJETO_ETAPA is
'Chave de PJ_PROJETO_ETAPA. Indica a que etapa do projeto o registro está ligado.';

/*==============================================================*/
/* Index: IN_SIWETAINT_INV                                      */
/*==============================================================*/
create index IN_SIWETAINT_INV on SIW_ETAPA_INTERESSADO (
   SQ_PROJETO_ETAPA ASC,
   SQ_UNIDADE ASC
);

/*==============================================================*/
/* Table: SIW_MAIL                                              */
/*==============================================================*/
create table SIW_MAIL  (
   SQ_MAIL              NUMBER(18)                      not null,
   CLIENTE              NUMBER(18)                      not null,
   REMETENTE            NUMBER(18)                      not null,
   ASSUNTO              VARCHAR2(200)                   not null,
   TEXTO                VARCHAR2(2000)                  not null,
   INCLUSAO             DATE                           default sysdate not null,
   ENVIO                DATE,
   ENVIADA              VARCHAR2(1)                    default 'N' not null
      constraint CKC_ENVIADA_SIW_MAIL check (ENVIADA in ('S','N') and ENVIADA = upper(ENVIADA)),
   MAIL_REMETENTE       VARCHAR2(100)                   not null,
   constraint PK_SIW_MAIL primary key (SQ_MAIL)
);

comment on table SIW_MAIL is
'Registra os e-mails enviados através do mecanismo de mensagens.';

comment on column SIW_MAIL.SQ_MAIL is
'Chave de SIW_MAIL.';

comment on column SIW_MAIL.CLIENTE is
'Chave de CO_PESSOA. Indica a que cliente o registro está ligado.';

comment on column SIW_MAIL.REMETENTE is
'Chave de CO_PESSOA. Indica o remetente da mensagem.';

comment on column SIW_MAIL.ASSUNTO is
'Assunto da mensagem.';

comment on column SIW_MAIL.TEXTO is
'Texto da mensagem.';

comment on column SIW_MAIL.INCLUSAO is
'Data da inclusão do registro. Alimentada automaticamente.';

comment on column SIW_MAIL.ENVIO is
'Data do envio da mensagem. Alimentada automaticamente.';

comment on column SIW_MAIL.ENVIADA is
'Indica se a mensagem já foi enviada.';

comment on column SIW_MAIL.MAIL_REMETENTE is
'Registra o e-mail do remetente utilizado para envio, evitando sua perda caso o usuário o altere.';

/*==============================================================*/
/* Index: IN_SIWMAI_CLIENTE                                     */
/*==============================================================*/
create index IN_SIWMAI_CLIENTE on SIW_MAIL (
   CLIENTE ASC,
   SQ_MAIL ASC
);

/*==============================================================*/
/* Index: IN_SIWMAI_REMETE                                      */
/*==============================================================*/
create index IN_SIWMAI_REMETE on SIW_MAIL (
   REMETENTE ASC,
   SQ_MAIL ASC
);

/*==============================================================*/
/* Index: IN_SIWMAI_ENVIADA                                     */
/*==============================================================*/
create index IN_SIWMAI_ENVIADA on SIW_MAIL (
   CLIENTE ASC,
   ENVIADA ASC,
   SQ_MAIL ASC
);

/*==============================================================*/
/* Table: SIW_MAIL_ANEXO                                        */
/*==============================================================*/
create table SIW_MAIL_ANEXO  (
   SQ_MAIL_ANEXO        NUMBER(18)                      not null,
   SQ_MAIL              NUMBER(18)                      not null,
   SQ_SIW_ARQUIVO       NUMBER(18)                      not null,
   constraint PK_SIW_MAIL_ANEXO primary key (SQ_MAIL_ANEXO)
);

comment on table SIW_MAIL_ANEXO is
'Registra os arquivos anexos à mensagem.';

comment on column SIW_MAIL_ANEXO.SQ_MAIL_ANEXO is
'Chave de SIW_MAIL_ANEXO.';

comment on column SIW_MAIL_ANEXO.SQ_MAIL is
'Chave de SIW_MAIL. Indica a que mensagem o anexo está ligado.';

comment on column SIW_MAIL_ANEXO.SQ_SIW_ARQUIVO is
'Chave de SIW_ARQUIVO. Indica a que arquivo o registro está ligado.';

/*==============================================================*/
/* Index: IN_SIWMAIANE_MAIL                                     */
/*==============================================================*/
create index IN_SIWMAIANE_MAIL on SIW_MAIL_ANEXO (
   SQ_MAIL ASC,
   SQ_MAIL_ANEXO ASC
);

/*==============================================================*/
/* Index: IN_SIWMAIANE_ARQUIVO                                  */
/*==============================================================*/
create index IN_SIWMAIANE_ARQUIVO on SIW_MAIL_ANEXO (
   SQ_SIW_ARQUIVO ASC,
   SQ_MAIL ASC,
   SQ_MAIL_ANEXO ASC
);

/*==============================================================*/
/* Table: SIW_MAIL_DESTINATARIO                                 */
/*==============================================================*/
create table SIW_MAIL_DESTINATARIO  (
   SQ_MAIL_DESTINATARIO NUMBER(18)                      not null,
   SQ_MAIL              NUMBER(18)                      not null,
   DESTINATARIO_PESSOA  NUMBER(18),
   DESTINATARIO_UNIDADE NUMBER(10),
   EMAIL_DESTINATARIO   VARCHAR2(100)                   not null,
   NOME_DESTINATARIO    VARCHAR2(40)                    not null,
   constraint PK_SIW_MAIL_DESTINATARIO primary key (SQ_MAIL_DESTINATARIO)
);

comment on table SIW_MAIL_DESTINATARIO is
'Registra os destinatários de uma mensagem de e-mail. A tabela preve o envio para pessoas e unidades, registradas ou não das tabelas internas.';

comment on column SIW_MAIL_DESTINATARIO.SQ_MAIL_DESTINATARIO is
'Chave de SQ_MAIL_DESTINATARIO.';

comment on column SIW_MAIL_DESTINATARIO.SQ_MAIL is
'Chave de SIW_MAIL. Indica a que mensagem o registro está ligado.';

comment on column SIW_MAIL_DESTINATARIO.DESTINATARIO_PESSOA is
'Chave de CO_PESSOA. Indica a que pessoa o registro está ligado. Este campo é preenchido somente quando o destinatário existe naquela tabela.';

comment on column SIW_MAIL_DESTINATARIO.DESTINATARIO_UNIDADE is
'Chave de EO_UNIDADE. Indica a que unidade o registro está ligado. Este campo é preenchido somente quando o destinatário existe naquela tabela.';

comment on column SIW_MAIL_DESTINATARIO.EMAIL_DESTINATARIO is
'Registra o e-mail do destinatario utilizado para envio, evitando sua perda caso o usuário o altere.';

comment on column SIW_MAIL_DESTINATARIO.NOME_DESTINATARIO is
'Registra o nome do destinatário utilizado para envio, evitando sua perda caso o usuário o altere.';

/*==============================================================*/
/* Index: IN_SIWMAIDES_MAIL                                     */
/*==============================================================*/
create index IN_SIWMAIDES_MAIL on SIW_MAIL_DESTINATARIO (
   SQ_MAIL ASC,
   SQ_MAIL_DESTINATARIO ASC
);

/*==============================================================*/
/* Index: IN_SIWMAIDES_PESSOA                                   */
/*==============================================================*/
create index IN_SIWMAIDES_PESSOA on SIW_MAIL_DESTINATARIO (
   DESTINATARIO_PESSOA ASC,
   SQ_MAIL_DESTINATARIO ASC
);

/*==============================================================*/
/* Index: IN_SIWMAIDES_UNIDADE                                  */
/*==============================================================*/
create index IN_SIWMAIDES_UNIDADE on SIW_MAIL_DESTINATARIO (
   DESTINATARIO_UNIDADE ASC,
   SQ_MAIL_DESTINATARIO ASC
);

/*==============================================================*/
/* Table: SIW_MENU                                              */
/*==============================================================*/
create table SIW_MENU  (
   SQ_MENU              NUMBER(18)                      not null,
   SQ_MODULO            NUMBER(18)                      not null,
   SQ_PESSOA            number(18)                      not null,
   SQ_MENU_PAI          NUMBER(18),
   NOME                 VARCHAR2(40)                    not null,
   FINALIDADE           VARCHAR2(200)                  default 'A ser inserido.' not null,
   LINK                 VARCHAR2(60),
   SQ_UNID_EXECUTORA    number(10),
   TRAMITE              VARCHAR2(1)                    default 'N' not null
      constraint CKC_SIWMEN_TRAM
               check (TRAMITE in ('S','N') and TRAMITE = upper(TRAMITE)),
   ORDEM                NUMBER(4)                       not null,
   ULTIMO_NIVEL         VARCHAR2(1)                    default 'N' not null
      constraint CKC_SIWMEN_ULT
               check (ULTIMO_NIVEL in ('S','N') and ULTIMO_NIVEL = upper(ULTIMO_NIVEL)),
   P1                   NUMBER(18),
   P2                   NUMBER(18),
   P3                   NUMBER(18),
   P4                   NUMBER(18),
   SIGLA                VARCHAR2(10),
   IMAGEM               VARCHAR2(60),
   ACESSO_GERAL         VARCHAR2(1)                    default 'N' not null
      constraint CKC_SIWMEN_ACGER
               check (ACESSO_GERAL in ('S','N') and ACESSO_GERAL = upper(ACESSO_GERAL)),
   DESCENTRALIZADO      VARCHAR2(1)                    default 'S' not null
      constraint CKC_SIWMEN_DESC
               check (DESCENTRALIZADO in ('S','N') and DESCENTRALIZADO = upper(DESCENTRALIZADO)),
   EXTERNO              VARCHAR2(1)                    default 'N' not null
      constraint CKC_EXTERNO_SIWMEN check (EXTERNO in ('S','N') and EXTERNO = upper(EXTERNO)),
   TARGET               VARCHAR2(15),
   EMITE_OS             VARCHAR2(1)                    
      constraint CKC_SIWMEN_OS
               check (EMITE_OS is null or (EMITE_OS in ('S','N') and EMITE_OS = upper(EMITE_OS))),
   CONSULTA_OPINIAO     VARCHAR2(1)                    
      constraint CKC_SIWMEN_OPI
               check (CONSULTA_OPINIAO is null or (CONSULTA_OPINIAO in ('S','N') and CONSULTA_OPINIAO = upper(CONSULTA_OPINIAO))),
   ENVIA_EMAIL          VARCHAR2(1)                    
      constraint CKC_SIWMEN_MAIL
               check (ENVIA_EMAIL is null or (ENVIA_EMAIL in ('S','N') and ENVIA_EMAIL = upper(ENVIA_EMAIL))),
   EXIBE_RELATORIO      VARCHAR2(1)                    
      constraint CKC_SIWMEN_REL
               check (EXIBE_RELATORIO is null or (EXIBE_RELATORIO in ('S','N') and EXIBE_RELATORIO = upper(EXIBE_RELATORIO))),
   COMO_FUNCIONA        VARCHAR2(1000),
   VINCULACAO           VARCHAR2(1)                    
      constraint CKC_SIWMEN_VIN
               check (VINCULACAO is null or (VINCULACAO in ('P','U') and VINCULACAO = upper(VINCULACAO))),
   DESTINATARIO         VARCHAR2(1)                    default 'S' not null
      constraint CKC_DESTINATARIO_SIW_MENU check (DESTINATARIO in ('S','N') and DESTINATARIO = upper(DESTINATARIO)),
   DATA_HORA            VARCHAR2(1)                    
      constraint CKC_DATA_HORA_SIW_MENU check (DATA_HORA is null or (DATA_HORA = upper(DATA_HORA))),
   ENVIA_DIA_UTIL       VARCHAR2(1)                    
      constraint CKC_SIWMEN_UTIL
               check (ENVIA_DIA_UTIL is null or (ENVIA_DIA_UTIL in ('S','N') and ENVIA_DIA_UTIL = upper(ENVIA_DIA_UTIL))),
   DESCRICAO            VARCHAR2(1)                    
      constraint CKC_SIWMEN_DESCR
               check (DESCRICAO is null or (DESCRICAO in ('S','N') and DESCRICAO = upper(DESCRICAO))),
   JUSTIFICATIVA        VARCHAR2(1)                    
      constraint CKC_SIWMEN_JUST
               check (JUSTIFICATIVA is null or (JUSTIFICATIVA in ('S','N') and JUSTIFICATIVA = upper(JUSTIFICATIVA))),
   ATIVO                VARCHAR2(1)                    default 'S' not null
      constraint CKC_ATIVO_SIW_MENU check (ATIVO in ('S','N') and ATIVO = upper(ATIVO)),
   CONTROLA_ANO         VARCHAR2(1)                    default 'N' not null
      constraint CKC_CONTROLA_ANO_SIW_MENU check (CONTROLA_ANO in ('S','N') and CONTROLA_ANO = upper(CONTROLA_ANO)),
   LIBERA_EDICAO        VARCHAR2(1)                    default 'S' not null
      constraint CKC_LIBERA_EDICAO_SIW_MENU check (LIBERA_EDICAO in ('S','N') and LIBERA_EDICAO = upper(LIBERA_EDICAO)),
   NUMERACAO_AUTOMATICA NUMBER(1)                      default 0 not null,
   SERVICO_NUMERADOR    NUMBER(18),
   SQ_ARQUIVO_PROCED    NUMBER(18),
   SEQUENCIAL           NUMBER(18),
   ANO_CORRENTE         NUMBER(4),
   PREFIXO              VARCHAR2(10),
   SUFIXO               VARCHAR2(10),
   ENVIO_INCLUSAO       VARCHAR2(1)                    default 'N' not null
      constraint CKC_ENVIO_INCLUSAO_SIW_MENU check (ENVIO_INCLUSAO in ('S','N') and ENVIO_INCLUSAO = upper(ENVIO_INCLUSAO)),
   CONSULTA_GERAL       VARCHAR2(1)                    default 'N' not null
      constraint CKC_CONSULTA_GERAL_SIW_MENU check (CONSULTA_GERAL in ('S','N') and CONSULTA_GERAL = upper(CONSULTA_GERAL)),
   CANCELA_SEM_TRAMITE  VARCHAR2(1)                    default 'S' not null
      constraint CKC_CANCELA_SEM_TRAMI_SIW_MENU check (CANCELA_SEM_TRAMITE in ('S','N') and CANCELA_SEM_TRAMITE = upper(CANCELA_SEM_TRAMITE)),
   constraint PK_SIW_MENU primary key (SQ_MENU)
);

comment on table SIW_MENU is
'Armazena as opções padrão do menu para um segmento';

comment on column SIW_MENU.SQ_MENU is
'Chave de SIW_MENU.';

comment on column SIW_MENU.SQ_MODULO is
'Chave de SIW_MODULO. Indica a que módulo o registro está ligado.';

comment on column SIW_MENU.SQ_PESSOA is
'Chave de CO_PESSOA. Indica a que cliente o registro está ligado.';

comment on column SIW_MENU.SQ_MENU_PAI is
'Chave de SIW_MENU. Auto-relacionamento da tabela.';

comment on column SIW_MENU.NOME is
'Informa o texto a ser apresentado no menu.';

comment on column SIW_MENU.FINALIDADE is
'Informa a finalidade da opção.';

comment on column SIW_MENU.LINK is
'Informa o link a ser chamado quando a opção for clicada.';

comment on column SIW_MENU.SQ_UNID_EXECUTORA is
'Chave de EO_UNIDADE. Unidade responsável pela execução do serviço.';

comment on column SIW_MENU.TRAMITE is
'Indica se a opção deve ter controle de trâmites (work-flow).';

comment on column SIW_MENU.ORDEM is
'Informa a ordem em que a opção deve ser apresentada, em relação a outras opções de mesma subordinação.';

comment on column SIW_MENU.ULTIMO_NIVEL is
'Indica se a opção deve ser apresentada num sub-menu (S) ou na montagem do menu principal (N)';

comment on column SIW_MENU.P1 is
'Parâmetro de uso geral pela aplicação.';

comment on column SIW_MENU.P2 is
'Parâmetro de uso geral pela aplicação.';

comment on column SIW_MENU.P3 is
'Parâmetro de uso geral pela aplicação.';

comment on column SIW_MENU.P4 is
'Parâmetro de uso geral pela aplicaço.';

comment on column SIW_MENU.SIGLA is
'Informa a sigla da opção, usada para controle interno da aplicação.';

comment on column SIW_MENU.IMAGEM is
'Informa qual ícone deve ser colocado ao lado da opção. Se for nulo, a imagem será a padrão.';

comment on column SIW_MENU.ACESSO_GERAL is
'Indica que a opção deve ser acessada por todos os usuários.';

comment on column SIW_MENU.DESCENTRALIZADO is
'Indica se a opção deve ser controlada por endereço.';

comment on column SIW_MENU.EXTERNO is
'Indica se o link da opção aponta para um endereço externo ao sistema.';

comment on column SIW_MENU.TARGET is
'Se preenchido, informa o nome da janela a ser aberta quando a opção for clicada.';

comment on column SIW_MENU.EMITE_OS is
'Indica se o serviço terá emissão de ordem de serviço';

comment on column SIW_MENU.CONSULTA_OPINIAO is
'Indica se o serviço deverá consultar a opinião do solicitante quanto ao atendimento';

comment on column SIW_MENU.ENVIA_EMAIL is
'Indica se deve ser enviado e-mail para o solicitante a cada trâmite';

comment on column SIW_MENU.EXIBE_RELATORIO is
'Indica se o serviço deve ser exibido no relatório gerencial';

comment on column SIW_MENU.COMO_FUNCIONA is
'Texto de apresentação do serviço, inclusive com as regras de negócio a serem respeitadas.';

comment on column SIW_MENU.VINCULACAO is
'Este campo determina se a solicitação do serviço é vinculada ao beneficiário ou à unidade solicitante. Se for ao beneficiário, outras pessoas da unidade, que não sejam titular ou substituto, não poderão vê-la. Além disso, se o beneficiário for para outra unidade, a solicitação deve ser vista pelos novos chefes. Se for à unidade, todos as pessoas da unidade poderão consultar a solicitação, mesmo que não sejam chefes. Mesmo que o solicitante vá para outra unidade, a solicitação é consultada pela unidade que cadastrou a solicitação.';

comment on column SIW_MENU.DESTINATARIO is
'Se igual a S, sempre pedirá destinatário quando um encaminhamento for feito. Caso contrário, aparecerá na mesa de trabalho das pessoas que puderem cumprir o trâmite.';

comment on column SIW_MENU.DATA_HORA is
'Indica como o sistema deve tratar a questão de horas. (0) Não pede data; (1) Pede apenas uma data; (2) Pede apenas uma data/hora; (3) Pede data início e fim; (4) Pede data/hora início e fim.';

comment on column SIW_MENU.ENVIA_DIA_UTIL is
'Indica se a solicitação só pode ser atendida em dia útil.';

comment on column SIW_MENU.DESCRICAO is
'Indica se deve ser informada uma descrição na solicitação';

comment on column SIW_MENU.JUSTIFICATIVA is
'Indica se deve ser informada uma justificativa na solicitação';

comment on column SIW_MENU.ATIVO is
'Indica se o registro deve ou não ser exibido.';

comment on column SIW_MENU.CONTROLA_ANO is
'Indica se a opção do menu deve ter seu controle por ano.';

comment on column SIW_MENU.LIBERA_EDICAO is
'Indica se pode haver inclusão, alteração ou exclusão dos registros.';

comment on column SIW_MENU.NUMERACAO_AUTOMATICA is
'Indica se o serviço tem numeração automática para suas solicitações: 0 - não tem; 1 - tem numeração própria; 2 - usa numeraçãode outro serviço.';

comment on column SIW_MENU.SERVICO_NUMERADOR is
'Chave de SIW_MENU apontando para o serviços que fornecerá o número da solicitação.';

comment on column SIW_MENU.SQ_ARQUIVO_PROCED is
'Chave de SIW_ARQUIVO. Indica o arquivo que detalha o procedimento operacional do serviço.';

comment on column SIW_MENU.SEQUENCIAL is
'Armazena o último número utilizado para numeração automática.';

comment on column SIW_MENU.ANO_CORRENTE is
'Ano no qual o sequencial está sendo incrementado.';

comment on column SIW_MENU.PREFIXO is
'Prefixo do código das solicitações.';

comment on column SIW_MENU.SUFIXO is
'Sufixo dos códigos das solicitações.';

comment on column SIW_MENU.ENVIO_INCLUSAO is
'Indica se o serviço permitirá o envio da solicitação juntamente com a inclusão.';

comment on column SIW_MENU.CONSULTA_GERAL is
'Indica que os registros desta opção podem ser acessados por todos os usuários.';

comment on column SIW_MENU.CANCELA_SEM_TRAMITE is
'Indica se solicitação sem trâmite deve ser cancelada. S - cancela; N - Exclui';

/*==============================================================*/
/* Index: IN_SIWMENU_SIGLA                                      */
/*==============================================================*/
create index IN_SIWMENU_SIGLA on SIW_MENU (
   SIGLA ASC,
   SQ_PESSOA ASC
);

/*==============================================================*/
/* Index: IN_SIWMENU_ULT                                        */
/*==============================================================*/
create unique index IN_SIWMENU_ULT on SIW_MENU (
   ULTIMO_NIVEL ASC,
   SQ_MENU ASC
);

/*==============================================================*/
/* Index: IN_SIWMENU_ATIVO                                      */
/*==============================================================*/
create unique index IN_SIWMENU_ATIVO on SIW_MENU (
   ATIVO ASC,
   SQ_MENU ASC
);

/*==============================================================*/
/* Index: IN_SIWMENU_PAI                                        */
/*==============================================================*/
create index IN_SIWMENU_PAI on SIW_MENU (
   SQ_MENU_PAI ASC,
   SQ_MENU ASC
);

/*==============================================================*/
/* Table: SIW_MENU_ARQUIVO                                      */
/*==============================================================*/
create table SIW_MENU_ARQUIVO  (
   SQ_MENU              NUMBER(18)                      not null,
   SQ_SIW_ARQUIVO       NUMBER(18)                      not null,
   constraint PK_SIW_MENU_ARQUIVO primary key (SQ_MENU, SQ_SIW_ARQUIVO)
);

comment on table SIW_MENU_ARQUIVO is
'Vincula um arquivo a opções de menu.';

comment on column SIW_MENU_ARQUIVO.SQ_MENU is
'Chave de SIW_MENU. Indica a que opção do menu o registro está ligado.';

comment on column SIW_MENU_ARQUIVO.SQ_SIW_ARQUIVO is
'Chave de SIW_ARQUIVO. Indica a que arquivo o registro está ligado.';

/*==============================================================*/
/* Index: IN_SIWMENARQ_INVERSA                                  */
/*==============================================================*/
create index IN_SIWMENARQ_INVERSA on SIW_MENU_ARQUIVO (
   SQ_SIW_ARQUIVO ASC,
   SQ_MENU ASC
);

/*==============================================================*/
/* Table: SIW_MENU_ENDERECO                                     */
/*==============================================================*/
create table SIW_MENU_ENDERECO  (
   SQ_MENU              NUMBER(18)                      not null,
   SQ_PESSOA_ENDERECO   NUMBER(18)                      not null,
   constraint PK_SIW_MENU_ENDERECO primary key (SQ_MENU, SQ_PESSOA_ENDERECO)
);

comment on table SIW_MENU_ENDERECO is
'Endereços do cliente onde a opção está disponível';

comment on column SIW_MENU_ENDERECO.SQ_MENU is
'Chave de SIW_MENU. Indica a que opção do menu o registro está ligado.';

comment on column SIW_MENU_ENDERECO.SQ_PESSOA_ENDERECO is
'Chave de CO_PESSOA_ENDERECO. Indica a que endereço o registro está ligado.';

/*==============================================================*/
/* Index: IN_SIWMENEND_INV                                      */
/*==============================================================*/
create index IN_SIWMENEND_INV on SIW_MENU_ENDERECO (
   SQ_PESSOA_ENDERECO ASC,
   SQ_MENU ASC
);

/*==============================================================*/
/* Table: SIW_MENU_RELAC                                        */
/*==============================================================*/
create table SIW_MENU_RELAC  (
   SERVICO_CLIENTE      NUMBER(18)                      not null,
   SERVICO_FORNECEDOR   NUMBER(18)                      not null,
   SQ_SIW_TRAMITE       NUMBER(18)                      not null,
   constraint PK_SIW_MENU_RELAC primary key (SERVICO_CLIENTE, SERVICO_FORNECEDOR, SQ_SIW_TRAMITE)
);

comment on table SIW_MENU_RELAC is
'Vincula serviços definindo os trâmites em que a vinculação pode ser feita.';

comment on column SIW_MENU_RELAC.SERVICO_CLIENTE is
'Chave de SIW_MENU apontando para o serviço que será vinculado a outro, nas fases indicadas.';

comment on column SIW_MENU_RELAC.SERVICO_FORNECEDOR is
'Chave de SIW_MENU apontando para o serviço ao qual solicitações de outro serviço serão vinculadas.';

comment on column SIW_MENU_RELAC.SQ_SIW_TRAMITE is
'Chave de SIW_TRAMITE, indicando as fases das solicitações do serviço fornecedor nas quais poderão ser vinculadas solicitações do serviço cliente.';

/*==============================================================*/
/* Index: IN_SIWMENREL_INV                                      */
/*==============================================================*/
create index IN_SIWMENREL_INV on SIW_MENU_RELAC (
   SERVICO_FORNECEDOR ASC,
   SERVICO_CLIENTE ASC,
   SQ_SIW_TRAMITE ASC
);

/*==============================================================*/
/* Table: SIW_META_ARQUIVO                                      */
/*==============================================================*/
create table SIW_META_ARQUIVO  (
   SQ_SOLIC_META        NUMBER(18)                      not null,
   SQ_SIW_ARQUIVO       NUMBER(18)                      not null,
   ORDEM                NUMBER(4)                       not null,
   constraint PK_SIW_META_ARQUIVO primary key (SQ_SOLIC_META, SQ_SIW_ARQUIVO)
);

comment on table SIW_META_ARQUIVO is
'Registra os arquivos ligados a metas.';

comment on column SIW_META_ARQUIVO.SQ_SOLIC_META is
'Chave de SIW_SOLIC_META. Indica a que meta o registro está ligado.';

comment on column SIW_META_ARQUIVO.SQ_SIW_ARQUIVO is
'Chave de SIW_ARQUIVO. Indica a que meta o registro está ligado.';

comment on column SIW_META_ARQUIVO.ORDEM is
'Número de ordem do arquivo para exibição em listagens.';

/*==============================================================*/
/* Index: IN_SIWMETARQ_INV                                      */
/*==============================================================*/
create index IN_SIWMETARQ_INV on SIW_META_ARQUIVO (
   SQ_SIW_ARQUIVO ASC,
   SQ_SOLIC_META ASC
);

/*==============================================================*/
/* Table: SIW_META_CRONOGRAMA                                   */
/*==============================================================*/
create table SIW_META_CRONOGRAMA  (
   SQ_META_CRONOGRAMA   NUMBER(18)                      not null,
   SQ_SOLIC_META        NUMBER(18)                      not null,
   SQ_PESSOA_ATUALIZACAO NUMBER(18),
   INICIO               DATE                            not null,
   FIM                  DATE                            not null,
   VALOR_PREVISTO       NUMBER(18,4)                    not null,
   VALOR_REAL           NUMBER(18,4),
   ULTIMA_ATUALIZACAO   DATE,
   constraint PK_SIW_META_CRONOGRAMA primary key (SQ_META_CRONOGRAMA)
);

comment on table SIW_META_CRONOGRAMA is
'Registra o cronograma de realização de metas.';

comment on column SIW_META_CRONOGRAMA.SQ_META_CRONOGRAMA is
'Chave de SIW_META_CRONOGRAMA.';

comment on column SIW_META_CRONOGRAMA.SQ_SOLIC_META is
'Chave de SIW_SOLIC_META. Indica a que meta o registro está ligado.';

comment on column SIW_META_CRONOGRAMA.SQ_PESSOA_ATUALIZACAO is
'Chave de CO_PESSOA. Indica a pessoa que informou o valor real para o período.';

comment on column SIW_META_CRONOGRAMA.INICIO is
'Início do período de aferição.';

comment on column SIW_META_CRONOGRAMA.FIM is
'Término do período de aferição.';

comment on column SIW_META_CRONOGRAMA.VALOR_PREVISTO is
'Valor previsto para o período.';

comment on column SIW_META_CRONOGRAMA.VALOR_REAL is
'Valor aferido no período.';

comment on column SIW_META_CRONOGRAMA.ULTIMA_ATUALIZACAO is
'Data da última atualização do valor real.';

/*==============================================================*/
/* Index: IN_SIWMETCRO_META                                     */
/*==============================================================*/
create index IN_SIWMETCRO_META on SIW_META_CRONOGRAMA (
   SQ_SOLIC_META ASC,
   SQ_META_CRONOGRAMA ASC
);

/*==============================================================*/
/* Index: IN_SIWMETCRO_INICIO                                   */
/*==============================================================*/
create index IN_SIWMETCRO_INICIO on SIW_META_CRONOGRAMA (
   INICIO ASC,
   SQ_META_CRONOGRAMA ASC
);

/*==============================================================*/
/* Index: IN_SIWMETCRO_FIM                                      */
/*==============================================================*/
create index IN_SIWMETCRO_FIM on SIW_META_CRONOGRAMA (
   FIM ASC,
   SQ_META_CRONOGRAMA ASC
);

/*==============================================================*/
/* Table: SIW_MODULO                                            */
/*==============================================================*/
create table SIW_MODULO  (
   SQ_MODULO            NUMBER(18)                      not null,
   NOME                 VARCHAR2(60)                    not null,
   SIGLA                VARCHAR2(3)                     not null,
   OBJETIVO_GERAL       VARCHAR2(4000),
   ORDEM                NUMBER(4)                      default 0 not null,
   constraint PK_SIW_MODULO primary key (SQ_MODULO)
);

comment on table SIW_MODULO is
'Armazena os módulos componentes do SIW';

comment on column SIW_MODULO.SQ_MODULO is
'Chave de SIW_MODULO.';

comment on column SIW_MODULO.NOME is
'Nome do módulo.';

comment on column SIW_MODULO.SIGLA is
'Sigla do módulo.';

comment on column SIW_MODULO.OBJETIVO_GERAL is
'Objetivo geral do módulo, independentemente do segmento que atende.';

comment on column SIW_MODULO.ORDEM is
'Indica a ordem do módulo nas listagens.';

/*==============================================================*/
/* Index: IN_SIWMOD_NOME                                        */
/*==============================================================*/
create unique index IN_SIWMOD_NOME on SIW_MODULO (
   NOME ASC
);

/*==============================================================*/
/* Index: IN_SIWMOD_SIGLA                                       */
/*==============================================================*/
create unique index IN_SIWMOD_SIGLA on SIW_MODULO (
   SIGLA ASC
);

/*==============================================================*/
/* Table: SIW_MOD_SEG                                           */
/*==============================================================*/
create table SIW_MOD_SEG  (
   SQ_MODULO            NUMBER(18)                      not null,
   SQ_SEGMENTO          NUMBER(18)                      not null,
   ATIVO                VARCHAR2(1)                    default 'S' not null
      constraint CKC_ATIVO_SIW_MOD_ check (ATIVO in ('S','N') and ATIVO = upper(ATIVO)),
   COMERCIALIZAR        VARCHAR2(1)                    default 'S' not null
      constraint CKC_SIWMODSE_COM
               check (COMERCIALIZAR in ('S','N') and COMERCIALIZAR = upper(COMERCIALIZAR)),
   OBJETIVO_ESPECIF     VARCHAR2(4000),
   constraint PK_SIW_MOD_SEG primary key (SQ_MODULO, SQ_SEGMENTO)
);

comment on table SIW_MOD_SEG is
'Armazena informações do módulo do SIW para um segmento específico';

comment on column SIW_MOD_SEG.SQ_MODULO is
'Chave de SIW_MODULO. Indica a que módulo o registro está ligado.';

comment on column SIW_MOD_SEG.SQ_SEGMENTO is
'Chave de CO_SEGMENTO. Indica a que segmento o registro está ligado.';

comment on column SIW_MOD_SEG.ATIVO is
'Indica se o registro deve ou não ser exibido.';

comment on column SIW_MOD_SEG.COMERCIALIZAR is
'Indica se o módulo pode ser comercializado.';

comment on column SIW_MOD_SEG.OBJETIVO_ESPECIF is
'Objetivos do módulo para o segmento ao qual está ligado.';

/*==============================================================*/
/* Index: IN_SIWMODSEG_SEG                                      */
/*==============================================================*/
create index IN_SIWMODSEG_SEG on SIW_MOD_SEG (
   SQ_SEGMENTO ASC,
   SQ_MODULO ASC
);

/*==============================================================*/
/* Table: SIW_RESTRICAO                                         */
/*==============================================================*/
create table SIW_RESTRICAO  (
   SQ_SIW_RESTRICAO     NUMBER(18)                      not null,
   SQ_SIW_SOLICITACAO   NUMBER(18)                      not null,
   SQ_PESSOA            NUMBER(18)                      not null,
   SQ_PESSOA_ATUALIZACAO NUMBER(18)                      not null,
   SQ_TIPO_RESTRICAO    NUMBER(18)                      not null,
   RISCO                VARCHAR2(1)                    default 'N' not null
      constraint CKC_RISCO_SIW_REST check (RISCO in ('S','N') and RISCO = upper(RISCO)),
   PROBLEMA             VARCHAR2(1)                    default 'N' not null
      constraint CKC_PROBLEMA_SIW_REST check (PROBLEMA in ('S','N') and PROBLEMA = upper(PROBLEMA)),
   DESCRICAO            VARCHAR2(2000)                  not null,
   PROBABILIDADE        NUMBER(1)                      default 1
      constraint CKC_PROBABILIDADE_SIW_REST check (PROBABILIDADE is null or (PROBABILIDADE in (1,2,3,4,5))),
   IMPACTO              NUMBER(1)                      default 1
      constraint CKC_IMPACTO_SIW_REST check (IMPACTO is null or (IMPACTO in (1,2,3,4,5))),
   CRITICIDADE          NUMBER(1)                      default 0 not null,
   ESTRATEGIA           VARCHAR2(1)                    default 'A' not null
      constraint CKC_ESTRATEGIA_SIW_REST check (ESTRATEGIA in ('A','E','T','M')),
   ACAO_RESPOSTA        VARCHAR2(2000)                  not null,
   FASE_ATUAL           VARCHAR2(1)                    default 'D' not null
      constraint CKC_FASE_ATUAL_SIW_REST check (FASE_ATUAL in ('D','P','A','C')),
   DATA_SITUACAO        DATE,
   SITUACAO_ATUAL       VARCHAR2(2000),
   ULTIMA_ATUALIZACAO   DATE,
   constraint PK_SIW_RESTRICAO primary key (SQ_SIW_RESTRICAO)
);

comment on table SIW_RESTRICAO is
'Registra riscos e problemas associados à solicitação.';

comment on column SIW_RESTRICAO.SQ_SIW_RESTRICAO is
'Chave de SQ_SOLIC_RISCO.';

comment on column SIW_RESTRICAO.SQ_SIW_SOLICITACAO is
'Chave de SIW_SOLICITACAO. Indica a que solicitação o registro está ligado.';

comment on column SIW_RESTRICAO.SQ_PESSOA is
'Chave de CO_PESSOA. Responsável pela restrição.';

comment on column SIW_RESTRICAO.SQ_PESSOA_ATUALIZACAO is
'Chave de CO_PESSOA. Usuário responsável pela criação ou última atualização da restrição.';

comment on column SIW_RESTRICAO.SQ_TIPO_RESTRICAO is
'Chave de SIW_TIPO_RESTRICAO. Indica a que tipo de restrição o registro está ligado.';

comment on column SIW_RESTRICAO.RISCO is
'Indica se o registro é um risco.';

comment on column SIW_RESTRICAO.PROBLEMA is
'Indica se o registro atual é um problema.';

comment on column SIW_RESTRICAO.DESCRICAO is
'Descrição da restrição.';

comment on column SIW_RESTRICAO.PROBABILIDADE is
'Probabilidade do risco. Não se aplica para problemas. 1 - Muito baixo, 2 - Baixo, 3 - Médio, 4 - Alto, 5 - Muito alto.';

comment on column SIW_RESTRICAO.IMPACTO is
'Impacto do risco. Não se aplica para problemas.  1 - Muito baixo, 2 - Baixo, 3 - Médio, 4 - Alto, 5 - Muito alto';

comment on column SIW_RESTRICAO.CRITICIDADE is
'Criticidade do risco ou problema. Para riscos, é calculado a partir da probabilidade e do impacto. 1 - Baixa, 2 - Moderada, 3 - Alta.';

comment on column SIW_RESTRICAO.ESTRATEGIA is
'Estratégia adotada frente ao risco ou problema. A - Aceitar, T - Transferir, E - Evitar, M - Mitigar';

comment on column SIW_RESTRICAO.ACAO_RESPOSTA is
'Texto descrevendo a ação de resposta ao risco/problema.';

comment on column SIW_RESTRICAO.FASE_ATUAL is
'Fase em que o risco ou problema se encontra. D - Definido, P - Pendente, A - Em andamento, C - Concluído.';

comment on column SIW_RESTRICAO.DATA_SITUACAO is
'Data em que a situação atual foi verificada.';

comment on column SIW_RESTRICAO.SITUACAO_ATUAL is
'Texto detalhando a situação atual do risco ou problema.';

comment on column SIW_RESTRICAO.ULTIMA_ATUALIZACAO is
'Registra a data da criação ou última atualização do risco/problema.';

/*==============================================================*/
/* Index: IN_SIWSOLRES_RESP                                     */
/*==============================================================*/
create index IN_SIWSOLRES_RESP on SIW_RESTRICAO (
   SQ_PESSOA ASC,
   SQ_SIW_SOLICITACAO ASC,
   SQ_SIW_RESTRICAO ASC
);

/*==============================================================*/
/* Index: IN_SIWSOLRES_ATUAL                                    */
/*==============================================================*/
create index IN_SIWSOLRES_ATUAL on SIW_RESTRICAO (
   SQ_PESSOA_ATUALIZACAO ASC,
   SQ_SIW_SOLICITACAO ASC,
   SQ_SIW_RESTRICAO ASC
);

/*==============================================================*/
/* Index: IN_SIWSOLRES_SOLIC                                    */
/*==============================================================*/
create index IN_SIWSOLRES_SOLIC on SIW_RESTRICAO (
   SQ_SIW_SOLICITACAO ASC,
   SQ_SIW_RESTRICAO ASC
);

/*==============================================================*/
/* Table: SIW_RESTRICAO_ETAPA                                   */
/*==============================================================*/
create table SIW_RESTRICAO_ETAPA  (
   SQ_SIW_RESTRICAO     NUMBER(18)                      not null,
   SQ_PROJETO_ETAPA     NUMBER(18)                      not null,
   constraint PK_SIW_RESTRICAO_ETAPA primary key (SQ_SIW_RESTRICAO, SQ_PROJETO_ETAPA)
);

comment on table SIW_RESTRICAO_ETAPA is
'Registra as vinculações entre restrições e pacotes de trabalho de um projeto.';

comment on column SIW_RESTRICAO_ETAPA.SQ_SIW_RESTRICAO is
'Chave de SQ_SOLIC_RISCO.';

comment on column SIW_RESTRICAO_ETAPA.SQ_PROJETO_ETAPA is
'Chave de PJ_PROJETO_ETAPA. Indica a que etapa do projeto o registro está ligado.';

/*==============================================================*/
/* Index: IN_SIWRESETA_INV                                      */
/*==============================================================*/
create index IN_SIWRESETA_INV on SIW_RESTRICAO_ETAPA (
   SQ_PROJETO_ETAPA ASC,
   SQ_SIW_RESTRICAO ASC
);

/*==============================================================*/
/* Table: SIW_SOLICITACAO                                       */
/*==============================================================*/
create table SIW_SOLICITACAO  (
   SQ_SIW_SOLICITACAO   NUMBER(18)                      not null,
   SQ_SOLIC_PAI         NUMBER(18),
   SQ_MENU              NUMBER(18)                      not null,
   SQ_UNIDADE           number(10)                      not null,
   SQ_SIW_TRAMITE       NUMBER(18)                      not null,
   SOLICITANTE          number(18)                      not null,
   CADASTRADOR          number(18)                      not null,
   EXECUTOR             number(18),
   RECEBEDOR            NUMBER(18),
   DESCRICAO            VARCHAR2(2000),
   JUSTIFICATIVA        VARCHAR2(2000),
   INICIO               DATE,
   FIM                  DATE,
   INCLUSAO             DATE                            not null,
   ULTIMA_ALTERACAO     DATE                            not null,
   CONCLUSAO            DATE,
   VALOR                NUMBER(18,2),
   DATA_HORA            VARCHAR2(1)                    default '0' not null
      constraint CKC_SIWSOL_DTHOR check (DATA_HORA in ('0','1','2','3','4') and DATA_HORA = upper(DATA_HORA)),
   PALAVRA_CHAVE        VARCHAR2(92),
   SQ_CIDADE_ORIGEM     NUMBER(18)                      not null,
   SQ_PLANO             NUMBER(18),
   PROTOCOLO_SIW        NUMBER(18),
   SQ_TIPO_EVENTO       NUMBER(18),
   ANO                  NUMBER(4)                      default to_number(to_char(sysdate,'yyyy')) not null,
   OBSERVACAO           VARCHAR2(2000),
   MOTIVO_INSATISFACAO  VARCHAR2(1000),
   TITULO               VARCHAR2(100),
   CODIGO_INTERNO       VARCHAR2(60),
   CODIGO_EXTERNO       VARCHAR2(60),
   INDICADOR1           VARCHAR2(1)                    default 'N' not null
      constraint CKC_INDICADOR1_SIW_SOLI check (INDICADOR1 in ('S','N') and INDICADOR1 = upper(INDICADOR1)),
   INDICADOR2           VARCHAR2(1)                    default 'N' not null
      constraint CKC_INDICADOR2_SIW_SOLI check (INDICADOR2 in ('S','N') and INDICADOR2 = upper(INDICADOR2)),
   INDICADOR3           VARCHAR2(1)                    default 'N' not null
      constraint CKC_INDICADOR3_SIW_SOLI check (INDICADOR3 in ('S','N') and INDICADOR3 = upper(INDICADOR3)),
   constraint PK_SIW_SOLICITACAO primary key (SQ_SIW_SOLICITACAO)
);

comment on table SIW_SOLICITACAO is
'Solicitação';

comment on column SIW_SOLICITACAO.SQ_SIW_SOLICITACAO is
'Chave de SIW_SOLICITACAO indicando a que solicitação o registro está ligado.';

comment on column SIW_SOLICITACAO.SQ_SOLIC_PAI is
'Chave de SIW_SOLICITACAO. Auto-relacionamento da tabela.';

comment on column SIW_SOLICITACAO.SQ_MENU is
'Chave de SIW_MENU. Indica a que opção do menu o registro está ligado.';

comment on column SIW_SOLICITACAO.SQ_UNIDADE is
'Chave de EO_UNIDADE. Unidade solicitante.';

comment on column SIW_SOLICITACAO.SQ_SIW_TRAMITE is
'Chave de SIW_TRAMITE. Indica o trâmite atual da solicitação.';

comment on column SIW_SOLICITACAO.SOLICITANTE is
'Chave de CO_PESSOA. Indica o solicitante.';

comment on column SIW_SOLICITACAO.CADASTRADOR is
'Chave de CO_PESSOA. Indica o cadastrador.';

comment on column SIW_SOLICITACAO.EXECUTOR is
'Chave de CO_PESSOA. Indica o executor.';

comment on column SIW_SOLICITACAO.RECEBEDOR is
'Chave de CO_PESSOA. Pessoa que aceitou a conclusão do serviço pelo solicitante.';

comment on column SIW_SOLICITACAO.DESCRICAO is
'Descrição da solicitação.';

comment on column SIW_SOLICITACAO.JUSTIFICATIVA is
'Justificativa da solicitação.';

comment on column SIW_SOLICITACAO.INICIO is
'Início da solicitação.';

comment on column SIW_SOLICITACAO.FIM is
'Data de término da solicitação.';

comment on column SIW_SOLICITACAO.INCLUSAO is
'Data e hora de inclusao da solicitação. Gerada automaticamente pelo sistema.';

comment on column SIW_SOLICITACAO.ULTIMA_ALTERACAO is
'Data da última alteração do registro.';

comment on column SIW_SOLICITACAO.CONCLUSAO is
'Data de conclusão da solicitação.';

comment on column SIW_SOLICITACAO.VALOR is
'Valor da solicitação.';

comment on column SIW_SOLICITACAO.DATA_HORA is
'Indica como o sistema deve tratar a questão de horas. (0) Não pede data; (1) Pede apenas uma data; (2) Pede apenas uma data/hora; (3) Pede data início e fim; (4) Pede data/hora início e fim.';

comment on column SIW_SOLICITACAO.PALAVRA_CHAVE is
'Contém palavras-chave para consulta';

comment on column SIW_SOLICITACAO.SQ_CIDADE_ORIGEM is
'Chave de CO_CIDADE. Cidade que originou a solicitação';

comment on column SIW_SOLICITACAO.SQ_PLANO is
'Chave de PE_PLANO. Indica a que plano estratégico a solicitação está ligada.';

comment on column SIW_SOLICITACAO.PROTOCOLO_SIW is
'Chave de SIW_SOLICITACAO. Indica a que protocolo a solicitação está ligada.';

comment on column SIW_SOLICITACAO.SQ_TIPO_EVENTO is
'Chave de SIW_TIPO_EVENTO. Indica a que tipo de evento o registro está ligado.';

comment on column SIW_SOLICITACAO.ANO is
'Registra o ano da solicitação, útil apenas para serviços que exijam o controle por ano.';

comment on column SIW_SOLICITACAO.OBSERVACAO is
'Observações.';

comment on column SIW_SOLICITACAO.MOTIVO_INSATISFACAO is
'Texto que armazena o motivo da insatisfação de um solicitante quando emite a opinião sobre um atendimento.';

comment on column SIW_SOLICITACAO.TITULO is
'Título da solicitação.';

comment on column SIW_SOLICITACAO.CODIGO_INTERNO is
'Código interno para a solicitação.';

comment on column SIW_SOLICITACAO.CODIGO_EXTERNO is
'Código da solicitação em outra organização ou sistema.';

comment on column SIW_SOLICITACAO.INDICADOR1 is
'Indicador tipo Não/Sim de uso geral.';

comment on column SIW_SOLICITACAO.INDICADOR2 is
'Indicador tipo Não/Sim de uso geral.';

comment on column SIW_SOLICITACAO.INDICADOR3 is
'Indicador tipo Não/Sim de uso geral.';

/*==============================================================*/
/* Index: IN_SIWSOL_CADASTR                                     */
/*==============================================================*/
create index IN_SIWSOL_CADASTR on SIW_SOLICITACAO (
   CADASTRADOR ASC,
   SQ_SIW_SOLICITACAO ASC
);

/*==============================================================*/
/* Index: IN_SIWSOL_SOLIC                                       */
/*==============================================================*/
create index IN_SIWSOL_SOLIC on SIW_SOLICITACAO (
   SOLICITANTE ASC,
   SQ_SIW_SOLICITACAO ASC
);

/*==============================================================*/
/* Index: IN_SIWSOL_EXECUTOR                                    */
/*==============================================================*/
create index IN_SIWSOL_EXECUTOR on SIW_SOLICITACAO (
   EXECUTOR ASC,
   SQ_SIW_SOLICITACAO ASC
);

/*==============================================================*/
/* Index: IN_SIWSOL_INICIO                                      */
/*==============================================================*/
create index IN_SIWSOL_INICIO on SIW_SOLICITACAO (
   SQ_MENU ASC,
   INICIO ASC,
   SQ_SIW_SOLICITACAO ASC
);

/*==============================================================*/
/* Index: IN_SIWSOL_FIM                                         */
/*==============================================================*/
create index IN_SIWSOL_FIM on SIW_SOLICITACAO (
   SQ_MENU ASC,
   FIM ASC,
   SQ_SIW_SOLICITACAO ASC
);

/*==============================================================*/
/* Index: IN_SIWSOL_INCLUSAO                                    */
/*==============================================================*/
create index IN_SIWSOL_INCLUSAO on SIW_SOLICITACAO (
   SQ_MENU ASC,
   INCLUSAO ASC,
   SQ_SIW_SOLICITACAO ASC
);

/*==============================================================*/
/* Index: IN_SIWSOL_ALTER                                       */
/*==============================================================*/
create index IN_SIWSOL_ALTER on SIW_SOLICITACAO (
   SQ_MENU ASC,
   ULTIMA_ALTERACAO ASC,
   SQ_SIW_SOLICITACAO ASC
);

/*==============================================================*/
/* Index: IN_SIWSOL_CONC                                        */
/*==============================================================*/
create index IN_SIWSOL_CONC on SIW_SOLICITACAO (
   SQ_MENU ASC,
   CONCLUSAO ASC,
   SQ_SIW_SOLICITACAO ASC
);

/*==============================================================*/
/* Index: IN_SIWSOL_PAI                                         */
/*==============================================================*/
create index IN_SIWSOL_PAI on SIW_SOLICITACAO (
   SQ_SOLIC_PAI ASC,
   SQ_SIW_SOLICITACAO ASC
);

/*==============================================================*/
/* Index: IN_SIWSOL_UNIDSOL                                     */
/*==============================================================*/
create index IN_SIWSOL_UNIDSOL on SIW_SOLICITACAO (
   SQ_UNIDADE ASC,
   SQ_SIW_SOLICITACAO ASC
);

/*==============================================================*/
/* Index: IN_SIWSOL_CIDADE                                      */
/*==============================================================*/
create index IN_SIWSOL_CIDADE on SIW_SOLICITACAO (
   SQ_CIDADE_ORIGEM ASC,
   SQ_SIW_SOLICITACAO ASC
);

/*==============================================================*/
/* Index: IN_SIWSOL_ANO                                         */
/*==============================================================*/
create index IN_SIWSOL_ANO on SIW_SOLICITACAO (
   ANO ASC,
   SQ_SIW_SOLICITACAO ASC
);

/*==============================================================*/
/* Index: IN_SIWSOL_RECEBEDOR                                   */
/*==============================================================*/
create index IN_SIWSOL_RECEBEDOR on SIW_SOLICITACAO (
   RECEBEDOR ASC,
   SQ_SIW_SOLICITACAO ASC
);

/*==============================================================*/
/* Index: IN_SIWSOL_TITULO                                      */
/*==============================================================*/
create index IN_SIWSOL_TITULO on SIW_SOLICITACAO (
   TITULO ASC,
   SQ_SIW_SOLICITACAO ASC
);

/*==============================================================*/
/* Index: IN_SIWSOL_CODINT                                      */
/*==============================================================*/
create index IN_SIWSOL_CODINT on SIW_SOLICITACAO (
   CODIGO_INTERNO ASC,
   SQ_SIW_SOLICITACAO ASC
);

/*==============================================================*/
/* Index: IN_SIWSOL_CODEXT                                      */
/*==============================================================*/
create index IN_SIWSOL_CODEXT on SIW_SOLICITACAO (
   CODIGO_EXTERNO ASC,
   SQ_SIW_SOLICITACAO ASC
);

/*==============================================================*/
/* Index: IN_SIWSOL_PLANO                                       */
/*==============================================================*/
create index IN_SIWSOL_PLANO on SIW_SOLICITACAO (
   SQ_PLANO ASC,
   SQ_SIW_SOLICITACAO ASC
);

/*==============================================================*/
/* Index: IN_SIWSOL_PROTOCOLO                                   */
/*==============================================================*/
create index IN_SIWSOL_PROTOCOLO on SIW_SOLICITACAO (
   PROTOCOLO_SIW ASC,
   SQ_SIW_SOLICITACAO ASC
);

/*==============================================================*/
/* Index: IN_SIWSOL_EVENTO                                      */
/*==============================================================*/
create index IN_SIWSOL_EVENTO on SIW_SOLICITACAO (
   SQ_TIPO_EVENTO ASC,
   SQ_SIW_SOLICITACAO ASC
);

/*==============================================================*/
/* Index: IN_SIWSOL_TRAMITE                                     */
/*==============================================================*/
create index IN_SIWSOL_TRAMITE on SIW_SOLICITACAO (
   SQ_SIW_TRAMITE ASC,
   SQ_SIW_SOLICITACAO ASC
);

/*==============================================================*/
/* Table: SIW_SOLICITACAO_INTERESSADO                           */
/*==============================================================*/
create table SIW_SOLICITACAO_INTERESSADO  (
   SQ_SOLICITACAO_INTERESSADO NUMBER(18)                      not null,
   SQ_SIW_SOLICITACAO   NUMBER(18)                      not null,
   SQ_PESSOA            NUMBER(18)                      not null,
   SQ_TIPO_INTERESSADO  NUMBER(18)                      not null,
   TIPO_VISAO           NUMBER(1)                      default 2 not null
      constraint CKC_SIWSOLINT_TIPVIS check (TIPO_VISAO in (0,1,2)),
   ENVIA_EMAIL          VARCHAR2(1)                    default 'N' not null
      constraint CKC_SIWSOLINT_ENVMAI check (ENVIA_EMAIL in ('S','N') and ENVIA_EMAIL = upper(ENVIA_EMAIL)),
   constraint PK_SIW_SOLICITACAO_INTERESSADO primary key (SQ_SOLICITACAO_INTERESSADO)
);

comment on table SIW_SOLICITACAO_INTERESSADO is
'Registra os interessados na execução da solicitação.';

comment on column SIW_SOLICITACAO_INTERESSADO.SQ_SOLICITACAO_INTERESSADO is
'Chave de SIW_SOLICITACAO_INTERESSADO.';

comment on column SIW_SOLICITACAO_INTERESSADO.SQ_SIW_SOLICITACAO is
'Chave de SIW_SOLICITACAO. Indica a que solicitação o registro está ligado.';

comment on column SIW_SOLICITACAO_INTERESSADO.SQ_PESSOA is
'Chave de CO_PESSOA. Indica a que pessoa o registro está ligado.';

comment on column SIW_SOLICITACAO_INTERESSADO.SQ_TIPO_INTERESSADO is
'Chave de SIW_TIPO_INTERESSADO. Indica o tipo do interessado.';

comment on column SIW_SOLICITACAO_INTERESSADO.TIPO_VISAO is
'Indica a visão que a pessoa pode ter dessa solicitação.';

comment on column SIW_SOLICITACAO_INTERESSADO.ENVIA_EMAIL is
'Indica se deve ser enviado e-mail ao interessado quando houver alguma ocorrência na solicitação.';

/*==============================================================*/
/* Index: IN_SIWSOLINT_SOLIC                                    */
/*==============================================================*/
create index IN_SIWSOLINT_SOLIC on SIW_SOLICITACAO_INTERESSADO (
   SQ_SIW_SOLICITACAO ASC,
   SQ_SOLICITACAO_INTERESSADO ASC
);

/*==============================================================*/
/* Index: IN_SIWSOLINT_PESSOA                                   */
/*==============================================================*/
create index IN_SIWSOLINT_PESSOA on SIW_SOLICITACAO_INTERESSADO (
   SQ_PESSOA ASC,
   SQ_SIW_SOLICITACAO ASC,
   SQ_SOLICITACAO_INTERESSADO ASC
);

/*==============================================================*/
/* Index: IN_SIWSOLINT_TIPO                                     */
/*==============================================================*/
create index IN_SIWSOLINT_TIPO on SIW_SOLICITACAO_INTERESSADO (
   SQ_SIW_SOLICITACAO ASC,
   SQ_TIPO_INTERESSADO ASC,
   SQ_SOLICITACAO_INTERESSADO ASC
);

/*==============================================================*/
/* Table: SIW_SOLICITACAO_OBJETIVO                              */
/*==============================================================*/
create table SIW_SOLICITACAO_OBJETIVO  (
   SQ_SIW_SOLICITACAO   NUMBER(18)                      not null,
   SQ_PLANO             NUMBER(18)                      not null,
   SQ_PEOBJETIVO        NUMBER(18)                      not null,
   constraint PK_SIW_SOLICITACAO_OBJETIVO primary key (SQ_SIW_SOLICITACAO, SQ_PEOBJETIVO, SQ_PLANO)
);

comment on table SIW_SOLICITACAO_OBJETIVO is
'Registra a vinculação entre objetivos estratégicos e solicitações.';

comment on column SIW_SOLICITACAO_OBJETIVO.SQ_SIW_SOLICITACAO is
'Chave de SIW_SOLICITACAO. Indica a que solicitação o registro está ligado.';

comment on column SIW_SOLICITACAO_OBJETIVO.SQ_PLANO is
'Chave de PE_PLANO. Indica a que plano a solicitação está ligada.';

comment on column SIW_SOLICITACAO_OBJETIVO.SQ_PEOBJETIVO is
'Chave de SQ_PEOBJETIVO. Indica a que objetivo estratégico a solicitação está ligada.';

/*==============================================================*/
/* Index: IN_SIWSOLOBJ_PLANO                                    */
/*==============================================================*/
create index IN_SIWSOLOBJ_PLANO on SIW_SOLICITACAO_OBJETIVO (
   SQ_PLANO ASC,
   SQ_SIW_SOLICITACAO ASC
);

/*==============================================================*/
/* Index: IN_SIWSOLOBJ_OBJETIVO                                 */
/*==============================================================*/
create index IN_SIWSOLOBJ_OBJETIVO on SIW_SOLICITACAO_OBJETIVO (
   SQ_PEOBJETIVO ASC,
   SQ_SIW_SOLICITACAO ASC
);

/*==============================================================*/
/* Table: SIW_SOLIC_APOIO                                       */
/*==============================================================*/
create table SIW_SOLIC_APOIO  (
   SQ_SOLIC_APOIO       NUMBER(18)                      not null,
   SQ_SIW_SOLICITACAO   NUMBER(18)                      not null,
   SQ_TIPO_APOIO        NUMBER(18)                      not null,
   SQ_PESSOA_ATUALIZACAO NUMBER(18)                      not null,
   ENTIDADE             VARCHAR2(50)                    not null,
   DESCRICAO            VARCHAR2(200),
   VALOR                NUMBER(18,2)                    not null,
   ULTIMA_ATUALIZACAO   DATE                           default sysdate not null,
   constraint PK_SIW_SOLIC_APOIO primary key (SQ_SOLIC_APOIO)
);

comment on table SIW_SOLIC_APOIO is
'Registra os apoios financeiros a uma solicitação.';

comment on column SIW_SOLIC_APOIO.SQ_SOLIC_APOIO is
'Chave de SIW_SOLIC_APOIO.';

comment on column SIW_SOLIC_APOIO.SQ_SIW_SOLICITACAO is
'Chave de SIW_SOLICITACAO, informando a que solicitação o apoio refere-se.';

comment on column SIW_SOLIC_APOIO.SQ_TIPO_APOIO is
'Chave de SIW_TIPO_APOIO, informando o tipo de apoio que a entidade está dando à solicitação.';

comment on column SIW_SOLIC_APOIO.SQ_PESSOA_ATUALIZACAO is
'Chave de CO_PESSOA, indicando o usuário responsável pela inclusão ou última atualização do registro.';

comment on column SIW_SOLIC_APOIO.ENTIDADE is
'Nome da entidade que está dando o apoio.';

comment on column SIW_SOLIC_APOIO.DESCRICAO is
'Descritivo do apoio dado pela entidade.';

comment on column SIW_SOLIC_APOIO.VALOR is
'Valor do apoio.';

comment on column SIW_SOLIC_APOIO.ULTIMA_ATUALIZACAO is
'Data e hora da inclusão ou última atualização do registro.';

/*==============================================================*/
/* Index: IN_SIWSOLAPO_SOLIC                                    */
/*==============================================================*/
create index IN_SIWSOLAPO_SOLIC on SIW_SOLIC_APOIO (
   SQ_SIW_SOLICITACAO ASC,
   SQ_SOLIC_APOIO ASC
);

/*==============================================================*/
/* Table: SIW_SOLIC_ARQUIVO                                     */
/*==============================================================*/
create table SIW_SOLIC_ARQUIVO  (
   SQ_SIW_SOLICITACAO   NUMBER(18)                      not null,
   SQ_SIW_ARQUIVO       NUMBER(18)                      not null,
   constraint PK_SIW_SOLIC_ARQUIVO primary key (SQ_SIW_SOLICITACAO, SQ_SIW_ARQUIVO)
);

comment on table SIW_SOLIC_ARQUIVO is
'Vincula uma solicitação a arquivos físicos.';

comment on column SIW_SOLIC_ARQUIVO.SQ_SIW_SOLICITACAO is
'Chave de SIW_SOLICITACAO indicando a que solicitação o registro está ligado.';

comment on column SIW_SOLIC_ARQUIVO.SQ_SIW_ARQUIVO is
'Chave de SIW_ARQUIVO. Indica a que arquivo o registro está ligado.';

/*==============================================================*/
/* Index: IN_SIWSOLARQ_INVERSA                                  */
/*==============================================================*/
create index IN_SIWSOLARQ_INVERSA on SIW_SOLIC_ARQUIVO (
   SQ_SIW_ARQUIVO ASC,
   SQ_SIW_SOLICITACAO ASC
);

/*==============================================================*/
/* Table: SIW_SOLIC_INDICADOR                                   */
/*==============================================================*/
create table SIW_SOLIC_INDICADOR  (
   SQ_SOLIC_INDICADOR   NUMBER(18)                      not null,
   SQ_SIW_SOLICITACAO   NUMBER(18)                      not null,
   SQ_EOINDICADOR       NUMBER(18)                      not null,
   constraint PK_SIW_SOLIC_INDICADOR primary key (SQ_SOLIC_INDICADOR)
);

comment on table SIW_SOLIC_INDICADOR is
'Registra os indicadores vinculados a solicitação.';

comment on column SIW_SOLIC_INDICADOR.SQ_SOLIC_INDICADOR is
'Chave de SIW_SOLIC_INDICADOR.';

comment on column SIW_SOLIC_INDICADOR.SQ_SIW_SOLICITACAO is
'Chave de SIW_SOLICITACAO indicando a que solicitação o registro está ligado.';

comment on column SIW_SOLIC_INDICADOR.SQ_EOINDICADOR is
'Chave de EO_INDICADOR. Referencia do indicador ligado ao registro.';

/*==============================================================*/
/* Index: IN_SIWSOLIND_SOLIC                                    */
/*==============================================================*/
create index IN_SIWSOLIND_SOLIC on SIW_SOLIC_INDICADOR (
   SQ_SIW_SOLICITACAO ASC,
   SQ_EOINDICADOR ASC,
   SQ_SOLIC_INDICADOR ASC
);

/*==============================================================*/
/* Index: IN_SIWSOLIND_IND                                      */
/*==============================================================*/
create index IN_SIWSOLIND_IND on SIW_SOLIC_INDICADOR (
   SQ_EOINDICADOR ASC,
   SQ_SIW_SOLICITACAO ASC,
   SQ_SOLIC_INDICADOR ASC
);

/*==============================================================*/
/* Table: SIW_SOLIC_LOG                                         */
/*==============================================================*/
create table SIW_SOLIC_LOG  (
   SQ_SIW_SOLIC_LOG     NUMBER(18)                      not null,
   SQ_SIW_SOLICITACAO   NUMBER(18)                      not null,
   SQ_PESSOA            number(18)                      not null,
   SQ_SIW_TRAMITE       NUMBER(18)                      not null,
   DATA                 DATE                           default SYSDATE not null,
   DEVOLUCAO            VARCHAR2(1)                    default 'S' not null
      constraint CKC_SIWSOLLOG_DEV
               check (DEVOLUCAO in ('S','N') and DEVOLUCAO = upper(DEVOLUCAO)),
   OBSERVACAO           VARCHAR2(2000),
   constraint PK_SIW_SOLIC_LOG primary key (SQ_SIW_SOLIC_LOG)
);

comment on table SIW_SOLIC_LOG is
'Registra os trâmites da solicitação';

comment on column SIW_SOLIC_LOG.SQ_SIW_SOLIC_LOG is
'Chave de SIW_SOLIC_LOG.';

comment on column SIW_SOLIC_LOG.SQ_SIW_SOLICITACAO is
'Chave de SIW_SOLICITACAO indicando a que solicitação o registro está ligado.';

comment on column SIW_SOLIC_LOG.SQ_PESSOA is
'Chave de CO_PESSOA. Indica a que pessoa o registro está ligado.';

comment on column SIW_SOLIC_LOG.SQ_SIW_TRAMITE is
'Chave de SIW_TRAMITE. Indica o trâmite da solicitação quando o log foi gerado.';

comment on column SIW_SOLIC_LOG.DATA is
'Data da ocorrência.';

comment on column SIW_SOLIC_LOG.DEVOLUCAO is
'Indica se ocorreu uma devolução de fase.';

comment on column SIW_SOLIC_LOG.OBSERVACAO is
'Observações.';

/*==============================================================*/
/* Index: IN_SIWSOLLOG_SOLIC                                    */
/*==============================================================*/
create index IN_SIWSOLLOG_SOLIC on SIW_SOLIC_LOG (
   SQ_SIW_SOLICITACAO ASC,
   SQ_SIW_TRAMITE ASC,
   SQ_SIW_SOLIC_LOG ASC
);

/*==============================================================*/
/* Index: IN_SIWSOLLOG_PESSOA                                   */
/*==============================================================*/
create index IN_SIWSOLLOG_PESSOA on SIW_SOLIC_LOG (
   SQ_PESSOA ASC,
   SQ_SIW_SOLICITACAO ASC,
   SQ_SIW_SOLIC_LOG ASC
);

/*==============================================================*/
/* Index: IN_SIWSOLLOG_TRAMITE                                  */
/*==============================================================*/
create index IN_SIWSOLLOG_TRAMITE on SIW_SOLIC_LOG (
   SQ_SIW_TRAMITE ASC,
   SQ_SIW_SOLICITACAO ASC,
   SQ_SIW_SOLIC_LOG ASC
);

/*==============================================================*/
/* Table: SIW_SOLIC_LOG_ARQ                                     */
/*==============================================================*/
create table SIW_SOLIC_LOG_ARQ  (
   SQ_SIW_SOLIC_LOG     NUMBER(18)                      not null,
   SQ_SIW_ARQUIVO       NUMBER(18)                      not null,
   constraint PK_SIW_SOLIC_LOG_ARQ primary key (SQ_SIW_SOLIC_LOG, SQ_SIW_ARQUIVO)
);

comment on table SIW_SOLIC_LOG_ARQ is
'Vincula logs de solicitação a arquivos físicos.';

comment on column SIW_SOLIC_LOG_ARQ.SQ_SIW_SOLIC_LOG is
'Chave de SIW_SOLIC_LOG.';

comment on column SIW_SOLIC_LOG_ARQ.SQ_SIW_ARQUIVO is
'Chave de SIW_ARQUIVO. Indica a que arquivo o registro está ligado.';

/*==============================================================*/
/* Index: IN_SIWSOLLOGARQ_INV                                   */
/*==============================================================*/
create index IN_SIWSOLLOGARQ_INV on SIW_SOLIC_LOG_ARQ (
   SQ_SIW_ARQUIVO ASC,
   SQ_SIW_SOLIC_LOG ASC
);

/*==============================================================*/
/* Table: SIW_SOLIC_META                                        */
/*==============================================================*/
create table SIW_SOLIC_META  (
   SQ_SOLIC_META        NUMBER(18)                      not null,
   SQ_SIW_SOLICITACAO   NUMBER(18),
   SQ_PLANO             NUMBER(18),
   SQ_EOINDICADOR       NUMBER(18)                      not null,
   SQ_PESSOA            NUMBER(18)                      not null,
   SQ_UNIDADE           NUMBER(10)                      not null,
   TITULO               VARCHAR2(100)                   not null,
   DESCRICAO            VARCHAR2(2000)                  not null,
   ORDEM                NUMBER(3)                       not null,
   INICIO               DATE                            not null,
   FIM                  DATE                            not null,
   QUANTIDADE           NUMBER(18,4)                   default 0 not null,
   CUMULATIVA           VARCHAR2(1)                    default 'N' not null
      constraint CKC_CUMULATIVA_SIWSOLMET check (CUMULATIVA in ('S','N') and CUMULATIVA = upper(CUMULATIVA)),
   BASE_GEOGRAFICA      NUMBER(1)                       not null,
   SQ_PAIS              NUMBER(18),
   SQ_REGIAO            NUMBER(18),
   CO_UF                VARCHAR2(3),
   SQ_CIDADE            NUMBER(18),
   EXEQUIVEL            VARCHAR2(1)                    default 'S' not null
      constraint CKC_EXEQUIVEL_SIWSOLMET check (EXEQUIVEL in ('S','N') and EXEQUIVEL = upper(EXEQUIVEL)),
   JUSTIFICATIVA_INEXEQUIVEL VARCHAR2(1000),
   OUTRAS_MEDIDAS       VARCHAR2(1000),
   SITUACAO_ATUAL       VARCHAR2(4000),
   CADASTRADOR          NUMBER(18)                      not null,
   INCLUSAO             DATE                           default sysdate not null,
   ULTIMA_ALTERACAO     DATE                           default sysdate,
   VALOR_INICIAL        NUMBER(18,4)                   default 0 not null,
   constraint PK_SIW_SOLIC_META primary key (SQ_SOLIC_META)
);

comment on table SIW_SOLIC_META is
'Registra as metas de uma solicitação ou de um plano estratégico.';

comment on column SIW_SOLIC_META.SQ_SOLIC_META is
'Chave de SIW_SOLIC_META.';

comment on column SIW_SOLIC_META.SQ_SIW_SOLICITACAO is
'Chave de SIW_SOLICITACAO indicando a que solicitação o registro está ligado.';

comment on column SIW_SOLIC_META.SQ_PLANO is
'Chave de PE_PLANO. Indica a que plano estratégico a meta está ligada.';

comment on column SIW_SOLIC_META.SQ_EOINDICADOR is
'Chave de EO_INDICADOR. Referencia do indicador ligado ao registro.';

comment on column SIW_SOLIC_META.SQ_PESSOA is
'Chave de CO_PESSOA. Indica a pessoa responsável pelo monitoramento da meta.';

comment on column SIW_SOLIC_META.SQ_UNIDADE is
'Chave de EO_UNIDADE. Indica a que unidade o registro está ligado.';

comment on column SIW_SOLIC_META.TITULO is
'Título da meta.';

comment on column SIW_SOLIC_META.DESCRICAO is
'Descrição da meta.';

comment on column SIW_SOLIC_META.ORDEM is
'Ordem da meta para exibição em listagens.';

comment on column SIW_SOLIC_META.INICIO is
'Início da execução da meta.';

comment on column SIW_SOLIC_META.FIM is
'Fim da execução da meta.';

comment on column SIW_SOLIC_META.QUANTIDADE is
'Quantidade prevista para a unidade de medida informada.';

comment on column SIW_SOLIC_META.CUMULATIVA is
'Indica se a realização da meta é cumulativa ou não.';

comment on column SIW_SOLIC_META.BASE_GEOGRAFICA is
'Indica a que base geográfica da meta aplica-se. 1 - Nacional, 2 - Regional, 3 - Estadual, 4 - Municipal, 5 - Organizacional.';

comment on column SIW_SOLIC_META.SQ_PAIS is
'Chave de CO_PAIS. Tem valor apenas quando a aferição é a nivel nacional.';

comment on column SIW_SOLIC_META.SQ_REGIAO is
'Chave de CO_REGIAO. Tem valor apenas quando a aferição é a nivel regional.';

comment on column SIW_SOLIC_META.CO_UF is
'Chave de CO_UF. Tem valor apenas quando a aferição é a nivel estadual.';

comment on column SIW_SOLIC_META.SQ_CIDADE is
'Chave de CO_CIDADE. Tem valor apenas quando a aferição é a nivel municipal.';

comment on column SIW_SOLIC_META.EXEQUIVEL is
'Indica se a meta está avaliada como passível de cumprimento ou não.';

comment on column SIW_SOLIC_META.JUSTIFICATIVA_INEXEQUIVEL is
'Motivos que justificam o não cumprimento da meta.';

comment on column SIW_SOLIC_META.OUTRAS_MEDIDAS is
'Descrição das medidas necessárias ao cumprimento da meta.';

comment on column SIW_SOLIC_META.SITUACAO_ATUAL is
'Texto detalhando a situação atual da meta.';

comment on column SIW_SOLIC_META.CADASTRADOR is
'Chave de CO_PESSOA. Indica o responsável pelo cadastramento ou pela última alteração no registro.';

comment on column SIW_SOLIC_META.INCLUSAO is
'Data de inclusão do registro.';

comment on column SIW_SOLIC_META.ULTIMA_ALTERACAO is
'Data da última alteração no registro.';

comment on column SIW_SOLIC_META.VALOR_INICIAL is
'Valor do indicador ligado à meta na data de início.';

/*==============================================================*/
/* Index: IN_SIWSOLMET_IND                                      */
/*==============================================================*/
create index IN_SIWSOLMET_IND on SIW_SOLIC_META (
   SQ_EOINDICADOR ASC,
   SQ_SIW_SOLICITACAO ASC,
   SQ_SOLIC_META ASC
);

/*==============================================================*/
/* Index: IN_SIWSOLMET_SOLIC                                    */
/*==============================================================*/
create index IN_SIWSOLMET_SOLIC on SIW_SOLIC_META (
   SQ_SIW_SOLICITACAO ASC,
   SQ_SOLIC_META ASC
);

/*==============================================================*/
/* Index: IN_SIWSOLMET_RESP                                     */
/*==============================================================*/
create index IN_SIWSOLMET_RESP on SIW_SOLIC_META (
   SQ_PESSOA ASC,
   SQ_SOLIC_META ASC
);

/*==============================================================*/
/* Index: IN_SIWSOLMET_UNID                                     */
/*==============================================================*/
create index IN_SIWSOLMET_UNID on SIW_SOLIC_META (
   SQ_UNIDADE ASC,
   SQ_SOLIC_META ASC
);

/*==============================================================*/
/* Index: IN_SIWSOLMET_PLANO                                    */
/*==============================================================*/
create index IN_SIWSOLMET_PLANO on SIW_SOLIC_META (
   SQ_PLANO ASC,
   SQ_SOLIC_META ASC
);

/*==============================================================*/
/* Table: SIW_SOLIC_RECURSO                                     */
/*==============================================================*/
create table SIW_SOLIC_RECURSO  (
   SQ_SOLIC_RECURSO     NUMBER(18)                      not null,
   SQ_SIW_SOLICITACAO   NUMBER(18)                      not null,
   SQ_RECURSO           NUMBER(18)                      not null,
   TIPO                 NUMBER(1)                       not null,
   SOLICITANTE          NUMBER(18)                      not null,
   JUSTIFICATIVA        VARCHAR2(2000)                  not null,
   INCLUSAO             DATE                            not null,
   AUTORIZADO           VARCHAR2(1)                    default 'N' not null
      constraint CKC_AUTORIZADO_SIW_SOLI check (AUTORIZADO in ('S','N') and AUTORIZADO = upper(AUTORIZADO)),
   AUTORIZACAO          DATE,
   AUTORIZADOR          NUMBER(18),
   constraint PK_SIW_SOLIC_RECURSO primary key (SQ_SOLIC_RECURSO)
);

comment on table SIW_SOLIC_RECURSO is
'Registra o consumo de um recurso por uma solicitação.';

comment on column SIW_SOLIC_RECURSO.SQ_SOLIC_RECURSO is
'Chave de SIW_SOLIC_RECURSO.';

comment on column SIW_SOLIC_RECURSO.SQ_SIW_SOLICITACAO is
'Chave de SIW_SOLICITACAO. Indica a que solicitação o pedido de alocação está ligado.';

comment on column SIW_SOLIC_RECURSO.SQ_RECURSO is
'Chave de EO_RECURSO. Indica a que recurso a solicitação está ligada.';

comment on column SIW_SOLIC_RECURSO.TIPO is
'Tipo do pedido. 1 - Alocação; 2 - Liberação.';

comment on column SIW_SOLIC_RECURSO.SOLICITANTE is
'Chave de CO_PESSOA. Indica a pessoa que solicitou a alocação ou liberação do recurso.';

comment on column SIW_SOLIC_RECURSO.JUSTIFICATIVA is
'Justificativa para alocação ou liberação do recurso, permitindo à área gestora do recurso decidir sobre sua autorização.';

comment on column SIW_SOLIC_RECURSO.INCLUSAO is
'Data de inclusão do pedido de alocação ou liberação do recurso.';

comment on column SIW_SOLIC_RECURSO.AUTORIZADO is
'Indica se a alocação ou liberação já foi autorizada pela unidade gestora do recurso.';

comment on column SIW_SOLIC_RECURSO.AUTORIZACAO is
'Data de autorização para alocação ou liberação do recurso.';

comment on column SIW_SOLIC_RECURSO.AUTORIZADOR is
'Chave de CO_PESSOA. Indica a pessoa que autorizou a alocação ou liberação do recurso.';

/*==============================================================*/
/* Index: IN_SIWSOLREC_RECURSO                                  */
/*==============================================================*/
create index IN_SIWSOLREC_RECURSO on SIW_SOLIC_RECURSO (
   SQ_RECURSO ASC,
   SQ_SIW_SOLICITACAO ASC,
   SQ_SOLIC_RECURSO ASC
);

/*==============================================================*/
/* Index: IN_SIWSOLREC_SOLIC                                    */
/*==============================================================*/
create index IN_SIWSOLREC_SOLIC on SIW_SOLIC_RECURSO (
   SQ_SIW_SOLICITACAO ASC,
   SQ_RECURSO ASC,
   SQ_SOLIC_RECURSO ASC
);

/*==============================================================*/
/* Table: SIW_SOLIC_RECURSO_ALOCACAO                            */
/*==============================================================*/
create table SIW_SOLIC_RECURSO_ALOCACAO  (
   SQ_SOLIC_RECURSO_ALOCACAO NUMBER(18)                      not null,
   SQ_SOLIC_RECURSO     NUMBER(18)                      not null,
   INICIO               DATE                            not null,
   FIM                  DATE                            not null,
   UNIDADES_SOLICITADAS NUMBER(18,2)                    not null,
   UNIDADES_AUTORIZADAS NUMBER(18,2)                    not null,
   constraint PK_SIW_SOLIC_RECURSO_ALOCACAO primary key (SQ_SOLIC_RECURSO_ALOCACAO)
);

comment on table SIW_SOLIC_RECURSO_ALOCACAO is
'Registra os dados da alocação do recurso por uma solicitação.';

comment on column SIW_SOLIC_RECURSO_ALOCACAO.SQ_SOLIC_RECURSO_ALOCACAO is
'Chave de SIW_SOLIC_RECURSO_ALOCACAO.';

comment on column SIW_SOLIC_RECURSO_ALOCACAO.SQ_SOLIC_RECURSO is
'Chave de SIW_SOLIC_RECURSO. Indica a que solicitação o registro está ligado.';

comment on column SIW_SOLIC_RECURSO_ALOCACAO.INICIO is
'Data de início da alocação ou liberação do recurso.';

comment on column SIW_SOLIC_RECURSO_ALOCACAO.FIM is
'Data de término da alocação ou liberação do recurso.';

comment on column SIW_SOLIC_RECURSO_ALOCACAO.UNIDADES_SOLICITADAS is
'Quantidade de unidades solicitadas para alocação ou liberação do recurso.';

comment on column SIW_SOLIC_RECURSO_ALOCACAO.UNIDADES_AUTORIZADAS is
'Quantidade de unidades autorizadas para alocação ou liberação do recurso.';

/*==============================================================*/
/* Index: IN_SIWSOLRECALO_INICIO                                */
/*==============================================================*/
create index IN_SIWSOLRECALO_INICIO on SIW_SOLIC_RECURSO_ALOCACAO (
   SQ_SOLIC_RECURSO ASC,
   INICIO ASC,
   SQ_SOLIC_RECURSO_ALOCACAO ASC
);

/*==============================================================*/
/* Index: IN_SIWSOLRECALO_FIM                                   */
/*==============================================================*/
create index IN_SIWSOLRECALO_FIM on SIW_SOLIC_RECURSO_ALOCACAO (
   SQ_SOLIC_RECURSO ASC,
   FIM ASC,
   SQ_SOLIC_RECURSO_ALOCACAO ASC
);

/*==============================================================*/
/* Table: SIW_SOLIC_RECURSO_LOG                                 */
/*==============================================================*/
create table SIW_SOLIC_RECURSO_LOG  (
   SQ_SOLIC_RECURSO_LOG NUMBER(18)                      not null,
   SQ_SOLIC_RECURSO     NUMBER(18)                      not null,
   SQ_PESSOA            NUMBER(18)                      not null,
   DATA                 DATE                            not null,
   TIPO                 NUMBER(1)                       not null,
   INICIO               DATE                            not null,
   FIM                  DATE                            not null,
   UNIDADES_AUTORIZADAS NUMBER(18,2),
   constraint PK_SIW_SOLIC_RECURSO_LOG primary key (SQ_SOLIC_RECURSO_LOG)
);

comment on table SIW_SOLIC_RECURSO_LOG is
'Registra o log de solicitações para alocação ou liberação de recursos.';

comment on column SIW_SOLIC_RECURSO_LOG.SQ_SOLIC_RECURSO_LOG is
'Chave de SIW_SOLIC_RECURSO_LOG.';

comment on column SIW_SOLIC_RECURSO_LOG.SQ_SOLIC_RECURSO is
'Chave de SIW_SOLIC_RECURSO. Indica a que solicitação o registro está ligado.';

comment on column SIW_SOLIC_RECURSO_LOG.SQ_PESSOA is
'Chave de CO_PESSOA. Indica a pessoa responsável pela ocorrência.';

comment on column SIW_SOLIC_RECURSO_LOG.DATA is
'Data da ocorrência.';

comment on column SIW_SOLIC_RECURSO_LOG.TIPO is
'Tipo da ocorrência. 1 - Inclusão pelo solicitante; 2 - Envio para autorização; 3 - Concessão de autorização; 4 - Ajuste pelo autorizador.';

comment on column SIW_SOLIC_RECURSO_LOG.INICIO is
'Data de início da alocação ou liberação do recurso.';

comment on column SIW_SOLIC_RECURSO_LOG.FIM is
'Data de término da alocação ou liberação do recurso.';

comment on column SIW_SOLIC_RECURSO_LOG.UNIDADES_AUTORIZADAS is
'Quantidade de unidades autorizadas para alocação ou liberação do recurso.';

/*==============================================================*/
/* Index: IN_SIWSOLRECLOG_SOLIC                                 */
/*==============================================================*/
create index IN_SIWSOLRECLOG_SOLIC on SIW_SOLIC_RECURSO_LOG (
   SQ_SOLIC_RECURSO ASC,
   DATA ASC,
   SQ_SOLIC_RECURSO_LOG ASC
);

/*==============================================================*/
/* Index: IN_SIWSOLRECLOG_PESSOA                                */
/*==============================================================*/
create index IN_SIWSOLRECLOG_PESSOA on SIW_SOLIC_RECURSO_LOG (
   SQ_PESSOA ASC,
   SQ_SOLIC_RECURSO_LOG ASC
);

/*==============================================================*/
/* Table: SIW_SOLIC_SITUACAO                                    */
/*==============================================================*/
create table SIW_SOLIC_SITUACAO  (
   SQ_SOLIC_SITUACAO    NUMBER(18)                      not null,
   SQ_SIW_SOLICITACAO   NUMBER(18)                      not null,
   SQ_PESSOA            NUMBER(18)                      not null,
   INICIO               DATE                            not null,
   FIM                  DATE                            not null,
   SITUACAO             VARCHAR2(1000)                  not null,
   PROGRESSOS           VARCHAR2(1000),
   PASSOS               VARCHAR2(1000),
   ULTIMA_ALTERACAO     DATE                           default sysdate not null,
   constraint PK_SIW_SOLIC_SITUACAO primary key (SQ_SOLIC_SITUACAO)
);

comment on table SIW_SOLIC_SITUACAO is
'Registra reportes periódicos sobre a situação da solicitação.';

comment on column SIW_SOLIC_SITUACAO.SQ_SOLIC_SITUACAO is
'Chave de SIW_SOLIC_SITUACAO.';

comment on column SIW_SOLIC_SITUACAO.SQ_SIW_SOLICITACAO is
'Chave de SIW_SOLICITACAO indicando a que solicitação o registro está ligado.';

comment on column SIW_SOLIC_SITUACAO.SQ_PESSOA is
'Chave de CO_PESSOA. Usuário responsável pela última alteração no registro.';

comment on column SIW_SOLIC_SITUACAO.INICIO is
'Início do período de reporte.';

comment on column SIW_SOLIC_SITUACAO.FIM is
'Término do período de reporte.';

comment on column SIW_SOLIC_SITUACAO.SITUACAO is
'Comentários gerais e pontos de atenção.';

comment on column SIW_SOLIC_SITUACAO.PROGRESSOS is
'Principais progressos.';

comment on column SIW_SOLIC_SITUACAO.PASSOS is
'Próximos passos.';

comment on column SIW_SOLIC_SITUACAO.ULTIMA_ALTERACAO is
'Data de última alteração do registro.';

/*==============================================================*/
/* Index: IN_SIWSOLSIT_INICIO                                   */
/*==============================================================*/
create index IN_SIWSOLSIT_INICIO on SIW_SOLIC_SITUACAO (
   INICIO ASC,
   SQ_SOLIC_SITUACAO ASC
);

/*==============================================================*/
/* Index: IN_SIWSOLSIT_FIM                                      */
/*==============================================================*/
create index IN_SIWSOLSIT_FIM on SIW_SOLIC_SITUACAO (
   FIM ASC,
   SQ_SOLIC_SITUACAO ASC
);

/*==============================================================*/
/* Index: IN_SIWSOLSIT_ALTER                                    */
/*==============================================================*/
create index IN_SIWSOLSIT_ALTER on SIW_SOLIC_SITUACAO (
   ULTIMA_ALTERACAO ASC,
   SQ_SOLIC_SITUACAO ASC
);

/*==============================================================*/
/* Index: IN_SIWSOLSIT_PESSOA                                   */
/*==============================================================*/
create index IN_SIWSOLSIT_PESSOA on SIW_SOLIC_SITUACAO (
   SQ_PESSOA ASC,
   SQ_SOLIC_SITUACAO ASC
);

/*==============================================================*/
/* Index: IN_SIWSOLSIT_SOLIC                                    */
/*==============================================================*/
create index IN_SIWSOLSIT_SOLIC on SIW_SOLIC_SITUACAO (
   SQ_SIW_SOLICITACAO ASC,
   SQ_SOLIC_SITUACAO ASC
);

/*==============================================================*/
/* Table: SIW_SOLIC_VINCULO                                     */
/*==============================================================*/
create table SIW_SOLIC_VINCULO  (
   SQ_SIW_SOLICITACAO   NUMBER(18)                      not null,
   SQ_MENU              NUMBER(18)                      not null,
   constraint PK_SIW_SOLIC_VINCULO primary key (SQ_SIW_SOLICITACAO, SQ_MENU)
);

comment on table SIW_SOLIC_VINCULO is
'Registra os vínculos possíveis para uma solicitação.';

comment on column SIW_SOLIC_VINCULO.SQ_SIW_SOLICITACAO is
'Chave de SIW_SOLICITACAO. Indica a que solicitação o registro está ligado.';

comment on column SIW_SOLIC_VINCULO.SQ_MENU is
'Chave de SIW_MENU. Indica a que opção do menu o registro está ligado.';

/*==============================================================*/
/* Index: IN_SIWSOLVIN_INV                                      */
/*==============================================================*/
create index IN_SIWSOLVIN_INV on SIW_SOLIC_VINCULO (
   SQ_MENU ASC,
   SQ_SIW_SOLICITACAO ASC
);

/*==============================================================*/
/* Table: SIW_TIPO_APOIO                                        */
/*==============================================================*/
create table SIW_TIPO_APOIO  (
   SQ_TIPO_APOIO        NUMBER(18)                      not null,
   CLIENTE              NUMBER(18)                      not null,
   NOME                 VARCHAR2(50)                    not null,
   SIGLA                VARCHAR2(10)                    not null,
   DESCRICAO            VARCHAR2(400),
   ATIVO                VARCHAR2(1)                    default 'S' not null
      constraint CKC_ATIVO_SIW_TIPO check (ATIVO in ('S','N') and ATIVO = upper(ATIVO)),
   constraint PK_SIW_TIPO_APOIO primary key (SQ_TIPO_APOIO)
);

comment on table SIW_TIPO_APOIO is
'Registra os tipos possíveis de apoio financeiro.';

comment on column SIW_TIPO_APOIO.SQ_TIPO_APOIO is
'Chave de SIW_TIPO_APOIO.';

comment on column SIW_TIPO_APOIO.CLIENTE is
'Cliente ao qual o tipo de apoio está vinculado.';

comment on column SIW_TIPO_APOIO.NOME is
'Nome do tipo de apoio.';

comment on column SIW_TIPO_APOIO.SIGLA is
'Sigla do tipo de apoio.';

comment on column SIW_TIPO_APOIO.DESCRICAO is
'Descrição do tipo de apoio.';

comment on column SIW_TIPO_APOIO.ATIVO is
'Indica se este tipo pode ser associado a novos registros.';

/*==============================================================*/
/* Index: IN_SIWTIPAPO_CLIENTE                                  */
/*==============================================================*/
create index IN_SIWTIPAPO_CLIENTE on SIW_TIPO_APOIO (
   CLIENTE ASC,
   SQ_TIPO_APOIO ASC
);

/*==============================================================*/
/* Table: SIW_TIPO_ARQUIVO                                      */
/*==============================================================*/
create table SIW_TIPO_ARQUIVO  (
   SQ_TIPO_ARQUIVO      NUMBER(18)                      not null,
   CLIENTE              NUMBER(18)                      not null,
   NOME                 VARCHAR2(50)                    not null,
   SIGLA                VARCHAR2(10)                    not null,
   DESCRICAO            VARCHAR2(400),
   ATIVO                VARCHAR2(1)                    default 'S' not null
      constraint CKC_ATIVO_SIWTPARQ check (ATIVO in ('S','N') and ATIVO = upper(ATIVO)),
   constraint PK_SIW_TIPO_ARQUIVO primary key (SQ_TIPO_ARQUIVO)
);

comment on table SIW_TIPO_ARQUIVO is
'Registra os tipos possíveis de arquivos.';

comment on column SIW_TIPO_ARQUIVO.SQ_TIPO_ARQUIVO is
'Chave de SIW_TIPO_ARQUIVO.';

comment on column SIW_TIPO_ARQUIVO.CLIENTE is
'Cliente ao qual o registro está vinculado.';

comment on column SIW_TIPO_ARQUIVO.NOME is
'Nome do tipo de arquivo.';

comment on column SIW_TIPO_ARQUIVO.SIGLA is
'Sigla do tipo de arquivo.';

comment on column SIW_TIPO_ARQUIVO.DESCRICAO is
'Descrição do tipo de arquivo.';

comment on column SIW_TIPO_ARQUIVO.ATIVO is
'Indica se este tipo pode ser associado a novos registros.';

/*==============================================================*/
/* Index: IN_SIWTIPARQ_ATIVO                                    */
/*==============================================================*/
create index IN_SIWTIPARQ_ATIVO on SIW_TIPO_ARQUIVO (
   CLIENTE ASC,
   ATIVO ASC,
   SQ_TIPO_ARQUIVO ASC
);

/*==============================================================*/
/* Table: SIW_TIPO_EVENTO                                       */
/*==============================================================*/
create table SIW_TIPO_EVENTO  (
   SQ_TIPO_EVENTO       NUMBER(18)                      not null,
   SQ_MENU              NUMBER(18)                      not null,
   NOME                 VARCHAR2(60)                    not null,
   ORDEM                NUMBER(4)                       not null,
   SIGLA                VARCHAR2(15)                    not null,
   DESCRICAO            VARCHAR2(2000)                  not null,
   ATIVO                VARCHAR2(1)                    default 'S' not null
      constraint CKC_ATIVO_SIWTIPEVE check (ATIVO in ('S','N') and ATIVO = upper(ATIVO)),
   constraint PK_SIW_TIPO_EVENTO primary key (SQ_TIPO_EVENTO)
);

comment on table SIW_TIPO_EVENTO is
'Registra os tipos de evento.';

comment on column SIW_TIPO_EVENTO.SQ_TIPO_EVENTO is
'Chave de SIW_TIPO_EVENTO.';

comment on column SIW_TIPO_EVENTO.SQ_MENU is
'Chave de SIW_MENU. Indica a que opção do menu o registro está ligado.';

comment on column SIW_TIPO_EVENTO.NOME is
'Nome do tipo de evento.';

comment on column SIW_TIPO_EVENTO.ORDEM is
'Indica a ordem do registro nas listagens.';

comment on column SIW_TIPO_EVENTO.SIGLA is
'Sigla do tipo de evento.';

comment on column SIW_TIPO_EVENTO.DESCRICAO is
'Descrição do tipo de evento.';

comment on column SIW_TIPO_EVENTO.ATIVO is
'Indica se o tipo pode ser vinculado a novos registros.';

/*==============================================================*/
/* Index: IN_SIWTIPEVE_NOME                                     */
/*==============================================================*/
create index IN_SIWTIPEVE_NOME on SIW_TIPO_EVENTO (
   SQ_MENU ASC,
   NOME ASC,
   SQ_TIPO_EVENTO ASC
);

/*==============================================================*/
/* Index: IN_SIWTIPEVE_SIGLA                                    */
/*==============================================================*/
create index IN_SIWTIPEVE_SIGLA on SIW_TIPO_EVENTO (
   SQ_MENU ASC,
   SIGLA ASC,
   SQ_TIPO_EVENTO ASC
);

/*==============================================================*/
/* Index: IN_SIWTIPEVE_ATIVO                                    */
/*==============================================================*/
create index IN_SIWTIPEVE_ATIVO on SIW_TIPO_EVENTO (
   SQ_MENU ASC,
   ATIVO ASC,
   SQ_TIPO_EVENTO ASC
);

/*==============================================================*/
/* Table: SIW_TIPO_INTERESSADO                                  */
/*==============================================================*/
create table SIW_TIPO_INTERESSADO  (
   SQ_TIPO_INTERESSADO  NUMBER(18)                      not null,
   SQ_MENU              NUMBER(18)                      not null,
   NOME                 VARCHAR2(60)                    not null,
   ORDEM                NUMBER(4)                       not null,
   SIGLA                VARCHAR2(15)                    not null,
   DESCRICAO            VARCHAR2(2000)                  not null,
   ATIVO                VARCHAR2(1)                    default 'S' not null
      constraint CKC_ATIVO_SIWTIPINT check (ATIVO in ('S','N') and ATIVO = upper(ATIVO)),
   constraint PK_SIW_TIPO_INTERESSADO primary key (SQ_TIPO_INTERESSADO)
);

comment on table SIW_TIPO_INTERESSADO is
'Registra os tipos de interessado.';

comment on column SIW_TIPO_INTERESSADO.SQ_TIPO_INTERESSADO is
'Chave de SIW_TIPO_INTERESSADO.';

comment on column SIW_TIPO_INTERESSADO.SQ_MENU is
'Chave de SIW_MENU. Indica a que opção do menu o registro está ligado.';

comment on column SIW_TIPO_INTERESSADO.NOME is
'Nome do tipo de interessado.';

comment on column SIW_TIPO_INTERESSADO.ORDEM is
'Indica a ordem do registro nas listagens.';

comment on column SIW_TIPO_INTERESSADO.SIGLA is
'Sigla do tipo de interessado.';

comment on column SIW_TIPO_INTERESSADO.DESCRICAO is
'Descrição do tipo de interessado.';

comment on column SIW_TIPO_INTERESSADO.ATIVO is
'Indica se o tipo pode ser vinculado a novos registros.';

/*==============================================================*/
/* Index: IN_SIWTIPINT_NOME                                     */
/*==============================================================*/
create index IN_SIWTIPINT_NOME on SIW_TIPO_INTERESSADO (
   SQ_MENU ASC,
   NOME ASC,
   SQ_TIPO_INTERESSADO ASC
);

/*==============================================================*/
/* Index: IN_SIWTIPINT_SIGLA                                    */
/*==============================================================*/
create index IN_SIWTIPINT_SIGLA on SIW_TIPO_INTERESSADO (
   SQ_MENU ASC,
   SIGLA ASC,
   SQ_TIPO_INTERESSADO ASC
);

/*==============================================================*/
/* Index: IN_SIWTIPINT_ATIVO                                    */
/*==============================================================*/
create index IN_SIWTIPINT_ATIVO on SIW_TIPO_INTERESSADO (
   SQ_MENU ASC,
   ATIVO ASC,
   SQ_TIPO_INTERESSADO ASC
);

/*==============================================================*/
/* Table: SIW_TIPO_LOG                                          */
/*==============================================================*/
create table SIW_TIPO_LOG  (
   SQ_TIPO_LOG          NUMBER(18)                      not null,
   CLIENTE              NUMBER(18)                      not null,
   SQ_MENU              NUMBER(18)                      not null,
   NOME                 VARCHAR2(60)                    not null,
   SIGLA                VARCHAR2(10)                    not null,
   ORDEM                NUMBER(4)                       not null,
   ATIVO                VARCHAR2(1)                    default 'S' not null
      constraint CKC_SIWTIPLOG_ATIVO check (ATIVO in ('S','N') and ATIVO = upper(ATIVO)),
   constraint PK_SIW_TIPO_LOG primary key (SQ_TIPO_LOG)
);

comment on table SIW_TIPO_LOG is
'Registra os tipos de log de cada serviço.';

comment on column SIW_TIPO_LOG.SQ_TIPO_LOG is
'Chave de SIW_TIPO_LOG.';

comment on column SIW_TIPO_LOG.CLIENTE is
'Chave de CO_PESSOA. Indica a que cliente o registro está ligado.';

comment on column SIW_TIPO_LOG.SQ_MENU is
'Chave de SIW_MENU. Indica a que serviço o tipo está ligado.';

comment on column SIW_TIPO_LOG.NOME is
'Nome do tipo.';

comment on column SIW_TIPO_LOG.SIGLA is
'Sigla do tipo.';

comment on column SIW_TIPO_LOG.ORDEM is
'Ordem do tipo para exibição em listagens.';

comment on column SIW_TIPO_LOG.ATIVO is
'Indica se o tipo de log pode ser vinculado a novos registros.';

/*==============================================================*/
/* Index: IN_SIWTIPLOG_MENU                                     */
/*==============================================================*/
create index IN_SIWTIPLOG_MENU on SIW_TIPO_LOG (
   SQ_MENU ASC,
   SQ_TIPO_LOG ASC
);

/*==============================================================*/
/* Index: IN_SIWTIPLOG_CLIENTE                                  */
/*==============================================================*/
create index IN_SIWTIPLOG_CLIENTE on SIW_TIPO_LOG (
   CLIENTE ASC,
   SQ_TIPO_LOG ASC
);

/*==============================================================*/
/* Table: SIW_TIPO_RESTRICAO                                    */
/*==============================================================*/
create table SIW_TIPO_RESTRICAO  (
   SQ_TIPO_RESTRICAO    NUMBER(18)                      not null,
   CLIENTE              NUMBER(18)                      not null,
   NOME                 VARCHAR2(20)                    not null,
   ATIVO                VARCHAR2(1)                    default 'S' not null
      constraint CKC_ATIVO_SIWTIPRES check (ATIVO in ('S','N') and ATIVO = upper(ATIVO)),
   CODIGO_EXTERNO       VARCHAR2(30),
   constraint PK_SIW_TIPO_RESTRICAO primary key (SQ_TIPO_RESTRICAO)
);

comment on table SIW_TIPO_RESTRICAO is
'Registra os tipos de restrição.';

comment on column SIW_TIPO_RESTRICAO.SQ_TIPO_RESTRICAO is
'Chave de SIW_TIPO_RESTRICAO.';

comment on column SIW_TIPO_RESTRICAO.CLIENTE is
'Chave de CO_PESSOA. Indica a que cliente o registro está ligado.';

comment on column SIW_TIPO_RESTRICAO.NOME is
'Nome do tipo de restrição.';

comment on column SIW_TIPO_RESTRICAO.ATIVO is
'Indica se o tipo pode ser vinculado a novos registros.';

comment on column SIW_TIPO_RESTRICAO.CODIGO_EXTERNO is
'Código desse registro num sistema externo.';

/*==============================================================*/
/* Index: IN_SIWTIPRES_CLIENTE                                  */
/*==============================================================*/
create index IN_SIWTIPRES_CLIENTE on SIW_TIPO_RESTRICAO (
   CLIENTE ASC,
   SQ_TIPO_RESTRICAO ASC
);

/*==============================================================*/
/* Index: IN_SIWTIPRES_NOME                                     */
/*==============================================================*/
create unique index IN_SIWTIPRES_NOME on SIW_TIPO_RESTRICAO (
   CLIENTE ASC,
   NOME ASC
);

/*==============================================================*/
/* Table: SIW_TRAMITE                                           */
/*==============================================================*/
create table SIW_TRAMITE  (
   SQ_SIW_TRAMITE       NUMBER(18)                      not null,
   SQ_MENU              NUMBER(18)                      not null,
   NOME                 VARCHAR2(50)                    not null,
   ORDEM                NUMBER(2)                       not null,
   SIGLA                VARCHAR2(2)                     not null,
   DESCRICAO            VARCHAR2(500),
   CHEFIA_IMEDIATA      VARCHAR2(1)                    default 'N' not null
      constraint CKC_SIWTRA_CHEIME
               check (CHEFIA_IMEDIATA in ('S','N','U','I') and CHEFIA_IMEDIATA = upper(CHEFIA_IMEDIATA)),
   ATIVO                VARCHAR2(1)                    default 'S' not null
      constraint CKC_ATIVO_SIW_TRAM check (ATIVO in ('S','N') and ATIVO = upper(ATIVO)),
   SOLICITA_CC          VARCHAR2(1)                    default 'N' not null
      constraint CKC_SIWTRA_SOLCC check (SOLICITA_CC in ('S','N') and SOLICITA_CC = upper(SOLICITA_CC)),
   ENVIA_MAIL           VARCHAR2(1)                    default 'N' not null
      constraint CKC_SIWTRA_MAIL check (ENVIA_MAIL in ('S','N') and ENVIA_MAIL = upper(ENVIA_MAIL)),
   DESTINATARIO         VARCHAR2(1)                    default 'S' not null
      constraint CKC_DESTINATARIO_SIW_TRAM check (DESTINATARIO in ('S','N') and DESTINATARIO = upper(DESTINATARIO)),
   ASSINA_TRAMITE_ANTERIOR VARCHAR2(1)                    default 'S' not null
      constraint CKC_ASSINA_TRAMITE_AN_SIW_TRAM check (ASSINA_TRAMITE_ANTERIOR in ('S','N') and ASSINA_TRAMITE_ANTERIOR = upper(ASSINA_TRAMITE_ANTERIOR)),
   BENEFICIARIO_CUMPRE  VARCHAR2(1)                    default 'S' not null
      constraint CKC_BENEFICIARIO_CUMP_SIW_TRAM check (BENEFICIARIO_CUMPRE in ('S','N') and BENEFICIARIO_CUMPRE = upper(BENEFICIARIO_CUMPRE)),
   GESTOR_CUMPRE        VARCHAR2(1)                    default 'S' not null
      constraint CKC_GESTOR_CUMPRE_SIW_TRAM check (GESTOR_CUMPRE in ('S','N') and GESTOR_CUMPRE = upper(GESTOR_CUMPRE)),
   constraint PK_SIW_TRAMITE primary key (SQ_SIW_TRAMITE)
);

comment on table SIW_TRAMITE is
'Trâmites de um serviço';

comment on column SIW_TRAMITE.SQ_SIW_TRAMITE is
'Chave de SIW_TRAMITE.';

comment on column SIW_TRAMITE.SQ_MENU is
'Chave de SIW_MENU. Indica a que opção do menu o registro está ligado.';

comment on column SIW_TRAMITE.NOME is
'Nome do trâmite.';

comment on column SIW_TRAMITE.ORDEM is
'Armazena a seqüência do trâmite. Seus valores devem ser inteiros consecutivos. O primeiro trâmite deve ter ordem=1, o segundo deve ter ordem=2 etc.

Se um novo trâmite precisar ser inserido entre dois que já existam, basta renumerar a ordem dos trâmites seguintes ao que foi incluído.';

comment on column SIW_TRAMITE.SIGLA is
'Sigla do trâmite.';

comment on column SIW_TRAMITE.DESCRICAO is
'Descrição do trâmite.';

comment on column SIW_TRAMITE.CHEFIA_IMEDIATA is
'Indica quem deverá cumprir este trâmite, podendo ser o chefe imediato, a unidade executora, qualquer pessoa com permissão ou todos os usuários internos. Se for a unidade solicitante ou executora, a solicitação aparecerá para o titular/substituto da unidade e para quaisquer outras pessoas com permissão.';

comment on column SIW_TRAMITE.ATIVO is
'Indica se o registro deve ou não ser exibido.';

comment on column SIW_TRAMITE.SOLICITA_CC is
'Indica se deve ser solicitado um centro de custo ao usuário no cumprimento deste trâmite.';

comment on column SIW_TRAMITE.ENVIA_MAIL is
'Indica se deve ser enviado e-mail aos interessados no cumprimento deste trâmite.';

comment on column SIW_TRAMITE.DESTINATARIO is
'Se igual a S, sempre pedirá destinatário quando um encaminhamento for feito. Caso contrário, aparecerá na mesa de trabalho das pessoas que puderem cumprir o trâmite.';

comment on column SIW_TRAMITE.ASSINA_TRAMITE_ANTERIOR is
'Indica se o trâmite atual pode ser cumprido pela mesma pessoa que cumpriu o trâmite anterior.';

comment on column SIW_TRAMITE.BENEFICIARIO_CUMPRE is
'Indica se o beneficiário da solicitação pode cumprir o trâmite.';

comment on column SIW_TRAMITE.GESTOR_CUMPRE is
'Indica se gestor pode cumprir o trâmite.';

/*==============================================================*/
/* Index: IN_SIWTRA_ORDEM                                       */
/*==============================================================*/
create unique index IN_SIWTRA_ORDEM on SIW_TRAMITE (
   SQ_MENU ASC,
   ORDEM ASC
);

/*==============================================================*/
/* Index: IN_SIWTRA_CHEFIA                                      */
/*==============================================================*/
create index IN_SIWTRA_CHEFIA on SIW_TRAMITE (
   CHEFIA_IMEDIATA ASC
);

/*==============================================================*/
/* Index: IN_SIWTRA_ATIVO                                       */
/*==============================================================*/
create index IN_SIWTRA_ATIVO on SIW_TRAMITE (
   ATIVO ASC
);

/*==============================================================*/
/* Table: SIW_TRAMITE_FLUXO                                     */
/*==============================================================*/
create table SIW_TRAMITE_FLUXO  (
   SQ_SIW_TRAMITE_ORIGEM NUMBER(18)                      not null,
   SQ_SIW_TRAMITE_DESTINO NUMBER(18)                      not null,
   constraint PK_SIW_TRAMITE_FLUXO primary key (SQ_SIW_TRAMITE_ORIGEM, SQ_SIW_TRAMITE_DESTINO)
);

comment on table SIW_TRAMITE_FLUXO is
'Registra os trâmites de destino possíveis para um trâmite de origem.';

comment on column SIW_TRAMITE_FLUXO.SQ_SIW_TRAMITE_ORIGEM is
'Chave de SIW_TRAMITE. Indica o trâmite de origem.';

comment on column SIW_TRAMITE_FLUXO.SQ_SIW_TRAMITE_DESTINO is
'Chave de SIW_TRAMITE. Indica o trâmite de destino.';

/*==============================================================*/
/* Index: IN_SIWTRAFLUX_INVERSA                                 */
/*==============================================================*/
create index IN_SIWTRAFLUX_INVERSA on SIW_TRAMITE_FLUXO (
   SQ_SIW_TRAMITE_DESTINO ASC,
   SQ_SIW_TRAMITE_ORIGEM ASC
);

alter table CO_AGENCIA
   add constraint FK_COBAN_COAGE foreign key (SQ_BANCO)
      references CO_BANCO (SQ_BANCO);

alter table CO_CIDADE
   add constraint FK_COREG_COCID foreign key (SQ_REGIAO)
      references CO_REGIAO (SQ_REGIAO);

alter table CO_CIDADE
   add constraint FK_COUF_COCID foreign key (CO_UF, SQ_PAIS)
      references CO_UF (CO_UF, SQ_PAIS);

alter table CO_PESSOA
   add constraint FK_COPES_COPES foreign key (SQ_PESSOA_PAI)
      references CO_PESSOA (SQ_PESSOA);

alter table CO_PESSOA
   add constraint FK_COPES_COTIPVIN foreign key (SQ_TIPO_VINCULO)
      references CO_TIPO_VINCULO (SQ_TIPO_VINCULO);

alter table CO_PESSOA
   add constraint FK_COPES_EOREC foreign key (SQ_RECURSO)
      references EO_RECURSO (SQ_RECURSO);

alter table CO_PESSOA
   add constraint FK_COTIPPES_COPES foreign key (SQ_TIPO_PESSOA)
      references CO_TIPO_PESSOA (SQ_TIPO_PESSOA);

alter table CO_PESSOA_CONTA
   add constraint FK_COAGE_COPESCON foreign key (SQ_AGENCIA)
      references CO_AGENCIA (SQ_AGENCIA);

alter table CO_PESSOA_CONTA
   add constraint FK_COPES_COPESCON foreign key (SQ_PESSOA)
      references CO_PESSOA (SQ_PESSOA);

alter table CO_PESSOA_ENDERECO
   add constraint FK_COCID_COPESEND foreign key (SQ_CIDADE)
      references CO_CIDADE (SQ_CIDADE);

alter table CO_PESSOA_ENDERECO
   add constraint FK_COPES_COPESEND foreign key (SQ_PESSOA)
      references CO_PESSOA (SQ_PESSOA);

alter table CO_PESSOA_ENDERECO
   add constraint FK_COTPEND_COPESEN foreign key (SQ_TIPO_ENDERECO)
      references CO_TIPO_ENDERECO (SQ_TIPO_ENDERECO);

alter table CO_PESSOA_FISICA
   add constraint FK_COCID_COPESFIS foreign key (SQ_CIDADE_NASC)
      references CO_CIDADE (SQ_CIDADE);

alter table CO_PESSOA_FISICA
   add constraint FK_COPAI_COPESFIS foreign key (SQ_PAIS_PASSAPORTE)
      references CO_PAIS (SQ_PAIS);

alter table CO_PESSOA_FISICA
   add constraint FK_COPESFIS_SIWCLI foreign key (CLIENTE)
      references SIW_CLIENTE (SQ_PESSOA);

alter table CO_PESSOA_FISICA
   add constraint FK_COPES_COPESFIS foreign key (SQ_PESSOA)
      references CO_PESSOA (SQ_PESSOA);

alter table CO_PESSOA_JURIDICA
   add constraint FK_COPESJUR_SIWCLI foreign key (CLIENTE)
      references SIW_CLIENTE (SQ_PESSOA);

alter table CO_PESSOA_JURIDICA
   add constraint FK_COPES_COPESJUR foreign key (SQ_PESSOA)
      references CO_PESSOA (SQ_PESSOA);

alter table CO_PESSOA_SEGMENTO
   add constraint FK_COPES_COPESSEG foreign key (SQ_PESSOA)
      references CO_PESSOA (SQ_PESSOA);

alter table CO_PESSOA_SEGMENTO
   add constraint FK_COSEG_COPESSEG foreign key (SQ_SEGMENTO)
      references CO_SEGMENTO (SQ_SEGMENTO);

alter table CO_PESSOA_TELEFONE
   add constraint FK_COCID_COPESTEL foreign key (SQ_CIDADE)
      references CO_CIDADE (SQ_CIDADE);

alter table CO_PESSOA_TELEFONE
   add constraint FK_COPES_COPESTEL foreign key (SQ_PESSOA)
      references CO_PESSOA (SQ_PESSOA);

alter table CO_PESSOA_TELEFONE
   add constraint FK_COTPTEL_COPESTL foreign key (SQ_TIPO_TELEFONE)
      references CO_TIPO_TELEFONE (SQ_TIPO_TELEFONE);

alter table CO_REGIAO
   add constraint FK_COPAI_COREG foreign key (SQ_PAIS)
      references CO_PAIS (SQ_PAIS);

alter table CO_TIPO_ENDERECO
   add constraint FK_COTPPES_COTIPEN foreign key (SQ_TIPO_PESSOA)
      references CO_TIPO_PESSOA (SQ_TIPO_PESSOA);

alter table CO_TIPO_TELEFONE
   add constraint FK_COTPPES_COTPTL foreign key (SQ_TIPO_PESSOA)
      references CO_TIPO_PESSOA (SQ_TIPO_PESSOA);

alter table CO_TIPO_VINCULO
   add constraint FK_COTIPVIN_SIWCLI foreign key (CLIENTE)
      references SIW_CLIENTE (SQ_PESSOA);

alter table CO_TIPO_VINCULO
   add constraint FK_COTPPES_COTPVIN foreign key (SQ_TIPO_PESSOA)
      references CO_TIPO_PESSOA (SQ_TIPO_PESSOA);

alter table CO_UF
   add constraint FK_COPAI_COUF foreign key (SQ_PAIS)
      references CO_PAIS (SQ_PAIS);

alter table CO_UF
   add constraint FK_COREG_COUF foreign key (SQ_REGIAO)
      references CO_REGIAO (SQ_REGIAO);

alter table CO_UNIDADE_MEDIDA
   add constraint FK_COUNIMED_COPES foreign key (CLIENTE)
      references CO_PESSOA (SQ_PESSOA);

alter table DC_ARQUIVO
   add constraint FK_DCARQ_DCSIS foreign key (SQ_SISTEMA)
      references DC_SISTEMA (SQ_SISTEMA);

alter table DC_COLUNA
   add constraint FK_DCCOL_DCDADTIP foreign key (SQ_DADO_TIPO)
      references DC_DADO_TIPO (SQ_DADO_TIPO);

alter table DC_COLUNA
   add constraint FK_DCCOL_DCTAB foreign key (SQ_TABELA)
      references DC_TABELA (SQ_TABELA);

alter table DC_ESQUEMA
   add constraint FK_DCESQ_COPES foreign key (CLIENTE)
      references CO_PESSOA (SQ_PESSOA);

alter table DC_ESQUEMA
   add constraint FK_DCESQ_SIWMOD foreign key (SQ_MODULO)
      references SIW_MODULO (SQ_MODULO);

alter table DC_ESQUEMA_ATRIBUTO
   add constraint FK_DCESQATR_DCCOL foreign key (SQ_COLUNA)
      references DC_COLUNA (SQ_COLUNA);

alter table DC_ESQUEMA_ATRIBUTO
   add constraint FK_DCMAP_DCINT foreign key (SQ_ESQUEMA_TABELA)
      references DC_ESQUEMA_TABELA (SQ_ESQUEMA_TABELA);

alter table DC_ESQUEMA_INSERT
   add constraint FK_DCESQINS_DCCOL foreign key (SQ_COLUNA)
      references DC_COLUNA (SQ_COLUNA);

alter table DC_ESQUEMA_INSERT
   add constraint FK_DCESQINS_DCESQTAB foreign key (SQ_ESQUEMA_TABELA)
      references DC_ESQUEMA_TABELA (SQ_ESQUEMA_TABELA);

alter table DC_ESQUEMA_SCRIPT
   add constraint FK_DCESQSCR_DCESQ foreign key (SQ_ESQUEMA)
      references DC_ESQUEMA (SQ_ESQUEMA);

alter table DC_ESQUEMA_SCRIPT
   add constraint FK_DCESQSCR_SIWARQ foreign key (SQ_SIW_ARQUIVO)
      references SIW_ARQUIVO (SQ_SIW_ARQUIVO);

alter table DC_ESQUEMA_TABELA
   add constraint FK_DCESQTAB_DCESQ foreign key (SQ_ESQUEMA)
      references DC_ESQUEMA (SQ_ESQUEMA);

alter table DC_ESQUEMA_TABELA
   add constraint FK_DCINT_DCTAB foreign key (SQ_TABELA)
      references DC_TABELA (SQ_TABELA);

alter table DC_INDICE
   add constraint FK_DCIND_DCINDTIP foreign key (SQ_INDICE_TIPO)
      references DC_INDICE_TIPO (SQ_INDICE_TIPO);

alter table DC_INDICE
   add constraint FK_DCIND_DCSIS foreign key (SQ_SISTEMA)
      references DC_SISTEMA (SQ_SISTEMA);

alter table DC_INDICE
   add constraint FK_DCIND_DCUSU foreign key (SQ_USUARIO)
      references DC_USUARIO (SQ_USUARIO);

alter table DC_INDICE_COLS
   add constraint FK_DCINDCOL_DCCOL foreign key (SQ_COLUNA)
      references DC_COLUNA (SQ_COLUNA);

alter table DC_INDICE_COLS
   add constraint FK_DCINDCOL_DCIND foreign key (SQ_INDICE)
      references DC_INDICE (SQ_INDICE);

alter table DC_OCORRENCIA
   add constraint FK_DCOCO_COPES foreign key (SQ_PESSOA)
      references CO_PESSOA (SQ_PESSOA);

alter table DC_OCORRENCIA
   add constraint FK_DCOCO_DCESQ foreign key (SQ_ESQUEMA)
      references DC_ESQUEMA (SQ_ESQUEMA);

alter table DC_OCORRENCIA
   add constraint FK_DCOCO_SIWARQ_PROCESSADO foreign key (ARQUIVO_PROCESSAMENTO)
      references SIW_ARQUIVO (SQ_SIW_ARQUIVO);

alter table DC_OCORRENCIA
   add constraint FK_DCOCO_SIWARQ_REJEICAO foreign key (ARQUIVO_REJEICAO)
      references SIW_ARQUIVO (SQ_SIW_ARQUIVO);

alter table DC_PROCEDURE
   add constraint FK_DCPROC_DCARQ foreign key (SQ_ARQUIVO)
      references DC_ARQUIVO (SQ_ARQUIVO);

alter table DC_PROCEDURE
   add constraint FK_DCPROC_DCSIS foreign key (SQ_SISTEMA)
      references DC_SISTEMA (SQ_SISTEMA);

alter table DC_PROCEDURE
   add constraint FK_DCPROC_DCSPTIP foreign key (SQ_SP_TIPO)
      references DC_SP_TIPO (SQ_SP_TIPO);

alter table DC_PROC_PARAM
   add constraint FK_DCPROPAR_DCDADTIP foreign key (SQ_DADO_TIPO)
      references DC_DADO_TIPO (SQ_DADO_TIPO);

alter table DC_PROC_PARAM
   add constraint FK_DCPROPAR_DCPRO foreign key (SQ_PROCEDURE)
      references DC_PROCEDURE (SQ_PROCEDURE);

alter table DC_PROC_SP
   add constraint FK_DCPROSP_DCPRO foreign key (SQ_PROCEDURE)
      references DC_PROCEDURE (SQ_PROCEDURE);

alter table DC_PROC_SP
   add constraint FK_DCPROSP_DCSTOPRO foreign key (SQ_STORED_PROC)
      references DC_STORED_PROC (SQ_STORED_PROC);

alter table DC_PROC_TABELA
   add constraint FK_DCPROTAB_DCPRO foreign key (SQ_PROCEDURE)
      references DC_PROCEDURE (SQ_PROCEDURE);

alter table DC_PROC_TABELA
   add constraint FK_DCPROTAB_DCTAB foreign key (SQ_TABELA)
      references DC_TABELA (SQ_TABELA);

alter table DC_RELACIONAMENTO
   add constraint FK_DCREL_DCSIS foreign key (SQ_SISTEMA)
      references DC_SISTEMA (SQ_SISTEMA);

alter table DC_RELACIONAMENTO
   add constraint FK_DCREL_DCTAB_FILHA foreign key (TABELA_FILHA)
      references DC_TABELA (SQ_TABELA);

alter table DC_RELACIONAMENTO
   add constraint FK_DCREL_DCTAB_PAI foreign key (TABELA_PAI)
      references DC_TABELA (SQ_TABELA);

alter table DC_RELAC_COLS
   add constraint FK_DCRELCOL_DCCOL_FILHA foreign key (COLUNA_FILHA)
      references DC_COLUNA (SQ_COLUNA);

alter table DC_RELAC_COLS
   add constraint FK_DCRELCOL_DCCOL_PAI foreign key (COLUNA_PAI)
      references DC_COLUNA (SQ_COLUNA);

alter table DC_RELAC_COLS
   add constraint FK_DCRELCOL_DCREL foreign key (SQ_RELACIONAMENTO)
      references DC_RELACIONAMENTO (SQ_RELACIONAMENTO);

alter table DC_SISTEMA
   add constraint FK_DCSIS_COPES foreign key (CLIENTE)
      references CO_PESSOA (SQ_PESSOA);

alter table DC_SP_PARAM
   add constraint FK_DCSPPAR_DCDADTIP foreign key (SQ_DADO_TIPO)
      references DC_DADO_TIPO (SQ_DADO_TIPO);

alter table DC_SP_PARAM
   add constraint FK_DCSPPAR_DCSTOPRO foreign key (SQ_STORED_PROC)
      references DC_STORED_PROC (SQ_STORED_PROC);

alter table DC_SP_SP
   add constraint FK_DCSPSP_DCSTOPRO_FILHA foreign key (SP_FILHA)
      references DC_STORED_PROC (SQ_STORED_PROC);

alter table DC_SP_SP
   add constraint FK_DCSPSP_DCSTOPRO_PAI foreign key (SP_PAI)
      references DC_STORED_PROC (SQ_STORED_PROC);

alter table DC_SP_TABS
   add constraint FK_DCSPTAB_DCTAB foreign key (SQ_TABELA)
      references DC_TABELA (SQ_TABELA);

alter table DC_SP_TABS
   add constraint FK_SPTAB_DCSTOPRO foreign key (SQ_STORED_PROC)
      references DC_STORED_PROC (SQ_STORED_PROC);

alter table DC_STORED_PROC
   add constraint FK_DCSTOPRO_DCSIS foreign key (SQ_SISTEMA)
      references DC_SISTEMA (SQ_SISTEMA);

alter table DC_STORED_PROC
   add constraint FK_DCSTOPRO_DCUSU foreign key (SQ_USUARIO)
      references DC_USUARIO (SQ_USUARIO);

alter table DC_STORED_PROC
   add constraint FK_STOPRO_SPTIP foreign key (SQ_SP_TIPO)
      references DC_SP_TIPO (SQ_SP_TIPO);

alter table DC_TABELA
   add constraint FK_DCTAB_DCSIS foreign key (SQ_SISTEMA)
      references DC_SISTEMA (SQ_SISTEMA);

alter table DC_TABELA
   add constraint FK_DCTAB_DCTABTIP foreign key (SQ_TABELA_TIPO)
      references DC_TABELA_TIPO (SQ_TABELA_TIPO);

alter table DC_TABELA
   add constraint FK_DCTAB_DCUSU foreign key (SQ_USUARIO)
      references DC_USUARIO (SQ_USUARIO);

alter table DC_TRIGGER
   add constraint FK_DCTRI_DCSIS foreign key (SQ_SISTEMA)
      references DC_SISTEMA (SQ_SISTEMA);

alter table DC_TRIGGER
   add constraint FK_DCTRI_DCTAB foreign key (SQ_TABELA)
      references DC_TABELA (SQ_TABELA);

alter table DC_TRIGGER
   add constraint FK_DCTRI_DCUSU foreign key (SQ_USUARIO)
      references DC_USUARIO (SQ_USUARIO);

alter table DC_TRIGGER_EVENTO
   add constraint FK_DCTRIEVE_DCEVE foreign key (SQ_EVENTO)
      references DC_EVENTO (SQ_EVENTO);

alter table DC_TRIGGER_EVENTO
   add constraint FK_DCTRIEVE_DCTRI foreign key (SQ_TRIGGER)
      references DC_TRIGGER (SQ_TRIGGER);

alter table DC_USUARIO
   add constraint FK_DCUSU_DCSIS foreign key (SQ_SISTEMA)
      references DC_SISTEMA (SQ_SISTEMA);

alter table DM_SEGMENTO_MENU
   add constraint FK_DMSEGMEN_EOUNI foreign key (SQ_UNID_EXECUTORA)
      references EO_UNIDADE (SQ_UNIDADE);

alter table DM_SEGMENTO_MENU
   add constraint FK_DSGMN_DSGMN_PAI foreign key (SQ_SEG_MENU_PAI)
      references DM_SEGMENTO_MENU (SQ_SEGMENTO_MENU);

alter table DM_SEGMENTO_MENU
   add constraint FK_SIWMODSG_DSGMEN foreign key (SQ_MODULO, SQ_SEGMENTO)
      references SIW_MOD_SEG (SQ_MODULO, SQ_SEGMENTO);

alter table DM_SEG_VINCULO
   add constraint FK_COSEG_DMSEGVIN foreign key (SQ_SEGMENTO)
      references CO_SEGMENTO (SQ_SEGMENTO);

alter table DM_SEG_VINCULO
   add constraint FK_COTPES_DSGVIN foreign key (SQ_TIPO_PESSOA)
      references CO_TIPO_PESSOA (SQ_TIPO_PESSOA);

alter table EO_AREA_ATUACAO
   add constraint FK_EOAREATU_COPES foreign key (SQ_PESSOA)
      references CO_PESSOA (SQ_PESSOA);

alter table EO_DATA_ESPECIAL
   add constraint FK_EODATESP_COCID foreign key (SQ_CIDADE)
      references CO_CIDADE (SQ_CIDADE);

alter table EO_DATA_ESPECIAL
   add constraint FK_EODATESP_COPAI foreign key (SQ_PAIS)
      references CO_PAIS (SQ_PAIS);

alter table EO_DATA_ESPECIAL
   add constraint FK_EODATESP_COPES foreign key (CLIENTE)
      references CO_PESSOA (SQ_PESSOA);

alter table EO_DATA_ESPECIAL
   add constraint FK_EODATESP_COUF foreign key (CO_UF, SQ_PAIS)
      references CO_UF (CO_UF, SQ_PAIS);

alter table EO_INDICADOR
   add constraint FK_EOIND_COPES foreign key (CLIENTE)
      references CO_PESSOA (SQ_PESSOA);

alter table EO_INDICADOR
   add constraint FK_EOIND_COUNIMED foreign key (SQ_UNIDADE_MEDIDA)
      references CO_UNIDADE_MEDIDA (SQ_UNIDADE_MEDIDA);

alter table EO_INDICADOR
   add constraint FK_EOIND_EOTIPIND foreign key (SQ_TIPO_INDICADOR)
      references EO_TIPO_INDICADOR (SQ_TIPO_INDICADOR);

alter table EO_INDICADOR_AFERICAO
   add constraint FK_EOINDAFE_COCID foreign key (SQ_CIDADE)
      references CO_CIDADE (SQ_CIDADE);

alter table EO_INDICADOR_AFERICAO
   add constraint FK_EOINDAFE_COPAI foreign key (SQ_PAIS)
      references CO_PAIS (SQ_PAIS);

alter table EO_INDICADOR_AFERICAO
   add constraint FK_EOINDAFE_COPES foreign key (CADASTRADOR)
      references CO_PESSOA (SQ_PESSOA);

alter table EO_INDICADOR_AFERICAO
   add constraint FK_EOINDAFE_COREG foreign key (SQ_REGIAO)
      references CO_REGIAO (SQ_REGIAO);

alter table EO_INDICADOR_AFERICAO
   add constraint FK_EOINDAFE_COUF foreign key (CO_UF, SQ_PAIS)
      references CO_UF (CO_UF, SQ_PAIS);

alter table EO_INDICADOR_AFERICAO
   add constraint FK_EOINDAFE_EOIND foreign key (SQ_EOINDICADOR)
      references EO_INDICADOR (SQ_EOINDICADOR);

alter table EO_INDICADOR_AFERIDOR
   add constraint FK_EOINDAFR_COPES foreign key (SQ_PESSOA)
      references CO_PESSOA (SQ_PESSOA);

alter table EO_INDICADOR_AFERIDOR
   add constraint FK_EOINDAFR_EOIND foreign key (SQ_EOINDICADOR)
      references EO_INDICADOR (SQ_EOINDICADOR);

alter table EO_INDICADOR_AGENDA
   add constraint FK_EOINDAGE_EOIND foreign key (SQ_EOINDICADOR)
      references EO_INDICADOR (SQ_EOINDICADOR);

alter table EO_LOCALIZACAO
   add constraint FK_EOLOC_COPES foreign key (CLIENTE)
      references CO_PESSOA (SQ_PESSOA);

alter table EO_LOCALIZACAO
   add constraint FK_EOLOC_COPESEND foreign key (SQ_PESSOA_ENDERECO)
      references CO_PESSOA_ENDERECO (SQ_PESSOA_ENDERECO);

alter table EO_LOCALIZACAO
   add constraint FK_EOLOC_EOUNI foreign key (SQ_UNIDADE)
      references EO_UNIDADE (SQ_UNIDADE);

alter table EO_RECURSO
   add constraint FK_EOREC_COPES foreign key (CLIENTE)
      references CO_PESSOA (SQ_PESSOA);

alter table EO_RECURSO
   add constraint FK_EOREC_COUNIMED foreign key (SQ_UNIDADE_MEDIDA)
      references CO_UNIDADE_MEDIDA (SQ_UNIDADE_MEDIDA);

alter table EO_RECURSO
   add constraint FK_EOREC_EOTIPREC foreign key (SQ_TIPO_RECURSO)
      references EO_TIPO_RECURSO (SQ_TIPO_RECURSO);

alter table EO_RECURSO
   add constraint FK_EOREC_EOUNI foreign key (UNIDADE_GESTORA)
      references EO_UNIDADE (SQ_UNIDADE);

alter table EO_RECURSO_DISPONIVEL
   add constraint FK_EORECDIS_EOREC foreign key (SQ_RECURSO)
      references EO_RECURSO (SQ_RECURSO);

alter table EO_RECURSO_INDISPONIVEL
   add constraint FK_EORECIND_EOREC foreign key (SQ_RECURSO)
      references EO_RECURSO (SQ_RECURSO);

alter table EO_RECURSO_MENU
   add constraint FK_EORECMEN_EOREC foreign key (SQ_RECURSO)
      references EO_RECURSO (SQ_RECURSO);

alter table EO_RECURSO_MENU
   add constraint FK_EORECMEN_SIWMEN foreign key (SQ_MENU)
      references SIW_MENU (SQ_MENU);

alter table EO_TIPO_INDICADOR
   add constraint FK_EOTIPIND_COPES foreign key (CLIENTE)
      references CO_PESSOA (SQ_PESSOA);

alter table EO_TIPO_RECURSO
   add constraint FK_EOTIPREC_COPES foreign key (CLIENTE)
      references CO_PESSOA (SQ_PESSOA);

alter table EO_TIPO_RECURSO
   add constraint FK_EOTIPREC_EOTIPREC foreign key (SQ_TIPO_PAI)
      references EO_TIPO_RECURSO (SQ_TIPO_RECURSO);

alter table EO_TIPO_RECURSO
   add constraint FK_EOTIPREC_EOUNI foreign key (UNIDADE_GESTORA)
      references EO_UNIDADE (SQ_UNIDADE);

alter table EO_TIPO_UNIDADE
   add constraint FK_EOTIPUNI_COPES foreign key (SQ_PESSOA)
      references CO_PESSOA (SQ_PESSOA);

alter table EO_UNIDADE
   add constraint FK_EOUNI_COPES foreign key (SQ_PESSOA)
      references CO_PESSOA (SQ_PESSOA);

alter table EO_UNIDADE
   add constraint FK_EOUNI_COPESEND foreign key (SQ_PESSOA_ENDERECO)
      references CO_PESSOA_ENDERECO (SQ_PESSOA_ENDERECO);

alter table EO_UNIDADE
   add constraint FK_EOUNI_EOAREATU foreign key (SQ_AREA_ATUACAO)
      references EO_AREA_ATUACAO (SQ_AREA_ATUACAO);

alter table EO_UNIDADE
   add constraint FK_EOUNI_EOTIPUNI foreign key (SQ_TIPO_UNIDADE)
      references EO_TIPO_UNIDADE (SQ_TIPO_UNIDADE);

alter table EO_UNIDADE
   add constraint FK_EOUNI_EOUNI_GES foreign key (SQ_UNIDADE_GESTORA)
      references EO_UNIDADE (SQ_UNIDADE);

alter table EO_UNIDADE
   add constraint FK_EOUNI_EOUNI_PAG foreign key (SQ_UNID_PAGADORA)
      references EO_UNIDADE (SQ_UNIDADE);

alter table EO_UNIDADE
   add constraint FK_EOUNI_EOUNI_PAI foreign key (SQ_UNIDADE_PAI)
      references EO_UNIDADE (SQ_UNIDADE);

alter table EO_UNIDADE_ARQUIVO
   add constraint FK_EOUNIARQ_EOUNI foreign key (SQ_UNIDADE)
      references EO_UNIDADE (SQ_UNIDADE);

alter table EO_UNIDADE_ARQUIVO
   add constraint FK_EOUNIARQ_SIWARQ foreign key (SQ_SIW_ARQUIVO)
      references SIW_ARQUIVO (SQ_SIW_ARQUIVO);

alter table EO_UNIDADE_RESP
   add constraint FK_EOUNIRES_COPES foreign key (SQ_PESSOA)
      references CO_PESSOA (SQ_PESSOA);

alter table EO_UNIDADE_RESP
   add constraint FK_EOUNIRES_EOUNI foreign key (SQ_UNIDADE)
      references EO_UNIDADE (SQ_UNIDADE);

alter table GD_DEMANDA
   add constraint FK_GDDEM_COPES foreign key (RESPONSAVEL)
      references CO_PESSOA (SQ_PESSOA);

alter table GD_DEMANDA
   add constraint FK_GDDEM_EOUNI foreign key (SQ_UNIDADE_RESP)
      references EO_UNIDADE (SQ_UNIDADE);

alter table GD_DEMANDA
   add constraint FK_GDDEM_GDDEM foreign key (SQ_DEMANDA_PAI)
      references GD_DEMANDA (SQ_SIW_SOLICITACAO);

alter table GD_DEMANDA
   add constraint FK_GDDEM_GDDEMTIPO foreign key (SQ_DEMANDA_TIPO)
      references GD_DEMANDA_TIPO (SQ_DEMANDA_TIPO);

alter table GD_DEMANDA
   add constraint FK_GDDEM_SIWRES foreign key (SQ_SIW_RESTRICAO)
      references SIW_RESTRICAO (SQ_SIW_RESTRICAO);

alter table GD_DEMANDA
   add constraint FK_GDDEM_SIWSOL foreign key (SQ_SIW_SOLICITACAO)
      references SIW_SOLICITACAO (SQ_SIW_SOLICITACAO);

alter table GD_DEMANDA_ENVOLV
   add constraint FK_GDDEMENV_EOUNI foreign key (SQ_UNIDADE)
      references EO_UNIDADE (SQ_UNIDADE);

alter table GD_DEMANDA_ENVOLV
   add constraint FK_GDDEMENV_GDDEM foreign key (SQ_SIW_SOLICITACAO)
      references GD_DEMANDA (SQ_SIW_SOLICITACAO);

alter table GD_DEMANDA_INTERES
   add constraint FK_GDDEMINT_COPES foreign key (SQ_PESSOA)
      references CO_PESSOA (SQ_PESSOA);

alter table GD_DEMANDA_INTERES
   add constraint FK_GDDEMINT_GDDEM foreign key (SQ_SIW_SOLICITACAO)
      references GD_DEMANDA (SQ_SIW_SOLICITACAO);

alter table GD_DEMANDA_LOG
   add constraint FK_GDDEMLOG_GDDEM foreign key (SQ_SIW_SOLICITACAO)
      references GD_DEMANDA (SQ_SIW_SOLICITACAO);

alter table GD_DEMANDA_LOG
   add constraint FK_GDDMLG_COPS_CAD foreign key (CADASTRADOR)
      references CO_PESSOA (SQ_PESSOA);

alter table GD_DEMANDA_LOG
   add constraint FK_GDDMLG_COPS_DEM foreign key (DESTINATARIO)
      references CO_PESSOA (SQ_PESSOA);

alter table GD_DEMANDA_LOG
   add constraint FK_GDLOG_SIWLOG foreign key (SQ_SIW_SOLIC_LOG)
      references SIW_SOLIC_LOG (SQ_SIW_SOLIC_LOG);

alter table GD_DEMANDA_LOG_ARQ
   add constraint FK_GDDEMLOGARQ_GDDEMLOG foreign key (SQ_DEMANDA_LOG)
      references GD_DEMANDA_LOG (SQ_DEMANDA_LOG);

alter table GD_DEMANDA_LOG_ARQ
   add constraint FK_GDDEMLOGARQ_SIWARQ foreign key (SQ_SIW_ARQUIVO)
      references SIW_ARQUIVO (SQ_SIW_ARQUIVO);

alter table GD_DEMANDA_TIPO
   add constraint FK_GDDEMTIP_COPES foreign key (CLIENTE)
      references CO_PESSOA (SQ_PESSOA);

alter table GD_DEMANDA_TIPO
   add constraint FK_GDDEMTIP_EOUNI foreign key (SQ_UNIDADE)
      references EO_UNIDADE (SQ_UNIDADE);

alter table PE_HORIZONTE
   add constraint FK_PEHOR_COPES foreign key (CLIENTE)
      references CO_PESSOA (SQ_PESSOA);

alter table PE_NATUREZA
   add constraint FK_PENAT_COPES foreign key (CLIENTE)
      references CO_PESSOA (SQ_PESSOA);

alter table PE_OBJETIVO
   add constraint FK_PEOBJ_COPES foreign key (CLIENTE)
      references CO_PESSOA (SQ_PESSOA);

alter table PE_OBJETIVO
   add constraint FK_PEOBJ_PEPLA foreign key (SQ_PLANO)
      references PE_PLANO (SQ_PLANO);

alter table PE_PLANO
   add constraint FK_PEPLA_COPES foreign key (CLIENTE)
      references CO_PESSOA (SQ_PESSOA);

alter table PE_PLANO
   add constraint FK_PEPLA_PEPLA foreign key (SQ_PLANO_PAI)
      references PE_PLANO (SQ_PLANO);

alter table PE_PLANO_ARQ
   add constraint FK_PEPLAARQ_PEPLA foreign key (SQ_PLANO)
      references PE_PLANO (SQ_PLANO);

alter table PE_PLANO_ARQ
   add constraint FK_PEPLAARQ_SIWARQ foreign key (SQ_SIW_ARQUIVO)
      references SIW_ARQUIVO (SQ_SIW_ARQUIVO);

alter table PE_PLANO_INDICADOR
   add constraint FK_PEPLANIND_EOIND foreign key (SQ_EOINDICADOR)
      references EO_INDICADOR (SQ_EOINDICADOR);

alter table PE_PLANO_INDICADOR
   add constraint FK_PEPLANIND_PEPLAN foreign key (SQ_PLANO)
      references PE_PLANO (SQ_PLANO);

alter table PE_PLANO_MENU
   add constraint FK_PEPLANMEN_PEPLA foreign key (SQ_PLANO)
      references PE_PLANO (SQ_PLANO);

alter table PE_PLANO_MENU
   add constraint FK_PEPLANMEN_SIWMEN foreign key (SQ_MENU)
      references SIW_MENU (SQ_MENU);

alter table PE_PROGRAMA
   add constraint FK_PEPRO_COPES foreign key (CLIENTE)
      references CO_PESSOA (SQ_PESSOA);

alter table PE_PROGRAMA
   add constraint FK_PEPRO_EOUNI foreign key (SQ_UNIDADE_RESP)
      references EO_UNIDADE (SQ_UNIDADE);

alter table PE_PROGRAMA
   add constraint FK_PEPRO_PEHOR foreign key (SQ_PEHORIZONTE)
      references PE_HORIZONTE (SQ_PEHORIZONTE);

alter table PE_PROGRAMA
   add constraint FK_PEPRO_PENAT foreign key (SQ_PENATUREZA)
      references PE_NATUREZA (SQ_PENATUREZA);

alter table PE_PROGRAMA
   add constraint FK_PEPRO_SIWSOL foreign key (SQ_SIW_SOLICITACAO)
      references SIW_SOLICITACAO (SQ_SIW_SOLICITACAO);

alter table PE_PROGRAMA_LOG
   add constraint FK_PEPROLOG_COPES_CAD foreign key (CADASTRADOR)
      references CO_PESSOA (SQ_PESSOA);

alter table PE_PROGRAMA_LOG
   add constraint FK_PEPROLOG_DEST foreign key (DESTINATARIO)
      references CO_PESSOA (SQ_PESSOA);

alter table PE_PROGRAMA_LOG
   add constraint FK_PEPROLOG_PEPRO foreign key (SQ_SIW_SOLICITACAO)
      references PE_PROGRAMA (SQ_SIW_SOLICITACAO);

alter table PE_PROGRAMA_LOG
   add constraint FK_PEPROLOG_SISOLLOG foreign key (SQ_SIW_SOLIC_LOG)
      references SIW_SOLIC_LOG (SQ_SIW_SOLIC_LOG);

alter table PE_PROGRAMA_LOG_ARQ
   add constraint FK_PEPROLOGARQ_PEPROLOG foreign key (SQ_PROGRAMA_LOG)
      references PE_PROGRAMA_LOG (SQ_PROGRAMA_LOG);

alter table PE_PROGRAMA_LOG_ARQ
   add constraint FK_PEPROLOGARQ_SIWARQ foreign key (SQ_SIW_ARQUIVO)
      references SIW_ARQUIVO (SQ_SIW_ARQUIVO);

alter table PE_UNIDADE
   add constraint FK_PEUNI_COPES foreign key (CLIENTE)
      references CO_PESSOA (SQ_PESSOA);

alter table PE_UNIDADE
   add constraint FK_PEUNI_EOUNI foreign key (SQ_UNIDADE)
      references EO_UNIDADE (SQ_UNIDADE);

alter table PJ_COMENTARIO_ARQ
   add constraint FK_PJCOMARQ_PJETACOM foreign key (SQ_ETAPA_COMENTARIO)
      references PJ_ETAPA_COMENTARIO (SQ_ETAPA_COMENTARIO);

alter table PJ_COMENTARIO_ARQ
   add constraint FK_PJCOMARQ_SIWARQ foreign key (SQ_SIW_ARQUIVO)
      references SIW_ARQUIVO (SQ_SIW_ARQUIVO);

alter table PJ_ETAPA_COMENTARIO
   add constraint FK_PJETACOM_COPES foreign key (SQ_PESSOA_INCLUSAO)
      references CO_PESSOA (SQ_PESSOA);

alter table PJ_ETAPA_COMENTARIO
   add constraint FK_PJETACOM_PJPROETA foreign key (SQ_PROJETO_ETAPA)
      references PJ_PROJETO_ETAPA (SQ_PROJETO_ETAPA);

alter table PJ_ETAPA_CONTRATO
   add constraint FK_ETACON_SIWSOL foreign key (SQ_SIW_SOLICITACAO)
      references SIW_SOLICITACAO (SQ_SIW_SOLICITACAO);

alter table PJ_ETAPA_CONTRATO
   add constraint FK_PJETACON_PJPROETA foreign key (SQ_PROJETO_ETAPA)
      references PJ_PROJETO_ETAPA (SQ_PROJETO_ETAPA);

alter table PJ_ETAPA_DEMANDA
   add constraint FK_PJETADEM_PJPROETA foreign key (SQ_PROJETO_ETAPA)
      references PJ_PROJETO_ETAPA (SQ_PROJETO_ETAPA);

alter table PJ_ETAPA_DEMANDA
   add constraint FK_PJETADEM_SIWSOL foreign key (SQ_SIW_SOLICITACAO)
      references SIW_SOLICITACAO (SQ_SIW_SOLICITACAO);

alter table PJ_ETAPA_MENSAL
   add constraint FK_PJETAMEN_PJPROETA foreign key (SQ_PROJETO_ETAPA)
      references PJ_PROJETO_ETAPA (SQ_PROJETO_ETAPA);

alter table PJ_PROJETO
   add constraint FK_PJPRO_COCID foreign key (SQ_CIDADE)
      references CO_CIDADE (SQ_CIDADE);

alter table PJ_PROJETO
   add constraint FK_PJPRO_COPES_OUTRA foreign key (OUTRA_PARTE)
      references CO_PESSOA (SQ_PESSOA);

alter table PJ_PROJETO
   add constraint FK_PJPRO_COPES_PREP foreign key (PREPOSTO)
      references CO_PESSOA (SQ_PESSOA);

alter table PJ_PROJETO
   add constraint FK_PJPRO_COTIPPES foreign key (SQ_TIPO_PESSOA)
      references CO_TIPO_PESSOA (SQ_TIPO_PESSOA);

alter table PJ_PROJETO
   add constraint FK_PJPRO_EOUNI foreign key (SQ_UNIDADE_RESP)
      references EO_UNIDADE (SQ_UNIDADE);

alter table PJ_PROJETO
   add constraint FK_PJPRO_SIWSOL foreign key (SQ_SIW_SOLICITACAO)
      references SIW_SOLICITACAO (SQ_SIW_SOLICITACAO);

alter table PJ_PROJETO_ENVOLV
   add constraint FK_PJPROENV_EOUNI foreign key (SQ_UNIDADE)
      references EO_UNIDADE (SQ_UNIDADE);

alter table PJ_PROJETO_ENVOLV
   add constraint FK_PJPROENV_PJPRO foreign key (SQ_SIW_SOLICITACAO)
      references PJ_PROJETO (SQ_SIW_SOLICITACAO);

alter table PJ_PROJETO_ETAPA
   add constraint FK_PJPROETA_COCID foreign key (SQ_CIDADE)
      references CO_CIDADE (SQ_CIDADE);

alter table PJ_PROJETO_ETAPA
   add constraint FK_PJPROETA_COPAI foreign key (SQ_PAIS)
      references CO_PAIS (SQ_PAIS);

alter table PJ_PROJETO_ETAPA
   add constraint FK_PJPROETA_COPES foreign key (SQ_PESSOA)
      references CO_PESSOA (SQ_PESSOA);

alter table PJ_PROJETO_ETAPA
   add constraint FK_PJPROETA_COPES_ATUAL foreign key (SQ_PESSOA_ATUALIZACAO)
      references CO_PESSOA (SQ_PESSOA);

alter table PJ_PROJETO_ETAPA
   add constraint FK_PJPROETA_COREG foreign key (SQ_REGIAO)
      references CO_REGIAO (SQ_REGIAO);

alter table PJ_PROJETO_ETAPA
   add constraint FK_PJPROETA_COUF foreign key (CO_UF, SQ_PAIS)
      references CO_UF (CO_UF, SQ_PAIS);

alter table PJ_PROJETO_ETAPA
   add constraint FK_PJPROETA_EOUNI foreign key (SQ_UNIDADE)
      references EO_UNIDADE (SQ_UNIDADE);

alter table PJ_PROJETO_ETAPA
   add constraint FK_PJPROETA_PJPRO foreign key (SQ_SIW_SOLICITACAO)
      references PJ_PROJETO (SQ_SIW_SOLICITACAO);

alter table PJ_PROJETO_ETAPA
   add constraint FK_PJPROETA_PJPROETA foreign key (SQ_ETAPA_PAI)
      references PJ_PROJETO_ETAPA (SQ_PROJETO_ETAPA);

alter table PJ_PROJETO_ETAPA_ARQ
   add constraint FK_PJPROETAAPQ_PJPROETA foreign key (SQ_PROJETO_ETAPA)
      references PJ_PROJETO_ETAPA (SQ_PROJETO_ETAPA);

alter table PJ_PROJETO_ETAPA_ARQ
   add constraint FK_PJPROETAARQ_SIWARQ foreign key (SQ_SIW_ARQUIVO)
      references SIW_ARQUIVO (SQ_SIW_ARQUIVO);

alter table PJ_PROJETO_INTERES
   add constraint FK_PJPROINT_COPES foreign key (SQ_PESSOA)
      references CO_PESSOA (SQ_PESSOA);

alter table PJ_PROJETO_INTERES
   add constraint FK_PJPROINT_PJPRO foreign key (SQ_SIW_SOLICITACAO)
      references PJ_PROJETO (SQ_SIW_SOLICITACAO);

alter table PJ_PROJETO_LOG
   add constraint FK_PJLOG_SIWLOG foreign key (SQ_SIW_SOLIC_LOG)
      references SIW_SOLIC_LOG (SQ_SIW_SOLIC_LOG);

alter table PJ_PROJETO_LOG
   add constraint FK_PJPRJLG_CPS_C foreign key (CADASTRADOR)
      references CO_PESSOA (SQ_PESSOA);

alter table PJ_PROJETO_LOG
   add constraint FK_PJPRJLG_CPS_D foreign key (DESTINATARIO)
      references CO_PESSOA (SQ_PESSOA);

alter table PJ_PROJETO_LOG
   add constraint FK_PJPRJLOG_PJPRJ foreign key (SQ_SIW_SOLICITACAO)
      references PJ_PROJETO (SQ_SIW_SOLICITACAO);

alter table PJ_PROJETO_LOG_ARQ
   add constraint FK_PJPROLOGARQ_PJPROLOG foreign key (SQ_PROJETO_LOG)
      references PJ_PROJETO_LOG (SQ_PROJETO_LOG);

alter table PJ_PROJETO_LOG_ARQ
   add constraint FK_PJPROLOGARQ_SIWARQ foreign key (SQ_SIW_ARQUIVO)
      references SIW_ARQUIVO (SQ_SIW_ARQUIVO);

alter table PJ_PROJETO_RECURSO
   add constraint FK_PJPROREC_PJPRO foreign key (SQ_SIW_SOLICITACAO)
      references PJ_PROJETO (SQ_SIW_SOLICITACAO);

alter table PJ_PROJETO_REPRESENTANTE
   add constraint FK_PJPROREP_PJPRO foreign key (SQ_SIW_SOLICITACAO)
      references PJ_PROJETO (SQ_SIW_SOLICITACAO);

alter table PJ_PROJETO_REPRESENTANTE
   add constraint FK_PJPROREP_SGAUT foreign key (SQ_PESSOA)
      references SG_AUTENTICACAO (SQ_PESSOA);

alter table PJ_RECURSO_ETAPA
   add constraint FK_PJRECETA_PJPROETA foreign key (SQ_PROJETO_ETAPA)
      references PJ_PROJETO_ETAPA (SQ_PROJETO_ETAPA);

alter table PJ_RECURSO_ETAPA
   add constraint FK_PJRECETA_PJPROREC foreign key (SQ_PROJETO_RECURSO)
      references PJ_PROJETO_RECURSO (SQ_PROJETO_RECURSO);

alter table PJ_RUBRICA
   add constraint FK_PJRUB_PJPRO foreign key (SQ_SIW_SOLICITACAO)
      references PJ_PROJETO (SQ_SIW_SOLICITACAO);

alter table PJ_RUBRICA_CRONOGRAMA
   add constraint FK_PJRUBCRO_PJRUB foreign key (SQ_PROJETO_RUBRICA)
      references PJ_RUBRICA (SQ_PROJETO_RUBRICA);

alter table PT_CAMPO
   add constraint FK_PTCAM_COPES_CLIENTE foreign key (CLIENTE)
      references CO_PESSOA (SQ_PESSOA);

alter table PT_CONTEUDO
   add constraint FK_PTCON_COPES_CLIENTE foreign key (CLIENTE)
      references CO_PESSOA (SQ_PESSOA);

alter table PT_CONTEUDO
   add constraint FK_PTCON_SGAUT foreign key (SQ_USUARIO)
      references SG_AUTENTICACAO (SQ_PESSOA);

alter table PT_EIXO
   add constraint FK_PTEIX_COPES_CLIENTE foreign key (CLIENTE)
      references CO_PESSOA (SQ_PESSOA);

alter table PT_EXIBICAO_CONTEUDO
   add constraint FK_PEEXICON_COPES_CLIENTE foreign key (CLIENTE)
      references CO_PESSOA (SQ_PESSOA);

alter table PT_EXIBICAO_CONTEUDO
   add constraint FK_PTEXICON_PTCON foreign key (SQ_CONTEUDO)
      references PT_CONTEUDO (SQ_CONTEUDO);

alter table PT_EXIBICAO_CONTEUDO
   add constraint FK_PTEXICON_PTMEN foreign key (SQ_MENU)
      references PT_MENU (SQ_MENU);

alter table PT_FILTRO
   add constraint FK_PTFIL_COPES_CLIENTE foreign key (CLIENTE)
      references CO_PESSOA (SQ_PESSOA);

alter table PT_FILTRO
   add constraint FK_PTFIL_PTCAM foreign key (SQ_CAMPO)
      references PT_CAMPO (SQ_CAMPO);

alter table PT_FILTRO
   add constraint FK_PTFIL_PTOPE foreign key (SQ_OPERADOR)
      references PT_OPERADOR (SQ_OPERADOR);

alter table PT_FILTRO
   add constraint FK_PTFIL_PTPES foreign key (SQ_PESQUISA)
      references PT_PESQUISA (SQ_PESQUISA);

alter table PT_MENU
   add constraint FK_PTMEN_COPES_CLIENTE foreign key (CLIENTE)
      references CO_PESSOA (SQ_PESSOA);

alter table PT_MENU
   add constraint FK_PTMEN_PTMEN foreign key (MENU_SQ_MENU)
      references PT_MENU (SQ_MENU);

alter table PT_OPERADOR
   add constraint FK_PTOPE_COPES_CLIENTE foreign key (CLIENTE)
      references CO_PESSOA (SQ_PESSOA);

alter table PT_PESQUISA
   add constraint FK_PEPES_COPES_CLIENTE foreign key (CLIENTE)
      references CO_PESSOA (SQ_PESSOA);

alter table PT_PESQUISA
   add constraint FK_PTPES_COPES foreign key (SQ_PESSOA)
      references CO_PESSOA (SQ_PESSOA);

alter table SG_AUTENTICACAO
   add constraint FK_SGAUT_COPES foreign key (SQ_PESSOA)
      references CO_PESSOA (SQ_PESSOA);

alter table SG_AUTENTICACAO
   add constraint FK_SGAUT_EOLOC foreign key (SQ_LOCALIZACAO)
      references EO_LOCALIZACAO (SQ_LOCALIZACAO);

alter table SG_AUTENTICACAO
   add constraint FK_SGAUT_EOUNI foreign key (SQ_UNIDADE)
      references EO_UNIDADE (SQ_UNIDADE);

alter table SG_AUTENTICACAO
   add constraint FK_SGAUT_SIWCLI foreign key (CLIENTE)
      references SIW_CLIENTE (SQ_PESSOA);

alter table SG_AUTENTICACAO_TEMP
   add constraint FK_SGAUTTEM_COPES foreign key (CLIENTE)
      references CO_PESSOA (SQ_PESSOA);

alter table SG_PERFIL_MENU
   add constraint FK_SGPERMN_COTPVIN foreign key (SQ_TIPO_VINCULO)
      references CO_TIPO_VINCULO (SQ_TIPO_VINCULO);

alter table SG_PERFIL_MENU
   add constraint FK_SGPERMN_SIWMNEN foreign key (SQ_MENU, SQ_PESSOA_ENDERECO)
      references SIW_MENU_ENDERECO (SQ_MENU, SQ_PESSOA_ENDERECO);

alter table SG_PESSOA_MAIL
   add constraint FK_SGPESMAI_SGAUT foreign key (SQ_PESSOA)
      references SG_AUTENTICACAO (SQ_PESSOA);

alter table SG_PESSOA_MAIL
   add constraint FK_SGPESMAI_SIWMEN foreign key (SQ_MENU)
      references SIW_MENU (SQ_MENU);

alter table SG_PESSOA_MENU
   add constraint FK_SGPESMEN_SGAUT foreign key (SQ_PESSOA)
      references SG_AUTENTICACAO (SQ_PESSOA);

alter table SG_PESSOA_MENU
   add constraint FK_SGPESMN_SIWMNEN foreign key (SQ_MENU, SQ_PESSOA_ENDERECO)
      references SIW_MENU_ENDERECO (SQ_MENU, SQ_PESSOA_ENDERECO);

alter table SG_PESSOA_MODULO
   add constraint FK_SGPESMD_COPESEN foreign key (SQ_PESSOA_ENDERECO)
      references CO_PESSOA_ENDERECO (SQ_PESSOA_ENDERECO);

alter table SG_PESSOA_MODULO
   add constraint FK_SGPESMD_SIWCLMD foreign key (CLIENTE, SQ_MODULO)
      references SIW_CLIENTE_MODULO (SQ_PESSOA, SQ_MODULO);

alter table SG_PESSOA_MODULO
   add constraint FK_SGPESMOD_COPES foreign key (SQ_PESSOA)
      references CO_PESSOA (SQ_PESSOA);

alter table SG_PESSOA_UNIDADE
   add constraint FK_SGPESUNI_EOUNI foreign key (SQ_UNIDADE)
      references EO_UNIDADE (SQ_UNIDADE);

alter table SG_PESSOA_UNIDADE
   add constraint FK_SGPESUNI_SGAUT foreign key (SQ_PESSOA)
      references SG_AUTENTICACAO (SQ_PESSOA);

alter table SG_TRAMITE_PESSOA
   add constraint FK_SGTRAPES_SGAUT foreign key (SQ_PESSOA)
      references SG_AUTENTICACAO (SQ_PESSOA);

alter table SG_TRAMITE_PESSOA
   add constraint FK_SGTRAPES_SIWTRA foreign key (SQ_SIW_TRAMITE)
      references SIW_TRAMITE (SQ_SIW_TRAMITE);

alter table SG_TRAMITE_PESSOA
   add constraint FK_SGTRAPS_COPSND foreign key (SQ_PESSOA_ENDERECO)
      references CO_PESSOA_ENDERECO (SQ_PESSOA_ENDERECO);

alter table SIW_ARQUIVO
   add constraint FK_SIWARQ_COPES foreign key (CLIENTE)
      references CO_PESSOA (SQ_PESSOA);

alter table SIW_ARQUIVO
   add constraint FK_SIWARQ_SIWTIPARQ foreign key (SQ_TIPO_ARQUIVO)
      references SIW_TIPO_ARQUIVO (SQ_TIPO_ARQUIVO);

alter table SIW_CLIENTE
   add constraint FK_COAGE_SIWCLI foreign key (SQ_AGENCIA_PADRAO)
      references CO_AGENCIA (SQ_AGENCIA);

alter table SIW_CLIENTE
   add constraint FK_COCID_SIWCLI foreign key (SQ_CIDADE_PADRAO)
      references CO_CIDADE (SQ_CIDADE);

alter table SIW_CLIENTE
   add constraint FK_COPES_SIWCLI foreign key (SQ_PESSOA)
      references CO_PESSOA (SQ_PESSOA);

alter table SIW_CLIENTE_MODULO
   add constraint FK_SIWCLI_SIWCLIMD foreign key (SQ_PESSOA)
      references SIW_CLIENTE (SQ_PESSOA);

alter table SIW_CLIENTE_MODULO
   add constraint FK_SIWMD_SIWCLIMD foreign key (SQ_MODULO)
      references SIW_MODULO (SQ_MODULO);

alter table SIW_COORDENADA
   add constraint FK_SIWCOO_COPES foreign key (CLIENTE)
      references CO_PESSOA (SQ_PESSOA);

alter table SIW_COORDENADA_ENDERECO
   add constraint FK_SIWCOOEND_SIWCOO foreign key (SQ_SIW_COORDENADA)
      references SIW_COORDENADA (SQ_SIW_COORDENADA);

alter table SIW_COORDENADA_ENDERECO
   add constraint FK_SIWCOOEND_SIWPESEND foreign key (SQ_PESSOA_ENDERECO)
      references CO_PESSOA_ENDERECO (SQ_PESSOA_ENDERECO);

alter table SIW_COORDENADA_SOLICITACAO
   add constraint FK_SIWCOOSOL_SIWCOO foreign key (SQ_SIW_COORDENADA)
      references SIW_COORDENADA (SQ_SIW_COORDENADA);

alter table SIW_COORDENADA_SOLICITACAO
   add constraint FK_SIWCOOSOL_SIWSOL foreign key (SQ_SIW_SOLICITACAO)
      references SIW_SOLICITACAO (SQ_SIW_SOLICITACAO);

alter table SIW_ETAPA_INTERESSADO
   add constraint FK_SIWETAINT_EOUNI foreign key (SQ_UNIDADE)
      references EO_UNIDADE (SQ_UNIDADE);

alter table SIW_ETAPA_INTERESSADO
   add constraint FK_SIWETAINT_PJPROETA foreign key (SQ_PROJETO_ETAPA)
      references PJ_PROJETO_ETAPA (SQ_PROJETO_ETAPA);

alter table SIW_MAIL
   add constraint FK_SIWMAI_COPES_CLIENTE foreign key (CLIENTE)
      references CO_PESSOA (SQ_PESSOA);

alter table SIW_MAIL
   add constraint FK_SIWMAI_COPES_REMETE foreign key (REMETENTE)
      references CO_PESSOA (SQ_PESSOA);

alter table SIW_MAIL_ANEXO
   add constraint FK_SIWARQ_SIWMAIANE foreign key (SQ_SIW_ARQUIVO)
      references SIW_ARQUIVO (SQ_SIW_ARQUIVO);

alter table SIW_MAIL_ANEXO
   add constraint FK_SIWMAI_SIWMAIANE foreign key (SQ_MAIL)
      references SIW_MAIL (SQ_MAIL);

alter table SIW_MAIL_DESTINATARIO
   add constraint FK_SIWMAIDES_COPES foreign key (DESTINATARIO_PESSOA)
      references CO_PESSOA (SQ_PESSOA);

alter table SIW_MAIL_DESTINATARIO
   add constraint FK_SIWMAIDES_EOUNI foreign key (DESTINATARIO_UNIDADE)
      references EO_UNIDADE (SQ_UNIDADE);

alter table SIW_MAIL_DESTINATARIO
   add constraint FK_SIWMAIDES_SIWMAI foreign key (SQ_MAIL)
      references SIW_MAIL (SQ_MAIL);

alter table SIW_MENU
   add constraint FK_SIWCLIMD_SIWMN foreign key (SQ_MODULO, SQ_PESSOA)
      references SIW_CLIENTE_MODULO (SQ_MODULO, SQ_PESSOA);

alter table SIW_MENU
   add constraint FK_SIWMEN_EOUNI foreign key (SQ_UNID_EXECUTORA)
      references EO_UNIDADE (SQ_UNIDADE);

alter table SIW_MENU
   add constraint FK_SIWMEN_SIWARQ foreign key (SQ_ARQUIVO_PROCED)
      references SIW_ARQUIVO (SQ_SIW_ARQUIVO);

alter table SIW_MENU
   add constraint FK_SIWMEN_SIWMEN_NUMERADOR foreign key (SERVICO_NUMERADOR)
      references SIW_MENU (SQ_MENU);

alter table SIW_MENU
   add constraint FK_SIWMN_SIWMN_PAI foreign key (SQ_MENU_PAI)
      references SIW_MENU (SQ_MENU);

alter table SIW_MENU_ARQUIVO
   add constraint FK_SIWMENARQ_SIWARQ foreign key (SQ_SIW_ARQUIVO)
      references SIW_ARQUIVO (SQ_SIW_ARQUIVO);

alter table SIW_MENU_ARQUIVO
   add constraint FK_SIWMENARQ_SIWMEN foreign key (SQ_MENU)
      references SIW_MENU (SQ_MENU);

alter table SIW_MENU_ENDERECO
   add constraint FK_SIWMNEN_COPESEN foreign key (SQ_PESSOA_ENDERECO)
      references CO_PESSOA_ENDERECO (SQ_PESSOA_ENDERECO);

alter table SIW_MENU_ENDERECO
   add constraint FK_SIWMNEN_SIWMN foreign key (SQ_MENU)
      references SIW_MENU (SQ_MENU);

alter table SIW_MENU_RELAC
   add constraint FK_SIWMENREL_SIWMEN_CLI foreign key (SERVICO_CLIENTE)
      references SIW_MENU (SQ_MENU);

alter table SIW_MENU_RELAC
   add constraint FK_SIWMENREL_SIWMEN_FORN foreign key (SERVICO_FORNECEDOR)
      references SIW_MENU (SQ_MENU);

alter table SIW_MENU_RELAC
   add constraint FK_SIWMENREL_SIWTRA foreign key (SQ_SIW_TRAMITE)
      references SIW_TRAMITE (SQ_SIW_TRAMITE);

alter table SIW_META_ARQUIVO
   add constraint FK_SIWMETARQ_SIWARQ foreign key (SQ_SIW_ARQUIVO)
      references SIW_ARQUIVO (SQ_SIW_ARQUIVO);

alter table SIW_META_ARQUIVO
   add constraint FK_SIWMETARQ_SIWSOLMET foreign key (SQ_SOLIC_META)
      references SIW_SOLIC_META (SQ_SOLIC_META);

alter table SIW_META_CRONOGRAMA
   add constraint FK_SIWMETCRO_COPES foreign key (SQ_PESSOA_ATUALIZACAO)
      references CO_PESSOA (SQ_PESSOA);

alter table SIW_META_CRONOGRAMA
   add constraint FK_SIWMETCRO_SIWSOLMET foreign key (SQ_SOLIC_META)
      references SIW_SOLIC_META (SQ_SOLIC_META);

alter table SIW_MOD_SEG
   add constraint FK_COSEG_SIWMODSEG foreign key (SQ_SEGMENTO)
      references CO_SEGMENTO (SQ_SEGMENTO);

alter table SIW_MOD_SEG
   add constraint FK_SIWMD_SIWMDSEG foreign key (SQ_MODULO)
      references SIW_MODULO (SQ_MODULO);

alter table SIW_RESTRICAO
   add constraint FK_SIWRES_SIWTIPRES foreign key (SQ_TIPO_RESTRICAO)
      references SIW_TIPO_RESTRICAO (SQ_TIPO_RESTRICAO);

alter table SIW_RESTRICAO
   add constraint FK_SIWSOLRES_COPES foreign key (SQ_PESSOA)
      references CO_PESSOA (SQ_PESSOA);

alter table SIW_RESTRICAO
   add constraint FK_SIWSOLRES_COPES_ATUAL foreign key (SQ_PESSOA_ATUALIZACAO)
      references CO_PESSOA (SQ_PESSOA);

alter table SIW_RESTRICAO
   add constraint FK_SIWSOLRES_SIWSOL foreign key (SQ_SIW_SOLICITACAO)
      references SIW_SOLICITACAO (SQ_SIW_SOLICITACAO);

alter table SIW_RESTRICAO_ETAPA
   add constraint FK_SIWRESETA_PJPROETA foreign key (SQ_PROJETO_ETAPA)
      references PJ_PROJETO_ETAPA (SQ_PROJETO_ETAPA);

alter table SIW_RESTRICAO_ETAPA
   add constraint FK_SIWRESETA_SIWRES foreign key (SQ_SIW_RESTRICAO)
      references SIW_RESTRICAO (SQ_SIW_RESTRICAO);

alter table SIW_SOLICITACAO
   add constraint FK_SIWSOL_COCID foreign key (SQ_CIDADE_ORIGEM)
      references CO_CIDADE (SQ_CIDADE);

alter table SIW_SOLICITACAO
   add constraint FK_SIWSOL_COPES_RECEB foreign key (RECEBEDOR)
      references CO_PESSOA (SQ_PESSOA);

alter table SIW_SOLICITACAO
   add constraint FK_SIWSOL_COPS_CAD foreign key (CADASTRADOR)
      references CO_PESSOA (SQ_PESSOA);

alter table SIW_SOLICITACAO
   add constraint FK_SIWSOL_COPS_EXE foreign key (EXECUTOR)
      references CO_PESSOA (SQ_PESSOA);

alter table SIW_SOLICITACAO
   add constraint FK_SIWSOL_COPS_SOL foreign key (SOLICITANTE)
      references CO_PESSOA (SQ_PESSOA);

alter table SIW_SOLICITACAO
   add constraint FK_SIWSOL_EOUNI foreign key (SQ_UNIDADE)
      references EO_UNIDADE (SQ_UNIDADE);

alter table SIW_SOLICITACAO
   add constraint FK_SIWSOL_PEPLA foreign key (SQ_PLANO)
      references PE_PLANO (SQ_PLANO);

alter table SIW_SOLICITACAO
   add constraint FK_SIWSOL_SIWMEN foreign key (SQ_MENU)
      references SIW_MENU (SQ_MENU);

alter table SIW_SOLICITACAO
   add constraint FK_SIWSOL_SIWSOL foreign key (SQ_SOLIC_PAI)
      references SIW_SOLICITACAO (SQ_SIW_SOLICITACAO);

alter table SIW_SOLICITACAO
   add constraint FK_SIWSOL_SIWSOL_PROT foreign key (PROTOCOLO_SIW)
      references SIW_SOLICITACAO (SQ_SIW_SOLICITACAO);

alter table SIW_SOLICITACAO
   add constraint FK_SIWSOL_SIWTIPEVE foreign key (SQ_TIPO_EVENTO)
      references SIW_TIPO_EVENTO (SQ_TIPO_EVENTO);

alter table SIW_SOLICITACAO
   add constraint FK_SIWSOL_SIWTRA foreign key (SQ_SIW_TRAMITE)
      references SIW_TRAMITE (SQ_SIW_TRAMITE);

alter table SIW_SOLICITACAO_INTERESSADO
   add constraint FK_SIWSOLINT_COPES foreign key (SQ_PESSOA)
      references CO_PESSOA (SQ_PESSOA);

alter table SIW_SOLICITACAO_INTERESSADO
   add constraint FK_SIWSOLINT_SIWSOL foreign key (SQ_SIW_SOLICITACAO)
      references SIW_SOLICITACAO (SQ_SIW_SOLICITACAO);

alter table SIW_SOLICITACAO_INTERESSADO
   add constraint FK_SIWSOLINT_SIWTIPINT foreign key (SQ_TIPO_INTERESSADO)
      references SIW_TIPO_INTERESSADO (SQ_TIPO_INTERESSADO);

alter table SIW_SOLICITACAO_OBJETIVO
   add constraint FK_SIWSOLOBJ_PEOBJ foreign key (SQ_PEOBJETIVO)
      references PE_OBJETIVO (SQ_PEOBJETIVO);

alter table SIW_SOLICITACAO_OBJETIVO
   add constraint FK_SIWSOLOBJ_PEPLA foreign key (SQ_PLANO)
      references PE_PLANO (SQ_PLANO);

alter table SIW_SOLICITACAO_OBJETIVO
   add constraint FK_SIWSOLOBJ_SIWSOL foreign key (SQ_SIW_SOLICITACAO)
      references SIW_SOLICITACAO (SQ_SIW_SOLICITACAO);

alter table SIW_SOLIC_APOIO
   add constraint FK_SIWSOLAPO_COPES foreign key (SQ_PESSOA_ATUALIZACAO)
      references CO_PESSOA (SQ_PESSOA);

alter table SIW_SOLIC_APOIO
   add constraint FK_SIWSOLAPO_SIWSOL foreign key (SQ_SIW_SOLICITACAO)
      references SIW_SOLICITACAO (SQ_SIW_SOLICITACAO);

alter table SIW_SOLIC_APOIO
   add constraint FK_SIWSOLAPO_SIWTIPAPO foreign key (SQ_TIPO_APOIO)
      references SIW_TIPO_APOIO (SQ_TIPO_APOIO);

alter table SIW_SOLIC_ARQUIVO
   add constraint FK_SIWSOLARQ_SIWARQ foreign key (SQ_SIW_ARQUIVO)
      references SIW_ARQUIVO (SQ_SIW_ARQUIVO);

alter table SIW_SOLIC_ARQUIVO
   add constraint FK_SIWSOLARQ_SIWSOL foreign key (SQ_SIW_SOLICITACAO)
      references SIW_SOLICITACAO (SQ_SIW_SOLICITACAO);

alter table SIW_SOLIC_INDICADOR
   add constraint FK_SIWSOLIND_EOIND foreign key (SQ_EOINDICADOR)
      references EO_INDICADOR (SQ_EOINDICADOR);

alter table SIW_SOLIC_INDICADOR
   add constraint FK_SIWSOLIND_SIWSOL foreign key (SQ_SIW_SOLICITACAO)
      references SIW_SOLICITACAO (SQ_SIW_SOLICITACAO);

alter table SIW_SOLIC_LOG
   add constraint FK_SIWSOLLOG_COPES foreign key (SQ_PESSOA)
      references CO_PESSOA (SQ_PESSOA);

alter table SIW_SOLIC_LOG
   add constraint FK_SIWSOLLOG_SIWSL foreign key (SQ_SIW_SOLICITACAO)
      references SIW_SOLICITACAO (SQ_SIW_SOLICITACAO);

alter table SIW_SOLIC_LOG
   add constraint FK_SIWSOLLOG_SIWTR foreign key (SQ_SIW_TRAMITE)
      references SIW_TRAMITE (SQ_SIW_TRAMITE);

alter table SIW_SOLIC_LOG_ARQ
   add constraint FK_SIWSOLLOGARQ_SIWARQ foreign key (SQ_SIW_ARQUIVO)
      references SIW_ARQUIVO (SQ_SIW_ARQUIVO);

alter table SIW_SOLIC_LOG_ARQ
   add constraint FK_SIWSOLLOGARQ_SIWSOLLOG foreign key (SQ_SIW_SOLIC_LOG)
      references SIW_SOLIC_LOG (SQ_SIW_SOLIC_LOG);

alter table SIW_SOLIC_META
   add constraint FK_SIWSOLMET_COCID foreign key (SQ_CIDADE)
      references CO_CIDADE (SQ_CIDADE);

alter table SIW_SOLIC_META
   add constraint FK_SIWSOLMET_COPAI foreign key (SQ_PAIS)
      references CO_PAIS (SQ_PAIS);

alter table SIW_SOLIC_META
   add constraint FK_SIWSOLMET_COPES_CAD foreign key (CADASTRADOR)
      references CO_PESSOA (SQ_PESSOA);

alter table SIW_SOLIC_META
   add constraint FK_SIWSOLMET_COPES_RESP foreign key (SQ_PESSOA)
      references CO_PESSOA (SQ_PESSOA);

alter table SIW_SOLIC_META
   add constraint FK_SIWSOLMET_COREG foreign key (SQ_REGIAO)
      references CO_REGIAO (SQ_REGIAO);

alter table SIW_SOLIC_META
   add constraint FK_SIWSOLMET_COUF foreign key (CO_UF, SQ_PAIS)
      references CO_UF (CO_UF, SQ_PAIS);

alter table SIW_SOLIC_META
   add constraint FK_SIWSOLMET_EOIND foreign key (SQ_EOINDICADOR)
      references EO_INDICADOR (SQ_EOINDICADOR);

alter table SIW_SOLIC_META
   add constraint FK_SIWSOLMET_EOUNI foreign key (SQ_UNIDADE)
      references EO_UNIDADE (SQ_UNIDADE);

alter table SIW_SOLIC_META
   add constraint FK_SIWSOLMET_PEPLAN foreign key (SQ_PLANO)
      references PE_PLANO (SQ_PLANO);

alter table SIW_SOLIC_META
   add constraint FK_SIWSOLMET_SIWSOL foreign key (SQ_SIW_SOLICITACAO)
      references SIW_SOLICITACAO (SQ_SIW_SOLICITACAO);

alter table SIW_SOLIC_RECURSO
   add constraint FK_SIWSOLREC_COPES_AUT foreign key (AUTORIZADOR)
      references CO_PESSOA (SQ_PESSOA);

alter table SIW_SOLIC_RECURSO
   add constraint FK_SIWSOLREC_COPES_SOL foreign key (SOLICITANTE)
      references CO_PESSOA (SQ_PESSOA);

alter table SIW_SOLIC_RECURSO
   add constraint FK_SIWSOLREC_EOREC foreign key (SQ_RECURSO)
      references EO_RECURSO (SQ_RECURSO);

alter table SIW_SOLIC_RECURSO
   add constraint FK_SIWSOLREC_SIWSOL foreign key (SQ_SIW_SOLICITACAO)
      references SIW_SOLICITACAO (SQ_SIW_SOLICITACAO);

alter table SIW_SOLIC_RECURSO_ALOCACAO
   add constraint FK_SIWSOLRECALO_SIWSOLREC foreign key (SQ_SOLIC_RECURSO)
      references SIW_SOLIC_RECURSO (SQ_SOLIC_RECURSO);

alter table SIW_SOLIC_RECURSO_LOG
   add constraint FK_SIWSOLRECLOG_COPES foreign key (SQ_PESSOA)
      references CO_PESSOA (SQ_PESSOA);

alter table SIW_SOLIC_RECURSO_LOG
   add constraint FK_SIWSOLRECLOG_SIWSOLREC foreign key (SQ_SOLIC_RECURSO)
      references SIW_SOLIC_RECURSO (SQ_SOLIC_RECURSO);

alter table SIW_SOLIC_SITUACAO
   add constraint FK_SIWSOLSIT_COPES foreign key (SQ_PESSOA)
      references CO_PESSOA (SQ_PESSOA);

alter table SIW_SOLIC_SITUACAO
   add constraint FK_SIWSOLSIT_SIWSOL foreign key (SQ_SIW_SOLICITACAO)
      references SIW_SOLICITACAO (SQ_SIW_SOLICITACAO);

alter table SIW_SOLIC_VINCULO
   add constraint FK_SIWSOLVIN_SIWMEN foreign key (SQ_MENU)
      references SIW_MENU (SQ_MENU);

alter table SIW_SOLIC_VINCULO
   add constraint FK_SIWSOLVIN_SIWSOL foreign key (SQ_SIW_SOLICITACAO)
      references SIW_SOLICITACAO (SQ_SIW_SOLICITACAO);

alter table SIW_TIPO_APOIO
   add constraint FK_SIWTIPAPO_COPES foreign key (CLIENTE)
      references CO_PESSOA (SQ_PESSOA);

alter table SIW_TIPO_ARQUIVO
   add constraint FK_SIWTIPARQ_COPES foreign key (CLIENTE)
      references CO_PESSOA (SQ_PESSOA);

alter table SIW_TIPO_INTERESSADO
   add constraint FK_SIWTIPINT_SIWMEN foreign key (SQ_MENU)
      references SIW_MENU (SQ_MENU);

alter table SIW_TIPO_LOG
   add constraint FK_SIWTIPLOG_COPES foreign key (CLIENTE)
      references CO_PESSOA (SQ_PESSOA);

alter table SIW_TIPO_LOG
   add constraint FK_SIWTIPLOG_SIWMEN foreign key (SQ_MENU)
      references SIW_MENU (SQ_MENU);

alter table SIW_TIPO_RESTRICAO
   add constraint FK_SIWTIPRES_COPES foreign key (CLIENTE)
      references CO_PESSOA (SQ_PESSOA);

alter table SIW_TRAMITE
   add constraint FK_SIWTRA_SIWMEN foreign key (SQ_MENU)
      references SIW_MENU (SQ_MENU);

alter table SIW_TRAMITE_FLUXO
   add constraint FK_SIWTRAFLUX_SIWTRA_DEST foreign key (SQ_SIW_TRAMITE_DESTINO)
      references SIW_TRAMITE (SQ_SIW_TRAMITE);

alter table SIW_TRAMITE_FLUXO
   add constraint FK_SIWTRAFLUX_SIWTRA_ORI foreign key (SQ_SIW_TRAMITE_ORIGEM)
      references SIW_TRAMITE (SQ_SIW_TRAMITE);

