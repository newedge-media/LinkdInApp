<?php
class ProspectSettingAdmin extends ModelAdmin {

    private static $url_segment = 'customer-profile';

    private static $menu_title = 'Settings';

    private static $menu_icon = 'mysite/images/three-icon.png';

    private static $managed_models = array('Member');

	public function getEditForm($id = null, $fields = null) {

		$member = Member::currentUser();
		$fields = $member->getCMSFields();

		$fields->fieldByName('Root')
			->fieldByName('Main')
			->setTitle('Profile');

		//$passwordField = $fields->dataFieldByName('Password');
		//$passwordField->showOnClick = false;

		// Tell the CMS what URL the preview should show
		$home = Director::absoluteBaseURL();
		$fields->push(new HiddenField('PreviewURL', 'Preview URL', $home));

		$actions= new FieldList(
			FormAction::create('saveMemberSettings')
				->setTitle('Save')
				->setUseButtonTag(true)
				->addExtraClass('ss-ui-action-constructive')
		);

		$form = CMSForm::create($this, 'EditForm', $fields, $actions, null)
			->setHTMLID('Form_EditForm');
		$form->setResponseNegotiator($this->getResponseNegotiator());
		$form->addExtraClass('cms-content center cms-edit-form');
		$form->setAttribute('data-pjax-fragment', 'CurrentForm');

		if($form->Fields()->hasTabset()) $form->Fields()->findOrMakeTab('Root')->setTemplate('CMSTabSet');
		$form->setHTMLID('Form_EditForm');
		$form->loadDataFrom($member);

		return $form;
	}

	public function saveMemberSettings($data, $form) {

		$member = Member::currentUser();
		$form->saveInto($member);
		
		try {
			$member->write();
		} catch(ValidationException $ex) {
			$form->sessionMessage($ex->getResult()->message(), 'bad');
			return $this->getResponseNegotiator()->respond($this->request);
		}
		
		$this->response->addHeader('X-Status', rawurlencode(_t('LeftAndMain.SAVEDUP', 'Saved.')));

		return $form->forTemplate();
	}	
}