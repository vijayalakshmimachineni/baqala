<?php

// Heading
$_['heading_title']                                     = 'Piazza';
$_['heading_title_transaction']                         = 'Visualizza transazione #%s';

// Help
$_['help_total']                                        = 'Il checkout totale dell\'ordine deve arrivare prima che questo metodo di pagamento diventi attivo.';
$_['help_local_cron']                                   = 'Inserisci questo comando nella scheda CRON del tuo server web. Configura l\'esecuzione almeno una volta al giorno.';
$_['help_remote_cron']                                  = 'Utilizzare questo URL per configurare un\'attività CRON tramite un servizio CRON basato sul Web. Configura l\'esecuzione almeno una volta al giorno.';
$_['help_recurring_status']                             = 'Abilita per consentire pagamenti periodici ricorrenti. <br /> NOTA: è necessario anche impostare un\'attività CRON giornaliera.';
$_['help_cron_email']                                   = 'Dopo il completamento verrà inviato a questa e-mail un riepilogo dell\'attività ricorrente.';
$_['help_cron_email_status']                            = 'Abilitare per ricevere un riepilogo dopo ogni attività CRON.';
$_['help_notify_recurring_success']                     = 'Notifica ai clienti le transazioni ricorrenti riuscite.';
$_['help_notify_recurring_fail']                        = 'Notifica ai clienti le transazioni ricorrenti non riuscite.';

// Tab
$_['tab_setting']                                       = 'impostazioni';
$_['tab_transaction']                                   = 'Le transazioni';
$_['tab_cron']                                          = 'CRON';
$_['tab_recurring']                                     = 'Pagamenti ricorrenti';

