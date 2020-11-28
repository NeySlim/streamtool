<?php
error_reporting(E_ERROR | E_PARSE);
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
            !empty($output['fps']) ? $stream->fps = $output['fps'] : '';
            !empty($output['out_time_ms']) ? $stream->duration = round($output['out_time_ms'] / 1000000) : '';
        }
        $stream->save();
        $statFile = null;
        $input = null;
        $data = null;
        $output = null;
        $f = null;
    }
    sleep(2);
}
