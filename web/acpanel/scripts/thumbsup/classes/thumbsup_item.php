<?php

if(!defined('IN_ACP')) die("Hacking attempt!");

class ThumbsUp_Item extends ThumbsUp {

	/**
	 * @var  string  The template to use for rendering the item.
	 */
	protected $template;

	/**
	 * @var  array  Item options (also passed to the template).
	 */
	protected $options;

	/**
	 * @var  integer  (Database field) Item id.
	 */
	public $id;

	/**
	 * @var  string  (Database field) Item name.
	 */
	public $name;

	/**
	 * @var  integer  (Database field) Timestamp of the creation date of the item.
	 */
	public $date;

	/**
	 * @var  boolean  (Database field) Is voting on this item closed or not?
	 */
	public $closed;

	/**
	 * @var  integer  (Database field) The number of "up" votes for this item.
	 */
	public $votes_up;

	/**
	 * @var  integer  (Database field) The number of "down" votes for this item.
	 */
	public $votes_down;

	/**
	 * @var  integer  The total number of votes cast for this item.
	 */
	public $votes_total;

	/**
	 * @var  integer  The votes balance for this item ("up" votes minus "down" votes)
	 */
	public $votes_balance;

	/**
	 * @var  integer  The percentage of all the votes for this item that are "up" votes.
	 */
	public $votes_pct_up;

	/**
	 * @var  integer  The percentage of all the votes for this item that are "down" votes.
	 */
	public $votes_pct_down;

	/**
	 * @var  boolean  Has the current user already cast a vote on this item or not?
	 */
	public $user_voted;

	/**
	 * @var  string  String with the format used to render the result.
	 */
	public $format;

	/**
	 * @var  array  A nicely formatted result based on the given format.
	 */
	public $result;

	/**
	 * Sets up a ThumbsUp item object.
	 *
	 * This constructor is protected which will prevent direct creation of an object.
	 *
	 * @return  void
	 */
	public function __construct()
	{
		// Use the default template... by default :-)
		$this->template = "mini_thumbs";

		// Also initialize all options (for all templates) with a default value
		$this->options = array(
			'align'       => 'center',
			'question'    => 'And you?',
			'up'          => 'Yes',
			'down'        => 'No',
			'color_up'    => '#ccc',
			'color_down'  => '#ccc',
		);
	}

	/**
	 * Loads an existing ThumbsUp item.
	 *
	 * @param   mixed  item name or id
	 * @return  mixed  ThumbsUp_Item object if the item could be found, FALSE otherwise
	 */
	public static function load($name)
	{
		// Are we loading by id or by name?
		$key = (is_numeric($name)) ? 'id' : 'address';

		// Load the item
		$sth = ThumbsUp::db()->Query("SELECT id, address, timestamp, active, votes_up, votes_down FROM `acp_servers` WHERE ".$key." = '{key}' LIMIT 1", array('key'=>$name));

		// Fetch the item record if it was found
		if( !is_array($sth) )
		{
			return FALSE;
		}

		// Setup the item object
		$item = new self;

		// Store the item values as properties
		foreach( $sth as $obj )
		{
			$item->id = (int)$obj->id;
			$item->name = $obj->address;
			$item->date = (int)$obj->timestamp;
			$item->closed = !(bool)$obj->active;
			$item->votes_up = (int)$obj->votes_up;
			$item->votes_down = (int)$obj->votes_down;
		}

		// Calculate the vote results
		$item->calculate_votes();

		// Initial default value
		$item->user_voted = FALSE;

		// Check cookie for a vote
		if( ThumbsUp_Cookie::find_id($item->id) )
		{
			$item->user_voted = TRUE;
		}

		if( !$item->user_voted )
		{
			$sql = $sql_ip = $sql_user = "";
			if( ThumbsUp::config('mon_vote_multiple') )
				$sql = " AND item_id = '{item_id}'";

			if( $ip = ThumbsUp::get_ip() )
			{
				$sql_ip = "(ip = '{ip}'".$sql.")";
			}

			if( $user_id = ThumbsUp::get_user_id() )
			{
				$sql_user = ( ($sql_ip) ? " OR " : "" )."(user_id = '{user_id}'".$sql.")";
			}

			$sql = $sql_ip.$sql_user;

			if( $sql )
			{
				$current_time = time() - ( 60 * ThumbsUp::config('mon_vote_lifetime') );

				if( $date = ThumbsUp::db()->Query("SELECT date FROM `acp_servers_votes` WHERE date > ".$current_time." AND (".$sql.") ORDER BY date DESC LIMIT 1", array('item_id'=>$item->id,'ip'=>$ip,'user_id'=>$user_id)) )
				{
					$item->user_voted = TRUE;
				}
			}
		}

		return $item;
	}

