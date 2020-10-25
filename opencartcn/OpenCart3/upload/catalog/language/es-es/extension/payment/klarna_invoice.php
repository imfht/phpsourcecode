<?php
// Text
$_['text_title']				= 'Factura Klarna - Pago en 14 días';
$_['text_terms_fee']			= '<span id="klarna_invoice_toc"></span> (+%s)<script type="text/javascript">var terms = new Klarna.Terms.Invoice({el: \'klarna_invoice_toc\', eid: \'%s\', country: \'%s\', charge: %s});</script>';
$_['text_terms_no_fee']			= '<span id="klarna_invoice_toc"></span><script type="text/javascript">var terms = new Klarna.Terms.Invoice({el: \'klarna_invoice_toc\', eid: \'%s\', country: \'%s\'});</script>';
$_['text_additional']			= 'Cuenta Klarna requiere alguna información adicional antes de poder procesar su pedido.';
$_['text_male']					= 'Masculino';
$_['text_female']				= 'Femenino';
$_['text_year']					= 'Año';
$_['text_month']				= 'Mes';
$_['text_day']					= 'Día';
$_['text_comment']				= 'Klarna\'s Invoice ID: %s. ' . "\n" . '%s/%s: %.4f';

// Entry
$_['entry_gender']				= 'Género';
$_['entry_pno']					= 'Número Personal';
$_['entry_dob']					= 'Fecha de nacimiento';
$_['entry_phone_no']			= 'Teléfono';
$_['entry_street']				= 'Dirección';
$_['entry_house_no']			= 'No.';
$_['entry_house_ext']			= 'Ext.';
$_['entry_company']				= 'Compañía Número de Registro';

// Help
$_['help_pno']					= 'Introduzca su número de Seguro Social aquí.';
$_['help_phone_no']				= 'Introduzca su número de teléfono.';
$_['help_street']				= 'Tenga en cuenta que la entrega sólo puede tener lugar a la dirección registrada al pagar con Klarna.';
$_['help_house_no']				= 'Por favor introduzca su número.';
$_['help_house_ext']			= 'Por favor introduzca su extensión aquí. E.g. A, B, C, Red, Blue ect.';
$_['help_company']				= 'Por favor introduzca su empresa\'s número de registro';

// Error
$_['error_deu_terms']			= 'Usted debe aceptar Klarna\'s política de privacidad (Datenschutz)';
$_['error_address_match']		= 'Direcciones de facturación y envío deben coincidir si desea utilizar Pagos Klarna';
$_['error_network']				= 'Se produjo un error al conectar con Klarna. Por favor, inténtelo de nuevo más tarde.';