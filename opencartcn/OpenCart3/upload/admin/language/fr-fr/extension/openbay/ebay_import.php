<?php


// Heading
$_['heading_title']                     = 'Import d’article sur eBay';
$_['text_openbay']                      = 'OpenBay Pro';
$_['text_ebay']                         = 'eBay';

// Text
$_['text_sync_import_line1']            = 'Attention, cela importera tous vos produits eBay et construira une structure de catégorie dans votre magasin. Il est conseillé de supprimer toutes les catégories et les produits avant d’exécuter cette option.<br />La structure de catégorie des catégories d’eBay normales, pas les catégories de votre boutique (si vous avez une boutique eBay). Vous pouvez renommer, supprimer et modifier les catégories importées sans affecter vos produits eBay.';
$_['text_sync_import_line3']            = 'Vous devez vous assurer que votre serveur peut accepter et traiter les grandes tailles de données POST. 1000 objets eBay est d’environ 40 Mo en taille, vous aurez besoin de calculer ce que vous désirez. Si votre appel échoue, alors il est probable que votre réglage est trop faible. Votre limite de mémoire de PHP doit être d’environ 128Mb.';
$_['text_sync_server_size']             = 'Actuellement votre serveur peut accepter : ';
$_['text_sync_memory_size']             = 'Votre limite de mémoire PHP : ';
$_['text_import_confirm']				= 'Cela va importer tous vos objets eBay ainsi que de nouveaux produits, êtes-vous sûr ? Cela ne peut pas être annulé ! Assurez-vous d’avoir d’abord une sauvegarde !';
$_['text_import_notify']				= 'Votre demande d’importation a été envoyé pour traitement. Une importation dure environ 1 heure par 1000 articles.';
$_['text_import_images_msg1']           = 'Les images sont en cours d’importation et de copie sur eBay. Actualisez cette page, si le nombre ne diminue pas';
$_['text_import_images_msg2']           = 'cliquer ici';
$_['text_import_images_msg3']           = 'et patientez. Plus d’informations sur les raisons de ce qui s’est passé peut être trouvé sur <a href="http://shop.openbaypro.com/index.php?route=information/faq&topic=8_45" target="_blank">here</a>';

// Entry
$_['entry_import_item_advanced']        = 'Obtenir les données détailées';
$_['entry_import_categories']         	= 'Importer les catégories';
$_['entry_import_description']			= 'Importer les descriptions des articles';
$_['entry_import']						= 'Importer les articles eBay';

// Button
$_['button_import']						= 'Importer';
$_['button_complete']					= 'Complèter';

// Help
$_['help_import_item_advanced']        	= 'Prendra 10 fois plus de temps pour importer les articles. Importe les poids, les tailles, ISBN et plus si disponible.';
$_['help_import_categories']         	= 'Construction d’une tructure de catégories dans votre boutique à partir des catégories eBay.';
$_['help_import_description']         	= 'Tout sera importé y compris HTML, compteurs etc...';

// Error
$_['error_import']                   	= 'Échec lors du chargement.';
$_['error_maintenance']					= 'Votre boutique est en maintenance. L’importation échouera !';
$_['error_ajax_load']					= 'Impossible de se connecter au serveur';
$_['error_validation']					= 'Vous devez vous inscrire pour obtenir votre clé API et activer le module.';