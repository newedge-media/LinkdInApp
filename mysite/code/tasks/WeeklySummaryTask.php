<?php
/**
 * Runs everyday that sends the member the number of actions for today
 * 
 * @author Julius Caamic <julius.caamic@yahoo.com>
 * @copyright Copyright (c) 2016, Julius Caamic
 */
class WeeklySummaryTask extends BuildTask {

	/**
	 * Set the title of the task
	 * 
	 * @var string
	 */
	protected $title = 'Weekly Summary';

	/**
	 * Set the description of the task
	 * 
	 * @var string
	 */
	protected $description = 'Get the summary of action list items this week, number of messages sent, action list items remaining and number of actions for next week';

	/**
	 * Set enabled to true
	 * 
	 * @var boolean
	 */
	protected $enabled = true;

	/**
	 * Run the checking for renewals
	 * 
	 * @param  SS_HTTPRequest $request
	 */
	public function run($request) {
	}
}