<?php

/**
 * Example: Renderer Extensions
 * 
 * Demonstrates post-processing rendered HTML with
 * RendererExtension and NoFollowLinksRendererExtension.
 */

require_once __DIR__ . '/../vendor/autoload.php';

use JDZ\Template\Extension\RendererExtension;
use JDZ\Template\Extension\NoFollowLinksRendererExtension;

// --- 1. Base RendererExtension (passthrough) ---

$baseExt = new RendererExtension();
$html = '<p>Hello World</p>';

echo "Base extension (passthrough):" . PHP_EOL;
echo $baseExt->render($html) . PHP_EOL . PHP_EOL;

// --- 2. NoFollowLinksRendererExtension ---

$noFollowExt = new NoFollowLinksRendererExtension();

$body = <<<HTML
<p>Contact us at <a href="mailto:info@example.com">info@example.com</a></p>
<p>Call us at <a href="tel:+33123456789">+33 1 23 45 67 89</a></p>
<p>Visit <a href="https://example.com" class="external">our website</a></p>
<p>Read <a href="/blog/article-1">our latest article</a></p>
HTML;

echo "NoFollow extension:" . PHP_EOL;
echo $noFollowExt->render($body) . PHP_EOL;

// Result:
//   mailto: and tel: links get target="_blank" and rel="nofollow"
//   Regular links (https, relative) are left unchanged
