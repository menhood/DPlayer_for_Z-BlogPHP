<?php
require dirname(__FILE__).'/function.php';
$dplayer = new DPlayer_class();
RegisterPlugin("DPlayer", "ActivePlugin_DPlayer");

function ActivePlugin_DPlayer() {
	Add_Filter_Plugin('Filter_Plugin_ViewPost_Template', 'DPlayer_Filter_Plugin_ViewPost_Template');
	Add_Filter_Plugin('Filter_Plugin_ViewList_Template', 'DPlayer_Filter_Plugin_ViewList_Template');
	Add_Filter_Plugin('Filter_Plugin_Zbp_MakeTemplatetags', 'DPlayer_Filter_Plugin_Zbp_MakeTemplatetags');
	Add_Filter_Plugin('Filter_Plugin_Edit_Response5','DPlayer_Edit_5');
	Add_Filter_Plugin('Filter_Plugin_Admin_Header','SCP_frame_src');
	Add_Filter_Plugin('Filter_Plugin_Other_Header','SCP_frame_src');
}

function DPlayer_Filter_Plugin_ViewPost_Template(&$template) {
    global $zbp;
    global $dplayer;
	$article = $template->GetTags('article');
	$article->Content = $dplayer->parseCallback($article->Content, $zbp->Config('DPlayer'));
}

function DPlayer_Filter_Plugin_ViewList_Template(&$template) {
    global $zbp;
    global $dplayer;
	$config = $zbp->Config('DPlayer');
	if ($config->parselist) {
	    $articles = $template->GetTags('articles');
	    foreach($articles as $article) $article->Intro = $dplayer->parseCallback($article->Intro, $config);
	}
}

