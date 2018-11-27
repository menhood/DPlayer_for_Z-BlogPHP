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
        table, td, th, tr, .api, tr.color1, tr.color2, tr.color3, tr.color4 {
            background: rgba(0, 0, 0, 0)!important;
            border: 2px solid rgba(100, 100, 100, 0.2)!important;
        }
        .al-toggle-button {
            appearance:none;
            -webkit-appearance:none;
            position:relative;
            width:40px;
            height:22px;
            background:#dfdfdf;
            border-radius:16px;
            border:1px solid #dfdfdf;
            outline:0;
            box-sizing:border-box;
            bottom: -5px;
        }
        .al-toggle-button:checked {
            border-color:#04be02;
            background-color:#04be02
        }
        .al-toggle-button:before, .al-toggle-button:after {
            content:" ";
            position:absolute;
            top:0;
            left:0;
            height:20px;
            border-radius:15px;
            transition:transform .3s;
            transition:-webkit-transform .3s;
            transition:transform .3s, -webkit-transform .3s;
            -webkit-transition:-webkit-transform .3s
        }
        .al-toggle-button:before {
            width:20px;
            background-color:#fdfdfd
        }
        .al-toggle-button:after {
            width:20px;
            background-color:white;
            box-shadow:0 1px 3px rgba(0, 0, 0, 0.4)
        }
        .al-toggle-button:checked:before {
            transform:scale(0);
            -webkit-transform:scale(0)
        }
        .al-toggle-button:checked:after {
            transform:translateX(20px);
            -webkit-transform:translateX(20px)
        }
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
            display:;
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
        <div id="dplayerinput" style="border-radius: 3px; padding: 10px; ">
            <div class="dplayerinputHeader">
                <div id="dplayerinput2">
                    <table width="90%" style='padding:0px;margin:0px;' cellspacing='0' cellpadding='0' class="tableBorder">
                        <tr>
                            <th width='20%'>
                                <p align="center">参数</p>
                            </th>
                            <th width='70%'>
                                <p align="center">内容</p>
                            </th>
                        </tr>
                        <tr>
                            <td><b><p align="center">图片地址（不支持列表）</p></b>

                            </td>
                            <td>
                                <p align="left">
                                    <input type="text" oninput="dplayerurls()" value="" size=100% id="dplayerpic" placeholder="https://ddns.menhood.wang:2233/img.jpg">
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <td><b><p align="center">视频/列表地址</p></b>
                            </td>
                            <td>
                                <p align="left">
                                    <input type="text" oninput="dplayerurls()" value="" size=100% id="dplayerurl" placeholder="https://ddns.menhood.wang:2233/video/01.mp4 或 https://ddns.menhood.wang:2233/video/">
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <td><b><p align="center">后缀名/集数</p></b>
                            </td>
                            <td>
                                <p align="left">
                                    * 后缀名：
                                    <input type="text" value="mp4" width=40% id="suffix" placeholder="mp4">
                                    * 集数：
                                    <input type="text" value="" width=30% id="listmax" placeholder="12">
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <td><b><p align="center">附加/默认设置</p></b>

                            </td>
                            <td>
                                <p align="left"></p>
                                <p align="left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;弹幕：
                                    <input type="checkbox" class="al-toggle-button" id="danmucheck">
                                </p>
                                <p align="left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;预加载（对列表无效）：
                                    <input type="checkbox" id="preloadcheck" class="al-toggle-button">
                                </p>
                                <p align="left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;自动播放（对列表无效）：
                                    <input type="checkbox" id="autoplaycheck" class="al-toggle-button">
                                </p>
                                <p align="left">---------------------------------------------------------</p>
                                <p align="left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;代码区：
                                    <textarea type="text" id="shortcode" width="100%" height="100px"></textarea>
                                </p>
                                <script>
                                
                                </script>
                                <p align="left">---------------------------------------------------------</p>
                                <p align="left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&diams;&nbsp;&nbsp;使用注意事项：</p>
                                <p align="left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;UE编辑器会出现插入转义，列表模式可复制代码区代码直接在html模式粘贴代码；</p>
                                <p align="left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;默认样式可在代码区直接更改，如更改代码生成样式进入插件目录找到include.php查找修改即可</p>
                                <p align="left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&diams;&nbsp;&nbsp;插入列表顺序为：</p>
                                <p align="left">
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;填写地址、后缀、集数，点击生成列表代码按钮，点击生成列表样式代码按钮，点击复制代码区域代码按钮</p>
                                <p align="left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;在html模式下粘贴代码然后发布</p>
                                <p align="left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&loz;&nbsp;&nbsp;<a href="http://diygod.me" target="_blank">关于作者</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="https://github.com/MoePlayer/DPlayer_for_Z-BlogPHP/issues" target="_blank">意见反馈</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="https://www.anotherhome.net/2648"
                                    target="_blank">关于 DPlayer 播放器</a>

                                </p>
                                
                            </td>
                        </tr>
                        
                        <tr>
                            <td><b><p align="center">操作</p></b>
                            </td>
                            <td>
                            <p align="left">
                            <input type="button" onclick="dplayerinsert()" class="button" value="生成视频短代码">
                            <input type="button" id="dplisgenerate" name="dplisgenerate" value="生成列表代码" class="button">
                            <input type="button" id="insertcss" class="button" value="生成列表样式代码">
                            </p>
                            <p align="left">
                            <input type="button" id="dpclear" onclick="dpc()" class="button" value="清除编辑器内容">
                            <input type="button" id="clcodetext" class="button" value="清除代码区域">
                            <input type="button" id="shortcodecopy" onclick="copycode()" class="button" value="复制代码区域代码">
                            </p> <span id="copysuccess">复制成功，请将短代码粘贴到编辑器内！<span>
                            </td>
                        </tr>
                        
                    </table>
                    
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
            var listcss="<style>.dplist{margin-top:20px} .dplist li{height:30px;width:auto;float:left;padding:5px;border:1px solid} .dpactive{background-color:#00a1d6;color:#fff}</style>";
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
                            suffix + '\','+i+')" ><li id="p' + i + '" name="dplistli" ><span>P' + pn + '</span></li></a>'
                    } else {
                        urlarr = urlarr + '<a href="javascript:void(0)" onclick="switchDPlayer(\'' + listurl + pn +
                            suffix + '\','+i+')" ><li id="p' + i + '" name="dplistli" ><span>P' + pn + '</span></li></a>'
                    }
                }
                //检查弹幕checkbox是否选中
                if (document.getElementById("danmucheck").checked) {
                    var listcode =
                        "<link href=\'https://cdnjs.loli.net/ajax/libs/dplayer/1.25.0/DPlayer.min.css\' rel=\'stylesheet\'><div id=\'dplayer\'></div><ul class='dplist'>" +
                        urlarr +
                        "</ul><script src=\'https://cdnjs.loli.net/ajax/libs/dplayer/1.25.0/DPlayer.min.js\'><\/script><script src=\'https://api.menhood.wang/getcip/getcipv2.php\'><\/script><script src=\'https://cdnjs.loli.net/ajax/libs/hls.js/0.9.1/hls.min.js\'><\/script><script src=\'https://cdnjs.loli.net/ajax/libs/flv.js/1.4.2/flv.min.js\'><\/script><script src=\'https://cdnjs.loli.net/ajax/libs/dashjs/2.9.2/dash.all.min.js\'><\/script><script src=\'https://cdnjs.loli.net/ajax/libs/blueimp-md5/2.10.0/js/md5.min.js\'><\/script><script>var url='" +
                        listurl + "01" + suffix +
                        "';const dp = new DPlayer({container: document.getElementById(\'dplayer\'),video: {url: url},danmaku: {id: md5(url),api: \'https://api.prprpr.me/dplayer/\',user: cip}});function switchDPlayer(url,pn){dp.switchVideo({url: url}, {id: md5(url),api: 'https://api.prprpr.me/dplayer/',user: cip});dp.toggle();var li=document.getElementsByName(\'dplistli\');for(var i=0;i<li.length;i++){li[i].className=\'wbf\';};document.getElementById('p'+pn).className=\'dpactive\';}<\/script>";
                } else {
                    var listcode =
                        "<link href=\'https://cdnjs.loli.net/ajax/libs/dplayer/1.25.0/DPlayer.min.css\' rel=\'stylesheet\'><div id=\'dplayer\'></div><ul class='dplist'>" +
                        urlarr +
                        "</ul><script src=\'https://cdnjs.loli.net/ajax/libs/dplayer/1.25.0/DPlayer.min.js\'><\/script><script src=\'https://api.menhood.wang/getcip/getcipv2.php\'><\/script><script src=\'https://cdnjs.loli.net/ajax/libs/hls.js/0.9.1/hls.min.js\'><\/script><script src=\'https://cdnjs.loli.net/ajax/libs/flv.js/1.4.2/flv.min.js\'><\/script><script src=\'https://cdnjs.loli.net/ajax/libs/dashjs/2.9.2/dash.all.min.js\'><\/script><script src=\'https://cdnjs.loli.net/ajax/libs/blueimp-md5/2.10.0/js/md5.min.js\'><\/script><script>var url='" +
                        listurl + "01" + suffix +
                        "';const dp = new DPlayer({container: document.getElementById(\'dplayer\'),video: {url: url}});function switchDPlayer(url,pn){dp.switchVideo({url: url}, {id: md5(url),api: 'https://api.prprpr.me/dplayer/',user: cip});dp.toggle();var li=document.getElementsByName(\'dplistli\');for(var i=0;i<li.length;i++){li[i].className=\'wbf\';};document.getElementById('p'+pn).className=\'dpactive\';}<\/script>";
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
                </div>
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
