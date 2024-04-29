<?php

use appleJuiceNETZ\appleJuice\Core;
use appleJuiceNETZ\appleJuice\Downloads;
use appleJuiceNETZ\appleJuice\Server;
use appleJuiceNETZ\appleJuice\Share;
use appleJuiceNETZ\appleJuice\Uploads;
use appleJuiceNETZ\GUI\Icons;
use appleJuiceNETZ\GUI\subs;
use appleJuiceNETZ\GUI\template;
use appleJuiceNETZ\Kernel;

$language = Kernel::getLanguage();
$lang = $language->translate();

//Classes abrufen
$Servers = new Server();
$core = new Core();
$icon_img = new Icons();
$subs = new subs();
$template = new template();
$Uploadlist = new Uploads();
$Sharelist = new Share();

//Cache laden

//Info holen
$modified = $core->command("xml", "modified.xml?filter=informations");
$temp = array_keys($modified['INFORMATION']);
$information =& $modified['INFORMATION'][$temp[0]];
$temp2 = array_keys($modified['NETWORKINFO']);
$netinfo =& $modified['NETWORKINFO'][$temp2[0]];


$_SESSION['phpaj']['core_source_ip'] = $netinfo['IP'];
if (empty($_SESSION['phpaj']['core_source_port'])) {
    $settings = $core->command("xml", "settings.xml");
    $_SESSION['phpaj']['core_source_port'] = $settings['PORT']['VALUES']['CDATA'];
}

if (isset($information['MAXUPLOADPOSITIONS'])) {
    $_SESSION['cache']['UPLOADS']['phpaj_MAXUPLOADPOSITIONS'] =
        $information['MAXUPLOADPOSITIONS'];
}

//Warnungen
if ($Servers->netstats['firewalled'] === 'true') {
    $template->alert("danger", $lang->System->warnung, $lang->System->firewall);
}
?>

<div class="row clearfix">
    <div class="col-xs-12 col-sm-12 col-md-8 col-lg-8">
        <!-- aktueller Server -->
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div class="panel panel-default" data-panel-collapsable="false" data-panel-fullscreen="false" data-panel-close="false">
                <div class="panel-heading bg-success"><i class="fa fa-server"></i> <?php echo $lang->Start->current_server; ?></div>
                <div class="panel-body">
                    <h3><?php echo $Servers->netstats['servername']; ?></h3>
                    <?php
                    //server welcome msg
                    if (!empty($Servers->netstats['welcome'])) {
                        echo $Servers->netstats['welcome'];
                    }
                    ?>
                </div>
            </div>
        </div>
        <!-- DOWNLOADS UND UPLOADS COUNTER -->
        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
            <div class="info-box infobox-type-1">
                <div class="icon bg-primary"><i class="material-icons">cloud_download</i></div>
                <div class="content">
                    <div class="text"><?php echo $lang->Start->active_downloads; ?></div>
                    <div class="number count-to" data-from="0" data-to="245" data-speed="1000" data-fresh-interval="20"><?php
                        $Downloadlist = new Downloads();
                        $counddown = $downloadids = $Downloadlist->ids("name", $subdir);
                        $Downloadlist->refresh_cache();
                        $subdircounter = 0;
                        //alle downloads zeigen
                        foreach (array_keys($Downloadlist->subdirs) as $subdir) {
                            $subdircounter++;
                            $downloadids = $Downloadlist->ids("", $subdir); //ids der downloads sortiert holen
                        }
                        echo count($downloadids);
                        ?></div>
                </div>
            </div>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
            <div class="info-box infobox-type-1">
                <div class="icon bg-warning"><i class="material-icons">cloud_upload</i></div>
                <div class="content">
                    <div class="text"><?php echo $lang->Start->active_uploads; ?></div>
                    <div class="number count-to" data-from="0" data-to="245" data-speed="1000" data-fresh-interval="20">
                        <?php 
                        	 
                        echo $Uploadlist->cache['phpaj_ul']; ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
            <div class="info-box infobox-type-1">
                <div class="icon bg-success"><i class="material-icons">folder_open</i></div>
                <?php


                if ($_ENV['GUI_SHOW_SHARE']) {
                    $Sharelist->refresh_cache(30);
                }

                if (!empty($_SESSION['phpaj']['share_LASTTIMESTAMP'])) {
                    $share_anzahl = 0;
                    $share_groesse = 0;
                    foreach (array_keys($Sharelist->cache['SHARES']['VALUES']['SHARE']) as $a) {
                        $share_anzahl++;
                        $share_groesse += $Sharelist->cache['SHARES']['VALUES']['SHARE'][$a]['SIZE'];
                    }
                    echo '<div class="content">
                    		<div class="text">' . number_format($share_anzahl) . ' ' . $lang->Start->share_dat . '</div>
                    		<div class="number">
                    			' . subs::sizeformat($share_groesse) . '
						 </div>';
                } else {
                }
                ?>
            </div>
        </div>
    </div>
    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
        <div class="info-box infobox-type-1">
            <div class="icon bg-primary"><i class="material-icons">star</i></div>
            <div class="content">
                <div class="text"><?php echo $lang->Start->credits; ?></div>
                <div class="number count-to" data-from="0" data-to="245" data-speed="1000" data-fresh-interval="20"><?php

                    if ($information['CREDITS'] <= 0) {
                        $creditcolor = " class='text-danger'";
                    } else {
                        $creditcolor = " class='ext-success'";
                    }
                    echo "<span" . $creditcolor . " >" . subs::sizeformat($information['CREDITS']) . "</span>";
                    ?>
                </div>
            </div>
        </div>
    </div>

    <?php
    $coreinfo = $Servers->core->getcoreversion();
    $coresubversions = explode(".", $_SESSION['cache']['STATUSBAR']['VERSION']);
    $info = $Servers->info();
    //AJ News
    $subs->appleJuiceNews(90, $coreinfo['VERSION']);


    ?>

