<?php

return [

    'emails' => [
        'general' => [
            'dear_customer' => 'Stimate client',
            'dear_admin' => 'Salut',
            'unsubscribe' => 'Dacă nu mai dorești să primești emailuri de la noi, poți modifica setările privind comunicarea în',
            'your_member_account' => 'contul tău de membru',
        ],
        'complaints' => [
            'consumer-subject' => 'Sesizare',
            'consumer-title' => 'Sesizare înregistrată',
            'consumer-content_p1' => 'Sesizarea ta cu numărul :id a fost înregistrată. Odată ce aceasta va fi rezolvată, vei fi notificat prin email.',
            'consumer-content_p2' => 'De asemenea, poți verifica statusul acesteia și în',
            'consumer-content_p3' => 'Sesizarea ta cu numărul :id a fost actualizată.',
            'admin-content_p1' => 'Sesizarea cu numărul :id a fost înregistrată.',
            'admin-content_p2' => 'Pentru a prelua aceasta sesizare :button.',
        ],
        'recover' => [
            'p1' => 'Primiți acest e-mail deoarece ați solicitat o resetare a parolei pentru contul dvs. ' . env('APP_NAME') . '<br />
                       Dați click pe butonul de mai jos pentru a alege o nouă parolă.',
            'subject' => 'Recuperare parola ' . env('APP_NAME')
        ],
        'verify' => [
            'title' => 'Verificați-vă contul',
            'p1' => 'Vă mulțumim pentru înregistrare. Confirmați că aceasta este adresa dumneavoastră de email.',
            'subject' => 'Activare cont ' . env('APP_NAME')
        ]
    ],

    'pages' => [
        'contact' => [
            'title' => 'Contact'
        ],

        'dashboard' => [
            'title' => 'Panou de control',
            'home' => 'Acasă',
            'index' => 'Index',
            'invoices' => 'Facturi',
            'complaints' => 'Sesizări',
            'notifications' => 'Notificări',
            'contact' => 'Contact',
            'my_account' => 'Contul meu',
            'users' => 'Utilizatori',
            'cms' => 'Configurare pagini',
            'settings' => 'Setări',
        ],

        'login' => [
            'title' => 'Autentificare',
            'error' => 'Datele de conectare nu sunt corecte!',
            'email_label' => 'Email',
            'password_label' => 'Parolă (minim 6 caractere)',
            'remember' => 'Ține-mă minte',
            'new_account' => 'Cont nou',
            'login_btn' => 'Autentificare',
            'verify_title' => 'Verificați adresa de email',
            'verify_resent' => 'Un link nou a fost retrimis către adresa dumneavoastră de email.',
            'verify_paragraph1' => 'Înainte de a continua, va rugăm să verificați adresa dumneavoastră de email pentru a activa contul.',
            'verify_paragraph2' => 'Dacă nu ați primit email',
            'verify_btn' => 'click aici pentru a solicita unul nou',
            'recover_btn' => 'Recuperare parolă',
            'api_failed' => 'Momentan aplicația este indisponibilă. Vă rugăm încercați mai târziu.',
            'inactive_user' => 'User-ul nu mai este activ',
            'recaptcha_error' => 'Nu ati trecut de validarea reCaptcha. Va rugam incercati din nou'
        ],
        'register' => [
            'title' => 'Creare cont',
            'password_label' => 'Parolă (minim 6 caractere)',
            'confirm_label' => 'Repetați parola',
            'register_btn' => 'Înregistrare cont nou',
            'login_btn' => 'Autentificare',
            'sivapp_fail' => 'Codul de client sau numărul de contract nu există. Vă rugăm verificați informțiile introduse.',
        ],

        'recover' => [
            'title' => 'Resetare parolă',
            'back_btn' => 'Înapoi',
            'reset_btn' => 'Resetează parola',
            'email_placeholder' => 'Adresa de email'
        ],

        'reset' => [
            'title' => 'Resetare parolă'
        ],

        'users' => [
            'save_success' => 'Utilizatorul a fost salvat cu succes',
            'delete_success' => 'Utilizatorul a fost șters cu succes.',
            'consumer_delete_success' => 'Utilizatorul dumneavoastra a fost șters cu succes.',
            'delete_confirm_title' => 'Sunteți sigur de ștergerea utlizatorului?',
            'delete_confirm_body_1' => 'Utilizatorul',
            'delete_confirm_body_2' => 'va fi șters. Această acțiune nu poate fi inversată. Toate informatiile acestui utilizator nu vor mai putea fi recuperate!',
            'delete_confirm_body_3' => 'Prin inchiderea contului se șterg datele cu caracter personal pe care le-ati completat la crearea contului și veti pierde facilitățile de a transmite indecși, vizualiza și plăti facturi precum și de a trimite și primi sesizări/notificări',
            'delete_confirm_submit' => 'Da, șterge utilizatorul',
            'title' => 'Administrare utilizatori',
            'new_user_btn' => ' Adaugă un utilizator nou',
            'create_title' => 'Creare utilizator',
            'edit_title' => 'Editează utilizator',
            'no_hash' => 'Parola nu este corectă',
            'update_success' => 'Informatiile au fost actualizate cu succes',
            'my_account_title' => 'Contul meu',
            'clients_title' => 'Clienti'
        ],

        'index' => [
            'title' => 'Transmitere index',
            'index_form' => 'Folosiți formularul de mai jos pentru a transmite indexul curent. <p>În cazul în care pe parcursul acestei luni personalul Aquaserv va efectua citirea apometrului dumneavoastră se va factura indexul citit de Aquaserv. Indexul facturat îl găsiți pe anexa facturii.</p>',
            'last_index' => 'Ultimul index:',
            'send_index' => 'Trimite index',
            'index_date' => 'Introduceți index la data:',
	    'index_mic' => '<br/>Stimate client, dacă indexul citit de dumneavoastră este mai mic decât cel afișat în portalul MYAPA vă rugăm să luați legătura cu Aquaserv pe e-mail la contractare.facturare@aquaservtulcea.ro',
            'history' => 'Istoric index',
            'index_period' => 'Perioada de introducere a indexului este între data de <b>:date_from</b> și <b>:date_to</b>',
            'index_error' => 'Nu puteți transmite mai multe indexuri pentru aceeași perioadă!',
            'success' => 'Indexul a fost trimis cu succes',
            'error' => 'Nu se poate face actualizarea. Există introdusă citire de la distribuitor.',
            'no_waterMeters' => 'Pentru această locație nu există contori',
            'no_locations' => 'Pentru acest cod de client nu există locații de consum alocate',
            'no_indexes' => 'Pentru această locație și contor nu există indexuri',
        ],

        'invoice' => [
            'title' => 'Listă facturi',
            'history' => 'Istoric facturi',
            'no_invoices' => 'Pentru această locație nu exista facturi achitate',
            'pay' => 'Achită ',
            'select_invoice' => 'Selectează factura',
            'unpayed' => 'Facturi de achitat',
            'no_unpayed_invoices' => 'Nu există facturi neachitate. Îți mulțumim!',
            'thankYou_title' => 'Plată online',
            'thankYou_p1' => 'Plata a fost inregistrata cu succes',
            'thankYou_p1_fail' => 'Plata nu a fost finalizata',
            'invoice' => 'Factură',
            'payment_details' => 'Detalii plată',
            'invoice_id' => 'ID factură',
            'payment_total' => 'Total de plată',
            'invoice_data' => 'Date factură',
            'invoice_issuance_date' => 'Dată emitere factură',
            'due_date' => 'Data scadentă',
            'client_code' => 'Cod client',
            'consumption_location' => 'Locație consum',
            'tax_details' => 'Detalii fiscale',
            'client_balance_at_invoice_date' => 'Sold client la data emiterii facturii',
        ],

        'complaints' => [
            'title' => 'Sesizări',
            'history' => 'Sesizări anterioare',
            'no_complaints' => 'Nu există sesizari',
            'send_btn' => 'Trimite sesizare',
            'new_title' => 'Sesizare nouă',
            'success' => 'Sesizarea a fost salvată cu succes',
            'error' => 'Sesizarea nu a fost salvată. Vă rugăm încercați din nou',
            'update_error' => 'Sesizarea nu mai poate fi modificată',
            'takeover_success' => 'Sesizarea a fost preluată cu succes',
            'takeover_error' => 'Sesizarea este preluată de :user',
            'takeover_btn' => 'Preia sesizare',
            'solve_btn' => 'Rezolvă sesizare',
            'popup_title' => 'Vizualizare sesizare',
            'answer_error' => 'Câmpul de rezoluție este obligatoriu',
            'admin_title' => 'Administrare sesizări',
            'edit_title' => 'Editare sesizare',
            'no_right' => 'Nu aveți rolul de <b>Responsabil sesizări</b>',
            'delete' => 'Sesizarea a fost stearsa'
        ],

        'complaint_type' => [
            'new_btn' => 'Creează tip nou',
            'create_title' => 'Creează tip sesizare',
            'edit_title' => 'Editează tip sesizare',
            'title' => 'Administrare tip sesizări',
            'success' => 'Tipul de sesizare a fost salvat cu succes',
            'save' => 'Salvează'
        ],

        'complaint_status' => [
            'new_btn' => 'Creează status nou',
            'create_title' => 'Creează status sesizare',
            'edit_title' => 'Editează status sesizare',
            'title' => 'Administrare status sesizări',
            'success' => 'Statusul de sesizare a fost salvat',
            'save' => 'Salvează'
        ],

        'cms' => [
            'title' => 'Pagini',
            'edit_title' => 'Editare pagină',
            'success' => 'Pagina a fost salvată cu succes'
        ],

        'settings' => [
            'title' => 'Administrare setări',
            'success' => 'Setarile au fost salvate cu succes'
        ],

        'notifications' => [
            'title' => 'Administrare notificări',
            'title_consumer' => 'Notificări',
            'new_btn' => 'Notificare nouă',
            'create_title' => 'Notificare nouă',
            'success' => 'Notificarea a fost salvată cu succes',
            'save' => 'Salvează și trimite notificarea'
        ],

        'notification_type' => [
            'new_btn' => 'Creează tip nou',
            'create_title' => 'Creează tip notificare',
            'edit_title' => 'Editează tip notificare',
            'title' => 'Administrare tip notificări',
            'success' => 'Tipul de notificare a fost salvat cu succes',
            'save' => 'Salvează'
        ],

        '404' => [
            'title' => 'Pagina nu a fost gasită',
            'back_to_home' => 'Înapoi acasă',
        ],

        '403' => [
            'title' => 'Acces respins',
            'no_permission' => 'Nu aveți permisiunile necesare pentru a accesa această pagină',
            'back_to_home' => 'Înapoi acasă',
        ]
    ]
];
