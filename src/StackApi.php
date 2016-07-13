<?php

namespace Nahid\StackApis;

use Nahid\StackApis\ConfigManager;
use duncan3dc\Sessions\SessionInstance;

class StackApi
{
	protected $config;
	protected $session;
	protected $code = '';
	protected $errors = null;
	protected $url;

	function __construct()
	{
		$confManager = new ConfigManager;
		$this->session = new SessionInstance('php-stack-api');
		$this->config = $confManager->config;
		$this->code = isset($_GET['code'])?$_GET['code']:null;
	}

	public function makeAuthLink($caption = 'Authentication', $scope = '')
	{
		return '<a href="https://stackexchange.com/oauth?client_id='. $this->config->get('client_id') .'&redirect_uri='. $this->config->get('redirect_uri') .'&scope='. $scope .'">'. $caption .'</a>';
	}

	public function getAccessToken()
	{
		if($this->isExpired()) {
			$url = 'https://stackexchange.com/oauth/access_token';

	    // Initialize curl
	        $ch = curl_init();
	        
	        // Set the options
	        curl_setopt($ch, CURLOPT_URL, $url);
	        curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
	        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	        curl_setopt ($ch, CURLOPT_POST, 1);
	        curl_setopt($ch, CURLOPT_POSTFIELDS,
	            'client_id='. $this->config->get('client_id'). '&client_secret='. $this->config->get('client_secret'). '&code='. $this->code. '&redirect_uri='. $this->config->get('redirect_uri'));

	        $result = curl_exec ($ch);
	        curl_close ($ch);
	    	
	    	
	    	parse_str($result);

	    	$this->session->set('accessToken', $access_token);
	    	$this->session->set('expires', time() + $expires);
	    	return $access_token;
    	}

    	return $this->session->get('accessToken');
	}


	public function info($site = 'stackoverflow', $sort='reputation')
	{
		$accessToken = $this->getAccessToken();
		//	var_dump($accessToken);

    	$this->url .= '?key='. $this->config->get('key') .'&site='. $site .'&access_token='. $accessToken. '&filter=default';
    	$obj = $this->getDataUsingCurl();

    	if(@$obj->error_id) {
    		$this->errors = $obj;
    		return false;
    	}

    	$this->errors = null;
    	return $obj->items[0];
	}

	public function answers(
		$site = 'stackoverflow',
		$page = 1, 
		$pageSize = 10, 
		$order = 'desc', 
		$sort = 'activity', 
		array $dateRange = null, 
		array $minDate = null
	)
	{
		$accessToken = $this->getAccessToken();

		$fromDate = is_array($dateRange && count($dateRange)==2)?strtotime($dateRange[0]):'';
		$toDate = is_array($dateRange && count($dateRange)==2)?strtotime($dateRange[1]):'';
		$min = is_array($minDate && count($minDate)==2)?strtotime($dateRange[0]):'';
		$max = is_array($minDate && count($minDate)==2)?strtotime($dateRange[1]):'';

		$this->url .= '/answers?page='. $page .'&pagesize='. $pageSize .'&fromdate='. $fromDate .'&todate='. $toDate .'&order='. $order .'&min='. $min .'&max='. $max .'&sort='. $sort .'&site='. $site .'&access_token='. $accessToken;
		$data = $this->getDataUsingCurl();
		return $data->items;
	}

	public function me()
	{
		$this->url = 'https://api.stackexchange.com/2.2/me';
		return $this;
	}

	public function user($id)
	{
		$this->url = 'https://api.stackexchange.com/2.2/users/'. $id;
		return $this;
	}


	protected function getDataUsingCurl()
	{
		$ch = curl_init();
        
        // Set the options
        curl_setopt($ch, CURLOPT_URL, $this->url);
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip');  // Required by API
        curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER,array('Content-Type: application/json')); 

        $result = curl_exec ($ch);
        curl_close ($ch);
    	
    	$obj = json_decode($result);
    	return $obj;
	}

	public function isExpired()
	{
		/*echo 'Now: '. time();
		echo '<br/>Expire: '. $this->session->get('expires'). '<br/>';*/
		if(time()>$this->session->get('expires')) {
			return true;
		}

		return false;
	}

}