function DPlayer_Filter_Plugin_Zbp_MakeTemplatetags() {
    global $zbp;
    if ($zbp->Config('DPlayer')->flv) $zbp->footer .= '<script type="text/javascript" src="'.$zbp->host.'zb_users/plugin/DPlayer/plugin/flv.min.js"></script>'."\n";
    if ($zbp->Config('DPlayer')->hls) $zbp->footer .= '<script type="text/javascript" src="'.$zbp->host.'zb_users/plugin/DPlayer/plugin/hls.min.js"></script>'."\n";
    $zbp->footer .=
    '<script type="text/javascript" src="'.$zbp->host.'zb_users/plugin/DPlayer/DPlayer.min.js?v=1.1.3"></script>'."\n".
    '<script>function dpajaxload(){if(0<$(\'#dpajax\').length){var DPlayerOptions=[];eval($(\'#dpajax\').text());for(i=0;i<DPlayerOptions.length;i++)new DPlayer({element:document.getElementById(\'dp\'+DPlayerOptions[i].id),autoplay:DPlayerOptions[i].autoplay,theme:DPlayerOptions[i].theme,loop:DPlayerOptions[i].loop,lang:DPlayerOptions[i].lang,screenshot:DPlayerOptions[i].screenshot,hotkey:DPlayerOptions[i].hotkey,preload:DPlayerOptions[i].preload,video:DPlayerOptions[i].video,danmaku:DPlayerOptions[i].danmaku})}}dpajaxload();</script>';
}
function SCP_frame_src(){
   echo '<meta http-equiv="Content-Security-Policy" content="child-src \'self\' https://api.menhood.wang;sandbox allow-forms allow-same-origin">'. "\r\n";
}
function DPlayer_Edit_5(){
    echo <<<EOF
<input type="button" id="ShowButton_2" name="ShowButton_2" value="显示DPlayer插入参数" class="button">
<style>
#dplayerinput {
    display:none;
    margin:5px;
    width:576px;
    height:100%;
}
#iframechild {
    border-radius:10px;
}
h4 {
    display:inline
}
#shortcode {
    display:none;
    margin:2px;
    width:570px;
    height:auto
}
#shortcodecopy {
    maigin:2px;
}
#copysuccess {
    display:none;
}
</style>
<div id="" style="display: flex;">
    <div id="dplayerinput">
        <br>
         <h4>视频地址格式</h4>：
        <br>https://ddns.menhood.wang:2233/video/01.mp4
        <br>
         <h4>视频列表格式</h4>：
        <br>https://ddns.menhood.wang:2233/video/
        <br>
         <h4>* 图片地址：</h4>

        <input type="text" oninput="dplayerurls()" value="" width=100% id="dplayerpic" placeholder="https://ddns.menhood.wang:2233/img.jpg">
        <br>
         <h4>* 视频地址：</h4>

        <input type="text" oninput="dplayerurls()" value="" width=100% id="dplayerurl" placeholder="https://ddns.menhood.wang:2233/video/01.mp4">
        <br>
         <h4>* 视频后缀：</h4>

        <input type="text" value="mp4" width=100% id="suffix" placeholder="mp4">
        <br>
         <h4>* 视频集数：</h4>

        <input type="text" value="" width=100% id="listmax" placeholder="12">
        <br>
         <h4>是否开启弹幕:</h4>

        <input type="checkbox" id="danmucheck">
        <br>
         <h4>是否开启自动播放（对列表无效）:</h4>

        <input type="checkbox" id="autoplaycheck">
        <br>
         <h4>是否开启预加载（对列表无效）:</h4>

        <input type="checkbox" id="preloadcheck">
        <script>
        $(function () {
            $("#ShowButton_2").click(function () {
                if ($("#dplayerinput").css("display") == 'none') {
                    $("#dplayerinput").slideDown();

                    $("#ShowButton_2").val("隐藏DPlayer插入参数");
                } else {
                    $("#dplayerinput").slideUp();
                    $("#ShowButton_2").val("显示DPlayer插入参数");
                }
            });

            $("#toggletextarea").click(function () {
                if ($("#shortcode").css("display") == 'none') {
                    $("#shortcode").slideDown();

                    $("#toggletextarea").val("隐藏代码区");
                } else {
                    $("#shortcode").slideUp();
                    $("#toggletextarea").val("显示代码区");
                }
            });
            
            $("#clcodetext").click(function () {
                $("#shortcode").val("");
            });
            
            $("#insertcss").click(
            function insertcss(){
            var listcss="<style>.dplist{margin-top:20px} .dplist li{height:30px;width:auto;float:left;padding:5px;border:1px solid}</style>";
            editor_api.editor.content.obj.execCommand('inserthtml', listcss);
            document.getElementById("shortcode").value = document.getElementById("shortcode").value+listcss;
            });

            $("#dplisgenerate").click(function () {
                var listurl = $("#dplayerurl").val();
                var listpic = $("#dplayerpic").val();
                var suffix = "." + $("#suffix").val();
                var max = $("#listmax").val();
                var urlarr = '';
                for (i = 0; i < max; i++) {
                    let pn = i + 1
                    if (i < 9) {
                        urlarr = urlarr + '<a href="javascript:void(0)" onclick="switchDPlayer(\'' + listurl + '0' + pn +
                            suffix + '\')" ><li id="p' + i + '"><span>P' + pn + '</span></li></a>'
                    } else {
                        urlarr = urlarr + '<a href="javascript:void(0)" onclick="switchDPlayer(\'' + listurl + pn +
                            suffix + '\')" ><li id="p' + i + '"><span>P' + pn + '</span></li></a>'
                    }
                }
                //检查弹幕checkbox是否选中
                if (document.getElementById("danmucheck").checked) {
                    var listcode =
                        "<link href=\'https://cdnjs.loli.net/ajax/libs/dplayer/1.25.0/DPlayer.min.css\' rel=\'stylesheet\'><div id=\'dplayer\'></div><ul class='dplist'>" +
                        urlarr +
                        "</ul><script src=\'https://cdnjs.loli.net/ajax/libs/dplayer/1.25.0/DPlayer.min.js\'><\/script><script src=\'https://api.menhood.wang/getcip/getcipv2.php\'><\/script><script src=\'https://cdnjs.loli.net/ajax/libs/hls.js/0.9.1/hls.min.js\'><\/script><script src=\'https://cdnjs.loli.net/ajax/libs/flv.js/1.4.2/flv.min.js\'><\/script><script src=\'https://cdnjs.loli.net/ajax/libs/dashjs/2.9.2/dash.all.min.js\'><\/script><script src=\'https://cdnjs.loli.net/ajax/libs/blueimp-md5/2.10.0/js/md5.min.js\'><\/script><script>var url='" +
                        listurl + "01" + suffix +
                        "';const dp = new DPlayer({container: document.getElementById(\'dplayer\'),video: {url: url},danmaku: {id: md5(url),api: \'https://api.prprpr.me/dplayer/\',user: cip}});function switchDPlayer(url){dp.switchVideo({url: url}, {id: md5(url),api: 'https://api.prprpr.me/dplayer/',user: cip});dp.toggle();}<\/script>";
                } else {
                    var listcode =
                        "<link href=\'https://cdnjs.loli.net/ajax/libs/dplayer/1.25.0/DPlayer.min.css\' rel=\'stylesheet\'><div id=\'dplayer\'></div><ul class='dplist'>" +
                        urlarr +
                        "</ul><script src=\'https://cdnjs.loli.net/ajax/libs/dplayer/1.25.0/DPlayer.min.js\'><\/script><script src=\'https://api.menhood.wang/getcip/getcipv2.php\'><\/script><script src=\'https://cdnjs.loli.net/ajax/libs/hls.js/0.9.1/hls.min.js\'><\/script><script src=\'https://cdnjs.loli.net/ajax/libs/flv.js/1.4.2/flv.min.js\'><\/script><script src=\'https://cdnjs.loli.net/ajax/libs/dashjs/2.9.2/dash.all.min.js\'><\/script><script src=\'https://cdnjs.loli.net/ajax/libs/blueimp-md5/2.10.0/js/md5.min.js\'><\/script><script>var url='" +
                        listurl + "01" + suffix +
                        "';const dp = new DPlayer({container: document.getElementById(\'dplayer\'),video: {url: url}});function switchDPlayer(url){dp.switchVideo({url: url});dp.toggle();}<\/script>";
                }

                editor_api.editor.content.obj.execCommand('inserthtml', listcode);
                document.getElementById("shortcode").value = listcode;
            });
        });


        var dplayerurl;
        var dplayerpic;
        var danmucheack;
        var dpcode;

        function dplayerurls() {
            dplayerurl = document.getElementById("dplayerurl").value;
            dplayerpic = document.getElementById("dplayerpic").value;

        } //文本框参数处理

        function dplayerinsert() {
            if (document.getElementById("danmucheck").checked) {
                danmucheack = "true";
            } else {
                danmucheack = "false";
            } //检查弹幕checkbox是否选中
            if (document.getElementById("autoplaycheck").checked) {
                autoplaycheck = "true";
            } else {
                autoplaycheck = "false";
            }
            if (document.getElementById("preloadcheck").checked) {
                preloadcheck = "true";
            } else {
                preloadcheck = "false";
            }
            dpcode = '[dplayer url="' + dplayerurl + '" pic="' + dplayerpic + '"preload=' + '"' + preloadcheck + '"' +
                '"autoplay=' + '"' + autoplaycheck + '"' + 'danmu=' + '"' + danmucheack + '"' + ' / ]';
            if (editor_api.editor.content.obj.execCommand) {
                editor_api.editor.content.obj.execCommand('inserthtml', dpcode);
                document.getElementById("shortcode").value = dpcode;
            } else {
                document.getElementById("shortcode").value = dpcode;
                document.getElementById("shortcode").style.display = "inline";
                document.getElementById("shortcodecopy").style.display = "inline";
                document.getElementById("dpclear").style.display = "none";
            }
        } //插入单个视频代码

        function dpc() {
            editor_api.editor.content.obj.setContent('', false);
        }

        function scd() {
            document.getElementById("shortcode").style.display = "inline";
        }

        function disinfo() {
            document.getElementById("copysuccess").style.display = "none";
        }

        function copycode() {
            document.getElementById("shortcode").select(); // 选择对象
            document.execCommand("Copy"); // 执行浏览器复制命令
            document.getElementById("copysuccess").style.display = "inline";

            setTimeout("disinfo()", 3000);
        } //复制代码
        
        
        </script>
        <br>
        <br>
        <input type="button" onclick="dplayerinsert()" class="button" value="生成代码">
        <input type="button" id="dplisgenerate" name="dplisgenerate" value="生成列表" class="button">
        <input type="button" id="dpclear" onclick="dpc()" class="button" value="清除编辑器">
        <input type="button" id="toggletextarea" class="button" value="显示代码区">
        <hr>
        <textarea type="text" id="shortcode"></textarea>
        <input type="button" id="shortcodecopy" onclick="copycode()" class="button" value="复制代码">
        <input type="button" id="insertcss"  class="button" value="生成样式">
        <input type="button" id="clcodetext"  class="button" value="清除代码区">
        <br> <span id="copysuccess">复制成功，请将短代码粘贴到编辑器内！<span>
    </div>
 </div>    
