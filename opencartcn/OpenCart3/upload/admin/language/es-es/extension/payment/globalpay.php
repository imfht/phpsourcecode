<?php
// Heading
$_['heading_title']					 = 'Globalpay Redirect';

// Text
$_['text_extension']				 = 'Extensiones';
$_['text_success']					 = 'Correcto: Ha modificado Globalpay!';
$_['text_edit']                      = 'Editar Globalpay Redirect';
$_['text_live']						 = 'En Vivo';
$_['text_demo']						 = 'Demo';
$_['text_card_type']				 = 'Tipo Tarjeta';
$_['text_enabled']					 = 'Habilitado';
$_['text_use_default']				 = 'Uso por defecto';
$_['text_merchant_id']				 = 'ID Comercio';
$_['text_subaccount']				 = 'Subcuenta';
$_['text_secret']					 = 'Shared secret';
$_['text_card_visa']				 = 'Visa';
$_['text_card_master']				 = 'Mastercard';
$_['text_card_amex']				 = 'American Express';
$_['text_card_switch']				 = 'Switch/Maestro';
$_['text_card_laser']				 = 'Laser';
$_['text_card_diners']				 = 'Diners';
$_['text_capture_ok']				 = 'Captura con &eacute;xito';
$_['text_capture_ok_order']			 = 'Captura correcta, el estado del pedido se actualizo - se instal&oacute;';
$_['text_rebate_ok']				 = 'Descuento correcto';
$_['text_rebate_ok_order']			 = 'Descuento correcto, estado del pedido actualizo con el descuento';
$_['text_void_ok']					 = 'Correcto, el estado del pedido se actualizo como anulado';
$_['text_settle_auto']				 = 'Auto';
$_['text_settle_delayed']			 = 'Retrasado';
$_['text_settle_multi']				 = 'Multi';
$_['text_url_message']				 = 'Debe proporcionar la URL de la tienda a GlobalPay antes de publicarla';
$_['text_payment_info']				 = 'Informaci&oacute;n Pago';
$_['text_capture_status']			 = 'Captura Pago';
$_['text_void_status']				 = 'Pago anulado';
$_['text_rebate_status']			 = 'Pago descontado';
$_['text_order_ref']				 = 'Pedido ref';
$_['text_order_total']				 = 'Total autorizado';
$_['text_total_captured']			 = 'Total capturado';
$_['text_transactions']				 = 'Transacciones';
$_['text_column_amount']			 = 'Importe';
$_['text_column_type']				 = 'Tipo';
$_['text_column_date_added']		 = 'Creado';
$_['text_confirm_void']				 = '¿Seguro que desea anular el pago?';
$_['text_confirm_capture']			 = '¿Seguro que desea capturar el pago?';
$_['text_confirm_rebate']			 = '¿Seguro que desea descontar el pago?';
$_['text_globalpay']                 = '<a target="_blank" href="https://resourcecentre.globaliris.com/getting-started.php?id=OpenCart"><img src="view/image/payment/globalpay.png" alt="Globalpay" title="Globalpay" style="border: 1px solid #EEEEEE;" /></a>';

// Entry
$_['entry_merchant_id']				 = 'ID Comercio';
$_['entry_secret']					 = 'Shared secret';
$_['entry_rebate_password']			 = 'Rebate password';
$_['entry_total']					 = 'Total';
$_['entry_sort_order']				 = 'Orden';
$_['entry_geo_zone']				 = 'Geo zona';
$_['entry_status']					 = 'Estado';
$_['entry_debug']					 = 'Debug logging';
$_['entry_live_demo']				 = 'En Vivo / Demo';
$_['entry_auto_settle']				 = 'Clase de liquidaci&oacute;n';
$_['entry_card_select']				 = 'Seleccionar Tarjeta';
$_['entry_tss_check']				 = 'TSS checks';
$_['entry_live_url']				 = 'URL conexi&oacute;n En vivo';
$_['entry_demo_url']				 = 'URL conexi&oacute;n Demo';
$_['entry_status_success_settled']	 = 'Correcto - settled';
$_['entry_status_success_unsettled'] = 'Correcto - not settled';
$_['entry_status_decline']			 = 'Denegado';
$_['entry_status_decline_pending']	 = 'Denegado - offline auth';
$_['entry_status_decline_stolen']	 = 'Denegado - tarjeta perdida o robada';
$_['entry_status_decline_bank']		 = 'Denegado - error banco';
$_['entry_status_void']				 = 'Anulado';
$_['entry_status_rebate']			 = 'Descontar';
$_['entry_notification_url']		 = 'URL Notificaci&oacute;n';

// Help
$_['help_total']					 = 'Inporte m&iacute;nimo que se debe alcanzar para que se active esta opci&oacute;n';
$_['help_card_select']				 = 'Pida al usuario  que debe de elegir su tipo de tarjeta antes de redireccionarlos';
$_['help_notification']				 = 'Debe suministrar esta URL a GlobalPay para recibir notificaciones de pago';
$_['help_debug']					 = 'Habilitar escritura en un archivo de registro. Usted debe desactivarla siempre, a menos que se indique lo contrario';
$_['help_dcc_settle']				 = 'Si su subcuenta es DCC debe utilizar Autosettle';

// Tab
$_['tab_api']					     = 'API Detalles';
$_['tab_account']		     		 = 'Cuentas';
$_['tab_order_status']				 = 'Orden';
$_['tab_payment']					 = 'Configuraci&oacute;n Pago';
$_['tab_advanced']					 = 'Avanzado';

// Button
$_['button_capture']				 = 'Captura';
$_['button_rebate']					 = 'Descuento / reembolso';
$_['button_void']					 = 'Vac&iacute;o';

// Error
$_['error_merchant_id']				 = 'ID Comercio requerido';
$_['error_secret']					 = 'Shared secret requerido';
$_['error_live_url']				 = 'URL En vivo requerida';
$_['error_demo_url']				 = 'URL Demo requerida';
$_['error_data_missing']			 = 'Faltan datos';
$_['error_use_select_card']			 = 'Debe tener "Seleccionar tarjeta" habilitado para el enrutamiento de subcuenta seg&uacute;n el tipo de tarjeta';