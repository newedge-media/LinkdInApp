<?php
class ActionListAdmin extends ModelAdmin {

    private static $managed_models = array(
        'ProspectActionList'
    );

    private static $url_segment = 'action-list';

    private static $menu_title = 'Action Lists';

    private static $menu_icon = 'mysite/images/lists-icon.png';

    public function init() {
    	parent::init();

        Requirements::customCSS(<<<CSS
            td.col-Content {background: #FFF;padding:0 !important;}
            td.col-Content textarea {width: 1px; height:1px; border:none;resize: none; color:#FFF;}
            td.col-Content textarea:focus{border:none;box-shadow:none;}
CSS
        );
    	Requirements::javascript('mysite/javascript/clipboard/dist/clipboard.min.js');
        Requirements::customScript(<<<JS
			(function($) {
				$.entwine('ss', function($){
                    $('.ss-gridfield button.ss-button-copyclipboard').removeClass('action');
                    var clipboard = new Clipboard('button.ss-button-copyclipboard');

                    $('.ss-gridfield button.ss-button-message-link').entwine({
                        onclick: function(e){
                            window.open($(this).attr('data-message-link-target'));
                            e.preventDefault();
                            return false;
                        }
                    });

                    $('.ss-gridfield button.ss-button-editprofile').entwine({
                        onclick: function(e){
                            window.open($(this).attr('data-profile-edit-url'));
                            e.preventDefault();
                            return false;
                        }
                    });                    

                    $('.ss-gridfield button.btn-done').addClass('btn-done').click(function(){
                        $(this).parents('tr').remove();
                    });

				});
			})(jQuery);
    		
JS
		);
    }

    public function getEditForm($id = null, $fields = null) {


        $form = parent::getEditForm();
        $fields = $form->Fields();

        $bulkManager = new GridFieldBulkManager();
        $bulkManager->removeBulkAction('bulkEdit');
        $bulkManager->removeBulkAction('unLink');
        $bulkManager->removeBulkAction('delete');

        $bulkManager->addBulkAction(
            'MarkDone', 
            'Mark Done', 
            'GridFieldBulkActionMarkDoneHandler',
            array(
                'isAjax' => true,
                'icon' => 'pencil',
                'isDestructive' => false
            )
        );        

        $fields->dataFieldByName('ProspectActionList')
            ->getConfig()
            ->addComponent(new GridFieldEditableColumns())
            ->addComponent($bulkManager)
            ->getComponentByType('GridFieldEditableColumns')->setDisplayFields(array(
                'Content'  => function($record, $column, $grid) {
                    return TextareaField::create($column)
                        ->setRows(1)
                        ->setColumns(1);
                }
            ));            

        $fields->dataFieldByName('ProspectActionList')
            ->getConfig()
            ->addComponent(new GridFieldMessageLinkAction())
            ->addComponent(new GridFieldEditProfileAction())
            ->addComponent(new GridFieldCopyClipboardAction())
            ->addComponent(new GridFieldMarkDoneAction());

        return $form;
    }

    public function getList() {
    	$list = parent::getList();

    	$list = $list->filter(array(
    		'Message.Action_Date:LessThanOrEqual' => date('Y-m-d'), 
    		'Complete' => false, 
            'MemberID' => Member::currentUserID()
    	));

    	return $list;
    }
}
