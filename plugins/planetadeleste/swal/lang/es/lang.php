<?php
/**
 * realestate.local
 * Created by 1505.
 * User: 1505
 * Date: 10/07/2015
 * Time: 5:13
 */
return [
    'plugin'   => [
        'name'        => 'Sweet Alert',
        'description' => 'Este componente muestra las alertas de Flash mediante la librería SweetAlert',
    ],
    'settings' => [
        'name'                        => 'Nombre de la configuración',
        'options'                     => 'Opciones',
        'setting'                     => 'Configuraciones',
        'global'                      => 'Global',
        'global_comment'              => 'Configuraciones globales para determinados eventos ajax',
        'theme'                       => 'Tema',
        'default_theme'               => 'Tema por defecto de Sweet Alert',
        'no_options'                  => 'Usar las configuraciones predeterminadas de Sweet Alert',
        'animation_top'               => 'Saltar',
        'animation_slide_from_top'    => 'Deslizar desde arriba',
        'animation_slide_from_bottom' => 'Deslizar desde abajo',
    ],
    'doc'      => [
        'allowescapekey'     => 'If set to true, the user can dismiss the modal by pressing the Escape key.',
        'customclass'        => 'A custom CSS class for the modal. It can be added to the object under the key "customClass".',
        'allowoutsideclick'  => 'If set to true, the user can dismiss the modal by clicking outside it.',
        'showcancelbutton'   => 'If set to true, a "Cancel"-button will be shown, which the user can click on to dismiss the modal.',
        'showconfirmbutton'  => 'If set to false, the "OK/Confirm"-button will be hidden. Make sure you set a timer or set allowOutsideClick to true when using this, in order not to annoy the user.',
        'confirmbuttontext'  => 'Use this to change the text on the "Confirm"-button. If showCancelButton is set as true, the confirm button will automatically show "Confirm" instead of "OK".',
        'confirmbuttoncolor' => 'Use this to change the background color of the "Confirm"-button (must be a HEX value).',
        'cancelbuttontext'   => 'Use this to change the text on the "Cancel"-button.',
        'closeonconfirm'     => 'Set to false if you want the modal to stay open even if the user presses the "Confirm"-button. This is especially useful if the function attached to the "Confirm"-button is another SweetAlert.',
        'closeoncancel'      => 'Same as closeOnConfirm, but for the cancel button.',
        'imageurl'           => 'Add a customized icon for the modal. Should contain a string with the path to the image.',
        'imagesize'          => 'If imageUrl is set, you can specify imageSize to describes how big you want the icon to be in px. Pass in a string with two values separated by an "x". The first value is the width, the second is the height.',
        'timer'              => 'Auto close timer of the modal. Set in ms (milliseconds).',
        'html'               => 'If set to true, will not escape title and text parameters. (Set to false if you\'re worried about XSS attacks.)',
        'animation'          => 'If set to false, the modal\'s animation will be disabled.',
        'inputtype'          => 'Change the type of the input field when using type: "input" (this can be useful if you want users to type in their password for example).',
        'inputplaceholder'   => 'When using the input-type, you can specify a placeholder to help the user.',
        'inputvalue'         => 'Specify a default text value that you want your input to show when using type: "input"',
        'theme'              => 'Cambia el tema de Swal - SweetAlert',
    ]
];