<?php

/**
 * Example: TwigRenderer Usage
 * 
 * Demonstrates rendering Twig templates with the TwigRenderer,
 * including layout fallbacks and custom extensions.
 */

require_once __DIR__ . '/../vendor/autoload.php';

use JDZ\Template\TwigRenderer;
use JDZ\Template\Extension\MergeAttributesTwigExtension;

// --- 1. Create renderer ---

$renderer = new TwigRenderer(
    debug: true,     // Enable debug mode
    cacheDir: null   // No caching (use a path in production)
);

// Configure paths — point to the example templates
$renderer->layoutPath = __DIR__ . '/templates';
$renderer->layoutFolder = 'views';
$renderer->timezone = 'Europe/Paris';

// --- 2. Render a simple template ---

$renderer->viewLayouts = ['page'];  // Will look for views/page.tmpl
$renderer->data = [
    'title' => 'Welcome',
    'content' => 'This is rendered with Twig!',
    'items' => ['PHP', 'Twig', 'Composer'],
];

$renderer->loadTwig();

// Add the merge attributes extension
$renderer->addTwigExtension(new MergeAttributesTwigExtension());

$html = $renderer->render();
echo $html;

// --- 3. Layout fallback ---
// If the first layout doesn't exist, the renderer tries the next one:
//   viewLayouts = ['article', 'default']  →  tries article.tmpl, then default.tmpl
