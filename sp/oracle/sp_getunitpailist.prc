create or replace procedure SP_GetUnitPaiList
   (p_operacao    in  varchar2,
    p_sq_pessoa   in  number,
    p_sq_unidade  in  number default null,
    p_result      out sys_refcursor
   ) is
begin
   If p_operacao = 'A' Then
   --Recupera a lista de unidades quem podem ser pai
   open p_result for
      select a.sq_unidade, a.nome
        from eo_unidade a
       where sq_pessoa = p_sq_pessoa
         and a.sq_unidade not in (select sq_unidade
                                    from eo_unidade a
                                   where a.sq_pessoa    = p_sq_pessoa
                                  start with sq_unidade = p_sq_unidade
                                  connect by prior sq_unidade = sq_unidade_pai
                                 );
   Else
      open p_result for
         select a.sq_unidade, a.nome
           from eo_unidade a, co_pessoa_endereco b
          where a.sq_pessoa_endereco = b.sq_pessoa_endereco
            and b.sq_pessoa          = p_sq_pessoa;
   End If;
end SP_GetUnitPaiList;
/

