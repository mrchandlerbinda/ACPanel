<?php

class CronParser
{
	private $path;
	private $delay;
	private $tasks = array();
	private $found = false;
	static public $mysql = NULL;
	static public $cfg = NULL;

	public function __construct($jobs, $cfg, $path, $mysql, $delay = 0)
	{
		$this->tasks = $jobs;
		$this->path = $path;
		$this->delay = $delay;
		self::$mysql = $mysql;
		self::$cfg = $cfg;

		$this->parseTask();
	}

	public function __destruct()
	{
		unset($this->tasks, $this->path, $this->delay, $this->found);
		self::$mysql = NULL;
		self::$cfg = NULL;
	}

	private function db()
	{
		$config = self::$cfg;

		if( is_null(self::$mysql) && isset($config['cron']['log']) && $config['cron']['log'] )
		{
			try {
				self::$mysql = new MySQL($config['hostname'],$config['username'],$config['password'],$config['dbname'],$config['charset_db']);
			} catch (Exception $e) {
				die($e->getMessage());
			}
		}

		return self::$mysql;
	}

	private function parseTask()
	{
		if( !empty($this->tasks) )
		{
			clearstatcache();
			if( file_exists($this->path . 'templates/_cache/cron.next') && ($taskNext = file_get_contents($this->path . 'templates/_cache/cron.next')) !== FALSE )
			{
				$taskNext = unserialize($taskNext);
			}
			else
			{
				$taskNext = array();
			}

			foreach( $this->tasks as $k => $oneTask )
			{
				$oneTask = (array)$oneTask;

				if( isset($taskNext[$oneTask['entry_id']]) && ($taskNext[$oneTask['entry_id']] + $this->delay) > time() )
					continue;

				if( $this->calculateNextRun($oneTask, $taskNext) )
				{
					$this->found = true;
					$this->runTask($oneTask);
				}
			}
		}
	}

	public function getFoundTask()
	{
		return $this->found;
	}

	private function runTask($t)
	{
		if( !$t['cron_file'] )
			return;

		$file = $this->path.'includes/cron/'.$t['cron_file'];
		clearstatcache();

		if( is_file($file) )
		{
			switch( substr($t['cron_file'], -4, 4) )
			{
				case ".php":
					$out = $this->runPHP($file);
					break;

				case ".xml":
					break;

				case ".sql":
					break;

				default:
					break;
			}
		}

		if( isset($out) )
		{
			if( !is_null($this->db()) )
			{
				$res = $this->db()->Query("INSERT INTO `acp_cron_log` SET entry_id = ".$t['entry_id'].", dateline = ".time(), array());
			}
		}
	}

	private function runPHP($filename)
	{
		ob_start();
		include $filename;
		$contents = ob_get_contents();
		ob_end_clean();
		return $contents;
	}

	private function runSQL($filename)
	{
		
	}

	private function runSH($filename)
	{
		
	}

	private function calculateNextRun($task, &$taskNext)
	{
		$nextRun = (isset($taskNext[$task['entry_id']])) ? $taskNext[$task['entry_id']] : time();
		$nextRun++;

		list($seconds, $minutes, $hours, $days, $months, $years) = unserialize($task['run_rules']);

		$arrT = array('seconds'=>'s', 'minutes'=>'i', 'hours'=>'H', 'days'=>'d', 'months'=>'m', 'years'=>'Y');
		foreach( $arrT as $time => $identifier )
		{
			if( $$time != '*' )
			{
				while( $$time != gmdate($identifier, $nextRun) )
				{
					switch( $identifier )
					{
						case 's' : $nextRun += 1; break;
						case 'i' : $nextRun += (60*1); break;
						case 'H' : $nextRun += (60*60); break;
						case 'd' : 
						case 'm' : 
						case 'Y' : $nextRun += (60*60*24); break;
					}
				}
			}
		}

		$taskNext[$task['entry_id']] = (($minus = $nextRun - time()) < 0) ? $nextRun - $minus : $nextRun;

		return (file_put_contents($this->path . 'templates/_cache/cron.next', serialize($taskNext), LOCK_EX)) ? true : false;
	}
}

?>