// Text
$_['text_access_token_expires_label']                   = 'Il token di accesso scade';
$_['text_access_token_expires_placeholder']             = 'Non configurato';
$_['text_acknowledge_cron']                             = 'Confermo di aver impostato un\'attività CRON automatizzata utilizzando uno dei metodi sopra riportati.';
$_['text_admin_notifications']                          = 'Notifiche di amministrazione';
$_['text_authorize_label']                              = 'Autorizzare';
$_['text_canceled_success']                             = 'Successo: hai annullato con successo questo pagamento!';
$_['text_capture']                                      = 'Catturare';
$_['text_client_id_help']                               = 'Ottieni questo dalla pagina Gestisci applicazione su Square';
$_['text_client_id_label']                              = 'ID applicazione quadrato';
$_['text_client_id_placeholder']                        = 'ID applicazione quadrato';
$_['text_client_secret_help']                           = 'Ottieni questo dalla pagina Gestisci applicazione su Square';
$_['text_client_secret_label']                          = 'Segreto dell\'applicazione OAuth';
$_['text_client_secret_placeholder']                    = 'Segreto dell\'applicazione OAuth';
$_['text_confirm_action']                               = 'Sei sicuro?';
$_['text_confirm_cancel']                               = 'Sei sicuro di voler annullare i pagamenti ricorrenti?';
$_['text_confirm_capture']                              = 'Stai per acquisire il seguente importo: <strong> %s </strong>. Fare clic su OK per procedere.';
$_['text_confirm_refund']                               = 'Si prega di fornire un motivo per il rimborso:';
$_['text_confirm_void']                                 = 'Stai per annullare il seguente importo: <strong> %s </strong>. Fare clic su OK per procedere.';
$_['text_connected']                                    = 'Collegato';
$_['text_connected_info']                               = 'Riconnetti se desideri cambiare account o hai revocato manualmente l\'accesso a questa estensione dalla console dell\'app Square. Aggiorna manualmente il token di accesso se è stato vicino a 45 giorni dall\'ultima vendita o riconnetti.';
$_['text_connection_section']                           = 'Connessione quadrata';
$_['text_connection_success']                           = 'Connesso con successo!';
$_['text_cron_email']                                   = 'Invia il riepilogo dell\'attività a questa e-mail:';
$_['text_cron_email_status']                            = 'Invia riepilogo e-mail:';
$_['text_customer_notifications']                       = 'Notifiche dei clienti';
$_['text_debug_disabled']                               = 'Disabilitato';
$_['text_debug_enabled']                                = 'Abilitato';
$_['text_debug_help']                                   = 'Le richieste e le risposte API verranno registrate nel log degli errori di OpenCart. Utilizzare questo solo per scopi di debug e di sviluppo.';
$_['text_debug_label']                                  = 'Registrazione di debug';
$_['text_delay_capture_help']                           = 'Autorizza solo le transazioni o addebita automaticamente';
$_['text_delay_capture_label']                          = 'Tipo di transazione';
$_['text_disabled_connect_help_text']                   = 'L\'ID client e il segreto sono campi obbligatori.';
$_['text_edit_heading']                                 = 'Modifica quadrato';
$_['text_enable_sandbox_help']                          = 'Abilita la modalità sandbox per testare le transazioni';
$_['text_enable_sandbox_label']                         = 'Abilita la modalità sandbox';
$_['text_executables']                                  = 'Metodi di esecuzione di CRON';
$_['text_extension']                                    = 'estensioni';
$_['text_extension_status']                             = 'Stato di estensione';
$_['text_extension_status_disabled']                    = 'Disabilitato';
$_['text_extension_status_enabled']                     = 'Abilitato';
$_['text_extension_status_help']                        = 'Abilita o disabilita il metodo di pagamento';
$_['text_insert_amount']                                = 'Si prega di inserire l\'importo del rimborso. Massimo: %s in %s:';
$_['text_loading']                                      = 'Caricamento dati in corso .. Attendere ..';
$_['text_loading_short']                                = 'Attendere prego..';
$_['text_local_cron']                                   = 'Metodo #1 - CRON Task:';
$_['text_location_error']                               = 'Si è verificato un errore durante il tentativo di sincronizzazione di posizioni e token: % s';
$_['text_location_help']                                = 'Seleziona quale posizione quadrata configurata utilizzare per le transazioni. Devono avere abilitate le capacità di elaborazione delle carte.';
$_['text_location_label']                               = 'Posizione';
$_['text_manage']                                       = 'Transazione con carta di credito (quadrato)';
$_['text_manage_tooltip']                               = 'Vedi i dettagli /Cattura /Non valido /Rimborso';
$_['text_merchant_info_section_heading']                = 'Informazioni di Mechant';
$_['text_merchant_name_label']                          = 'Nome del commerciante';
$_['text_merchant_name_placeholder']                    = 'Non configurato';
$_['text_no_appropriate_locations_warning']             = 'Non ci sono posizioni in grado di configurare l\'elaborazione delle carte online nel tuo account Square.';
$_['text_no_location_selected_warning']                 = 'Non esiste una posizione selezionata.';
$_['text_no_locations_label']                           = 'Nessuna località valida';
$_['text_no_transactions']                              = 'Nessuna transazione è stata ancora registrata.';
$_['text_not_connected']                                = 'Non collegata';
$_['text_not_connected_info']                           = 'Facendo clic su questo pulsante, questo modulo verrà collegato al tuo account Square e verrà attivato il servizio.';
$_['text_notification_ssl']                             = 'Assicurati di avere SSL abilitato sulla tua pagina di pagamento. Altrimenti, l\'estensione non funzionerà.';
$_['text_notify_recurring_fail']                        = 'Transazione ricorrente non riuscita:';
$_['text_notify_recurring_success']                     = 'Transazione ricorrente riuscita:';
$_['text_ok']                                           = 'ok';
$_['text_order_history_cancel']                         = 'Un amministratore ha annullato i pagamenti ricorrenti. La tua carta non verrà più addebitata.';
$_['text_payment_method_name_help']                     = 'Controlla il nome del metodo di pagamento';
$_['text_payment_method_name_label']                    = 'Nome del metodo di pagamento';
$_['text_payment_method_name_placeholder']              = 'carta di credito /debito';
$_['text_recurring_info']                               = 'Assicurati di impostare un\'attività CRON giornaliera utilizzando uno dei seguenti metodi. I lavori CRON ti aiutano con: <br /> <br /> &bull; Aggiornamento automatico del token di accesso API <br /> &bull; Elaborazione di transazioni ricorrenti';
$_['text_recurring_status']                             = 'Stato dei pagamenti ricorrenti:';
$_['text_redirect_uri_help']                            = 'Incolla questo link nel campo URI di reindirizzamento in Gestisci applicazione /oAuth';
$_['text_redirect_uri_label']                           = 'URL di reindirizzamento OAuth quadrato';
$_['text_refresh_access_token_success']                 = 'Aggiornato con successo la connessione al tuo account Square.';
$_['text_refresh_token']                                = 'Ricrea token';
$_['text_refund']                                       = 'Rimborso';
$_['text_refund_details']                               = 'Dettagli del rimborso';
$_['text_refunded_amount']                              = 'Rimborso: %s. Stato del rimborso: %s. Motivo del rimborso: % s';
$_['text_refunds']                                      = 'Rimborsi (%s)';
$_['text_remote_cron']                                  = 'Metodo #2 - CRON remoto:';
$_['text_sale_label']                                   = 'saldi';
$_['text_sandbox_access_token_help']                    = 'Ottieni questo dalla pagina Gestisci applicazione su Square';
$_['text_sandbox_access_token_label']                   = 'Token di accesso alla sandbox';
$_['text_sandbox_access_token_placeholder']             = 'Token di accesso alla sandbox';
$_['text_sandbox_client_id_help']                       = 'Ottieni questo dalla pagina Gestisci applicazione su Square';
$_['text_sandbox_client_id_label']                      = 'Sandbox Application ID';
$_['text_sandbox_client_id_placeholder']                = 'Sandbox Application ID';
$_['text_sandbox_disabled_label']                       = 'Disabilitato';
$_['text_sandbox_enabled']                              = 'La modalità sandbox è abilitata! Le transazioni sembreranno passare, ma non verranno effettuati addebiti.';
$_['text_sandbox_enabled_label']                        = 'Abilitato';
$_['text_sandbox_section_heading']                      = 'Impostazioni Square Sandbox';
$_['text_select_location']                              = 'Selezionare la posizione';
$_['text_settings_section_heading']                     = 'Impostazioni quadrate';
$_['text_squareup']                                     = '<a target="_BLANK" href="https://squareup.com"> <img src="view/image/payment/squareup.png" alt="Square" title="Square" style="border: 1px solido #EEEEEE; "/> </a>';
$_['text_success']                                      = 'Successo: hai modificato il modulo di pagamento quadrato!';
$_['text_success_capture']                              = 'Transazione acquisita con successo!';
$_['text_success_refund']                               = 'Transazione correttamente rimborsata!';
$_['text_success_void']                                 = 'Transazione annullata con successo!';
$_['text_token_expired']                                = 'Il token di accesso Square è scaduto! <a href="%s"> Fai clic qui </a> per rinnovarlo ora.';
$_['text_token_expiry_warning']                         = 'Il tuo token di accesso Square scadrà il giorno %s. <a href="%s"> Fare clic qui </a> per rinnovarlo ora.';
$_['text_token_revoked']                                = 'Il token di accesso Square è scaduto o è stato revocato! <a href="%s"> Fare clic qui </a> per autorizzare di nuovo l\'estensione Square.';
$_['text_transaction_statuses']                         = 'Stati di transazione';
$_['text_view']                                         = 'Visualizza altro';
$_['text_void']                                         = 'vuoto';
$_['text_na']                                           = 'N /A';
$_['text_no_reason_provided']                           = 'Motivo non fornito.';

