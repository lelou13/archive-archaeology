<!--
/*
 * THIS SOURCE FILE, ITS MACHINE READABLE FORM, AND ANY REPRESENTATION
 * OF THE MATERIAL CONTAINED HEREIN ARE OWNED BY GEOTRUST.
 * THESE MATERIALS ARE PROPRIETARY AND CONFIDENTIAL AND MAY NOT BE
 * REPRODUCED IN ANY FORM WITHOUT THE PRIOR WRITTEN PERMISSION OF
 * GeoTrust.
 * COPYRIGHT (C) 1999-2003 BY GEOTRUST, INC.
 * ALL RIGHTS RESERVED
 */
gt__ua=navigator.userAgent.toLowerCase();
gt__isie=(gt__ua.indexOf("msie")!=-1);
gt__isop=(gt__ua.indexOf("opera")!=-1);
gt__msg="Click for company profile";
gt__rcm="This icon is protected.\nPlease use left button to view company information.";
gt__bma=parseInt(navigator.appVersion);
gt__s="smarticon";
gt__lb="#";
gt__si=gt__s+".geotrust.com/";
gt__hn=window.location.hostname;
gt__sip="https://"+gt__si+"smarticonprofile";
gt__rsip=gt__sip+"?Referer="+window.location.protocol+"//"+gt__hn;
gt__is="//"+gt__si+"smarticon?ref="+gt__hn;
gt__ph=600;
if(screen!=null)if(screen.height<670)gt__ph=screen.height-70;
gt__ws="status=1,location=0,scrollbars=1,width=400,height="+gt__ph;
gt__w=null;
if(gt__ua.indexOf("msie 5")!=-1)gt__bma=5;
if(gt__ua.indexOf("msie 6")!=-1)gt__bma=6;
function gt__sp(){
 gt__w=window.open(gt__rsip,'GT__SIP',gt__ws);
 if ( gt__w != null ) gt__w.focus();
}
function gt__dc(e){
 if (gt__isop||document.addEventListener) {
  var eit=(e.target.name=="rqahxkxq");
   if (eit){
    if (e.which==3){
	  alert(gt__rcm);
	  return false;
    }else if(e.which==1){
     gt__sp();
     return false;
    }
   }
 }else if(document.captureEvents) {
  var tgt=e.target.toString();
  var eit=(tgt.indexOf(gt__s)!=-1);
  if (eit){
   if (e.which==3){
    alert(gt__rcm);
    return false;
   }else if(e.which==1) {
    gt__sp();
    return false;
   }
  }
 }
 return true;
}
function gt__md(e){
 if(typeof event != 'undefined'){  if (event.button==2){
  alert(gt__rcm);
  return false;
 }else if(event.button==1){
  if(gt__isie&&(gt__bma<=4)) {
   return true;
  }else{
   gt__sp();
   return false;
  }
 }}
 return false;
}
if(gt__isie&&(gt__bma<=4)) {
 document.write("<A TABINDEX=\"-1\" HREF=\""+gt__rsip+"\" onmousedown=\"return gt__md();\"><IMG NAME=\"rqahxkxq\" HEIGHT=\"55\" WIDTH=\"115\" BORDER=\"0\" SRC=\""+gt__is+"\" ALT=\""+gt__msg+"\" oncontextmenu=\"return false;\"></A>");
}
else if(gt__isie&&(gt__bma>=5)&&!gt__isop) {
 document.write("<A TABINDEX=\"-1\" onmouseout=\"window.status='';\" onmouseover=\"this.style.cursor='hand'; window.status='"+gt__msg+"';\" onmousedown=\"return gt__md();\"><IMG NAME=\"rqahxkxq\" HEIGHT=\"55\" WIDTH=\"115\" BORDER=\"0\" SRC=\""+gt__is+"\" ALT=\""+gt__msg+"\" oncontextmenu=\"return false;\"></A>");
}
else { 
 document.write("<A TABINDEX=\"-1\" HREF=\""+gt__rsip+"\" onclick=\"return gt__md();\" target=\"GT__SIP\"><IMG NAME=\"rqahxkxq\" HEIGHT=\"55\" WIDTH=\"115\" BORDER=\"0\" SRC=\""+gt__is+"\" ALT=\""+gt__msg+"\" oncontextmenu=\"return false;\"></A>");
}
if (document.addEventListener){
 document.addEventListener('mouseup',gt__dc,true);
}
else {
 if (document.layers){
  document.captureEvents(Event.MOUSEDOWN);
 }
 document.onmousedown=gt__dc;
}
// -->

