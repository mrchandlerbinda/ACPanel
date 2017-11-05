<?php defined('THUMBSUP_DOCROOT') or exit('No direct script access');

?>

<form method="post" id="thumbsup_<?php echo $item->id ?>" class="thumbsup <?php echo $template ?> <?php if ($item->closed) echo 'closed' ?> <?php if ($item->user_voted) echo 'user_voted' ?> <?php if ($item->closed OR $item->user_voted) echo 'disabled' ?> <?php echo $options->align ?>" name="<?php echo $template ?>">
	<input type="hidden" name="thumbsup_id" value="<?php echo $item->id ?>" />
	<input type="hidden" name="thumbsup_format" value="<?php echo htmlspecialchars($item->format) ?>" />

	<strong class="result1 error"><?php echo htmlspecialchars($item->result[0]) ?></strong>

	<input class="up"   name="thumbsup_vote" type="submit" value="+1" title="@@vote_up@@"   <?php if ($item->closed OR $item->user_voted) echo 'disabled="disabled"' ?> />
	<input class="down" name="thumbsup_vote" type="submit" value="-1" title="@@vote_down@@" <?php if ($item->closed OR $item->user_voted) echo 'disabled="disabled"' ?> />
</form>
