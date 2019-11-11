<?php
namespace AndikaMC\TiReq;

use AndikaMC\TiReq\Exceptions\TiReqInvalidArgumentException;

/**
 * Description
 *
 * @version 1.0
 * @author Andika Muhammad Cahya <me@andikamc.me>
 * @package Request
 */
class Request
{

	private $curl,
			$header = [],
			$option = [],
			$cookie,
			$method,
			$user_agent,
			$base_uri = "",
			$body_data,
			$response;

	public function __construct()
	{
		$this->method     = "GET";
		$this->cookie     = @tempnam("/tmp_cookie", "COOKIEFILE");
		$this->user_agent = $this->GetUserAgent();
	}

	/**
	 * Perform a set method
	 */
	private function SetMethod(string $method)
	{
		$this->method = strtoupper($method);

		if ($this->method == "GET")
		{
			$this->SetOption("HTTPGET", true);
		} else
		if ($this->method == "POST")
		{
			$this->SetOption("POST", true);
		} else
		if ($this->method == "NOBODY")
		{
			$this->SetOption("NOBODY", true);
		}
		else
		{
			$this->SetOption("CUSTOMREQUEST", strtoupper($method));
		}
	}

	public function SetHeader($header, string $value = null)
	{
		if (is_array($header))
		{
			$this->header = array_merge($this->header, $header);
		}
		else
		{
			$this->header[$header] = $value;
		}
	}

	/**
	 * Perform set option
	 */
	public function SetOption(string $option, $value)
	{
		$option = strtoupper(str_replace("CURLOPT_", "", $option));

		if (!defined("CURLOPT_".$option))
		{
			throw new TiReqInvalidArgumentException("CURLOPT_".$option." is not a valid constant.");
		}

		$this->option[constant("CURLOPT_".$option)] = $value;

	}

	/**
	 * Perform a set base uri request
	 * 
	 * @param string $uri
	 */
	public function SetBaseURI(string $uri)
	{
		$this->base_uri = trim($uri);
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
	 * Perform a request
	 */
	public function MakeRequest(string $method, string $url, $data = [])
	{
		$this->SetMethod($method);
		$this->ParseBodyData($data);

		$headers = [];
		foreach ($this->header as $header => $value)
		{
			$headers[] = $header . ": " . $value;
		}

		$this->SetOption("FRESH_CONNECT", true);
		$this->SetOption("HTTPHEADER", $headers);
		$this->SetOption("URL", $this->base_uri.$url);
		$this->SetOption("HEADER", true);
		$this->SetOption("RETURNTRANSFER", true);
		$this->SetOption("USERAGENT", $this->user_agent);
		$this->SetOption("COOKIEJAR", $this->cookie);
		$this->SetOption("COOKIEFILE", $this->cookie);

		if (!empty($this->body_data))
		{
			$this->SetOption("POSTFIELDS", $this->body_data);
		}

		$this->response = new Response($this->option);
		$this->ResetProperties();

		return $this->response;
	}

	/**
	 * Reset params
	 */
	private function ResetProperties()
	{
		$this->header    = [];
		$this->option    = [];
		$this->body_data = "";
	}

	/**
	 * Perform set data to post
	 */
	public function ParseBodyData($body)
	{
		if (Request::IsJSON($body))
		{
			$this->SetHeader("Content-Type", "application/json");
			$this->body_data = $body;
		} else
		if (is_array($body))
		{
			$this->SetHeader("Content-Type", "application/x-www-form-urlencoded");
			$this->body_data = http_build_query($body, '', '&');
		} else
		if ($body)
		{
			$this->body_data = $body;
		}

		$this->SetHeader("Content-Length", strlen($this->body_data));
	}

	/**
	 * Perform to get user agent
	 */
	private function GetUserAgent()
	{
		return "cURL/".curl_version()["version"]." PHP/" . PHP_VERSION . " (AndikaMC/TiReq)";
	}

	/**
	 * Perform check string is json or not
	 * 
	 * @param string $string
	 */
	public static function IsJSON($string)
	{
		return is_string($string) && is_array(json_decode(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $string), true)) && (json_last_error() == JSON_ERROR_NONE) ? true : false;
	}

}
