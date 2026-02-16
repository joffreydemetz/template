<?php

namespace JDZ\Template;

/**
 * Twig renderer
 *
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 */
class TwigRenderer
{
  protected bool $debug;
  public ?string $cacheDir;
  public string $layoutPath;
  public string $layoutFolder;
  public string $timezone;
  public string $layout;
  public array $data = [];
  public array $viewLayouts = [];

  protected string $template;
  protected \Twig\Environment $twig;

  public function __construct(bool $debug = false, ?string $cacheDir = null)
  {
    $this->debug = $debug;
    $this->cacheDir = $cacheDir;
    $this->timezone = \date_default_timezone_get();
  }

  public function loadTwig()
  {
    $this->layout = $this->getViewFile($this->viewLayouts);

    $this->twig = new \Twig\Environment(new \Twig\Loader\FilesystemLoader($this->layoutPath), [
      'debug' => $this->debug,
      'cacheDir' => $this->cacheDir,
    ]);

    $core = $this->twig->getExtension("\\Twig\\Extension\\CoreExtension");
    $core->setDateFormat('d/m/Y H:i', '%d days');
    $core->setTimezone($this->timezone);

    $this->twig->addExtension(new \Twig\Extension\DebugExtension());

    return $this;
  }

  public function addTwigExtension(object $extension)
  {
    $this->twig->addExtension($extension);
    return $this;
  }

  public function render(): string
  {
    return $this->twig->render($this->layout, $this->data);
  }

  protected function getViewFile(array $layouts): string
  {
    if ( !($filepath = $this->getLayoutFile($layouts)) ){
      throw new \RuntimeException('Template Error : error loading layout file for '.implode(', ', $layouts));
    }

    return $filepath;
  }

  protected function getLayoutFile(array $layouts, string $extension = 'tmpl'): string|false
  {
    foreach($layouts as $layout){
      $filepath = $this->layoutPath.'/views/'.$layout.'.'.$extension;

      if ( @file_exists($filepath) ){
        return 'views/'.$layout.'.'.$extension;
      }
    }

    return false;
  }
}
