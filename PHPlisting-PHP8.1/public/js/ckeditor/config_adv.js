/**
 * @license Copyright (c) 2003-2022, CKSource Holding sp. z o.o. All rights reserved.
 * For licensing, see https://ckeditor.com/legal/ckeditor-oss-license
 */

CKEDITOR.editorConfig = function( config ) {
    // Define changes to default configuration here.
    // For complete reference see:
    // https://ckeditor.com/docs/ckeditor4/latest/api/CKEDITOR_config.html

    // The toolbar groups arrangement, optimized for a single toolbar row.
    config.toolbarGroups = [
        { name: 'clipboard',   groups: [ 'clipboard', 'undo' ] },
        { name: 'editing',     groups: [ 'find', 'selection', 'spellchecker' ] },
        { name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
        { name: 'forms' },
        { name: 'styles' },
        { name: 'colors' },
        { name: 'paragraph',   groups: [ 'list', 'indent', 'blocks', 'align', 'bidi' ] },
        { name: 'links' },
        { name: 'insert' },
        { name: 'tools' },
        { name: 'others' },
        { name: 'document',    groups: [ 'mode', 'document', 'doctools' ] },
        { name: 'about' }
    ];

    config.extraPlugins = 'colorbutton,panelbutton,button,floatpanel,panel,justify,font,richcombo,listblock,liststyle,dialog,colordialog,dialogui,contextmenu,menu,blockquote,format,sourcearea,maximize,table,tabletools,tableresize,image,uploadimage,uploadwidget,filebrowser,widget,lineutils,widgetselection,clipboard,notification,toolbar,filetools,notificationaggregator,cowriter';
    config.removeDialogTabs = 'link:advanced';
    config.allowedContent = true;
};
