<?php
include('config.php');
logincheck();

$message = [];
$title = "Create IP filter";
$ipblock = new BlockedIp;
$edit = 0;

if (isset($_GET['id'])) {
    $title = "Edit IP filter";
    $ipblock = BlockedIp::find($_GET['id']);
}

if (isset($_POST['submit'])) {
    $error = 0;
    $exists = BlockedIp::where('ip', '=', $_POST['ip'])->get();
    if (empty($_POST['ip'])) {
        $message['type'] = "error";
        $message['message'] = "IP field cannot be empty";
        $error = 1;
    }

    if ($error == 0) {
        $message['type'] = "success";
        if (isset($_GET['id'])) {
            $message['message'] = "filter edited";
        } else {
            $message['message'] = "filter Created";
        }


        $ipblock->ip = $_POST['ip'];
        $ipblock->description = $_POST['description'];
        $ipblock->save();

        redirect("manage_ipblock.php?id=" . $ipblock->id, 2000);
    }
}

echo $template->view()->make('manage_ipblock')
    ->with('ipblock', $ipblock)
    ->with('message', $message)
    ->with('title', $title)
    ->with('setting', Setting::first())
    ->render();
