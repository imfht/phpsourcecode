<?php
// Heading
$_['heading_title']                 = 'CardConnect';

// Tab
$_['tab_settings']                  = 'Configuraci&oacute;n';
$_['tab_order_status']              = 'Estado del pedido';

// Text
$_['text_extension']                = 'Extensiones';
$_['text_success']                  = 'Correcto: Ha modificado CardConnect.';
$_['text_edit']                     = 'Editar CardConnect';
$_['text_cardconnect']              = '<a href="http://www.cardconnect.com" target="_blank"><img src="view/image/payment/cardconnect.png" alt="CardConnect" title="CardConnect"></a>';
$_['text_payment']                  = 'Pago';
$_['text_authorize']                = 'Autorizar';
$_['text_live']                     = 'En vivo';
$_['text_test']                     = 'Prueba';
$_['text_no_cron_time']             = 'El cron a&uacute;n no se ha ejecutado';
$_['text_payment_info']             = 'Informaci&oacute;n pago';
$_['text_payment_method']           = 'M&eacute;todo de pago';
$_['text_card']                     = 'Trajeta';
$_['text_echeck']                   = 'eCheck';
$_['text_reference']                = 'Referencia';
$_['text_update']                   = 'Actualizar';
$_['text_order_total']              = 'Total pedido';
$_['text_total_captured']           = 'Total Capturado';
$_['text_capture_payment']          = 'Pago Capturado';
$_['text_refund_payment']           = 'Devoluci&oacute;n pago';
$_['text_void']                     = 'Anulado';
$_['text_transactions']             = 'Transactiones';
$_['text_column_type']              = 'Tipo';
$_['text_column_reference']         = 'Referencia';
$_['text_column_amount']            = 'Importe';
$_['text_column_status']            = 'Estado';
$_['text_column_date_modified']     = 'Fecha modificaci&oacute;n';
$_['text_column_date_added']        = 'A&ntilde;adido el';
$_['text_column_update']            = 'Actualizar';
$_['text_column_void']              = 'Anulado';
$_['text_confirm_capture']          = '¿Seguro que desea capturar el pago?';
$_['text_confirm_refund']           = '¿Seguro que quiere consolidar el pago?';
$_['text_inquire_success']          = 'Petici&oacute;n correcta';
$_['text_capture_success']          = 'Captura correcta';
$_['text_refund_success']           = 'Devoluci&oacute;n correcta';
$_['text_void_success']             = 'Solicitud de anulaci&oacute;n correcta';

// Entry
$_['entry_merchant_id']             = 'ID Comercio';
$_['entry_api_username']            = 'API Usuario';
$_['entry_api_password']            = 'API Contrase&ntilde;a';
$_['entry_token']                   = 'Token Secreto';
$_['entry_transaction']             = 'Transacci&oacute;n';
$_['entry_environment']             = 'Environment';
$_['entry_site']                    = 'Site';
$_['entry_store_cards']             = 'Tarjetas Tienda';
$_['entry_echeck']                  = 'eCheck';
$_['entry_total']                   = 'Total';
$_['entry_geo_zone']                = 'Geo Zona';
$_['entry_status']                  = 'Estado';
$_['entry_logging']                 = 'Debug Logging';
$_['entry_sort_order']              = 'Posici&oacute;n';
$_['entry_cron_url']                = 'Cron Job URL';
$_['entry_cron_time']               = 'Cron Job Last Run';
$_['entry_order_status_pending']    = 'Pendiente';
$_['entry_order_status_processing'] = 'En proceso';

// Help
$_['help_merchant_id']              = 'Your personal CardConnect account merchant ID.';
$_['help_api_username']             = 'Your username to access the CardConnect API.';
$_['help_api_password']             = 'Your password to access the CardConnect API.';
$_['help_token']                    = 'Enter a random token for security or use the one generated.';
$_['help_transaction']              = 'Choose \'Payment\' to capture the payment immediately or \'Authorize\' to have to approve it.';
$_['help_site']                     = 'This determines the first part of the API URL. Only change if instructed.';
$_['help_store_cards']              = 'Whether you want to store cards using tokenization.';
$_['help_echeck']                   = 'Whether you want to offer the ability to pay using an eCheck.';
$_['help_total']                    = 'The checkout total the order must reach before this payment method becomes active. Must be a value with no currency sign.';
$_['help_logging']                  = 'Enabling debug will write sensitive data to a log file. You should always disable unless instructed otherwise.';
$_['help_cron_url']                 = 'Set a cron job to call this URL so that the orders are auto-updated. It is designed to be ran a few hours after the last capture of a business day.';
$_['help_cron_time']                = 'This is the last time that the cron job URL was executed.';
$_['help_order_status_pending']     = 'The order status when the order has to be authorized by the merchant.';
$_['help_order_status_processing']  = 'The order status when the order is automatically captured.';

// Button
$_['button_inquire_all']            = 'Inquire All';
$_['button_capture']                = 'Capture';
$_['button_refund']                 = 'Refund';
$_['button_void_all']               = 'Void All';
$_['button_inquire']                = 'Inquire';
$_['button_void']                   = 'Void';

// Error
$_['error_permission']              = 'Warning: You do not have permission to modify payment CardConnect!';
$_['error_merchant_id']             = 'Merchant ID Required!';
$_['error_api_username']            = 'API Username Required!';
$_['error_api_password']            = 'API Password Required!';
$_['error_token']                   = 'Secret Token Required!';
$_['error_site']                    = 'Site Required!';
$_['error_amount_zero']             = 'Amount must be higher than zero!';
$_['error_no_order']                = 'No matching order info!';
$_['error_invalid_response']        = 'Invalid response received!';
$_['error_data_missing']            = 'Missing data!';
$_['error_not_enabled']             = 'Module not enabled!';