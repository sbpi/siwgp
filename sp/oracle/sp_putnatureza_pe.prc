create or replace procedure sp_PutNatureza_PE
   (p_operacao in  varchar2             ,
    p_chave    in  number   default null,
    p_cliente  in  number   default null,
    p_nome     in  varchar2 default null,
    p_ativo    in  varchar2 default null
   ) is
begin
   If p_operacao = 'I' Then
      -- Insere registro
      insert into pe_natureza (sq_penatureza, cliente, nome, ativo)
      (select sq_penatureza.nextval, p_cliente, p_nome,  p_ativo from dual);
   Elsif p_operacao = 'A' Then
      -- Altera registro
      update pe_natureza
         set
             cliente     = p_cliente,
             nome        = p_nome,
             ativo       = p_ativo
       where sq_penatureza = p_chave;
   Elsif p_operacao = 'E' Then
      -- Exclui registro
      delete pe_natureza
       where sq_penatureza = p_chave;
   End If;
end sp_PutNatureza_PE;
/

