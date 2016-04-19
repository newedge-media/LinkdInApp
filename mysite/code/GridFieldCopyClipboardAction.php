<?php
class GridFieldCopyClipboardAction implements GridField_ColumnProvider, GridField_ActionProvider {

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
            'CopyAction'.$record->ID,
            'Copy to Clipboard',
            "docopytoclipboard",
            array('RecordID' => $record->ID)
        );

        $field->addExtraClass('ss-button-copyclipboard ss-ui-action-constructive ss-ui-button ui-button ui-widget ui-state-default ui-corner-all');
        $field->setAttribute('data-clipboard-target', '#Form_ProspectActionList_GridFieldEditableColumns_' .$record->ID. '_Content');

        return $field->Field();
    }

    public function getActions($gridField) {
        return array('docopytoclipboard');
    }

    public function handleAction(GridField $gridField, $actionName, $arguments, $data) {
        return false;
        if($actionName == 'docopytoclipboard') {
            Controller::curr()->getResponse()->setStatusCode(200, 'Message copied to clipboard');
        }
    }
}