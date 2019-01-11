<?php 
/**
 * (c) Joffrey Demetz <joffrey.demetz@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace JDZ\Template;

use JDZ\Filesystem\Path;
use JDZ\Filesystem\File;
use Twig_Loader_Filesystem;
use Twig_Environment;
use Twig_Extension_Debug;
// use Twig_Extension_Profiler;
// use Twig_Profiler_Profile;
// use Twig_Profiler_Dumper_Text;

/**
 * Twig renderer
 * 
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 */
class Twig 
{
  protected $layoutPath;
  protected $viewLayout;
  protected $viewLayoutFallback;
  protected $templateClass;
  protected $json;
  protected $debug;
  protected $timezone;
  protected $cacheDir;
  protected $templateData;
  protected $viewData;
  protected $layout;
  protected $twig;
  
  public function __construct($layoutPath, $templateClass, $viewLayout, $viewLayoutFallback)
  {
    $this->setLayoutPath($layoutPath);
    $this->setTemplateClass($templateClass);
    $this->setViewLayout($viewLayout);
    $this->setViewLayoutFallback($viewLayoutFallback);
    
    $this->json     = false;
    $this->debug    = false;
    $this->cacheDir = false;
    $this->timezone = date_default_timezone_get();
    
    $this->templateData = [];
    $this->viewData     = [];
  }
  
  public function setLayoutPath($layoutPath)
  {
    $this->layoutPath = $layoutPath;
    return $this;
  }
  
  public function setTemplateClass($templateClass)
  {
    $this->templateClass = $templateClass;
    return $this;
  }
  
  public function setViewLayout($viewLayout)
  {
    $this->viewLayout = $viewLayout;
    return $this;
  }
  
  public function setViewLayoutFallback($viewLayoutFallback)
  {
    $this->viewLayoutFallback = $viewLayoutFallback;
    return $this;
  }
  
  public function setJson()
  {
    $this->json = true;
    return $this;
  }
  
  public function setDebug()
  {
    $this->debug = true;
    return $this;
  }
  
  public function setTimezone($timezone)
  {
    $this->timezone = $timezone;
    return $this;
  }
  
  public function setCacheDir($cacheDir)
  {
    $this->cacheDir = $cacheDir;
    return $this;
  }
  
  public function loadTwig(array $extensions=[])
  {
    $this->layout = $this->getViewFile($this->viewLayout, $this->viewLayoutFallback);
    
    $loader = new Twig_Loader_Filesystem($this->layoutPath);
    
    $twigEnv = [
      'debug' => $this->debug,
    ];
    
    if ( $this->debug ){
      $twigEnv['debug'] = true;
    }
    
    if ( $this->cacheDir ){
      $twigEnv['cacheDir'] = $this->cacheDir;
    }
    
    $this->twig = new Twig_Environment($loader, $twigEnv);
    $core = $this->twig->getExtension('Twig_Extension_Core');
    $core->setDateFormat('d/m/Y H:i', '%d days');
    $core->setTimezone($this->timezone);
    
    $this->twig->addExtension(new Twig_Extension_Debug());
    $this->twig->addExtension(new TwigExtension());
    
    // $profile = new Twig_Profiler_Profile();
    // $twig->addExtension(new Twig_Extension_Profiler($profile));
    
    if ( $extensions ){
      foreach($extensions as $extension){
        $this->addTwigExtension($extension);
      }
    }
    
    return $this;
  }
  
  public function addTwigExtension($className)
  {
    $this->twig->addExtension(new $className());
    return $this;
  }
  
  public function setTemplateData(array $templateData)
  {
    $this->templateData = $templateData;
    return $this;
  }
  
  public function setViewData(array $viewData)
  {
    $this->viewData = $viewData;
    return $this;
  }
  
  /** 
   * Render the view
   * 
   * @param   object   $vData  View data
   * @return   void
   */
  public function render()
  {
    $template = new $this->templateClass($this->templateData);
    
    $data = $template->getData();
    $data['json']  = $this->json;
    $data['vData'] = $this->viewData;
    
    $body = $this->twig->render($this->layout, $data);
    
    // $dumper = new Twig_Profiler_Dumper_Text();
    // $body .= '<pre style="width:90%;margin:0 0 0 10%;">'.$dumper->dump($profile).'</pre>';
    
    return $body;
  }
  
  /**
   * Load the view file
   * 
   * @param   string      $viewLayout             The layout template
   * @param   string|null $viewLayoutFallback     The layout template fallback
   * @return   string      The view filepath
   * @throws  TemplateException    If the file was not found
   */
  protected function getViewFile($viewLayout, $viewLayoutFallback=null)
  {
    $layoutFiles = [];
    
    if ( null !== $viewLayoutFallback ){
      $layoutFiles = [ 
        'views/'.IAPP.'/'.$viewLayout.'.'.$viewLayoutFallback, 
        'views/'.IAPP.'/'.$viewLayout, 
        'views/'.IAPP.'/'.$viewLayoutFallback,
      ];
    }
    else {
      $layoutFiles = [ 
        'views/'.IAPP.'/'.$viewLayout,
      ];
    }
    
    $filepath = $this->getLayoutFile($layoutFiles);
    
    if ( !$filepath ){
      if ( null !== $viewLayoutFallback ){
        throw new TemplateException('Error loading layout nor fallbacks for '.implode(', ', $layoutFiles));
      }
      
      throw new TemplateException('Error loading layout file for '.implode(', ', $layoutFiles));
    }
    
    return $filepath;
  }
  
  /**
   * Look for a file
   * 
   * @param   string|array  $file     The file name(s)
   * @return   string|false  The found file path or false if not found 
   */
  protected function getLayoutFile($file, $extension='tmpl')
  {
    if ( !is_array($file) ){
      $file = [ $file ];
    }
    
    foreach($file as $possible){
      $filepath = Path::clean($this->layoutPath.'/'.$possible.'.'.$extension);
      if ( File::exists($filepath) ){
        return $possible.'.'.$extension;
      }
    }
    
    return false;
  }
}
