<?php
	
	require'API.php';
 	$api = new BTCLendMerchantAPI();
 	if (!isset($_GET['action'])) {

	 	// Collect Order Info
	    //$newOrder = $api->initTObject($_POST["amount"],$_POST["orderId"],$_POST["customerEmail"]);
	    $newOrder = $api->initTObject("0.000","0000000","JohnDoe@example.com");

	    // Get token (changes every hour, so you could save it instead of requesting every single time)
	    $newOrder = $api->requestNewToken($newOrder); 

	    if ($newOrder->error === 0)
	    {
	    	$newOrder = $api->createNewOrder($newOrder); 
	    	echo $newOrder->error;
	    	if ($newOrder->error === 5)
	    	{
	    	 	$_SESSION["amount"] =  $newOrder->amount;
				$_SESSION["orderId"] = $newOrder->orderId;
				$_SESSION["customerEmail"] =  $newOrder->customerEmail;
				$_SESSION["passCode"] =  $newOrder->passCode;
				$_SESSION["transactionId"] =  $newOrder->transactionId;
				$_SESSION["apikey"] =  $newOrder->apikey;
				$_SESSION["token"] =  $newOrder->token;
				header('Location: index.php?action=confirm');

	    	}
	    	else
	    	{
	    		echo "Error: " . $newOrder->message;
	    	}
	    }
	    else
	    	{
	    		echo "Error: " . $newOrder->message;
	    	}
	    
    }else{

    	if($_GET['action'] =="confirm")
    	{	
    	
    		echo "<form action='index.php' method='get'> confirm passcode <input type='text' name='passCode' value='passcode'><input type='hidden' name='action' value='submit'><input type='submit'></form>";
    	
    	}elseif ($_GET['action'] =="submit") {	

    		$TXSubmit = new Transaction();
			$TXSubmit->amount = $_SESSION["amount"] ;
		    $TXSubmit->orderId = $_SESSION["orderId"];
		    $TXSubmit->customerEmail = $_SESSION["customerEmail"];
		    $TXSubmit->passCode = $_GET["passCode"];
		    $TXSubmit->transactionId = $_SESSION["transactionId"];
		    $TXSubmit->apikey = $_SESSION["apikey"];
		    $TXSubmit->token = $_SESSION["token"];
    		$TXSubmit = $api->confirmOrder($TXSubmit); 

    		if ($TXSubmit->error === 9)
	    	{
    			echo "<br/>Redeem Code: " . $TXSubmit->redeemCode;
    		}
    		else
    		{
    			echo "Error: " . $TXSubmit->message;
    		}
    	
    	}

    }
?>