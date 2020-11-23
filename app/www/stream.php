<?php
error_reporting(E_ALL);
set_time_limit(0);
include("config.php");
function closed()
{
    global $user_activity_id;
    if ($user_activity_id != 0) {
        $active = Activity::find($user_activity_id);
        $active->date_end = date('Y-m-d H:i:s');
        $active->save();
    }
    fastcgi_finish_request();
    exit;
}
$user_activity_id = 0;
$user_ip = $_SERVER['REMOTE_ADDR'];
header("Access-Control-Allow-Origin: *");
register_shutdown_function('closed');
header("application/octet-stream");
header('Content-Description: File Transfer');
if (ob_get_length() > 0) {
    ob_end_flush();
}
if (isset($_GET['username']) && isset($_GET['password']) && isset($_GET['stream'])) {
    $user_agent = (empty($_SERVER['HTTP_USER_AGENT'])) ? "0" : trim($_SERVER['HTTP_USER_AGENT']);
    $username = $_GET['username'];
    $password = $_GET['password'];
    $stream_id = intval($_GET['stream']);
    if (!BlockedUseragent::where('name', '=', $user_agent)->first())
        if (!BlockedIp::where('ip', '=', $_SERVER['REMOTE_ADDR'])->first()) {
            if ($user = User::where('username', '=', $username)->where('password', '=', $password)->where('active', '=', 1)->first()) {
            } else {
                $log  = "Worning --> Ip: [" . $_SERVER['REMOTE_ADDR'] . '] - ' . date("d-m-Y H:i:s") .
                    " - Attempt " . ('Failed Login -') .
                    " User: " . $username .
                    " Pass: " . $password .
                    " " . PHP_EOL;
                file_put_contents('/opt/streamtool/app/wws/log/streamtool-loginfail' . '.log', $log, FILE_APPEND);
                sleep(10);
            }

            if ($user = User::where('username', '=', $username)->where('password', '=', $password)->where('active', '=', 1)->first()) {
                if ($user->exp_date == "0000-00-00" || $user->exp_date > date('Y-m-d H:i:s')) {
                    $user_id = $user->id;
                    $user_max_connections = $user->max_connections;
                    $user_expire_date = $user->exp_date;
                    $user_activity = $user->activity()->where('date_end', '=', '0000-00-00')->get();
                    $active_cons = $user_activity->count();
                    if ($user_max_connections != 1 && $active_cons >= $user_max_connections) {
                        $maxconntactionactivity = Activity::where("user_id", "=", $user_id)->where("user_ip", "=", $user_ip)->where("date_end", "=", '0000-00-00')->first();
                        if ($maxconntactionactivity != null) {
                            if ($maxconntactionactivity->count() > 0) {
                                --$active_cons;
                            }
                        }
                    }
                    if ($user_max_connections == 0 || $active_cons < $user_max_connections) {
                        if ($stream = Stream::find($_GET['stream'])) {
                            if ($user_activity_id != 0) {
                                $active = Activity::find($user_activity_id);
                            } else {
                                $active = new Activity();
                            }
                            $active->user_id = $user->id;
                            $active->stream_id = $stream->id;
                            $active->user_agent = $user_agent;
                            $active->user_ip = $user_ip;
                            $active->pid = getmypid();
                            $active->bandwidth = 0;
                            $active->date_start = date('Y-m-d H:i:s');
                            $active->save();
                            $user_activity_id = $active->id;
                            $user->lastconnected_ip = $_SERVER['REMOTE_ADDR'];
                            $user->last_stream = $stream->id;
                            $user->useragent = $user_agent;
                            $user->save();
                            $setting = Setting::first();
                            if ($stream->checker == 2) {
                                $url = $stream->streamurl2;
                            } else if ($stream->checker == 3) {
                                $url = $stream->streamurl3;
                            } else {
                                $url = $stream->streamurl;
                            }
                            $folder = "/opt/streamtool/app/www/" . $setting->hlsfolder . '/';
                            $files = "";
                            $file = "/opt/streamtool/app/www/" . $setting->hlsfolder . '/' . $stream->id . '_.m3u8';
                            header('Content-Disposition: attachment; filename="' . basename($file) . '"');
                            header('Content-Length: ' . filesize($file));
                            header('Expires: 0');
                            header('Cache-Control: must-revalidate');
                            header('Pragma: public');
                            if (file_exists($file) && preg_match_all("/(.*?).ts/", file_get_contents($file), $data)) {
                                readfile($file);
                                exit();
                            }
                        }
                    }
                }
            }
        }
}
