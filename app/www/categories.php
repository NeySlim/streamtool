<?php
/**
 * Created by NeySlim 2020
 */
include('config.php');
logincheck();
$message = [];

if (isset($_GET['delete'])) {
    $category = Category::find($_GET['delete']);
    $category->delete();

    $message['type'] = "success";
    $message['message'] = "Category deleted";
}

$categories = Category::all();

echo $template->view()->make('categories')
    ->with('categories', $categories)
    ->with('message', $message)
    ->with('setting', Setting::first())
    ->render();
