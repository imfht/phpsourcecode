<?php


// Text
$_['text_title']				= 'Carte de crédit';
$_['text_credit_card']			= 'Détails de la carte';
$_['text_wait']					= 'Veuillez patienter !';

// Entry
$_['entry_cc_number']			= 'Numéro de la carte';
$_['entry_cc_name']				= 'Nom du titulaire de la carte';
$_['entry_cc_expire_date']		= 'Date d’expiration de la carte';
$_['entry_cc_cvv2']				= 'Code de sécurité de la carte (CVV2)';

// Help
$_['help_start_date']			= '(si disponible)';
$_['help_issue']				= '(uniquement pour les cartes Maestro et Solo)';

// Text
$_['text_result']				= 'Résultat : ';
$_['text_approval_code']		= 'Code d’approbation: ';
$_['text_reference_number']		= 'Référence : ';
$_['text_card_number_ref']		= '4 derniers chiffre de la carte : xxxx ';
$_['text_card_brand']			= 'Fournisseur de la carte : ';
$_['text_response_code']		= 'Code de réponse : ';
$_['text_fault']				= 'Message d’erreur : ';
$_['text_error']				= 'Message d’erreur : ';
$_['text_avs']					= 'Vérification de l’adresse : ';
$_['text_address_ppx']			= 'Aucune donnée d’adresses fournie ou adresse non vérifiée par l’émetteur de la carte';
$_['text_address_yyy']			= 'L’émetteur de la carte a confirmé que la rue et le code postal correspondent avec leurs dossiers';
$_['text_address_yna']			= 'L’émetteur de la carte a confirmé que la rue correspond à leurs dossiers, mais que le code postal ne correspond pas';
$_['text_address_nyz']			= 'L’émetteur de la carte a confirmé que le code postal correspond à leurs dossiers, mais que la rue ne correspond pas';
$_['text_address_nnn']			= 'La rue et le code postal ne correspondent pas aux dossiers sur l’émetteur de la carte';
$_['text_address_ypx']			= 'L’émetteur de la carte a confirmé que la rue correspond à leurs dossiers. L’émetteur n’a pas vérifié le code postal';
$_['text_address_pyx']			= 'L’émetteur de la carte a confirmé que le code postal correspond à leurs dossiers. L’émetteur n’a pas vérifié la rue';
$_['text_address_xxu']			= 'L’émetteur de la carte n’a pas vérifié les informations AVS';
$_['text_card_code_verify']		= 'Code de sécurité: ';
$_['text_card_code_m']			= 'Le code de sécurité de la carte correspond';
$_['text_card_code_n']			= 'Le code de sécurité de la carte ne correspond pas';
$_['text_card_code_p']			= 'Non traitée';
$_['text_card_code_s']			= 'Le commerçant a indiqué que le code de sécurité n’est pas présent sur la carte';
$_['text_card_code_u']			= 'L’émetteur de la carte n’a pas certifié et/ou n’a pas fourni les clés de cryptage';
$_['text_card_code_x']			= 'Aucune réponse de l’association de la carte de crédit n’a été reçu';
$_['text_card_code_blank']		= 'Une réponse à blanc doit indiquer qu’aucun code n’a été envoyé et qu’il n’y avait aucune indication que le code n’était pas présent sur ​​la carte.';
$_['text_card_accepted']		= 'Cartes acceptées : ';
$_['text_card_type_m']			= 'Mastercard';
$_['text_card_type_v']			= 'Visa (Crédit/Débit/Electron/Delta)';
$_['text_card_type_c']			= 'Diners';
$_['text_card_type_a']			= 'American Express';
$_['text_card_type_ma']			= 'Maestro';
$_['text_card_new']				= 'Nouvelle carte';
$_['text_response_proc_code']	= 'Code processeur : ';
$_['text_response_ref']			= 'Numéro de référence : ';

// Error
$_['error_card_number']			= 'Veuillez vérifier que le numéro de la carte soit valide';
$_['error_card_name']			= 'Veuillez vérifier que le nom du titulaire de la carte soit valide';
$_['error_card_cvv']			= 'Veuillez vérifier que le code CVV de la carte soit valide';
$_['error_failed']				= 'Impossible de procéder au paiement, veuillez prendre contact avec le marchand';
?>