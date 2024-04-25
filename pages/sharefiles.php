<?php
require_once "_classes/subs.php";
require_once "_classes/share.php";
require_once "_classes/core.php";

$core = new Core;
$Share = new Share();
$template = new template();

//Language
$language = new language($_ENV['GUI_LANGUAGE']);
$lang = $language->translate();

$queryString = strstr($_SERVER['REQUEST_URI'], '?');    // $queryString enth�lt jetzt "?arg1=foo&arg2=bar" oder (bool)false falls keine Parameter definiert wurden
$queryString = ($queryString===false) ? '' : substr($queryString, 1);
 
$Sharelist = $Share;

if(!empty($_GET['clear_list'])){
	$_SESSION['shareexport']=array();
	�template->allert("success",$lang->Share->success, $lang->Share->link_export_alert);
}

if (!empty($_GET['shareexpfile'])) {
    $_SESSION['shareexport'] = [];
    foreach ($_GET['shareexpfile'] as $expid) {
        $shareentry = $Sharelist->get_file($expid);
        $export_currlink = $shareentry['LINK'];
        $testx = array_search($export_currlink, $_SESSION['shareexport']);
        if ($testx !== FALSE) continue;
        $_SESSION['shareexport'][] = $export_currlink;
    }
    

if(empty($_GET['exp_format'])) $_GET['exp_format']="Default";

echo "<form name=\"exportform\" action=\"\">";
if(!empty($_GET['withsource']) && $_GET['withsource']=="true"){}

echo '<div class="card">
            <div class="card-body">
              <h5 class="card-title">'.$lang->Share->Link_export_title.'</h5>
';
if(!empty($_SESSION['shareexport'])){
	asort($_SESSION['shareexport']);
	
	foreach($_SESSION['shareexport'] as $a){
		$share_ex = explode('/',$a);
		$share_ex = explode('|',$share_ex[2]);
		$share_ex_link = $a;
		if(!empty($_GET['withsource']) && $_GET['withsource']=="true"){
			$share_ex_link=substr($share_ex_link,0,strlen($share_ex_link)-1)."|"
				.$curr_coreip.":".$_SESSION['phpaj']['core_source_port']
				.$curr_serverip.$curr_serverport."/";
		}
		$share_ex_name = $share_ex[1];
		$share_ex_hash = $share_ex[2];
		$share_ex_bytesize = $share_ex[3];
		$share_ex_size = sizeformat($share_ex[3]);
			}
		
		for($i = 0, $anzahl = count($_SESSION['shareexport']); $i < $anzahl; ++$i) {
			echo $_SESSION['shareexport'][$i]."<br>";
		}
	
}
echo "</div></div>";
echo "<input type=\"button\" value=\""
	.$lang->Share->delet_export.""
	."\" onclick=\"document.location.href='index.php?site=sharefiles&dir=".$_GET["dir"]."&clear_list=1&"
	.SID."';\" />";
echo "</form>";

}

echo "\n<script>
share_ids = [];

function change(id){
	var share_zeile=document.getElementById('zeile_'+id);
	var zelle=share_zeile.firstChild;
	if(share_ids[id]==1){
		share_ids[id]=0;
		while(zelle!=null){
			if(zelle.nodeName=='TD')
				zelle.style.backgroundColor='';
			zelle=zelle.nextSibling;
		}
		document.getElementById('sharecheck_'+id).checked=false;
	}else{
		share_ids[id]=1;
		while(zelle!=null){
			if(zelle.nodeName=='TD')
				zelle.style.backgroundColor='#01c0c8';
			zelle=zelle.nextSibling;
		}
		document.getElementById('sharecheck_'+id).checked=true;
	}
}

function changeshareprio(){
	var shareline='';
	var counter=-1;
	for (var i in share_ids){
		if(share_ids[i]==0) continue;
		counter++;
		shareline+='&sharefile['+counter+']=' + i;
	}
	window.location.href='index.php?site=sharefiles&dir=".urlencode($_GET['dir']) . "'+ shareline + '&sprio=' + document.shareprioform.shareprio.value + '&".SID."';
}

function reload(){
	window.location.href='index.php?site=sharefiles&dir=".urlencode($_GET['dir']) . "&forcereload=1&".SID."';
}

function exportlinks(){
	var shareexpline='';
	var counter=-1;
	
	for (var i in share_ids){
		if(share_ids[i]==0) continue;
		counter++;
		shareexpline+='&shareexpfile['+counter+']=' + i;
	}

	window.location.href='?site=sharefiles&dir=".urlencode($_GET['dir']) . "'+ shareexpline+'&".SID."';
}

function selectall(){
	for(var v in share_ids){
		if(share_ids[v]==0) change(v);
	}
}
	
function selectnone(){
	for(var v in share_ids){
		if(share_ids[v]==1) change(v);
	}
}
</script>";
    
//prio setzen
if(!empty($_GET['sharefile'])){
	$Sharelist->setpriority($_GET['sharefile'],$_GET['sprio']);
}

