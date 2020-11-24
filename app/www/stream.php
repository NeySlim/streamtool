<?php
error_reporting(E_ALL);
set_time_limit(0);
include("config.php");
$user_activity_id = 0;
$user_ip = $_SERVER['REMOTE_ADDR'];

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
                file_put_contents('/opt/streamtool/app/logs/auth-failed.log', $log , FILE_APPEND);
                sleep(10);
            }

            if ($user = User::where('username', '=', $username)->where('password', '=', $password)->where('active', '=', 1)->first()) {
                if ($user->exp_date == "0000-00-00" || $user->exp_date > date('Y-m-d H:i:s')) {
                    $user_id = $user->id;
                    $user_max_connections = $user->max_connections;
                    $user_expire_date = $user->exp_date;
                    //$user_activity = $user->activity()->where('date_end', '=', '0000-00-00')->get();
                    $active_cons = 1;
                    if ($user_max_connections != 1 && $active_cons >= $user_max_connections) {
                        $maxconntactionactivity = Activity::where("user_id", "=", $user_id)->where("user_ip", "=", $user_ip)->where("date_end", "=", '0000-00-00')->first();
                        //if ($maxconntactionactivity != null) {
                        //    if ($maxconntactionactivity->count() > 0) {
                        //        --$active_cons;
                        //    }
                        }
                    }
                    if ($user_max_connections == 0 || $active_cons < $user_max_connections) {
                        if ($stream = Stream::find($_GET['stream'])) {
                            $setting = Setting::first();
                            if ($stream->checker == 2) {
                                $url = $stream->streamurl2;
                            } else if ($stream->checker == 3) {
                                $url = $stream->streamurl3;
                            } else {
                                $url = $stream->streamurl;
                            }
                            header("location: /hls/" . $stream->id ."_.m3u8");
                            exit();
                        }
                    }
                }
            }
        }

