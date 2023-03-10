<?php

// Heading
$_['heading_title']                                     = 'Fyrkant';
$_['heading_title_transaction']                         = 'Visa transaktion #%s';

// Help
$_['help_total']                                        = 'Kassa totalt ordern måste nås innan betalningsmetoden blir aktiv.';
$_['help_local_cron']                                   = 'Sätt in det här kommandot i fliken CRON på din webbserver. Ställ in det för att köra minst en gång per dag.';
$_['help_remote_cron']                                  = 'Använd den här webbadressen för att skapa en CRON-uppgift via en webbaserad CRON-tjänst. Ställ in det för att köra minst en gång per dag.';
$_['help_recurring_status']                             = 'Aktivera att periodiska återkommande betalningar tillåts. <br /> OBS! Du måste också ställa in en daglig CRON-uppgift.';
$_['help_cron_email']                                   = 'En sammanfattning av den återkommande uppgiften kommer att skickas till detta e-postmeddelande efter slutförandet.';
$_['help_cron_email_status']                            = 'Aktivera att du får en sammanfattning efter varje CRON-uppgift.';
$_['help_notify_recurring_success']                     = 'Meddela kunder om framgångsrika återkommande transaktioner.';
$_['help_notify_recurring_fail']                        = 'Meddela kunder om misslyckade återkommande transaktioner.';

// Tab
$_['tab_setting']                                       = 'inställningar';
$_['tab_transaction']                                   = 'transaktioner';
$_['tab_cron']                                          = 'CRON';
$_['tab_recurring']                                     = 'Återkommande betalningar';

