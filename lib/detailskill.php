<HTML>
<HEAD>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<TITLE>Skill detail</TITLE>
<STYLE TYPE="TEXT/CSS"> 
<!-- 
.lll { width: 50% ;text-align: right} 
.rrr { width: 100% } 
--> 
</STYLE> 
</HEAD>
<BODY bgcolor="#98a0a5" text="#342d0c" link="#611b0c" vlink="#611b0c" alink="#611b0c">
<P>
<?php
include("data.php");
function method($num){
switch($num){
case 0: return "ATTACK";
case 1: return "DEFENCE";
case 2: return "SUPPORT";
case 3: return "SPECIAL";
case 4: return "UNIQUE";
default: return "???";
}
}
function mydecrypt($num){
$ky = array("A","B","C","D","E","F","G","H","I","J");
$total=array_search(substr($num,0,1),$ky);
$ky = array_flip($ky);
foreach($ky as $key => $val){
$ky[$key]+=$total;
if($ky[$key]>9) $ky[$key]-=10;
}
$num = strtr(substr($num,1), $ky);
$num-=37126;
return "$num";
}
function typeofwep($num){
switch($num){
case "101": $name="검"; break;
case "102": $name="단검"; break;
case "103": $name="스태프"; break;
case "104": $name="활"; break;
}
return "$name";
}
$type = "";
$ratio = "";
$id = $_GET['id'];
$id = mydecrypt($id);
if(!isset($id)) exit;
$s=skilldetail($id);
if(isset($s["disable"]))
echo "<CENTER>이 스킬은 사용할 수 없습니다</CENTER><BR>\n";
echo "<SPAN class=\"lll\">".$s['name']."</SPAN>\n";
if(isset($s["method"])){
if(0<=$s["method"]&&$s["method"]<=4)
echo " (PhysicalAttack)\n";
else if(5<=$s["method"]&&$s["method"]<=9)
echo " (MagicalAttack)\n";
else if($s["method"]=="99")
echo " (Passive)\n";
}
echo "<BR><CENTER>".$s['exp']."</CENTER><BR>\n";
if(isset($s["weplimit"])){
echo "<SPAN class=\"lll\">무기제한:</SPAN>\n";
foreach($s["weplimit"] as $val)
$type .= typeofwep($val).", ";
print(substr($type,0,-2)."<BR>");
}
if(isset($s["sp"]))
echo "<SPAN class=\"lll\">소모SP:</SPAN>".$s['sp']."<BR>\n";
$power = (isset($s["str"]) ? $s["str"] : 0)+(isset($s["int"]) ? $s["int"] : 0)+(isset($s["dex"]) ? $s["dex"] : 0)+(isset($s["spd"]) ? $s["spd"] : 0);
if($power>0){
echo "<SPAN class=\"lll\">위력:</SPAN>$power% x ".$s['target']."<BR>\n";
if(isset($s["str"])){
if($s["str"]>0) $sids["str"]=$s["str"];
}
if(isset($s["int"])){
if($s["int"]>0) $sids["int"]=$s["int"];
}
if(isset($s["dex"])){
if($s["dex"]>0) $sids["dex"]=$s["dex"];
}
if(isset($s["spd"])){
if($s["spd"]>0) $sids["spd"]=$s["spd"];
}
$min = min($sids);
echo "<SPAN class=\"lll\">필요스탯&nbsp;</SPAN>\n";
foreach($sids as $key => $val)
$ratio .= sprintf("%s%1.0f:",$key,$val/$min);
print(substr($ratio,0,-1)."<BR>\n");
}
if(isset($s["hit"]))
echo "<SPAN class=\"lll\">명중률:</SPAN>".$s['hit']."%<BR>\n";
if(isset($s["wepeffect"]))
echo "<SPAN class=\"lll\">웨폰이펙트:</SPAN>".$s['wepeffect']."<BR>\n";
if(isset($s["width"]))
echo "<SPAN class=\"lll\">데미지안정성:</SPAN>".$s['width']."<BR>\n";
if(isset($s["INV_BATK"]))
echo "<CENTER>Ignore back line damage redcuing.</CENTER>\n";
?>
<FORM>
<br>
<CENTER><INPUT TYPE="button" VALUE="닫는다" onClick="window.close()"></CENTER>
</FORM>
</P>
</BODY>
</HTML>