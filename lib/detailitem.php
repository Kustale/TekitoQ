<HTML>
<HEAD>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<TITLE>item detail</TITLE>
</HEAD>
<BODY bgcolor="#98a0a5" text="#342d0c" link="#611b0c" vlink="#611b0c" alink="#611b0c">
<P>
<?php
include("data.php");
function itemsteal($a){
if($a==0) echo "훔칠 수 없습니다<br>\n";
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
$id = $_GET['id'];
$id = mydecrypt($id);
$ary1 = array("exp" => "","hp" => "HP:","sp" => "");
$ary2 = array("weight" => "무게:","atk" => "공격력:","mag" => "마법공격력:","buy" => "구입가격:","sell" => "판매가격:");
$i = itemlist($id);
$type = typeofitem_detail($i['type']);
print '<img src="../images/icon/'.$i['img'].'.png"><br>'."\n";
print $i['name'].'('.$type.')<br>'."\n";

foreach($ary1 as $key => $val){
if(isset($i[$key])){
echo "$val".$i[$key]."<br>\n";
}
}
if(isset($i["limit"]))
echo "레벨제한: Lv".$i['limit']."<BR>\n";
if(isset($i['d_div'])||isset($i['d_min'])||isset($i['m_div'])||isset($i['m_min'])){
if(!isset($i['d_div'])) $i['d_div'] = 0;
if(!isset($i['d_min'])) $i['d_min'] = 0;
if(!isset($i['m_div'])) $i['m_div'] = 0;
if(!isset($i['m_min'])) $i['m_min'] = 0;
echo "방어력: ".$i['d_div']." + ".$i['d_min']."<br>\n";
echo "마법방어력: ".$i['m_div']." + ".$i['m_min']."<br>\n";
}
foreach($ary2 as $key => $val){
if(isset($i[$key])){
echo "$val".$i[$key]."<br>\n";
}
}
itemsteal($i['steal']);
?>
<FORM>
<INPUT TYPE="button" VALUE="닫는다" onClick="window.close()">
</FORM>
</P>
</BODY>
</HTML>