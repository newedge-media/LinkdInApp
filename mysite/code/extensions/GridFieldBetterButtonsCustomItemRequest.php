<?php
class GridFieldBetterButtonsCustomItemRequest extends DataExtension {

	private static $allowed_actions = array (
		'doMarkDone'
	);

	public function updateItemEditForm($form) {
		Requirements::css('mysite/css/gridfield_custombetterbuttons.css');
	}

	public function doMarkDone($data, $form, $redirectLink) {
		Controller::curr()->getResponse()->addHeader("X-Pjax","Content");

		$new_record = $this->owner->record->ID == 0;
		$controller = Controller::curr();
		$list = $this->owner->gridField->getList();

		try {
			$this->owner->record->Complete = true;
			$form->saveInto($this->owner->record);
			$this->owner->record->write();
			$list->add($this->owner->record);
		} catch(ValidationException $e) {
			$form->sessionMessage($e->getResult()->message(), 'bad');
			$responseNegotiator = new PjaxResponseNegotiator(array(
				'CurrentForm' => function() use(&$form) {
					return $form->forTemplate();
				},
				'default' => function() use(&$controller) {
					return $controller->redirectBack();
				}
			));
			if($controller->getRequest()->isAjax()){
				$controller->getRequest()->addHeader('X-Pjax', 'CurrentForm');
			}
			return $responseNegotiator->respond($controller->getRequest());
		}

		return Controller::curr()->redirect($this->getBackLink());
	}

	public function getBackLink(){
		// TODO Coupling with CMS
		$backlink = '';
		$toplevelController = $this->getToplevelController();
		if($toplevelController && $toplevelController instanceof LeftAndMain) {
			if($toplevelController->hasMethod('Backlink')) {
				$backlink = $toplevelController->Backlink();
			} elseif($this->owner->getController()->hasMethod('Breadcrumbs')) {
				$parents = $this->owner->getController()->Breadcrumbs(false)->items;
				$backlink = array_pop($parents)->Link;
			}
		}
		if(!$backlink) $backlink = $toplevelController->Link();

		return $backlink;
	}

	protected function getToplevelController() {
		$c = $this->owner->getController();
		while($c && $c instanceof GridFieldDetailForm_ItemRequest) {
			$c = $c->getController();
		}
		return $c;
	}


}