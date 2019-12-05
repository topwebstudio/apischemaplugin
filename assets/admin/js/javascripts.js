require('webpack-jquery-ui/dialog');
 

define(['jquery'], function ($) {

    var aceEditor = null;
    var currentyActiveJsonSchema = null;

    $(document).ready(function () {

//
//        $('#confirm-delete').on('show.bs.modal', function () {
//
//        });

        $('#deleteButton').click(function () {
            $.getJSON($('#deleteEntity').data('goto'), function (response) {
                if (response.success === true) {
                    window.location.href = response.data;
                }
            });
        });

        $('#userLocations a').click(function (event) {
            event.preventDefault();
            $.getJSON($(this).attr('href'), function (response) {
                if (response.success === true) {
                    location.reload();
                }
            });
        });


        $('.enableDisableDomain .ios-toggle').click(function (event) {
            var prop = $(this).prop('checked');

            event.preventDefault();

            if (!prop) {
                $('#confirm-disable-domain').data('id', $(this).parents('tr').data('id')).modal();
            } else {
                $('#confirm-enable-domain').data('id', $(this).parents('tr').data('id')).modal();
            }

        });


        $('#disableDomain').click(function () {
            var id = $('#confirm-disable-domain').data('id');

            $('#confirm-disable-domain').modal('hide');
            $('#checkbox' + id).prop('checked', '');

            enableDisableDomain(id, '0');

        });

        $('#enableDomain').click(function () {
            var id = $('#confirm-enable-domain').data('id');

            $('#confirm-enable-domain').modal('hide');
            $('#checkbox' + id).prop('checked', 'checked');

            enableDisableDomain(id, '1');

        });


        function enableDisableDomain(id, action) {
            $.post(Routing.generate('enable_disable_domain', {id: id, action: action}), function (response) {

            });

            if (action == 0) {
                $('tr[data-id="' + id + '"]').addClass('inactive');
            } else {
                $('tr[data-id="' + id + '"]').removeClass('inactive');
            }

        }

        // admin/purchases
        $('.enableDisableKey .ios-toggle').click(function (event) {
            var prop = $(this).prop('checked');

            event.preventDefault();

            if (!prop) {
                $('#confirm-disable-key').data('id', $(this).parents('tr').data('id')).modal();
            } else {
                $('#confirm-enable-key').data('id', $(this).parents('tr').data('id')).modal();
            }
        });

        $('.disableApiKey').click(function () {
            var id = $('#confirm-disable-key').data('id');
            var domains = $(this).data('domains') == true ? "true" : "false";

            $('#confirm-disable-key').modal('hide');
            $('#checkbox' + id).prop('checked', '');

            $('#checkbox' + id).parents('tr').addClass('inactive');

            $.post(Routing.generate('enable_disable_apikey', {id: id, action: 0, domains: domains, jvzoo: jvzootruefalse()}), function (response) {

            });

        });

        $('#enableApiKey').click(function () {
            var id = $('#confirm-enable-key').data('id');

            $('#confirm-enable-key').modal('hide');
            $('#checkbox' + id).prop('checked', 'checked');
            $('#checkbox' + id).parents('tr').removeClass('inactive');

            $.post(Routing.generate('enable_disable_apikey', {id: id, action: 1, domains: "true", jvzoo: jvzootruefalse()}), function (response) {

            });
        });
//
//        $('#searchDirectPurchase').autocomplete({
//            serviceUrl: $('#searchDirectPurchase').data('url'),
//            onSelect: function (suggestion) {
//                if (suggestion.data) {
//                    window.location.href = suggestion.data;
//                }
//            }
//        });

        $('.deleteDomain').click(function () {
            $('#confirm-delete-domain').data('id', $(this).parents('tr').data('id')).data('url', $(this).data('url')).modal();
        });


        $('#deleteDomain').click(function () {
            $.post($('#confirm-delete-domain').data('url'), function (response) {
                if (response.success) {
                    $('#confirm-delete-domain').modal('hide');
                    var id = $('#confirm-delete-domain').data('id');
                    $('tr[data-id="' + id + '"]').remove();
                }
            });
        });


        // EDITOR
        $.each($('textarea'), function () {
            ClassicEditor.create($(this)[0]);
        });


        $('#add-json-schema').click(function () {
            var id = $('#schemas .view-json-schema').length + 1;

            var schemaHtmlButton = "<span class='view-json-schema ui-button ui-corner-all ui-widget'>Schema #" + id + "</span>";

            $('#schemas').append(schemaHtmlButton);
        });


        function createAceEditor(id, value) {
            aceEditor = ace.edit(id, {
                mode: 'ace/mode/json',
                theme: 'ace/theme/textmate'
            });

            if (value) {
                aceEditor.session.setValue(JSON.stringify(value, null, '\t'));
            } else {
                aceEditor.session.setValue('');
            }

            aceEditor.setOptions({
                maxLines: Infinity,
                fontSize: "13px",
                fontFamily: "monospace"
            });

            aceEditor.setShowPrintMargin(false);
        }


        $("#json-schema-dialog").dialog({
            autoOpen: false,
            width: 900,
            height: 550,
            open: function () {
                if (typeof (currentyActiveJsonSchema) !== 'object') {
                    createAceEditor($('#ace-json')[0], $('#schema-' + currentyActiveJsonSchema).data('json-schema'));
                } else {
                    createAceEditor($('#ace-json')[0], null);
                }
            },
            buttons: [
                {
                    text: "Save",
                    icon: "ui-icon-check",
                    click: function () {
                        var json = aceEditor.getSession().getValue();
                        var id = null;


                        if (typeof (currentyActiveJsonSchema) !== 'object') {
                            id = $('#schema-' + currentyActiveJsonSchema).data('id');
                            $('#schema-' + currentyActiveJsonSchema).data('json-schema', json);
                        } else {
                            $(currentyActiveJsonSchema).data('json-schema', json);
                        }


                        var dialog = $(this);

                        var data = {
                            json: json,
                            id: id,
                            'schema-builder-id': $('#schemas').data('schema-builder-id')
                        };

                        $.post($('#schemas').data('save-json-url'), data, function (response) {
                            if (response.success) {
                                if (typeof (currentyActiveJsonSchema) === 'object') {
                                    $(currentyActiveJsonSchema).data('id', response.id);
                                    $(currentyActiveJsonSchema).attr('id', 'schema-' + response.id);
                                    $(currentyActiveJsonSchema).data('json-schema', JSON.parse(json));
                                }

                                dialog.dialog("close");
                            }
                        });
                    }
                },
                {
                    text: "Cancel",
                    icon: 'ui-icon-close',
                    click: function () {
                        $(this).dialog("close");
                    }
                },
                {
                    text: "Delete",
                    icon: 'ui-icon-trash',
                    click: function () {
                        var dialog = $(this);

                        if (typeof (currentyActiveJsonSchema) !== 'object') {
                            var data = {
                                id: $('#schema-' + currentyActiveJsonSchema).data('id')
                            };

                            $.post($('#schemas').data('delete-json-url'), data, function (response) {
                                if (response.success) {

                                    if (typeof (currentyActiveJsonSchema) !== 'object') {
                                        $('#schema-' + currentyActiveJsonSchema).remove();
                                    } else {
                                        $(currentyActiveJsonSchema).remove();
                                    }

                                    dialog.dialog("close");
                                }
                            });
                        } else {
                            $(currentyActiveJsonSchema).remove();
                        }

                        $(this).dialog("close");
                    }
                }

            ]
        });


        $(document).on('click', '.view-json-schema', function () {
            currentyActiveJsonSchema = $(this).data('id');

            if (!currentyActiveJsonSchema) {
                currentyActiveJsonSchema = $(this);
            }

            $("#json-schema-dialog").dialog("open");
            return false;
        });


    });
});