	public function cast_vote($vote, $num = 1)
	{
		// Vote value must be either 0 or 1
		$vote = min(1, max(0, (int) $vote));

		if( $vote )
		{
			// Add an "up" vote
			$this->votes_up += $num;
			$sql = 'votes_up = votes_up + '.$num;
			$vote_value = $num;
		}
		else
		{
			// Add a "down" vote
			$this->votes_down += $num;
			$sql = 'votes_down = votes_down + '.$num;
			$vote_value = "-".$num;
		}

		// Recalculate the vote results, no need to reload the item from database
		$this->calculate_votes();

		// Update the item record
		$sth = ThumbsUp::db()->Query("UPDATE `acp_servers` SET ".$sql." WHERE id = '{server}'", array('server'=>$this->id));

		// The current user has just cast a vote
		$this->user_voted = TRUE;
		ThumbsUp_Cookie::add_id($this->id);

		// Combine the storage of the IP and user id into one query for optimization
		$ip = ThumbsUp::get_ip();
		$user_id = ThumbsUp::get_user_id();

		if( $ip OR $user_id )
		{
			$sth = ThumbsUp::db()->Query("INSERT INTO `acp_servers_votes` (item_id, ip, user_id, value, vote_value, date) VALUES ('{item_id}', '{ip}', '{user_id}', '{value}', '{vote_value}', '{date}')", array('item_id'=>$this->id,'ip'=>$ip,'user_id'=>$user_id,'value'=>$vote,'vote_value'=>$vote_value,'date'=>time()));
		}
	}

	/**
	 * Calculates the vote results based on the current votes_up and votes_down values.
	 *
	 * @return  void
	 */
	public function calculate_votes()
	{
		$this->votes_total = $this->votes_up + $this->votes_down;
		$this->votes_balance = $this->votes_up - $this->votes_down;

		// Note: division by zero must be prevented
		$this->votes_pct_up = ($this->votes_total === 0) ? 0 : $this->votes_up / $this->votes_total * 100;
		$this->votes_pct_down = ($this->votes_total === 0) ? 0 : $this->votes_down / $this->votes_total * 100;
	}

	/**
	 * Sets the template to use for rendering the item.
	 *
	 * @param   string  template name
	 * @return  object  ThumbsUp_Item
	 */
	public function template($template = NULL)
	{
		// No template name provided
		if ($template === NULL)
			return $this;

		// We are a bit flexible in the names we accept
		$this->template = str_replace(array('-', ' '), '_', strtolower(trim($template)));

		// Chainable method
		return $this;
	}

	/**
	 * Sets options for this item. New options will be merged with existing options.
	 *
	 * @param   mixed   item options (as array or query string)
	 * @return  object  ThumbsUp_Item
	 */
	public function options($options = NULL)
	{
		// No options provided
		if ($options === NULL)
			return $this;

		// Convert a query string to an array
		if (is_string($options))
		{
			parse_str($options, $options);
		}

		// Store and merge the item options in the object
		$this->options = (array) $options + (array) $this->options;

		// Chainable method
		return $this;
	}

	/**
	 * Generates nicely formatted results for each result area.
	 *
	 * @param   string  format string
	 * @return  object  ThumbsUp_Item
	 */
	public function format($format = NULL)
	{
		if ($format === NULL)
			return $this;

		$this->format = (string) $format;

		// Update the result for this item
		$this->result = preg_replace_callback(
			'/\{([+-])?+(up|down|total|balance|pct_(?:up|down))(?:([.,])(\d++))?\}/i',
			array($this, 'format_callback'),
			$this->format
		);

		// Split into different result areas separated by "||"
		$this->result = preg_split('/\s*\|\|\s*/', $this->result);

		// Chainable method
		return $this;
	}

	/**
	 * Used by the format() method to format numbers.
	 *
	 * @param   array   preg_replace_callback matches
	 * @return  string  a formatted number
	 */
	protected function format_callback($matches)
	{
		// Load the correct number to display
		$property = 'votes_'.strtolower($matches[2]);
		$number = $this->$property;

		// Decimals need to be added
		if ( ! empty($matches[4]))
		{
			// $matches[4] contains the number of desired decimals.
			// $matches[3] contains the decimal separator (dot or comma).
			$number = number_format($number, $matches[4], $matches[3], '');
		}
		// No decimals wanted
		else
		{
			$number = (int) round($number);
		}

		// Prepend a "+" or "-" sign if the result is greater than zero.
		// Note: if the property is lower than zero, a "-" sign is already prepended automatically.
		if ( ! empty($matches[1]) AND $this->$property > 0)
		{
			$number = $matches[1].$number;
		}

		return (string) $number;
	}

	/**
	 * Returns the rendered HTML template.
	 *
	 * @return  string  the item rendered in HTML
	 */
	public function render()
	{
		// The formatted result has not been generated yet
		if( empty($this->result) )
		{
			// Generate the result using the default format for the chosen template
			$this->format('{+BALANCE}');
		}

		// Prepare item data for template
		$item = get_object_vars($this);
		unset($item['template'], $item['options']);

		// Load the chosen template
		$template = new ThumbsUp_Template(THUMBSUP_DOCROOT.'templates/'.$this->template.'.php');

		// Pass on all item data to the template
		$template
			->set('item', (object) $item)
			->set('template', $this->template)
			->set('options', (object) $this->options);

		// Render the template output
		return $template->render();
	}

	/**
	 * Magic method to convert to item to a string.
	 *
	 * @return  string  the item rendered in HTML
	 */
	public function __toString()
	{
		return $this->render();
	}
}