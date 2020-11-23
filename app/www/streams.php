<?php
include('config.php');
logincheck();

$setting = Setting::first();

$message = [];
$title = "Manage Streams";

if (isset($_GET['start'])) {
    start_stream($_GET['start']);
    $message = ['type' => 'success', 'message' => 'Stream started'];
} else if (isset($_GET['stop'])) {
    stop_stream($_GET['stop']);
    $message = ['type' => 'success', 'message' => 'Stream stopped'];
}
if (isset($_GET['restart'])) {
    stop_stream($_GET['restart']);
    usleep(100000);
    start_stream($_GET['restart']);
    $message = ['type' => 'success', 'message' => 'Stream restarted succesfully'];
} 


if (isset($_POST['start_cron'])) {
    $setting->enableCheck = "1";
    $setting->save();
    exec(sprintf("%s > %s 2>&1 & ", "/opt/streamtool/app/php/bin/php /opt/streamtool/app/www/cron.php" , "/tmp/streamtool-watcher.log"));
    $message = ['type' => 'success', 'message' => "Stream process watcher started"];
}
if (isset($_POST['stop_cron'])) {
    $setting->enableCheck = "0";
    $setting->save();
    sleep(1);
    $message = ['type' => 'error', 'message' => "Stream process watcher stopped"];
}

if (isset($_GET['delete'])) {
    $stream = Stream::find($_GET['delete'])->delete();
    $message = ['type' => 'success', 'message' => 'Stream deleted'];
}

if (isset($_POST['mass_delete']) && isset($_POST['mselect'])) {
    foreach ($_POST['mselect'] as $streamids) {
        Stream::find($streamids)->delete();
    }
    $message = ['type' => 'success', 'message' => 'Streams deleted'];
}

if (isset($_POST['mass_start']) && isset($_POST['mselect'])) {
    foreach ($_POST['mselect'] as $streamids) {
        start_stream($streamids);
    }

    $message = ['type' => 'success', 'message' => 'Streams started'];
}

if (isset($_POST['mass_stop']) && isset($_POST['mselect'])) {
    foreach ($_POST['mselect'] as $streamids) {
        stop_stream($streamids);
    }
    $message = ['type' => 'success', 'message' => 'Streams stopped'];
}

if (isset($_GET['running']) && $_GET['running'] == 1) {
    $title = "Running Streams";
    $stream = Stream::where('status', '=', 1)->get();

} else if (isset($_GET['running']) && $_GET['running'] == 2) {
    $title = "Stopped Streams";
    $stream = Stream::where('status', '=', 2)->get();
} else {
    $stream = Stream::all();
}




$cronStatus=shell_exec('ps faux | grep "/[o]pt/streamtool/app/php/bin/php /opt/streamtool/app/www/cron.php" > /dev/null; echo $?') == 0 ? 1 : 0;


echo $template->view()->make('streams')
    ->with('streams', $stream)
    ->with('message', $message)
    ->with('title', $title)
    ->with('cronStatus', $cronStatus)
    ->with('setting', Setting::first())
    ->render();
