create or replace view vw_status_projeto as
select s.sq_siw_solicitacao, s.titulo,
case
    when s.fim >= sysdate+p.dias_aviso then 1
    WHEN s.fim > sysdate then 4
    when s.fim < sysdate then 3
end as sq_status,
case
    when s.fim >= sysdate+p.dias_aviso then 'Prazo final dentro do previsto'
    when s.fim < sysdate then 'Prazo final fora do previsto'
    WHEN s.fim > sysdate then 'Prazo final próximo do previsto'
end as status,
v.id_programa,v.programa,vi.ide,vi.idc,vi.ige, vi.igc, v.valor_orcado,v.unidade_responsavel as responsavel
from vw_projeto v, siw_solicitacao s, vw_indicador_projeto vi, pj_projeto p
where v.sq_siw_solicitacao = s.sq_siw_solicitacao and
v.sq_siw_solicitacao = vi.sq_siw_solicitacao and v.sq_siw_solicitacao = p.sq_siw_solicitacao;

