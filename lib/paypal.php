<?php
class paypal {
	//获取token的函数
	function SetExpressCheckout($params) {
		$token = '';
		$payAmount = urlencode($params['amount']);
		$currency = urlencode($params['currency']);
		$desc = urlencode($params['desc']);
		$returnURL = urlencode($params['return']);
		$cancelURL = urlencode($params['cancel']);
		$custom = urlencode($params['custom']);
		$invnum = urlencode($params['invnum']);
		$nvpstr = "&AMT=".$payAmount."&RETURNURL=".$returnURL."&CANCELURL=".$cancelURL."&CURRENCYCODE=".$currency."&DESC=".$desc."&CUSTOM=".$custom."&INVNUM=".$invnum;
		$resArray=self::makeCall("SetExpressCheckout", $nvpstr);
 
		if(array_key_exists('ACK', $resArray) AND strtoupper($resArray['ACK']) == 'SUCCESS') {
			if (array_key_exists("TOKEN",$resArray)) {
				$token = urldecode($resArray["TOKEN"]);
			}
			$payPalURL = PAYPAL_URL.$token;
			return $payPalURL;
		}else{
			return 'Payment Exception. Reason: '.$resArray['L_LONGMESSAGE0'];
		}
	}

	//获取客户信息 
	function GetExpressCheckoutDetails($params) {
		$token = urlencode($params['token']);
		$currency = urlencode($params['currency']);
		$nvpstr = "&TOKEN=".$token."&CURRENCYCODE=".$currency;
		$resArray = self::makeCall("GetExpressCheckoutDetails",$nvpstr);
		if(array_key_exists('ACK', $resArray) AND strtoupper($resArray['ACK']) == 'SUCCESS') {
			return $resArray;
		} else {
			return 'Payment Exception. Reason: '.$resArray['L_LONGMESSAGE0'];
		}
	}

	//确定执行交易
	function DoExpressCheckoutPayment($params) {
		$token = urlencode($params['token']);
		$payAmount = urlencode($params['payAmount']);
		$payType = urlencode($params['payType']);
		$payerID = urlencode($params['PayerID']);
		$currency = urlencode($params['currency']);
		$nvpstr = '&TOKEN='.$token.'&PAYERID='.$payerID.'&PAYMENTACTION='.$payType.'&AMT='.$payAmount.'&CURRENCYCODE='.$currency;
		$resArray = self::makeCall("DoExpressCheckoutPayment",$nvpstr);
		if(array_key_exists('ACK', $resArray) AND strtoupper($resArray['ACK']) == 'SUCCESS') {
			return $resArray;
		} else {
			return 'Payment Exception. Reason: '.$resArray['L_LONGMESSAGE0'];
		}
	}

	//退款处理
	function RefundTransaction($params) {
		$type = urlencode($params['type']);
		$transactionId = urlencode($params['transactionId']);
		$amount = urlencode($params['amount']);
		$currency = urlencode($params['currency']);
		$nvpstr = '&TRANSACTIONID='.$transactionId.'&REFUNDTYPE='.$type.'&CURRENCYCODE='.$currency;
		if($type == 'Full')
		$nvpstr .= '&AMT='.$amount;
		$resArray = self::makeCall("RefundTransaction", $nvpstr);
		if(array_key_exists('ACK', $resArray) AND strtoupper($resArray['ACK']) == 'SUCCESS') {
			return $resArray;
		} else {
			return 'Payment Exception. Reason: '.$resArray['L_LONGMESSAGE0'];
		}
	}

	//通过curl库来发送请求，被以上的函数调用
	function makeCall($methodName,$nvpStr) {
		$version = '82.0';
		$API_UserName = API_UserName;
		$API_Password = API_Password;
		$API_Signature = API_Signature;

		//$nvp_Header;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,API_ENDPOINT);
		curl_setopt($ch, CURLOPT_VERBOSE, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT,20);//10秒超时
 
		$nvpreq = "METHOD=".urlencode($methodName)."&VERSION=".urlencode($version)."&PWD=".urlencode($API_Password)."&USER=".urlencode($API_UserName)."&SIGNATURE=".urlencode($API_Signature).$nvpStr;
		
		writeLog($methodName.': '.$nvpreq);
		
		curl_setopt($ch, CURLOPT_POSTFIELDS, $nvpreq);
		$response = curl_exec($ch);
		$nvpResArray=self::deformatNVP($response);
		if (!$response) {
			//请求超时,没有数据返回 
			writeLog('USER_AGENT:'.$_SERVER['HTTP_USER_AGENT'].' IP:'.getIP().' URL:'.'http://'.$_SERVER['SERVER_NAME'].':'.$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"]." \r\nCOOKIE['cart']:".var_export($_COOKIE['cart'],true)." \r\nGET:".var_export($_GET,true)." \r\nPOST:".var_export($_POST,true),'error');
			exit('Sorry, the payment request failed, please try again later!');
		} else {
			curl_close($ch);
		}
		return $nvpResArray;
	}

	//格式化 
	function deformatNVP($nvpstr) {
		$intial = 0;
		$nvpArray = array();
		while(strlen($nvpstr)) {
			$keypos = strpos($nvpstr, '=');
			$valuepos = strpos($nvpstr,'&') ? strpos($nvpstr,'&') : strlen($nvpstr);
			$keyval = substr($nvpstr, $intial, $keypos);
			$valval = substr($nvpstr, $keypos+1, $valuepos-$keypos-1);
			$nvpArray[urldecode($keyval)] = urldecode($valval);
			$nvpstr = substr($nvpstr, $valuepos+1, strlen($nvpstr));
		}
		return $nvpArray;
	}
}
?>