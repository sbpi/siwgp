create or replace procedure SP_PutMenuRelac
   (p_operacao           in  varchar2,
    p_servico_cliente    in  number,
    p_servico_fornecedor in  number,
    p_sq_siw_tramite     in  varchar2
   ) is
begin

   If p_operacao = 'I' Then
      -- Insere registro
      insert into siw_menu_relac (servico_cliente,   servico_fornecedor,  sq_siw_tramite )
      values                     (p_servico_cliente, p_servico_fornecedor, p_sq_siw_tramite);
   Elsif p_operacao = 'E' Then
      -- Exclui todos os registros do cliente desejado
      delete siw_menu_relac where servico_cliente = p_servico_cliente and servico_fornecedor = p_servico_fornecedor;
   End If;
end SP_PutMenuRelac;
/

