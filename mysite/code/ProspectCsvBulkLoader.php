<?php
class ProspectsCsvBulkLoader extends CsvBulkLoader {

	public $columnMap = array(
		'First Name' => 'FirstName', 
		'Last Name' => 'Lastname', 
		'E-mail Address' => 'Email', 
		'Job Title' => 'JobTitle', 
		'Company' => 'CompanyName'
	);

	public $duplicateChecks = array(
		'Email' => 'Email'
	);
}