if(!empty($_GET['forcereload'])){
	$Sharelist->refresh_cache(0);
}

//sharecache neu laden, falls aelter als 60min
$Sharelist->refresh_cache(60);

echo "<form name=\"shareprioform\" action=\"\">\n";
echo'<div class="row clearfix">
                    <div class="col-sm-12">
                        <div class="panel panel-default" data-panel-collapsable="false" data-panel-fullscreen="false" data-panel-close="false">
                        	<div class="panel-heading bg-success"></div>
                            <div class="panel-body">
                            <div class="align-right"><nav aria-label="Page navigation">
                <ul class="pagination">
                  <li class="page-item">
                    <a class="page-link" onclick="exportlinks();">'.$lang->Share->export.'</a>
                  </li>
                  
                  <li class="page-item"><a class="page-link" onclick="reload();"><i class="fa fa-repeat"></i></a></li>
                </ul>
              </nav>
               <div class="form-group">
               <div class="col-sm-10"></div>
                                <div class="col-sm-2">
                                    <div class="input-group">
                           
               <select class="form-control" name="shareprio">';
                                      for($i=1;$i<=250;$i++){
										echo "<option value=\"$i\">".$i."</option>";
									  }
									  echo'</select>
                                        <span class="input-group-btn">
                                            <button class="btn btn-default" type="button" onclick="changeshareprio();">Set</button>
                                        </span>
                              </div></div></div></div>
       </div>   


                            	<div class="table-responsive">
									<table class="table table-striped">
										<thead>
                							<tr>
                    							<th scope="col">#</th>
                								<th scope="col">'.$lang->Share->name.'</th>
                    							<th width="1" scope="col"><i class="fa fa-info-circle text-info"></i></th>
                    							<th scope="col">'.$lang->Share->size.'</th>
                    							<th width="3" scope="col">'.$lang->Share->prio.'</th>
                    						</tr>
            							</thead>
                						<tbody>';
echo'<tr>
		<th colspan="5" class="align-right">'.$lang->System->select.'
		<input type="button" value="'.$lang->System->all.'" onclick="selectall();" />
		<input type="button" value="'.$lang->System->none.'"" onclick="selectnone();" />
		</th>
	</tr>';
//unterverzeichnisse
$dirliste=$Sharelist->directory($_GET['dir']);
foreach($dirliste as $a){

echo'<tr>
		<td width="1"><i class="fa fa-folder"></i></td>
		<td colspan="4">
			<a href="index.php?site=sharefiles&dir='.rawurlencode($a[0]).'" aria-current="true">
            '.htmlspecialchars($a[1]).'
            </a>
        </td>
    </tr>';
}

//dateien anzeigen
$items = $Sharelist->get_fileids($_GET['dir']);

$list = [];
foreach($items as $item) {
    $file = $Sharelist->get_file($item);
    $list[$file['SHORTFILENAME']] = $file;
}
unset($items);

ksort($list);

foreach($list as $shareentry){
    $a = $shareentry['ID'];
    $prio = $shareentry['PRIORITY'];
    $lastasked=(isset($shareentry['LASTASKED'])) ?
		date("j.n.y - H:i:s",($shareentry['LASTASKED']/1000))
		: "N/A";
	if(!isset($shareentry['ASKCOUNT'])) $shareentry['ASKCOUNT']="N/A";
	if(!isset($shareentry['SEARCHCOUNT'])) $shareentry['SEARCHCOUNT']="N/A";
	
    $tooltip = "<small align='left'>ID:".$a."<br>Letzte Anfrage: ".$lastasked."<br>Anzahl Anfragen: "
    			.$shareentry['ASKCOUNT']."<br>Anzahl Suchanfragen: ".$shareentry['SEARCHCOUNT']."<br>"
    			."<a href='".addslashes($shareentry['LINK'])."'>Scource Link</a></small>";
    echo'<tr>
		<td width="1" class="form-group"><input type="checkbox" id="sharecheck_'.$a.'" onclick="change('.$a.')" />';
                    echo"<script>\n"
						."share_ids[$a]=0;\n"
						."</script>";
					echo'</td>
		<td id="zeile_'.$a.'" data-toggle="tooltip" data-placement="bottom" title="'.$tooltip.'" twipsy-content-set="true" data-html="true" aria-current="true">
			'.$shareentry["SHORTFILENAME"].'</td>
		<td>'; echo"<a href='".sprintf($_ENV['REL_INFO'], $shareentry['LINK'])."'><i class='fa fa-info-circle'></i></a>"; echo'</td>
		<td>'.sizeformat($shareentry["SIZE"]).'</td>
		<td>'.$prio.'</td>
    </tr>';
}

echo "</tbody></table></div></div></div></div></div>
</form>";
echo strtr($lang->Share->prio_spend ,array("%spent"=>$Sharelist->spentprio));

echo "</body>
</html>";
