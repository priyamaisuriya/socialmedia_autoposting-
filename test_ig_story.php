<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$page = \App\Models\FacebookPage::find(7);
$api = new \App\Services\FacebookApiService();
$res = $api->publishToInstagram($page, '', 'posts/7AsEpx7PfQoll4udcgZdwpRQeQQxSxdG0uWKAOAg.jpg', 'image', 'story');
echo json_encode($res);
