create or replace procedure sp_ajustaDataEtapa(p_projeto in number, p_todos in varchar2 default null) is
  w_existe    number(18);
  w_inicio    date := null;

  -- Cursor que recupera todos os projetos
  cursor c_projetos is
    select a.sq_siw_solicitacao
           from pj_projeto a
                inner join pj_projeto_etapa b on (a.sq_siw_solicitacao = b.sq_siw_solicitacao);

  cursor c_pacotes is
     select a.sq_etapa_pai, a.inicio_real, a.fim_real
       from pj_projeto_etapa a
      where a.sq_siw_solicitacao = p_projeto
        and a.inicio_real        is not null
        and a.pacote_trabalho    = 'S';

  cursor c_pais (w_etapa in number) is
     select a.sq_projeto_etapa
       from pj_projeto_etapa a
     connect by prior a.sq_etapa_pai = a.sq_projeto_etapa
     start with a.sq_projeto_etapa = w_etapa;
begin
  -- Verifica se o projeto existe
  select count(sq_siw_solicitacao) into w_existe from pj_projeto where sq_siw_solicitacao = coalesce(p_projeto,0);
  If w_existe = 0 and coalesce(p_todos,'nulo') <> 'TODOS' Then
     return;
  Elsif coalesce(p_todos,'nulo') = 'TODOS' Then
     -- Atualiza as datas de todos os projetos
     for crec in c_projetos loop
        sp_ajustadataEtapa(crec.sq_siw_solicitacao);
     end loop;
  End If;

  -- Reinicializa as datas das etapas que não são pacote de trabalho
  update pj_projeto_etapa set inicio_real = null, fim_real = null where pacote_trabalho = 'N' and sq_siw_solicitacao = p_projeto;

  for r_pacote in c_pacotes loop
     -- Ajusta a data de início das etapas
     for r_pai in c_pais (r_pacote.sq_etapa_pai) loop
        update pj_projeto_etapa set inicio_real = r_pacote.inicio_real where (inicio_real is null or inicio_real > r_pacote.inicio_real) and sq_projeto_etapa = r_pai.sq_projeto_etapa;
        if w_inicio is null or w_inicio > r_pacote.inicio_real then w_inicio := r_pacote.inicio_real; end if;
     end loop;

     -- Ajusta a data de término das etapas
     for r_pai in c_pais (r_pacote.sq_etapa_pai) loop
        update pj_projeto_etapa x
           set fim_real = r_pacote.fim_real
        where (fim_real is null or fim_real < r_pacote.fim_real)
          and 0 = (select count(sq_projeto_etapa)
                     from pj_projeto_etapa
                    where pacote_trabalho = 'S'
                      and fim_real        is null
                   connect by prior sq_projeto_etapa = sq_etapa_pai
                   start with sq_projeto_etapa = x.sq_projeto_etapa
                  )
          and sq_projeto_etapa = r_pai.sq_projeto_etapa;
     end loop;

     -- Ajusta o início real do projeto
     if w_inicio is not null then
        update pj_projeto set inicio_real = w_inicio where sq_siw_solicitacao = p_projeto;
     end if;
  end loop;
end sp_ajustaDataEtapa;
/

