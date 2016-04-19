<?php
class ProspectActionList extends DataObject {

	private static $db = array(
		'Title' => 'Text', 
		'Content' => 'Text', 
		'Complete' => 'Boolean'
	);

	private static $has_one = array(
		'Message' => 'ProspectMessage', 
		'Prospect' => 'Prospect', 
		'Member' => 'Member'
	);

	private static $singular_name = 'Action List';

	private static $plural_name = 'Action Lists';

	private static $summary_fields = array(
		'Message.Title' => 'Message Title', 
		//'ButtonMessageLink' => 'Message Link',
		'Prospect.Title' => 'Prospect', 
		'Prospect.Email' => 'Email', 
		'Prospect.Telephone' => 'Telephone'
		//'Message.ActionDate' => 'Action Date', 
	);

	public function getCMSFields() {
		$fields = parent::getCMSFields();

		$fields->removeFieldsFromTab(
			'Root.Main', 
			array('Title', 'MemberID', 'MessageID', 'Complete', 'ProspectID')
		);

		$fields->insertBefore(
			LiteralField::create('Prospect', '<p><strong>Prospect</strong><br>' . $this->Prospect()->Title . '</p>'), 
			'Content'
		);

		$fields->insertAfter(
			LiteralField::create('Email', '<p><strong>Email</strong><br><a href="mailto:' .$this->Prospect()->Email. '">' . $this->Prospect()->Email . '</a></p>'), 
			'Prospect'
		);

		$fields->insertAfter(
			LiteralField::create('Telephone', '<p><strong>Telephone</strong><br>' . $this->Prospect()->Telephone . '</p>'),
			'Email'
		);

		$fields->insertAfter(
			LiteralField::create('ProfileLink', '<p><strong>Profile Link</strong><br><a href="' .$this->Prospect()->ProfileLink. '" target="blank">' . $this->Prospect()->ProfileLink . '</a></p>'), 
			'Telephone'
		);

		$fields->insertAfter(
			LiteralField::create('MessageLink', '<p><strong>Message Link</strong><br><a href="' .$this->Prospect()->MessageLink. '" target="blank">' . $this->Prospect()->MessageLink . '</a></p>'), 
			'ProfileLink'
		);

		$fields->insertAfter(
			LiteralField::create('Content', '<textarea rows="20" cols="100" style="padding:10px;">' . $this->Content . '</textarea>'), 
			'MessageLink'
		);

		return $fields;
	}

	public function canCreate($member = null) {
		return false;
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

	/*public function ButtonMessageLink() {
		if ($this->Prospect()->MessageLink) {
			return LiteralField::create('MessageLink', '<a href="' .$this->Prospect()->MessageLink. '" class="gfMessageLink ss-ui-button ui-button ui-widget ui-state-default ui-corner-all" target="_blank">Msg Link</a>');
		}

		return null;
	}*/

}