<?php
include_once filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/custom_form.php';

$passportForm = new CustomForm(
                    name: 'passportForm',
                    fields: [
                        new CustomTextField(
                                name: 'series', 
                                label: 'auto', 
                                verbose_name_nom: 'Серия', 
                                verbose_name_acc: 'Серию', 
                                required: true, 
                                regexp: '^\d{4}$', 
                                hint: "Серия паспорта должна состоять из 4 цифр, "
                            . "вводить их нужно без пробелов и других разделителей",
                                maxlength: 4
                            ),
                        new CustomTextField(
                                name: 'number', 
                                label: 'auto', 
                                verbose_name_nom: 'Номер', 
                                verbose_name_acc: 'Номер', 
                                required: true, 
                                regexp: '^\d{6}$', 
                                hint: "Номер паспорта должен состоять из 6 цифр, "
                            . "вводить их нужно без пробелов и других разделителей",
                                maxlength: 6
                            ),
                        new CustomDateField(
                            name: 'issue_date',
                            label: 'auto',
                            verbose_name_nom: 'Когда выдан',
                            verbose_name_acc: 'Дату выдачи',
                            required: true,
                            before: "now",
                            hint: "Дата выдачи указана в неверном формате "
                            . "или еще не наступила"
                        ),
                        new CustomTextField(
                                name: 'issue_organ', 
                                label: 'auto', 
                                verbose_name_nom: 'Кем выдан', 
                                verbose_name_acc: 'Место выдачи', 
                                required: true, 
                                regexp: '^[^A-Za-z]+$', 
                                hint: "Укажите место выдачи на русском языке.",
                                maxlength: 255),
                    ],
                action: "?success=true" 
            );

