<?php

/**
 * Example: MetasData Usage
 * 
 * Demonstrates the MetasData class for managing
 * page metadata (title, description, Open Graph, etc.).
 */

require_once __DIR__ . '/../vendor/autoload.php';

use JDZ\Template\MetasData;

$metas = new MetasData();

// --- Set individual values ---
$metas->set('title', 'My Awesome Page');
$metas->set('description', 'A description of my page');
$metas->set('robots', 'index, follow');

echo "Title: " . $metas->get('title') . PHP_EOL;

// --- Bulk set ---
$metas->sets([
    'og:title' => 'My Page - OG',
    'og:type' => 'website',
    'og:image' => 'https://example.com/image.jpg',
]);

// --- Default values (won't overwrite existing) ---
$metas->def('title', 'Fallback Title');  // Won't change — already set
$metas->def('author', 'Joffrey Demetz'); // Will set — not yet defined

echo "Title (unchanged): " . $metas->get('title') . PHP_EOL;
echo "Author (default): " . $metas->get('author') . PHP_EOL;

// --- Append with recursive merge ---
$metas->set('social', ['twitter' => '@handle', 'facebook' => 'page']);
$metas->append(['social' => ['twitter' => '@newhandle', 'linkedin' => 'profile']]);

$social = $metas->get('social');
echo "Twitter: " . $social['twitter'] . PHP_EOL;     // @newhandle
echo "Facebook: " . $social['facebook'] . PHP_EOL;    // page
echo "LinkedIn: " . $social['linkedin'] . PHP_EOL;    // profile

// --- Check & erase ---
echo "Has robots? " . ($metas->has('robots') ? 'Yes' : 'No') . PHP_EOL;
$metas->erase('robots');
echo "Has robots? " . ($metas->has('robots') ? 'Yes' : 'No') . PHP_EOL;

// --- Get all data ---
echo PHP_EOL . "All metas:" . PHP_EOL;
print_r($metas->all());
