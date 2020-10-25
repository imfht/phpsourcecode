//PLUGIN PARA VALIDAÇÃO DE FORMULARIOS CRIADO POR ERIC ANDRADE - ericferreira1992@gmail.com
"use strict";
$.fn.formValidator = function (options) {
    var defaults = {
        before: function () {},
        after: {
            successValidation: '',
            errorValidation: ''
        },
        submitEnable: true,
        confirmSubmit: '',
        thisForm: this,
        erro: false,
        alertaErro: '',
        myValidations: [],
        sending: {
            type: 'redirect',
            dataType: 'html',
            success: '',
            error: ''
        },
        lang: 'cn'
    };
    var settings = $.extend({}, defaults, options);

    settings.thisForm = this;
    $(document).on('submit', '#' + settings.thisForm.attr('id'), function (e) {
        if (settings.submitEnable) {
            if (settings.confirmSubmit != '') {
                if (!window.confirm(settings.confirmSubmit))
                    return false;
            }

            if (typeof settings.before == 'function') {
                settings.before.call(this);
            }

            settings.erro = false;
            settings.alertaErro = languages[settings.lang].precisam_ser_preenchidos;
            $(this).find('input[data-required="true"], select[data-required="true"], textarea[data-required="true"]').map(function () {
                if ($(this).attr('type') == "radio") {
                    if ($(this).prop('checked') == false) {
                        if ($(this).data("title") != "" && $(this).data("title") != undefined)
                            var nome = $(this).data("title");
                        else
                            var nome = $(this).parent().children('label').text();

                        var checked = false;
                        $(this).find('input[name="' + $(this).attr("name") + '"]').map(function () {
                            if ($(this).prop('checked') == true) {
                                checked = true;
                            }
                        });
                        if (!checked) {
                            settings.erro = true;
                            settings.alertaErro = settings.alertaErro.replace("- " + nome + "\n", '');
                            settings.alertaErro += "- " + nome + "\n";
                        }
                    }
                } else if ($(this).attr('type') == "checkbox") {
                    if ($(this).prop('checked') == false) {
                        settings.erro = true;
                        if ($(this).data("title") != "" && $(this).data("title") != undefined)
                            var nome = $(this).data("title");
                        else
                            var nome = $(this).parent().children('label').text();
                        settings.alertaErro = settings.alertaErro.replace("- " + nome + "\n", '');
                        settings.alertaErro += "- " + nome + "\n";
                    }
                } else {
                    if ($(this).val() == "") {
                        settings.erro = true;
                        if ($(this).data("title") != "" && $(this).data("title") != undefined)
                            var nome = $(this).data("title");
                        else
                            var nome = $(this).parents('.form-group').children('label').text();
                        settings.alertaErro += "- " + nome + "\n";
                    }
                }
            });

            if (!settings.erro) {
                settings.alertaErro = "";
                settings.alertaErro = languages[settings.lang].precisam_ser_iguais;
                $(this).find('input[data-equals], select[data-equals], textarea[data-equals]').map(function () {
                    var inputNameIgual = $(this).data("equals");
                    if ($(this).val() != $('input[name="' + inputNameIgual + '"]').val()) {
                        settings.erro = true;
                        if ($(this).data("title") != "" && $(this).data("title") != undefined)
                            var nomeInput = $(this).data("title");
                        else
                            var nomeInput = $(this).parent().children('label').text();

                        if ($('input[name="' + inputNameIgual + '"]').data("title") != "" && $('input[name="' + inputNameIgual + '"]').data("title") != undefined)
                            var nomeInputIgual = $('input[name="' + inputNameIgual + '"]').data("title");
                        else
                            var nomeInputIgual = $('input[name="' + inputNameIgual + '"]').parent().children('label').text();
                        settings.alertaErro += "- " + nomeInputIgual + " e " + nomeInput + "\n";
                    }
                });
            }

            if (!settings.erro) {
                settings.alertaErro = languages[settings.lang].valores_invalidos;
                $(this).find('input[data-min-characters], textarea[data-min-characters]').map(function () {
                    var minCharacters = parseInt($(this).data("min-characters"));
                    if ($(this).val().length < minCharacters) {
                        settings.erro = true;
                        if ($(this).data("title") != "" && $(this).data("title") != undefined)
                            var nomeInput = $(this).data("title");
                        else
                            var nomeInput = $(this).parent().children('label').text();

                        settings.alertaErro = (languages[settings.lang].precisa_no_minimo_caract).replace('nomeInput', nomeInput).replace('minCharacters', minCharacters);
                    }
                });
            }

            if (!settings.erro) {
                if (settings.myValidations.length > 0) {
                    settings.alertaErro = languages[settings.lang].ocorreu_erro;
                    for (var i = 0; i < settings.myValidations.length; i++) {
                        if (typeof settings.myValidations[i] == 'function') {
                            var msg = settings.myValidations[i].call(this);
                            if (msg != "" && msg != undefined) {
                                settings.alertaErro += "- " + msg + "\n";
                                settings.erro = true;
                            }
                        }
                    }
                }
            }
            done();
        }
        e.stopImmediatePropagation();
        return false;
    });

    var ajaxingForm = false;
    function done() {
        if (!settings.erro) {//SUCCESS
            if (typeof settings.after.successValidation == 'function') {
                settings.after.successValidation.call(this);
            }
            if (settings.sending.type == "ajax") {
                var inputs = $('#' + settings.thisForm.attr('id')).serialize();
                var url = $(settings.thisForm).attr('action');
                var method = ($(settings.thisForm).attr('method') == "" || $(settings.thisForm).attr('method') == undefined) ? "POST" : $(settings.thisForm).attr('method');
                var enctype = ($(settings.thisForm).attr('enctype') == "" || $(settings.thisForm).attr('enctype') == undefined) ?'' : $(settings.thisForm).attr('enctype');
                $.ajaxSetup({data: inputs});
                if (!ajaxingForm) {
                    ajaxingForm = true;
                    if (enctype=='multipart/form-data') {
                        $.ajaxSetup({data: new FormData($(settings.thisForm)[0]),processData: false,contentType: false});
                    }
                    $.ajax({
                        type: method,
                        url: url,
                        dataType: settings.sending.dataType,
                        success: function (retorno) {
                            ajaxingForm = false;
                            if (typeof settings.sending.success == 'function') {
                                settings.sending.success(retorno);
                            }
                        },
                        error: function () {
                            ajaxingForm = false;
                            if (typeof settings.sending.error == 'function') {
                                settings.sending.error(retorno);
                            }
                        }
                    });
                }
            } else {
                $(settings.thisForm).submit();
                settings.submitEnable = false;
            }
        } else {//ERROR
            if (typeof settings.after.errorValidation == 'function') {
                settings.after.errorValidation.call(this);
            }
            show_error({'message':settings.alertaErro,'color':'danger'});
        }
    }

    var languages = {
        'pt-br': {
            'precisam_ser_preenchidos': "Os seguintes campos precisam ser preenchidos:\n",
            'valores_invalidos': "Os valores dos campos a seguir são inválidos:\n",
            'precisam_ser_iguais': "Os campos abaixo precisam ser iguais:\n",
            'precisa_no_minimo_caract': "- nomeInput precisa ter no mínimo minCharacters caracteres.\n",
            'ocorreu_erro': "Ops, ocorreram alguns erros:\n"
        },
        'en': {
            'precisam_ser_preenchidos': "The following fields need to be filled:\n",
            'valores_invalidos': "The values of the following fields are invalid:\n",
            'precisam_ser_iguais': "The fields must match:\n",
            'precisa_no_minimo_caract': "- Input name must be at least min Characters characters.\n",
            'ocorreu_erro': "Oops, there were some errors:\n"
        },
        'cn': {
            'precisam_ser_preenchidos': "以下字段的值必须填写：<br/>",
            'valores_invalidos': "以下字段的值无效：<br/>",
            'precisam_ser_iguais': "字段必须匹配：<br/>",
            'precisa_no_minimo_caract': "- 输入字段必须至少为 Characters 字符.\n<br/>",
            'ocorreu_erro': "发生错误：\n<br/>"
        },
        'es': {
            'precisam_ser_preenchidos': "Los siguientes campos deben ser llenados:\n",
            'valores_invalidos': "Los valores de los campos siguientes son válidos:\n",
            'precisam_ser_iguais': "Los campos deben coincidir:\n",
            'precisa_no_minimo_caract': "- nomeInput debe ser de al menos minCharacters caracteres.\n",
            'ocorreu_erro': "Vaya, hubo algunos errores:\n"
        }
    };
};