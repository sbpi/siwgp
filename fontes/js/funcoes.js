/*
objetivo: mascarar de acordo com a mascara passada
caracteres: # - caracter a ser mascarado
| - separador de mascaras
modos (exemplos):
mascara simples: "###-####"                   mascara utilizando a mascara passada
mascara composta: "###-####|####-####"       mascara de acordo com o tamanho (length) do valor passado
mascara din?mica: "[###.]###,##"             multiplica o valor entre colchetes de acordo com o length do valor para que a mascara seja din?mica ex: ###.###.###.###,##
utilizar no onkeyup do objeto
ex: onkeyup="this.value = mascara_global('#####-###',this.value);"
tratar o maxlength do objeto na p?gina (a fun??o n?o trata isso)
onkeyup="this.value = mascaraGlobal('###.###.###-##',this.value);"

onkeyup="this.value = mascaraGlobal('##.###.###/####-##',this.value);"

onkeyup="this.value = mascaraGlobal('#####-###',this.value);"

onkeyup="this.value = mascaraGlobal('####-####',this.value);"

onkeyup="this.value = mascaraGlobal('##/##/####',this.value);"

*/
function mascaraGlobal(mascara, valor)
{
  var mascara_utilizar;
  var mascara_limpa;
  var temp;
  var i;
  var j;
  var caracter;
  var separador;
  var dif;
  var validar;
  var mult;
  var ret;
  var tam;
  var tvalor;
  var valorm;
  var masct;
  tvalor = "";
  ret = "";
  caracter = "#";
  separador = "|";
  mascara_utilizar = "";
  valor = trim(valor);
  if (valor == "")return valor;
  temp = mascara.split(separador);
  dif = 1000;

  valorm = valor;
  //tirando mascara do valor já existente
  for (i=0;i<valor.length;i++){
    if (!isNaN(valor.substr(i,1))){
      tvalor = tvalor + valor.substr(i,1);
    }
  }
  valor = tvalor;

  //formatar mascara dinamica
  for (i = 0; i<temp.length;i++){
    mult = "";
    validar = 0;
    for (j=0;j<temp[i].length;j++){
      if (temp[i].substr(j,1) == "]"){
        temp[i] = temp[i].substr(j+1);
        break;
      }
      if (validar == 1)mult = mult + temp[i].substr(j,1);
      if (temp[i].substr(j,1) == "[")validar = 1;
    }
    for (j=0;j<valor.length;j++){
      temp[i] = mult + temp[i];
    }
  }


  //verificar qual mascara utilizar
  if (temp.length == 1){
    mascara_utilizar = temp[0];
    mascara_limpa = "";
    for (j=0;j<mascara_utilizar.length;j++){
      if (mascara_utilizar.substr(j,1) == caracter){
        mascara_limpa = mascara_limpa + caracter;
      }
    }
    tam = mascara_limpa.length;
  }else{
    //limpar caracteres diferente do caracter da m?scara
    for (i=0;i<temp.length;i++){
      mascara_limpa = "";
      for (j=0;j<temp[i].length;j++){
        if (temp[i].substr(j,1) == caracter){
          mascara_limpa = mascara_limpa + caracter;
        }
      }

      if (valor.length > mascara_limpa.length){
        if (dif > (valor.length - mascara_limpa.length)){
          dif = valor.length - mascara_limpa.length;
          mascara_utilizar = temp[i];
          tam = mascara_limpa.length;
        }
      }else if (valor.length < mascara_limpa.length){
        if (dif > (mascara_limpa.length - valor.length)){
          dif = mascara_limpa.length - valor.length;
          mascara_utilizar = temp[i];
          tam = mascara_limpa.length;
        }
      }else{
        mascara_utilizar = temp[i];
        tam = mascara_limpa.length;
        break;
      }
    }
  }

  //validar tamanho da mascara de acordo com o tamanho do valor
  if (valor.length > tam){
    valor = valor.substr(0,tam);
  }else if (valor.length < tam){
    masct = "";
    j = valor.length;
    for (i = mascara_utilizar.length-1;i>=0;i--){
      if (j == 0) break;
      if (mascara_utilizar.substr(i,1) == caracter){
        j--;
      }
      masct = mascara_utilizar.substr(i,1) + masct;
    }
    mascara_utilizar = masct;
  }

  //mascarar
  j = mascara_utilizar.length -1;
  for (i = valor.length - 1;i>=0;i--){
    if (mascara_utilizar.substr(j,1) != caracter){
      ret = mascara_utilizar.substr(j,1) + ret;
      j--;
    }
    ret = valor.substr(i,1) + ret;
    j--;
  }
  return ret;
}
//tirar os espaços das extremidades do valor passado (utilizada pela mascaraglobal)
function trim(s) {
  return (s ? '' + s : '').replace(/^\s*|\s*$/g, '');
}

