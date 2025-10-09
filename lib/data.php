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

데이터베이스+템플렛 부분

작업한것 :

data폴더 없을때 생성하는 코드 추가

EUC-KR -> UTF-8 변환
Call-time pass-by-reference 관련 오류 수정

config.php에 있는거 모두 이동...

php 업그레이드에 의한 제거된 변수 관련 수정

count 함수 오류 나는거 수정 (count를 _count로 바꾸고 함수 추가로 isset + count로 구현)
ID가 입력란에 남는 문제 제거 (아이디 해킹 당하면 슬프기에...)

php8 되자마자 함수, 변수 Warning, Error뜨는거 찾아내서 다 고침...(는 전부 고치기는 힘듬)

파티 설정할때 오류 터지는거 고침
플레이어 츳코미 들어갈때 오류 터지는거 고침
플레이어 살아 있는데 뻘건 글씨 뜨는거 고침
php8 되자마자 갑툭튀 srand 오류 -> mt_srand로 싹 바꿈

파티 없거나 탈퇴 했을때 또 오류 나는거 수정...

DATA ZONE!
**********************************************************/

define("START_MAP","100"); //시작 맵
define("START_MONEY","100000"); //시작 금액
define("START_EQUIP","<><><><>"); //시작 장비
define("START_ITEM","0000<>3003"); //시작 아이템
define("START_SKILL","100"); //시작 스킬
define("START_TURN","100<>100<>100<>100<>100"); // 시작 턴
define("M_CURRENCY","money"); //금액 단위(그냥 표시용임..-_-a)
define("CRYPT_KEY","00"); //건들지 마세요
define("HP_REC_RATE",(2/3)); //HP 회복률(%)
define("SP_REC_RATE",(1/4)); //SP 회복률(%)
define("ATTACK_DMG_RATE","2.0"); //공격 데미지 배수
define("MAX_ITEM","30"); //아이템 넣을수 있는 최대 개수
define("EXP_RATE","1.0"); //EXP 득 배수
define("JXP_RATE","1.3"); //JXP 득 배수
define("MONEY_RATE","1.0"); //금액 득 배수
define("DMG_WAVE_TYPE","1"); //데미지 웨이브 타입?
define("PARTY_DEAD_IMG","0"); //파티원 중에 DEATH된 경우 그림
define("PT_ONLINE_TIME",60*15); //파티가 온라인상일때 목록에 나오는 시간
define("GET_NORMAL_EXP_TIME",60*60*6); //파티가 오프라인일때 목록에 나오는 시간?
define("CUT_OFFLINE_EXP",50); //모름
define("CUT_OFFLINE_MONEY",80); //모름
define("CUT_OFFLINE_JXP",100); //모름
define("AVOID_FORMULA","1"); //모름
define("HIT_MAX",200); //최대 힛트 수
define("HIT_MIN",50); //최소 힛트 수
define("WEP_LIMIT","0"); //모름
define("DEATH_PENALTY","0"); //사망 패눨티
define("BBSLOCAL",""); //게시판 주소(비어 있을때 버튼이 뜨지 않음)
define("LIMIT_LEVEL_UNDEAD",15);//레벨 이하일때 무적(HP SP 만땅!)되는 수

