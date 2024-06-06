<?php

include_once('CurlHelper.php');

class PayPalHelper {

	private $_curl = null;
	private $_apiUrl = null;
	private $_token = null;

	public function __construct() {
		$this->_curl = new CurlHelper;
		$this->_apiUrl = PAYPAL_ENDPOINTS[PAYPAL_ENVIRONMENT];
	}

	private function _createApiUrl($resource) {
		if($resource == 'oauth2/token') {
			return $this->_apiUrl . "/v1/" . $resource;
		} else {
			return $this->_apiUrl . "/v2/" . $resource;
		}
	}

	private function _getToken() {
		$this->_curl->resetHelper();
		$this->_curl->setUrl($this->_createApiUrl("oauth2/token"));
		$this->_curl->setAuthentication(PAYPAL_CREDENTIALS[PAYPAL_ENVIRONMENT]['client_id'] . ":" . PAYPAL_CREDENTIALS[PAYPAL_ENVIRONMENT]['client_secret']);
		$this->_curl->setBody("grant_type=client_credentials");
		$returnData = $this->_curl->sendRequest();
		$this->_token = $returnData["response"]['access_token'];
	}

	private function _createOrder($postData) {
		$this->_curl->resetHelper();
		$this->_curl->addHeader("Content-Type: application/json");
		$this->_curl->addHeader("Authorization: Bearer " . $this->_token);
		$this->_curl->setUrl($this->_createApiUrl("checkout/orders"));
		$this->_curl->setBody($postData);
		return $this->_curl->sendRequest();
	}

	private function _getOrderDetails($orderId) {
		$this->_curl->resetHelper();
		$this->_curl->addHeader("Content-Type: application/json");
		$this->_curl->addHeader("Authorization: Bearer " . $this->_token);
		$this->_curl->setUrl($this->_createApiUrl("checkout/orders/" . $orderId));
		return $this->_curl->sendRequest();
	}

	private function _patchOrder($orderId, $postData) {
		$this->_curl->resetHelper();
		$this->_curl->addHeader("Content-Type: application/json");
		$this->_curl->addHeader("Authorization: Bearer " . $this->_token);
		$this->_curl->setUrl($this->_createApiUrl("checkout/orders/" . $orderId));
		$this->_curl->setPatchBody($postData);
		return $this->_curl->sendRequest();
	}

	private function _authorizeOrder($orderId) {
		$this->_curl->resetHelper();
		$this->_curl->addHeader("Content-Type: application/json");
		$this->_curl->addHeader("Authorization: Bearer " . $this->_token);
//		$this->_curl->addHeader("PayPal-Mock-Response: {'mock_application_codes': 'DUPLICATE_INVOICE_ID'}");
//		$this->_curl->addHeader("PayPal-Mock-Response: {'mock_application_codes': 'INSTRUMENT_DECLINED'}");
		$this->_curl->setUrl($this->_createApiUrl("checkout/orders/" . $orderId . "/authorize"));
		$postData='{}';
		$this->_curl->setBody($postData);
		return $this->_curl->sendRequest();
	}

	private function _captureOrder($orderId) {
		$this->_curl->resetHelper();
		$this->_curl->addHeader("Content-Type: application/json");
		$this->_curl->addHeader("Authorization: Bearer " . $this->_token);
//		$this->_curl->addHeader("PayPal-Mock-Response: {'mock_application_codes': 'DUPLICATE_INVOICE_ID'}");
//		$this->_curl->addHeader("PayPal-Mock-Response: {'mock_application_codes': 'INSTRUMENT_DECLINED'}");
		$this->_curl->setUrl($this->_createApiUrl("checkout/orders/" . $orderId . "/capture"));
		$postData='{}';
		$this->_curl->setBody($postData);
		return $this->_curl->sendRequest();
	}

	public function createOrder($postData) {
		if($this->_token === null) {
			$this->_getToken();
		}
		$returnData = $this->_createOrder($postData);
		return $returnData;
	}

	public function getOrderDetails($orderId) {
		if($this->_token === null) {
			$this->_getToken();
		}
		return $this->_getOrderDetails($orderId);
	}

	public function patchOrder($orderId, $postData) {
		if($this->_token === null) {
			$this->_getToken();
		}
		// $returnData = $this->_patchOrder($orderId, $postData);
		// return array(
		// 	"ack" => true,
		// 	"data" => $returnData
		// );
		return $this->_patchOrder($orderId, $postData);
	}

	public function authorizeOrder($orderId) {
		if($this->_token === null) {
			$this->_getToken();
		}
		return $this->_authorizeOrder($orderId);
	}

	public function captureOrder($orderId) {
		if($this->_token === null) {
			$this->_getToken();
		}
		return $this->_captureOrder($orderId);
	}

}

?>