// Text
$_['text_access_token_expires_label']                   = 'Access token löper ut';
$_['text_access_token_expires_placeholder']             = 'Inte inställd';
$_['text_acknowledge_cron']                             = 'Jag bekräftar att jag har skapat en automatiserad CRON-uppgift med en av metoderna ovan.';
$_['text_admin_notifications']                          = 'Admin-meddelanden';
$_['text_authorize_label']                              = 'Godkänna';
$_['text_canceled_success']                             = 'Framgång: Du har framgångsrikt avbrutit denna betalning!';
$_['text_capture']                                      = 'Fånga';
$_['text_client_id_help']                               = 'Hämta det här från sidan Hantera program på kvadrat';
$_['text_client_id_label']                              = 'Square Application ID';
$_['text_client_id_placeholder']                        = 'Square Application ID';
$_['text_client_secret_help']                           = 'Hämta det här från sidan Hantera program på kvadrat';
$_['text_client_secret_label']                          = 'OAuth Application Secret';
$_['text_client_secret_placeholder']                    = 'OAuth Application Secret';
$_['text_confirm_action']                               = 'Är du säker?';
$_['text_confirm_cancel']                               = 'Är du säker på att du vill avbryta de återkommande betalningarna?';
$_['text_confirm_capture']                              = 'Du håller på att fånga följande antal: <strong> %s </strong>. Klicka på OK för att fortsätta.';
$_['text_confirm_refund']                               = 'Var god ange skälen till återbetalningen:';
$_['text_confirm_void']                                 = 'Du håller på att radera följande antal: <strong> %s </strong>. Klicka på OK för att fortsätta.';
$_['text_connected']                                    = 'Ansluten';
$_['text_connected_info']                               = 'Återanslut om du vill byta konton eller manuellt återkalla tilläggets åtkomst från Square App-konsolen. Uppdatera åtkomsttoken manuellt om det har varit nära 45 dagar sedan den senaste försäljningen eller återanslutningen.';
$_['text_connection_section']                           = 'Fyrkantig anslutning';
$_['text_connection_success']                           = 'Framgångsrikt ansluten!';
$_['text_cron_email']                                   = 'Skicka uppgiftsuppsättning till det här e-postmeddelandet:';
$_['text_cron_email_status']                            = 'Skicka e-post sammanfattning:';
$_['text_customer_notifications']                       = 'Kundmeddelanden';
$_['text_debug_disabled']                               = 'Inaktiverad';
$_['text_debug_enabled']                                = 'Aktiverad';
$_['text_debug_help']                                   = 'API-förfrågningar och svar kommer att loggas i OpenCart-felsökningen. Använd detta endast för debugging och utvecklingsändamål.';
$_['text_debug_label']                                  = 'Felsökningsloggning';
$_['text_delay_capture_help']                           = 'Tillåt endast transaktioner eller utföra avgifter automatiskt';
$_['text_delay_capture_label']                          = 'Överföringstyp';
$_['text_disabled_connect_help_text']                   = 'Klientens id och hemliga är obligatoriska fält.';
$_['text_edit_heading']                                 = 'Redigera kvadrat';
$_['text_enable_sandbox_help']                          = 'Aktivera sandlådsläge för att testa transaktioner';
$_['text_enable_sandbox_label']                         = 'Aktivera sandlåsläge';
$_['text_executables']                                  = 'CRON-exekveringsmetoder';
$_['text_extension']                                    = 'Extensions';
$_['text_extension_status']                             = 'Förlängningsstatus';
$_['text_extension_status_disabled']                    = 'Inaktiverad';
$_['text_extension_status_enabled']                     = 'Aktiverad';
$_['text_extension_status_help']                        = 'Aktivera eller inaktivera betalningsmetoden';
$_['text_insert_amount']                                = 'Vänligen ange återbetalningsbeloppet. Max: %s i %s:';
$_['text_loading']                                      = 'Laddar data .. Vänligen vänta ..';
$_['text_loading_short']                                = 'Vänta..';
$_['text_local_cron']                                   = 'Metod #1 - CRON Uppgift:';
$_['text_location_error']                               = 'Det uppstod ett fel när du försökte synkronisera platser och token: %s';
$_['text_location_help']                                = 'Välj vilken konfigurerad fyrkantig plats som ska användas för transaktioner. Måste ha kortbehandlingsfunktioner aktiverat.';
$_['text_location_label']                               = 'Plats';
$_['text_manage']                                       = 'Kreditkortstransaktion (kvadrat)';
$_['text_manage_tooltip']                               = 'Se detaljer /Capture /Void /Refund';
$_['text_merchant_info_section_heading']                = 'Mechant Information';
$_['text_merchant_name_label']                          = 'Handlarens namn';
$_['text_merchant_name_placeholder']                    = 'Inte inställd';
$_['text_no_appropriate_locations_warning']             = 'Det finns inga platser som kan konfigurera online-kortbehandling på ditt kvadratkonto.';
$_['text_no_location_selected_warning']                 = 'Det finns ingen vald plats.';
$_['text_no_locations_label']                           = 'Inga giltiga platser';
$_['text_no_transactions']                              = 'Inga transaktioner har loggats än.';
$_['text_not_connected']                                = 'Ej ansluten';
$_['text_not_connected_info']                           = 'Genom att klicka på den här knappen kopplar du den här modulen till ditt kvadratkonto och aktiverar tjänsten.';
$_['text_notification_ssl']                             = 'Se till att du har SSL-aktiverad på din kassa sida. Annars fungerar inte förlängningen.';
$_['text_notify_recurring_fail']                        = 'Återkommande transaktion misslyckades:';
$_['text_notify_recurring_success']                     = 'Återkommande transaktion framgångsrik:';
$_['text_ok']                                           = 'ok';
$_['text_order_history_cancel']                         = 'En administratör har avbrutit dina återkommande betalningar. Ditt kort debiteras inte längre.';
$_['text_payment_method_name_help']                     = 'Kassa betalningsmetod namn';
$_['text_payment_method_name_label']                    = 'Betalningsmetodsnamn';
$_['text_payment_method_name_placeholder']              = 'Kredit /Betalkort';
$_['text_recurring_info']                               = 'Se till att du konfigurerar en daglig CRON-uppgift med någon av metoderna nedan. CRON-jobb hjälper dig med: <br /> <br /> &bull; Automatisk uppdatering av din API-åtkomsttoken <br /> &bull; Behandling av återkommande transaktioner';
$_['text_recurring_status']                             = 'Status för återkommande betalningar:';
$_['text_redirect_uri_help']                            = 'Klistra in den här länken i fältet Omdirigera URI under Hantera program /oAuth';
$_['text_redirect_uri_label']                           = 'Fyrkantig OAuth-omdirigeringsadress';
$_['text_refresh_access_token_success']                 = 'Uppdaterat anslutningen till ditt kvadratkonto.';
$_['text_refresh_token']                                = 'Återskapa token';
$_['text_refund']                                       = 'Återbetalning';
$_['text_refund_details']                               = 'Återbetalningsuppgifter';
$_['text_refunded_amount']                              = 'Återbetalas: %s. Återbetalningsstatus: %s. Orsak till återbetalningen: %s';
$_['text_refunds']                                      = 'Återbetalningar (%s)';
$_['text_remote_cron']                                  = 'Metod #2 - Remote CRON:';
$_['text_sale_label']                                   = 'Försäljning';
$_['text_sandbox_access_token_help']                    = 'Hämta det här från sidan Hantera program på kvadrat';
$_['text_sandbox_access_token_label']                   = 'Sandbox Access Token';
$_['text_sandbox_access_token_placeholder']             = 'Sandbox Access Token';
$_['text_sandbox_client_id_help']                       = 'Hämta det här från sidan Hantera program på kvadrat';
$_['text_sandbox_client_id_label']                      = 'Sandbox Application ID';
$_['text_sandbox_client_id_placeholder']                = 'Sandbox Application ID';
$_['text_sandbox_disabled_label']                       = 'Inaktiverad';
$_['text_sandbox_enabled']                              = 'Sandbox-läget är aktiverat! Transaktioner verkar gå igenom, men inga avgifter kommer att utföras.';
$_['text_sandbox_enabled_label']                        = 'Aktiverad';
$_['text_sandbox_section_heading']                      = 'Kvadratiska Sandbox Inställningar';
$_['text_select_location']                              = 'Välj plats';
$_['text_settings_section_heading']                     = 'Kvadratinställningar';
$_['text_squareup']                                     = '<a mål="_BLANK" href="https://squareup.com"> <img src="view/image/payment/squareup.png" alt="Square" title="Square" style="border: 1px solid #EEEEEE; "/> </a>';
$_['text_success']                                      = 'Framgång: Du har ändrat Square Payment Module!';
$_['text_success_capture']                              = 'Transaktionen har tagits framgångsrikt!';
$_['text_success_refund']                               = 'Transaktionen återbetalades successfully!';
$_['text_success_void']                                 = 'Transaktionen lyckades framgångsrikt!';
$_['text_token_expired']                                = 'Din fyrkantiga åtkomsttoken har löpt ut! <a href="%s"> Klicka här </a> för att förnya det nu.';
$_['text_token_expiry_warning']                         = 'Din fyrkantiga access token upphör att gälla på %s. <a href="%s"> Klicka här </a> för att förnya det nu.';
$_['text_token_revoked']                                = 'Din kvadratisk åtkomsttoken har löpt ut eller har blivit återkallad! <a href="%s"> Klicka här </a> för att godkänna kvadratförlängningen.';
$_['text_transaction_statuses']                         = 'Transaktionsstatistik';
$_['text_view']                                         = 'Visa mer';
$_['text_void']                                         = 'Ogiltig';
$_['text_na']                                           = 'N /A';
$_['text_no_reason_provided']                           = 'Motivering inte angiven.';