function repeteCaracter(car,qtd){
  var saida = ""
  var i =0;
  for(i=0;i<qtd;i++)
  {
    saida += car;
  }
  return saida;
}

messageObj = new DHTML_modalMessage();  // We only create one object of this class
messageObj.setShadowOffset(5);  // Large shadow

function displayMessage(x,y,url)
{
  retiraGrafico();
  messageObj.setSource(url);
  messageObj.setCssClassMessageBox(false);
  messageObj.setSize(x,y);
  messageObj.setShadowDivVisible(true);  // Enable shadow for these boxes
  messageObj.display();
}

function displayStaticMessage(messageContent,cssClass)
{
  retiraGrafico();
  messageObj.setHtmlContent(x,ymessageContent);
  messageObj.setSize(x,y);
  messageObj.setCssClassMessageBox(cssClass);
  messageObj.setSource(false);  // no html source since we want to use a static message here.
  messageObj.setShadowDivVisible(false);  // Disable shadow for these boxes
  messageObj.display();
}

function closeMessage()
{
  messageObj.close();
  colocaGrafico();
}

function retiraGrafico(){
  $("#ProjetosDiv").hide();
  $("#AnaliseDiv").hide();
}

function colocaGrafico(){
  $("#ProjetosDiv").show();
  $("#AnaliseDiv").show();
}


function abreFecha(string){
    
  var  img = $("#img-" + string);

  img.hide();

  var tipo = "";

  if(img.attr("src").indexOf("mais") > -1 ){
    img.removeAttr("src");
    img.removeAttr("alt");
    img.attr("src","images/menos.jpg");
    img.attr("alt","Minimizar");
    tipo = "abrir";
        $('#tr-'+string+'-xp').val('true');
  }else{
    img.removeAttr("src");
    img.attr("src","images/mais.jpg");
    img.removeAttr("alt");
    img.attr("alt","Expandir");
    tipo = "fechar";
        $('#'+string+'-xp').val('false');
  }


  var par = string.split("_");
  prefixo = par[0];
  sufixo  = par[1];
  var nivelString = sufixo.split("-");
  nivelString = nivelString.length + 1;


  var tamanhoString = sufixo.length;
  var identificadorTr = "";
  var niveis = "";

  $(".arvore").each(function()
  {
    identificadorTr = this.id;
    identificadorTr = identificadorTr.substring(identificadorTr.indexOf("_")+1);



    if(sufixo == identificadorTr.substring(0,tamanhoString)){
         niveis = identificadorTr.split("-").length;
         if((nivelString == niveis) && (tipo == "abrir")){
              $("#tr-"+prefixo +'_' +  identificadorTr).show();
             // alert(prefixo +'_' +identificadorTr);
         }
         if((tipo == "fechar") && identificadorTr != sufixo){
              $("#tr-"+prefixo +'_' +  identificadorTr).hide();
              var imagem = $("#" + this.id.replace("tr-","img-"));
              imagem.removeAttr("src");
              imagem.attr("src","images/mais.jpg");
              imagem.removeAttr("alt");
              imagem.attr("alt","Expandir");
         }
    }
  });

  img.fadeIn("slow");
}

function fecharTodos(){
    
    var identificadorTr = "";
    var imagem = "";
    $(".arvore").each(function()
  {
    identificadorTr = this.id;
    identificadorTr = identificadorTr.substring(3);


    imagem = $("#" + identificadorTr);
      imagem.removeAttr("src");
        imagem.attr("src","images/mais.jpg");
        imagem.removeAttr("alt");
        imagem.attr("alt","Expandir");

    if(identificadorTr.indexOf("-") > -1){
      $("#tr-" + identificadorTr).hide();
    }
  });
}

function abrirTodos(){
    var identificadorTr = "";
    $(".arvore").each(function()
  {
    identificadorTr = this.id;
    identificadorTr = identificadorTr.substring(3);

    if(identificadorTr.indexOf("-") > -1){
        $("#tr-" + identificadorTr).show();
        var imagem = $("#" + identificadorTr);
        imagem.removeAttr("src");
        imagem.removeAttr("alt");
        imagem.attr("src","images/menos.jpg");
        imagem.attr("alt","Minimizar");
    }
  });
}
function replaceAll(str, de, para) {
  var pos = str.indexOf(de);
  while (pos > -1) {
    str = str.replace(de, para);
    pos = str.indexOf(de);
  }
  return (str);
}

