function w(line) {
	document.write(line);
}
w('<html><head>');
w('<style type="text/css">');
w('.skin0{position:absolute; width:120px; border:1px solid #000000; background-color:#d0f0ff; font-family:Arial; line-height:12px; cursor:yellow; font-size:10px; z-index:100; visibility:hidden; }');
w('.menuitems{ padding-left:8px; padding-right:8px; border-color = green; color: blue; }');
w('.button {   padding-left:4px;   border: none ;   background-color:  #E8E8FF;   font-family: Arial, Helvetica, sans-serif;     font-size: 10px;    border-bottom-width: 0px;    border-bottom: 0px;    bottom: 0px;    margin-bottom: 0px;    padding-bottom: 0px; color: #000000;}');
w('<\/style>');
w('<script language="javascript">');
w('var menu_to; ');
w('var last_menu_to; ');
w('<\/script><\/head>');
w('<div align="left" id="ie5menu" class="skin0" onMouseover="highlightie5(event)" onMouseout="lowlightie5(event)" display:none>');
w('<form name="menuform">');
w('<div class="button"><input class="button" type="text" name="button" value=""><\/div>');
w('<div class="menuitems" onClick="c(1)">ignorieren ja/nein<\/div>');
w('<div class="menuitems" onClick="c(2)">mail senden<\/div>');
w('<div class="menuitems" onClick="c(3)">freund ja/nein<\/div>');
if (level2 == 'admin')
	w('<div class="menuitems" onClick="c(4)">knebeln<\/div>');
if (level2 == 'admin')
	w('<div class="menuitems" onClick="c(5)">kicken<\/div>');
if (level2 == 'admin')
	w('<div class="menuitems" onClick="c(6)">sperren<\/div>');
if (level2 == 'admin')
	w('<div class="menuitems" onClick="c(7)">aus dem chat kicken<\/div>');
if (level2 == 'admin')
	w('<div class="menuitems" onClick="c(8)">kicken und sperren<\/div>');
if (level2 == 'admin')
	w('<div class="menuitems" onClick="c(9)">blacklist<\/div>');
w('<\/div>');
w('<\/form>');
w('<script type="text/javascript">');
w('var display_url=0; ');
w('var ie5=document.all&&document.getElementById; ');
w('var ns6=document.getElementById&&!document.all; ');
w('if (ie5||ns6) ');
w('var menuobj=document.getElementById("ie5menu"); ');
w('var act =\'\'; ');
w('function showmenuie5(e){ ');

w('if(menu_to){ ');
w('	act = menu_to.replace(/\\+/g," "); ');
w('  document.menuform.button.value=(act); ');
w('  var rightedge=ie5? document.body.clientWidth-event.clientX : window.innerWidth-e.clientX; ');
w('  var bottomedge=ie5? document.body.clientHeight-event.clientY : window.innerHeight-e.clientY; ');
w('  if (rightedge<menuobj.offsetWidth) ');
w('  menuobj.style.left=ie5? document.body.scrollLeft+event.clientX-menuobj.offsetWidth : window.pageXOffset+e.clientX-menuobj.offsetWidth; ');
w('  else ');
w('  menuobj.style.left=ie5? document.body.scrollLeft+event.clientX : window.pageXOffset+e.clientX; ');
w('  if (bottomedge<menuobj.offsetHeight) ');
w('  menuobj.style.top=ie5? document.body.scrollTop+event.clientY-menuobj.offsetHeight : window.pageYOffset+e.clientY-menuobj.offsetHeight; ');
w('  else ');
w('  menuobj.style.top=ie5? document.body.scrollTop+event.clientY : window.pageYOffset+e.clientY; ');
w('   ');
w('menuobj.style.left=\'75px\';');
w('  menuobj.style.visibility="visible"; ');
w('  return false; ');
w('} ');
w('} ');
w(' ');
w('function hidemenuie5(e){ ');
w('menuobj.style.visibility="hidden"; ');
w('} ');
w(' ');
w('function highlightie5(e){ ');
w('var firingobj=ie5? event.srcElement : e.target; ');
w('if (firingobj.className=="menuitems"||ns6&&firingobj.parentNode.className=="menuitems"){ ');
w('if (ns6&&firingobj.parentNode.className=="menuitems") firingobj=firingobj.parentNode ; ');
w('firingobj.style.backgroundColor="#D8D8EE"; ');
w('firingobj.style.color=""; ');
w('if (display_url==1) ');
w('window.status=event.srcElement.url; ');
w('} ');
w('} ');
w(' ');
w('function lowlightie5(e){ ');
w('var firingobj=ie5? event.srcElement : e.target; ');
w('if (firingobj.className=="menuitems"||ns6&&firingobj.parentNode.className=="menuitems"){ ');
w('if (ns6&&firingobj.parentNode.className=="menuitems") firingobj=firingobj.parentNode ; ');
w('firingobj.style.backgroundColor=""; ');
w('firingobj.style.color=""; ');
w('window.status=""; ');
w('} ');
w('} ');
w(' ');
w('function jumptoie5(e){ ');
w('  return; ');
w('} ');
w(' ');
w('if (ie5||ns6){ ');
w('  menuobj.style.display=""; ');
w('  document.oncontextmenu=showmenuie5; ');
w('  document.onclick=hidemenuie5; ');
w('} ');
w(' ');
w(' ');
w(' ');
w(' ');
w('function a(nick){ ');
w(' if (menuobj.style.visibility != \'visible\') { ');
w('  menu_to = nick; ');
w('  last_menu_to = menu_to; ');
w(' } ');
w('   ');
w('} ');
w(' ');
w('function b(){ ');
w('} ');
w(' ');
w('function c(action){ ');
w('  switch (action){ ');
w(' ');
w('    case 1:  ');
w(' url=\'schreibe.php?http_host=\'+http_host+\'&id=\'+id+\'&text=/ignore%20\'+menu_to;');
w('  parent.frames[\'schreibe\'].location=url;');
w('      break; ');
w('    case 2:  ');
w(' url=\'mail.php?http_host=\'+http_host+\'&id=\'+id+\'&aktion=neu2&neue_email[an_nick]=\'+menu_to;');
w('window.open(url,\'640_' + menu_to
		+ '\',\'resizable=yes,scrollbars=yes,width=780,height=580\');');