// Statuses
$_['squareup_status_comment_authorized']                = 'Korttransaktionen har godkänts men ännu inte tagits.';
$_['squareup_status_comment_captured']                  = 'Korttransaktionen godkändes och följdes därefter (dvs färdigställd).';
$_['squareup_status_comment_voided']                    = 'Korttransaktionen godkändes och följde därefter bort (dvs avbruten).';
$_['squareup_status_comment_failed']                    = 'Korttransaktionen misslyckades.';

// Entry
$_['entry_total']                                       = 'Total';
$_['entry_geo_zone']                                    = 'Geo Zone';
$_['entry_sort_order']                                  = 'Sorteringsordning';
$_['entry_merchant']                                    = 'Merchant ID';
$_['entry_transaction_id']                              = 'Transaktions ID';
$_['entry_order_id']                                    = 'Order-ID';
$_['entry_partner_solution_id']                         = 'Partner Solution ID';
$_['entry_type']                                        = 'Överföringstyp';
$_['entry_currency']                                    = 'Valuta';
$_['entry_amount']                                      = 'Belopp';
$_['entry_browser']                                     = 'Kundanvändare';
$_['entry_ip']                                          = 'Kundens IP';
$_['entry_date_created']                                = 'Skapat datum';
$_['entry_billing_address_company']                     = 'Billing Company';
$_['entry_billing_address_street']                      = 'Billing Street';
$_['entry_billing_address_city']                        = 'Billing City';
$_['entry_billing_address_postcode']                    = 'Fakturering ZIP';
$_['entry_billing_address_province']                    = 'Faktureringsprovinsen /staten';
$_['entry_billing_address_country']                     = 'Faktureringsland';
$_['entry_status_authorized']                           = 'auktoriserad';
$_['entry_status_captured']                             = 'Fångad';
$_['entry_status_voided']                               = 'ogiltigförklaras';
$_['entry_status_failed']                               = 'misslyckades';
$_['entry_setup_confirmation']                          = 'Inställningsbekräftelse:';

