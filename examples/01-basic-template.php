<?php

/**
 * Example: Basic Template Usage
 * 
 * Demonstrates how to create a concrete template class,
 * load data, set body classes and themes.
 */

require_once __DIR__ . '/../vendor/autoload.php';

use JDZ\Template\Template;

// --- 1. Create a concrete template by extending the abstract class ---

class HomeTemplate extends Template
{
    protected function loadData(): void
    {
        $this->data['title'] = 'Welcome Home';
        $this->data['description'] = 'This is the home page';
        $this->data['typeClass'] = ['type-page', 'type-home'];
    }

    protected function loadBodyClass(): void
    {
        $this->bodyClasses[] = 'page-home';
        $this->bodyClasses[] = 'layout-wide';
    }
}

// --- 2. Instantiate and load ---

// Auto-detects name from class: "homeTemplate"
$template = new HomeTemplate();
$template->theme = 'dark';
$template->load();

echo "Template Name: " . $template->getName() . PHP_EOL;
echo "Body Class: " . $template->getData()['bodyclass'] . PHP_EOL;
echo PHP_EOL;

// --- 3. With explicit name ---

$template2 = new HomeTemplate('main');
$template2->load();

echo "Template Name: " . $template2->getName() . PHP_EOL;
echo "Body Class: " . $template2->getData()['bodyclass'] . PHP_EOL;
echo PHP_EOL;

// Output:
// Template Name: homeTemplate
// Body Class: theme-dark type-home type-page page-home layout-wide
//
// Template Name: main
// Body Class: type-home type-page page-home layout-wide
