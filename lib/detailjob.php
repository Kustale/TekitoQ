<HTML>
<HEAD>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<TITLE>item detail</TITLE>
</HEAD>
<BODY bgcolor="#98a0a5" text="#342d0c" link="#611b0c" vlink="#611b0c" alink="#611b0c">
<P>
<?php
include("data.php");
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
$word = "";
$id = $_GET['id'];
$id = mydecrypt($id);
$j["job"]=$id;
$j["skill"]=array("100");
$j=jobdata($j);
if(isset($j["id"])){
$img="../images/char1/".$j["id"]."0.png";
print("<img src=\"$img\">&nbsp;\n");
$img="../images/char1/".$j["id"]."1.png";
print("<img src=\"$img\"><BR>\n");
}
if(isset($j["name"]))
print("JobName : ".$j['name']."<BR>\n");
if(isset($j["rank"]))
print("Rank : ".$j['rank']."<BR>\n");
if(isset($j["equip"])){
foreach($j["equip"] as $val){
$type = typeofitem_detail($val);
$word.=$type.", ";
}
print("Equip : ".substr($word,0,-2)."<BR>\n");
}
if(isset($j["phy"]))
print("PhysicalAttack : ".$j["phy"]."%<BR>\n");
if(isset($j["mag"]))
print("MagicalAttack : ".$j["mag"]."%<BR>\n");
?>
<FORM>
<INPUT TYPE="button" VALUE="Close" onClick="window.close()">
</FORM>
</P>
</BODY>
</HTML>