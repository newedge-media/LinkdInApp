<?php
class Member_Extension extends DataExtension {

	private static $db = array(
		'TurnOnDailyActionList' => 'Boolean', 
		'TurnOnWeeklySummary' => 'Boolean'
	);

	private static $has_many = array(
		'Prospects' => 'Prospect', 
		'Campaigns' => 'ProspectCampaign', 
		'Messages' => 'ProspectMessage', 
		'Playbooks' => 'ProspectMessage_Playbook', 
		'ActionLists' => 'ProspectActionList'
	);

	public function updateCMSFields(FieldList $fields) {
		if (!Permission::check('ADMIN')) {
			$fields->removeByName('Prospects');
			$fields->removeByName('Campaigns');
			$fields->removeByName('Messages');
			$fields->removeByName('Playbooks');
			$fields->removeByName('ActionLists');
			$fields->removeFieldsFromTab(
				'Root.Main', 
				array('LastVisited', 'Locale', 'FailedLoginCount', 'HasConfiguredDashboard', 'DateFormat', 'TimeFormat')
			);
			$fields->addFieldsToTab(
				'Root.Notifications', 
				$fields->dataFieldByName('TurnOnDailyActionList')
			);

			$fields->addFieldsToTab(
				'Root.Notifications', 
				$fields->dataFieldByName('TurnOnWeeklySummary')
			);
		}
	}

	public function onAfterWrite() {
		parent::onAfterWrite();
	}
}