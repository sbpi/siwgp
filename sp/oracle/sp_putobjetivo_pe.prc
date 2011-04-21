create or replace procedure sp_putobjetivo_pe
   (p_operacao  in  varchar2,
    p_chave     in  number   default null,
    p_chave_aux in  number   default null,
    p_cliente   in  number   default null,
    p_nome      in  varchar2 default null,
    p_sigla     in  varchar2 default null,
    p_descricao in  varchar2 default null,
    p_codigo    in  varchar2 default null,
    p_ativo     in  varchar2 default null
   ) is
   w_chave number(18);
begin
   If p_operacao = 'I' Then
      -- Recupera a próxima chave
      select sq_peobjetivo.nextval into w_chave from dual;

      -- Insere registro
      insert into pe_objetivo
        (sq_peobjetivo, cliente,   sq_plano, nome,   sigla,   descricao,   ativo,   codigo_externo)
      values
        (w_chave,       p_cliente, p_chave,  p_nome, p_sigla, p_descricao, p_ativo, p_codigo);
   Elsif p_operacao = 'A' Then
      -- Altera registro
      update pe_objetivo
         set sq_plano       = p_chave,
             nome           = p_nome,
             sigla          = p_sigla,
             descricao      = p_descricao,
             ativo          = p_ativo,
             codigo_externo = p_codigo
       where sq_peobjetivo = p_chave_aux;
   Elsif p_operacao = 'E' Then
      -- Exclui registro
      delete pe_objetivo where sq_peobjetivo = p_chave_aux;
   Elsif p_operacao = 'T' Then
      -- Insere registro a partir do que foi indicado na tela de importação de objetivos
      insert into pe_objetivo (sq_peobjetivo, cliente,   sq_plano, nome,   sigla,   descricao,   ativo, codigo_externo)
      (select sq_peobjetivo.nextval, cliente, p_chave, nome, sigla, descricao, ativo, codigo_externo
         from pe_objetivo
        where sq_peobjetivo = p_chave_aux
      );
   End If;
end sp_putobjetivo_pe;
/

