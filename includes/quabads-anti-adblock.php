<?php

/**
 * Based on AntiAdBlock custom library for API, with some caching.
 */
class QuabAdsAntiAdblock
{
    /** @var string */
    private $publisherID;
    /** @var int */
    private $adslotId;
    public $adjsFile;
    public $jsReadFile;
    ///// do not change anything below this point /////
    private $err_reporting = 0;//change to 0 in production, -1 in development.
    public $folderDir = 'quab';
	private $viewFile;// = 'sw_view.php';
	private $clickFile;// = 'sw_click.php';
	private $readerFile;// = 'sw_read.php';
    
    private $requestDomainName = 'https://adshop.quabads.com';
    private $requestIsSSL = true;
    private $cacheTtl = 10080; // minutes
    private $RSWDispenser = 'reading-service-worker.php';
    private $VSWDispenser = 'viewing-service-worker.php';
	private $CSWDispenser = 'clicking-service-worker.php';
	private $JSWDispenser = 'javascript-service-worker.php';

    public function __construct($adslot_id, $publisher_id)
    {
        $this->publisherID  = $publisher_id;
        $this->adslotId = $adslot_id;
        $this->adjsFile = md5($publisher_id.'123').'.js';
        $this->jsReadFile = md5($publisher_id.'123').'.php';
        $this->viewFile = md5($publisher_id.'vwr').'.php';
        $this->clickFile = md5($publisher_id.'clk').'.php';
        $this->readerFile = md5($publisher_id.'rea').'.php';
    }
    /**
     * @return bool
     */
    protected function ignoreCache()
    {
        /*$key = md5('PMy6vsrjIf-' . $this->zoneId);
        return array_key_exists($key, $_GET);*/
        return true;
    }

    /**
     * @param string $url
     * @return string
     */
    private function getCacheFilePath($url,$sw_file)
    {
        return $this->findTmpDir() .'/'. $sw_file . '.php';
    }

    /**
     * @return null|string
     */
    public function findTmpDir()
    {
        
		$UploadDir = wp_upload_dir();
		$UploadURL = $UploadDir['basedir'];
        $dir = $UploadURL . "/" . md5(strtr($this->publisherID,'us_','123'));
        $old = umask(0);
		if (!is_dir($dir)) {
			if(mkdir($dir, 0755, true)){
				umask($old);
				return $dir;
			}
		}else{
			umask($old);
			return $dir;
		} 
    }

    /**
     * @param string $file
     * @return bool
     */
    private function isActualCache($file)
    {
        if ($this->ignoreCache()) {
            return false;
        }
        //return file_exists($file) && (time() - filemtime($file) < $this->cacheTtl * 60);
    }

