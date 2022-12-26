<?php
// Heading
$_['heading_title']              = 'Елемент за внос';
$_['text_openbay']               = 'OpenBay Pro';
$_['text_ebay']                  = 'иБей';

// Text
$_['text_sync_import_line1']     = '<strong> Внимание! </strong> Това ще импортира всички ваши продукти на eBay и ще изгради категория структура във вашия магазин. Препоръчваме да изтриете всички категории и продукти, преди да изпълните тази опция. <br /> Структурата на категориите е от нормалните категории на eBay, а не от категориите ви на магазини (ако имате магазин eBay). Можете да преименувате, премахвате и редактирате внесените категории, без да се отразявате на продуктите на eBay.';
$_['text_sync_import_line3']     = 'Трябва да сте сигурни, че вашият сървър може да приема и обработва големи размери на POST данни. 1000 елемента на eBay е с размер около 40 МБ, ще трябва да изчислите това, което ви е необходимо. Ако вашето обаждане се провали, вероятно вашата настройка е твърде малка. Ограничението на вашата PHP памет трябва да бъде около 128Mb.';
$_['text_sync_server_size']      = 'Понастоящем вашият сървър може да приеме:';
$_['text_sync_memory_size']      = 'Вашият PHP памет ограничение:';
$_['text_import_confirm']		 = 'Това ще импортира всички ваши продукти на eBay като нови продукти, сигурен ли си? ? Това НЕ МОЖЕ да бъде undone! Уверете се, че имате резервно копие first!';
$_['text_import_notify']		 = 'Заявката ви за импортиране бе изпратена за обработка. Импортирането отнема около 1 час на 1000 елемента.';
$_['text_import_images_msg1']    = 'изображенията предстои да бъдат импортирани /копирани от eBay. Обновете тази страница, ако броят не намалява след това';
$_['text_import_images_msg2']    = 'Натисни тук';
$_['text_import_images_msg3']    = 'и изчакайте. Повече информация за причините за това може да се намери <a href="http://shop.openbaypro.com/index.php?route=information/faq&topic=8_45" target="_blank"> тук </a>';

// Entry
$_['entry_import_item_advanced'] = 'Получавайте разширени данни';
$_['entry_import_categories']    = 'Категории за импортиране';
$_['entry_import_description']	 = 'Импортиране на описания на елементи';
$_['entry_import']				 = 'Импортиране на иБей елементи';

// Buttons
$_['button_import']				 = 'внос';
$_['button_complete']			 = 'пълен';

// Help
$_['help_import_item_advanced']  = 'Ще внесете до 10 пъти по-дълго, за да импортирате елементи. Импортира тегла, размери, ISBN и още, ако има такива';
$_['help_import_categories']     = 'Изгражда структура от категории във вашия магазин от категориите eBay';
$_['help_import_description']    = 'Това ще импортира всичко, включително HTML, посещение на броячи и т.н.';

// Error
$_['error_import']               = 'Не успя да се зареди';
$_['error_maintenance']			 = 'Вашият магазин е в режим на поддръжка. Импортирането ще се окаже неуспешно!';
$_['error_ajax_load']			 = 'Не може да се свърже със сървъра';
$_['error_validation']			 = 'Трябва да се регистрирате за означението си за API и да активирате модула.';