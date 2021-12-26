<?php
include filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/custom_form.php';

$applicationForm = new CustomForm(
        name: 'applicationForm',
        fields: [
            new CustomSelectField(
                    name: 'reason', 
                    label: 'auto',
                    verbose_name_nom: 'Причина получения'
                ),
            new CustomCheckField(
                    name: 'army_temporary', 
                    label: new CustomLabel('Подтведждаю, что не прохожу '
                    . 'срочную службу в армии*', 'army_temporary', 
                            class: 'form-check-label'), 
                    verbose_name_nom: 'Подтведждаю, что не прохожу '
                    . 'срочную службу в армии', 
                    required: true, 
                    hint: "Подтверждение отсутствия обстоятельств, "
                . "ограничивающих выезд за рубеж, обязательно",
                ),
            new CustomCheckField(
                    name: 'army_contract', 
                    label: new CustomLabel('Подтведждаю, что не прохожу '
                    . 'службу в армии по контракту*', 'army_temporary', 
                            class: 'form-check-label'),  
                    verbose_name_nom: 'Подтведждаю, что не прохожу '
                    . 'службу в армии по контракту', 
                    required: true, 
                    hint: "Подтверждение отсутствия обстоятельств, "
                . "ограничивающих выезд за рубеж, обязательно",
                ),
            new CustomCheckField(
                    name: 'punishment', 
                    label: new CustomLabel('Подтведждаю, что не отбываю '
                    . 'наказание или меру пресечения, предусматривающие ограничение '
                    . 'в выезде за рубеж*', 'army_temporary', 
                            class: 'form-check-label'), 
                    verbose_name_nom: 'Подтведждаю, что не отбываю '
                    . 'наказание, предусматривающее ограничение '
                    . 'в выезде за рубеж', 
                    required: true, 
                    hint: "Подтверждение отсутствия обстоятельств, "
                . "ограничивающих выезд за рубеж, обязательно",
                ),
            new CustomCheckField(
                    name: 'national_secret', 
                    label: new CustomLabel('Подтведждаю, что не обладаю '
                    . 'доступом к сведениям, являющимися государственной '
                    . 'тайной, доступ к которой'
                    . ' ограничивает выезд за рубеж*', 'army_temporary', 
                            class: 'form-check-label'), 
                    verbose_name_nom: 'Подтведждаю, что не обладаю '
                    . 'доступом к сведениям, являющимися государственной '
                    . 'тайной, доступ к которой'
                    . ' ограничивает выезд за рубеж', 
                    required: true, 
                    hint: "Подтверждение отсутствия обстоятельств, "
                . "ограничивающих выезд за рубеж, обязательно",
                ),
            new CustomCheckField(
                    name: 'debt', 
                    label: new CustomLabel('Подтведждаю, что не имею '
                    . 'невыполненных долговых обязательств*', 'army_temporary', 
                            class: 'form-check-label'), 
                    verbose_name_nom: 'Подтведждаю, что не имею '
                    . 'невыполненных долговых обязательств', 
                    required: true, 
                    hint: "Подтверждение отсутствия обстоятельств, "
                . "ограничивающих выезд за рубеж, обязательно",
                ),
            new CustomCheckField(
                    name: 'bankruptcy', 
                    label: new CustomLabel('Подтведждаю, что в течение '
                . 'последних 5 лет не был признан банкротом*',
                            'army_temporary', 
                            class: 'form-check-label'), 
                    verbose_name_nom: 'Подтведждаю, что в течение '
                . 'последних 5 лет не был признан банкротом', 
                    required: true, 
                    hint: "Подтверждение отсутствия обстоятельств, "
                . "ограничивающих выезд за рубеж, обязательно",
                ),
        ],
    action: "#",
    submitText: "Далее >",

);

$workPlaceForm = new CustomForm(
        name: 'workPlaceForm',
        fields: [
            new CustomTextField(
                    name: 'name', 
                    label: 'auto', 
                    verbose_name_nom: 'Название организации', 
                    verbose_name_acc: 'Название организации', 
                    required: true, 
                    regexp: '^[^A-Za-z]+$', 
                    hint: "Укажите название организации на русском языке.",
                    maxlength: 255),
            new CustomTextField(
                    name: 'address', 
                    label: 'auto', 
                    verbose_name_nom: 'Адрес организации', 
                    verbose_name_acc: 'Адрес организации', 
                    required: true, 
                    regexp: '^[^A-Za-z]+$', 
                    hint: "Укажите адрес организации на русском языке.",
                    maxlength: 255),
            new CustomDateField(
                name: 'employment_date',
                label: 'auto',
                verbose_name_nom: 'Дата зачисления',
                verbose_name_acc: 'Дату зачисления',
                required: true,
                before: "now",
                hint: "Дата зачисления указана в неправильном формате "
                . "или еще не наступила"
            ),
            new CustomDateField(
                name: 'unemployment_date',
                label: 'auto',
                verbose_name_nom: 'Дата увольнения (если трудоустроены в этой '
                    . 'организации в данный момент, оставьте пустым)',
                verbose_name_acc: 'Дату увольнения',
                before: "now",
                hint: "Дата увольнения указана в неправильном формате "
                . "или еще не наступила"
            ),
        ],
);