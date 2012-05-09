<?php
class HAProxy_Stats_HttpResponseCode extends HAProxy_Stats_Base {
	protected $map = array(
		'hrsp_1xx' => 'http_1xx',
		'hrsp_2xx' => 'http_2xx',
		'hrsp_3xx' => 'http_3xx',
		'hrsp_4xx' => 'http_4xx',
		'hrsp_5xx' => 'http_5xx',
	);
	/**
	 * number of HTTP 1xx (Informational) responses sent
	 * @var string
	 */
	public $http_1xx;
	/**
	 * number of HTTP 2xx (Successful) responses sent
	 * @var string
	 */
	public $http_2xx;
	/**
	 * number of HTTP 3xx (Redirection) responses sent
	 * @var string
	 */
	public $http_3xx;
	/**
	 * number of HTTP 4xx (Client Error) responses sent
	 * @var string
	 */
	public $http_4xx;
	/**
	 * number of HTTP 5xx (Internal Server Error) responses sent
	 * @var string
	 */
	public $http_5xx;
	
}