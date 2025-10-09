<?php
/**********************************************************

Tekito Quest를 만든자 : TEKITO PROJECT for JUICE( http://tekito.kanichat.com/ )
배포한자 : gagax.com, Kustale
소스 고치고 번역한자 :
1. Angelhalo( http://angelhalo.org/ ), 옥탑방 연구소( http://mephu.egloos.com ), Enterbrain (번역, 일부 수정, 맵, 캐릭터 칩 등등?)
2. Kustale ( jinsu8527@gmail.com, http://blog.naver.com/dk00007 ) (소스 최적화 및 간략화, 마개조, 덜된 번역 완료[일부는 안 됬을 수도 있음. 보이는 것만 했으니...])

이 게임 소스는 Opensource입니다. (아마도)

개꿀대로 수정해도 아무말 없십니다.
소스만 제대로 보면 뭘 수정 해도 되는지 보입니다.
단, 위의 저작권 내용은 명시 해주십시오.
저작권 삭제시 개인은 걍 신경 안쓰지만
1차 개조자 및 번역자에게
무슨 일을 당할 수도 있음이... (옥탑방 연구소 여기는 game114.kr이 사고쳐서 뿔남. 꼭 저작권 명시... 꼭)

2차 창작 및 배포...
game114.kr에게 문의...
(근데 망한거 같은데...
그럼 배포 수정해도 상관 없나?)

공동(파일+함수) 부분(공동소스가 다 모인부분인데 잘못건들시 오류 왕창 생기거나, 일부 기능이 맛감)

작업한것 :

수정 : array부분적 수정 (*추가 : (array))
수정 : ereg -> preg_match (에고 힘들어..)
망할 php5..ㅠㅠ

EUC-KR -> UTF-8 변환
Call-time pass-by-reference 관련 오류 수정 (&$ -> $)

file.php에 있는거 모두 이동
template.php에 있는거 모두 이동
아랫쪽 래디오 버튼이 안되는 것 수정

php 업그레이드에 의한 제거된 변수 관련 수정

count 함수 오류 나는거 수정 (count를 _count로 바꾸고 함수 추가로 isset + count로 구현)

가입 이름란에 한국어, 영어, 숫자 띄어쓰기 만 가능한 기능 추가

php8 되자마자 함수, 변수 Warning, Error뜨는거 찾아내서 다 고침...(는 전부 고치기는 힘듬)

처음에 플레이어 가입할때 스테이터스 1~20 인식 못하는거 고침
배틀 들어가자 마자 터지는거 고침(근데 적 캐릭이 둘이 되버리는 엌)

파티 짠 상태로 전투 들어갈때 오류 뜨는거 고침

파티 벗어날때 오류 뜨는거 고침

파티 없거나 탈퇴 했을때 또 오류 나는거 수정...
+ 파티 없을때 파티 요청 걸면 오류 나거나 아무것도 안뜨는거 적절한 메세지 추가.

+ 파티 강퇴 당했거나 상대방이 파티 해체 하자마자 만들었을때 전투에 버그 터지는거 수정.
(답은 여기 코드 중에 있다고 KADORA)
당신은 강퇴당했습니다 라는 메세지는 추가 안함.

**********************************************************/
function _count($a){
if(isset($a)){
return count($a);
}else{
return 0;
}
}
function mes($text){
global $mes;
$mes .= $text;
}
function fileload($file){
if(file_exists("$file")){
$pointer = fopen("$file", "r") or ErrShow("파일(".$file.")을 열지 못했습니다.");
$list = file("$file");
fclose($pointer);
}
return $list;
}
function writefile($file,$text,$text2=NULL){
$pointer = fopen("$file", "w") or ErrShow("파일(".$file.")을 열지 못했습니다.");
flock($pointer, LOCK_EX);
ftruncate($pointer,0);
rewind($pointer);
if(!empty($text2))
fputs($pointer, $text2);
fputs($pointer, $text);
flock($pointer, LOCK_UN);
fclose($pointer);
}
function load_id($id){
$file = "./data/user/".$id.".txt";
if(file_exists("$file")){
$data = parse_ini_file("$file") or ErrShow("Error to Load INI : ".$file);
$data["equip"] = explode("<>",$data["equip"]);
$data["item"] = explode("<>",$data["item"]);
$data["skill"] = explode("<>",$data["skill"]);
$data["turn"] = explode("<>",$data["turn"]);
$data["class"] = explode("<>",$data["class"]);
if(empty($data["class"][0])){
$data["class"]=array("{$data['job']}"."1");
$data["jlv"]=1;
$data["jxp"]=0;
$data["turn"] = array("100","100","100","100","100");
$data["skill"]=array("100");
$data["sklp"]=1;
}
$dif = time() - $data["timestamp"];
$data["timestamp"] = time();
$data["hp"] += $dif*HP_REC_RATE;
if($data["hp"] >= $data["maxhp"]){
$data["hp"] = $data["maxhp"];
$data["state"] = 0;
}
$data["sp"] += $dif*SP_REC_RATE;
if($data["sp"] > $data["maxsp"]) $data["sp"] = $data["maxsp"];
$data["jdata"]=loadjob($data);
$data["fstr"]=$data["str"]+$data["jdata"]["bonus"]["str"];
$data["fint"]=$data["int"]+$data["jdata"]["bonus"]["int"];
$data["fdex"]=$data["dex"]+$data["jdata"]["bonus"]["dex"];
$data["fspd"]=$data["spd"]+$data["jdata"]["bonus"]["spd"];
return $data;
} else {
return false;
}
}
function write_id($data){
$word = "";
if(isset($data["mons"])) return false;
$file = "./data/user/".$data["id"].".txt";
if(file_exists("$file")){
if(isset($data["equip"]))
$data["equip"] = implode("<>",$data["equip"]);
if(isset($data["item"]))
$data["item"] = implode("<>",$data["item"]);
if(isset($data["skill"]))
$data["skill"] = implode("<>",$data["skill"]);
if(isset($data["turn"]))
$data["turn"] = implode("<>",$data["turn"]);
if(isset($data["class"]))
$data["class"] = implode("<>",$data["class"]);
unset($data["fstr"]);
unset($data["fint"]);
unset($data["fdex"]);
unset($data["fspd"]);
foreach($data as $key => $val){
if(is_array($val))
continue;
$word .= "$key=\"$val\"\n";
}
$pointer = fopen("$file", "w+") or ErrShow("파일(".$file.")을 열지 못했습니다.");
flock($pointer, LOCK_EX);
ftruncate($pointer,0);
rewind($pointer);
fputs($pointer, $word);
flock($pointer, LOCK_UN);
fclose($pointer);
}
}
function load_msg($id){
$word = "";
$file = "./data/message/".$id.".txt";
if(file_exists("$file")){
$msg = parse_ini_file("$file") or ErrShow("Error to Load INI : ".$file);
if(isset($msg["mail"]))
$msg["mail"]=explode("<>",$msg["mail"]);
return $msg;
}
return false;
}
function loadworld(){
$file="./data/worldstat.txt";
if(file_exists($file))
$world = parse_ini_file("$file") or ErrShow("Error to Load INI : ".$file);
if(isset($world["level_rank_id"]))
$world["level_rank_id"]=explode("<>",$world["level_rank_id"]);
return $world;
}
function saveworld(&$world){
$word = "";
foreach($world as $key =>$val){
if(is_array($world["$key"]))
$world["$key"] = implode("<>",$world["$key"]);
$word .= "$key=\"$world[$key]\"\n";
}
$file="./data/worldstat.txt";
if(file_exists($file))
writefile($file,$word);
}
function delmap(&$map,&$del_id){
$whois = parse_ini_file("./data/whois.txt") or ErrShow("Error to Load INI : ".$file);
$charlist = explode("<>",$whois["$map"]);
if(in_array($del_id,$charlist)){
$num = array_search($del_id,$charlist);
unset($charlist["$num"]);
$whois["$map"] = implode("<>",$charlist);
foreach($whois as $key => $value)
$word .= "$key=\"$value\"\n";
writefile("./data/whois.txt",$word);
}
}
function load_party($num,$myparty=NULL){
$file = "./data/party/".$num.".txt";
if(!file_exists("$file")){
if($myparty){
$temp = '
<FORM style="margin:0" action="index.php" method="post">
당신의 파티가 해산되었습니다<BR><BR>
<INPUT type="submit" name ="order" value="party">
</FORM>
';
mes($temp);
}
return false;
} else {
$data = parse_ini_file("$file") or ErrShow("Error to Load INI : ".$file);
if(isset($data["front"])) $data["front"] = explode("<>",$data["front"]);
if(isset($data["back"])) $data["back"] = explode("<>",$data["back"]);
if(isset($data["request"])) $data["request"] = explode("<>",$data["request"]);
if(isset($data["chat"])) $data["chat"] = explode("<>",$data["chat"]);
if(isset($data["sub"])) $data["sub"] = explode("<>",$data["sub"]);
return $data;
}
}
function saveparty($party){
$word = "";
$file="./data/party/".$party["num"].".txt";
$head=array("key=","front=","back=");
if(isset($party["front"])){
if(_count($party["front"])==0) unset($party["front"]);
else $party["front"]=implode("<>",$party["front"]);
}
if(isset($party["back"])){
if(_count((array)$party["back"])==0) unset($party["back"]);
else $party["back"]=implode("<>",$party["back"]);
}
if(isset($party["request"])){
if(_count((array)$party["request"])==0) unset($party["request"]);
else $party["request"]=implode("<>",$party["request"]);
}
if(isset($party["chat"])){
if(_count((array)$party["chat"])==0) unset($party["chat"]);
else {
if(_count($party["chat"])>15)
array_pop($party["chat"]);
$party["chat"]=implode("<>",$party["chat"]);
}
}
if(isset($party["sub"])){
if(_count((array)$party["sub"])==0) unset($party["sub"]);
else $party["sub"]=implode("<>",$party["sub"]);
}
foreach($party as $key => $value)
$word .= "$key=\"$value\"\n";
writefile($file,$word);
}
function write_msg(&$data,$msg){
$file = "./data/message/".$data["id"].".txt";
foreach($msg as $key => $value)
$word .= "$key=\"$value\"\n";
writefile($file,$word);
}
function breakparty(&$data){
if(!isset($data["party"])){
mes("파티가 뭐임?<BR>\n");
return false;
}
$party=load_party($data["party"]);
if($data["id"]==$party["leader"]){
$file = "./data/party/".$data["party"].".txt";
$btlfile = "./data/party/".$data["party"]."det.txt";
if(file_exists($file))
unlink($file);
if(file_exists($btlfile))
unlink($btlfile);
mes("파티가 해산되었습니다.<BR>\n");
unset($data["party"]);
makeparty();
} else {
mes("알수없는");
return false;
}
}
function seelastbattle(&$data){
if(!isset($data["party"])) return false;
$party=load_party($data["party"]);
$file="./data/party/".$party["num"]."det.txt";
if(file_exists($file))
$html = fileload($file);
foreach($html as $val) mes($val);
}
function addmap($a_map, $l_map, &$data, $message="0"){
$word = "";
$whois = parse_ini_file("./data/whois.txt");
if(!empty($a_map)){
if(empty($whois["$a_map"])){
$whois["$a_map"] = $data["id"];
} else {
$charlist = explode("<>",$whois["$a_map"]);
$charlist = array_unique($charlist);
array_unshift($charlist,$data["id"]);
if(empty($charlist[0])){
unset($charlist[0]);
}
$whois["$a_map"] = implode("<>",$charlist);
}
}
if(!empty($l_map)){
$charlist = explode("<>",$whois["$l_map"]);
$charlist = array_unique($charlist);
if(in_array($data["id"],$charlist)){
$num = array_search($data["id"],$charlist);
unset($charlist["$num"]);
}
$whois["$l_map"] = implode("<>",$charlist);
}
$data["map"] = $a_map;
$name = mapname($a_map);
$data["mdata"]["name"] = $name;
foreach($whois as $key => $value)
$word .= "$key=\"$value\"\n";
writefile("./data/whois.txt",$word);
if($message=="1")
mes("You move to $name.<br>");
elseif($message=="2")
mes("{$data['name']} move to $name.<br>");
return true;
}
function fixmapmovebug(&$data,&$char){
if($char["map"]==$data["map"]) return false;
$whois = parse_ini_file("./data/whois.txt") or ErrShow("Error to Load INI : ".$file);
$charlist = explode("<>",$whois["{$data['map']}"]);
if(in_array($char["id"],$charlist)){
$num = array_search($char["id"],$charlist);
unset($charlist["$num"]);
$whois["".$data['map'].""] = implode("<>",$charlist);
foreach($whois as $key => $value)
$word .= "$key=\"$value\"\n";
writefile("./data/whois.txt",$word);
}
}
function whois($map){
if(file_exists("./data/whois.txt")){
$data = parse_ini_file("./data/whois.txt") or ErrShow("Error to Load INI : ".$file);
if(isset($data["$map"])){
$mdata["char"] = explode("<>",$data["$map"]);
$mdata["name"] = mapname($map);
return $mdata;
} else {
mes("where are you?<BR>");
return NULL;
}
} else {
ErrShow("ERROR:mapfile<br>");
return false;
}
}
function savebtldetail(&$base_fre,$whostart=NULL){
global $mes;
if(isset($base_fre["0"]["party"])){
$party = load_party($base_fre["0"]["party"]);
if($party != false){
$file="./data/party/".$party["num"]."det.txt";
if(!empty($whostart))
$whostart = "This battle has started by $whostart.<BR>\n";
writefile($file,$mes,$whostart);
}
}
}
function openbattle($friend,$enemy,$name=NULL){
return battle($friend,$enemy,$name);
}
function nmedetail($num){
return mobdetail($num);
}
function loadjob($data){
return jobdata($data);
}
function loadskill($num){
return skilldetail($num);
}
function outputidetail($target){
return itemlist($target);
}
function load_npc(&$data,$target){
return npcdata($data,$target);
}
function getorder(){
if(isset($_POST['order'])) return $_POST['order'];
if(isset($_POST['battle_x'])) return "mapbattle";
if(isset($_POST['search_x'])) return "search";
if(isset($_POST['shop_x'])) return "shop";
if(isset($_POST['changeposition_x'])) return "changeposition";
if(isset($_POST['learnskill_x'])) return "learnskill";
if(isset($_POST['up_str_x'])) return "up_str";
if(isset($_POST['up_int_x'])) return "up_int";
if(isset($_POST['up_dex_x'])) return "up_dex";
if(isset($_POST['up_spd_x'])) return "up_spd";
if(isset($_POST['inn_x'])) return "inn";
if(isset($_POST['smith_x'])) return "smith";
if(isset($_POST['storage_x'])) return "storage";
if(isset($_POST['casino_x'])) return "casino";
if(isset($_POST['bar_x'])) return "bar";
if(isset($_POST['dungeon_x'])) return "dungeon";
if(isset($_POST['library_x'])) return "library";
if(isset($_POST['guild_x'])) return "guild";
return false;
}
function hiddenorder() {
if(isset($_POST['hid'])) return $_POST['hid'];
return false;
}
function login_check($id,$pass,$data){
if($id == (isset($data["id"]) ? $data["id"] : "") && md5((string)$pass) == (isset($data["pass"]) ? $data["pass"] : "") && $id != ""){
return true;
}else{
return false;
}
}
function mapstatimg($num){
switch($num){
case "0": return "m_city01";
case "1": return "m_village01";
case "2": return "m_grsland01";
case "3": return "m_waste01";
case "4": return "m_ecoast01";
case "5": return "m_snow01";
case "6": return "m_grsland01";
default: return "m_grsland01";
}
}
function facility($num){
switch($num){
case "100":
case "113":
case "122":
case "123":
case "127":
case "146": return array("search","shop1");
case "103": return array("search","shop1");
default: return array("battle1","search");
}
}
function char_make() {
$errmes="<ul>";
$u_id = $_POST['u_id'];
$u_pass = $_POST['u_pass'];
$u_name = $_POST['u_name'];
$u_gender = $_POST['u_gender'];
$u_job = $_POST['u_job'];
$u_str = $_POST['u_str'];
$u_int = $_POST['u_int'];
$u_dex = $_POST['u_dex'];
$u_spd = $_POST['u_spd'];
$u_id = htmlspecialchars($u_id, ENT_QUOTES);
$u_pass = htmlspecialchars($u_pass, ENT_QUOTES);
$E = 0;
switch(1){
case 1:
if($u_id == ""){
$errmes.= "<li>id미입력</li><br>";
$E = 1;
}
case 2:
if(preg_match("/[^a-zA-Z0-9]+/",$u_id)){
$errmes.= "id에 반각문자 이외의 문자를 이용하셨습니다</li><br>";
$E = 1;
}
case 3:
if(!preg_match("/[a-zA-Z0-9]{4,8}/",$u_id)){
$errmes.= "<li>id는 4~8자까지 가능합니다</li><br>";
$E = 1;
}
case 4:
if($u_pass == ""){
$errmes.= "<li>pass미입력</li><br>";
$E = 1;
}
case 5:
if(preg_match("/[^a-zA-Z0-9]+/",$u_pass)){
$errmes.= "<li>pass에 반각문자 이외의 문자를 이용하셨습니다</li><br>";
$E = 1;
}
case 6:
if(!preg_match("/[a-zA-Z0-9]{4,12}/",$u_pass)){
$errmes.= "<li>pass는 4~8자까지 가능합니다</li><br>";
$E = 1;
}
case 7:
if($u_name == ""){
$errmes.= "<li>name미입력</li><br>";
$E = 1;
}
case 8:
if(preg_match("/^[ ]+/",$u_name)){
$errmes.= "<li>name의 앞부분이 공백입니다.</li><br>";
$E = 1;
}
case 9:
if(preg_match("/[^가-힣a-zA-Z0-9 ]/",$u_name)){
$errmes.= "<li>name에 한국어(자모음, 특수 한국어 포함...), 영어, 숫자, 공백 이외에 작성 할 수 없습니다.</li>";
$E = 1;
}
case 10:
if((strlen($u_name) == 0) or (strlen($u_name) > 16)){
$errmes.= "<li>name의 길이는 반각 0~16 전각 0~8 입니다<br>";
$E = 1;
}
case 11:
$u_name = htmlspecialchars($u_name, ENT_QUOTES);
$u_name = stripslashes($u_name);
case 12:
if(!preg_match("/[0-1]{1}/",$u_gender)){
$errmes.= "<li>gender 이상해</li><br>";
$E = 1;
}
case 13:
if(!preg_match("/^[0][0-3]/",$u_job)){
$errmes.= "<li>job 이상해</li><br>";
$E = 1;
}
case 14:
$stat_det = array('str','int','dex','spd');
$stat_arr = array($u_str,$u_int,$u_dex,$u_spd);
$stat_total = $u_str+$u_int+$u_dex+$u_spd;
if(($stat_total) != 20){
$errmes.= "<li>합계가 20이 아닙니다.</li><br>";
$errmes.= "<li>str : $u_str</li><br>";
$errmes.= "<li>int : $u_int</li><br>";
$errmes.= "<li>dex : $u_dex</li><br>";
$errmes.= "<li>spd : $u_spd</li><br>";
$errmes.= "<li>합계 : $stat_total</li><br>";
$E = 1;
}
foreach($stat_arr as $key => $value)
if((0 < $value) and ($value < 21)){

}else{
$errmes.= "<li>$value:".$stat_det[$key]."는 0-20이내</li><br>";
$E = 1;
}
case 15: 
if($E==1){
break;
}
case 16:
$u_SP = 100+$u_int*5;
case 17:
$file = "./data/user/".$u_id.".txt";
if(file_exists("$file")){
$errmes.= "<li>같은 ID가 있다!? 캐릭터 작성 실패!</li><br>"; break;
}
case 18:
$word = "";
$mword = "";
$time = time();
$data = array("id" => $u_id,"pass" => md5($u_pass),"starting_timestamp" => $time,"timestamp" => $time,"name" => $u_name,"gender" => $u_gender,"level" => "1","exp" => "0","job" => $u_job,"str" => $u_str,"int" => $u_int,"dex" => $u_dex,"spd" => $u_spd,"maxhp" => 0,"hp" => 9999,"maxsp" => 0,"sp" => 9999,"state" => "0","map" => START_MAP,"mapdepth" => "0","money" => START_MONEY,"position" => "0","statuspoint" => "0","wep_atk" => "0", "wep_matk" => "0","def_div" => "0", "def_min" => "0", "mdef_div" => "0", "mdef_min" => "0","weight" => "0","equip" => START_EQUIP,"item" => START_ITEM,"seek" => "0","hide" => "0","skill" => START_SKILL,"turn" => START_TURN,"lt" => $time,"class" => "$u_job"."1","jlv" => "1","jxp" => "0","sklp" => "1");
calchpsp($data);
foreach($data as $key => $value)
$word .= "$key=\"$value\"\n";
$msgfile="./data/message/".$u_id.".txt";
$msg=array("message"=>"No Message.");
foreach($msg as $key => $value)
$mword .= "$key=\"$value\"\n";
case 19:
writefile($file,$word);
writefile($msgfile,$mword);
case 20:
print '
<FORM style="margin-bottom:0" action="index.php" method="post">
<INPUT type="submit" name ="order" value="play!">
</FORM><br>
';
case 21:
return true;
}
$errmes.="</ul>";
ErrShow($errmes);
return false;
}
function mycrypt($num){
$total = 0;
$num+=37126;
$len = strlen($num);
for($i=0; $i<$len; $i++){
$total+=substr($num,$i,1);
}
$total=$total%10;
$ky = array("A","B","C","D","E","F","G","H","I","J");
$num= strtr($total,$ky).$num;
$ky = array_flip($ky);
foreach($ky as $key => $val){
$ky[$key]+=$total;
if($ky[$key]>9) $ky[$key]-=10;
}
$ky = array_flip($ky);
$num = strtr($num, $ky);
return "$num";
}
function mydecrypt($num){
if(!isset($num)) return "";
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

function jobchange(&$data,$jobid){
if($data["job"]==$jobid){
mes("Same job.<BR>");
return false;
}
$data["jxp"]=0;
foreach($data["class"] as $key => $val){
if(substr($val,0,2)==$data["job"]){
if($data["jlv"]<10)
$data["class"]["$key"]="{$data['job']}"."{$data['jlv']}";
elseif($data["jlv"]>9)
$data["class"]["$key"]="{$data['job']}"."L";
}
}
foreach($data["class"] as $key => $val){
if(substr($val,0,2)==$jobid){
if(is_numeric(substr($val,-1)))
$data["jlv"]=substr($val,-1);
else {
$data["jlv"]=10;
$data["jxp"]=10;
}
$changed=1;
}
}
$data["job"]=$jobid;
if(!isset($changed)){
$data["jlv"]=1;
$data["sklp"]++;
$data["class"][]="$jobid"."1";
}
foreach($data["equip"] as $key => $val)
$data["equip"]["$key"]=NULL;
foreach($data["turn"] as $key => $val)
$data["turn"]["$key"]="100";
return true;
}
function GDbattleimg(&$base_fre,&$base_ene){
$img = "";
$bg = ""."./images/bg/301c.png";
mes("<TABLE cellpadding=\"10\" cellspacing=\"0\" background=\"$bg\"><TBODY><TR><TD width=\"30\">");
foreach($base_ene as $key => $val){
if(isset($base_ene["$key"]["position"])){
if(isset($base_ene["$key"]["state"])){
if($base_ene["$key"]["state"]=="1") $img = "ddd";
}
elseif(isset($base_ene["$key"]["mons"])) $img = $base_ene[$key]['img'];
else $img = $base_ene[$key]['job'].$base_ene[$key]['gender'];
$img = ""."./images/char1/".$img.".png";
mes("<BR><IMG src=\"$img\"><BR>");
}
}
mes("</TD><TD width=\"30\">");
foreach($base_ene as $key => $val){
if($base_ene["$key"]["position"]=="0"){
if(isset($base_ene["$key"]["state"])){
if($base_ene["$key"]["state"]=="1") $img = "ddd";
}
elseif(isset($base_ene["$key"]["mons"])) $img = $base_ene[$key]['img'];
else $img = $base_ene[$key]['job'].$base_ene[$key]['gender'];
$img = ""."./images/char1/".$img.".png";
mes("<BR><IMG src=\"$img\"><BR>");
}
}
mes("</TD><TD width=\"40\"></TD><TD width=\"30\">");
foreach($base_fre as $key => $val){
if($base_fre["$key"]["position"]=="0"){
if($base_fre["$key"]["state"]=="1") $img = "ddd";
elseif(isset($base_fre["$key"]["mons"])) $img = $base_fre[$key]['img'];
else $img = $base_fre[$key]['job'].$base_fre[$key]['gender'];
$img = ""."./images/char0/".$img.".png";
mes("<BR><IMG src=\"$img\"><BR>");
}
}
mes("</TD><TD width=\"30\">");
foreach($base_fre as $key => $val){
if($base_fre["$key"]["position"]){
if($base_fre["$key"]["state"]=="1") $img = "ddd";
elseif(isset($base_fre["$key"]["mons"])) $img = $base_fre[$key]['img'];
else $img = $base_fre[$key]['job'].$base_fre[$key]['gender'];
$img = ""."./images/char0/".$img.".png";
mes("<BR><IMG src=\"$img\"><BR>");
}
}
mes("</TD></TR></TBODY></TABLE>");
}
function questnpc(&$data,$target,$no){
$npc = npcdata($data,$target,$no);
if(isset($npc["name"]))
mes("Name : ".$npc['name']."<BR>");
if(isset($npc["img"])){
$charimg = ""."./images/char1/".$npc["img"].".png";
$img = "<img src=\"$charimg\">";
mes("$img");
}
if(isset($npc["exp"]))
mes("<BR><BR>".$npc['exp']."<BR>");
}
function load_char_ids($ids){
if(is_array($ids)){
foreach($ids as $key => $val){
if(strlen($val)==3){
$chardetails["$key"]=nmedetail($val);
} else {
$chardetails["$key"]=load_id($val);
$chardetails["$key"]["msg"]=load_msg($chardetails["$key"]["id"]);
$chardetails["$key"]["jdata"]=loadjob($chardetails["$key"]);
}
}
} else {
if(strlen($ids)==3){
$chardetails["0"]=nmedetail($ids);
} else {
$chardetails["0"]=load_id($ids);
$chardetails["0"]["msg"]=load_msg($chardetails["0"]["id"]);
$chardetails["0"]["jdata"]=loadjob($chardetails["0"]);
}
}
return $chardetails;
}
function fixposition(&$freq){
$max=5; 
$front=0; $back=0;
foreach($freq as $key => $val){
if(isset($freq["$key"]["position"])){
if($freq["$key"]["position"]==0)
$front++;
else
$back++;
}
}
if($front>$max||$back>$max) ErrShow("ERROR!!!posi<BR>");
mt_srand();
foreach($freq as $key => $val){
if(!isset($freq["$key"]["position"])){
if($front<$max&&$back<$max){
$posi=mt_rand(0,1);
if($posi) $back++;
else $front++;
$freq["$key"]["position"]=$posi;
} elseif($front>=$max) {
$freq["$key"]["position"]=1;
} else {
$freq["$key"]["position"]=0;
}
}
}
if($front>$max||$back>$max) ErrShow("ERROR!!!posi<BR>");
}
function actchar($turn){
$spd=0;
mt_srand();
$among=mt_rand(0, array_sum($turn));
foreach($turn as $key => $val){
$spd+=$val;
if($among<=$spd){
return $key;
}
}
}
function sortorder(&$friend,&$enemy){
foreach($friend as $key => $val)
$arrayspd["fre".$key]=$friend[$key]["fspd"];
foreach($enemy as $key => $val)
$arrayspd["ene".$key]=$enemy[$key]["fspd"];
arsort($arrayspd, SORT_REGULAR);
$loop = _count($arrayspd);
for($i=0; $i<$loop; $i++){
$act=actchar($arrayspd);
$turnorder[]=$act;
unset($arrayspd[$act]);
}
return $turnorder;
}
function weplimit(&$char,&$skill){
if(WEP_LIMIT)
return false;
if(!isset($skill["weplimit"]))
return false;
elseif(isset($char["mons"]))
return false;
else {
if(empty($char["equip"]["0"])){
mes("".$char['name']." failed to act! ".$char['name']." doesnt have weapon!<BR>");
return true;
}
$wepdata=outputidetail($char["equip"]["0"]);
if(in_array($wepdata["type"],$skill["weplimit"]))
return false;
else {
mes("".$char['name']." failed to act! The weapon doesnt MATCH!<BR>");
return true;
}
}
}
function saytsccomi($freq,$target_num){
if(_count($freq)<2) return false;
unset($freq["$target_num"]);
$num = array_rand($freq);
$char = $freq["$num"];
$msg = $char["msg"];
if(isset($msg["tsccomi"]))
mes("".$char['name'].":".$msg['tsccomi']."<BR>");
}
function nosp(&$data,$spneed){
if(isset($data["mons"])) return false;
$result=$data["sp"]-$spneed;
if($result<1){
$msg = $data["msg"];
if(isset($msg["wspout"])){
mes("".$data['name']." can't act for NO SP and ");
if($data["gender"]==0)
mes("he said<BR>");
else
mes("she said<BR>");
mes("".$data['name'].":".$msg['wspout']."<BR>");
return true;
}
$message=array("".$data['name']." try to act but feet tripped and trumble.",
"".$data['name']." is thinking about own life and forgot about battle.",
"".$data['name']." got hungry and looking for some cookies in bag.",
"".$data['name']." felt sleepy and almost to sleep.",
"".$data['name']." got homesick and forgot about battle.",
"".$data['name']."'s foot be cramped.",
"".$data['name']." is sneezing.");
mes("<BR>".$message[array_rand($message)]."<BR>");
return true;
}
return false;
}
function lnd(&$data,$no){
if(in_array($no,$data["skill"])) return true;
return false;
}
function skilltarget($myfreq_array,$opfreq_array,$myside,$type){
$target = [];
if($myside=="fre")
$opside="ene";
else
$opside="fre";
if(is_numeric($type)){
for($i=0; $i<$type; $i++){
mt_srand();
$tofront=mt_rand(1,100);
if($tofront<15){
foreach($opfreq_array as $key => $val){
if($opfreq_array["$key"]["position"]=="0")
$whoesfront["$key"] = $opfreq_array["$key"];
}
if(isset($whoesfront)){
if(_count($whoesfront)>0){
$target[]="$opside".array_rand($whoesfront)."F";
} else $target[]="$opside".array_rand($opfreq_array);
}
} else $target[]="$opside".array_rand($opfreq_array);
}
} else {
$keys=array_keys($array);
foreach($keys as $val)
$target[] = "$opside".$val;
}
return $target;
}
function avoidattack(&$act,&$target,$skill){
if(!isset($skill["hit"]))
return false;
if(AVOID_FORMULA=="1"){
$hit = $skill["hit"]+$skill["hit"]/2*($act["fdex"]+0-$target["fspd"]-0)/100;
}else
return false;
$max = $skill["hit"]*(HIT_MAX/100);
$min = $skill["hit"]*(HIT_MIN/100);
if($max<$hit)
$hit = $max;
elseif($hit<$min)
$hit = $min;
mt_srand();
$num = mt_rand(0,101);
if($hit>$num)
return false;
else
return true;
}
function skilldamage($acter, $target, &$skill){
$str=sqrt($acter["fstr"]+1)*(isset($skill["str"]) ? $skill["str"] : 0)/100;
$int=sqrt($acter["fint"]+1)*(isset($skill["int"]) ? $skill["int"] : 0)/100;
$dex=sqrt($acter["fdex"]+1)*(isset($skill["dex"]) ? $skill["dex"] : 0)/100;
$spd=sqrt($acter["fspd"]+1)*(isset($skill["spd"]) ? $skill["spd"] : 0)/100;
$basedmg=$str+$int+$dex+$spd;
if(0<=$skill["method"]&&$skill["method"]<=4){
$attribute="wep_atk";
$attribute_def="def";
if(isset($acter["jdata"]["phy"]))
$jobvalue=$acter["jdata"]["phy"];
} elseif(5<=$skill["method"]&&$skill["method"]<=9) {
$attribute="wep_matk";
$attribute_def="mdef";
if(isset($acter["jdata"]["mag"]))
$jobvalue=$acter["jdata"]["mag"];
}
$wepmagni = pow($acter["$attribute"]+1,$skill["wepeffect"]/100)/10+1;
$damage=$wepmagni*$basedmg*ATTACK_DMG_RATE; 
if($acter["position"]=="0")
$damage *= 1.1;
if($target["position"]=="1" && !isset($skill["INV_BATK"]))
$damage *= 0.9;
mt_srand();
if(DMG_WAVE_TYPE){
$wave = $skill["width"]/2;
$width1=mt_rand(-1*$wave,$wave);
$width2=mt_rand(-1*$wave,$wave);
$damage*=(($width1+$width2)/100+1);
} else {
$width=mt_rand(-1*$skill["width"],$skill["width"]);
$damage*=($width/100+1);
}
if(isset($jobvalue))
$damage *= $jobvalue/100;
if(isset($skill["mindmg"])){
$mindmg=$damage*($skill["mindmg"]/100);
$mindmg=ceil($mindmg);
} else {
$mindmg=$damage*(1/20);
$mindmg=ceil($mindmg);
}
$damage*=((100-$target["$attribute_def"."_div"])/100);
$damage-=$target["$attribute_def"."_min"];
if($damage<$mindmg) $damage=$mindmg;
return round($damage);
}
function checkonline(&$data,$time){
if(!isset($data["lt"]))
return false;
$now = time();
$diff = $now - $data["lt"];
if($diff<$time)
return true;
return false;
}
function calcexp($level){
if($level>24){
$exp = 2*pow($level,3)+100*$level+100;
$exp -= substr($exp,-2);
} else
$exp = pow($level-1,2)/2*100+100;
return $exp;
}
function calcjxp($jlv){
if($jlv=="10")
return 10;
return pow($jlv+1,3)*10;
}
function calchpsp(&$data){
$job=loadjob($data);
$data["maxhp"]=$job["basehp"]+$data["level"]*10+$data["str"]*5;
$data["maxsp"]=$job["basesp"]+$data["level"]*5+$data["int"]*3;
}
function levelup(&$data){
if($data["exp"]>=calcexp($data["level"])){
mes($data['name']." LEVEL UP!<BR>\n");
$data["exp"]=0;
$data["level"]++;
$data["statuspoint"]+=3;
calchpsp($data);
$world = loadworld();
if(!isset($world["level_rank_id"])){
$world["level_rank_id"]=$data["id"];
saveworld($world);
} else {
foreach($world["level_rank_id"] as $val){
if($who = load_id($val))
$rank[$who["id"]]=$who["level"];
}
$rank[$data["id"]]=$data["level"];
arsort($rank);
$min = end($rank);
$rank_num = array_unique($rank);
if(isset($world["level_rank_max"])){
if(_count($rank_num)>$world["level_rank_max"]){
foreach($rank as $key => $val){
if($val==$min)
unset($rank["$key"]);
}
}
}
$world["level_rank_id"] = array_keys($rank);
saveworld($world);
}
return true;
}
return false;
}
function jlevelup(&$data){
if($data["jlv"]>9){
$data["jxp"]=10;
return false;
}
if($data["jxp"]>=calcjxp($data["jlv"])){
$job=loadjob($data);
mes("".$data['name']." JOB LEVEL UP!<BR>");
$data["jxp"]=0;
$data["jlv"]++;
if($data["jlv"]=="10")
$data["jxp"]=10;
if($job["rank"]=="1"){
switch($data["jlv"]){
case "2": case "3":
$data["sklp"]+=1; break;
case "4": case "5": case "6":
$data["sklp"]+=2; break;
case "7": case "8": case "9":
$data["sklp"]+=3; break;
case "10";
$data["sklp"]+=5; break;
}
}elseif($job["rank"]=="2"){
}
return true;
}
return false;
}
function allsay(&$freq,$word,$defin=NULL){
foreach($freq as $key => $val){
$charsay=$freq["$key"];
$msg=$freq["$key"]["msg"];
if(isset($msg["$word"]))
mes("".$charsay[name].":".$msg[$word]."<BR>");
}
}
function savedata(&$freq){
foreach($freq as $key => $val){
if(!isset($freq[$key]['mons'])){
if(isset($freq["$key"]["msg"])) unset($freq["$key"]["msg"]);
write_id($freq["$key"]);
}
}
}
function penalty(&$data){
$exp = calcexp($data["level"]);
$exp = round($exp*DEATH_PENALTY/100);
if(isset($data["exp"])){
$data["exp"]-=$exp;
if($data["exp"]<0)
$data["exp"]=0;
}
}
function judgedie(&$data){
if($data["hp"]<1){
$data["hp"]=0;
$data["state"]=1;
penalty($data);
return true;
}
return false;
}
function battle($friend, $enemy, $whostart=NULL){
$base_fre = load_char_ids($friend);
$base_ene = load_char_ids($enemy);
fixposition($base_fre);
fixposition($base_ene);
for($i=0; $i<_count($base_fre); $i++){
if((isset($base_fre["$i"]["state"]) ? $base_fre["$i"]["state"] : 0) !=1) $fre["$i"] = &$base_fre["$i"];
}
for($i=0; $i<_count($base_ene); $i++){
if((isset($base_ene["$i"]["state"]) ? $base_ene["$i"]["state"] : 0) !=1) $ene["$i"] = &$base_ene["$i"];
}
mes("<CENTER>");
GDbattleimg($base_fre,$base_ene);
mes("</CENTER>");
showleftHP($base_fre,$base_ene);
for($turn=0; $turn<5; $turn++){
mes("<CENTER><b>=-=-=-=-=-=-= TURN ".($turn+1)." =-=-=-=-=-=-=</b><BR></CENTER>");
$turnorder=sortorder($fre,$ene);
foreach($turnorder as $turn_num => $val){
mes("<BR>");
$myfreq = substr($val,0,3);
$num=substr($val,-1);
if(isset(${$myfreq}[$num]))
$char = &${$myfreq}[$num];
else
continue;
$msg = $char["msg"];
if(isset($msg["tl"])&&$turn_num=="0") mes("".$char['name'].":".$msg['tl']."");
$myfreq_array = ${$myfreq};
if($myfreq=="fre"){
$opfreq_array = &$ene;
$align = "right";
} else {
$opfreq_array = &$fre;
$align = "left";
}
$turnskill = loadskill($char["turn"][$turn]);
if(weplimit($char,$turnskill)) {
$msg = $char["msg"];
if(isset($msg["wspout"])){
mes("".$char['name'].":".$msg['wspout']."<BR>");
saytsccomi(${$myfreq},$num);
}
continue; 
}
if(nosp($char,(isset($turnskill["sp"]) ? $turnskill["sp"] : 0))){
saytsccomi(${$myfreq},$num);
continue;
}
if(isset($char["sp"])) $char["sp"]-=$turnskill["sp"];
$skillmes = skillmessage($turnskill["method"]);
mes("<DIV><U><B>".$char["name"]."</B> $skillmes".$turnskill["name"]." !</U><br>\n");
$target = skilltarget($myfreq_array,$opfreq_array,$myfreq,$turnskill["target"]);
$row=0;
if(_count($target)>8) mes("<TABLE cellpadding=\"0\" cellspacing=\"0\"><TBODY><TR><TD valign=\"top\">");
foreach($target as $val){
$targetfreq = substr($val,0,3); 
$tnum=substr($val,3,1);
$targetchar = &${$targetfreq}[$tnum];
$row++;
if($row%8==0&&$row!=0) mes("</TD><TD valign=\"top\" style=\"padding-left : 15px;\">");
if(avoidattack($char,$targetchar,$turnskill)){
mes("<SPAN class=\"avid\">".$targetchar['name']." avoided!</SPAN><BR>");
continue;
}
if(substr($val,-1)=="F") mes("<B>FRONT GUARDED!!</B><BR>");
$damage=skilldamage($char,$targetchar,$turnskill);
$targetchar["hp"]-=$damage;
mes($damage." to ".$targetchar['name'].".<BR>");
}
if(_count($target)>8) mes("</TD></TR></TBODY></TABLE>");
$target = array_unique($target);
foreach($target as $val){
$targetfreq = substr($val,0,3); 
$tnum=substr($val,3,1);
$targetchar = &${$targetfreq}[$tnum];
if(!isset($targetchar)){
unset(${$targetfreq}[$tnum]);
continue;
}
if(judgedie($targetchar)){
$saymessage = 1;
mes("<FONT COLOR=\"RED\"><B>".$targetchar['name']."</B></FONT> defeated by ".$char['name']." !<BR>\n");
$msg = $targetchar["msg"];
if(isset($msg["wdie"])){
mes("".$targetchar['name'].":".$msg['wdie']."<BR>");
saytsccomi(${$targetfreq},$tnum);
}
if(isset($targetchar["gexp"])||isset($targetchar["gmoney"])){
$exp=0;
$money=0;
$exp=$targetchar["gexp"]/_count($myfreq_array);
$exp=round($exp*EXP_RATE);
$expoff=floor($exp*((100-CUT_OFFLINE_EXP)/100));
$jxp=ceil(JXP_RATE*$targetchar["level"]/_count($myfreq_array));
$jxpoff=floor($jxp*(100-CUT_OFFLINE_JXP)/100);
$money=$targetchar["gmoney"]/_count($myfreq_array);
$money=round($money*MONEY_RATE);
$moneyoff=floor($money*((100-CUT_OFFLINE_MONEY)/100));
mes("Every person who ALIVEs get $exp"."EXP, $money".M_CURRENCY." and $jxp"."JXP<BR>");
foreach($myfreq_array as $key => $val){
if($myfreq_array[$key]['state']!=1){
if(checkonline($myfreq_array[$key],GET_NORMAL_EXP_TIME)) {
$myfreq_array[$key]["exp"]+=$exp;
$myfreq_array[$key]["jxp"]+=$jxp;
$myfreq_array[$key]["money"]+=$money;
} else {
$myfreq_array[$key]["exp"]+=$expoff;
$myfreq_array[$key]["jxp"]+=$jxpoff;
$myfreq_array[$key]["money"]+=$moneyoff;
}
levelup($myfreq_array[$key]);
jlevelup($myfreq_array[$key]);
}
}
}
unset(${$targetfreq}[$tnum]);
}
}
if(isset($saymessage)){
$msg = $char["msg"];
if(isset($msg["wkill"])) mes("".$char['name'].":".$msg['wkill']."<BR>");
unset($saymessage);
}
mes("</DIV>");
if(_count($ene)==0){
showleftHP($base_fre,$base_ene);
mes("<B><CENTER><FONT SIZE=\"6\">You WIN the Battle!</FONT></CENTER></B><BR>");
allsay($base_fre,'win');
savedata($base_fre);
allsay($base_ene,'lose');
savedata($base_ene);
savebtldetail($base_fre,$whostart);
return true;
}
if(_count($fre)==0){
showleftHP($base_fre,$base_ene);
mes("<B><CENTER><FONT SIZE=\"6\">You are DEFEATED...</FONT></CENTER></B><BR>");
allsay($base_fre,'lose');
savedata($base_fre);
allsay($base_fre,'win');
savedata($base_ene);
savebtldetail($base_fre,$whostart);
return false;
}
}
showleftHP($base_fre,$base_ene);
}
savedata($base_fre);
savedata($base_ene);
savebtldetail($base_fre,$whostart);
return true;
}
function learnskill(&$data,$id){
$id=mydecrypt($id);
$s=loadskill("$id");
if(empty($id)){
mes("Select skill.<BR>");
return false;
}
if(isset($s["disable"])){
mes("이 스킬은 사용 할 수 없음 입니다.");
return false;
}
if(in_array($id,$data["skill"])){
mes("You have alredy learned <B>".$s['name']."</B>.<BR>\n");
return false;
}
if($s["learn"]>$data["sklp"]){
mes("SkillPoint not enough to learn <B>".$s['name']."</B>.<BR>\n");
return false;
}
$job=loadjob($data);
if(!in_array($id,$job["learn"])) return false;
$data["skill"][]=$s["id"];
asort($data["skill"]);
$data["sklp"]-=$s["learn"];
addsubstatus($data,$s["id"]);
mes("You learned <B>".$s['name']."</B> !<BR>\n");
}
function skillstat(&$data){
$job=loadjob($data);
$code=mycrypt($data["job"]);
$crypturl="./lib/detailjob.php?id=".$code;
$url = "<A href=javascript:itemdetail(\"$crypturl\")>";
mes("SkillPoint : ".$data['sklp']."<BR><BR>");
mes("$url<B>".$job['name']."</B></A> Lv".$data['jlv']."<BR>");
mes("<FORM style=\"margin:0\" action=\"index.php\" method=\"post\">\n");
foreach($job["learn"] as $key => $val){
$s=loadskill($val);
$img = "<IMG SRC=\""."./images/icon/".$s["img"].".png\" class=\"sp\">";
$code = mycrypt($s["id"]);
$crypturl = "./lib/detailskill.php?id=".$code;
$s["learn"]=$s["learn"]."sp";
$url = "<A href=javascript:itemdetail(\"$crypturl\")>";
if(isset($s["disable"])) $disable="disabled";
else $disable=NULL;
if(lnd($data,$s["id"])){
$s["learn"]=" <B>(Learned!)</B>";
$disable="disabled";
}
$radio = "<input type=\"radio\" class=\"sp\" name=\"ls\" value=\"$code\" $disable>";
mes("$img$radio$url".$s['name']."</A> ".$s['learn']."<BR>\n");
}
mes("<INPUT type=\"submit\" name=\"order\" value=\" Learn ! \" style=\"margin-left:40px;margin-top:5px\">");
mes("</FORM>");
foreach($data["class"] as $val){
$job["job"]=substr($val,0,2);
if($job["job"]==$data["job"]) continue;
$job["jlv"]=substr($val,-1);
if($job["jlv"]=="L") $job["jlv"]=10;
$jlv=$job["jlv"];
$job["skill"]=$data["skill"];
$job=loadjob($job);
$code=mycrypt($job["id"]);
$crypturl="./lib/detailjob.php?id=".$code;
$url = "<A href=javascript:itemdetail(\"$crypturl\")>";
mes("<BR>$url<B>".$job['name']."</B></A> Lv$jlv<BR>");
foreach($job["learn"] as $val){
$s=loadskill($val);
$img = "<IMG SRC=\""."./images/icon/".$s["img"].".png\" class=\"sp\">";
$code = mycrypt($s["id"]);
$crypturl = "./lib/detailskill.php?id=".$code;
$s["learn"]=$s["learn"]."sp";
$url = "<A href=javascript:itemdetail(\"$crypturl\")>";
if(lnd($data,$s["id"])) $s["learn"]=" <B>(Learned!)</B>";
$radio = "<input type=\"radio\" disabled>";
mes("$img$radio$url".$s['name']."</A> ".$s['learn']."<BR>\n");
}
}
}
function meetnpc(&$data,$target){
$target = mydecrypt($target);
$npc = load_npc($data,$target);
if(isset($npc["name"])) mes("Name : ".$npc['name']."<BR>");
if(isset($npc["img"])){
$charimg = ""."./images/char1/".$npc["img"].".png";
$img = "<img src=\"$charimg\">";
mes("$img");
}
if(isset($npc["exp"])) mes("<BR><BR>".$npc['exp']."<BR>");
}
function at_same_map(&$data,$target){
if(empty($target)){
mes("명령을 실행할 캐릭터를 선택하세요.<BR>\n");
return false;
}
$target=CRYPT_KEY.$target;
if(!isset($data["mdata"]["char"])) $data["mdata"] = whois($data["map"]);
foreach($data["mdata"]["char"] as $val){
$code=crypt($val,CRYPT_KEY);
if($code==$target){
$char=$val;
break;
}
}
if(isset($char)){
$char=load_id($char);
if($char["map"]!=$data["map"]){
mes("".$char['name']." is not at same MAP.<BR>\n");
fixmapmovebug($data,$char);
return false;
}
} else {
mes("He/She is not at same MAP.<BR>\n");
return false;
}
return $char;
}
function checkrecycle(&$target){
if(!isset($target["lt"])) return true;
$now = time();
$day_3 = 60*60*24*3;
switch(1){
case 1:
if(!isset($target["level"])) break;
case 2:
if($target["level"] > 3) break;
case 3:
$diff = $now - $target["lt"];
if($diff < $day_3) break;
case 4:
return true;
}
$day_7 = 60*60*24*7;
switch(1){
case 1:
if($target["level"] > 15) break;
case 2:
$diff = $now - $target["lt"];
if($diff < $day_7) break;
case 4:
return true;
}
$day_30 = 60*60*24*30;
switch(1){
case 1:
if($target["level"] < 16) break;
case 2:
$diff = $now - $target["lt"];
if($diff < $day_30) break;
case 4:
return true;
}
}
function sleepchar(&$data,$target){
if(!$char=at_same_map($data,$target)) return false;
if(checkrecycle($char)){
if(isset($char["party"])){
$party=load_party($char["party"]);
$ptall = (!isset($party["front"]) ? $party["back"] : (!isset($party["back"]) ? (array)$party["front"] : array_merge((array)$party["front"],(array)$party["back"])));
if(in_array($char["id"],$ptall)){
$num = array_search($char["id"],$ptall);
unset($ptall["$num"]);
}
if(_count($ptall)<2){
mes("".$char['name']."'s party has been breaked.<BR>");
$file = "./data/party/".$char["party"].".txt";
$btlfile = "./data/party/".$char["party"]."det.txt";
if(file_exists($file)) unlink($file);
if(file_exists($btlfile)) unlink($btlfile);
}
foreach($ptall as $key => $val){
if($who = load_id($val)){
if(isset($who["party"])){
if($who["party"]==$char["party"]) break;
} else $cnt++;
} else {
$cnt++;
continue;
}
}
if(_count($ptall)==$cnt){
mes("".$char['name']."'s party has been breaked.<BR>");
$file = "./data/party/".$char["party"].".txt";
$btlfile = "./data/party/".$char["party"]."det.txt";
if(file_exists($file)) unlink($file);
if(file_exists($btlfile)) unlink($btlfile);
}
}
delmap($data["map"],$char["id"]);
mes("".$char['name']." has been deleted from map.<BR>");
$file = "./data/message/".$char["id"].".txt";
if(file_exists($file)){
unlink($file);
}
$file = "./data/user/".$char["id"].".txt";
if(file_exists($file)){
unlink($file);
mes("".$char['name']." has gone to heaven...<BR>");
}
$value = pow($char["level"]+16,2);
$data["money"]+=$value;
mes("You get $value".M_CURRENCY.".<BR>");
$data["exp"]+=$value;
mes("You get $value"."EXP.<BR>");
levelup($data);
}
}
function partystat(&$data){
$infront = false;
$inback = false;
if(!isset($data["party"])){
makeparty();
return false;
}
if(!$party=load_party($data["party"],true)){
unset($data["party"]);
return false;
}
if(isset($party["front"])) $infront = in_array($data["id"],$party["front"]);
if(isset($party["back"])) $inback = in_array($data["id"],$party["back"]);
if(!$infront&&!$inback){
mes("The party has been DESTROYED or you got kicked.<BR>\n");
unset($data["party"]);
makeparty();
return false;
}
if(!$leader=load_id($party["leader"])){
mes("Cant find leader. The leader has changed.\n");
$ptall = (!isset($party["front"]) ? $party["back"] : (!isset($party["back"]) ? (array)$party["front"] : array_merge((array)$party["front"],(array)$party["back"])));
$num = array_rand($ptall);
$party["leader"] = $ptall["$num"];
if(isset($party["sub"])){
if(in_array($ptall["$num"],$party["sub"])){
$num2 = array_search($ptall["$num"],$party["sub"]);
unset($party["sub"]["$num2"]);
saveparty($party);
} else saveparty($party);
} else saveparty($party);
$party=load_party($data["party"]);
$leader=load_id($party["leader"]);
}
$temp = '
<FORM style="margin:0" action="index.php" method="post">
<INPUT size="60" type="text" maxlength="50" name="partychat" id="partychat">
<INPUT type="hidden" name="hid" value="ptchat">
<INPUT type="hidden" name="order" value="ptchat">
<INPUT type="submit" value="chat">
<script type="text/javascript">
document.getElementById("partychat").focus()
</script>
';
mes($temp);
if(isset($party["chat"])){
mes("<BR>");
foreach($party["chat"] as $val) mes($val."<BR>");
}
mes("</FORM><BR>");
$temp = '
<b>파티이름</b> : '.$party['name'].'<BR>
<b>파티리더</b> : '.$leader['name'].'
';
mes($temp);
if(isset($party["sub"])){
mes("<BR>SubLeader : ");
foreach($party["sub"] as $key => $val){
$subdata = load_id($val);
if($key) mes(",".$subdata['name']."");
else mes("".$subdata['name']."");
}
}
mes("<BR><FORM style=\"margin:0\" action=\"index.php\" method=\"post\">");
$file="./data/party/".$party["num"]."det.txt";
if(file_exists($file)){
mes("<INPUT type=\"submit\" name =\"order\" value=\"last battle\"><BR>");
}
if(isset($party["front"])){
foreach($party["front"] as $key => $val){
$file = "./data/user/".$val.".txt";
if(!file_exists($file)){
unset($party["front"]["$key"]);
saveparty($party);
}
}
mes("<CENTER>--- <B>FRONT</B> ---</CENTER>");
partytable($party["front"],1,1);
}
if(isset($party["back"])){
foreach($party["back"] as $key => $val){
$file = "./data/user/".$val.".txt";
if(!file_exists($file)){
unset($party["back"]["$key"]);
saveparty($party);
}
}
mes("<BR><CENTER>--- <B>BACK</B> ---</CENTER>");
partytable($party["back"],1,1);
}
if($data["id"]==$party["leader"]){
mes("<INPUT type=\"submit\" name=\"order\" value=\"서브리더 추가\">\n");
mes("<INPUT type=\"submit\" name=\"order\" value=\"전후열전환\">\n");
mes("<INPUT type=\"submit\" name=\"order\" value=\"파티리더 양도\">\n");
mes("<INPUT type=\"submit\" name=\"order\" value=\" 강제퇴장 \">\n");
mes("<INPUT type=\"submit\" name=\"order\" value=\"파티를 해산\"><BR>\n");
} else {
$code=crypt($data["id"],CRYPT_KEY);
$code=substr($code,2);
mes("<INPUT type=\"hidden\" name=\"hid\" value=\"$code\">\n");
mes("<INPUT type=\"submit\" name =\"order\" value=\"leave party\">");
}
mes("</FORM>");
if(isset($party["request"])){
foreach($party["request"] as $key => $val){
if($who=load_id($val)){
if(isset($who["party"])){
unset($party["request"]["$key"]);
$changed=1;
}
} else {
unset($party["request"]["$key"]);
$changed=1;
}
}
if(isset($changed)) saveparty($party);
}
if($party["leader"]==$data["id"]){
if(isset($party["request"])){
mes("<BR><FORM style=\"margin:0\" action=\"index.php\" method=\"post\">");
mes("<BR>These people want to join your party.<BR>\n");
humantable($party["request"]);
mes("<INPUT type=\"submit\" name=\"order\" value=\" join \">\n");
mes("<INPUT type=\"submit\" name =\"order\" value=\"refuse\">&nbsp;\n");
mes("</FORM><BR>");
}
}
}
function refuserequest(&$data,$target){
if(empty($target)){
mes("명령을 실행할 캐릭터를 선택하세요.<BR>\n");
return false;
}
if(!isset($data["party"])){
mes("You dont have a party.<BR>");
return false;
}
$party = load_party($data["party"]);
if($party["leader"]!=$data["id"]) return false;
if(!isset($party["request"])) return false;
$target = CRYPT_KEY.$target;
foreach($party["request"] as $val){
$code=crypt($val,CRYPT_KEY);
if($code==$target){
$char=$val;
break;
}
}
if(isset($char)){
if(in_array($char,$party["request"])){
$num = array_search($char,$party["request"]);
unset($party["request"]["$num"]);
$who = load_id($char);
mes("<B>$who[name]</B>'s request has been refused.");
saveparty($party);
}
}
}
function load_id_map($id){
$data=load_id($id);
$data["mdata"]["name"] = mapname($data["map"]);
$data["mdata"]["stat"] = mapstat($data["map"]);
return $data;
}
function setsubleader(&$data,$target){
if(!isset($data["party"])){
mes("당신은 파티에 참가하고 있지 않습니다.<BR>\n");
return false;
}
$party=load_party($data["party"]);
if($data["id"]!=$party["leader"]){
mes("You arent leader<BR>\n");
return false;
}
if(!$char=at_same_map($data,$target)) return false;
if($char["id"]==$data["id"]){
mes("당신은 이 파티의 리더입니다.<BR>\n");
return false;
}
if($char["party"]!=$data["party"]){
mes("Not same party.<BR>\n");
return false;
}
if(!isset($party["sub"])){
$party["sub"]=array("{$char['id']}");
mes("<B>".$char['name']."</B> has been promoted to sub.");
} else {
if(in_array($char["id"],$party["sub"])){
$no = array_search($char["id"],$party["sub"]);
unset($party["sub"]["$no"]);
mes("<B>".$char['name']."</B> has been deprived sub.");
} else {
$party["sub"][]=$char["id"];
mes("<B>".$char['name']."</B> has been promoted to sub.");
}
}
saveparty($party);
}
function shiftptposi(&$data,$target){
if(!isset($data["party"])){ mes("당신은 파티에 참가하고 있지 않습니다.<BR>\n"); return false;}
$party=load_party($data["party"]);
if($data["id"]!=$party["leader"]){
mes("You arent leader.<BR>\n");
return false;
}
if(!$char=at_same_map($data,$target)) return false;
if($char["party"]!=$data["party"]){
mes("Not same party.<BR>\n");
return false;
}
if($char["position"]=="0"){
if(isset($party["back"])){
if(_count($party["back"])>4){
mes("BACK is full!");
return false;
}
}
if(isset($party["front"])){
if(in_array($char["id"],$party["front"])){
$no=array_search($char["id"],$party["front"]);
unset($party["front"]["$no"]);
$party["back"][]=$char["id"];
$char["position"]=1;
mes("<B>".$char['name']."</B> - 후열로 자리를 이동했습니다");
}
}
} else {
if(_count($party["front"])>4){
mes("FRONT is full!");
return false;
}
if(isset($party["back"])){
if(in_array($char["id"],$party["back"])){
$no=array_search($char["id"],$party["back"]);
unset($party["back"]["$no"]);
$party["front"][]=$char["id"];
$char["position"]=0;
mes("<B>".$char['name']."</B> - 전열로 자리를 이동했습니다");
}
}
}
write_id($char);
saveparty($party);
if($data["id"]==$char["id"]) $data=load_id_map($data["id"]);
}
function partyhistory(&$party,$comment){
$time = date("m/d G:i");
$word = "[".$time."]&nbsp;".$comment;
if(isset($party["chat"])){
if(is_array($party["chat"])) array_unshift($party["chat"],$word);
} else $party["chat"]=array($word);
}
function giveleader(&$data,$target){
if(!isset($data["party"])){
mes("당신은 파티에 참가하고 있지 않습니다.<BR>\n");
return false;
}
$party=load_party($data["party"]);
if($data["id"]!=$party["leader"]){
mes("You arent leader.<BR>\n");
return false;
}
if(!$char=at_same_map($data,$target)) return false;
if($char["id"]==$data["id"]){
mes("당신은 파티의 리더입니다.<BR>\n");
return false;
}
if($char["party"]!=$data["party"]){
mes("Not same party.<BR>\n");
return false;
}
mes("The leader has changed to <B>".$char["name"]."</B>.<BR>\n");
partyhistory($party,"*** Leader changed from <B>".$data['name']."</B> to <B>".$char['name']."</B>");
$party["leader"]=$char["id"];
if(isset($party["sub"])){
if(in_array($char["id"],$party["sub"])){
$num = array_search($char["id"],$party["sub"]);
unset($party["sub"]["$num"]);
}
}
saveparty($party);
}
function partychat(&$data,$comment){
if(empty($comment)) return false;
if(strlen($comment)>110){
mes("길다");
return false;
}
if(!isset($data["party"])) return false;
if(!$party=load_party($data["party"])){
unset($data["party"]);
return false;
}
$time = date("m/d G:i");
$comment = htmlspecialchars($comment, ENT_QUOTES);
$comment = stripslashes($comment);
$word = "[".$time."]&nbsp;".$data["name"]." > ".$comment;
if(is_array($party["chat"])) array_unshift($party["chat"],$word);
else $party["chat"]=array($word);
saveparty($party);
}
function kickfromparty(&$data,$target){
if(isset($data["party"])){
if(!$char=at_same_map($data,$target)) return false;
if(!isset($char["party"])){
ErrShow("ERROR:64<BR>");
return false;
}
$party=load_party($data["party"]);
if($char["id"]==$party["leader"]){
mes("파티리더는 강제퇴장할 수 없습니다<BR>");
return false;
}
if($data["id"]!=$party["leader"]){
mes("당신에게는 강제퇴장 권한이 없습니다<BR>");
return false;
}
if(isset($party["sub"])){
if(in_array($char["id"],$party["sub"])){
$num = array_search($char["id"],$party["sub"]);
unset($party["sub"]["$num"]);
}
}
if(isset($party["front"])){
$infront=array_search($char["id"],$party["front"]);
if(is_numeric($infront)){
unset($party["front"][$infront]);
}
}
if(isset($party["back"])){
$inback=array_search($char["id"],$party["back"]);
if(is_numeric($inback)){
unset($party["back"][$inback]);
}
}
mes("".$char['name']." 님이 파티에서 강제퇴장 당하셨습니다.<BR>\n");
partyhistory($party,"*** <B>".$data['name']."</B> kicked <B>".$char['name']."</B> from party.");
unset($char["party"]);
write_id($char);
saveparty($party); return true;
}
}
function leaveparty(&$data,$target){
if(isset($data["party"])){
$party=load_party($data["party"]);
if(isset($party["sub"])){
if(in_array($data["id"],$party["sub"])){
$num = array_search($data["id"],$party["sub"]);
unset($party["sub"]["$num"]);
}
}
if(isset($party["front"])) $infront=array_search($data["id"],$party["front"]);
if(isset($infront)){
if(is_numeric($infront)){
unset($party["front"][$infront]);
saveparty($party);
unset($data["party"]);
mes("You leave the party\"".$party['name']."\".<BR>\n");
return true;
}
}
if(isset($party["back"])) $inback=array_search($data["id"],$party["back"]);
if(isset($inback)){
if(is_numeric($inback)){
unset($party["back"][$inback]);
saveparty($party);
unset($data["party"]);
mes("You leave the party\"".$party['name']."\".");
return true;
}
}
return false;
}
}
function joinparty(&$data,$target){
if(!isset($data["party"])){
mes("You dont have a party.<BR>");
return false;
}
$party = load_party($data["party"]);
if($party["leader"]!=$data["id"]) return false;
if(!$char=at_same_map($data,$target)) return false;
if(isset($char["party"])){
mes("".$char['name']." has already join to anothere party.<BR>");
$num = array_search($char["id"],(isset($party["request"]) ? $party["request"] : []));
unset($party["request"][$num]);
saveparty($party); return false;
}
$max=4;
if($char["position"]==0){
if(_count($party["front"])>$max){
mes("We have maximum people of positon \"FRONT\".<BR>\n");
return false;
}
if(isset($party["front"])) array_push($party["front"],$char["id"]);
else $party["front"]=array("".$char['id']."");
} else {
if(_count($party["back"])>$max){
mes("We have maximum people of positon \"BACK\".<BR>\n");
return false;
}
if(isset($party["back"])) array_push($party["back"],$char["id"]);
else $party["back"]=array("".$char['id']."");
}
$num = array_search($char["id"],$party["request"]);
unset($party["request"][$num]);
if(_count($party["request"])==0) unset($party["request"]);
$char["party"]=$party["num"];
mes("<B>".$char['name']."</B> has joined party!<BR>\n");
partyhistory($party,"*** <B>".$char['name']."</B> has joined party!");
write_id($char);
saveparty($party); return true;
}
function requestparty(&$data,$target){
if(!$char=at_same_map($data,$target)) return false;
if(!isset($char["party"])){
mes("He/She doesnt belongs to any party.<BR>\n");
return false;
}
$party=load_party($char["party"]);
if(isset($party["request"])){
if(in_array($data["id"],$party["request"])){
mes("You have already requested to this party.<BR>\n");
return false;
if(_count($party["request"])>9) mes("This party has so much request.<BR>\n");
return false;
}
array_push($party["request"],$data["id"]);
} else {
if(isset($party['name'])){
$party["request"]=array("".$data['id']."");
mes("You request for joining to the party\"<b>{$party['name']}</b>\"");
saveparty($party);
} else mes("The opponent did not create a party.");
}
}
function convertmsg($msg){
$msg = str_replace(array("\r\n","\n","\r"),"<BR>",$msg);
$msg = htmlentities($msg,ENT_QUOTES,"UTF-8");
$msg = stripslashes($msg);
return $msg;
}
function setmsg(&$data,&$msg,$newmsg){
foreach($newmsg as $key => $val){
if(empty($val)) unset($msg["$key"]);
else $msg["$key"] = convertmsg($newmsg["$key"]);
}
$len=strlen($msg["message"]);
if($len>1000){
mes("<B>FAILED</B> : Message must be less then 1000byte($len)\n");
return false;
}
mes("<b>메시지가 변경되었습니다</b>($len/1000 byte)<p>");
foreach($msg as $key => $val){
if($key=="message") continue;
else {
$len=strlen($msg["$key"]);
if($len>62){
mes("too long message.<BR>");
return false;
}
}
}
write_msg($data,$msg);
}
function upstatus(&$data,$stat){
if($data['statuspoint']<1){
mes("No Status point.<BR>");
return false;
}
$data[$stat]+=1;
$data['statuspoint']--;
calchpsp($data);
mes("<b>".$stat."</b> increased.<br>");
}
function changeposi(&$data){
if($data["position"]!="1"){
$data['position']="1";
} else {
$data['position']="0";
}
mes("You shift position.<br>");
}
function changeorder(&$data,$turn){
foreach($turn as $key =>$val)
$turn[$key]=mydecrypt($val);
$data["turn"]=$turn;
mes("턴별 공격설정이 저장되었습니다.<br>");
}
function gender($num){
if($num=="0")
return "MALE";
return "FEMALE";
}
function additem(&$data,$itemid){
if(_count($data["item"])<MAX_ITEM){
if(empty($data["item"]["0"])) $data["item"]["0"]=$itemid;
else array_push($data["item"], $itemid);
sort($data["item"], SORT_REGULAR);
return true;
} else {
return false;
}
}
function senditem(&$data,$target,$itemnum){
if(empty($itemnum)){
mes("Select item.<BR><BR>");
return false;
}
if(!$char=at_same_map($data,$target)) return false;
$iid = mydecrypt($itemnum);
$item = outputidetail($iid);
if(!in_array($iid,$data["item"])){
mes("You dont have {$item['name']}.<BR>");
return false;
}
if(additem($char,$item["id"])){
write_id($char);
mes("You send <B>".$item[name]."</B> to <B>".$char['name']."</B>.<BR><BR>");
$num = array_search($item["id"],$data["item"]);
unset($data["item"]["$num"]);
} else {
mes("".$char['name']." has too many items.<BR><BR>");
return false;
}
}
function showequipstat(&$data){
mes("ATK : ".$data['wep_atk']."<br>");
mes("MATK : ".$data['wep_matk']."<br>");
mes("DEF : ".$data['def_div']." + ".$data['def_min']."<br>");
mes("MDEF : ".$data['mdef_div']." + ".$data['mdef_min']."<br>");
mes("WEIGHT : ".$data['weight']."<br>");
}
function market(&$data){
$list = sellinglist($data['map']);
mes("<FORM style=\"margin-bottom:0\" action=\"index.php\" method=\"post\">");
mes("<TABLE width=\"100%\"><TBODY><TR><TD valign=\"top\">");
mes("<INPUT type=\"hidden\" name=\"hid\" value=\"shop\">");
itemtable($list,"buy","shop");
itemtableclose(" buy ");
mes("</TD><TD valign=\"top\">");
equiptable($data, 'equip');
itemtableclose('remove');
itemtable($data["item"], 'sell', 'item', $data["equip"]);
itemtableclose(" sell ");
mes("</TD></TR></TBODY></TABLE></FORM>");
}
function itembuy(&$data,$target){
if(empty($target)){
mes("Select item.");
return false;
}
$i = outputidetail($target);
$list = sellinglist($data["map"]);
if(!in_array($i['id'],$list)){
mes("Failed to buy ".$i['name'].".<br>");
} else if($i["buy"] > $data["money"]) {
mes("Not enough money to buy ".$i['name'].".<br>");
} else {
if(additem($data,$i["id"])){
$data["money"]-=$i["buy"];
mes("You bought <b>".$i['name']."</b> for <b>".$i['buy']."</b> ".M_CURRENCY.".<br>");
} else {
mes("You havetoo many items.<br>");
}
}
}
function itemsell(&$data,$target){
if(empty($target)){
mes("Select item.");
return false;
}
$i = outputidetail($target);
$countary=array_count_values($data["item"]);
if(in_array($target,$data["equip"])&&$countary["$target"]<2){
mes("Cant sell EquipedItem(<b>".$i['name']."</b>).<BR>");
return false;
}
if(in_array($target,$data["item"])){
unset($data["item"][array_search($target,$data["item"])]);
$data["money"]+=$i["sell"];
mes("You sell <b>".$i['name']."</b> for <b>".$i['sell']."</b> ".M_CURRENCY.".<br>");
} else {
mes("Failed to sell ".$i['name'].".<br>");
}
}
function showitemstat(&$data){
mes("<FORM style=\"margin-bottom:0\" action=\"index.php\" method=\"post\">");
mes("<TABLE width=\"100%\"><TBODY><TR><TD valign=\"top\">");
equiptable($data, 'equip');
itemtableclose('remove');
mes("</TD><TD valign=\"top\">");
itemtable($data["item"], 'item', 'item', $data["equip"]);
itemtableclose($bt1="use/equip", $bt2="trash");
mes("</TD></TR></TBODY></TABLE></FORM>");
$itemnum=_count($data["item"]);
if(empty($data["item"][0])) $itemnum="0";
mes("ITEMS [".$itemnum."/".MAX_ITEM."]<br>\n");
}
function removeequip(&$data, $target){
if($target==""){mes("Select equipment.<br>"); return false;}
$idetail = outputidetail($target);
if(!in_array($idetail["id"],$data["equip"])){
mes($idetail['name']." dosent exist.<br>");
return false;
}
foreach($data['equip'] as $key => $value){
if($value == $idetail["id"]){
$data['equip'][$key] = "";
mes("You take off <b>".$idetail["name"]."</b>.<br>");
return true;
}
}
}
function calcstat(&$data){
$data["wep_atk"] = 0;
$data["wep_matk"] = 0;
$data["def_div"] = 0;
$data["def_min"] = 0;
$data["mdef_div"] = 0;
$data["mdef_min"] = 0;
$data["weight"] = 0;
foreach($data['equip'] as $value){
if(!empty($value)){
$idetail=outputidetail($value);
if(!empty($idetail["atk"])) $data["wep_atk"] += $idetail["atk"];
if(!empty($idetail["mag"])) $data["wep_matk"] += $idetail["mag"];
if(!empty($idetail["d_div"])) $data["def_div"] += $idetail["d_div"];
if(!empty($idetail["d_min"])) $data["def_min"] += $idetail["d_min"];
if(!empty($idetail["m_div"])) $data["mdef_div"] += $idetail["m_div"];
if(!empty($idetail["m_min"])) $data["mdef_min"] += $idetail["m_min"];
if(!empty($idetail["weight"])) $data["weight"] += $idetail["weight"];
}
}
}
function useitem(&$data,$idetail){
if(in_array($idetail["id"],$data["item"])){
$num = array_search($idetail["id"],$data["item"]);
unset($data["item"]["$num"]);
mes("You used ".$idetail['name'].".<BR>");
if(isset($idetail["hp"])){
mes("Recovered ".$idetail['hp']."HP.\n");
$data["hp"] += $idetail["hp"];
if($data["hp"]>$data["maxhp"]) $data["hp"]=$data["maxhp"];
}
switch($idetail["id"]){
case "0100":
$world = loadworld();
$rank = 1;
mes("<BR><FONT SIZE=\"7\"><B>[ TEKITO_NOW ]</B></FONT><BR>");
if(isset($world["level_rank_id"])){
mes("<FONT SIZE=\"5\"><B>WORLD LEVEL RANKING</B></FONT><BR>");
mes("TekitoNow research the people's level<BR>all over the world at REAL TIME!<BR>");
mes("Ranking below is the TOP5 of latest LEVEL RANKING.<BR>");
foreach($world["level_rank_id"] as $key => $val){
if($who = load_id($val)){
if($key=="0") $level = $who["level"];
elseif($level!=$who["level"]){
$level = $who["level"];
$rank++;
}
mes("<B>Rank $rank</B>:".$who['name']."(".$who['level'].")<BR>");
}
}
}
mes("<BR>Wait for more comming up newses!<BR><BR>");
break;
case "0901":
mes("lira cash backed.");
$data["money"]+=$idetail["sell"];
break;
}
}
}
function equipitem(&$data, &$idetail){
if(empty($idetail)){
mes("Select item.<BR>");
return false;
}
$job = loadjob($data);
if(!in_array($idetail["type"],$job["equip"])){
mes("Your JOB can't equip this type of item.<BR>");
return false;
}
if(!in_array($idetail["id"],$data["item"])){
mes("Select item.<BR>"); return false;
} else {
if(isset($idetail["limit"])){
if($idetail["limit"]>$data["level"]){
mes("Your level is not enough to equip.<BR>");
return false;
}
}
$type = typeofitem($idetail["type"]);
switch($type){
case 0:
mes("this is item!<br>");
return true;
case 10:
mes("한손무기를 장비하였습니다 - <b>".$idetail["name"]."</b>.<br>");
$data['equip'][0]=$idetail["id"]; return true;
case 11:
mes("양손무기를 장비하였습니다 - <b>".$idetail["name"]."</b>.<br>");
$data['equip'][0]=$idetail["id"]; $data['equip'][1]="";
return true;
case 12:
mes("방패를 장비하였습니다 - <b>".$idetail["name"]."</b>.<br>");
if($data['equip'][0]!=""){
$wep=outputidetail($data['equip'][0]);
$wep=typeofitem($wep["type"]);
if($wep==11){
$data['equip'][1]=$idetail["id"];
$data['equip'][0]="";
}
}
$data['equip'][1]=$idetail["id"];
return true;
case 13:
mes("방어구를 장비하였습니다 - <b>".$idetail["name"]."</b>.<br>");
$data['equip'][2]=$idetail["id"]; return true;
case 14:
mes("악세서리를 장비하였습니다 - <b>".$idetail["name"]."</b>.<br>");
$data['equip'][3]=$idetail["id"]; return true;
case 15:
mes("이 아이템은 장비할 수 없습니다 - <b>".$idetail["name"]."</b>.<br>");
return $equip;
}
}
}
function trashitem(&$data, $target, $comment="0"){
if(empty($target)){
mes("Select item.<BR>");
return false;
}
if(!in_array($target,$data["item"])){
mes("Item doesnt exist.<BR>");
return false;
} else { 
$i = outputidetail($target);
unset($data["item"][array_search($target,$data["item"])]);
if($comment) mes("You throw away ".$i['name'].".<br>\n");
}
}
function mapbattle(&$data,$enemymap){
$infront = false;
$inback = false;
if($data["state"]==1){
mes("You cant act while you are DEAD.<BR>\n");
return false;
}
if(isset($data["party"])){
$party=load_party($data["party"]);
if($party != false){
if(isset($party["front"])) $infront = in_array($data["id"],$party["front"]);
if(isset($party["back"])) $inback = in_array($data["id"],$party["back"]);
if(!$infront&&!$inback){
$friend[] = $data["id"];
}else{
$member = (!isset($party["front"]) ? $party["back"] : (!isset($party["back"]) ? (array)$party["front"] : array_merge((array)$party["front"],(array)$party["back"])));
$friend = $member;
}
} else $friend[] = $data["id"];
} else {
$friend[] = $data["id"];
}
for($i=0; $i<_count($friend); $i++)
$enemy[] = nmenum($enemymap);
return openbattle($friend,$enemy,$data["name"]);
}
function damage(&$data, $hpdamage, $spdamage="0", $die=true){
if(substr($hpdamage,-1)=="%") $hpdamage=$data["maxhp"]*(substr($hpdamage,0,-1)/100);
if(substr($spdamage,-1)=="%") $spdamage=$data["maxsp"]*(substr($spdamage,0,-1)/100);
$hpdamage=round($hpdamage);
$spdamage=round($spdamage);
if($die){
$data["hp"]-=$hpdamage;
$data["sp"]-=$spdamage;
if($data["hp"]<=0){
$data["hp"]=0;
$data["state"]=1;
}
if($data["sp"]<=0){
$data["sp"]=0;
}
return true;
} else {
$hp = $data["hp"]-$hpdamage;
$sp = $data["sp"]-$spdamage;
if( $hp<=0 || $sp<=0 ){
mes("Not enough HP and(or) SP.<BR>");
return false;
} else {
$data["hp"]=$hp;
$data["sp"]=$sp;
return true;
}
}
}
function encountmove(&$data,$a_map){
if(isset($data["party"])){
$party=load_party($data["party"]);
if($party["leader"]!=$data["id"]){
mes("Only party Leader or SubLeader can decide to move.<BR>");
mes("If you want to move by yourself, you have to leave this party.<BR>");
return false;
}
}
if($data["state"]==1){
mes("You cant act while you are DEAD.<BR>\n");
return false;
}
if(!in_array($a_map,canmove($data['map']))){
mes("Select map.<BR>");
return false;
}
if(damage($data,"30","10",false)){
if(nmeencount($a_map)){
unset($data["mdata"]);
write_id($data); 
if(mapbattle($data,$a_map)){
$data=load_id_map($data["id"]);
if(isset($data["party"])){
$member=(!isset($party["front"]) ? $party["back"] : (!isset($party["back"]) ? (array)$party["front"] : array_merge((array)$party["front"],(array)$party["back"])));
unset($member[array_search($data["id"],$member)]);
foreach($member as $value){
$who = load_id($value);
addmap($a_map, $who["map"], $who, 2);
write_id($who);
}
}
if(in_array($data["map"], canmove($a_map))) addmap($a_map, $data["map"], $data, 1);
} else $data=load_id_map($data["id"]);
} else {
if(isset($data["party"])){
$member=(!isset($party["front"]) ? $party["back"] : (!isset($party["back"]) ? (array)$party["front"] : array_merge((array)$party["front"],(array)$party["back"])));
unset($member[array_search($data["id"],$member)]);
foreach($member as $value){
$who = load_id($value);
addmap($a_map, $who["map"], $who, 2);
write_id($who);
}
}
if(in_array($data["map"], canmove($a_map))) addmap($a_map, $data["map"], $data, 1);
mapmove($data["map"]);
}
}
}
function position($num){
if($num=="0"){
return "FRONT <";
}else{
return "< BACK";
}
}
function statbar($hpspexp, &$data){
$span = "";
$val=$data["$hpspexp"];
if($hpspexp=="exp") $div=calcexp($data["level"]);
elseif($hpspexp=="jxp") $div=calcjxp($data["jlv"]);
else $div=$data['max'."$hpspexp"];
$length = $val/$div*100*1.15;
$length = round($length) + 1;
$bar = ""."./images/bg/bar.png";
if($hpspexp=="hp"){
$color=""."./images/bg/bar_r.png";
$span="HP_BAR";
}elseif($hpspexp=="sp"){
$color=""."./images/bg/bar_b.png";
$span="SP_BAR";
}elseif($hpspexp=="exp"){
$color=""."./images/bg/bar_g.png";
}elseif($hpspexp=="jxp"){
$color=""."./images/bg/bar_y.png";
}
$bar = '
<TABLE width="130" height="7" cellpadding="2" cellspacing="5">
<TBODY>
<TR>
<TD background="'.$bar.'"><IMG src="'.$color.'" width="'.$length.'" height="3" border="0" id="'.$span.'"></TD>
</TR>
</TBODY>
</TABLE>
';
return $bar;
}
function makepartyfile(&$data,$name){
if(isset($data["party"])){
$temp = '
<FORM style="margin:0" action="index.php" method="post">
당신은 이미 파티에 참가해 있습니다.<br><br>
<INPUT type="submit" name ="order" value="party">
</FORM>
';
mes("$temp");
return false;
}
if(empty($name)){
mes("Input party name.<BR>\n");
return false;
}
$num=0;
for(;;){
$file="./data/party/".$num.".txt";
if(!file_exists($file)) break;
$num++;
}
mes("파티를 만들었습니다 \"<B>".$name."</B>\".<BR><BR>");
$data["party"]=$num;
$name = htmlspecialchars($name, ENT_QUOTES);
$name = stripslashes($name);
$party["num"]=$num;
$party["leader"]=$data["id"];
$party["name"]=$name;
$party["make"]=time();
if($data["position"]==0) $party["front"]=array("".$data['id']."");
else $party["back"]=array("".$data['id']."");
saveparty($party);
return true;
}
?>