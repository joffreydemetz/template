<?php

/**
 * Example: TemplateData & ViewData Usage
 * 
 * Demonstrates data management classes for templates and views.
 */

require_once __DIR__ . '/../vendor/autoload.php';

use JDZ\Template\TemplateData;
use JDZ\Template\ViewData;

// =====================
// TemplateData
// =====================

$templateData = new TemplateData();

// Append data (recursive merge)
$templateData->append([
    'siteName' => 'My Website',
    'navigation' => [
        'home' => '/',
        'about' => '/about',
    ],
]);

// Add more navigation items
$templateData->append([
    'navigation' => [
        'contact' => '/contact',
    ],
]);

// Push to array with key
$templateData->pushToArray('scripts', '/js/app.js', 'app');
$templateData->pushToArray('scripts', '/js/vendor.js', 'vendor');

// Add type classes (used by Template::load for body classes)
$templateData->addTypeClass('page-article');
$templateData->addTypeClass('has-sidebar');
$templateData->addTypeClass('page-article'); // duplicate — ignored

echo "Template Data:" . PHP_EOL;
print_r($templateData->all());

// =====================
// ViewData
// =====================

$viewData = new ViewData();

// Add JS translations for i18n
$viewData->addJsTranslation('btn.save', 'Save');
$viewData->addJsTranslation('btn.cancel', 'Cancel');

// Bulk add translations
$viewData->addJsTranslations([
    'msg.success' => 'Operation successful',
    'msg.error' => 'An error occurred',
    'msg.loading',  // int key — value becomes the key, value is null
]);

echo PHP_EOL . "View Data (i18n):" . PHP_EOL;
print_r($viewData->all());
