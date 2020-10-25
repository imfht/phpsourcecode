<?php


// Text
$_['text_title']			= 'Facture Klarna';
$_['text_terms_fee']		= '<span id="klarna_invoice_toc"></span> (+%s)<script type="text/javascript">var terms = new Klarna.Terms.Invoice({el: ’klarna_invoice_toc’, eid: ’%s’, country: ’%s’, charge: %s});</script>';
$_['text_terms_no_fee']		= '<span id="klarna_invoice_toc"></span><script type="text/javascript">var terms = new Klarna.Terms.Invoice({el: ’klarna_invoice_toc’, eid: ’%s’, country: ’%s’});</script>';
$_['text_additional']		= 'Klarna requiert des informations supplémentaires avant de pouvoir procéder à votre commande.';
$_['text_male']				= 'Homme';
$_['text_female']			= 'Femme';
$_['text_year']				= 'Année';
$_['text_month']			= 'Mois';
$_['text_day']				= 'Jour';
$_['text_comment']			= 'Identifiant de facturation Klarna: %s\n%s/%s: %.4f';

// Entry
$_['entry_gender']			= 'Genre';
$_['entry_pno']				= 'N° personnel';
$_['entry_dob']				= 'Date de naissance';
$_['entry_phone_no']		= 'N° de téléphone';
$_['entry_street']			= 'Rue';
$_['entry_house_no']		= 'N° de rue';
$_['entry_house_ext']		= 'Informations complémentaires';
$_['entry_company']			= 'Numéro d’immatriculation société';

// Help
$_['help_pno']				= 'Veuillez entrer ici votre N° de sécurité sociale.';
$_['help_phone_no']			= 'Veuillez entrer ici votre N° de téléphone.';
$_['help_street']			= 'Veuillez noter que la livraison ne peut pas avoir lieu à l’adresse enregistrée lorsque vous payez par Klarna.';
$_['help_house_no']			= 'Veuillez entrer ici votre N° de rue.';
$_['help_house_ext']		= 'Veuillez entrer ici les informations complémentaires concernant votre complément d’adresse. Ex. : A, B, C, Rouge, Bleu ect...';
$_['help_company']			= 'Veuillez entrer ici votre N° d’immatriculation société.';

// Error
$_['error_deu_terms']		= 'Vous devez accepter la politique de confidentialité de Klarna (Datenschutz)';
$_['error_address_match']	= 'Les adresses de facturation et d’expédition doivent correspondre si vous souhaitez utiliser les paiements par Klarna';
$_['error_network']			= 'Une erreur est survenue lors de la connexion à Klarna. Veuillez réessayer plus tard.';
?>