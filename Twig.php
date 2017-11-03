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
use Twig_Extension_Profiler;
use Twig_Profiler_Profile;
use Twig_Profiler_Dumper_Text;

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
  protected $templateData;
  protected $viewData;
  protected $json;
  protected $debug;
  protected $timezone;
  protected $layout;
  protected $body;
  
  public function __construct(array $properties=[])
  {
    $this->templateData = [];
    $this->json         = false;
    $this->debug        = false;
    $this->timezone     = 'UTC';
    $this->body         = '';
    
    foreach($properties as $key => $value){
      $this->{$key} = $value;
    }
    
    $this->json  = (bool)$this->json;
    $this->debug = (bool)$this->debug;
    
    if ( isset($this->viewData->viewLayout) ){
      $this->viewLayout = $this->viewData->viewLayout;
      unset($this->viewData->viewLayout);
    }
    
    if ( isset($this->viewData->viewLayoutFallback) ){
      $this->viewLayoutFallback = $this->viewData->viewLayoutFallback;
      unset($this->viewData->viewLayoutFallback);
    }
    
    foreach(['layoutPath','viewLayout','viewLayoutFallback','templateClass',''] as $mandatory){
      if ( !$this->{$key} ){
        throw new TemplateException('Missing '.$key);
      }
    }
    
    $this->layout = $this->getViewFile($this->viewLayout, $this->viewLayoutFallback);
    
    $this->setBody();
  }
  
  public function getBody()
  {
    return $this->body;
  }
  
  /** 
   * Render the view
   * 
   * @param   object   $vData  View data
   * @return 	void
   */
  protected function setBody()
  {
    $loader  = new Twig_Loader_Filesystem($this->layoutPath);
    // $profile = new Twig_Profiler_Profile();
    
    $twig = new Twig_Environment($loader, array(
      'debug' => true,
      // Uncomment the line below to cache compiled templates
      // 'cache' => __DIR__.'/../cache',
    ));
    $core = $twig->getExtension('Twig_Extension_Core');
    $core->setDateFormat('d/m/Y H:i', '%d days');
    $core->setTimezone($this->timezone);
    
    $twig->addExtension(new Twig_Extension_Debug());
    // $twig->addExtension(new Twig_Extension_Profiler($profile));
    $twig->addExtension(new TwigExtension());
    
    $template = new $this->templateClass($this->templateData);
    
    $data = $template->getData();
    $data['json']  = $this->json;
    $data['vData'] = $this->viewData;
    // debugMe($data)->end();
    
    $this->body = $twig->render($this->layout, $data);
    
    // $dumper = new Twig_Profiler_Dumper_Text();
    // $this->body .= '<pre style="width:90%;margin:0 0 0 10%;">'.$dumper->dump($profile).'</pre>';
  }
  
  /**
   * Load the view file
   * 
   * @param 	string      $viewLayout             The layout template
   * @param 	string|null $viewLayoutFallback     The layout template fallback
   * @return 	string      The view filepath
   * @throws  TemplateException    If the file was not found
   */
	protected function getViewFile($viewLayout, $viewLayoutFallback=null)
	{
    if ( null !== $viewLayoutFallback ){
      $filepath = $this->getLayoutFile([ 'views/'.$viewLayout, 'views/'.$viewLayoutFallback ]);
    }
    else {
      $filepath = $this->getLayoutFile([ 'views/'.$viewLayout ]);
    }
    
    if ( !$filepath ){
      if ( null !== $viewLayoutFallback ){
        throw new TemplateException('Error loading layout file for views/'.$viewLayout.' nor views/'.$viewLayoutFallback);
      }
      
      throw new TemplateException('Error loading layout file for views/'.$viewLayout);
    }
    
    return $filepath;
	}
  
	/**
	 * Look for a file
   * 
	 * @param 	string|array  $file     The file name(s)
	 * @return 	string|false  The found file path or false if not found 
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
