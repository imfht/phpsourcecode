<?php


// Heading
$_['heading_title']					= 'First Data EMEA Connect (3DSecure activé)';

// Text
$_['text_extension']				= 'Extensions';
$_['text_success']					= 'Félicitations, vous avez modifié le module de paiement First Data avec succès !';
$_['text_edit']                     = 'Éditer le module de paiement First Data EMEA Connect (3DSecure activé)';
$_['text_notification_url']			= 'URL de notification';
$_['text_live']						= 'Mode réel';
$_['text_demo']						= 'Démo';
$_['text_enabled']					= 'Activé';
$_['text_merchant_id']				= 'Identifiant marchand';
$_['text_secret']					= 'Mot secret';
$_['text_capture_ok']				= 'L’achat a été réalisé avec succès';
$_['text_capture_ok_order']			= 'L’achat a été réalisé avec succès, l’état de la commande est passé à « réglée »';
$_['text_void_ok']					= 'L’annulation a été réalisée avec succès, l’état de la commande est passé à « annulée »';
$_['text_settle_auto']				= 'Vente';
$_['text_settle_delayed']			= 'Pré autorisation';
$_['text_success_void']				= 'La transaction a été annulée';
$_['text_success_capture']			= 'La transaction a été effectuée';
$_['text_firstdata']				= '<img src="view/image/payment/firstdata.png" alt="First Data" title="First Data" style="border: 1px solid #EEEEEE;" />';
$_['text_payment_info']				= 'Informations de paiement';
$_['text_capture_status']			= 'Paiement effectué';
$_['text_void_status']				= 'Paiement annulé';
$_['text_order_ref']				= 'Référence de la commande';
$_['text_order_total']				= 'Total autorisé';
$_['text_total_captured']			= 'Total récupéré';
$_['text_transactions']				= 'Transactions';
$_['text_column_amount']			= 'Montant';
$_['text_column_type']				= 'Type';
$_['text_column_date_added']		= 'Date d’ajout';
$_['text_confirm_void']				= 'Êtes-vous sûr de vouloir annuler le paiement ?';
$_['text_confirm_capture']			= 'Êtes-vous sûr de vouloir récupérer le paiement ?';

// Entry
$_['entry_merchant_id']				= 'Identifiant marchand';
$_['entry_secret']					= 'Mot secret';
$_['entry_total']					= 'Total';
$_['entry_sort_order']				= 'Classement';
$_['entry_geo_zone']				= 'Zone géographique';
$_['entry_status']					= 'État';
$_['entry_debug']					= 'Enregistrement de débogage';
$_['entry_live_demo']				= 'Production / Démo';
$_['entry_auto_settle']				= 'Type de règlement';
$_['entry_card_select']				= 'Sélectionnez une carte';
$_['entry_tss_check']				= 'Contrôles TSS';
$_['entry_live_url']				= 'URL de connexion du mode réel';
$_['entry_demo_url']				= 'URL de connexion du mode démo';
$_['entry_status_success_settled']	= 'Succès - réglé';
$_['entry_status_success_unsettled']= 'Succès - non réglé';
$_['entry_status_decline']			= 'Refusé';
$_['entry_status_void']				= 'Annulé';
$_['entry_enable_card_store']		= 'Activez les jetons de stockage de carte';

// Help
$_['help_total']					= 'Montant total de la commande devant être atteint avant que ce mode de paiement ne devienne actif.';
$_['help_notification']				= 'Vous devez fournir cette URL à First Data pour recevoir des notifications de paiement';
$_['help_debug']					= 'Autoriser le débogage vous permettra d’écrire des données sensibles dans un journal. Vous devez toujours désactiver sauf indication contraire.';
$_['help_settle']					= 'Si vous utilisez «  avant autorisation  », vous devez effectuer l’action «  après authentification  » dans 3 à 5 jours sinon votre transaction sera abandonnée';

// Tab
$_['tab_account']					= 'Information sur l’API';
$_['tab_order_status']				= 'État de la commande';
$_['tab_payment']					= 'Paramètres de paiement';
$_['tab_advanced']					= 'Options avancées';

// Button
$_['button_capture']				= 'Récupérer';
$_['button_void']					= 'Annuler';

// Error
$_['error_merchant_id']				= 'L’identifiant marchand est requis !';
$_['error_secret']					= 'Le mot secret partagé est requis';
$_['error_live_url']				= 'L’URL de connexion du mode réel est requise !';
$_['error_demo_url']				= 'L’URL de connexion du mode démo est requise !';
$_['error_data_missing']			= 'Données manquantes';
$_['error_void_error']				= 'Annulation de la transaction impossible';
$_['error_capture_error']			= 'Impossible de récupérer la transaction';