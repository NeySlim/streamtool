<?php
if (isset($_SERVER['SERVER_ADDR'])) {
    if ($_SERVER['REMOTE_ADDR'] != $_SERVER['SERVER_ADDR']) {
        die('access is not permitted');
    }
}
include('config.php');

while (TRUE) {
    $setting = Setting::first();
    foreach (Stream::where('pid', '!=', 0)->where('running', '=', 1)->get() as $stream) {
        if (checkPid($stream->pid)) {
            $statFile = '/opt/streamtool/app/www/' . $setting->hlsfolder . '/' . $stream->id . '_.stats';
            $input = shell_exec('/usr/bin/tail -12 ' . $statFile);
            $f = @fopen($statFile, "r+");
            if ($f !== false) {
                ftruncate($f, 0);
                fclose($f);
            }
            $data = explode("
", $input);
            foreach ($data as $row) {
                list($key, $value) = explode('=', $row);
                $output[$key] = $value;
            }
            $stream->fps = !empty($output['fps']) ? $output['fps'] : $stream->fps;
            $stream->duration = !empty(strtotime($output['out_time'])) ? strtotime($output['out_time']) : $stream->duration;
        }
        $stream->save();
        
    }
    sleep(1);
}
