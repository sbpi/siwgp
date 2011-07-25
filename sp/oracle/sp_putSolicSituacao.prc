create or replace procedure sp_putSolicSituacao
   (p_operacao              in  varchar2,
    p_chave                 in  number   default null,
    p_chave_aux             in  number   default null,
    p_pessoa                in  number   default null,
    p_inicio                in  date     default null,
    p_fim                   in  date     default null,
    p_situacao              in  varchar2 default null,
    p_progressos            in  varchar2 default null,
    p_passos                in  varchar2 default null
   ) is
   w_chave_aux  number(18);

begin
   -- informada
   If p_operacao = 'I'  Then
      -- Gera a nova chave do registro, a partir da sequence
      select sq_solic_situacao.nextval into w_chave_aux from dual;    
      -- Insere registro
      insert into siw_solic_situacao
        (sq_solic_situacao, sq_siw_solicitacao, sq_pessoa, inicio,   fim,   situacao,   progressos,   passos,   ultima_alteracao)
      values
        (w_chave_aux,       p_chave,            p_pessoa,  p_inicio, p_fim, p_situacao, p_progressos, p_passos, sysdate);
   Elsif p_operacao = 'A' Then 
      -- Altera registro
      update siw_solic_situacao
         set sq_pessoa         = p_pessoa, 
             inicio            = p_inicio,
             fim               = p_fim,
             situacao          = p_situacao,
             progressos        = p_progressos,
             passos            = p_passos,
             ultima_alteracao  = sysdate
       where sq_solic_situacao = p_chave_aux;
   Elsif p_operacao = 'E' Then
      -- Apaga o registro
      delete siw_solic_situacao where sq_solic_situacao = p_chave_aux;
   End If;
end sp_putSolicSituacao;
/
