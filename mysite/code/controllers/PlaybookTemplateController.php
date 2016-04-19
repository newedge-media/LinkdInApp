<?php
class PlaybookTemplateController extends Controller {

	private static $allowed_actions= array(
		'GetTemplate'
	);

	public function init() {
		parent::init();

		if (!Member::currentUser()) {
			return Security::permissionFailure($this);
		}		
	}

	public function GetTemplate(SS_HTTPRequest $request) {
		$id = Convert::raw2sql($request->param('ID'));
		$template = ProspectMessage_Playbook::get()->byID($id);
		if ($template) {
			return json_encode(array(
				'success' => true, 
				'title' => $template->Title,
				'content' => $template->Content
			));
		}

		return json_encode(array(
			'success' => false, 
			'title' => '',
			'content' => ''
		));
	}
}