// Statuses
$_['squareup_status_comment_authorized']                = 'La transazione con la carta è stata autorizzata ma non ancora catturata.';
$_['squareup_status_comment_captured']                  = 'La transazione con la carta è stata autorizzata e successivamente catturata (vale a dire completata).';
$_['squareup_status_comment_voided']                    = 'La transazione con la carta è stata autorizzata e successivamente annullata (vale a dire, annullata).';
$_['squareup_status_comment_failed']                    = 'La transazione con la carta è fallita.';

// Entry
$_['entry_total']                                       = 'Totale';
$_['entry_geo_zone']                                    = 'Geo Zone';
$_['entry_sort_order']                                  = 'Ordinamento';
$_['entry_merchant']                                    = 'ID Commerciante';
$_['entry_transaction_id']                              = 'ID transazione';
$_['entry_order_id']                                    = 'ID ordine';
$_['entry_partner_solution_id']                         = 'ID soluzione partner';
$_['entry_type']                                        = 'Tipo di transazione';
$_['entry_currency']                                    = 'Moneta';
$_['entry_amount']                                      = 'Quantità';
$_['entry_browser']                                     = 'Agente utente del cliente';
$_['entry_ip']                                          = 'IP del cliente';
$_['entry_date_created']                                = 'data di creazione';
$_['entry_billing_address_company']                     = 'Società di fatturazione';
$_['entry_billing_address_street']                      = 'Billing Street';
$_['entry_billing_address_city']                        = 'Città di fatturazione';
$_['entry_billing_address_postcode']                    = 'ZIP di fatturazione';
$_['entry_billing_address_province']                    = 'Provincia di fatturazione /Stato';
$_['entry_billing_address_country']                     = 'Paese di fatturazione';
$_['entry_status_authorized']                           = 'autorizzato';
$_['entry_status_captured']                             = 'catturato';
$_['entry_status_voided']                               = 'Voided';
$_['entry_status_failed']                               = 'mancato';
$_['entry_setup_confirmation']                          = 'Conferma di installazione:';

