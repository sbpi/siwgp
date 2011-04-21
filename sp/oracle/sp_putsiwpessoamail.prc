create or replace procedure SP_PutSiwPessoaMail
   (p_operacao          in  varchar2,
    p_pessoa            in  number,
    p_menu              in  number   default null,
    p_alerta            in  varchar2 default null,
    p_tramitacao        in  varchar2 default null,
    p_conclusao         in  varchar2 default null,
    p_responsabilidade  in  varchar2 default null
   ) is
begin
   If p_operacao = 'I' Then
      -- Insere registro em SG_PESSOA_MAIL, para cada serviço que conténha a opção
      insert into sg_pessoa_mail (sq_pessoa_mail,         sq_pessoa, sq_menu, alerta_diario, tramitacao,
                                  conclusao,              responsabilidade)
                          values (sq_pessoa_mail.nextval, p_pessoa,  p_menu, p_alerta,       p_tramitacao,
                                  p_conclusao,            p_responsabilidade);
   Elsif p_operacao = 'E' Then
      -- Remove a permissão
       delete sg_pessoa_mail
        where sq_pessoa = p_pessoa;
   End If;
end SP_PutSiwPessoaMail;
/

