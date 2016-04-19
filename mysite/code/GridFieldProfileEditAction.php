<?php
class GridFieldEditProfileAction implements GridField_ColumnProvider, GridField_ActionProvider {

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
            'EditProfile'.$record->ID,
            'Profile',
            "doeditprofile",
            array('RecordID' => $record->ID)
        );

        $field->addExtraClass('ss-button-editprofile ss-ui-action-constructive ss-ui-button ui-button ui-widget ui-state-default ui-corner-all');
        $field->setAttribute('data-profile-edit-url', 'admin/prospects/Prospect/EditForm/field/Prospect/item/' .$record->ProspectID. '/edit');

        return $field->Field();
    }

    public function getActions($gridField) {
        return array('doeditprofile');
    }

    public function handleAction(GridField $gridField, $actionName, $arguments, $data) {
        return false;
    }
}