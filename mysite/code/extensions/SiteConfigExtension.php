<?php
class SiteConfig_Extension extends DataExtension {

	/*private static $db = array(
		'TurnOnDailyActionList' => 'Boolean', 
		'TurnOnWeeklySummary' => 'Boolean'
	);

	public function updateCMSFields(FieldList $fields) {
		$fields->removeByName('Main');
		$fields->removeByName('Access');

		$fields->addFieldToTab(
			'Root.Notifications', 
			HeaderField::create('Turn on/off email notifications')
		);
		$fields->addFieldToTab(
			'Root.Notifications', 
			CheckboxField::create(
				'TurnOnDailyActionList', 
				'Daily Action List '
			)
		);

		$fields->addFieldToTab(
			'Root.Notifications', 
			CheckboxField::create(
				'TurnOnWeeklySummary', 
				'Weekly Summary '
			)
		);
	}*/
}