function mapmain(&$data){
$bgname = mapstatimg($data['mdata']['stat']);
$stringsize="30";
$temp = '
<FORM style="margin-bottom:0" action="index.php" method="post">
<HR color="#CACACA">
';
mes($temp);
$bg=""."./images/bg/".$bgname.".png";
$temp = '
<TABLE width="480" height="50" cellpadding="0" cellspacing="0" background="'.$bg.'"><TBODY><TR>
<TD>&nbsp;<FONT SIZE="6" COLOR="WHITE"><B>'.$data['mdata']['name'].'</B></FONT></TD>
</TR></TBODY></TABLE>
';
mes($temp);
switch($data["map"]){
case "000":
$text = "모든 사람이 이 마을에서 시작합니다.
<BR>여기에서 시작하고, 여기에서 모든 것이 끝납니다."; break;
case "003":
$text = "BrownWood는 대장간으로 유명한 마을입니다.<br>
대장간에서 무기들을 싼 가격에 구입할 수 있죠."; break;
case "101": case "102": case "104": case "105": case "116": case "115": case "111": case "112": case "117": case "118": case "119": case "124":
$text = "초원입니다. 여러 몬스터들이 뛰어노는 곳이죠.<br>약하다면 여기서 착실히 경험을 쌓는게 중요합니다."; break;
case "113": case "114": case "121":
$text = "해변.<br>바닷가엔 비키니 입은 미녀 대신에 몬스터들이 잔뜩 있네요."; break;
case "125": case "126": case "128": case "129": case "130": case "131": case "132": case "133":
$text = "추워어어어어어어어어!!!<br>
들리는 소문에 의하면 이곳에 진짜 산타클로스가 산다던데<br>운이 좋다면 만날지도 모르겠네요."; break;
case "139": case "140": case "141": case "142": case "143": case "144": case "145": case "147": case "148": case "149": case "150": case "151":
$text = "이근방에는 독성이 있는 몬스터들이 자주 나오는 것 같습니다.<br>이런 곳은 빨리 오아시스에 가서 쉬는게 답이겠죠"; break;
default: $text = "이 맵에 대한 정보가 없습니다."; break;
}
$button = facility($data["map"]);
array_walk($button, 'm_icon');
$history = "history here?";
html($text,$button,$history);
mes("</FORM>");
}
function mapname($num){
switch($num){
case "100": return "SpringHills";
case "101": return "GrassLand 1A";
case "102": return "GrassLand 1B";
case "103": return "BrownWood";
case "104": return "GrassLand 1C";
case "105": return "GrassLand 1D";
case "106": return "WasteLand 1A";
case "107": return "WasteLand 1B";
case "108": return "WasteLand 1C";
case "109": return "WasteLand 1D";
case "110": return "WasteLand 1E";
case "111": return "GrassLand 1J";
case "112": return "GrassLand 1K";
case "113": return "SanMalero";
case "114": return "EastCoast 1A";
case "115": return "GrassLand 1F";
case "116": return "GrassLand 1E";
case "117": return "GrassLand 1G";
case "118": return "GrassLand 1H";
case "119": return "GrassLand 1I";
case "120": return "EastCoast 1B";
case "121": return "EastCoast 1C";
case "122": return "Mingston";
case "123": return "NewCollins";
case "124": return "GrassLand 1L";
case "125": return "SnowSnow 1A";
case "126": return "SnowSnow 1B";
case "127": return "WestNogles";
case "128": return "SnowSnow 1C";
case "129": return "SnowSnow 1D";
case "130": return "SnowSnow 1E";
case "131": return "SnowSnow 1F";
case "132": return "SnowSnow 1G";
case "133": return "SnowSnow 1H";
case "134": return "WasteLand 2A";
case "135": return "WasteLand 2B";
case "136": return "WasteLand 2C";
case "137": return "WasteLand 2D";
case "138": return "WasteLand 2E";
case "139": return "SapiDesert 1A";
case "140": return "SapiDesert 1B";
case "141": return "SapiDesert 1C";
case "142": return "SapiDesert 1D";
case "143": return "SapiDesert 1E";
case "144": return "SapiDesert 1F";
case "145": return "SapiDesert 1G";
case "146": return "ElSendro";
case "147": return "SapiDesert 1H";
case "148": return "SapiDesert 1I";
case "149": return "SapiDesert 1J";
case "150": return "SapiDesert 1K";
case "151": return "SapiDesert 1L";
case "152": return "NULL";
default : return "DOSENT EXIST.";
}
}
function mapstat($num){
switch($num){
case "103":
case "122":
case "113": return "0";
case "100":
case "123":
case "127":
case "146": return "1";
case "101":
case "102":
case "104":
case "105": 
case "116":
case "115":
case "111":
case "112": 
case "117":
case "118":
case "119":
case "124": return "2";
case "114":
case "120":
case "121": return "4";
case "125":
case "126":
case "128":
case "129": 
case "130":
case "131":
case "132":
case "133": return "5";
case "139":
case "140":
case "141":
case "142": 
case "143":
case "144":
case "145":
case "147":
case "148":
case "149":
case "150":
case "151": return "6";
default: return "3";
}
}
function npcdata($data,$no,$quest=0){
switch($no){
case "1001":
$temp = '
안녕하세요, '.$data['name'].' 씨.<BR><BR>
기분은 어떠세요?<BR>
';
$npc = array("id"=>"1001","name"=>"세쓰","img"=>"n001","exp"=>"$temp");
break;
case "1002":
$time = date("G");
switch($time){
case "0":
case "1":
case "2":
case "3":
case "4":
case "5":
case "6":
case "7":
$temp = "할거 없으면 잠이나 자는게 어때?";
break;
case "10":
case "13":
case "16":
case "19":
case "22":
$temp = "여기서 나가주세요! 제기랄<BR>이게 다 알바때문입니다!";
break;
default:
if($data["job"]!=01){
$code=mycrypt("104");
$temp = '
사람은 살아가면서 조그만 행복을 우연찮게 얻을 수 있어요.<BR>
당신이 원한다면 당신을 마법사로 전직시켜줄 수 있어요.
어떻게 하시겠어요?<BR><BR>
<FORM style="margin:0" action="index.php" method="post">
<INPUT type="hidden" name="hid" value="'.$code.'">
<INPUT type="hidden" name="order" value="quest">
<INPUT type="submit" value="마법사로 전직한다">
</FORM>
';
} else {
$temp = "안녕하세요, {$data['name']}.<BR><BR>기분은 어떠세요?";
}
}
$npc = array("id"=>"1002","name"=>"멜리사","img"=>"n002","exp"=>"$temp");
break;
case "1003":
$npc = array("id"=>"1003","name"=>"마츠모토","img"=>"n003","exp"=>"약한 사람은 동쪽으로 가면 좋다고 하더군.");
break;
case "1004":
$npc = array("id"=>"1004","name"=>"데이빗","img"=>"n004","exp"=>"어느날 나가본 바다는 매우 거친 파도를 보였어<BR>낚시는 커녕, 바다에 배를 띄워보지도 못했어.<BR><BR>바다에 무슨 일이 있다고 생각되지 않아?");
break;
case "1005":
$npc = array("id"=>"1005","name"=>"마더베스","img"=>"n005","exp"=>"이 마을은 어시장으로 유명한 곳이죠.<BR>이곳에선 대부분의 주민들이 어시장으로 생계를 이어가고 있어요.<BR>물고기를 잡으러 바다에 나가지만 요즘은 거친 바다 때문에 쉽지 않답니다.<BR>물고기들은 rough sea에 있어요.");
break;
case "1006":
$npc = array("id"=>"1006","name"=>"애니","img"=>"n006","exp"=>"난 흐린 날이 싫어!!");
break;
case "1007":
$HPrate = HP_REC_RATE;
$SPrate = SP_REC_RATE;
$ATKdmgrate = ATTACK_DMG_RATE;
$maxitem = MAX_ITEM;
$exprate = EXP_RATE;
$jxprate = JXP_RATE;
$online = PT_ONLINE_TIME;
$currency = M_CURRENCY;
$exptime = GET_NORMAL_EXP_TIME;
$expoff = 100-CUT_OFFLINE_EXP;
$jxpoff = 100-CUT_OFFLINE_MONEY;
$moneyoff =100-CUT_OFFLINE_JXP;
$temp = '
좋은 날씨네요, '.$data['name'].'!<BR>
<BR>
혹시, 당신.<br>
지금 이 세계에 대한 모든것을 알고 싶진 않나요?<BR>
이 세계의 진실을 말입니다.<BR>
<BR>
여기서는 HP가 '.$HPrate.'/초 로 회복되고,<BR>
SP는 '.$SPrate.'/초. 로 회복됩니다<BR>
<BR>
응? 당신이 최대한 갖을 수 있는 아이템말인가요?<br>
'.$maxitem.' 정도는 가질 수 있을꺼같네요.<BR>
또, 스킬을 쓸 때 '.$ATKdmgrate.' 정도의 데미지가 나올거 같아요.<BR>
덤으로 경험치도 알려드릴께요. 엣헴.<br>
<br>
EXP는 대략 '.$exprate.' 경험치,<BR>
JXP는 대충 '.$jxprate.' 경험치 정도려나?<BR>
<BR>
파티멤버가 온라인에 있다 나갈때는 '.$online.'/초 만큼 더 보여지게 될거예요<BR>
<BR>
이 정도면 이 세계에 대해 모두 알았겠죠?<br>
나머지는 스스로 알아가도록 하는건 어떨까요<br>
';
$npc = array("id"=>"1007","name"=>"자칭 현자","img"=>"n007","exp"=>$temp);
break;
case "1008":
if($data["job"]!="02"&&$data["money"]>3000){
$code=mycrypt("101");
$money = M_CURRENCY;
$temp = '
돈만 내면 스카우트로 전직 시켜줄께, 어때?<BR><BR>
<FORM style="margin:0" action="index.php" method="post">
<INPUT type="hidden" name="hid" value="'.$code.'">
<INPUT type="hidden" name="order" value="quest">
<INPUT type="submit" value="3000$을 준다">
</FORM>
';
} else
$temp = "여어, 기분은 어때?";
$npc = array("id"=>"1008","name"=>"딜트","img"=>"n008","exp"=>"$temp");
break;
case "1009":
$me=array("00","02");
$code1=mycrypt("102");
$code2=mycrypt("103");
if($quest=="1"){
$temp = "예아!! 그렇지? 넌 이해하는구만.<BR><BR>
근육은 최고지?<BR>
근육에 흐르는 땀과 눈물은 최고지?<BR>
그런데 왜 그녀는 이해를 못하는거야?!<BR><BR>
제기랄!<BR>";
}else{
if(in_array($data["job"],$me) && $data["state"]!="1"){
$temp = '
우오오, 땀과 눈물 그리고 우정!!
보이느냐이여.
나의 근육을 어떻게 생각하는가!<BR>
<FORM style="margin:0" action="index.php" method="post">
<INPUT type="hidden" name="hid" value="'.$code1.'">
<INPUT type="hidden" name="order" value="quest">
<INPUT type="submit" value="좀 짱인데요">
</FORM><BR>
<FORM style="margin:0" action="index.php" method="post">
<INPUT type="hidden" name="hid" value="'.$code2.'">
<INPUT type="hidden" name="order" value="quest">
<INPUT type="submit" value="뇌까지 근육이네요">
</FORM>
';
} else {
$temp = '
네놈도 그녀와 같은 소리를 하는군<BR><BR>
몸이 건강하면, 정신도 건강한 법!<BR>
뇌까지 근육이라는 말은 듣고 싶지 않다.<BR>
역시, 근육에 흐르는 땀과 눈물은 최고지?<BR>
그런데 왜 그녀는 이해를 못하는거야?!<BR><BR>
제기랄!<BR>
<FORM style="margin:0" action="index.php" method="post">
<INPUT type="hidden" name="hid" value="'.$code1.'">
<INPUT type="hidden" name="order" value="quest">
<INPUT type="submit" value="Great!">
</FORM>
';
}
}
$npc = array("id"=>"1009","name"=>"근육사나이","img"=>"n009","exp"=>"$temp");
break;
case "1010":
$npc = array("id"=>"1010","name"=>"루키 톰","img"=>"n010","exp"=>"그거 알아?<BR>NewCollins는 Mingston의 위성도시야.");
break;
case "1011":
if($data["job"]=="00"){
$temp = $data['name'].'씨, 전 더이상 당신에겐 가르쳐 드릴게 없어요.<br>
남은건 이 몸밖에...<br><br>
에? 설마 당신?<br><br>
..농담이예요.<br>
사람을 그런 눈으로 바라보지 말아주세요.<br>
';
} elseif($data["level"]>9){
$button=fbutton("105","전사도 나쁘진 않을거 같은데");
$temp = '
어, 거기 당신.<BR>
잠깐, 이리 와보지 않겠어요?<BR>
<BR>
네, '.$data['name'].'씨라고 하는군요.<BR>
뭐라구요? 종교권유 같은게 아니예요. 걱정하지 마세요.<BR>
<BR>
당신 골격을 보니 그런 직업을 가질 사람이 아니예요.<BR>
당신, 전사가 되보지 않겠어요?<BR>
근육으로 똘똘 뭉친 강인한 전사! 이것이야 말로 로망이죠.<BR>
<BR>
어때요?<BR>
'.$button;
} else {
$temp = '
이 근방의 몇몇 몬스터는 정말 위험하죠.<BR>
당신의 몸을 보니..정말 연약하게 생겼네요.<BR><BR>
실격. 실격.<BR><BR>
하지만 당신에게도 투쟁의 피가 숨겨져 있을지도 모르죠.<BR>
당신의 가녀린 몸을 단련하고 단련하고 또 단련하세요.<BR><BR>
근육.. 아니, 정의의 마음이 가득차면 제가 당신을 교육시켜드리죠.<BR>
';
}
$npc = array("id"=>"1010","name"=>"자칭 가녀린 근육전사","img"=>"n011","exp"=>"$temp");
break;
}
return $npc;
}
function jobdata(&$data){
switch($data["job"]){
case "00":
$j=array("id"=>"00","name"=>"Soldier","basehp"=>"300","basesp"=>"80","equip"=>array("101","102","201","301","302","401"),"phy"=>"100","mag"=>"60","bonus"=>array("str"=>"0","int"=>"0","dex"=>"0","spd"=>"0"),"rank"=>"1","learn"=>array("101"));
if(isset($data["jlv"])){
if($data["jlv"]>1){
if(lnd($data,"101")) array_push($j["learn"],"106");
$j["bonus"]["str"]++;
}
if($data["jlv"]>2){
array_push($j["learn"],"501");
}
if($data["jlv"]>3){
array_push($j["learn"],"641");
$j["bonus"]["str"]++;
}
if($data["jlv"]>4){
array_push($j["learn"],"551");
$j["bonus"]["dex"]++;
}
if($data["jlv"]>5){
if(lnd($data,"101")) array_push($j["learn"],"116");
}
if($data["jlv"]>6){
if(lnd($data,"501")) array_push($j["learn"],"504");
}
if($data["jlv"]>7){
array_push($j["learn"],"591");
$j["bonus"]["str"]++;
}
if($data["jlv"]>8){
if(lnd($data,"101")) array_push($j["learn"],"117");
if(lnd($data,"501")) array_push($j["learn"],"507");
$j["bonus"]["dex"]++;
}
if($data["jlv"]>9){
if(lnd($data,"116")) array_push($j["learn"],"104");
$j["bonus"]["str"]++;
}
}
break;
case "01":
$j=array("id"=>"01","name"=>"Magician","basehp"=>"200","basesp"=>"100","equip"=>array("102","103","202","301","303","401"),"phy"=>"60","mag"=>"100","bonus"=>array("str"=>"0","int"=>"0","dex"=>"0","spd"=>"0"),"rank"=>"1","learn"=>array("102"));
if(isset($data["jlv"])){
if($data["jlv"]>1){
array_push($j["learn"],"107");
$j["bonus"]["int"]++;
}
if($data["jlv"]>2){
array_push($j["learn"],"521");
}
if($data["jlv"]>3){
array_push($j["learn"],"647");
$j["bonus"]["int"]++;
}
if($data["jlv"]>4){
if(lnd($data,"102")) array_push($j["learn"],"105");
$j["bonus"]["int"]++;
}
if($data["jlv"]>5){
array_push($j["learn"],"111");
$j["bonus"]["int"]++;
}
if($data["jlv"]>6){
array_push($j["learn"],"571");
}
if($data["jlv"]>7){
if(lnd($data,"102")) array_push($j["learn"],"112");
}
if($data["jlv"]>8){
if(lnd($data,"521")) array_push($j["learn"],"524");
$j["bonus"]["dex"]++;
}
if($data["jlv"]>9){
if(lnd($data,"105")&&lnd($data,"112")) array_push($j["learn"],"110");
$j["bonus"]["int"]++;
}
}
break;
case "02":
$j=array("id"=>"02","name"=>"Scout","basehp"=>"250","basesp"=>"90","equip"=>array("101","102","104","201","203","301","401"),"phy"=>"100","mag"=>"70","bonus"=>array("str"=>"0","int"=>"0","dex"=>"0","spd"=>"0"),"rank"=>"1","learn"=>array("109"));
if(isset($data["jlv"])){
if($data["jlv"]>1){
if(lnd($data,"109")) array_push($j["learn"],"108");
$j["bonus"]["dex"]++;
}
if($data["jlv"]>2){
array_push($j["learn"],"103");
}
if($data["jlv"]>3){
array_push($j["learn"],"541","644");
$j["bonus"]["spd"]++;
}
if($data["jlv"]>4){
array_push($j["learn"],"631");
$j["bonus"]["str"]++;
}
if($data["jlv"]>5){
array_push($j["learn"],"621");
}
if($data["jlv"]>6){
array_push($j["learn"],"113");
}
if($data["jlv"]>7){
array_push($j["learn"],"721","723");
$j["bonus"]["str"]++;
}
if($data["jlv"]>8){
if(lnd($data,"108")) array_push($j["learn"],"596");
$j["bonus"]["spd"]++;
}
if($data["jlv"]>9){
if(lnd($data,"108")) array_push($j["learn"],"592");
$j["bonus"]["dex"]++;
}
}
break;
case "89":
$j=array("id"=>"89","name"=>"GreenTurtle","basehp"=>"200","basesp"=>"100","equip"=>array("102","103","201","202","301","303","401"),"phy"=>"100","mag"=>"100","bonus"=>array("int"=>"4","dex"=>"1")); break;
}
return $j;
}
function skilldetail($num){
switch($num){
case "100":$s = array("id"=>"100","name"=>"Attack","method"=>"0","learn"=>"1","exp"=>"손에 들고있는 무기로 적을 내려친다","sp"=>"7","hit"=>"80","str"=>"200","dex"=>"200","spd"=>"200","weplimit"=>"101<>102<>103","img" => "sk1000","wepeffect"=>"70","target"=>"1","width"=>"30"); break;
case "101":$s = array("id"=>"101","name"=>"Smash","method"=>"0","learn"=>"1","exp"=>"Smash!","sp"=>"7","hit"=>"80","str"=>"370","dex"=>"100","spd"=>"100","weplimit"=>"101<>102<>103","img" => "sk1000","wepeffect"=>"70","target"=>"1","width"=>"30"); break;
case "102":$s = array("id"=>"102","name"=>"FireBall","method"=>"5","learn"=>"1","exp"=>"Its Mario!","sp"=>"9","hit"=>"80","int"=>"250","dex"=>"40","spd"=>"20","img" => "s_fire","wepeffect"=>"40","target"=>"3","width"=>"30","mindmg"=>"10"); break;
case "109":$s = array("id"=>"109","name"=>"CrossStab","method"=>"0","learn"=>"1","exp"=>"Stab twice.","sp"=>"9","hit"=>"80","str"=>"140","dex"=>"140","spd"=>"140","weplimit"=>"101<>102","img" => "sk1000","wepeffect"=>"80","target"=>"2","width"=>"10"); break;
case "103":$s = array("id"=>"103","name"=>"TrueShot","method"=>"0","learn"=>"2","exp"=>"TrueShot","sp"=>"16","hit"=>"80","INV_BATK"=>"1","str"=>"50","dex"=>"350","spd"=>"250","weplimit"=>"104","img" => "sk1001","wepeffect"=>"35","target"=>"1","width"=>"30"); break;
case "104":$s = array("id"=>"104","name"=>"DeepSwing","method"=>"0","learn"=>"3","exp"=>"Powerful Attack","sp"=>"33","hit"=>"80","str"=>"700","int"=>"0","dex"=>"100","spd"=>"100","weplimit"=>"101<>102<>103","img" => "sk1002","wepeffect"=>"70","target"=>"1","width"=>"30"); break;
case "105":$s = array("id"=>"105","name"=>"FireStorm","method"=>"5","learn"=>"3","exp"=>"Stronger than Fireball.","sp"=>"24","hit"=>"80","str"=>"0","int"=>"220","dex"=>"40","spd"=>"20","img" => "s_fire2","wepeffect"=>"40","target"=>"5","width"=>"30","mindmg"=>"10"); break;
case "106":$s = array("id"=>"106","name"=>"QuadrupleSlash","method"=>"0","learn"=>"2","exp"=>"Slash four times","sp"=>"20","hit"=>"80","str"=>"120","dex"=>"120","spd"=>"120","weplimit"=>"101<>102","img" => "sk1003","wepeffect"=>"40","target"=>"4","width"=>"30"); break;
case "107":$s = array("id"=>"107","name"=>"IceVulcan","method"=>"5","learn"=>"2","exp"=>"no intro.","sp"=>"20","hit"=>"80","int"=>"90","dex"=>"30","img" => "s_ice","wepeffect"=>"70","target"=>"12","width"=>"30","mindmg"=>"15"); break;
case "108":$s = array("id"=>"108","name"=>"CaringKnives","method"=>"0","learn"=>"2","exp"=>"Stub SIX times","sp"=>"18","hit"=>"80","str"=>"70","dex"=>"100","spd"=>"70","weplimit"=>"102","img" => "sk1004","wepeffect"=>"40","target"=>"6","width"=>"30"); break;
case "110":$s = array("id"=>"110","name"=>"NovaDrive","method"=>"5","learn"=>"4","exp"=>"NovaDrive","sp"=>"36","hit"=>"80","int"=>"650","dex"=>"100","img" => "sk1002","wepeffect"=>"30","target"=>"1","width"=>"50"); break;
case "111":$s = array("id"=>"111","name"=>"WaterSplash","method"=>"5","learn"=>"3","exp"=>"WaterSplash","sp"=>"25","hit"=>"80","int"=>"110","dex"=>"80","img" => "s_ice2","wepeffect"=>"30","target"=>"7","width"=>"20"); break;
case "112":$s = array("id"=>"112","name"=>"FireSpear","method"=>"5","learn"=>"3","exp"=>"FireSpear","sp"=>"19","hit"=>"80","int"=>"300","dex"=>"100","img" => "s_fire3","wepeffect"=>"70","target"=>"2","width"=>"40"); break;
case "113":$s = array("id"=>"113","name"=>"SpiralWave","method"=>"0","learn"=>"3","exp"=>"SpiralWave","sp"=>"21","hit"=>"80","str"=>"150","dex"=>"130","spd"=>"130","weplimit"=>"102","img" => "sk1000","wepeffect"=>"70","target"=>"3","width"=>"40"); break;
case "114":$s = array("id"=>"114","name"=>"WildShot","method"=>"0","learn"=>"3","exp"=>"WildShot","sp"=>"16","hit"=>"45","str"=>"200","dex"=>"500","spd"=>"300","weplimit"=>"104","img" => "sk1005","wepeffect"=>"70","target"=>"1","width"=>"40"); break;
case "115":$s = array("id"=>"115","name"=>"TripleStrafe","method"=>"0","learn"=>"3","exp"=>"TripleStrafe","sp"=>"21","hit"=>"80","str"=>"50","dex"=>"220","spd"=>"100","weplimit"=>"104","img" => "sk1005","wepeffect"=>"40","target"=>"3","width"=>"40"); break;
case "116":$s = array("id"=>"116","name"=>"TripleOffense","method"=>"0","learn"=>"4","exp"=>"TripleOffense","sp"=>"23","hit"=>"80","str"=>"150","dex"=>"150","spd"=>"120","weplimit"=>"101","img" => "sk1005","wepeffect"=>"40","target"=>"3","width"=>"50"); break;
case "117":$s = array("id"=>"117","name"=>"SkullSplitter","method"=>"0","learn"=>"3","exp"=>"SkullSplitter","sp"=>"20","hit"=>"80","str"=>"300","dex"=>"250","weplimit"=>"101","img" => "sk1002","wepeffect"=>"20","target"=>"2","width"=>"70"); break;
case "801":$s = array("id"=>"801","name"=>"punch","method"=>"0","hit"=>"80","str"=>"350","dex"=>"200","spd"=>"100","wepeffect"=>"50","target"=>"1","width"=>"30"); break;
case "802":$s = array("id"=>"802","name"=>"bite","method"=>"0","hit"=>"80","str"=>"550","dex"=>"150","spd"=>"100","wepeffect"=>"50","target"=>"1","width"=>"30"); break;
case "803":$s = array("id"=>"803","name"=>"venom","method"=>"5","hit"=>"80","str"=>"30","dex"=>"150","spd"=>"80","int"=>"550","wepeffect"=>"50","target"=>"1","width"=>"30"); break;
case "804":$s = array("id"=>"804","name"=>"PoisonNeedle","method"=>"0","hit"=>"80","str"=>"450","dex"=>"200","spd"=>"80","int"=>"100","wepeffect"=>"50","target"=>"1","width"=>"30"); break;
case "805":$s = array("id"=>"805","name"=>"FireBreath","method"=>"5","hit"=>"80","str"=>"100","dex"=>"150","spd"=>"100","int"=>"600","wepeffect"=>"50","target"=>"1","width"=>"30"); break;
case "806":$s = array("id"=>"806","name"=>"stab","method"=>"0","hit"=>"80","str"=>"450","dex"=>"150","spd"=>"150","int"=>"0","wepeffect"=>"50","target"=>"1","width"=>"30"); break;
case "807":$s = array("id"=>"807","name"=>"IronFist","method"=>"0","hit"=>"80","str"=>"600","dex"=>"50","spd"=>"50","int"=>"0","wepeffect"=>"50","target"=>"1","width"=>"30"); break;
case "808":$s = array("id"=>"808","name"=>"SnowBall","method"=>"0","hit"=>"80","str"=>"280","dex"=>"120","spd"=>"100","int"=>"0","wepeffect"=>"50","target"=>"2","width"=>"30"); break;
case "809":$s = array("id"=>"809","name"=>"Blizzard","method"=>"5","hit"=>"80","str"=>"0","dex"=>"50","spd"=>"0","int"=>"200","wepeffect"=>"50","target"=>"5","width"=>"30"); break;
case "810":$s = array("id"=>"810","name"=>"WaterGun","method"=>"0","hit"=>"80","str"=>"450","dex"=>"150","spd"=>"150","int"=>"100","wepeffect"=>"50","target"=>"1","width"=>"30"); break;
case "811":$s = array("id"=>"811","name"=>"TidalWave","method"=>"5","hit"=>"80","str"=>"0","int"=>"140","dex"=>"100","spd"=>"100","wepeffect"=>"50","target"=>"5","width"=>"30"); break;
case "501":$s = array("id"=>"501","name"=>"LifeBoost","method"=>"99","learn"=>"2","disable"=>"1","img" => "s_life","exp"=>"Increase HP 50.","addmhp"=>"50"); break;
case "502":$s = array("id"=>"502","name"=>"LifeBoost2","method"=>"99","learn"=>"4","disable"=>"1","img" => "s_life","exp"=>"Increase HP 80","addmhp"=>"80"); break;
case "503":$s = array("id"=>"503","name"=>"LifeBoost3","method"=>"99","learn"=>"6","disable"=>"1","img" => "s_life","exp"=>"Increase HP 120","addmhp"=>"120"); break;
case "504":$s = array("id"=>"504","name"=>"QuickReviveA","method"=>"99","learn"=>"7","disable"=>"1","img" => "s_life","exp"=>"Recovery HP100 when DEAD.","rhp"=>"100"); break;
case "505":$s = array("id"=>"505","name"=>"QuickReviveB","method"=>"99","learn"=>"7","disable"=>"1","img" => "s_life","exp"=>"Recovery 50+5% of HP when DEAD.","rhp"=>"50","rphp"=>"5"); break;
case "506":$s = array("id"=>"506","name"=>"QuickReviveC","method"=>"99","learn"=>"7","disable"=>"1","img" => "s_life","exp"=>"Recovery 10% of HP when DEAD.","rphp"=>"10"); break;
case "507":$s = array("id"=>"507","name"=>"FastRecovey","method"=>"99","learn"=>"12","disable"=>"1","img" => "s_life","exp"=>"Recovery HP 10% faster.","brhp"=>"10"); break;
case "508":$s = array("id"=>"508","name"=>"FastRecovey2","method"=>"99","learn"=>"27","disable"=>"1","img" => "s_life","exp"=>"Recovery HP 20% faster.","brhp"=>"20"); break;
case "521":$s = array("id"=>"521","name"=>"EnergyBoost","method"=>"99","learn"=>"2","disable"=>"1","img" => "s_nrg","exp"=>"Increase SP 30","addmsp"=>"30"); break;
case "522":$s = array("id"=>"522","name"=>"EnergyBoost2","method"=>"99","learn"=>"4","disable"=>"1","img" => "s_nrg","exp"=>"Increase SP 50","addmsp"=>"50"); break;
case "523":$s = array("id"=>"523","name"=>"EnergyBoost3","method"=>"99","learn"=>"6","disable"=>"1","img" => "s_nrg","exp"=>"Increase SP 70","addmsp"=>"70"); break;
case "524":$s = array("id"=>"524","name"=>"EnergyCharge","method"=>"99","learn"=>"8","disable"=>"1","img" => "s_nrg","exp"=>"Recovery SP 10% faster.","brsp"=>"10"); break;
case "525":$s = array("id"=>"525","name"=>"EnergyCharge2","method"=>"99","learn"=>"18","disable"=>"1","img" => "s_nrg","exp"=>"Recovery SP 20% faster.","brsp"=>"20"); break;
case "526":$s = array("id"=>"526","name"=>"SuppressEnergy","method"=>"99","learn"=>"5","disable"=>"1","img" => "s_nrg","exp"=>"suppress 5% SP cost"); break;
case "527":$s = array("id"=>"527","name"=>"EnergyRestoration","method"=>"99","learn"=>"12","disable"=>"1","img" => "s_nrg","exp"=>"suppress 10% SP cost"); break;
case "541":$s = array("id"=>"541","name"=>"ShorterWay","method"=>"99","learn"=>"2","disable"=>"1","img" => "s_way","exp"=>"Save half SP when moving."); break;
case "542":$s = array("id"=>"542","name"=>"ShortestWay","method"=>"99","learn"=>"10","disable"=>"1","img" => "s_way","exp"=>"Cost only 1SP to move another MAP."); break;
case "551":$s = array("id"=>"551","name"=>"PowerHitting","method"=>"99","learn"=>"4","disable"=>"1","img" => "s_powup","exp"=>"Increase physical damge on 3%.","adddmg"=>"3"); break;
case "552":$s = array("id"=>"552","name"=>"FatalSpot","method"=>"99","learn"=>"10","disable"=>"1","img" => "s_powup","exp"=>"Increase physical damge on 5%.","adddmg"=>"5"); break;
case "553":$s = array("id"=>"553","name"=>"ArmorPiercing","method"=>"99","learn"=>"16","disable"=>"1","img" => "s_powup","exp"=>"Increase physical damge on 7%.","adddmg"=>"7"); break;
case "571":$s = array("id"=>"571","name"=>"EffectiveMagic","method"=>"99","learn"=>"4","disable"=>"1","img" => "s_mgcup","exp"=>"Increase Magical damge on 3%.","addmgc"=>"3"); break;
case "572":$s = array("id"=>"572","name"=>"AdvancedMagic","method"=>"99","learn"=>"10","disable"=>"1","img" => "s_mgcup","exp"=>"Increase Magical damge on 5%.","addmgc"=>"5"); break;
case "573":$s = array("id"=>"573","name"=>"MagicRealization","method"=>"99","learn"=>"16","disable"=>"1","img" => "s_mgcup","exp"=>"Increase Magical damge on 7%.","addmgc"=>"7"); break;
case "591":$s = array("id"=>"591","name"=>"KineticVision","method"=>"99","learn"=>"5","disable"=>"1","img" => "s_eye1","exp"=>"Increase hit rate 2%.","addhit"=>"2"); break;
case "592":$s = array("id"=>"592","name"=>"Insight","method"=>"99","learn"=>"7","disable"=>"1","img" => "s_eye1","exp"=>"Increase hit rate 3%.","addhit"=>"3"); break;
case "593":$s = array("id"=>"593","name"=>"TrueSight","method"=>"99","learn"=>"14","disable"=>"1","img" => "s_eye1","exp"=>"Increase hit rate 5%.","addhit"=>"5"); break;
case "594":$s = array("id"=>"594","name"=>"DivineEyes","method"=>"99","learn"=>"23","disable"=>"1","img" => "s_eye1","exp"=>"Increase hit rate 7%.","addhit"=>"7"); break;
case "595":$s = array("id"=>"595","name"=>"ExpectMovement","method"=>"99","learn"=>"6","disable"=>"1","img" => "s_spdup","exp"=>"Increase avoiding rate 2%.","addavd"=>"2"); break;
case "596":$s = array("id"=>"596","name"=>"QuickMotion","method"=>"99","learn"=>"5","disable"=>"1","img" => "s_spdup","exp"=>"Increase avoiding rate 2%.","addavd"=>"2"); break;
case "597":$s = array("id"=>"597","name"=>"SensibleReflex","method"=>"99","learn"=>"7","disable"=>"1","img" => "s_spdup","exp"=>"Increase avoiding rate 3%.","addavd"=>"3"); break;
case "598":$s = array("id"=>"598","name"=>"Parring","method"=>"99","learn"=>"14","disable"=>"1","img" => "s_spdup","exp"=>"Increase avoiding rate 5%.","addavd"=>"5"); break;
case "599":$s = array("id"=>"599","name"=>"SixthSense","method"=>"99","learn"=>"23","disable"=>"1","img" => "s_spdup","exp"=>"Increase avoiding rate 7%.","addavd"=>"7"); break;
case "621":$s = array("id"=>"621","name"=>"AwareIndication","method"=>"99","learn"=>"2","disable"=>"1","img" => "s_moon","exp"=>"Increase hide level+1."); break;
case "631":$s = array("id"=>"631","name"=>"FarSight","method"=>"99","learn"=>"1","disable"=>"1","img" => "s_sun","exp"=>"Increase seek level+1."); break;
case "641":$s = array("id"=>"641","name"=>"SwordMastery1","method"=>"99","learn"=>"3","disable"=>"1","img" => "sword1","exp"=>"Increase ATK+5 when equipping SWORD."); break;
case "642":$s = array("id"=>"642","name"=>"SwordMastery2","method"=>"99","learn"=>"7","disable"=>"1","img" => "sword1","exp"=>"Increase ATK+10 when equipping SWORD."); break;
case "643":$s = array("id"=>"643","name"=>"SwordMastery3","method"=>"99","learn"=>"11","disable"=>"1","img" => "sword1","exp"=>"Increase ATK+15 when equipping SWORD."); break;
case "644":$s = array("id"=>"644","name"=>"DaggerMastery1","method"=>"99","learn"=>"3","disable"=>"1","img" => "dagger1","exp"=>"Increase ATK+5 when equipping DAGGER."); break;
case "645":$s = array("id"=>"645","name"=>"DaggerMastery2","method"=>"99","learn"=>"7","disable"=>"1","img" => "dagger1","exp"=>"Increase ATK+10 when equipping DAGGER."); break;
case "646":$s = array("id"=>"646","name"=>"DaggerMastery3","method"=>"99","learn"=>"11","disable"=>"1","img" => "dagger1","exp"=>"Increase ATK+15 when equipping DAGGER."); break;
case "647":$s = array("id"=>"647","name"=>"StaffMastery1","method"=>"99","learn"=>"3","disable"=>"1","img" => "wand1","exp"=>"Increase MATK+5 when equipping STAFF."); break;
case "648":$s = array("id"=>"648","name"=>"StaffMastery2","method"=>"99","learn"=>"7","disable"=>"1","img" => "wand1","exp"=>"Increase MATK+10 when equipping STAFF."); break;
case "649":$s = array("id"=>"649","name"=>"StaffMastery3","method"=>"99","learn"=>"11","disable"=>"1","img" => "wand1","exp"=>"Increase MATK+15 when equipping STAFF."); break;
case "650":$s = array("id"=>"650","name"=>"BowMastery1","method"=>"99","learn"=>"3","disable"=>"1","img" => "bow1","exp"=>"Increase ATK+5 when equipping BOW."); break;
case "651":$s = array("id"=>"651","name"=>"BowMastery2","method"=>"99","learn"=>"7","disable"=>"1","img" => "bow1","exp"=>"Increase ATK+10 when equipping BOW."); break;
case "652":$s = array("id"=>"652","name"=>"BowMastery3","method"=>"99","learn"=>"11","disable"=>"1","img" => "bow1","exp"=>"Increase ATK+15 when equipping BOW"); break;
case "701":
case "721":$s = array("id"=>"721","name"=>"PotionKwouledge","method"=>"99","learn"=>"3","disable"=>"1","img" => "item01","exp"=>"Get 5% bonus HP recovery when using Potion.","ihpb"=>"5"); break;
case "722":$s = array("id"=>"722","name"=>"PotionResearch","method"=>"99","learn"=>"7","disable"=>"1","img" => "item01","exp"=>"Get 10% bonus HP recovery when using Potion.","ihpb"=>"10"); break;
case "723":$s = array("id"=>"723","name"=>"Discount","method"=>"99","learn"=>"2","disable"=>"1","img" => "s_money","exp"=>"Get 3% discount at the shop.","disc"=>"3"); break;
case "":$s = array("id"=>"","name"=>"SilentMoving","method"=>"30","learn"=>"12","exp"=>"Decrease the probability of encounting enemy when moving."); break;
case "900":$s = array("id"=>"900","name"=>"Simpletreatment","method"=>"50","exp"=>"Treats simply for the damage.","sp_p"=>"20","rechp_p"=>"10"); break;
default:$s = array("id"=>"000","name"=>"Attack"); break;
}
if(!isset($s["img"])) $s["img"] = "sk1002";
if(isset($s["weplimit"])) $s["weplimit"]=explode("<>",$s["weplimit"]);
return $s;
}
function itemlist($num){
switch($num){
case "0000":$i=array("id"=>"0000", "type"=>"0", "name"=>"포션","exp"=>"HP를 회복합니다", "steal"=>"1","buy"=>"500", "sell"=>"100", "img"=>"item01", "hp"=>"200"); break;
case "0001":$i=array("id"=>"0001", "type"=>"0", "name"=>"포션","exp"=>"HP를 회복합니다.", "steal"=>"1","buy"=>"500", "sell"=>"100", "img"=>"item01_1", "hp"=>"200"); break;
case "0002":$i=array("id"=>"0002", "type"=>"0", "name"=>"포션+","exp"=>"HP를 회복합니다.", "steal"=>"1","buy"=>"1000", "sell"=>"200", "img"=>"item01_2", "hp"=>"350"); break;
case "0100":$i=array("id"=>"0100", "type"=>"0", "name"=>"TEKITO_NOW","exp"=>"You can read the TODAY's TEKITO_NOW newspaper.", "steal"=>"1","buy"=>"1000", "sell"=>"200", "img"=>"tekito01"); break;
case "0901":$i=array("id"=>"0901", "type"=>"0", "name"=>"리셋의 수정","exp"=>"스테이터스 포인트를 리셋.", "steal"=>"1","buy"=>"11110", "sell"=>"11110", "img"=>"crysta01"); break;
case "1000":$i=array("id"=>"1000", "type"=>"101", "name"=>"에스터크","exp"=>"찌르기용의 얇은 검.", "steal"=>"1","buy"=>"500", "sell"=>"100", "img"=>"sword01","weight"=>"10", "atk"=>"10"); break;
case "1001":$i=array("id"=>"1001", "type"=>"102", "name"=>"스크라마색스","exp"=>"40센티의 짧은 검", "steal"=>"1","buy"=>"400", "sell"=>"80", "img"=>"dagger01","weight"=>"10", "atk"=>"5"); break;
case "1002":$i=array("id"=>"1002", "type"=>"103", "name"=>"완드","exp"=>"나무로 만든 마법봉", "steal"=>"1","buy"=>"300", "sell"=>"60", "img"=>"wand01","weight"=>"10", "atk"=>"2", "mag"=>"8"); break;
case "1003":$i=array("id"=>"1003", "type"=>"104", "name"=>"헌터보우","exp"=>"이 활 참 다루기 쉽다", "steal"=>"1","buy"=>"500", "sell"=>"100", "img"=>"bow01","weight"=>"10", "atk"=>"21"); break;
case "2000":$i=array("id"=>"2000", "type"=>"201", "name"=>"목재방패","exp"=>"나무로 만든 방패", "steal"=>"1","buy"=>"500", "sell"=>"100", "img"=>"shield01","weight"=>"10","d_div"=>"4", "d_min"=>"2", "m_div"=>"4", "m_min"=>"2"); break;
case "2001":$i=array("id"=>"2001", "type"=>"201", "name"=>"강철방패","exp"=>"강철제 방패", "steal"=>"1","buy"=>"700", "sell"=>"140", "img"=>"shield02","weight"=>"10","d_div"=>"7", "d_min"=>"3", "m_div"=>"3", "m_min"=>"1"); break;
case "2002":$i=array("id"=>"2002", "type"=>"201", "name"=>"가시방패","exp"=>"가시가 달린 방패", "steal"=>"1","buy"=>"3500", "sell"=>"700", "img"=>"shield03","weight"=>"10","atk"=>"8","limit"=>"20","d_div"=>"6", "d_min"=>"1", "m_div"=>"0", "m_min"=>"1"); break;
case "2003":$i=array("id"=>"2003", "type"=>"202", "name"=>"마력의 책","exp"=>"마력을 가진 책", "steal"=>"1","buy"=>"7000", "sell"=>"1400", "img"=>"book01","weight"=>"10","mag"=>"13","limit"=>"23","d_div"=>"1", "d_min"=>"0", "m_div"=>"3", "m_min"=>"1"); break;
case "3000":$i=array("id"=>"3000", "type"=>"301", "name"=>"가죽옷","exp"=>"가죽제 잠바", "steal"=>"1","buy"=>"500", "sell"=>"100", "img"=>"cloth01","weight"=>"10","d_div"=>"10", "d_min"=>"5", "m_div"=>"10", "m_min"=>"5"); break;
case "3001":$i=array("id"=>"3001", "type"=>"302", "name"=>"강철갑옷","exp"=>"강철제 갑옷", "steal"=>"1","buy"=>"500", "sell"=>"100", "img"=>"armor01","weight"=>"10","d_div"=>"15", "d_min"=>"6", "m_div"=>"5", "m_min"=>"5"); break;
case "3002":$i=array("id"=>"3002", "type"=>"303", "name"=>"실크로브","exp"=>"실크 제질의 로브", "steal"=>"1","buy"=>"500", "sell"=>"100", "img"=>"robe01","weight"=>"10","d_div"=>"7", "d_min"=>"4", "m_div"=>"20", "m_min"=>"7"); break;
case "3003":$i=array("id"=>"3003", "type"=>"301", "name"=>"싸구려 옷","exp"=>"그냥 옷이다", "steal"=>"1","buy"=>"100", "sell"=>"20", "img"=>"pcloth01","weight"=>"10","d_div"=>"3", "d_min"=>"2", "m_div"=>"4", "m_min"=>"3"); break;
case "3004":$i=array("id"=>"3004", "type"=>"301", "name"=>"강화된 옷","exp"=>"적당히 강화되서 보통 옷보다 좋다", "steal"=>"1","buy"=>"4000", "sell"=>"800", "img"=>"cloth02","weight"=>"10","limit"=>"15","d_div"=>"14", "d_min"=>"1", "m_div"=>"14", "m_min"=>"1"); break;
case "3005":$i=array("id"=>"3005", "type"=>"301", "name"=>"축복받은 옷","exp"=>"적당히 축복받아 보통 옷보다 좋다", "steal"=>"1","buy"=>"6000", "sell"=>"1200", "img"=>"cloth03","weight"=>"10","limit"=>"25","d_div"=>"3", "d_min"=>"11", "m_div"=>"3", "m_min"=>"11"); break;
case "3006":$i=array("id"=>"3006", "type"=>"302", "name"=>"강화된 갑옷","exp"=>"적당히 강화된 갑옷", "steal"=>"1","buy"=>"6000", "sell"=>"1200", "img"=>"armor02","weight"=>"10","limit"=>"15","d_div"=>"20", "d_min"=>"1", "m_div"=>"10", "m_min"=>"1"); break;
case "3007":$i=array("id"=>"3007", "type"=>"302", "name"=>"축복받은 갑옷","exp"=>"적당히 축복받은 듯한 갑옷", "steal"=>"1","buy"=>"8000", "sell"=>"1600", "img"=>"armor03","weight"=>"10","limit"=>"25","d_div"=>"7", "d_min"=>"16", "m_div"=>"3", "m_min"=>"2"); break;
case "3008":$i=array("id"=>"3008", "type"=>"303", "name"=>"강화된 로브","exp"=>"로브주제에 강화되었다", "steal"=>"1","buy"=>"5000", "sell"=>"1000", "img"=>"robe02","weight"=>"10","limit"=>"15","d_div"=>"4", "d_min"=>"2", "m_div"=>"23", "m_min"=>"6"); break;
case "3009":$i=array("id"=>"3009", "type"=>"303", "name"=>"축복받은 로브","exp"=>"로브주제에 축복받았다", "steal"=>"1","buy"=>"7000", "sell"=>"1400", "img"=>"robe03","weight"=>"10","limit"=>"25","d_div"=>"3", "d_min"=>"2", "m_div"=>"9", "m_min"=>"19"); break;
case "4000":$i=array("id"=>"4000", "type"=>"401", "name"=>"파워 브로치","exp"=>"파워를 증가시켜주는 힘을 가진 브로치", "steal"=>"1","buy"=>"800", "sell"=>"160", "img"=>"acc01","weight"=>"10","atk"=>"2"); break;
case "4001":$i=array("id"=>"4001", "type"=>"401", "name"=>"매직 브로치","exp"=>"마력을 증가시켜주는 힘을 가진 브로치", "steal"=>"1","buy"=>"800", "sell"=>"160", "img"=>"acc01","weight"=>"10","mag"=>"3"); break;
case "5001":$i=array("id"=>"5001", "type"=>"501", "name"=>"우타다 히카루","exp"=>"섬나라 가수인데 국내에서는 안유명하다", "steal"=>"1","buy"=>"200000", "sell"=>"10", "img"=>"cd"); break;
default:$i=array("id"=>"9999", "type"=>"501", "name"=>"토마토","exp"=>"붉은 색의 열매", "steal"=>"1","buy"=>"99999", "sell"=>"10", "img"=>"tomato"); break;
}
return $i;
}
function mobdetail($num){
static $already;
$n = array("mons"=>"1");
switch($num){
case "101":
$m = array("id"=>"101","name"=>"Goblin","img"=>"501","level"=>"2","gexp"=>"30","gmoney"=>"20","hp"=>"100","wep_atk"=>"10","wep_matk"=>"1","def_div"=>"5","def_min"=>"3","mdef_div"=>"5","mdef_min"=>"3","str"=>"8","int"=>"6","dex"=>"6","spd"=>"4");
for($i=0; $i<5; $i++) $ta[]=array("801","101");
break;
case "102":
$m = array("id"=>"102","name"=>"HungryWolf","img"=>"502","level"=>"4","gexp"=>"40","gmoney"=>"25","hp"=>"150","wep_atk"=>"10","wep_matk"=>"1","def_div"=>"5","def_min"=>"3","mdef_div"=>"5","mdef_min"=>"3","str"=>"10","int"=>"5","dex"=>"8","spd"=>"8");
for($i=0; $i<5; $i++) $ta[]=array("802","101");
break;
case "103":
$m = array("id"=>"103","name"=>"PoisonFrog","img"=>"503","level"=>"4","gexp"=>"35","gmoney"=>"30","hp"=>"140","wep_atk"=>"10","wep_matk"=>"10","def_div"=>"5","def_min"=>"3","mdef_div"=>"5","mdef_min"=>"3","str"=>"6","int"=>"11","dex"=>"6","spd"=>"6");
for($i=0; $i<5; $i++) $ta[]=array("803","101");
break;
case "104":
$m = array("id"=>"104","name"=>"GelGel","img"=>"504","level"=>"6","gexp"=>"85","gmoney"=>"30","hp"=>"260","wep_atk"=>"10","wep_matk"=>"10","def_div"=>"1","def_min"=>"1","mdef_div"=>"1","mdef_min"=>"1","str"=>"13","int"=>"8","dex"=>"8","spd"=>"8");
for($i=0; $i<5; $i++) $ta[]=array("100","101");
break;
case "105":
$m = array("id"=>"105","name"=>"VenomousSnake","img"=>"505","level"=>"8","gexp"=>"110","gmoney"=>"40","hp"=>"180","wep_atk"=>"20","wep_matk"=>"10","def_div"=>"10","def_min"=>"5","mdef_div"=>"10","mdef_min"=>"5","str"=>"15","int"=>"8","dex"=>"12","spd"=>"8");
for($i=0; $i<5; $i++) $ta[]=array("803","802","101");
break;
case "106":
$m = array("id"=>"106","name"=>"ThreatingLizard","img"=>"506","level"=>"13","gexp"=>"100","gmoney"=>"40","hp"=>"170","wep_atk"=>"10","wep_matk"=>"10","def_div"=>"19","def_min"=>"8","mdef_div"=>"11","mdef_min"=>"6","str"=>"14","int"=>"12","dex"=>"22","spd"=>"11");
for($i=0; $i<5; $i++) $ta[]=array("100","802");
break;
case "107":
$m = array("id"=>"107","name"=>"BanaMonkey","img"=>"507","level"=>"13","gexp"=>"130","gmoney"=>"45","hp"=>"250","wep_atk"=>"5","wep_matk"=>"10","def_div"=>"13","def_min"=>"6","mdef_div"=>"12","mdef_min"=>"4","str"=>"17","int"=>"8","dex"=>"15","spd"=>"17");
for($i=0; $i<5; $i++) $ta[]=array("100","101");
break;
case "108":
$m = array("id"=>"108","name"=>"Lantern","img"=>"508","level"=>"12","gexp"=>"180","gmoney"=>"55","hp"=>"210","wep_atk"=>"10","wep_matk"=>"20","def_div"=>"0","def_min"=>"1","mdef_div"=>"40","mdef_min"=>"20","str"=>"5","int"=>"22","dex"=>"21","spd"=>"6");
for($i=0; $i<5; $i++) $ta[]=array("102","801");
break;
case "109":
$m = array("id"=>"109","name"=>"Scorpio","img"=>"509","level"=>"10","gexp"=>"210","gmoney"=>"50","hp"=>"70","wep_atk"=>"15","wep_matk"=>"10","def_div"=>"75","def_min"=>"15","mdef_div"=>"40","mdef_min"=>"15","str"=>"17","int"=>"11","dex"=>"21","spd"=>"5");
for($i=0; $i<5; $i++) $ta[]=array("804","802","100");
break;
case "110":
$m = array("id"=>"110","name"=>"DoubleHead","img"=>"510","level"=>"15","gexp"=>"330","gmoney"=>"85","hp"=>"220","wep_atk"=>"25","wep_matk"=>"20","def_div"=>"5","def_min"=>"10","mdef_div"=>"15","mdef_min"=>"10","str"=>"16","int"=>"7","dex"=>"10","spd"=>"31");
for($i=0; $i<5; $i++) $ta[]=array("805","802");
break;
case "111":
$m = array("id"=>"111","name"=>"InnocentMummy","img"=>"511","level"=>"15","gexp"=>"280","gmoney"=>"70","hp"=>"260","wep_atk"=>"20","wep_matk"=>"5","def_div"=>"20","def_min"=>"15","mdef_div"=>"10","mdef_min"=>"5","str"=>"25","int"=>"5","dex"=>"20","spd"=>"20");
for($i=0; $i<5; $i++) $ta[]=array("801","806","108");
break;
case "112":
$m = array("id"=>"112","name"=>"LonelyCat","img"=>"512","level"=>"2","gexp"=>"20","gmoney"=>"20","hp"=>"80","wep_atk"=>"10","wep_matk"=>"5","def_div"=>"5","def_min"=>"3","mdef_div"=>"3","mdef_min"=>"3","str"=>"10","int"=>"8","dex"=>"4","spd"=>"4");
for($i=0; $i<5; $i++) $ta[]=array("802","101","100");
break;
case "113":
$m = array("id"=>"113","name"=>"LowTensionOctopus","img"=>"513","level"=>"16","gexp"=>"300","gmoney"=>"75","hp"=>"280","wep_atk"=>"15","wep_matk"=>"15","def_div"=>"10","def_min"=>"8","mdef_div"=>"7","mdef_min"=>"5","str"=>"22","int"=>"14","dex"=>"25","spd"=>"15");
for($i=0; $i<5; $i++) $ta[]=array("801","803");
break;
case "114":
$m = array("id"=>"114","name"=>"PigLancer","img"=>"514","level"=>"16","gexp"=>"330","gmoney"=>"80","hp"=>"310","wep_atk"=>"15","wep_matk"=>"10","def_div"=>"13","def_min"=>"8","mdef_div"=>"10","mdef_min"=>"10","str"=>"17","int"=>"7","dex"=>"30","spd"=>"20");
for($i=0; $i<5; $i++) $ta[]=array("806","100","108");
break;
case "115":
$m = array("id"=>"115","name"=>"Maimai","img"=>"515","level"=>"21","gexp"=>"350","gmoney"=>"85","hp"=>"300","wep_atk"=>"10","wep_matk"=>"15","def_div"=>"30","def_min"=>"5","mdef_div"=>"15","mdef_min"=>"10","str"=>"12","int"=>"20","dex"=>"20","spd"=>"15");
for($i=0; $i<5; $i++) $ta[]=array("802","803");
break;
case "116":
$m = array("id"=>"116","name"=>"CruelScorpio","img"=>"516","level"=>"19","gexp"=>"340","gmoney"=>"90","hp"=>"110","wep_atk"=>"20","wep_matk"=>"8","def_div"=>"70","def_min"=>"20","mdef_div"=>"40","mdef_min"=>"20","str"=>"22","int"=>"14","dex"=>"25","spd"=>"10");
for($i=0; $i<5; $i++) $ta[]=array("802","804");
break;
case "117":
$m = array("id"=>"117","name"=>"ComplainingMonk","img"=>"517","level"=>"22","gexp"=>"370","gmoney"=>"90","hp"=>"260","wep_atk"=>"8","wep_matk"=>"25","def_div"=>"10","def_min"=>"5","mdef_div"=>"30","mdef_min"=>"10","str"=>"9","int"=>"36","dex"=>"25","spd"=>"11");
for($i=0; $i<5; $i++) $ta[]=array("102");
break;
case "118":
$m = array("id"=>"118","name"=>"WhiteBearMini","img"=>"518","level"=>"25","gexp"=>"400","gmoney"=>"100","hp"=>"410","wep_atk"=>"15","wep_matk"=>"15","def_div"=>"8","def_min"=>"4","mdef_div"=>"2","mdef_min"=>"2","str"=>"27","int"=>"6","dex"=>"30","spd"=>"16");
for($i=0; $i<5; $i++) $ta[]=array("808","801","807");
break;
case "119":
$m = array("id"=>"119","name"=>"SnowFighter","img"=>"519","level"=>"26","gexp"=>"420","gmoney"=>"105","hp"=>"440","wep_atk"=>"15","wep_matk"=>"15","def_div"=>"10","def_min"=>"5","mdef_div"=>"7","mdef_min"=>"7","str"=>"30","int"=>"8","dex"=>"33","spd"=>"21");
for($i=0; $i<5; $i++) $ta[]=array("808","101");
break;
case "120":
$m = array("id"=>"120","name"=>"Reindeer","img"=>"520","level"=>"28","gexp"=>"470","gmoney"=>"110","hp"=>"470","wep_atk"=>"20","wep_matk"=>"20","def_div"=>"12","def_min"=>"8","mdef_div"=>"4","mdef_min"=>"11","str"=>"31","int"=>"25","dex"=>"18","spd"=>"8");
for($i=0; $i<5; $i++) $ta[]=array("806","809");
break;
case "121":
$m = array("id"=>"121","name"=>"GoblinSkier","img"=>"521","level"=>"24","gexp"=>"390","gmoney"=>"95","hp"=>"800","wep_atk"=>"15","wep_matk"=>"10","def_div"=>"1","def_min"=>"1","mdef_div"=>"1","mdef_min"=>"1","str"=>"9","int"=>"36","dex"=>"25","spd"=>"11");
for($i=0; $i<5; $i++) $ta[]=array("806","808");
break;
case "122":
$m = array("id"=>"122","name"=>"SeaAnemone","img"=>"522","level"=>"17","gexp"=>"340","gmoney"=>"80","hp"=>"270","wep_atk"=>"14","wep_matk"=>"18","def_div"=>"10","def_min"=>"8","mdef_div"=>"20","mdef_min"=>"10","str"=>"18","int"=>"21","dex"=>"22","spd"=>"20");
for($i=0; $i<5; $i++) $ta[]=array("810","803");
break;
case "123":
$m = array("id"=>"123","name"=>"Duck","img"=>"523","level"=>"22","gexp"=>"390","gmoney"=>"93","hp"=>"360","wep_atk"=>"22","wep_matk"=>"15","def_div"=>"5","def_min"=>"5","mdef_div"=>"10","mdef_min"=>"7","str"=>"32","int"=>"17","dex"=>"19","spd"=>"25");
for($i=0; $i<5; $i++) $ta[]=array("810","806");
break;
case "124":
$m = array("id"=>"124","name"=>"GreenTurtle","img"=>"524","level"=>"28","gexp"=>"480","gmoney"=>"110","hp"=>"330","wep_atk"=>"20","wep_matk"=>"20","def_div"=>"40","def_min"=>"10","mdef_div"=>"15","mdef_min"=>"9","str"=>"34","int"=>"40","dex"=>"31","spd"=>"5");
for($i=0; $i<5; $i++) $ta[]=array("810","811");
break;
case "125":
$m = array("id"=>"125","name"=>"Seagull","img"=>"525","level"=>"30","gexp"=>"510","gmoney"=>"115","hp"=>"520","wep_atk"=>"30","wep_matk"=>"15","def_div"=>"20","def_min"=>"10","mdef_div"=>"10","mdef_min"=>"7","str"=>"41","int"=>"22","dex"=>"33","spd"=>"40");
for($i=0; $i<5; $i++) $ta[]=array("810","811");
break;
case "301":
$m = array("id"=>"301","name"=>"DragonHead","img"=>"701","level"=>"20","gexp"=>"745","gmoney"=>"540","hp"=>"540","wep_atk"=>"20","wep_matk"=>"20","def_div"=>"20","def_min"=>"20","mdef_div"=>"20","mdef_min"=>"20","str"=>"20","int"=>"20","dex"=>"20","spd"=>"20");
for($i=0; $i<5; $i++) $ta[]=array("802","805");
break;
case "302":
$m = array("id"=>"302","name"=>"Angerina","img"=>"702","level"=>"20","gexp"=>"400","gmoney"=>"300","hp"=>"320","wep_atk"=>"20","wep_matk"=>"10","def_div"=>"15","def_min"=>"15","mdef_div"=>"15","mdef_min"=>"15","str"=>"15","int"=>"15","dex"=>"15","spd"=>"35");
for($i=0; $i<5; $i++) $ta[]=array("100","101","102");
break;
case "303":
$m = array("id"=>"303","name"=>"Eddy","img"=>"703","level"=>"40","gexp"=>"1500","gmoney"=>"1100","hp"=>"650","wep_atk"=>"40","wep_matk"=>"40","def_div"=>"30","def_min"=>"10","mdef_div"=>"15","mdef_min"=>"5","str"=>"35","int"=>"35","dex"=>"35","spd"=>"35");
for($i=0; $i<5; $i++) $ta[]=array("805","801","802");
break;
case "304":
$m = array("id"=>"304","name"=>"Leonard","img"=>"704","level"=>"29","gexp"=>"3000","gmoney"=>"2000","hp"=>"90","wep_atk"=>"15","wep_matk"=>"20","def_div"=>"94","def_min"=>"7","mdef_div"=>"95","mdef_min"=>"7","str"=>"20","int"=>"30","dex"=>"20","spd"=>"40");
for($i=0; $i<5; $i++) $ta[]=array("803","103");
break;
case "305":
$m = array("id"=>"305","name"=>"Cecilia","img"=>"705","level"=>"35","gexp"=>"4000","gmoney"=>"900","hp"=>"750","wep_atk"=>"50","wep_matk"=>"50","def_div"=>"25","def_min"=>"15","mdef_div"=>"18","mdef_min"=>"8","str"=>"60","int"=>"60","dex"=>"50","spd"=>"40");
for($i=0; $i<5; $i++) $ta[]=array("808","809");
break;
case "306":
$m = array("id"=>"306","name"=>"Eliza","img"=>"706","level"=>"40","gexp"=>"5000","gmoney"=>"1000","hp"=>"830","wep_atk"=>"40","wep_matk"=>"50","def_div"=>"25","def_min"=>"20","mdef_div"=>"20","mdef_min"=>"10","str"=>"45","int"=>"70","dex"=>"70","spd"=>"50");
for($i=0; $i<5; $i++) $ta[]=array("811");
break;
case "801":
$m = array("id"=>"801","name"=>"MuscleUncle","img"=>"n009","level"=>"10","gexp"=>"10","gmoney"=>"10","hp"=>"930","wep_atk"=>"60","wep_matk"=>"10","def_div"=>"33","def_min"=>"20","mdef_div"=>"33","mdef_min"=>"20","str"=>"395","int"=>"0","dex"=>"170","spd"=>"5");
for($i=0; $i<5; $i++) $ta[]=array("801");
break;
}
$ending = array("B","C","D","E","F","G","H","I","J");
if(isset($already[$num])){
$already[$num]++;
if($already[$num]>1) $m["name"] = $m["name"]."_".$ending[$already[$num]-2];
}
for($i=0; $i<5; $i++){
$turn["turn"][]=$ta[$i][array_rand($ta[$i])];
}
$word["tl"] = array("자!가보실까나∼!","이몸에게 맡겨라!","해냈당께!","먼저 공격한다","첫번째놈 잡아다단계---!","너희들 너무 둔해..!","네~네~ 매번 감사합니다","오. 왔다! 내 차레!");
$word["wkill"] = array("이것이 이몸의 힘이다!","봤느냐, 이정도 쯤이야!","누워서 떡먹기랑께","어..어..벌써 끝?","시작도 안했는데 이러기야?","무지 약해?!","두부를 먹으면 몸이 건강해지지.","난 아직 20%의 힘밖에 내지 않았는데..","네놈 뭐 그럭저럭했지만 이몸에게 상대는 안되는구만","수련이 부족하구만?","임무완료","우와.. 너무 약하잖아","넌 이미 죽어있다.");
$word["wdie"] = array("아아, 이제 됬당깽...","에 너무 강하잖아!","이런 맵에서 싸우지 말고 더 높은 곳에 가라고","뒤는 잘 부탁할께!!","아, 잠깐 화장실","너 기분나쁘다고!","내가 진건 아침식사를 안해서야. 실력이 아니야","어제 야근때문에 진거야","과자 먹을래?","나의 맛을 잘 부탁한다...","내가 죽으면..여동생에게 전해줘..","가족에게 전해줘.. 용맹하게 싸우다 죽었다고","무슨 농담?","너 반칙썼어. 이번거 무효","초~ 짜증나는 느낌?","엄청 짜증나지않아? 이 자식?","내일도 또 리젠되려나..","난 맛 없어. 진짜");
$word["tsccomi"] = array("이건 뭐셔!!","개념 없잖아 요 녀석아","어제 야근했냐?","좀 제대로 하랑께","아빠는 널 그렇게 키우지 않았단다?!","네놈에겐 정말 실망했당께","엄마는 널 그렇게 키우지 않았단다?!","정신차려! 아직 빠르당께!","거기서 뭘 하는겨?","나 혼자 냅두고 뭔 뻘짓이냐.","너도 먼저 가버리는구나아아","뒤는 나에게 맡겨라...훗","널 잊지 않을께","무지 한심하구만...","진짜임까?!","자..잠깐 기둥기!","에..에엣?!","너..정말 한심하구만","어..너도?!","뭐어, 너치고는 지금까지 잘한거겠지","너 정말 싸울 마음이 있긴 하냐","그런 대사 이제 배부르다. 정말");
$word["win"] = array("이정도야 여유!","겨우 이정도냐?","여유여유");
foreach($word as $key => $val) {
if(isset($word["$key"])) $msg["msg"]["$key"] = $word["$key"][array_rand($word["$key"])];
}
$m["fstr"]=$m["str"];
$m["fint"]=$m["int"];
$m["fdex"]=$m["dex"];
$m["fspd"]=$m["spd"];
if(!isset($m["maxhp"]))
$m["maxhp"]=$m["hp"];
return $m+$n+$turn+$msg;
}
function canmove($map){
switch($map){
case "100": $canmove = array("101","110"); break;
case "101": $canmove = array("100","102"); break;
case "102": $canmove = array("101","103"); break;
case "103": $canmove = array("102","104","111","117"); break;
case "104": $canmove = array("103","105"); break;
case "105": $canmove = array("104","106","116"); break;
case "106": $canmove = array("105","107","108"); break;
case "107": $canmove = array("106"); break;
case "108": $canmove = array("106","109"); break;
case "109": $canmove = array("108","110"); break;
case "110": $canmove = array("109","100"); break;
case "111": $canmove = array("103","112"); break;
case "112": $canmove = array("111","113","119"); break;
case "113": $canmove = array("112","114","120"); break;
case "114": $canmove = array("113","115"); break;
case "115": $canmove = array("114","116"); break;
case "116": $canmove = array("105","115"); break;
case "117": $canmove = array("103","118"); break;
case "118": $canmove = array("117","119","121","122","124"); break;
case "119": $canmove = array("112","118"); break;
case "120": $canmove = array("113","121"); break;
case "121": $canmove = array("118","120","122","138","139"); break;
case "122": $canmove = array("118","121","123","124","138"); break;
case "123": $canmove = array("122","124","134","138"); break;
case "124": $canmove = array("118","122","123","125"); break;
case "125": $canmove = array("124","126","133"); break;
case "126": $canmove = array("125","127","130"); break;
case "127": $canmove = array("126","128"); break;
case "128": $canmove = array("127","129","130"); break;
case "129": $canmove = array("128","130"); break;
case "130": $canmove = array("126","128","129","131","133"); break;
case "131": $canmove = array("130","132","134"); break;
case "132": $canmove = array("131"); break;
case "133": $canmove = array("125","130","134"); break;
case "134": $canmove = array("123","131","133","135"); break;
case "135": $canmove = array("134","136"); break;
case "136": $canmove = array("135","137","150"); break;
case "137": $canmove = array("136","138","147","148"); break;
case "138": $canmove = array("121","122","123","137"); break;
case "139": $canmove = array("121","140"); break;
case "140": $canmove = array("139","141","147"); break;
case "141": $canmove = array("140","142","146"); break;
case "142": $canmove = array("141","143","145"); break;
case "143": $canmove = array("142","144"); break;
case "144": $canmove = array("143","149","151"); break;
case "145": $canmove = array("142","144","146"); break;
case "146": $canmove = array("141","145","147","148"); break;
case "147": $canmove = array("137","140","146"); break;
case "148": $canmove = array("137","146","149"); break;
case "149": $canmove = array("144","148","150"); break;
case "150": $canmove = array("136","149","151"); break;
case "151": $canmove = array("144","150"); break;
case "152": $canmove = array("100"); break;
default: mes("<b>CANMOVE EORROR!!!</b><br>"); $canmove = array("100"); break;
}
return $canmove;
}
function smapview($map){
switch($map){
case "100":
case "101":
case "102":
case "103":
case "104":
case "105":
case "106":
case "107":
case "108":
case "109":
case "110":
case "111":
case "112":
case "115":
case "116":
case "117":
case "119": return "smap01";
case "118":
case "121":
case "122":
case "123":
case "124":
case "134":
case "135":
case "137":
case "138":
case "139": return "smap02";
case "113":
case "114":
case "120": return "smap03";
case "136":
case "140":
case "141":
case "142":
case "143":
case "144":
case "145":
case "146":
case "147":
case "148":
case "149":
case "150":
case "151": return "smap04";
case "125":
case "126":
case "127":
case "128":
case "129":
case "130":
case "131":
case "132":
case "133": return "smap05";
default:return "ERROR";
}
}
function questhappen(&$data,$no){
$no=mydecrypt($no);
switch($no){
case "105":
if($data["map"]!=103) return false;
if(jobchange($data,"00")){
mes("전사로 전직하였습니다.<BR>");
}
case "104":
if($data["map"]!=122) return false;
if(jobchange($data,"01")){
mes("마법사로 전직하였습니다<BR>");
}
break;
case "103":
if($data["map"]!=122) return false;
mes("MuscleUncle : 뭐, 임마?!<BR>");
write_id($data);
openbattle($data["id"],"801");
$data = load_id($data["id"]);
break;
case "102":
if($data["map"]!=122) return false;
questnpc($data,"1009",1);
break;
case "101":
if($data["map"]!=123) return false;
if(jobchange($data,"02")){
mes("스카우트로 전직하였습니다<BR>");
$data["money"]-=3000;
}
break;
}
}
function skillmessage($method){
switch($method){
case "0":
case "1":
case "2":
case "3":
case "4": return NULL;
case "5":
case "6":
case "7":
case "8":
case "9":
$word = array("spelled ","cast ");
return $word[array_rand($word)];
default: return NULL;
}
}
function addsubstatus(&$data,$no){
switch($no){
case "501": break;
case "502": break;
case "503": break;
case "504": break;
case "505": break;
case "506": break;
case "507": break;
case "508": break;
case "521": break;
case "522": break;
case "523": break;
case "524": break;
case "525": break;
case "526": break;
case "527": break;
case "551": break;
case "552": break;
case "553": break;
case "571": break;
case "572": break;
case "573": break;
case "591": break;
case "592": break;
case "593": break;
case "594": break;
case "595": break;
case "596": break;
case "597": break;
case "598": break;
case "599": break;
case "621": break;
case "631": break;
case "721": break;
case "722": break;
case "723": break;
}
}
function whoisnpc(&$data){
switch($data["map"]){
case "100":
$npc = array("1001","1003");
return $npc;
case "113":
$npc = array("1004","1005","1006");
return $npc;
case "103":
$npc = array("1007","1011");
return $npc;
case "122":
$npc = array("1009");
if(6<date("G"))
array_push($npc, "1002");
return $npc;
case "123":
$npc = array("1010");
if($data["job"]!="02")
array_push($npc, "1008");
return $npc;
default:
return NULL;
}
}
function sellinglist($num){
switch($num){
case "100": return array("0001","0002","0100","1000","1001","1002","1003","2000","2001","3000","3001","4000","4001");
case "103": return array("0001","0002","1000","1001","1002","1003","2000","2001","3000","3001","3002","3006","3007","4000","4001");
case "113": return array("0001","0002","1000","1001","1002","1003","2000","2001","2003","3000","3001","3002","3008","3009","4000","4001");
case "122": return array("0001","0002","1000","1001","1002","1003","2000","2001","3000","3001","3002","3004","3005","4000","4001");
case "123": return array("0001","0002","0100","2002");
case "127": return array("0001","0002","0100");
case "146": return array("0001","0002","0100","1000","1001","1002","1003","3003");
default: return array("9999");
}
}
function typeofitem($num){
switch($num){
case 0: return 0;
case 101:
case 102:
case 103: return 10;
case 104: return 11;
case 201:
case 202:
case 203: return 12;
case 301:
case 302:
case 303: return 13;
case 401: return 14;
case 501: return 15;
default: return "type doesnt match";
}
}
function typeofitem_detail($num){
switch($num){
case 0: return "Supplie";
case 101: return "Sword";
case 102: return "Dagger";
case 103: return "Staff";
case 104: return "Bow";
case 201: return "Shield";
case 202: return "Book";
case 203: return "Knife";
case 301: return "Cloth";
case 302: return "Armor";
case 303: return "Robe";
case 401: return "Accessory";
case 501: return "other";
default: return "type doesnt match";
}
}

