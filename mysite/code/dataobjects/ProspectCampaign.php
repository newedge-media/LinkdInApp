<?php
class ProspectCampaign extends DataObject {
	
	private static $db = array(
		'Title' => 'Varchar'
	);

	private static $has_one = array(
		'Member' => 'Member'
	);

	private static $has_many = array(
		'Prospects' => 'Prospect', 
		'Messages' => 'ProspectMessage'
	);

	private static $summary_fields = array(
		'Title' => 'Title', 
		'NumberOfProspects' => 'Number of prospects', 
		'NextMessageTitle' => 'Next Message'
	);

	private static $singular_name = 'Campaign';

	private static $plural_name = 'Campaigns';

	public function getCMSFields() {
		$fields = parent::getCMSFields();

		$fields->removeFieldsFromTab(
			'Root.Main', 
			array('MemberID')
		);

		if (!$this->ID) {
			$fields->fieldByName('Root')
				->fieldByName('Main')
				->setTitle('Settings');
		} else {
			$fields->removeByName('Prospects');
			$fields->fieldByName('Root')
				->fieldByName('Main')
				->setTitle('Prospects');

			$fields->addFieldToTab(
				'Root.Main', 
				GridField::create(
					'Prospects', 
					'Prospects', 
					$this->Prospects()->filter(array('Status:not' => 'Hide')), 
					GridFieldConfig_RelationEditor::create()
						->addComponents(
							new Milkyway\SS\GridFieldUtils\AddExistingPicker('buttons-before-left'),
			                new GridFieldMessageLinkAction(),
			                new GridFieldProfileLinkAction()							
						)
				)
			);

			$fields->addFieldToTab(
				'Root.Settings', 
				$fields->dataFieldByName('Title')
					->setTitle('Campaign Title')
			);
		}

		return $fields;
	}

	public function onBeforeWrite() {
		parent::onBeforeWrite();

		if (!$this->ID) {
			$this->MemberID = Member::currentUserID();
		}
	}

	public function canCreate($member = NULL) {
		return true;
	}

	public function canEdit($member = NULL) {
		return true;
	}

	public function canView($member = NULL) {
		return true;
	}	

	public function canDelete($member = NULL) {
		return true;
	}

	public function NumberOfProspects() {
		return $this->Prospects()->Count();
	}

	public function NextMessageTitle() {
		$messages = $this->Messages()->sort('Action_Date ASC');
		if ($messages->exists()) {
			foreach ($messages as $message) {
				$notCompletedActions = $message->ActionLists()->filter('ActionLists.Complete', false);
				if ($notCompletedActions->exists()) {
					foreach ($notCompletedActions as $action) {
						return $action->Message()->Title;
					}
				}
			}
		}
	}
}