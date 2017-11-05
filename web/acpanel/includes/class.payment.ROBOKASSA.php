<?php

class ROBOKASSA
{
	static public $mysql = NULL;
	private $error_message = "";

	public function __construct($mysql)
	{
		self::$mysql = $mysql;
	}

	private function SetErrorInfo($string = "")
	{
		$this->error_message = $string;
	}

	public function GetErrorInfo()
	{
		return $this->error_message;
	}

	public function createPayment($args)
	{
		if( !is_array($args) )
		{
			$this->SetErrorInfo("Can not create payment. Please contact site administrator for further assistance[1].");
			return FALSE;
		}

		$args['created'] = time();

		$required_params = array('uid', 'created', 'amount', 'memo');
		$additional_params = array();
		foreach( $args as $key => $value )
		{
			if( !in_array($key, $required_params) )
			{
				$additional_params[$key] = $value;
			}
		}
		$additional_params_ar = $additional_params;
		$additional_params = serialize($additional_params);

		if( empty($args['uid']) )
		{
			$this->SetErrorInfo("Can not create payment. User ID not set. uid: ".$args['uid']);
			return FALSE;
		}

		$args['amount'] = (float) $args['amount'];
		if( $args['amount'] <= 0 )
		{
			$this->SetErrorInfo("Invalid amount.");
			return FALSE;
		}
		if( !preg_match("/^[0-9]{1,10}$/", $args['amount']) && !preg_match("/^[0-9]{1,6}\.[0-9]{1,6}$/", $args['amount']) )
		{
			$this->SetErrorInfo("Invalid amount.");
			return FALSE;
		}

		$args['memo'] = substr($args['memo'], 0, 255);

		// INSERTING PAYMENT
		$db = self::$mysql;
		$arguments = array('uid' => $args['uid'], 'amount' => $args['amount'], 'created' => $args['created'], 'memo' => $args['memo'], 'params' => $additional_params);
		$query = $db->Query("INSERT INTO `acp_payment` (uid, amount, created, memo, params) VALUES ('{uid}', '{amount}', '{created}', '{memo}', '{params}')", $arguments);
		$pid = $db->LastInsertID();

		if( $pid <= 0 )
		{
			$this->SetErrorInfo("Can not create payment. Please contact site administrator for further assistance [4].");
			return FALSE;
		}

		$payment = array(
			'pid' => $pid,
			'uid' => $args['uid'],
			'created' => $args['created'],
			'amount' => $args['amount'],
			'memo' => $args['memo']
		);
		$payment = array_merge($additional_params_ar, $payment);

		return $payment;
	}

	public function deletePayment($pid)
	{
		$db = self::$mysql;
		$pid = (int)$pid;
		if( $pid > 0 && $query = $db->Query("DELETE FROM `acp_payment` WHERE pid = '{pid}'", array('pid' => $pid)) )
			return TRUE;

		return FALSE;
	}

	public function pidLoad($pid)
	{
		$db = self::$mysql;
		$pid = (int)$pid;
		if( $pid > 0 )
		{
			$query = $db->Query("SELECT pid, uid, created, amount, memo, enrolled, params FROM `acp_payment` WHERE pid = '{pid}'", array('pid' => $pid));
			if( is_array($query) )
			{
				foreach($query as $obj)
				{
					$result = (array)$obj;
					$result = array_merge(unserialize($obj->params), $result);
					return $result;
				}
			}
		}

		return FALSE;
	}

	public function enrollPayment($pid, $t = "")
	{
		$db = self::$mysql;
		$pid = (int)$pid;
		if( empty($t) )
			$t = time();

		if( $pid > 0 && $query = $db->Query("UPDATE `acp_payment` SET enrolled = '{time}' WHERE pid = '{pid}'", array('pid' => $pid, 'time' => $t)) )
			return TRUE;

		return FALSE;
	}
}