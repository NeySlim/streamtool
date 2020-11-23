<?php
include('config.php');
logincheck();

$message = [];
$title = "Create stream";
$stream = new Stream;
$categories = Category::all();
$transcodes = Transcode::all();

if(isset($_GET['id'])) {
    $title = "Edit stream";
    $stream = Stream::find( $_GET['id']);
}

if (isset($_POST['submit'])) {
    $stream->name = $_POST['name'];
    $stream->streamurl = $_POST['streamurl'];
    $stream->cat_id = $_POST['category'];
    $stream->trans_id = $_POST['transcode'];
    $stream->streamurl2 = $_POST['streamurl2'];
    $stream->streamurl3 = $_POST['streamurl3'];
    $stream->tvid = $_POST['tvid'];
    $stream->logo = $_POST['logo'];
    $stream->bitstreamfilter = 0;
    if(isset($_POST['bitstreamfilter'])) {
        $stream->bitstreamfilter = 1;
    }

    if (empty($_POST['name'])) {
        $message['type'] = "error";
        $message['message'] = "Name field is empty";
    }
    else if (empty($_POST['streamurl'])) {
        $message['type'] = "error";
        $message['message'] = "streamurl is empty";
    }
    else if (empty($_POST['category'])) {
        $message['type'] = "error";
        $message['message'] = "Select one category";
    } else {

        if(isset($_GET['id'])) {
            $message['type'] = "success";
            $message['message'] = "Stream saved";
            $stream->save();
        } else {
            $exists = Stream::where('name', '=', $_POST['name'])->get();

            if(count($exists) > 0) {
                $message['type'] = "error";
                $message['message'] = "streamname already in use";
            } else {
                $message['type'] = "success";
                $message['message'] = "Stream created";
                $stream->save();
                redirect("manage_stream.php?id=" . $stream->id, 1000);
            }
        }
    }
}

echo $template->view()->make('manage_stream')
    ->with('stream',  $stream)
    ->with('categories',  $categories)
    ->with('transcodes',  $transcodes)
    ->with('message', $message)
    ->with('title', $title)
    ->with('setting', Setting::first())
    ->render();
