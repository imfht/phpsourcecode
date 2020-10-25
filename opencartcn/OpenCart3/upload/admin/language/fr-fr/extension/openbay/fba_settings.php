<?php


// Heading
$_['heading_title']				= 'Paramètres';
$_['text_openbay']				= 'OpenBay Pro';
$_['text_fba']					= 'Expédition par Amazon';

// Text
$_['text_success']     			= 'Vos paramètres ont bien été sauvegardés';
$_['text_status']         		= 'État';
$_['text_account_ok']  			= 'La connexion à l’expédition par Amazon s’est bien déroulée';
$_['text_api_ok']       		= 'La connexion à l’API connection s’est bien déroulée';
$_['text_api_status']           = 'Connexion à l’API';
$_['text_edit']           		= 'Éditer les paramètres de l’expédition par Amazon';
$_['text_standard']           	= 'Standard';
$_['text_expedited']           	= 'Expédition';
$_['text_priority']           	= 'Priorité';
$_['text_fillorkill']           = 'Remplir ou détruire';
$_['text_fillall']           	= 'Tout remplir';
$_['text_fillallavailable']     = 'Remplir tous les disponibles';
$_['text_prefix_warning']     	= 'Ne pas modifier ce paramètre après que les ordres ont été envoyés à Amazon, il faut définir celui-ci lorsque vous installez.';
$_['text_disabled_cancel']     	= 'Désactivé - Ne pas annuler automatiquement les expéditions';
$_['text_validate_success']     = 'Vos données API fonctionnent correctement ! Vous devez appuyer sur enregistrer pour vous assurer que les réglages sont bien enregistrés.';
$_['text_register_banner']      = 'Cliquez ici si vous désirez vous inscrire à un compte';

// Entry
$_['entry_api_key']            	= 'Clé API';
$_['entry_encryption_key']		= 'Clé de cryptage 1';
$_['entry_encryption_iv']		= 'Clé de cryptage 2';
$_['entry_account_id']          = 'Identifiant compte';
$_['entry_send_orders']         = 'Envoyer les commandes automatiquement';
$_['entry_fulfill_policy']      = 'Politique sur les expéditions';
$_['entry_shipping_speed']      = 'Livraison rapide par défaut';
$_['entry_debug_log']           = 'Activer la journalisation de débogage';
$_['entry_new_order_status']    = 'Nouveau déclencheur d’expédition';
$_['entry_order_id_prefix'] 	= 'Préfixe de l’identifiant commande';
$_['entry_only_fill_complete'] 	= 'Tous les articles doivent être expédiés par Amazon';

// Help
$_['help_api_key']            	= 'Ceci est votre clé API, obtenue à partir de votre compte OpenBay Pro';
$_['help_encryption_key']		= 'C’est votre clé de cryptage # 1, obtenez-la dans votre zone de compte OpenBay Pro';
$_['help_encryption_iv']		= 'C’est votre clé de cryptage # 2, obtenez-la dans votre zone de compte OpenBay Pro';
$_['help_account_id']           = 'Ceci est l’identifiant de votre compte correspondant au compte Amazon enregistré pour OpenBay Pro, obtenu à partir de votre compte OpenBay Pro';
$_['help_send_orders']  		= 'Les commandes contenant les produits correspondant à "expédié par Amazon" seront envoyés à Amazon automatiquement';
$_['help_fulfill_policy']  		= 'Politique d’expédition par défaut :<br />► Tout remplir - Tous les articles expédiables dans l’ordre d’expédition sont livrées. L’ordre d’expédition reste dans l’état de "traitement" jusqu’à ce que tous les éléments s’y trouvant soient expédiés par Amazon ou annulées par le vendeur.<br />► Remplir tous les disponibles - Tous les articles expédiables dans l’ordre d’expédition sont livrés, tous les articles irréalisables dans l’ordre sont annulés par Amazon.<br />► Remplir ou détruire - Si un article dans un ordre d’expédition est jugé irréalisable avant toute expédition, l’ordre d’expédition se déplace à l’état de "en attente" (le processus de récupération des unités de l’inventaire a commencé ), puis l’ensemble de la commande est considéré comme irréalisable. Toutefois, si un article dans un ordre d’exécution est déterminé à être irréalisable après une expédition, l’ordre d’expédition se déplace à l’état de "en attente", Amazon annule autant d’ordre d’expédition que possible.)';
$_['help_shipping_speed']  		= 'Ceci est la catégorie de livraison rapide par défaut à appliquer aux nouvelles commandes, différents niveaux de service peuvent entraîner des coûts différents.';
$_['help_debug_log']  		    = 'Les journaux de débogage enregistreront les informations dans un fichier journal sur les actions fait par le module. Cela doit être laissé activé pour aider à trouver la cause de tous les problèmes.';
$_['help_new_order_status']  	= 'Ceci est l’état de la commande qui va déclencher l’ordre doit être créé pour la réalisation. Assurez-vous que cela est d’utiliser un statut seulement après avoir reçu le paiement.';
$_['help_order_id_prefix']  	= 'Avoir un préfixe de commande permettra d’identifier les commandes qui sont venus de votre magasin et non par d’autres intégrations. Ceci est très utile lorsque les marchands vendent sur de nombreuses places de marchés et utilisent l’expédition par Amazon';
$_['help_only_fill_complete']  	= 'Cela ne fera que permettre aux commandes à envoyer pour expédition que si tous les articles dans l’ordre d’expédition sont jumelés à un article expédié par Amazon. Si un article ne l’est pas alors tout l’ordre d’expédition restera vacant.';

// Error
$_['error_api_connect']         = 'Impossible de se connecter à l’API';
$_['error_account_info']    	= 'Impossible de vérifier la connexion de l’API pour l’expédition par Amazon';
$_['error_api_key']    			= 'La clé de l’API est invalide';
$_['error_api_account_id']    	= 'L’identifiant du compte est invalide';
$_['error_encryption_key']		= 'La clé de cryptage # 1 est invalide';
$_['error_encryption_iv']		= 'La clé de cryptage # 2 est invalide';
$_['error_validation']    		= 'Il y a une erreur qui empêche la validation de vos coordonnées';

// Tab
$_['tab_api_info']            	= 'Détails de l’API';

// Button
$_['button_verify']            	= 'Vérifier les détails';