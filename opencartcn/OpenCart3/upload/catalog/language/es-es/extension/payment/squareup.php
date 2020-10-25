<?php
// Text
$_['text_new_card']                     = '+ Añadir nueva tarjeta';
$_['text_loading']                      = 'Cargando... Por favor, espere...';
$_['text_card_details']                 = 'Detalles tarjeta';
$_['text_saved_card']                   = 'Utilizar la tarjeta guardada:';
$_['text_card_ends_in']                 = 'Pagar con la tarjeta existente %s que termina en XXXX XXXX XXXX %s';
$_['text_card_number']                  = 'Tarjeta número:';
$_['text_card_expiry']                  = 'Expira (MM/YY):';
$_['text_card_cvc']                     = 'Código seguridad (CVC):';
$_['text_card_zip']                     = 'Código postal de la tarjeta:';
$_['text_card_save']                    = '¿Guardar tarjeta para usos futuros?';
$_['text_trial']                        = '%s cada %s %s para %s pagos ';
$_['text_recurring']                    = '%s cada %s %s';
$_['text_length']                       = ' para %s pagos';
$_['text_cron_subject']                 = 'Resumen de trabajos de Square CRON';
$_['text_cron_message']                 = 'Aquí está una lista de todas las tareas de CRON realizadas por su extensión Square:';
$_['text_squareup_profile_suspended']   = ' Sus pagos recurrentes se han suspendido. Por favor, póngase en contacto con nosotros para más detalles.';
$_['text_squareup_trial_expired']       = ' Su período de prueba ha caducado.';
$_['text_squareup_recurring_expired']   = ' Sus pagos recurrentes han caducado. Este fue tu último pago.';
$_['text_cron_summary_token_heading']   = 'Actualización del token de acceso:';
$_['text_cron_summary_token_updated']   = '¡Token de acceso actualizado';
$_['text_cron_summary_error_heading']   = 'Errores transacción:';
$_['text_cron_summary_fail_heading']    = 'Transacciones fallidas (Perfiles suspendidos):';
$_['text_cron_summary_success_heading'] = 'Transacciones correctas:';
$_['text_cron_fail_charge']             = 'Perfil <strong>#%s</strong> no se pudo cargar con <strong>%s</strong>';
$_['text_cron_success_charge']          = 'Perfil <strong>#%s</strong> cargado con <strong>%s</strong>';
$_['text_card_placeholder']             = 'XXXX XXXX XXXX XXXX';
$_['text_cvv']                          = 'CVV';
$_['text_expiry']                       = 'MM/YY';
$_['text_default_squareup_name']        = 'Tarjeta Crédito/Débito';
$_['text_token_issue_customer_error']   = 'Estamos experimentando una interrupción técnica en nuestro sistema de pago. Por favor, inténtelo de nuevo más tarde.';
$_['text_token_revoked_subject']        = '¡Su token de acceso se ha revocado!';
$_['text_token_revoked_message']        = "El acceso de la extensión de pago Square a su cuenta se ha revocado en el panel de Square. Debe verificar las credenciales de la aplicación en la configuración de la extensión y volver a conectarse.";
$_['text_token_expired_subject']        = '¡Tu token de acceso ha caducado!';
$_['text_token_expired_message']        = "El token de acceso de la extensión de pago Square que lo conecta a su cuenta de Square ha expirado. Debe verificar las credenciales de la aplicación y el trabajo CRON en la configuración de la extensión y volver a conectarse.";

// Error
$_['error_browser_not_supported']       = 'Error: El sistema de pago no es compatible con su navegador web. Actualice o utilice otro.';
$_['error_card_invalid']                = 'Error: ¡Tarjeta no válida!';
$_['error_squareup_cron_token']         = 'Error: El token de acceso no se pudo actualizar. Conecte su extensión Square a través del panel de administración de OpenCart.';

// Warning
$_['warning_test_mode']                 = 'Advertencia: ¡El modo Sandbox está habilitado! Las transacciones parecerán pasar, pero no se realizarán cargos.';

// Statuses
$_['squareup_status_comment_authorized']    = 'La transacción de la tarjeta se ha autorizado pero aún no se ha capturado.';
$_['squareup_status_comment_captured']      = 'La transacción de la tarjeta fue autorizada y posteriormente capturada (i.e., completado).';
$_['squareup_status_comment_voided']        = 'La transacción de la tarjeta fue autorizada y posteriormente anulada (i.e., cancelado).   ';
$_['squareup_status_comment_failed']        = 'Error en la transacción de la tarjeta.';

// Override errors
$_['squareup_override_error_billing_address.country']       = 'El país de dirección de pago no es válido. Modifíquela e inténtelo de nuevo.';
$_['squareup_override_error_shipping_address.country']      = 'El país de dirección de envío no es válido. Modifíquela e inténtelo de nuevo.';
$_['squareup_override_error_email_address']                 = 'La dirección de correo electrónico de su cliente no es válida. Modifíquela e inténtelo de nuevo.';
$_['squareup_override_error_phone_number']                  = 'El número de teléfono de su cliente no es válido. Modifíquela e inténtelo de nuevo.';
$_['squareup_error_field']                                  = ' Campo: %s';