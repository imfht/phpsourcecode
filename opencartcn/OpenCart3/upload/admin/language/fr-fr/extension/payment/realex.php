<?php


// Heading
$_['heading_title']					= 'Realex Redirect';

// Text
$_['text_extension']				= 'Extensions';
$_['text_success']					= 'Félicitations, vous avez modifié le module de paiement Realex avec succès !';
$_['text_edit']                     = 'Éditer le module de paiement Realex Redirect';
$_['text_live']						= 'Mode réel';
$_['text_demo']						= 'Mode test';
$_['text_card_type']				= 'Type carte';
$_['text_enabled']					= 'Activé';
$_['text_use_default']				= 'Utiliser par défaut';
$_['text_merchant_id']				= 'Identifiant marchand';
$_['text_subaccount']				= 'Sous-compte';
$_['text_secret']					= 'Mot secret';
$_['text_card_visa']				= 'Visa';
$_['text_card_master']				= 'Mastercard';
$_['text_card_amex']				= 'American Express';
$_['text_card_switch']				= 'Switch/Maestro';
$_['text_card_laser']				= 'Laser';
$_['text_card_diners']				= 'Diners';
$_['text_capture_ok']				= 'La capture a été réalisé avec succès';
$_['text_capture_ok_order']			= 'La capture a été réalisé avec succès, l’état de la commande est passé à « réglée »';
$_['text_rebate_ok']				= 'Le remboursement a été réalisé avec succès';
$_['text_rebate_ok_order']			= 'Le remboursement a été réalisé avec succès, l’état de la commande est passé à « remboursée »';
$_['text_void_ok']					= 'L’annulation a été réalisé avec succès, l’état de la commande est passé à « annulée »';
$_['text_settle_auto']				= 'Auto';
$_['text_settle_delayed']			= 'Différé';
$_['text_settle_multi']				= 'Multi';
$_['text_url_message']				= 'Vous devez fournir l’URL du magasin dans votre gestionnaire de compte Realex avant d’utiliser le mode réel';
$_['text_payment_info']				= 'Information de paiement';
$_['text_capture_status']			= 'Paiement capturé';
$_['text_void_status']				= 'Paiement annulé';
$_['text_rebate_status']			= 'Paiement remboursé';
$_['text_order_ref']				= 'Référence de la commande';
$_['text_order_total']				= 'Total autorisé';
$_['text_total_captured']			= 'Total capturé';
$_['text_transactions']				= 'Transactions';
$_['text_column_amount']			= 'Montant';
$_['text_column_type']				= 'Type';
$_['text_column_date_added']		= 'Créé';
$_['text_confirm_void']				= 'Êtes-vous sûr de vouloir annuler le paiement ?';
$_['text_confirm_capture']			= 'Êtes-vous sûr de vouloir capturer le paiement ?';
$_['text_confirm_rebate']			= 'Êtes-vous sûr de vouloir rembourser le paiement ?';
$_['text_realex']					= '<a target="_blank" href="http://www.realexpayments.co.uk/partner-refer?id=opencart"><img src="view/image/payment/realex.png" alt="Realex" title="Realex" style="border: 1px solid #EEEEEE;" /></a>';

// Entry
$_['entry_merchant_id']				= 'Identifiant marchand';
$_['entry_secret']					= 'Mot secret';
$_['entry_rebate_password']			= 'Mot de passe pour le remboursement';
$_['entry_total']					= 'Total';
$_['entry_sort_order']				= 'Classement';
$_['entry_geo_zone']				= 'Zone géographique';
$_['entry_status']					= 'État';
$_['entry_debug']					= 'Journal de débogage';
$_['entry_live_demo']				= 'Réel/Test';
$_['entry_auto_settle']				= 'Type de réglement';
$_['entry_card_select']				= 'Sélectionnez une carte';
$_['entry_tss_check']				= 'Contrôles TSS';
$_['entry_live_url']				= 'URL de connexion du mode réel';
$_['entry_demo_url']				= 'URL de connexion du mode démo';
$_['entry_status_success_settled']	= 'Succès - réglé';
$_['entry_status_success_unsettled']= 'Succès - non réglé';
$_['entry_status_decline']			= 'Refusé';
$_['entry_status_decline_pending']	= 'Refusé - Déconnecté';
$_['entry_status_decline_stolen']	= 'Refusé - Carte perdue ou volée';
$_['entry_status_decline_bank']		= 'Refusé - Erreur banque';
$_['entry_status_void']				= 'Annulé';
$_['entry_status_rebate']			= 'Remboursé';
$_['entry_notification_url']		= 'URL de notification';

// Help
$_['help_total']					= 'Le montant total que la commande doit atteindre avant que ce mode de paiement devienne actif.';
$_['help_card_select']				= 'Demander à l’utilisateur de choisir son type de carte avant qu’il ne soit redirigé';
$_['help_notification']				= 'Vous devez fournir cette URL à Realex pour obtenir des notifications de paiement';
$_['help_debug']					= 'Autoriser le débogage permettra d’écrire des données sensibles dans un journal. Vous devez toujours désactiver sauf indication contraire.';
$_['help_dcc_settle']				= 'Si votre sous-compte est activé DCC vous devez utiliser Autosettle';

// Tab
$_['tab_api']						= 'Détails de l’API';
$_['tab_account']					= 'Comptes';
$_['tab_order_status']				= 'État de la commande';
$_['tab_payment']					= 'Paramètres de paiement';
$_['tab_advanced']					= 'Avancé';

// Button
$_['button_capture']				= 'Capturer';
$_['button_rebate']					= 'Rembourser';
$_['button_void']					= 'Annuler';

// Error
$_['error_merchant_id']				= 'L’identifiant marchand est requis !';
$_['error_secret']					= 'Le mot secret est requis';
$_['error_live_url']				= 'L’URL de connexion du mode réel est requise !';
$_['error_demo_url']				= 'L’URL de connexion du mode démo est requise !';
$_['error_data_missing']			= 'Données manquantes';
$_['error_use_select_card']			= 'Vous devez avoir activé « Sélectionnez la carte » dans le sous-compte pour le routage par type de carte pour travailler.';