function colapsar(chave,obj){

  var id = "tr[id^=tr-" + chave + "]";
  var src = $(obj).attr("src");
  var img = "img[id^=img-" + chave + "]";
  var parte = "";

  if(src.indexOf("mais") != -1){

    $(id).each(function(){
      $(this).show();
    });

    $(img).each(function(){
      $(this).attr("src",src.replace("mais","menos"));
    });

    $(obj).attr("src",src.replace("mais","menos"));
        $(".p_arvore").val('true');
  }else{

    $(id).each(function(){
       parte = $(this).attr("id").split("-");
       if( parte.length > 2 ){
        $(this).hide();
       }
    });

    $(img).each(function(){
      $(this).attr("src",src.replace("menos","mais"));
    });

    $(obj).attr("src",src.replace("menos","mais"));
        $(".p_arvore").val('false');
  }
}
function replaceExtChars(text) {
  text = text.replace(eval('/&/g'), '&amp;');
  fromTo = new Array('&AElig;','Æ','&Aacute;','Á','&Acirc;','Â','&Agrave;','À','&Aring;','Å','&Atilde;', 'Ã','&Auml;','Ä','&Ccedil;','Ç','&ETH;','Ð','&Eacute;','É','&Ecirc;','Ê','&Egrave;','È ','&Euml;','Ë','&Iacute;','Í','&Icirc;','Î','&Igrave;','Ì','&Iuml;','Ï','&Ntilde;','Ñ', '&Oacute;','Ó','&Ocirc;','Ô','&Ograve;','Ò','&Oslash;','Ø','&Otilde;','Õ','&Ouml;','Ö','&THORN; ','Þ','&Uacute;','Ú','&Ucirc;','Û','&Ugrave;','Ù','&Uuml;','Ü','&Yacute;','Ý','&aacute;', 'á','&acirc;','â','&aelig;','æ','&agrave;','à','&aring;','å','&atilde;','ã','&auml;','ä ','&brvbar;','¦','&ccedil;','ç','&cent;','¢','&copy;','©','&deg;','°','&eacute;','é', '&ecirc;','ê','&egrave;','è','&eth;','ð','&euml;','ë','&frac12;','½','&frac14;','¼','&frac34; ','¾','&gt;','>','&gt','>','&iacute;','í','&icirc;','î','&iexcl;','¡','&igrave;','ì','&iquest;','¿','&iuml;','ï', '&laquo;','«','&lt;','<','&lt','<','&mdash;','—','&micro;','µ','&middot;','·','&ndash;','–','&not;','¬','&ntilde;','ñ', '&oacute;','ó','&ocirc;','ô','&ograve;','ò','&oslash;','ø','&otilde;','õ','&ouml;','ö','&para;','¶','&plusmn;','±','&pound;',' £','&quot;','\"','&raquo;','»','&reg;','®','&sect;','§','&shy;','*','&sup1;','¹','&sup2;','²', '&sup3;','³','&szlig;','ß','&thorn;','þ','&tilde;','˜','&trade;','™','&uacute;','ú','&ucirc; ','û','&ugrave;','ù','&uuml;','ü','&yacute;','ý','&yen;','¥','&yuml;','ÿ');

  for (i=0; i < fromTo.length; i=i+2)
    //text = text.replace(fromTo[i+1]), fromTo[i]);
    text = replaceAll(text,fromTo[i+1],fromTo[i]);
  return (text);
}

function destacaLinha(){
  //alert('oi');
  if (typeof jQuery != 'undefined') {  
    $(document).ready(function() {
      $(".tudo tr").hover(
        function(){
          $(this).addClass("highlight");
        },
        function(){
          //alert('oi');
          $(this).removeClass("highlight");
        }
        )
    });
  }
}
destacaLinha();

function exportarArquivo(id){
  $(document).ready(function() {
    var elemento = "."+id;
    //$("#nada").remove();
    $("#botaoExcel").click(function() {
      var texto = $("<div>").append( $(elemento).eq(0).clone()).html();
      texto = texto.replace('id=tudo','id=nada');
      $("#conteudo").val(texto);
      $("#temp").attr('action',$('#caminho').val() + 'funcoes/arquivoExcel.php');
      $("#temp").submit();
    });
    $("#botaoWord").click(function(event) {
      var texto = $("<div>").append( $(elemento).eq(0).clone()).html();
      texto = texto.replace('id=tudo','id=nada');
      $("#conteudo").val(texto);
      $("#temp").attr('action',$('#caminho').val() + 'funcoes/arquivoWord.php');
      $("#temp").submit();
    });

    $("#botaoPDF").click(function(event) {
      $("#conteudo").val( $("<div>").append( $(elemento).eq(0).clone()).html());
      $("#temp").submit();
    });
  });
}