// Error
$_['error_permission']                                  = '<strong> Varning: </strong> Du har inte behörighet att ändra betalning Square!';
$_['error_permission_recurring']                        = '<strong> Varning: </strong> Du har inte behörighet att ändra återkommande betalningar!';
$_['error_transaction_missing']                         = 'Transaktion ej hittad!';
$_['error_no_ssl']                                      = '<strong> Varning: </strong> SSL är inte aktiverat på din adminpanel. Vänligen aktivera den för att slutföra din konfiguration.';
$_['error_user_rejected_connect_attempt']               = 'Anslutningsförsöket avbröts av användaren.';
$_['error_possible_xss']                                = 'Vi upptäckte en eventuell cross-site attack och har avslutat ditt anslutningsförsök. Verifiera ditt användar-ID och hemligt och försök igen med knapparna i adminpanelen.';
$_['error_invalid_email']                               = 'Den angivna e-postadressen är inte giltig!';
$_['error_cron_acknowledge']                            = 'Vänligen bekräfta att du har ställt in ett CRON-jobb.';
$_['error_client_id']                                   = 'App-klient-id är ett obligatoriskt fält';
$_['error_client_secret']                               = 'App-klientens hemlighet är ett obligatoriskt fält';
$_['error_sandbox_client_id']                           = 'Sandbox-klient-id är ett obligatoriskt fält när sandboxläget är aktiverat';
$_['error_sandbox_token']                               = 'Sandbox token är ett obligatoriskt fält när sandboxläge är aktiverat';
$_['error_no_location_selected']                        = 'Platsen är ett obligatoriskt fält';
$_['error_refresh_access_token']                        = 'Ett fel uppstod när du försökte uppdatera tilläggets anslutning till ditt kvadratkonto. Verifiera dina ansökningsuppgifter och försök igen.';
$_['error_form']                                        = 'Vänligen kolla formuläret för fel och försök spara agian.';
$_['error_token']                                       = 'Ett fel uppstod under uppfriskningen av token: %s';
$_['error_no_refund']                                   = 'Återbetalningen misslyckades.';

// Column
$_['column_transaction_id']                             = 'Transaktions ID';
$_['column_order_id']                                   = 'Order-ID';
$_['column_customer']                                   = 'Kund';
$_['column_status']                                     = 'Status';
$_['column_type']                                       = 'Typ';
$_['column_amount']                                     = 'Belopp';
$_['column_ip']                                         = 'IP';
$_['column_date_created']                               = 'Skapat datum';
$_['column_action']                                     = 'Handling';
$_['column_refunds']                                    = 'återbetalning';
$_['column_reason']                                     = 'Anledning';
$_['column_fee']                                        = 'Administrativ avgift';

// Button
$_['button_void']                                       = 'Ogiltig';
$_['button_refund']                                     = 'Återbetalning';
$_['button_capture']                                    = 'Fånga';
$_['button_connect']                                    = 'Ansluta';
$_['button_reconnect']                                  = 'Anslut';
$_['button_refresh']                                    = 'Uppdatera token';
