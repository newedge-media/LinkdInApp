<?php
class ProspectMap extends DataObject {

	private static $db = array(
		'Title' => 'Text', 
		'Keyword' => 'Varchar', 
		'City' => 'Varchar', 
		'PostalCode' => 'Varchar', 
		'Industry' => 'Varchar', 
		'CompanySize' => 'Text', 
		'NumberOfProspects' => 'Int', 
		'SearchLink' => 'Text', 
		'Date' => 'Date', 
		'Status' => 'Enum("Not Started, In Progress, Complete", "Not Started")'
	);

	private static $has_one = array(
		'Member' => 'Member'
	);

	private static $singular_name = 'Research';

	private static $plural_name = 'Researches';

	private static $summary_fields = array(
		'Title' => 'Title', 
		'Keyword' => 'Keywords', 
		'City' => 'City', 
		'PostalCode' => 'Postal Code', 
		'Industry' => 'Industry', 
		'CompanySize' => 'Company Size', 
		'NumberOfProspects' => 'Number of Prospects', 
		'Status' => 'Status', 
		'Date' => 'Date', 
		'ButtonSearchLink' => ''
	);

	public function getCMSFields() {
		$fields = parent::getCMSFields();

		$fields->removeFieldsFromTab(
			'Root.Main', 
			array('MemberID')
		);

		$fields->replaceField(
			'Title', 
			TextField::create('Title', 'Title')
		);

		$fields->replaceField(
			'CompanySize', 
			TextField::create('CompanySize', 'Company Size')
		);

		$fields->dataFieldByName('Date')
			->setConfig('showcalendar', true)
			->setConfig('dateformat', 'dd/MM/yyyy');

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

	public function ButtonSearchLink() {
		return LiteralField::create('SearchLink', '<a href="' .$this->SearchLink. '" class="gfSearchLink ss-ui-action-constructive ss-ui-button ui-button ui-widget ui-state-default ui-corner-all" target="_blank">Search Link</a>');
	}
}