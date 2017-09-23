<html>
<head>
<title>Разделение сети на подсети ipv6</title>
<link rel="stylesheet" href="style.css">
<link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
<script Language="JavaScript">
function XmlHttp()
{
var xmlhttp;
try{xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");}
catch(e)
{
 try {xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");} 
 catch (E) {xmlhttp = false;}
}
if (!xmlhttp && typeof XMLHttpRequest!='undefined')
{
 xmlhttp = new XMLHttpRequest();
}
  return xmlhttp;
}
 
function ajax(param)
{
                if (window.XMLHttpRequest) req = new XmlHttp();
                method=(!param.method ? "POST" : param.method.toUpperCase());
 
                if(method=="GET")
                {
                               send=null;
                               param.url=param.url+"&ajax=true";
                }
                else
                {
                               send="";
                               for (var i in param.data) send+= i+"="+param.data[i]+"&";
                               send=send+"ajax=true";
                }
 
                req.open(method, param.url, true);
                if(param.statbox)document.getElementById(param.statbox).innerHTML = '<img src="image.gif">';
                req.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                req.send(send);
                req.onreadystatechange = function()
                {
                               if (req.readyState == 4 && req.status == 200) //если ответ положительный
                               {
                                               if(param.success)param.success(req.responseText);
                               }
                }
}
</script>
</head>
<body>
                <?php require('prefixes.php'); ?>
                <!-- Подключение библиотеки jQuery -->
                <script src="js/jquery.js"></script>
                <!-- Подключение jQuery плагина Masked Input -->
                <script src="js/jquery.maskedinput.min.js"></script>
                <!-- Сам скрипт для маски -->
                <div class="lab-title">Разделение сети на подсети (протокол IPv6)</div>
                <div class="spoiler">
                    <input type="checkbox" id="spoilerid_1">

                    <label for="spoilerid_1">
                    ТЕОРЕТИЧЕСКАЯ ЧАСТЬ: ПРИНЦИП РАЗДЕЛЕНИЯ СЕТИ НА ПОДСЕТИ ДЛЯ ПРОТОКОЛА IPv6
                    </label>

                    <div class="spoiler_body">
                    <?php require('theory.php') ?>
                    </div>

                </div>
                <form action="get_ajax.php">
                <p><b>IP адрес</b></p>
                <p><input id="area_1" name="area_1" style="height:25px; width:500px;" type="text" placeholder="**** : **** : **** : **** : **** : **** : **** : ****"></p>
                <p><b>Длина префикса</b></p>
                <p><select name="select" id="select">
                  <?php 
                    for($i = 0; $i < count($prefixes); $i++) {
                      echo "<option value=". $prefixes[$i] .">" . $prefixes[$i] . "</option>"; 
                    }
                   ?>
                </select></p>
                <p><b>Количество подсетей</b></p>
                <p><input type="number" min="2" id="area_2" name="area_1" placeholder="2"></p>
                <input type='button' value='РАЗДЕЛИТЬ' onclick='
                               ajax({
                                                               url:"get_ajax.php",
                                                               statbox:"status",
                                                               method:"POST",
                                                               data:
                                                               {
                                                                              first_area:document.getElementById("area_1").value,
                                                                              second_area:document.getElementById("area_2").value,
                                                                              select_list:document.getElementById("select").value
                                                              },
                                                               success:function(data){document.getElementById("status").innerHTML=data;}
                                               })'
                >
                </form>

                <div id="status">
                Здесь будет отображаться информация о разделении сети на подсети и параметрах сети и подсетей.
                </div>
                <script>
                $(function(){
                $("#area_1").mask("**** : **** : **** : **** : **** : **** : **** : ****");
                });
                </script>
</body>
</html>