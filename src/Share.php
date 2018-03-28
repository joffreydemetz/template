<?php 
/**
 * (c) Joffrey Demetz <joffrey.demetz@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace JDZ\Template;

/**
 * Share
 * 
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 */
class Share 
{
  protected $data;
  
  public function __construct()
  {
    $this->data = [
      'og' => [
        'title' => '',
        'description' => '',
        'image' => '',
        'url' => '',
        'site_name' => '',
        'locale' => 'fr_FR',
        'type' => 'website',
      ],
      'fb' => [
        'admins' => '',
      ],
      'twitter' => [
        'card' => 'summary',
        'creator' => '',
      ],
    ];
  }
  
  public function getData()
  {
    return $this->data;
  }
  
  public function setTitle($title)
  {
    $title = $this->cleanString($title);
    if ( strlen($title) > 90 ){
      $title = substr($title, 0, 87).'...';
    }
    
    $this->data['og']['title'] = $title;
    return $this;
  }
  
  public function setDescription($description)
  {
    $description = $this->cleanString($description);
    if ( strlen($description) > 200 ){
      $description = substr($description, 0, 197).'...';
    }
    
    $this->data['og']['description'] = $description;
    return $this;
  }
  
  public function setImage($image)
  {
    $this->data['og']['image'] = $image;
    return $this;
  }
  
  public function setUrl($url)
  {
    $this->data['og']['url'] = $url;
    return $this;
  }
  
  public function setSiteName($site_name)
  {
    $this->data['og']['site_name'] = $site_name;
    return $this;
  }
  
  public function setLanguage($locale)
  {
    $this->data['og']['locale'] = $locale;
    return $this;
  }
  
  public function setType($type)
  {
    $this->data['og']['type'] = $type;
    return $this;
  }
  
  public function setFacebookAdmins($admins)
  {
    $this->data['fb']['admins'] = $admins;
    return $this;
  }
  
  public function setTwitterCard($card)
  {
    $this->data['twitter']['card'] = $card;
    return $this;
  }
  
  public function setTwitterCreator($creator)
  {
    $this->data['twitter']['creator'] = $creator;
    return $this;
  }
  
  protected function cleanString($str)
  {
    $str = strip_tags($str);
    $str = htmlentities($str);
    $str = preg_replace("/\s+/", " ", $str);
    $str = html_entity_decode($str);
    $str = trim($str);
    return $str;
  }
}
