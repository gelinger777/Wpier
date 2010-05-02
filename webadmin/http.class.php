<?
class http {
	var $proxy_host="";
	var $proxy_port=0;

	function http_fopen($host, $path, $port=80,$user,$password) {
		if (empty($this->proxy_host)) {
			$conn_host=$host;
			$conn_port=$port;
		} else {
			$conn_host=$this->proxy_host;
			$conn_port=$this->proxy_port;
		}

		if ($user && $password) 
			$autoriz64="Authorization: Basic ".base64_encode ("$user:$password")."\r\n";
		else $autoriz64="";

		$query="GET $path HTTP/1.0\r\nHost: $host:$port\r\nUser-agent: PHP/class http 0.1\r\n$autoriz64\r\n";

		$fp = fsockopen($conn_host, $conn_port,$errno,$errstr);

		if (!$fp) return false;

		fputs ($fp,$query);

		while (trim(fgets($fp,1024))!="");

		return $fp;
	}
}
?>