<?php

/**
 * Created by NeySlim 2020
 */
include('config.php');
logincheck();

//Create settings if not exists
$settings = Setting::first();
if (is_null($settings)) {
    $settings = new Setting;
    $settings->webip = $_SERVER['SERVER_ADDR'];
    $settings->webport = 8000;
    $settings->save();
}
$all = Stream::all()->count();
$online = Stream::where('running', '=', 1)->where('status', '!=', 2)->count();
$offline = Stream::where('running', '=', 0)->count();
$space_pr = 0;
$space_free = round((disk_free_space('/')) / 1048576, 1);
$space_total = round((disk_total_space('/')) / 1048576, 1);
$space_used = $space_total - $space_free;
$space_pr = (int)(100 * ($space_used / $space_total));
$cpu_usage = "";
$cpu_total = "";

$loads = sys_getloadavg();
$core_nums = trim(shell_exec("grep -P '^processor' /proc/cpuinfo|wc -l"));
$cpu_pr = round($loads[0] / ($core_nums + 1) * 100, 2);
$free = shell_exec('free');
$free = (string)trim($free);
$free_arr = explode("\n", $free);
$mem = explode(" ", $free_arr[1]);
$mem = array_filter($mem);
$mem = array_merge($mem);
$mem_usage = $mem[2];
$mem_total = $mem[1];
$mem_pr = $mem[2] / $mem[1] * 100;

$gpupresent = FALSE;
if (file_exists('/usr/bin/nvidia-smi')) {
    shell_exec('/opt/streamtool/app/bin/nvsmi-parser.sh > /tmp/smi.csv');
    $gpupresent = TRUE;
    $gpuinfos = csv_to_array('/tmp/smi.csv');    
}


$space = [];
$space['pr'] = $space_pr;
$space['count'] = $space_used;
$space['total'] = $space_total;

$cpu = [];
$cpu['pr'] = $cpu_pr;
$cpu['count'] = $cpu_usage;
$cpu['total'] = $cpu_total;

$mem = [];
$mem['pr'] = $mem_pr;
$mem['count'] = $mem_usage;
$mem['total'] = $mem_total;


echo $template->view()
    ->make('dashboard')
    ->with('all', $all)
    ->with('online', $online)
    ->with('offline', $offline)
    ->with('space', $space)
    ->with('cpu', $cpu)
    ->with('mem', $mem)
    ->with('gpupresent', $gpupresent)
    ->with('gpuinfos', $gpuinfos)
    ->whih('setting', $settings)
    ->render();