w('      break; ');
w('    case 3:  ');
w(' url=\'schreibe.php?http_host=\'+http_host+\'&id=\'+id+\'&text=/freund%20\'+menu_to;');
w('  parent.frames[\'schreibe\'].location=url;');
w('      break; ');
w('    case 4:  ');
w(' url=\'schreibe.php?http_host=\'+http_host+\'&id=\'+id+\'&text=/gag%20\'+menu_to;');
w('  parent.frames[\'schreibe\'].location=url;');
w('      break; ');
w('    case 5:  ');
w(' url=\'schreibe.php?http_host=\'+http_host+\'&id=\'+id+\'&text=/kick%20\'+menu_to;');
w('  parent.frames[\'schreibe\'].location=url;');
w('      break; ');
w('    case 6:  ');
w('for (i=0;i<liste.length;i=i+9) {');
w('s_nick=liste[i+2];');
w('s_ip=liste[i+4];');
w('if (s_nick==menu_to) sperren(\'\',s_ip,menu_to)');
w('}');
w('      break; ');
w('    case 7:  ');
w('for (i=0;i<liste.length;i=i+9) {');
w('s_uid=liste[i];');
w('s_nick=liste[i+2];');
w('s_ip=liste[i+4];');
w('if (s_nick==menu_to) s_nummer=s_uid;');
w('}');
w(' url=\'user.php?http_host=\'+http_host+\'&id=\'+id+\'&kick_user_chat=1&user=\'+s_nummer;');
w('  parent.frames[\'schreibe\'].location=url;');
w('      break; ');
w(' ');
w('    case 8:  ');
w('for (i=0;i<liste.length;i=i+9) {');
w('s_uid=liste[i];');
w('s_nick=liste[i+2];');
w('s_ip=liste[i+4];');
w('if (s_nick==menu_to) s_nummer=s_uid;');
w('}');
w(' url=\'user.php?http_host=\'+http_host+\'&id=\'+id+\'&kick_user_chat=1&user=\'+s_nummer;');
w('  parent.frames[\'schreibe\'].location=url;');
w('for (i=0;i<liste.length;i=i+9) {');
w('s_nick=liste[i+2];');
w('s_ip=liste[i+4];');
w('if (s_nick==menu_to) sperren(\'\',s_ip,menu_to)');
w('}');
w('      break; ');
w('    case 9:  ');
w(' url=\'blacklist.php?http_host=\'+http_host+\'&id=\'+id+\'&aktion=neu&neuer_blacklist[u_nick]=\'+menu_to;');
w('window.open(url,\'640_' + menu_to
		+ '\',\'resizable=yes,scrollbars=yes,width=780,height=580\');');
w('      break; ');

w('    default: ');
w('      break; ');
w('  } ');
w('  return false; ');
w('} ');
w('<\/script>');
