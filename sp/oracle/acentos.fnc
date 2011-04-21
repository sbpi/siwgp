CREATE OR REPLACE FUNCTION 
          ACENTOS ( Valor IN VARCHAR2, Tipo IN NUMBER DEFAULT NULL) RETURN  VARCHAR2 IS
/*
Tipo = 1 => Converte acentos formato Benner (Paradox Intl) para ASCII Ansi
Tipo diferente de 1 ou nulo => Retira caracteres acentuados e converte para min�sculas
                               para ordena��o no SELECT
*/

   nome varchar2(8000) := trim(Valor);

BEGIN

   IF Tipo IS NULL OR Tipo <> 1 THEN
      nome := translate(lower((nome)),'�������������','aaaaeeiooouuc');
   ELSE
      nome := replace(replace(translate(nome,'��������������������������','AAAAEEIOOOUUCaaaaeeiooouuc'),'&','e'),'-','- ');
   END IF;

   RETURN upper(nome) ;
END;
/

