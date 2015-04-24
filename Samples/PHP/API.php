<?php
session_start();
class BTCLendMerchantAPI
{

	private function APIKey(){
		return "";
	}
	
	public function ServerURL(){
		return "https://api.btclend.org/v1/";
	}
	public function ServerURLTransaction(){
		return $this->ServerURL()."Transaction";
	}
	public function ServerURLToken(){
		return $this->ServerURL()."Token";
	}

	public function initTObject($amount,$orderId,$customerEmail) 
	{
		
	   $tran = new Transaction();
	   $tran->amount = $amount;
       $tran->orderId = $orderId;
       $tran->customerEmail = $customerEmail;
       $tran->passCode = "NA";
       $tran->transactionId = "NA";
       $tran->redeemCode = "NA";
       $tran->apikey = $this->APIKey();
       return $tran;
	}
	/// You will need to request new token every hour.
	public function requestNewToken(Transaction $tran)
	{
		$params = array('APIKey' => $this->APIKey());
		$data = $this->execRequest($params,$this->ServerURLToken());

		$tran->error = $data['error'];
		$tran->message = $data['message'];
		$tran->token = $data['token'];

		return $tran;

	
	
	}

	/// If everything is OK you will get your transaction ID
	public function createNewOrder(Transaction $tran)
	{
		$params = array('APIKey' => $tran->apikey, 'Token' => $tran->token, 'reqAmount' => $tran->amount, 'reqOrderId' => $tran->orderId, 'reqUserEmail' => $tran->customerEmail);
		$data = $this->execRequest($params,$this->ServerURLTransaction());

		$tran->error = $data['error'];
		$tran->message = $data['message'];
		$tran->transactionId = $data['respTransactionId'];

		return $tran;
	
	
	}

	/// If everything is OK you will get your transaction ID
	public function confirmOrder(Transaction $tran)
	{
		$params = array('APIKey' => $tran->apikey, 'Token' => $tran->token, 'reqAmount' => $tran->amount, 'reqOrderId' => $tran->orderId, 'reqUserEmail' => $tran->customerEmail, 'reqPasscode' => $tran->passCode,'respTransactionId' => $tran->transactionId);
		$data = $this->execRequest($params,$this->ServerURLTransaction());

		$tran->error = $data['error'];
		$tran->message = $data['message'];
		$tran->redeemCode = $data['reqSignature'];

		return $tran;
	
	
	}


	private function execRequest($data,$url)
	{
		$options = array(
		    'http' => array(
		        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
		        'method'  => 'POST',
		        'content' => http_build_query($data),
		    ),
		);

		$context  = stream_context_create($options);
		$result = file_get_contents($url, false, $context);
		return json_decode($result,TRUE);
	}
	
	

}



class Transaction
{
	var $apikey;
	var $token;
	var $amount;
	var $orderId;
	var $customerEmail;
	var $passCode;
	var $transactionId;
	var $redeemCode;
	var $error;
	var $message;

}

?>