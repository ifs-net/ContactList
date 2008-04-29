<?php
/**
 * @package      ContactList
 * @version      $Id$
 * @author       Florian Schie�l, Carsten Volmer
 * @link         http://www.ifs-net.de, http://www.carsten-volmer.de
 * @copyright    Copyright (C) 2008
 * @license      http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

// foaf plugin for contactlist
define('_CONTACTLISTCONNECTION',                        'Deiner Verbindung hierher');
define('_CONTACTLISTNOLINKFOUND',                       'Keine Verbindung gefunden');

// menu
define('_CONTACTLISTCREATEBUDDY',                       'Kontakt hinzuf�gen');
define('_CONTACTLISTSHOWBUDDIES',                       'Kontaktliste');
define('_CONTACTLISTSHOWIGNORELIST',                    'Ignorierliste');
define('_CONTACTLISTUSERPREFERENCES',                   'Einstellungen');

// user preferences
define('_CONTACTLISTUSERPREFS',                         'Meine Einstellungen');
define('_CONTACTLISTMYPUBLICSTATE',                     'Einstellungen zum Datenschutz');
define('_CONTACTLISTUPDATEPREFS',                       'Einstellungen speichern');
define('_CONTACTLISTPRIVACYNOBODY',                     'Keiner soll meine Kontaktliste einsehen k�nnen');
define('_CONTACTLISTPRIVACYBUDDIES',                    'Best�tigte Kontakte sollen meine Kontaktliste einsehen k�nnen');
define('_CONTACTLISTPRIVACYMEMBERS',                    'Alle angemeldeten Benutzer sollen meine Kontaktliste einsehen k�nnen');
define('_CONTACTLISTPREFSUPDATEERROR',                  'Beim Speichern der Einstellungen ist ein Fehler aufgetreten');
define('_CONTACTLISTPREFSUPDATED',                      'Einstellungen aktualisiert');

// main
define('_CONTACTLISTSORTITEMS',                         'Liste nach');
define('_CONTACTLISTRELOAD',                            'sortieren');
define('_CONTACTLISTSORTUNAME',                         'Benutzernamen');
define('_CONTACTLISTSORTBIRTHDAY',                      'Geburtsdatum');
define('_CONTACTLISTSORTSTATE',                         'Kontaktstatus');
define('_CONTACTLISTSORTAYSTONEXTBIRTHDAY',             'n�chsten Geburtstagen');
define('_CONTACTLISTMYCONTACTS',                        'Meine Kontakte');
define('_CONTACTLISTADDBUDDY',                          'Neuen Kontakt hinzuf�gen');
define('_CONTACTLISTTOCONFIRM',                         'Folgende Anfragen liegen vor');
define('_CONTACTLISTUNAME',                             'Benutzername');
define('_CONTACTLISTCOMMENTS',                          'Kommentare');
define('_CONTACTLISTSTATE',                             'Status');
define('_CONTACTLISTACTIONS',                           'Aktion');
define('_CONTACTLISTPUBCOMMENT',                        '�ffentlicher Kommentar');
define('_CONTACTLISTPRVCOMMENT',                        'Privater Kommentar');
define('_CONTACTLISTEDIT',                              'Bearbeiten');
define('_CONTACTLISTADD',                               'Als Kontakt hinzuf�gen');
define('_CONTACTLISTNOREQUTEXT',                        'Es wurde kein Grund f�r die Kontaktaufnahme angegeben');
define('_CONTACTLISTREQUTEXT',                          'Begr�ndung');
define('_CONTACTLISTCANCEL',                            'Anfrage abbrechen');
define('_CONTACTLISTREQUESTDATE',                       'Datum der Anfrage');
define('_CONTACTLISTDECLINE',                           'Anfrage zur�ckweisen');
define('_CONTACTLISTCONFIRM',                           'Anfrage akzeptieren');
define('_CONTACTLISTPENDING',                           'Anfrage noch nicht beantwortet');
define('_CONTACTLISTCANNOTDELETEYET',                   'Eine offene Anfrage kann fr�hestens nach 30 Tagen wieder gel�scht werden, um unn�tig viele Anfragen an einen gleichen Benutzer zu vermeiden');
define('_CONTACTLISTCONFIRMED',                         'Kontakt best�tigt');
define('_CONTACTLISTSUSPENDED',                         'Kontakt aufgehoben');
define('_CONTACTLISTREQUESTDECLINED',                   'Anfrage zur�ckgewiesen');
define('_CONTACTLISTREQUESTACCEPTED',                   'Kontakt akzeptiert');
define('_CONTACTLISTREQUESTDECLINEERR',                 'Beim Zur�ckweisen einer Kontaktanfrage ist ein Fehler aufgetreten');
define('_CONTACTLISTREQUESTACCERR',                     'Beim Akzeptieren einer Kontaktanfrage ist ein Fehler aufgetreten');
define('_CONTACTLISTNA',                                'nicht verf�gbar');
define('_CONTACTLISTSUSPEND',                           'Aufheben');
define('_CONTACTLISTDELETE',                            'Entfernen');
define('_CONTACTLISTREALLYSUSPEND',                     'Soll die Verbindung zu diesem Kontakt wirklich aufgel�st werden? Der andere Kontakt wird �ber diese Status�nderung per E-Mail informiert.');
define('_CONTACTLISTSUSPENDERROR',                      'Beim Aufl�sen der Verbindung zu dem Kontakt ist ein Fehler aufgetreten');
define('_CONTACTLISTBUDDYSUSPENDED',                    'Der Kontakt wurde entfernt');

define('_CONTACTLISTFILTERBUDDIESALL',                  'Alle');
define('_CONTACTLISTFILTERBUDDIESPENDING',              'Unbeantwortet');
define('_CONTACTLISTFILTERBUDDIESCONFIRM',              'Best�tigt');
define('_CONTACTLISTFILTERYOURBUDDIESREJECT',           'Zur�ckgewiesen');
define('_CONTACTLISTFILTERYOURBUDDIESSUSPEND',          'Aufgehoben');

define('_CONTACTLISTYOURBUDDIES',                       'Deine Kontakte');
define('_CONTACTLISTYOURBUDDIESPENDING',                'Deine unbeantworteten Anfragen');
define('_CONTACTLISTYOURBUDDIESCONFIRM',                'Deine best�tigten Kontakte');
define('_CONTACTLISTYOURBUDDIESREJECT',                 'Deine zur�ckgewiesenen Anfragen');
define('_CONTACTLISTYOURBUDDIESSUSPEND',                'Deine aufgehobenen Kontakte');
define('_CONTACTLISTAMOUNTPENDING',                     'Anzahl unbeantworteter Anfragen');
define('_CONTACTLISTAMOUNTCONFIRM',                     'Anzahl best�tigter Kontakte');
define('_CONTACTLISTAMOUNTREJECTED',                    'Anzahl zur�ckgewiesener Anfragen');
define('_CONTACTLISTAMOUNTSUSPENDED',                   'Anzahl aufgehobener Kontakte');
define('_CONTACTLISTBIRTHDAY',                          'Geburtstag');
define('_CONTACTLISTSENDPM',                            'Nachricht senden');
define('_CONTACTLISTSHOWCONTACTS',                      'Kontakte des Benutzers');
define('_CONTACTLISTONLINE',                            'online');
define('_CONTACTLISTOF',                                'von');

// ignore
define('_CONTACTLISTIGNORELISTMANAGEMENT',              'Ignorierte Kontakte verwalten');
define('_CONTACTLISTIGNOREEXPLAIN',                     'Hier k�nnen Benutzer auf eine Ignorierliste gesetzt werden. Das heisst, dass Du von diesen dann keine E-Mails etc. mehr empfangen wirst.');
define('_CONTACTLISTADDUSER',                           'Benutzer hinzuf�gen');
define('_CONTACTLISTUSERNOTFOUND',                      'Der angegebene Benutzer konnte nicht gefunden weden');
define('_CONTACTLISTDONOTIGNOREYOURSELF',               'Ich wei�, manche k�nnen sich selbst nicht leiden. Aber deswegen kannst Du dich dennoch nicht selbst auf deine eigene Ignorierliste setzen :-)');
define('_CONTACTLISTIGNOREDUSERADDED',                  'Benutzer wurde als hinzugef�gt');
define('_CONTACTLISTNOIGNOREDUSERS',                    'Es befinden sich noch keine Benutzer auf der Liste');
define('_CONTACTLISTIGNORELIST',                        'Folgende Benutzer werden aktuell ignoriert');
define('_CONTACTLISTREMOVEIGNOREDUSER',                 'Benutzer nicht mehr ignorieren');
define('_CONTACTLISTUSERUPDATEERROR',                   'Aktualisieren der Ignorierliste fehlgeschlagen');
define('_CONTACTLISTUSERNOLONGERIGNORED',               'Der Benutzer wird nun nicht mehr ignoriert');

// create
define('_CONTACTLISTCONFIRMREQU',                       'Nach Abschicken dieses Formulars wird der andere Benutzer eine Nachricht erhalten, dass er die Anfrage bearbeiten soll. Sobald die Kontaktanfrage akzeptiert oder abgelehnt wurde, wirst Du informiert.');
define('_CONTACTLISTREQUESTTEXT',                       'Grund f�r die Kontaktaufnahme');
define('_CONTACTLISTNOTADDYOURSELF',                    'Such Dir doch Freunde... Oder willst Du dich selbst als Kontakt hinzuf�gen? Das geht nicht ;-)');
define('_CONTACTLISTUNAMEINVALID',                      'Es wurde kein g�ltiger Benutzername �bermittelt');
define('_CONTACTLISTDUPLICATEREQUEST',                  'Der �bermittelte Benutzer steht schon auf deiner Liste. Evtl abgelehnte Kontakte m�ssen zuerst gel�scht werden, bevor eine neue Anfrage gestartet werden kann. Aber doppelt Eintragen geht nicht. Geh lieber raus und such Dir weitere Freunde ;-)');
define('_CONTACTLISTREQUESTSENT',                       'Die Kontaktanfrage wurde an den anderen Benutzer �bermittelt');
define('_CONTACTLISTBUDDYADDED',                        'Kontakt hinzugef�gt');
define('_CONTACTLISTUSERIGNORESYOU',                    'Nicht gut... Dein potentieller Buddy hat dich auf seiner Ignorier-Liste - daher war eine Kontaktanfrage nicht m�glich');

// emails
define('_CONTACTLISTUNCONFIRMSUBJECT',                  'Du wurdest als neuer Kontakt hinzugef�gt');
define('_CONTACTLISTCONFIRMSUBJECT',                    'Ein Benutzer will dich als Kontakt hinzuf�gen');
define('_CONTACTLISTBUDDYSUSPENDEDYOU',                 'Einer deiner Kontakte hat die Verbindung aufgehoben');
define('_CONTACTLISTREQUESTREJECTED',                   'Eine Kontaktanfrage wurde abgelehnt');
define('_CONTACTLISTDEARUSER',                          'Liebe(r)');
define('_CONTACTLISTTHEUSER',                           'der Benutzer');
define('_CONTACTLISTADDEDYOUASBUDDY',                   'hat Dich als Kontakt auf %sitename% hinzugef�gt. Das heisst, ihr seid jetzt Freunde und gegenseitig in euren Kontaktlisten verlinkt.');
define('_CONTACTLISTATTENTION',                         'Achtung');
define('_CONTACTLISTAUTOMATICCONFIRM',                  'Diese Anfrage wurde automatisch angenommen, da Du selbst erst k�rzlich eine Kontaktanfrage an den anderen Benutzer gestellt hast, welche in der Zwischenzeit noch nicht gel�scht wurde.');
define('_CONTACTBUDDYMANAGEMENT',                       'Verwaltung der Kontaktliste');
define('_CONTACTLISTDONOTREPLY',                        'Bitte auf diese E-Mail nicht antworten. Diese E-Mail wurde automatisch aufgrund Deiner Mitgliedschaft auf obiger Seite an Dich verschickt. Wenn es Fragen wegen der Kontaktaufnahme, einer Ablehnung oder einer Aufhebung eines Kontaktes geht, wende Dich bitte an den oben angegebenen Benutzer. In Problemf�llen wende dich einfach an den Administrator der obigen Internetseite.');
define('_CONTACTLISTWANTSTOADDYOUAT',                   'will dich als neuen Kontakt auf %sitename% hinzuf�gen.');
define('_CONTACTLISTREJECTORACCEPT',                    'Bitte akzeptiere oder lehne die Anfrage ab.');
define('_CONTACTLISTHASREJECTEDYOURREQUESTAT',          'hat deinen Kontaktwunsch auf %sitename% leider abgelehnt.');
define('_CONTACTLISTHASACCEPTEDYOURREQUESTAT',          'hat deinen Kontaktwunsch auf %sitename% angenommen.');
define('_CONTACTLISTSUSPENDEDYOUAT',                    'hat dich als Kontakt auf %sitename% aufgehoben und von seiner Kontaktliste entfernt.');

// edit
define('_CONTACTLISTEDITINFO',                          'Kontaktdetails bearbeiten');
define('_CONTACTLISTUPDATEINFO',                        'Kontaktdetails aktualisieren');
define('_CONTACTLISTBUDDYNOTFOUND',                     'Es trat ein Fehler beim Laden des Kontaktes auf');
define('_CONTACTLISTFOREIGNBUDDY',                      'Du kannst nat�rlich nur deine eigenen Freunde bearbeiten... Geh lieber raus und such Dir neue Freunde ;-)');
define('_CONTACTLISTBUDDYUPDATED',                      'Kontaktdetails wurden aktualisiert');
define('_CONTACTLISTBUDDYUPDATEFAILED',                 'Beim Aktualisieren der Kontaktdetails trat ein Fehler auf');

// myprofile plugin and display-function
define('_CONTACTLISTTABTITLE',                          'Freunde');
define('_CONTACTLISTMYBUDDIES',                         'Kontakte von');
define('_CONTACTLISTTHEUSERHAS',                        'Der Benutzer hat');
define('_CONTACTLISTBUDDIES',                           'Kontakte');
define('_CONTACTLISTLISTNOTPUBLIC',                     'Die Liste des Benutzers ist f�r dich nicht sichtbar');