EOF;
}

function InstallPlugin_DPlayer() {
	global $zbp,$obj,$bucket;
    if (!$zbp->Config('DPlayer')->HasKey('theme')) {
        $zbp->Config('DPlayer')->siteurl = $zbp->host;
        $zbp->Config('DPlayer')->dmserver = '//api.prprpr.me/dplayer/';
        $zbp->Config('DPlayer')->useue = 1;
		$zbp->Config('DPlayer')->hidermmenu = 0;
		$zbp->Config('DPlayer')->hotkey = 1;
		$zbp->Config('DPlayer')->danmaku = 1;
		$zbp->Config('DPlayer')->screenshot = 0;
		$zbp->Config('DPlayer')->loop = 0;
		$zbp->Config('DPlayer')->autoplay = 0;
		$zbp->Config('DPlayer')->preload = 0;
		$zbp->Config('DPlayer')->lang = 1;
		$zbp->Config('DPlayer')->maximum = 1000;
		$zbp->Config('DPlayer')->flv = 1;
		$zbp->Config('DPlayer')->hls = 0;
		$zbp->Config('DPlayer')->theme = '#FADFA3';
		$zbp->Config('DPlayer')->parselist = 0;
        $zbp->SaveConfig('DPlayer');
    }
}

function UninstallPlugin_DPlayer() {
	global $zbp;
	if ($zbp->Config('DPlayer')->hidermmenu == '1') {
	    $dpjs = file_get_contents(dirname(__FILE__)."/DPlayer.min.js");
		$dpjs = str_replace('<!--<div class="dplayer-menu">', '<div class="dplayer-menu">', $dpjs);
		$dpjs = str_replace('About DPlayer")+"</a></span></div>\n            </div>-->\n', 'About DPlayer")+"</a></span></div>\n            </div>\n', $dpjs);
		file_put_contents(dirname(__FILE__)."/DPlayer.min.js", $dpjs);
	}
	$zbp->DelConfig('DPlayer');
}
