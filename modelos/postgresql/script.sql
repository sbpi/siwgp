/*==============================================================*/
/* DBMS name:      PostgreSQL 8                                 */
/* Created on:     14/10/2011 11:56:35                          */
/*==============================================================*/


CREATE SEQUENCE SQ_AGENCIA;

CREATE SEQUENCE SQ_AREA_ATUACAO;

CREATE SEQUENCE SQ_ARQUIVO;

CREATE SEQUENCE SQ_BANCO;

CREATE SEQUENCE SQ_CIDADE;

CREATE SEQUENCE SQ_COLUNA;

CREATE SEQUENCE SQ_CPF_ESPECIAL;

CREATE SEQUENCE SQ_DADO_TIPO;

CREATE SEQUENCE SQ_DATA_ESPECIAL;

CREATE SEQUENCE SQ_DEMANDA_LOG;

CREATE SEQUENCE SQ_DEMANDA_TIPO;

CREATE SEQUENCE SQ_EOINDICADOR;

CREATE SEQUENCE SQ_EOINDICADOR_AFERICAO;

CREATE SEQUENCE SQ_EOINDICADOR_AFERIDOR;

CREATE SEQUENCE SQ_EOINDICADOR_AGENDA;

CREATE SEQUENCE SQ_ESQUEMA;

CREATE SEQUENCE SQ_ESQUEMA_ATRIBUTO;

CREATE SEQUENCE SQ_ESQUEMA_INSERT;

CREATE SEQUENCE SQ_ESQUEMA_SCRIPT;

CREATE SEQUENCE SQ_ESQUEMA_TABELA;

CREATE SEQUENCE SQ_ETAPA_COMENTARIO;

CREATE SEQUENCE SQ_ETAPA_CONTRATO;

CREATE SEQUENCE SQ_ETAPA_DEMANDA;

CREATE SEQUENCE SQ_EVENTO;

CREATE SEQUENCE SQ_INDICE;

CREATE SEQUENCE SQ_INDICE_TIPO;

CREATE SEQUENCE SQ_LOCALIZACAO;

CREATE SEQUENCE SQ_MAIL;

CREATE SEQUENCE SQ_MAIL_ANEXO;

CREATE SEQUENCE SQ_MAIL_DESTINATARIO;

CREATE SEQUENCE SQ_MENU;

CREATE SEQUENCE SQ_META_CRONOGRAMA;

CREATE SEQUENCE SQ_MODULO;

CREATE SEQUENCE SQ_OCORRENCIA;

CREATE SEQUENCE SQ_PAIS;

CREATE SEQUENCE SQ_PARAM;

CREATE SEQUENCE SQ_PEHORIZONTE;

CREATE SEQUENCE SQ_PENATUREZA;

CREATE SEQUENCE SQ_PEOBJETIVO;

CREATE SEQUENCE SQ_PESSOA;

CREATE SEQUENCE SQ_PESSOA_CONTA_BANCARIA;

CREATE SEQUENCE SQ_PESSOA_ENDERECO;

CREATE SEQUENCE SQ_PESSOA_MAIL;

CREATE SEQUENCE SQ_PESSOA_TELEFONE;

CREATE SEQUENCE SQ_PLANO;

CREATE SEQUENCE SQ_PLANO_INDICADOR;

CREATE SEQUENCE SQ_PROCEDURE;

CREATE SEQUENCE SQ_PROGRAMA_LOG;

CREATE SEQUENCE SQ_PROJETO_ETAPA;

CREATE SEQUENCE SQ_PROJETO_LOG;

CREATE SEQUENCE SQ_PROJETO_RECURSO;

CREATE SEQUENCE SQ_PROJETO_RUBRICA;

CREATE SEQUENCE SQ_PT_CAMPO;

CREATE SEQUENCE SQ_PT_CONTEUDO;

CREATE SEQUENCE SQ_PT_EIXO;

CREATE SEQUENCE SQ_PT_EXIBICAO_CONTEUDO;

CREATE SEQUENCE SQ_PT_FILTRO;

CREATE SEQUENCE SQ_PT_MENU;

CREATE SEQUENCE SQ_PT_OPERADOR;

CREATE SEQUENCE SQ_PT_PESQUISA;

CREATE SEQUENCE SQ_RECURSO;

CREATE SEQUENCE SQ_RECURSO_DISPONIVEL;

CREATE SEQUENCE SQ_RECURSO_INDISPONIVEL;

CREATE SEQUENCE SQ_REGIAO;

CREATE SEQUENCE SQ_RELACIONAMENTO;

CREATE SEQUENCE SQ_RUBRICA_CRONOGRAMA;

CREATE SEQUENCE SQ_SEGMENTO;

CREATE SEQUENCE SQ_SEGMENTO_MENU;

CREATE SEQUENCE SQ_SEGMENTO_VINCULO;

CREATE SEQUENCE SQ_SISTEMA;

CREATE SEQUENCE SQ_SIW_ARQUIVO;

CREATE SEQUENCE SQ_SIW_COORDENADA;

CREATE SEQUENCE SQ_SIW_RESTRICAO;

CREATE SEQUENCE SQ_SIW_SOLICITACAO;

CREATE SEQUENCE SQ_SIW_SOLIC_LOG;

CREATE SEQUENCE SQ_SIW_TRAMITE;

CREATE SEQUENCE SQ_SOLICITACAO_INTERESSADO;

CREATE SEQUENCE SQ_SOLIC_APOIO;

CREATE SEQUENCE SQ_SOLIC_INDICADOR;

CREATE SEQUENCE SQ_SOLIC_META;

CREATE SEQUENCE SQ_SOLIC_RECURSO;

CREATE SEQUENCE SQ_SOLIC_RECURSO_ALOCACAO;

CREATE SEQUENCE SQ_SOLIC_RECURSO_LOG;

CREATE SEQUENCE SQ_SOLIC_SITUACAO;

CREATE SEQUENCE SQ_SP_PARAM;

CREATE SEQUENCE SQ_SP_TIPO;

CREATE SEQUENCE SQ_STORED_PROC;

CREATE SEQUENCE SQ_TABELA;

CREATE SEQUENCE SQ_TABELA_TIPO;

CREATE SEQUENCE SQ_TIPO_APOIO;

CREATE SEQUENCE SQ_TIPO_ARQUIVO;

CREATE SEQUENCE SQ_TIPO_ENDERECO;

CREATE SEQUENCE SQ_TIPO_EVENTO;

CREATE SEQUENCE SQ_TIPO_INDICADOR;

CREATE SEQUENCE SQ_TIPO_INTERESSADO;

CREATE SEQUENCE SQ_TIPO_LOG;

CREATE SEQUENCE SQ_TIPO_PESSOA;

CREATE SEQUENCE SQ_TIPO_POSTO;

CREATE SEQUENCE SQ_TIPO_RECURSO;

CREATE SEQUENCE SQ_TIPO_RESTRICAO;

CREATE SEQUENCE SQ_TIPO_TELEFONE;

CREATE SEQUENCE SQ_TIPO_UNIDADE;

CREATE SEQUENCE SQ_TIPO_VINCULO;

CREATE SEQUENCE SQ_TRIGGER;

CREATE SEQUENCE SQ_UNIDADE;

CREATE SEQUENCE SQ_UNIDADE_MEDIDA;

CREATE SEQUENCE SQ_UNIDADE_RESPONSAVEL;

CREATE SEQUENCE SQ_USUARIO;

/*==============================================================*/
/* Table: CO_AGENCIA                                            */
/*==============================================================*/
CREATE TABLE CO_AGENCIA (
   SQ_AGENCIA           NUMERIC(18)          NOT NULL,
   SQ_BANCO             NUMERIC(18)          NOT NULL,
   CODIGO               VARCHAR(30)          NOT NULL,
   ATIVO                VARCHAR(1)           NOT NULL DEFAULT 'S'
      CONSTRAINT CKC_ATIVO_CO_AGENC CHECK (ATIVO IN ('S','N') AND ATIVO = UPPER(ATIVO)),
   PADRAO               VARCHAR(1)           NOT NULL DEFAULT 'N'
      CONSTRAINT CKC_PADRAO_COAGE CHECK (PADRAO IN ('S','N') AND PADRAO = UPPER(PADRAO)),
   NOME                 VARCHAR(43)          NOT NULL,
   CONSTRAINT PK_CO_AGENCIA PRIMARY KEY (SQ_AGENCIA)
);

COMMENT ON TABLE CO_AGENCIA IS
'Armazena a tabela de agências';

COMMENT ON COLUMN CO_AGENCIA.SQ_AGENCIA IS
'Chave de CO_AGENCIA.';

COMMENT ON COLUMN CO_AGENCIA.SQ_BANCO IS
'Chave de CO_BANCO. Indica a que banco o registro está ligado.';

COMMENT ON COLUMN CO_AGENCIA.CODIGO IS
'Código da agência bancária. Está com tamanho acima do normal para aceitar agências no exterior.';

COMMENT ON COLUMN CO_AGENCIA.ATIVO IS
'Indica se o registro deve ou não ser exibido.';

COMMENT ON COLUMN CO_AGENCIA.PADRAO IS
'Indica se este registro deve ser apresentado como padrão para o usuário.';

COMMENT ON COLUMN CO_AGENCIA.NOME IS
'Nome da agência.';

/*==============================================================*/
/* Index: IN_COAGE_SQBANCO                                      */
/*==============================================================*/
CREATE UNIQUE INDEX IN_COAGE_SQBANCO ON CO_AGENCIA (
SQ_BANCO,
SQ_AGENCIA
);

/*==============================================================*/
/* Index: IN_COAGE_ATIVO                                        */
/*==============================================================*/
CREATE  INDEX IN_COAGE_ATIVO ON CO_AGENCIA (
SQ_BANCO,
ATIVO
);

/*==============================================================*/
/* Index: IN_COAGE_PADRAO                                       */
/*==============================================================*/
CREATE  INDEX IN_COAGE_PADRAO ON CO_AGENCIA (
SQ_BANCO,
PADRAO
);

/*==============================================================*/
/* Index: IN_COAGE_NOME                                         */
/*==============================================================*/
CREATE  INDEX IN_COAGE_NOME ON CO_AGENCIA (
NOME,
SQ_AGENCIA
);

/*==============================================================*/
/* Index: IN_COAGE_UNICO                                        */
/*==============================================================*/
CREATE UNIQUE INDEX IN_COAGE_UNICO ON CO_AGENCIA (
SQ_BANCO,
CODIGO
);

/*==============================================================*/
/* Table: CO_BANCO                                              */
/*==============================================================*/
CREATE TABLE CO_BANCO (
   SQ_BANCO             NUMERIC(18)          NOT NULL,
   CODIGO               VARCHAR(30)          NOT NULL,
   NOME                 VARCHAR(31)          NOT NULL,
   ATIVO                VARCHAR(1)           NOT NULL DEFAULT 'S'
      CONSTRAINT CKC_ATIVO_CO_BANCO CHECK (ATIVO IN ('S','N') AND ATIVO = UPPER(ATIVO)),
   PADRAO               VARCHAR(1)           NOT NULL DEFAULT 'N'
      CONSTRAINT CKC_PADRAO_COBAN CHECK (PADRAO IN ('S','N') AND PADRAO = UPPER(PADRAO)),
   EXIGE_OPERACAO       VARCHAR(1)           NOT NULL DEFAULT 'N'
      CONSTRAINT CKC_EXIGE_OPERACAO_CO_BANCO CHECK (EXIGE_OPERACAO IN ('S','N') AND EXIGE_OPERACAO = UPPER(EXIGE_OPERACAO)),
   CONSTRAINT PK_CO_BANCO PRIMARY KEY (SQ_BANCO)
);

COMMENT ON TABLE CO_BANCO IS
'Armazena a tabela de bancos';

COMMENT ON COLUMN CO_BANCO.SQ_BANCO IS
'Chave de CO_BANCO.';

COMMENT ON COLUMN CO_BANCO.CODIGO IS
'Código do banco. Está com tamanho acima do normal para aceitar bancos do exterior.';

COMMENT ON COLUMN CO_BANCO.NOME IS
'Nome do banco.';

COMMENT ON COLUMN CO_BANCO.ATIVO IS
'Indica se o registro deve ou não ser exibido.';

COMMENT ON COLUMN CO_BANCO.PADRAO IS
'Indica se este registro deve ser apresentado como padrão para o usuário.';

COMMENT ON COLUMN CO_BANCO.EXIGE_OPERACAO IS
'Indica se os dados bancários do banco exigem o campo Operação.';

/*==============================================================*/
/* Index: IN_COBANCO_CODIGO                                     */
/*==============================================================*/
CREATE UNIQUE INDEX IN_COBANCO_CODIGO ON CO_BANCO (
CODIGO
);

/*==============================================================*/
/* Index: IN_COBANCO_NOME                                       */
/*==============================================================*/
CREATE UNIQUE INDEX IN_COBANCO_NOME ON CO_BANCO (
NOME
);

/*==============================================================*/
/* Index: IN_COBANCO_ATIVO                                      */
/*==============================================================*/
CREATE  INDEX IN_COBANCO_ATIVO ON CO_BANCO (
ATIVO
);

/*==============================================================*/
/* Index: IN_COBANCO_PADRAO                                     */
/*==============================================================*/
CREATE  INDEX IN_COBANCO_PADRAO ON CO_BANCO (
PADRAO
);

/*==============================================================*/
/* Table: CO_CIDADE                                             */
/*==============================================================*/
CREATE TABLE CO_CIDADE (
   SQ_CIDADE            NUMERIC(18)          NOT NULL,
   SQ_PAIS              NUMERIC(18)          NOT NULL,
   SQ_REGIAO            NUMERIC(18)          NOT NULL,
   CO_UF                VARCHAR(3)           NOT NULL,
   NOME                 VARCHAR(60)          NOT NULL,
   DDD                  VARCHAR(4)           NULL,
   CODIGO_IBGE          VARCHAR(20)          NULL,
   CAPITAL              VARCHAR(1)           NOT NULL DEFAULT 'N'
      CONSTRAINT CKC_COCID_CAP
               CHECK (CAPITAL IN ('S','N') AND CAPITAL = UPPER(CAPITAL)),
   CODIGO_EXTERNO       VARCHAR(60)          NULL,
   AEROPORTOS           NUMERIC(1)           NOT NULL DEFAULT 1,
   CONSTRAINT PK_CO_CIDADE PRIMARY KEY (SQ_CIDADE)
);

COMMENT ON TABLE CO_CIDADE IS
'Armazena a tabela de cidades';

COMMENT ON COLUMN CO_CIDADE.SQ_CIDADE IS
'Chave de CO_CIDADE.';

COMMENT ON COLUMN CO_CIDADE.SQ_PAIS IS
'Chave de CO_PAIS. Indica a que país o registro está ligado.';

COMMENT ON COLUMN CO_CIDADE.SQ_REGIAO IS
'Chave de CO_REGIAO. Indica a que região o registro está ligado.';

COMMENT ON COLUMN CO_CIDADE.CO_UF IS
'Chave de CO_UF. Indica a que estado o registro está ligado.';

COMMENT ON COLUMN CO_CIDADE.NOME IS
'Nome da cidade.';

COMMENT ON COLUMN CO_CIDADE.DDD IS
'DDD da cidade.';

COMMENT ON COLUMN CO_CIDADE.CODIGO_IBGE IS
'Código IBGE da cidade.';

COMMENT ON COLUMN CO_CIDADE.CAPITAL IS
'Indica se a cidade é capital do estado. Apenas uma cidade por estado pode ter valor igual a ''S''.';

COMMENT ON COLUMN CO_CIDADE.CODIGO_EXTERNO IS
'Código desse registro num sistema externo.';

COMMENT ON COLUMN CO_CIDADE.AEROPORTOS IS
'Indica o número de aeroportos da cidade.';

/*==============================================================*/
/* Index: IN_COCID_NOME                                         */
/*==============================================================*/
CREATE UNIQUE INDEX IN_COCID_NOME ON CO_CIDADE (
NOME,
CO_UF,
SQ_PAIS
);

/*==============================================================*/
/* Index: IN_COCID_PAISUF                                       */
/*==============================================================*/
CREATE  INDEX IN_COCID_PAISUF ON CO_CIDADE (
CO_UF
);

/*==============================================================*/
/* Index: IN_COCID_PAISREG                                      */
/*==============================================================*/
CREATE  INDEX IN_COCID_PAISREG ON CO_CIDADE (
SQ_REGIAO
);

/*==============================================================*/
/* Index: IN_COCID_CODIBGE                                      */
/*==============================================================*/
CREATE  INDEX IN_COCID_CODIBGE ON CO_CIDADE (
CODIGO_IBGE
);

/*==============================================================*/
/* Index: IN_COCID_EXTERNO                                      */
/*==============================================================*/
CREATE  INDEX IN_COCID_EXTERNO ON CO_CIDADE (
CODIGO_EXTERNO,
SQ_CIDADE
);

/*==============================================================*/
/* Table: CO_PAIS                                               */
/*==============================================================*/
CREATE TABLE CO_PAIS (
   SQ_PAIS              NUMERIC(18)          NOT NULL,
   NOME                 VARCHAR(60)          NOT NULL,
   ATIVO                VARCHAR(1)           NOT NULL DEFAULT 'S'
      CONSTRAINT CKC_ATIVO_CO_PAIS CHECK (ATIVO IN ('S','N') AND ATIVO = UPPER(ATIVO)),
   PADRAO               VARCHAR(1)           NOT NULL DEFAULT 'N'
      CONSTRAINT CKC_PADRAO_COPAI CHECK (PADRAO IN ('S','N') AND PADRAO = UPPER(PADRAO)),
   DDI                  VARCHAR(10)          NULL,
   SIGLA                VARCHAR(3)           NULL,
   CODIGO_EXTERNO       VARCHAR(60)          NULL,
   CONTINENTE           NUMERIC(1)           NOT NULL DEFAULT 1,
   CONSTRAINT PK_CO_PAIS PRIMARY KEY (SQ_PAIS)
);

COMMENT ON TABLE CO_PAIS IS
'Armazena a tabela de países';

COMMENT ON COLUMN CO_PAIS.SQ_PAIS IS
'Chave de CO_PAIS.';

COMMENT ON COLUMN CO_PAIS.NOME IS
'Nome do país.';

COMMENT ON COLUMN CO_PAIS.ATIVO IS
'Indica se o registro deve ou não ser exibido.';

COMMENT ON COLUMN CO_PAIS.PADRAO IS
'Indica se este registro deve ser apresentado como padrão para o usuário.';

COMMENT ON COLUMN CO_PAIS.DDI IS
'DDI do país.';

COMMENT ON COLUMN CO_PAIS.SIGLA IS
'Sigla do país, usada em relatórios para facilitar a exibição com largura pequena.';

COMMENT ON COLUMN CO_PAIS.CODIGO_EXTERNO IS
'Código desse registroo para um sistema externo.';

COMMENT ON COLUMN CO_PAIS.CONTINENTE IS
'Continente do país: 1 - América, 2 - Europa, 3 - Ásia, 4 - África, 5 - Oceania.';

/*==============================================================*/
/* Index: IN_COPAIS_NOME                                        */
/*==============================================================*/
CREATE UNIQUE INDEX IN_COPAIS_NOME ON CO_PAIS (
NOME
);

/*==============================================================*/
/* Index: IN_COPAIS_ATIVO                                       */
/*==============================================================*/
CREATE  INDEX IN_COPAIS_ATIVO ON CO_PAIS (
ATIVO
);

/*==============================================================*/
/* Index: IN_COPAIS_PADRAO                                      */
/*==============================================================*/
CREATE  INDEX IN_COPAIS_PADRAO ON CO_PAIS (
PADRAO
);

/*==============================================================*/
/* Index: IN_COPAIS_EXTERNO                                     */
/*==============================================================*/
CREATE  INDEX IN_COPAIS_EXTERNO ON CO_PAIS (
CODIGO_EXTERNO,
SQ_PAIS
);

/*==============================================================*/
/* Table: CO_PESSOA                                             */
/*==============================================================*/
CREATE TABLE CO_PESSOA (
   SQ_PESSOA            NUMERIC(18)          NOT NULL,
   SQ_PESSOA_PAI        NUMERIC(18)          NULL,
   SQ_TIPO_VINCULO      NUMERIC(18)          NULL,
   SQ_TIPO_PESSOA       NUMERIC(10)          NULL,
   SQ_RECURSO           NUMERIC(18)          NULL,
   NOME                 VARCHAR(63)          NOT NULL,
   NOME_RESUMIDO        VARCHAR(21)          NULL,
   NOME_INDICE          VARCHAR(63)          NOT NULL,
   NOME_RESUMIDO_IND    VARCHAR(21)          NULL,
   CLIENTE              VARCHAR(1)           NOT NULL DEFAULT 'N'
      CONSTRAINT CKC_CLIENTE_CO_PESSO CHECK (CLIENTE IN ('S','N') AND CLIENTE = UPPER(CLIENTE)),
   FORNECEDOR           VARCHAR(1)           NOT NULL DEFAULT 'N'
      CONSTRAINT CKC_FORNECEDOR_CO_PESSO CHECK (FORNECEDOR IN ('S','N') AND FORNECEDOR = UPPER(FORNECEDOR)),
   ENTIDADE             VARCHAR(1)           NOT NULL DEFAULT 'N'
      CONSTRAINT CKC_ENTIDADE_CO_PESSO CHECK (ENTIDADE IN ('S','N') AND ENTIDADE = UPPER(ENTIDADE)),
   PARCEIRO             VARCHAR(1)           NOT NULL DEFAULT 'N'
      CONSTRAINT CKC_PARCEIRO_CO_PESSO CHECK (PARCEIRO IN ('S','N') AND PARCEIRO = UPPER(PARCEIRO)),
   FUNCIONARIO          VARCHAR(1)           NOT NULL DEFAULT 'N'
      CONSTRAINT CKC_FUNCIONARIO_CO_PESSO CHECK (FUNCIONARIO IN ('S','N') AND FUNCIONARIO = UPPER(FUNCIONARIO)),
   DEPENDENTE           VARCHAR(1)           NOT NULL DEFAULT 'N'
      CONSTRAINT CKC_DEPENDENTE_CO_PESSO CHECK (DEPENDENTE IN ('S','N') AND DEPENDENTE = UPPER(DEPENDENTE)),
   CODIGO_EXTERNO       VARCHAR(60)          NULL,
   INCLUSAO             DATE                 NOT NULL DEFAULT 'now()',
   CONSTRAINT PK_CO_PESSOA PRIMARY KEY (SQ_PESSOA)
);

COMMENT ON TABLE CO_PESSOA IS
'Armazena pessoas físicas e jurídicas';

COMMENT ON COLUMN CO_PESSOA.SQ_PESSOA IS
'Chave de CO_PESSOA.';

COMMENT ON COLUMN CO_PESSOA.SQ_PESSOA_PAI IS
'Chave de CO_PESSOA. Auto-relacionamento da tabela.';

COMMENT ON COLUMN CO_PESSOA.SQ_TIPO_VINCULO IS
'Chave de CO_TIPO_VINCULO. Indica a que tipo de vínculo o registro está ligado.';

COMMENT ON COLUMN CO_PESSOA.SQ_TIPO_PESSOA IS
'Chave de CO_TIPO_PESSOA. Indica a que tipo de pessoa o registro está ligado.';

COMMENT ON COLUMN CO_PESSOA.SQ_RECURSO IS
'Chave de EO_RECURSO. Indica a que recurso a pessoa está ligada.';

COMMENT ON COLUMN CO_PESSOA.NOME IS
'Nome da pessoa.';

COMMENT ON COLUMN CO_PESSOA.NOME_RESUMIDO IS
'Nome pelo qual a pessoa é conhecida (apelido, cognome etc.)';

COMMENT ON COLUMN CO_PESSOA.NOME_INDICE IS
'Este campo é alimentado por uma trigger nos eventos insert e update, com a finalidade de facilitar a busca por nome. Seu conteúdo é igual al nome, mas em maiúsculas e sem acentos.';

COMMENT ON COLUMN CO_PESSOA.NOME_RESUMIDO_IND IS
'Igual a NOME_INDICE';

COMMENT ON COLUMN CO_PESSOA.CLIENTE IS
'Indica se a pessoa é cliente da organização.';

COMMENT ON COLUMN CO_PESSOA.FORNECEDOR IS
'Indica se a pessoa é fornecedora da organização.';

COMMENT ON COLUMN CO_PESSOA.ENTIDADE IS
'Indica se a pessoa é uma entidade de interesse da organização.';

COMMENT ON COLUMN CO_PESSOA.PARCEIRO IS
'Indica se a pessoa é parceira da organização.';

COMMENT ON COLUMN CO_PESSOA.FUNCIONARIO IS
'Indica se a pessoa é funcionária da organização.';

COMMENT ON COLUMN CO_PESSOA.DEPENDENTE IS
'Indica se a pessoa é dependente de um funcionário da organização.';

COMMENT ON COLUMN CO_PESSOA.CODIGO_EXTERNO IS
'Código desse registro num sistema externo.';

COMMENT ON COLUMN CO_PESSOA.INCLUSAO IS
'Data de inclusão do registro na tabela.';

/*==============================================================*/
/* Index: IN_COPES_NMIND                                        */
/*==============================================================*/
CREATE  INDEX IN_COPES_NMIND ON CO_PESSOA (
NOME_INDICE
);

/*==============================================================*/
/* Index: IN_COPES_NMRESIND                                     */
/*==============================================================*/
CREATE  INDEX IN_COPES_NMRESIND ON CO_PESSOA (
NOME_RESUMIDO_IND
);

/*==============================================================*/
/* Index: IN_COPES_SQPESPAI                                     */
/*==============================================================*/
CREATE  INDEX IN_COPES_SQPESPAI ON CO_PESSOA (
SQ_PESSOA_PAI,
SQ_PESSOA
);

/*==============================================================*/
/* Index: IN_COPES_SQTPVINC                                     */
/*==============================================================*/
CREATE  INDEX IN_COPES_SQTPVINC ON CO_PESSOA (
SQ_PESSOA_PAI,
SQ_TIPO_VINCULO,
SQ_PESSOA
);

/*==============================================================*/
/* Index: IN_COPES_CLIENTE                                      */
/*==============================================================*/
CREATE  INDEX IN_COPES_CLIENTE ON CO_PESSOA (
SQ_PESSOA_PAI,
CLIENTE,
SQ_PESSOA
);

/*==============================================================*/
/* Index: IN_COPES_FORNEC                                       */
/*==============================================================*/
CREATE  INDEX IN_COPES_FORNEC ON CO_PESSOA (
SQ_PESSOA_PAI,
FORNECEDOR,
SQ_PESSOA
);

/*==============================================================*/
/* Index: IN_COPES_ENTIDADE                                     */
/*==============================================================*/
CREATE  INDEX IN_COPES_ENTIDADE ON CO_PESSOA (
SQ_PESSOA_PAI,
ENTIDADE,
SQ_PESSOA
);

/*==============================================================*/
/* Index: IN_COPES_PARCEIRO                                     */
/*==============================================================*/
CREATE  INDEX IN_COPES_PARCEIRO ON CO_PESSOA (
SQ_PESSOA_PAI,
PARCEIRO,
SQ_PESSOA
);

/*==============================================================*/
/* Index: IN_COPES_FUNCION                                      */
/*==============================================================*/
CREATE  INDEX IN_COPES_FUNCION ON CO_PESSOA (
SQ_PESSOA_PAI,
FUNCIONARIO,
SQ_PESSOA
);

/*==============================================================*/
/* Index: IN_COPES_DEPEND                                       */
/*==============================================================*/
CREATE  INDEX IN_COPES_DEPEND ON CO_PESSOA (
SQ_PESSOA_PAI,
DEPENDENTE,
SQ_PESSOA
);

/*==============================================================*/
/* Index: IN_COPES_EXTERNO                                      */
/*==============================================================*/
CREATE  INDEX IN_COPES_EXTERNO ON CO_PESSOA (
CODIGO_EXTERNO,
SQ_PESSOA
);

/*==============================================================*/
/* Index: IN_COPES_REC                                          */
/*==============================================================*/
CREATE  INDEX IN_COPES_REC ON CO_PESSOA (
SQ_RECURSO,
SQ_PESSOA
);

/*==============================================================*/
/* Table: CO_PESSOA_CONTA                                       */
/*==============================================================*/
CREATE TABLE CO_PESSOA_CONTA (
   SQ_PESSOA_CONTA      NUMERIC(18)          NOT NULL,
   SQ_PESSOA            NUMERIC(18)          NOT NULL,
   SQ_AGENCIA           NUMERIC(18)          NOT NULL,
   OPERACAO             VARCHAR(6)           NULL,
   NUMERO               VARCHAR(30)          NOT NULL,
   ATIVO                VARCHAR(1)           NOT NULL DEFAULT 'S'
      CONSTRAINT CKC_ATIVO_CO_PESSO CHECK (ATIVO IN ('S','N') AND ATIVO = UPPER(ATIVO)),
   PADRAO               VARCHAR(1)           NOT NULL DEFAULT 'N'
      CONSTRAINT CKC_COPESCONBAN_PD
               CHECK (PADRAO IN ('S','N') AND PADRAO = UPPER(PADRAO)),
   TIPO_CONTA           VARCHAR(1)           NOT NULL
      CONSTRAINT CKC_COPESCONBAN_TC
               CHECK (TIPO_CONTA IN ('1','2')),
   INVALIDA             VARCHAR(1)           NOT NULL DEFAULT 'N'
      CONSTRAINT CKC_COPESCONBAN_IV
               CHECK (INVALIDA IN ('S','N') AND INVALIDA = UPPER(INVALIDA)),
   DEVOLUCAO_VALOR      VARCHAR(1)           NOT NULL DEFAULT 'N'
      CONSTRAINT CKC_DEVOLUCAO_VALOR_CO_PESSO CHECK (DEVOLUCAO_VALOR IN ('S','N') AND DEVOLUCAO_VALOR = UPPER(DEVOLUCAO_VALOR)),
   SALDO_INICIAL        NUMERIC(18,2)        NOT NULL DEFAULT 0,
   CONSTRAINT PK_CO_PESSOA_CONTA PRIMARY KEY (SQ_PESSOA_CONTA)
);

COMMENT ON TABLE CO_PESSOA_CONTA IS
'Armazena a conta bancária das pessoas';

COMMENT ON COLUMN CO_PESSOA_CONTA.SQ_PESSOA_CONTA IS
'Chave de CO_PESSOA_CONTA.';

COMMENT ON COLUMN CO_PESSOA_CONTA.SQ_PESSOA IS
'Chave de CO_PESSOA. Indica a que pessoa o registro está ligado.';

COMMENT ON COLUMN CO_PESSOA_CONTA.SQ_AGENCIA IS
'Chave de CO_AGENCIA. Indica a que agência o registro está ligado.';

COMMENT ON COLUMN CO_PESSOA_CONTA.OPERACAO IS
'Armazena a operação da conta, utilizada por bancos como Caixa Econômica e Bradesco.';

COMMENT ON COLUMN CO_PESSOA_CONTA.NUMERO IS
'Número da conta bancária.';

COMMENT ON COLUMN CO_PESSOA_CONTA.ATIVO IS
'Indica se o registro deve ou não ser exibido.';

COMMENT ON COLUMN CO_PESSOA_CONTA.PADRAO IS
'Indica se este registro deve ser apresentado como padrão para o usuário.';

COMMENT ON COLUMN CO_PESSOA_CONTA.TIPO_CONTA IS
'Armazena o tipo da conta corrente';

COMMENT ON COLUMN CO_PESSOA_CONTA.INVALIDA IS
'Indica se a conta é inválida ou não.';

COMMENT ON COLUMN CO_PESSOA_CONTA.DEVOLUCAO_VALOR IS
'Indica se a conta pode ser usada para devolução de valores.';

COMMENT ON COLUMN CO_PESSOA_CONTA.SALDO_INICIAL IS
'Saldo inicial da conta bancária. Utilizado na geração de relatórios financeiros.';

/*==============================================================*/
/* Index: IN_COPESCONBAN_PES                                    */
/*==============================================================*/
CREATE  INDEX IN_COPESCONBAN_PES ON CO_PESSOA_CONTA (
SQ_PESSOA,
TIPO_CONTA
);

/*==============================================================*/
/* Table: CO_PESSOA_ENDERECO                                    */
/*==============================================================*/
CREATE TABLE CO_PESSOA_ENDERECO (
   SQ_PESSOA_ENDERECO   NUMERIC(18)          NOT NULL,
   SQ_PESSOA            NUMERIC(18)          NULL,
   SQ_TIPO_ENDERECO     NUMERIC(18)          NOT NULL,
   LOGRADOURO           VARCHAR(65)          NOT NULL,
   COMPLEMENTO          VARCHAR(21)          NULL,
   BAIRRO               VARCHAR(40)          NULL,
   SQ_CIDADE            NUMERIC(18)          NOT NULL,
   CEP                  VARCHAR(9)           NULL,
   PADRAO               VARCHAR(1)           NOT NULL DEFAULT 'N'
      CONSTRAINT CKC_COPESEND_PAD
               CHECK (PADRAO IN ('S','N') AND PADRAO = UPPER(PADRAO)),
   CODIGO_EXTERNO       VARCHAR(60)          NULL,
   CONSTRAINT PK_CO_PESSOA_ENDERECO PRIMARY KEY (SQ_PESSOA_ENDERECO)
);

COMMENT ON TABLE CO_PESSOA_ENDERECO IS
'Armazena os endereços da pessoa';

COMMENT ON COLUMN CO_PESSOA_ENDERECO.SQ_PESSOA_ENDERECO IS
'Chave de CO_PESSOA_ENDERECO.';

COMMENT ON COLUMN CO_PESSOA_ENDERECO.SQ_PESSOA IS
'Chave de CO_PESSOA. Indica a que pessoa o registro está ligado.';

COMMENT ON COLUMN CO_PESSOA_ENDERECO.SQ_TIPO_ENDERECO IS
'Chave de CO_TIPO_ENDERECO. Indica a que tipo de endereço o registro está ligado.';

COMMENT ON COLUMN CO_PESSOA_ENDERECO.LOGRADOURO IS
'Logradouro do endereço.';

COMMENT ON COLUMN CO_PESSOA_ENDERECO.COMPLEMENTO IS
'Complemento do endereço.';

COMMENT ON COLUMN CO_PESSOA_ENDERECO.BAIRRO IS
'Nome do bairro.';

COMMENT ON COLUMN CO_PESSOA_ENDERECO.SQ_CIDADE IS
'Chave de CO_CIDADE. Indica a que cidade o registro está ligado.';

COMMENT ON COLUMN CO_PESSOA_ENDERECO.CEP IS
'Código de endereçamento postal.';

COMMENT ON COLUMN CO_PESSOA_ENDERECO.PADRAO IS
'Indica se este registro deve ser apresentado como padrão para o usuário.';

COMMENT ON COLUMN CO_PESSOA_ENDERECO.CODIGO_EXTERNO IS
'Código desse registro num sistema externo.';

/*==============================================================*/
/* Index: IN_COPESEND_SQPES                                     */
/*==============================================================*/
CREATE  INDEX IN_COPESEND_SQPES ON CO_PESSOA_ENDERECO (
SQ_PESSOA
);

/*==============================================================*/
/* Index: IN_COPESEND_CIDADE                                    */
/*==============================================================*/
CREATE  INDEX IN_COPESEND_CIDADE ON CO_PESSOA_ENDERECO (
SQ_CIDADE
);

/*==============================================================*/
/* Index: IN_COPESEND_BAIRRO                                    */
/*==============================================================*/
CREATE  INDEX IN_COPESEND_BAIRRO ON CO_PESSOA_ENDERECO (
BAIRRO
);

/*==============================================================*/
/* Index: IN_COPESEND_PADRAO                                    */
/*==============================================================*/
CREATE  INDEX IN_COPESEND_PADRAO ON CO_PESSOA_ENDERECO (
PADRAO
);

/*==============================================================*/
/* Index: IN_COPESEND_EXTERNO                                   */
/*==============================================================*/
CREATE  INDEX IN_COPESEND_EXTERNO ON CO_PESSOA_ENDERECO (
CODIGO_EXTERNO,
SQ_PESSOA,
SQ_PESSOA_ENDERECO
);

/*==============================================================*/
/* Table: CO_PESSOA_FISICA                                      */
/*==============================================================*/
CREATE TABLE CO_PESSOA_FISICA (
   SQ_PESSOA            NUMERIC(18)          NOT NULL,
   CLIENTE              NUMERIC(18)          NOT NULL,
   NASCIMENTO           DATE                 NULL,
   RG_NUMERO            VARCHAR(30)          NULL,
   RG_EMISSOR           VARCHAR(32)          NULL,
   RG_EMISSAO           DATE                 NULL,
   CPF                  VARCHAR(20)          NULL,
   SQ_CIDADE_NASC       NUMERIC(18)          NULL,
   PASSAPORTE_NUMERO    VARCHAR(20)          NULL,
   SQ_PAIS_PASSAPORTE   NUMERIC(18)          NULL,
   SEXO                 VARCHAR(1)           NOT NULL DEFAULT 'M'
      CONSTRAINT CKC_SEXO_CO_PESSO CHECK (SEXO IN ('M','F')),
   INCLUSAO             DATE                 NOT NULL DEFAULT 'now()',
   MATRICULA            VARCHAR(30)          NULL,
   CONSTRAINT PK_CO_PESSOA_FISICA PRIMARY KEY (SQ_PESSOA)
);

COMMENT ON TABLE CO_PESSOA_FISICA IS
'Armazena dados das pessoas físicas';

COMMENT ON COLUMN CO_PESSOA_FISICA.SQ_PESSOA IS
'Chave de CO_PESSOA. Indica a que pessoa o registro está ligado.';

COMMENT ON COLUMN CO_PESSOA_FISICA.CLIENTE IS
'Chave de CO_PESSOA. Indica a que cliente o registro está ligado.';

COMMENT ON COLUMN CO_PESSOA_FISICA.NASCIMENTO IS
'Data de nascimento da pessoa física';

COMMENT ON COLUMN CO_PESSOA_FISICA.RG_NUMERO IS
'Número do rg';

COMMENT ON COLUMN CO_PESSOA_FISICA.RG_EMISSOR IS
'Órgão emissor do rg';

COMMENT ON COLUMN CO_PESSOA_FISICA.RG_EMISSAO IS
'Data de emissão do registro geral (Identidade)';

COMMENT ON COLUMN CO_PESSOA_FISICA.CPF IS
'CPF da pessoa física.';

COMMENT ON COLUMN CO_PESSOA_FISICA.SQ_CIDADE_NASC IS
'Chave de CO_CIDADE. Indica a cidade de nascimento da pessoa.';

COMMENT ON COLUMN CO_PESSOA_FISICA.PASSAPORTE_NUMERO IS
'Número do passaporte.';

COMMENT ON COLUMN CO_PESSOA_FISICA.SQ_PAIS_PASSAPORTE IS
'Chave de CO_PAIS. Indica o país emissor do passaporte.';

COMMENT ON COLUMN CO_PESSOA_FISICA.SEXO IS
'Indica se esta pessoa jurídica é a sede (matriz).';

COMMENT ON COLUMN CO_PESSOA_FISICA.INCLUSAO IS
'Data de inclusão do registro na tabela.
';

COMMENT ON COLUMN CO_PESSOA_FISICA.MATRICULA IS
'Registro funcional da pessoa na organização.';

/*==============================================================*/
/* Index: IN_COPESFIS_NASC                                      */
/*==============================================================*/
CREATE  INDEX IN_COPESFIS_NASC ON CO_PESSOA_FISICA (
NASCIMENTO
);

/*==============================================================*/
/* Index: IN_COPESFIS_SEXO                                      */
/*==============================================================*/
CREATE  INDEX IN_COPESFIS_SEXO ON CO_PESSOA_FISICA (
SEXO
);

/*==============================================================*/
/* Index: IN_COPESFIS_CPF                                       */
/*==============================================================*/
CREATE  INDEX IN_COPESFIS_CPF ON CO_PESSOA_FISICA (
CPF,
CLIENTE
);

/*==============================================================*/
/* Index: IN_COPESFIS_CLI                                       */
/*==============================================================*/
CREATE  INDEX IN_COPESFIS_CLI ON CO_PESSOA_FISICA (
CLIENTE,
SQ_PESSOA
);

/*==============================================================*/
/* Table: CO_PESSOA_JURIDICA                                    */
/*==============================================================*/
CREATE TABLE CO_PESSOA_JURIDICA (
   SQ_PESSOA            NUMERIC(18)          NOT NULL,
   CLIENTE              NUMERIC(18)          NOT NULL,
   INICIO_ATIVIDADE     DATE                 NULL,
   CNPJ                 VARCHAR(20)          NULL,
   INSCRICAO_ESTADUAL   VARCHAR(20)          NULL,
   SEDE                 VARCHAR(1)           NOT NULL DEFAULT 'S'
      CONSTRAINT CKC_SEDE_CO_PESSO CHECK (SEDE IN ('S','N') AND SEDE = UPPER(SEDE)),
   INCLUSAO             DATE                 NOT NULL DEFAULT 'now()',
   CONSTRAINT PK_CO_PESSOA_JURIDICA PRIMARY KEY (SQ_PESSOA)
);

COMMENT ON TABLE CO_PESSOA_JURIDICA IS
'Armazena os dados específicos de pessoa jurídica';

COMMENT ON COLUMN CO_PESSOA_JURIDICA.SQ_PESSOA IS
'Chave de CO_PESSOA. Indica a que pessoa o registro está ligado.';

COMMENT ON COLUMN CO_PESSOA_JURIDICA.CLIENTE IS
'Chave de CO_PESSOA. Indica a que cliente o registro está ligado.';

COMMENT ON COLUMN CO_PESSOA_JURIDICA.INICIO_ATIVIDADE IS
'Inicio das atividades da pessoa jurídica';

COMMENT ON COLUMN CO_PESSOA_JURIDICA.CNPJ IS
'CNPJ da pessoa jurídica.';

COMMENT ON COLUMN CO_PESSOA_JURIDICA.INSCRICAO_ESTADUAL IS
'Inscrição estadual da pessoa jurídica.';

COMMENT ON COLUMN CO_PESSOA_JURIDICA.SEDE IS
'Indica se esta pessoa é a sede da empresa';

COMMENT ON COLUMN CO_PESSOA_JURIDICA.INCLUSAO IS
'Data de inclusão do registro na tabela.
';

/*==============================================================*/
/* Index: IN_COPESJUR_CNPJ                                      */
/*==============================================================*/
CREATE  INDEX IN_COPESJUR_CNPJ ON CO_PESSOA_JURIDICA (
CNPJ,
CLIENTE
);

/*==============================================================*/
/* Index: IN_COPESJUR_SEDE                                      */
/*==============================================================*/
CREATE  INDEX IN_COPESJUR_SEDE ON CO_PESSOA_JURIDICA (
SEDE,
CNPJ
);

/*==============================================================*/
/* Index: IN_COPESJUR_INIATI                                    */
/*==============================================================*/
CREATE  INDEX IN_COPESJUR_INIATI ON CO_PESSOA_JURIDICA (
INICIO_ATIVIDADE
);

/*==============================================================*/
/* Index: IN_COPESJUR_IE                                        */
/*==============================================================*/
CREATE  INDEX IN_COPESJUR_IE ON CO_PESSOA_JURIDICA (
INSCRICAO_ESTADUAL,
CNPJ
);

/*==============================================================*/
/* Index: IN_COPESJUR_CLI                                       */
/*==============================================================*/
CREATE  INDEX IN_COPESJUR_CLI ON CO_PESSOA_JURIDICA (
CLIENTE,
SQ_PESSOA
);

/*==============================================================*/
/* Table: CO_PESSOA_SEGMENTO                                    */
/*==============================================================*/
CREATE TABLE CO_PESSOA_SEGMENTO (
   SQ_PESSOA            NUMERIC(18)          NOT NULL,
   SQ_SEGMENTO          NUMERIC(18)          NOT NULL,
   CONSTRAINT PK_CO_PESSOA_SEGMENTO PRIMARY KEY (SQ_PESSOA, SQ_SEGMENTO)
);

COMMENT ON TABLE CO_PESSOA_SEGMENTO IS
'Armazena o segmento em que a pessoa se enquadra. É utilizado para definir as regras de negócio do SIW. Só pode haver um registro para cada pessoa.';

COMMENT ON COLUMN CO_PESSOA_SEGMENTO.SQ_PESSOA IS
'Chave de CO_PESSOA. Indica a que pessoa o registro está ligado.';

COMMENT ON COLUMN CO_PESSOA_SEGMENTO.SQ_SEGMENTO IS
'Chave de CO_SEGMENTO. Indica a que segmento o registro está ligado.';

/*==============================================================*/
/* Index: IN_COPESSEG_SQSEG                                     */
/*==============================================================*/
CREATE  INDEX IN_COPESSEG_SQSEG ON CO_PESSOA_SEGMENTO (
SQ_SEGMENTO
);

/*==============================================================*/
/* Table: CO_PESSOA_TELEFONE                                    */
/*==============================================================*/
CREATE TABLE CO_PESSOA_TELEFONE (
   SQ_PESSOA_TELEFONE   NUMERIC(18)          NOT NULL,
   SQ_PESSOA            NUMERIC(18)          NOT NULL,
   SQ_TIPO_TELEFONE     NUMERIC(18)          NOT NULL,
   SQ_CIDADE            NUMERIC(18)          NOT NULL,
   DDD                  VARCHAR(4)           NOT NULL,
   NUMERO               VARCHAR(25)          NOT NULL,
   PADRAO               VARCHAR(1)           NOT NULL DEFAULT 'N'
      CONSTRAINT CKC_PADRAO_COPES CHECK (PADRAO IN ('S','N') AND PADRAO = UPPER(PADRAO)),
   CONSTRAINT PK_CO_PESSOA_TELEFONE PRIMARY KEY (SQ_PESSOA_TELEFONE)
);

COMMENT ON TABLE CO_PESSOA_TELEFONE IS
'Armazena os endereços da pessoa';

COMMENT ON COLUMN CO_PESSOA_TELEFONE.SQ_PESSOA_TELEFONE IS
'Chave de CO_PESSOA_TELEFONE.';

COMMENT ON COLUMN CO_PESSOA_TELEFONE.SQ_PESSOA IS
'Chave de CO_PESSOA. Indica a que pessoa o registro está ligado.';

COMMENT ON COLUMN CO_PESSOA_TELEFONE.SQ_TIPO_TELEFONE IS
'Chave de CO_TIPO_TELEFONE. Indica a que tipo de telefone o registro está ligado.';

COMMENT ON COLUMN CO_PESSOA_TELEFONE.SQ_CIDADE IS
'Chave de CO_CIDADE. Indica a que cidade o registro está ligado.';

COMMENT ON COLUMN CO_PESSOA_TELEFONE.DDD IS
'DDD do telefone.';

COMMENT ON COLUMN CO_PESSOA_TELEFONE.NUMERO IS
'Número do telefone e ramal.';

COMMENT ON COLUMN CO_PESSOA_TELEFONE.PADRAO IS
'Indica se este registro deve ser apresentado como padrão para o usuário.';

/*==============================================================*/
/* Index: IN_COPESTEL_SQPES                                     */
/*==============================================================*/
CREATE  INDEX IN_COPESTEL_SQPES ON CO_PESSOA_TELEFONE (
SQ_PESSOA
);

/*==============================================================*/
/* Index: IN_COPESTEL_TPFONE                                    */
/*==============================================================*/
CREATE  INDEX IN_COPESTEL_TPFONE ON CO_PESSOA_TELEFONE (
SQ_TIPO_TELEFONE
);

/*==============================================================*/
/* Index: IN_COPESTEL_SQCID                                     */
/*==============================================================*/
CREATE  INDEX IN_COPESTEL_SQCID ON CO_PESSOA_TELEFONE (
SQ_CIDADE
);

/*==============================================================*/
/* Index: IN_COPESTEL_NUMERO                                    */
/*==============================================================*/
CREATE  INDEX IN_COPESTEL_NUMERO ON CO_PESSOA_TELEFONE (
NUMERO
);

/*==============================================================*/
/* Table: CO_REGIAO                                             */
/*==============================================================*/
CREATE TABLE CO_REGIAO (
   SQ_REGIAO            NUMERIC(18)          NOT NULL,
   SQ_PAIS              NUMERIC(18)          NOT NULL,
   NOME                 VARCHAR(20)          NOT NULL,
   SIGLA                VARCHAR(2)           NOT NULL,
   ORDEM                NUMERIC(4)           NOT NULL,
   CONSTRAINT PK_CO_REGIAO PRIMARY KEY (SQ_REGIAO)
);

COMMENT ON TABLE CO_REGIAO IS
'Armazena a tabela de regiões';

COMMENT ON COLUMN CO_REGIAO.SQ_REGIAO IS
'Chave de CO_REGIAO.';

COMMENT ON COLUMN CO_REGIAO.SQ_PAIS IS
'Chave de CO_PAIS. Indica a que país o registro está ligado.';

COMMENT ON COLUMN CO_REGIAO.NOME IS
'Nome da região.';

COMMENT ON COLUMN CO_REGIAO.SIGLA IS
'Sigla da região.';

COMMENT ON COLUMN CO_REGIAO.ORDEM IS
'Indica a ordem do registro nas listagens.';

/*==============================================================*/
/* Index: IN_COREGIAO_PAIS                                      */
/*==============================================================*/
CREATE  INDEX IN_COREGIAO_PAIS ON CO_REGIAO (
SQ_PAIS
);

/*==============================================================*/
/* Index: IN_COREGIAO_NOME                                      */
/*==============================================================*/
CREATE UNIQUE INDEX IN_COREGIAO_NOME ON CO_REGIAO (
NOME,
SQ_PAIS
);

/*==============================================================*/
/* Index: IN_COREGIAO_SIGLA                                     */
/*==============================================================*/
CREATE UNIQUE INDEX IN_COREGIAO_SIGLA ON CO_REGIAO (
SIGLA,
SQ_PAIS
);

/*==============================================================*/
/* Table: CO_SEGMENTO                                           */
/*==============================================================*/
CREATE TABLE CO_SEGMENTO (
   SQ_SEGMENTO          NUMERIC(18)          NOT NULL,
   NOME                 VARCHAR(40)          NOT NULL,
   ATIVO                VARCHAR(1)           NOT NULL DEFAULT 'S'
      CONSTRAINT CKC_ATIVO_CO_SEGME CHECK (ATIVO IN ('S','N') AND ATIVO = UPPER(ATIVO)),
   PADRAO               VARCHAR(1)           NOT NULL DEFAULT 'N'
      CONSTRAINT CKC_PADRAO_COSEG CHECK (PADRAO IN ('S','N') AND PADRAO = UPPER(PADRAO)),
   CONSTRAINT PK_CO_SEGMENTO PRIMARY KEY (SQ_SEGMENTO)
);

COMMENT ON TABLE CO_SEGMENTO IS
'Armazena a tabela de segmentos onde as pessoas jurídicas se enquadram. Pode ser organismo internacional, órgão público, comércio varejista, franquias, associações etc.';

COMMENT ON COLUMN CO_SEGMENTO.SQ_SEGMENTO IS
'Chave de CO_SEGMENTO.';

COMMENT ON COLUMN CO_SEGMENTO.NOME IS
'Nome do segmento.';

COMMENT ON COLUMN CO_SEGMENTO.ATIVO IS
'Indica se o registro deve ou não ser exibido.';

COMMENT ON COLUMN CO_SEGMENTO.PADRAO IS
'Indica se este registro deve ser apresentado como padrão para o usuário.';

/*==============================================================*/
/* Index: IN_COSEG_NOME                                         */
/*==============================================================*/
CREATE UNIQUE INDEX IN_COSEG_NOME ON CO_SEGMENTO (
NOME
);

/*==============================================================*/
/* Index: IN_COSEG_ATIVO                                        */
/*==============================================================*/
CREATE  INDEX IN_COSEG_ATIVO ON CO_SEGMENTO (
ATIVO
);

/*==============================================================*/
/* Index: IN_COSEG_PADRAO                                       */
/*==============================================================*/
CREATE  INDEX IN_COSEG_PADRAO ON CO_SEGMENTO (
PADRAO
);

/*==============================================================*/
/* Table: CO_TIPO_ENDERECO                                      */
/*==============================================================*/
CREATE TABLE CO_TIPO_ENDERECO (
   SQ_TIPO_ENDERECO     NUMERIC(18)          NOT NULL,
   SQ_TIPO_PESSOA       NUMERIC(18)          NOT NULL,
   NOME                 VARCHAR(30)          NOT NULL,
   ATIVO                VARCHAR(1)           NOT NULL DEFAULT 'S'
      CONSTRAINT CKC_ATIVO_COTIPEND CHECK (ATIVO IN ('S','N') AND ATIVO = UPPER(ATIVO)),
   PADRAO               VARCHAR(1)           NOT NULL DEFAULT 'N'
      CONSTRAINT CKC_COTIPEND_PAD
               CHECK (PADRAO IN ('S','N') AND PADRAO = UPPER(PADRAO)),
   EMAIL                VARCHAR(1)           NOT NULL DEFAULT 'N'
      CONSTRAINT CKC_EMAIL_CO_TIPO_ CHECK (EMAIL IN ('S','N') AND EMAIL = UPPER(EMAIL)),
   INTERNET             VARCHAR(1)           NOT NULL DEFAULT 'N'
      CONSTRAINT CKC_COTIPEND_WEB
               CHECK (INTERNET IN ('S','N') AND INTERNET = UPPER(INTERNET)),
   CODIGO_EXTERNO       VARCHAR(60)          NULL,
   CONSTRAINT PK_CO_TIPO_ENDERECO PRIMARY KEY (SQ_TIPO_ENDERECO)
);

COMMENT ON TABLE CO_TIPO_ENDERECO IS
'Armazena os tipos de endereço';

COMMENT ON COLUMN CO_TIPO_ENDERECO.SQ_TIPO_ENDERECO IS
'Chave de CO_TIPO_ENDERECO.';

COMMENT ON COLUMN CO_TIPO_ENDERECO.SQ_TIPO_PESSOA IS
'Chave de CO_TIPO_PESSOA. Indica a que tipo de pessoa o registro está ligado.';

COMMENT ON COLUMN CO_TIPO_ENDERECO.NOME IS
'Nome do tipo de endereço.';

COMMENT ON COLUMN CO_TIPO_ENDERECO.ATIVO IS
'Indica se o registro deve ou não ser exibido.';

COMMENT ON COLUMN CO_TIPO_ENDERECO.PADRAO IS
'Indica se este registro deve ser apresentado como padrão para o usuário.';

COMMENT ON COLUMN CO_TIPO_ENDERECO.EMAIL IS
'Indica se o endereço é de e-mail.';

COMMENT ON COLUMN CO_TIPO_ENDERECO.INTERNET IS
'Indica se o endereço é de Internet.';

COMMENT ON COLUMN CO_TIPO_ENDERECO.CODIGO_EXTERNO IS
'Código desse registro num sistema externo.';

/*==============================================================*/
/* Index: IN_COTIPEND_TPPES                                     */
/*==============================================================*/
CREATE  INDEX IN_COTIPEND_TPPES ON CO_TIPO_ENDERECO (
SQ_TIPO_PESSOA
);

/*==============================================================*/
/* Index: IN_COTIPEND_ATIVO                                     */
/*==============================================================*/
CREATE  INDEX IN_COTIPEND_ATIVO ON CO_TIPO_ENDERECO (
ATIVO
);

/*==============================================================*/
/* Index: IN_COTIPEND_PADRAO                                    */
/*==============================================================*/
CREATE  INDEX IN_COTIPEND_PADRAO ON CO_TIPO_ENDERECO (
PADRAO
);

/*==============================================================*/
/* Index: IN_COTIPEND_EMAIL                                     */
/*==============================================================*/
CREATE  INDEX IN_COTIPEND_EMAIL ON CO_TIPO_ENDERECO (
EMAIL
);

/*==============================================================*/
/* Index: IN_COTIPEND_WEB                                       */
/*==============================================================*/
CREATE  INDEX IN_COTIPEND_WEB ON CO_TIPO_ENDERECO (
INTERNET
);

/*==============================================================*/
/* Index: IN_COTIPEND_EXTERNO                                   */
/*==============================================================*/
CREATE  INDEX IN_COTIPEND_EXTERNO ON CO_TIPO_ENDERECO (
CODIGO_EXTERNO,
SQ_TIPO_ENDERECO
);

/*==============================================================*/
/* Table: CO_TIPO_PESSOA                                        */
/*==============================================================*/
CREATE TABLE CO_TIPO_PESSOA (
   SQ_TIPO_PESSOA       NUMERIC(18)          NOT NULL,
   NOME                 VARCHAR(60)          NULL,
   ATIVO                VARCHAR(1)           NOT NULL DEFAULT 'S'
      CONSTRAINT CKC_ATIVO_COTIPPES CHECK (ATIVO IN ('S','N') AND ATIVO = UPPER(ATIVO)),
   PADRAO               VARCHAR(1)           NOT NULL DEFAULT 'N'
      CONSTRAINT CKC_COTIPPES_PAD
               CHECK (PADRAO IN ('S','N') AND PADRAO = UPPER(PADRAO)),
   CONSTRAINT PK_CO_TIPO_PESSOA PRIMARY KEY (SQ_TIPO_PESSOA)
);

COMMENT ON TABLE CO_TIPO_PESSOA IS
'Armazena os tipos de pessoa';

COMMENT ON COLUMN CO_TIPO_PESSOA.SQ_TIPO_PESSOA IS
'Chave de CO_TIPO_PESSOA.';

COMMENT ON COLUMN CO_TIPO_PESSOA.NOME IS
'Nome do tipo de pessoa.';

COMMENT ON COLUMN CO_TIPO_PESSOA.ATIVO IS
'Indica se o registro deve ou não ser exibido.';

COMMENT ON COLUMN CO_TIPO_PESSOA.PADRAO IS
'Indica se este registro deve ser apresentado como padrão para o usuário.';

/*==============================================================*/
/* Index: IN_COTIPPES_ATIVO                                     */
/*==============================================================*/
CREATE  INDEX IN_COTIPPES_ATIVO ON CO_TIPO_PESSOA (
ATIVO
);

/*==============================================================*/
/* Index: IN_COTIPPES_PADRAO                                    */
/*==============================================================*/
CREATE  INDEX IN_COTIPPES_PADRAO ON CO_TIPO_PESSOA (
PADRAO
);

/*==============================================================*/
/* Table: CO_TIPO_TELEFONE                                      */
/*==============================================================*/
CREATE TABLE CO_TIPO_TELEFONE (
   SQ_TIPO_TELEFONE     NUMERIC(18)          NOT NULL,
   SQ_TIPO_PESSOA       NUMERIC(18)          NOT NULL,
   NOME                 VARCHAR(25)          NOT NULL,
   ATIVO                VARCHAR(1)           NOT NULL DEFAULT 'S'
      CONSTRAINT CKC_ATIVO_COTIPTEL CHECK (ATIVO IN ('S','N') AND ATIVO = UPPER(ATIVO)),
   PADRAO               VARCHAR(1)           NOT NULL DEFAULT 'N'
      CONSTRAINT CKC_COTIPTEL_PAD
               CHECK (PADRAO IN ('S','N') AND PADRAO = UPPER(PADRAO)),
   CONSTRAINT PK_CO_TIPO_TELEFONE PRIMARY KEY (SQ_TIPO_TELEFONE)
);

COMMENT ON TABLE CO_TIPO_TELEFONE IS
'Armazena os tipos de telefone';

COMMENT ON COLUMN CO_TIPO_TELEFONE.SQ_TIPO_TELEFONE IS
'Chave de CO_TIPO_TELEFONE.';

COMMENT ON COLUMN CO_TIPO_TELEFONE.SQ_TIPO_PESSOA IS
'Chave de CO_TIPO_PESSOA. Indica a que tipo de pessoa o registro está ligado.';

COMMENT ON COLUMN CO_TIPO_TELEFONE.NOME IS
'Nome do tipo de telefone.';

COMMENT ON COLUMN CO_TIPO_TELEFONE.ATIVO IS
'Indica se o registro deve ou não ser exibido.';

COMMENT ON COLUMN CO_TIPO_TELEFONE.PADRAO IS
'Indica se este registro deve ser apresentado como padrão para o usuário.';

/*==============================================================*/
/* Index: IN_COTIPTEL_TPPES                                     */
/*==============================================================*/
CREATE  INDEX IN_COTIPTEL_TPPES ON CO_TIPO_TELEFONE (
SQ_TIPO_PESSOA
);

/*==============================================================*/
/* Table: CO_TIPO_VINCULO                                       */
/*==============================================================*/
CREATE TABLE CO_TIPO_VINCULO (
   SQ_TIPO_VINCULO      NUMERIC(18)          NOT NULL,
   SQ_TIPO_PESSOA       NUMERIC(18)          NOT NULL,
   CLIENTE              NUMERIC(18)          NULL,
   NOME                 VARCHAR(21)          NOT NULL,
   INTERNO              VARCHAR(1)           NOT NULL DEFAULT 'N'
      CONSTRAINT CKC_COTIPVIN_INT
               CHECK (INTERNO IN ('S','N') AND INTERNO = UPPER(INTERNO)),
   ATIVO                VARCHAR(1)           NOT NULL DEFAULT 'S'
      CONSTRAINT CKC_ATIVO_COTIPVIN CHECK (ATIVO IN ('S','N') AND ATIVO = UPPER(ATIVO)),
   PADRAO               VARCHAR(1)           NOT NULL DEFAULT 'N'
      CONSTRAINT CKC_COTIPVIN_PAD
               CHECK (PADRAO IN ('S','N') AND PADRAO = UPPER(PADRAO)),
   CONTRATADO           VARCHAR(1)           NOT NULL DEFAULT 'N'
      CONSTRAINT CKC_COTIPVIN_CONT
               CHECK (CONTRATADO IN ('S','N') AND CONTRATADO = UPPER(CONTRATADO)),
   ORDEM                NUMERIC(6)           NULL,
   CODIGO_EXTERNO       VARCHAR(60)          NULL,
   ENVIA_MAIL_TRAMITE   VARCHAR(1)           NOT NULL DEFAULT 'S'
      CONSTRAINT CKC_ENVIA_MAIL_TRAMIT_CO_TIPO_ CHECK (ENVIA_MAIL_TRAMITE IN ('S','N') AND ENVIA_MAIL_TRAMITE = UPPER(ENVIA_MAIL_TRAMITE)),
   ENVIA_MAIL_ALERTA    VARCHAR(1)           NOT NULL DEFAULT 'S'
      CONSTRAINT CKC_ENVIA_MAIL_ALERTA_CO_TIPO_ CHECK (ENVIA_MAIL_ALERTA IN ('S','N') AND ENVIA_MAIL_ALERTA = UPPER(ENVIA_MAIL_ALERTA)),
   CONSTRAINT PK_CO_TIPO_VINCULO PRIMARY KEY (SQ_TIPO_VINCULO)
);

COMMENT ON TABLE CO_TIPO_VINCULO IS
'Armazena os tipos de vinculo entre pessoas físicas e jurídicas';

COMMENT ON COLUMN CO_TIPO_VINCULO.SQ_TIPO_VINCULO IS
'Chave de CO_TIPO_VINCULO.';

COMMENT ON COLUMN CO_TIPO_VINCULO.SQ_TIPO_PESSOA IS
'Chave de CO_TIPO_PESSOA. Indica a que tipo de pessoa o registro está ligado.';

COMMENT ON COLUMN CO_TIPO_VINCULO.CLIENTE IS
'Chave de CO_PESSOA. Indica a que cliente o registro está ligado.';

COMMENT ON COLUMN CO_TIPO_VINCULO.NOME IS
'Nome do tipo de vínculo.';

COMMENT ON COLUMN CO_TIPO_VINCULO.INTERNO IS
'Indica se o vínculo é interno à organização.';

COMMENT ON COLUMN CO_TIPO_VINCULO.ATIVO IS
'Indica se o registro deve ou não ser exibido.';

COMMENT ON COLUMN CO_TIPO_VINCULO.PADRAO IS
'Indica se este registro deve ser apresentado como padrão para o usuário.';

COMMENT ON COLUMN CO_TIPO_VINCULO.CONTRATADO IS
'Indica se a pessoa é contratada ou não pela organização.';

COMMENT ON COLUMN CO_TIPO_VINCULO.ORDEM IS
'Indica a ordem do registro nas listagens.';

COMMENT ON COLUMN CO_TIPO_VINCULO.CODIGO_EXTERNO IS
'Código desse registro num sistema externo.';

COMMENT ON COLUMN CO_TIPO_VINCULO.ENVIA_MAIL_TRAMITE IS
'Indica se usuários desse vínculo devem ser receber e-mail de alerta quando houver tramitação ou conclusão das solicitações.';

COMMENT ON COLUMN CO_TIPO_VINCULO.ENVIA_MAIL_ALERTA IS
'Indica se usuários desse vínculo devem receber e-mail de alerta de atraso ou proximidade.';

/*==============================================================*/
/* Index: IN_COTIPVIN_TPPES                                     */
/*==============================================================*/
CREATE  INDEX IN_COTIPVIN_TPPES ON CO_TIPO_VINCULO (
CLIENTE,
SQ_TIPO_PESSOA,
SQ_TIPO_VINCULO
);

/*==============================================================*/
/* Index: IN_COTIPVIN_ATIVO                                     */
/*==============================================================*/
CREATE  INDEX IN_COTIPVIN_ATIVO ON CO_TIPO_VINCULO (
CLIENTE,
ATIVO,
SQ_TIPO_VINCULO
);

/*==============================================================*/
/* Index: IN_COTIPVIN_PADRAO                                    */
/*==============================================================*/
CREATE  INDEX IN_COTIPVIN_PADRAO ON CO_TIPO_VINCULO (
CLIENTE,
PADRAO,
SQ_TIPO_VINCULO
);

/*==============================================================*/
/* Index: IN_COTIPVIN_INT                                       */
/*==============================================================*/
CREATE  INDEX IN_COTIPVIN_INT ON CO_TIPO_VINCULO (
CLIENTE,
INTERNO,
SQ_TIPO_VINCULO
);

/*==============================================================*/
/* Index: IN_COTIPVIN_CONTR                                     */
/*==============================================================*/
CREATE  INDEX IN_COTIPVIN_CONTR ON CO_TIPO_VINCULO (
CLIENTE,
CONTRATADO,
SQ_TIPO_VINCULO
);

/*==============================================================*/
/* Index: IN_COTIPVIN_EXTERNO                                   */
/*==============================================================*/
CREATE  INDEX IN_COTIPVIN_EXTERNO ON CO_TIPO_VINCULO (
CLIENTE,
CODIGO_EXTERNO,
SQ_TIPO_VINCULO
);

/*==============================================================*/
/* Table: CO_UF                                                 */
/*==============================================================*/
CREATE TABLE CO_UF (
   CO_UF                VARCHAR(3)           NOT NULL,
   SQ_PAIS              NUMERIC(18)          NOT NULL,
   SQ_REGIAO            NUMERIC(18)          NOT NULL,
   NOME                 VARCHAR(30)          NOT NULL,
   ATIVO                VARCHAR(1)           NOT NULL DEFAULT 'S'
      CONSTRAINT CKC_ATIVO_CO_UF CHECK (ATIVO IN ('S','N') AND ATIVO = UPPER(ATIVO)),
   PADRAO               VARCHAR(1)           NOT NULL DEFAULT 'N'
      CONSTRAINT CKC_PADRAO_COUF CHECK (PADRAO IN ('S','N') AND PADRAO = UPPER(PADRAO)),
   CODIGO_IBGE          VARCHAR(2)           NULL,
   ORDEM                NUMERIC(5)           NULL,
   CONSTRAINT PK_CO_UF PRIMARY KEY (CO_UF, SQ_PAIS)
);

COMMENT ON TABLE CO_UF IS
'Armazena a tabela de estados';

COMMENT ON COLUMN CO_UF.CO_UF IS
'Chave de CO_UF.';

COMMENT ON COLUMN CO_UF.SQ_PAIS IS
'Chave de CO_PAIS. Indica a que país o registro está ligado.';

COMMENT ON COLUMN CO_UF.SQ_REGIAO IS
'Chave de CO_REGIAO. Indica a que região o registro está ligado.';

COMMENT ON COLUMN CO_UF.NOME IS
'Nome da unidade da federação.';

COMMENT ON COLUMN CO_UF.ATIVO IS
'Indica se o registro deve ou não ser exibido.';

COMMENT ON COLUMN CO_UF.PADRAO IS
'Indica se este registro deve ser apresentado como padrão para o usuário.';

COMMENT ON COLUMN CO_UF.CODIGO_IBGE IS
'Código IBGE da UF. Este código abrange a região e a UF.';

COMMENT ON COLUMN CO_UF.ORDEM IS
'Indica a ordem do registro nas listagens.';

/*==============================================================*/
/* Index: IN_COUF_PAISREGIAO                                    */
/*==============================================================*/
CREATE  INDEX IN_COUF_PAISREGIAO ON CO_UF (
SQ_REGIAO,
SQ_PAIS
);

/*==============================================================*/
/* Index: IN_COUF_NOME                                          */
/*==============================================================*/
CREATE UNIQUE INDEX IN_COUF_NOME ON CO_UF (
NOME,
SQ_PAIS
);

/*==============================================================*/
/* Table: CO_UNIDADE_MEDIDA                                     */
/*==============================================================*/
CREATE TABLE CO_UNIDADE_MEDIDA (
   SQ_UNIDADE_MEDIDA    NUMERIC(18)          NOT NULL,
   CLIENTE              NUMERIC(18)          NOT NULL,
   NOME                 VARCHAR(30)          NOT NULL,
   SIGLA                VARCHAR(10)          NOT NULL,
   ATIVO                VARCHAR(1)           NOT NULL,
   CONSTRAINT PK_CO_UNIDADE_MEDIDA PRIMARY KEY (SQ_UNIDADE_MEDIDA)
);

COMMENT ON TABLE CO_UNIDADE_MEDIDA IS
'Registra as unidades de medida.';

COMMENT ON COLUMN CO_UNIDADE_MEDIDA.SQ_UNIDADE_MEDIDA IS
'Chave de CO_UNIDADE_MEDIDA.';

COMMENT ON COLUMN CO_UNIDADE_MEDIDA.CLIENTE IS
'Chave de CO_PESSOA. Indica a que cliente a unidade de medida está vinculada.';

COMMENT ON COLUMN CO_UNIDADE_MEDIDA.NOME IS
'Nome da unidade de medida.';

COMMENT ON COLUMN CO_UNIDADE_MEDIDA.SIGLA IS
'Sigla da unidade de medida.';

COMMENT ON COLUMN CO_UNIDADE_MEDIDA.ATIVO IS
'Indica se a unidade de medida pode ser vinculaao a novos registros.';

/*==============================================================*/
/* Index: IN_COUNIMED_CLIENTE                                   */
/*==============================================================*/
CREATE  INDEX IN_COUNIMED_CLIENTE ON CO_UNIDADE_MEDIDA (
CLIENTE,
SQ_UNIDADE_MEDIDA
);

/*==============================================================*/
/* Index: IN_COUNIMED_NOME                                      */
/*==============================================================*/
CREATE  INDEX IN_COUNIMED_NOME ON CO_UNIDADE_MEDIDA (
CLIENTE,
NOME,
SQ_UNIDADE_MEDIDA
);

/*==============================================================*/
/* Index: IN_COUNIMED_SIGLA                                     */
/*==============================================================*/
CREATE  INDEX IN_COUNIMED_SIGLA ON CO_UNIDADE_MEDIDA (
CLIENTE,
SIGLA,
SQ_UNIDADE_MEDIDA
);

/*==============================================================*/
/* Index: IN_COUNIMED_ATIVO                                     */
/*==============================================================*/
CREATE  INDEX IN_COUNIMED_ATIVO ON CO_UNIDADE_MEDIDA (
CLIENTE,
ATIVO,
SQ_UNIDADE_MEDIDA
);

/*==============================================================*/
/* Table: DC_ARQUIVO                                            */
/*==============================================================*/
CREATE TABLE DC_ARQUIVO (
   SQ_ARQUIVO           NUMERIC(18)          NOT NULL,
   SQ_SISTEMA           NUMERIC(18)          NOT NULL,
   NOME                 VARCHAR(40)          NOT NULL,
   DESCRICAO            VARCHAR(4000)        NOT NULL,
   TIPO                 VARCHAR(1)           NOT NULL DEFAULT 'G'
      CONSTRAINT CKC_TIPO_DC_ARQUI CHECK (TIPO IN ('G','I','C','R')),
   DIRETORIO            VARCHAR(100)         NULL,
   CONSTRAINT PK_DC_ARQUIVO PRIMARY KEY (SQ_ARQUIVO)
);

COMMENT ON TABLE DC_ARQUIVO IS
'Armazena os arquivos de um sistema.';

COMMENT ON COLUMN DC_ARQUIVO.SQ_ARQUIVO IS
'Chave de SIW_ARQUIVO. Indica a que arquivo o registro está ligado.';

COMMENT ON COLUMN DC_ARQUIVO.SQ_SISTEMA IS
'Chave de DC_SISTEMA. Indica a que sistema o registro está ligado.';

COMMENT ON COLUMN DC_ARQUIVO.NOME IS
'Nome do arquivo.';

COMMENT ON COLUMN DC_ARQUIVO.DESCRICAO IS
'Descrição do arquivo.';

COMMENT ON COLUMN DC_ARQUIVO.TIPO IS
'Armazena o tipo do arquivo (G - rotinas genéricas; I - inclusão; C - configuração; R - requisitos)';

COMMENT ON COLUMN DC_ARQUIVO.DIRETORIO IS
'Diretório onde o arquivo encontra-se.';

/*==============================================================*/
/* Index: IN_DCARQ_SISTEMA                                      */
/*==============================================================*/
CREATE  INDEX IN_DCARQ_SISTEMA ON DC_ARQUIVO (
SQ_SISTEMA,
SQ_ARQUIVO
);

/*==============================================================*/
/* Index: IN_DCARQ_NOME                                         */
/*==============================================================*/
CREATE UNIQUE INDEX IN_DCARQ_NOME ON DC_ARQUIVO (
NOME,
DIRETORIO,
SQ_SISTEMA
);

/*==============================================================*/
/* Table: DC_COLUNA                                             */
/*==============================================================*/
CREATE TABLE DC_COLUNA (
   SQ_COLUNA            NUMERIC(18)          NOT NULL,
   SQ_TABELA            NUMERIC(18)          NOT NULL,
   SQ_DADO_TIPO         NUMERIC(18)          NOT NULL,
   NOME                 VARCHAR(30)          NOT NULL,
   DESCRICAO            VARCHAR(4000)        NOT NULL,
   ORDEM                NUMERIC(18)          NULL,
   TAMANHO              NUMERIC(18)          NOT NULL,
   PRECISAO             NUMERIC(18)          NULL,
   ESCALA               NUMERIC(18)          NULL,
   OBRIGATORIO          VARCHAR(1)           NOT NULL DEFAULT 'N'
      CONSTRAINT CKC_OBRIGATORIO_DC_COLUN CHECK (OBRIGATORIO IN ('S','N') AND OBRIGATORIO = UPPER(OBRIGATORIO)),
   VALOR_PADRAO         VARCHAR(255)         NULL,
   CONSTRAINT PK_DC_COLUNA PRIMARY KEY (SQ_COLUNA)
);

COMMENT ON TABLE DC_COLUNA IS
'Armazena dados das colunas.';

COMMENT ON COLUMN DC_COLUNA.SQ_COLUNA IS
'Chave de DC_COLUNA.';

COMMENT ON COLUMN DC_COLUNA.SQ_TABELA IS
'Chave de DC_TABELA. Indica a que tabela o registro está ligado.';

COMMENT ON COLUMN DC_COLUNA.SQ_DADO_TIPO IS
'Chave de DC_DADO_TIPO. Indica a que tipo de dado o registro está ligado.';

COMMENT ON COLUMN DC_COLUNA.NOME IS
'Nome da coluna.';

COMMENT ON COLUMN DC_COLUNA.DESCRICAO IS
'Finalidade da coluna.';

COMMENT ON COLUMN DC_COLUNA.ORDEM IS
'Número de ordem da coluna na tabela.';

COMMENT ON COLUMN DC_COLUNA.TAMANHO IS
'Tamanho da coluna, em bytes.';

COMMENT ON COLUMN DC_COLUNA.PRECISAO IS
'Número de casas decimais quando for a coluna for do tipo numérico';

COMMENT ON COLUMN DC_COLUNA.ESCALA IS
'Número de dígitos à direita da vírgula decimal, quando a coluna for do tipo numérico.';

COMMENT ON COLUMN DC_COLUNA.OBRIGATORIO IS
'Indica se o campo é de preenchimento obrigqatório.';

COMMENT ON COLUMN DC_COLUNA.VALOR_PADRAO IS
'Valor da coluna, caso não seja especificado um.';

/*==============================================================*/
/* Index: IN_DCCOL_TIPO                                         */
/*==============================================================*/
CREATE  INDEX IN_DCCOL_TIPO ON DC_COLUNA (
SQ_DADO_TIPO,
SQ_COLUNA
);

/*==============================================================*/
/* Index: IN_DCCOL_TABELA                                       */
/*==============================================================*/
CREATE  INDEX IN_DCCOL_TABELA ON DC_COLUNA (
SQ_TABELA,
SQ_COLUNA
);

/*==============================================================*/
/* Index: IN_DCCOL_NOME                                         */
/*==============================================================*/
CREATE UNIQUE INDEX IN_DCCOL_NOME ON DC_COLUNA (
NOME,
SQ_TABELA
);

/*==============================================================*/
/* Table: DC_DADO_TIPO                                          */
/*==============================================================*/
CREATE TABLE DC_DADO_TIPO (
   SQ_DADO_TIPO         NUMERIC(18)          NOT NULL,
   NOME                 VARCHAR(30)          NOT NULL,
   DESCRICAO            VARCHAR(4000)        NOT NULL,
   CONSTRAINT PK_DC_DADO_TIPO PRIMARY KEY (SQ_DADO_TIPO)
);

COMMENT ON TABLE DC_DADO_TIPO IS
'Armazena os tipos de dado válidos.';

COMMENT ON COLUMN DC_DADO_TIPO.SQ_DADO_TIPO IS
'Chave de DC_DADO_TIPO.';

COMMENT ON COLUMN DC_DADO_TIPO.NOME IS
'Nome do tipo de dado.';

COMMENT ON COLUMN DC_DADO_TIPO.DESCRICAO IS
'Descrição do tipo de dado.';

/*==============================================================*/
/* Index: IN_DCDADTIP_NOME                                      */
/*==============================================================*/
CREATE UNIQUE INDEX IN_DCDADTIP_NOME ON DC_DADO_TIPO (
NOME
);

/*==============================================================*/
/* Table: DC_ESQUEMA                                            */
/*==============================================================*/
CREATE TABLE DC_ESQUEMA (
   SQ_ESQUEMA           NUMERIC(18)          NOT NULL,
   CLIENTE              NUMERIC(18)          NOT NULL,
   SQ_MODULO            NUMERIC(18)          NOT NULL,
   NOME                 VARCHAR(60)          NOT NULL,
   DESCRICAO            VARCHAR(500)         NULL,
   TIPO                 VARCHAR(1)           NOT NULL DEFAULT 'I'
      CONSTRAINT CKC_TIPO_DC_ESQUE CHECK (TIPO IN ('I','E')),
   ATIVO                VARCHAR(1)           NOT NULL DEFAULT 'S'
      CONSTRAINT CKC_ATIVO_DC_ESQUE CHECK (ATIVO IN ('S','N') AND ATIVO = UPPER(ATIVO)),
   FORMATO              VARCHAR(1)           NOT NULL DEFAULT 'A'
      CONSTRAINT CKC_FORMATO_DC_ESQUE CHECK (FORMATO IN ('A','W','T')),
   WS_SERVIDOR          VARCHAR(100)         NULL,
   WS_URL               VARCHAR(100)         NULL,
   WS_ACAO              VARCHAR(100)         NULL,
   WS_MENSAGEM          VARCHAR(4000)        NULL,
   NO_RAIZ              VARCHAR(50)          NULL,
   BD_HOSTNAME          VARCHAR(50)          NULL,
   BD_USERNAME          VARCHAR(50)          NULL,
   BD_PASSWORD          VARCHAR(50)          NULL,
   TX_DELIMITADOR       VARCHAR(5)           NULL,
   TIPO_EFETIVACAO      NUMERIC(1)           NOT NULL DEFAULT 0
      CONSTRAINT CKC_TIPO_EFETIVACAO_DC_ESQUE CHECK (TIPO_EFETIVACAO IN (0,1)),
   TX_ORIGEM_ARQUIVOS   NUMERIC(1)           NULL DEFAULT 0
      CONSTRAINT CKC_TX_ORIGEM_ARQUIVO_DC_ESQUE CHECK (TX_ORIGEM_ARQUIVOS IS NULL OR (TX_ORIGEM_ARQUIVOS IN (0,1))),
   FTP_HOSTNAME         VARCHAR(50)          NULL,
   FTP_USERNAME         VARCHAR(50)          NULL,
   FTP_PASSWORD         VARCHAR(50)          NULL,
   FTP_DIRETORIO        VARCHAR(100)         NULL,
   ENVIA_MAIL           NUMERIC(1)           NULL DEFAULT 0
      CONSTRAINT CKC_ENVIA_MAIL_DC_ESQUE CHECK (ENVIA_MAIL IS NULL OR (ENVIA_MAIL IN (0,1,2))),
   LISTA_MAIL           VARCHAR(255)         NULL,
   CONSTRAINT PK_DC_ESQUEMA PRIMARY KEY (SQ_ESQUEMA)
);

COMMENT ON TABLE DC_ESQUEMA IS
'Registra os esquemas de importação e exprotação.';

COMMENT ON COLUMN DC_ESQUEMA.SQ_ESQUEMA IS
'Chave de DC_ESQUEMA.';

COMMENT ON COLUMN DC_ESQUEMA.CLIENTE IS
'Chave de CO_PESSOA. Indica a que cliente o esquema pertence.';

COMMENT ON COLUMN DC_ESQUEMA.SQ_MODULO IS
'Chave de SIW_MODULO. Indica a que módulo o esquema está vinculado.';

COMMENT ON COLUMN DC_ESQUEMA.NOME IS
'Nome do esquema.';

COMMENT ON COLUMN DC_ESQUEMA.DESCRICAO IS
'Descrição do esquema.';

COMMENT ON COLUMN DC_ESQUEMA.TIPO IS
'Indica o tipo do esquema. I para importação, E para exportação.';

COMMENT ON COLUMN DC_ESQUEMA.ATIVO IS
'Indica se esta tabela deve ser tratada em novas rotinas de importação ou exportação.';

COMMENT ON COLUMN DC_ESQUEMA.FORMATO IS
'Indica o formato do esquema: Arquivo (A), web service (W) ou TXT delimitado (T).';

COMMENT ON COLUMN DC_ESQUEMA.WS_SERVIDOR IS
'Servidor onde o Web Service está instalado.';

COMMENT ON COLUMN DC_ESQUEMA.WS_URL IS
'URL do web service.';

COMMENT ON COLUMN DC_ESQUEMA.WS_ACAO IS
'Ação SOAP a ser executada.';

COMMENT ON COLUMN DC_ESQUEMA.WS_MENSAGEM IS
'Mensagem SOAP a ser enviada para o Web Service.';

COMMENT ON COLUMN DC_ESQUEMA.NO_RAIZ IS
'Tag do esquema no arquivo XML.';

COMMENT ON COLUMN DC_ESQUEMA.BD_HOSTNAME IS
'Servidor de destino para importação de dados.';

COMMENT ON COLUMN DC_ESQUEMA.BD_USERNAME IS
'Usuário de destino para importação de dados.';

COMMENT ON COLUMN DC_ESQUEMA.BD_PASSWORD IS
'Senha do usuário de destino para importação de dados.';

COMMENT ON COLUMN DC_ESQUEMA.TX_DELIMITADOR IS
'Delimitador a ser usado nos arquivos de importação.';

COMMENT ON COLUMN DC_ESQUEMA.TIPO_EFETIVACAO IS
'(0) efetiva mesmo que haja algum registro errado (1) efetiva somente se não achar registro errado.';

COMMENT ON COLUMN DC_ESQUEMA.TX_ORIGEM_ARQUIVOS IS
'Indica como os arquivos TXT devem ser obtidos. (0) Do diretório padrão (1) De servidor FTP.';

COMMENT ON COLUMN DC_ESQUEMA.FTP_HOSTNAME IS
'Endereço do servidor FTP a ser usado para obter os arquivos TXT.';

COMMENT ON COLUMN DC_ESQUEMA.FTP_USERNAME IS
'Username do servidor FTP a ser usado para obter os arquivos TXT.';

COMMENT ON COLUMN DC_ESQUEMA.FTP_PASSWORD IS
'Senha do servidor FTP a ser usado para obter os arquivos TXT.';

COMMENT ON COLUMN DC_ESQUEMA.FTP_DIRETORIO IS
'Diretório do servidor FTP a ser usado para obter os arquivos TXT.';

COMMENT ON COLUMN DC_ESQUEMA.ENVIA_MAIL IS
'Define forma de envio de e-mails. (0) Não envia (1) Envia sempre (2) Envia somente em caso de insucesso';

COMMENT ON COLUMN DC_ESQUEMA.LISTA_MAIL IS
'Endereços para envio de e-mails.';

/*==============================================================*/
/* Index: IN_DCESQ_CLIENTE                                      */
/*==============================================================*/
CREATE  INDEX IN_DCESQ_CLIENTE ON DC_ESQUEMA (
CLIENTE,
SQ_ESQUEMA
);

/*==============================================================*/
/* Index: IN_DCESQ_MODULO                                       */
/*==============================================================*/
CREATE  INDEX IN_DCESQ_MODULO ON DC_ESQUEMA (
CLIENTE,
SQ_MODULO,
SQ_ESQUEMA
);

/*==============================================================*/
/* Index: IN_DCESQ_NOME                                         */
/*==============================================================*/
CREATE UNIQUE INDEX IN_DCESQ_NOME ON DC_ESQUEMA (
CLIENTE,
NOME,
SQ_MODULO
);

/*==============================================================*/
/* Table: DC_ESQUEMA_ATRIBUTO                                   */
/*==============================================================*/
CREATE TABLE DC_ESQUEMA_ATRIBUTO (
   SQ_ESQUEMA_ATRIBUTO  NUMERIC(18)          NOT NULL,
   SQ_ESQUEMA_TABELA    NUMERIC(18)          NOT NULL,
   SQ_COLUNA            NUMERIC(18)          NOT NULL,
   ORDEM                NUMERIC(4)           NOT NULL DEFAULT 0,
   CAMPO_EXTERNO        VARCHAR(30)          NOT NULL,
   MASCARA_DATA         VARCHAR(50)          NULL,
   VALOR_DEFAULT        VARCHAR(50)          NULL,
   CONSTRAINT PK_DC_ESQUEMA_ATRIBUTO PRIMARY KEY (SQ_ESQUEMA_ATRIBUTO)
);

COMMENT ON TABLE DC_ESQUEMA_ATRIBUTO IS
'Registra o mapeamento entre o atributo do arquivo XML e o campo.';

COMMENT ON COLUMN DC_ESQUEMA_ATRIBUTO.SQ_ESQUEMA_ATRIBUTO IS
'Chave de DC_ESQUEMA_ATRIBUTO.';

COMMENT ON COLUMN DC_ESQUEMA_ATRIBUTO.SQ_ESQUEMA_TABELA IS
'Chave de DC_ESQUEMA_TABELA. Indica a que tabela refere-se este mapeamento.';

COMMENT ON COLUMN DC_ESQUEMA_ATRIBUTO.SQ_COLUNA IS
'Chave de DC_COLUNA. Indica a que coluna da tabela refere-se este mapeamento.';

COMMENT ON COLUMN DC_ESQUEMA_ATRIBUTO.ORDEM IS
'Número de ordem do campo, utilizado para exportação. Para importação, a seqüência será igual a DC_COLUNA.ORDEM.';

COMMENT ON COLUMN DC_ESQUEMA_ATRIBUTO.CAMPO_EXTERNO IS
'Nome do campo no arquivo de inclusão ou exclusão.';

COMMENT ON COLUMN DC_ESQUEMA_ATRIBUTO.MASCARA_DATA IS
'Indica a máscara para campos data.';

COMMENT ON COLUMN DC_ESQUEMA_ATRIBUTO.VALOR_DEFAULT IS
'Indica o valor a ser inserido caso o campo de origem seja nulo.';

/*==============================================================*/
/* Index: IN_DCESQATR_TABELA                                    */
/*==============================================================*/
CREATE  INDEX IN_DCESQATR_TABELA ON DC_ESQUEMA_ATRIBUTO (
SQ_ESQUEMA_TABELA,
SQ_COLUNA,
SQ_ESQUEMA_ATRIBUTO
);

/*==============================================================*/
/* Index: IN_DCESQATR_COLUNA                                    */
/*==============================================================*/
CREATE UNIQUE INDEX IN_DCESQATR_COLUNA ON DC_ESQUEMA_ATRIBUTO (
SQ_COLUNA,
SQ_ESQUEMA_TABELA
);

/*==============================================================*/
/* Table: DC_ESQUEMA_INSERT                                     */
/*==============================================================*/
CREATE TABLE DC_ESQUEMA_INSERT (
   SQ_ESQUEMA_INSERT    NUMERIC(18)          NOT NULL,
   SQ_ESQUEMA_TABELA    NUMERIC(18)          NULL,
   REGISTRO             NUMERIC(4)           NOT NULL,
   SQ_COLUNA            NUMERIC(18)          NULL,
   ORDEM                NUMERIC(4)           NOT NULL DEFAULT 0,
   VALOR                VARCHAR(255)         NULL,
   CONSTRAINT PK_DC_ESQUEMA_INSERT PRIMARY KEY (SQ_ESQUEMA_INSERT)
);

COMMENT ON TABLE DC_ESQUEMA_INSERT IS
'Registra inserções de registro numa tabela.';

COMMENT ON COLUMN DC_ESQUEMA_INSERT.SQ_ESQUEMA_INSERT IS
'Chave de DC_ESQUEMA_INSERT.';

COMMENT ON COLUMN DC_ESQUEMA_INSERT.SQ_ESQUEMA_TABELA IS
'Chave de DC_ESQUEMA_TABELA. Indica a tabela que receberá o registro.';

COMMENT ON COLUMN DC_ESQUEMA_INSERT.REGISTRO IS
'Número do registro. Define a ordem de inserção.';

COMMENT ON COLUMN DC_ESQUEMA_INSERT.SQ_COLUNA IS
'Chave de DC_COLUNA. Indica o campo que receberá o valor.';

COMMENT ON COLUMN DC_ESQUEMA_INSERT.ORDEM IS
'Número de ordem do campo, utilizado para montagem do comando de inserção.';

COMMENT ON COLUMN DC_ESQUEMA_INSERT.VALOR IS
'Valor a ser inserido no campo.';

/*==============================================================*/
/* Index: IN_DCESQINS_TAB                                       */
/*==============================================================*/
CREATE  INDEX IN_DCESQINS_TAB ON DC_ESQUEMA_INSERT (
SQ_ESQUEMA_TABELA,
SQ_ESQUEMA_INSERT
);

/*==============================================================*/
/* Index: IN_DCESQINS_COL                                       */
/*==============================================================*/
CREATE  INDEX IN_DCESQINS_COL ON DC_ESQUEMA_INSERT (
SQ_COLUNA,
SQ_ESQUEMA_INSERT
);

/*==============================================================*/
/* Table: DC_ESQUEMA_SCRIPT                                     */
/*==============================================================*/
CREATE TABLE DC_ESQUEMA_SCRIPT (
   SQ_ESQUEMA_SCRIPT    NUMERIC(18)          NOT NULL,
   SQ_ESQUEMA           NUMERIC(18)          NOT NULL,
   SQ_SIW_ARQUIVO       NUMERIC(18)          NOT NULL,
   ORDEM                NUMERIC(4)           NOT NULL DEFAULT 0,
   CONSTRAINT PK_DC_ESQUEMA_SCRIPT PRIMARY KEY (SQ_ESQUEMA_SCRIPT)
);

COMMENT ON TABLE DC_ESQUEMA_SCRIPT IS
'Registra scripts a serem disparados pelo esquema.';

COMMENT ON COLUMN DC_ESQUEMA_SCRIPT.SQ_ESQUEMA_SCRIPT IS
'Chave de DC_ESQUEMA_SCRIPT.';

COMMENT ON COLUMN DC_ESQUEMA_SCRIPT.SQ_ESQUEMA IS
'Chave de DC_ESQUEMA. Indica o esquema ao qual o script está ligado.';

COMMENT ON COLUMN DC_ESQUEMA_SCRIPT.SQ_SIW_ARQUIVO IS
'chave de SIW_ARQUIVO, indicando o arquivo de upload do script.';

COMMENT ON COLUMN DC_ESQUEMA_SCRIPT.ORDEM IS
'Informa posição deste script na execução do programa de carga.';

/*==============================================================*/
/* Index: IN_DCESQSCR_ESQUEMA                                   */
/*==============================================================*/
CREATE  INDEX IN_DCESQSCR_ESQUEMA ON DC_ESQUEMA_SCRIPT (
SQ_ESQUEMA,
SQ_ESQUEMA_SCRIPT
);

/*==============================================================*/
/* Table: DC_ESQUEMA_TABELA                                     */
/*==============================================================*/
CREATE TABLE DC_ESQUEMA_TABELA (
   SQ_ESQUEMA_TABELA    NUMERIC(18)          NOT NULL,
   SQ_ESQUEMA           NUMERIC(18)          NOT NULL,
   SQ_TABELA            NUMERIC(18)          NOT NULL,
   ORDEM                NUMERIC(4)           NOT NULL DEFAULT 0,
   ELEMENTO             VARCHAR(255)         NOT NULL,
   REMOVE_REGISTRO      VARCHAR(1)           NOT NULL DEFAULT 'N'
      CONSTRAINT CKC_REMOVE_REGISTRO_DC_ESQUE CHECK (REMOVE_REGISTRO IN ('S','N') AND REMOVE_REGISTRO = UPPER(REMOVE_REGISTRO)),
   CONSTRAINT PK_DC_ESQUEMA_TABELA PRIMARY KEY (SQ_ESQUEMA_TABELA)
);

COMMENT ON TABLE DC_ESQUEMA_TABELA IS
'Registra as tabelas do dicionário que serão integradas a sistemas externos.';

COMMENT ON COLUMN DC_ESQUEMA_TABELA.SQ_ESQUEMA_TABELA IS
'Chave de DC_ESQUEMA_TABELA.';

COMMENT ON COLUMN DC_ESQUEMA_TABELA.SQ_ESQUEMA IS
'Chave de DC_ESQUEMA. Indica a que esquema o registro está ligado.';

COMMENT ON COLUMN DC_ESQUEMA_TABELA.SQ_TABELA IS
'Chave de DC_TABELA. Indica a que tabela o registro está ligado.';

COMMENT ON COLUMN DC_ESQUEMA_TABELA.ORDEM IS
'Informa posição desta tabela na lista de importação ou exportação.';

COMMENT ON COLUMN DC_ESQUEMA_TABELA.ELEMENTO IS
'Indica o elemento do arquivo XML que contém os dados da tabela. Para arquivos TXT indica o caminho físico do arquivo a ser importado.';

COMMENT ON COLUMN DC_ESQUEMA_TABELA.REMOVE_REGISTRO IS
'Indica se o conteúdo da tabela deve ser removido antes de iniciar a carga dos dados.';

/*==============================================================*/
/* Index: IN_DCESQTAB_TABELA                                    */
/*==============================================================*/
CREATE UNIQUE INDEX IN_DCESQTAB_TABELA ON DC_ESQUEMA_TABELA (
SQ_TABELA,
SQ_ESQUEMA
);

/*==============================================================*/
/* Table: DC_EVENTO                                             */
/*==============================================================*/
CREATE TABLE DC_EVENTO (
   SQ_EVENTO            NUMERIC(18)          NOT NULL,
   NOME                 VARCHAR(30)          NOT NULL,
   DESCRICAO            VARCHAR(4000)        NOT NULL,
   CONSTRAINT PK_DC_EVENTO PRIMARY KEY (SQ_EVENTO)
);

COMMENT ON TABLE DC_EVENTO IS
'Armazena os eventos possíveis para uma trigger.';

COMMENT ON COLUMN DC_EVENTO.SQ_EVENTO IS
'Chave de DC_EVENTO.';

COMMENT ON COLUMN DC_EVENTO.NOME IS
'Nome do evento.';

COMMENT ON COLUMN DC_EVENTO.DESCRICAO IS
'Descrição do evento.';

/*==============================================================*/
/* Index: IN_DCEVE_NOME                                         */
/*==============================================================*/
CREATE UNIQUE INDEX IN_DCEVE_NOME ON DC_EVENTO (
NOME
);

/*==============================================================*/
/* Table: DC_INDICE                                             */
/*==============================================================*/
CREATE TABLE DC_INDICE (
   SQ_INDICE            NUMERIC(18)          NOT NULL,
   SQ_INDICE_TIPO       NUMERIC(18)          NOT NULL,
   SQ_USUARIO           NUMERIC(18)          NOT NULL,
   SQ_SISTEMA           NUMERIC(18)          NOT NULL,
   NOME                 VARCHAR(30)          NOT NULL,
   DESCRICAO            VARCHAR(4000)        NOT NULL,
   CONSTRAINT PK_DC_INDICE PRIMARY KEY (SQ_INDICE)
);

COMMENT ON TABLE DC_INDICE IS
'Armazena os índices das tabelas.';

COMMENT ON COLUMN DC_INDICE.SQ_INDICE IS
'Chave de DC_INDICE.';

COMMENT ON COLUMN DC_INDICE.SQ_INDICE_TIPO IS
'Chave de DC_INDICE_TIPO. Indica a que tipo o registro está ligado.';

COMMENT ON COLUMN DC_INDICE.SQ_USUARIO IS
'Chave de DC_USUARIO. Indica a que usuário o do banco de dados o registro está ligado.';

COMMENT ON COLUMN DC_INDICE.SQ_SISTEMA IS
'Chave de DC_SISTEMA. Indica a que sistema o registro está ligado.';

COMMENT ON COLUMN DC_INDICE.NOME IS
'Nome do índice.';

COMMENT ON COLUMN DC_INDICE.DESCRICAO IS
'Descrição do índice.';

/*==============================================================*/
/* Index: IN_DCIND_TIPO                                         */
/*==============================================================*/
CREATE  INDEX IN_DCIND_TIPO ON DC_INDICE (
SQ_INDICE_TIPO,
SQ_INDICE
);

/*==============================================================*/
/* Index: IN_DCIND_SISTEMA                                      */
/*==============================================================*/
CREATE  INDEX IN_DCIND_SISTEMA ON DC_INDICE (
SQ_SISTEMA,
SQ_INDICE
);

/*==============================================================*/
/* Index: IN_DCIND_NOME                                         */
/*==============================================================*/
CREATE UNIQUE INDEX IN_DCIND_NOME ON DC_INDICE (
NOME,
SQ_USUARIO,
SQ_SISTEMA
);

/*==============================================================*/
/* Table: DC_INDICE_COLS                                        */
/*==============================================================*/
CREATE TABLE DC_INDICE_COLS (
   SQ_INDICE            NUMERIC(18)          NOT NULL,
   SQ_COLUNA            NUMERIC(18)          NOT NULL,
   ORDEM                NUMERIC(18)          NOT NULL,
   ORDENACAO            VARCHAR(1)           NOT NULL DEFAULT 'D'
      CONSTRAINT CKC_ORDENACAO_DC_INDIC CHECK (ORDENACAO IN ('A','D')),
   CONSTRAINT PK_DC_INDICE_COLS PRIMARY KEY (SQ_INDICE, SQ_COLUNA)
);

COMMENT ON TABLE DC_INDICE_COLS IS
'Armazena as colunas de um índice.';

COMMENT ON COLUMN DC_INDICE_COLS.SQ_INDICE IS
'Chave de DC_INDICE. Indica a que índice o registro está ligado.';

COMMENT ON COLUMN DC_INDICE_COLS.SQ_COLUNA IS
'Chave de DC_COLUNA. Indica a que coluna da tabela refere-se este índice.';

COMMENT ON COLUMN DC_INDICE_COLS.ORDEM IS
'Número de ordem da coluna no índice.';

COMMENT ON COLUMN DC_INDICE_COLS.ORDENACAO IS
'Modo de ordenação da coluna (A - ascendente; D - descendente).';

/*==============================================================*/
/* Index: IN_DCINDCOL_INVERSA                                   */
/*==============================================================*/
CREATE  INDEX IN_DCINDCOL_INVERSA ON DC_INDICE_COLS (
SQ_COLUNA,
SQ_INDICE
);

/*==============================================================*/
/* Table: DC_INDICE_TIPO                                        */
/*==============================================================*/
CREATE TABLE DC_INDICE_TIPO (
   SQ_INDICE_TIPO       NUMERIC(18)          NOT NULL,
   NOME                 VARCHAR(30)          NOT NULL,
   DESCRICAO            VARCHAR(4000)        NOT NULL,
   CONSTRAINT PK_DC_INDICE_TIPO PRIMARY KEY (SQ_INDICE_TIPO)
);

COMMENT ON TABLE DC_INDICE_TIPO IS
'Armazena os tipos possíveis de índice.';

COMMENT ON COLUMN DC_INDICE_TIPO.SQ_INDICE_TIPO IS
'Chave de DC_INDICE_TIPO.';

COMMENT ON COLUMN DC_INDICE_TIPO.NOME IS
'Nome do tipo de índice.';

COMMENT ON COLUMN DC_INDICE_TIPO.DESCRICAO IS
'Descrição do tipo de índice.';

/*==============================================================*/
/* Table: DC_OCORRENCIA                                         */
/*==============================================================*/
CREATE TABLE DC_OCORRENCIA (
   SQ_OCORRENCIA        NUMERIC(18)          NOT NULL,
   SQ_ESQUEMA           NUMERIC(18)          NOT NULL,
   SQ_PESSOA            NUMERIC(18)          NOT NULL,
   DATA_OCORRENCIA      DATE                 NOT NULL DEFAULT 'now()',
   DATA_REFERENCIA      DATE                 NOT NULL DEFAULT 'now()',
   PROCESSADOS          NUMERIC(18)          NOT NULL,
   REJEITADOS           NUMERIC(18)          NOT NULL,
   ARQUIVO_PROCESSAMENTO NUMERIC(18)          NOT NULL,
   ARQUIVO_REJEICAO     NUMERIC(18)          NULL,
   CONSTRAINT PK_DC_OCORRENCIA PRIMARY KEY (SQ_OCORRENCIA)
);

COMMENT ON TABLE DC_OCORRENCIA IS
'Registra as ocorrências de importação ou exportação.';

COMMENT ON COLUMN DC_OCORRENCIA.SQ_OCORRENCIA IS
'Chave de DC_OCORRENCIA.';

COMMENT ON COLUMN DC_OCORRENCIA.SQ_ESQUEMA IS
'Chave de DC_ESQUEMA. Indica a que esquema a ocorrência refere-se.';

COMMENT ON COLUMN DC_OCORRENCIA.SQ_PESSOA IS
'Chave de CO_PESSOA. Indica o usuário responsável pela ocorrência.';

COMMENT ON COLUMN DC_OCORRENCIA.DATA_OCORRENCIA IS
'Data de processamento da importação ou exportação.';

COMMENT ON COLUMN DC_OCORRENCIA.DATA_REFERENCIA IS
'Data de referência dos dados importados ou exportados.';

COMMENT ON COLUMN DC_OCORRENCIA.PROCESSADOS IS
'Quantidade de registros processados.';

COMMENT ON COLUMN DC_OCORRENCIA.REJEITADOS IS
'Quantidade de registros rejeitados.';

COMMENT ON COLUMN DC_OCORRENCIA.ARQUIVO_PROCESSAMENTO IS
'Chave de SIW_ARQUIVO, que contém os dados do arquivo processado.';

COMMENT ON COLUMN DC_OCORRENCIA.ARQUIVO_REJEICAO IS
'Chave de SIW_ARQUIVO, que contém dados do arquivo com os registros rejeitados no processamento.';

/*==============================================================*/
/* Index: IN_DCOCO_DATA                                         */
/*==============================================================*/
CREATE  INDEX IN_DCOCO_DATA ON DC_OCORRENCIA (
DATA_OCORRENCIA,
SQ_ESQUEMA,
SQ_OCORRENCIA
);

/*==============================================================*/
/* Table: DC_PROCEDURE                                          */
/*==============================================================*/
CREATE TABLE DC_PROCEDURE (
   SQ_PROCEDURE         NUMERIC(18)          NOT NULL,
   SQ_ARQUIVO           NUMERIC(18)          NOT NULL,
   SQ_SISTEMA           NUMERIC(18)          NOT NULL,
   SQ_SP_TIPO           NUMERIC(18)          NOT NULL,
   NOME                 VARCHAR(30)          NOT NULL,
   DESCRICAO            VARCHAR(4000)        NOT NULL,
   CONSTRAINT PK_DC_PROCEDURE PRIMARY KEY (SQ_PROCEDURE)
);

COMMENT ON TABLE DC_PROCEDURE IS
'Armazena as rotinas e funções da aplicação.';

COMMENT ON COLUMN DC_PROCEDURE.SQ_PROCEDURE IS
'Chave de DC_PROCEDURE.';

COMMENT ON COLUMN DC_PROCEDURE.SQ_ARQUIVO IS
'Chave de SIW_ARQUIVO. Indica a que arquivo o registro está ligado.';

COMMENT ON COLUMN DC_PROCEDURE.SQ_SISTEMA IS
'Chave de DC_SISTEMA. Indica a que sistema o registro está ligado.';

COMMENT ON COLUMN DC_PROCEDURE.SQ_SP_TIPO IS
'Chave de DC_SP_TIPO. Indica a que tipo de SP o registro está ligado.';

COMMENT ON COLUMN DC_PROCEDURE.NOME IS
'Nome da procedure.';

COMMENT ON COLUMN DC_PROCEDURE.DESCRICAO IS
'Descrição da rotina.';

/*==============================================================*/
/* Index: IN_DCPRO_SISTEMA                                      */
/*==============================================================*/
CREATE  INDEX IN_DCPRO_SISTEMA ON DC_PROCEDURE (
SQ_SISTEMA,
SQ_PROCEDURE
);

/*==============================================================*/
/* Index: IN_DCPRO_ARQUIVO                                      */
/*==============================================================*/
CREATE  INDEX IN_DCPRO_ARQUIVO ON DC_PROCEDURE (
SQ_ARQUIVO,
SQ_PROCEDURE
);

/*==============================================================*/
/* Index: IN_DCPRO_TIPO                                         */
/*==============================================================*/
CREATE  INDEX IN_DCPRO_TIPO ON DC_PROCEDURE (
SQ_SP_TIPO,
SQ_PROCEDURE
);

/*==============================================================*/
/* Index: IN_DCPRO_NOME                                         */
/*==============================================================*/
CREATE  INDEX IN_DCPRO_NOME ON DC_PROCEDURE (
NOME,
SQ_PROCEDURE
);

/*==============================================================*/
/* Table: DC_PROC_PARAM                                         */
/*==============================================================*/
CREATE TABLE DC_PROC_PARAM (
   SQ_PARAM             NUMERIC(18)          NOT NULL,
   SQ_PROCEDURE         NUMERIC(18)          NOT NULL,
   SQ_DADO_TIPO         NUMERIC(18)          NOT NULL,
   NOME                 VARCHAR(30)          NOT NULL,
   DESCRICAO            VARCHAR(4000)        NOT NULL,
   TIPO                 VARCHAR(1)           NOT NULL DEFAULT 'E'
      CONSTRAINT CKC_TIPO_DC_PROC_ CHECK (TIPO IN ('E','S','A')),
   ORDEM                NUMERIC(18)          NOT NULL,
   CONSTRAINT PK_DC_PROC_PARAM PRIMARY KEY (SQ_PARAM)
);

COMMENT ON TABLE DC_PROC_PARAM IS
'Armazena os parâmetros de procedures.';

COMMENT ON COLUMN DC_PROC_PARAM.SQ_PARAM IS
'Chave de DC_PROC_PARAM.';

COMMENT ON COLUMN DC_PROC_PARAM.SQ_PROCEDURE IS
'Chave de DC_PROCEDURE. Indica a que procedure o registro está ligado.';

COMMENT ON COLUMN DC_PROC_PARAM.SQ_DADO_TIPO IS
'Chave de DC_DADO_TIPO. Indica a que tipo de dado o registro está ligado.';

COMMENT ON COLUMN DC_PROC_PARAM.NOME IS
'Nome do parâmetro.';

COMMENT ON COLUMN DC_PROC_PARAM.DESCRICAO IS
'Descrição do parâmetro.';

COMMENT ON COLUMN DC_PROC_PARAM.TIPO IS
'Tipo do parâmetro (E - entrada; S - saída; A - ambos)';

COMMENT ON COLUMN DC_PROC_PARAM.ORDEM IS
'Número de ordem do parâmetro.';

/*==============================================================*/
/* Index: IN_DCPROPAR_PROCEDURE                                 */
/*==============================================================*/
CREATE  INDEX IN_DCPROPAR_PROCEDURE ON DC_PROC_PARAM (
SQ_PROCEDURE,
SQ_PARAM
);

/*==============================================================*/
/* Index: IN_DCPROPAR_NOME                                      */
/*==============================================================*/
CREATE  INDEX IN_DCPROPAR_NOME ON DC_PROC_PARAM (
NOME,
SQ_PROCEDURE
);

/*==============================================================*/
/* Table: DC_PROC_SP                                            */
/*==============================================================*/
CREATE TABLE DC_PROC_SP (
   SQ_PROCEDURE         NUMERIC(18)          NOT NULL,
   SQ_STORED_PROC       NUMERIC(18)          NOT NULL,
   CONSTRAINT PK_DC_PROC_SP PRIMARY KEY (SQ_PROCEDURE, SQ_STORED_PROC)
);

COMMENT ON TABLE DC_PROC_SP IS
'Armazena as storage procedures chamadas por uma função ou rotina do sistema.';

COMMENT ON COLUMN DC_PROC_SP.SQ_PROCEDURE IS
'Chave de DC_PROCEDURE. Indica a que procedure o registro está ligado.';

COMMENT ON COLUMN DC_PROC_SP.SQ_STORED_PROC IS
'Chave de DC_STORED_PROC. Indica a que SP o registro está lgado.';

/*==============================================================*/
/* Index: IN_DCPROSP_INVERSA                                    */
/*==============================================================*/
CREATE  INDEX IN_DCPROSP_INVERSA ON DC_PROC_SP (
SQ_STORED_PROC,
SQ_PROCEDURE
);

/*==============================================================*/
/* Table: DC_PROC_TABELA                                        */
/*==============================================================*/
CREATE TABLE DC_PROC_TABELA (
   SQ_PROCEDURE         NUMERIC(18)          NOT NULL,
   SQ_TABELA            NUMERIC(18)          NOT NULL,
   DESCRICAO            VARCHAR(4000)        NOT NULL,
   CONSTRAINT PK_DC_PROC_TABELA PRIMARY KEY (SQ_PROCEDURE, SQ_TABELA)
);

COMMENT ON TABLE DC_PROC_TABELA IS
'Armazena as tabelas referenciadas por uma função ou procedure do sistema.';

COMMENT ON COLUMN DC_PROC_TABELA.SQ_PROCEDURE IS
'Chave de DC_PROCEDURE. Indica a que procedure o registro está ligado.';

COMMENT ON COLUMN DC_PROC_TABELA.SQ_TABELA IS
'Chave de DC_TABELA. Indica a que tabela o registro está ligado.';

COMMENT ON COLUMN DC_PROC_TABELA.DESCRICAO IS
'Descrição das operações que a função ou rotina executa sobre a tabela.';

/*==============================================================*/
/* Index: IN_DCPROTAB_INVERSA                                   */
/*==============================================================*/
CREATE  INDEX IN_DCPROTAB_INVERSA ON DC_PROC_TABELA (
SQ_TABELA,
SQ_PROCEDURE
);

/*==============================================================*/
/* Table: DC_RELACIONAMENTO                                     */
/*==============================================================*/
CREATE TABLE DC_RELACIONAMENTO (
   SQ_RELACIONAMENTO    NUMERIC(18)          NOT NULL,
   NOME                 VARCHAR(30)          NOT NULL,
   DESCRICAO            VARCHAR(4000)        NOT NULL,
   TABELA_PAI           NUMERIC(18)          NOT NULL,
   TABELA_FILHA         NUMERIC(18)          NOT NULL,
   SQ_SISTEMA           NUMERIC(18)          NOT NULL,
   CONSTRAINT PK_DC_RELACIONAMENTO PRIMARY KEY (SQ_RELACIONAMENTO)
);

COMMENT ON COLUMN DC_RELACIONAMENTO.SQ_RELACIONAMENTO IS
'Chave de DC_RELACIONAMENTO.';

COMMENT ON COLUMN DC_RELACIONAMENTO.NOME IS
'Nome do relacionamento.';

COMMENT ON COLUMN DC_RELACIONAMENTO.DESCRICAO IS
'Descrição  do relacionamento.';

COMMENT ON COLUMN DC_RELACIONAMENTO.TABELA_PAI IS
'Sequence';

COMMENT ON COLUMN DC_RELACIONAMENTO.TABELA_FILHA IS
'Sequence';

COMMENT ON COLUMN DC_RELACIONAMENTO.SQ_SISTEMA IS
'Chave de DC_SISTEMA. Indica a que sistema o registro está ligado.';

/*==============================================================*/
/* Index: IN_DCREL_PAI                                          */
/*==============================================================*/
CREATE  INDEX IN_DCREL_PAI ON DC_RELACIONAMENTO (
TABELA_PAI,
SQ_RELACIONAMENTO
);

/*==============================================================*/
/* Index: IN_DCREL_FILHA                                        */
/*==============================================================*/
CREATE  INDEX IN_DCREL_FILHA ON DC_RELACIONAMENTO (
TABELA_FILHA,
SQ_RELACIONAMENTO
);

/*==============================================================*/
/* Index: IN_DCREL_NOME                                         */
/*==============================================================*/
CREATE UNIQUE INDEX IN_DCREL_NOME ON DC_RELACIONAMENTO (
NOME,
SQ_SISTEMA
);

/*==============================================================*/
/* Index: IN_DCREL_SISTEMA                                      */
/*==============================================================*/
CREATE  INDEX IN_DCREL_SISTEMA ON DC_RELACIONAMENTO (
SQ_SISTEMA,
SQ_RELACIONAMENTO
);

/*==============================================================*/
/* Table: DC_RELAC_COLS                                         */
/*==============================================================*/
CREATE TABLE DC_RELAC_COLS (
   SQ_RELACIONAMENTO    NUMERIC(18)          NOT NULL,
   COLUNA_PAI           NUMERIC(18)          NOT NULL,
   COLUNA_FILHA         NUMERIC(18)          NOT NULL,
   CONSTRAINT PK_DC_RELAC_COLS PRIMARY KEY (SQ_RELACIONAMENTO, COLUNA_PAI, COLUNA_FILHA)
);

COMMENT ON TABLE DC_RELAC_COLS IS
'Armazena as colunas de ligação.';

COMMENT ON COLUMN DC_RELAC_COLS.SQ_RELACIONAMENTO IS
'Chave de DC_RELACIONAMENTO. Indica a que relacionamento o registro está ligado.';

COMMENT ON COLUMN DC_RELAC_COLS.COLUNA_PAI IS
'Sequence.';

COMMENT ON COLUMN DC_RELAC_COLS.COLUNA_FILHA IS
'Sequence.';

/*==============================================================*/
/* Index: IN_DCRELCOL_PAI                                       */
/*==============================================================*/
CREATE  INDEX IN_DCRELCOL_PAI ON DC_RELAC_COLS (
COLUNA_PAI,
SQ_RELACIONAMENTO
);

/*==============================================================*/
/* Index: IN_DCRELCOL_FILHA                                     */
/*==============================================================*/
CREATE  INDEX IN_DCRELCOL_FILHA ON DC_RELAC_COLS (
COLUNA_FILHA,
SQ_RELACIONAMENTO
);

/*==============================================================*/
/* Table: DC_SISTEMA                                            */
/*==============================================================*/
CREATE TABLE DC_SISTEMA (
   SQ_SISTEMA           NUMERIC(18)          NOT NULL,
   CLIENTE              NUMERIC(18)          NOT NULL,
   NOME                 VARCHAR(31)          NOT NULL,
   SIGLA                VARCHAR(10)          NOT NULL,
   DESCRICAO            VARCHAR(4000)        NOT NULL,
   CONSTRAINT PK_DC_SISTEMA PRIMARY KEY (SQ_SISTEMA)
);

COMMENT ON TABLE DC_SISTEMA IS
'Armazena os dados do sistema.';

COMMENT ON COLUMN DC_SISTEMA.SQ_SISTEMA IS
'Chave de DC_SISTEMA.';

COMMENT ON COLUMN DC_SISTEMA.CLIENTE IS
'Chave de CO_PESSOA. Indica a que cliente o registro está ligado.';

COMMENT ON COLUMN DC_SISTEMA.NOME IS
'Nome do sistema.';

COMMENT ON COLUMN DC_SISTEMA.SIGLA IS
'Sigla do sistema.';

COMMENT ON COLUMN DC_SISTEMA.DESCRICAO IS
'Descrição do sistema: finalidade, objetivos, características etc.';

/*==============================================================*/
/* Index: IN_DCSIS_CLIENTE                                      */
/*==============================================================*/
CREATE  INDEX IN_DCSIS_CLIENTE ON DC_SISTEMA (
CLIENTE,
SQ_SISTEMA
);

/*==============================================================*/
/* Table: DC_SP_PARAM                                           */
/*==============================================================*/
CREATE TABLE DC_SP_PARAM (
   SQ_SP_PARAM          NUMERIC(18)          NOT NULL,
   SQ_STORED_PROC       NUMERIC(18)          NOT NULL,
   SQ_DADO_TIPO         NUMERIC(18)          NOT NULL,
   NOME                 VARCHAR(30)          NOT NULL,
   DESCRICAO            VARCHAR(4000)        NOT NULL,
   TIPO                 VARCHAR(1)           NOT NULL DEFAULT 'E'
      CONSTRAINT CKC_TIPO_DC_SP_PA CHECK (TIPO IN ('E','S','A')),
   ORDEM                NUMERIC(18)          NOT NULL,
   TAMANHO              NUMERIC(18)          NOT NULL DEFAULT 0,
   PRECISAO             NUMERIC(18)          NULL,
   ESCALA               NUMERIC(18)          NULL,
   OBRIGATORIO          VARCHAR(1)           NOT NULL DEFAULT 'N'
      CONSTRAINT CKC_OBRIGATORIO_DC_SP_PA CHECK (OBRIGATORIO IN ('S','N') AND OBRIGATORIO = UPPER(OBRIGATORIO)),
   VALOR_PADRAO         VARCHAR(255)         NULL,
   CONSTRAINT PK_DC_SP_PARAM PRIMARY KEY (SQ_SP_PARAM)
);

COMMENT ON TABLE DC_SP_PARAM IS
'Armazena os parâmetros da stored procedure.';

COMMENT ON COLUMN DC_SP_PARAM.SQ_SP_PARAM IS
'Chave de DC_SP_PARAM.';

COMMENT ON COLUMN DC_SP_PARAM.SQ_STORED_PROC IS
'Chave de DC_STORED_PROC. Indica a que SP o registro está lgado.';

COMMENT ON COLUMN DC_SP_PARAM.SQ_DADO_TIPO IS
'Chave de DC_DADO_TIPO. Indica a que tipo de dado o registro está ligado.';

COMMENT ON COLUMN DC_SP_PARAM.NOME IS
'Nome do parâmetro.';

COMMENT ON COLUMN DC_SP_PARAM.DESCRICAO IS
'Descrição do parâmetro.';

COMMENT ON COLUMN DC_SP_PARAM.TIPO IS
'Tipo do parâmetro (E - entrada; S - saída; A - ambos)';

COMMENT ON COLUMN DC_SP_PARAM.ORDEM IS
'Número de ordem do parâmetro.';

COMMENT ON COLUMN DC_SP_PARAM.TAMANHO IS
'Tamanho do parâmetro, em bytes.';

COMMENT ON COLUMN DC_SP_PARAM.PRECISAO IS
'Número de casas decimais quando for o parâmetro for do tipo numérico';

COMMENT ON COLUMN DC_SP_PARAM.ESCALA IS
'Número de dígitos à direita da vírgula decimal, quando o parâmetro for do tipo numérico.';

COMMENT ON COLUMN DC_SP_PARAM.OBRIGATORIO IS
'Indica se o parâmetro é obrigqatório.';

COMMENT ON COLUMN DC_SP_PARAM.VALOR_PADRAO IS
'Valor do parâmetro, caso não seja especificado um.';

/*==============================================================*/
/* Index: IN_DCSPPAR_SP                                         */
/*==============================================================*/
CREATE  INDEX IN_DCSPPAR_SP ON DC_SP_PARAM (
SQ_STORED_PROC,
SQ_SP_PARAM
);

/*==============================================================*/
/* Index: IN_DCSPPAR_NOME                                       */
/*==============================================================*/
CREATE  INDEX IN_DCSPPAR_NOME ON DC_SP_PARAM (
NOME,
SQ_STORED_PROC
);

/*==============================================================*/
/* Table: DC_SP_SP                                              */
/*==============================================================*/
CREATE TABLE DC_SP_SP (
   SP_PAI               NUMERIC(18)          NOT NULL,
   SP_FILHA             NUMERIC(18)          NOT NULL,
   CONSTRAINT PK_DC_SP_SP PRIMARY KEY (SP_PAI, SP_FILHA)
);

COMMENT ON TABLE DC_SP_SP IS
'Armazena as stored procedures chamadas por outras stored procedures.';

COMMENT ON COLUMN DC_SP_SP.SP_PAI IS
'Chave de DC_STORED_PROC. Indica a SP que é referenciada pela SP filha.';

COMMENT ON COLUMN DC_SP_SP.SP_FILHA IS
'Chave de DC_STORED_PROC. Indica a SP que referencia a SP pai.';

/*==============================================================*/
/* Index: IN_DCSPSP_INVERSA                                     */
/*==============================================================*/
CREATE  INDEX IN_DCSPSP_INVERSA ON DC_SP_SP (
SP_FILHA,
SP_PAI
);

/*==============================================================*/
/* Table: DC_SP_TABS                                            */
/*==============================================================*/
CREATE TABLE DC_SP_TABS (
   SQ_STORED_PROC       NUMERIC(18)          NOT NULL,
   SQ_TABELA            NUMERIC(18)          NOT NULL,
   CONSTRAINT PK_DC_SP_TABS PRIMARY KEY (SQ_STORED_PROC, SQ_TABELA)
);

COMMENT ON TABLE DC_SP_TABS IS
'Armazena as tabelas referenciadas por uma stored procedure.';

COMMENT ON COLUMN DC_SP_TABS.SQ_STORED_PROC IS
'Chave de DC_STORED_PROC. Indica a que SP o registro está lgado.';

COMMENT ON COLUMN DC_SP_TABS.SQ_TABELA IS
'Chave de DC_TABELA. Indica a que tabela o registro está ligado.';

/*==============================================================*/
/* Index: IN_DCSPTAB_INVERSA                                    */
/*==============================================================*/
CREATE  INDEX IN_DCSPTAB_INVERSA ON DC_SP_TABS (
SQ_TABELA,
SQ_STORED_PROC
);

/*==============================================================*/
/* Table: DC_SP_TIPO                                            */
/*==============================================================*/
CREATE TABLE DC_SP_TIPO (
   SQ_SP_TIPO           NUMERIC(18)          NOT NULL,
   NOME                 VARCHAR(30)          NOT NULL,
   DESCRICAO            VARCHAR(4000)        NOT NULL,
   CONSTRAINT PK_DC_SP_TIPO PRIMARY KEY (SQ_SP_TIPO)
);

COMMENT ON TABLE DC_SP_TIPO IS
'Armazena os tipos possíveis de stored procedures.';

COMMENT ON COLUMN DC_SP_TIPO.SQ_SP_TIPO IS
'Chave de DC_SP_TIPO.';

COMMENT ON COLUMN DC_SP_TIPO.NOME IS
'Nome da stored procedure.';

COMMENT ON COLUMN DC_SP_TIPO.DESCRICAO IS
'Descrição do tipo de stored procedure.';

/*==============================================================*/
/* Index: IN_DCSPTIP_NOME                                       */
/*==============================================================*/
CREATE UNIQUE INDEX IN_DCSPTIP_NOME ON DC_SP_TIPO (
NOME
);

/*==============================================================*/
/* Table: DC_STORED_PROC                                        */
/*==============================================================*/
CREATE TABLE DC_STORED_PROC (
   SQ_STORED_PROC       NUMERIC(18)          NOT NULL,
   SQ_SP_TIPO           NUMERIC(18)          NOT NULL,
   SQ_USUARIO           NUMERIC(18)          NOT NULL,
   SQ_SISTEMA           NUMERIC(18)          NOT NULL,
   NOME                 VARCHAR(30)          NOT NULL,
   DESCRICAO            VARCHAR(4000)        NOT NULL,
   CONSTRAINT PK_DC_STORED_PROC PRIMARY KEY (SQ_STORED_PROC)
);

COMMENT ON TABLE DC_STORED_PROC IS
'Armazena dados das stored procedures do sistema.';

COMMENT ON COLUMN DC_STORED_PROC.SQ_STORED_PROC IS
'Chave de DC_STORED_PROC.';

COMMENT ON COLUMN DC_STORED_PROC.SQ_SP_TIPO IS
'Chave de DC_SP_TIPO. Indica a que tipo de SP o registro está ligado.';

COMMENT ON COLUMN DC_STORED_PROC.SQ_USUARIO IS
'Chave de DC_USUARIO. Indica a que usuário o do banco de dados o registro está ligado.';

COMMENT ON COLUMN DC_STORED_PROC.SQ_SISTEMA IS
'Chave de DC_SISTEMA. Indica a que sistema o registro está ligado.';

COMMENT ON COLUMN DC_STORED_PROC.NOME IS
'Nome da stored procedure.';

COMMENT ON COLUMN DC_STORED_PROC.DESCRICAO IS
'Descrição da stored procedure.';

/*==============================================================*/
/* Index: IN_DCSTOPRO_TIPO                                      */
/*==============================================================*/
CREATE  INDEX IN_DCSTOPRO_TIPO ON DC_STORED_PROC (
SQ_SP_TIPO,
SQ_STORED_PROC
);

/*==============================================================*/
/* Index: IN_DCSTOPRO_SISTEMA                                   */
/*==============================================================*/
CREATE  INDEX IN_DCSTOPRO_SISTEMA ON DC_STORED_PROC (
SQ_SISTEMA,
SQ_STORED_PROC
);

/*==============================================================*/
/* Index: IN_DCSTOPRO_NOME                                      */
/*==============================================================*/
CREATE UNIQUE INDEX IN_DCSTOPRO_NOME ON DC_STORED_PROC (
NOME,
SQ_USUARIO,
SQ_SISTEMA
);

/*==============================================================*/
/* Table: DC_TABELA                                             */
/*==============================================================*/
CREATE TABLE DC_TABELA (
   SQ_TABELA            NUMERIC(18)          NOT NULL,
   SQ_TABELA_TIPO       NUMERIC(18)          NOT NULL,
   SQ_USUARIO           NUMERIC(18)          NOT NULL,
   SQ_SISTEMA           NUMERIC(18)          NOT NULL,
   NOME                 VARCHAR(30)          NOT NULL,
   DESCRICAO            VARCHAR(4000)        NOT NULL,
   CONSTRAINT PK_DC_TABELA PRIMARY KEY (SQ_TABELA)
);

COMMENT ON TABLE DC_TABELA IS
'Armazena dados das tabelas do sistema.';

COMMENT ON COLUMN DC_TABELA.SQ_TABELA IS
'Chave de DC_TABELA.';

COMMENT ON COLUMN DC_TABELA.SQ_TABELA_TIPO IS
'Chave de DC_TABELA_TIPO. Indica a que tipo de tabela o registro está ligado.';

COMMENT ON COLUMN DC_TABELA.SQ_USUARIO IS
'Chave de DC_USUARIO. Indica a que usuário o do banco de dados o registro está ligado.';

COMMENT ON COLUMN DC_TABELA.SQ_SISTEMA IS
'Chave de DC_SISTEMA. Indica a que sistema o registro está ligado.';

COMMENT ON COLUMN DC_TABELA.NOME IS
'Nome da tabela.';

COMMENT ON COLUMN DC_TABELA.DESCRICAO IS
'Descrição da tabela: finalidade, objetivos, tipos de dados armazenados etc.';

/*==============================================================*/
/* Index: IN_DCTAB_SISTEMA                                      */
/*==============================================================*/
CREATE  INDEX IN_DCTAB_SISTEMA ON DC_TABELA (
SQ_SISTEMA,
SQ_TABELA
);

/*==============================================================*/
/* Index: IN_DCTAB_TIPO                                         */
/*==============================================================*/
CREATE  INDEX IN_DCTAB_TIPO ON DC_TABELA (
SQ_TABELA_TIPO,
SQ_TABELA
);

/*==============================================================*/
/* Index: IN_DCTAB_NOME                                         */
/*==============================================================*/
CREATE UNIQUE INDEX IN_DCTAB_NOME ON DC_TABELA (
NOME,
SQ_USUARIO,
SQ_SISTEMA
);

/*==============================================================*/
/* Table: DC_TABELA_TIPO                                        */
/*==============================================================*/
CREATE TABLE DC_TABELA_TIPO (
   SQ_TABELA_TIPO       NUMERIC(18)          NOT NULL,
   NOME                 VARCHAR(30)          NOT NULL,
   DESCRICAO            VARCHAR(4000)        NOT NULL,
   CONSTRAINT PK_DC_TABELA_TIPO PRIMARY KEY (SQ_TABELA_TIPO)
);

COMMENT ON TABLE DC_TABELA_TIPO IS
'Armazena os tipos possíveis de tabelas (física, materializada, view etc.).';

COMMENT ON COLUMN DC_TABELA_TIPO.SQ_TABELA_TIPO IS
'Chave de DC_TABELA_TIPO.';

COMMENT ON COLUMN DC_TABELA_TIPO.NOME IS
'Nome do tipo de tabela.';

COMMENT ON COLUMN DC_TABELA_TIPO.DESCRICAO IS
'Descrição do tipo de tabela.';

/*==============================================================*/
/* Index: IN_DCTABTIP_NOME                                      */
/*==============================================================*/
CREATE UNIQUE INDEX IN_DCTABTIP_NOME ON DC_TABELA_TIPO (
NOME
);

/*==============================================================*/
/* Table: DC_TRIGGER                                            */
/*==============================================================*/
CREATE TABLE DC_TRIGGER (
   SQ_TRIGGER           NUMERIC(18)          NOT NULL,
   SQ_TABELA            NUMERIC(18)          NOT NULL,
   SQ_USUARIO           NUMERIC(18)          NOT NULL,
   SQ_SISTEMA           NUMERIC(18)          NOT NULL,
   NOME                 VARCHAR(30)          NOT NULL,
   DESCRICAO            VARCHAR(4000)        NOT NULL,
   CONSTRAINT PK_DC_TRIGGER PRIMARY KEY (SQ_TRIGGER)
);

COMMENT ON TABLE DC_TRIGGER IS
'Armazena as triggers do sistema.';

COMMENT ON COLUMN DC_TRIGGER.SQ_TRIGGER IS
'Chave de DC_TRIGGER.';

COMMENT ON COLUMN DC_TRIGGER.SQ_TABELA IS
'Chave de DC_TABELA. Indica a que tabela o registro está ligado.';

COMMENT ON COLUMN DC_TRIGGER.SQ_USUARIO IS
'Chave de DC_USUARIO. Indica a que usuário o do banco de dados o registro está ligado.';

COMMENT ON COLUMN DC_TRIGGER.SQ_SISTEMA IS
'Chave de DC_SISTEMA. Indica a que sistema o registro está ligado.';

COMMENT ON COLUMN DC_TRIGGER.NOME IS
'Nome da trigger.';

COMMENT ON COLUMN DC_TRIGGER.DESCRICAO IS
'Descrição da trigger: finalidade, objetivos etc.';

/*==============================================================*/
/* Index: IN_DCTRI_TABELA                                       */
/*==============================================================*/
CREATE  INDEX IN_DCTRI_TABELA ON DC_TRIGGER (
SQ_TABELA,
SQ_TRIGGER
);

/*==============================================================*/
/* Index: IN_DCTRI_NOME                                         */
/*==============================================================*/
CREATE UNIQUE INDEX IN_DCTRI_NOME ON DC_TRIGGER (
NOME,
SQ_SISTEMA
);

/*==============================================================*/
/* Index: IN_DCTRI_SISTEMA                                      */
/*==============================================================*/
CREATE  INDEX IN_DCTRI_SISTEMA ON DC_TRIGGER (
SQ_SISTEMA,
SQ_TRIGGER
);

/*==============================================================*/
/* Table: DC_TRIGGER_EVENTO                                     */
/*==============================================================*/
CREATE TABLE DC_TRIGGER_EVENTO (
   SQ_TRIGGER           NUMERIC(18)          NOT NULL,
   SQ_EVENTO            NUMERIC(18)          NOT NULL,
   CONSTRAINT PK_DC_TRIGGER_EVENTO PRIMARY KEY (SQ_TRIGGER, SQ_EVENTO)
);

COMMENT ON TABLE DC_TRIGGER_EVENTO IS
'Armazena os eventos que disparam uma trigger.';

COMMENT ON COLUMN DC_TRIGGER_EVENTO.SQ_TRIGGER IS
'Chave de DC_TRIGGER. Indica a que trigger o evento está ligado.';

COMMENT ON COLUMN DC_TRIGGER_EVENTO.SQ_EVENTO IS
'Chave de DC_EVENTO. Indica a que evento o registro está ligado.';

/*==============================================================*/
/* Table: DC_USUARIO                                            */
/*==============================================================*/
CREATE TABLE DC_USUARIO (
   SQ_USUARIO           NUMERIC(18)          NOT NULL,
   SQ_SISTEMA           NUMERIC(18)          NOT NULL,
   NOME                 VARCHAR(30)          NOT NULL,
   DESCRICAO            VARCHAR(4000)        NOT NULL,
   CONSTRAINT PK_DC_USUARIO PRIMARY KEY (SQ_USUARIO)
);

COMMENT ON TABLE DC_USUARIO IS
'Armazena usuários do sistema.';

COMMENT ON COLUMN DC_USUARIO.SQ_USUARIO IS
'Chave de DC_USUARIO.';

COMMENT ON COLUMN DC_USUARIO.SQ_SISTEMA IS
'Chave de DC_SISTEMA. Indica a que sistema o registro está ligado.';

COMMENT ON COLUMN DC_USUARIO.NOME IS
'Nome do usuário.';

COMMENT ON COLUMN DC_USUARIO.DESCRICAO IS
'Descrição do usuário: finalidade, objetos por ele armazenados etc.';

/*==============================================================*/
/* Index: IN_DCUSU_SISTEMA                                      */
/*==============================================================*/
CREATE  INDEX IN_DCUSU_SISTEMA ON DC_USUARIO (
SQ_SISTEMA,
SQ_USUARIO
);

/*==============================================================*/
/* Index: IN_DCUSU_NOME                                         */
/*==============================================================*/
CREATE UNIQUE INDEX IN_DCUSU_NOME ON DC_USUARIO (
NOME,
SQ_SISTEMA
);

/*==============================================================*/
/* Table: DM_SEGMENTO_MENU                                      */
/*==============================================================*/
CREATE TABLE DM_SEGMENTO_MENU (
   SQ_SEGMENTO_MENU     NUMERIC(18)          NOT NULL,
   SQ_MODULO            NUMERIC(18)          NOT NULL,
   SQ_SEGMENTO          NUMERIC(18)          NOT NULL,
   SQ_SEG_MENU_PAI      NUMERIC(18)          NULL,
   NOME                 VARCHAR(40)          NOT NULL,
   FINALIDADE           VARCHAR(200)         NOT NULL DEFAULT 'A ser inserido.',
   LINK                 VARCHAR(60)          NULL,
   SQ_UNID_EXECUTORA    NUMERIC(10)          NULL,
   TRAMITE              VARCHAR(1)           NOT NULL DEFAULT 'N'
      CONSTRAINT CKC_DMSEGMEN_TRAM
               CHECK (TRAMITE IN ('S','N') AND TRAMITE = UPPER(TRAMITE)),
   ORDEM                NUMERIC(4)           NOT NULL,
   ULTIMO_NIVEL         VARCHAR(1)           NOT NULL DEFAULT 'N'
      CONSTRAINT CKC_DMSEGMEN_ULT
               CHECK (ULTIMO_NIVEL IN ('S','N') AND ULTIMO_NIVEL = UPPER(ULTIMO_NIVEL)),
   P1                   NUMERIC(18)          NULL,
   P2                   NUMERIC(18)          NULL,
   P3                   NUMERIC(18)          NULL,
   P4                   NUMERIC(18)          NULL,
   SIGLA                VARCHAR(10)          NULL,
   IMAGEM               VARCHAR(60)          NULL,
   ACESSO_GERAL         VARCHAR(1)           NOT NULL DEFAULT 'N'
      CONSTRAINT CKC_DMSEGMEN_ACGER
               CHECK (ACESSO_GERAL IN ('S','N') AND ACESSO_GERAL = UPPER(ACESSO_GERAL)),
   DESCENTRALIZADO      VARCHAR(1)           NOT NULL DEFAULT 'S'
      CONSTRAINT CKC_DMSEGMEN_DESC
               CHECK (DESCENTRALIZADO IN ('S','N') AND DESCENTRALIZADO = UPPER(DESCENTRALIZADO)),
   EXTERNO              VARCHAR(1)           NOT NULL DEFAULT 'N'
      CONSTRAINT CKC_DMSEGMEN_EXT
               CHECK (EXTERNO IN ('S','N') AND EXTERNO = UPPER(EXTERNO)),
   TARGET               VARCHAR(15)          NULL,
   EMITE_OS             VARCHAR(1)           NULL
      CONSTRAINT CKC_DMSEGMEN_OS
               CHECK (EMITE_OS IS NULL OR (EMITE_OS IN ('S','N') AND EMITE_OS = UPPER(EMITE_OS))),
   CONSULTA_OPINIAO     VARCHAR(1)           NULL
      CONSTRAINT CKC_DMSEGMEN_OPI
               CHECK (CONSULTA_OPINIAO IS NULL OR (CONSULTA_OPINIAO IN ('S','N') AND CONSULTA_OPINIAO = UPPER(CONSULTA_OPINIAO))),
   ENVIA_EMAIL          VARCHAR(1)           NULL
      CONSTRAINT CKC_DMSEGMEN_MAIL
               CHECK (ENVIA_EMAIL IS NULL OR (ENVIA_EMAIL IN ('S','N') AND ENVIA_EMAIL = UPPER(ENVIA_EMAIL))),
   EXIBE_RELATORIO      VARCHAR(1)           NULL
      CONSTRAINT CKC_DMSEGMEN_REL
               CHECK (EXIBE_RELATORIO IS NULL OR (EXIBE_RELATORIO IN ('S','N') AND EXIBE_RELATORIO = UPPER(EXIBE_RELATORIO))),
   COMO_FUNCIONA        VARCHAR(1000)        NULL,
   ARQUIVO_PROCED       VARCHAR(60)          NULL,
   VINCULACAO           VARCHAR(1)           NULL
      CONSTRAINT CKC_DMSEGMEN_VIN
               CHECK (VINCULACAO IS NULL OR (VINCULACAO IN ('P','U') AND VINCULACAO = UPPER(VINCULACAO))),
   DATA_HORA            VARCHAR(1)           NULL
      CONSTRAINT CKC_DATA_HORA_DM_SEGME CHECK (DATA_HORA IS NULL OR (DATA_HORA = UPPER(DATA_HORA))),
   ENVIA_DIA_UTIL       VARCHAR(1)           NULL
      CONSTRAINT CKC_DMSEGMEN_UTIL
               CHECK (ENVIA_DIA_UTIL IS NULL OR (ENVIA_DIA_UTIL IN ('S','N') AND ENVIA_DIA_UTIL = UPPER(ENVIA_DIA_UTIL))),
   DESCRICAO            VARCHAR(1)           NULL
      CONSTRAINT CKC_DMSEGMEN_DESCR
               CHECK (DESCRICAO IS NULL OR (DESCRICAO IN ('S','N') AND DESCRICAO = UPPER(DESCRICAO))),
   JUSTIFICATIVA        VARCHAR(1)           NULL
      CONSTRAINT CKC_DMSEGMEN_JUST
               CHECK (JUSTIFICATIVA IS NULL OR (JUSTIFICATIVA IN ('S','N') AND JUSTIFICATIVA = UPPER(JUSTIFICATIVA))),
   ATIVO                VARCHAR(1)           NOT NULL DEFAULT 'S'
      CONSTRAINT CKC_ATIVO_DMSEGMEN CHECK (ATIVO IN ('S','N') AND ATIVO = UPPER(ATIVO)),
   CONTROLA_ANO         VARCHAR(1)           NOT NULL DEFAULT 'N'
      CONSTRAINT CKC_CONTROLA_ANO_DM_SEGME CHECK (CONTROLA_ANO IN ('S','N') AND CONTROLA_ANO = UPPER(CONTROLA_ANO)),
   LIBERA_EDICAO        VARCHAR(1)           NOT NULL DEFAULT 'S'
      CONSTRAINT CKC_LIBERA_EDICAO_DM_SEGME CHECK (LIBERA_EDICAO IN ('S','N') AND LIBERA_EDICAO = UPPER(LIBERA_EDICAO)),
   CONSTRAINT PK_DM_SEGMENTO_MENU PRIMARY KEY (SQ_SEGMENTO_MENU)
);

COMMENT ON TABLE DM_SEGMENTO_MENU IS
'Armazena as opções padrão do menu para um segmento';

COMMENT ON COLUMN DM_SEGMENTO_MENU.SQ_SEGMENTO_MENU IS
'Chave de DM_SEGMENTO_MENU.';

COMMENT ON COLUMN DM_SEGMENTO_MENU.SQ_MODULO IS
'Chave de SIW_MODULO. Indica a que módulo o registro está ligado.';

COMMENT ON COLUMN DM_SEGMENTO_MENU.SQ_SEGMENTO IS
'Chave de CO_SEGMENTO. Indica a que segmento o registro está ligado.';

COMMENT ON COLUMN DM_SEGMENTO_MENU.SQ_SEG_MENU_PAI IS
'Chave de DM_SEGMENTO_MENU. Se preenchido, informa a subordinação da opção.';

COMMENT ON COLUMN DM_SEGMENTO_MENU.NOME IS
'Informa o texto a ser apresentado no menu.';

COMMENT ON COLUMN DM_SEGMENTO_MENU.FINALIDADE IS
'Informa a finalidade da opção.';

COMMENT ON COLUMN DM_SEGMENTO_MENU.LINK IS
'Informa o link a ser chamado quando a opção for clicada.';

COMMENT ON COLUMN DM_SEGMENTO_MENU.SQ_UNID_EXECUTORA IS
'Chave de EO_UNIDADE. Unidade responsável pela execução do serviço.';

COMMENT ON COLUMN DM_SEGMENTO_MENU.TRAMITE IS
'Indica se a opção deve ter controle de trâmites (work-flow).';

COMMENT ON COLUMN DM_SEGMENTO_MENU.ORDEM IS
'Informa a ordem em que a opção deve ser apresentada, em relação a outras opções de mesma subordinação.';

COMMENT ON COLUMN DM_SEGMENTO_MENU.ULTIMO_NIVEL IS
'Indica se a opção deve ser apresentada num sub-menu (S) ou na montagem do menu principal (N)';

COMMENT ON COLUMN DM_SEGMENTO_MENU.P1 IS
'Parâmetro de uso geral pela aplicação.';

COMMENT ON COLUMN DM_SEGMENTO_MENU.P2 IS
'Parâmetro de uso geral pela aplicação.';

COMMENT ON COLUMN DM_SEGMENTO_MENU.P3 IS
'Parâmetro de uso geral pela aplicação.';

COMMENT ON COLUMN DM_SEGMENTO_MENU.P4 IS
'Parâmetro de uso geral pela aplicação.';

COMMENT ON COLUMN DM_SEGMENTO_MENU.SIGLA IS
'Informa a sigla da opção, usada para controle interno da aplicação.';

COMMENT ON COLUMN DM_SEGMENTO_MENU.IMAGEM IS
'Informa qual ícone deve ser colocado ao lado da opção. Se for nulo, a imagem será a padrão.';

COMMENT ON COLUMN DM_SEGMENTO_MENU.ACESSO_GERAL IS
'Indica que a opção deve ser acessada por todos os usuários.';

COMMENT ON COLUMN DM_SEGMENTO_MENU.DESCENTRALIZADO IS
'Indica se a opção deve ser controlada por endereço.';

COMMENT ON COLUMN DM_SEGMENTO_MENU.EXTERNO IS
'Indica se o link da opção aponta para um endereço externo ao sistema.';

COMMENT ON COLUMN DM_SEGMENTO_MENU.TARGET IS
'Se preenchido, informa o nome da janela a ser aberta quando a opção for clicada.';

COMMENT ON COLUMN DM_SEGMENTO_MENU.EMITE_OS IS
'Indica se o serviço terá emissão de ordem de serviço';

COMMENT ON COLUMN DM_SEGMENTO_MENU.CONSULTA_OPINIAO IS
'Indica se o serviço deverá consultar a opinião do solicitante quanto ao atendimento';

COMMENT ON COLUMN DM_SEGMENTO_MENU.ENVIA_EMAIL IS
'Indica se deve ser enviado e-mail para o solicitante a cada trâmite';

COMMENT ON COLUMN DM_SEGMENTO_MENU.EXIBE_RELATORIO IS
'Indica se o serviço deve ser exibido no relatório gerencial';

COMMENT ON COLUMN DM_SEGMENTO_MENU.COMO_FUNCIONA IS
'Texto de apresentação do serviço, inclusive com as regras de negócio a serem respeitadas.';

COMMENT ON COLUMN DM_SEGMENTO_MENU.ARQUIVO_PROCED IS
'Arquivo que contém descrição dos procedimentos relacionados à opção.';

COMMENT ON COLUMN DM_SEGMENTO_MENU.VINCULACAO IS
'Este campo determina se a solicitação do serviço é vinculada ao beneficiário ou à unidade solicitante. Se for ao beneficiário, outras pessoas da unidade, que não sejam titular ou substituto, não poderão vê-la. Além disso, se o beneficiário for para outra unidade, a solicitação deve ser vista pelos novos chefes. Se for à unidade, todos as pessoas da unidade poderão consultar a solicitação, mesmo que não sejam chefes. Mesmo que o solicitante vá para outra unidade, a solicitação é consultada pela unidade que cadastrou a solicitação.';

COMMENT ON COLUMN DM_SEGMENTO_MENU.DATA_HORA IS
'Indica como o sistema deve tratar a questão de horas. (0) Não pede data; (1) Pede apenas uma data; (2) Pede apenas uma data/hora; (3) Pede data início e fim; (4) Pede data/hora início e fim.';

COMMENT ON COLUMN DM_SEGMENTO_MENU.ENVIA_DIA_UTIL IS
'Indica se a solicitação só pode ser atendida em dia útil.';

COMMENT ON COLUMN DM_SEGMENTO_MENU.DESCRICAO IS
'Indica se deve ser informada uma descrição na solicitação';

COMMENT ON COLUMN DM_SEGMENTO_MENU.JUSTIFICATIVA IS
'Indica se deve ser informada uma justificativa na solicitação';

COMMENT ON COLUMN DM_SEGMENTO_MENU.ATIVO IS
'Indica se o registro deve ou não ser exibido.';

COMMENT ON COLUMN DM_SEGMENTO_MENU.CONTROLA_ANO IS
'Indica se a opção do menu deve ter seu controle por ano.';

COMMENT ON COLUMN DM_SEGMENTO_MENU.LIBERA_EDICAO IS
'Indica se pode haver inclusão, alteração ou exclusão dos registros.';

/*==============================================================*/
/* Index: IN_DMSEGMEN_SIGLA                                     */
/*==============================================================*/
CREATE  INDEX IN_DMSEGMEN_SIGLA ON DM_SEGMENTO_MENU (
SIGLA,
SQ_SEGMENTO
);

/*==============================================================*/
/* Index: IN_DMSEGMEN_ULT                                       */
/*==============================================================*/
CREATE UNIQUE INDEX IN_DMSEGMEN_ULT ON DM_SEGMENTO_MENU (
ULTIMO_NIVEL,
SQ_SEGMENTO_MENU
);

/*==============================================================*/
/* Index: IN_DMSEGMEN_ATIVO                                     */
/*==============================================================*/
CREATE UNIQUE INDEX IN_DMSEGMEN_ATIVO ON DM_SEGMENTO_MENU (
ATIVO,
SQ_SEGMENTO_MENU
);

/*==============================================================*/
/* Index: IN_DMSEGMEN_PAI                                       */
/*==============================================================*/
CREATE  INDEX IN_DMSEGMEN_PAI ON DM_SEGMENTO_MENU (
SQ_SEG_MENU_PAI,
SQ_SEGMENTO_MENU
);

/*==============================================================*/
/* Index: IN_DMSEGMEN_SEG                                       */
/*==============================================================*/
CREATE  INDEX IN_DMSEGMEN_SEG ON DM_SEGMENTO_MENU (
SQ_SEGMENTO,
SQ_MODULO,
SQ_SEGMENTO_MENU
);

/*==============================================================*/
/* Table: DM_SEG_VINCULO                                        */
/*==============================================================*/
CREATE TABLE DM_SEG_VINCULO (
   SQ_SEG_VINCULO       NUMERIC(18)          NOT NULL,
   SQ_SEGMENTO          NUMERIC(18)          NOT NULL,
   SQ_TIPO_PESSOA       NUMERIC(18)          NOT NULL,
   NOME                 VARCHAR(21)          NOT NULL,
   INTERNO              VARCHAR(1)           NOT NULL DEFAULT 'N'
      CONSTRAINT CKC_DMSEGVIN_INT
               CHECK (INTERNO IN ('S','N') AND INTERNO = UPPER(INTERNO)),
   CONTRATADO           VARCHAR(1)           NOT NULL DEFAULT 'N'
      CONSTRAINT CKC_DMSEGVIN_CONT
               CHECK (CONTRATADO IN ('S','N') AND CONTRATADO = UPPER(CONTRATADO)),
   ORDEM                NUMERIC(6)           NULL,
   ATIVO                VARCHAR(1)           NOT NULL DEFAULT 'S'
      CONSTRAINT CKC_ATIVO_DMSEG CHECK (ATIVO IN ('S','N') AND ATIVO = UPPER(ATIVO)),
   PADRAO               VARCHAR(1)           NOT NULL DEFAULT 'N'
      CONSTRAINT CKC_DMSEGVIN_PAD
               CHECK (PADRAO IN ('S','N') AND PADRAO = UPPER(PADRAO)),
   CONSTRAINT PK_DM_SEG_VINCULO PRIMARY KEY (SQ_SEG_VINCULO)
);

COMMENT ON TABLE DM_SEG_VINCULO IS
'Armazena os vínculos padrão para o segmento da pessoa jurídica e tipo de pessoa';

COMMENT ON COLUMN DM_SEG_VINCULO.SQ_SEG_VINCULO IS
'Chave de DM_SEG_VINCULO.';

COMMENT ON COLUMN DM_SEG_VINCULO.SQ_SEGMENTO IS
'Chave de CO_SEGMENTO. Indica a que segmento o registro está ligado.';

COMMENT ON COLUMN DM_SEG_VINCULO.SQ_TIPO_PESSOA IS
'Chave de CO_TIPO_PESSOA. Indica a que tipo de pessoa o registro está ligado.';

COMMENT ON COLUMN DM_SEG_VINCULO.NOME IS
'Nome do vínculo.';

COMMENT ON COLUMN DM_SEG_VINCULO.INTERNO IS
'Indica se o vínculo é interno à organização.';

COMMENT ON COLUMN DM_SEG_VINCULO.CONTRATADO IS
'Indica se a pessoa é contratada ou não pela organização.';

COMMENT ON COLUMN DM_SEG_VINCULO.ORDEM IS
'Indica a ordem do registro nas listagens.';

COMMENT ON COLUMN DM_SEG_VINCULO.ATIVO IS
'Indica se o registro deve ou não ser exibido.';

COMMENT ON COLUMN DM_SEG_VINCULO.PADRAO IS
'Indica se este registro deve ser apresentado como padrão para o usuário.';

/*==============================================================*/
/* Index: IN_DMSEGVIN_SEG                                       */
/*==============================================================*/
CREATE  INDEX IN_DMSEGVIN_SEG ON DM_SEG_VINCULO (
SQ_SEGMENTO,
SQ_TIPO_PESSOA
);

/*==============================================================*/
/* Table: EO_AREA_ATUACAO                                       */
/*==============================================================*/
CREATE TABLE EO_AREA_ATUACAO (
   SQ_AREA_ATUACAO      NUMERIC(10)          NOT NULL,
   SQ_PESSOA            NUMERIC(18)          NOT NULL,
   NOME                 VARCHAR(29)          NOT NULL,
   ATIVO                VARCHAR(1)           NOT NULL DEFAULT 'S'
      CONSTRAINT CKC_ATIVO_EO_AREA_ CHECK (ATIVO IN ('S','N') AND ATIVO = UPPER(ATIVO)),
   CODIGO_EXTERNO       VARCHAR(60)          NULL,
   CONSTRAINT PK_EO_AREA_ATUACAO PRIMARY KEY (SQ_AREA_ATUACAO)
);

COMMENT ON TABLE EO_AREA_ATUACAO IS
'Armazena a tabela de áreas de atuação das unidades organizacionais';

COMMENT ON COLUMN EO_AREA_ATUACAO.SQ_AREA_ATUACAO IS
'Chave de EO_AREA_ATUACAO.';

COMMENT ON COLUMN EO_AREA_ATUACAO.SQ_PESSOA IS
'Chave de CO_PESSOA. Indica a que cliente o registro está ligado.';

COMMENT ON COLUMN EO_AREA_ATUACAO.NOME IS
'Nome da área  de atuação.';

COMMENT ON COLUMN EO_AREA_ATUACAO.ATIVO IS
'Indica se este registro está disponível para ligação a outras tabelas.';

COMMENT ON COLUMN EO_AREA_ATUACAO.CODIGO_EXTERNO IS
'Código desse registro num sistema externo.';

/*==============================================================*/
/* Index: IN_EOAREATU_PESSOA                                    */
/*==============================================================*/
CREATE  INDEX IN_EOAREATU_PESSOA ON EO_AREA_ATUACAO (
SQ_PESSOA,
SQ_AREA_ATUACAO
);

/*==============================================================*/
/* Index: IN_EOAREATU_ATIVO                                     */
/*==============================================================*/
CREATE  INDEX IN_EOAREATU_ATIVO ON EO_AREA_ATUACAO (
ATIVO,
SQ_AREA_ATUACAO
);

/*==============================================================*/
/* Index: IN_EOAREATU_EXTERNO                                   */
/*==============================================================*/
CREATE  INDEX IN_EOAREATU_EXTERNO ON EO_AREA_ATUACAO (
SQ_PESSOA,
CODIGO_EXTERNO,
SQ_AREA_ATUACAO
);

/*==============================================================*/
/* Table: EO_DATA_ESPECIAL                                      */
/*==============================================================*/
CREATE TABLE EO_DATA_ESPECIAL (
   SQ_DATA_ESPECIAL     NUMERIC(18)          NOT NULL,
   CLIENTE              NUMERIC(18)          NOT NULL,
   SQ_PAIS              NUMERIC(18)          NULL,
   CO_UF                VARCHAR(3)           NULL,
   SQ_CIDADE            NUMERIC(18)          NULL,
   TIPO                 VARCHAR(1)           NOT NULL
      CONSTRAINT CKC_TIPO_EO_DATA_ CHECK (TIPO IN ('I','E','S','C','Q','P','D','H')),
   DATA_ESPECIAL        VARCHAR(10)          NULL,
   NOME                 VARCHAR(60)          NOT NULL,
   ABRANGENCIA          VARCHAR(1)           NOT NULL
      CONSTRAINT CKC_ABRANGENCIA_EO_DATA_ CHECK (ABRANGENCIA IN ('I','N','E','M','O')),
   EXPEDIENTE           VARCHAR(1)           NOT NULL
      CONSTRAINT CKC_EXPEDIENTE_EO_DATA_ CHECK (EXPEDIENTE IN ('S','N','M','T')),
   ATIVO                VARCHAR(1)           NOT NULL DEFAULT 'S'
      CONSTRAINT CKC_ATIVO_EO_DATA_ CHECK (ATIVO IN ('S','N') AND ATIVO = UPPER(ATIVO)),
   CONSTRAINT PK_EO_DATA_ESPECIAL PRIMARY KEY (SQ_DATA_ESPECIAL)
);

COMMENT ON TABLE EO_DATA_ESPECIAL IS
'Registra datas especiais da organização, tais como feriados, pontos facultativos e outras.';

COMMENT ON COLUMN EO_DATA_ESPECIAL.SQ_DATA_ESPECIAL IS
'Chave de EO_DATA_ESPECIAL.';

COMMENT ON COLUMN EO_DATA_ESPECIAL.CLIENTE IS
'Chave de CO_PESSOA. Indica a que cliente a data está ligada.';

COMMENT ON COLUMN EO_DATA_ESPECIAL.SQ_PAIS IS
'Se a abrangência for nacional ou estadual, indica a que país ela refere-se.';

COMMENT ON COLUMN EO_DATA_ESPECIAL.CO_UF IS
'Se a abrangência for estadual, indica a que estado ela refere-se.';

COMMENT ON COLUMN EO_DATA_ESPECIAL.SQ_CIDADE IS
'Chave de CO_CIDADE. Se a abrangência for estadual, indica a que cidade ela refere-se.';

COMMENT ON COLUMN EO_DATA_ESPECIAL.TIPO IS
'Tipo da data: I - Invariável, E - Específica, S - Segunda de carnaval, C - Carnaval, Q - Cinzas, P - Paixão, D - Páscoa, H - Corpus Christi.';

COMMENT ON COLUMN EO_DATA_ESPECIAL.DATA_ESPECIAL IS
'Contém dia e mês (DD/MM) quando o tipo for I (Invariável) ou dia, mês e ano (DD/MM/AAAA) se tipo for E (Específico). Nos outros casos, recebe nulo.';

COMMENT ON COLUMN EO_DATA_ESPECIAL.NOME IS
'Nome da ocorrência que torna a data especial.';

COMMENT ON COLUMN EO_DATA_ESPECIAL.ABRANGENCIA IS
'I - Internacional, N - Nacional, E - Estadual, M - Municipal, O - Organização';

COMMENT ON COLUMN EO_DATA_ESPECIAL.EXPEDIENTE IS
'Indica se há expediente na data. S - Sim, N - Não, M - Somente manhã, T - Somente tarde.';

COMMENT ON COLUMN EO_DATA_ESPECIAL.ATIVO IS
'Indica se a data deve ser tratada para novos registros.';

/*==============================================================*/
/* Index: IN_EODATESP_ATIVO                                     */
/*==============================================================*/
CREATE  INDEX IN_EODATESP_ATIVO ON EO_DATA_ESPECIAL (
CLIENTE,
ATIVO,
SQ_DATA_ESPECIAL
);

/*==============================================================*/
/* Table: EO_INDICADOR                                          */
/*==============================================================*/
CREATE TABLE EO_INDICADOR (
   SQ_EOINDICADOR       NUMERIC(18)          NOT NULL,
   CLIENTE              NUMERIC(18)          NOT NULL,
   SQ_TIPO_INDICADOR    NUMERIC(18)          NOT NULL,
   SQ_UNIDADE_MEDIDA    NUMERIC(18)          NOT NULL,
   NOME                 VARCHAR(60)          NOT NULL,
   SIGLA                VARCHAR(15)          NOT NULL,
   DESCRICAO            VARCHAR(2000)        NOT NULL,
   FORMA_AFERICAO       VARCHAR(2000)        NOT NULL,
   FONTE_COMPROVACAO    VARCHAR(2000)        NOT NULL,
   CICLO_AFERICAO       VARCHAR(2000)        NOT NULL,
   ATIVO                VARCHAR(1)           NOT NULL DEFAULT 'S'
      CONSTRAINT CKC_ATIVO_EO_INDIC CHECK (ATIVO IN ('S','N') AND ATIVO = UPPER(ATIVO)),
   VINCULA_META         VARCHAR(1)           NOT NULL DEFAULT 'N'
      CONSTRAINT CKC_VINCULA_META_EO_INDIC CHECK (VINCULA_META IN ('S','N') AND VINCULA_META = UPPER(VINCULA_META)),
   EXIBE_MESA           VARCHAR(1)           NOT NULL DEFAULT 'S'
      CONSTRAINT CKC_EXIBE_MESA_EO_INDIC CHECK (EXIBE_MESA IN ('S','N') AND EXIBE_MESA = UPPER(EXIBE_MESA)),
   CONSTRAINT PK_EO_INDICADOR PRIMARY KEY (SQ_EOINDICADOR)
);

COMMENT ON TABLE EO_INDICADOR IS
'Registra dos dados de um indicador da organização.';

COMMENT ON COLUMN EO_INDICADOR.SQ_EOINDICADOR IS
'Chave de EO_INDICADOR.';

COMMENT ON COLUMN EO_INDICADOR.CLIENTE IS
'Chave de CO_PESSOA. Indica a que cliente o registro está ligado.';

COMMENT ON COLUMN EO_INDICADOR.SQ_TIPO_INDICADOR IS
'Chave de EO_TIPO_INDICADOR. Referencia para o tipo do indicador ligado ao registro.';

COMMENT ON COLUMN EO_INDICADOR.SQ_UNIDADE_MEDIDA IS
'Chave de PE_UNIDADE_MEDIDA. Indica a unidade de medida do indicador.';

COMMENT ON COLUMN EO_INDICADOR.NOME IS
'Nome do indicador.';

COMMENT ON COLUMN EO_INDICADOR.SIGLA IS
'Sigla do indicador.';

COMMENT ON COLUMN EO_INDICADOR.DESCRICAO IS
'Definição do indicador (o quê pretende medir).';

COMMENT ON COLUMN EO_INDICADOR.FORMA_AFERICAO IS
'Forma de aferição do indicador.';

COMMENT ON COLUMN EO_INDICADOR.FONTE_COMPROVACAO IS
'Fonte de comprovação do indicador.';

COMMENT ON COLUMN EO_INDICADOR.CICLO_AFERICAO IS
'Ciclo de aferição sugerido para o indicador.';

COMMENT ON COLUMN EO_INDICADOR.ATIVO IS
'Indica se o indicador pode ser vinculado a novos registros.';

COMMENT ON COLUMN EO_INDICADOR.VINCULA_META IS
'Indica se o registro pode ser vinculado a uma meta.';

COMMENT ON COLUMN EO_INDICADOR.EXIBE_MESA IS
'Indica se o registro deve ser exibido na mesa de trabalho.';

/*==============================================================*/
/* Index: IN_EOIND_NOME                                         */
/*==============================================================*/
CREATE  INDEX IN_EOIND_NOME ON EO_INDICADOR (
CLIENTE,
NOME,
SQ_EOINDICADOR
);

/*==============================================================*/
/* Index: IN_EOIND_SIGLA                                        */
/*==============================================================*/
CREATE  INDEX IN_EOIND_SIGLA ON EO_INDICADOR (
CLIENTE,
SIGLA,
SQ_EOINDICADOR
);

/*==============================================================*/
/* Index: IN_EOIND_ATIVO                                        */
/*==============================================================*/
CREATE  INDEX IN_EOIND_ATIVO ON EO_INDICADOR (
CLIENTE,
ATIVO,
SQ_EOINDICADOR
);

/*==============================================================*/
/* Index: IN_EOIND_META                                         */
/*==============================================================*/
CREATE  INDEX IN_EOIND_META ON EO_INDICADOR (
CLIENTE,
VINCULA_META,
SQ_EOINDICADOR
);

/*==============================================================*/
/* Index: IN_EOIND_MESA                                         */
/*==============================================================*/
CREATE  INDEX IN_EOIND_MESA ON EO_INDICADOR (
CLIENTE,
EXIBE_MESA,
SQ_EOINDICADOR
);

/*==============================================================*/
/* Table: EO_INDICADOR_AFERICAO                                 */
/*==============================================================*/
CREATE TABLE EO_INDICADOR_AFERICAO (
   SQ_EOINDICADOR_AFERICAO NUMERIC(18)          NOT NULL,
   SQ_EOINDICADOR       NUMERIC(18)          NOT NULL,
   DATA_AFERICAO        DATE                 NOT NULL,
   REFERENCIA_INICIO    DATE                 NOT NULL,
   REFERENCIA_FIM       DATE                 NOT NULL,
   SQ_PAIS              NUMERIC(18)          NULL,
   SQ_REGIAO            NUMERIC(18)          NULL,
   CO_UF                VARCHAR(3)           NULL,
   SQ_CIDADE            NUMERIC(18)          NULL,
   CADASTRADOR          NUMERIC(18)          NOT NULL,
   BASE_GEOGRAFICA      NUMERIC(1)           NOT NULL,
   FONTE                VARCHAR(60)          NULL,
   VALOR                NUMERIC(18,4)        NOT NULL DEFAULT 0,
   INCLUSAO             DATE                 NOT NULL DEFAULT 'now()',
   ULTIMA_ALTERACAO     DATE                 NULL DEFAULT 'now()',
   PREVISAO             VARCHAR(1)           NOT NULL DEFAULT 'N'
      CONSTRAINT CKC_PREVISAO_EO_INDIC CHECK (PREVISAO IN ('S','N') AND PREVISAO = UPPER(PREVISAO)),
   OBSERVACAO           VARCHAR(255)         NULL,
   CONSTRAINT PK_EO_INDICADOR_AFERICAO PRIMARY KEY (SQ_EOINDICADOR_AFERICAO)
);

COMMENT ON TABLE EO_INDICADOR_AFERICAO IS
'Registra as aferições do indicador.';

COMMENT ON COLUMN EO_INDICADOR_AFERICAO.SQ_EOINDICADOR_AFERICAO IS
'Chave de EO_INDICADOR_AFERICAO.';

COMMENT ON COLUMN EO_INDICADOR_AFERICAO.SQ_EOINDICADOR IS
'Chave de EO_INDICADOR. Indica a que indicador o registro está ligado.';

COMMENT ON COLUMN EO_INDICADOR_AFERICAO.DATA_AFERICAO IS
'Data de aferição do indicador.';

COMMENT ON COLUMN EO_INDICADOR_AFERICAO.REFERENCIA_INICIO IS
'Início do período de referência da aferição.';

COMMENT ON COLUMN EO_INDICADOR_AFERICAO.REFERENCIA_FIM IS
'Término do período de referência da aferição.';

COMMENT ON COLUMN EO_INDICADOR_AFERICAO.SQ_PAIS IS
'Chave de CO_PAIS. Tem valor apenas quando a aferição é a nivel nacional.';

COMMENT ON COLUMN EO_INDICADOR_AFERICAO.SQ_REGIAO IS
'Chave de CO_REGIAO. Tem valor apenas quando a aferição é a nivel regional.';

COMMENT ON COLUMN EO_INDICADOR_AFERICAO.CO_UF IS
'Chave de CO_UF. Tem valor apenas quando a aferição é a nivel estadual.';

COMMENT ON COLUMN EO_INDICADOR_AFERICAO.SQ_CIDADE IS
'Chave de CO_CIDADE. Tem valor apenas quando a aferição é a nivel municipal.';

COMMENT ON COLUMN EO_INDICADOR_AFERICAO.CADASTRADOR IS
'Chave de CO_PESSOA. Indica o responsável pelo cadastramento ou pela última alteração no registro.';

COMMENT ON COLUMN EO_INDICADOR_AFERICAO.BASE_GEOGRAFICA IS
'Indica a que base geográfica a aferição aplica-se. 1 - Nacional, 2 - Regional, 3 - Estadual, 4 - Municipal, 5 - Organizacional.';

COMMENT ON COLUMN EO_INDICADOR_AFERICAO.FONTE IS
'Fonte de aferição do indicador.';

COMMENT ON COLUMN EO_INDICADOR_AFERICAO.VALOR IS
'Valor aferido.';

COMMENT ON COLUMN EO_INDICADOR_AFERICAO.INCLUSAO IS
'Data de inclusão do registro.';

COMMENT ON COLUMN EO_INDICADOR_AFERICAO.ULTIMA_ALTERACAO IS
'Data da última alteração no registro.';

COMMENT ON COLUMN EO_INDICADOR_AFERICAO.PREVISAO IS
'Indica se o valor registrado é uma previsão.';

COMMENT ON COLUMN EO_INDICADOR_AFERICAO.OBSERVACAO IS
'Observações quaisquer, julgadas relevantes pelo usuário.';

/*==============================================================*/
/* Index: IN_EOINDAFE_DATA                                      */
/*==============================================================*/
CREATE  INDEX IN_EOINDAFE_DATA ON EO_INDICADOR_AFERICAO (
SQ_EOINDICADOR,
DATA_AFERICAO,
SQ_EOINDICADOR_AFERICAO
);

/*==============================================================*/
/* Index: IN_EOINDAFE_INICIO                                    */
/*==============================================================*/
CREATE  INDEX IN_EOINDAFE_INICIO ON EO_INDICADOR_AFERICAO (
SQ_EOINDICADOR,
REFERENCIA_INICIO,
SQ_EOINDICADOR_AFERICAO
);

/*==============================================================*/
/* Index: IN_EOINDAFE_FIM                                       */
/*==============================================================*/
CREATE  INDEX IN_EOINDAFE_FIM ON EO_INDICADOR_AFERICAO (
SQ_EOINDICADOR,
REFERENCIA_FIM,
SQ_EOINDICADOR_AFERICAO
);

/*==============================================================*/
/* Index: IN_EOINDAFE_UNICO                                     */
/*==============================================================*/
CREATE UNIQUE INDEX IN_EOINDAFE_UNICO ON EO_INDICADOR_AFERICAO (
SQ_EOINDICADOR,
DATA_AFERICAO,
BASE_GEOGRAFICA
);

/*==============================================================*/
/* Table: EO_INDICADOR_AFERIDOR                                 */
/*==============================================================*/
CREATE TABLE EO_INDICADOR_AFERIDOR (
   SQ_EOINDICADOR_AFERIDOR NUMERIC(18)          NOT NULL,
   SQ_EOINDICADOR       NUMERIC(18)          NOT NULL,
   SQ_PESSOA            NUMERIC(18)          NOT NULL,
   PRAZO_DEFINIDO       VARCHAR(1)           NOT NULL DEFAULT 'N'
      CONSTRAINT CKC_PRAZO_DEFINIDO_EO_INDIC CHECK (PRAZO_DEFINIDO IN ('S','N') AND PRAZO_DEFINIDO = UPPER(PRAZO_DEFINIDO)),
   INICIO               DATE                 NOT NULL,
   FIM                  DATE                 NOT NULL,
   CONSTRAINT PK_EO_INDICADOR_AFERIDOR PRIMARY KEY (SQ_EOINDICADOR_AFERIDOR)
);

COMMENT ON TABLE EO_INDICADOR_AFERIDOR IS
'Registra as pessoas responsáveis pela aferição do indicador.';

COMMENT ON COLUMN EO_INDICADOR_AFERIDOR.SQ_EOINDICADOR_AFERIDOR IS
'Chave de EO_INDICADOR_AFERIDOR.';

COMMENT ON COLUMN EO_INDICADOR_AFERIDOR.SQ_EOINDICADOR IS
'Chave de EO_INDICADOR. Indica a que indicador o registro está ligado.';

COMMENT ON COLUMN EO_INDICADOR_AFERIDOR.SQ_PESSOA IS
'Chave de CO_PESSOA. Indica a que pessoa o registro está ligado.';

COMMENT ON COLUMN EO_INDICADOR_AFERIDOR.PRAZO_DEFINIDO IS
'Indica se a responsabilidade tem prazo definido.';

COMMENT ON COLUMN EO_INDICADOR_AFERIDOR.INICIO IS
'Início da responsabilidade pela aferição.';

COMMENT ON COLUMN EO_INDICADOR_AFERIDOR.FIM IS
'Se o prazo de responsabilidade é definido, indica o término da responsabilidade. Se for indefinido, armazena 31/12/2100.';

/*==============================================================*/
/* Index: IN_EOINDAFR_INDICADOR                                 */
/*==============================================================*/
CREATE  INDEX IN_EOINDAFR_INDICADOR ON EO_INDICADOR_AFERIDOR (
SQ_EOINDICADOR,
SQ_EOINDICADOR_AFERIDOR
);

/*==============================================================*/
/* Index: IN_EOINDAFR_PESSOA                                    */
/*==============================================================*/
CREATE  INDEX IN_EOINDAFR_PESSOA ON EO_INDICADOR_AFERIDOR (
SQ_PESSOA,
SQ_EOINDICADOR_AFERIDOR
);

/*==============================================================*/
/* Index: IN_EOINDAFR_INICIO                                    */
/*==============================================================*/
CREATE  INDEX IN_EOINDAFR_INICIO ON EO_INDICADOR_AFERIDOR (
INICIO,
SQ_EOINDICADOR_AFERIDOR
);

/*==============================================================*/
/* Index: IN_EOINDAFR_FIM                                       */
/*==============================================================*/
CREATE  INDEX IN_EOINDAFR_FIM ON EO_INDICADOR_AFERIDOR (
FIM,
SQ_EOINDICADOR_AFERIDOR
);

/*==============================================================*/
/* Table: EO_INDICADOR_AGENDA                                   */
/*==============================================================*/
CREATE TABLE EO_INDICADOR_AGENDA (
   SQ_EOINDICADOR_AGENDA NUMERIC(18)          NOT NULL,
   SQ_EOINDICADOR       NUMERIC(18)          NOT NULL,
   PADRAO_RECORRENCIA   NUMERIC(1)           NOT NULL,
   TIPO_RECORRENCIA     NUMERIC(2)           NOT NULL,
   PRAZO_DEFINIDO       VARCHAR(1)           NOT NULL DEFAULT 'N'
      CONSTRAINT CKC_PRAZO_DEFINIDO_EOINDAGE CHECK (PRAZO_DEFINIDO IN ('S','N') AND PRAZO_DEFINIDO = UPPER(PRAZO_DEFINIDO)),
   INICIO               DATE                 NOT NULL,
   FIM                  DATE                 NOT NULL,
   LIMITE_OCORRENCIAS   NUMERIC(4)           NULL,
   INTERVALO_OCORRENCIAS NUMERIC(4)           NULL,
   DIA_UTIL             VARCHAR(1)           NOT NULL DEFAULT 'S'
      CONSTRAINT CKC_DIA_UTIL_EOINDAGE CHECK (DIA_UTIL IN ('S','N') AND DIA_UTIL = UPPER(DIA_UTIL)),
   DIA_MES              NUMERIC(2)           NULL,
   DIA_SEMANA           NUMERIC(2)           NULL,
   ORDINAL_SEMANA       NUMERIC(1)           NULL,
   MES                  NUMERIC(2)           NULL,
   ATIVO                VARCHAR(1)           NOT NULL DEFAULT 'S'
      CONSTRAINT CKC_ATIVO_EOINDAGE CHECK (ATIVO IN ('S','N') AND ATIVO = UPPER(ATIVO)),
   CONSTRAINT PK_EO_INDICADOR_AGENDA PRIMARY KEY (SQ_EOINDICADOR_AGENDA)
);

COMMENT ON TABLE EO_INDICADOR_AGENDA IS
'Registra o agendamento de aferições de um indicador.';

COMMENT ON COLUMN EO_INDICADOR_AGENDA.SQ_EOINDICADOR_AGENDA IS
'Chave de EO_INDICADOR_AGENDA.';

COMMENT ON COLUMN EO_INDICADOR_AGENDA.SQ_EOINDICADOR IS
'Chave de EO_INDICADOR. Indica a que indicador o registro está ligado.';

COMMENT ON COLUMN EO_INDICADOR_AGENDA.PADRAO_RECORRENCIA IS
'Indica o padrão de recorrência da agenda: 1 - Diário, 2 - Semanal, 3 - Mensal, 4 - Anual.';

COMMENT ON COLUMN EO_INDICADOR_AGENDA.TIPO_RECORRENCIA IS
'Indica o tipo de recorrência: 1 - A cada x dias/semanas/meses; 2 - Dia específico; 3 - Dia relativo.';

COMMENT ON COLUMN EO_INDICADOR_AGENDA.PRAZO_DEFINIDO IS
'Indica se o prazo de agendamento tem término definido.';

COMMENT ON COLUMN EO_INDICADOR_AGENDA.INICIO IS
'Data de início do agendamento.';

COMMENT ON COLUMN EO_INDICADOR_AGENDA.FIM IS
'Se o prazo de agendamento for definido e baseado em datas, registra a data de término. Se for indefinido, armazena 31/12/2100.';

COMMENT ON COLUMN EO_INDICADOR_AGENDA.LIMITE_OCORRENCIAS IS
'Se o prazo de agendamento for definido e baseado em número de ocorrências, registra esse número.';

COMMENT ON COLUMN EO_INDICADOR_AGENDA.INTERVALO_OCORRENCIAS IS
'Informa o intervalo entre os agendamentos (dias, semanas, meses ou anos).';

COMMENT ON COLUMN EO_INDICADOR_AGENDA.DIA_UTIL IS
'Indica se o agendamento deve ser feito apenas em dias úteis.';

COMMENT ON COLUMN EO_INDICADOR_AGENDA.DIA_MES IS
'Dia do mês em que deve ocorrer o agendamento.';

COMMENT ON COLUMN EO_INDICADOR_AGENDA.DIA_SEMANA IS
'Dia da semana do agendamento: 1 - Domingo ... 7 - Sábado, 8 - Dia, 9 - Dia da semana, 10 - Final de semana.';

COMMENT ON COLUMN EO_INDICADOR_AGENDA.ORDINAL_SEMANA IS
'Indica o ordinal da semana: 1 - Primeiro, 2 - Segundo, 3 - Terceiro, 4 - Quarto, 5 - Último.';

COMMENT ON COLUMN EO_INDICADOR_AGENDA.MES IS
'Indica o mês em que deve ocorrer o agendamento. Apenas para PADRAO=4.';

COMMENT ON COLUMN EO_INDICADOR_AGENDA.ATIVO IS
'Indica se o agendamento está ativo.';

/*==============================================================*/
/* Index: IN_EOINDAGE_INICIO                                    */
/*==============================================================*/
CREATE  INDEX IN_EOINDAGE_INICIO ON EO_INDICADOR_AGENDA (
SQ_EOINDICADOR,
INICIO,
SQ_EOINDICADOR_AGENDA
);

/*==============================================================*/
/* Index: IN_EOINDAGE_FIM                                       */
/*==============================================================*/
CREATE  INDEX IN_EOINDAGE_FIM ON EO_INDICADOR_AGENDA (
SQ_EOINDICADOR,
FIM,
SQ_EOINDICADOR_AGENDA
);

/*==============================================================*/
/* Table: EO_LOCALIZACAO                                        */
/*==============================================================*/
CREATE TABLE EO_LOCALIZACAO (
   SQ_LOCALIZACAO       NUMERIC(10)          NOT NULL,
   SQ_UNIDADE           NUMERIC(10)          NOT NULL,
   SQ_PESSOA_ENDERECO   NUMERIC(18)          NULL,
   CLIENTE              NUMERIC(18)          NOT NULL,
   NOME                 VARCHAR(30)          NOT NULL,
   TELEFONE             VARCHAR(12)          NULL,
   TELEFONE2            VARCHAR(12)          NULL,
   RAMAL                VARCHAR(6)           NULL,
   FAX                  VARCHAR(12)          NULL,
   ATIVO                VARCHAR(1)           NOT NULL DEFAULT 'S'
      CONSTRAINT CKC_ATIVO_EO_LOCAL CHECK (ATIVO IN ('S','N') AND ATIVO = UPPER(ATIVO)),
   CODIGO_EXTERNO       VARCHAR(60)          NULL,
   ALMOXARIFADO_CONSUMO VARCHAR(1)           NOT NULL DEFAULT 'N'
      CONSTRAINT CKC_ALMOXARIFADO_CONS_EO_LOCAL CHECK (ALMOXARIFADO_CONSUMO IN ('S','N') AND ALMOXARIFADO_CONSUMO = UPPER(ALMOXARIFADO_CONSUMO)),
   DEPOSITO_PERMANENTE  VARCHAR(1)           NOT NULL DEFAULT 'N'
      CONSTRAINT CKC_DEPOSITO_PERMANEN_EO_LOCAL CHECK (DEPOSITO_PERMANENTE IN ('S','N') AND DEPOSITO_PERMANENTE = UPPER(DEPOSITO_PERMANENTE)),
   ARQUIVO_SETORIAL     VARCHAR(1)           NOT NULL DEFAULT 'N'
      CONSTRAINT CKC_ARQUIVO_SETORIAL_EO_LOCAL CHECK (ARQUIVO_SETORIAL IN ('S','N') AND ARQUIVO_SETORIAL = UPPER(ARQUIVO_SETORIAL)),
   ARQUIVO_CENTRAL      VARCHAR(1)           NOT NULL DEFAULT 'N'
      CONSTRAINT CKC_ARQUIVO_CENTRAL_EO_LOCAL CHECK (ARQUIVO_CENTRAL IN ('S','N') AND ARQUIVO_CENTRAL = UPPER(ARQUIVO_CENTRAL)),
   CONSTRAINT PK_EO_LOCALIZACAO PRIMARY KEY (SQ_LOCALIZACAO)
);

COMMENT ON TABLE EO_LOCALIZACAO IS
'Armazena a tabela de localizações de unidades';

COMMENT ON COLUMN EO_LOCALIZACAO.SQ_LOCALIZACAO IS
'Chave de EO_LOCALIZACAO.';

COMMENT ON COLUMN EO_LOCALIZACAO.SQ_UNIDADE IS
'Chave de EO_UNIDADE. Indica a que unidade o registro está ligado.';

COMMENT ON COLUMN EO_LOCALIZACAO.SQ_PESSOA_ENDERECO IS
'Chave de CO_PESSOA_ENDERECO. Indica a que endereço o registro está ligado.';

COMMENT ON COLUMN EO_LOCALIZACAO.CLIENTE IS
'Chave de CO_PESSOA. Indica a que cliente o registro está ligado.';

COMMENT ON COLUMN EO_LOCALIZACAO.NOME IS
'Nome da localização.';

COMMENT ON COLUMN EO_LOCALIZACAO.TELEFONE IS
'Telefone da localização';

COMMENT ON COLUMN EO_LOCALIZACAO.TELEFONE2 IS
'Outro telefone da localização';

COMMENT ON COLUMN EO_LOCALIZACAO.RAMAL IS
'Ramal da localização.';

COMMENT ON COLUMN EO_LOCALIZACAO.FAX IS
'Fax do local.';

COMMENT ON COLUMN EO_LOCALIZACAO.ATIVO IS
'Indica se este registro está disponível para ligação a outras tabelas.';

COMMENT ON COLUMN EO_LOCALIZACAO.CODIGO_EXTERNO IS
'Código desse registro num sistema externo.';

COMMENT ON COLUMN EO_LOCALIZACAO.ALMOXARIFADO_CONSUMO IS
'Indica se o local é almoxarifado de materiais de consumo.';

COMMENT ON COLUMN EO_LOCALIZACAO.DEPOSITO_PERMANENTE IS
'Indica se o local é depósito de materiais permanentes.';

COMMENT ON COLUMN EO_LOCALIZACAO.ARQUIVO_SETORIAL IS
'Indica se o local é arquivo setorial de protocolo.';

COMMENT ON COLUMN EO_LOCALIZACAO.ARQUIVO_CENTRAL IS
'Indica se o local é arquivo central de protocolo.';

/*==============================================================*/
/* Index: IN_EOLOC_UNIDADE                                      */
/*==============================================================*/
CREATE  INDEX IN_EOLOC_UNIDADE ON EO_LOCALIZACAO (
CLIENTE,
SQ_UNIDADE,
SQ_LOCALIZACAO
);

/*==============================================================*/
/* Index: IN_EOLOC_ENDERECO                                     */
/*==============================================================*/
CREATE  INDEX IN_EOLOC_ENDERECO ON EO_LOCALIZACAO (
CLIENTE,
SQ_PESSOA_ENDERECO,
SQ_LOCALIZACAO
);

/*==============================================================*/
/* Index: IN_EOLOC_ATIVO                                        */
/*==============================================================*/
CREATE  INDEX IN_EOLOC_ATIVO ON EO_LOCALIZACAO (
CLIENTE,
ATIVO,
SQ_LOCALIZACAO
);

/*==============================================================*/
/* Index: IN_EOLOC_EXTERNO                                      */
/*==============================================================*/
CREATE  INDEX IN_EOLOC_EXTERNO ON EO_LOCALIZACAO (
CLIENTE,
CODIGO_EXTERNO,
SQ_LOCALIZACAO
);

/*==============================================================*/
/* Index: IN_EOLOC_ALMOXARIFADO                                 */
/*==============================================================*/
CREATE  INDEX IN_EOLOC_ALMOXARIFADO ON EO_LOCALIZACAO (
CLIENTE,
ALMOXARIFADO_CONSUMO,
SQ_LOCALIZACAO
);

/*==============================================================*/
/* Index: IN_EOLOC_DEPOSITO                                     */
/*==============================================================*/
CREATE  INDEX IN_EOLOC_DEPOSITO ON EO_LOCALIZACAO (
CLIENTE,
DEPOSITO_PERMANENTE,
SQ_LOCALIZACAO
);

/*==============================================================*/
/* Index: IN_EOLOC_SETORIAL                                     */
/*==============================================================*/
CREATE  INDEX IN_EOLOC_SETORIAL ON EO_LOCALIZACAO (
CLIENTE,
ARQUIVO_SETORIAL,
SQ_LOCALIZACAO
);

/*==============================================================*/
/* Index: IN_EOLOC_CENTRAL                                      */
/*==============================================================*/
CREATE  INDEX IN_EOLOC_CENTRAL ON EO_LOCALIZACAO (
CLIENTE,
ARQUIVO_CENTRAL,
SQ_LOCALIZACAO
);

/*==============================================================*/
/* Table: EO_RECURSO                                            */
/*==============================================================*/
CREATE TABLE EO_RECURSO (
   SQ_RECURSO           NUMERIC(18)          NOT NULL,
   CLIENTE              NUMERIC(18)          NOT NULL,
   SQ_TIPO_RECURSO      NUMERIC(18)          NOT NULL,
   SQ_UNIDADE_MEDIDA    NUMERIC(18)          NULL,
   UNIDADE_GESTORA      NUMERIC(10)          NOT NULL,
   NOME                 VARCHAR(100)         NOT NULL,
   CODIGO               VARCHAR(40)          NULL,
   DESCRICAO            VARCHAR(2000)        NULL,
   FINALIDADE           VARCHAR(2000)        NULL,
   DISPONIBILIDADE_TIPO NUMERIC(1)           NULL,
   ATIVO                VARCHAR(1)           NOT NULL DEFAULT 'S'
      CONSTRAINT CKC_ATIVO_EO_RECUR CHECK (ATIVO IN ('S','N') AND ATIVO = UPPER(ATIVO)),
   EXIBE_MESA           VARCHAR(1)           NOT NULL DEFAULT 'N'
      CONSTRAINT CKC_EXIBE_MESA_EO_RECUR CHECK (EXIBE_MESA IN ('S','N') AND EXIBE_MESA = UPPER(EXIBE_MESA)),
   CONSTRAINT PK_EO_RECURSO PRIMARY KEY (SQ_RECURSO)
);

COMMENT ON TABLE EO_RECURSO IS
'Registra informações sobre o pool de recursos.';

COMMENT ON COLUMN EO_RECURSO.SQ_RECURSO IS
'Chave de EO_RECURSO.';

COMMENT ON COLUMN EO_RECURSO.CLIENTE IS
'Chave de CO_PESSOA. Indica a que cliente o registro está ligado.';

COMMENT ON COLUMN EO_RECURSO.SQ_TIPO_RECURSO IS
'Chave de EO_TIPO_RECURSO. Indica o tipo do recurso.';

COMMENT ON COLUMN EO_RECURSO.SQ_UNIDADE_MEDIDA IS
'Chave de PE_UNIDADE_MEDIDA.';

COMMENT ON COLUMN EO_RECURSO.UNIDADE_GESTORA IS
'Chave de EO_UNIDADE. Indica a unidade gestora do recurso.';

COMMENT ON COLUMN EO_RECURSO.NOME IS
'Nome do recurso.';

COMMENT ON COLUMN EO_RECURSO.CODIGO IS
'Código de identificação única do recurso (RGP, matrícula, CPF, placa etc.)';

COMMENT ON COLUMN EO_RECURSO.DESCRICAO IS
'Descrição do recurso.';

COMMENT ON COLUMN EO_RECURSO.FINALIDADE IS
'Finalidade do recurso.';

COMMENT ON COLUMN EO_RECURSO.DISPONIBILIDADE_TIPO IS
'Indica a disponibilidade do recurso. 1 - Indefinida; 2 - Definida com limite de unidades; 3 - Definida sem limite de unidades.';

COMMENT ON COLUMN EO_RECURSO.ATIVO IS
'Indica se o recurso pode ser ligado a novos registros.';

COMMENT ON COLUMN EO_RECURSO.EXIBE_MESA IS
'Indica se o recurso deve ser exibido na mesa de trabalho.';

/*==============================================================*/
/* Index: IN_EOREC_GESTORA                                      */
/*==============================================================*/
CREATE  INDEX IN_EOREC_GESTORA ON EO_RECURSO (
CLIENTE,
UNIDADE_GESTORA,
SQ_RECURSO
);

/*==============================================================*/
/* Index: IN_EOREC_TIPO                                         */
/*==============================================================*/
CREATE  INDEX IN_EOREC_TIPO ON EO_RECURSO (
CLIENTE,
SQ_TIPO_RECURSO,
SQ_RECURSO
);

/*==============================================================*/
/* Index: IN_EOREC_ATIVO                                        */
/*==============================================================*/
CREATE  INDEX IN_EOREC_ATIVO ON EO_RECURSO (
CLIENTE,
ATIVO,
SQ_RECURSO
);

/*==============================================================*/
/* Index: IN_EOREC_MESA                                         */
/*==============================================================*/
CREATE  INDEX IN_EOREC_MESA ON EO_RECURSO (
CLIENTE,
EXIBE_MESA,
SQ_RECURSO
);

/*==============================================================*/
/* Table: EO_RECURSO_DISPONIVEL                                 */
/*==============================================================*/
CREATE TABLE EO_RECURSO_DISPONIVEL (
   SQ_RECURSO_DISPONIVEL NUMERIC(18)          NOT NULL,
   SQ_RECURSO           NUMERIC(18)          NOT NULL,
   INICIO               DATE                 NULL,
   FIM                  DATE                 NULL,
   VALOR                NUMERIC(18,2)        NOT NULL,
   UNIDADES             NUMERIC(18,2)        NULL,
   LIMITE_DIARIO        NUMERIC(18,2)        NULL,
   DIA_UTIL             VARCHAR(1)           NOT NULL DEFAULT 'S'
      CONSTRAINT CKC_DIA_UTIL_EO_RECUR CHECK (DIA_UTIL IN ('S','N') AND DIA_UTIL = UPPER(DIA_UTIL)),
   CONSTRAINT PK_EO_RECURSO_DISPONIVEL PRIMARY KEY (SQ_RECURSO_DISPONIVEL)
);

COMMENT ON TABLE EO_RECURSO_DISPONIVEL IS
'Registra períodos de disponibilidade do recurso.';

COMMENT ON COLUMN EO_RECURSO_DISPONIVEL.SQ_RECURSO_DISPONIVEL IS
'Chave de EO_RECURSO_DISPONIVEL.';

COMMENT ON COLUMN EO_RECURSO_DISPONIVEL.SQ_RECURSO IS
'Chave de EO_RECURSO. Indica a que recurso o registro está vinculado.';

COMMENT ON COLUMN EO_RECURSO_DISPONIVEL.INICIO IS
'Registra o inicio da disponibilidade, quando ela for do tipo 2 ou 3.';

COMMENT ON COLUMN EO_RECURSO_DISPONIVEL.FIM IS
'Registra o fim da disponibilidade, quando ela for do tipo 2 ou 3.';

COMMENT ON COLUMN EO_RECURSO_DISPONIVEL.VALOR IS
'Valor da unidade definida para o recurso.';

COMMENT ON COLUMN EO_RECURSO_DISPONIVEL.UNIDADES IS
'Quantidade disponível de unidades do recurso no período.';

COMMENT ON COLUMN EO_RECURSO_DISPONIVEL.LIMITE_DIARIO IS
'Registra o limite de unidades que podem ser consumidas diariamente, se o tipo de disponibilidade for 2 ou 3.';

COMMENT ON COLUMN EO_RECURSO_DISPONIVEL.DIA_UTIL IS
'Indica se o recurso pode ser alocado apenas em dias úteis. Se for igual a não, permite a alocação em qualquer dia.';

/*==============================================================*/
/* Index: IN_EORECDIS_INICIO                                    */
/*==============================================================*/
CREATE  INDEX IN_EORECDIS_INICIO ON EO_RECURSO_DISPONIVEL (
SQ_RECURSO,
INICIO,
SQ_RECURSO_DISPONIVEL
);

/*==============================================================*/
/* Index: IN_EORECDIS_FIM                                       */
/*==============================================================*/
CREATE  INDEX IN_EORECDIS_FIM ON EO_RECURSO_DISPONIVEL (
SQ_RECURSO,
FIM,
SQ_RECURSO_DISPONIVEL
);

/*==============================================================*/
/* Table: EO_RECURSO_INDISPONIVEL                               */
/*==============================================================*/
CREATE TABLE EO_RECURSO_INDISPONIVEL (
   SQ_RECURSO_INDISPONIVEL NUMERIC(18)          NOT NULL,
   SQ_RECURSO           NUMERIC(18)          NOT NULL,
   INICIO               DATE                 NOT NULL,
   FIM                  DATE                 NOT NULL,
   JUSTIFICATIVA        VARCHAR(2000)        NOT NULL,
   CONSTRAINT PK_EO_RECURSO_INDISPONIVEL PRIMARY KEY (SQ_RECURSO_INDISPONIVEL)
);

COMMENT ON TABLE EO_RECURSO_INDISPONIVEL IS
'Registra períodos de indisponibilidade do recurso.';

COMMENT ON COLUMN EO_RECURSO_INDISPONIVEL.SQ_RECURSO_INDISPONIVEL IS
'Chave de EO_RECURSO_INDISPONIVEL.';

COMMENT ON COLUMN EO_RECURSO_INDISPONIVEL.SQ_RECURSO IS
'Chave de EO_RECURSO. Indica a que recurso o registro está vinculado.';

COMMENT ON COLUMN EO_RECURSO_INDISPONIVEL.INICIO IS
'Data de início da indisponibilidade do recurso.';

COMMENT ON COLUMN EO_RECURSO_INDISPONIVEL.FIM IS
'Data de término da indisponibilidade do recurso.';

COMMENT ON COLUMN EO_RECURSO_INDISPONIVEL.JUSTIFICATIVA IS
'Justificativa para a indisponibilidade do recurso.';

/*==============================================================*/
/* Index: IN_EORECIND_INICIO                                    */
/*==============================================================*/
CREATE  INDEX IN_EORECIND_INICIO ON EO_RECURSO_INDISPONIVEL (
SQ_RECURSO,
INICIO,
SQ_RECURSO_INDISPONIVEL
);

/*==============================================================*/
/* Index: IN_EORECIND_FIM                                       */
/*==============================================================*/
CREATE  INDEX IN_EORECIND_FIM ON EO_RECURSO_INDISPONIVEL (
SQ_RECURSO,
FIM,
SQ_RECURSO_INDISPONIVEL
);

/*==============================================================*/
/* Table: EO_RECURSO_MENU                                       */
/*==============================================================*/
CREATE TABLE EO_RECURSO_MENU (
   SQ_RECURSO           NUMERIC(18)          NOT NULL,
   SQ_MENU              NUMERIC(18)          NOT NULL,
   CONSTRAINT PK_EO_RECURSO_MENU PRIMARY KEY (SQ_RECURSO, SQ_MENU)
);

COMMENT ON TABLE EO_RECURSO_MENU IS
'Registra que recursos estão disponíveis para cada opção do menu.';

COMMENT ON COLUMN EO_RECURSO_MENU.SQ_RECURSO IS
'Chave de EO_RECURSO. Indica a que recurso o registro está ligado.';

COMMENT ON COLUMN EO_RECURSO_MENU.SQ_MENU IS
'Chave de SIW_MENU. Indica a que opção do menu o registro está ligado.';

/*==============================================================*/
/* Index: IN_EORECMEN_INVERSA                                   */
/*==============================================================*/
CREATE  INDEX IN_EORECMEN_INVERSA ON EO_RECURSO_MENU (
SQ_MENU,
SQ_RECURSO
);

/*==============================================================*/
/* Table: EO_TIPO_INDICADOR                                     */
/*==============================================================*/
CREATE TABLE EO_TIPO_INDICADOR (
   SQ_TIPO_INDICADOR    NUMERIC(18)          NOT NULL,
   CLIENTE              NUMERIC(18)          NOT NULL,
   NOME                 VARCHAR(25)          NOT NULL,
   ATIVO                VARCHAR(1)           NOT NULL DEFAULT 'S'
      CONSTRAINT CKC_ATIVO_EOTIPIND CHECK (ATIVO IN ('S','N') AND ATIVO = UPPER(ATIVO)),
   CONSTRAINT PK_EO_TIPO_INDICADOR PRIMARY KEY (SQ_TIPO_INDICADOR)
);

COMMENT ON TABLE EO_TIPO_INDICADOR IS
'Registra os tipos de indicador.';

COMMENT ON COLUMN EO_TIPO_INDICADOR.SQ_TIPO_INDICADOR IS
'Chave de EO_TIPO_INDICADOR.';

COMMENT ON COLUMN EO_TIPO_INDICADOR.CLIENTE IS
'Chave de CO_PESSOA. Indica a que cliente o registro está ligado.';

COMMENT ON COLUMN EO_TIPO_INDICADOR.NOME IS
'Nome do tipo de indicador.';

COMMENT ON COLUMN EO_TIPO_INDICADOR.ATIVO IS
'Indica se o tipo do indicador pode ser vinculado a novos registros.';

/*==============================================================*/
/* Index: IN_EOTIPIND_ATIVO                                     */
/*==============================================================*/
CREATE  INDEX IN_EOTIPIND_ATIVO ON EO_TIPO_INDICADOR (
CLIENTE,
ATIVO,
SQ_TIPO_INDICADOR
);

/*==============================================================*/
/* Index: IN_EOTIPIND_NOME                                      */
/*==============================================================*/
CREATE  INDEX IN_EOTIPIND_NOME ON EO_TIPO_INDICADOR (
CLIENTE,
NOME,
SQ_TIPO_INDICADOR
);

/*==============================================================*/
/* Table: EO_TIPO_RECURSO                                       */
/*==============================================================*/
CREATE TABLE EO_TIPO_RECURSO (
   SQ_TIPO_RECURSO      NUMERIC(18)          NOT NULL,
   CLIENTE              NUMERIC(18)          NOT NULL,
   SQ_TIPO_PAI          NUMERIC(18)          NULL,
   UNIDADE_GESTORA      NUMERIC(10)          NOT NULL,
   NOME                 VARCHAR(30)          NOT NULL,
   SIGLA                VARCHAR(10)          NOT NULL,
   DESCRICAO            VARCHAR(2000)        NOT NULL,
   ATIVO                VARCHAR(1)           NOT NULL DEFAULT 'S'
      CONSTRAINT CKC_ATIVO_PETIPREC CHECK (ATIVO IN ('S','N') AND ATIVO = UPPER(ATIVO)),
   CONSTRAINT PK_EO_TIPO_RECURSO PRIMARY KEY (SQ_TIPO_RECURSO)
);

COMMENT ON TABLE EO_TIPO_RECURSO IS
'Registra os tipos de recurso.';

COMMENT ON COLUMN EO_TIPO_RECURSO.SQ_TIPO_RECURSO IS
'Chave de EO_TIPO_RECURSO.';

COMMENT ON COLUMN EO_TIPO_RECURSO.CLIENTE IS
'Chave de CO_PESSOA. Indica a que cliente o registro está ligado.';

COMMENT ON COLUMN EO_TIPO_RECURSO.SQ_TIPO_PAI IS
'Chave de EO_TIPO_RECURSO. Indica a que tipo de recurso o registro está ligado.';

COMMENT ON COLUMN EO_TIPO_RECURSO.UNIDADE_GESTORA IS
'Chave de EO_UNIDADE. Indica a unidade responsável pela gestão dos recursos deste tipo.';

COMMENT ON COLUMN EO_TIPO_RECURSO.NOME IS
'Nome do tipo de recurso.';

COMMENT ON COLUMN EO_TIPO_RECURSO.SIGLA IS
'Sigla do tipo de recurso.';

COMMENT ON COLUMN EO_TIPO_RECURSO.DESCRICAO IS
'Descrição do tipo de recurso.';

COMMENT ON COLUMN EO_TIPO_RECURSO.ATIVO IS
'Indica se este tipo de recurso pode ser vinculado a novos registros.';

/*==============================================================*/
/* Index: IN_EOTIPREC_CLIENTE                                   */
/*==============================================================*/
CREATE  INDEX IN_EOTIPREC_CLIENTE ON EO_TIPO_RECURSO (
CLIENTE,
SQ_TIPO_RECURSO
);

/*==============================================================*/
/* Index: IN_EOTIPREC_NOME                                      */
/*==============================================================*/
CREATE  INDEX IN_EOTIPREC_NOME ON EO_TIPO_RECURSO (
CLIENTE,
NOME,
SQ_TIPO_RECURSO
);

/*==============================================================*/
/* Index: IN_EOTIPREC_SIGLA                                     */
/*==============================================================*/
CREATE  INDEX IN_EOTIPREC_SIGLA ON EO_TIPO_RECURSO (
CLIENTE,
SIGLA,
SQ_TIPO_RECURSO
);

/*==============================================================*/
/* Index: IN_EOTIPREC_ATIVO                                     */
/*==============================================================*/
CREATE  INDEX IN_EOTIPREC_ATIVO ON EO_TIPO_RECURSO (
CLIENTE,
ATIVO,
SQ_TIPO_RECURSO
);

/*==============================================================*/
/* Index: IN_EOTIPREC_PAI                                       */
/*==============================================================*/
CREATE  INDEX IN_EOTIPREC_PAI ON EO_TIPO_RECURSO (
CLIENTE,
SQ_TIPO_PAI,
SQ_TIPO_RECURSO
);

/*==============================================================*/
/* Index: IN_EOTIPREC_GESTORA                                   */
/*==============================================================*/
CREATE  INDEX IN_EOTIPREC_GESTORA ON EO_TIPO_RECURSO (
CLIENTE,
UNIDADE_GESTORA,
SQ_TIPO_RECURSO
);

/*==============================================================*/
/* Table: EO_TIPO_UNIDADE                                       */
/*==============================================================*/
CREATE TABLE EO_TIPO_UNIDADE (
   SQ_TIPO_UNIDADE      NUMERIC(10)          NOT NULL,
   SQ_PESSOA            NUMERIC(18)          NOT NULL,
   NOME                 VARCHAR(25)          NOT NULL,
   ATIVO                VARCHAR(1)           NOT NULL DEFAULT 'S'
      CONSTRAINT CKC_ATIVO_EOTIPPOSTO CHECK (ATIVO IN ('S','N') AND ATIVO = UPPER(ATIVO)),
   CODIGO_EXTERNO       VARCHAR(60)          NULL,
   CONSTRAINT PK_EO_TIPO_UNIDADE PRIMARY KEY (SQ_TIPO_UNIDADE)
);

COMMENT ON TABLE EO_TIPO_UNIDADE IS
'Armazena os tipos de unidades organizacionais';

COMMENT ON COLUMN EO_TIPO_UNIDADE.SQ_TIPO_UNIDADE IS
'Chave de EO_TIPO_UNIDADE.';

COMMENT ON COLUMN EO_TIPO_UNIDADE.SQ_PESSOA IS
'Chave de CO_PESSOA. Indica a que cliente o registro está ligado.';

COMMENT ON COLUMN EO_TIPO_UNIDADE.NOME IS
'Nome do tipo de unidade.';

COMMENT ON COLUMN EO_TIPO_UNIDADE.ATIVO IS
'Indica se este registro está disponível para ligação a outras tabelas.';

COMMENT ON COLUMN EO_TIPO_UNIDADE.CODIGO_EXTERNO IS
'Código desse registro num sistema externo.';

/*==============================================================*/
/* Index: IN_EOTIPUNI_PESSOA                                    */
/*==============================================================*/
CREATE  INDEX IN_EOTIPUNI_PESSOA ON EO_TIPO_UNIDADE (
SQ_TIPO_UNIDADE,
SQ_PESSOA
);

/*==============================================================*/
/* Index: IN_EOTIPUNI_ATIVO                                     */
/*==============================================================*/
CREATE  INDEX IN_EOTIPUNI_ATIVO ON EO_TIPO_UNIDADE (
ATIVO,
SQ_TIPO_UNIDADE
);

/*==============================================================*/
/* Index: IN_TOTIPUNI_EXTERNO                                   */
/*==============================================================*/
CREATE  INDEX IN_TOTIPUNI_EXTERNO ON EO_TIPO_UNIDADE (
SQ_PESSOA,
CODIGO_EXTERNO,
SQ_TIPO_UNIDADE
);

/*==============================================================*/
/* Table: EO_UNIDADE                                            */
/*==============================================================*/
CREATE TABLE EO_UNIDADE (
   SQ_UNIDADE           NUMERIC(10)          NOT NULL,
   SQ_UNIDADE_PAI       NUMERIC(10)          NULL,
   SQ_UNIDADE_GESTORA   NUMERIC(10)          NULL,
   SQ_UNID_PAGADORA     NUMERIC(10)          NULL,
   SQ_AREA_ATUACAO      NUMERIC(10)          NULL,
   SQ_TIPO_UNIDADE      NUMERIC(10)          NULL,
   SQ_PESSOA_ENDERECO   NUMERIC(18)          NULL,
   SQ_PESSOA            NUMERIC(18)          NOT NULL,
   NOME                 VARCHAR(60)          NOT NULL,
   SIGLA                VARCHAR(20)          NOT NULL,
   ORDEM                NUMERIC(2)           NOT NULL,
   INFORMAL             VARCHAR(1)           NOT NULL DEFAULT 'N'
      CONSTRAINT CKC_EOUNI_INFORM
               CHECK (INFORMAL IN ('S','N') AND INFORMAL = UPPER(INFORMAL)),
   VINCULADA            VARCHAR(1)           NOT NULL DEFAULT 'N'
      CONSTRAINT CKC_EOUNI_VINC
               CHECK (VINCULADA IN ('S','N') AND VINCULADA = UPPER(VINCULADA)),
   ADM_CENTRAL          VARCHAR(1)           NOT NULL DEFAULT 'N'
      CONSTRAINT CKC_EOUNI_ADMCEN
               CHECK (ADM_CENTRAL IN ('S','N') AND ADM_CENTRAL = UPPER(ADM_CENTRAL)),
   UNIDADE_GESTORA      VARCHAR(1)           NOT NULL DEFAULT 'N'
      CONSTRAINT CKC_EOUNI_GEST
               CHECK (UNIDADE_GESTORA IN ('S','N') AND UNIDADE_GESTORA = UPPER(UNIDADE_GESTORA)),
   UNIDADE_PAGADORA     VARCHAR(1)           NOT NULL DEFAULT 'N'
      CONSTRAINT CKC_EOUNI_PAG
               CHECK (UNIDADE_PAGADORA IN ('S','N') AND UNIDADE_PAGADORA = UPPER(UNIDADE_PAGADORA)),
   CODIGO               VARCHAR(15)          NULL,
   EMAIL                VARCHAR(60)          NULL,
   ATIVO                VARCHAR(1)           NOT NULL DEFAULT 'S'
      CONSTRAINT CKC_ATIVO_EO_UNIDA CHECK (ATIVO IN ('S','N') AND ATIVO = UPPER(ATIVO)),
   EXTERNO              VARCHAR(1)           NOT NULL DEFAULT 'N'
      CONSTRAINT CKC_EXTERNO_EO_UNIDA CHECK (EXTERNO IN ('S','N') AND EXTERNO = UPPER(EXTERNO)),
   CONSTRAINT PK_EO_UNIDADE PRIMARY KEY (SQ_UNIDADE)
);

COMMENT ON TABLE EO_UNIDADE IS
'Unidades organizacionais';

COMMENT ON COLUMN EO_UNIDADE.SQ_UNIDADE IS
'Chave de EO_UNIDADE. Indica a que unidade o registro está ligado.';

COMMENT ON COLUMN EO_UNIDADE.SQ_UNIDADE_PAI IS
'Chave de EO_UNIDADE. Auto-relacionamento da tabela.';

COMMENT ON COLUMN EO_UNIDADE.SQ_UNIDADE_GESTORA IS
'Chave de EO_UNIDADE. Unidade gestora dos bens patrimoniais da unidade. Auto-relacionamento.';

COMMENT ON COLUMN EO_UNIDADE.SQ_UNID_PAGADORA IS
'Chave de EO_UNIDADE. Unidade responsável pelas despesas da unidade. Auto-relacionamento.';

COMMENT ON COLUMN EO_UNIDADE.SQ_AREA_ATUACAO IS
'Chave de EO_AREA_ATUACAO. Área de atuação da unidade.';

COMMENT ON COLUMN EO_UNIDADE.SQ_TIPO_UNIDADE IS
'Chave de EO_TIPO_UNIDADE. Indica a que tipo de unidade o registro está ligado.';

COMMENT ON COLUMN EO_UNIDADE.SQ_PESSOA_ENDERECO IS
'Chave de CO_PESSOA_ENDERECO. Endereço da organização à qual a unidade está vinculada. Chave de CO_PESSOA_ENDERECO.';

COMMENT ON COLUMN EO_UNIDADE.SQ_PESSOA IS
'Chave de CO_PESSOA. Indica a que cliente o registro está ligado.';

COMMENT ON COLUMN EO_UNIDADE.NOME IS
'Nome da unidade.';

COMMENT ON COLUMN EO_UNIDADE.SIGLA IS
'Sigla da unidade.';

COMMENT ON COLUMN EO_UNIDADE.ORDEM IS
'Número de ordem da unidade para listagem.';

COMMENT ON COLUMN EO_UNIDADE.INFORMAL IS
'Indica se a unidade faz parte da estrutura formal da organização.';

COMMENT ON COLUMN EO_UNIDADE.VINCULADA IS
'Indica se a unidade é, na verdade, um órgão vinculado à organização.';

COMMENT ON COLUMN EO_UNIDADE.ADM_CENTRAL IS
'Indica se a unidade faz parte da administração central da organização.';

COMMENT ON COLUMN EO_UNIDADE.UNIDADE_GESTORA IS
'Indica se a unidade é responsável por bens patrimoniais da organização (depósito).';

COMMENT ON COLUMN EO_UNIDADE.UNIDADE_PAGADORA IS
'Indica se a unidade é um centro de custo da organização.';

COMMENT ON COLUMN EO_UNIDADE.CODIGO IS
'Código livre utilizado pela organização para identificar a unidade.';

COMMENT ON COLUMN EO_UNIDADE.EMAIL IS
'e-Mail da unidade.';

COMMENT ON COLUMN EO_UNIDADE.ATIVO IS
'Indica se a unidade está ativa.';

COMMENT ON COLUMN EO_UNIDADE.EXTERNO IS
'Indica se a unidade é externa à organização. Só aparece em listagens do protocolo.';

/*==============================================================*/
/* Index: IN_EOUNI_PAI                                          */
/*==============================================================*/
CREATE  INDEX IN_EOUNI_PAI ON EO_UNIDADE (
SQ_UNIDADE_PAI,
SQ_UNIDADE
);

/*==============================================================*/
/* Index: IN_EOUNI_GESTORA                                      */
/*==============================================================*/
CREATE  INDEX IN_EOUNI_GESTORA ON EO_UNIDADE (
SQ_UNIDADE_GESTORA,
SQ_UNIDADE
);

/*==============================================================*/
/* Index: IN_EOUNI_PAGADORA                                     */
/*==============================================================*/
CREATE  INDEX IN_EOUNI_PAGADORA ON EO_UNIDADE (
SQ_UNID_PAGADORA,
SQ_UNIDADE
);

/*==============================================================*/
/* Index: IN_EOUNI_ENDERECO                                     */
/*==============================================================*/
CREATE  INDEX IN_EOUNI_ENDERECO ON EO_UNIDADE (
SQ_PESSOA_ENDERECO,
SQ_UNIDADE
);

/*==============================================================*/
/* Index: IN_EOUNI_AREA                                         */
/*==============================================================*/
CREATE  INDEX IN_EOUNI_AREA ON EO_UNIDADE (
SQ_AREA_ATUACAO,
SQ_UNIDADE
);

/*==============================================================*/
/* Index: IN_EOUNI_TIPO_UNID                                    */
/*==============================================================*/
CREATE  INDEX IN_EOUNI_TIPO_UNID ON EO_UNIDADE (
SQ_TIPO_UNIDADE,
SQ_UNIDADE
);

/*==============================================================*/
/* Index: IN_EOUNI_NOME                                         */
/*==============================================================*/
CREATE  INDEX IN_EOUNI_NOME ON EO_UNIDADE (
NOME,
SQ_UNIDADE
);

/*==============================================================*/
/* Index: IN_EOUNI_SIGLA                                        */
/*==============================================================*/
CREATE  INDEX IN_EOUNI_SIGLA ON EO_UNIDADE (
SIGLA,
SQ_UNIDADE
);

/*==============================================================*/
/* Index: IN_EOUNI_ORDEM                                        */
/*==============================================================*/
CREATE  INDEX IN_EOUNI_ORDEM ON EO_UNIDADE (
ORDEM,
SQ_UNIDADE
);

/*==============================================================*/
/* Index: IN_EOUNI_ATIVO                                        */
/*==============================================================*/
CREATE  INDEX IN_EOUNI_ATIVO ON EO_UNIDADE (
ATIVO,
SQ_UNIDADE
);

/*==============================================================*/
/* Index: IN_EOUNI_CLIENTE                                      */
/*==============================================================*/
CREATE  INDEX IN_EOUNI_CLIENTE ON EO_UNIDADE (
SQ_PESSOA,
SQ_UNIDADE
);

/*==============================================================*/
/* Index: IN_EOUNI_EXTERNO                                      */
/*==============================================================*/
CREATE  INDEX IN_EOUNI_EXTERNO ON EO_UNIDADE (
SQ_PESSOA,
EXTERNO,
SQ_UNIDADE
);

/*==============================================================*/
/* Table: EO_UNIDADE_ARQUIVO                                    */
/*==============================================================*/
CREATE TABLE EO_UNIDADE_ARQUIVO (
   SQ_UNIDADE           NUMERIC(10)          NOT NULL,
   SQ_SIW_ARQUIVO       NUMERIC(18)          NOT NULL,
   ORDEM                NUMERIC(4)           NOT NULL DEFAULT 0,
   CONSTRAINT PK_EO_UNIDADE_ARQUIVO PRIMARY KEY (SQ_UNIDADE, SQ_SIW_ARQUIVO)
);

COMMENT ON TABLE EO_UNIDADE_ARQUIVO IS
'Registra os arquivos anexados para a unidade.';

COMMENT ON COLUMN EO_UNIDADE_ARQUIVO.SQ_UNIDADE IS
'Chave de EO_UNIDADE. Indica a que unidade o registro está ligado.';

COMMENT ON COLUMN EO_UNIDADE_ARQUIVO.SQ_SIW_ARQUIVO IS
'Chave de SIW_ARQUIVO. Indica a que arquivo o registro está ligado.';

COMMENT ON COLUMN EO_UNIDADE_ARQUIVO.ORDEM IS
'Número de ordem do registro nas listagens.';

/*==============================================================*/
/* Index: IN_EOUNIARQ_INV                                       */
/*==============================================================*/
CREATE  INDEX IN_EOUNIARQ_INV ON EO_UNIDADE_ARQUIVO (
SQ_SIW_ARQUIVO,
SQ_UNIDADE
);

/*==============================================================*/
/* Table: EO_UNIDADE_RESP                                       */
/*==============================================================*/
CREATE TABLE EO_UNIDADE_RESP (
   SQ_UNIDADE_RESP      NUMERIC(18)          NOT NULL,
   SQ_UNIDADE           NUMERIC(10)          NOT NULL,
   SQ_PESSOA            NUMERIC(18)          NOT NULL,
   TIPO_RESPONS         VARCHAR(1)           NOT NULL DEFAULT 'T'
      CONSTRAINT CKC_EOUNIRES_TPRES
               CHECK (TIPO_RESPONS IN ('T','S') AND TIPO_RESPONS = UPPER(TIPO_RESPONS)),
   INICIO               DATE                 NOT NULL,
   FIM                  DATE                 NULL,
   CONSTRAINT PK_EO_UNIDADE_RESP PRIMARY KEY (SQ_UNIDADE_RESP)
);

COMMENT ON TABLE EO_UNIDADE_RESP IS
'Armazena o histórico de ocupação da chefia titular e substituta de uma unidade.';

COMMENT ON COLUMN EO_UNIDADE_RESP.SQ_UNIDADE_RESP IS
'Chave de EO_UNIDADE_RESP.';

COMMENT ON COLUMN EO_UNIDADE_RESP.SQ_UNIDADE IS
'Chave de EO_UNIDADE. Indica a que unidade o registro está ligado.';

COMMENT ON COLUMN EO_UNIDADE_RESP.SQ_PESSOA IS
'Chave de CO_PESSOA. Indica a que pessoa o registro está ligado.';

COMMENT ON COLUMN EO_UNIDADE_RESP.TIPO_RESPONS IS
'Indica se a pessoa é titular ou substituto da unidade.';

COMMENT ON COLUMN EO_UNIDADE_RESP.INICIO IS
'Início da responsabilidade.';

COMMENT ON COLUMN EO_UNIDADE_RESP.FIM IS
'Fim da responsabilidade.';

/*==============================================================*/
/* Index: IN_EOUNIRES_PESSOA                                    */
/*==============================================================*/
CREATE  INDEX IN_EOUNIRES_PESSOA ON EO_UNIDADE_RESP (
SQ_PESSOA,
SQ_UNIDADE
);

/*==============================================================*/
/* Index: IN_EOUNIRES_INICIO                                    */
/*==============================================================*/
CREATE  INDEX IN_EOUNIRES_INICIO ON EO_UNIDADE_RESP (
INICIO,
SQ_UNIDADE
);

/*==============================================================*/
/* Index: IN_EOUNIRES_FIM                                       */
/*==============================================================*/
CREATE  INDEX IN_EOUNIRES_FIM ON EO_UNIDADE_RESP (
FIM,
SQ_UNIDADE
);

/*==============================================================*/
/* Index: IN_EOUNIRES_TIPO                                      */
/*==============================================================*/
CREATE  INDEX IN_EOUNIRES_TIPO ON EO_UNIDADE_RESP (
TIPO_RESPONS,
SQ_UNIDADE,
SQ_PESSOA
);

/*==============================================================*/
/* Index: IN_EOUNIRES_UNID                                      */
/*==============================================================*/
CREATE  INDEX IN_EOUNIRES_UNID ON EO_UNIDADE_RESP (
SQ_UNIDADE,
SQ_UNIDADE_RESP
);

/*==============================================================*/
/* Table: GD_DEMANDA                                            */
/*==============================================================*/
CREATE TABLE GD_DEMANDA (
   SQ_SIW_SOLICITACAO   NUMERIC(18)          NOT NULL,
   SQ_UNIDADE_RESP      NUMERIC(10)          NULL,
   SQ_DEMANDA_PAI       NUMERIC(18)          NULL,
   SQ_SIW_RESTRICAO     NUMERIC(18)          NULL,
   SQ_DEMANDA_TIPO      NUMERIC(18)          NULL,
   RESPONSAVEL          NUMERIC(18)          NULL,
   ASSUNTO              VARCHAR(2096)        NULL,
   PRIORIDADE           NUMERIC(2)           NULL,
   AVISO_PROX_CONC      VARCHAR(1)           NOT NULL DEFAULT 'N'
      CONSTRAINT CKC_GDDEM_AVISO CHECK (AVISO_PROX_CONC IN ('S','N') AND AVISO_PROX_CONC = UPPER(AVISO_PROX_CONC)),
   DIAS_AVISO           NUMERIC(3)           NOT NULL DEFAULT 0,
   INICIO_REAL          DATE                 NULL,
   FIM_REAL             DATE                 NULL,
   CONCLUIDA            VARCHAR(1)           NOT NULL DEFAULT 'N'
      CONSTRAINT CKC_GDDEM_CONC CHECK (CONCLUIDA IN ('S','N') AND CONCLUIDA = UPPER(CONCLUIDA)),
   DATA_CONCLUSAO       DATE                 NULL,
   NOTA_CONCLUSAO       VARCHAR(2005)        NULL,
   CUSTO_REAL           NUMERIC(18,2)        NOT NULL DEFAULT 0,
   PROPONENTE           VARCHAR(90)          NULL,
   ORDEM                NUMERIC(3)           NOT NULL DEFAULT 0,
   RECEBIMENTO          DATE                 NULL,
   LIMITE_CONCLUSAO     DATE                 NULL,
   CONSTRAINT PK_GD_DEMANDA PRIMARY KEY (SQ_SIW_SOLICITACAO)
);

COMMENT ON TABLE GD_DEMANDA IS
'Registra informações cadastrais da demanda.';

COMMENT ON COLUMN GD_DEMANDA.SQ_SIW_SOLICITACAO IS
'Chave de SIW_SOLICITACAO indicando a que solicitação o registro está ligado.';

COMMENT ON COLUMN GD_DEMANDA.SQ_UNIDADE_RESP IS
'Chave de EO_UNIDADE. Indica a unidade responsável pelo monitoramento da demanda.';

COMMENT ON COLUMN GD_DEMANDA.SQ_DEMANDA_PAI IS
'Chave de GD_DEMANDA. Usado APENAS para vinculação de um atividade a outra.';

COMMENT ON COLUMN GD_DEMANDA.SQ_SIW_RESTRICAO IS
'Chave de SIW_RESTRICAO. Indica a que risco a tarefa está vinculada.';

COMMENT ON COLUMN GD_DEMANDA.SQ_DEMANDA_TIPO IS
'Chave de GD_DEMANDA_TIPO. Indica o tipo da demanda eventual.';

COMMENT ON COLUMN GD_DEMANDA.RESPONSAVEL IS
'Chave de CO_PESSOA. Indica o responsável pela execução da demanda.';

COMMENT ON COLUMN GD_DEMANDA.ASSUNTO IS
'Assunto ou ementa da demanda. Será usado para recuperação textual.';

COMMENT ON COLUMN GD_DEMANDA.PRIORIDADE IS
'Registra a prioridade da demanda. Quanto menor o número, mais alta a prioridade.';

COMMENT ON COLUMN GD_DEMANDA.AVISO_PROX_CONC IS
'Indica se  necessrio avisar a proximidade da data limite para conclusão da demanda.';

COMMENT ON COLUMN GD_DEMANDA.DIAS_AVISO IS
'Se o campo AVISO_PROX_CONC igual a S, indica o número de dias a partir do qual devem ser enviados os avisos por e-mail.';

COMMENT ON COLUMN GD_DEMANDA.INICIO_REAL IS
'Início real da demanda.';

COMMENT ON COLUMN GD_DEMANDA.FIM_REAL IS
'Fim real da demanda.';

COMMENT ON COLUMN GD_DEMANDA.CONCLUIDA IS
'Indica se a demanda está concluída ou não.';

COMMENT ON COLUMN GD_DEMANDA.DATA_CONCLUSAO IS
'Data informada pelo usuário.';

COMMENT ON COLUMN GD_DEMANDA.NOTA_CONCLUSAO IS
'Observações relativas à conclusão da demanda.';

COMMENT ON COLUMN GD_DEMANDA.CUSTO_REAL IS
'Custo real dispendido com o atendimento da demanda.';

COMMENT ON COLUMN GD_DEMANDA.PROPONENTE IS
'Proponente da demanda. Texto livre.';

COMMENT ON COLUMN GD_DEMANDA.ORDEM IS
'Indica o número de ordem a ser utilizado pelas rotinas de visualização.';

COMMENT ON COLUMN GD_DEMANDA.RECEBIMENTO IS
'Data de recebimento da demanda.';

COMMENT ON COLUMN GD_DEMANDA.LIMITE_CONCLUSAO IS
'Limite para conclusão da demanda.';

/*==============================================================*/
/* Index: IN_GDDEM_UNID                                         */
/*==============================================================*/
CREATE  INDEX IN_GDDEM_UNID ON GD_DEMANDA (
SQ_UNIDADE_RESP,
SQ_SIW_SOLICITACAO
);

/*==============================================================*/
/* Index: IN_GDDEM_PRIOR                                        */
/*==============================================================*/
CREATE  INDEX IN_GDDEM_PRIOR ON GD_DEMANDA (
PRIORIDADE,
SQ_SIW_SOLICITACAO
);

/*==============================================================*/
/* Index: IN_GDDEM_INI                                          */
/*==============================================================*/
CREATE  INDEX IN_GDDEM_INI ON GD_DEMANDA (
INICIO_REAL,
SQ_SIW_SOLICITACAO
);

/*==============================================================*/
/* Index: IN_GDDEM_FIM                                          */
/*==============================================================*/
CREATE  INDEX IN_GDDEM_FIM ON GD_DEMANDA (
FIM_REAL,
SQ_SIW_SOLICITACAO
);

/*==============================================================*/
/* Index: IN_GDDEM_CONC                                         */
/*==============================================================*/
CREATE  INDEX IN_GDDEM_CONC ON GD_DEMANDA (
CONCLUIDA,
SQ_SIW_SOLICITACAO
);

/*==============================================================*/
/* Index: IN_GDDEM_CUSTO                                        */
/*==============================================================*/
CREATE  INDEX IN_GDDEM_CUSTO ON GD_DEMANDA (
CUSTO_REAL,
SQ_SIW_SOLICITACAO
);

/*==============================================================*/
/* Index: IN_GDDEM_DTCONC                                       */
/*==============================================================*/
CREATE  INDEX IN_GDDEM_DTCONC ON GD_DEMANDA (
DATA_CONCLUSAO,
SQ_SIW_SOLICITACAO
);

/*==============================================================*/
/* Index: IN_GDDEM_PROPON                                       */
/*==============================================================*/
CREATE  INDEX IN_GDDEM_PROPON ON GD_DEMANDA (
PROPONENTE,
SQ_SIW_SOLICITACAO
);

/*==============================================================*/
/* Index: IN_GDDEM_PAI                                          */
/*==============================================================*/
CREATE  INDEX IN_GDDEM_PAI ON GD_DEMANDA (
SQ_DEMANDA_PAI,
SQ_SIW_SOLICITACAO
);

/*==============================================================*/
/* Index: IN_GDDEM_RESTRICAO                                    */
/*==============================================================*/
CREATE  INDEX IN_GDDEM_RESTRICAO ON GD_DEMANDA (
SQ_SIW_RESTRICAO,
SQ_SIW_SOLICITACAO
);

/*==============================================================*/
/* Index: IN_GDDEM_TIPO                                         */
/*==============================================================*/
CREATE  INDEX IN_GDDEM_TIPO ON GD_DEMANDA (
SQ_DEMANDA_TIPO,
SQ_SIW_SOLICITACAO
);

/*==============================================================*/
/* Index: IN_GDDEM_RESP                                         */
/*==============================================================*/
CREATE  INDEX IN_GDDEM_RESP ON GD_DEMANDA (
RESPONSAVEL,
SQ_SIW_SOLICITACAO
);

/*==============================================================*/
/* Index: IN_GDDEM_LIMITE                                       */
/*==============================================================*/
CREATE  INDEX IN_GDDEM_LIMITE ON GD_DEMANDA (
LIMITE_CONCLUSAO,
SQ_SIW_SOLICITACAO
);

/*==============================================================*/
/* Index: IN_GDDEM_RECEB                                        */
/*==============================================================*/
CREATE  INDEX IN_GDDEM_RECEB ON GD_DEMANDA (
RECEBIMENTO,
SQ_SIW_SOLICITACAO
);

/*==============================================================*/
/* Table: GD_DEMANDA_ENVOLV                                     */
/*==============================================================*/
CREATE TABLE GD_DEMANDA_ENVOLV (
   SQ_UNIDADE           NUMERIC(10)          NOT NULL,
   SQ_SIW_SOLICITACAO   NUMERIC(18)          NOT NULL,
   PAPEL                VARCHAR(2000)        NOT NULL,
   CONSTRAINT PK_GD_DEMANDA_ENVOLV PRIMARY KEY (SQ_UNIDADE, SQ_SIW_SOLICITACAO)
);

COMMENT ON TABLE GD_DEMANDA_ENVOLV IS
'Registra as unidades envolvidas no atendimento da demanda.';

COMMENT ON COLUMN GD_DEMANDA_ENVOLV.SQ_UNIDADE IS
'Chave de EO_UNIDADE. Indica a que unidade o registro está ligado.';

COMMENT ON COLUMN GD_DEMANDA_ENVOLV.SQ_SIW_SOLICITACAO IS
'Chave de SIW_SOLICITACAO indicando a que solicitação o registro está ligado.';

COMMENT ON COLUMN GD_DEMANDA_ENVOLV.PAPEL IS
'Papel cumprido pela unidade envolvida.';

/*==============================================================*/
/* Index: IN_GDDEMENV_INVERSA                                   */
/*==============================================================*/
CREATE  INDEX IN_GDDEMENV_INVERSA ON GD_DEMANDA_ENVOLV (
SQ_UNIDADE
);

/*==============================================================*/
/* Table: GD_DEMANDA_INTERES                                    */
/*==============================================================*/
CREATE TABLE GD_DEMANDA_INTERES (
   SQ_PESSOA            NUMERIC(18)          NOT NULL,
   SQ_SIW_SOLICITACAO   NUMERIC(18)          NOT NULL,
   TIPO_VISAO           NUMERIC(1)           NOT NULL
      CONSTRAINT CKC_TIPO_VISAO_GD_DEMAN CHECK (TIPO_VISAO IN (0,1,2)),
   ENVIA_EMAIL          VARCHAR(1)           NOT NULL DEFAULT 'N'
      CONSTRAINT CKC_GDDEMINT_MAIL CHECK (ENVIA_EMAIL IN ('S','N') AND ENVIA_EMAIL = UPPER(ENVIA_EMAIL)),
   CONSTRAINT PK_GD_DEMANDA_INTERES PRIMARY KEY (SQ_PESSOA, SQ_SIW_SOLICITACAO)
);

COMMENT ON TABLE GD_DEMANDA_INTERES IS
'Registra os interessados pela demanda e que tipo de informações eles podem receber ou visualizar.';

COMMENT ON COLUMN GD_DEMANDA_INTERES.SQ_PESSOA IS
'Chave de CO_PESSOA. Indica a que pessoa o registro está ligado.';

COMMENT ON COLUMN GD_DEMANDA_INTERES.SQ_SIW_SOLICITACAO IS
'Chave de SIW_SOLICITACAO indicando a que solicitação o registro está ligado.';

COMMENT ON COLUMN GD_DEMANDA_INTERES.TIPO_VISAO IS
'Indica a visão que a pessoa pode ter dessa demanda.';

COMMENT ON COLUMN GD_DEMANDA_INTERES.ENVIA_EMAIL IS
'Indica se deve ser enviado e-mail ao interessado quando houver alguma ocorrência na demanda.';

/*==============================================================*/
/* Index: IN_GDDEMINT_INVERSA                                   */
/*==============================================================*/
CREATE  INDEX IN_GDDEMINT_INVERSA ON GD_DEMANDA_INTERES (
SQ_SIW_SOLICITACAO,
SQ_PESSOA
);

/*==============================================================*/
/* Index: IN_GDDEMINT_MAIL                                      */
/*==============================================================*/
CREATE  INDEX IN_GDDEMINT_MAIL ON GD_DEMANDA_INTERES (
ENVIA_EMAIL,
SQ_PESSOA,
SQ_SIW_SOLICITACAO
);

/*==============================================================*/
/* Table: GD_DEMANDA_LOG                                        */
/*==============================================================*/
CREATE TABLE GD_DEMANDA_LOG (
   SQ_DEMANDA_LOG       NUMERIC(18)          NOT NULL,
   SQ_SIW_SOLICITACAO   NUMERIC(18)          NOT NULL,
   SQ_SIW_SOLIC_LOG     NUMERIC(18)          NULL,
   CADASTRADOR          NUMERIC(18)          NOT NULL,
   DESTINATARIO         NUMERIC(18)          NULL,
   DATA_INCLUSAO        DATE                 NOT NULL,
   OBSERVACAO           VARCHAR(2088)        NULL,
   DESPACHO             VARCHAR(2000)        NULL,
   CONSTRAINT PK_GD_DEMANDA_LOG PRIMARY KEY (SQ_DEMANDA_LOG)
);

COMMENT ON TABLE GD_DEMANDA_LOG IS
'Registra o histórico da demanda';

COMMENT ON COLUMN GD_DEMANDA_LOG.SQ_DEMANDA_LOG IS
'Chave de GD_DEMANDA_LOG.';

COMMENT ON COLUMN GD_DEMANDA_LOG.SQ_SIW_SOLICITACAO IS
'Chave de SIW_SOLICITACAO indicando a que solicitação o registro está ligado.';

COMMENT ON COLUMN GD_DEMANDA_LOG.SQ_SIW_SOLIC_LOG IS
'Chave do log da solicitação, informada apenas quando for envio entre fases.';

COMMENT ON COLUMN GD_DEMANDA_LOG.CADASTRADOR IS
'Responsável pela inserção do histórico.';

COMMENT ON COLUMN GD_DEMANDA_LOG.DESTINATARIO IS
'Pessoa à qual a demanda está sendo encaminhada.';

COMMENT ON COLUMN GD_DEMANDA_LOG.DATA_INCLUSAO IS
'Data de inclusão do registro, gerado pelo sistema.';

COMMENT ON COLUMN GD_DEMANDA_LOG.OBSERVACAO IS
'Observações inseridas pelo usuário.';

COMMENT ON COLUMN GD_DEMANDA_LOG.DESPACHO IS
'Orientação ao destinatário sobre as ações necessárias.';

/*==============================================================*/
/* Index: IN_GDDEMLOG_DEM                                       */
/*==============================================================*/
CREATE  INDEX IN_GDDEMLOG_DEM ON GD_DEMANDA_LOG (
SQ_SIW_SOLICITACAO,
SQ_DEMANDA_LOG
);

/*==============================================================*/
/* Index: IN_GDDEMLOG_DATA                                      */
/*==============================================================*/
CREATE  INDEX IN_GDDEMLOG_DATA ON GD_DEMANDA_LOG (
DATA_INCLUSAO,
SQ_DEMANDA_LOG
);

/*==============================================================*/
/* Index: IN_GDDEMLOG_CADAST                                    */
/*==============================================================*/
CREATE  INDEX IN_GDDEMLOG_CADAST ON GD_DEMANDA_LOG (
CADASTRADOR,
SQ_DEMANDA_LOG
);

/*==============================================================*/
/* Index: IN_GDDEMLOG_DEST                                      */
/*==============================================================*/
CREATE  INDEX IN_GDDEMLOG_DEST ON GD_DEMANDA_LOG (
DESTINATARIO,
SQ_DEMANDA_LOG
);

/*==============================================================*/
/* Index: IN_GDDEMLOG_SIWLOG                                    */
/*==============================================================*/
CREATE  INDEX IN_GDDEMLOG_SIWLOG ON GD_DEMANDA_LOG (
SQ_SIW_SOLIC_LOG,
SQ_DEMANDA_LOG
);

/*==============================================================*/
/* Table: GD_DEMANDA_LOG_ARQ                                    */
/*==============================================================*/
CREATE TABLE GD_DEMANDA_LOG_ARQ (
   SQ_DEMANDA_LOG       NUMERIC(18)          NOT NULL,
   SQ_SIW_ARQUIVO       NUMERIC(18)          NOT NULL,
   CONSTRAINT PK_GD_DEMANDA_LOG_ARQ PRIMARY KEY (SQ_DEMANDA_LOG, SQ_SIW_ARQUIVO)
);

COMMENT ON TABLE GD_DEMANDA_LOG_ARQ IS
'Vincula arquivos a logs de demanda.';

COMMENT ON COLUMN GD_DEMANDA_LOG_ARQ.SQ_DEMANDA_LOG IS
'Chave de GD_DEMANDA_LOG. Indica a que registro o arquivo está ligado.';

COMMENT ON COLUMN GD_DEMANDA_LOG_ARQ.SQ_SIW_ARQUIVO IS
'Chave de SIW_ARQUIVO. Indica a que arquivo o registro está ligado.';

/*==============================================================*/
/* Index: IN_GDDEMLOGARQ_INV                                    */
/*==============================================================*/
CREATE  INDEX IN_GDDEMLOGARQ_INV ON GD_DEMANDA_LOG_ARQ (
SQ_SIW_ARQUIVO,
SQ_DEMANDA_LOG
);

/*==============================================================*/
/* Table: GD_DEMANDA_TIPO                                       */
/*==============================================================*/
CREATE TABLE GD_DEMANDA_TIPO (
   SQ_DEMANDA_TIPO      NUMERIC(18)          NOT NULL,
   CLIENTE              NUMERIC(18)          NOT NULL,
   NOME                 VARCHAR(60)          NOT NULL,
   SIGLA                VARCHAR(20)          NOT NULL,
   DESCRICAO            VARCHAR(500)         NULL,
   SQ_UNIDADE           NUMERIC(10)          NULL,
   ATIVO                VARCHAR(1)           NOT NULL DEFAULT 'S'
      CONSTRAINT CKC_ATIVO_GD_DEMAN CHECK (ATIVO IN ('S','N') AND ATIVO = UPPER(ATIVO)),
   REUNIAO              VARCHAR(1)           NOT NULL DEFAULT 'N'
      CONSTRAINT CKC_REUNIAO_GD_DEMAN CHECK (REUNIAO IN ('S','N') AND REUNIAO = UPPER(REUNIAO)),
   CONSTRAINT PK_GD_DEMANDA_TIPO PRIMARY KEY (SQ_DEMANDA_TIPO)
);

COMMENT ON TABLE GD_DEMANDA_TIPO IS
'Registra os tipos de demandas eventuais.';

COMMENT ON COLUMN GD_DEMANDA_TIPO.SQ_DEMANDA_TIPO IS
'Chave de GD_DEMANDA_TIPO.';

COMMENT ON COLUMN GD_DEMANDA_TIPO.CLIENTE IS
'Chave de CO_PESSOA. Indica a que cliente o registro pertence.';

COMMENT ON COLUMN GD_DEMANDA_TIPO.NOME IS
'Nome do tipo da demanda.';

COMMENT ON COLUMN GD_DEMANDA_TIPO.SIGLA IS
'Sigla do tipo da demanda.';

COMMENT ON COLUMN GD_DEMANDA_TIPO.DESCRICAO IS
'Descrição do tipo da demanda.';

COMMENT ON COLUMN GD_DEMANDA_TIPO.SQ_UNIDADE IS
'Chave de EO_UNIDADE. Indica a unidade responsável pela execução de demandas deste tipo.';

COMMENT ON COLUMN GD_DEMANDA_TIPO.ATIVO IS
'Indica se este tipo pode ser vinculado a novos registros.';

COMMENT ON COLUMN GD_DEMANDA_TIPO.REUNIAO IS
'Indica se o tipo deve ter tratamento de reunião: S - Sim, N - Não.';

/*==============================================================*/
/* Index: IN_GDDEMTIP_NOME                                      */
/*==============================================================*/
CREATE UNIQUE INDEX IN_GDDEMTIP_NOME ON GD_DEMANDA_TIPO (
CLIENTE,
NOME
);

/*==============================================================*/
/* Index: IN_GDDEMTIP_SIGLA                                     */
/*==============================================================*/
CREATE UNIQUE INDEX IN_GDDEMTIP_SIGLA ON GD_DEMANDA_TIPO (
CLIENTE,
SIGLA
);

/*==============================================================*/
/* Index: IN_GDDEMTIP_CLIENTE                                   */
/*==============================================================*/
CREATE  INDEX IN_GDDEMTIP_CLIENTE ON GD_DEMANDA_TIPO (
CLIENTE,
SQ_DEMANDA_TIPO
);

/*==============================================================*/
/* Index: IN_GDDEMTIP_ATIVO                                     */
/*==============================================================*/
CREATE  INDEX IN_GDDEMTIP_ATIVO ON GD_DEMANDA_TIPO (
CLIENTE,
ATIVO,
SQ_DEMANDA_TIPO
);

/*==============================================================*/
/* Table: PE_HORIZONTE                                          */
/*==============================================================*/
CREATE TABLE PE_HORIZONTE (
   SQ_PEHORIZONTE       NUMERIC(18)          NOT NULL,
   CLIENTE              NUMERIC(18)          NOT NULL,
   NOME                 VARCHAR(30)          NOT NULL,
   ATIVO                VARCHAR(1)           NOT NULL DEFAULT 'S'
      CONSTRAINT CKC_PEHOR_ATIVO CHECK (ATIVO IN ('S','N') AND ATIVO = UPPER(ATIVO)),
   CONSTRAINT PK_PE_HORIZONTE PRIMARY KEY (SQ_PEHORIZONTE)
);

COMMENT ON TABLE PE_HORIZONTE IS
'Domínio de valores para o horizonte temporal de um programa.';

COMMENT ON COLUMN PE_HORIZONTE.SQ_PEHORIZONTE IS
'Chave de PE_HORIZONTE.';

COMMENT ON COLUMN PE_HORIZONTE.CLIENTE IS
'Chave de CO_PESSOA. Indica a que cliente o horizonte está ligado.';

COMMENT ON COLUMN PE_HORIZONTE.NOME IS
'Nome do horizonte do programa.';

COMMENT ON COLUMN PE_HORIZONTE.ATIVO IS
'Indica se o registro pode ser associado a novos registros.';

/*==============================================================*/
/* Index: IN_PEHOR_CLIENTE                                      */
/*==============================================================*/
CREATE  INDEX IN_PEHOR_CLIENTE ON PE_HORIZONTE (
CLIENTE,
SQ_PEHORIZONTE
);

/*==============================================================*/
/* Table: PE_NATUREZA                                           */
/*==============================================================*/
CREATE TABLE PE_NATUREZA (
   SQ_PENATUREZA        NUMERIC(18)          NOT NULL,
   CLIENTE              NUMERIC(18)          NOT NULL,
   NOME                 VARCHAR(30)          NOT NULL,
   ATIVO                VARCHAR(1)           NOT NULL DEFAULT 'S'
      CONSTRAINT CKC_PENAT_ATIVO CHECK (ATIVO IN ('S','N') AND ATIVO = UPPER(ATIVO)),
   CONSTRAINT PK_PE_NATUREZA PRIMARY KEY (SQ_PENATUREZA)
);

COMMENT ON TABLE PE_NATUREZA IS
'Domnio de valores para a natureza do programa';

COMMENT ON COLUMN PE_NATUREZA.SQ_PENATUREZA IS
'Chave de PE_NATUREZA.';

COMMENT ON COLUMN PE_NATUREZA.CLIENTE IS
'Chave de CO_PESSOA. Indica a que cliente o registro está ligado.';

COMMENT ON COLUMN PE_NATUREZA.NOME IS
'Nome da natureza do programa.';

COMMENT ON COLUMN PE_NATUREZA.ATIVO IS
'Indica se o registro pode ser associado a novos registros.';

/*==============================================================*/
/* Index: IN_PENAT_CLIENTE                                      */
/*==============================================================*/
CREATE  INDEX IN_PENAT_CLIENTE ON PE_NATUREZA (
CLIENTE,
SQ_PENATUREZA
);

/*==============================================================*/
/* Table: PE_OBJETIVO                                           */
/*==============================================================*/
CREATE TABLE PE_OBJETIVO (
   SQ_PEOBJETIVO        NUMERIC(18)          NOT NULL,
   CLIENTE              NUMERIC(18)          NOT NULL,
   SQ_PLANO             NUMERIC(18)          NOT NULL,
   NOME                 VARCHAR(100)         NOT NULL,
   SIGLA                VARCHAR(10)          NOT NULL,
   DESCRICAO            VARCHAR(4000)        NOT NULL,
   ATIVO                VARCHAR(1)           NOT NULL DEFAULT 'S'
      CONSTRAINT CKC_ATIVO_PE_OBJET CHECK (ATIVO IN ('S','N') AND ATIVO = UPPER(ATIVO)),
   CODIGO_EXTERNO       VARCHAR(30)          NULL,
   CONSTRAINT PK_PE_OBJETIVO PRIMARY KEY (SQ_PEOBJETIVO)
);

COMMENT ON TABLE PE_OBJETIVO IS
'Registra os objetivos do planejamento estratégico, sendo vinculado apenas ao nivel folha da tabela PE_GERAL.';

COMMENT ON COLUMN PE_OBJETIVO.SQ_PEOBJETIVO IS
'Chave de SQ_PEOBJETIVO.';

COMMENT ON COLUMN PE_OBJETIVO.CLIENTE IS
'Chave de CO_PESSOA. Indica a que cliente o objetivo está ligado.';

COMMENT ON COLUMN PE_OBJETIVO.SQ_PLANO IS
'Chave de PE_PLANO. Indica a que plano estratégico o objetivo está ligado.';

COMMENT ON COLUMN PE_OBJETIVO.NOME IS
'Nome do objetivo estratégico.';

COMMENT ON COLUMN PE_OBJETIVO.SIGLA IS
'Sigla do objetivo estratégico.';

COMMENT ON COLUMN PE_OBJETIVO.DESCRICAO IS
'Descrição do objetivo estratégico.';

COMMENT ON COLUMN PE_OBJETIVO.ATIVO IS
'Indica se o objetivo estratégico está ativo.';

COMMENT ON COLUMN PE_OBJETIVO.CODIGO_EXTERNO IS
'Código desse registro num sistema externo.';

/*==============================================================*/
/* Index: IN_PEOBJ_CLIENTE                                      */
/*==============================================================*/
CREATE  INDEX IN_PEOBJ_CLIENTE ON PE_OBJETIVO (
CLIENTE,
SQ_PEOBJETIVO
);

/*==============================================================*/
/* Index: IN_PEOBJ_PLANO                                        */
/*==============================================================*/
CREATE  INDEX IN_PEOBJ_PLANO ON PE_OBJETIVO (
CLIENTE,
SQ_PLANO,
SQ_PEOBJETIVO
);

/*==============================================================*/
/* Index: IN_PEOBJ_NOME                                         */
/*==============================================================*/
CREATE  INDEX IN_PEOBJ_NOME ON PE_OBJETIVO (
CLIENTE,
NOME,
SQ_PEOBJETIVO
);

/*==============================================================*/
/* Index: IN_PEOBJ_SIGLA                                        */
/*==============================================================*/
CREATE  INDEX IN_PEOBJ_SIGLA ON PE_OBJETIVO (
CLIENTE,
SIGLA,
SQ_PEOBJETIVO
);

/*==============================================================*/
/* Index: IN_PEOBJ_ATIVO                                        */
/*==============================================================*/
CREATE  INDEX IN_PEOBJ_ATIVO ON PE_OBJETIVO (
CLIENTE,
ATIVO,
SQ_PEOBJETIVO
);

/*==============================================================*/
/* Table: PE_PLANO                                              */
/*==============================================================*/
CREATE TABLE PE_PLANO (
   SQ_PLANO             NUMERIC(18)          NOT NULL,
   CLIENTE              NUMERIC(18)          NOT NULL,
   SQ_PLANO_PAI         NUMERIC(18)          NULL,
   TITULO               VARCHAR(100)         NOT NULL,
   MISSAO               VARCHAR(2000)        NOT NULL,
   VALORES              VARCHAR(2000)        NOT NULL,
   VISAO_PRESENTE       VARCHAR(2000)        NOT NULL,
   VISAO_FUTURO         VARCHAR(2000)        NOT NULL,
   INICIO               DATE                 NOT NULL,
   FIM                  DATE                 NOT NULL,
   ATIVO                VARCHAR(1)           NOT NULL DEFAULT 'S'
      CONSTRAINT CKC_ATIVO_PE_PLANO CHECK (ATIVO IN ('S','N') AND ATIVO = UPPER(ATIVO)),
   CODIGO_EXTERNO       VARCHAR(30)          NULL,
   CONSTRAINT PK_PE_PLANO PRIMARY KEY (SQ_PLANO)
);

COMMENT ON TABLE PE_PLANO IS
'Registra os dados gerais do planejamento estratégico.';

COMMENT ON COLUMN PE_PLANO.SQ_PLANO IS
'Chave de PE_PLANO.';

COMMENT ON COLUMN PE_PLANO.CLIENTE IS
'Chave de CO_PESSOA. Indica a que cliente o planejamento está ligado.';

COMMENT ON COLUMN PE_PLANO.SQ_PLANO_PAI IS
'Chave de PE_PLANO. Auto-relacionamento da tabela.';

COMMENT ON COLUMN PE_PLANO.TITULO IS
'Título do planejamento estratégico.';

COMMENT ON COLUMN PE_PLANO.MISSAO IS
'Missão (negócio) da organização durante a execução do planejamento estratégico.';

COMMENT ON COLUMN PE_PLANO.VALORES IS
'Valores (crenças) da organização que o planejamento estratégico deve respeitar e ter como diretrizes.';

COMMENT ON COLUMN PE_PLANO.VISAO_PRESENTE IS
'Visão presente da organização no momento da elaboração do planejamento estratégico.';

COMMENT ON COLUMN PE_PLANO.VISAO_FUTURO IS
'Visão do futuro desejado para a organização ao final da execução do planejamento estratégico.';

COMMENT ON COLUMN PE_PLANO.INICIO IS
'Data inicial do planejamento estratégico.';

COMMENT ON COLUMN PE_PLANO.FIM IS
'Data final do planejamento estratégico.';

COMMENT ON COLUMN PE_PLANO.ATIVO IS
'Indica se o item do planejamento estratégico está ativo.';

COMMENT ON COLUMN PE_PLANO.CODIGO_EXTERNO IS
'Código desse registro num sistema externo.';

/*==============================================================*/
/* Index: IN_PEPLA_CLIENTE                                      */
/*==============================================================*/
CREATE  INDEX IN_PEPLA_CLIENTE ON PE_PLANO (
CLIENTE,
SQ_PLANO
);

/*==============================================================*/
/* Index: IN_PEPLA_PAI                                          */
/*==============================================================*/
CREATE  INDEX IN_PEPLA_PAI ON PE_PLANO (
CLIENTE,
SQ_PLANO_PAI,
SQ_PLANO
);

/*==============================================================*/
/* Index: IN_PEPLA_INICIO                                       */
/*==============================================================*/
CREATE  INDEX IN_PEPLA_INICIO ON PE_PLANO (
CLIENTE,
INICIO,
SQ_PLANO
);

/*==============================================================*/
/* Index: IN_PEPLA_FIM                                          */
/*==============================================================*/
CREATE  INDEX IN_PEPLA_FIM ON PE_PLANO (
CLIENTE,
FIM,
SQ_PLANO
);

/*==============================================================*/
/* Index: IN_PEPLA_TITULO                                       */
/*==============================================================*/
CREATE  INDEX IN_PEPLA_TITULO ON PE_PLANO (
CLIENTE,
TITULO,
SQ_PLANO
);

/*==============================================================*/
/* Table: PE_PLANO_ARQ                                          */
/*==============================================================*/
CREATE TABLE PE_PLANO_ARQ (
   SQ_SIW_ARQUIVO       NUMERIC(18)          NOT NULL,
   SQ_PLANO             NUMERIC(18)          NOT NULL,
   CONSTRAINT PK_PE_PLANO_ARQ PRIMARY KEY (SQ_SIW_ARQUIVO, SQ_PLANO)
);

COMMENT ON TABLE PE_PLANO_ARQ IS
'Registra os arquivos ligados a um item do planejamento estratégico';

COMMENT ON COLUMN PE_PLANO_ARQ.SQ_SIW_ARQUIVO IS
'Chave de SIW_ARQUIVO. Indica a que arquivo o registro está ligado.';

COMMENT ON COLUMN PE_PLANO_ARQ.SQ_PLANO IS
'Chave de PE_PLANO. Indica a que item do planejamento estratégico o registro está ligado.';

/*==============================================================*/
/* Index: IN_PEPLAARQ_INVERSA                                   */
/*==============================================================*/
CREATE  INDEX IN_PEPLAARQ_INVERSA ON PE_PLANO_ARQ (
SQ_PLANO,
SQ_SIW_ARQUIVO
);

/*==============================================================*/
/* Table: PE_PLANO_INDICADOR                                    */
/*==============================================================*/
CREATE TABLE PE_PLANO_INDICADOR (
   SQ_PLANO_INDICADOR   NUMERIC(18)          NOT NULL,
   SQ_PLANO             NUMERIC(18)          NOT NULL,
   SQ_EOINDICADOR       NUMERIC(18)          NOT NULL,
   CONSTRAINT PK_PE_PLANO_INDICADOR PRIMARY KEY (SQ_PLANO_INDICADOR)
);

COMMENT ON TABLE PE_PLANO_INDICADOR IS
'Registra os indicadores de um plano estratégico.';

COMMENT ON COLUMN PE_PLANO_INDICADOR.SQ_PLANO_INDICADOR IS
'Chave de PE_PLANO_INDICADOR.';

COMMENT ON COLUMN PE_PLANO_INDICADOR.SQ_PLANO IS
'Chave de PE_PLANO. Indica a que plano estratégico o registro está ligado.';

COMMENT ON COLUMN PE_PLANO_INDICADOR.SQ_EOINDICADOR IS
'Chave de EO_INDICADOR. Indica a que indicador o registro está ligado.';

/*==============================================================*/
/* Index: IN_PEPLANIND_PLANO                                    */
/*==============================================================*/
CREATE  INDEX IN_PEPLANIND_PLANO ON PE_PLANO_INDICADOR (
SQ_PLANO,
SQ_PLANO_INDICADOR
);

/*==============================================================*/
/* Index: IN_PEPLANIND_INDICADOR                                */
/*==============================================================*/
CREATE  INDEX IN_PEPLANIND_INDICADOR ON PE_PLANO_INDICADOR (
SQ_EOINDICADOR,
SQ_PLANO_INDICADOR
);

/*==============================================================*/
/* Table: PE_PLANO_MENU                                         */
/*==============================================================*/
CREATE TABLE PE_PLANO_MENU (
   SQ_PLANO             NUMERIC(18)          NOT NULL,
   SQ_MENU              NUMERIC(18)          NOT NULL,
   CONSTRAINT PK_PE_PLANO_MENU PRIMARY KEY (SQ_PLANO, SQ_MENU)
);

COMMENT ON TABLE PE_PLANO_MENU IS
'Reqistra os serviços que podem vincular-se a planos estratégicos.';

COMMENT ON COLUMN PE_PLANO_MENU.SQ_PLANO IS
'Chave de PE_PLANO. Indica a que plano o registro está ligado';

COMMENT ON COLUMN PE_PLANO_MENU.SQ_MENU IS
'Chave de SIW_MENU. Indica a que serviço o registro está ligado.';

/*==============================================================*/
/* Index: IN_PEPLANMEN_INVERSA                                  */
/*==============================================================*/
CREATE  INDEX IN_PEPLANMEN_INVERSA ON PE_PLANO_MENU (
SQ_MENU,
SQ_PLANO
);

/*==============================================================*/
/* Table: PE_PROGRAMA                                           */
/*==============================================================*/
CREATE TABLE PE_PROGRAMA (
   SQ_SIW_SOLICITACAO   NUMERIC(18)          NOT NULL,
   CLIENTE              NUMERIC(18)          NOT NULL,
   SQ_PEHORIZONTE       NUMERIC(18)          NOT NULL,
   SQ_PENATUREZA        NUMERIC(18)          NOT NULL,
   SQ_UNIDADE_RESP      NUMERIC(10)          NOT NULL,
   PUBLICO_ALVO         VARCHAR(2000)        NULL,
   ESTRATEGIA           VARCHAR(2000)        NULL,
   LN_PROGRAMA          VARCHAR(120)         NULL,
   SITUACAO_ATUAL       VARCHAR(2000)        NULL,
   EXEQUIVEL            VARCHAR(1)           NOT NULL DEFAULT 'S'
      CONSTRAINT CKC_EXEQUIVEL_PE_PROGR CHECK (EXEQUIVEL IN ('S','N') AND EXEQUIVEL = UPPER(EXEQUIVEL)),
   JUSTIFICATIVA_INEXEQUIVEL VARCHAR(2000)        NULL,
   OUTRAS_MEDIDAS       VARCHAR(2000)        NULL,
   INICIO_REAL          DATE                 NULL,
   FIM_REAL             DATE                 NULL,
   CUSTO_REAL           NUMERIC(18,2)        NULL,
   NOTA_CONCLUSAO       VARCHAR(2005)        NULL,
   AVISO_PROX_CONC      VARCHAR(1)           NOT NULL DEFAULT 'N'
      CONSTRAINT CKC_PEPRO_AVISO CHECK (AVISO_PROX_CONC IN ('S','N') AND AVISO_PROX_CONC = UPPER(AVISO_PROX_CONC)),
   DIAS_AVISO           NUMERIC(3)           NOT NULL DEFAULT 0,
   CONSTRAINT PK_PE_PROGRAMA PRIMARY KEY (SQ_SIW_SOLICITACAO)
);

COMMENT ON TABLE PE_PROGRAMA IS
'Registra os programas do planejamento estratégico.';

COMMENT ON COLUMN PE_PROGRAMA.SQ_SIW_SOLICITACAO IS
'Chave de SIW_SOLICITACAO. Indica a que solicitação o programa está ligado.';

COMMENT ON COLUMN PE_PROGRAMA.CLIENTE IS
'Chave de CO_PESSOA. Indica a que cliente o programa est[a vinculado.';

COMMENT ON COLUMN PE_PROGRAMA.SQ_PEHORIZONTE IS
'Chave de PE_PROGRAMA. Indica o horizonte temporal do programa.';

COMMENT ON COLUMN PE_PROGRAMA.SQ_PENATUREZA IS
'Chave de PE_NATUREZA. Indica a natureza do programa.';

COMMENT ON COLUMN PE_PROGRAMA.SQ_UNIDADE_RESP IS
'Chave de EO_UNIDADE. Indica a unidade responsável pelo monitoramento do programa.';

COMMENT ON COLUMN PE_PROGRAMA.PUBLICO_ALVO IS
'Descrição do Público alvo do Programa';

COMMENT ON COLUMN PE_PROGRAMA.ESTRATEGIA IS
'Estratégia de execuçao do programa.';

COMMENT ON COLUMN PE_PROGRAMA.LN_PROGRAMA IS
'Link para um site específico do programa, se existir.';

COMMENT ON COLUMN PE_PROGRAMA.SITUACAO_ATUAL IS
'Texto detalhando a situação atual do programa.';

COMMENT ON COLUMN PE_PROGRAMA.EXEQUIVEL IS
'Indica se o programa está avaliado como passível de cumprimento ou não.';

COMMENT ON COLUMN PE_PROGRAMA.JUSTIFICATIVA_INEXEQUIVEL IS
'Motivos que justificam o não atingimento dos objetivos do programa.';

COMMENT ON COLUMN PE_PROGRAMA.OUTRAS_MEDIDAS IS
'Descrição das medidas necessárias ao cumprimento do objetivo.';

COMMENT ON COLUMN PE_PROGRAMA.INICIO_REAL IS
'Início real do programa, informado na conclusão do programa.';

COMMENT ON COLUMN PE_PROGRAMA.FIM_REAL IS
'Fim real do programa, informado na conclusão do programa.';

COMMENT ON COLUMN PE_PROGRAMA.CUSTO_REAL IS
'Custo real do programa, informado na conclusão do programa.';

COMMENT ON COLUMN PE_PROGRAMA.NOTA_CONCLUSAO IS
'Avaliação final do programa.';

COMMENT ON COLUMN PE_PROGRAMA.AVISO_PROX_CONC IS
'Indica se é necessário avisar a proximidade da data final do programa.';

COMMENT ON COLUMN PE_PROGRAMA.DIAS_AVISO IS
'Se o campo AVISO_PROX_CONC igual a S, indica o número de dias a partir do qual devem ser enviados os avisos por e-mail.';

/*==============================================================*/
/* Index: IN_PEPRO_UNIDADE                                      */
/*==============================================================*/
CREATE  INDEX IN_PEPRO_UNIDADE ON PE_PROGRAMA (
SQ_UNIDADE_RESP,
SQ_SIW_SOLICITACAO
);

/*==============================================================*/
/* Table: PE_PROGRAMA_LOG                                       */
/*==============================================================*/
CREATE TABLE PE_PROGRAMA_LOG (
   SQ_PROGRAMA_LOG      NUMERIC(18)          NOT NULL,
   SQ_SIW_SOLICITACAO   NUMERIC(18)          NOT NULL,
   SQ_SIW_SOLIC_LOG     NUMERIC(18)          NULL,
   CADASTRADOR          NUMERIC(18)          NOT NULL,
   DESTINATARIO         NUMERIC(18)          NULL,
   DATA_INCLUSAO        DATE                 NOT NULL DEFAULT 'now()',
   OBSERVACAO           VARCHAR(2000)        NULL,
   DESPACHO             VARCHAR(2000)        NULL,
   CONSTRAINT PK_PE_PROGRAMA_LOG PRIMARY KEY (SQ_PROGRAMA_LOG)
);

COMMENT ON TABLE PE_PROGRAMA_LOG IS
'Registra o histórico da tramitação do programa.';

COMMENT ON COLUMN PE_PROGRAMA_LOG.SQ_PROGRAMA_LOG IS
'Chave de PE_PROGRAMA_LOG.';

COMMENT ON COLUMN PE_PROGRAMA_LOG.SQ_SIW_SOLICITACAO IS
'Chave de SIW_SOLICITACAO. Indica a que programa o registro está ligado.';

COMMENT ON COLUMN PE_PROGRAMA_LOG.SQ_SIW_SOLIC_LOG IS
'Chave do log do acordo, informada apenas quando for envio entre fases.';

COMMENT ON COLUMN PE_PROGRAMA_LOG.CADASTRADOR IS
'Chave de CO_PESSOA. Responsável pela inserção do histórico.';

COMMENT ON COLUMN PE_PROGRAMA_LOG.DESTINATARIO IS
'Chave de CO_PESSOA. Pessoa à qual o acordo está sendo encaminhado.';

COMMENT ON COLUMN PE_PROGRAMA_LOG.DATA_INCLUSAO IS
'Data de inclusão do registro, gerado pelo sistema.';

COMMENT ON COLUMN PE_PROGRAMA_LOG.OBSERVACAO IS
'Observações inseridas pelo usuário.';

COMMENT ON COLUMN PE_PROGRAMA_LOG.DESPACHO IS
'Orientação ao destinatário sobre as ações necessárias.';

/*==============================================================*/
/* Index: IN_PEPROLOG_PROGRAMA                                  */
/*==============================================================*/
CREATE  INDEX IN_PEPROLOG_PROGRAMA ON PE_PROGRAMA_LOG (
SQ_SIW_SOLICITACAO,
SQ_PROGRAMA_LOG
);

/*==============================================================*/
/* Index: IN_PEPROLOG_DATA                                      */
/*==============================================================*/
CREATE  INDEX IN_PEPROLOG_DATA ON PE_PROGRAMA_LOG (
SQ_SIW_SOLICITACAO,
DATA_INCLUSAO,
SQ_PROGRAMA_LOG
);

/*==============================================================*/
/* Index: IN_PEPROLOG_CADAST                                    */
/*==============================================================*/
CREATE  INDEX IN_PEPROLOG_CADAST ON PE_PROGRAMA_LOG (
CADASTRADOR,
SQ_PROGRAMA_LOG
);

/*==============================================================*/
/* Index: IN_PEPROLOG_DEST                                      */
/*==============================================================*/
CREATE  INDEX IN_PEPROLOG_DEST ON PE_PROGRAMA_LOG (
DESTINATARIO,
SQ_PROGRAMA_LOG
);

/*==============================================================*/
/* Index: IN_PEPROLOG_SIWLOG                                    */
/*==============================================================*/
CREATE  INDEX IN_PEPROLOG_SIWLOG ON PE_PROGRAMA_LOG (
SQ_SIW_SOLIC_LOG,
SQ_PROGRAMA_LOG
);

/*==============================================================*/
/* Table: PE_PROGRAMA_LOG_ARQ                                   */
/*==============================================================*/
CREATE TABLE PE_PROGRAMA_LOG_ARQ (
   SQ_PROGRAMA_LOG      NUMERIC(18)          NOT NULL,
   SQ_SIW_ARQUIVO       NUMERIC(18)          NOT NULL,
   CONSTRAINT PK_PE_PROGRAMA_LOG_ARQ PRIMARY KEY (SQ_PROGRAMA_LOG, SQ_SIW_ARQUIVO)
);

COMMENT ON TABLE PE_PROGRAMA_LOG_ARQ IS
'Vincula logs de solicitação a arquivos físicos.';

COMMENT ON COLUMN PE_PROGRAMA_LOG_ARQ.SQ_PROGRAMA_LOG IS
'Chave de PE_PROGRAMA_LOG. Indica a que log o registro está ligado.';

COMMENT ON COLUMN PE_PROGRAMA_LOG_ARQ.SQ_SIW_ARQUIVO IS
'Chave de SIW_ARQUIVO. Indica a que arquivo o registro está ligado.';

/*==============================================================*/
/* Index: IN_PEPROLOGARQ_INV                                    */
/*==============================================================*/
CREATE  INDEX IN_PEPROLOGARQ_INV ON PE_PROGRAMA_LOG_ARQ (
SQ_SIW_ARQUIVO,
SQ_PROGRAMA_LOG
);

/*==============================================================*/
/* Table: PE_UNIDADE                                            */
/*==============================================================*/
CREATE TABLE PE_UNIDADE (
   SQ_UNIDADE           NUMERIC(10)          NOT NULL,
   CLIENTE              NUMERIC(18)          NOT NULL,
   DESCRICAO            VARCHAR(2000)        NOT NULL,
   PLANEJAMENTO         VARCHAR(1)           NOT NULL DEFAULT 'S'
      CONSTRAINT CKC_PLANEJAMENTO_PE_UNIDA CHECK (PLANEJAMENTO IN ('S','N') AND PLANEJAMENTO = UPPER(PLANEJAMENTO)),
   EXECUCAO             VARCHAR(1)           NOT NULL DEFAULT 'S'
      CONSTRAINT CKC_EXECUCAO_PE_UNIDA CHECK (EXECUCAO IN ('S','N') AND EXECUCAO = UPPER(EXECUCAO)),
   GESTAO_RECURSOS      VARCHAR(1)           NOT NULL DEFAULT 'S'
      CONSTRAINT CKC_GESTAO_RECURSOS_PE_UNIDA CHECK (GESTAO_RECURSOS IN ('S','N') AND GESTAO_RECURSOS = UPPER(GESTAO_RECURSOS)),
   ATIVO                VARCHAR(1)           NOT NULL DEFAULT 'S'
      CONSTRAINT CKC_ATIVO_PE_UNIDA CHECK (ATIVO IN ('S','N') AND ATIVO = UPPER(ATIVO)),
   CONSTRAINT PK_PE_UNIDADE PRIMARY KEY (SQ_UNIDADE)
);

COMMENT ON TABLE PE_UNIDADE IS
'Registra as unidades responsáveis pelo monitoramento do planejamento estratégico.';

COMMENT ON COLUMN PE_UNIDADE.SQ_UNIDADE IS
'Chave de EO_UNIDADE. Indica a que unidade o registro está vinculado.';

COMMENT ON COLUMN PE_UNIDADE.CLIENTE IS
'Chave de EO_UNIDADE. Indica a que unidade o registro está ligado.';

COMMENT ON COLUMN PE_UNIDADE.DESCRICAO IS
'Descrição do papel que a unidade cumpre no monitoramento do planejamento estratégico.';

COMMENT ON COLUMN PE_UNIDADE.PLANEJAMENTO IS
'Indica se a unidade pode monitorar o planejamento estratégico.';

COMMENT ON COLUMN PE_UNIDADE.EXECUCAO IS
'Indica se a unidade pode executar o planejamento estratégico.';

COMMENT ON COLUMN PE_UNIDADE.GESTAO_RECURSOS IS
'Indica se a unidade é gestora de recursos.';

COMMENT ON COLUMN PE_UNIDADE.ATIVO IS
'Indica se a unidade pode ser vinculada a novos registros.';

/*==============================================================*/
/* Index: IN_PEUNI_CLIENTE                                      */
/*==============================================================*/
CREATE  INDEX IN_PEUNI_CLIENTE ON PE_UNIDADE (
CLIENTE,
ATIVO,
SQ_UNIDADE
);

/*==============================================================*/
/* Table: PJ_COMENTARIO_ARQ                                     */
/*==============================================================*/
CREATE TABLE PJ_COMENTARIO_ARQ (
   SQ_ETAPA_COMENTARIO  NUMERIC(18)          NOT NULL,
   SQ_SIW_ARQUIVO       NUMERIC(18)          NOT NULL,
   CONSTRAINT PK_PJ_COMENTARIO_ARQ PRIMARY KEY (SQ_ETAPA_COMENTARIO, SQ_SIW_ARQUIVO)
);

COMMENT ON TABLE PJ_COMENTARIO_ARQ IS
'Registra os arquivos vinculados a um comentário';

COMMENT ON COLUMN PJ_COMENTARIO_ARQ.SQ_ETAPA_COMENTARIO IS
'Chave de PJ_ETAPA_COMENTARIO.';

COMMENT ON COLUMN PJ_COMENTARIO_ARQ.SQ_SIW_ARQUIVO IS
'Chave de SIW_ARQUIVO. Indica a que arquivo o registro está ligado.';

/*==============================================================*/
/* Index: IN_PJCOMARQ_INV                                       */
/*==============================================================*/
CREATE  INDEX IN_PJCOMARQ_INV ON PJ_COMENTARIO_ARQ (
SQ_SIW_ARQUIVO,
SQ_ETAPA_COMENTARIO
);

/*==============================================================*/
/* Table: PJ_ETAPA_COMENTARIO                                   */
/*==============================================================*/
CREATE TABLE PJ_ETAPA_COMENTARIO (
   SQ_ETAPA_COMENTARIO  NUMERIC(18)          NOT NULL,
   SQ_PROJETO_ETAPA     NUMERIC(18)          NOT NULL,
   SQ_PESSOA_INCLUSAO   NUMERIC(18)          NOT NULL,
   COMENTARIO           VARCHAR(4000)        NOT NULL,
   INCLUSAO             DATE                 NOT NULL DEFAULT 'now()',
   ENVIA_MAIL           VARCHAR(1)           NOT NULL DEFAULT 'S'
      CONSTRAINT CKC_ENVIA_MAIL_PJ_ETAPA CHECK (ENVIA_MAIL IN ('S','N') AND ENVIA_MAIL = UPPER(ENVIA_MAIL)),
   REGISTRADO           VARCHAR(1)           NOT NULL DEFAULT 'N'
      CONSTRAINT CKC_REGISTRADO_PJ_ETAPA CHECK (REGISTRADO IN ('S','N') AND REGISTRADO = UPPER(REGISTRADO)),
   REGISTRO             DATE                 NULL,
   CONSTRAINT PK_PJ_ETAPA_COMENTARIO PRIMARY KEY (SQ_ETAPA_COMENTARIO)
);

COMMENT ON TABLE PJ_ETAPA_COMENTARIO IS
'Registra comentários dos usuários a respeito de uma etapa do projeto.';

COMMENT ON COLUMN PJ_ETAPA_COMENTARIO.SQ_ETAPA_COMENTARIO IS
'Chave de PJ_ETAPA_COMENTARIO.';

COMMENT ON COLUMN PJ_ETAPA_COMENTARIO.SQ_PROJETO_ETAPA IS
'Chave de PJ_PROJETO_ETAPA. Indica a que etapa do projeto o registro está ligado.';

COMMENT ON COLUMN PJ_ETAPA_COMENTARIO.SQ_PESSOA_INCLUSAO IS
'Chave de CO_PESSOA. Indica a pessoa responsável pela inclusão do registro.';

COMMENT ON COLUMN PJ_ETAPA_COMENTARIO.COMENTARIO IS
'Detalhamento do comentário.';

COMMENT ON COLUMN PJ_ETAPA_COMENTARIO.INCLUSAO IS
'Data de inclusão do comentário.';

COMMENT ON COLUMN PJ_ETAPA_COMENTARIO.ENVIA_MAIL IS
'Indica se deve ser enviado e-mail aos responsáveis pela etapa ao final da gravação.';

COMMENT ON COLUMN PJ_ETAPA_COMENTARIO.REGISTRADO IS
'Indica se o comentário está em fase de edição ou já foi registrado.';

COMMENT ON COLUMN PJ_ETAPA_COMENTARIO.REGISTRO IS
'Data de registro do comentário. Preenchido apenas para comentários registrados.';

/*==============================================================*/
/* Index: IN_PJETAPACOM_ETAPA                                   */
/*==============================================================*/
CREATE  INDEX IN_PJETAPACOM_ETAPA ON PJ_ETAPA_COMENTARIO (
SQ_PROJETO_ETAPA,
SQ_ETAPA_COMENTARIO
);

/*==============================================================*/
/* Index: IN_PJETAPACOM_INCLUSAO                                */
/*==============================================================*/
CREATE  INDEX IN_PJETAPACOM_INCLUSAO ON PJ_ETAPA_COMENTARIO (
INCLUSAO,
SQ_ETAPA_COMENTARIO
);

/*==============================================================*/
/* Index: IN_PJETAPACOM_PESSOA                                  */
/*==============================================================*/
CREATE  INDEX IN_PJETAPACOM_PESSOA ON PJ_ETAPA_COMENTARIO (
SQ_PESSOA_INCLUSAO,
SQ_ETAPA_COMENTARIO
);

/*==============================================================*/
/* Index: IN_PJETACOM_REGISTRO                                  */
/*==============================================================*/
CREATE  INDEX IN_PJETACOM_REGISTRO ON PJ_ETAPA_COMENTARIO (
REGISTRADO,
SQ_ETAPA_COMENTARIO
);

/*==============================================================*/
/* Table: PJ_ETAPA_CONTRATO                                     */
/*==============================================================*/
CREATE TABLE PJ_ETAPA_CONTRATO (
   SQ_ETAPA_CONTRATO    NUMERIC(18)          NOT NULL,
   SQ_PROJETO_ETAPA     NUMERIC(18)          NULL,
   SQ_SIW_SOLICITACAO   NUMERIC(18)          NULL,
   CONSTRAINT PK_PJ_ETAPA_CONTRATO PRIMARY KEY (SQ_ETAPA_CONTRATO)
);

COMMENT ON TABLE PJ_ETAPA_CONTRATO IS
'Relaciona os contratos vinculados à etapa.';

COMMENT ON COLUMN PJ_ETAPA_CONTRATO.SQ_ETAPA_CONTRATO IS
'Chave de PJ_ETAPA_CONTRATO. Indica a que etapa do projeto o contrato está ligado.';

COMMENT ON COLUMN PJ_ETAPA_CONTRATO.SQ_PROJETO_ETAPA IS
'Chave de PJ_PROJETO_ETAPA. Indica a que etapa do projeto o registro está ligado.';

COMMENT ON COLUMN PJ_ETAPA_CONTRATO.SQ_SIW_SOLICITACAO IS
'Chave de SIW_SOLICITACAO indicando a que solicitação o registro está ligado.';

/*==============================================================*/
/* Index: IN_PJETACON_ETAPA                                     */
/*==============================================================*/
CREATE  INDEX IN_PJETACON_ETAPA ON PJ_ETAPA_CONTRATO (
SQ_PROJETO_ETAPA,
SQ_ETAPA_CONTRATO
);

/*==============================================================*/
/* Index: IN_PJETACON_SOLIC                                     */
/*==============================================================*/
CREATE  INDEX IN_PJETACON_SOLIC ON PJ_ETAPA_CONTRATO (
SQ_SIW_SOLICITACAO,
SQ_ETAPA_CONTRATO
);

/*==============================================================*/
/* Table: PJ_ETAPA_DEMANDA                                      */
/*==============================================================*/
CREATE TABLE PJ_ETAPA_DEMANDA (
   SQ_ETAPA_DEMANDA     NUMERIC(18)          NOT NULL,
   SQ_PROJETO_ETAPA     NUMERIC(18)          NOT NULL,
   SQ_SIW_SOLICITACAO   NUMERIC(18)          NOT NULL,
   CONSTRAINT PK_PJ_ETAPA_DEMANDA PRIMARY KEY (SQ_ETAPA_DEMANDA)
);

COMMENT ON TABLE PJ_ETAPA_DEMANDA IS
'Relaciona as demandas necessárias ao cumprimento da etapa.';

COMMENT ON COLUMN PJ_ETAPA_DEMANDA.SQ_ETAPA_DEMANDA IS
'Chave de PJ_ETAPA_DEMANDA. Indica a que etapa do projeto a demanda está ligada.';

COMMENT ON COLUMN PJ_ETAPA_DEMANDA.SQ_PROJETO_ETAPA IS
'Chave de PJ_PROJETO_ETAPA. Indica a que etapa do projeto o registro está ligado.';

COMMENT ON COLUMN PJ_ETAPA_DEMANDA.SQ_SIW_SOLICITACAO IS
'Chave de SIW_SOLICITACAO indicando a que solicitação o registro está ligado.';

/*==============================================================*/
/* Index: IN_PJETADEM_ETAPA                                     */
/*==============================================================*/
CREATE  INDEX IN_PJETADEM_ETAPA ON PJ_ETAPA_DEMANDA (
SQ_PROJETO_ETAPA,
SQ_ETAPA_DEMANDA
);

/*==============================================================*/
/* Index: IN_PJETADEM_SOLIC                                     */
/*==============================================================*/
CREATE  INDEX IN_PJETADEM_SOLIC ON PJ_ETAPA_DEMANDA (
SQ_SIW_SOLICITACAO,
SQ_ETAPA_DEMANDA
);

/*==============================================================*/
/* Table: PJ_ETAPA_MENSAL                                       */
/*==============================================================*/
CREATE TABLE PJ_ETAPA_MENSAL (
   SQ_PROJETO_ETAPA     NUMERIC(18)          NOT NULL,
   REFERENCIA           DATE                 NOT NULL,
   EXECUCAO_FISICA      NUMERIC(18,2)        NOT NULL,
   EXECUCAO_FINANCEIRA  NUMERIC(18,2)        NOT NULL,
   CONSTRAINT PK_PJ_ETAPA_MENSAL PRIMARY KEY (SQ_PROJETO_ETAPA, REFERENCIA)
);

COMMENT ON TABLE PJ_ETAPA_MENSAL IS
'Registra quantitativos mensais de execução da etapa.';

COMMENT ON COLUMN PJ_ETAPA_MENSAL.SQ_PROJETO_ETAPA IS
'Chave de PJ_PROJETO_ETAPA. Indica a que etapa do projeto o registro está ligado.';

COMMENT ON COLUMN PJ_ETAPA_MENSAL.REFERENCIA IS
'Mês de referência da informação. Será informado sempre o último dia do mês.';

COMMENT ON COLUMN PJ_ETAPA_MENSAL.EXECUCAO_FISICA IS
'Quantitativo físico executado no mês de referência.';

COMMENT ON COLUMN PJ_ETAPA_MENSAL.EXECUCAO_FINANCEIRA IS
'Valor financeiro executado no mês de referência.';

/*==============================================================*/
/* Index: IN_PJETAMEN_INVERSA                                   */
/*==============================================================*/
CREATE  INDEX IN_PJETAMEN_INVERSA ON PJ_ETAPA_MENSAL (
REFERENCIA,
SQ_PROJETO_ETAPA
);

/*==============================================================*/
/* Table: PJ_PROJETO                                            */
/*==============================================================*/
CREATE TABLE PJ_PROJETO (
   SQ_SIW_SOLICITACAO   NUMERIC(18)          NOT NULL,
   SQ_UNIDADE_RESP      NUMERIC(10)          NOT NULL,
   OUTRA_PARTE          NUMERIC(18)          NULL,
   PREPOSTO             NUMERIC(18)          NULL,
   SQ_TIPO_PESSOA       NUMERIC(18)          NULL,
   PRIORIDADE           NUMERIC(2)           NULL,
   DIAS_AVISO           NUMERIC(3)           NOT NULL DEFAULT 0,
   PROPONENTE           VARCHAR(90)          NULL,
   INICIO_REAL          DATE                 NULL,
   FIM_REAL             DATE                 NULL,
   CONCLUIDA            VARCHAR(1)           NOT NULL DEFAULT 'N'
      CONSTRAINT CKC_PJPRO_CONC CHECK (CONCLUIDA IN ('S','N') AND CONCLUIDA = UPPER(CONCLUIDA)),
   DATA_CONCLUSAO       DATE                 NULL,
   NOTA_CONCLUSAO       VARCHAR(2005)        NULL,
   CUSTO_REAL           NUMERIC(18,2)        NOT NULL DEFAULT 0,
   VINCULA_CONTRATO     VARCHAR(1)           NOT NULL DEFAULT 'N'
      CONSTRAINT CKC_PJPRO_CONTRATO CHECK (VINCULA_CONTRATO IN ('S','N') AND VINCULA_CONTRATO = UPPER(VINCULA_CONTRATO)),
   VINCULA_VIAGEM       VARCHAR(1)           NOT NULL DEFAULT 'N'
      CONSTRAINT CKC_PJPRO_VIAGEM CHECK (VINCULA_VIAGEM IN ('S','N') AND VINCULA_VIAGEM = UPPER(VINCULA_VIAGEM)),
   AVISO_PROX_CONC      VARCHAR(1)           NOT NULL DEFAULT 'N'
      CONSTRAINT CKC_PJPRO_AVISO CHECK (AVISO_PROX_CONC IN ('S','N') AND AVISO_PROX_CONC = UPPER(AVISO_PROX_CONC)),
   SQ_CIDADE            NUMERIC(18)          NULL,
   LIMITE_PASSAGEM      NUMERIC(18)          NULL,
   OBJETIVO_SUPERIOR    VARCHAR(2000)        NULL,
   EXCLUSOES            VARCHAR(2000)        NULL,
   PREMISSAS            VARCHAR(2000)        NULL,
   RESTRICOES           VARCHAR(2000)        NULL,
   AVISO_PROX_CONC_PACOTE VARCHAR(1)           NOT NULL DEFAULT 'N'
      CONSTRAINT CKC_AVISO_PROX_CONC_P_PJ_PROJE CHECK (AVISO_PROX_CONC_PACOTE IN ('S','N') AND AVISO_PROX_CONC_PACOTE = UPPER(AVISO_PROX_CONC_PACOTE)),
   PERC_DIAS_AVISO_PACOTE NUMERIC(3)           NOT NULL DEFAULT 0,
   INSTANCIA_ARTICULACAO VARCHAR(500)         NULL,
   COMPOSICAO_INSTANCIA VARCHAR(500)         NULL,
   ESTUDOS              VARCHAR(2000)        NULL,
   ANALISE1             VARCHAR(2000)        NULL,
   ANALISE2             VARCHAR(2000)        NULL,
   ANALISE3             VARCHAR(2000)        NULL,
   ANALISE4             VARCHAR(2000)        NULL,
   EXIBE_RELATORIO      VARCHAR(1)           NOT NULL DEFAULT 'N'
      CONSTRAINT CKC_EXIBE_RELATORIO_PJ_PROJE CHECK (EXIBE_RELATORIO IN ('S','N') AND EXIBE_RELATORIO = UPPER(EXIBE_RELATORIO)),
   CONSTRAINT PK_PJ_PROJETO PRIMARY KEY (SQ_SIW_SOLICITACAO)
);

COMMENT ON TABLE PJ_PROJETO IS
'Registra as informações cadastrais do projeto';

COMMENT ON COLUMN PJ_PROJETO.SQ_SIW_SOLICITACAO IS
'Chave de SIW_SOLICITACAO indicando a que solicitação o registro está ligado.';

COMMENT ON COLUMN PJ_PROJETO.SQ_UNIDADE_RESP IS
'Chave de EO_UNIDADE. Indica a unidade responsável pelo monitoramento do projeto.';

COMMENT ON COLUMN PJ_PROJETO.OUTRA_PARTE IS
'Chave de CO_PESSOA, indicando a outra parte do projeto, se existir.';

COMMENT ON COLUMN PJ_PROJETO.PREPOSTO IS
'Chave de CO_PESSOA, indicando o preposto se a outra parte for pessoa jurídica.';

COMMENT ON COLUMN PJ_PROJETO.SQ_TIPO_PESSOA IS
'Chave de CO_TIPO_PESSOA. Quando o projeto está associado a outra parte, indica se ela é pessoa física ou jurídica.';

COMMENT ON COLUMN PJ_PROJETO.PRIORIDADE IS
'Registra a prioridade do projeto. Quanto menor o número, mais alta a prioridade.';

COMMENT ON COLUMN PJ_PROJETO.DIAS_AVISO IS
'Se o campo AVISO_PROX_CONC igual a S, indica o número de dias a partir do qual devem ser enviados os avisos por e-mail.';

COMMENT ON COLUMN PJ_PROJETO.PROPONENTE IS
'Proponente da demanda. Texto livre.';

COMMENT ON COLUMN PJ_PROJETO.INICIO_REAL IS
'Início real do projeto.';

COMMENT ON COLUMN PJ_PROJETO.FIM_REAL IS
'Fim real do projeto.';

COMMENT ON COLUMN PJ_PROJETO.CONCLUIDA IS
'Indica se a demanda está concluída ou não.';

COMMENT ON COLUMN PJ_PROJETO.DATA_CONCLUSAO IS
'Data informada pelo usuário.';

COMMENT ON COLUMN PJ_PROJETO.NOTA_CONCLUSAO IS
'Observações relativas à conclusão da demanda.';

COMMENT ON COLUMN PJ_PROJETO.CUSTO_REAL IS
'Custo real para execução do projeto.';

COMMENT ON COLUMN PJ_PROJETO.VINCULA_CONTRATO IS
'Indica se é possível a vinculação de contratos ao projeto.';

COMMENT ON COLUMN PJ_PROJETO.VINCULA_VIAGEM IS
'Indica se é possível a vinculação de passagens e diárias ao projeto.';

COMMENT ON COLUMN PJ_PROJETO.AVISO_PROX_CONC IS
'Indica se é necessário avisar a proximidade da data limite para conclusão da demanda.';

COMMENT ON COLUMN PJ_PROJETO.SQ_CIDADE IS
'Chave de CO_CIDADE indicando a cidade de realização do projeto.';

COMMENT ON COLUMN PJ_PROJETO.LIMITE_PASSAGEM IS
'Indica a quantidade máxima de passagens permitidas para este projeto.';

COMMENT ON COLUMN PJ_PROJETO.OBJETIVO_SUPERIOR IS
'Objetivo superior do projeto.';

COMMENT ON COLUMN PJ_PROJETO.EXCLUSOES IS
'Objetivo específicas, ligadas ao projeto. ';

COMMENT ON COLUMN PJ_PROJETO.PREMISSAS IS
'Premissas para a execução do projeto.';

COMMENT ON COLUMN PJ_PROJETO.RESTRICOES IS
'Restrições do projeto.';

COMMENT ON COLUMN PJ_PROJETO.AVISO_PROX_CONC_PACOTE IS
'Indica se é necessário avisar a proximidade da data limite para conclusão dos pacotes de trabalho.';

COMMENT ON COLUMN PJ_PROJETO.PERC_DIAS_AVISO_PACOTE IS
'Se o campo AVISO_PROX_CONC_PACOTE igual a S, indica o percentual de dias a partir do qual devem ser enviados os avisos por e-mail.';

COMMENT ON COLUMN PJ_PROJETO.INSTANCIA_ARTICULACAO IS
'Instância de articulação público-privada.';

COMMENT ON COLUMN PJ_PROJETO.COMPOSICAO_INSTANCIA IS
'Composição da instância de articulação público-privada.';

COMMENT ON COLUMN PJ_PROJETO.ESTUDOS IS
'Estudos.';

COMMENT ON COLUMN PJ_PROJETO.ANALISE1 IS
'Texto  1 de análise. Utilizado para registro da análise de um perfil da aplicação.';

COMMENT ON COLUMN PJ_PROJETO.ANALISE2 IS
'Texto  2 de análise. Utilizado para registro da análise de um perfil da aplicação.';

COMMENT ON COLUMN PJ_PROJETO.ANALISE3 IS
'Texto  3 de análise. Utilizado para registro da análise de um perfil da aplicação.';

COMMENT ON COLUMN PJ_PROJETO.ANALISE4 IS
'Texto  4 de análise. Utilizado para registro da análise de um perfil da aplicação.';

COMMENT ON COLUMN PJ_PROJETO.EXIBE_RELATORIO IS
'Se o projeto já foi concluído, indica se deve ser exibido nos relatórios gerenciais.';

/*==============================================================*/
/* Index: IN_PJPRO_UNID                                         */
/*==============================================================*/
CREATE  INDEX IN_PJPRO_UNID ON PJ_PROJETO (
SQ_UNIDADE_RESP,
SQ_SIW_SOLICITACAO
);

/*==============================================================*/
/* Index: IN_PJPRO_INI                                          */
/*==============================================================*/
CREATE  INDEX IN_PJPRO_INI ON PJ_PROJETO (
INICIO_REAL,
SQ_SIW_SOLICITACAO
);

/*==============================================================*/
/* Index: IN_PJPRO_FIM                                          */
/*==============================================================*/
CREATE  INDEX IN_PJPRO_FIM ON PJ_PROJETO (
FIM_REAL,
SQ_SIW_SOLICITACAO
);

/*==============================================================*/
/* Index: IN_PJPRO_PRIOR                                        */
/*==============================================================*/
CREATE  INDEX IN_PJPRO_PRIOR ON PJ_PROJETO (
PRIORIDADE,
SQ_SIW_SOLICITACAO
);

/*==============================================================*/
/* Index: IN_PJPRO_CONC                                         */
/*==============================================================*/
CREATE  INDEX IN_PJPRO_CONC ON PJ_PROJETO (
CONCLUIDA,
SQ_SIW_SOLICITACAO
);

/*==============================================================*/
/* Index: IN_PJPRO_CUSTO                                        */
/*==============================================================*/
CREATE  INDEX IN_PJPRO_CUSTO ON PJ_PROJETO (
CUSTO_REAL,
SQ_SIW_SOLICITACAO
);

/*==============================================================*/
/* Index: IN_PJPRO_DTCONC                                       */
/*==============================================================*/
CREATE  INDEX IN_PJPRO_DTCONC ON PJ_PROJETO (
DATA_CONCLUSAO,
SQ_SIW_SOLICITACAO
);

/*==============================================================*/
/* Index: IN_PJPRO_PROPON                                       */
/*==============================================================*/
CREATE  INDEX IN_PJPRO_PROPON ON PJ_PROJETO (
PROPONENTE,
SQ_SIW_SOLICITACAO
);

/*==============================================================*/
/* Index: IN_PJPRO_OUTRA                                        */
/*==============================================================*/
CREATE  INDEX IN_PJPRO_OUTRA ON PJ_PROJETO (
OUTRA_PARTE,
SQ_SIW_SOLICITACAO
);

/*==============================================================*/
/* Index: IN_PJPRO_REPRES                                       */
/*==============================================================*/
CREATE  INDEX IN_PJPRO_REPRES ON PJ_PROJETO (
PREPOSTO,
SQ_SIW_SOLICITACAO
);

/*==============================================================*/
/* Index: IN_PJPRO_TIPOPESSOA                                   */
/*==============================================================*/
CREATE  INDEX IN_PJPRO_TIPOPESSOA ON PJ_PROJETO (
SQ_TIPO_PESSOA,
SQ_SIW_SOLICITACAO
);

/*==============================================================*/
/* Table: PJ_PROJETO_ENVOLV                                     */
/*==============================================================*/
CREATE TABLE PJ_PROJETO_ENVOLV (
   SQ_UNIDADE           NUMERIC(10)          NOT NULL,
   SQ_SIW_SOLICITACAO   NUMERIC(18)          NOT NULL,
   PAPEL                VARCHAR(2000)        NOT NULL,
   INTERESSE_POSITIVO   VARCHAR(1)           NOT NULL DEFAULT 'S'
      CONSTRAINT CKC_INTERESSE_POSITIV_PJ_PROJE CHECK (INTERESSE_POSITIVO IN ('S','N') AND INTERESSE_POSITIVO = UPPER(INTERESSE_POSITIVO)),
   INFLUENCIA           NUMERIC(2)           NULL,
   CONSTRAINT PK_PJ_PROJETO_ENVOLV PRIMARY KEY (SQ_UNIDADE, SQ_SIW_SOLICITACAO)
);

COMMENT ON TABLE PJ_PROJETO_ENVOLV IS
'Registra as unidades envolvidas na execução do projeto.';

COMMENT ON COLUMN PJ_PROJETO_ENVOLV.SQ_UNIDADE IS
'Chave de EO_UNIDADE. Indica a que unidade o registro está ligado.';

COMMENT ON COLUMN PJ_PROJETO_ENVOLV.SQ_SIW_SOLICITACAO IS
'Chave de SIW_SOLICITACAO indicando a que solicitação o registro está ligado.';

COMMENT ON COLUMN PJ_PROJETO_ENVOLV.PAPEL IS
'Papel cumprido pela área envolvida.';

COMMENT ON COLUMN PJ_PROJETO_ENVOLV.INTERESSE_POSITIVO IS
'Indica se o interesse da área é positivo ou negativo.';

COMMENT ON COLUMN PJ_PROJETO_ENVOLV.INFLUENCIA IS
'Registra a influência da área no projeto. 0 - Alta, 1 - Média, 2 - Baixa.';

/*==============================================================*/
/* Index: IN_PJPROENV_INVERSA                                   */
/*==============================================================*/
CREATE  INDEX IN_PJPROENV_INVERSA ON PJ_PROJETO_ENVOLV (
SQ_SIW_SOLICITACAO,
SQ_UNIDADE
);

/*==============================================================*/
/* Table: PJ_PROJETO_ETAPA                                      */
/*==============================================================*/
CREATE TABLE PJ_PROJETO_ETAPA (
   SQ_PROJETO_ETAPA     NUMERIC(18)          NOT NULL,
   SQ_SIW_SOLICITACAO   NUMERIC(18)          NOT NULL,
   SQ_ETAPA_PAI         NUMERIC(18)          NULL,
   SQ_PESSOA            NUMERIC(18)          NOT NULL,
   SQ_PESSOA_ATUALIZACAO NUMERIC(18)          NOT NULL,
   SQ_UNIDADE           NUMERIC(10)          NOT NULL,
   ORDEM                NUMERIC(3)           NOT NULL,
   TITULO               VARCHAR(150)         NOT NULL,
   DESCRICAO            VARCHAR(2000)        NOT NULL,
   INICIO_PREVISTO      DATE                 NOT NULL,
   FIM_PREVISTO         DATE                 NOT NULL,
   INICIO_REAL          DATE                 NULL,
   FIM_REAL             DATE                 NULL,
   PERC_CONCLUSAO       NUMERIC(18,2)        NOT NULL DEFAULT 0,
   ORCAMENTO            NUMERIC(18,2)        NOT NULL DEFAULT 0,
   VINCULA_ATIVIDADE    VARCHAR(1)           NOT NULL DEFAULT 'S'
      CONSTRAINT CKC_VINCULA_ATIVIDADE_PJ_PROJE CHECK (VINCULA_ATIVIDADE IN ('S','N') AND VINCULA_ATIVIDADE = UPPER(VINCULA_ATIVIDADE)),
   ULTIMA_ATUALIZACAO   DATE                 NOT NULL DEFAULT 'now()',
   SITUACAO_ATUAL       VARCHAR(4000)        NULL,
   UNIDADE_MEDIDA       VARCHAR(30)          NULL,
   QUANTIDADE           NUMERIC(18,2)        NOT NULL DEFAULT 0,
   CUMULATIVA           VARCHAR(1)           NOT NULL DEFAULT 'N'
      CONSTRAINT CKC_CUMULATIVA_PJ_PROJE CHECK (CUMULATIVA IN ('S','N') AND CUMULATIVA = UPPER(CUMULATIVA)),
   PROGRAMADA           VARCHAR(1)           NOT NULL DEFAULT 'N'
      CONSTRAINT CKC_PROGRAMADA_PJ_PROJE CHECK (PROGRAMADA IN ('S','N') AND PROGRAMADA = UPPER(PROGRAMADA)),
   EXEQUIVEL            VARCHAR(1)           NOT NULL DEFAULT 'S'
      CONSTRAINT CKC_EXEQUIVEL_PJ_PROJE CHECK (EXEQUIVEL IN ('S','N') AND EXEQUIVEL = UPPER(EXEQUIVEL)),
   JUSTIFICATIVA_INEXEQUIVEL VARCHAR(1000)        NULL,
   OUTRAS_MEDIDAS       VARCHAR(1000)        NULL,
   VINCULA_CONTRATO     VARCHAR(1)           NOT NULL DEFAULT 'N'
      CONSTRAINT CKC_VINCULA_CONTRATO_PJ_PROJE CHECK (VINCULA_CONTRATO IN ('S','N') AND VINCULA_CONTRATO = UPPER(VINCULA_CONTRATO)),
   PACOTE_TRABALHO      VARCHAR(1)           NOT NULL DEFAULT 'N'
      CONSTRAINT CKC_PACOTE_PJPROETA CHECK (PACOTE_TRABALHO IN ('S','N') AND PACOTE_TRABALHO = UPPER(PACOTE_TRABALHO)),
   BASE_GEOGRAFICA      NUMERIC(1)           NULL,
   SQ_PAIS              NUMERIC(18)          NULL,
   SQ_REGIAO            NUMERIC(18)          NULL,
   CO_UF                VARCHAR(3)           NULL,
   SQ_CIDADE            NUMERIC(18)          NULL,
   PESO                 NUMERIC(2)           NOT NULL DEFAULT 1,
   PESO_PAI             NUMERIC(18,15)       NOT NULL DEFAULT 0,
   PESO_PROJETO         NUMERIC(18,15)       NOT NULL DEFAULT 0,
   CONSTRAINT PK_PJ_PROJETO_ETAPA PRIMARY KEY (SQ_PROJETO_ETAPA)
);

COMMENT ON TABLE PJ_PROJETO_ETAPA IS
'Registra as etapas do projeto.';

COMMENT ON COLUMN PJ_PROJETO_ETAPA.SQ_PROJETO_ETAPA IS
'Chave de PJ_PROJETO_ETAPA.';

COMMENT ON COLUMN PJ_PROJETO_ETAPA.SQ_SIW_SOLICITACAO IS
'Chave de SIW_SOLICITACAO indicando a que solicitação o registro está ligado.';

COMMENT ON COLUMN PJ_PROJETO_ETAPA.SQ_ETAPA_PAI IS
'Chave de PJ_PROJETO_ETAPA. Auto-relacionamento da tabela.';

COMMENT ON COLUMN PJ_PROJETO_ETAPA.SQ_PESSOA IS
'Chave de CO_PESSOA. Responsável pela etapa.';

COMMENT ON COLUMN PJ_PROJETO_ETAPA.SQ_PESSOA_ATUALIZACAO IS
'Chave de CO_PESSOA. Usuário responsável pela criação ou última atualização da etapa.';

COMMENT ON COLUMN PJ_PROJETO_ETAPA.SQ_UNIDADE IS
'Chave de EO_UNIDADE. Indica a unidade responsável pela etapa.';

COMMENT ON COLUMN PJ_PROJETO_ETAPA.ORDEM IS
'Ordem de execução da etapa.';

COMMENT ON COLUMN PJ_PROJETO_ETAPA.TITULO IS
'Título da etapa.';

COMMENT ON COLUMN PJ_PROJETO_ETAPA.DESCRICAO IS
'Descrição da etapa.';

COMMENT ON COLUMN PJ_PROJETO_ETAPA.INICIO_PREVISTO IS
'Início previsto da etapa.';

COMMENT ON COLUMN PJ_PROJETO_ETAPA.FIM_PREVISTO IS
'Fim previsto para a etapa.';

COMMENT ON COLUMN PJ_PROJETO_ETAPA.INICIO_REAL IS
'Início real da etapa.';

COMMENT ON COLUMN PJ_PROJETO_ETAPA.FIM_REAL IS
'Fim real da etapa.';

COMMENT ON COLUMN PJ_PROJETO_ETAPA.PERC_CONCLUSAO IS
'Percentual de concluso da etapa.';

COMMENT ON COLUMN PJ_PROJETO_ETAPA.ORCAMENTO IS
'Orçamento disponível para cumprimento da etapa.';

COMMENT ON COLUMN PJ_PROJETO_ETAPA.VINCULA_ATIVIDADE IS
'Indica se atividades podem ser vinculadas a esta etapa ou se ele existe apenas para agrupamento.';

COMMENT ON COLUMN PJ_PROJETO_ETAPA.ULTIMA_ATUALIZACAO IS
'Registra a data da criação ou última atualização da etapa.';

COMMENT ON COLUMN PJ_PROJETO_ETAPA.SITUACAO_ATUAL IS
'Texto detalhando a situação atual da etapa.';

COMMENT ON COLUMN PJ_PROJETO_ETAPA.UNIDADE_MEDIDA IS
'Unidade de medida a ser realizada.';

COMMENT ON COLUMN PJ_PROJETO_ETAPA.QUANTIDADE IS
'Quantidade prevista para a unidade de medida informada.';

COMMENT ON COLUMN PJ_PROJETO_ETAPA.CUMULATIVA IS
'Indica se a realização da etapa é cumulativa ou não.';

COMMENT ON COLUMN PJ_PROJETO_ETAPA.PROGRAMADA IS
'Indica se a etapa está vinculada ao planejamento estratégico.';

COMMENT ON COLUMN PJ_PROJETO_ETAPA.EXEQUIVEL IS
'Indica se a etapa está avaliada como passível de cumprimento ou não.';

COMMENT ON COLUMN PJ_PROJETO_ETAPA.JUSTIFICATIVA_INEXEQUIVEL IS
'Motivos que justificam o não cumprimento da etapa.';

COMMENT ON COLUMN PJ_PROJETO_ETAPA.OUTRAS_MEDIDAS IS
'Descrição das medidas necessárias ao cumprimento da etapa.';

COMMENT ON COLUMN PJ_PROJETO_ETAPA.VINCULA_CONTRATO IS
'Indica se contratos podem ser vinculados a esta etapa.';

COMMENT ON COLUMN PJ_PROJETO_ETAPA.PACOTE_TRABALHO IS
'Indica se a etapa é um pacote de trabalho. Se for, tem que ser último nível.';

COMMENT ON COLUMN PJ_PROJETO_ETAPA.BASE_GEOGRAFICA IS
'Indica a que base geográfica da etapa. 1 - Nacional, 2 - Regional, 3 - Estadual, 4 - Municipal, 5 - Organizacional.';

COMMENT ON COLUMN PJ_PROJETO_ETAPA.SQ_PAIS IS
'Chave de CO_PAIS. Tem valor apenas quando a base geográfica é a nivel nacional.';

COMMENT ON COLUMN PJ_PROJETO_ETAPA.SQ_REGIAO IS
'Chave de CO_REGIAO. Tem valor apenas quando a base geográfica é a nivel regional.';

COMMENT ON COLUMN PJ_PROJETO_ETAPA.CO_UF IS
'Chave de CO_UF. Tem valor apenas quando a base geográfica é a nivel estadual.';

COMMENT ON COLUMN PJ_PROJETO_ETAPA.SQ_CIDADE IS
'Chave de CO_CIDADE. Tem valor apenas quando a base geográfica é a nivel municipal.';

COMMENT ON COLUMN PJ_PROJETO_ETAPA.PESO IS
'Peso da etapa para cálculo do percentual de execução.';

COMMENT ON COLUMN PJ_PROJETO_ETAPA.PESO_PAI IS
'Peso relativo da etapa em relação ao pai.';

COMMENT ON COLUMN PJ_PROJETO_ETAPA.PESO_PROJETO IS
'Peso relativo da etapa em relação ao projeto.';

/*==============================================================*/
/* Index: IN_PJPROETA_PAI                                       */
/*==============================================================*/
CREATE  INDEX IN_PJPROETA_PAI ON PJ_PROJETO_ETAPA (
SQ_ETAPA_PAI,
SQ_PROJETO_ETAPA
);

/*==============================================================*/
/* Index: IN_PJPROETA_PROJ                                      */
/*==============================================================*/
CREATE  INDEX IN_PJPROETA_PROJ ON PJ_PROJETO_ETAPA (
SQ_SIW_SOLICITACAO,
SQ_PROJETO_ETAPA
);

/*==============================================================*/
/* Index: IN_PJPROETA_INI                                       */
/*==============================================================*/
CREATE  INDEX IN_PJPROETA_INI ON PJ_PROJETO_ETAPA (
INICIO_PREVISTO,
SQ_PROJETO_ETAPA
);

/*==============================================================*/
/* Index: IN_PJPROETA_INIRE                                     */
/*==============================================================*/
CREATE  INDEX IN_PJPROETA_INIRE ON PJ_PROJETO_ETAPA (
INICIO_REAL,
SQ_PROJETO_ETAPA
);

/*==============================================================*/
/* Index: IN_PJPROETA_FIM                                       */
/*==============================================================*/
CREATE  INDEX IN_PJPROETA_FIM ON PJ_PROJETO_ETAPA (
FIM_PREVISTO,
SQ_PROJETO_ETAPA
);

/*==============================================================*/
/* Index: IN_PJPROETA_ORDEM                                     */
/*==============================================================*/
CREATE  INDEX IN_PJPROETA_ORDEM ON PJ_PROJETO_ETAPA (
ORDEM,
SQ_PROJETO_ETAPA
);

/*==============================================================*/
/* Index: IN_PJPROETA_FIMRE                                     */
/*==============================================================*/
CREATE  INDEX IN_PJPROETA_FIMRE ON PJ_PROJETO_ETAPA (
FIM_REAL,
SQ_PROJETO_ETAPA
);

/*==============================================================*/
/* Index: IN_PJPROETA_RESP                                      */
/*==============================================================*/
CREATE  INDEX IN_PJPROETA_RESP ON PJ_PROJETO_ETAPA (
SQ_PESSOA,
SQ_PROJETO_ETAPA
);

/*==============================================================*/
/* Index: IN_PJPROETA_SETOR                                     */
/*==============================================================*/
CREATE  INDEX IN_PJPROETA_SETOR ON PJ_PROJETO_ETAPA (
SQ_UNIDADE,
SQ_SIW_SOLICITACAO,
SQ_PROJETO_ETAPA
);

/*==============================================================*/
/* Index: IN_PJPROETA_PAIS                                      */
/*==============================================================*/
CREATE  INDEX IN_PJPROETA_PAIS ON PJ_PROJETO_ETAPA (
SQ_PAIS,
SQ_SIW_SOLICITACAO,
SQ_PROJETO_ETAPA
);

/*==============================================================*/
/* Index: IN_PJPROETA_REGIAO                                    */
/*==============================================================*/
CREATE  INDEX IN_PJPROETA_REGIAO ON PJ_PROJETO_ETAPA (
SQ_REGIAO,
SQ_SIW_SOLICITACAO,
SQ_PROJETO_ETAPA
);

/*==============================================================*/
/* Index: IN_PJPROETA_UF                                        */
/*==============================================================*/
CREATE  INDEX IN_PJPROETA_UF ON PJ_PROJETO_ETAPA (
SQ_PAIS,
CO_UF,
SQ_SIW_SOLICITACAO,
SQ_PROJETO_ETAPA
);

/*==============================================================*/
/* Index: IN_PJPROETA_CIDADE                                    */
/*==============================================================*/
CREATE  INDEX IN_PJPROETA_CIDADE ON PJ_PROJETO_ETAPA (
SQ_CIDADE,
SQ_SIW_SOLICITACAO,
SQ_PROJETO_ETAPA
);

/*==============================================================*/
/* Index: IN_PJPROETA_BASE                                      */
/*==============================================================*/
CREATE  INDEX IN_PJPROETA_BASE ON PJ_PROJETO_ETAPA (
BASE_GEOGRAFICA,
SQ_SIW_SOLICITACAO,
SQ_PROJETO_ETAPA
);

/*==============================================================*/
/* Table: PJ_PROJETO_ETAPA_ARQ                                  */
/*==============================================================*/
CREATE TABLE PJ_PROJETO_ETAPA_ARQ (
   SQ_PROJETO_ETAPA     NUMERIC(18)          NOT NULL,
   SQ_SIW_ARQUIVO       NUMERIC(18)          NOT NULL,
   CONSTRAINT PK_PJ_PROJETO_ETAPA_ARQ PRIMARY KEY (SQ_PROJETO_ETAPA, SQ_SIW_ARQUIVO)
);

COMMENT ON TABLE PJ_PROJETO_ETAPA_ARQ IS
'Registra os anexos de etapas.';

COMMENT ON COLUMN PJ_PROJETO_ETAPA_ARQ.SQ_PROJETO_ETAPA IS
'Chave de PJ_PROJETO_ETAPA. Indica a que etapa do projeto o registro está ligado.';

COMMENT ON COLUMN PJ_PROJETO_ETAPA_ARQ.SQ_SIW_ARQUIVO IS
'Chave de SIW_ARQUIVO. Indica a que arquivo o registro está ligado.';

/*==============================================================*/
/* Index: IN_PROETAARQ_INV                                      */
/*==============================================================*/
CREATE  INDEX IN_PROETAARQ_INV ON PJ_PROJETO_ETAPA_ARQ (
SQ_SIW_ARQUIVO,
SQ_PROJETO_ETAPA
);

/*==============================================================*/
/* Table: PJ_PROJETO_INTERES                                    */
/*==============================================================*/
CREATE TABLE PJ_PROJETO_INTERES (
   SQ_PESSOA            NUMERIC(18)          NOT NULL,
   SQ_SIW_SOLICITACAO   NUMERIC(18)          NOT NULL,
   TIPO_VISAO           NUMERIC(1)           NOT NULL
      CONSTRAINT CKC_TIPO_VISAO_PJ_PROJE CHECK (TIPO_VISAO IN (0,1,2)),
   ENVIA_EMAIL          VARCHAR(1)           NOT NULL DEFAULT 'N'
      CONSTRAINT CKC_ENVIA_EMAIL_PJ_PROJE CHECK (ENVIA_EMAIL IN ('S','N') AND ENVIA_EMAIL = UPPER(ENVIA_EMAIL)),
   CONSTRAINT PK_PJ_PROJETO_INTERES PRIMARY KEY (SQ_PESSOA, SQ_SIW_SOLICITACAO)
);

COMMENT ON TABLE PJ_PROJETO_INTERES IS
'Registra os interessados pelo projeto e que tipo de informações eles podem receber ou visualizar.';

COMMENT ON COLUMN PJ_PROJETO_INTERES.SQ_PESSOA IS
'Chave de CO_PESSOA. Indica a que pessoa o registro está ligado.';

COMMENT ON COLUMN PJ_PROJETO_INTERES.SQ_SIW_SOLICITACAO IS
'Chave de SIW_SOLICITACAO indicando a que solicitação o registro está ligado.';

COMMENT ON COLUMN PJ_PROJETO_INTERES.TIPO_VISAO IS
'Indica a visão que a pessoa pode ter dessa demanda.';

COMMENT ON COLUMN PJ_PROJETO_INTERES.ENVIA_EMAIL IS
'Indica se deve ser enviado e-mail ao interessado quando houver alguma ocorrência no projeto.';

/*==============================================================*/
/* Index: IN_PJPROINT_INVERSA                                   */
/*==============================================================*/
CREATE  INDEX IN_PJPROINT_INVERSA ON PJ_PROJETO_INTERES (
SQ_SIW_SOLICITACAO,
SQ_PESSOA
);

/*==============================================================*/
/* Index: IN_PJPROINT_EMAIL                                     */
/*==============================================================*/
CREATE  INDEX IN_PJPROINT_EMAIL ON PJ_PROJETO_INTERES (
ENVIA_EMAIL,
SQ_PESSOA,
SQ_SIW_SOLICITACAO
);

/*==============================================================*/
/* Table: PJ_PROJETO_LOG                                        */
/*==============================================================*/
CREATE TABLE PJ_PROJETO_LOG (
   SQ_PROJETO_LOG       NUMERIC(18)          NOT NULL,
   SQ_SIW_SOLICITACAO   NUMERIC(18)          NOT NULL,
   SQ_SIW_SOLIC_LOG     NUMERIC(18)          NULL,
   CADASTRADOR          NUMERIC(18)          NOT NULL,
   DESTINATARIO         NUMERIC(18)          NULL,
   DATA_INCLUSAO        DATE                 NOT NULL,
   OBSERVACAO           VARCHAR(2000)        NULL,
   DESPACHO             VARCHAR(2000)        NULL,
   CONSTRAINT PK_PJ_PROJETO_LOG PRIMARY KEY (SQ_PROJETO_LOG)
);

COMMENT ON TABLE PJ_PROJETO_LOG IS
'Registra o histórico do projjeto';

COMMENT ON COLUMN PJ_PROJETO_LOG.SQ_PROJETO_LOG IS
'Chave de PJ_PROJETO_LOG.';

COMMENT ON COLUMN PJ_PROJETO_LOG.SQ_SIW_SOLICITACAO IS
'Chave de SIW_SOLICITACAO indicando a que solicitação o registro está ligado.';

COMMENT ON COLUMN PJ_PROJETO_LOG.SQ_SIW_SOLIC_LOG IS
'Chave de SIW_SOLIC_LOG.';

COMMENT ON COLUMN PJ_PROJETO_LOG.CADASTRADOR IS
'Chave de CO_PESSOA.';

COMMENT ON COLUMN PJ_PROJETO_LOG.DESTINATARIO IS
'Chave de CO_PESSOA.';

COMMENT ON COLUMN PJ_PROJETO_LOG.DATA_INCLUSAO IS
'Data de inclusão do registro, gerado pelo sistema.';

COMMENT ON COLUMN PJ_PROJETO_LOG.OBSERVACAO IS
'Observações inseridas pelo usuário.';

COMMENT ON COLUMN PJ_PROJETO_LOG.DESPACHO IS
'Orientação ao destinatário sobre as ações necessárias.';

/*==============================================================*/
/* Index: IN_PJPROLOG_PRJ                                       */
/*==============================================================*/
CREATE  INDEX IN_PJPROLOG_PRJ ON PJ_PROJETO_LOG (
SQ_SIW_SOLICITACAO,
SQ_PROJETO_LOG
);

/*==============================================================*/
/* Index: IN_PRJPROLOG_DATA                                     */
/*==============================================================*/
CREATE  INDEX IN_PRJPROLOG_DATA ON PJ_PROJETO_LOG (
SQ_SIW_SOLICITACAO,
DATA_INCLUSAO,
SQ_PROJETO_LOG
);

/*==============================================================*/
/* Index: IN_PJPROLOG_CADAST                                    */
/*==============================================================*/
CREATE  INDEX IN_PJPROLOG_CADAST ON PJ_PROJETO_LOG (
SQ_SIW_SOLICITACAO,
CADASTRADOR,
SQ_PROJETO_LOG
);

/*==============================================================*/
/* Index: IN_PJPROLOG_DEST                                      */
/*==============================================================*/
CREATE  INDEX IN_PJPROLOG_DEST ON PJ_PROJETO_LOG (
SQ_SIW_SOLICITACAO,
DESTINATARIO,
SQ_PROJETO_LOG
);

/*==============================================================*/
/* Index: IN_PJLOG_SIWLOG                                       */
/*==============================================================*/
CREATE  INDEX IN_PJLOG_SIWLOG ON PJ_PROJETO_LOG (
SQ_SIW_SOLIC_LOG,
SQ_PROJETO_LOG
);

/*==============================================================*/
/* Table: PJ_PROJETO_LOG_ARQ                                    */
/*==============================================================*/
CREATE TABLE PJ_PROJETO_LOG_ARQ (
   SQ_PROJETO_LOG       NUMERIC(18)          NOT NULL,
   SQ_SIW_ARQUIVO       NUMERIC(18)          NOT NULL,
   CONSTRAINT PK_PJ_PROJETO_LOG_ARQ PRIMARY KEY (SQ_PROJETO_LOG, SQ_SIW_ARQUIVO)
);

COMMENT ON TABLE PJ_PROJETO_LOG_ARQ IS
'Vincula arquivos a logs de projeto.';

COMMENT ON COLUMN PJ_PROJETO_LOG_ARQ.SQ_PROJETO_LOG IS
'Chave de PJ_PROJETO_LOG. Indica a que registro o arquivo está ligado.';

COMMENT ON COLUMN PJ_PROJETO_LOG_ARQ.SQ_SIW_ARQUIVO IS
'Chave de SIW_ARQUIVO. Indica a que arquivo o registro está ligado.';

/*==============================================================*/
/* Index: IN_PJPROLOGARQ_INVERSA                                */
/*==============================================================*/
CREATE  INDEX IN_PJPROLOGARQ_INVERSA ON PJ_PROJETO_LOG_ARQ (
SQ_SIW_ARQUIVO,
SQ_PROJETO_LOG
);

/*==============================================================*/
/* Table: PJ_PROJETO_RECURSO                                    */
/*==============================================================*/
CREATE TABLE PJ_PROJETO_RECURSO (
   SQ_PROJETO_RECURSO   NUMERIC(18)          NOT NULL,
   SQ_SIW_SOLICITACAO   NUMERIC(18)          NOT NULL,
   NOME                 VARCHAR(100)         NOT NULL,
   TIPO                 NUMERIC(2)           NOT NULL,
   DESCRICAO            VARCHAR(2000)        NULL,
   FINALIDADE           VARCHAR(2000)        NULL,
   CONSTRAINT PK_PJ_PROJETO_RECURSO PRIMARY KEY (SQ_PROJETO_RECURSO)
);

COMMENT ON TABLE PJ_PROJETO_RECURSO IS
'Registra informações sobre os recursos alocados ao projeto.';

COMMENT ON COLUMN PJ_PROJETO_RECURSO.SQ_PROJETO_RECURSO IS
'Chave de PJ_PROJETO_RECURSO.';

COMMENT ON COLUMN PJ_PROJETO_RECURSO.SQ_SIW_SOLICITACAO IS
'Chave de SIW_SOLICITACAO indicando a que solicitação o registro está ligado.';

COMMENT ON COLUMN PJ_PROJETO_RECURSO.NOME IS
'Nome do recurso.';

COMMENT ON COLUMN PJ_PROJETO_RECURSO.TIPO IS
'Tipo do recurso (Humano, Material, Financeiro etc)';

COMMENT ON COLUMN PJ_PROJETO_RECURSO.DESCRICAO IS
'Descrição do recurso';

COMMENT ON COLUMN PJ_PROJETO_RECURSO.FINALIDADE IS
'Finalidade cumprida pelo recurso.';

/*==============================================================*/
/* Index: IN_PJPROREC_PROJ                                      */
/*==============================================================*/
CREATE  INDEX IN_PJPROREC_PROJ ON PJ_PROJETO_RECURSO (
SQ_SIW_SOLICITACAO,
SQ_PROJETO_RECURSO
);

/*==============================================================*/
/* Index: IN_PJPROREC_TIPO                                      */
/*==============================================================*/
CREATE  INDEX IN_PJPROREC_TIPO ON PJ_PROJETO_RECURSO (
TIPO,
SQ_PROJETO_RECURSO
);

/*==============================================================*/
/* Table: PJ_PROJETO_REPRESENTANTE                              */
/*==============================================================*/
CREATE TABLE PJ_PROJETO_REPRESENTANTE (
   SQ_PESSOA            NUMERIC(18)          NOT NULL,
   SQ_SIW_SOLICITACAO   NUMERIC(18)          NOT NULL,
   CONSTRAINT PK_PJ_PROJETO_REPRESENTANTE PRIMARY KEY (SQ_PESSOA, SQ_SIW_SOLICITACAO)
);

COMMENT ON TABLE PJ_PROJETO_REPRESENTANTE IS
'Registra os representantes da outra parte do projeto, se for pessoa jurídica.';

COMMENT ON COLUMN PJ_PROJETO_REPRESENTANTE.SQ_PESSOA IS
'Chave de CO_PESSOA. Indica a que pessoa o registro está ligado.';

COMMENT ON COLUMN PJ_PROJETO_REPRESENTANTE.SQ_SIW_SOLICITACAO IS
'Chave de SIW_SOLICITACAO indicando a que solicitação o registro está ligado.';

/*==============================================================*/
/* Index: IN_PJPROREP_INV                                       */
/*==============================================================*/
CREATE UNIQUE INDEX IN_PJPROREP_INV ON PJ_PROJETO_REPRESENTANTE (
SQ_SIW_SOLICITACAO,
SQ_PESSOA
);

/*==============================================================*/
/* Table: PJ_RECURSO_ETAPA                                      */
/*==============================================================*/
CREATE TABLE PJ_RECURSO_ETAPA (
   SQ_PROJETO_ETAPA     NUMERIC(18)          NOT NULL,
   SQ_PROJETO_RECURSO   NUMERIC(18)          NOT NULL,
   OBSERVACAO           VARCHAR(500)         NULL,
   CONSTRAINT PK_PJ_RECURSO_ETAPA PRIMARY KEY (SQ_PROJETO_ETAPA, SQ_PROJETO_RECURSO)
);

COMMENT ON TABLE PJ_RECURSO_ETAPA IS
'Relaciona os recursos do projeto alocados a essa etapa.';

COMMENT ON COLUMN PJ_RECURSO_ETAPA.SQ_PROJETO_ETAPA IS
'Chave de PJ_PROJETO_ETAPA. Indica a que etapa do projeto o registro está ligado.';

COMMENT ON COLUMN PJ_RECURSO_ETAPA.SQ_PROJETO_RECURSO IS
'Chave de PJ_PROJETO_RECURSO. Indica a que recurso do projeto o registro está ligado.';

COMMENT ON COLUMN PJ_RECURSO_ETAPA.OBSERVACAO IS
'Observações sobre a participação do recurso no cumprimento da etapa.';

/*==============================================================*/
/* Index: IN_PJRECETA_INVERSA                                   */
/*==============================================================*/
CREATE  INDEX IN_PJRECETA_INVERSA ON PJ_RECURSO_ETAPA (
SQ_PROJETO_RECURSO,
SQ_PROJETO_ETAPA
);

/*==============================================================*/
/* Table: PJ_RUBRICA                                            */
/*==============================================================*/
CREATE TABLE PJ_RUBRICA (
   SQ_PROJETO_RUBRICA   NUMERIC(18)          NOT NULL,
   SQ_SIW_SOLICITACAO   NUMERIC(18)          NOT NULL,
   CODIGO               VARCHAR(20)          NOT NULL,
   NOME                 VARCHAR(60)          NOT NULL,
   DESCRICAO            VARCHAR(500)         NOT NULL,
   ATIVO                VARCHAR(1)           NOT NULL DEFAULT 'S'
      CONSTRAINT CKC_ATIVO_PJ_RUBRI CHECK (ATIVO IN ('S','N') AND ATIVO = UPPER(ATIVO)),
   VALOR_INICIAL        NUMERIC(18,2)        NOT NULL DEFAULT 0,
   ENTRADA_PREVISTA     NUMERIC(18,2)        NOT NULL DEFAULT 0,
   ENTRADA_REAL         NUMERIC(18,2)        NOT NULL DEFAULT 0,
   SAIDA_PREVISTA       NUMERIC(18,2)        NOT NULL DEFAULT 0,
   SAIDA_REAL           NUMERIC(18,2)        NOT NULL DEFAULT 0,
   APLICACAO_FINANCEIRA VARCHAR(1)           NOT NULL DEFAULT 'N'
      CONSTRAINT CKC_APLICACAO_FINANCE_PJ_RUBRI CHECK (APLICACAO_FINANCEIRA IN ('S','N') AND APLICACAO_FINANCEIRA = UPPER(APLICACAO_FINANCEIRA)),
   CONSTRAINT PK_PJ_RUBRICA PRIMARY KEY (SQ_PROJETO_RUBRICA)
);

COMMENT ON TABLE PJ_RUBRICA IS
'Registra as rubricas do projeto.';

COMMENT ON COLUMN PJ_RUBRICA.SQ_PROJETO_RUBRICA IS
'Chave de PJ_PROJETO_RUBRICA.';

COMMENT ON COLUMN PJ_RUBRICA.SQ_SIW_SOLICITACAO IS
'Chave de SIW_SOLICITACAO. Indica a que projeto a rubrica está ligada.';

COMMENT ON COLUMN PJ_RUBRICA.CODIGO IS
'Código da rubrica.';

COMMENT ON COLUMN PJ_RUBRICA.NOME IS
'Nome da rubrica.';

COMMENT ON COLUMN PJ_RUBRICA.DESCRICAO IS
'Descrição da rubrica.';

COMMENT ON COLUMN PJ_RUBRICA.ATIVO IS
'Indica se a rubrica pode ser associada a novos lancamentos financeiros.';

COMMENT ON COLUMN PJ_RUBRICA.VALOR_INICIAL IS
'Valor da dotação inicial para a rubrica. Atribuído por trigger a partir dos recebimentos.';

COMMENT ON COLUMN PJ_RUBRICA.ENTRADA_PREVISTA IS
'Somatório das receitas não liquidadas. Calculado por trigger a partir dos recebimentos.';

COMMENT ON COLUMN PJ_RUBRICA.ENTRADA_REAL IS
'Somatório das receitas liquidadas. Calculado por trigger a partir dos recebimentos.';

COMMENT ON COLUMN PJ_RUBRICA.SAIDA_PREVISTA IS
'Somatório das despesas não liquidadas. Calculado por trigger a partir dos pagamentos.';

COMMENT ON COLUMN PJ_RUBRICA.SAIDA_REAL IS
'Somatório das despesas liquidadas. Calculado por trigger a partir dos pagamentos.';

COMMENT ON COLUMN PJ_RUBRICA.APLICACAO_FINANCEIRA IS
'Indica se a rubrica é de relativa a aplicações financeiras.';

/*==============================================================*/
/* Index: IN_PJRUB_SOLIC                                        */
/*==============================================================*/
CREATE  INDEX IN_PJRUB_SOLIC ON PJ_RUBRICA (
SQ_SIW_SOLICITACAO,
SQ_PROJETO_RUBRICA
);

/*==============================================================*/
/* Table: PJ_RUBRICA_CRONOGRAMA                                 */
/*==============================================================*/
CREATE TABLE PJ_RUBRICA_CRONOGRAMA (
   SQ_RUBRICA_CRONOGRAMA NUMERIC(18)          NOT NULL,
   SQ_PROJETO_RUBRICA   NUMERIC(18)          NOT NULL,
   INICIO               DATE                 NOT NULL,
   FIM                  DATE                 NOT NULL,
   VALOR_PREVISTO       NUMERIC(18,2)        NOT NULL DEFAULT 0,
   VALOR_REAL           NUMERIC(18,2)        NOT NULL DEFAULT 0,
   CONSTRAINT PK_PJ_RUBRICA_CRONOGRAMA PRIMARY KEY (SQ_RUBRICA_CRONOGRAMA)
);

COMMENT ON TABLE PJ_RUBRICA_CRONOGRAMA IS
'Registra o cronograma desembolso da rubrica.';

COMMENT ON COLUMN PJ_RUBRICA_CRONOGRAMA.SQ_RUBRICA_CRONOGRAMA IS
'Chave de PJ_RUBRICA_CRONOGRAMA.';

COMMENT ON COLUMN PJ_RUBRICA_CRONOGRAMA.SQ_PROJETO_RUBRICA IS
'Chave de PJ_PROJETO_RUBRICA. Indica a que rubrica o registro está ligado.';

COMMENT ON COLUMN PJ_RUBRICA_CRONOGRAMA.INICIO IS
'Início do período de referência do cronograma.';

COMMENT ON COLUMN PJ_RUBRICA_CRONOGRAMA.FIM IS
'Término do período de referência do cronograma.';

COMMENT ON COLUMN PJ_RUBRICA_CRONOGRAMA.VALOR_PREVISTO IS
'Valor previsto para a rubrica no período.';

COMMENT ON COLUMN PJ_RUBRICA_CRONOGRAMA.VALOR_REAL IS
'Valor executado da rubrica no período.';

/*==============================================================*/
/* Index: IN_PJRUBCRO_INICIO                                    */
/*==============================================================*/
CREATE  INDEX IN_PJRUBCRO_INICIO ON PJ_RUBRICA_CRONOGRAMA (
INICIO,
SQ_PROJETO_RUBRICA,
SQ_RUBRICA_CRONOGRAMA
);

/*==============================================================*/
/* Index: IN_PJRUBCRO_FIM                                       */
/*==============================================================*/
CREATE  INDEX IN_PJRUBCRO_FIM ON PJ_RUBRICA_CRONOGRAMA (
FIM,
SQ_PROJETO_RUBRICA,
SQ_RUBRICA_CRONOGRAMA
);

/*==============================================================*/
/* Index: IN_PJRUBCRO_RUBRICA                                   */
/*==============================================================*/
CREATE  INDEX IN_PJRUBCRO_RUBRICA ON PJ_RUBRICA_CRONOGRAMA (
SQ_PROJETO_RUBRICA,
INICIO,
FIM,
SQ_RUBRICA_CRONOGRAMA
);

/*==============================================================*/
/* Table: PT_CAMPO                                              */
/*==============================================================*/
CREATE TABLE PT_CAMPO (
   SQ_CAMPO             NUMERIC(11)          NOT NULL,
   NOME_CAMPO           VARCHAR(100)         NOT NULL,
   NOME_CAMPO_TABELA    VARCHAR(50)          NULL,
   NOME_TABELA          VARCHAR(45)          NULL,
   DAT_CADASTRO         DATE                 NULL,
   SITUACAO_CAMPO       NUMERIC(11)          NOT NULL,
   CONSTRAINT PK_PT_CAMPO PRIMARY KEY (SQ_CAMPO)
);

COMMENT ON TABLE PT_CAMPO IS
'Campos das tabelas do portal.';

COMMENT ON COLUMN PT_CAMPO.SQ_CAMPO IS
'Chave de PT_FILTRO.';

COMMENT ON COLUMN PT_CAMPO.DAT_CADASTRO IS
'Data de cadastramento do registro.';

/*==============================================================*/
/* Table: PT_CONTEUDO                                           */
/*==============================================================*/
CREATE TABLE PT_CONTEUDO (
   SQ_CONTEUDO          NUMERIC(11)          NOT NULL,
   TITULO_CONTEUDO      VARCHAR(200)         NULL,
   TEXTO_CONTEUDO       TEXT                 NULL,
   EXIBE_PO             NUMERIC(11)          NULL,
   EXIBE_BANNER         NUMERIC(11)          NULL,
   SQ_USUARIO           NUMERIC(11)          NULL,
   DATA_CRIACAO         DATE                 NULL,
   SITUACAO_CONTEUDO    VARCHAR(1)           NULL,
   CONSTRAINT PK_PT_CONTEUDO PRIMARY KEY (SQ_CONTEUDO)
);

COMMENT ON TABLE PT_CONTEUDO IS
'Conteúdo a ser exibido.';

COMMENT ON COLUMN PT_CONTEUDO.SQ_CONTEUDO IS
'Chave de PT_CONTEUDO.';

COMMENT ON COLUMN PT_CONTEUDO.TITULO_CONTEUDO IS
'Título do conteúdo.';

COMMENT ON COLUMN PT_CONTEUDO.TEXTO_CONTEUDO IS
'Texto que compõe o conteúdo.';

COMMENT ON COLUMN PT_CONTEUDO.SQ_USUARIO IS
'Chave de SG_AUTENTICACAO. Indica a que usuário o registro está ligado.';

COMMENT ON COLUMN PT_CONTEUDO.DATA_CRIACAO IS
'Data de cadastramento do registro.';

/*==============================================================*/
/* Table: PT_EIXO                                               */
/*==============================================================*/
CREATE TABLE PT_EIXO (
   SQ_EIXO              NUMERIC(11)          NOT NULL,
   NOME_EIXO            VARCHAR(100)         NOT NULL,
   SITUACAO_EIXO        NUMERIC(11)          NOT NULL,
   TIPO_EIXO            NUMERIC(11)          NOT NULL,
   DAT_CADASTRO         DATE                 NULL,
   CONSTRAINT PK_PT_EIXO PRIMARY KEY (SQ_EIXO)
);

COMMENT ON TABLE PT_EIXO IS
'Títulos dos eixos utilizados na montagem de gráficos.';

COMMENT ON COLUMN PT_EIXO.SQ_EIXO IS
'Chave de PT_EIXO.';

COMMENT ON COLUMN PT_EIXO.TIPO_EIXO IS
'Tipo do eixo: 1 e 2 - Eixo X; 3, 4, 5 e 6 - Eixo Y.';

COMMENT ON COLUMN PT_EIXO.DAT_CADASTRO IS
'Data de cadastramento do registro.';

/*==============================================================*/
/* Table: PT_EXIBICAO_CONTEUDO                                  */
/*==============================================================*/
CREATE TABLE PT_EXIBICAO_CONTEUDO (
   SQ_EXIBICAO_CONTEUDO NUMERIC(11)          NOT NULL,
   SQ_CONTEUDO          NUMERIC(11)          NOT NULL,
   SQ_MENU              NUMERIC(11)          NOT NULL,
   DATA_EXIBICAO        VARCHAR(45)          NULL,
   SITUACAO_EXIBICAO    NUMERIC(11)          NOT NULL,
   PAGINA_INICIAL       VARCHAR(1)           NOT NULL DEFAULT 'N',
   CONSTRAINT PK_PT_EXIBICAO_CONTEUDO PRIMARY KEY (SQ_EXIBICAO_CONTEUDO)
);

COMMENT ON TABLE PT_EXIBICAO_CONTEUDO IS
'Indica se o conteúdo deve ser exibido.';

COMMENT ON COLUMN PT_EXIBICAO_CONTEUDO.SQ_EXIBICAO_CONTEUDO IS
'Chave de PT_EXIBICAO_CONTEUDO.';

COMMENT ON COLUMN PT_EXIBICAO_CONTEUDO.SQ_CONTEUDO IS
'Chave de PT_CONTEUDO. Indica a que conteúdo o registro está ligado.';

COMMENT ON COLUMN PT_EXIBICAO_CONTEUDO.SQ_MENU IS
'Chave de PT_MENU. Indica a que menu o registro está ligado.';

/*==============================================================*/
/* Index: IN_PTEXICON_CONTEUDO                                  */
/*==============================================================*/
CREATE  INDEX IN_PTEXICON_CONTEUDO ON PT_EXIBICAO_CONTEUDO (
SQ_CONTEUDO
);

/*==============================================================*/
/* Index: IN_PTEXICON_MENU                                      */
/*==============================================================*/
CREATE  INDEX IN_PTEXICON_MENU ON PT_EXIBICAO_CONTEUDO (
SQ_MENU
);

/*==============================================================*/
/* Table: PT_FILTRO                                             */
/*==============================================================*/
CREATE TABLE PT_FILTRO (
   SQ_FILTRO            NUMERIC(11)          NOT NULL,
   VALOR_FILTRO         VARCHAR(45)          NULL,
   DAT_CADASTRO         VARCHAR(45)          NULL,
   SQ_OPERADOR          NUMERIC(11)          NOT NULL,
   SQ_CAMPO             NUMERIC(11)          NOT NULL,
   SQ_PESQUISA          NUMERIC(11)          NOT NULL,
   CONSTRAINT PK_PT_FILTRO PRIMARY KEY (SQ_FILTRO)
);

COMMENT ON TABLE PT_FILTRO IS
'Filtro de pesquisa no portal.';

COMMENT ON COLUMN PT_FILTRO.SQ_FILTRO IS
'Chave de PT_FILTRO.';

COMMENT ON COLUMN PT_FILTRO.VALOR_FILTRO IS
'Valor a ser aplicado ao filtro.';

COMMENT ON COLUMN PT_FILTRO.DAT_CADASTRO IS
'Data de cadastramento do registro.';

COMMENT ON COLUMN PT_FILTRO.SQ_OPERADOR IS
'Chave de PT_OPERADOR. Indica a que operador o registro está ligado.';

COMMENT ON COLUMN PT_FILTRO.SQ_CAMPO IS
'Chave de PT_FILTRO. Indica a que campo o registro está ligado.';

COMMENT ON COLUMN PT_FILTRO.SQ_PESQUISA IS
'Chave d ePT_PESQUISA. Indica a que pesquisa o registro está ligado.';

/*==============================================================*/
/* Index: IN_PTFIL_CAMPO                                        */
/*==============================================================*/
CREATE  INDEX IN_PTFIL_CAMPO ON PT_FILTRO (
SQ_CAMPO
);

/*==============================================================*/
/* Index: IN_PTFIL_OPERADOR                                     */
/*==============================================================*/
CREATE  INDEX IN_PTFIL_OPERADOR ON PT_FILTRO (
SQ_OPERADOR
);

/*==============================================================*/
/* Index: IN_PTFIL_PESQUISA                                     */
/*==============================================================*/
CREATE  INDEX IN_PTFIL_PESQUISA ON PT_FILTRO (
SQ_PESQUISA
);

/*==============================================================*/
/* Table: PT_MENU                                               */
/*==============================================================*/
CREATE TABLE PT_MENU (
   SQ_MENU              NUMERIC(11)          NOT NULL,
   NOME_MENU            VARCHAR(50)          NULL,
   MENU_SQ_MENU         NUMERIC(11)          NULL,
   SITUACAO_MENU        NUMERIC(11)          NULL,
   CONTEUDO_RESTRITO    CHAR(1)              NOT NULL DEFAULT 'N',
   CONSTRAINT PK_PT_MENU PRIMARY KEY (SQ_MENU)
);

COMMENT ON TABLE PT_MENU IS
'Opções de menu disponíveis no portal.';

COMMENT ON COLUMN PT_MENU.SQ_MENU IS
'Chave de PT_MENU. Indica a que menu o registro está ligado.';

COMMENT ON COLUMN PT_MENU.NOME_MENU IS
'Nome a ser exibido no menu.';

COMMENT ON COLUMN PT_MENU.MENU_SQ_MENU IS
'Chave de PT_MENU. Autorrelacionamento.';

COMMENT ON COLUMN PT_MENU.SITUACAO_MENU IS
'Situação do menu quanto à exibição no portal: 0 - não exibe; 1 - exibe.';

COMMENT ON COLUMN PT_MENU.CONTEUDO_RESTRITO IS
'Indica se o conteúdo só pode ser visto por gestores de conteúdo.';

/*==============================================================*/
/* Index: IN_PTMEN_PTMEN_PAI                                    */
/*==============================================================*/
CREATE  INDEX IN_PTMEN_PTMEN_PAI ON PT_MENU (
MENU_SQ_MENU
);

/*==============================================================*/
/* Table: PT_OPERADOR                                           */
/*==============================================================*/
CREATE TABLE PT_OPERADOR (
   SQ_OPERADOR          NUMERIC(11)          NOT NULL,
   NOME_OPERACAO        VARCHAR(50)          NOT NULL,
   OPERADOR_RELACIONAL  VARCHAR(5)           NOT NULL,
   SITUACAO_OPERADOR    NUMERIC(11)          NOT NULL,
   DAT_CADASTRO         DATE                 NULL,
   EXPRESSAO_RELACIONAL VARCHAR(45)          NOT NULL,
   CONSTRAINT PK_PT_OPERADOR PRIMARY KEY (SQ_OPERADOR)
);

COMMENT ON TABLE PT_OPERADOR IS
'Operador relacional disponível para montagem de filtros.';

COMMENT ON COLUMN PT_OPERADOR.SQ_OPERADOR IS
'Chave de PT_OPERADOR.';

COMMENT ON COLUMN PT_OPERADOR.DAT_CADASTRO IS
'Data de cadastramento do registro.';

/*==============================================================*/
/* Table: PT_PESQUISA                                           */
/*==============================================================*/
CREATE TABLE PT_PESQUISA (
   SQ_PESQUISA          NUMERIC(11)          NOT NULL,
   NOME_PESQUISA        VARCHAR(100)         NOT NULL,
   SITUACAO_PESQUISA    NUMERIC(11)          NOT NULL,
   SQ_PESSOA            NUMERIC(11)          NULL,
   PESQUISA_PADRAO      NUMERIC(11)          NOT NULL,
   SQ_EIXO_X            NUMERIC(11)          NOT NULL,
   SQ_EIXO_Y            NUMERIC(11)          NOT NULL,
   PESQUISA_PUBLICA     VARCHAR(1)           NOT NULL DEFAULT 'N'
      CONSTRAINT CKC_SGAUT_GESPUB CHECK (PESQUISA_PUBLICA IN ('S','N') AND PESQUISA_PUBLICA = UPPER(PESQUISA_PUBLICA)),
   CONSTRAINT PK_PT_PESQUISA PRIMARY KEY (SQ_PESQUISA)
);

COMMENT ON TABLE PT_PESQUISA IS
'Pesquisas gravadas.';

COMMENT ON COLUMN PT_PESQUISA.SQ_PESQUISA IS
'Chave d ePT_PESQUISA.';

COMMENT ON COLUMN PT_PESQUISA.SQ_PESSOA IS
'Chave de CO_PESSOA. Indica a que pessoa o registro está ligado.';

COMMENT ON COLUMN PT_PESQUISA.SQ_EIXO_X IS
'Chave de PT_EIXO. Indica a que eixo o registro está ligado.';

COMMENT ON COLUMN PT_PESQUISA.SQ_EIXO_Y IS
'Chave de PT_EIXO. Indica a que eixo o registro está ligado.';

COMMENT ON COLUMN PT_PESQUISA.PESQUISA_PUBLICA IS
'Indica se a pesquisa é de acesso público.';

/*==============================================================*/
/* Index: IN_PTPES_EIXOX                                        */
/*==============================================================*/
CREATE  INDEX IN_PTPES_EIXOX ON PT_PESQUISA (
SQ_EIXO_X
);

/*==============================================================*/
/* Index: IN_PTPES_EIXOY                                        */
/*==============================================================*/
CREATE  INDEX IN_PTPES_EIXOY ON PT_PESQUISA (
SQ_EIXO_Y
);

/*==============================================================*/
/* Table: SG_AUTENTICACAO                                       */
/*==============================================================*/
CREATE TABLE SG_AUTENTICACAO (
   SQ_PESSOA            NUMERIC(18)          NOT NULL,
   SQ_UNIDADE           NUMERIC(10)          NOT NULL,
   SQ_LOCALIZACAO       NUMERIC(10)          NOT NULL,
   CLIENTE              NUMERIC(18)          NOT NULL,
   USERNAME             VARCHAR(60)          NOT NULL,
   EMAIL                VARCHAR(60)          NOT NULL,
   SENHA                VARCHAR(255)         NOT NULL,
   ASSINATURA           VARCHAR(255)         NULL,
   GESTOR_SEGURANCA     VARCHAR(1)           NOT NULL DEFAULT 'N'
      CONSTRAINT CKC_SGAUT_GESSEG
               CHECK (GESTOR_SEGURANCA IN ('S','N') AND GESTOR_SEGURANCA = UPPER(GESTOR_SEGURANCA)),
   GESTOR_SISTEMA       VARCHAR(1)           NOT NULL DEFAULT 'N'
      CONSTRAINT CKC_SGAUT_GESSIS
               CHECK (GESTOR_SISTEMA IN ('S','N') AND GESTOR_SISTEMA = UPPER(GESTOR_SISTEMA)),
   ULTIMA_TROCA_SENHA   DATE                 NOT NULL DEFAULT 'now()',
   ULTIMA_TROCA_ASSIN   DATE                 NOT NULL DEFAULT 'now()',
   ATIVO                VARCHAR(1)           NOT NULL DEFAULT 'S'
      CONSTRAINT CKC_ATIVO_SG_AUTEN CHECK (ATIVO IN ('S','N') AND ATIVO = UPPER(ATIVO)),
   TENTATIVAS_SENHA     NUMERIC(2)           NOT NULL DEFAULT 0,
   TENTATIVAS_ASSIN     NUMERIC(2)           NOT NULL DEFAULT 0,
   TIPO_AUTENTICACAO    VARCHAR(1)           NOT NULL DEFAULT 'B',
   GESTOR_CONTEUDO      VARCHAR(1)           NOT NULL DEFAULT 'N'
      CONSTRAINT CKC_SGAUT_GESCON CHECK (GESTOR_CONTEUDO IN ('S','N') AND GESTOR_CONTEUDO = UPPER(GESTOR_CONTEUDO)),
   GESTOR_DASHBOARD     VARCHAR(1)           NOT NULL DEFAULT 'N'
      CONSTRAINT CKC_SGAUT_GESDAS CHECK (GESTOR_DASHBOARD IN ('S','N') AND GESTOR_DASHBOARD = UPPER(GESTOR_DASHBOARD)),
   GESTOR_PORTAL        VARCHAR(1)           NOT NULL DEFAULT 'N'
      CONSTRAINT CKC_SGAUT_GESPOR CHECK (GESTOR_PORTAL IN ('S','N') AND GESTOR_PORTAL = UPPER(GESTOR_PORTAL)),
   GESTOR_PESQUISA_PUBLICA VARCHAR(1)           NOT NULL DEFAULT 'N'
      CONSTRAINT CKC_SGAUT_GESPUB CHECK (GESTOR_PESQUISA_PUBLICA IN ('S','N') AND GESTOR_PESQUISA_PUBLICA = UPPER(GESTOR_PESQUISA_PUBLICA)),
   CONSTRAINT PK_SG_AUTENTICACAO PRIMARY KEY (SQ_PESSOA)
);

COMMENT ON TABLE SG_AUTENTICACAO IS
'Armazena os dados necessários para o usuário autenticar-se na aplicação.';

COMMENT ON COLUMN SG_AUTENTICACAO.SQ_PESSOA IS
'Chave de SG_AUTENTICACAO, importada de CO_PESSOA.';

COMMENT ON COLUMN SG_AUTENTICACAO.SQ_UNIDADE IS
'Chave de EO_UNIDADE. Indica a que unidade o registro está ligado.';

COMMENT ON COLUMN SG_AUTENTICACAO.SQ_LOCALIZACAO IS
'Chave de EO_LOCALIZACAO. Indica a que localização o registro está ligado.';

COMMENT ON COLUMN SG_AUTENTICACAO.CLIENTE IS
'Chave de CO_PESSOA. Indica a que cliente o registro está ligado.';

COMMENT ON COLUMN SG_AUTENTICACAO.USERNAME IS
'Nome de usuário vinculado à pessoa.';

COMMENT ON COLUMN SG_AUTENTICACAO.EMAIL IS
'e-Mail do usuário.';

COMMENT ON COLUMN SG_AUTENTICACAO.SENHA IS
'Senha do usuário.';

COMMENT ON COLUMN SG_AUTENTICACAO.ASSINATURA IS
'Assinatura eletrônica do usuário.';

COMMENT ON COLUMN SG_AUTENTICACAO.GESTOR_SEGURANCA IS
'Indica se o usuário tem acesso geral nas funcionalidades do módulo de controle.';

COMMENT ON COLUMN SG_AUTENTICACAO.GESTOR_SISTEMA IS
'Indica se o usuário tem acesso geral a funcionalidades e dados, exeto as de controle.';

COMMENT ON COLUMN SG_AUTENTICACAO.ULTIMA_TROCA_SENHA IS
'Data da última alteração da assinatura senha.';

COMMENT ON COLUMN SG_AUTENTICACAO.ULTIMA_TROCA_ASSIN IS
'Data da última alteração da assinatura eletrônica.';

COMMENT ON COLUMN SG_AUTENTICACAO.ATIVO IS
'Indica se este registro está disponível para ligação a outras tabelas.';

COMMENT ON COLUMN SG_AUTENTICACAO.TENTATIVAS_SENHA IS
'Número de vezes que a senha de acesso foi informada incorretamente';

COMMENT ON COLUMN SG_AUTENTICACAO.TENTATIVAS_ASSIN IS
'Número de vezes que a assinatura eletrônica foi informada incorretamente';

COMMENT ON COLUMN SG_AUTENTICACAO.TIPO_AUTENTICACAO IS
'Tipo da autenticação do usuário: A - MS Active Directory; O - Open LDAP; B - Banco de dados.';

COMMENT ON COLUMN SG_AUTENTICACAO.GESTOR_CONTEUDO IS
'Indica se o usuário é gestor de conteúdo do portal.';

COMMENT ON COLUMN SG_AUTENTICACAO.GESTOR_DASHBOARD IS
'Indica se o usuário é gestor de dashboard.';

COMMENT ON COLUMN SG_AUTENTICACAO.GESTOR_PORTAL IS
'Indica se o usuário tem acesso geral como gestor do portal.';

COMMENT ON COLUMN SG_AUTENTICACAO.GESTOR_PESQUISA_PUBLICA IS
'Indica se o usuário tem acesso para criar pesquisas públicas.';

/*==============================================================*/
/* Index: IN_SGAUT_USERNAME                                     */
/*==============================================================*/
CREATE  INDEX IN_SGAUT_USERNAME ON SG_AUTENTICACAO (
USERNAME
);

/*==============================================================*/
/* Index: IN_SGAUT_UNIDADE                                      */
/*==============================================================*/
CREATE  INDEX IN_SGAUT_UNIDADE ON SG_AUTENTICACAO (
SQ_UNIDADE,
SQ_PESSOA
);

/*==============================================================*/
/* Index: IN_SGAUT_LOCAL                                        */
/*==============================================================*/
CREATE  INDEX IN_SGAUT_LOCAL ON SG_AUTENTICACAO (
SQ_LOCALIZACAO,
SQ_PESSOA
);

/*==============================================================*/
/* Index: IN_SGAUT_CLIENTE                                      */
/*==============================================================*/
CREATE  INDEX IN_SGAUT_CLIENTE ON SG_AUTENTICACAO (
CLIENTE,
USERNAME
);

/*==============================================================*/
/* Index: IN_SGAUT_ATIVO                                        */
/*==============================================================*/
CREATE  INDEX IN_SGAUT_ATIVO ON SG_AUTENTICACAO (
SQ_PESSOA,
ATIVO
);

/*==============================================================*/
/* Table: SG_AUTENTICACAO_TEMP                                  */
/*==============================================================*/
CREATE TABLE SG_AUTENTICACAO_TEMP (
   CLIENTE              NUMERIC(18)          NOT NULL,
   CPF                  VARCHAR(14)          NOT NULL,
   NOME                 VARCHAR(60)          NOT NULL,
   NOME_RESUMIDO        VARCHAR(15)          NOT NULL,
   SEXO                 VARCHAR(1)           NOT NULL
      CONSTRAINT CKC_SEXO_SG_AUTEN CHECK (SEXO IN ('M','F')),
   EMAIL                VARCHAR(60)          NOT NULL,
   VINCULO              NUMERIC(1)           NOT NULL,
   UNIDADE              VARCHAR(40)          NOT NULL,
   SALA                 VARCHAR(20)          NOT NULL,
   RAMAL                VARCHAR(20)          NOT NULL,
   EFETIVAR             VARCHAR(1)           NOT NULL DEFAULT 'N'
      CONSTRAINT CKC_ATIVO_SGAUTTEM CHECK (EFETIVAR IN ('S','N') AND EFETIVAR = UPPER(EFETIVAR)),
   EFETIVADO            VARCHAR(1)           NOT NULL DEFAULT 'N'
      CONSTRAINT CKC_EFETIVADO_SG_AUTEN CHECK (EFETIVADO IN ('S','N') AND EFETIVADO = UPPER(EFETIVADO)),
   EFETIVACAO           DATE                 NULL,
   CONSTRAINT PK_SG_AUTENTICACAO_TEMP PRIMARY KEY (CLIENTE, CPF)
);

COMMENT ON TABLE SG_AUTENTICACAO_TEMP IS
'Armazena dados temporários de usuários, para posterior inserção na tabela definitiva.';

COMMENT ON COLUMN SG_AUTENTICACAO_TEMP.CLIENTE IS
'Indica o cliente a que pertence o usuário.';

COMMENT ON COLUMN SG_AUTENTICACAO_TEMP.CPF IS
'CPF do usuário, a ser convertido em username caso sua criação seja efetivada.';

COMMENT ON COLUMN SG_AUTENTICACAO_TEMP.NOME IS
'Nome do usuário.';

COMMENT ON COLUMN SG_AUTENTICACAO_TEMP.NOME_RESUMIDO IS
'Nome pelo qual a pessoa é conhecida (apelido, cognome etc.)';

COMMENT ON COLUMN SG_AUTENTICACAO_TEMP.SEXO IS
'Sexo do usuário (F) Feminino (M) Masculino.';

COMMENT ON COLUMN SG_AUTENTICACAO_TEMP.EMAIL IS
'e-Mail do usuário.';

COMMENT ON COLUMN SG_AUTENTICACAO_TEMP.VINCULO IS
'Vínculo que o usuário mantém com a organização.';

COMMENT ON COLUMN SG_AUTENTICACAO_TEMP.UNIDADE IS
'Unidade de exercício do usuário.';

COMMENT ON COLUMN SG_AUTENTICACAO_TEMP.SALA IS
'Sala (localização) do usuário.';

COMMENT ON COLUMN SG_AUTENTICACAO_TEMP.RAMAL IS
'Ramal do usuário.';

COMMENT ON COLUMN SG_AUTENTICACAO_TEMP.EFETIVAR IS
'Indica se o usuário deve ser efetivado na tabela definitiva de usuários do sistema.';

COMMENT ON COLUMN SG_AUTENTICACAO_TEMP.EFETIVADO IS
'Indica se o registro foi efetivado na base de usuários.';

COMMENT ON COLUMN SG_AUTENTICACAO_TEMP.EFETIVACAO IS
'Data da efetivação do usuário na tabela definitiva.';

/*==============================================================*/
/* Table: SG_PERFIL_MENU                                        */
/*==============================================================*/
CREATE TABLE SG_PERFIL_MENU (
   SQ_TIPO_VINCULO      NUMERIC(18)          NOT NULL,
   SQ_MENU              NUMERIC(18)          NOT NULL,
   SQ_PESSOA_ENDERECO   NUMERIC(18)          NOT NULL,
   CONSTRAINT PK_SG_PERFIL_MENU PRIMARY KEY (SQ_TIPO_VINCULO, SQ_MENU, SQ_PESSOA_ENDERECO)
);

COMMENT ON TABLE SG_PERFIL_MENU IS
'Registra as permissões de perfis às opções do menu.';

COMMENT ON COLUMN SG_PERFIL_MENU.SQ_TIPO_VINCULO IS
'Chave de CO_TIPO_VINCULO. Indica a que tipo de vínculo o registro está ligado.';

COMMENT ON COLUMN SG_PERFIL_MENU.SQ_MENU IS
'Chave de SIW_MENU_ENDERECO.';

COMMENT ON COLUMN SG_PERFIL_MENU.SQ_PESSOA_ENDERECO IS
'Chave de CO_PESSOA_ENDERECO. Indica a que endereço o registro está ligado.';

/*==============================================================*/
/* Index: IN_SGPERMEN_SQMENU                                    */
/*==============================================================*/
CREATE  INDEX IN_SGPERMEN_SQMENU ON SG_PERFIL_MENU (
SQ_MENU,
SQ_PESSOA_ENDERECO,
SQ_TIPO_VINCULO
);

/*==============================================================*/
/* Index: IN_SGPERMENU_SQEND                                    */
/*==============================================================*/
CREATE  INDEX IN_SGPERMENU_SQEND ON SG_PERFIL_MENU (
SQ_PESSOA_ENDERECO,
SQ_MENU,
SQ_TIPO_VINCULO
);

/*==============================================================*/
/* Table: SG_PESSOA_MAIL                                        */
/*==============================================================*/
CREATE TABLE SG_PESSOA_MAIL (
   SQ_PESSOA_MAIL       NUMERIC(18)          NOT NULL,
   SQ_PESSOA            NUMERIC(18)          NOT NULL,
   SQ_MENU              NUMERIC(18)          NOT NULL,
   ALERTA_DIARIO        VARCHAR(1)           NOT NULL DEFAULT 'S'
      CONSTRAINT CKC_ALERTA_DIARIO_SG_PESSO CHECK (ALERTA_DIARIO IN ('S','N') AND ALERTA_DIARIO = UPPER(ALERTA_DIARIO)),
   TRAMITACAO           VARCHAR(1)           NOT NULL DEFAULT 'S'
      CONSTRAINT CKC_TRAMITACAO_SG_PESSO CHECK (TRAMITACAO IN ('S','N') AND TRAMITACAO = UPPER(TRAMITACAO)),
   CONCLUSAO            VARCHAR(1)           NOT NULL DEFAULT 'S'
      CONSTRAINT CKC_CONCLUSAO_SG_PESSO CHECK (CONCLUSAO IN ('S','N') AND CONCLUSAO = UPPER(CONCLUSAO)),
   RESPONSABILIDADE     VARCHAR(1)           NOT NULL DEFAULT 'S'
      CONSTRAINT CKC_RESPONSABILIDADE_SG_PESSO CHECK (RESPONSABILIDADE IN ('S','N') AND RESPONSABILIDADE = UPPER(RESPONSABILIDADE)),
   CONSTRAINT PK_SG_PESSOA_MAIL PRIMARY KEY (SQ_PESSOA_MAIL)
);

COMMENT ON TABLE SG_PESSOA_MAIL IS
'Registra as configurações de envio de e-mail para o usuário.';

COMMENT ON COLUMN SG_PESSOA_MAIL.SQ_PESSOA_MAIL IS
'Chave de SG_PESSOA_MAIL.';

COMMENT ON COLUMN SG_PESSOA_MAIL.SQ_PESSOA IS
'Chave de SG_AUTENTICACAO. Indica a que usuário o registro está ligado.';

COMMENT ON COLUMN SG_PESSOA_MAIL.SQ_MENU IS
'Chave de SIW_MENU. Indica a que serviço o registro está ligado.';

COMMENT ON COLUMN SG_PESSOA_MAIL.ALERTA_DIARIO IS
'Indica se o usuário deve receber e-mail de alerta diário para o serviço indicado.';

COMMENT ON COLUMN SG_PESSOA_MAIL.TRAMITACAO IS
'Indica se o usuário deve receber e-mail de tramitação para o serviço indicado.';

COMMENT ON COLUMN SG_PESSOA_MAIL.CONCLUSAO IS
'Indica se o usuário deve receber e-mail de conclusão  para o serviço indicado.';

COMMENT ON COLUMN SG_PESSOA_MAIL.RESPONSABILIDADE IS
'Indica se o usuário deve receber e-mail comunicando responsabilidade para o serviço indicado. No caso de projetos, refere-se à responsabilidade por etapa.';

/*==============================================================*/
/* Index: IN_SGPESMAI_PESSOA                                    */
/*==============================================================*/
CREATE  INDEX IN_SGPESMAI_PESSOA ON SG_PESSOA_MAIL (
SQ_PESSOA,
SQ_PESSOA_MAIL
);

/*==============================================================*/
/* Index: IN_SGPESMAI_MENU                                      */
/*==============================================================*/
CREATE  INDEX IN_SGPESMAI_MENU ON SG_PESSOA_MAIL (
SQ_MENU,
SQ_PESSOA_MAIL
);

/*==============================================================*/
/* Table: SG_PESSOA_MENU                                        */
/*==============================================================*/
CREATE TABLE SG_PESSOA_MENU (
   SQ_PESSOA            NUMERIC(18)          NOT NULL,
   SQ_MENU              NUMERIC(18)          NOT NULL,
   SQ_PESSOA_ENDERECO   NUMERIC(18)          NOT NULL,
   CONSTRAINT PK_SG_PESSOA_MENU PRIMARY KEY (SQ_PESSOA, SQ_MENU, SQ_PESSOA_ENDERECO)
);

COMMENT ON TABLE SG_PESSOA_MENU IS
'Permissões que a pessoa têm às opções do menu';

COMMENT ON COLUMN SG_PESSOA_MENU.SQ_PESSOA IS
'Chave de CO_PESSOA. Indica a que pessoa o registro está ligado.';

COMMENT ON COLUMN SG_PESSOA_MENU.SQ_MENU IS
'Chave de SIW_MENU. Indica a que opção do menu o registro está ligado.';

COMMENT ON COLUMN SG_PESSOA_MENU.SQ_PESSOA_ENDERECO IS
'Chave de CO_PESSOA_ENDERECO. Indica a que endereço o registro está ligado.';

/*==============================================================*/
/* Index: IN_SGPESMEN_SQMEN                                     */
/*==============================================================*/
CREATE  INDEX IN_SGPESMEN_SQMEN ON SG_PESSOA_MENU (
SQ_MENU,
SQ_PESSOA
);

/*==============================================================*/
/* Index: IN_SGPESMEN_END                                       */
/*==============================================================*/
CREATE  INDEX IN_SGPESMEN_END ON SG_PESSOA_MENU (
SQ_PESSOA_ENDERECO,
SQ_MENU
);

/*==============================================================*/
/* Table: SG_PESSOA_MODULO                                      */
/*==============================================================*/
CREATE TABLE SG_PESSOA_MODULO (
   SQ_PESSOA            NUMERIC(18)          NOT NULL,
   CLIENTE              NUMERIC(18)          NOT NULL,
   SQ_MODULO            NUMERIC(18)          NOT NULL,
   SQ_PESSOA_ENDERECO   NUMERIC(18)          NOT NULL,
   CONSTRAINT PK_SG_PESSOA_MODULO PRIMARY KEY (SQ_PESSOA, CLIENTE, SQ_MODULO, SQ_PESSOA_ENDERECO)
);

COMMENT ON TABLE SG_PESSOA_MODULO IS
'Registra os gestores de módulo, por endereço da organização.';

COMMENT ON COLUMN SG_PESSOA_MODULO.SQ_PESSOA IS
'Chave de CO_PESSOA. Indica a que pessoa o registro está ligado.';

COMMENT ON COLUMN SG_PESSOA_MODULO.CLIENTE IS
'Cliente. Chave de SIW_CLIENTE_MODULO.';

COMMENT ON COLUMN SG_PESSOA_MODULO.SQ_MODULO IS
'Chave de SIW_MODULO. Indica a que módulo o registro está ligado.';

COMMENT ON COLUMN SG_PESSOA_MODULO.SQ_PESSOA_ENDERECO IS
'Chave de CO_PESSOA_ENDERECO. Indica a que endereço o registro está ligado.';

/*==============================================================*/
/* Index: IN_SGPESMOD_END                                       */
/*==============================================================*/
CREATE  INDEX IN_SGPESMOD_END ON SG_PESSOA_MODULO (
CLIENTE,
SQ_PESSOA_ENDERECO,
SQ_MODULO,
SQ_PESSOA
);

/*==============================================================*/
/* Index: IN_SGPESMOD_MODULO                                    */
/*==============================================================*/
CREATE  INDEX IN_SGPESMOD_MODULO ON SG_PESSOA_MODULO (
CLIENTE,
SQ_MODULO,
SQ_PESSOA_ENDERECO,
SQ_PESSOA
);

/*==============================================================*/
/* Index: IN_SGPESMOD_CLI                                       */
/*==============================================================*/
CREATE  INDEX IN_SGPESMOD_CLI ON SG_PESSOA_MODULO (
CLIENTE,
SQ_PESSOA,
SQ_MODULO,
SQ_PESSOA_ENDERECO
);

/*==============================================================*/
/* Table: SG_PESSOA_UNIDADE                                     */
/*==============================================================*/
CREATE TABLE SG_PESSOA_UNIDADE (
   SQ_PESSOA            NUMERIC(18)          NULL,
   SQ_UNIDADE           NUMERIC(10)          NULL
);

COMMENT ON TABLE SG_PESSOA_UNIDADE IS
'Registra as unidades que o usuário tem acesso. ';

COMMENT ON COLUMN SG_PESSOA_UNIDADE.SQ_PESSOA IS
'Chave de SG_AUTENTICACAO, importada de CO_PESSOA.';

COMMENT ON COLUMN SG_PESSOA_UNIDADE.SQ_UNIDADE IS
'Chave de EO_UNIDADE. Indica a que unidade o registro está ligado.';

/*==============================================================*/
/* Index: IN_SGPESUNI_INVERSA                                   */
/*==============================================================*/
CREATE  INDEX IN_SGPESUNI_INVERSA ON SG_PESSOA_UNIDADE (
SQ_UNIDADE,
SQ_PESSOA
);

/*==============================================================*/
/* Table: SG_TRAMITE_PESSOA                                     */
/*==============================================================*/
CREATE TABLE SG_TRAMITE_PESSOA (
   SQ_PESSOA            NUMERIC(18)          NOT NULL,
   SQ_SIW_TRAMITE       NUMERIC(18)          NOT NULL,
   SQ_PESSOA_ENDERECO   NUMERIC(18)          NOT NULL,
   CONSTRAINT PK_SG_TRAMITE_PESSOA PRIMARY KEY (SQ_PESSOA, SQ_SIW_TRAMITE, SQ_PESSOA_ENDERECO)
);

COMMENT ON TABLE SG_TRAMITE_PESSOA IS
'Permissões da pessoa a um trâmite de serviço';

COMMENT ON COLUMN SG_TRAMITE_PESSOA.SQ_PESSOA IS
'Chave de CO_PESSOA. Indica a que pessoa o registro está ligado.';

COMMENT ON COLUMN SG_TRAMITE_PESSOA.SQ_SIW_TRAMITE IS
'Chave de SIW_TRAMITE. Indica a que trâmite o registro está ligado.';

COMMENT ON COLUMN SG_TRAMITE_PESSOA.SQ_PESSOA_ENDERECO IS
'Chave de CO_PESSOA_ENDERECO. Indica a que endereço o registro está ligado.';

/*==============================================================*/
/* Index: IN_SGTRAPES_END                                       */
/*==============================================================*/
CREATE  INDEX IN_SGTRAPES_END ON SG_TRAMITE_PESSOA (
SQ_PESSOA_ENDERECO,
SQ_PESSOA,
SQ_SIW_TRAMITE
);

/*==============================================================*/
/* Table: SIW_ARQUIVO                                           */
/*==============================================================*/
CREATE TABLE SIW_ARQUIVO (
   SQ_SIW_ARQUIVO       NUMERIC(18)          NOT NULL,
   CLIENTE              NUMERIC(18)          NOT NULL,
   SQ_TIPO_ARQUIVO      NUMERIC(18)          NULL,
   NOME                 VARCHAR(255)         NOT NULL,
   DESCRICAO            VARCHAR(1000)        NULL,
   INCLUSAO             DATE                 NOT NULL DEFAULT 'now()',
   TAMANHO              NUMERIC(18)          NOT NULL DEFAULT 0,
   TIPO                 VARCHAR(100)         NULL,
   CAMINHO              VARCHAR(255)         NULL,
   NOME_ORIGINAL        VARCHAR(255)         NOT NULL,
   CONSTRAINT PK_SIW_ARQUIVO PRIMARY KEY (SQ_SIW_ARQUIVO)
);

COMMENT ON TABLE SIW_ARQUIVO IS
'Registra o link para os arquivos físicos recebidos por upload.';

COMMENT ON COLUMN SIW_ARQUIVO.SQ_SIW_ARQUIVO IS
'Chave de SIW_ARQUIVO. Indica a que arquivo o registro está ligado.';

COMMENT ON COLUMN SIW_ARQUIVO.CLIENTE IS
'Chave de CO_PESSOA. Indica a que cliente o registro está ligado.';

COMMENT ON COLUMN SIW_ARQUIVO.SQ_TIPO_ARQUIVO IS
'Chave de SIW_TIPO_ARQUIVO. Indica a que tipo de arquivo o registro está ligado.';

COMMENT ON COLUMN SIW_ARQUIVO.NOME IS
'Nome original do arquivo.';

COMMENT ON COLUMN SIW_ARQUIVO.DESCRICAO IS
'Descrição do conteúdo do arquivo.';

COMMENT ON COLUMN SIW_ARQUIVO.INCLUSAO IS
'Data da inclusão do arquivo.';

COMMENT ON COLUMN SIW_ARQUIVO.TAMANHO IS
'Tamanho do arquivo em bytes.';

COMMENT ON COLUMN SIW_ARQUIVO.TIPO IS
'Tipo do arquivo, a ser usado na visualização.';

COMMENT ON COLUMN SIW_ARQUIVO.CAMINHO IS
'Caminho físico do arquivo.';

COMMENT ON COLUMN SIW_ARQUIVO.NOME_ORIGINAL IS
'Nome original do arquivo.';

/*==============================================================*/
/* Index: IN_SIWARQ_CLIENTE                                     */
/*==============================================================*/
CREATE  INDEX IN_SIWARQ_CLIENTE ON SIW_ARQUIVO (
CLIENTE,
SQ_SIW_ARQUIVO
);

/*==============================================================*/
/* Table: SIW_CLIENTE                                           */
/*==============================================================*/
CREATE TABLE SIW_CLIENTE (
   SQ_PESSOA            NUMERIC(18)          NOT NULL,
   SQ_CIDADE_PADRAO     NUMERIC(18)          NOT NULL,
   SQ_AGENCIA_PADRAO    NUMERIC(18)          NULL,
   ATIVACAO             DATE                 NOT NULL,
   BLOQUEIO             DATE                 NULL,
   DESATIVACAO          DATE                 NULL,
   TIPO_AUTENTICACAO    NUMERIC(1)           NOT NULL
      CONSTRAINT CKC_SIWCLI_TPAUT
               CHECK (TIPO_AUTENTICACAO IN (1,2)),
   SMTP_SERVER          VARCHAR(60)          NULL,
   SIW_EMAIL_NOME       VARCHAR(60)          NULL,
   SIW_EMAIL_CONTA      VARCHAR(60)          NULL,
   SIW_EMAIL_SENHA      VARCHAR(60)          NULL,
   LOGO                 VARCHAR(60)          NULL,
   LOGO1                VARCHAR(60)          NULL,
   TAMANHO_MIN_SENHA    NUMERIC(2)           NOT NULL DEFAULT 6,
   TAMANHO_MAX_SENHA    NUMERIC(2)           NOT NULL DEFAULT 15,
   DIAS_VIG_SENHA       NUMERIC(3)           NOT NULL DEFAULT 90,
   DIAS_AVISO_EXPIR     NUMERIC(3)           NOT NULL DEFAULT 10,
   MAXIMO_TENTATIVAS    NUMERIC(2)           NOT NULL DEFAULT 4,
   FUNDO                VARCHAR(60)          NULL,
   UPLOAD_MAXIMO        NUMERIC(18)          NOT NULL DEFAULT 0,
   ENVIA_MAIL_TRAMITE   VARCHAR(1)           NOT NULL DEFAULT 'S'
      CONSTRAINT CKC_ENVIA_MAIL_TRAMIT_SIW_CLIE CHECK (ENVIA_MAIL_TRAMITE IN ('S','N') AND ENVIA_MAIL_TRAMITE = UPPER(ENVIA_MAIL_TRAMITE)),
   ENVIA_MAIL_ALERTA    VARCHAR(1)           NOT NULL DEFAULT 'S'
      CONSTRAINT CKC_ENVIA_MAIL_ALERTA_SIW_CLIE CHECK (ENVIA_MAIL_ALERTA IN ('S','N') AND ENVIA_MAIL_ALERTA = UPPER(ENVIA_MAIL_ALERTA)),
   GEOREFERENCIA        VARCHAR(1)           NOT NULL DEFAULT 'N'
      CONSTRAINT CKC_GEOREFERENCIA_SIW_CLIE CHECK (GEOREFERENCIA IN ('S','N') AND GEOREFERENCIA = UPPER(GEOREFERENCIA)),
   GOOGLEMAPS_KEY       VARCHAR(2000)        NULL,
   ATA_REGISTRO_PRECO   VARCHAR(1)           NOT NULL DEFAULT 'S'
      CONSTRAINT CKC_ATA_REGISTRO_PREC_SIW_CLIE CHECK (ATA_REGISTRO_PRECO IN ('S','N') AND ATA_REGISTRO_PRECO = UPPER(ATA_REGISTRO_PRECO)),
   AD_ACCOUNT_SUFIX     VARCHAR(40)          NULL,
   AD_BASE_DN           VARCHAR(40)          NULL,
   AD_DOMAIN_CONTROLERS VARCHAR(40)          NULL,
   OL_ACCOUNT_SUFIX     VARCHAR(40)          NULL,
   OL_BASE_DN           VARCHAR(40)          NULL,
   OL_DOMAIN_CONTROLERS VARCHAR(40)          NULL,
   SYSLOG_SERVER_NAME   VARCHAR(30)          NULL,
   SYSLOG_SERVER_PROTOCOL VARCHAR(10)          NULL,
   SYSLOG_SERVER_PORT   NUMERIC(5)           NULL,
   SYSLOG_FACILITY      NUMERIC(2)           NULL,
   SYSLOG_FQDN          VARCHAR(30)          NULL,
   SYSLOG_TIMEOUT       NUMERIC(2)           NULL,
   SYSLOG_LEVEL_PASS_OK NUMERIC(2)           NULL,
   SYSLOG_LEVEL_PASS_ER NUMERIC(2)           NULL,
   SYSLOG_LEVEL_SIGN_ER NUMERIC(2)           NULL,
   SYSLOG_LEVEL_WRITE_OK NUMERIC(2)           NULL,
   SYSLOG_LEVEL_WRITE_ER NUMERIC(2)           NULL,
   SYSLOG_LEVEL_RES_ER  NUMERIC(2)           NULL,
   CONSTRAINT PK_SIW_CLIENTE PRIMARY KEY (SQ_PESSOA)
);

COMMENT ON TABLE SIW_CLIENTE IS
'Armazena os clientes do SIW';

COMMENT ON COLUMN SIW_CLIENTE.SQ_PESSOA IS
'Chave de CO_PESSOA. Indica a que pessoa o registro está ligado.';

COMMENT ON COLUMN SIW_CLIENTE.SQ_CIDADE_PADRAO IS
'Chave de CO_CIDADE. Indica a que cidade padrão do cliente, a ser usada nas telas onde a cidade é solicitada.';

COMMENT ON COLUMN SIW_CLIENTE.SQ_AGENCIA_PADRAO IS
'Chave de CO_AGENCIA. Indica a agência padrao do cliente, a ser sugerida nas telas onde a agência é solicitada.';

COMMENT ON COLUMN SIW_CLIENTE.ATIVACAO IS
'Data de ativação do cliente no SIW';

COMMENT ON COLUMN SIW_CLIENTE.BLOQUEIO IS
'Data de bloqueio do cliente no SIW';

COMMENT ON COLUMN SIW_CLIENTE.DESATIVACAO IS
'Data de desativação do cliente no SIW.';

COMMENT ON COLUMN SIW_CLIENTE.TIPO_AUTENTICACAO IS
'Armazena o tipo de autenticação desejado pelo cliente.';

COMMENT ON COLUMN SIW_CLIENTE.SMTP_SERVER IS
'Endereço eletrônico do servidor SMTP.';

COMMENT ON COLUMN SIW_CLIENTE.SIW_EMAIL_NOME IS
'Nome a ser usado como sender de uma mensagem automática de e-mail.';

COMMENT ON COLUMN SIW_CLIENTE.SIW_EMAIL_CONTA IS
'Conta de e-mail para conexão ao servidor SMTP.';

COMMENT ON COLUMN SIW_CLIENTE.SIW_EMAIL_SENHA IS
'Senha da conta de e-mail para conexão ao servidor SMTP.';

COMMENT ON COLUMN SIW_CLIENTE.LOGO IS
'Nome do arquivo a ser usado como logotipo em relatórios.';

COMMENT ON COLUMN SIW_CLIENTE.LOGO1 IS
'Nome do arquivo a ser usado como logotipo no menu.';

COMMENT ON COLUMN SIW_CLIENTE.TAMANHO_MIN_SENHA IS
'Tamanho mínimo aceito pelo sistema para a senha de acesso e assinatura eletrônica';

COMMENT ON COLUMN SIW_CLIENTE.TAMANHO_MAX_SENHA IS
'Tamanho máximo aceito pelo sistema para a senha de acesso e assinatura eletrônica';

COMMENT ON COLUMN SIW_CLIENTE.DIAS_VIG_SENHA IS
'Dias de vigência da senha de acesso/assinatura eletrônica antes que o sistema bloqueie automaticamente';

COMMENT ON COLUMN SIW_CLIENTE.DIAS_AVISO_EXPIR IS
'Dias antes da expiração da senha de acesso/assinatura eletrônica que o sistema avisará o usuário';

COMMENT ON COLUMN SIW_CLIENTE.MAXIMO_TENTATIVAS IS
'Número de tentativas inválidas de uso da senha ou assinatura antes do sistema bloquear o acesso.';

COMMENT ON COLUMN SIW_CLIENTE.FUNDO IS
'Nome do arquivo que contém a imagem de fundo do menu.';

COMMENT ON COLUMN SIW_CLIENTE.UPLOAD_MAXIMO IS
'Tamanho máximo, em bytes, que um upload pode aceitar.';

COMMENT ON COLUMN SIW_CLIENTE.ENVIA_MAIL_TRAMITE IS
'Indica se devem ser encaminhados e-mail de alerta quando houver tramitação ou conclusão das solicitações do cliente.';

COMMENT ON COLUMN SIW_CLIENTE.ENVIA_MAIL_ALERTA IS
'Indica se devem ser encaminhados e-mail de alerta de proximidade das solicitações do cliente.';

COMMENT ON COLUMN SIW_CLIENTE.GEOREFERENCIA IS
'Indica se o cliente tem acesso às funcionalidades de geo-referenciamento.';

COMMENT ON COLUMN SIW_CLIENTE.GOOGLEMAPS_KEY IS
'Chave de acesso ao Web Service do Google Maps.';

COMMENT ON COLUMN SIW_CLIENTE.ATA_REGISTRO_PRECO IS
'Se o cliente tiver módulo de licitações, indica se há controle de ARP.';

COMMENT ON COLUMN SIW_CLIENTE.AD_ACCOUNT_SUFIX IS
'Sufixo das contas de usuário para autenticação no microsoft active directory.';

COMMENT ON COLUMN SIW_CLIENTE.AD_BASE_DN IS
'Nome base do domínio para autenticação no microsoft active directory.';

COMMENT ON COLUMN SIW_CLIENTE.AD_DOMAIN_CONTROLERS IS
'Lista de controladores active directory, separados por vírgula, sem espaços.';

COMMENT ON COLUMN SIW_CLIENTE.OL_ACCOUNT_SUFIX IS
'Sufixo das contas de usuário para autenticação no Open LDAP.';

COMMENT ON COLUMN SIW_CLIENTE.OL_BASE_DN IS
'Nome base do domínio para autenticação no Open LDAP.';

COMMENT ON COLUMN SIW_CLIENTE.OL_DOMAIN_CONTROLERS IS
'Lista de controladores Open LDAP, separados por vírgula, sem espaços.';

COMMENT ON COLUMN SIW_CLIENTE.SYSLOG_SERVER_NAME IS
'Endereço do servidor syslog. IP ou nome.';

COMMENT ON COLUMN SIW_CLIENTE.SYSLOG_SERVER_PROTOCOL IS
'Protocolo do servidor syslog. Default UDP.';

COMMENT ON COLUMN SIW_CLIENTE.SYSLOG_SERVER_PORT IS
'Porta do servidor syslog. Default 514.';

COMMENT ON COLUMN SIW_CLIENTE.SYSLOG_FACILITY IS
'Categoria do evento.';

COMMENT ON COLUMN SIW_CLIENTE.SYSLOG_FQDN IS
'Nome base do domínio para syslog.';

COMMENT ON COLUMN SIW_CLIENTE.SYSLOG_TIMEOUT IS
'Tempo limite para a conexão (segundos).';

COMMENT ON COLUMN SIW_CLIENTE.SYSLOG_LEVEL_PASS_OK IS
'Nível de erro para login correto.';

COMMENT ON COLUMN SIW_CLIENTE.SYSLOG_LEVEL_PASS_ER IS
'Nível de erro para login incorreto.';

COMMENT ON COLUMN SIW_CLIENTE.SYSLOG_LEVEL_SIGN_ER IS
'Nível de erro para assinatura eletrônica incorreta.';

COMMENT ON COLUMN SIW_CLIENTE.SYSLOG_LEVEL_WRITE_OK IS
'Nível de erro para insert, update e delete correto.';

COMMENT ON COLUMN SIW_CLIENTE.SYSLOG_LEVEL_WRITE_ER IS
'Nível de erro para insert, update e delete incorreto.';

COMMENT ON COLUMN SIW_CLIENTE.SYSLOG_LEVEL_RES_ER IS
'Nível de erro para problema no acesso a recursos (banco de dados, servidor de e-mail etc).';

/*==============================================================*/
/* Index: IN_SIWCLI_CIDPD                                       */
/*==============================================================*/
CREATE  INDEX IN_SIWCLI_CIDPD ON SIW_CLIENTE (
SQ_CIDADE_PADRAO
);

/*==============================================================*/
/* Index: IN_SIWCLI_AGEPD                                       */
/*==============================================================*/
CREATE  INDEX IN_SIWCLI_AGEPD ON SIW_CLIENTE (
SQ_AGENCIA_PADRAO
);

/*==============================================================*/
/* Index: IN_SIWCLI_ATIVACAO                                    */
/*==============================================================*/
CREATE  INDEX IN_SIWCLI_ATIVACAO ON SIW_CLIENTE (
ATIVACAO
);

/*==============================================================*/
/* Index: IN_SIWCLI_BLOQUEIO                                    */
/*==============================================================*/
CREATE  INDEX IN_SIWCLI_BLOQUEIO ON SIW_CLIENTE (
BLOQUEIO
);

/*==============================================================*/
/* Index: IN_SIWCLI_DESAT                                       */
/*==============================================================*/
CREATE  INDEX IN_SIWCLI_DESAT ON SIW_CLIENTE (
DESATIVACAO
);

/*==============================================================*/
/* Index: IN_SIWCLI_AUTENT                                      */
/*==============================================================*/
CREATE  INDEX IN_SIWCLI_AUTENT ON SIW_CLIENTE (
TIPO_AUTENTICACAO
);

/*==============================================================*/
/* Table: SIW_CLIENTE_MODULO                                    */
/*==============================================================*/
CREATE TABLE SIW_CLIENTE_MODULO (
   SQ_PESSOA            NUMERIC(18)          NOT NULL,
   SQ_MODULO            NUMERIC(18)          NOT NULL,
   CONSTRAINT PK_SIW_CLIENTE_MODULO PRIMARY KEY (SQ_PESSOA, SQ_MODULO)
);

COMMENT ON TABLE SIW_CLIENTE_MODULO IS
'Armazena os módulos contratados pelos clientes';

COMMENT ON COLUMN SIW_CLIENTE_MODULO.SQ_PESSOA IS
'Chave de CO_PESSOA. Indica a que pessoa o registro está ligado.';

COMMENT ON COLUMN SIW_CLIENTE_MODULO.SQ_MODULO IS
'Chave de SIW_MODULO. Indica a que módulo o registro está ligado.';

/*==============================================================*/
/* Index: IN_SIWCLIMOD_MOD                                      */
/*==============================================================*/
CREATE  INDEX IN_SIWCLIMOD_MOD ON SIW_CLIENTE_MODULO (
SQ_MODULO,
SQ_PESSOA
);

/*==============================================================*/
/* Table: SIW_COORDENADA                                        */
/*==============================================================*/
CREATE TABLE SIW_COORDENADA (
   SQ_SIW_COORDENADA    NUMERIC(18)          NOT NULL,
   CLIENTE              NUMERIC(18)          NOT NULL,
   NOME                 VARCHAR(100)         NOT NULL,
   LATITUDE             NUMERIC(18,10)       NOT NULL,
   LONGITUDE            NUMERIC(18,10)       NOT NULL,
   ICONE                VARCHAR(30)          NOT NULL,
   TIPO                 VARCHAR(30)          NOT NULL,
   CONSTRAINT PK_SIW_COORDENADA PRIMARY KEY (SQ_SIW_COORDENADA)
);

COMMENT ON TABLE SIW_COORDENADA IS
'Registras as coordenadas geográficas de um ponto.';

COMMENT ON COLUMN SIW_COORDENADA.SQ_SIW_COORDENADA IS
'Chave de SIW_COORDENADA.';

COMMENT ON COLUMN SIW_COORDENADA.CLIENTE IS
'Chave de CO_PESSOA. Indica a que cliente o registro está ligado.';

COMMENT ON COLUMN SIW_COORDENADA.NOME IS
'Nome para exibição da coordenada no mapa.';

COMMENT ON COLUMN SIW_COORDENADA.LATITUDE IS
'Latitude do ponto.';

COMMENT ON COLUMN SIW_COORDENADA.LONGITUDE IS
'Longitude do ponto.';

COMMENT ON COLUMN SIW_COORDENADA.ICONE IS
'Icone a ser exibido.';

COMMENT ON COLUMN SIW_COORDENADA.TIPO IS
'Indica a que objeto a coordenada está ligada. ENDERECO, PROJETO, ETAPA...';

/*==============================================================*/
/* Index: IN_SIWCOO_CLIENTE                                     */
/*==============================================================*/
CREATE  INDEX IN_SIWCOO_CLIENTE ON SIW_COORDENADA (
CLIENTE,
SQ_SIW_COORDENADA
);

/*==============================================================*/
/* Table: SIW_COORDENADA_ENDERECO                               */
/*==============================================================*/
CREATE TABLE SIW_COORDENADA_ENDERECO (
   SQ_SIW_COORDENADA    NUMERIC(18)          NOT NULL,
   SQ_PESSOA_ENDERECO   NUMERIC(18)          NOT NULL,
   CONSTRAINT PK_SIW_COORDENADA_ENDERECO PRIMARY KEY (SQ_SIW_COORDENADA, SQ_PESSOA_ENDERECO)
);

COMMENT ON TABLE SIW_COORDENADA_ENDERECO IS
'Vincula uma coordenada geográfica a um endereço físico.';

COMMENT ON COLUMN SIW_COORDENADA_ENDERECO.SQ_SIW_COORDENADA IS
'Chave de SIW_COORDENADA. Indica a que coordenada o registro está ligado.';

COMMENT ON COLUMN SIW_COORDENADA_ENDERECO.SQ_PESSOA_ENDERECO IS
'Chave de CO_PESSOA_ENDERECO. Indica a que endereço o registro está ligado.';

/*==============================================================*/
/* Index: IN_SIWCOOEND_INV                                      */
/*==============================================================*/
CREATE  INDEX IN_SIWCOOEND_INV ON SIW_COORDENADA_ENDERECO (
SQ_PESSOA_ENDERECO,
SQ_SIW_COORDENADA
);

/*==============================================================*/
/* Table: SIW_COORDENADA_SOLICITACAO                            */
/*==============================================================*/
CREATE TABLE SIW_COORDENADA_SOLICITACAO (
   SQ_SIW_COORDENADA    NUMERIC(18)          NOT NULL,
   SQ_SIW_SOLICITACAO   NUMERIC(18)          NOT NULL,
   CONSTRAINT PK_SIW_COORDENADA_SOLICITACAO PRIMARY KEY (SQ_SIW_COORDENADA, SQ_SIW_SOLICITACAO)
);

COMMENT ON TABLE SIW_COORDENADA_SOLICITACAO IS
'Vincula coordenadas geográficas a uma solicitação.';

COMMENT ON COLUMN SIW_COORDENADA_SOLICITACAO.SQ_SIW_COORDENADA IS
'Chave de SIW_COORDENADA. Indica a que coordenada o registro está ligado.';

COMMENT ON COLUMN SIW_COORDENADA_SOLICITACAO.SQ_SIW_SOLICITACAO IS
'Chave de SIW_SOLICITACAO. Indica a que solicitação o registro está ligado.';

/*==============================================================*/
/* Index: IN_SIWCOOSOL_INVERSA                                  */
/*==============================================================*/
CREATE  INDEX IN_SIWCOOSOL_INVERSA ON SIW_COORDENADA_SOLICITACAO (
SQ_SIW_SOLICITACAO,
SQ_SIW_COORDENADA
);

/*==============================================================*/
/* Table: SIW_ETAPA_INTERESSADO                                 */
/*==============================================================*/
CREATE TABLE SIW_ETAPA_INTERESSADO (
   SQ_UNIDADE           NUMERIC(10)          NOT NULL,
   SQ_PROJETO_ETAPA     NUMERIC(18)          NOT NULL,
   CONSTRAINT PK_SIW_ETAPA_INTERESSADO PRIMARY KEY (SQ_UNIDADE, SQ_PROJETO_ETAPA)
);

COMMENT ON TABLE SIW_ETAPA_INTERESSADO IS
'Registra as vinculações entre partes interessadas e pacotes de trabalho de um projeto.';

COMMENT ON COLUMN SIW_ETAPA_INTERESSADO.SQ_UNIDADE IS
'Chave de EO_UNIDADE. Indica a que unidade o registro está ligado.';

COMMENT ON COLUMN SIW_ETAPA_INTERESSADO.SQ_PROJETO_ETAPA IS
'Chave de PJ_PROJETO_ETAPA. Indica a que etapa do projeto o registro está ligado.';

/*==============================================================*/
/* Index: IN_SIWETAINT_INV                                      */
/*==============================================================*/
CREATE  INDEX IN_SIWETAINT_INV ON SIW_ETAPA_INTERESSADO (
SQ_PROJETO_ETAPA,
SQ_UNIDADE
);

/*==============================================================*/
/* Table: SIW_MAIL                                              */
/*==============================================================*/
CREATE TABLE SIW_MAIL (
   SQ_MAIL              NUMERIC(18)          NOT NULL,
   CLIENTE              NUMERIC(18)          NOT NULL,
   REMETENTE            NUMERIC(18)          NOT NULL,
   ASSUNTO              VARCHAR(200)         NOT NULL,
   TEXTO                VARCHAR(2000)        NOT NULL,
   INCLUSAO             DATE                 NOT NULL DEFAULT 'now()',
   ENVIO                DATE                 NULL,
   ENVIADA              VARCHAR(1)           NOT NULL DEFAULT 'N'
      CONSTRAINT CKC_ENVIADA_SIW_MAIL CHECK (ENVIADA IN ('S','N') AND ENVIADA = UPPER(ENVIADA)),
   MAIL_REMETENTE       VARCHAR(100)         NOT NULL,
   CONSTRAINT PK_SIW_MAIL PRIMARY KEY (SQ_MAIL)
);

COMMENT ON TABLE SIW_MAIL IS
'Registra os e-mails enviados através do mecanismo de mensagens.';

COMMENT ON COLUMN SIW_MAIL.SQ_MAIL IS
'Chave de SIW_MAIL.';

COMMENT ON COLUMN SIW_MAIL.CLIENTE IS
'Chave de CO_PESSOA. Indica a que cliente o registro está ligado.';

COMMENT ON COLUMN SIW_MAIL.REMETENTE IS
'Chave de CO_PESSOA. Indica o remetente da mensagem.';

COMMENT ON COLUMN SIW_MAIL.ASSUNTO IS
'Assunto da mensagem.';

COMMENT ON COLUMN SIW_MAIL.TEXTO IS
'Texto da mensagem.';

COMMENT ON COLUMN SIW_MAIL.INCLUSAO IS
'Data da inclusão do registro. Alimentada automaticamente.';

COMMENT ON COLUMN SIW_MAIL.ENVIO IS
'Data do envio da mensagem. Alimentada automaticamente.';

COMMENT ON COLUMN SIW_MAIL.ENVIADA IS
'Indica se a mensagem já foi enviada.';

COMMENT ON COLUMN SIW_MAIL.MAIL_REMETENTE IS
'Registra o e-mail do remetente utilizado para envio, evitando sua perda caso o usuário o altere.';

/*==============================================================*/
/* Index: IN_SIWMAI_CLIENTE                                     */
/*==============================================================*/
CREATE  INDEX IN_SIWMAI_CLIENTE ON SIW_MAIL (
CLIENTE,
SQ_MAIL
);

/*==============================================================*/
/* Index: IN_SIWMAI_REMETE                                      */
/*==============================================================*/
CREATE  INDEX IN_SIWMAI_REMETE ON SIW_MAIL (
REMETENTE,
SQ_MAIL
);

/*==============================================================*/
/* Index: IN_SIWMAI_ENVIADA                                     */
/*==============================================================*/
CREATE  INDEX IN_SIWMAI_ENVIADA ON SIW_MAIL (
CLIENTE,
ENVIADA,
SQ_MAIL
);

/*==============================================================*/
/* Table: SIW_MAIL_ANEXO                                        */
/*==============================================================*/
CREATE TABLE SIW_MAIL_ANEXO (
   SQ_MAIL_ANEXO        NUMERIC(18)          NOT NULL,
   SQ_MAIL              NUMERIC(18)          NOT NULL,
   SQ_SIW_ARQUIVO       NUMERIC(18)          NOT NULL,
   CONSTRAINT PK_SIW_MAIL_ANEXO PRIMARY KEY (SQ_MAIL_ANEXO)
);

COMMENT ON TABLE SIW_MAIL_ANEXO IS
'Registra os arquivos anexos à mensagem.';

COMMENT ON COLUMN SIW_MAIL_ANEXO.SQ_MAIL_ANEXO IS
'Chave de SIW_MAIL_ANEXO.';

COMMENT ON COLUMN SIW_MAIL_ANEXO.SQ_MAIL IS
'Chave de SIW_MAIL. Indica a que mensagem o anexo está ligado.';

COMMENT ON COLUMN SIW_MAIL_ANEXO.SQ_SIW_ARQUIVO IS
'Chave de SIW_ARQUIVO. Indica a que arquivo o registro está ligado.';

/*==============================================================*/
/* Index: IN_SIWMAIANE_MAIL                                     */
/*==============================================================*/
CREATE  INDEX IN_SIWMAIANE_MAIL ON SIW_MAIL_ANEXO (
SQ_MAIL,
SQ_MAIL_ANEXO
);

/*==============================================================*/
/* Index: IN_SIWMAIANE_ARQUIVO                                  */
/*==============================================================*/
CREATE  INDEX IN_SIWMAIANE_ARQUIVO ON SIW_MAIL_ANEXO (
SQ_SIW_ARQUIVO,
SQ_MAIL,
SQ_MAIL_ANEXO
);

/*==============================================================*/
/* Table: SIW_MAIL_DESTINATARIO                                 */
/*==============================================================*/
CREATE TABLE SIW_MAIL_DESTINATARIO (
   SQ_MAIL_DESTINATARIO NUMERIC(18)          NOT NULL,
   SQ_MAIL              NUMERIC(18)          NOT NULL,
   DESTINATARIO_PESSOA  NUMERIC(18)          NULL,
   DESTINATARIO_UNIDADE NUMERIC(10)          NULL,
   EMAIL_DESTINATARIO   VARCHAR(100)         NOT NULL,
   NOME_DESTINATARIO    VARCHAR(40)          NOT NULL,
   CONSTRAINT PK_SIW_MAIL_DESTINATARIO PRIMARY KEY (SQ_MAIL_DESTINATARIO)
);

COMMENT ON TABLE SIW_MAIL_DESTINATARIO IS
'Registra os destinatários de uma mensagem de e-mail. A tabela preve o envio para pessoas e unidades, registradas ou não das tabelas internas.';

COMMENT ON COLUMN SIW_MAIL_DESTINATARIO.SQ_MAIL_DESTINATARIO IS
'Chave de SQ_MAIL_DESTINATARIO.';

COMMENT ON COLUMN SIW_MAIL_DESTINATARIO.SQ_MAIL IS
'Chave de SIW_MAIL. Indica a que mensagem o registro está ligado.';

COMMENT ON COLUMN SIW_MAIL_DESTINATARIO.DESTINATARIO_PESSOA IS
'Chave de CO_PESSOA. Indica a que pessoa o registro está ligado. Este campo é preenchido somente quando o destinatário existe naquela tabela.';

COMMENT ON COLUMN SIW_MAIL_DESTINATARIO.DESTINATARIO_UNIDADE IS
'Chave de EO_UNIDADE. Indica a que unidade o registro está ligado. Este campo é preenchido somente quando o destinatário existe naquela tabela.';

COMMENT ON COLUMN SIW_MAIL_DESTINATARIO.EMAIL_DESTINATARIO IS
'Registra o e-mail do destinatario utilizado para envio, evitando sua perda caso o usuário o altere.';

COMMENT ON COLUMN SIW_MAIL_DESTINATARIO.NOME_DESTINATARIO IS
'Registra o nome do destinatário utilizado para envio, evitando sua perda caso o usuário o altere.';

/*==============================================================*/
/* Index: IN_SIWMAIDES_MAIL                                     */
/*==============================================================*/
CREATE  INDEX IN_SIWMAIDES_MAIL ON SIW_MAIL_DESTINATARIO (
SQ_MAIL,
SQ_MAIL_DESTINATARIO
);

/*==============================================================*/
/* Index: IN_SIWMAIDES_PESSOA                                   */
/*==============================================================*/
CREATE  INDEX IN_SIWMAIDES_PESSOA ON SIW_MAIL_DESTINATARIO (
DESTINATARIO_PESSOA,
SQ_MAIL_DESTINATARIO
);

/*==============================================================*/
/* Index: IN_SIWMAIDES_UNIDADE                                  */
/*==============================================================*/
CREATE  INDEX IN_SIWMAIDES_UNIDADE ON SIW_MAIL_DESTINATARIO (
DESTINATARIO_UNIDADE,
SQ_MAIL_DESTINATARIO
);

/*==============================================================*/
/* Table: SIW_MENU                                              */
/*==============================================================*/
CREATE TABLE SIW_MENU (
   SQ_MENU              NUMERIC(18)          NOT NULL,
   SQ_MODULO            NUMERIC(18)          NOT NULL,
   SQ_PESSOA            NUMERIC(18)          NOT NULL,
   SQ_MENU_PAI          NUMERIC(18)          NULL,
   NOME                 VARCHAR(40)          NOT NULL,
   FINALIDADE           VARCHAR(200)         NOT NULL DEFAULT 'A ser inserido.',
   LINK                 VARCHAR(60)          NULL,
   SQ_UNID_EXECUTORA    NUMERIC(10)          NULL,
   TRAMITE              VARCHAR(1)           NOT NULL DEFAULT 'N'
      CONSTRAINT CKC_SIWMEN_TRAM
               CHECK (TRAMITE IN ('S','N') AND TRAMITE = UPPER(TRAMITE)),
   ORDEM                NUMERIC(4)           NOT NULL,
   ULTIMO_NIVEL         VARCHAR(1)           NOT NULL DEFAULT 'N'
      CONSTRAINT CKC_SIWMEN_ULT
               CHECK (ULTIMO_NIVEL IN ('S','N') AND ULTIMO_NIVEL = UPPER(ULTIMO_NIVEL)),
   P1                   NUMERIC(18)          NULL,
   P2                   NUMERIC(18)          NULL,
   P3                   NUMERIC(18)          NULL,
   P4                   NUMERIC(18)          NULL,
   SIGLA                VARCHAR(10)          NULL,
   IMAGEM               VARCHAR(60)          NULL,
   ACESSO_GERAL         VARCHAR(1)           NOT NULL DEFAULT 'N'
      CONSTRAINT CKC_SIWMEN_ACGER
               CHECK (ACESSO_GERAL IN ('S','N') AND ACESSO_GERAL = UPPER(ACESSO_GERAL)),
   DESCENTRALIZADO      VARCHAR(1)           NOT NULL DEFAULT 'S'
      CONSTRAINT CKC_SIWMEN_DESC
               CHECK (DESCENTRALIZADO IN ('S','N') AND DESCENTRALIZADO = UPPER(DESCENTRALIZADO)),
   EXTERNO              VARCHAR(1)           NOT NULL DEFAULT 'N'
      CONSTRAINT CKC_EXTERNO_SIWMEN CHECK (EXTERNO IN ('S','N') AND EXTERNO = UPPER(EXTERNO)),
   TARGET               VARCHAR(15)          NULL,
   EMITE_OS             VARCHAR(1)           NULL
      CONSTRAINT CKC_SIWMEN_OS
               CHECK (EMITE_OS IS NULL OR (EMITE_OS IN ('S','N') AND EMITE_OS = UPPER(EMITE_OS))),
   CONSULTA_OPINIAO     VARCHAR(1)           NULL
      CONSTRAINT CKC_SIWMEN_OPI
               CHECK (CONSULTA_OPINIAO IS NULL OR (CONSULTA_OPINIAO IN ('S','N') AND CONSULTA_OPINIAO = UPPER(CONSULTA_OPINIAO))),
   ENVIA_EMAIL          VARCHAR(1)           NULL
      CONSTRAINT CKC_SIWMEN_MAIL
               CHECK (ENVIA_EMAIL IS NULL OR (ENVIA_EMAIL IN ('S','N') AND ENVIA_EMAIL = UPPER(ENVIA_EMAIL))),
   EXIBE_RELATORIO      VARCHAR(1)           NULL
      CONSTRAINT CKC_SIWMEN_REL
               CHECK (EXIBE_RELATORIO IS NULL OR (EXIBE_RELATORIO IN ('S','N') AND EXIBE_RELATORIO = UPPER(EXIBE_RELATORIO))),
   COMO_FUNCIONA        VARCHAR(1000)        NULL,
   VINCULACAO           VARCHAR(1)           NULL
      CONSTRAINT CKC_SIWMEN_VIN
               CHECK (VINCULACAO IS NULL OR (VINCULACAO IN ('P','U') AND VINCULACAO = UPPER(VINCULACAO))),
   DESTINATARIO         VARCHAR(1)           NOT NULL DEFAULT 'S'
      CONSTRAINT CKC_DESTINATARIO_SIW_MENU CHECK (DESTINATARIO IN ('S','N') AND DESTINATARIO = UPPER(DESTINATARIO)),
   DATA_HORA            VARCHAR(1)           NULL
      CONSTRAINT CKC_DATA_HORA_SIW_MENU CHECK (DATA_HORA IS NULL OR (DATA_HORA = UPPER(DATA_HORA))),
   ENVIA_DIA_UTIL       VARCHAR(1)           NULL
      CONSTRAINT CKC_SIWMEN_UTIL
               CHECK (ENVIA_DIA_UTIL IS NULL OR (ENVIA_DIA_UTIL IN ('S','N') AND ENVIA_DIA_UTIL = UPPER(ENVIA_DIA_UTIL))),
   DESCRICAO            VARCHAR(1)           NULL
      CONSTRAINT CKC_SIWMEN_DESCR
               CHECK (DESCRICAO IS NULL OR (DESCRICAO IN ('S','N') AND DESCRICAO = UPPER(DESCRICAO))),
   JUSTIFICATIVA        VARCHAR(1)           NULL
      CONSTRAINT CKC_SIWMEN_JUST
               CHECK (JUSTIFICATIVA IS NULL OR (JUSTIFICATIVA IN ('S','N') AND JUSTIFICATIVA = UPPER(JUSTIFICATIVA))),
   ATIVO                VARCHAR(1)           NOT NULL DEFAULT 'S'
      CONSTRAINT CKC_ATIVO_SIW_MENU CHECK (ATIVO IN ('S','N') AND ATIVO = UPPER(ATIVO)),
   CONTROLA_ANO         VARCHAR(1)           NOT NULL DEFAULT 'N'
      CONSTRAINT CKC_CONTROLA_ANO_SIW_MENU CHECK (CONTROLA_ANO IN ('S','N') AND CONTROLA_ANO = UPPER(CONTROLA_ANO)),
   LIBERA_EDICAO        VARCHAR(1)           NOT NULL DEFAULT 'S'
      CONSTRAINT CKC_LIBERA_EDICAO_SIW_MENU CHECK (LIBERA_EDICAO IN ('S','N') AND LIBERA_EDICAO = UPPER(LIBERA_EDICAO)),
   NUMERACAO_AUTOMATICA NUMERIC(1)           NOT NULL DEFAULT 0,
   SERVICO_NUMERADOR    NUMERIC(18)          NULL,
   SQ_ARQUIVO_PROCED    NUMERIC(18)          NULL,
   SEQUENCIAL           NUMERIC(18)          NULL,
   ANO_CORRENTE         NUMERIC(4)           NULL,
   PREFIXO              VARCHAR(10)          NULL,
   SUFIXO               VARCHAR(10)          NULL,
   ENVIO_INCLUSAO       VARCHAR(1)           NOT NULL DEFAULT 'N'
      CONSTRAINT CKC_ENVIO_INCLUSAO_SIW_MENU CHECK (ENVIO_INCLUSAO IN ('S','N') AND ENVIO_INCLUSAO = UPPER(ENVIO_INCLUSAO)),
   CONSULTA_GERAL       VARCHAR(1)           NOT NULL DEFAULT 'N'
      CONSTRAINT CKC_CONSULTA_GERAL_SIW_MENU CHECK (CONSULTA_GERAL IN ('S','N') AND CONSULTA_GERAL = UPPER(CONSULTA_GERAL)),
   CANCELA_SEM_TRAMITE  VARCHAR(1)           NOT NULL DEFAULT 'S'
      CONSTRAINT CKC_CANCELA_SEM_TRAMI_SIW_MENU CHECK (CANCELA_SEM_TRAMITE IN ('S','N') AND CANCELA_SEM_TRAMITE = UPPER(CANCELA_SEM_TRAMITE)),
   CONSTRAINT PK_SIW_MENU PRIMARY KEY (SQ_MENU)
);

COMMENT ON TABLE SIW_MENU IS
'Armazena as opções padrão do menu para um segmento';

COMMENT ON COLUMN SIW_MENU.SQ_MENU IS
'Chave de SIW_MENU.';

COMMENT ON COLUMN SIW_MENU.SQ_MODULO IS
'Chave de SIW_MODULO. Indica a que módulo o registro está ligado.';

COMMENT ON COLUMN SIW_MENU.SQ_PESSOA IS
'Chave de CO_PESSOA. Indica a que cliente o registro está ligado.';

COMMENT ON COLUMN SIW_MENU.SQ_MENU_PAI IS
'Chave de SIW_MENU. Auto-relacionamento da tabela.';

COMMENT ON COLUMN SIW_MENU.NOME IS
'Informa o texto a ser apresentado no menu.';

COMMENT ON COLUMN SIW_MENU.FINALIDADE IS
'Informa a finalidade da opção.';

COMMENT ON COLUMN SIW_MENU.LINK IS
'Informa o link a ser chamado quando a opção for clicada.';

COMMENT ON COLUMN SIW_MENU.SQ_UNID_EXECUTORA IS
'Chave de EO_UNIDADE. Unidade responsável pela execução do serviço.';

COMMENT ON COLUMN SIW_MENU.TRAMITE IS
'Indica se a opção deve ter controle de trâmites (work-flow).';

COMMENT ON COLUMN SIW_MENU.ORDEM IS
'Informa a ordem em que a opção deve ser apresentada, em relação a outras opções de mesma subordinação.';

COMMENT ON COLUMN SIW_MENU.ULTIMO_NIVEL IS
'Indica se a opção deve ser apresentada num sub-menu (S) ou na montagem do menu principal (N)';

COMMENT ON COLUMN SIW_MENU.P1 IS
'Parâmetro de uso geral pela aplicação.';

COMMENT ON COLUMN SIW_MENU.P2 IS
'Parâmetro de uso geral pela aplicação.';

COMMENT ON COLUMN SIW_MENU.P3 IS
'Parâmetro de uso geral pela aplicação.';

COMMENT ON COLUMN SIW_MENU.P4 IS
'Parâmetro de uso geral pela aplicaço.';

COMMENT ON COLUMN SIW_MENU.SIGLA IS
'Informa a sigla da opção, usada para controle interno da aplicação.';

COMMENT ON COLUMN SIW_MENU.IMAGEM IS
'Informa qual ícone deve ser colocado ao lado da opção. Se for nulo, a imagem será a padrão.';

COMMENT ON COLUMN SIW_MENU.ACESSO_GERAL IS
'Indica que a opção deve ser acessada por todos os usuários.';

COMMENT ON COLUMN SIW_MENU.DESCENTRALIZADO IS
'Indica se a opção deve ser controlada por endereço.';

COMMENT ON COLUMN SIW_MENU.EXTERNO IS
'Indica se o link da opção aponta para um endereço externo ao sistema.';

COMMENT ON COLUMN SIW_MENU.TARGET IS
'Se preenchido, informa o nome da janela a ser aberta quando a opção for clicada.';

COMMENT ON COLUMN SIW_MENU.EMITE_OS IS
'Indica se o serviço terá emissão de ordem de serviço';

COMMENT ON COLUMN SIW_MENU.CONSULTA_OPINIAO IS
'Indica se o serviço deverá consultar a opinião do solicitante quanto ao atendimento';

COMMENT ON COLUMN SIW_MENU.ENVIA_EMAIL IS
'Indica se deve ser enviado e-mail para o solicitante a cada trâmite';

COMMENT ON COLUMN SIW_MENU.EXIBE_RELATORIO IS
'Indica se o serviço deve ser exibido no relatório gerencial';

COMMENT ON COLUMN SIW_MENU.COMO_FUNCIONA IS
'Texto de apresentação do serviço, inclusive com as regras de negócio a serem respeitadas.';

COMMENT ON COLUMN SIW_MENU.VINCULACAO IS
'Este campo determina se a solicitação do serviço é vinculada ao beneficiário ou à unidade solicitante. Se for ao beneficiário, outras pessoas da unidade, que não sejam titular ou substituto, não poderão vê-la. Além disso, se o beneficiário for para outra unidade, a solicitação deve ser vista pelos novos chefes. Se for à unidade, todos as pessoas da unidade poderão consultar a solicitação, mesmo que não sejam chefes. Mesmo que o solicitante vá para outra unidade, a solicitação é consultada pela unidade que cadastrou a solicitação.';

COMMENT ON COLUMN SIW_MENU.DESTINATARIO IS
'Se igual a S, sempre pedirá destinatário quando um encaminhamento for feito. Caso contrário, aparecerá na mesa de trabalho das pessoas que puderem cumprir o trâmite.';

COMMENT ON COLUMN SIW_MENU.DATA_HORA IS
'Indica como o sistema deve tratar a questão de horas. (0) Não pede data; (1) Pede apenas uma data; (2) Pede apenas uma data/hora; (3) Pede data início e fim; (4) Pede data/hora início e fim.';

COMMENT ON COLUMN SIW_MENU.ENVIA_DIA_UTIL IS
'Indica se a solicitação só pode ser atendida em dia útil.';

COMMENT ON COLUMN SIW_MENU.DESCRICAO IS
'Indica se deve ser informada uma descrição na solicitação';

COMMENT ON COLUMN SIW_MENU.JUSTIFICATIVA IS
'Indica se deve ser informada uma justificativa na solicitação';

COMMENT ON COLUMN SIW_MENU.ATIVO IS
'Indica se o registro deve ou não ser exibido.';

COMMENT ON COLUMN SIW_MENU.CONTROLA_ANO IS
'Indica se a opção do menu deve ter seu controle por ano.';

COMMENT ON COLUMN SIW_MENU.LIBERA_EDICAO IS
'Indica se pode haver inclusão, alteração ou exclusão dos registros.';

COMMENT ON COLUMN SIW_MENU.NUMERACAO_AUTOMATICA IS
'Indica se o serviço tem numeração automática para suas solicitações: 0 - não tem; 1 - tem numeração própria; 2 - usa numeraçãode outro serviço.';

COMMENT ON COLUMN SIW_MENU.SERVICO_NUMERADOR IS
'Chave de SIW_MENU apontando para o serviços que fornecerá o número da solicitação.';

COMMENT ON COLUMN SIW_MENU.SQ_ARQUIVO_PROCED IS
'Chave de SIW_ARQUIVO. Indica o arquivo que detalha o procedimento operacional do serviço.';

COMMENT ON COLUMN SIW_MENU.SEQUENCIAL IS
'Armazena o último número utilizado para numeração automática.';

COMMENT ON COLUMN SIW_MENU.ANO_CORRENTE IS
'Ano no qual o sequencial está sendo incrementado.';

COMMENT ON COLUMN SIW_MENU.PREFIXO IS
'Prefixo do código das solicitações.';

COMMENT ON COLUMN SIW_MENU.SUFIXO IS
'Sufixo dos códigos das solicitações.';

COMMENT ON COLUMN SIW_MENU.ENVIO_INCLUSAO IS
'Indica se o serviço permitirá o envio da solicitação juntamente com a inclusão.';

COMMENT ON COLUMN SIW_MENU.CONSULTA_GERAL IS
'Indica que os registros desta opção podem ser acessados por todos os usuários.';

COMMENT ON COLUMN SIW_MENU.CANCELA_SEM_TRAMITE IS
'Indica se solicitação sem trâmite deve ser cancelada. S - cancela; N - Exclui';

/*==============================================================*/
/* Index: IN_SIWMENU_SIGLA                                      */
/*==============================================================*/
CREATE  INDEX IN_SIWMENU_SIGLA ON SIW_MENU (
SIGLA,
SQ_PESSOA
);

/*==============================================================*/
/* Index: IN_SIWMENU_ULT                                        */
/*==============================================================*/
CREATE UNIQUE INDEX IN_SIWMENU_ULT ON SIW_MENU (
ULTIMO_NIVEL,
SQ_MENU
);

/*==============================================================*/
/* Index: IN_SIWMENU_ATIVO                                      */
/*==============================================================*/
CREATE UNIQUE INDEX IN_SIWMENU_ATIVO ON SIW_MENU (
ATIVO,
SQ_MENU
);

/*==============================================================*/
/* Index: IN_SIWMENU_PAI                                        */
/*==============================================================*/
CREATE  INDEX IN_SIWMENU_PAI ON SIW_MENU (
SQ_MENU_PAI,
SQ_MENU
);

/*==============================================================*/
/* Table: SIW_MENU_ARQUIVO                                      */
/*==============================================================*/
CREATE TABLE SIW_MENU_ARQUIVO (
   SQ_MENU              NUMERIC(18)          NOT NULL,
   SQ_SIW_ARQUIVO       NUMERIC(18)          NOT NULL,
   CONSTRAINT PK_SIW_MENU_ARQUIVO PRIMARY KEY (SQ_MENU, SQ_SIW_ARQUIVO)
);

COMMENT ON TABLE SIW_MENU_ARQUIVO IS
'Vincula um arquivo a opções de menu.';

COMMENT ON COLUMN SIW_MENU_ARQUIVO.SQ_MENU IS
'Chave de SIW_MENU. Indica a que opção do menu o registro está ligado.';

COMMENT ON COLUMN SIW_MENU_ARQUIVO.SQ_SIW_ARQUIVO IS
'Chave de SIW_ARQUIVO. Indica a que arquivo o registro está ligado.';

/*==============================================================*/
/* Index: IN_SIWMENARQ_INVERSA                                  */
/*==============================================================*/
CREATE  INDEX IN_SIWMENARQ_INVERSA ON SIW_MENU_ARQUIVO (
SQ_SIW_ARQUIVO,
SQ_MENU
);

/*==============================================================*/
/* Table: SIW_MENU_ENDERECO                                     */
/*==============================================================*/
CREATE TABLE SIW_MENU_ENDERECO (
   SQ_MENU              NUMERIC(18)          NOT NULL,
   SQ_PESSOA_ENDERECO   NUMERIC(18)          NOT NULL,
   CONSTRAINT PK_SIW_MENU_ENDERECO PRIMARY KEY (SQ_MENU, SQ_PESSOA_ENDERECO)
);

COMMENT ON TABLE SIW_MENU_ENDERECO IS
'Endereços do cliente onde a opção está disponível';

COMMENT ON COLUMN SIW_MENU_ENDERECO.SQ_MENU IS
'Chave de SIW_MENU. Indica a que opção do menu o registro está ligado.';

COMMENT ON COLUMN SIW_MENU_ENDERECO.SQ_PESSOA_ENDERECO IS
'Chave de CO_PESSOA_ENDERECO. Indica a que endereço o registro está ligado.';

/*==============================================================*/
/* Index: IN_SIWMENEND_INV                                      */
/*==============================================================*/
CREATE  INDEX IN_SIWMENEND_INV ON SIW_MENU_ENDERECO (
SQ_PESSOA_ENDERECO,
SQ_MENU
);

/*==============================================================*/
/* Table: SIW_MENU_RELAC                                        */
/*==============================================================*/
CREATE TABLE SIW_MENU_RELAC (
   SERVICO_CLIENTE      NUMERIC(18)          NOT NULL,
   SERVICO_FORNECEDOR   NUMERIC(18)          NOT NULL,
   SQ_SIW_TRAMITE       NUMERIC(18)          NOT NULL,
   CONSTRAINT PK_SIW_MENU_RELAC PRIMARY KEY (SERVICO_CLIENTE, SERVICO_FORNECEDOR, SQ_SIW_TRAMITE)
);

COMMENT ON TABLE SIW_MENU_RELAC IS
'Vincula serviços definindo os trâmites em que a vinculação pode ser feita.';

COMMENT ON COLUMN SIW_MENU_RELAC.SERVICO_CLIENTE IS
'Chave de SIW_MENU apontando para o serviço que será vinculado a outro, nas fases indicadas.';

COMMENT ON COLUMN SIW_MENU_RELAC.SERVICO_FORNECEDOR IS
'Chave de SIW_MENU apontando para o serviço ao qual solicitações de outro serviço serão vinculadas.';

COMMENT ON COLUMN SIW_MENU_RELAC.SQ_SIW_TRAMITE IS
'Chave de SIW_TRAMITE, indicando as fases das solicitações do serviço fornecedor nas quais poderão ser vinculadas solicitações do serviço cliente.';

/*==============================================================*/
/* Index: IN_SIWMENREL_INV                                      */
/*==============================================================*/
CREATE  INDEX IN_SIWMENREL_INV ON SIW_MENU_RELAC (
SERVICO_FORNECEDOR,
SERVICO_CLIENTE,
SQ_SIW_TRAMITE
);

/*==============================================================*/
/* Table: SIW_META_ARQUIVO                                      */
/*==============================================================*/
CREATE TABLE SIW_META_ARQUIVO (
   SQ_SOLIC_META        NUMERIC(18)          NOT NULL,
   SQ_SIW_ARQUIVO       NUMERIC(18)          NOT NULL,
   ORDEM                NUMERIC(4)           NOT NULL,
   CONSTRAINT PK_SIW_META_ARQUIVO PRIMARY KEY (SQ_SOLIC_META, SQ_SIW_ARQUIVO)
);

COMMENT ON TABLE SIW_META_ARQUIVO IS
'Registra os arquivos ligados a metas.';

COMMENT ON COLUMN SIW_META_ARQUIVO.SQ_SOLIC_META IS
'Chave de SIW_SOLIC_META. Indica a que meta o registro está ligado.';

COMMENT ON COLUMN SIW_META_ARQUIVO.SQ_SIW_ARQUIVO IS
'Chave de SIW_ARQUIVO. Indica a que meta o registro está ligado.';

COMMENT ON COLUMN SIW_META_ARQUIVO.ORDEM IS
'Número de ordem do arquivo para exibição em listagens.';

/*==============================================================*/
/* Index: IN_SIWMETARQ_INV                                      */
/*==============================================================*/
CREATE  INDEX IN_SIWMETARQ_INV ON SIW_META_ARQUIVO (
SQ_SIW_ARQUIVO,
SQ_SOLIC_META
);

/*==============================================================*/
/* Table: SIW_META_CRONOGRAMA                                   */
/*==============================================================*/
CREATE TABLE SIW_META_CRONOGRAMA (
   SQ_META_CRONOGRAMA   NUMERIC(18)          NOT NULL,
   SQ_SOLIC_META        NUMERIC(18)          NOT NULL,
   SQ_PESSOA_ATUALIZACAO NUMERIC(18)          NULL,
   INICIO               DATE                 NOT NULL,
   FIM                  DATE                 NOT NULL,
   VALOR_PREVISTO       NUMERIC(18,4)        NOT NULL,
   VALOR_REAL           NUMERIC(18,4)        NULL,
   ULTIMA_ATUALIZACAO   DATE                 NULL,
   CONSTRAINT PK_SIW_META_CRONOGRAMA PRIMARY KEY (SQ_META_CRONOGRAMA)
);

COMMENT ON TABLE SIW_META_CRONOGRAMA IS
'Registra o cronograma de realização de metas.';

COMMENT ON COLUMN SIW_META_CRONOGRAMA.SQ_META_CRONOGRAMA IS
'Chave de SIW_META_CRONOGRAMA.';

COMMENT ON COLUMN SIW_META_CRONOGRAMA.SQ_SOLIC_META IS
'Chave de SIW_SOLIC_META. Indica a que meta o registro está ligado.';

COMMENT ON COLUMN SIW_META_CRONOGRAMA.SQ_PESSOA_ATUALIZACAO IS
'Chave de CO_PESSOA. Indica a pessoa que informou o valor real para o período.';

COMMENT ON COLUMN SIW_META_CRONOGRAMA.INICIO IS
'Início do período de aferição.';

COMMENT ON COLUMN SIW_META_CRONOGRAMA.FIM IS
'Término do período de aferição.';

COMMENT ON COLUMN SIW_META_CRONOGRAMA.VALOR_PREVISTO IS
'Valor previsto para o período.';

COMMENT ON COLUMN SIW_META_CRONOGRAMA.VALOR_REAL IS
'Valor aferido no período.';

COMMENT ON COLUMN SIW_META_CRONOGRAMA.ULTIMA_ATUALIZACAO IS
'Data da última atualização do valor real.';

/*==============================================================*/
/* Index: IN_SIWMETCRO_META                                     */
/*==============================================================*/
CREATE  INDEX IN_SIWMETCRO_META ON SIW_META_CRONOGRAMA (
SQ_SOLIC_META,
SQ_META_CRONOGRAMA
);

/*==============================================================*/
/* Index: IN_SIWMETCRO_INICIO                                   */
/*==============================================================*/
CREATE  INDEX IN_SIWMETCRO_INICIO ON SIW_META_CRONOGRAMA (
INICIO,
SQ_META_CRONOGRAMA
);

/*==============================================================*/
/* Index: IN_SIWMETCRO_FIM                                      */
/*==============================================================*/
CREATE  INDEX IN_SIWMETCRO_FIM ON SIW_META_CRONOGRAMA (
FIM,
SQ_META_CRONOGRAMA
);

/*==============================================================*/
/* Table: SIW_MODULO                                            */
/*==============================================================*/
CREATE TABLE SIW_MODULO (
   SQ_MODULO            NUMERIC(18)          NOT NULL,
   NOME                 VARCHAR(60)          NOT NULL,
   SIGLA                VARCHAR(3)           NOT NULL,
   OBJETIVO_GERAL       VARCHAR(4000)        NULL,
   ORDEM                NUMERIC(4)           NOT NULL DEFAULT 0,
   CONSTRAINT PK_SIW_MODULO PRIMARY KEY (SQ_MODULO)
);

COMMENT ON TABLE SIW_MODULO IS
'Armazena os módulos componentes do SIW';

COMMENT ON COLUMN SIW_MODULO.SQ_MODULO IS
'Chave de SIW_MODULO.';

COMMENT ON COLUMN SIW_MODULO.NOME IS
'Nome do módulo.';

COMMENT ON COLUMN SIW_MODULO.SIGLA IS
'Sigla do módulo.';

COMMENT ON COLUMN SIW_MODULO.OBJETIVO_GERAL IS
'Objetivo geral do módulo, independentemente do segmento que atende.';

COMMENT ON COLUMN SIW_MODULO.ORDEM IS
'Indica a ordem do módulo nas listagens.';

/*==============================================================*/
/* Index: IN_SIWMOD_NOME                                        */
/*==============================================================*/
CREATE UNIQUE INDEX IN_SIWMOD_NOME ON SIW_MODULO (
NOME
);

/*==============================================================*/
/* Index: IN_SIWMOD_SIGLA                                       */
/*==============================================================*/
CREATE UNIQUE INDEX IN_SIWMOD_SIGLA ON SIW_MODULO (
SIGLA
);

/*==============================================================*/
/* Table: SIW_MOD_SEG                                           */
/*==============================================================*/
CREATE TABLE SIW_MOD_SEG (
   SQ_MODULO            NUMERIC(18)          NOT NULL,
   SQ_SEGMENTO          NUMERIC(18)          NOT NULL,
   ATIVO                VARCHAR(1)           NOT NULL DEFAULT 'S'
      CONSTRAINT CKC_ATIVO_SIW_MOD_ CHECK (ATIVO IN ('S','N') AND ATIVO = UPPER(ATIVO)),
   COMERCIALIZAR        VARCHAR(1)           NOT NULL DEFAULT 'S'
      CONSTRAINT CKC_SIWMODSE_COM
               CHECK (COMERCIALIZAR IN ('S','N') AND COMERCIALIZAR = UPPER(COMERCIALIZAR)),
   OBJETIVO_ESPECIF     VARCHAR(4000)        NULL,
   CONSTRAINT PK_SIW_MOD_SEG PRIMARY KEY (SQ_MODULO, SQ_SEGMENTO)
);

COMMENT ON TABLE SIW_MOD_SEG IS
'Armazena informações do módulo do SIW para um segmento específico';

COMMENT ON COLUMN SIW_MOD_SEG.SQ_MODULO IS
'Chave de SIW_MODULO. Indica a que módulo o registro está ligado.';

COMMENT ON COLUMN SIW_MOD_SEG.SQ_SEGMENTO IS
'Chave de CO_SEGMENTO. Indica a que segmento o registro está ligado.';

COMMENT ON COLUMN SIW_MOD_SEG.ATIVO IS
'Indica se o registro deve ou não ser exibido.';

COMMENT ON COLUMN SIW_MOD_SEG.COMERCIALIZAR IS
'Indica se o módulo pode ser comercializado.';

COMMENT ON COLUMN SIW_MOD_SEG.OBJETIVO_ESPECIF IS
'Objetivos do módulo para o segmento ao qual está ligado.';

/*==============================================================*/
/* Index: IN_SIWMODSEG_SEG                                      */
/*==============================================================*/
CREATE  INDEX IN_SIWMODSEG_SEG ON SIW_MOD_SEG (
SQ_SEGMENTO,
SQ_MODULO
);

/*==============================================================*/
/* Table: SIW_RESTRICAO                                         */
/*==============================================================*/
CREATE TABLE SIW_RESTRICAO (
   SQ_SIW_RESTRICAO     NUMERIC(18)          NOT NULL,
   SQ_SIW_SOLICITACAO   NUMERIC(18)          NOT NULL,
   SQ_PESSOA            NUMERIC(18)          NOT NULL,
   SQ_PESSOA_ATUALIZACAO NUMERIC(18)          NOT NULL,
   SQ_TIPO_RESTRICAO    NUMERIC(18)          NOT NULL,
   RISCO                VARCHAR(1)           NOT NULL DEFAULT 'N'
      CONSTRAINT CKC_RISCO_SIW_REST CHECK (RISCO IN ('S','N') AND RISCO = UPPER(RISCO)),
   PROBLEMA             VARCHAR(1)           NOT NULL DEFAULT 'N'
      CONSTRAINT CKC_PROBLEMA_SIW_REST CHECK (PROBLEMA IN ('S','N') AND PROBLEMA = UPPER(PROBLEMA)),
   DESCRICAO            VARCHAR(2000)        NOT NULL,
   PROBABILIDADE        NUMERIC(1)           NULL DEFAULT 1
      CONSTRAINT CKC_PROBABILIDADE_SIW_REST CHECK (PROBABILIDADE IS NULL OR (PROBABILIDADE IN (1,2,3,4,5))),
   IMPACTO              NUMERIC(1)           NULL DEFAULT 1
      CONSTRAINT CKC_IMPACTO_SIW_REST CHECK (IMPACTO IS NULL OR (IMPACTO IN (1,2,3,4,5))),
   CRITICIDADE          NUMERIC(1)           NOT NULL DEFAULT 0,
   ESTRATEGIA           VARCHAR(1)           NOT NULL DEFAULT 'A'
      CONSTRAINT CKC_ESTRATEGIA_SIW_REST CHECK (ESTRATEGIA IN ('A','E','T','M')),
   ACAO_RESPOSTA        VARCHAR(2000)        NOT NULL,
   FASE_ATUAL           VARCHAR(1)           NOT NULL DEFAULT 'D'
      CONSTRAINT CKC_FASE_ATUAL_SIW_REST CHECK (FASE_ATUAL IN ('D','P','A','C')),
   DATA_SITUACAO        DATE                 NULL,
   SITUACAO_ATUAL       VARCHAR(2000)        NULL,
   ULTIMA_ATUALIZACAO   DATE                 NULL,
   CONSTRAINT PK_SIW_RESTRICAO PRIMARY KEY (SQ_SIW_RESTRICAO)
);

COMMENT ON TABLE SIW_RESTRICAO IS
'Registra riscos e problemas associados à solicitação.';

COMMENT ON COLUMN SIW_RESTRICAO.SQ_SIW_RESTRICAO IS
'Chave de SQ_SOLIC_RISCO.';

COMMENT ON COLUMN SIW_RESTRICAO.SQ_SIW_SOLICITACAO IS
'Chave de SIW_SOLICITACAO. Indica a que solicitação o registro está ligado.';

COMMENT ON COLUMN SIW_RESTRICAO.SQ_PESSOA IS
'Chave de CO_PESSOA. Responsável pela restrição.';

COMMENT ON COLUMN SIW_RESTRICAO.SQ_PESSOA_ATUALIZACAO IS
'Chave de CO_PESSOA. Usuário responsável pela criação ou última atualização da restrição.';

COMMENT ON COLUMN SIW_RESTRICAO.SQ_TIPO_RESTRICAO IS
'Chave de SIW_TIPO_RESTRICAO. Indica a que tipo de restrição o registro está ligado.';

COMMENT ON COLUMN SIW_RESTRICAO.RISCO IS
'Indica se o registro é um risco.';

COMMENT ON COLUMN SIW_RESTRICAO.PROBLEMA IS
'Indica se o registro atual é um problema.';

COMMENT ON COLUMN SIW_RESTRICAO.DESCRICAO IS
'Descrição da restrição.';

COMMENT ON COLUMN SIW_RESTRICAO.PROBABILIDADE IS
'Probabilidade do risco. Não se aplica para problemas. 1 - Muito baixo, 2 - Baixo, 3 - Médio, 4 - Alto, 5 - Muito alto.';

COMMENT ON COLUMN SIW_RESTRICAO.IMPACTO IS
'Impacto do risco. Não se aplica para problemas.  1 - Muito baixo, 2 - Baixo, 3 - Médio, 4 - Alto, 5 - Muito alto';

COMMENT ON COLUMN SIW_RESTRICAO.CRITICIDADE IS
'Criticidade do risco ou problema. Para riscos, é calculado a partir da probabilidade e do impacto. 1 - Baixa, 2 - Moderada, 3 - Alta.';

COMMENT ON COLUMN SIW_RESTRICAO.ESTRATEGIA IS
'Estratégia adotada frente ao risco ou problema. A - Aceitar, T - Transferir, E - Evitar, M - Mitigar';

COMMENT ON COLUMN SIW_RESTRICAO.ACAO_RESPOSTA IS
'Texto descrevendo a ação de resposta ao risco/problema.';

COMMENT ON COLUMN SIW_RESTRICAO.FASE_ATUAL IS
'Fase em que o risco ou problema se encontra. D - Definido, P - Pendente, A - Em andamento, C - Concluído.';

COMMENT ON COLUMN SIW_RESTRICAO.DATA_SITUACAO IS
'Data em que a situação atual foi verificada.';

COMMENT ON COLUMN SIW_RESTRICAO.SITUACAO_ATUAL IS
'Texto detalhando a situação atual do risco ou problema.';

COMMENT ON COLUMN SIW_RESTRICAO.ULTIMA_ATUALIZACAO IS
'Registra a data da criação ou última atualização do risco/problema.';

/*==============================================================*/
/* Index: IN_SIWSOLRES_RESP                                     */
/*==============================================================*/
CREATE  INDEX IN_SIWSOLRES_RESP ON SIW_RESTRICAO (
SQ_PESSOA,
SQ_SIW_SOLICITACAO,
SQ_SIW_RESTRICAO
);

/*==============================================================*/
/* Index: IN_SIWSOLRES_ATUAL                                    */
/*==============================================================*/
CREATE  INDEX IN_SIWSOLRES_ATUAL ON SIW_RESTRICAO (
SQ_PESSOA_ATUALIZACAO,
SQ_SIW_SOLICITACAO,
SQ_SIW_RESTRICAO
);

/*==============================================================*/
/* Index: IN_SIWSOLRES_SOLIC                                    */
/*==============================================================*/
CREATE  INDEX IN_SIWSOLRES_SOLIC ON SIW_RESTRICAO (
SQ_SIW_SOLICITACAO,
SQ_SIW_RESTRICAO
);

/*==============================================================*/
/* Table: SIW_RESTRICAO_ETAPA                                   */
/*==============================================================*/
CREATE TABLE SIW_RESTRICAO_ETAPA (
   SQ_SIW_RESTRICAO     NUMERIC(18)          NOT NULL,
   SQ_PROJETO_ETAPA     NUMERIC(18)          NOT NULL,
   CONSTRAINT PK_SIW_RESTRICAO_ETAPA PRIMARY KEY (SQ_SIW_RESTRICAO, SQ_PROJETO_ETAPA)
);

COMMENT ON TABLE SIW_RESTRICAO_ETAPA IS
'Registra as vinculações entre restrições e pacotes de trabalho de um projeto.';

COMMENT ON COLUMN SIW_RESTRICAO_ETAPA.SQ_SIW_RESTRICAO IS
'Chave de SQ_SOLIC_RISCO.';

COMMENT ON COLUMN SIW_RESTRICAO_ETAPA.SQ_PROJETO_ETAPA IS
'Chave de PJ_PROJETO_ETAPA. Indica a que etapa do projeto o registro está ligado.';

/*==============================================================*/
/* Index: IN_SIWRESETA_INV                                      */
/*==============================================================*/
CREATE  INDEX IN_SIWRESETA_INV ON SIW_RESTRICAO_ETAPA (
SQ_PROJETO_ETAPA,
SQ_SIW_RESTRICAO
);

/*==============================================================*/
/* Table: SIW_SOLICITACAO                                       */
/*==============================================================*/
CREATE TABLE SIW_SOLICITACAO (
   SQ_SIW_SOLICITACAO   NUMERIC(18)          NOT NULL,
   SQ_SOLIC_PAI         NUMERIC(18)          NULL,
   SQ_MENU              NUMERIC(18)          NOT NULL,
   SQ_UNIDADE           NUMERIC(10)          NOT NULL,
   SQ_SIW_TRAMITE       NUMERIC(18)          NOT NULL,
   SOLICITANTE          NUMERIC(18)          NOT NULL,
   CADASTRADOR          NUMERIC(18)          NOT NULL,
   EXECUTOR             NUMERIC(18)          NULL,
   RECEBEDOR            NUMERIC(18)          NULL,
   DESCRICAO            VARCHAR(2000)        NULL,
   JUSTIFICATIVA        VARCHAR(2000)        NULL,
   INICIO               DATE                 NULL,
   FIM                  DATE                 NULL,
   INCLUSAO             DATE                 NOT NULL,
   ULTIMA_ALTERACAO     DATE                 NOT NULL,
   CONCLUSAO            DATE                 NULL,
   VALOR                NUMERIC(18,2)        NULL,
   DATA_HORA            VARCHAR(1)           NOT NULL DEFAULT '0'
      CONSTRAINT CKC_SIWSOL_DTHOR CHECK (DATA_HORA IN ('0','1','2','3','4') AND DATA_HORA = UPPER(DATA_HORA)),
   PALAVRA_CHAVE        VARCHAR(92)          NULL,
   SQ_CIDADE_ORIGEM     NUMERIC(18)          NOT NULL,
   SQ_PLANO             NUMERIC(18)          NULL,
   PROTOCOLO_SIW        NUMERIC(18)          NULL,
   SQ_TIPO_EVENTO       NUMERIC(18)          NULL,
   ANO                  NUMERIC(4)           NOT NULL DEFAULT SELECT TO_NUMBER(TO_CHAR(NOW(),'yyyy')),
   OBSERVACAO           VARCHAR(2000)        NULL,
   MOTIVO_INSATISFACAO  VARCHAR(1000)        NULL,
   TITULO               VARCHAR(100)         NULL,
   CODIGO_INTERNO       VARCHAR(60)          NULL,
   CODIGO_EXTERNO       VARCHAR(60)          NULL,
   INDICADOR1           VARCHAR(1)           NOT NULL DEFAULT 'N'
      CONSTRAINT CKC_INDICADOR1_SIW_SOLI CHECK (INDICADOR1 IN ('S','N') AND INDICADOR1 = UPPER(INDICADOR1)),
   INDICADOR2           VARCHAR(1)           NOT NULL DEFAULT 'N'
      CONSTRAINT CKC_INDICADOR2_SIW_SOLI CHECK (INDICADOR2 IN ('S','N') AND INDICADOR2 = UPPER(INDICADOR2)),
   INDICADOR3           VARCHAR(1)           NOT NULL DEFAULT 'N'
      CONSTRAINT CKC_INDICADOR3_SIW_SOLI CHECK (INDICADOR3 IN ('S','N') AND INDICADOR3 = UPPER(INDICADOR3)),
   CONSTRAINT PK_SIW_SOLICITACAO PRIMARY KEY (SQ_SIW_SOLICITACAO)
);

COMMENT ON TABLE SIW_SOLICITACAO IS
'Solicitação';

COMMENT ON COLUMN SIW_SOLICITACAO.SQ_SIW_SOLICITACAO IS
'Chave de SIW_SOLICITACAO indicando a que solicitação o registro está ligado.';

COMMENT ON COLUMN SIW_SOLICITACAO.SQ_SOLIC_PAI IS
'Chave de SIW_SOLICITACAO. Auto-relacionamento da tabela.';

COMMENT ON COLUMN SIW_SOLICITACAO.SQ_MENU IS
'Chave de SIW_MENU. Indica a que opção do menu o registro está ligado.';

COMMENT ON COLUMN SIW_SOLICITACAO.SQ_UNIDADE IS
'Chave de EO_UNIDADE. Unidade solicitante.';

COMMENT ON COLUMN SIW_SOLICITACAO.SQ_SIW_TRAMITE IS
'Chave de SIW_TRAMITE. Indica o trâmite atual da solicitação.';

COMMENT ON COLUMN SIW_SOLICITACAO.SOLICITANTE IS
'Chave de CO_PESSOA. Indica o solicitante.';

COMMENT ON COLUMN SIW_SOLICITACAO.CADASTRADOR IS
'Chave de CO_PESSOA. Indica o cadastrador.';

COMMENT ON COLUMN SIW_SOLICITACAO.EXECUTOR IS
'Chave de CO_PESSOA. Indica o executor.';

COMMENT ON COLUMN SIW_SOLICITACAO.RECEBEDOR IS
'Chave de CO_PESSOA. Pessoa que aceitou a conclusão do serviço pelo solicitante.';

COMMENT ON COLUMN SIW_SOLICITACAO.DESCRICAO IS
'Descrição da solicitação.';

COMMENT ON COLUMN SIW_SOLICITACAO.JUSTIFICATIVA IS
'Justificativa da solicitação.';

COMMENT ON COLUMN SIW_SOLICITACAO.INICIO IS
'Início da solicitação.';

COMMENT ON COLUMN SIW_SOLICITACAO.FIM IS
'Data de término da solicitação.';

COMMENT ON COLUMN SIW_SOLICITACAO.INCLUSAO IS
'Data e hora de inclusao da solicitação. Gerada automaticamente pelo sistema.';

COMMENT ON COLUMN SIW_SOLICITACAO.ULTIMA_ALTERACAO IS
'Data da última alteração do registro.';

COMMENT ON COLUMN SIW_SOLICITACAO.CONCLUSAO IS
'Data de conclusão da solicitação.';

COMMENT ON COLUMN SIW_SOLICITACAO.VALOR IS
'Valor da solicitação.';

COMMENT ON COLUMN SIW_SOLICITACAO.DATA_HORA IS
'Indica como o sistema deve tratar a questão de horas. (0) Não pede data; (1) Pede apenas uma data; (2) Pede apenas uma data/hora; (3) Pede data início e fim; (4) Pede data/hora início e fim.';

COMMENT ON COLUMN SIW_SOLICITACAO.PALAVRA_CHAVE IS
'Contém palavras-chave para consulta';

COMMENT ON COLUMN SIW_SOLICITACAO.SQ_CIDADE_ORIGEM IS
'Chave de CO_CIDADE. Cidade que originou a solicitação';

COMMENT ON COLUMN SIW_SOLICITACAO.SQ_PLANO IS
'Chave de PE_PLANO. Indica a que plano estratégico a solicitação está ligada.';

COMMENT ON COLUMN SIW_SOLICITACAO.PROTOCOLO_SIW IS
'Chave de SIW_SOLICITACAO. Indica a que protocolo a solicitação está ligada.';

COMMENT ON COLUMN SIW_SOLICITACAO.SQ_TIPO_EVENTO IS
'Chave de SIW_TIPO_EVENTO. Indica a que tipo de evento o registro está ligado.';

COMMENT ON COLUMN SIW_SOLICITACAO.ANO IS
'Registra o ano da solicitação, útil apenas para serviços que exijam o controle por ano.';

COMMENT ON COLUMN SIW_SOLICITACAO.OBSERVACAO IS
'Observações.';

COMMENT ON COLUMN SIW_SOLICITACAO.MOTIVO_INSATISFACAO IS
'Texto que armazena o motivo da insatisfação de um solicitante quando emite a opinião sobre um atendimento.';

COMMENT ON COLUMN SIW_SOLICITACAO.TITULO IS
'Título da solicitação.';

COMMENT ON COLUMN SIW_SOLICITACAO.CODIGO_INTERNO IS
'Código interno para a solicitação.';

COMMENT ON COLUMN SIW_SOLICITACAO.CODIGO_EXTERNO IS
'Código da solicitação em outra organização ou sistema.';

COMMENT ON COLUMN SIW_SOLICITACAO.INDICADOR1 IS
'Indicador tipo Não/Sim de uso geral.';

COMMENT ON COLUMN SIW_SOLICITACAO.INDICADOR2 IS
'Indicador tipo Não/Sim de uso geral.';

COMMENT ON COLUMN SIW_SOLICITACAO.INDICADOR3 IS
'Indicador tipo Não/Sim de uso geral.';

/*==============================================================*/
/* Index: IN_SIWSOL_CADASTR                                     */
/*==============================================================*/
CREATE  INDEX IN_SIWSOL_CADASTR ON SIW_SOLICITACAO (
CADASTRADOR,
SQ_SIW_SOLICITACAO
);

/*==============================================================*/
/* Index: IN_SIWSOL_SOLIC                                       */
/*==============================================================*/
CREATE  INDEX IN_SIWSOL_SOLIC ON SIW_SOLICITACAO (
SOLICITANTE,
SQ_SIW_SOLICITACAO
);

/*==============================================================*/
/* Index: IN_SIWSOL_EXECUTOR                                    */
/*==============================================================*/
CREATE  INDEX IN_SIWSOL_EXECUTOR ON SIW_SOLICITACAO (
EXECUTOR,
SQ_SIW_SOLICITACAO
);

/*==============================================================*/
/* Index: IN_SIWSOL_INICIO                                      */
/*==============================================================*/
CREATE  INDEX IN_SIWSOL_INICIO ON SIW_SOLICITACAO (
SQ_MENU,
INICIO,
SQ_SIW_SOLICITACAO
);

/*==============================================================*/
/* Index: IN_SIWSOL_FIM                                         */
/*==============================================================*/
CREATE  INDEX IN_SIWSOL_FIM ON SIW_SOLICITACAO (
SQ_MENU,
FIM,
SQ_SIW_SOLICITACAO
);

/*==============================================================*/
/* Index: IN_SIWSOL_INCLUSAO                                    */
/*==============================================================*/
CREATE  INDEX IN_SIWSOL_INCLUSAO ON SIW_SOLICITACAO (
SQ_MENU,
INCLUSAO,
SQ_SIW_SOLICITACAO
);

/*==============================================================*/
/* Index: IN_SIWSOL_ALTER                                       */
/*==============================================================*/
CREATE  INDEX IN_SIWSOL_ALTER ON SIW_SOLICITACAO (
SQ_MENU,
ULTIMA_ALTERACAO,
SQ_SIW_SOLICITACAO
);

/*==============================================================*/
/* Index: IN_SIWSOL_CONC                                        */
/*==============================================================*/
CREATE  INDEX IN_SIWSOL_CONC ON SIW_SOLICITACAO (
SQ_MENU,
CONCLUSAO,
SQ_SIW_SOLICITACAO
);

/*==============================================================*/
/* Index: IN_SIWSOL_PAI                                         */
/*==============================================================*/
CREATE  INDEX IN_SIWSOL_PAI ON SIW_SOLICITACAO (
SQ_SOLIC_PAI,
SQ_SIW_SOLICITACAO
);

/*==============================================================*/
/* Index: IN_SIWSOL_UNIDSOL                                     */
/*==============================================================*/
CREATE  INDEX IN_SIWSOL_UNIDSOL ON SIW_SOLICITACAO (
SQ_UNIDADE,
SQ_SIW_SOLICITACAO
);

/*==============================================================*/
/* Index: IN_SIWSOL_CIDADE                                      */
/*==============================================================*/
CREATE  INDEX IN_SIWSOL_CIDADE ON SIW_SOLICITACAO (
SQ_CIDADE_ORIGEM,
SQ_SIW_SOLICITACAO
);

/*==============================================================*/
/* Index: IN_SIWSOL_ANO                                         */
/*==============================================================*/
CREATE  INDEX IN_SIWSOL_ANO ON SIW_SOLICITACAO (
ANO,
SQ_SIW_SOLICITACAO
);

/*==============================================================*/
/* Index: IN_SIWSOL_RECEBEDOR                                   */
/*==============================================================*/
CREATE  INDEX IN_SIWSOL_RECEBEDOR ON SIW_SOLICITACAO (
RECEBEDOR,
SQ_SIW_SOLICITACAO
);

/*==============================================================*/
/* Index: IN_SIWSOL_TITULO                                      */
/*==============================================================*/
CREATE  INDEX IN_SIWSOL_TITULO ON SIW_SOLICITACAO (
TITULO,
SQ_SIW_SOLICITACAO
);

/*==============================================================*/
/* Index: IN_SIWSOL_CODINT                                      */
/*==============================================================*/
CREATE  INDEX IN_SIWSOL_CODINT ON SIW_SOLICITACAO (
CODIGO_INTERNO,
SQ_SIW_SOLICITACAO
);

/*==============================================================*/
/* Index: IN_SIWSOL_CODEXT                                      */
/*==============================================================*/
CREATE  INDEX IN_SIWSOL_CODEXT ON SIW_SOLICITACAO (
CODIGO_EXTERNO,
SQ_SIW_SOLICITACAO
);

/*==============================================================*/
/* Index: IN_SIWSOL_PLANO                                       */
/*==============================================================*/
CREATE  INDEX IN_SIWSOL_PLANO ON SIW_SOLICITACAO (
SQ_PLANO,
SQ_SIW_SOLICITACAO
);

/*==============================================================*/
/* Index: IN_SIWSOL_PROTOCOLO                                   */
/*==============================================================*/
CREATE  INDEX IN_SIWSOL_PROTOCOLO ON SIW_SOLICITACAO (
PROTOCOLO_SIW,
SQ_SIW_SOLICITACAO
);

/*==============================================================*/
/* Index: IN_SIWSOL_EVENTO                                      */
/*==============================================================*/
CREATE  INDEX IN_SIWSOL_EVENTO ON SIW_SOLICITACAO (
SQ_TIPO_EVENTO,
SQ_SIW_SOLICITACAO
);

/*==============================================================*/
/* Index: IN_SIWSOL_TRAMITE                                     */
/*==============================================================*/
CREATE  INDEX IN_SIWSOL_TRAMITE ON SIW_SOLICITACAO (
SQ_SIW_TRAMITE,
SQ_SIW_SOLICITACAO
);

/*==============================================================*/
/* Table: SIW_SOLICITACAO_INTERESSADO                           */
/*==============================================================*/
CREATE TABLE SIW_SOLICITACAO_INTERESSADO (
   SQ_SOLICITACAO_INTERESSADO NUMERIC(18)          NOT NULL,
   SQ_SIW_SOLICITACAO   NUMERIC(18)          NOT NULL,
   SQ_PESSOA            NUMERIC(18)          NOT NULL,
   SQ_TIPO_INTERESSADO  NUMERIC(18)          NOT NULL,
   TIPO_VISAO           NUMERIC(1)           NOT NULL DEFAULT 2
      CONSTRAINT CKC_SIWSOLINT_TIPVIS CHECK (TIPO_VISAO IN (0,1,2)),
   ENVIA_EMAIL          VARCHAR(1)           NOT NULL DEFAULT 'N'
      CONSTRAINT CKC_SIWSOLINT_ENVMAI CHECK (ENVIA_EMAIL IN ('S','N') AND ENVIA_EMAIL = UPPER(ENVIA_EMAIL)),
   CONSTRAINT PK_SIW_SOLICITACAO_INTERESSADO PRIMARY KEY (SQ_SOLICITACAO_INTERESSADO)
);

COMMENT ON TABLE SIW_SOLICITACAO_INTERESSADO IS
'Registra os interessados na execução da solicitação.';

COMMENT ON COLUMN SIW_SOLICITACAO_INTERESSADO.SQ_SOLICITACAO_INTERESSADO IS
'Chave de SIW_SOLICITACAO_INTERESSADO.';

COMMENT ON COLUMN SIW_SOLICITACAO_INTERESSADO.SQ_SIW_SOLICITACAO IS
'Chave de SIW_SOLICITACAO. Indica a que solicitação o registro está ligado.';

COMMENT ON COLUMN SIW_SOLICITACAO_INTERESSADO.SQ_PESSOA IS
'Chave de CO_PESSOA. Indica a que pessoa o registro está ligado.';

COMMENT ON COLUMN SIW_SOLICITACAO_INTERESSADO.SQ_TIPO_INTERESSADO IS
'Chave de SIW_TIPO_INTERESSADO. Indica o tipo do interessado.';

COMMENT ON COLUMN SIW_SOLICITACAO_INTERESSADO.TIPO_VISAO IS
'Indica a visão que a pessoa pode ter dessa solicitação.';

COMMENT ON COLUMN SIW_SOLICITACAO_INTERESSADO.ENVIA_EMAIL IS
'Indica se deve ser enviado e-mail ao interessado quando houver alguma ocorrência na solicitação.';

/*==============================================================*/
/* Index: IN_SIWSOLINT_SOLIC                                    */
/*==============================================================*/
CREATE  INDEX IN_SIWSOLINT_SOLIC ON SIW_SOLICITACAO_INTERESSADO (
SQ_SIW_SOLICITACAO,
SQ_SOLICITACAO_INTERESSADO
);

/*==============================================================*/
/* Index: IN_SIWSOLINT_PESSOA                                   */
/*==============================================================*/
CREATE  INDEX IN_SIWSOLINT_PESSOA ON SIW_SOLICITACAO_INTERESSADO (
SQ_PESSOA,
SQ_SIW_SOLICITACAO,
SQ_SOLICITACAO_INTERESSADO
);

/*==============================================================*/
/* Index: IN_SIWSOLINT_TIPO                                     */
/*==============================================================*/
CREATE  INDEX IN_SIWSOLINT_TIPO ON SIW_SOLICITACAO_INTERESSADO (
SQ_SIW_SOLICITACAO,
SQ_TIPO_INTERESSADO,
SQ_SOLICITACAO_INTERESSADO
);

/*==============================================================*/
/* Table: SIW_SOLICITACAO_OBJETIVO                              */
/*==============================================================*/
CREATE TABLE SIW_SOLICITACAO_OBJETIVO (
   SQ_SIW_SOLICITACAO   NUMERIC(18)          NOT NULL,
   SQ_PLANO             NUMERIC(18)          NOT NULL,
   SQ_PEOBJETIVO        NUMERIC(18)          NOT NULL,
   CONSTRAINT PK_SIW_SOLICITACAO_OBJETIVO PRIMARY KEY (SQ_SIW_SOLICITACAO, SQ_PEOBJETIVO, SQ_PLANO)
);

COMMENT ON TABLE SIW_SOLICITACAO_OBJETIVO IS
'Registra a vinculação entre objetivos estratégicos e solicitações.';

COMMENT ON COLUMN SIW_SOLICITACAO_OBJETIVO.SQ_SIW_SOLICITACAO IS
'Chave de SIW_SOLICITACAO. Indica a que solicitação o registro está ligado.';

COMMENT ON COLUMN SIW_SOLICITACAO_OBJETIVO.SQ_PLANO IS
'Chave de PE_PLANO. Indica a que plano a solicitação está ligada.';

COMMENT ON COLUMN SIW_SOLICITACAO_OBJETIVO.SQ_PEOBJETIVO IS
'Chave de SQ_PEOBJETIVO. Indica a que objetivo estratégico a solicitação está ligada.';

/*==============================================================*/
/* Index: IN_SIWSOLOBJ_PLANO                                    */
/*==============================================================*/
CREATE  INDEX IN_SIWSOLOBJ_PLANO ON SIW_SOLICITACAO_OBJETIVO (
SQ_PLANO,
SQ_SIW_SOLICITACAO
);

/*==============================================================*/
/* Index: IN_SIWSOLOBJ_OBJETIVO                                 */
/*==============================================================*/
CREATE  INDEX IN_SIWSOLOBJ_OBJETIVO ON SIW_SOLICITACAO_OBJETIVO (
SQ_PEOBJETIVO,
SQ_SIW_SOLICITACAO
);

/*==============================================================*/
/* Table: SIW_SOLIC_APOIO                                       */
/*==============================================================*/
CREATE TABLE SIW_SOLIC_APOIO (
   SQ_SOLIC_APOIO       NUMERIC(18)          NOT NULL,
   SQ_SIW_SOLICITACAO   NUMERIC(18)          NOT NULL,
   SQ_TIPO_APOIO        NUMERIC(18)          NOT NULL,
   SQ_PESSOA_ATUALIZACAO NUMERIC(18)          NOT NULL,
   ENTIDADE             VARCHAR(50)          NOT NULL,
   DESCRICAO            VARCHAR(200)         NULL,
   VALOR                NUMERIC(18,2)        NOT NULL,
   ULTIMA_ATUALIZACAO   DATE                 NOT NULL DEFAULT 'now()',
   CONSTRAINT PK_SIW_SOLIC_APOIO PRIMARY KEY (SQ_SOLIC_APOIO)
);

COMMENT ON TABLE SIW_SOLIC_APOIO IS
'Registra os apoios financeiros a uma solicitação.';

COMMENT ON COLUMN SIW_SOLIC_APOIO.SQ_SOLIC_APOIO IS
'Chave de SIW_SOLIC_APOIO.';

COMMENT ON COLUMN SIW_SOLIC_APOIO.SQ_SIW_SOLICITACAO IS
'Chave de SIW_SOLICITACAO, informando a que solicitação o apoio refere-se.';

COMMENT ON COLUMN SIW_SOLIC_APOIO.SQ_TIPO_APOIO IS
'Chave de SIW_TIPO_APOIO, informando o tipo de apoio que a entidade está dando à solicitação.';

COMMENT ON COLUMN SIW_SOLIC_APOIO.SQ_PESSOA_ATUALIZACAO IS
'Chave de CO_PESSOA, indicando o usuário responsável pela inclusão ou última atualização do registro.';

COMMENT ON COLUMN SIW_SOLIC_APOIO.ENTIDADE IS
'Nome da entidade que está dando o apoio.';

COMMENT ON COLUMN SIW_SOLIC_APOIO.DESCRICAO IS
'Descritivo do apoio dado pela entidade.';

COMMENT ON COLUMN SIW_SOLIC_APOIO.VALOR IS
'Valor do apoio.';

COMMENT ON COLUMN SIW_SOLIC_APOIO.ULTIMA_ATUALIZACAO IS
'Data e hora da inclusão ou última atualização do registro.';

/*==============================================================*/
/* Index: IN_SIWSOLAPO_SOLIC                                    */
/*==============================================================*/
CREATE  INDEX IN_SIWSOLAPO_SOLIC ON SIW_SOLIC_APOIO (
SQ_SIW_SOLICITACAO,
SQ_SOLIC_APOIO
);

/*==============================================================*/
/* Table: SIW_SOLIC_ARQUIVO                                     */
/*==============================================================*/
CREATE TABLE SIW_SOLIC_ARQUIVO (
   SQ_SIW_SOLICITACAO   NUMERIC(18)          NOT NULL,
   SQ_SIW_ARQUIVO       NUMERIC(18)          NOT NULL,
   CONSTRAINT PK_SIW_SOLIC_ARQUIVO PRIMARY KEY (SQ_SIW_SOLICITACAO, SQ_SIW_ARQUIVO)
);

COMMENT ON TABLE SIW_SOLIC_ARQUIVO IS
'Vincula uma solicitação a arquivos físicos.';

COMMENT ON COLUMN SIW_SOLIC_ARQUIVO.SQ_SIW_SOLICITACAO IS
'Chave de SIW_SOLICITACAO indicando a que solicitação o registro está ligado.';

COMMENT ON COLUMN SIW_SOLIC_ARQUIVO.SQ_SIW_ARQUIVO IS
'Chave de SIW_ARQUIVO. Indica a que arquivo o registro está ligado.';

/*==============================================================*/
/* Index: IN_SIWSOLARQ_INVERSA                                  */
/*==============================================================*/
CREATE  INDEX IN_SIWSOLARQ_INVERSA ON SIW_SOLIC_ARQUIVO (
SQ_SIW_ARQUIVO,
SQ_SIW_SOLICITACAO
);

/*==============================================================*/
/* Table: SIW_SOLIC_INDICADOR                                   */
/*==============================================================*/
CREATE TABLE SIW_SOLIC_INDICADOR (
   SQ_SOLIC_INDICADOR   NUMERIC(18)          NOT NULL,
   SQ_SIW_SOLICITACAO   NUMERIC(18)          NOT NULL,
   SQ_EOINDICADOR       NUMERIC(18)          NOT NULL,
   CONSTRAINT PK_SIW_SOLIC_INDICADOR PRIMARY KEY (SQ_SOLIC_INDICADOR)
);

COMMENT ON TABLE SIW_SOLIC_INDICADOR IS
'Registra os indicadores vinculados a solicitação.';

COMMENT ON COLUMN SIW_SOLIC_INDICADOR.SQ_SOLIC_INDICADOR IS
'Chave de SIW_SOLIC_INDICADOR.';

COMMENT ON COLUMN SIW_SOLIC_INDICADOR.SQ_SIW_SOLICITACAO IS
'Chave de SIW_SOLICITACAO indicando a que solicitação o registro está ligado.';

COMMENT ON COLUMN SIW_SOLIC_INDICADOR.SQ_EOINDICADOR IS
'Chave de EO_INDICADOR. Referencia do indicador ligado ao registro.';

/*==============================================================*/
/* Index: IN_SIWSOLIND_SOLIC                                    */
/*==============================================================*/
CREATE  INDEX IN_SIWSOLIND_SOLIC ON SIW_SOLIC_INDICADOR (
SQ_SIW_SOLICITACAO,
SQ_EOINDICADOR,
SQ_SOLIC_INDICADOR
);

/*==============================================================*/
/* Index: IN_SIWSOLIND_IND                                      */
/*==============================================================*/
CREATE  INDEX IN_SIWSOLIND_IND ON SIW_SOLIC_INDICADOR (
SQ_EOINDICADOR,
SQ_SIW_SOLICITACAO,
SQ_SOLIC_INDICADOR
);

/*==============================================================*/
/* Table: SIW_SOLIC_LOG                                         */
/*==============================================================*/
CREATE TABLE SIW_SOLIC_LOG (
   SQ_SIW_SOLIC_LOG     NUMERIC(18)          NOT NULL,
   SQ_SIW_SOLICITACAO   NUMERIC(18)          NOT NULL,
   SQ_PESSOA            NUMERIC(18)          NOT NULL,
   SQ_SIW_TRAMITE       NUMERIC(18)          NOT NULL,
   DATA                 DATE                 NOT NULL DEFAULT 'now()',
   DEVOLUCAO            VARCHAR(1)           NOT NULL DEFAULT 'S'
      CONSTRAINT CKC_SIWSOLLOG_DEV
               CHECK (DEVOLUCAO IN ('S','N') AND DEVOLUCAO = UPPER(DEVOLUCAO)),
   OBSERVACAO           VARCHAR(2000)        NULL,
   CONSTRAINT PK_SIW_SOLIC_LOG PRIMARY KEY (SQ_SIW_SOLIC_LOG)
);

COMMENT ON TABLE SIW_SOLIC_LOG IS
'Registra os trâmites da solicitação';

COMMENT ON COLUMN SIW_SOLIC_LOG.SQ_SIW_SOLIC_LOG IS
'Chave de SIW_SOLIC_LOG.';

COMMENT ON COLUMN SIW_SOLIC_LOG.SQ_SIW_SOLICITACAO IS
'Chave de SIW_SOLICITACAO indicando a que solicitação o registro está ligado.';

COMMENT ON COLUMN SIW_SOLIC_LOG.SQ_PESSOA IS
'Chave de CO_PESSOA. Indica a que pessoa o registro está ligado.';

COMMENT ON COLUMN SIW_SOLIC_LOG.SQ_SIW_TRAMITE IS
'Chave de SIW_TRAMITE. Indica o trâmite da solicitação quando o log foi gerado.';

COMMENT ON COLUMN SIW_SOLIC_LOG.DATA IS
'Data da ocorrência.';

COMMENT ON COLUMN SIW_SOLIC_LOG.DEVOLUCAO IS
'Indica se ocorreu uma devolução de fase.';

COMMENT ON COLUMN SIW_SOLIC_LOG.OBSERVACAO IS
'Observações.';

/*==============================================================*/
/* Index: IN_SIWSOLLOG_SOLIC                                    */
/*==============================================================*/
CREATE  INDEX IN_SIWSOLLOG_SOLIC ON SIW_SOLIC_LOG (
SQ_SIW_SOLICITACAO,
SQ_SIW_TRAMITE,
SQ_SIW_SOLIC_LOG
);

/*==============================================================*/
/* Index: IN_SIWSOLLOG_PESSOA                                   */
/*==============================================================*/
CREATE  INDEX IN_SIWSOLLOG_PESSOA ON SIW_SOLIC_LOG (
SQ_PESSOA,
SQ_SIW_SOLICITACAO,
SQ_SIW_SOLIC_LOG
);

/*==============================================================*/
/* Index: IN_SIWSOLLOG_TRAMITE                                  */
/*==============================================================*/
CREATE  INDEX IN_SIWSOLLOG_TRAMITE ON SIW_SOLIC_LOG (
SQ_SIW_TRAMITE,
SQ_SIW_SOLICITACAO,
SQ_SIW_SOLIC_LOG
);

/*==============================================================*/
/* Table: SIW_SOLIC_LOG_ARQ                                     */
/*==============================================================*/
CREATE TABLE SIW_SOLIC_LOG_ARQ (
   SQ_SIW_SOLIC_LOG     NUMERIC(18)          NOT NULL,
   SQ_SIW_ARQUIVO       NUMERIC(18)          NOT NULL,
   CONSTRAINT PK_SIW_SOLIC_LOG_ARQ PRIMARY KEY (SQ_SIW_SOLIC_LOG, SQ_SIW_ARQUIVO)
);

COMMENT ON TABLE SIW_SOLIC_LOG_ARQ IS
'Vincula logs de solicitação a arquivos físicos.';

COMMENT ON COLUMN SIW_SOLIC_LOG_ARQ.SQ_SIW_SOLIC_LOG IS
'Chave de SIW_SOLIC_LOG.';

COMMENT ON COLUMN SIW_SOLIC_LOG_ARQ.SQ_SIW_ARQUIVO IS
'Chave de SIW_ARQUIVO. Indica a que arquivo o registro está ligado.';

/*==============================================================*/
/* Index: IN_SIWSOLLOGARQ_INV                                   */
/*==============================================================*/
CREATE  INDEX IN_SIWSOLLOGARQ_INV ON SIW_SOLIC_LOG_ARQ (
SQ_SIW_ARQUIVO,
SQ_SIW_SOLIC_LOG
);

/*==============================================================*/
/* Table: SIW_SOLIC_META                                        */
/*==============================================================*/
CREATE TABLE SIW_SOLIC_META (
   SQ_SOLIC_META        NUMERIC(18)          NOT NULL,
   SQ_SIW_SOLICITACAO   NUMERIC(18)          NULL,
   SQ_PLANO             NUMERIC(18)          NULL,
   SQ_EOINDICADOR       NUMERIC(18)          NOT NULL,
   SQ_PESSOA            NUMERIC(18)          NOT NULL,
   SQ_UNIDADE           NUMERIC(10)          NOT NULL,
   TITULO               VARCHAR(100)         NOT NULL,
   DESCRICAO            VARCHAR(2000)        NOT NULL,
   ORDEM                NUMERIC(3)           NOT NULL,
   INICIO               DATE                 NOT NULL,
   FIM                  DATE                 NOT NULL,
   QUANTIDADE           NUMERIC(18,4)        NOT NULL DEFAULT 0,
   CUMULATIVA           VARCHAR(1)           NOT NULL DEFAULT 'N'
      CONSTRAINT CKC_CUMULATIVA_SIWSOLMET CHECK (CUMULATIVA IN ('S','N') AND CUMULATIVA = UPPER(CUMULATIVA)),
   BASE_GEOGRAFICA      NUMERIC(1)           NOT NULL,
   SQ_PAIS              NUMERIC(18)          NULL,
   SQ_REGIAO            NUMERIC(18)          NULL,
   CO_UF                VARCHAR(3)           NULL,
   SQ_CIDADE            NUMERIC(18)          NULL,
   EXEQUIVEL            VARCHAR(1)           NOT NULL DEFAULT 'S'
      CONSTRAINT CKC_EXEQUIVEL_SIWSOLMET CHECK (EXEQUIVEL IN ('S','N') AND EXEQUIVEL = UPPER(EXEQUIVEL)),
   JUSTIFICATIVA_INEXEQUIVEL VARCHAR(1000)        NULL,
   OUTRAS_MEDIDAS       VARCHAR(1000)        NULL,
   SITUACAO_ATUAL       VARCHAR(4000)        NULL,
   CADASTRADOR          NUMERIC(18)          NOT NULL,
   INCLUSAO             DATE                 NOT NULL DEFAULT 'now()',
   ULTIMA_ALTERACAO     DATE                 NULL DEFAULT 'now()',
   VALOR_INICIAL        NUMERIC(18,4)        NOT NULL DEFAULT 0,
   CONSTRAINT PK_SIW_SOLIC_META PRIMARY KEY (SQ_SOLIC_META)
);

COMMENT ON TABLE SIW_SOLIC_META IS
'Registra as metas de uma solicitação ou de um plano estratégico.';

COMMENT ON COLUMN SIW_SOLIC_META.SQ_SOLIC_META IS
'Chave de SIW_SOLIC_META.';

COMMENT ON COLUMN SIW_SOLIC_META.SQ_SIW_SOLICITACAO IS
'Chave de SIW_SOLICITACAO indicando a que solicitação o registro está ligado.';

COMMENT ON COLUMN SIW_SOLIC_META.SQ_PLANO IS
'Chave de PE_PLANO. Indica a que plano estratégico a meta está ligada.';

COMMENT ON COLUMN SIW_SOLIC_META.SQ_EOINDICADOR IS
'Chave de EO_INDICADOR. Referencia do indicador ligado ao registro.';

COMMENT ON COLUMN SIW_SOLIC_META.SQ_PESSOA IS
'Chave de CO_PESSOA. Indica a pessoa responsável pelo monitoramento da meta.';

COMMENT ON COLUMN SIW_SOLIC_META.SQ_UNIDADE IS
'Chave de EO_UNIDADE. Indica a que unidade o registro está ligado.';

COMMENT ON COLUMN SIW_SOLIC_META.TITULO IS
'Título da meta.';

COMMENT ON COLUMN SIW_SOLIC_META.DESCRICAO IS
'Descrição da meta.';

COMMENT ON COLUMN SIW_SOLIC_META.ORDEM IS
'Ordem da meta para exibição em listagens.';

COMMENT ON COLUMN SIW_SOLIC_META.INICIO IS
'Início da execução da meta.';

COMMENT ON COLUMN SIW_SOLIC_META.FIM IS
'Fim da execução da meta.';

COMMENT ON COLUMN SIW_SOLIC_META.QUANTIDADE IS
'Quantidade prevista para a unidade de medida informada.';

COMMENT ON COLUMN SIW_SOLIC_META.CUMULATIVA IS
'Indica se a realização da meta é cumulativa ou não.';

COMMENT ON COLUMN SIW_SOLIC_META.BASE_GEOGRAFICA IS
'Indica a que base geográfica da meta aplica-se. 1 - Nacional, 2 - Regional, 3 - Estadual, 4 - Municipal, 5 - Organizacional.';

COMMENT ON COLUMN SIW_SOLIC_META.SQ_PAIS IS
'Chave de CO_PAIS. Tem valor apenas quando a aferição é a nivel nacional.';

COMMENT ON COLUMN SIW_SOLIC_META.SQ_REGIAO IS
'Chave de CO_REGIAO. Tem valor apenas quando a aferição é a nivel regional.';

COMMENT ON COLUMN SIW_SOLIC_META.CO_UF IS
'Chave de CO_UF. Tem valor apenas quando a aferição é a nivel estadual.';

COMMENT ON COLUMN SIW_SOLIC_META.SQ_CIDADE IS
'Chave de CO_CIDADE. Tem valor apenas quando a aferição é a nivel municipal.';

COMMENT ON COLUMN SIW_SOLIC_META.EXEQUIVEL IS
'Indica se a meta está avaliada como passível de cumprimento ou não.';

COMMENT ON COLUMN SIW_SOLIC_META.JUSTIFICATIVA_INEXEQUIVEL IS
'Motivos que justificam o não cumprimento da meta.';

COMMENT ON COLUMN SIW_SOLIC_META.OUTRAS_MEDIDAS IS
'Descrição das medidas necessárias ao cumprimento da meta.';

COMMENT ON COLUMN SIW_SOLIC_META.SITUACAO_ATUAL IS
'Texto detalhando a situação atual da meta.';

COMMENT ON COLUMN SIW_SOLIC_META.CADASTRADOR IS
'Chave de CO_PESSOA. Indica o responsável pelo cadastramento ou pela última alteração no registro.';

COMMENT ON COLUMN SIW_SOLIC_META.INCLUSAO IS
'Data de inclusão do registro.';

COMMENT ON COLUMN SIW_SOLIC_META.ULTIMA_ALTERACAO IS
'Data da última alteração no registro.';

COMMENT ON COLUMN SIW_SOLIC_META.VALOR_INICIAL IS
'Valor do indicador ligado à meta na data de início.';

/*==============================================================*/
/* Index: IN_SIWSOLMET_IND                                      */
/*==============================================================*/
CREATE  INDEX IN_SIWSOLMET_IND ON SIW_SOLIC_META (
SQ_EOINDICADOR,
SQ_SIW_SOLICITACAO,
SQ_SOLIC_META
);

/*==============================================================*/
/* Index: IN_SIWSOLMET_SOLIC                                    */
/*==============================================================*/
CREATE  INDEX IN_SIWSOLMET_SOLIC ON SIW_SOLIC_META (
SQ_SIW_SOLICITACAO,
SQ_SOLIC_META
);

/*==============================================================*/
/* Index: IN_SIWSOLMET_RESP                                     */
/*==============================================================*/
CREATE  INDEX IN_SIWSOLMET_RESP ON SIW_SOLIC_META (
SQ_PESSOA,
SQ_SOLIC_META
);

/*==============================================================*/
/* Index: IN_SIWSOLMET_UNID                                     */
/*==============================================================*/
CREATE  INDEX IN_SIWSOLMET_UNID ON SIW_SOLIC_META (
SQ_UNIDADE,
SQ_SOLIC_META
);

/*==============================================================*/
/* Index: IN_SIWSOLMET_PLANO                                    */
/*==============================================================*/
CREATE  INDEX IN_SIWSOLMET_PLANO ON SIW_SOLIC_META (
SQ_PLANO,
SQ_SOLIC_META
);

/*==============================================================*/
/* Table: SIW_SOLIC_RECURSO                                     */
/*==============================================================*/
CREATE TABLE SIW_SOLIC_RECURSO (
   SQ_SOLIC_RECURSO     NUMERIC(18)          NOT NULL,
   SQ_SIW_SOLICITACAO   NUMERIC(18)          NOT NULL,
   SQ_RECURSO           NUMERIC(18)          NOT NULL,
   TIPO                 NUMERIC(1)           NOT NULL,
   SOLICITANTE          NUMERIC(18)          NOT NULL,
   JUSTIFICATIVA        VARCHAR(2000)        NOT NULL,
   INCLUSAO             DATE                 NOT NULL,
   AUTORIZADO           VARCHAR(1)           NOT NULL DEFAULT 'N'
      CONSTRAINT CKC_AUTORIZADO_SIW_SOLI CHECK (AUTORIZADO IN ('S','N') AND AUTORIZADO = UPPER(AUTORIZADO)),
   AUTORIZACAO          DATE                 NULL,
   AUTORIZADOR          NUMERIC(18)          NULL,
   CONSTRAINT PK_SIW_SOLIC_RECURSO PRIMARY KEY (SQ_SOLIC_RECURSO)
);

COMMENT ON TABLE SIW_SOLIC_RECURSO IS
'Registra o consumo de um recurso por uma solicitação.';

COMMENT ON COLUMN SIW_SOLIC_RECURSO.SQ_SOLIC_RECURSO IS
'Chave de SIW_SOLIC_RECURSO.';

COMMENT ON COLUMN SIW_SOLIC_RECURSO.SQ_SIW_SOLICITACAO IS
'Chave de SIW_SOLICITACAO. Indica a que solicitação o pedido de alocação está ligado.';

COMMENT ON COLUMN SIW_SOLIC_RECURSO.SQ_RECURSO IS
'Chave de EO_RECURSO. Indica a que recurso a solicitação está ligada.';

COMMENT ON COLUMN SIW_SOLIC_RECURSO.TIPO IS
'Tipo do pedido. 1 - Alocação; 2 - Liberação.';

COMMENT ON COLUMN SIW_SOLIC_RECURSO.SOLICITANTE IS
'Chave de CO_PESSOA. Indica a pessoa que solicitou a alocação ou liberação do recurso.';

COMMENT ON COLUMN SIW_SOLIC_RECURSO.JUSTIFICATIVA IS
'Justificativa para alocação ou liberação do recurso, permitindo à área gestora do recurso decidir sobre sua autorização.';

COMMENT ON COLUMN SIW_SOLIC_RECURSO.INCLUSAO IS
'Data de inclusão do pedido de alocação ou liberação do recurso.';

COMMENT ON COLUMN SIW_SOLIC_RECURSO.AUTORIZADO IS
'Indica se a alocação ou liberação já foi autorizada pela unidade gestora do recurso.';

COMMENT ON COLUMN SIW_SOLIC_RECURSO.AUTORIZACAO IS
'Data de autorização para alocação ou liberação do recurso.';

COMMENT ON COLUMN SIW_SOLIC_RECURSO.AUTORIZADOR IS
'Chave de CO_PESSOA. Indica a pessoa que autorizou a alocação ou liberação do recurso.';

/*==============================================================*/
/* Index: IN_SIWSOLREC_RECURSO                                  */
/*==============================================================*/
CREATE  INDEX IN_SIWSOLREC_RECURSO ON SIW_SOLIC_RECURSO (
SQ_RECURSO,
SQ_SIW_SOLICITACAO,
SQ_SOLIC_RECURSO
);

/*==============================================================*/
/* Index: IN_SIWSOLREC_SOLIC                                    */
/*==============================================================*/
CREATE  INDEX IN_SIWSOLREC_SOLIC ON SIW_SOLIC_RECURSO (
SQ_SIW_SOLICITACAO,
SQ_RECURSO,
SQ_SOLIC_RECURSO
);

/*==============================================================*/
/* Table: SIW_SOLIC_RECURSO_ALOCACAO                            */
/*==============================================================*/
CREATE TABLE SIW_SOLIC_RECURSO_ALOCACAO (
   SQ_SOLIC_RECURSO_ALOCACAO NUMERIC(18)          NOT NULL,
   SQ_SOLIC_RECURSO     NUMERIC(18)          NOT NULL,
   INICIO               DATE                 NOT NULL,
   FIM                  DATE                 NOT NULL,
   UNIDADES_SOLICITADAS NUMERIC(18,2)        NOT NULL,
   UNIDADES_AUTORIZADAS NUMERIC(18,2)        NOT NULL,
   CONSTRAINT PK_SIW_SOLIC_RECURSO_ALOCACAO PRIMARY KEY (SQ_SOLIC_RECURSO_ALOCACAO)
);

COMMENT ON TABLE SIW_SOLIC_RECURSO_ALOCACAO IS
'Registra os dados da alocação do recurso por uma solicitação.';

COMMENT ON COLUMN SIW_SOLIC_RECURSO_ALOCACAO.SQ_SOLIC_RECURSO_ALOCACAO IS
'Chave de SIW_SOLIC_RECURSO_ALOCACAO.';

COMMENT ON COLUMN SIW_SOLIC_RECURSO_ALOCACAO.SQ_SOLIC_RECURSO IS
'Chave de SIW_SOLIC_RECURSO. Indica a que solicitação o registro está ligado.';

COMMENT ON COLUMN SIW_SOLIC_RECURSO_ALOCACAO.INICIO IS
'Data de início da alocação ou liberação do recurso.';

COMMENT ON COLUMN SIW_SOLIC_RECURSO_ALOCACAO.FIM IS
'Data de término da alocação ou liberação do recurso.';

COMMENT ON COLUMN SIW_SOLIC_RECURSO_ALOCACAO.UNIDADES_SOLICITADAS IS
'Quantidade de unidades solicitadas para alocação ou liberação do recurso.';

COMMENT ON COLUMN SIW_SOLIC_RECURSO_ALOCACAO.UNIDADES_AUTORIZADAS IS
'Quantidade de unidades autorizadas para alocação ou liberação do recurso.';

/*==============================================================*/
/* Index: IN_SIWSOLRECALO_INICIO                                */
/*==============================================================*/
CREATE  INDEX IN_SIWSOLRECALO_INICIO ON SIW_SOLIC_RECURSO_ALOCACAO (
SQ_SOLIC_RECURSO,
INICIO,
SQ_SOLIC_RECURSO_ALOCACAO
);

/*==============================================================*/
/* Index: IN_SIWSOLRECALO_FIM                                   */
/*==============================================================*/
CREATE  INDEX IN_SIWSOLRECALO_FIM ON SIW_SOLIC_RECURSO_ALOCACAO (
SQ_SOLIC_RECURSO,
FIM,
SQ_SOLIC_RECURSO_ALOCACAO
);

/*==============================================================*/
/* Table: SIW_SOLIC_RECURSO_LOG                                 */
/*==============================================================*/
CREATE TABLE SIW_SOLIC_RECURSO_LOG (
   SQ_SOLIC_RECURSO_LOG NUMERIC(18)          NOT NULL,
   SQ_SOLIC_RECURSO     NUMERIC(18)          NOT NULL,
   SQ_PESSOA            NUMERIC(18)          NOT NULL,
   DATA                 DATE                 NOT NULL,
   TIPO                 NUMERIC(1)           NOT NULL,
   INICIO               DATE                 NOT NULL,
   FIM                  DATE                 NOT NULL,
   UNIDADES_AUTORIZADAS NUMERIC(18,2)        NULL,
   CONSTRAINT PK_SIW_SOLIC_RECURSO_LOG PRIMARY KEY (SQ_SOLIC_RECURSO_LOG)
);

COMMENT ON TABLE SIW_SOLIC_RECURSO_LOG IS
'Registra o log de solicitações para alocação ou liberação de recursos.';

COMMENT ON COLUMN SIW_SOLIC_RECURSO_LOG.SQ_SOLIC_RECURSO_LOG IS
'Chave de SIW_SOLIC_RECURSO_LOG.';

COMMENT ON COLUMN SIW_SOLIC_RECURSO_LOG.SQ_SOLIC_RECURSO IS
'Chave de SIW_SOLIC_RECURSO. Indica a que solicitação o registro está ligado.';

COMMENT ON COLUMN SIW_SOLIC_RECURSO_LOG.SQ_PESSOA IS
'Chave de CO_PESSOA. Indica a pessoa responsável pela ocorrência.';

COMMENT ON COLUMN SIW_SOLIC_RECURSO_LOG.DATA IS
'Data da ocorrência.';

COMMENT ON COLUMN SIW_SOLIC_RECURSO_LOG.TIPO IS
'Tipo da ocorrência. 1 - Inclusão pelo solicitante; 2 - Envio para autorização; 3 - Concessão de autorização; 4 - Ajuste pelo autorizador.';

COMMENT ON COLUMN SIW_SOLIC_RECURSO_LOG.INICIO IS
'Data de início da alocação ou liberação do recurso.';

COMMENT ON COLUMN SIW_SOLIC_RECURSO_LOG.FIM IS
'Data de término da alocação ou liberação do recurso.';

COMMENT ON COLUMN SIW_SOLIC_RECURSO_LOG.UNIDADES_AUTORIZADAS IS
'Quantidade de unidades autorizadas para alocação ou liberação do recurso.';

/*==============================================================*/
/* Index: IN_SIWSOLRECLOG_SOLIC                                 */
/*==============================================================*/
CREATE  INDEX IN_SIWSOLRECLOG_SOLIC ON SIW_SOLIC_RECURSO_LOG (
SQ_SOLIC_RECURSO,
DATA,
SQ_SOLIC_RECURSO_LOG
);

/*==============================================================*/
/* Index: IN_SIWSOLRECLOG_PESSOA                                */
/*==============================================================*/
CREATE  INDEX IN_SIWSOLRECLOG_PESSOA ON SIW_SOLIC_RECURSO_LOG (
SQ_PESSOA,
SQ_SOLIC_RECURSO_LOG
);

/*==============================================================*/
/* Table: SIW_SOLIC_SITUACAO                                    */
/*==============================================================*/
CREATE TABLE SIW_SOLIC_SITUACAO (
   SQ_SOLIC_SITUACAO    NUMERIC(18)          NOT NULL,
   SQ_SIW_SOLICITACAO   NUMERIC(18)          NOT NULL,
   SQ_PESSOA            NUMERIC(18)          NOT NULL,
   INICIO               DATE                 NOT NULL,
   FIM                  DATE                 NOT NULL,
   SITUACAO             VARCHAR(1000)        NOT NULL,
   PROGRESSOS           VARCHAR(1000)        NULL,
   PASSOS               VARCHAR(1000)        NULL,
   ULTIMA_ALTERACAO     DATE                 NOT NULL DEFAULT 'now()',
   CONSTRAINT PK_SIW_SOLIC_SITUACAO PRIMARY KEY (SQ_SOLIC_SITUACAO)
);

COMMENT ON TABLE SIW_SOLIC_SITUACAO IS
'Registra reportes periódicos sobre a situação da solicitação.';

COMMENT ON COLUMN SIW_SOLIC_SITUACAO.SQ_SOLIC_SITUACAO IS
'Chave de SIW_SOLIC_SITUACAO.';

COMMENT ON COLUMN SIW_SOLIC_SITUACAO.SQ_SIW_SOLICITACAO IS
'Chave de SIW_SOLICITACAO indicando a que solicitação o registro está ligado.';

COMMENT ON COLUMN SIW_SOLIC_SITUACAO.SQ_PESSOA IS
'Chave de CO_PESSOA. Usuário responsável pela última alteração no registro.';

COMMENT ON COLUMN SIW_SOLIC_SITUACAO.INICIO IS
'Início do período de reporte.';

COMMENT ON COLUMN SIW_SOLIC_SITUACAO.FIM IS
'Término do período de reporte.';

COMMENT ON COLUMN SIW_SOLIC_SITUACAO.SITUACAO IS
'Comentários gerais e pontos de atenção.';

COMMENT ON COLUMN SIW_SOLIC_SITUACAO.PROGRESSOS IS
'Principais progressos.';

COMMENT ON COLUMN SIW_SOLIC_SITUACAO.PASSOS IS
'Próximos passos.';

COMMENT ON COLUMN SIW_SOLIC_SITUACAO.ULTIMA_ALTERACAO IS
'Data de última alteração do registro.';

/*==============================================================*/
/* Index: IN_SIWSOLSIT_INICIO                                   */
/*==============================================================*/
CREATE  INDEX IN_SIWSOLSIT_INICIO ON SIW_SOLIC_SITUACAO (
INICIO,
SQ_SOLIC_SITUACAO
);

/*==============================================================*/
/* Index: IN_SIWSOLSIT_FIM                                      */
/*==============================================================*/
CREATE  INDEX IN_SIWSOLSIT_FIM ON SIW_SOLIC_SITUACAO (
FIM,
SQ_SOLIC_SITUACAO
);

/*==============================================================*/
/* Index: IN_SIWSOLSIT_ALTER                                    */
/*==============================================================*/
CREATE  INDEX IN_SIWSOLSIT_ALTER ON SIW_SOLIC_SITUACAO (
ULTIMA_ALTERACAO,
SQ_SOLIC_SITUACAO
);

/*==============================================================*/
/* Index: IN_SIWSOLSIT_PESSOA                                   */
/*==============================================================*/
CREATE  INDEX IN_SIWSOLSIT_PESSOA ON SIW_SOLIC_SITUACAO (
SQ_PESSOA,
SQ_SOLIC_SITUACAO
);

/*==============================================================*/
/* Index: IN_SIWSOLSIT_SOLIC                                    */
/*==============================================================*/
CREATE  INDEX IN_SIWSOLSIT_SOLIC ON SIW_SOLIC_SITUACAO (
SQ_SIW_SOLICITACAO,
SQ_SOLIC_SITUACAO
);

/*==============================================================*/
/* Table: SIW_SOLIC_VINCULO                                     */
/*==============================================================*/
CREATE TABLE SIW_SOLIC_VINCULO (
   SQ_SIW_SOLICITACAO   NUMERIC(18)          NOT NULL,
   SQ_MENU              NUMERIC(18)          NOT NULL,
   CONSTRAINT PK_SIW_SOLIC_VINCULO PRIMARY KEY (SQ_SIW_SOLICITACAO, SQ_MENU)
);

COMMENT ON TABLE SIW_SOLIC_VINCULO IS
'Registra os vínculos possíveis para uma solicitação.';

COMMENT ON COLUMN SIW_SOLIC_VINCULO.SQ_SIW_SOLICITACAO IS
'Chave de SIW_SOLICITACAO. Indica a que solicitação o registro está ligado.';

COMMENT ON COLUMN SIW_SOLIC_VINCULO.SQ_MENU IS
'Chave de SIW_MENU. Indica a que opção do menu o registro está ligado.';

/*==============================================================*/
/* Index: IN_SIWSOLVIN_INV                                      */
/*==============================================================*/
CREATE  INDEX IN_SIWSOLVIN_INV ON SIW_SOLIC_VINCULO (
SQ_MENU,
SQ_SIW_SOLICITACAO
);

/*==============================================================*/
/* Table: SIW_TIPO_APOIO                                        */
/*==============================================================*/
CREATE TABLE SIW_TIPO_APOIO (
   SQ_TIPO_APOIO        NUMERIC(18)          NOT NULL,
   CLIENTE              NUMERIC(18)          NOT NULL,
   NOME                 VARCHAR(50)          NOT NULL,
   SIGLA                VARCHAR(10)          NOT NULL,
   DESCRICAO            VARCHAR(400)         NULL,
   ATIVO                VARCHAR(1)           NOT NULL DEFAULT 'S'
      CONSTRAINT CKC_ATIVO_SIW_TIPO CHECK (ATIVO IN ('S','N') AND ATIVO = UPPER(ATIVO)),
   CONSTRAINT PK_SIW_TIPO_APOIO PRIMARY KEY (SQ_TIPO_APOIO)
);

COMMENT ON TABLE SIW_TIPO_APOIO IS
'Registra os tipos possíveis de apoio financeiro.';

COMMENT ON COLUMN SIW_TIPO_APOIO.SQ_TIPO_APOIO IS
'Chave de SIW_TIPO_APOIO.';

COMMENT ON COLUMN SIW_TIPO_APOIO.CLIENTE IS
'Cliente ao qual o tipo de apoio está vinculado.';

COMMENT ON COLUMN SIW_TIPO_APOIO.NOME IS
'Nome do tipo de apoio.';

COMMENT ON COLUMN SIW_TIPO_APOIO.SIGLA IS
'Sigla do tipo de apoio.';

COMMENT ON COLUMN SIW_TIPO_APOIO.DESCRICAO IS
'Descrição do tipo de apoio.';

COMMENT ON COLUMN SIW_TIPO_APOIO.ATIVO IS
'Indica se este tipo pode ser associado a novos registros.';

/*==============================================================*/
/* Index: IN_SIWTIPAPO_CLIENTE                                  */
/*==============================================================*/
CREATE  INDEX IN_SIWTIPAPO_CLIENTE ON SIW_TIPO_APOIO (
CLIENTE,
SQ_TIPO_APOIO
);

/*==============================================================*/
/* Table: SIW_TIPO_ARQUIVO                                      */
/*==============================================================*/
CREATE TABLE SIW_TIPO_ARQUIVO (
   SQ_TIPO_ARQUIVO      NUMERIC(18)          NOT NULL,
   CLIENTE              NUMERIC(18)          NOT NULL,
   NOME                 VARCHAR(50)          NOT NULL,
   SIGLA                VARCHAR(10)          NOT NULL,
   DESCRICAO            VARCHAR(400)         NULL,
   ATIVO                VARCHAR(1)           NOT NULL DEFAULT 'S'
      CONSTRAINT CKC_ATIVO_SIWTPARQ CHECK (ATIVO IN ('S','N') AND ATIVO = UPPER(ATIVO)),
   CONSTRAINT PK_SIW_TIPO_ARQUIVO PRIMARY KEY (SQ_TIPO_ARQUIVO)
);

COMMENT ON TABLE SIW_TIPO_ARQUIVO IS
'Registra os tipos possíveis de arquivos.';

COMMENT ON COLUMN SIW_TIPO_ARQUIVO.SQ_TIPO_ARQUIVO IS
'Chave de SIW_TIPO_ARQUIVO.';

COMMENT ON COLUMN SIW_TIPO_ARQUIVO.CLIENTE IS
'Cliente ao qual o registro está vinculado.';

COMMENT ON COLUMN SIW_TIPO_ARQUIVO.NOME IS
'Nome do tipo de arquivo.';

COMMENT ON COLUMN SIW_TIPO_ARQUIVO.SIGLA IS
'Sigla do tipo de arquivo.';

COMMENT ON COLUMN SIW_TIPO_ARQUIVO.DESCRICAO IS
'Descrição do tipo de arquivo.';

COMMENT ON COLUMN SIW_TIPO_ARQUIVO.ATIVO IS
'Indica se este tipo pode ser associado a novos registros.';

/*==============================================================*/
/* Index: IN_SIWTIPARQ_ATIVO                                    */
/*==============================================================*/
CREATE  INDEX IN_SIWTIPARQ_ATIVO ON SIW_TIPO_ARQUIVO (
CLIENTE,
ATIVO,
SQ_TIPO_ARQUIVO
);

/*==============================================================*/
/* Table: SIW_TIPO_EVENTO                                       */
/*==============================================================*/
CREATE TABLE SIW_TIPO_EVENTO (
   SQ_TIPO_EVENTO       NUMERIC(18)          NOT NULL,
   SQ_MENU              NUMERIC(18)          NOT NULL,
   NOME                 VARCHAR(60)          NOT NULL,
   ORDEM                NUMERIC(4)           NOT NULL,
   SIGLA                VARCHAR(15)          NOT NULL,
   DESCRICAO            VARCHAR(2000)        NOT NULL,
   ATIVO                VARCHAR(1)           NOT NULL DEFAULT 'S'
      CONSTRAINT CKC_ATIVO_SIWTIPEVE CHECK (ATIVO IN ('S','N') AND ATIVO = UPPER(ATIVO)),
   CONSTRAINT PK_SIW_TIPO_EVENTO PRIMARY KEY (SQ_TIPO_EVENTO)
);

COMMENT ON TABLE SIW_TIPO_EVENTO IS
'Registra os tipos de evento.';

COMMENT ON COLUMN SIW_TIPO_EVENTO.SQ_TIPO_EVENTO IS
'Chave de SIW_TIPO_EVENTO.';

COMMENT ON COLUMN SIW_TIPO_EVENTO.SQ_MENU IS
'Chave de SIW_MENU. Indica a que opção do menu o registro está ligado.';

COMMENT ON COLUMN SIW_TIPO_EVENTO.NOME IS
'Nome do tipo de evento.';

COMMENT ON COLUMN SIW_TIPO_EVENTO.ORDEM IS
'Indica a ordem do registro nas listagens.';

COMMENT ON COLUMN SIW_TIPO_EVENTO.SIGLA IS
'Sigla do tipo de evento.';

COMMENT ON COLUMN SIW_TIPO_EVENTO.DESCRICAO IS
'Descrição do tipo de evento.';

COMMENT ON COLUMN SIW_TIPO_EVENTO.ATIVO IS
'Indica se o tipo pode ser vinculado a novos registros.';

/*==============================================================*/
/* Index: IN_SIWTIPEVE_NOME                                     */
/*==============================================================*/
CREATE  INDEX IN_SIWTIPEVE_NOME ON SIW_TIPO_EVENTO (
SQ_MENU,
NOME,
SQ_TIPO_EVENTO
);

/*==============================================================*/
/* Index: IN_SIWTIPEVE_SIGLA                                    */
/*==============================================================*/
CREATE  INDEX IN_SIWTIPEVE_SIGLA ON SIW_TIPO_EVENTO (
SQ_MENU,
SIGLA,
SQ_TIPO_EVENTO
);

/*==============================================================*/
/* Index: IN_SIWTIPEVE_ATIVO                                    */
/*==============================================================*/
CREATE  INDEX IN_SIWTIPEVE_ATIVO ON SIW_TIPO_EVENTO (
SQ_MENU,
ATIVO,
SQ_TIPO_EVENTO
);

/*==============================================================*/
/* Table: SIW_TIPO_INTERESSADO                                  */
/*==============================================================*/
CREATE TABLE SIW_TIPO_INTERESSADO (
   SQ_TIPO_INTERESSADO  NUMERIC(18)          NOT NULL,
   SQ_MENU              NUMERIC(18)          NOT NULL,
   NOME                 VARCHAR(60)          NOT NULL,
   ORDEM                NUMERIC(4)           NOT NULL,
   SIGLA                VARCHAR(15)          NOT NULL,
   DESCRICAO            VARCHAR(2000)        NOT NULL,
   ATIVO                VARCHAR(1)           NOT NULL DEFAULT 'S'
      CONSTRAINT CKC_ATIVO_SIWTIPINT CHECK (ATIVO IN ('S','N') AND ATIVO = UPPER(ATIVO)),
   CONSTRAINT PK_SIW_TIPO_INTERESSADO PRIMARY KEY (SQ_TIPO_INTERESSADO)
);

COMMENT ON TABLE SIW_TIPO_INTERESSADO IS
'Registra os tipos de interessado.';

COMMENT ON COLUMN SIW_TIPO_INTERESSADO.SQ_TIPO_INTERESSADO IS
'Chave de SIW_TIPO_INTERESSADO.';

COMMENT ON COLUMN SIW_TIPO_INTERESSADO.SQ_MENU IS
'Chave de SIW_MENU. Indica a que opção do menu o registro está ligado.';

COMMENT ON COLUMN SIW_TIPO_INTERESSADO.NOME IS
'Nome do tipo de interessado.';

COMMENT ON COLUMN SIW_TIPO_INTERESSADO.ORDEM IS
'Indica a ordem do registro nas listagens.';

COMMENT ON COLUMN SIW_TIPO_INTERESSADO.SIGLA IS
'Sigla do tipo de interessado.';

COMMENT ON COLUMN SIW_TIPO_INTERESSADO.DESCRICAO IS
'Descrição do tipo de interessado.';

COMMENT ON COLUMN SIW_TIPO_INTERESSADO.ATIVO IS
'Indica se o tipo pode ser vinculado a novos registros.';

/*==============================================================*/
/* Index: IN_SIWTIPINT_NOME                                     */
/*==============================================================*/
CREATE  INDEX IN_SIWTIPINT_NOME ON SIW_TIPO_INTERESSADO (
SQ_MENU,
NOME,
SQ_TIPO_INTERESSADO
);

/*==============================================================*/
/* Index: IN_SIWTIPINT_SIGLA                                    */
/*==============================================================*/
CREATE  INDEX IN_SIWTIPINT_SIGLA ON SIW_TIPO_INTERESSADO (
SQ_MENU,
SIGLA,
SQ_TIPO_INTERESSADO
);

/*==============================================================*/
/* Index: IN_SIWTIPINT_ATIVO                                    */
/*==============================================================*/
CREATE  INDEX IN_SIWTIPINT_ATIVO ON SIW_TIPO_INTERESSADO (
SQ_MENU,
ATIVO,
SQ_TIPO_INTERESSADO
);

/*==============================================================*/
/* Table: SIW_TIPO_LOG                                          */
/*==============================================================*/
CREATE TABLE SIW_TIPO_LOG (
   SQ_TIPO_LOG          NUMERIC(18)          NOT NULL,
   CLIENTE              NUMERIC(18)          NOT NULL,
   SQ_MENU              NUMERIC(18)          NOT NULL,
   NOME                 VARCHAR(60)          NOT NULL,
   SIGLA                VARCHAR(10)          NOT NULL,
   ORDEM                NUMERIC(4)           NOT NULL,
   ATIVO                VARCHAR(1)           NOT NULL DEFAULT 'S'
      CONSTRAINT CKC_SIWTIPLOG_ATIVO CHECK (ATIVO IN ('S','N') AND ATIVO = UPPER(ATIVO)),
   CONSTRAINT PK_SIW_TIPO_LOG PRIMARY KEY (SQ_TIPO_LOG)
);

COMMENT ON TABLE SIW_TIPO_LOG IS
'Registra os tipos de log de cada serviço.';

COMMENT ON COLUMN SIW_TIPO_LOG.SQ_TIPO_LOG IS
'Chave de SIW_TIPO_LOG.';

COMMENT ON COLUMN SIW_TIPO_LOG.CLIENTE IS
'Chave de CO_PESSOA. Indica a que cliente o registro está ligado.';

COMMENT ON COLUMN SIW_TIPO_LOG.SQ_MENU IS
'Chave de SIW_MENU. Indica a que serviço o tipo está ligado.';

COMMENT ON COLUMN SIW_TIPO_LOG.NOME IS
'Nome do tipo.';

COMMENT ON COLUMN SIW_TIPO_LOG.SIGLA IS
'Sigla do tipo.';

COMMENT ON COLUMN SIW_TIPO_LOG.ORDEM IS
'Ordem do tipo para exibição em listagens.';

COMMENT ON COLUMN SIW_TIPO_LOG.ATIVO IS
'Indica se o tipo de log pode ser vinculado a novos registros.';

/*==============================================================*/
/* Index: IN_SIWTIPLOG_MENU                                     */
/*==============================================================*/
CREATE  INDEX IN_SIWTIPLOG_MENU ON SIW_TIPO_LOG (
SQ_MENU,
SQ_TIPO_LOG
);

/*==============================================================*/
/* Index: IN_SIWTIPLOG_CLIENTE                                  */
/*==============================================================*/
CREATE  INDEX IN_SIWTIPLOG_CLIENTE ON SIW_TIPO_LOG (
CLIENTE,
SQ_TIPO_LOG
);

/*==============================================================*/
/* Table: SIW_TIPO_RESTRICAO                                    */
/*==============================================================*/
CREATE TABLE SIW_TIPO_RESTRICAO (
   SQ_TIPO_RESTRICAO    NUMERIC(18)          NOT NULL,
   CLIENTE              NUMERIC(18)          NOT NULL,
   NOME                 VARCHAR(20)          NOT NULL,
   ATIVO                VARCHAR(1)           NOT NULL DEFAULT 'S'
      CONSTRAINT CKC_ATIVO_SIWTIPRES CHECK (ATIVO IN ('S','N') AND ATIVO = UPPER(ATIVO)),
   CODIGO_EXTERNO       VARCHAR(30)          NULL,
   CONSTRAINT PK_SIW_TIPO_RESTRICAO PRIMARY KEY (SQ_TIPO_RESTRICAO)
);

COMMENT ON TABLE SIW_TIPO_RESTRICAO IS
'Registra os tipos de restrição.';

COMMENT ON COLUMN SIW_TIPO_RESTRICAO.SQ_TIPO_RESTRICAO IS
'Chave de SIW_TIPO_RESTRICAO.';

COMMENT ON COLUMN SIW_TIPO_RESTRICAO.CLIENTE IS
'Chave de CO_PESSOA. Indica a que cliente o registro está ligado.';

COMMENT ON COLUMN SIW_TIPO_RESTRICAO.NOME IS
'Nome do tipo de restrição.';

COMMENT ON COLUMN SIW_TIPO_RESTRICAO.ATIVO IS
'Indica se o tipo pode ser vinculado a novos registros.';

COMMENT ON COLUMN SIW_TIPO_RESTRICAO.CODIGO_EXTERNO IS
'Código desse registro num sistema externo.';

/*==============================================================*/
/* Index: IN_SIWTIPRES_CLIENTE                                  */
/*==============================================================*/
CREATE  INDEX IN_SIWTIPRES_CLIENTE ON SIW_TIPO_RESTRICAO (
CLIENTE,
SQ_TIPO_RESTRICAO
);

/*==============================================================*/
/* Index: IN_SIWTIPRES_NOME                                     */
/*==============================================================*/
CREATE UNIQUE INDEX IN_SIWTIPRES_NOME ON SIW_TIPO_RESTRICAO (
CLIENTE,
NOME
);

/*==============================================================*/
/* Table: SIW_TRAMITE                                           */
/*==============================================================*/
CREATE TABLE SIW_TRAMITE (
   SQ_SIW_TRAMITE       NUMERIC(18)          NOT NULL,
   SQ_MENU              NUMERIC(18)          NOT NULL,
   NOME                 VARCHAR(50)          NOT NULL,
   ORDEM                NUMERIC(2)           NOT NULL,
   SIGLA                VARCHAR(2)           NOT NULL,
   DESCRICAO            VARCHAR(500)         NULL,
   CHEFIA_IMEDIATA      VARCHAR(1)           NOT NULL DEFAULT 'N'
      CONSTRAINT CKC_SIWTRA_CHEIME
               CHECK (CHEFIA_IMEDIATA IN ('S','N','U','I') AND CHEFIA_IMEDIATA = UPPER(CHEFIA_IMEDIATA)),
   ATIVO                VARCHAR(1)           NOT NULL DEFAULT 'S'
      CONSTRAINT CKC_ATIVO_SIW_TRAM CHECK (ATIVO IN ('S','N') AND ATIVO = UPPER(ATIVO)),
   SOLICITA_CC          VARCHAR(1)           NOT NULL DEFAULT 'N'
      CONSTRAINT CKC_SIWTRA_SOLCC CHECK (SOLICITA_CC IN ('S','N') AND SOLICITA_CC = UPPER(SOLICITA_CC)),
   ENVIA_MAIL           VARCHAR(1)           NOT NULL DEFAULT 'N'
      CONSTRAINT CKC_SIWTRA_MAIL CHECK (ENVIA_MAIL IN ('S','N') AND ENVIA_MAIL = UPPER(ENVIA_MAIL)),
   DESTINATARIO         VARCHAR(1)           NOT NULL DEFAULT 'S'
      CONSTRAINT CKC_DESTINATARIO_SIW_TRAM CHECK (DESTINATARIO IN ('S','N') AND DESTINATARIO = UPPER(DESTINATARIO)),
   ASSINA_TRAMITE_ANTERIOR VARCHAR(1)           NOT NULL DEFAULT 'S'
      CONSTRAINT CKC_ASSINA_TRAMITE_AN_SIW_TRAM CHECK (ASSINA_TRAMITE_ANTERIOR IN ('S','N') AND ASSINA_TRAMITE_ANTERIOR = UPPER(ASSINA_TRAMITE_ANTERIOR)),
   BENEFICIARIO_CUMPRE  VARCHAR(1)           NOT NULL DEFAULT 'S'
      CONSTRAINT CKC_BENEFICIARIO_CUMP_SIW_TRAM CHECK (BENEFICIARIO_CUMPRE IN ('S','N') AND BENEFICIARIO_CUMPRE = UPPER(BENEFICIARIO_CUMPRE)),
   GESTOR_CUMPRE        VARCHAR(1)           NOT NULL DEFAULT 'S'
      CONSTRAINT CKC_GESTOR_CUMPRE_SIW_TRAM CHECK (GESTOR_CUMPRE IN ('S','N') AND GESTOR_CUMPRE = UPPER(GESTOR_CUMPRE)),
   CONSTRAINT PK_SIW_TRAMITE PRIMARY KEY (SQ_SIW_TRAMITE)
);

COMMENT ON TABLE SIW_TRAMITE IS
'Trâmites de um serviço';

COMMENT ON COLUMN SIW_TRAMITE.SQ_SIW_TRAMITE IS
'Chave de SIW_TRAMITE.';

COMMENT ON COLUMN SIW_TRAMITE.SQ_MENU IS
'Chave de SIW_MENU. Indica a que opção do menu o registro está ligado.';

COMMENT ON COLUMN SIW_TRAMITE.NOME IS
'Nome do trâmite.';

COMMENT ON COLUMN SIW_TRAMITE.ORDEM IS
'Armazena a seqüência do trâmite. Seus valores devem ser inteiros consecutivos. O primeiro trâmite deve ter ordem=1, o segundo deve ter ordem=2 etc.

Se um novo trâmite precisar ser inserido entre dois que já existam, basta renumerar a ordem dos trâmites seguintes ao que foi incluído.';

COMMENT ON COLUMN SIW_TRAMITE.SIGLA IS
'Sigla do trâmite.';

COMMENT ON COLUMN SIW_TRAMITE.DESCRICAO IS
'Descrição do trâmite.';

COMMENT ON COLUMN SIW_TRAMITE.CHEFIA_IMEDIATA IS
'Indica quem deverá cumprir este trâmite, podendo ser o chefe imediato, a unidade executora, qualquer pessoa com permissão ou todos os usuários internos. Se for a unidade solicitante ou executora, a solicitação aparecerá para o titular/substituto da unidade e para quaisquer outras pessoas com permissão.';

COMMENT ON COLUMN SIW_TRAMITE.ATIVO IS
'Indica se o registro deve ou não ser exibido.';

COMMENT ON COLUMN SIW_TRAMITE.SOLICITA_CC IS
'Indica se deve ser solicitado um centro de custo ao usuário no cumprimento deste trâmite.';

COMMENT ON COLUMN SIW_TRAMITE.ENVIA_MAIL IS
'Indica se deve ser enviado e-mail aos interessados no cumprimento deste trâmite.';

COMMENT ON COLUMN SIW_TRAMITE.DESTINATARIO IS
'Se igual a S, sempre pedirá destinatário quando um encaminhamento for feito. Caso contrário, aparecerá na mesa de trabalho das pessoas que puderem cumprir o trâmite.';

COMMENT ON COLUMN SIW_TRAMITE.ASSINA_TRAMITE_ANTERIOR IS
'Indica se o trâmite atual pode ser cumprido pela mesma pessoa que cumpriu o trâmite anterior.';

COMMENT ON COLUMN SIW_TRAMITE.BENEFICIARIO_CUMPRE IS
'Indica se o beneficiário da solicitação pode cumprir o trâmite.';

COMMENT ON COLUMN SIW_TRAMITE.GESTOR_CUMPRE IS
'Indica se gestor pode cumprir o trâmite.';

/*==============================================================*/
/* Index: IN_SIWTRA_ORDEM                                       */
/*==============================================================*/
CREATE UNIQUE INDEX IN_SIWTRA_ORDEM ON SIW_TRAMITE (
SQ_MENU,
ORDEM
);

/*==============================================================*/
/* Index: IN_SIWTRA_CHEFIA                                      */
/*==============================================================*/
CREATE  INDEX IN_SIWTRA_CHEFIA ON SIW_TRAMITE (
CHEFIA_IMEDIATA
);

/*==============================================================*/
/* Index: IN_SIWTRA_ATIVO                                       */
/*==============================================================*/
CREATE  INDEX IN_SIWTRA_ATIVO ON SIW_TRAMITE (
ATIVO
);

/*==============================================================*/
/* Table: SIW_TRAMITE_FLUXO                                     */
/*==============================================================*/
CREATE TABLE SIW_TRAMITE_FLUXO (
   SQ_SIW_TRAMITE_ORIGEM NUMERIC(18)          NOT NULL,
   SQ_SIW_TRAMITE_DESTINO NUMERIC(18)          NOT NULL,
   CONSTRAINT PK_SIW_TRAMITE_FLUXO PRIMARY KEY (SQ_SIW_TRAMITE_ORIGEM, SQ_SIW_TRAMITE_DESTINO)
);

COMMENT ON TABLE SIW_TRAMITE_FLUXO IS
'Registra os trâmites de destino possíveis para um trâmite de origem.';

COMMENT ON COLUMN SIW_TRAMITE_FLUXO.SQ_SIW_TRAMITE_ORIGEM IS
'Chave de SIW_TRAMITE. Indica o trâmite de origem.';

COMMENT ON COLUMN SIW_TRAMITE_FLUXO.SQ_SIW_TRAMITE_DESTINO IS
'Chave de SIW_TRAMITE. Indica o trâmite de destino.';

/*==============================================================*/
/* Index: IN_SIWTRAFLUX_INVERSA                                 */
/*==============================================================*/
CREATE  INDEX IN_SIWTRAFLUX_INVERSA ON SIW_TRAMITE_FLUXO (
SQ_SIW_TRAMITE_DESTINO,
SQ_SIW_TRAMITE_ORIGEM
);

ALTER TABLE CO_AGENCIA
   ADD CONSTRAINT FK_COBAN_COAGE FOREIGN KEY (SQ_BANCO)
      REFERENCES CO_BANCO (SQ_BANCO)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE CO_CIDADE
   ADD CONSTRAINT FK_COREG_COCID FOREIGN KEY (SQ_REGIAO)
      REFERENCES CO_REGIAO (SQ_REGIAO)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE CO_CIDADE
   ADD CONSTRAINT FK_COUF_COCID FOREIGN KEY (CO_UF, SQ_PAIS)
      REFERENCES CO_UF (CO_UF, SQ_PAIS)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE CO_PESSOA
   ADD CONSTRAINT FK_COPES_COPES FOREIGN KEY (SQ_PESSOA_PAI)
      REFERENCES CO_PESSOA (SQ_PESSOA)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE CO_PESSOA
   ADD CONSTRAINT FK_COPES_COTIPVIN FOREIGN KEY (SQ_TIPO_VINCULO)
      REFERENCES CO_TIPO_VINCULO (SQ_TIPO_VINCULO)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE CO_PESSOA
   ADD CONSTRAINT FK_COPES_EOREC FOREIGN KEY (SQ_RECURSO)
      REFERENCES EO_RECURSO (SQ_RECURSO)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE CO_PESSOA
   ADD CONSTRAINT FK_COTIPPES_COPES FOREIGN KEY (SQ_TIPO_PESSOA)
      REFERENCES CO_TIPO_PESSOA (SQ_TIPO_PESSOA)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE CO_PESSOA_CONTA
   ADD CONSTRAINT FK_COAGE_COPESCON FOREIGN KEY (SQ_AGENCIA)
      REFERENCES CO_AGENCIA (SQ_AGENCIA)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE CO_PESSOA_CONTA
   ADD CONSTRAINT FK_COPES_COPESCON FOREIGN KEY (SQ_PESSOA)
      REFERENCES CO_PESSOA (SQ_PESSOA)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE CO_PESSOA_ENDERECO
   ADD CONSTRAINT FK_COCID_COPESEND FOREIGN KEY (SQ_CIDADE)
      REFERENCES CO_CIDADE (SQ_CIDADE)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE CO_PESSOA_ENDERECO
   ADD CONSTRAINT FK_COPES_COPESEND FOREIGN KEY (SQ_PESSOA)
      REFERENCES CO_PESSOA (SQ_PESSOA)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE CO_PESSOA_ENDERECO
   ADD CONSTRAINT FK_COTPEND_COPESEN FOREIGN KEY (SQ_TIPO_ENDERECO)
      REFERENCES CO_TIPO_ENDERECO (SQ_TIPO_ENDERECO)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE CO_PESSOA_FISICA
   ADD CONSTRAINT FK_COCID_COPESFIS FOREIGN KEY (SQ_CIDADE_NASC)
      REFERENCES CO_CIDADE (SQ_CIDADE)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE CO_PESSOA_FISICA
   ADD CONSTRAINT FK_COPAI_COPESFIS FOREIGN KEY (SQ_PAIS_PASSAPORTE)
      REFERENCES CO_PAIS (SQ_PAIS)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE CO_PESSOA_FISICA
   ADD CONSTRAINT FK_COPESFIS_SIWCLI FOREIGN KEY (CLIENTE)
      REFERENCES SIW_CLIENTE (SQ_PESSOA)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE CO_PESSOA_FISICA
   ADD CONSTRAINT FK_COPES_COPESFIS FOREIGN KEY (SQ_PESSOA)
      REFERENCES CO_PESSOA (SQ_PESSOA)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE CO_PESSOA_JURIDICA
   ADD CONSTRAINT FK_COPESJUR_SIWCLI FOREIGN KEY (CLIENTE)
      REFERENCES SIW_CLIENTE (SQ_PESSOA)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE CO_PESSOA_JURIDICA
   ADD CONSTRAINT FK_COPES_COPESJUR FOREIGN KEY (SQ_PESSOA)
      REFERENCES CO_PESSOA (SQ_PESSOA)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE CO_PESSOA_SEGMENTO
   ADD CONSTRAINT FK_COPES_COPESSEG FOREIGN KEY (SQ_PESSOA)
      REFERENCES CO_PESSOA (SQ_PESSOA)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE CO_PESSOA_SEGMENTO
   ADD CONSTRAINT FK_COSEG_COPESSEG FOREIGN KEY (SQ_SEGMENTO)
      REFERENCES CO_SEGMENTO (SQ_SEGMENTO)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE CO_PESSOA_TELEFONE
   ADD CONSTRAINT FK_COCID_COPESTEL FOREIGN KEY (SQ_CIDADE)
      REFERENCES CO_CIDADE (SQ_CIDADE)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE CO_PESSOA_TELEFONE
   ADD CONSTRAINT FK_COPES_COPESTEL FOREIGN KEY (SQ_PESSOA)
      REFERENCES CO_PESSOA (SQ_PESSOA)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE CO_PESSOA_TELEFONE
   ADD CONSTRAINT FK_COTPTEL_COPESTL FOREIGN KEY (SQ_TIPO_TELEFONE)
      REFERENCES CO_TIPO_TELEFONE (SQ_TIPO_TELEFONE)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE CO_REGIAO
   ADD CONSTRAINT FK_COPAI_COREG FOREIGN KEY (SQ_PAIS)
      REFERENCES CO_PAIS (SQ_PAIS)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE CO_TIPO_ENDERECO
   ADD CONSTRAINT FK_COTPPES_COTIPEN FOREIGN KEY (SQ_TIPO_PESSOA)
      REFERENCES CO_TIPO_PESSOA (SQ_TIPO_PESSOA)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE CO_TIPO_TELEFONE
   ADD CONSTRAINT FK_COTPPES_COTPTL FOREIGN KEY (SQ_TIPO_PESSOA)
      REFERENCES CO_TIPO_PESSOA (SQ_TIPO_PESSOA)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE CO_TIPO_VINCULO
   ADD CONSTRAINT FK_COTIPVIN_SIWCLI FOREIGN KEY (CLIENTE)
      REFERENCES SIW_CLIENTE (SQ_PESSOA)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE CO_TIPO_VINCULO
   ADD CONSTRAINT FK_COTPPES_COTPVIN FOREIGN KEY (SQ_TIPO_PESSOA)
      REFERENCES CO_TIPO_PESSOA (SQ_TIPO_PESSOA)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE CO_UF
   ADD CONSTRAINT FK_COPAI_COUF FOREIGN KEY (SQ_PAIS)
      REFERENCES CO_PAIS (SQ_PAIS)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE CO_UF
   ADD CONSTRAINT FK_COREG_COUF FOREIGN KEY (SQ_REGIAO)
      REFERENCES CO_REGIAO (SQ_REGIAO)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE CO_UNIDADE_MEDIDA
   ADD CONSTRAINT FK_COUNIMED_COPES FOREIGN KEY (CLIENTE)
      REFERENCES CO_PESSOA (SQ_PESSOA)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE DC_ARQUIVO
   ADD CONSTRAINT FK_DCARQ_DCSIS FOREIGN KEY (SQ_SISTEMA)
      REFERENCES DC_SISTEMA (SQ_SISTEMA)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE DC_COLUNA
   ADD CONSTRAINT FK_DCCOL_DCDADTIP FOREIGN KEY (SQ_DADO_TIPO)
      REFERENCES DC_DADO_TIPO (SQ_DADO_TIPO)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE DC_COLUNA
   ADD CONSTRAINT FK_DCCOL_DCTAB FOREIGN KEY (SQ_TABELA)
      REFERENCES DC_TABELA (SQ_TABELA)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE DC_ESQUEMA
   ADD CONSTRAINT FK_DCESQ_COPES FOREIGN KEY (CLIENTE)
      REFERENCES CO_PESSOA (SQ_PESSOA)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE DC_ESQUEMA
   ADD CONSTRAINT FK_DCESQ_SIWMOD FOREIGN KEY (SQ_MODULO)
      REFERENCES SIW_MODULO (SQ_MODULO)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE DC_ESQUEMA_ATRIBUTO
   ADD CONSTRAINT FK_DCESQATR_DCCOL FOREIGN KEY (SQ_COLUNA)
      REFERENCES DC_COLUNA (SQ_COLUNA)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE DC_ESQUEMA_ATRIBUTO
   ADD CONSTRAINT FK_DCMAP_DCINT FOREIGN KEY (SQ_ESQUEMA_TABELA)
      REFERENCES DC_ESQUEMA_TABELA (SQ_ESQUEMA_TABELA)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE DC_ESQUEMA_INSERT
   ADD CONSTRAINT FK_DCESQINS_DCCOL FOREIGN KEY (SQ_COLUNA)
      REFERENCES DC_COLUNA (SQ_COLUNA)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE DC_ESQUEMA_INSERT
   ADD CONSTRAINT FK_DCESQINS_DCESQTAB FOREIGN KEY (SQ_ESQUEMA_TABELA)
      REFERENCES DC_ESQUEMA_TABELA (SQ_ESQUEMA_TABELA)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE DC_ESQUEMA_SCRIPT
   ADD CONSTRAINT FK_DCESQSCR_DCESQ FOREIGN KEY (SQ_ESQUEMA)
      REFERENCES DC_ESQUEMA (SQ_ESQUEMA)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE DC_ESQUEMA_SCRIPT
   ADD CONSTRAINT FK_DCESQSCR_SIWARQ FOREIGN KEY (SQ_SIW_ARQUIVO)
      REFERENCES SIW_ARQUIVO (SQ_SIW_ARQUIVO)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE DC_ESQUEMA_TABELA
   ADD CONSTRAINT FK_DCESQTAB_DCESQ FOREIGN KEY (SQ_ESQUEMA)
      REFERENCES DC_ESQUEMA (SQ_ESQUEMA)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE DC_ESQUEMA_TABELA
   ADD CONSTRAINT FK_DCINT_DCTAB FOREIGN KEY (SQ_TABELA)
      REFERENCES DC_TABELA (SQ_TABELA)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE DC_INDICE
   ADD CONSTRAINT FK_DCIND_DCINDTIP FOREIGN KEY (SQ_INDICE_TIPO)
      REFERENCES DC_INDICE_TIPO (SQ_INDICE_TIPO)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE DC_INDICE
   ADD CONSTRAINT FK_DCIND_DCSIS FOREIGN KEY (SQ_SISTEMA)
      REFERENCES DC_SISTEMA (SQ_SISTEMA)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE DC_INDICE
   ADD CONSTRAINT FK_DCIND_DCUSU FOREIGN KEY (SQ_USUARIO)
      REFERENCES DC_USUARIO (SQ_USUARIO)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE DC_INDICE_COLS
   ADD CONSTRAINT FK_DCINDCOL_DCCOL FOREIGN KEY (SQ_COLUNA)
      REFERENCES DC_COLUNA (SQ_COLUNA)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE DC_INDICE_COLS
   ADD CONSTRAINT FK_DCINDCOL_DCIND FOREIGN KEY (SQ_INDICE)
      REFERENCES DC_INDICE (SQ_INDICE)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE DC_OCORRENCIA
   ADD CONSTRAINT FK_DCOCO_COPES FOREIGN KEY (SQ_PESSOA)
      REFERENCES CO_PESSOA (SQ_PESSOA)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE DC_OCORRENCIA
   ADD CONSTRAINT FK_DCOCO_DCESQ FOREIGN KEY (SQ_ESQUEMA)
      REFERENCES DC_ESQUEMA (SQ_ESQUEMA)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE DC_OCORRENCIA
   ADD CONSTRAINT FK_DCOCO_SIWARQ_PROCESSADO FOREIGN KEY (ARQUIVO_PROCESSAMENTO)
      REFERENCES SIW_ARQUIVO (SQ_SIW_ARQUIVO)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE DC_OCORRENCIA
   ADD CONSTRAINT FK_DCOCO_SIWARQ_REJEICAO FOREIGN KEY (ARQUIVO_REJEICAO)
      REFERENCES SIW_ARQUIVO (SQ_SIW_ARQUIVO)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE DC_PROCEDURE
   ADD CONSTRAINT FK_DCPROC_DCARQ FOREIGN KEY (SQ_ARQUIVO)
      REFERENCES DC_ARQUIVO (SQ_ARQUIVO)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE DC_PROCEDURE
   ADD CONSTRAINT FK_DCPROC_DCSIS FOREIGN KEY (SQ_SISTEMA)
      REFERENCES DC_SISTEMA (SQ_SISTEMA)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE DC_PROCEDURE
   ADD CONSTRAINT FK_DCPROC_DCSPTIP FOREIGN KEY (SQ_SP_TIPO)
      REFERENCES DC_SP_TIPO (SQ_SP_TIPO)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE DC_PROC_PARAM
   ADD CONSTRAINT FK_DCPROPAR_DCDADTIP FOREIGN KEY (SQ_DADO_TIPO)
      REFERENCES DC_DADO_TIPO (SQ_DADO_TIPO)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE DC_PROC_PARAM
   ADD CONSTRAINT FK_DCPROPAR_DCPRO FOREIGN KEY (SQ_PROCEDURE)
      REFERENCES DC_PROCEDURE (SQ_PROCEDURE)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE DC_PROC_SP
   ADD CONSTRAINT FK_DCPROSP_DCPRO FOREIGN KEY (SQ_PROCEDURE)
      REFERENCES DC_PROCEDURE (SQ_PROCEDURE)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE DC_PROC_SP
   ADD CONSTRAINT FK_DCPROSP_DCSTOPRO FOREIGN KEY (SQ_STORED_PROC)
      REFERENCES DC_STORED_PROC (SQ_STORED_PROC)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE DC_PROC_TABELA
   ADD CONSTRAINT FK_DCPROTAB_DCPRO FOREIGN KEY (SQ_PROCEDURE)
      REFERENCES DC_PROCEDURE (SQ_PROCEDURE)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE DC_PROC_TABELA
   ADD CONSTRAINT FK_DCPROTAB_DCTAB FOREIGN KEY (SQ_TABELA)
      REFERENCES DC_TABELA (SQ_TABELA)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE DC_RELACIONAMENTO
   ADD CONSTRAINT FK_DCREL_DCSIS FOREIGN KEY (SQ_SISTEMA)
      REFERENCES DC_SISTEMA (SQ_SISTEMA)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE DC_RELACIONAMENTO
   ADD CONSTRAINT FK_DCREL_DCTAB_FILHA FOREIGN KEY (TABELA_FILHA)
      REFERENCES DC_TABELA (SQ_TABELA)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE DC_RELACIONAMENTO
   ADD CONSTRAINT FK_DCREL_DCTAB_PAI FOREIGN KEY (TABELA_PAI)
      REFERENCES DC_TABELA (SQ_TABELA)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE DC_RELAC_COLS
   ADD CONSTRAINT FK_DCRELCOL_DCCOL_FILHA FOREIGN KEY (COLUNA_FILHA)
      REFERENCES DC_COLUNA (SQ_COLUNA)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE DC_RELAC_COLS
   ADD CONSTRAINT FK_DCRELCOL_DCCOL_PAI FOREIGN KEY (COLUNA_PAI)
      REFERENCES DC_COLUNA (SQ_COLUNA)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE DC_RELAC_COLS
   ADD CONSTRAINT FK_DCRELCOL_DCREL FOREIGN KEY (SQ_RELACIONAMENTO)
      REFERENCES DC_RELACIONAMENTO (SQ_RELACIONAMENTO)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE DC_SISTEMA
   ADD CONSTRAINT FK_DCSIS_COPES FOREIGN KEY (CLIENTE)
      REFERENCES CO_PESSOA (SQ_PESSOA)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE DC_SP_PARAM
   ADD CONSTRAINT FK_DCSPPAR_DCDADTIP FOREIGN KEY (SQ_DADO_TIPO)
      REFERENCES DC_DADO_TIPO (SQ_DADO_TIPO)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE DC_SP_PARAM
   ADD CONSTRAINT FK_DCSPPAR_DCSTOPRO FOREIGN KEY (SQ_STORED_PROC)
      REFERENCES DC_STORED_PROC (SQ_STORED_PROC)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE DC_SP_SP
   ADD CONSTRAINT FK_DCSPSP_DCSTOPRO_FILHA FOREIGN KEY (SP_FILHA)
      REFERENCES DC_STORED_PROC (SQ_STORED_PROC)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE DC_SP_SP
   ADD CONSTRAINT FK_DCSPSP_DCSTOPRO_PAI FOREIGN KEY (SP_PAI)
      REFERENCES DC_STORED_PROC (SQ_STORED_PROC)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE DC_SP_TABS
   ADD CONSTRAINT FK_DCSPTAB_DCTAB FOREIGN KEY (SQ_TABELA)
      REFERENCES DC_TABELA (SQ_TABELA)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE DC_SP_TABS
   ADD CONSTRAINT FK_SPTAB_DCSTOPRO FOREIGN KEY (SQ_STORED_PROC)
      REFERENCES DC_STORED_PROC (SQ_STORED_PROC)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE DC_STORED_PROC
   ADD CONSTRAINT FK_DCSTOPRO_DCSIS FOREIGN KEY (SQ_SISTEMA)
      REFERENCES DC_SISTEMA (SQ_SISTEMA)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE DC_STORED_PROC
   ADD CONSTRAINT FK_DCSTOPRO_DCUSU FOREIGN KEY (SQ_USUARIO)
      REFERENCES DC_USUARIO (SQ_USUARIO)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE DC_STORED_PROC
   ADD CONSTRAINT FK_STOPRO_SPTIP FOREIGN KEY (SQ_SP_TIPO)
      REFERENCES DC_SP_TIPO (SQ_SP_TIPO)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE DC_TABELA
   ADD CONSTRAINT FK_DCTAB_DCSIS FOREIGN KEY (SQ_SISTEMA)
      REFERENCES DC_SISTEMA (SQ_SISTEMA)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE DC_TABELA
   ADD CONSTRAINT FK_DCTAB_DCTABTIP FOREIGN KEY (SQ_TABELA_TIPO)
      REFERENCES DC_TABELA_TIPO (SQ_TABELA_TIPO)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE DC_TABELA
   ADD CONSTRAINT FK_DCTAB_DCUSU FOREIGN KEY (SQ_USUARIO)
      REFERENCES DC_USUARIO (SQ_USUARIO)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE DC_TRIGGER
   ADD CONSTRAINT FK_DCTRI_DCSIS FOREIGN KEY (SQ_SISTEMA)
      REFERENCES DC_SISTEMA (SQ_SISTEMA)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE DC_TRIGGER
   ADD CONSTRAINT FK_DCTRI_DCTAB FOREIGN KEY (SQ_TABELA)
      REFERENCES DC_TABELA (SQ_TABELA)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE DC_TRIGGER
   ADD CONSTRAINT FK_DCTRI_DCUSU FOREIGN KEY (SQ_USUARIO)
      REFERENCES DC_USUARIO (SQ_USUARIO)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE DC_TRIGGER_EVENTO
   ADD CONSTRAINT FK_DCTRIEVE_DCEVE FOREIGN KEY (SQ_EVENTO)
      REFERENCES DC_EVENTO (SQ_EVENTO)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE DC_TRIGGER_EVENTO
   ADD CONSTRAINT FK_DCTRIEVE_DCTRI FOREIGN KEY (SQ_TRIGGER)
      REFERENCES DC_TRIGGER (SQ_TRIGGER)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE DC_USUARIO
   ADD CONSTRAINT FK_DCUSU_DCSIS FOREIGN KEY (SQ_SISTEMA)
      REFERENCES DC_SISTEMA (SQ_SISTEMA)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE DM_SEGMENTO_MENU
   ADD CONSTRAINT FK_DMSEGMEN_EOUNI FOREIGN KEY (SQ_UNID_EXECUTORA)
      REFERENCES EO_UNIDADE (SQ_UNIDADE)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE DM_SEGMENTO_MENU
   ADD CONSTRAINT FK_DSGMN_DSGMN_PAI FOREIGN KEY (SQ_SEG_MENU_PAI)
      REFERENCES DM_SEGMENTO_MENU (SQ_SEGMENTO_MENU)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE DM_SEGMENTO_MENU
   ADD CONSTRAINT FK_SIWMODSG_DSGMEN FOREIGN KEY (SQ_MODULO, SQ_SEGMENTO)
      REFERENCES SIW_MOD_SEG (SQ_MODULO, SQ_SEGMENTO)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE DM_SEG_VINCULO
   ADD CONSTRAINT FK_COSEG_DMSEGVIN FOREIGN KEY (SQ_SEGMENTO)
      REFERENCES CO_SEGMENTO (SQ_SEGMENTO)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE DM_SEG_VINCULO
   ADD CONSTRAINT FK_COTPES_DSGVIN FOREIGN KEY (SQ_TIPO_PESSOA)
      REFERENCES CO_TIPO_PESSOA (SQ_TIPO_PESSOA)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE EO_AREA_ATUACAO
   ADD CONSTRAINT FK_EOAREATU_COPES FOREIGN KEY (SQ_PESSOA)
      REFERENCES CO_PESSOA (SQ_PESSOA)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE EO_DATA_ESPECIAL
   ADD CONSTRAINT FK_EODATESP_COCID FOREIGN KEY (SQ_CIDADE)
      REFERENCES CO_CIDADE (SQ_CIDADE)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE EO_DATA_ESPECIAL
   ADD CONSTRAINT FK_EODATESP_COPAI FOREIGN KEY (SQ_PAIS)
      REFERENCES CO_PAIS (SQ_PAIS)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE EO_DATA_ESPECIAL
   ADD CONSTRAINT FK_EODATESP_COPES FOREIGN KEY (CLIENTE)
      REFERENCES CO_PESSOA (SQ_PESSOA)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE EO_DATA_ESPECIAL
   ADD CONSTRAINT FK_EODATESP_COUF FOREIGN KEY (CO_UF, SQ_PAIS)
      REFERENCES CO_UF (CO_UF, SQ_PAIS)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE EO_INDICADOR
   ADD CONSTRAINT FK_EOIND_COPES FOREIGN KEY (CLIENTE)
      REFERENCES CO_PESSOA (SQ_PESSOA)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE EO_INDICADOR
   ADD CONSTRAINT FK_EOIND_COUNIMED FOREIGN KEY (SQ_UNIDADE_MEDIDA)
      REFERENCES CO_UNIDADE_MEDIDA (SQ_UNIDADE_MEDIDA)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE EO_INDICADOR
   ADD CONSTRAINT FK_EOIND_EOTIPIND FOREIGN KEY (SQ_TIPO_INDICADOR)
      REFERENCES EO_TIPO_INDICADOR (SQ_TIPO_INDICADOR)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE EO_INDICADOR_AFERICAO
   ADD CONSTRAINT FK_EOINDAFE_COCID FOREIGN KEY (SQ_CIDADE)
      REFERENCES CO_CIDADE (SQ_CIDADE)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE EO_INDICADOR_AFERICAO
   ADD CONSTRAINT FK_EOINDAFE_COPAI FOREIGN KEY (SQ_PAIS)
      REFERENCES CO_PAIS (SQ_PAIS)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE EO_INDICADOR_AFERICAO
   ADD CONSTRAINT FK_EOINDAFE_COPES FOREIGN KEY (CADASTRADOR)
      REFERENCES CO_PESSOA (SQ_PESSOA)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE EO_INDICADOR_AFERICAO
   ADD CONSTRAINT FK_EOINDAFE_COREG FOREIGN KEY (SQ_REGIAO)
      REFERENCES CO_REGIAO (SQ_REGIAO)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE EO_INDICADOR_AFERICAO
   ADD CONSTRAINT FK_EOINDAFE_COUF FOREIGN KEY (CO_UF, SQ_PAIS)
      REFERENCES CO_UF (CO_UF, SQ_PAIS)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE EO_INDICADOR_AFERICAO
   ADD CONSTRAINT FK_EOINDAFE_EOIND FOREIGN KEY (SQ_EOINDICADOR)
      REFERENCES EO_INDICADOR (SQ_EOINDICADOR)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE EO_INDICADOR_AFERIDOR
   ADD CONSTRAINT FK_EOINDAFR_COPES FOREIGN KEY (SQ_PESSOA)
      REFERENCES CO_PESSOA (SQ_PESSOA)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE EO_INDICADOR_AFERIDOR
   ADD CONSTRAINT FK_EOINDAFR_EOIND FOREIGN KEY (SQ_EOINDICADOR)
      REFERENCES EO_INDICADOR (SQ_EOINDICADOR)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE EO_INDICADOR_AGENDA
   ADD CONSTRAINT FK_EOINDAGE_EOIND FOREIGN KEY (SQ_EOINDICADOR)
      REFERENCES EO_INDICADOR (SQ_EOINDICADOR)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE EO_LOCALIZACAO
   ADD CONSTRAINT FK_EOLOC_COPES FOREIGN KEY (CLIENTE)
      REFERENCES CO_PESSOA (SQ_PESSOA)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE EO_LOCALIZACAO
   ADD CONSTRAINT FK_EOLOC_COPESEND FOREIGN KEY (SQ_PESSOA_ENDERECO)
      REFERENCES CO_PESSOA_ENDERECO (SQ_PESSOA_ENDERECO)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE EO_LOCALIZACAO
   ADD CONSTRAINT FK_EOLOC_EOUNI FOREIGN KEY (SQ_UNIDADE)
      REFERENCES EO_UNIDADE (SQ_UNIDADE)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE EO_RECURSO
   ADD CONSTRAINT FK_EOREC_COPES FOREIGN KEY (CLIENTE)
      REFERENCES CO_PESSOA (SQ_PESSOA)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE EO_RECURSO
   ADD CONSTRAINT FK_EOREC_COUNIMED FOREIGN KEY (SQ_UNIDADE_MEDIDA)
      REFERENCES CO_UNIDADE_MEDIDA (SQ_UNIDADE_MEDIDA)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE EO_RECURSO
   ADD CONSTRAINT FK_EOREC_EOTIPREC FOREIGN KEY (SQ_TIPO_RECURSO)
      REFERENCES EO_TIPO_RECURSO (SQ_TIPO_RECURSO)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE EO_RECURSO
   ADD CONSTRAINT FK_EOREC_EOUNI FOREIGN KEY (UNIDADE_GESTORA)
      REFERENCES EO_UNIDADE (SQ_UNIDADE)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE EO_RECURSO_DISPONIVEL
   ADD CONSTRAINT FK_EORECDIS_EOREC FOREIGN KEY (SQ_RECURSO)
      REFERENCES EO_RECURSO (SQ_RECURSO)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE EO_RECURSO_INDISPONIVEL
   ADD CONSTRAINT FK_EORECIND_EOREC FOREIGN KEY (SQ_RECURSO)
      REFERENCES EO_RECURSO (SQ_RECURSO)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE EO_RECURSO_MENU
   ADD CONSTRAINT FK_EORECMEN_EOREC FOREIGN KEY (SQ_RECURSO)
      REFERENCES EO_RECURSO (SQ_RECURSO)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE EO_RECURSO_MENU
   ADD CONSTRAINT FK_EORECMEN_SIWMEN FOREIGN KEY (SQ_MENU)
      REFERENCES SIW_MENU (SQ_MENU)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE EO_TIPO_INDICADOR
   ADD CONSTRAINT FK_EOTIPIND_COPES FOREIGN KEY (CLIENTE)
      REFERENCES CO_PESSOA (SQ_PESSOA)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE EO_TIPO_RECURSO
   ADD CONSTRAINT FK_EOTIPREC_COPES FOREIGN KEY (CLIENTE)
      REFERENCES CO_PESSOA (SQ_PESSOA)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE EO_TIPO_RECURSO
   ADD CONSTRAINT FK_EOTIPREC_EOTIPREC FOREIGN KEY (SQ_TIPO_PAI)
      REFERENCES EO_TIPO_RECURSO (SQ_TIPO_RECURSO)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE EO_TIPO_RECURSO
   ADD CONSTRAINT FK_EOTIPREC_EOUNI FOREIGN KEY (UNIDADE_GESTORA)
      REFERENCES EO_UNIDADE (SQ_UNIDADE)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE EO_TIPO_UNIDADE
   ADD CONSTRAINT FK_EOTIPUNI_COPES FOREIGN KEY (SQ_PESSOA)
      REFERENCES CO_PESSOA (SQ_PESSOA)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE EO_UNIDADE
   ADD CONSTRAINT FK_EOUNI_COPES FOREIGN KEY (SQ_PESSOA)
      REFERENCES CO_PESSOA (SQ_PESSOA)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE EO_UNIDADE
   ADD CONSTRAINT FK_EOUNI_COPESEND FOREIGN KEY (SQ_PESSOA_ENDERECO)
      REFERENCES CO_PESSOA_ENDERECO (SQ_PESSOA_ENDERECO)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE EO_UNIDADE
   ADD CONSTRAINT FK_EOUNI_EOAREATU FOREIGN KEY (SQ_AREA_ATUACAO)
      REFERENCES EO_AREA_ATUACAO (SQ_AREA_ATUACAO)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE EO_UNIDADE
   ADD CONSTRAINT FK_EOUNI_EOTIPUNI FOREIGN KEY (SQ_TIPO_UNIDADE)
      REFERENCES EO_TIPO_UNIDADE (SQ_TIPO_UNIDADE)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE EO_UNIDADE
   ADD CONSTRAINT FK_EOUNI_EOUNI_GES FOREIGN KEY (SQ_UNIDADE_GESTORA)
      REFERENCES EO_UNIDADE (SQ_UNIDADE)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE EO_UNIDADE
   ADD CONSTRAINT FK_EOUNI_EOUNI_PAG FOREIGN KEY (SQ_UNID_PAGADORA)
      REFERENCES EO_UNIDADE (SQ_UNIDADE)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE EO_UNIDADE
   ADD CONSTRAINT FK_EOUNI_EOUNI_PAI FOREIGN KEY (SQ_UNIDADE_PAI)
      REFERENCES EO_UNIDADE (SQ_UNIDADE)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE EO_UNIDADE_ARQUIVO
   ADD CONSTRAINT FK_EOUNIARQ_EOUNI FOREIGN KEY (SQ_UNIDADE)
      REFERENCES EO_UNIDADE (SQ_UNIDADE)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE EO_UNIDADE_ARQUIVO
   ADD CONSTRAINT FK_EOUNIARQ_SIWARQ FOREIGN KEY (SQ_SIW_ARQUIVO)
      REFERENCES SIW_ARQUIVO (SQ_SIW_ARQUIVO)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE EO_UNIDADE_RESP
   ADD CONSTRAINT FK_EOUNIRES_COPES FOREIGN KEY (SQ_PESSOA)
      REFERENCES CO_PESSOA (SQ_PESSOA)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE EO_UNIDADE_RESP
   ADD CONSTRAINT FK_EOUNIRES_EOUNI FOREIGN KEY (SQ_UNIDADE)
      REFERENCES EO_UNIDADE (SQ_UNIDADE)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE GD_DEMANDA
   ADD CONSTRAINT FK_GDDEM_COPES FOREIGN KEY (RESPONSAVEL)
      REFERENCES CO_PESSOA (SQ_PESSOA)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE GD_DEMANDA
   ADD CONSTRAINT FK_GDDEM_EOUNI FOREIGN KEY (SQ_UNIDADE_RESP)
      REFERENCES EO_UNIDADE (SQ_UNIDADE)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE GD_DEMANDA
   ADD CONSTRAINT FK_GDDEM_GDDEM FOREIGN KEY (SQ_DEMANDA_PAI)
      REFERENCES GD_DEMANDA (SQ_SIW_SOLICITACAO)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE GD_DEMANDA
   ADD CONSTRAINT FK_GDDEM_GDDEMTIPO FOREIGN KEY (SQ_DEMANDA_TIPO)
      REFERENCES GD_DEMANDA_TIPO (SQ_DEMANDA_TIPO)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE GD_DEMANDA
   ADD CONSTRAINT FK_GDDEM_SIWRES FOREIGN KEY (SQ_SIW_RESTRICAO)
      REFERENCES SIW_RESTRICAO (SQ_SIW_RESTRICAO)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE GD_DEMANDA
   ADD CONSTRAINT FK_GDDEM_SIWSOL FOREIGN KEY (SQ_SIW_SOLICITACAO)
      REFERENCES SIW_SOLICITACAO (SQ_SIW_SOLICITACAO)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE GD_DEMANDA_ENVOLV
   ADD CONSTRAINT FK_GDDEMENV_EOUNI FOREIGN KEY (SQ_UNIDADE)
      REFERENCES EO_UNIDADE (SQ_UNIDADE)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE GD_DEMANDA_ENVOLV
   ADD CONSTRAINT FK_GDDEMENV_GDDEM FOREIGN KEY (SQ_SIW_SOLICITACAO)
      REFERENCES GD_DEMANDA (SQ_SIW_SOLICITACAO)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE GD_DEMANDA_INTERES
   ADD CONSTRAINT FK_GDDEMINT_COPES FOREIGN KEY (SQ_PESSOA)
      REFERENCES CO_PESSOA (SQ_PESSOA)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE GD_DEMANDA_INTERES
   ADD CONSTRAINT FK_GDDEMINT_GDDEM FOREIGN KEY (SQ_SIW_SOLICITACAO)
      REFERENCES GD_DEMANDA (SQ_SIW_SOLICITACAO)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE GD_DEMANDA_LOG
   ADD CONSTRAINT FK_GDDEMLOG_GDDEM FOREIGN KEY (SQ_SIW_SOLICITACAO)
      REFERENCES GD_DEMANDA (SQ_SIW_SOLICITACAO)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE GD_DEMANDA_LOG
   ADD CONSTRAINT FK_GDDMLG_COPS_CAD FOREIGN KEY (CADASTRADOR)
      REFERENCES CO_PESSOA (SQ_PESSOA)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE GD_DEMANDA_LOG
   ADD CONSTRAINT FK_GDDMLG_COPS_DEM FOREIGN KEY (DESTINATARIO)
      REFERENCES CO_PESSOA (SQ_PESSOA)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE GD_DEMANDA_LOG
   ADD CONSTRAINT FK_GDLOG_SIWLOG FOREIGN KEY (SQ_SIW_SOLIC_LOG)
      REFERENCES SIW_SOLIC_LOG (SQ_SIW_SOLIC_LOG)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE GD_DEMANDA_LOG_ARQ
   ADD CONSTRAINT FK_GDDEMLOGARQ_GDDEMLOG FOREIGN KEY (SQ_DEMANDA_LOG)
      REFERENCES GD_DEMANDA_LOG (SQ_DEMANDA_LOG)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE GD_DEMANDA_LOG_ARQ
   ADD CONSTRAINT FK_GDDEMLOGARQ_SIWARQ FOREIGN KEY (SQ_SIW_ARQUIVO)
      REFERENCES SIW_ARQUIVO (SQ_SIW_ARQUIVO)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE GD_DEMANDA_TIPO
   ADD CONSTRAINT FK_GDDEMTIP_COPES FOREIGN KEY (CLIENTE)
      REFERENCES CO_PESSOA (SQ_PESSOA)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE GD_DEMANDA_TIPO
   ADD CONSTRAINT FK_GDDEMTIP_EOUNI FOREIGN KEY (SQ_UNIDADE)
      REFERENCES EO_UNIDADE (SQ_UNIDADE)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE PE_HORIZONTE
   ADD CONSTRAINT FK_PEHOR_COPES FOREIGN KEY (CLIENTE)
      REFERENCES CO_PESSOA (SQ_PESSOA)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE PE_NATUREZA
   ADD CONSTRAINT FK_PENAT_COPES FOREIGN KEY (CLIENTE)
      REFERENCES CO_PESSOA (SQ_PESSOA)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE PE_OBJETIVO
   ADD CONSTRAINT FK_PEOBJ_COPES FOREIGN KEY (CLIENTE)
      REFERENCES CO_PESSOA (SQ_PESSOA)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE PE_OBJETIVO
   ADD CONSTRAINT FK_PEOBJ_PEPLA FOREIGN KEY (SQ_PLANO)
      REFERENCES PE_PLANO (SQ_PLANO)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE PE_PLANO
   ADD CONSTRAINT FK_PEPLA_COPES FOREIGN KEY (CLIENTE)
      REFERENCES CO_PESSOA (SQ_PESSOA)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE PE_PLANO
   ADD CONSTRAINT FK_PEPLA_PEPLA FOREIGN KEY (SQ_PLANO_PAI)
      REFERENCES PE_PLANO (SQ_PLANO)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE PE_PLANO_ARQ
   ADD CONSTRAINT FK_PEPLAARQ_PEPLA FOREIGN KEY (SQ_PLANO)
      REFERENCES PE_PLANO (SQ_PLANO)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE PE_PLANO_ARQ
   ADD CONSTRAINT FK_PEPLAARQ_SIWARQ FOREIGN KEY (SQ_SIW_ARQUIVO)
      REFERENCES SIW_ARQUIVO (SQ_SIW_ARQUIVO)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE PE_PLANO_INDICADOR
   ADD CONSTRAINT FK_PEPLANIND_EOIND FOREIGN KEY (SQ_EOINDICADOR)
      REFERENCES EO_INDICADOR (SQ_EOINDICADOR)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE PE_PLANO_INDICADOR
   ADD CONSTRAINT FK_PEPLANIND_PEPLAN FOREIGN KEY (SQ_PLANO)
      REFERENCES PE_PLANO (SQ_PLANO)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE PE_PLANO_MENU
   ADD CONSTRAINT FK_PEPLANMEN_PEPLA FOREIGN KEY (SQ_PLANO)
      REFERENCES PE_PLANO (SQ_PLANO)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE PE_PLANO_MENU
   ADD CONSTRAINT FK_PEPLANMEN_SIWMEN FOREIGN KEY (SQ_MENU)
      REFERENCES SIW_MENU (SQ_MENU)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE PE_PROGRAMA
   ADD CONSTRAINT FK_PEPRO_COPES FOREIGN KEY (CLIENTE)
      REFERENCES CO_PESSOA (SQ_PESSOA)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE PE_PROGRAMA
   ADD CONSTRAINT FK_PEPRO_EOUNI FOREIGN KEY (SQ_UNIDADE_RESP)
      REFERENCES EO_UNIDADE (SQ_UNIDADE)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE PE_PROGRAMA
   ADD CONSTRAINT FK_PEPRO_PEHOR FOREIGN KEY (SQ_PEHORIZONTE)
      REFERENCES PE_HORIZONTE (SQ_PEHORIZONTE)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE PE_PROGRAMA
   ADD CONSTRAINT FK_PEPRO_PENAT FOREIGN KEY (SQ_PENATUREZA)
      REFERENCES PE_NATUREZA (SQ_PENATUREZA)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE PE_PROGRAMA
   ADD CONSTRAINT FK_PEPRO_SIWSOL FOREIGN KEY (SQ_SIW_SOLICITACAO)
      REFERENCES SIW_SOLICITACAO (SQ_SIW_SOLICITACAO)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE PE_PROGRAMA_LOG
   ADD CONSTRAINT FK_PEPROLOG_COPES_CAD FOREIGN KEY (CADASTRADOR)
      REFERENCES CO_PESSOA (SQ_PESSOA)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE PE_PROGRAMA_LOG
   ADD CONSTRAINT FK_PEPROLOG_DEST FOREIGN KEY (DESTINATARIO)
      REFERENCES CO_PESSOA (SQ_PESSOA)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE PE_PROGRAMA_LOG
   ADD CONSTRAINT FK_PEPROLOG_PEPRO FOREIGN KEY (SQ_SIW_SOLICITACAO)
      REFERENCES PE_PROGRAMA (SQ_SIW_SOLICITACAO)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE PE_PROGRAMA_LOG
   ADD CONSTRAINT FK_PEPROLOG_SISOLLOG FOREIGN KEY (SQ_SIW_SOLIC_LOG)
      REFERENCES SIW_SOLIC_LOG (SQ_SIW_SOLIC_LOG)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE PE_PROGRAMA_LOG_ARQ
   ADD CONSTRAINT FK_PEPROLOGARQ_PEPROLOG FOREIGN KEY (SQ_PROGRAMA_LOG)
      REFERENCES PE_PROGRAMA_LOG (SQ_PROGRAMA_LOG)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE PE_PROGRAMA_LOG_ARQ
   ADD CONSTRAINT FK_PEPROLOGARQ_SIWARQ FOREIGN KEY (SQ_SIW_ARQUIVO)
      REFERENCES SIW_ARQUIVO (SQ_SIW_ARQUIVO)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE PE_UNIDADE
   ADD CONSTRAINT FK_PEUNI_COPES FOREIGN KEY (CLIENTE)
      REFERENCES CO_PESSOA (SQ_PESSOA)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE PE_UNIDADE
   ADD CONSTRAINT FK_PEUNI_EOUNI FOREIGN KEY (SQ_UNIDADE)
      REFERENCES EO_UNIDADE (SQ_UNIDADE)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE PJ_COMENTARIO_ARQ
   ADD CONSTRAINT FK_PJCOMARQ_PJETACOM FOREIGN KEY (SQ_ETAPA_COMENTARIO)
      REFERENCES PJ_ETAPA_COMENTARIO (SQ_ETAPA_COMENTARIO)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE PJ_COMENTARIO_ARQ
   ADD CONSTRAINT FK_PJCOMARQ_SIWARQ FOREIGN KEY (SQ_SIW_ARQUIVO)
      REFERENCES SIW_ARQUIVO (SQ_SIW_ARQUIVO)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE PJ_ETAPA_COMENTARIO
   ADD CONSTRAINT FK_PJETACOM_COPES FOREIGN KEY (SQ_PESSOA_INCLUSAO)
      REFERENCES CO_PESSOA (SQ_PESSOA)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE PJ_ETAPA_COMENTARIO
   ADD CONSTRAINT FK_PJETACOM_PJPROETA FOREIGN KEY (SQ_PROJETO_ETAPA)
      REFERENCES PJ_PROJETO_ETAPA (SQ_PROJETO_ETAPA)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE PJ_ETAPA_CONTRATO
   ADD CONSTRAINT FK_ETACON_SIWSOL FOREIGN KEY (SQ_SIW_SOLICITACAO)
      REFERENCES SIW_SOLICITACAO (SQ_SIW_SOLICITACAO)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE PJ_ETAPA_CONTRATO
   ADD CONSTRAINT FK_PJETACON_PJPROETA FOREIGN KEY (SQ_PROJETO_ETAPA)
      REFERENCES PJ_PROJETO_ETAPA (SQ_PROJETO_ETAPA)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE PJ_ETAPA_DEMANDA
   ADD CONSTRAINT FK_PJETADEM_PJPROETA FOREIGN KEY (SQ_PROJETO_ETAPA)
      REFERENCES PJ_PROJETO_ETAPA (SQ_PROJETO_ETAPA)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE PJ_ETAPA_DEMANDA
   ADD CONSTRAINT FK_PJETADEM_SIWSOL FOREIGN KEY (SQ_SIW_SOLICITACAO)
      REFERENCES SIW_SOLICITACAO (SQ_SIW_SOLICITACAO)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE PJ_ETAPA_MENSAL
   ADD CONSTRAINT FK_PJETAMEN_PJPROETA FOREIGN KEY (SQ_PROJETO_ETAPA)
      REFERENCES PJ_PROJETO_ETAPA (SQ_PROJETO_ETAPA)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE PJ_PROJETO
   ADD CONSTRAINT FK_PJPRO_COCID FOREIGN KEY (SQ_CIDADE)
      REFERENCES CO_CIDADE (SQ_CIDADE)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE PJ_PROJETO
   ADD CONSTRAINT FK_PJPRO_COPES_OUTRA FOREIGN KEY (OUTRA_PARTE)
      REFERENCES CO_PESSOA (SQ_PESSOA)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE PJ_PROJETO
   ADD CONSTRAINT FK_PJPRO_COPES_PREP FOREIGN KEY (PREPOSTO)
      REFERENCES CO_PESSOA (SQ_PESSOA)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE PJ_PROJETO
   ADD CONSTRAINT FK_PJPRO_COTIPPES FOREIGN KEY (SQ_TIPO_PESSOA)
      REFERENCES CO_TIPO_PESSOA (SQ_TIPO_PESSOA)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE PJ_PROJETO
   ADD CONSTRAINT FK_PJPRO_EOUNI FOREIGN KEY (SQ_UNIDADE_RESP)
      REFERENCES EO_UNIDADE (SQ_UNIDADE)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE PJ_PROJETO
   ADD CONSTRAINT FK_PJPRO_SIWSOL FOREIGN KEY (SQ_SIW_SOLICITACAO)
      REFERENCES SIW_SOLICITACAO (SQ_SIW_SOLICITACAO)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE PJ_PROJETO_ENVOLV
   ADD CONSTRAINT FK_PJPROENV_EOUNI FOREIGN KEY (SQ_UNIDADE)
      REFERENCES EO_UNIDADE (SQ_UNIDADE)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE PJ_PROJETO_ENVOLV
   ADD CONSTRAINT FK_PJPROENV_PJPRO FOREIGN KEY (SQ_SIW_SOLICITACAO)
      REFERENCES PJ_PROJETO (SQ_SIW_SOLICITACAO)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE PJ_PROJETO_ETAPA
   ADD CONSTRAINT FK_PJPROETA_COCID FOREIGN KEY (SQ_CIDADE)
      REFERENCES CO_CIDADE (SQ_CIDADE)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE PJ_PROJETO_ETAPA
   ADD CONSTRAINT FK_PJPROETA_COPAI FOREIGN KEY (SQ_PAIS)
      REFERENCES CO_PAIS (SQ_PAIS)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE PJ_PROJETO_ETAPA
   ADD CONSTRAINT FK_PJPROETA_COPES FOREIGN KEY (SQ_PESSOA)
      REFERENCES CO_PESSOA (SQ_PESSOA)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE PJ_PROJETO_ETAPA
   ADD CONSTRAINT FK_PJPROETA_COPES_ATUAL FOREIGN KEY (SQ_PESSOA_ATUALIZACAO)
      REFERENCES CO_PESSOA (SQ_PESSOA)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE PJ_PROJETO_ETAPA
   ADD CONSTRAINT FK_PJPROETA_COREG FOREIGN KEY (SQ_REGIAO)
      REFERENCES CO_REGIAO (SQ_REGIAO)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE PJ_PROJETO_ETAPA
   ADD CONSTRAINT FK_PJPROETA_COUF FOREIGN KEY (CO_UF, SQ_PAIS)
      REFERENCES CO_UF (CO_UF, SQ_PAIS)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE PJ_PROJETO_ETAPA
   ADD CONSTRAINT FK_PJPROETA_EOUNI FOREIGN KEY (SQ_UNIDADE)
      REFERENCES EO_UNIDADE (SQ_UNIDADE)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE PJ_PROJETO_ETAPA
   ADD CONSTRAINT FK_PJPROETA_PJPRO FOREIGN KEY (SQ_SIW_SOLICITACAO)
      REFERENCES PJ_PROJETO (SQ_SIW_SOLICITACAO)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE PJ_PROJETO_ETAPA
   ADD CONSTRAINT FK_PJPROETA_PJPROETA FOREIGN KEY (SQ_ETAPA_PAI)
      REFERENCES PJ_PROJETO_ETAPA (SQ_PROJETO_ETAPA)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE PJ_PROJETO_ETAPA_ARQ
   ADD CONSTRAINT FK_PJPROETAAPQ_PJPROETA FOREIGN KEY (SQ_PROJETO_ETAPA)
      REFERENCES PJ_PROJETO_ETAPA (SQ_PROJETO_ETAPA)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE PJ_PROJETO_ETAPA_ARQ
   ADD CONSTRAINT FK_PJPROETAARQ_SIWARQ FOREIGN KEY (SQ_SIW_ARQUIVO)
      REFERENCES SIW_ARQUIVO (SQ_SIW_ARQUIVO)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE PJ_PROJETO_INTERES
   ADD CONSTRAINT FK_PJPROINT_COPES FOREIGN KEY (SQ_PESSOA)
      REFERENCES CO_PESSOA (SQ_PESSOA)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE PJ_PROJETO_INTERES
   ADD CONSTRAINT FK_PJPROINT_PJPRO FOREIGN KEY (SQ_SIW_SOLICITACAO)
      REFERENCES PJ_PROJETO (SQ_SIW_SOLICITACAO)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE PJ_PROJETO_LOG
   ADD CONSTRAINT FK_PJLOG_SIWLOG FOREIGN KEY (SQ_SIW_SOLIC_LOG)
      REFERENCES SIW_SOLIC_LOG (SQ_SIW_SOLIC_LOG)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE PJ_PROJETO_LOG
   ADD CONSTRAINT FK_PJPRJLG_CPS_C FOREIGN KEY (CADASTRADOR)
      REFERENCES CO_PESSOA (SQ_PESSOA)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE PJ_PROJETO_LOG
   ADD CONSTRAINT FK_PJPRJLG_CPS_D FOREIGN KEY (DESTINATARIO)
      REFERENCES CO_PESSOA (SQ_PESSOA)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE PJ_PROJETO_LOG
   ADD CONSTRAINT FK_PJPRJLOG_PJPRJ FOREIGN KEY (SQ_SIW_SOLICITACAO)
      REFERENCES PJ_PROJETO (SQ_SIW_SOLICITACAO)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE PJ_PROJETO_LOG_ARQ
   ADD CONSTRAINT FK_PJPROLOGARQ_PJPROLOG FOREIGN KEY (SQ_PROJETO_LOG)
      REFERENCES PJ_PROJETO_LOG (SQ_PROJETO_LOG)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE PJ_PROJETO_LOG_ARQ
   ADD CONSTRAINT FK_PJPROLOGARQ_SIWARQ FOREIGN KEY (SQ_SIW_ARQUIVO)
      REFERENCES SIW_ARQUIVO (SQ_SIW_ARQUIVO)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE PJ_PROJETO_RECURSO
   ADD CONSTRAINT FK_PJPROREC_PJPRO FOREIGN KEY (SQ_SIW_SOLICITACAO)
      REFERENCES PJ_PROJETO (SQ_SIW_SOLICITACAO)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE PJ_PROJETO_REPRESENTANTE
   ADD CONSTRAINT FK_PJPROREP_PJPRO FOREIGN KEY (SQ_SIW_SOLICITACAO)
      REFERENCES PJ_PROJETO (SQ_SIW_SOLICITACAO)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE PJ_PROJETO_REPRESENTANTE
   ADD CONSTRAINT FK_PJPROREP_SGAUT FOREIGN KEY (SQ_PESSOA)
      REFERENCES SG_AUTENTICACAO (SQ_PESSOA)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE PJ_RECURSO_ETAPA
   ADD CONSTRAINT FK_PJRECETA_PJPROETA FOREIGN KEY (SQ_PROJETO_ETAPA)
      REFERENCES PJ_PROJETO_ETAPA (SQ_PROJETO_ETAPA)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE PJ_RECURSO_ETAPA
   ADD CONSTRAINT FK_PJRECETA_PJPROREC FOREIGN KEY (SQ_PROJETO_RECURSO)
      REFERENCES PJ_PROJETO_RECURSO (SQ_PROJETO_RECURSO)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE PJ_RUBRICA
   ADD CONSTRAINT FK_PJRUB_PJPRO FOREIGN KEY (SQ_SIW_SOLICITACAO)
      REFERENCES PJ_PROJETO (SQ_SIW_SOLICITACAO)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE PJ_RUBRICA_CRONOGRAMA
   ADD CONSTRAINT FK_PJRUBCRO_PJRUB FOREIGN KEY (SQ_PROJETO_RUBRICA)
      REFERENCES PJ_RUBRICA (SQ_PROJETO_RUBRICA)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE PT_CONTEUDO
   ADD CONSTRAINT FK_PTCON_SGAUT FOREIGN KEY (SQ_USUARIO)
      REFERENCES SG_AUTENTICACAO (SQ_PESSOA)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE PT_EXIBICAO_CONTEUDO
   ADD CONSTRAINT FK_PTEXICON_PTCON FOREIGN KEY (SQ_CONTEUDO)
      REFERENCES PT_CONTEUDO (SQ_CONTEUDO)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE PT_EXIBICAO_CONTEUDO
   ADD CONSTRAINT FK_PTEXICON_PTMEN FOREIGN KEY (SQ_MENU)
      REFERENCES PT_MENU (SQ_MENU)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE PT_FILTRO
   ADD CONSTRAINT FK_PTFIL_PTCAM FOREIGN KEY (SQ_CAMPO)
      REFERENCES PT_CAMPO (SQ_CAMPO)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE PT_FILTRO
   ADD CONSTRAINT FK_PTFIL_PTOPE FOREIGN KEY (SQ_OPERADOR)
      REFERENCES PT_OPERADOR (SQ_OPERADOR)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE PT_FILTRO
   ADD CONSTRAINT FK_PTFIL_PTPES FOREIGN KEY (SQ_PESQUISA)
      REFERENCES PT_PESQUISA (SQ_PESQUISA)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE PT_MENU
   ADD CONSTRAINT FK_PTMEN_PTMEN FOREIGN KEY (MENU_SQ_MENU)
      REFERENCES PT_MENU (SQ_MENU)
      ON UPDATE RESTRICT;

ALTER TABLE PT_PESQUISA
   ADD CONSTRAINT FK_PTPES_COPES FOREIGN KEY (SQ_PESSOA)
      REFERENCES CO_PESSOA (SQ_PESSOA)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE SG_AUTENTICACAO
   ADD CONSTRAINT FK_SGAUT_COPES FOREIGN KEY (SQ_PESSOA)
      REFERENCES CO_PESSOA (SQ_PESSOA)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE SG_AUTENTICACAO
   ADD CONSTRAINT FK_SGAUT_EOLOC FOREIGN KEY (SQ_LOCALIZACAO)
      REFERENCES EO_LOCALIZACAO (SQ_LOCALIZACAO)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE SG_AUTENTICACAO
   ADD CONSTRAINT FK_SGAUT_EOUNI FOREIGN KEY (SQ_UNIDADE)
      REFERENCES EO_UNIDADE (SQ_UNIDADE)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE SG_AUTENTICACAO
   ADD CONSTRAINT FK_SGAUT_SIWCLI FOREIGN KEY (CLIENTE)
      REFERENCES SIW_CLIENTE (SQ_PESSOA)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE SG_AUTENTICACAO_TEMP
   ADD CONSTRAINT FK_SGAUTTEM_COPES FOREIGN KEY (CLIENTE)
      REFERENCES CO_PESSOA (SQ_PESSOA)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE SG_PERFIL_MENU
   ADD CONSTRAINT FK_SGPERMN_COTPVIN FOREIGN KEY (SQ_TIPO_VINCULO)
      REFERENCES CO_TIPO_VINCULO (SQ_TIPO_VINCULO)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE SG_PERFIL_MENU
   ADD CONSTRAINT FK_SGPERMN_SIWMNEN FOREIGN KEY (SQ_MENU, SQ_PESSOA_ENDERECO)
      REFERENCES SIW_MENU_ENDERECO (SQ_MENU, SQ_PESSOA_ENDERECO)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE SG_PESSOA_MAIL
   ADD CONSTRAINT FK_SGPESMAI_SGAUT FOREIGN KEY (SQ_PESSOA)
      REFERENCES SG_AUTENTICACAO (SQ_PESSOA)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE SG_PESSOA_MAIL
   ADD CONSTRAINT FK_SGPESMAI_SIWMEN FOREIGN KEY (SQ_MENU)
      REFERENCES SIW_MENU (SQ_MENU)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE SG_PESSOA_MENU
   ADD CONSTRAINT FK_SGPESMEN_SGAUT FOREIGN KEY (SQ_PESSOA)
      REFERENCES SG_AUTENTICACAO (SQ_PESSOA)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE SG_PESSOA_MENU
   ADD CONSTRAINT FK_SGPESMN_SIWMNEN FOREIGN KEY (SQ_MENU, SQ_PESSOA_ENDERECO)
      REFERENCES SIW_MENU_ENDERECO (SQ_MENU, SQ_PESSOA_ENDERECO)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE SG_PESSOA_MODULO
   ADD CONSTRAINT FK_SGPESMD_COPESEN FOREIGN KEY (SQ_PESSOA_ENDERECO)
      REFERENCES CO_PESSOA_ENDERECO (SQ_PESSOA_ENDERECO)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE SG_PESSOA_MODULO
   ADD CONSTRAINT FK_SGPESMD_SIWCLMD FOREIGN KEY (CLIENTE, SQ_MODULO)
      REFERENCES SIW_CLIENTE_MODULO (SQ_PESSOA, SQ_MODULO)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE SG_PESSOA_MODULO
   ADD CONSTRAINT FK_SGPESMOD_COPES FOREIGN KEY (SQ_PESSOA)
      REFERENCES CO_PESSOA (SQ_PESSOA)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE SG_PESSOA_UNIDADE
   ADD CONSTRAINT FK_SGPESUNI_EOUNI FOREIGN KEY (SQ_UNIDADE)
      REFERENCES EO_UNIDADE (SQ_UNIDADE)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE SG_PESSOA_UNIDADE
   ADD CONSTRAINT FK_SGPESUNI_SGAUT FOREIGN KEY (SQ_PESSOA)
      REFERENCES SG_AUTENTICACAO (SQ_PESSOA)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE SG_TRAMITE_PESSOA
   ADD CONSTRAINT FK_SGTRAPES_SGAUT FOREIGN KEY (SQ_PESSOA)
      REFERENCES SG_AUTENTICACAO (SQ_PESSOA)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE SG_TRAMITE_PESSOA
   ADD CONSTRAINT FK_SGTRAPES_SIWTRA FOREIGN KEY (SQ_SIW_TRAMITE)
      REFERENCES SIW_TRAMITE (SQ_SIW_TRAMITE)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE SG_TRAMITE_PESSOA
   ADD CONSTRAINT FK_SGTRAPS_COPSND FOREIGN KEY (SQ_PESSOA_ENDERECO)
      REFERENCES CO_PESSOA_ENDERECO (SQ_PESSOA_ENDERECO)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE SIW_ARQUIVO
   ADD CONSTRAINT FK_SIWARQ_COPES FOREIGN KEY (CLIENTE)
      REFERENCES CO_PESSOA (SQ_PESSOA)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE SIW_ARQUIVO
   ADD CONSTRAINT FK_SIWARQ_SIWTIPARQ FOREIGN KEY (SQ_TIPO_ARQUIVO)
      REFERENCES SIW_TIPO_ARQUIVO (SQ_TIPO_ARQUIVO)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE SIW_CLIENTE
   ADD CONSTRAINT FK_COAGE_SIWCLI FOREIGN KEY (SQ_AGENCIA_PADRAO)
      REFERENCES CO_AGENCIA (SQ_AGENCIA)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE SIW_CLIENTE
   ADD CONSTRAINT FK_COCID_SIWCLI FOREIGN KEY (SQ_CIDADE_PADRAO)
      REFERENCES CO_CIDADE (SQ_CIDADE)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE SIW_CLIENTE
   ADD CONSTRAINT FK_COPES_SIWCLI FOREIGN KEY (SQ_PESSOA)
      REFERENCES CO_PESSOA (SQ_PESSOA)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE SIW_CLIENTE_MODULO
   ADD CONSTRAINT FK_SIWCLI_SIWCLIMD FOREIGN KEY (SQ_PESSOA)
      REFERENCES SIW_CLIENTE (SQ_PESSOA)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE SIW_CLIENTE_MODULO
   ADD CONSTRAINT FK_SIWMD_SIWCLIMD FOREIGN KEY (SQ_MODULO)
      REFERENCES SIW_MODULO (SQ_MODULO)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE SIW_COORDENADA
   ADD CONSTRAINT FK_SIWCOO_COPES FOREIGN KEY (CLIENTE)
      REFERENCES CO_PESSOA (SQ_PESSOA)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE SIW_COORDENADA_ENDERECO
   ADD CONSTRAINT FK_SIWCOOEND_SIWCOO FOREIGN KEY (SQ_SIW_COORDENADA)
      REFERENCES SIW_COORDENADA (SQ_SIW_COORDENADA)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE SIW_COORDENADA_ENDERECO
   ADD CONSTRAINT FK_SIWCOOEND_SIWPESEND FOREIGN KEY (SQ_PESSOA_ENDERECO)
      REFERENCES CO_PESSOA_ENDERECO (SQ_PESSOA_ENDERECO)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE SIW_COORDENADA_SOLICITACAO
   ADD CONSTRAINT FK_SIWCOOSOL_SIWCOO FOREIGN KEY (SQ_SIW_COORDENADA)
      REFERENCES SIW_COORDENADA (SQ_SIW_COORDENADA)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE SIW_COORDENADA_SOLICITACAO
   ADD CONSTRAINT FK_SIWCOOSOL_SIWSOL FOREIGN KEY (SQ_SIW_SOLICITACAO)
      REFERENCES SIW_SOLICITACAO (SQ_SIW_SOLICITACAO)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE SIW_ETAPA_INTERESSADO
   ADD CONSTRAINT FK_SIWETAINT_EOUNI FOREIGN KEY (SQ_UNIDADE)
      REFERENCES EO_UNIDADE (SQ_UNIDADE)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE SIW_ETAPA_INTERESSADO
   ADD CONSTRAINT FK_SIWETAINT_PJPROETA FOREIGN KEY (SQ_PROJETO_ETAPA)
      REFERENCES PJ_PROJETO_ETAPA (SQ_PROJETO_ETAPA)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE SIW_MAIL
   ADD CONSTRAINT FK_SIWMAI_COPES_CLIENTE FOREIGN KEY (CLIENTE)
      REFERENCES CO_PESSOA (SQ_PESSOA)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE SIW_MAIL
   ADD CONSTRAINT FK_SIWMAI_COPES_REMETE FOREIGN KEY (REMETENTE)
      REFERENCES CO_PESSOA (SQ_PESSOA)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE SIW_MAIL_ANEXO
   ADD CONSTRAINT FK_SIWARQ_SIWMAIANE FOREIGN KEY (SQ_SIW_ARQUIVO)
      REFERENCES SIW_ARQUIVO (SQ_SIW_ARQUIVO)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE SIW_MAIL_ANEXO
   ADD CONSTRAINT FK_SIWMAI_SIWMAIANE FOREIGN KEY (SQ_MAIL)
      REFERENCES SIW_MAIL (SQ_MAIL)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE SIW_MAIL_DESTINATARIO
   ADD CONSTRAINT FK_SIWMAIDES_COPES FOREIGN KEY (DESTINATARIO_PESSOA)
      REFERENCES CO_PESSOA (SQ_PESSOA)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE SIW_MAIL_DESTINATARIO
   ADD CONSTRAINT FK_SIWMAIDES_EOUNI FOREIGN KEY (DESTINATARIO_UNIDADE)
      REFERENCES EO_UNIDADE (SQ_UNIDADE)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE SIW_MAIL_DESTINATARIO
   ADD CONSTRAINT FK_SIWMAIDES_SIWMAI FOREIGN KEY (SQ_MAIL)
      REFERENCES SIW_MAIL (SQ_MAIL)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE SIW_MENU
   ADD CONSTRAINT FK_SIWCLIMD_SIWMN FOREIGN KEY (SQ_MODULO, SQ_PESSOA)
      REFERENCES SIW_CLIENTE_MODULO (SQ_MODULO, SQ_PESSOA)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE SIW_MENU
   ADD CONSTRAINT FK_SIWMEN_EOUNI FOREIGN KEY (SQ_UNID_EXECUTORA)
      REFERENCES EO_UNIDADE (SQ_UNIDADE)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE SIW_MENU
   ADD CONSTRAINT FK_SIWMEN_SIWARQ FOREIGN KEY (SQ_ARQUIVO_PROCED)
      REFERENCES SIW_ARQUIVO (SQ_SIW_ARQUIVO)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE SIW_MENU
   ADD CONSTRAINT FK_SIWMEN_SIWMEN_NUMERADOR FOREIGN KEY (SERVICO_NUMERADOR)
      REFERENCES SIW_MENU (SQ_MENU)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE SIW_MENU
   ADD CONSTRAINT FK_SIWMN_SIWMN_PAI FOREIGN KEY (SQ_MENU_PAI)
      REFERENCES SIW_MENU (SQ_MENU)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE SIW_MENU_ARQUIVO
   ADD CONSTRAINT FK_SIWMENARQ_SIWARQ FOREIGN KEY (SQ_SIW_ARQUIVO)
      REFERENCES SIW_ARQUIVO (SQ_SIW_ARQUIVO)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE SIW_MENU_ARQUIVO
   ADD CONSTRAINT FK_SIWMENARQ_SIWMEN FOREIGN KEY (SQ_MENU)
      REFERENCES SIW_MENU (SQ_MENU)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE SIW_MENU_ENDERECO
   ADD CONSTRAINT FK_SIWMNEN_COPESEN FOREIGN KEY (SQ_PESSOA_ENDERECO)
      REFERENCES CO_PESSOA_ENDERECO (SQ_PESSOA_ENDERECO)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE SIW_MENU_ENDERECO
   ADD CONSTRAINT FK_SIWMNEN_SIWMN FOREIGN KEY (SQ_MENU)
      REFERENCES SIW_MENU (SQ_MENU)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE SIW_MENU_RELAC
   ADD CONSTRAINT FK_SIWMENREL_SIWMEN_CLI FOREIGN KEY (SERVICO_CLIENTE)
      REFERENCES SIW_MENU (SQ_MENU)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE SIW_MENU_RELAC
   ADD CONSTRAINT FK_SIWMENREL_SIWMEN_FORN FOREIGN KEY (SERVICO_FORNECEDOR)
      REFERENCES SIW_MENU (SQ_MENU)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE SIW_MENU_RELAC
   ADD CONSTRAINT FK_SIWMENREL_SIWTRA FOREIGN KEY (SQ_SIW_TRAMITE)
      REFERENCES SIW_TRAMITE (SQ_SIW_TRAMITE)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE SIW_META_ARQUIVO
   ADD CONSTRAINT FK_SIWMETARQ_SIWARQ FOREIGN KEY (SQ_SIW_ARQUIVO)
      REFERENCES SIW_ARQUIVO (SQ_SIW_ARQUIVO)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE SIW_META_ARQUIVO
   ADD CONSTRAINT FK_SIWMETARQ_SIWSOLMET FOREIGN KEY (SQ_SOLIC_META)
      REFERENCES SIW_SOLIC_META (SQ_SOLIC_META)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE SIW_META_CRONOGRAMA
   ADD CONSTRAINT FK_SIWMETCRO_COPES FOREIGN KEY (SQ_PESSOA_ATUALIZACAO)
      REFERENCES CO_PESSOA (SQ_PESSOA)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE SIW_META_CRONOGRAMA
   ADD CONSTRAINT FK_SIWMETCRO_SIWSOLMET FOREIGN KEY (SQ_SOLIC_META)
      REFERENCES SIW_SOLIC_META (SQ_SOLIC_META)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE SIW_MOD_SEG
   ADD CONSTRAINT FK_COSEG_SIWMODSEG FOREIGN KEY (SQ_SEGMENTO)
      REFERENCES CO_SEGMENTO (SQ_SEGMENTO)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE SIW_MOD_SEG
   ADD CONSTRAINT FK_SIWMD_SIWMDSEG FOREIGN KEY (SQ_MODULO)
      REFERENCES SIW_MODULO (SQ_MODULO)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE SIW_RESTRICAO
   ADD CONSTRAINT FK_SIWRES_SIWTIPRES FOREIGN KEY (SQ_TIPO_RESTRICAO)
      REFERENCES SIW_TIPO_RESTRICAO (SQ_TIPO_RESTRICAO)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE SIW_RESTRICAO
   ADD CONSTRAINT FK_SIWSOLRES_COPES FOREIGN KEY (SQ_PESSOA)
      REFERENCES CO_PESSOA (SQ_PESSOA)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE SIW_RESTRICAO
   ADD CONSTRAINT FK_SIWSOLRES_COPES_ATUAL FOREIGN KEY (SQ_PESSOA_ATUALIZACAO)
      REFERENCES CO_PESSOA (SQ_PESSOA)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE SIW_RESTRICAO
   ADD CONSTRAINT FK_SIWSOLRES_SIWSOL FOREIGN KEY (SQ_SIW_SOLICITACAO)
      REFERENCES SIW_SOLICITACAO (SQ_SIW_SOLICITACAO)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE SIW_RESTRICAO_ETAPA
   ADD CONSTRAINT FK_SIWRESETA_PJPROETA FOREIGN KEY (SQ_PROJETO_ETAPA)
      REFERENCES PJ_PROJETO_ETAPA (SQ_PROJETO_ETAPA)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE SIW_RESTRICAO_ETAPA
   ADD CONSTRAINT FK_SIWRESETA_SIWRES FOREIGN KEY (SQ_SIW_RESTRICAO)
      REFERENCES SIW_RESTRICAO (SQ_SIW_RESTRICAO)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE SIW_SOLICITACAO
   ADD CONSTRAINT FK_SIWSOL_COCID FOREIGN KEY (SQ_CIDADE_ORIGEM)
      REFERENCES CO_CIDADE (SQ_CIDADE)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE SIW_SOLICITACAO
   ADD CONSTRAINT FK_SIWSOL_COPES_RECEB FOREIGN KEY (RECEBEDOR)
      REFERENCES CO_PESSOA (SQ_PESSOA)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE SIW_SOLICITACAO
   ADD CONSTRAINT FK_SIWSOL_COPS_CAD FOREIGN KEY (CADASTRADOR)
      REFERENCES CO_PESSOA (SQ_PESSOA)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE SIW_SOLICITACAO
   ADD CONSTRAINT FK_SIWSOL_COPS_EXE FOREIGN KEY (EXECUTOR)
      REFERENCES CO_PESSOA (SQ_PESSOA)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE SIW_SOLICITACAO
   ADD CONSTRAINT FK_SIWSOL_COPS_SOL FOREIGN KEY (SOLICITANTE)
      REFERENCES CO_PESSOA (SQ_PESSOA)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE SIW_SOLICITACAO
   ADD CONSTRAINT FK_SIWSOL_EOUNI FOREIGN KEY (SQ_UNIDADE)
      REFERENCES EO_UNIDADE (SQ_UNIDADE)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE SIW_SOLICITACAO
   ADD CONSTRAINT FK_SIWSOL_PEPLA FOREIGN KEY (SQ_PLANO)
      REFERENCES PE_PLANO (SQ_PLANO)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE SIW_SOLICITACAO
   ADD CONSTRAINT FK_SIWSOL_SIWMEN FOREIGN KEY (SQ_MENU)
      REFERENCES SIW_MENU (SQ_MENU)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE SIW_SOLICITACAO
   ADD CONSTRAINT FK_SIWSOL_SIWSOL FOREIGN KEY (SQ_SOLIC_PAI)
      REFERENCES SIW_SOLICITACAO (SQ_SIW_SOLICITACAO)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE SIW_SOLICITACAO
   ADD CONSTRAINT FK_SIWSOL_SIWSOL_PROT FOREIGN KEY (PROTOCOLO_SIW)
      REFERENCES SIW_SOLICITACAO (SQ_SIW_SOLICITACAO)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE SIW_SOLICITACAO
   ADD CONSTRAINT FK_SIWSOL_SIWTIPEVE FOREIGN KEY (SQ_TIPO_EVENTO)
      REFERENCES SIW_TIPO_EVENTO (SQ_TIPO_EVENTO)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE SIW_SOLICITACAO
   ADD CONSTRAINT FK_SIWSOL_SIWTRA FOREIGN KEY (SQ_SIW_TRAMITE)
      REFERENCES SIW_TRAMITE (SQ_SIW_TRAMITE)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE SIW_SOLICITACAO_INTERESSADO
   ADD CONSTRAINT FK_SIWSOLINT_COPES FOREIGN KEY (SQ_PESSOA)
      REFERENCES CO_PESSOA (SQ_PESSOA)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE SIW_SOLICITACAO_INTERESSADO
   ADD CONSTRAINT FK_SIWSOLINT_SIWSOL FOREIGN KEY (SQ_SIW_SOLICITACAO)
      REFERENCES SIW_SOLICITACAO (SQ_SIW_SOLICITACAO)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE SIW_SOLICITACAO_INTERESSADO
   ADD CONSTRAINT FK_SIWSOLINT_SIWTIPINT FOREIGN KEY (SQ_TIPO_INTERESSADO)
      REFERENCES SIW_TIPO_INTERESSADO (SQ_TIPO_INTERESSADO)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE SIW_SOLICITACAO_OBJETIVO
   ADD CONSTRAINT FK_SIWSOLOBJ_PEOBJ FOREIGN KEY (SQ_PEOBJETIVO)
      REFERENCES PE_OBJETIVO (SQ_PEOBJETIVO)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE SIW_SOLICITACAO_OBJETIVO
   ADD CONSTRAINT FK_SIWSOLOBJ_PEPLA FOREIGN KEY (SQ_PLANO)
      REFERENCES PE_PLANO (SQ_PLANO)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE SIW_SOLICITACAO_OBJETIVO
   ADD CONSTRAINT FK_SIWSOLOBJ_SIWSOL FOREIGN KEY (SQ_SIW_SOLICITACAO)
      REFERENCES SIW_SOLICITACAO (SQ_SIW_SOLICITACAO)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE SIW_SOLIC_APOIO
   ADD CONSTRAINT FK_SIWSOLAPO_COPES FOREIGN KEY (SQ_PESSOA_ATUALIZACAO)
      REFERENCES CO_PESSOA (SQ_PESSOA)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE SIW_SOLIC_APOIO
   ADD CONSTRAINT FK_SIWSOLAPO_SIWSOL FOREIGN KEY (SQ_SIW_SOLICITACAO)
      REFERENCES SIW_SOLICITACAO (SQ_SIW_SOLICITACAO)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE SIW_SOLIC_APOIO
   ADD CONSTRAINT FK_SIWSOLAPO_SIWTIPAPO FOREIGN KEY (SQ_TIPO_APOIO)
      REFERENCES SIW_TIPO_APOIO (SQ_TIPO_APOIO)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE SIW_SOLIC_ARQUIVO
   ADD CONSTRAINT FK_SIWSOLARQ_SIWARQ FOREIGN KEY (SQ_SIW_ARQUIVO)
      REFERENCES SIW_ARQUIVO (SQ_SIW_ARQUIVO)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE SIW_SOLIC_ARQUIVO
   ADD CONSTRAINT FK_SIWSOLARQ_SIWSOL FOREIGN KEY (SQ_SIW_SOLICITACAO)
      REFERENCES SIW_SOLICITACAO (SQ_SIW_SOLICITACAO)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE SIW_SOLIC_INDICADOR
   ADD CONSTRAINT FK_SIWSOLIND_EOIND FOREIGN KEY (SQ_EOINDICADOR)
      REFERENCES EO_INDICADOR (SQ_EOINDICADOR)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE SIW_SOLIC_INDICADOR
   ADD CONSTRAINT FK_SIWSOLIND_SIWSOL FOREIGN KEY (SQ_SIW_SOLICITACAO)
      REFERENCES SIW_SOLICITACAO (SQ_SIW_SOLICITACAO)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE SIW_SOLIC_LOG
   ADD CONSTRAINT FK_SIWSOLLOG_COPES FOREIGN KEY (SQ_PESSOA)
      REFERENCES CO_PESSOA (SQ_PESSOA)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE SIW_SOLIC_LOG
   ADD CONSTRAINT FK_SIWSOLLOG_SIWSL FOREIGN KEY (SQ_SIW_SOLICITACAO)
      REFERENCES SIW_SOLICITACAO (SQ_SIW_SOLICITACAO)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE SIW_SOLIC_LOG
   ADD CONSTRAINT FK_SIWSOLLOG_SIWTR FOREIGN KEY (SQ_SIW_TRAMITE)
      REFERENCES SIW_TRAMITE (SQ_SIW_TRAMITE)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE SIW_SOLIC_LOG_ARQ
   ADD CONSTRAINT FK_SIWSOLLOGARQ_SIWARQ FOREIGN KEY (SQ_SIW_ARQUIVO)
      REFERENCES SIW_ARQUIVO (SQ_SIW_ARQUIVO)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE SIW_SOLIC_LOG_ARQ
   ADD CONSTRAINT FK_SIWSOLLOGARQ_SIWSOLLOG FOREIGN KEY (SQ_SIW_SOLIC_LOG)
      REFERENCES SIW_SOLIC_LOG (SQ_SIW_SOLIC_LOG)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE SIW_SOLIC_META
   ADD CONSTRAINT FK_SIWSOLMET_COCID FOREIGN KEY (SQ_CIDADE)
      REFERENCES CO_CIDADE (SQ_CIDADE)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE SIW_SOLIC_META
   ADD CONSTRAINT FK_SIWSOLMET_COPAI FOREIGN KEY (SQ_PAIS)
      REFERENCES CO_PAIS (SQ_PAIS)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE SIW_SOLIC_META
   ADD CONSTRAINT FK_SIWSOLMET_COPES_CAD FOREIGN KEY (CADASTRADOR)
      REFERENCES CO_PESSOA (SQ_PESSOA)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE SIW_SOLIC_META
   ADD CONSTRAINT FK_SIWSOLMET_COPES_RESP FOREIGN KEY (SQ_PESSOA)
      REFERENCES CO_PESSOA (SQ_PESSOA)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE SIW_SOLIC_META
   ADD CONSTRAINT FK_SIWSOLMET_COREG FOREIGN KEY (SQ_REGIAO)
      REFERENCES CO_REGIAO (SQ_REGIAO)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE SIW_SOLIC_META
   ADD CONSTRAINT FK_SIWSOLMET_COUF FOREIGN KEY (CO_UF, SQ_PAIS)
      REFERENCES CO_UF (CO_UF, SQ_PAIS)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE SIW_SOLIC_META
   ADD CONSTRAINT FK_SIWSOLMET_EOIND FOREIGN KEY (SQ_EOINDICADOR)
      REFERENCES EO_INDICADOR (SQ_EOINDICADOR)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE SIW_SOLIC_META
   ADD CONSTRAINT FK_SIWSOLMET_EOUNI FOREIGN KEY (SQ_UNIDADE)
      REFERENCES EO_UNIDADE (SQ_UNIDADE)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE SIW_SOLIC_META
   ADD CONSTRAINT FK_SIWSOLMET_PEPLAN FOREIGN KEY (SQ_PLANO)
      REFERENCES PE_PLANO (SQ_PLANO)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE SIW_SOLIC_META
   ADD CONSTRAINT FK_SIWSOLMET_SIWSOL FOREIGN KEY (SQ_SIW_SOLICITACAO)
      REFERENCES SIW_SOLICITACAO (SQ_SIW_SOLICITACAO)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE SIW_SOLIC_RECURSO
   ADD CONSTRAINT FK_SIWSOLREC_COPES_AUT FOREIGN KEY (AUTORIZADOR)
      REFERENCES CO_PESSOA (SQ_PESSOA)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE SIW_SOLIC_RECURSO
   ADD CONSTRAINT FK_SIWSOLREC_COPES_SOL FOREIGN KEY (SOLICITANTE)
      REFERENCES CO_PESSOA (SQ_PESSOA)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE SIW_SOLIC_RECURSO
   ADD CONSTRAINT FK_SIWSOLREC_EOREC FOREIGN KEY (SQ_RECURSO)
      REFERENCES EO_RECURSO (SQ_RECURSO)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE SIW_SOLIC_RECURSO
   ADD CONSTRAINT FK_SIWSOLREC_SIWSOL FOREIGN KEY (SQ_SIW_SOLICITACAO)
      REFERENCES SIW_SOLICITACAO (SQ_SIW_SOLICITACAO)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE SIW_SOLIC_RECURSO_ALOCACAO
   ADD CONSTRAINT FK_SIWSOLRECALO_SIWSOLREC FOREIGN KEY (SQ_SOLIC_RECURSO)
      REFERENCES SIW_SOLIC_RECURSO (SQ_SOLIC_RECURSO)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE SIW_SOLIC_RECURSO_LOG
   ADD CONSTRAINT FK_SIWSOLRECLOG_COPES FOREIGN KEY (SQ_PESSOA)
      REFERENCES CO_PESSOA (SQ_PESSOA)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE SIW_SOLIC_RECURSO_LOG
   ADD CONSTRAINT FK_SIWSOLRECLOG_SIWSOLREC FOREIGN KEY (SQ_SOLIC_RECURSO)
      REFERENCES SIW_SOLIC_RECURSO (SQ_SOLIC_RECURSO)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE SIW_SOLIC_SITUACAO
   ADD CONSTRAINT FK_SIWSOLSIT_COPES FOREIGN KEY (SQ_PESSOA)
      REFERENCES CO_PESSOA (SQ_PESSOA)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE SIW_SOLIC_SITUACAO
   ADD CONSTRAINT FK_SIWSOLSIT_SIWSOL FOREIGN KEY (SQ_SIW_SOLICITACAO)
      REFERENCES SIW_SOLICITACAO (SQ_SIW_SOLICITACAO)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE SIW_SOLIC_VINCULO
   ADD CONSTRAINT FK_SIWSOLVIN_SIWMEN FOREIGN KEY (SQ_MENU)
      REFERENCES SIW_MENU (SQ_MENU)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE SIW_SOLIC_VINCULO
   ADD CONSTRAINT FK_SIWSOLVIN_SIWSOL FOREIGN KEY (SQ_SIW_SOLICITACAO)
      REFERENCES SIW_SOLICITACAO (SQ_SIW_SOLICITACAO)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE SIW_TIPO_APOIO
   ADD CONSTRAINT FK_SIWTIPAPO_COPES FOREIGN KEY (CLIENTE)
      REFERENCES CO_PESSOA (SQ_PESSOA)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE SIW_TIPO_ARQUIVO
   ADD CONSTRAINT FK_SIWTIPARQ_COPES FOREIGN KEY (CLIENTE)
      REFERENCES CO_PESSOA (SQ_PESSOA)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE SIW_TIPO_INTERESSADO
   ADD CONSTRAINT FK_SIWTIPINT_SIWMEN FOREIGN KEY (SQ_MENU)
      REFERENCES SIW_MENU (SQ_MENU)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE SIW_TIPO_LOG
   ADD CONSTRAINT FK_SIWTIPLOG_COPES FOREIGN KEY (CLIENTE)
      REFERENCES CO_PESSOA (SQ_PESSOA)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE SIW_TIPO_LOG
   ADD CONSTRAINT FK_SIWTIPLOG_SIWMEN FOREIGN KEY (SQ_MENU)
      REFERENCES SIW_MENU (SQ_MENU)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE SIW_TIPO_RESTRICAO
   ADD CONSTRAINT FK_SIWTIPRES_COPES FOREIGN KEY (CLIENTE)
      REFERENCES CO_PESSOA (SQ_PESSOA)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE SIW_TRAMITE
   ADD CONSTRAINT FK_SIWTRA_SIWMEN FOREIGN KEY (SQ_MENU)
      REFERENCES SIW_MENU (SQ_MENU)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE SIW_TRAMITE_FLUXO
   ADD CONSTRAINT FK_SIWTRAFLUX_SIWTRA_DEST FOREIGN KEY (SQ_SIW_TRAMITE_DESTINO)
      REFERENCES SIW_TRAMITE (SQ_SIW_TRAMITE)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE SIW_TRAMITE_FLUXO
   ADD CONSTRAINT FK_SIWTRAFLUX_SIWTRA_ORI FOREIGN KEY (SQ_SIW_TRAMITE_ORIGEM)
      REFERENCES SIW_TRAMITE (SQ_SIW_TRAMITE)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

