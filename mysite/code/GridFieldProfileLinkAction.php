<?php
class GridFieldProfileLinkAction implements GridField_ColumnProvider, GridField_ActionProvider {

    /*public function __construct(){
        Requirements::customScript(<<<JS
            (function($){
                $.entwine('ss', function($) {

                    $('.ss-gridfield button.ss-button-profile-link').entwine({
                        onclick: function (e) {
                            alert('Hello World');
                            window.open($(this).attr('data-profile-link-target'));
                            e.preventDefault();
                            return false;
                        }
                    });
                });
            })(jQuery);

JS
        );
    }*/

    public function augmentColumns($gridField, &$columns) {
        if(!in_array('Actions', $columns)) {
            $columns[] = 'Actions';
        }
    }

    public function getColumnAttributes($gridField, $record, $columnName) {
        return array('class' => 'col-buttons');
    }

    public function getColumnMetadata($gridField, $columnName) {
        if($columnName == 'Actions') {
            return array('title' => '');
        }
    }

    public function getColumnsHandled($gridField) {
        return array('Actions');
    }

    public function getColumnContent($gridField, $record, $columnName) {
        if(!$record->canEdit()) return;

        $field = GridField_FormAction::create(
            $gridField,
            'ProfileLink'.$record->ID,
            'Profile Link',
            "doredirectprofilelink",
            array('RecordID' => $record->ID)
        );


        $field->addExtraClass('ss-button-profile-link ss-ui-action-constructive ss-ui-button ui-button ui-widget ui-state-default ui-corner-all');

        $field->setAttribute('data-profile-link-target', $record->ProfileLink);

        return $field->Field();
    }    

    public function getActions($gridField) {
        return array('doredirectprofilelink');
    }

    public function handleAction(GridField $gridField, $actionName, $arguments, $data) {
        if($actionName == 'doredirectprofilelink') {
            Controller::curr()->getResponse()->setStatusCode(200, 'Redirected to the profile link');
        }
    }
}