<?php
class BetterButton_Done extends BetterButton {

    public function __construct() {
        parent::__construct('doMarkDone', 'Mark Done');
    }

    /**
     * Adds the JS, sets up necessary HTML attributes
     * @return FormAction
     */
    public function baseTransform() {
        parent::baseTransform();
        Requirements::javascript('mysite/javascript/gridfield_betterbuttons_done.js');

        return $this
            ->setUseButtonTag(true)
            ->addExtraClass('gridfield-better-buttons-done ss-ui-action-constructive')
            ->setAttribute("data-toggletext", 'Yes. Mark this item done.')
            ->setAttribute("data-confirmtext", 'No. Don\'t mark it done.');
    }    

    /**
     * Determines if the button should show
     * @return boolean
     */
    public function shouldDisplay() {
    	if (!$this->gridFieldRequest->recordIsPublished() && $this->gridFieldRequest->record->canDelete() && $this->gridFieldRequest->record->ClassName == 'ProspectActionList') {
    		return true;
    	}

    	return false;
    }
}