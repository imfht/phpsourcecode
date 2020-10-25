<?php


// Heading
$_['heading_title']								= 'Système Anti-Fraude MaxMind';

// Text
$_['text_extension']							= 'Extensions';
$_['text_success']								= 'Félicitations, vous avez modifié le système Anti-Fraude MaxMind avec succès !';
$_['text_edit']									= 'Éditer le système Anti-Fraude MaxMind';
$_['text_signup']								= 'MaxMind est un service de détection de fraude. Vous pouvez vous <a href="http://www.maxmind.com/?rId=opencart" target="_blank"><u>inscrire ici</u></a> pour obtenir votre clé API.';
$_['text_country_match']						= 'Correspondance de pays :';
$_['text_country_code']							= 'Code pays :';
$_['text_high_risk_country']					= 'Pays à haut risque :';
$_['text_distance']								= 'Distance :';
$_['text_ip_region']							= 'Région de l’IP :';
$_['text_ip_city']								= 'Ville de l’IP :';
$_['text_ip_latitude']							= 'Latitude de l’IP :';
$_['text_ip_longitude']							= 'Longitude de l’IP :';
$_['text_ip_isp']								= 'Fournisseur accès internet (FAI) :';
$_['text_ip_org']								= 'Organisation des IP :';
$_['text_ip_asnum']								= 'ASNUM :';
$_['text_ip_user_type']							= 'Type d’utilisateur de l’IP :';
$_['text_ip_country_confidence']				= 'IP des pays de confiance :';
$_['text_ip_region_confidence']					= 'IP des régions de confiance :';
$_['text_ip_city_confidence']					= 'IP des villes de confiance :';
$_['text_ip_postal_confidence']					= 'IP des codes postaux de confiance :';
$_['text_ip_postal_code']						= 'IP du code postal :';
$_['text_ip_accuracy_radius']					= 'Exactitude du rayon de l’IP :';
$_['text_ip_net_speed_cell']					= 'Vitesse de l’IP du mobile sur le net';
$_['text_ip_metro_code']						= 'IP du Code Metro :';
$_['text_ip_area_code']							= 'IP de l’Indicatif régional :';
$_['text_ip_time_zone']							= 'IP Du fuseau horaire :';
$_['text_ip_region_name']						= 'IP du nom de la région :';
$_['text_ip_domain']							= 'IP du domaine :';
$_['text_ip_country_name']						= 'IP du nom du pays :';
$_['text_ip_continent_code']					= 'Code IP du continent :';
$_['text_ip_corporate_proxy']					= 'IP du proxy d’entreprise :';
$_['text_anonymous_proxy']						= 'Proxy anonyme :';
$_['text_proxy_score']							= 'Niveau du proxy :';
$_['text_is_trans_proxy']						= 'Is du proxy transparent :';
$_['text_free_mail']							= 'Courriel libre :';
$_['text_carder_email']							= 'Courriel :';
$_['text_high_risk_username']					= 'Nom d’utilisateur de risque elevé :';
$_['text_high_risk_password']					= 'Mot de passe de risque elevé :';
$_['text_bin_match']							= 'Correspondance binaire :';
$_['text_bin_country']							= 'Pays binaire :';
$_['text_bin_name_match']						= 'Nom de la correspondance binaire :';
$_['text_bin_name']								= 'Nom binaire :';
$_['text_bin_phone_match']						= 'Concordance de téléphone binaire :';
$_['text_bin_phone']							= 'Téléphone binaire :';
$_['text_customer_phone_in_billing_location']	= 'Numéro de téléphone client dans la Localisation de la facturation :';
$_['text_ship_forward']							= 'Livraison mise en avant:';
$_['text_city_postal_match']					= 'Concordance du code postal de la ville :';
$_['text_ship_city_postal_match']				= 'Concordance du code postal de la ville de livraison :';
$_['text_score']								= 'Niveau :';
$_['text_explanation']							= 'Explication :';
$_['text_risk_score']							= 'Niveau de risque :';
$_['text_queries_remaining']					= 'Requêtes restantes :';
$_['text_maxmind_id']							= 'Identifiant Maxmind :';
$_['text_error']								= 'Erreur :';

