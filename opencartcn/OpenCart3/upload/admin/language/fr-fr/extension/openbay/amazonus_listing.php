<?php


// Heading
$_['heading_title']						= 'Nouvelle annonce sur Amazon';
$_['text_title_advanced'] 				= 'Annonces avancées';
$_['text_openbay']						= 'OpenBay Pro';
$_['text_amazon']						= 'Amazon US';

// Button
$_['button_new']						= 'Créer un nouveau produit';
$_['button_amazon_price']				= 'Obtenez le prix d’Amazon';
$_['button_list'] 						= 'Annonces sur Amazon';
$_['button_remove_error'] 				= 'Supprimer les messages d’erreurs';
$_['button_save_upload'] 				= 'Sauvegarder et charger';
$_['button_browse'] 					= 'Parcourir';
$_['button_saved_listings'] 			= 'Voir les annonces sauvegardées';
$_['button_remove_links'] 				= 'Supprimer les liens';
$_['button_create_new_listing'] 		= 'Créer une nouvelle liste';

// Help
$_['help_sku'] 							= 'Identifiant unique du produit assigné par le marchand.';
$_['help_restock_date'] 				= 'Il s’agit de la date à laquelle vous serez en mesure d’expédier les articles au client. Cette date ne doit pas être plus de 30 jours à compter de la date indiquée ou les commandes reçues automatiquement peuvent être annulées.';
$_['help_sale_price'] 					= 'Le prix de vente doit avoir une date de début et une date de fin.';

// Text
$_['text_products_sent'] 				= 'Les produits ont été envoyés pour traitement.';
$_['button_view_on_amazon'] 			= 'Voir sur Amazon';
$_['text_list'] 						= 'Liste Amazon';
$_['text_new'] 							= 'Neuf';
$_['text_used_like_new'] 				= 'Occasion - Comme neuf';
$_['text_used_very_good'] 				= 'Occasion - Très bon état';
$_['text_used_good'] 					= 'Occasion - Bon état';
$_['text_used_acceptable'] 				= 'Occasion - Acceptable';
$_['text_collectible_like_new'] 		= 'Collection - Comme neuf';
$_['text_collectible_very_good'] 		= 'Collection - Très bon état';
$_['text_collectible_good'] 			= 'Collection - Bon état';
$_['text_collectible_acceptable'] 		= 'Collection - Acceptable';
$_['text_refurbished'] 					= 'Restauré';
$_['text_product_not_sent'] 			= 'Le produit n’a pas été envoyé à Amazon. Raison : %s';
$_['text_not_in_catalog'] 				= 'Ou, si ce n’est pas dans le catalogue   ';
$_['text_placeholder_search'] 			= 'Entrer le nom du produit, UPC, EAN, ISBN ou ASIN';
$_['text_placeholder_condition'] 		= 'Utilisez cet emplacement pour décrire l’état de vos produits.';
$_['text_characters'] 					= 'caractères';
$_['text_uploaded'] 					= 'Annonces sauvegardées téléchargées !';
$_['text_saved_local'] 					= 'Annonces sauvegardées mais pas encore téléchargées';
$_['text_product_sent'] 				= 'Le produit a été envoyé avec succès à Amazon.';
$_['text_links_removed'] 				= 'Liens des produits Amazon supprimés';
$_['text_product_links'] 				= 'Liens des produits';
$_['text_has_saved_listings'] 			= 'Ce produit a une ou plusieurs annonces enregistrées et ne peut être téléchargé.';
$_['text_edit_heading'] 				= 'Éditer la liste';

// Column
$_['column_image'] 						= 'Image';
$_['column_asin'] 						= 'ASIN';
$_['column_price'] 						= 'Prix';
$_['column_action'] 					= 'Action';
$_['column_name'] 						= 'Nom du produit';
$_['column_model'] 						= 'Modèle';
$_['column_combination'] 				= 'Combinaison variante';
$_['column_sku_variant']				= 'Référence SKU Variant';
$_['column_sku'] 						= 'Référence SKU';
$_['column_amazon_sku'] 				= 'Référence SKU de l’article sur Amazon';

// Entry
$_['entry_sku'] 						= 'Référence SKU';
$_['entry_condition'] 					= 'État de l’article';
$_['entry_condition_note'] 				= 'Note sur l’état';
$_['entry_price'] 						= 'Prix';
$_['entry_sale_price'] 					= 'Prix de vente';
$_['entry_sale_date'] 					= 'Plage de date de vente';
$_['entry_quantity'] 				  	= 'Quantité';
$_['entry_start_selling'] 			  	= 'Disponible à partir de';
$_['entry_restock_date'] 			  	= 'Date de ré-approvisionnement';
$_['entry_country_of_origin'] 		  	= 'Pays d’origine';
$_['entry_release_date'] 			  	= 'Date de sortie';
$_['entry_from'] 					  	= 'Date à partir de';
$_['entry_to'] 						  	= 'Date jusqu’à';
$_['entry_product'] 				  	= 'Annonce pour le produit';
$_['entry_category'] 				    = 'Catégorie Amazon';

// Tab
$_['tab_main'] 						    = 'Page principale';
$_['tab_required'] 					    = 'Information requise';
$_['tab_additional'] 				    = 'Options additionnelles';

// Error
$_['error_text_missing'] 			  	= 'Vous devez entrer des informations de recherche.';
$_['error_data_missing'] 			  	= 'Les données requises sont manquantes.';
$_['error_missing_asin'] 			  	= 'L’ASIN est manquant';
$_['error_marketplace_missing'] 	  	= 'Veuillez sélectionner une place de marché.';
$_['error_condition_missing'] 		  	= 'Veuillez sélectionner un état pour le produit.';
$_['error_fetch'] 					  	= 'Impossible d’obtenir les données.';
$_['error_amazonus_price'] 			  	= 'Impossible d’obtenir le prix sur Amazon US.';
$_['error_stock'] 					  	= 'Vous ne pouvez pas mettre un article ayant moins de 1 en quantité en stock.';
$_['error_sku'] 					  	= 'Vous devez entrer un SKU pour cet article.';
$_['error_price'] 					  	= 'Vous devez entrer un prix pour cet article.';
$_['error_connecting'] 				  	= 'Attention : Il y a un problème de connexion aux serveurs de l’API Welford médias. Veuillez vérifier vos paramètres de l’ extension OpenBay Pro Amazon. Si le problème persiste, veuillez contacter le support.';
$_['error_required'] 				  	= 'Le champ est requis !';
$_['error_not_saved'] 				  	= 'L’annonce n’a pas été enregistré. Vérifiez votre entrée.';
$_['error_char_limit'] 				  	= 'Dépassement de la limite de caractères autorisés.';
$_['error_length'] 					  	= 'La longueur minimun est';
$_['error_upload_failed'] 			  	= 'L’ajout du produit avec ce SKU a échoué : %s. Raison : %s Processus de Téléchargement Annulé.';
$_['error_load_nodes'] 				  	= 'Impossible de charger les noeuds de navigation';
$_['error_not_searched'] 				= 'Rechercher des articles correspondants avant d’essayer d’annoncer. Les articles doivent correspondre aux articles du catalogue Amazon.';