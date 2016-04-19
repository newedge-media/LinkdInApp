<?php
class ProspectMessage_Playbook extends DataObject {

	private static $db = array(
		'Title' => 'Varchar', 
		'Content' => 'Text'
	);

	private static $has_one = array(
		'Member' => 'Member'
	);

	private static $has_many = array(
		'Messages' => 'ProspectMessage'
	);

	private static $singular_name = 'Playbook';

	private static $plural_name = 'Playbooks';

	public function getCMSFields() {
		$fields = parent::getCMSFields();

		$fields->removeByName('Messages');
		$fields->removeFieldsFromTab(
			'Root.Main', 
			array('MemberID')
		);

		$fields->dataFieldByName('Title')
			->setTitle('Playbook Title');
		$fields->dataFieldByName('Content')
			->setTitle('Message Content')
			->setRows(20);
		$fields->addFieldToTab(
			'Root.Main', 
			ToggleCompositeField::create('MessageContentVariablesContainer', 'Variables Guide',
				array(
					LiteralField::create(
						'MessageContentVariables', 
						'<div style="padding:10px;">
							<p>Below are the variables that can be used to the message content:</p>
							<ul style="list-style: circle; margin-left:10px;">
								<li style="margin-bottom:5px;">Use <strong>{{firstname}}</strong> to get the First Name of the prospect.</li>
								<li style="margin-bottom:5px;">Use <strong>{{lastname}}</strong> to get the Last Name of the prospect.</li>
								<li style="margin-bottom:5px;">Use <strong>{{email}}</strong> to get the email of the prospect.</li>
							</ul>
						</div>'
					)
				)
			)->setHeadingLevel(4)
		);
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
}