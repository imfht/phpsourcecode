<?php


// Heading
$_['heading_title']			= 'Authorize.Net (SIM)';

// Text
$_['text_extension']		= 'Extensions';
$_['text_success']			= 'Félicitations, vous avez modifié le module de paiement Authorize.Net (SIM) avec succès !';
$_['text_edit']				= 'Éditer Authorize.Net (SIM)';
$_['text_authorizenet_sim']	= '<a onclick="window.open(\'http://reseller.authorize.net/application/?id=5561142\');"><img src="view/image/payment/authorizenet.png" alt="Authorize.Net" title="Authorize.Net" style="border: 1px solid #EEEEEE;" /></a>';

// Entry
$_['entry_merchant']		= 'N° d’identification marchand';
$_['entry_key']				= 'Clé de transaction';
$_['entry_callback']		= 'Réponse de l’URL de relais';
$_['entry_md5']				= 'Valeur du hachage MD5';
$_['entry_test']			= 'Mode de test';
$_['entry_total']			= 'Total';
$_['entry_order_status']	= 'État de la commande';
$_['entry_geo_zone']		= 'Zone géographique';
$_['entry_status']			= 'État';
$_['entry_sort_order']		= 'Classement';

// Help
$_['help_callback']			= 'Veuiller vous connecter à cette adresse <a href="https://secure.authorize.net" target="_blank" class="txtLink">https://secure.authorize.net</a>.';
$_['help_md5']				= 'La fonction de hachage MD5 permet d’authentifier qu’une réponse à une transaction a bien été reçue par Authorize.Net. Veuillez vous connecter et définir celui-ci <a href="https://secure.authorize.net" target="_blank" class="txtLink">https://secure.authorize.net</a>.(Optionnel)';
$_['help_total']			= 'Montant total de la commande devant être atteint avant que ce mode de paiement ne devienne actif.';

// Error
$_['error_permission']		= 'Attention, vous n’avez pas la permission de modifier le module de paiement  Authorize.Net (AIM) !';
$_['error_merchant']		= 'Attention, le N° d’identification marchand est requis !';
$_['error_key']				= 'Attention, la clé de transaction est requise !';