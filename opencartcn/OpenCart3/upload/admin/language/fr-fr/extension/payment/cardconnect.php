<?php


// Heading
$_['heading_title']                 = 'CardConnect';

// Tab
$_['tab_settings']                  = 'Paramètres';
$_['tab_order_status']              = 'État de la commande';

// Text
$_['text_extension']                = 'Extensions';
$_['text_success']                  = 'Félicitations, vous avez modifié le module de paiement CardConnect avec succès !';
$_['text_edit']                     = 'Éditer CardConnect';
$_['text_cardconnect']              = '<a href="http://www.cardconnect.com" target="_blank"><img src="view/image/payment/cardconnect.png" alt="CardConnect" title="CardConnect"></a>';
$_['text_payment']                  = 'Paiement';
$_['text_authorize']                = 'Autoriser';
$_['text_live']                     = 'Mode réel';
$_['text_test']                     = 'Mode test';
$_['text_no_cron_time']             = 'La tâche cron n’a pas encore été exécutée';
$_['text_payment_info']             = 'Information paiement';
$_['text_payment_method']           = 'Mode de paiement';
$_['text_card']                     = 'Carte';
$_['text_echeck']                   = 'eCheck';
$_['text_reference']                = 'Référence';
$_['text_update']                   = 'Mise à jour';
$_['text_order_total']              = 'Total de la commande';
$_['text_total_captured']           = 'Total Capturé';
$_['text_capture_payment']          = 'Capture du paiement';
$_['text_refund_payment']           = 'Remboursement du paiement';
$_['text_void']                     = 'Nul';
$_['text_transactions']             = 'Transactions';
$_['text_column_type']              = 'Type';
$_['text_column_reference']         = 'Référence';
$_['text_column_amount']            = 'Montant';
$_['text_column_status']            = 'État';
$_['text_column_date_modified']     = 'Date de modification';
$_['text_column_date_added']        = 'Date d’ajout';
$_['text_column_update']            = 'Mise à jour';
$_['text_column_void']              = 'Nul';
$_['text_confirm_capture']          = 'Êtes-vous sûr de vouloir capturer le paiement ?';
$_['text_confirm_refund']           = 'Êtes-vous sûr de vouloir rembourser le paiement ?';
$_['text_inquire_success']          = 'La demande a réussie';
$_['text_capture_success']          = 'La requête de capture a réussie';
$_['text_refund_success']           = 'La requête de remboursement a réussie';
$_['text_void_success']             = 'La requête d’annulation a réussie';

// Entry
$_['entry_merchant_id']             = 'Identifiant marchand';
$_['entry_api_username']            = 'Nom de l’utilisateur de l’API';
$_['entry_api_password']            = 'Mot de passe de l’API';
$_['entry_token']                   = 'Jeton secret';
$_['entry_transaction']             = 'Transaction';
$_['entry_environment']             = 'Environnement';
$_['entry_site']                    = 'Site';
$_['entry_store_cards']             = 'Cartes de la boutique';
$_['entry_echeck']                  = 'eCheck';
$_['entry_total']                   = 'Total';
$_['entry_geo_zone']                = 'Zone géographique';
$_['entry_status']                  = 'État';
$_['entry_logging']                 = 'Debug Logging';
$_['entry_sort_order']              = 'Classement';
$_['entry_cron_url']                = 'URL de la tâche cron';
$_['entry_cron_time']               = 'Dernière tâche cron';
$_['entry_order_status_pending']    = 'En attente';
$_['entry_order_status_processing'] = 'En traitement';

// Help
$_['help_merchant_id']              = 'Your personal CardConnect account merchant ID.';
$_['help_api_username']             = 'Your username to access the CardConnect API.';
$_['help_api_password']             = 'Your password to access the CardConnect API.';
$_['help_token']                    = 'Enter a random token for security or use the one generated.';
$_['help_transaction']              = 'Choose \'Payment\' to capture the payment immediately or \'Authorize\' to have to approve it.';
$_['help_site']                     = 'This determines the first part of the API URL. Only change if instructed.';
$_['help_store_cards']              = 'Whether you want to store cards using tokenization.';
$_['help_echeck']                   = 'Whether you want to offer the ability to pay using an eCheck.';
$_['help_total']                    = 'Montant total de la commande devant être atteint avant que ce mode de paiement ne devienne actif.. Must be a value with no currency sign.';
$_['help_logging']                  = 'Enabling debug will write sensitive data to a log file. You should always disable unless instructed otherwise.';
$_['help_cron_url']                 = 'Set a cron job to call this URL so that the orders are auto-updated. It is designed to be ran a few hours after the last capture of a business day.';
$_['help_cron_time']                = 'This is the last time that the cron job URL was executed.';
$_['help_order_status_pending']     = 'The order status when the order has to be authorized by the merchant.';
$_['help_order_status_processing']  = 'The order status when the order is automatically captured.';

// Button
$_['button_inquire_all']            = 'Inquire All';
$_['button_capture']                = 'Capture';
$_['button_refund']                 = 'Refund';
$_['button_void_all']               = 'Nul All';
$_['button_inquire']                = 'Inquire';
$_['button_void']                   = 'Nul';

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
