create or replace function GeraCPFEspecial (p_tipo in Varchar2) return varchar2 is
  -- Gera um c�digo equivalente ao CPF ou CNPJ, para pessoas f�sicas e jur�dicas
  -- estrangeiras, ind�genas, menores etc.

  -- Se p_tipo = 1, gera c�digo equivalente a um CPF.
  -- Caso contr�rio, gera c�digo equivalente a um CNPJ.
  Result varchar2(10);
  w_Sequencial number(10);
  w_Codigo     varchar2(12);
  w_DV         Varchar2(2);
begin
  select sq_cpf_especial.nextval into w_Sequencial from dual;
  If p_tipo = 1
     Then w_Codigo := substr(1000000000+w_Sequencial,2,9);
     Else w_Codigo := substr(1000000000000+w_Sequencial,2,12);
  End If;
  w_DV     := ValidaCNPJCPF(w_Codigo,1);
  w_DV     := substr(w_Dv,1,1)||substr(w_DV,2,1);
  If p_tipo = 1
     Then Result := substr(w_Codigo,4,3)||'.'||substr(w_Codigo,7,3)||'-'||w_DV;
     Else Result := substr(w_Codigo,7,2)||'/'||substr(w_Codigo,9,4)||'-'||w_DV;
  End if;
  return(Result);
end GeraCPFEspecial;
/

