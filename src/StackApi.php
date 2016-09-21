<?php

namespace Nahid\StackApis;

use duncan3dc\Sessions\SessionInstance;

class StackApi
{
    protected $config;
    protected $session;
    protected $code = '';
    protected $errors = null;

    public $url = '';
    public $answers;

    public function __construct(array $config)
    {
        $confManager = new ConfigManager($config);
        $this->session = new SessionInstance('php-stack-api');
        $this->config = $confManager->config;
        $this->code = isset($_GET['code']) ? $_GET['code'] : null;
        $this->url = 'https://api.stackexchange.com/2.2';
    }

    public function __call($method, $params)
    {
        $method = $this->fromCamelCase($method);
        $param = count($params) > 0 ? '/'.implode('/', $params) : '';
        $this->url .= '/'.$method.$param;

        return $this;
        /*return call_user_func_array([$this, 'makeStackMethod'], array_merge([strtolower($method)], $params));*/
    }

    public function makeAuthUri($caption = 'Authentication', $scope = '')
    {
        return 'https://stackexchange.com/oauth?client_id='.$this->config->get('client_id').'&redirect_uri='.$this->config->get('redirect_uri').'&scope='.$scope;
    }

    public function getAccessToken()
    {
        if ($this->isExpired()) {
            $url = 'https://stackexchange.com/oauth/access_token';

        // Initialize curl
            $ch = curl_init();

            // Set the options
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS,
                'client_id='.$this->config->get('client_id').'&client_secret='.$this->config->get('client_secret').'&code='.$this->code.'&redirect_uri='.$this->config->get('redirect_uri'));

            $result = curl_exec($ch);
            curl_close($ch);

            parse_str($result);

            $this->session->set('accessToken', $access_token);
            $this->session->set('expires', time() + $expires);

            return $access_token;
        }

        return $this->session->get('accessToken');
    }

    public function info($site = 'stackoverflow', $sort = 'reputation')
    {
        $accessTokenUri = $this->makeAccessTokenQueryString();

        $this->url .= '?key='.$this->config->get('key').'&site='.$site.$accessTokenUri.'&filter=default';
        $obj = $this->getDataUsingCurl();

        if (@$obj->error_id) {
            $this->errors = $obj;

            return false;
        }

        $this->errors = null;

        return $obj->items[0];
    }

    public function me()
    {
        $this->url = 'https://api.stackexchange.com/2.2/me';

        return $this;
    }

    public function user($id)
    {
        $this->url = 'https://api.stackexchange.com/2.2/users/'.$id;

        return $this;
    }

    public function users($ids)
    {
        if(is_array($ids)) {
            $ids = implode(',', $ids);
        }

        $this->url = 'https://api.stackexchange.com/2.2/users/' . $ids;

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
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

        $result = curl_exec($ch);
        curl_close($ch);

        $obj = json_decode($result);

        return $obj;
    }

    public function get($site = 'stackoverflow', $page = 1, $pageSize = 10, $sort = '', $order = 'desc', $dateRange = null, $minDate = null)
    {
        $accessTokenUri = $this->makeAccessTokenQueryString();

        $siteUri = strpos($this->url, 'network-activity') == false ? '&site='.$site : '';

        $fromDate = is_array($dateRange && count($dateRange) == 2) ? strtotime($dateRange[0]) : '';
        $toDate = is_array($dateRange && count($dateRange) == 2) ? strtotime($dateRange[1]) : '';
        $min = is_array($minDate && count($minDate) == 2) ? strtotime($dateRange[0]) : '';
        $max = is_array($minDate && count($minDate) == 2) ? strtotime($dateRange[1]) : '';

        $this->url .= '?page='.$page.'&pagesize='.$pageSize.'&fromdate='.$fromDate.'&todate='.$toDate.'&order='.$order.'&min='.$min.'&max='.$max.'&sort='.$sort.$siteUri.'&filter=default'.$accessTokenUri;
        $data = $this->getDataUsingCurl();

        return $data;
    }

    protected function makeAccessTokenQueryString()
    {
        $accessToken = '';
        $accessTokenUri = '';
        if (strpos($this->url, '/2.2/me')) {
            $accessToken = $this->getAccessToken();
            $accessTokenUri = '&access_token='.$accessToken.'&key='.$this->config->get('key');

            return $accessTokenUri;
        }

        return '';
    }

    public function isExpired()
    {
        if (time() > $this->session->get('expires')) {
            return true;
        }

        return false;
    }

    public function destroyAccessToken()
    {
        $this->session->set('accessToken', null);
        $this->session->set('expires', time());
        return true;
    }

    public static function fromCamelCase($str)
    {
        $str[0] = strtolower($str[0]);

        return strtolower(preg_replace('/([A-Z])/', '-'.strtolower('\\1'), $str));
    }
}