    /**
     * @param string $url
     * @param bool $decode
     * @return bool|string
     */
    public function getCode($url, $decode = false)
    {
        global $wp_version;

		if ($url === null) {
			return null;
		}
        $full_url = $this->requestDomainName . $url;
		$response = wp_remote_get($full_url, array(
			'user-agent' => 'WordPress/' . $wp_version . '; ' . get_bloginfo( 'url' ),
		));

		if (is_array($response)) {
			if ($decode) {
				$decodedData = json_decode($response['body'], true);

				if (json_last_error() === JSON_ERROR_NONE) {
					return $decodedData;
				}

				return null;
			}

			return $response['body'];
		}

		return null;
    }
    /**
     * @param string $adslot
     * @return string
     */
    public function displayAd($adslot){
        $uploadDir = wp_upload_dir();
		$uploadURL = $uploadDir['baseurl'];
		$pub['publisher'] = $this->publisherID;
		$pub['slot'] = $adslot;
		$pub['directory'] = $uploadURL.'/'.$this->publisherID;
		$pub['page'] = $_SERVER['PHP_SELF'];
		$pub['host'] = $_SERVER['SERVER_NAME'];
		$pub['finger'] = md5($_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_USER_AGENT'].$_SERVER['REMOTE_PORT']);
		$pub['reader'] = $this->readerFile;
		$pub['clicker'] = $this->clickFile;
		$xai = str_replace("=","",base64_encode(json_encode($pub)));
		$dir = '/wp/1.2.1/wp-token-gen.php?xai='.$xai;
		$ad = $this->getCode($dir,false);
		if(!empty($ad)){
			$ad = json_decode($ad);
            if($ad->loadHelper == 'okay'){
                $this->get($this->RSWDispenser,$this->readerFile);
                $this->get($this->VSWDispenser,$this->viewFile);
                $this->get($this->CSWDispenser,$this->clickFile);
                $this->get($this->JSWDispenser,$this->adjsFile);
            }
            return $ad->ad;
		}
	}
    
    /**
     * @return string
     */
    public function requestServiceWorkers(){
        $request_code = true;
        $uploadDir = wp_upload_dir();
		$uploadURL = $uploadDir['baseurl'];
		$pub['publisher'] = $this->publisherID;
		$pub['host'] = $_SERVER['SERVER_NAME'];
		$pub['directory'] = $uploadURL.'/'. md5(strtr($this->publisherID,'us_','123'));
		$xai = str_replace("=","",base64_encode(json_encode($pub)));
		$dir = '/wp/1.2.1/request-service-workers.php?xai='.$xai;
        $file_dir = $this->findTmpDir() .'/';//$this->findTmpDir().'/';
        $js_file = $file_dir.$this->adjsFile;
        $js_read_file = $file_dir.$this->jsReadFile;
        $read_file = $file_dir.$this->readerFile;
        $view_file = $file_dir.$this->viewFile;
        $click_file = $file_dir.$this->clickFile;
        $cache_file = $file_dir.$this->adjsFile;
        if (file_exists($cache_file)) {
             $filemtime = @filemtime($cache_file);  // returns FALSE if file does not exist
            if (!$filemtime or (time() - $filemtime >= $this->cacheTtl*60)){
                $request_code = true;
            }else{
                $request_code = false;
            }
        }
        if($request_code){
            $ret_code = $this->getCode($dir, false);
            if(!empty($ret_code)){
                $new_code = json_decode($ret_code);
                /*write reader file*/
                if(!empty($new_code->reader)){
                    $fp = fopen($read_file, "w+");
                    if (flock($fp, LOCK_EX)) {
                        ftruncate($fp, 0);
                        fwrite($fp, $new_code->reader);
                        fflush($fp);
                        flock($fp, LOCK_UN);
                    }
                    fclose($fp);
                }
                /*write viewer file*/
                if(!empty($new_code->viewer)){
                    $fp = fopen($view_file, "w+");
                    if (flock($fp, LOCK_EX)) {
                        ftruncate($fp, 0);
                        fwrite($fp, $new_code->viewer);
                        fflush($fp);
                        flock($fp, LOCK_UN);
                    }
                    fclose($fp);
                }
                /*write clicker file*/
                if(!empty($new_code->clicker)){
                    $fp = fopen($click_file, "w+");
                    if (flock($fp, LOCK_EX)) {
                        ftruncate($fp, 0);
                        fwrite($fp, $new_code->clicker);
                        fflush($fp);
                        flock($fp, LOCK_UN);
                    }
                    fclose($fp);
                }
                /*write js_file*/
                if(!empty($new_code->javascript)){
                    $fp = fopen($js_file, "w+");
                    if (flock($fp, LOCK_EX)) {
                        ftruncate($fp, 0);
                        fwrite($fp, $new_code->javascript);
                        fflush($fp);
                        flock($fp, LOCK_UN);
                    }
                    fclose($fp);
                }
                /*write js_reader file*/
                if(!empty($new_code->js_reader)){
                    $fp = fopen($js_read_file, "w+");
                    if (flock($fp, LOCK_EX)) {
                        ftruncate($fp, 0);
                        fwrite($fp, $new_code->js_reader);
                        fflush($fp);
                        flock($fp, LOCK_UN);
                    }
                    fclose($fp);
                }
            }
        }
            
	}
	
    /**
     * @param string $code
     * @return string
     */
    private function getTag($code)
    {
        $codes = explode('{[DEL]}', $code);

        if (isset($codes[0])) {
            if (isset($_COOKIE['5117932282'])) {
                return $codes[0];
            } else {
                return (isset($codes[1]) ? $codes[1] : '');
            }
        } else {
            return '';
        }
    }
    /**
     * @param string $sw_file
     * @param string $service_worker
     * @return string
     */
    public function get($sw_file,$service_worker)
    {
        $url      = '/wp/1.2.1/'.$sw_file;
        $file     = $this->getCacheFilePath($url,$service_worker);
        if ($this->isActualCache($file)) {
            return file_get_contents($file);
        }
        if (!file_exists($file)) {
            @touch($file);
        }
        $code = '';
        if ($this->ignoreCache()) {
            $fp = fopen($file, "r+");
            if (flock($fp, LOCK_EX)) {
                $code = $this->getCode($url, false);
                ftruncate($fp, 0);
                fwrite($fp, $code);
                fflush($fp);
                flock($fp, LOCK_UN);
            }
            fclose($fp);
        } else {
            $fp = fopen($file, 'r+');
            if (!flock($fp, LOCK_EX | LOCK_NB)) {
                if (file_exists($file)) {
                    // take old cache
                    $code = file_get_contents($file);
                } else {
                    $code = "<!-- cache not found / file locked  -->";
                }
            } else {
                $code = $this->getCode($url, false);
                ftruncate($fp, 0);
                fwrite($fp, $code);
                fflush($fp);
                flock($fp, LOCK_UN);
            }
            fclose($fp);
        }

        return $this->getTag($code);
    }
}
