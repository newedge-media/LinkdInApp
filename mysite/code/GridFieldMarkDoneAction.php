<?php
class GridFieldMarkDoneAction implements GridField_ColumnProvider, GridField_ActionProvider {

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
            'MarkAction'.$record->ID,
            'Done',
            "domarklist",
            array('RecordID' => $record->ID)
        );
        $field->addExtraClass('ss-ui-action-constructive');

        return $field->Field();
    }

    public function getActions($gridField) {
        return array('domarklist');
    }

    public function handleAction(GridField $gridField, $actionName, $arguments, $data) {
        if($actionName == 'domarklist') {
        	$actionList = ProspectActionList::get()->byID($arguments['RecordID']);
        	$actionList->Complete = true;
        	$actionList->write();

            Controller::curr()->getResponse()->setStatusCode(200, $actionList->Title . ' is marked done.');
        }
    }
}