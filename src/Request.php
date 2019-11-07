<?php
namespace AndikaMC\TiReq;

class Request
{

	protected $cookie;
	protected $user_agent;
	protected $base_uri   = "";

	public function __construct()
	{
		$this->cookie     = tempnam("/tmp", "COOKIEFILE");
		$this->user_agent = $this->GetUserAgent();
	}

	/**
	 * Perform a set base uri request
	 * 
	 * @param string $uri
	 */
	public function SetBaseURI(string $uri)
	{
		# code...
	}

	/**
	 * Perform a set user agent request
	 *
	 * @param string $user_agent
	 */
	public function SetUserAgent(string $user_agent)
	{
		$this->user_agent = trim($user_agent);
	}

	/**
	 * Perform to get user agent
	 */
	private function GetUserAgent()
	{
		return "cURL/".curl_version()["version"]." PHP/" . PHP_VERSION . " (AndikaMC/TiReq)";
	}

}
