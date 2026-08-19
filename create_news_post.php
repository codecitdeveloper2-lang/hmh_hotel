<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$post = new \App\Models\NewsPost();
$post->title = 'Test News';
$post->slug = 'test-news';
$post->is_active = 1;
$post->channel = 'news';
$post->published_at = now();
$post->body = json_encode(['content' => 'Test Content']);
$post->save();

echo "News post created with ID: " . $post->id . "\n";