// Error
$_['error_permission']                                  = '<strong> Avvertenza: </strong> Non hai i permessi per modificare il pagamento Square!';
$_['error_permission_recurring']                        = '<strong> Avvertenza: </strong> Non sei autorizzato a modificare pagamenti ricorrenti!';
$_['error_transaction_missing']                         = 'Transazione non trovata!';
$_['error_no_ssl']                                      = '<strong> Avviso: </strong> SSL non è abilitato sul pannello di amministrazione. Si prega di abilitare per completare la configurazione.';
$_['error_user_rejected_connect_attempt']               = 'Il tentativo di connessione è stato annullato dall\'utente.';
$_['error_possible_xss']                                = 'Abbiamo rilevato un possibile attacco su più siti e abbiamo interrotto il tentativo di connessione. Verifica l\'ID dell\'applicazione e il segreto e riprova utilizzando i pulsanti nel pannello di amministrazione.';
$_['error_invalid_email']                               = 'L\'indirizzo e-mail fornito non è valido!';
$_['error_cron_acknowledge']                            = 'Conferma di aver impostato un lavoro CRON.';
$_['error_client_id']                                   = 'L\'ID del client dell\'app è un campo obbligatorio';
$_['error_client_secret']                               = 'Il segreto del client dell\'app è un campo obbligatorio';
$_['error_sandbox_client_id']                           = 'L\'ID client sandbox è un campo obbligatorio quando la modalità sandbox è abilitata';
$_['error_sandbox_token']                               = 'Il token sandbox è un campo obbligatorio quando la modalità sandbox è abilitata';
$_['error_no_location_selected']                        = 'La posizione è un campo obbligatorio';
$_['error_refresh_access_token']                        = 'Si è verificato un errore durante il tentativo di aggiornare la connessione dell\'estensione al tuo account Square. Verifica le credenziali dell\'applicazione e riprova.';
$_['error_form']                                        = 'Si prega di controllare il modulo per gli errori e provare a salvare l\'agiano.';
$_['error_token']                                       = 'Si è verificato un errore durante l\'aggiornamento del token: %s';
$_['error_no_refund']                                   = 'Rimborso fallito';

// Column
$_['column_transaction_id']                             = 'ID transazione';
$_['column_order_id']                                   = 'ID ordine';
$_['column_customer']                                   = 'Cliente';
$_['column_status']                                     = 'Stato';
$_['column_type']                                       = 'genere';
$_['column_amount']                                     = 'Quantità';
$_['column_ip']                                         = 'IP';
$_['column_date_created']                               = 'data di creazione';
$_['column_action']                                     = 'Azione';
$_['column_refunds']                                    = 'rimborsi';
$_['column_reason']                                     = 'Ragionare';
$_['column_fee']                                        = 'Tassa di elaborazione';

// Button
$_['button_void']                                       = 'vuoto';
$_['button_refund']                                     = 'Rimborso';
$_['button_capture']                                    = 'Catturare';
$_['button_connect']                                    = 'Collegare';
$_['button_reconnect']                                  = 'ricollegare';
$_['button_refresh']                                    = 'Aggiorna token';