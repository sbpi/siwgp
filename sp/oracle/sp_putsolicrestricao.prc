create or replace procedure sp_putSolicRestricao
   (p_operacao              in  varchar2,
    p_chave                 in  number   default null,
    p_chave_aux             in  number   default null,
    p_pessoa                in  number   default null,
    p_pessoa_atualizacao    in  number   default null,
    p_tipo_restricao        in  number   default null,
    p_risco                 in  varchar2 default null,
    p_problema              in  varchar2 default null,
    p_descricao             in  varchar2 default null,
    p_probabilidade         in  number   default null,
    p_impacto               in  number   default null,
    p_criticidade           in  number   default null,
    p_estrategia            in  varchar2 default null,
    p_acao_resposta         in  varchar2 default null,
    p_fase_atual            in  varchar2 default null,
    p_data_situacao         in  date     default null,
    p_situacao_atual        in  varchar2 default null
   ) is
   w_chave_aux  number(18);

begin
   -- informada
   If p_operacao = 'I'  Then
      -- Gera a nova chave do registro, a partir da sequence
      select sq_siw_restricao.nextval into w_chave_aux from dual;
      -- Insere registro
      insert into siw_restricao
        (sq_siw_restricao,  sq_siw_solicitacao,       sq_pessoa,      sq_pessoa_atualizacao,      sq_tipo_restricao,               risco,            problema,         descricao,        probabilidade,
                  impacto,          criticidade,      estrategia,   acao_resposta,                 fase_atual,          data_situacao,      situacao_atual,   ultima_atualizacao)

      values
        (w_chave_aux,                 p_chave,          p_pessoa,      p_pessoa_atualizacao,      p_tipo_restricao,            p_risco,          p_problema,         p_descricao,   p_probabilidade,
        p_impacto,                p_criticidade,     p_estrategia,   p_acao_resposta,             p_fase_atual,        p_data_situacao,    p_situacao_atual,            sysdate);
   Elsif p_operacao = 'A' Then
      -- Altera registro
      update siw_restricao
         set sq_pessoa             = p_pessoa,
             sq_pessoa_atualizacao = p_pessoa_atualizacao,
             sq_tipo_restricao     = p_tipo_restricao,
             risco                 = p_risco,
             problema              = p_problema,
             descricao             = p_descricao,
             probabilidade         = p_probabilidade,
             impacto               = p_impacto,
             criticidade           = p_criticidade,
             estrategia            = p_estrategia ,
             acao_resposta         = p_acao_resposta ,
             fase_atual            = p_fase_atual,
             data_situacao         = p_data_situacao,
             situacao_atual        = p_situacao_atual,
             ultima_atualizacao    = sysdate
       where sq_siw_restricao  = p_chave_aux;
   Elsif p_operacao = 'E' Then
      -- Exclui o registro de siw_restricao_etapa
      delete siw_restricao_etapa where sq_siw_restricao = p_chave_aux;
      -- Recupera o período do registro
      delete siw_restricao where sq_siw_restricao = p_chave_aux;
   End If;
end sp_putSolicRestricao;
/

