create or replace trigger TG_SG_AUTENTICACAO_IN_UP
  before insert or update on sg_autenticacao
  for each row
declare
  w_recurso eo_recurso.sq_recurso%type;
begin
  If :new.ativo <> :old.ativo or :new.sq_unidade <> :old.sq_unidade Then
     -- Verifica se o usuário está ligado a um recurso
     select sq_recurso into w_recurso from co_pessoa where sq_pessoa = :new.sq_pessoa;

     If w_recurso is not null Then
        update eo_recurso set
           ativo           = :new.ativo,
           unidade_gestora = :new.sq_unidade
        where sq_recurso = w_recurso;
     End If;
  End If;
end TG_SG_AUTENTICACAO_IN_UP;
/

