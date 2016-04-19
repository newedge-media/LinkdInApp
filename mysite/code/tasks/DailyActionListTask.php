<?php
/**
 * Runs everyday that sends the member the number of actions for today
 * 
 * @author Julius Caamic <julius.caamic@yahoo.com>
 * @copyright Copyright (c) 2016, Julius Caamic
 */
class DailyActionListTask extends BuildTask {

	/**
	 * Set the title of the task
	 * 
	 * @var string
	 */
	protected $title = 'Daily Action List';

	/**
	 * Set the description of the task
	 * 
	 * @var string
	 */
	protected $description = 'Get number of actions for today sent via email to the member';

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