CKEDITOR.dialog.add('cowriterDialog', function (editor) {
    return {
        title: 'Ask ChatGPT',
        minWidth: 400,
        minHeight: 70,
        contents: [
            {
                id: 'tab-basic',
                label: editor.lang.cowriter.tabGeneral || 'General',
                elements: [
                    {
                        type: 'textarea',
                        id: 'cowriter',
                        label: editor.lang.cowriter.writeAbout || 'What should I write about?',
                        rows: 2,
                        validate: CKEDITOR.dialog.validate.notEmpty(editor.lang.cowriter.errorNotEmpty),
                        setup: function (element) {
                            this.setValue(element.getText());
                        },
                        commit: function (element) {
                            element.setAttribute('src', CKEDITOR.getUrl(CKEDITOR.plugins.get('cowriter').path+'icons/spinner.gif'));

                            var xhr = new XMLHttpRequest();

                            xhr.open('POST', CKEDITOR.aiAjaxUrl);

                            var params = new FormData();
                            params.append('request', this.getValue());

                            xhr.send(params);

                            xhr.onreadystatechange = function () {
                                if (this.readyState === 4) {
                                    if (this.status === 200) {
                                        var status = true;
                                        
                                        try {
                                            var parsedJSON = JSON.parse(this.responseText);
                                        } catch (error) {
                                            status = false;
                                        }

                                        if (true === status) {
                                            if (undefined !== parsedJSON["response"]) {
                                                var newElement = CKEDITOR.dom.element.createFromHtml('<p>'+parsedJSON["response"]+'</p>');
                                                newElement.insertAfter(element);
                                                element.remove();
                                            } else if (undefined !== parsedJSON["error"]) {
                                                element.remove();
                                                alert(parsedJSON["error"]);
                                            }
                                        } else {
                                            element.remove();
                                            alert('Request failed.');
                                        }
                                    } else {
                                        element.remove();
                                        alert('Request failed.');
                                    }
                                }
                            }

                            xhr.onerror = function () {
                                element.remove();
                                alert('Request failed.');
                            }
                        }
                    },
                ]
            },
        ],
        onOk: function () {
            var dialog = this
            var cowriter = editor.document.createElement('img')
            dialog.commitContent(cowriter)
            editor.insertElement(cowriter)
        }
    }
})

CKEDITOR.plugins.add('cowriter', {
    icons: 'cowriter',
    lang: ['en', 'es', 'de', 'fr'],
    init: function (editor) {
        editor.addCommand('cowriter', new CKEDITOR.dialogCommand('cowriterDialog'))
        editor.ui.addButton('Ask ChatGPT', {
            label: 'Ask ChatGPT',
            command: 'cowriter',
            toolbar: 'about,1',
            icon: this.path + 'icons/cowriter-logo.png'
        })
    }
})