// Entry
$_['entry_key']									= 'Clé de licence MaxMind';
$_['entry_score']								= 'Niveau de risque';
$_['entry_order_status']						= 'État de la commande';
$_['entry_status']								= 'État';

// Help
$_['help_order_status']							= 'Les commandes ayant un niveau de risque elevés seront assignés à cet état de commande et ne seront pas autorisés à atteindre l’état de commande complète automatiquement.';
$_['help_country_match']						= 'Savoir si le pays de l’adresse IP correspond à l’adresse du pays de facturation (décalage = risque le plus élevé).';
$_['help_country_code']							= 'Code pays de l’adresse IP.';
$_['help_high_risk_country']					= 'Savoir si l’adresse IP correspond au Ghana, Nigeria, ou Vietnam.';
$_['help_distance']								= 'Distance en kilomètres entre l’adresse IP et la l’adresse de facturation (grande distance = risque élevé).';
$_['help_ip_region']							= 'Estimation du Département / Région de l’adresse IP.';
$_['help_ip_city']								= 'Estimation de la Ville de l’adresse IP.';
$_['help_ip_latitude']							= 'Estimation de la Latitude de l’adresse IP.';
$_['help_ip_longitude']							= 'Estimation de la Longitude de l’adresse IP.';
$_['help_ip_isp']								= 'FAI de l’adresse IP.';
$_['help_ip_org']								= 'Entreprise de l’adresse IP.';
$_['help_ip_asnum']								= 'Estimation de l’ASN de l’adresse IP';
$_['help_ip_user_type']							= 'Estimation du Type d’utilisateur de l’adresse IP.';
$_['help_ip_country_confidence']				= 'Représente notre confiance que l’emplacement du pays est correct.';
$_['help_ip_region_confidence']					= 'Représente notre confiance que l’emplacement du département / région est correct.';
$_['help_ip_city_confidence']					= 'Représente notre confiance que l’emplacement de la ville est correct.';
$_['help_ip_postal_confidence']					= 'Représente notre confiance que l’emplacement du code postal est correct.';
$_['help_ip_postal_code']						= 'Estimation du code postal de l’adresse IP.';
$_['help_ip_accuracy_radius']					= 'Distance moyenne entre le lieu réel de l’utilisateur final en utilisant l’adresse IP et l’emplacement en miles renvoyé par la base de données GeoIP City.';
$_['help_ip_net_speed_cell']					= 'Estimation du type de réseau de l’adresse IP.';
$_['help_ip_metro_code']						= 'Estimation du code métro de l’adresse IP.';
$_['help_ip_area_code']							= 'Estimation du code de la zone de l’adresse IP.';
$_['help_ip_time_zone']							= 'Estimation du fuseau horaire de l’adresse IP.';
$_['help_ip_region_name']						= 'Estimation du nom du département / région de l’adresse IP.';
$_['help_ip_domain']							= 'Estimation du domaine de l’adresse IP.';
$_['help_ip_country_name']						= 'Estimation du nom du pays de l’adresse IP.';
$_['help_ip_continent_code']					= 'Estimation du code continent de l’adresse IP.';
$_['help_ip_corporate_proxy']					= 'Savoir si l’IP appartient à un proxy d’entreprise et si celui-ci figure dans la base de données.';
$_['help_anonymous_proxy']						= 'Savoir si l’IP appartient à un proxy anonyme (proxy anonyme = niveau de très haut rique).';
$_['help_proxy_score']							= 'Probabilité que l’adresse IP appartienne à un Proxy transparent.';
$_['help_is_trans_proxy']						= 'Savoir si l’adresse IP se trouve dans notre base de données des serveurs proxy transparents connus, retourné si forwardedIP est passé comme une entrée.';
$_['help_free_mail']							= 'Savoir si le courriel est libre de fournisseur de messagerie électronique (courriel gratuit = risque plus élevé).';
$_['help_carder_email']							= 'Savoir si le courriel se trouve dans notre base de données de courriel à haut risque.';
$_['help_high_risk_username']					= 'Savoir si l’utilisateur MD5 se trouve dans la base de données des noms d’utilisateurs à haut risque. Sera seulement retourné si l’utilisateur MD5 est inclus dans les entrées.';
$_['help_high_risk_password']					= 'Savoir si le mot de passe MD5 se trouve dans la base de données des mots de passe à haut risque. Sera seulement retourné si le mot de passe MD5 est inclus dans les entrées.';
$_['help_bin_match']							= 'Savoir si le pays de la banque émettrice basé sur le nombre BIN correspond à l’adresse du pays de facturation.';
$_['help_bin_country']							= 'Code Pays de la banque qui a émis la carte de crédit basé sur le nombre BIN.';
$_['help_bin_name_match']						= 'Savoir si le nom de délivrance bancaire correspond au nom BIN entré. La valeur de retour sur « Oui » fournit l’indication positive que le titulaire de la carte de crédit est bien possesseur de celle-ci.';
$_['help_bin_name']								= 'Nom de la banque qui a émis la carte de crédit basé sur le nombre BIN. Disponible pour environ 96% du nombre BIN.';
$_['help_bin_phone_match']						= 'Savoir si le numéro de téléphone du service client correspond au numéro de téléphone BIN entré. La valeur de retour sur « Oui » fournit l’indication positive que le titulaire de la carte de crédit est bien possesseur de celle-ci.';
$_['help_bin_phone']							= 'Numéro de téléphone du service clientèle inscrit à dos de la carte de crédit. Disponible pour environ 75% du nombre BIN. Dans certains cas, le numéro de téléphone peut être retourné.';
$_['help_customer_phone_in_billing_location']	= 'Savoir si le numéro de téléphone du client à une correspondance avec le code postal de l’adresse de facturation. Une valeur de retour sur « Oui » fournit l’indication positive que le numéro de téléphone indiqué appartient au titulaire de la carte. Une valeur de retour sur « Non » indique que le numéro de téléphone peut être dans une zone différente, ou ne peut pas être référencé dans notre base de données. « Non trouvé » est renvoyé lorsque le préfixe du numéro de téléphone ne peut pas être aucunement trouvé dans notre base de données. Actuellement nous ne supportons que les numéros de téléphone US.';
$_['help_ship_forward']							= 'Savoir si l’adresse d’expédition se trouve dans la base de courrier électronique connue.';
$_['help_city_postal_match']					= 'Savoir si la ville et le département / région de facturation correspond au code postal. Actuellement disponible pour les adresses US seulement, renvoie une chaîne vide si en dehors des États-Unis.';
$_['help_ship_city_postal_match']				= 'Savoir si la ville et le département / région de livraison correspond au code postal. Actuellement disponible pour les adresses US seulement, renvoie une chaîne vide si en dehors des États-Unis.';
$_['help_score']								= 'Score de la fraude sur la base de l’ensemble des sorties énumérées ci-dessus. Ceci est la partition originale de la fraude, et est basé sur une formule simple. Il a été remplacé par le score de risque (voir ci-dessous), mais est maintenu pour la compatibilité ascendante.';
$_['help_explanation']							= 'Brève explication de la partition, détaillant quels facteurs ont contribué à celle-ci, selon notre formule. Veuillez noter ce qui correspond à la partition, pas le riskScore.';
$_['help_risk_score']							= 'Nouveau score de fraude représentant la probabilité estimée que la commande est la fraude, basé sur de l’analyse des dernières transactions minFraud. Nécessite une mise à niveau pour les clients qui se sont inscrits avant Février de 2007.';
$_['help_queries_remaining']					= 'Nombre de requêtes restant à votre compte, celles-ci peuvent être utilisées pour vous alerter lorsque vous pourriez avoir besoin d’ajouter d’autres requêtes à votre compte.';
$_['help_maxmind_id']							= 'Nombre de requêtes restant à votre compte, celles-ci peuvent être utilisées pour vous alerter lorsque vous pourriez avoir besoin d’ajouter plus de requêtes à votre identifiant de compte unique, utilisé pour faire référence à des transactions lors de la déclaration de retour des activités frauduleuses à MaxMind. Ce rapport aidera MaxMind améliorer son service et va activer une fonctionnalité prévue pour personnaliser la formule  de notation de fraude basé sur votre historique de rejet de débit.';
$_['help_error']								= 'Retourne une chaîne d’erreur avec un message d’avertissement ou la raison pour laquelle la demande a échoué ..';

// Error
$_['error_permission']							= 'Attention, vous n’avez pas la permission de modifier le Système Anti-Fraude MaxMind !';
$_['error_key']									= 'Clé de l’API requise !';