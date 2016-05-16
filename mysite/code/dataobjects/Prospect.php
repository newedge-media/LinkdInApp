<?php
class Prospect extends DataObject {

	private static $db = array(
		'Title' => 'Varchar', 
		'FirstName' => 'Varchar', 
		'Lastname' => 'Varchar', 
		'Email' => 'Varchar', 
		'Telephone' => 'Varchar', 
		'JobTitle' => 'Text', 
		'CompanyName' => 'Text', 
		'ProfileLink' => 'Text', 
		'MessageLink' => 'Text', 
		'Status' => 'Enum("Prospect, Client, Sales Lead, Do Not Contact, Not Interested, Hide", "Prospect")'
	);

	private static $has_one = array(
		'Group' => 'ProspectCampaign', 
		'Member' => 'Member'
	);

	private static $has_many = array(
		'ActionLists' => 'ProspectActionList'
	);

	private static $summary_fields=  array(
		'NameSummary' => 'Name', 
		'Email' => 'Email', 
		'Telephone' => 'Telephone', 
		'Status' => 'Status', 
		'Group.Title' => 'Campaign', 
		'NextMessageDate' => 'Next Message Date'
	);

	private static $searchable_fields = array(
		'FirstName' => 'First Name', 
		'Lastname' => 'Last Name', 
		'Email' => 'Email', 
		'Status' => 'Status'
	);

	private static $default_sort = 'Created DESC';

	public function getCMSFields() {
		$fields = parent::getCMSFields();

		$fields->removeFieldsFromTab(
			'Root.Main', 
			array('Title', 'MemberID')
		);

		$fields->dataFieldByName('Lastname')
			->setTitle('Last Name');
		$fields->replaceField(
			'JobTitle', 
			TextField::create('JobTitle', 'Job Title')
		);
		$fields->replaceField(
			'CompanyName', 
			TextField::create('CompanyName', 'Company Name')
		);
		$fields->replaceField(
			'ProfileLink', 
			TextField::create('ProfileLink', 'Profile Link')
		);
		$fields->replaceField(
			'MessageLink', 
			TextField::create('MessageLink', 'Message Link')
		);
		$fields->dataFieldByName('GroupID')
			->setTitle('Campaign')
			->setSource(ProspectCampaign::get()->filter(array('MemberID' => Member::currentUserID()))->map('ID', 'Title'));

		return $fields;
	}

	public function onBeforeWrite() {
		parent::onBeforeWrite();

		$this->Title = $this->FirstName . ' ' . $this->Lastname;

		if (!$this->ID) {
			$this->MemberID = Member::currentUserID();
		}

		if (!$this->GroupID) {
			$actionLists = ProspectActionList::get()->filter(array(
				'ProspectID' => $this->ID, 
				'Complete' => false, 
				'MemberID' => Member::currentUserID()
			));

			if ($actionLists->exists()) {
				foreach ($actionLists as $actionList) {
					$actionList->delete();
				}
			}
		}
	}

	public function onAfterWrite() {
		parent::onAfterWrite();

		if ($this->GroupID) {
			if ($this->Group()->Messages()->exists()) {
				foreach ($this->Group()->Messages() as $message) {
					$actionList = $message->ActionLists()->filter(array('ProspectID' => $this->ID, 'MessageID' => $message->ID));
					if (!$actionList->exists()) {
						$data = array(
							'{{firstname}}' => $this->FirstName, 
							'{{lastname}}' => $this->Lastname, 
							'{{email}}' => $this->Email
						);

						$actionList = new ProspectActionList();
						$actionList->MemberID = Member::currentUserID();
						$actionList->Title = $this->FirstName . ' ' . $this->Lastname;
						$actionList->ProspectID = $this->ID;
						$actionList->MessageID = $message->ID;
						$actionList->Email = $this->Email;
						$actionList->ProfileLink = $this->ProfileLink;
						$actionList->MessageLink = $this->MessageLink;
						$actionList->Content = str_replace(array_keys($data), array_values($data), $message->Content);
						$actionList->write();
					}
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

	public function NameSummary() {
		$title = "<strong>" . $this->FirstName . " " . $this->Lastname . "</strong>";

		if ($this->JobTitle && $this->CompanyName) {
			$title .= "<br>" . $this->JobTitle . " at " . $this->CompanyName;
		}

		if ($this->JobTitle && $this->CompanyName == "") {
			$title .= "<br>" . $this->JobTitle;
		}

		if ($this->CompanyName  && $this->JobTitle == "") {
			$title .= "<br>" . $this->CompanyName;
		}

		return DBField::create_field('HTMLText', $title);
	}

	public function NextMessageDate() {
		$actionLists = $this->ActionLists()->filter('Complete', false);
		if ($actionLists->exists()) {
			$messages = ProspectMessage::get()->byIds($actionLists->column('MessageID'))->sort('Action_Date ASC');
			if ($messages->exists()) {
				return DBField::create_field('Date', $messages->First()->Action_Date)->Nice();
			}
		}
	}	
}