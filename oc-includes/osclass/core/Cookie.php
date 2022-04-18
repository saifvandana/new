<?php if ( ! defined( 'ABS_PATH' ) ) {
	exit( 'ABS_PATH is not loaded. Direct access is not allowed.' );
}

/*
 * Copyright 2014 Osclass
 * Copyright 2021 Osclass by OsclassPoint.com
 *
 * Osclass maintained & developed by OsclassPoint.com
 * You may not use this file except in compliance with the License.
 * You may download copy of Osclass at
 *
 *     https://osclass-classifieds.com/download
 *
 * Do not edit or add to this file if you wish to upgrade Osclass to newer
 * versions in the future. Software is distributed on an "AS IS" basis, without
 * warranties or conditions of any kind, either express or implied. Do not remove
 * this NOTICE section as it contains license information and copyrights.
 */


class Cookie
{
  public $name;
  public $val;
  public $expires;
  
  private static $instance;

  /**
   * @return \Cookie
   */
  public static function newInstance() {
    if(!self::$instance instanceof self) {
        self::$instance = new self;
    }
    return self::$instance;
  }

  public function __construct()
  {
    $this->val = array();
    $web_path = MULTISITE ? osc_multisite_url() : WEB_PATH;
    $this->name = md5($web_path);
    $this->expires = time() + 3600; // 1 hour by default
    
    if (isset($_COOKIE[$this->name])) {
      $tmp = explode('&', $_COOKIE[$this->name]);
      
      $vars = isset($tmp[0]) ? $tmp[0] : '';
      $vals = isset($tmp[1]) ? $tmp[1] : '';
      
      $vars = explode( '._.' , $vars);
      $vals = explode( '._.' , $vals);

      foreach($vars as $key => $var) {
        if($var != '' && isset($vals[$key])) {
          $this->val["$var"] = $vals[$key];
          $_COOKIE["$var"] = $vals[$key];
        } else {
          $this->val["$var"] = '';
          $_COOKIE["$var"] = '';
        }
      }
    }
  }

  /**
   * @param $var
   * @param $value
   */
  public function push($var, $value)
  {
    $this->val["$var"] = $value;
    $_COOKIE["$var"] = $value;
  }

  /**
   * @param $var
   */
  public function pop($var)
  {
    unset( $this->val[ $var ] , $_COOKIE[ $var ] );
  }
    
  public function clear()
  {
    $this->val = array();
  }
    
  public function set()
  {
    $cookie_val = '';
    
    if(is_array($this->val) && count($this->val) > 0) {
      $cookie_val = '';
      $vars = $vals = array();
      
      foreach ($this->val as $key => $curr){
        if( $curr !== '' ) {
          $vars[] = $key;
          $vals[] = $curr;
        }
      }
      
      if(count($vars) > 0 && count($vals) > 0) {
        $cookie_val = implode( '._.' , $vars) . '&' . implode( '._.' , $vals);
      }
    }
    
    setcookie($this->name, $cookie_val, $this->expires, REL_WEB_URL);
  }

  /**
   * @return int
   */
  public function num_vals() {
    return count($this->val);
  }

  /**
   * @param $str
   *
   * @return mixed|string
   */
  public function get_value( $str ) {
    if (isset($this->val[ $str ])) {
      return $this->val[ $str ];
    }
    
    return '';
  }

  /**
   * @param $tm in seconds
   */
  public function set_expires($tm) {
    $this->expires = time() + $tm;
  }
}

/* file end: ./oc-includes/osclass/core/Cookie.php */