function typeofskill_detail($num){
switch($num){
case 99: return "Passive";
case 1: return "Active";
default: return "???";
}
}

function nmenum($map){
mt_srand();
$x = mt_rand(0,100);
switch($map){
case "100": 
case "101":
case "102":
switch(1){
case (50>$x): return 101;
case (101>$x): return 112;
}
case "103": 
case "111":
case "112":
case "117":
case "118":
case "119":
case "124":
switch(1){
case (11>$x): return 101;
case (21>$x): return 112;
case (41>$x): return 102;
case (61>$x): return 103;
case (81>$x): return 104;
case (86>$x): return 105;
case (91>$x): return 106;
case (98>$x): return 107;
case (101>$x): return 302;
}
case "113": 
case "104":
case "105":
case "115":
case "116":
switch(1){
case (21>$x): return 103;
case (41>$x): return 104;
case (56>$x): return 105;
case (71>$x): return 106;
case (86>$x): return 107;
case (96>$x): return 108;
case (100>$x): return 302;
case (101>$x): return 301;
}
case "134":
case "135":
case "136":
case "137":
case "138":
switch(1){
case (11>$x): return 104;
case (31>$x): return 106;
case (51>$x): return 107;
case (71>$x): return 108;
case (81>$x): return 110;
case (89>$x): return 114;
case (97>$x): return 115;
case (99>$x): return 301;
case (101>$x): return 304;
}
case "122": 
case "123": 
case "125":
case "126":
case "127": 
case "128":
case "129":
case "130":
case "131":
case "132":
case "133":
switch(1){
case (11>$x): return 102;
case (21>$x): return 108;
case (35>$x): return 117;
case (50>$x): return 118;
case (65>$x): return 119;
case (80>$x): return 120;
case (99>$x): return 121;
case (101>$x): return 305;
}
case "106":
case "107":
case "108":
case "109":
case "110":
switch(1){
case (12>$x): return 103;
case (25>$x): return 105;
case (37>$x): return 106;
case (53>$x): return 107;
case (78>$x): return 108;
case (98>$x): return 110;
case (101>$x): return 301;
}
case "139":
case "140":
case "141":
case "142":
case "143":
case "144":
case "145":
case "146": 
case "147":
case "148":
case "149":
case "150":
case "151":
switch(1){
case (11>$x): return 103;
case (21>$x): return 105;
case (41>$x): return 106;
case (64>$x): return 109;
case (81>$x): return 111;
case (99>$x): return 116;
case (101>$x): return 303;
}
case "114":
case "120":
case "121":
switch(1){
case (11>$x): return 109;
case (21>$x): return 115;
case (41>$x): return 122;
case (64>$x): return 123;
case (81>$x): return 124;
case (99>$x): return 125;
case (101>$x): return 306;
}
case "152":
}
}
function nmeencount($map){
mt_srand();
$encprob = mt_rand(0,100);
switch($map){
case "101":
case "102":
case "104":
case "111":
case "112":
case "114":
case "115":
case "116":
case "117":
case "118":
case "119":
case "120":
case "121":
case "124":
case "125":
case "126":
case "128":
case "129":
case "130":
case "131":
case "132":
case "133":
case "134":
case "135":
case "136":
case "137":
case "138":
case "139":
case "140":
case "141":
case "142":
case "143":
case "144":
case "145":
case "147":
case "148":
case "149":
case "150":
case "151":
case "152":
case "105":
case "106":
case "107":
case "108":
case "109":
case "110":
if(50<$encprob){
return true;
}
break;
}
return false;
}
function ErrShow($mes){
die('
<table width="400" cellpadding="3" cellspacing="5">
<tr>
<td id="tableProps" valign="top" align="left"><img id="pagerrorImg" SRC="./images/icon/pagerror.gif" width="25" height="33"></td>
<td id="tableProps2" align="left" valign="middle" width="360">
<h1 id="errortype" style="COLOR: black; FONT: 13pt/15pt 굴림">
<span id="errorText">'.$mes.'</span>
</h1><br>
<input type="button" onclick="history.back(-1)" value="뒤로">
</td>
</tr>
</table>
');
exit;
}
function helpscr(){
$tmes='
<h2>도움말</h2> *원제작자또는 번역 특성상 비속어가 섞여있습니다.<br>
<br>
직업<br>
<br>
직업은 적당히 3종류가 있다. <br>
후에 적당히 추가될 지는 불명.<br>
<br>
전사는 전열에 서서 적당히 탱킹을 한다.<br>
마법사는 후열에서 적당히 강력한 마법을 사용한다.<br>
스카웃은 적당히 전열이든 후열이든 공격한다.<br>
<br>
스테이터스 정보<br>
<br>
STR - 힘. 높을수록 힘과 방어력이 적당하게 올라간다.<br>
INT - 지능. 높을수록 마법이 적당히 강해진다.<br>
DEX - 민첩. 능력치가 3개면 없어 보이니까 적당히 땜빵한 능력치.<br>
SPD - 속도. 적당히 빨라진다.<br>
<br>
스테이터스 배분<br>
<br>
레벨업 하여 적당히 습득한 스텟 포인트와 스킬 포인트를 적당히<br>
분배하거나 공격 패턴을 적당히 정할 수 있다.<br>
<br>
각각 1턴~5턴까지의 행동을 적당히 설정할 수 있으며,<br>
처음에는 적당히 일반공격만 있다.<br>
<br>
맨땅에 헤딩하겠다면 적당히 말겠지만 처음부터 적당히 있는<br>
스킬 포인트를 적당한 스킬에 투자해서 패턴을 바꾸는 것이<br>
초반의 적당한 플레이를 위한 포인트.<br>
<br>
적당히 몬스터만 찾아다니다간 적당한 공격만 하다가 적당히 사망하는 경우가 많다.<br>
특히 마법사들이 적당히 주의하자.<br>
<br>
지도<br>
<br>
적당한 화면이다.
플레이어가 위치한 장소를 기준으로 적당히 인접한 곳으로 이동할 수 있다.<br>
이동한 후에 새로고침을 누르면 이하와 같은 메뉴가 적당히 뜬다.<br>
<br>
서치 - 같은 장소에서 적당히 노닥거리는 플레이어를 파티로 적당히 끌어들일 수 있다.<br>
가끔 NPC도 있는데 적당히 유용한 정보를 가지고 있는 경우도 적당히 있다. <br>
배틀 - 적당한 몬스터와 싸운다. 적당한 필드에서만 뜬다. <br>
샵 - 마을에 있을 경우에 적당히 이용할 수 있다. 마을별로 적당한 아이템을 살 수 있다. <br>
<br>
아이템<br>
<br>
현재 소지하고 있는 적당한 도구를 장비하거나 적당하게 사용할 수 있다.<br>
적당히 말해두지만 적당히 주어진 돈으로 꼭 적당한 장비를 사자.<br>
맨몸으로 적당히 몬스터에게 덤비다간 적당히 위험하다.<br>
<br>
정보<br>
<br>
적당한 메시지를 설정할 수 있다.<br>
멋진걸 좋아하는 어린이들은 적당히 쿨하고 멋지게 적어서 만족감을 느끼시고,<br>
독고다이로 노는 폐인들은 적당히 무시해도 좋다.<br>
<br>
파티<br>
<br>
적당하게 파티를 작성하거나 다른 유저의 적당한 파티에 참가할 수 있다.<br>
고렙들은 쪼렙 유저들을 적당히 이끌고 노예처럼 부리며 자기 배를 불리고,<br>
쪼렙들은 고렙에게 적당히 아부를 하며 파티에 참가한 다음에 적당히 잠적타자.<br>
이후 적당한 때에 들어가 보면 자신의 캐릭터가적당히 강해져 있으리라 적당히 믿는다.<br>
<br>
랭킹<br>
<br>
적당한 5명의 랭킹을 알려준다.<br>
레벨에 따라 적당히 갱신된다.<br>
여러분의 폐인력을 적당히 뽐내봅시다.<br>
자기보다 적당히 강한 사람을 인정하지 않는 마음가짐을 지니며 적당하게 마우스질을 합니다.<br>
<br>
그밖의 안내사항<br>
*그밖에 여러분들이 알아낸 정보는 적당히 붙은 채팅창으로<br>
공유하거나 운영자에게 적당하게 찌르도록 합시다.<br>
적당히 버그가 고쳐지거나 적당히 메뉴얼이 수정될 겁니다,<br>
적당히 이루어지는 작업이므로 적당하게 기다리는 자세를 적당히 익힙시다.<br>
<br>
기타..<br>
<br>
랩 15때 까지는 HP, SP가 가득 차있습니다.<br>
<br>
Tekito Quest를 만든자 : TEKITO PROJECT for JUICE( http://tekito.kanichat.com/ )<br>
배포한자 : gagax.com, Kustale<br>
소스 고치고 번역한자 :<br>
1. Angelhalo( http://angelhalo.org/ ), 옥탑방 연구소( http://mephu.egloos.com ), Enterbrain (번역, 일부 수정, 맵, 캐릭터 칩 등등?)<br>
2. Kustale ( jinsu8527@gmail.com, http://blog.naver.com/dk00007 ) (소스 최적화 및 간략화, 마개조, 덜된 번역 완료[일부는 안 됬을 수도 있음. 보이는 것만 했으니...])<br>
';
mes($tmes);
}
function bbslt(){
mes ("
<script language='JavaScript' type='text/javascript'>
function resizeFrame(iframeObj){
var innerBody = iframeObj.contentWindow.document.body;
oldEvent = innerBody.onclick;
innerBody.onclick = function(){ resizeFrame(iframeObj, 1);oldEvent; };
var innerHeight = innerBody.scrollHeight + (innerBody.offsetHeight - innerBody.clientHeight);
iframeObj.style.height = innerHeight;
if( !arguments[1] )
this.scrollTo(1,1);
}
</script>
<iframe width=600 height=600 src='".BBSLOCAL."' offload='resizeFrame(this)'  frameborder=0></iframe>          
");
}
function head(&$id, &$pass,$order){
$id = isset($_POST['u_id']) ? $_POST['u_id'] : (isset($_POST['id']) ? $_POST['id'] : null);
$pass = isset($_POST['u_pass']) ? $_POST['u_pass'] : (isset($_POST['pass']) ? $_POST['pass'] : null);
if($pass == null or $id == null){
$id = isset($_COOKIE['id']) ? $_COOKIE['id'] : null;
$pass = isset($_COOKIE['pass']) ? $_COOKIE['pass'] : null;
}
setcookie("pass", (string)$pass, time()+60*60*24*5);
setcookie("id", (string)$id, time()+60*60*24*5);
if($order == "logoff"){
$pass = "";
setcookie("pass");
}
print '
<HTML>
<HEAD>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<TITLE>Tekito Quest</TITLE>
<style type="text/css">
.avid{
color:#9b8624;
}
.sp{
vertical-align:middle;
margin-right:7px;
}
input {
background-color: #433828;
border: 1px solid #806B4D;
color: #CCCCCC;
}
select {
background-color: #433828;
border: 1px solid #806B4D;
color: #CCCCCC;
}
</style>
<script language="JavaScript">
function itemdetail(gurl){
renew = window.open(gurl,"new","width=300,height=300");
renew.focus();
}
</script>
';
}
function head2(){
print '
</HEAD>
<BODY bgcolor="#ffffff" text="#342D0C" link="#611b0c" vlink="#611b0c" alink="#611b0c">
<center>
<TABLE cellpadding="5">
<TBODY>
<TR>
<TD valign="top" bgcolor="#C2C2C2" nowrap></center>
';
}
function head3(){
print '
</HEAD>
<BODY bgcolor="#ffffff" text="#8A9CB7" link="#611b0c" vlink="#611b0c" alink="#611b0c">
<center>
<TABLE cellpadding="5">
<TBODY>
<TR>
<TD valign="top" bgcolor="#C2C2C2" nowrap></center>
';
}
function foot($text){
print '
</TD>
</TR>
</TBODY>
</TABLE>
<center>
<p style="font-size:12px;color:black;">Copyright (C) TEKITO QUEST FOR JUICE. Edited by Project IRENSIA, Angelhalo, 옥탑방 연구소<br />
All rights reserved. Published by Project IRENSIA.</p>
</center>
</TD>
</TR>
</TBODY>
</TABLE>
</BODY>
</HTML>
';
}
function foot2($text){
print '
</TD>
</TR>
</TBODY>
</TABLE>
</BODY>
</HTML>
';
}
function maken($text){
print '
</TD>
</TR>
</TBODY>
</TABLE>
</BODY>
</HTML>
';
}
function fbutton($no,$text){
$no = MYCRYPT($no);
$temp = '
<FORM style="margin:0" action="index.php" method="post">
<INPUT type="hidden" name="hid" value="'.$no.'">
<INPUT type="hidden" name="order" value="quest">
<INPUT type="submit" value="'.$text.'">
</FORM>
';
return $temp;
}
function name(&$freq,$posi){
foreach($freq as $key => $val){
if($freq["$key"]["position"]=="$posi"){
if(isset($freq["$key"]["state"])){
if($freq["$key"]["state"]) mes("<B><FONT COLOR=\"RED\">".$freq["$key"]["name"]."</FONT></B>
<div style=\"margin-left:20px\"><FONT COLOR=\"RED\">Lv".sprintf("%2d",$freq["$key"]["level"])." "
.round($freq["$key"]["hp"])."/".$freq["$key"]["maxhp"]."</FONT></div>\n");
else mes("<B>".$freq["$key"]["name"]."</B><BR>
<div style=\"margin-left:20px\">Lv".sprintf("%2d",$freq["$key"]["level"])." "
.round($freq["$key"]["hp"])."/".$freq["$key"]["maxhp"]."</div>\n");
}else{
mes("<B>".$freq["$key"]["name"]."</B><BR>
<div style=\"margin-left:20px\">Lv".sprintf("%2d",$freq["$key"]["level"])." "
.round($freq["$key"]["hp"])."/".$freq["$key"]["maxhp"]."</div>\n");
}
}
}
}
function showleftHP(&$base_fre,&$base_ene){
mes("<BR><CENTER><TABLE cellpadding=\"15\" bgcolor=\"#202935\"><TBODY><TR><TD>\n");
name($base_ene,1);
mes("</TD><TD>");
name($base_ene,0);
mes("</TD><TD>");
name($base_fre,0);
mes("</TD><TD>");
name($base_fre,1);
mes("</TD></TR></TBODY></TABLE></CENTER><BR>\n");
}
function makeparty(){
$temp = '
당신은 파티에 참가하고 있지 않습니다.<BR>
원하실 경우 파티를 생성할 수 있습니다.<BR><p>
<FORM style="margin:0" action="index.php" method="post">
Party name : <INPUT size="20" type="text" maxlength="16" name="partyname">
<INPUT type="hidden" name="hid" value="party"><INPUT type="hidden" name="order" value="makeparty">
<INPUT type="submit" name="order" value="makeparty">
</FORM>
';
mes($temp);
}
function partystatbar($hpspexp, &$data){
$val=$data["$hpspexp"];
if($hpspexp=="exp"){
$div= calcexp($data["level"]);
} else {
$div=$data["max".$hpspexp];
}
$length = $val/$div*100*1.15;
$length = round($length) + 1;
$bar = ""."./images/bg/bar.png";
if($hpspexp=="hp"){
$color=""."./images/bg/bar_r.png";
$span="HP_BAR";
}elseif($hpspexp=="sp"){
$color=""."./images/bg/bar_b.png";
$span="SP_BAR";
}else{
$color=""."./images/bg/bar_g.png";
}
$bar = '
<TABLE width="128" height="7" cellpadding="2" cellspacing="4">
<TBODY>
<TR>
<TD background="'.$bar.'"><IMG src="'.$color.'" width="'.$length.'" height="3" border="0"></TD>
</TR>
</TBODY>
</TABLE>
';
return $bar;
}
function partytable($human,$hpbar=NULL,$spbar=NULL){
$row = 0;
mes("<TABLE width=\"100%\"><TBODY><TR>\n");
foreach($human as $key => $value){
$who = load_id($value);
if($row%5==0&&$row!=0) mes("</TR><TR>\n");
$map = mapname($who["map"]);
$charimg = ""."./images/char1/".$who["job"].$who["gender"].".png";
$code=crypt($who["id"],CRYPT_KEY);
$code=substr($code,2);
if($who["state"]==1) {
$who["name"]="<FONT COLOR=\"red\"><B>".$who["name"]."</B></FONT>";
if(PARTY_DEAD_IMG) $charimg = ""."./images/char1/ddd.png";
}
$job=loadjob($who);
$job=$job["name"];
if($hpbar) $hpbar=partystatbar('hp',$who);
if($spbar) $spbar=partystatbar('sp',$who);
if(checkonline($who,PT_ONLINE_TIME)) $online = "<img src=\""."./images/icon/001.png\"align=\"left\">";
elseif(checkonline($who,GET_NORMAL_EXP_TIME)) $online = "<img src=\""."./images/icon/002.png\"align=\"left\">";
else $online = NULL;
$temp = '
<TD align="center" valign="bottom">
<img src="'.$charimg.'"><BR>
<TABLE><TBODY><TR><TD nowrap><CENTER>
'.$online.$who['name'].'<BR>Lv'.$who['level'].'/'.$job.'
</CENTER></TD></TR></TBODY></TABLE>
'.$hpbar.'
'.$spbar.'
At '.$map.'<BR>
<INPUT type="radio" name="char" value="'.$code.'">
</TD>
';
mes($temp);
$row++;
}
mes("</TR></TBODY></TABLE>\n");
}
function humantable($human){
$row = 0;
mes("<TABLE width=\"100%\"><TBODY><TR>\n");
foreach($human as $key => $value){
$who = load_id($value);
if(isset($who["party"])) continue; 
if($row%5==0&&$row!=0) mes("</TR><TR>\n");
$map = mapname($who["map"]);
$charimg = ""."./images/char1/".$who["job"].$who["gender"].".png";
$code=crypt($who["id"],CRYPT_KEY);
$code=substr($code,2);
$temp = '
<TD align="center" valign="bottom">
<img src="'.$charimg.'">
<BR><BR>'.$who['name'].'(Lv'.$who['level'].')<BR>At '.$map.'<BR>
<INPUT type="radio" name="char" value="'.$code.'">
</TD>
';
mes($temp);
$row++;
}
mes("</TR></TBODY></TABLE>\n");
}
function setting($msg){
$text= str_replace("<BR>","\n",$msg["message"]);
$temp = '
<FORM style="margin:0" action="index.php" method="post">
적을 해치웠을 때의 대사<BR>
<INPUT size="34" type="text" maxlength="30" name="wkill" value="'.(isset($msg['wkill']) ? $msg['wkill'] : '').'"><BR>
적에게 당하였을 때의 대사<BR>
<INPUT size="34" type="text" maxlength="30" name="wdie" value="'.(isset($msg['wdie']) ? $msg['wdie'] : '').'"><BR>
SP가 부족하여 공격에 실패할 때의 대사<BR>
<INPUT size="34" type="text" maxlength="30" name="wspout" value="'.(isset($msg['wspout']) ? $msg['wspout'] : '').'"><BR>
츳코미<BR>
<INPUT size="34" type="text" maxlength="30" name="tsccomi" value="'.(isset($msg['tsccomi']) ? $msg['tsccomi'] : '').'"><BR>
승리대사<BR><INPUT size="34" type="text" maxlength="30" name="win" value="'.(isset($msg['win']) ? $msg['win'] : '').'"><BR>
패배대사<BR><INPUT size="34" type="text" maxlength="30" name="lose" value="'.(isset($msg['lose']) ? $msg['lose'] : '').'"><BR>
자기소개<BR>
<TEXTAREA name="message" rows="8" cols="58">'.$text.'</TEXTAREA>
<BR><BR>
<INPUT type="submit" name ="order" value="메시지 설정">
</FORM><BR>
<div style="width:460px; overflow:hidden;">
'.$msg['message'].'
</div>
';
mes($temp);
}
function confirm($command,$button,$message){
$temp = '
'.$message.'<BR><BR>
<FORM style="margin:0" action="index.php" method="post">
<INPUT type="hidden" name="order" value="'.$command.'">
<INPUT type="submit" value="'.$button.'">
</FORM>
';
mes($temp);
}
function mapmove($map){
$canmove = canmove($map);
foreach($canmove as $value){
$mapname[]=mapname($value);
}
$now = mapname($map);
mes("당신은 현재 <B>$now</B>에 있습니다.<BR>");
$mapimg = smapview($map);
$mapimg = "<img src=\"".""."./images/bg/".$mapimg.".png\" border=\"1\">";
mes("$mapimg");
mes("<FORM style=\"margin:0\" action=\"index.php\" method=\"post\"><TABLE><TBODY>");
foreach($mapname as $key => $value){
$radio = '
<TR>
<TD><INPUT type="radio" name="mapmove" value="'.$canmove[$key].'"></TD>
<TD>'.$value.'</TD>
</TR>
';
mes($radio);
}
mes("</TBODY></TABLE><INPUT type=\"submit\" name=\"order\" value=\" move \"></FORM>");
mes("<BR>월드맵을 보시려면 이쪽을 클릭하세요 <a href=\""."./images/bg/map01.png\" target=_blank>클릭</a><BR><BR>");
}
function statdetail($data){
$charimg = ""."./images/char0/".$data["job"].$data["gender"].".png";
$exp = pow($data["level"]-1,2)/2*100+100;
$hp = floor($data["hp"]);
$sp = floor($data["sp"]);
$birth = date("M j,Y",$data["starting_timestamp"]);
$gend = gender($data['gender']);
$stat = '
<FORM style="margin-bottom:0" action="index.php" method="post">
<TABLE cellpadding="8"><TBODY><TR><TD valign="top">
<TABLE><TBODY>
<TR><TD align="right">Nickname : </TD><TD>'.$data['name'].'</TD></TR><TR>
<TD align="right">성        별 : </TD><TD>'.$gend.'</TD></TR><TR>
<TD align="right">등록 일자 : </TD><TD nowrap>'.$birth.'</TD></TR><TR>
<TD height="10"></TD><TD></TD></TR><TR>
<TD align="right">스탯포인트 :</TD><TD>'.$data['statuspoint'].'</TD></TR><TR>
<TD align="right">스킬포인트 :</TD><TD>'.$data['sklp'].'</TD></TR><TR>
</TBODY></TABLE>
';
mes($stat);
$ifile=""."./images/icon/";
$itype=".png";
$a=array("up_str","up_int","up_dex","up_spd");
mes("<CENTER><BR><TABLE cellpadding=\"2\" cellspacing=\"0\"><TBODY><TR>\n");
if(isset($data["party"])) mes("<TD colspan=\"2\"><img src=\"".$ifile."changeposi_dis".$itype."\" 
alt=\"disable\"></TD></TR><TR>");
else mes("<TD colspan=\"2\"><INPUT type=\"image\" src=\"".$ifile."changeposi".$itype."\" 
alt=\"change position\" name=\"changeposition\"></TD></TR><TR>");
foreach($a as $val){
$name = substr($val,-3,3);
if($data['statuspoint']>0){
mes("<TD><INPUT type=\"image\" src=\"".$ifile.$val.$itype."\" 
alt=\"increase ".$name."\" name=\"".$val."\"></TD>\n");
} else {
mes("<TD><img src=\"".$ifile.$val."dis".$itype."\" alt=\"disable\"></TD>");
}
if($val=="up_int") mes("</TR><TR>");
}
mes("</TR><TR><TD colspan=\"2\">");
mes("<INPUT type=\"image\" src=\"".$ifile."learnskill".$itype."\" name=\"learnskill\">");
mes("</TD></TR></TBODY></TABLE></CENTER>\n");
foreach($data['skill'] as $key => $val) $s[]=loadskill("$val");
mes("</TD><TD valign=\"top\">\n");
for($i=0; $i<5; $i++){
$turnnum=$i+1;
mes("turn".$turnnum."<br><SELECT name=\"turn".$turnnum."\">");
foreach($s as $key => $val){
if($s["$key"]["method"]<10){
$idcrypted=mycrypt($s["$key"]["id"]);
if($data["turn"][$i]==$s["$key"]["id"]){
mes("<OPTION value=\"".$idcrypted."\" selected>{$s[$key]['name']}</OPTION>\n");
} else {
mes("<OPTION value=\"".$idcrypted."\">{$s[$key]['name']}</OPTION>\n");
}
}
}
mes("</SELECT><br>\n");
}
mes("<BR><INPUT type=\"submit\" name =\"order\" value=\"공격설정 저장\"><p>\n");
mes("<B>습득한 스킬</b><p>");
foreach($s as $key => $val){
$idcrypted = mycrypt($s[$key]["id"]);
$crypturl = "./lib/detailskill.php?id=".$idcrypted;
$img = ""."./images/icon/".$s["$key"]["img"].".png";
mes("<img src=\"$img\"><A href=javascript:itemdetail(\"$crypturl\")>{$s[$key]['name']}</A><BR>");
}
mes("</TD></TR></TBODY></TABLE></FORM>\n");
}
function itemtable($list, $name, $title, $equip=NULL){
mes("&nbsp;<b>아이템</b><BR>=-=-=-=-=-=-=-=-=-=-=<BR>\n");
if(($list[0]=="")&&(_count($list)<=1)) {
mes("<TABLE width=\"100%\"><TBODY>\n");
mes("<TR><TD>Nothing<br></TD></TR>\n");
} else {
mes("<TABLE cellpadding=\"3\"><TBODY>\n");
}
foreach($list as $key => $value){
if(empty($value)) continue;
$radiodisable="";
$idetail = outputidetail($value);
$img = ""."./images/icon/".$idetail['img'].".png";
$idcrypted = mycrypt($idetail['id']);
$crypturl = "./lib/detailitem.php"."?id=".$idcrypted;
if(!empty($equip)){
if(in_array($value,$equip)){
unset($equip[array_search($value,$equip)]);
$idetail['name'] .= "</A>&nbsp;<b>장착</b><A>";
$radiodisable = " disabled";
}
}
$temp = '
<TR>
<TD><INPUT type="radio" name="'.$name.'" value="'.$idcrypted.'"'.$radiodisable.'></TD>
<TD><IMG src="'.$img.'" width="24" height="24" border="0"></TD>
<TD width="100%"><A href=javascript:itemdetail("'.$crypturl.'")>'.$idetail['name'].'</A></TD>
</TR>
';
mes($temp);
}
}
function itemtableclose($bt1="", $bt2="", $bt3=""){
if($bt1!="") mes("<TR><TD colspan=\"3\" align=\"left\"><BR><span style=\"padding:30px\">
<INPUT type=\"submit\" name =\"order\" value=\"".$bt1."\">\n");
if($bt2!="") mes("<INPUT type=\"submit\" name =\"order\" value=\"".$bt2."\">\n");
if(($bt1!="")&&($bt2!="")&&($bt3=="")) mes("</span><BR><BR></TD></TR>");
if($bt3!="") mes("<INPUT type=\"submit\" name =\"order\" value=\"".$bt3."\"></span><BR><BR></TD></TR>\n");
if(($bt1!="")&&($bt2=="")&&($bt3=="")) mes("</span><BR><BR></TD></TR>");
mes("</TBODY></TABLE>\n");
}
function seepeople(&$data,$target,$showitem="0"){
$ptname = "";
$recycle = "";
$trade = "";
$ask = "";
if(!$char=at_same_map($data,$target)) return false;
$msg = load_msg($char["id"]);
$job = loadjob($char);
$charimg = ""."./images/char1/".$char["job"].$char["gender"].".png";
if(isset($char["party"])){
if(!isset($data["party"])) $ask = "<INPUT type=\"submit\" name =\"order\" value=\"request party\"><BR>";
$pt = load_party($char["party"]);
if(isset($pt['name'])) $ptname = "Party : {$pt['name']}<BR>";
}
if($data["level"]>9) $trade = "<INPUT type=\"submit\" name =\"order\" value=\"send item\"><BR>";
if(checkrecycle($char)){
if($char["gender"] == 1) $gend = "her";
else $gend = "him";
$recycle = "<INPUT type=\"submit\" name =\"order\" value=\"made $gend sleep\"><BR>";
}
$temp = '
Name : '.$char['name'].'<BR>
Level : '.$char['level'].'<BR>
Job : '.$job['name'].'<BR>
'.$ptname.'
<img src="'.$charimg.'"><BR><BR>
<FORM style="margin:0" action="index.php" method="post">
<INPUT type="hidden" name="hid" value="'.$target.'">
'.$recycle.$trade.$ask.'<BR>
';
mes($temp);
if(!$showitem){
$temp = '
<div style="width:460px; overflow:hidden;">
'.$msg['message'].'
</div>
';
mes($temp);
} else {
itemtable($data["item"], "item_s", "item", $data["equip"]);
itemtableclose(" send ");
mes("</FORM>");
}
}
function showwhois(&$data){
$row = 0;
$ptname = "";
$mapdata=$data["mdata"];
$npcdata=whoisnpc($data);
mes("Person at <FONT SIZE=5><B>".$mapdata['name']."</B></FONT><br>");
if(isset($npcdata)){
mes("--- NPC ---------<BR>");
mes("<TABLE width=\"100%\"><TBODY><TR>\n");
foreach($npcdata as $key => $val){
if($row%5==0&&$row!=0) mes("</TR><TR>\n");
$npc = load_npc($data,$val);
$charimg = ""."./images/char1/".$npc["img"].".png";
$val = mycrypt($val);
$temp = '
<TD align="center" valign="bottom">
<FORM style="margin-bottom:0" name="n'.$key.'" action="index.php" method="post">
<INPUT type="hidden" name="order" value="meetnpc">
<INPUT type="hidden" name="hid" value="'.$val.'">
<img src="'.$charimg.'" style="cursor:pointer;" onClick="document.n'.$key.'.submit()">
<CENTER>'.$npc['name'].'</CENTER>
</FORM></TD>
';
mes($temp);
$row++;
}
mes("</TR></TBODY></TABLE>\n");
}
mes("--- PLAYER ------<BR>");
if(_count($mapdata["char"])==1){
mes("Only {$data['name']} is here.<br>\n");
return false;
}
mes("<TABLE width=\"100%\"><TBODY><TR>\n");
$row = 0;
foreach($mapdata["char"] as $key => $value){
if("$value"!=$data["id"]){
if(!$who = load_id($value)){
delmap($data["map"],$value);
mes("※존재하지 않는 아이디를 맵에서 삭제했습니다<BR>");
continue;
}
if($who["state"]==1) continue; 
if($row%5==0&&$row!=0) mes("</TR><TR>\n");
$charimg = ""."./images/char1/".$who["job"].$who["gender"].".png";
$code=crypt($who["id"],CRYPT_KEY);
$code=substr($code,2);
if(isset($who["party"])){
$whoesparty=load_party($who["party"]);
if(isset($whoesparty["name"])) $ptname = $whoesparty["name"]."<BR>";
} else $ptname = NULL;
if(checkrecycle($who)) $recycle = "<img src=\"".""."./images/icon/angel.png"."\" align=\"left\">";
else $recycle = NULL;
$temp = '
<TD align="center" valign="bottom">
<FORM style="margin-bottom:0" name="c'.$key.'" action="index.php" method="post">
<INPUT type="hidden" name="order" value="search">
<INPUT type="hidden" name="hid" value="'.$code.'">
<img src="'.$charimg.'" style="cursor:pointer;" onClick="document.c'.$key.'.submit()">
<TABLE><TBODY><TR><TD nowrap><CENTER>
'.$recycle.$who['name'].'<BR>Lv'.$who['level'].'<BR>'.$ptname.'<BR>
</CENTER></TD></TR></TBODY></TABLE>
</FORM></TD>
';
mes($temp);
$row++;
}
}
mes("</TR></TBODY></TABLE>\n");
}
function equiptable(&$data, $name){
mes("&nbsp;<b>착용중인 장비</b><BR>=-=-=-=-=-=-=-=-=-=-=<BR>");
if(($data['equip'][0]=="")&&($data['equip'][1]=="") && ($data['equip'][2]=="") && ($data['equip'][3]=="")){
mes("<TABLE width=\"100%\"><TBODY>");
mes("<TR><TD>No Equipment.<br></TD></TR>\n");
} else {
mes("<TABLE cellpadding=\"3\"><TBODY>");
}
showequipstat($data);
foreach($data["equip"] as $key => $value){
if(empty($value)) continue;
$idetail = outputidetail($value);
$img = ""."./images/icon/".$idetail['img'].".png";
$idcrypted = mycrypt($idetail['id']);
$crypturl = "./lib/detailitem.php"."?id=".$idcrypted;
$temp = '
<TR>
<TD><INPUT type="radio" name="'.$name.'" value="'.$idcrypted.'"></TD>
<TD><IMG src="'.$img.'" width="24" height="24" border="0"></TD>
<TD width="100%"><A href=javascript:itemdetail("'.$crypturl.'")>'.$idetail['name'].'</A></TD>
</TR>
';
mes($temp);
}
}
function html($intromap, $button, $intro2=""){
$temp1 = '
<HR color="#CACACA">
'.$intromap.'
<HR color="#CACACA">
<TABLE width="100%" cellpadding="3" cellspacing="0">
<TBODY>
<TR><TD>
';
$temp2 = "";
foreach($button as $key => $val){
if($key=="0"){
$temp2 .= $val;
} else {
$temp2 .= "<BR><BR>".$val;
}
}
$temp3 = '
</TD><TD width="100%" valign="top">'.$intro2.'</TD>
</TR>
</TBODY>
</TABLE>
';
mes($temp1.$temp2.$temp3);
}
function m_icon(&$menu){
switch($menu){
case "search":
$val="search";
$img="m_search";
break;
case "shop1":
$val="shop";
$img="m_shop01";
break;
case "battle1":
$val="battle";
$img="m_battle01";
break;
case "":
break;
}
$menu = "<INPUT type=\"image\" src=\""."./images/icon/".$img.".png\" alt=\"".$val."\" name=\"".$val."\">";
}
function javarecover(&$data){
$hprate=HP_REC_RATE;
$sprate=SP_REC_RATE;
$hpcolor=""."./images/bg/bar_r.png";
$spcolor=""."./images/bg/bar_b.png";
print '
<script language="JavaScript">
n=0;
function recover(){
hp='.$data['hp'].'+n*'.$hprate.';
sp='.$data['sp'].'+n*'.$sprate.';
if(hp>'.$data['maxhp'].'){hp='.$data['maxhp'].'}
if(sp>'.$data['maxsp'].'){sp='.$data['maxsp'].'}
hplength=hp/'.$data['maxhp'].'*100*1.15;
hplength=Math.round(hplength)+1;
splength=sp/'.$data['maxsp'].'*100*1.15;
splength=Math.round(splength)+1;
document.getElementById("HP_NOW").innerHTML = Math.floor(hp);
document.getElementById("SP_NOW").innerHTML = Math.floor(sp);
document.getElementById("HP_BAR").width = hplength;
document.getElementById("SP_BAR").width = splength;
n++;
setTimeout("recover()",1000);
}
</script>
';
}
function headlogin(&$data){
print '
</HEAD>
<BODY bgcolor="#98a0a5" text="#8a9cb7" link="#8a9cb7" vlink="#8a9cb7" alink="#8a9cb7" onLoad="recover()">
<center>
<TABLE cellpadding="0">
<TBODY>
<Td bgcolor="#10151b">
<img src="./images/bg/tekitomain.gif">
</Td>
<TR>
<TD bgcolor="#304052" width="740">
<FORM style="margin:0;width:100%" action="index.php" method="post">
<TABLE cellpadding="5" width="100%"><TBODY><TR><TD>
<INPUT type="submit" name="order" value="맵을 탐색한다">
<INPUT type="submit" name="order" value=" 지도 ">
<INPUT type="submit" name="order" value="스테이터스">
<INPUT type="submit" name="order" value=" 아이템 ">
<INPUT type="submit" name="order" value=" 정보 ">
<INPUT type="submit" name="order" value=" 파티 ">
<INPUT type="submit" name="order" value=" 랭킹 ">
<INPUT type="submit" name="order" value="도움말">
';
if(BBSLOCAL != "") print '<INPUT type="submit" name="order" value="게시판">';
print '
<INPUT type="submit" name="order" value="logoff">
</TD></TR></TBODY></TABLE>
</center>
';
print '</FORM></TD></TR><TR><TD>';
}
function mainstat(&$data){
$job=loadjob($data);
$code=mycrypt($data["job"]);
$crypturl="./lib/detailjob.php?id=".$code;
$charimg = ""."./images/char1/".$data["job"].$data["gender"].".png";
$exp = calcexp($data["level"]);
$hp = floor($data["hp"]);
$sp = floor($data["sp"]);
$hpbar = statbar('hp', $data);
$spbar = statbar('sp', $data);
$expbar = statbar('exp', $data);
$jxpbar = statbar('jxp', $data);
if($data["jlv"]>9) $jlv="Limit!";
else $jlv=$data["jlv"];
if($data["jlv"]>9) $jxp="Limit!";
else $jxp=$data["jxp"]."/".calcjxp($data["jlv"]);
$job = loadjob($data);
$state = statenow($data["state"]);
$position = position($data["position"]);
$money = $data['money']." ".M_CURRENCY;
if(isset($data["jdata"]["bonus"])){
foreach($data["jdata"]["bonus"] as $key => $val){
if($val>0) ${"b".$key}=" + $val";
}
}
print '
<TABLE cellpadding="15" cellspacing="0">
<TBODY>
<TR>
<TD bgcolor="#202935" valign="top">
ID : '.$data['id'].'<br>
Name : '.$data['name'].'<br>
<img src="'.$charimg.'"><br>
Lv : '.$data['level'].'<br>
Job : <A href=javascript:itemdetail("'.$crypturl.'")>'.$job['name'].'</a><br>
JLv : '.$jlv.'<br><br>
HP : <SPAN id="HP_NOW">'.$hp.'</SPAN>/'.$data['maxhp'].'
'.$hpbar.'
SP : <SPAN id="SP_NOW">'.$sp.'</SPAN>/'.$data['maxsp'].'
'.$spbar.'
EXP : '.$data['exp'].'/'.$exp.'
'.$expbar.'
JXP : '.$jxp.'
'.$jxpbar.'
<center>
<TABLE><TBODY>
<tr>
<td align="center"><b>현재금액</b></td><tr>
<td align="center">'.$money.'</td></tr>
</TBODY></TABLE>
</center>
<br>
<center>
<table border="0">
<tr>
<td><div align="right"><font size="2">STR :</div></td>
<td><font size="2">'.$data['str'].'</td>
</tr>
<tr>
<td><div align="right"><font size="2">INT :</div></td>
<td><font size="2">'.$data['int'].'</td>
</tr>
<tr>
<td><div align="right"><font size="2">DEX :</div></td>
<td><font size="2">'.$data['dex'].'</td>
</tr>
<tr>
<td><div align="right"><font size="2">SPD :</div></td>
<td><font size="2">'.$data['spd'].'</td>
</tr>
<tr>
<td height="10"></td>
<td></td>
</tr>
<tr>
<td><div align="right"><font size="2">상태 :</div></td>
<td><font size="2">'.$state.'</td>
</tr>
<tr>
<td><div align="right"><font size="2">위치 :</div></td>
<td><font size="2">'.$position.'</td>
</tr>
<tr>
<td><div align="right"><font size="2">지도 :</div></td>
<td><font size="1">'.$data['mdata']['name'].'</td>
</tr>
</table>
</center>
<TD bgcolor="#10151b" valign="top" width="100%">
';
}
function login($id){
print '
<FORM action="index.php" method="post">
<CENTER> <br>
<img src="./images/bg/main.gif">
<p>
<TABLE>
<TBODY>
<TR>
<TD align="right"><b>ID :</b></TD>
<TD><input type="text" name="id" size="20" value="" maxlength="12"></TD>
</TR>
<TR>
<TD align="right"><b>PASS :</b></TD>
<TD><input type="password" name="pass" value="" size="20" maxlength="12"></TD>
</TR>
<TR>
<TD></TD>
<TD><INPUT type="submit" name ="order" value="로그인"> 
<INPUT type="submit" name ="order" value=" 신규가입 "></TD>
</TR>
</TBODY>
</TABLE>
';
if(!BBSLOCAL == "") print '<a href="'.BBSLOCAL.'">게시판 가기</a>';
print '
</CENTER>
</FORM>
';
}
function make(){
print '
<FORM style="margin-bottom:0" action="index.php" method="post"><CENTER><BR><BR>
<TABLE><TBODY><TR>
<TD align="right">ID :</TD>
<TD colspan="8"><INPUT size="20" type="text" name="u_id" value="" maxlength="8"></TD>
</TR><TR>
<TD align="right">Pass :</TD>
<TD colspan="8"><INPUT size="20" type="password" name="u_pass" value="" maxlength="12"></TD>
</TR><TR>
<TD align="right">Name :</TD>
<TD colspan="8"><INPUT size="20" type="text" name="u_name" value="" maxlength="16"></TD>
</TR><TR>
<TD align="right">Gender :</TD>
<TD colspan="8"><SELECT name="u_gender">
<option value="0">♂</option>
<option value="1">♀</option></SELECT></TD>
</TR><TR>
<TD align="right">Job :</TD>
<TD colspan="8"><SELECT name="u_job">
<option value="00">Soldier</option>
<option value="01">Magician</option>
<option value="02">Scout</option></SELECT></TD>
</TR><TR>
<TD></TD><TD><font size=2>STR</font></TD>
<TD></TD><TD><font size=2>INT</font></TD>
<TD></TD><TD><font size=2>DEX</font></TD>
<TD></TD><TD><font size=2>SPD</font></TD>
<TD></TD>
</TR><TR>
<TD align="right">Status :</TD>
<TD><SELECT name="u_str">
';
for($a=0; $a<21; $a++){
if($a==5){
echo "<OPTION value=\"$a\" selected>$a</OPTION>\n";
}else{
echo "<OPTION value=\"$a\">$a</OPTION>\n";
}
}
echo "</SELECT></TD><TD>+</TD>\n";
echo "<TD><SELECT name=\"u_int\">\n";
for($a=0; $a<21; $a++){
if($a==5){
echo "<OPTION value=\"$a\" selected>$a</OPTION>\n";
}else{
echo "<OPTION value=\"$a\">$a</OPTION>\n";
}
}
echo "</SELECT></TD><TD>+</TD>\n";;
echo "<TD><SELECT name=\"u_dex\">\n";
for($a=0; $a<21; $a++){
if($a==5){
echo "<OPTION value=\"$a\" selected>$a</OPTION>\n";
}else{
echo "<OPTION value=\"$a\">$a</OPTION>\n";
}
}
echo "</SELECT></TD><TD>+</TD>\n";
echo "<TD><SELECT name=\"u_spd\">\n";
for($a=0; $a<21; $a++){
if($a==5){
echo "<OPTION value=\"$a\" selected>$a</OPTION>\n";
}else{
echo "<OPTION value=\"$a\">$a</OPTION>\n";
}
}
print '
</SELECT></TD><TD><b>= 20</b></TD>
</TR><TR>
<TD></TD>
<TD colspan="8"><INPUT type="submit" name ="order" value="make"><INPUT type="submit" name ="order" value="취소"></TD>
</TR></TBODY></TABLE></CENTER></FORM>
';
}
function statenow($num){
switch($num){
case 0: return "<FONT COLOR=\"BLUE\">ALIVE</FONT>";
case 1: return "<FONT COLOR=\"RED\"><B>DEAD</B></FONT>";
case 2: return "<FONT COLOR=\"PURPLE\"><B>POISON</B></FONT>";
default : return "UNKNOWN";
}
}
function installer(){
$mes = "";
$errmes = "";
if(!file_exists("./data/")){
mkdir("./data/") or ($errmes .="./data/ 폴더 또는 파일 생성 실패.<br>\n");
$mes .="파일 또는 폴더가 생성됨. : ./data/<br>\n";
}
if(!file_exists("./data/message/")){
mkdir("./data/message/") or ($errmes .="./data/message/ 폴더 또는 파일 생성 실패.<br>\n");
$mes .="파일 또는 폴더가 생성됨. : ./data/message/<br>\n";
}
if(!file_exists("./data/party/")){
mkdir("./data/party/") or ($errmes .="./data/party/ 폴더 또는 파일 생성 실패.<br>\n");
$mes .="파일 또는 폴더가 생성됨. : ./data/party/<br>\n";
}
if(!file_exists("./data/user/")){
mkdir("./data/user/") or ($errmes .="./data/user/ 폴더 또는 파일 생성 실패.<br>\n");
$mes .="파일 또는 폴더가 생성됨. : ./data/user/<br>\n";
}
if(!file_exists("./data/whois.txt")){
$fp = fopen("./data/whois.txt","w") or ($errmes .="./data/whois.txt 폴더 또는 파일 생성 실패.<br>\n");
fwrite($fp,"100=\"\"\n");
fclose($fp);
$mes .="파일 또는 폴더가 생성됨. : ./data/whois.txt<br>\n";
}
if(!file_exists("./data/worldstat.txt")){
$fp = fopen("./data/worldstat.txt","w") or ($errmes .="./data/worldstat.txt 폴더 또는 파일 생성 실패.<br>\n");
fwrite($fp,"level_rank_id=\"\"\n");
fclose($fp);
$mes .="파일 또는 폴더가 생성됨. : ./data/worldstat.txt<br>\n";
}
if(!file_exists("./data/index.php")){
$fp = fopen("./data/index.php","w") or ($errmes .="./data/index.php 폴더 또는 파일 생성 실패.<br>\n");
fwrite($fp,"<?php
//폴더 못건들게 막는 소스
header(\"location:../\");
?>\n");
fclose($fp);
$mes .="파일 또는 폴더가 생성됨. : ./data/index.php<br>\n";
}
if(!file_exists("./data/party/index.php")){
$fp = fopen("./data/party/index.php","w") or ($errmes .="./data/party/index.php 폴더 또는 파일 생성 실패.<br>\n");
fwrite($fp,"<?php
//폴더 못건들게 막는 소스
header(\"location:../\");
?>\n");
fclose($fp);
$mes .="파일 또는 폴더가 생성됨. : ./data/party/index.php<br>\n";
}
if(!file_exists("./data/message/index.php")){
$fp = fopen("./data/message/index.php","w") or ($errmes .="./data/message/index.php 폴더 또는 파일 생성 실패.<br>\n");
fwrite($fp,"<?php
//폴더 못건들게 막는 소스
header(\"location:../\");
?>\n");
fclose($fp);
$mes .="파일 또는 폴더가 생성됨. : ./data/message/index.php<br>\n";
}
if(!file_exists("./data/user/index.php")){
$fp = fopen("./data/user/index.php","w") or ($errmes .="./data/user/index.php 폴더 또는 파일 생성 실패.<br>\n");
fwrite($fp,"<?php
//폴더 못건들게 막는 소스
header(\"location:../\");
?>\n");
fclose($fp);
$mes .="파일 또는 폴더가 생성됨. : ./data/user/index.php<br>\n";
}
if(!$errmes == ""){
$errmes .= "<br>\n";
$errmes .= "뒤로 버튼을 눌러 재 실행.<br>\n";
ErrShow($errmes);
}
if(!$mes == "" && $errmes == ""){
$mes .= "<br>\n";
$mes .= "뒤로 버튼을 눌러 재 실행.<br>\n";
ErrShow($mes);
}
}
?>