<?php
include('config.php');
logincheck();
$message = [];
$setting = Setting::first();

if (isset($_POST['patchnv'])) {
   
    $result = patchnv();
    if ($result != null) {
        $message['type'] = "success";
        $message['message'] = "Nvidia Patch successful: " . $result;
    } else {
        $message['type'] = "error";
        $message['message'] = "Nvidia Patch application failed";
    }
}



if (isset($_POST['submit'])) {

    $port = false;
    $setting->ffmpeg_path = $_POST['ffmpeg_path'];
    $setting->ffprobe_path = $_POST['ffprobe_path'];

    if ($setting->webport != $_POST['webport']) {

        $setting->webport = $_POST['webport'];
        if (is_null($_POST['webport'])) {
            $setting->webport = 8000;
        }
        generateNginxConfPort($_POST['webport']);
        $port = true;
    }


    $setting->webip = $_POST['webip'];
    $setting->server_name = $_POST['server_name'];
    $setting->logourl = $_POST['logourl'];
    $setting->hlsfolder = $_POST['hlsfolder'];
    mkdir($_POST['hlsfolder'], 0777);

    $setting->user_agent = $_POST['user_agent'];
    $message['type'] = "success";
    $message['message'] = "Setting saved";
    $setting->save();

    if ($port) {
        shell_exec('/usr/bin/sudo /opt/streamtool/app/nginx/sbin/nginx_streamtool -s reload');
        redirect("settings.php", 1000);
    } else {
        redirect("settings.php", 1000);
    }
}
echo $template->view()->make('manage_settings')
    ->with('setting', $setting)
    ->with('message', $message)
    ->render();
