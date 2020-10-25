<?php

// Heading
$_['heading_title']					= 'First Data EMEA Web Service API';

// Text
$_['text_firstdata_remote']			= '<img src="view/image/payment/firstdata.png" alt="First Data" title="First Data" style="border: 1px solid #EEEEEE;" />';
$_['text_extension']				= 'Extensions';
$_['text_success']					= 'Félicitations, vous avez modifié le module de paiement First Data !';
$_['text_edit']                     = 'Modifier le module de paiement First Data EMEA Web Service API';
$_['text_card_type']				= 'Type de carte';
$_['text_enabled']					= 'Autorisé';
$_['text_merchant_id']				= 'Identifiant marchand';
$_['text_subaccount']				= 'Sous-compte';
$_['text_user_id']					= 'Identifiant utilisateur';
$_['text_capture_ok']				= 'L’achat a été un succès';
$_['text_capture_ok_order']			= 'L’achat a été un succès, état de la commande mis à jour à « réglée »';
$_['text_refund_ok']				= 'Le remboursement a été un succès';
$_['text_refund_ok_order']			= 'Le remboursement a été un succès, état de la commande mis à jour à « remboursée »';
$_['text_void_ok']					= 'L’annulation a été un succès, état de la commande mis à jour à « annulée »';
$_['text_settle_auto']				= 'Vente';
$_['text_settle_delayed']			= 'Avant authentification';
$_['text_mastercard']				= 'Mastercard';
$_['text_visa']						= 'Visa';
$_['text_diners']					= 'Diners';
$_['text_amex']						= 'American Express';
$_['text_maestro']					= 'Maestro';
$_['text_payment_info']				= 'Information de paiement';
$_['text_capture_status']			= 'Paiement récupéré';
$_['text_void_status']				= 'Paiement annulé';
$_['text_refund_status']			= 'Paiement remboursé';
$_['text_order_ref']				= 'Référence commande';
$_['text_order_total']				= 'Total autorisé';
$_['text_total_captured']			= 'Total récupéré';
$_['text_transactions']				= 'Transactions';
$_['text_column_amount']			= 'Montant';
$_['text_column_type']				= 'Type';
$_['text_column_date_added']		= 'Date d’ajout';
$_['text_confirm_void']				= 'Êtes-vous sûr de vouloir annuler le paiement ?';
$_['text_confirm_capture']			= 'Êtes-vous sûr de vouloir récupérer le paiement ?';
$_['text_confirm_refund']			= 'Êtes-vous sûr de vouloir rembourser le paiement ?';

// Entry
$_['entry_certificate_path']		= 'Chemin du Certificat';
$_['entry_certificate_key_path']	= 'Chemin de la clé privé';
$_['entry_certificate_key_pw']		= 'Mot de passe de clé privé';
$_['entry_certificate_ca_path']		= 'Chemin CA';
$_['entry_merchant_id']				= 'Identifiant marchand';
$_['entry_user_id']					= 'Numéro d’utilisateur';
$_['entry_password']				= 'Mot de passe';
$_['entry_total']					= 'Total';
$_['entry_sort_order']				= 'Classement';
$_['entry_geo_zone']				= 'Zone géographique';
$_['entry_status']					= 'État';
$_['entry_debug']					= 'Enregistrement de débogage';
$_['entry_auto_settle']				= 'Type de règlement';
$_['entry_status_success_settled']	= 'Succès - réglé';
$_['entry_status_success_unsettled']= 'Succès - non réglé';
$_['entry_status_decline']			= 'Refusé';
$_['entry_status_void']				= 'Annulé';
$_['entry_status_refund']			= 'Remboursé';
$_['entry_enable_card_store']		= 'Activez les jetons de stockage de carte';
$_['entry_cards_accepted']			= 'Types de carte acceptés';

// Help
$_['help_total']					= 'Le total que la commande doit atteindre avant que cette méthode de paiement devienne active';
$_['help_certificate']				= 'Les Certificats et la clé privée ne doivent pas être stoqués dans un répertoire publique de votre site';
$_['help_card_select']				= 'Demander à l’utilisateur de choisir son type de carte avant d’être redirigé';
$_['help_notification']				= 'Vous devez fournir cette URL à First Data pour recevoir des notifications de paiement';
$_['help_debug']					= 'En autorisant le débogage, vous autorisez l’écriture des données sensibles dans un fichier journal. Vous devez toujours désactiver sauf avis contraire';
$_['help_settle']					= 'Si vous utilisez avant authentification, vous devez effectuer une action après authentification dans les 3-5 jours sinon votre transaction sera abandonnée';

// Tab
$_['tab_account']					= 'Information de l’API';
$_['tab_order_status']				= 'État de la commande';
$_['tab_payment']					= 'Paramètres de paiement';

// Button
$_['button_capture']				= 'Récupérer';
$_['button_refund']					= 'Rembourser';
$_['button_void']					= 'Annuler';

// Error
$_['error_merchant_id']				= 'L’identifiant marchand est requis';
$_['error_user_id']					= 'Le numéro d’utilisateur est requis';
$_['error_password']				= 'Le mot de passe est requis';
$_['error_certificate']				= 'Le chemin du Certificat est requis';
$_['error_key']						= 'La clé du Certificat est requise';
$_['error_key_pw']					= 'Le mot de passe de la clé du Certificat est requis';
$_['error_ca']						= 'L’autorité du Certificat (CA) est requise';