<?php
namespace AndikaMC\TiReq;

use AndikaMC\TiReq\Exceptions\TiReqException;

/**
 * Description
 *
 * @version 1.0
 * @author Andika Muhammad Cahya <me@andikamc.me>
 * @package Response
 */
class Response
{
	protected $curl,
			$raw,
			$http_code,
			$header_size,
			$header = "NULL",
			$body;

	public function __construct($options=[], &$last=true)
	{
		$this->curl = curl_init();
		curl_setopt_array($this->curl, $options);
		if (!$this->raw = curl_exec($this->curl))
		{
			throw new TiReqException(curl_error($this->curl));
		}

		$this->http_code = curl_getinfo($this->curl, CURLINFO_HTTP_CODE);
		$this->header_size = curl_getinfo($this->curl, CURLINFO_HEADER_SIZE);
		$this->header = substr($this->raw, 0, $this->header_size);
		$this->body   = substr($this->raw, $this->header_size);
		curl_close($this->curl);

		return $this;
	}

	/**
	 * [GetResponseRaw description]
	 */
	public function GetResponseRaw()
	{
		return $this->raw;
	}

	/**
	 * [GetResponseHead description]
	 */
	public function GetResponseHead()
	{
		return $this->header;
	}

	/**
	 * [GetResponseItems description]
	 */
	public function GetResponseItems()
	{
		$items = [];
		foreach (explode("\r\n", $this->header) as $header_item)
		{
			if (explode(":", $header_item)[0] !== "")
			{
				if (str_replace(explode(":", $header_item)[0], NULL, $header_item) == "")
				{
					$items[] = explode(":", $header_item)[0];
				} else
				{
					$items[explode(":", $header_item)[0]] = trim(substr(str_replace(explode(":", $header_item)[0], NULL, $header_item), 1));
				}
			}
		}
		return $items;
	}

	/**
	 * [GetResponseItem description]
	 */
	public function GetResponseItem($header)
	{
		return @$this->GetResponseItems()[$header];
	}

	/**
	 * [GetResponseBody description]
	 */
	public function GetResponseBody($encode = false)
	{
		if ($encode)
		{
			if (Request::IsJSON($this->body)) return json_decode($this->body, true);
		}
		return $this->body;
	}

	/**
	 * [GetResponseBodyLength description]
	 */
	public function GetResponseBodyLength()
	{
		return strlen($this->body);
	}

	/**
	 * 
	 */
	public function GetResponseHttpCode()
	{
		return $this->http_code;
	}

}
