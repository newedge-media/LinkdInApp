<?php
class GridFieldMessageLinkAction implements GridField_ColumnProvider, GridField_ActionProvider {

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
            'MessageLink'.$record->ID,
            'Msg Link',
            "doredirectmessagelink",
            array('RecordID' => $record->ID)
        );


        $field->addExtraClass('ss-button-message-link ss-ui-action-constructive ss-ui-button ui-button ui-widget ui-state-default ui-corner-all');

        if ($record->ClassName == 'Prospect') {
            $field->setAttribute('data-message-link-target', $record->MessageLink);
        } else {
            $field->setAttribute('data-message-link-target', $record->Prospect()->MessageLink);
        }

        return $field->Field();
    }    

    public function getActions($gridField) {
        return array('doredirectmessagelink');
    }

    public function handleAction(GridField $gridField, $actionName, $arguments, $data) {
        if($actionName == 'doredirectmessagelink') {
            Controller::curr()->getResponse()->setStatusCode(200, 'Redirected to the message link');
        }
    }
}