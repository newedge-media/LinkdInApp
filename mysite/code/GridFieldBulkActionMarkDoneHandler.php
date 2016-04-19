<?php
class GridFieldBulkActionMarkDoneHandler extends GridFieldBulkActionHandler {
	
	private static $allowed_actions = array(
		'MarkDone'
	);

	private static $url_handlers = array(
		'MarkDone' => 'MarkDone'
	);	

	public function MarkDone(SS_HTTPRequest $request)	{
		$ids = array();

		foreach ( $this->getRecords() as $record ){
			array_push($ids, $record->ID);

			$record->Complete = true;
			$record->write();
		}

		$response = new SS_HTTPResponse(Convert::raw2json(array(
			'done' => true,
			'records' => $ids
		)));

		$response->addHeader('Content-Type', 'text/json');

		return $response;	
	}
}