</div>
<div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
    <!--INFOS EINHOLEN -->

    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <div class="panel panel-default" data-panel-collapsable="false" data-panel-fullscreen="false" data-panel-close="false">
            <div class="panel-heading bg-success"><i class="material-icons">public</i> <?php echo $lang->Start->core_info; ?></div>
            <div class="panel-body">
                <table class="table">
                    <tbody>
                    <tr>
                        <td><?php echo $lang->Start->server_time; ?></td>
                        <td nowrap>
                            <?php
                            echo $Servers->time();
                            echo "<a href=\"javascript: window.location.href='" . $_SERVER['PHP_SELF'] . "\"><i class=\"bi bi-arrow-clockwise\"></i></a>";
                            ?>
                        </td>
                    <tr>
                        <td nowrap>phpGUI Version</td>
                        <td><?php echo $gui->versions_update(PHP_GUI_VERSION); ?></td>
                    </tr>
                    <tr>
                        <td nowrap>Core Version</td>
                        <td><?php echo $coreinfo['VERSION']; ?></td>
                    </tr>
                    <tr>
                        <td nowrap><?php echo $lang->Start->op_system; ?></td>
                        <td nowrap><?php echo $icon_img->os_system[$coreinfo['SYSTEM']] . " " . $coreinfo['SYSTEM']; ?></td>
                    </tr>
                    <tr>
                        <td nowrap><?php echo $lang->Start->connected_since; ?></td>
                        <td nowrap>
                            <?php
                            $srv_timediff = $Servers->netstats['timeconnected'];
                            $srv_timediff = sprintf("%dh %dmin %ds", $srv_timediff / 3600, ($srv_timediff % 3600) / 60, $srv_timediff % 60);
                            echo $srv_timediff;
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td nowrap><?php echo $lang->Start->open_connections; ?></td>
                        <td nowrap><?php echo $info['OPENCONNECTIONS']; ?></td>
                    </tr>
                    <tr>
                        <td nowrap><?php echo $lang->Start->shared_users; ?></td>
                        <td nowrap><?php echo $Servers->netstats['users']; ?></td>
                    </tr>
                    <tr>
                        <td nowrap><?php echo $lang->Start->all_data; ?></td>
                        <td nowrap><?php echo number_format($Servers->netstats['filecount']) . " - " . subs::sizeformat($Servers->netstats['filesize']); ?></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="panel panel-default" data-panel-collapsable="false" data-panel-fullscreen="false" data-panel-close="false">
            <div class="panel-heading"><i class="material-icons">compare_arrows</i> <?php echo $lang->Start->network_info; ?></div>
            <div class="panel-body">
                <table class="table">
                    <tbody>
                    <tr>
                        <td nowrap><?php echo $lang->Start->bytes_in; ?></td>
                        <td nowrap><?php echo subs::sizeformat($information['SESSIONDOWNLOAD']); ?></td>
                    </tr>
                    <tr>
                        <td nowrap><?php echo $lang->Start->bytes_out; ?></td>
                        <td nowrap><?php echo subs::sizeformat($information['SESSIONUPLOAD']); ?></td>
                    </tr>
                    <tr>
                        <td nowrap><?php echo $lang->Start->download_speed; ?></td>
                        <td nowrap><?php echo subs::sizeformat($information['DOWNLOADSPEED']); ?></td>
                    </tr>
                    <tr>
                        <td nowrap><?php echo $lang->Start->upload_speed; ?></td>
                        <td nowrap><?php echo subs::sizeformat($information['UPLOADSPEED']); ?></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</div>	