<?php
class ProspectMessage extends DataObject {

	private static $db = array(
		'Title' => 'Text', 
		'Action_Date' => 'Date', 
		'Type' => 'Enum(array("LinkedIn Message", "Facebook Message", "Email", "Phone Call", "Appointment"), "LinkedIn Message")', 
		'Content' => 'Text' 
	);

	private static $has_one = array(
		'Group' => 'ProspectCampaign', 
		'Playbook' => 'ProspectMessage_Playbook', 
		'Member' => 'Member'
	);

	private static $has_many = array(
		'ActionLists' => 'ProspectActionList'
	);

	private static $singular_name = 'Message';

	private static $plural_name = 'Messages';

	private static $summary_fields = array(
		'Title' => 'Title', 
		'Type' => 'Type', 
		'ActionDate' => 'Action Date'
	);

	public function getCMSFields() {
		$fields = parent::getCMSFields();

		$fields->removeFieldsFromTab(
			'Root.Main', 
			array('GroupID', 'MemberID', 'PlaybookID')
		);

		$fields->replaceField(
			'Title', 
			TextField::create('Title', 'Title')
		);

		$fields->dataFieldByName('Content')
			->setRows(20);

		$fields->removeFieldsFromTab(
			'Root.Main', 
			array('GroupID')
		);

		$fields->dataFieldByName('Action_Date')
			->setConfig('showcalendar', true)
			->setConfig('dateformat', 'dd/MM/yyyy')
			->setTitle('Action Date');

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

		$fields->insertBefore(
			DropdownField::create(
				'PlaybookTemplate', 
				'Use a playbook template?', 
				ProspectMessage_Playbook::get()->filter(array('MemberID' => Member::currentUserID()))->map('ID', 'Title')
			)->setEmptyString('select one'), 
			'Title'
		);


		$fields->insertAfter(
			$fields->dataFieldByName('Action_Date'), 
			'Content'
		);
		$fields->insertAfter(
			$fields->dataFieldByName('Type'), 
			'Action_Date'
		);

		return $fields;
	}

	public function onBeforeWrite() {
		parent::onBeforeWrite();

		if (!$this->ID) {
			$this->MemberID = Member::currentUserID();
		}

		if ($this->PlaybookTemplate) {
			$playbook = ProspectMessage_Playbook::get()->byID($this->PlaybookTemplate);
			if ($playbook) {
				$this->Title = $playbook->Title;
				$this->Content = $playbook->Content;
			}
		}
	}

	public function onAfterWrite() {
		parent::onAfterWrite();

		$prospects = $this->Group()->Prospects();
		if ($prospects->exists()) {
			foreach ($prospects as $prospect) {
				$currentActionList = ProspectActionList::get()->filter(array(
					'ProspectID' => $prospect->ID, 
					'MessageID' => $this->ID
				));

				if (!$currentActionList->exists()) {
					$data = array(
						'{{firstname}}' => $prospect->FirstName, 
						'{{lastname}}' => $prospect->Lastname, 
						'{{email}}' => $prospect->Email
					);

					$actionList = new ProspectActionList();
					$actionList->MemberID = Member::currentUserID();
					$actionList->Title = $prospect->FirstName . ' ' . $prospect->Lastname;
					$actionList->ProspectID = $prospect->ID;
					$actionList->MessageID = $this->ID;
					$actionList->Email = $prospect->Email;
					$actionList->ProfileLink = $prospect->ProfileLink;
					$actionList->MessageLink = $prospect->MessageLink;
					$actionList->Content = str_replace(array_keys($data), array_values($data), $this->Content);
					$actionList->write();
				} else {

					$actionList = $currentActionList->First();
					$data = array(
						'{{firstname}}' => $prospect->FirstName, 
						'{{lastname}}' => $prospect->Lastname, 
						'{{email}}' => $prospect->Email
					);

					$actionList->Content = str_replace(array_keys($data), array_values($data), $this->Content);
					$actionList->write();
				}
			}
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

	public function ActionDate() {
		return DBField::create_field('Date', $this->Action_Date)->Format('d/m/Y');
	}
}