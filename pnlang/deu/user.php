<?php
/**
 * @package      ContactList
 * @version      $Id$
 * @author       Florian Schießl, Carsten Volmer
 * @link         http://www.ifs-net.de, http://www.carsten-volmer.de
 * @copyright    Copyright (C) 2008
 * @license      http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

define('_CONTACTLISTTITLE',                             'Kontaktliste');
 
// foaf plugin for contactlist
define('_CONTACTLISTCONNECTION',                        'Deine Verbindung hierher');
define('_CONTACTLISTNOLINKFOUND',                       'Keine Verbindung gefunden');

// costum fields / myprofile - plugin
define('_CONTACTLISTINMYPROFILELIST',					'Darf alle meine Daten sehen');

// menu
define('_CONTACTLISTCREATEBUDDY',                       'Kontakt hinzufügen');
define('_CONTACTLISTSHOWBUDDIES',                       'Kontaktliste');
define('_CONTACTLISTSHOWIGNORELIST',                    'Ignorierliste');
define('_CONTACTLISTUSERPREFERENCES',                   'Einstellungen');

// user preferences
define('_CONTACTLISTUSERPREFS',                         'Meine Einstellungen');
define('_CONTACTLISTMYPUBLICSTATE',                     'Einstellungen zum Datenschutz');
define('_CONTACTLISTUPDATEPREFS',                       'Einstellungen speichern');
define('_CONTACTLISTPRIVACYNOBODY',                     'Keiner soll meine Kontaktliste einsehen können');
define('_CONTACTLISTPRIVACYBUDDIES',                    'Bestätigte Kontakte sollen meine Kontaktliste direkt einsehen können');
define('_CONTACTLISTPRIVACYMEMBERS',                    'Alle angemeldeten Benutzer sollen meine Kontaktliste direkt einsehen können');
define('_CONTACTLISTPREFSUPDATEERROR',                  'Beim Speichern der Einstellungen ist ein Fehler aufgetreten');
define('_CONTACTLISTPREFSUPDATED',                      'Einstellungen aktualisiert');
define('_CONTACTLISTFOAFUSAGE',							'Es gibt z.B. die Möglichkeit, in Profilen oder anderen Modulen einzubinden, ob man einen Kontakt direkt oder über Umwege kennt. Dass heisst, Person A kennt Person B über Person C, Person D und Person E. Ist jemand der in dieser Reihe steht, kein direkter eigener Kontakt, kann er vom direkten Einsehen in die eigene Kontaktliste zwar ausgeschlossen werden - die Verknüpfungskette wird jedoch angezeigt. Ausser, es wird eingestellt, dass niemand die eigene Kontaktliste einsehen darf.');

// main
define('_CONTACTLISTCONTACTS',							'Kontakte');
define('_CONTACTLIST1STGRADE',							'ersten Grades');
define('_CONTACTLIST2NDGRADE',							'zweiten Grades');
define('_CONTACTLISTONLINE',                            'online');
define('_CONTACTLISTSORTITEMS',                         'Liste nach');
define('_CONTACTLISTRELOAD',                            'sortieren');
define('_CONTACTLISTSORTUNAME',                         'Benutzernamen');
define('_CONTACTLISTSORTBIRTHDAY',                      'Geburtsdatum');
define('_CONTACTLISTSORTSTATE',                         'Kontaktstatus');
define('_CONTACTLISTSORTAYSTONEXTBIRTHDAY',             'nächsten Geburtstagen');
define('_CONTACTLISTMYCONTACTS',                        'Meine Kontakte');
define('_CONTACTLISTADDBUDDY',                          'Neuen Kontakt hinzufügen');
define('_CONTACTLISTTOCONFIRM',                         'Folgende Anfragen liegen vor');
define('_CONTACTLISTUNAME',                             'Benutzername');
define('_CONTACTLISTCOMMENTS',                          'Kommentare');
define('_CONTACTLISTSTATE',                             'Status');
define('_CONTACTLISTACTIONS',                           'Aktion');
define('_CONTACTLISTPUBCOMMENT',                        'Öffentlicher Kommentar');
define('_CONTACTLISTPRVCOMMENT',                        'Privater Kommentar');
define('_CONTACTLISTEDIT',                              'Kontakt bearbeiten');
define('_CONTACTLISTADD',                               'Als Kontakt hinzufügen');
define('_CONTACTLISTNOREQUTEXT',                        'Es wurde kein Grund für die Kontaktaufnahme angegeben');
define('_CONTACTLISTREQUTEXT',                          'Begründung');
define('_CONTACTLISTCANCEL',                            'Anfrage abbrechen');
define('_CONTACTLISTREQUESTDATE',                       'Datum der Anfrage');
define('_CONTACTLISTDECLINE',                           'Anfrage zurückweisen');
define('_CONTACTLISTCONFIRM',                           'Anfrage akzeptieren');
define('_CONTACTLISTPENDING',                           'Anfrage noch nicht beantwortet');
define('_CONTACTLISTCANNOTDELETEYET',                   'Eine offene Anfrage kann frühestens nach einem Tag wieder gelöscht werden, um unnötig viele Anfragen an einen gleichen Benutzer zu vermeiden');
define('_CONTACTLISTCONFIRMED',                         'Kontakt bestätigt');
define('_CONTACTLISTSUSPENDED',                         'Kontakt aufgehoben');
define('_CONTACTLISTREQUESTDECLINED',                   'Anfrage zurückgewiesen');
define('_CONTACTLISTREQUESTACCEPTED',                   'Kontakt akzeptiert');
define('_CONTACTLISTREQUESTDECLINEERR',                 'Beim Zurückweisen einer Kontaktanfrage ist ein Fehler aufgetreten');
define('_CONTACTLISTREQUESTACCERR',                     'Beim Akzeptieren einer Kontaktanfrage ist ein Fehler aufgetreten');
define('_CONTACTLISTNA',                                'nicht verfügbar');
define('_CONTACTLISTSUSPEND',                           'Aufheben');
define('_CONTACTLISTDELETE',                            'Entfernen');
define('_CONTACTLISTREALLYSUSPEND',                     'Soll die Verbindung zu diesem Kontakt wirklich aufgelöst werden? Der andere Kontakt wird über diese Statusänderung per E-Mail informiert.');
define('_CONTACTLISTSUSPENDERROR',                      'Beim Auflösen der Verbindung zu dem Kontakt ist ein Fehler aufgetreten');
define('_CONTACTLISTBUDDYSUSPENDED',                    'Der Kontakt wurde entfernt');

define('_CONTACTLISTFILTERBUDDIESALL',                  'Alle');
define('_CONTACTLISTFILTERBUDDIESPENDING',              'Unbeantwortet');
define('_CONTACTLISTFILTERBUDDIESCONFIRM',              'Bestätigt');
define('_CONTACTLISTFILTERYOURBUDDIESREJECT',           'Zurückgewiesen');
define('_CONTACTLISTFILTERYOURBUDDIESSUSPEND',          'Aufgehoben');

define('_CONTACTLISTYOURBUDDIES',                       'Deine Kontakte');
define('_CONTACTLISTYOURBUDDIESPENDING',                'Deine unbeantworteten Anfragen');
define('_CONTACTLISTYOURBUDDIESCONFIRM',                'Deine bestätigten Kontakte');
define('_CONTACTLISTYOURBUDDIESREJECT',                 'Deine zurückgewiesenen Anfragen');
define('_CONTACTLISTYOURBUDDIESSUSPEND',                'Deine aufgehobenen Kontakte');
define('_CONTACTLISTAMOUNTPENDING',                     'Anzahl unbeantworteter Anfragen');
define('_CONTACTLISTAMOUNTCONFIRM',                     'Anzahl bestätigter Kontakte');
define('_CONTACTLISTAMOUNTREJECTED',                    'Anzahl zurückgewiesener Anfragen');
define('_CONTACTLISTAMOUNTSUSPENDED',                   'Anzahl aufgehobener Kontakte');
define('_CONTACTLISTBIRTHDAY',                          'Geburtstag');
define('_CONTACTLISTSENDPM',                            'Nachricht senden');
define('_CONTACTLISTSHOWCONTACTS',                      'Kontakte des Benutzers');
define('_CONTACTLISTOF',                                'von');
define('_CONTACTLISTSHOWPROFILE',                       'Profil anzeigen');

// ignore
define('_CONTACTLISTBUDDYNOTIGNORABLE',					'Einen besättigten Kontakt auf die Ignorierliste setzen? Das passt doch nicht zusammen!');
define('_CONTACTLISTIGNORELISTMANAGEMENT',              'Ignorierte Kontakte verwalten');
define('_CONTACTLISTIGNOREEXPLAIN',                     'Hier können Benutzer auf eine Ignorierliste gesetzt werden. Das heisst, dass Du von diesen dann keine E-Mails etc. mehr empfangen wirst.');
define('_CONTACTLISTADDUSER',                           'Benutzer hinzufügen');
define('_CONTACTLISTUSERNOTFOUND',                      'Der angegebene Benutzer konnte nicht gefunden weden');
define('_CONTACTLISTDONOTIGNOREYOURSELF',               'Ich weiß, manche können sich selbst nicht leiden. Aber deswegen kannst Du dich dennoch nicht selbst auf deine eigene Ignorierliste setzen :-)');
define('_CONTACTLISTIGNOREDUSERADDED',                  'Benutzer wird nun von Dir ignoriert');
define('_CONTACTLISTNOIGNOREDUSERS',                    'Es befinden sich noch keine Benutzer auf der Liste');
define('_CONTACTLISTIGNORELIST',                        'Folgende Benutzer werden aktuell ignoriert');
define('_CONTACTLISTREMOVEIGNOREDUSER',                 'Benutzer nicht mehr ignorieren');
define('_CONTACTLISTUSERUPDATEERROR',                   'Aktualisieren der Ignorierliste fehlgeschlagen');
define('_CONTACTLISTUSERNOLONGERIGNORED',               'Der Benutzer wird nun nicht mehr ignoriert');
define('_CONTACTLISTIGNORELISTENTRY',					'Der Benutzer steht auf deiner eigenen Ignorierliste; so fängt aber keine gute Freundschaft an...');
define('_CONTACTLISTDISALLOWEDGROUPMEMEBRSHIP',			'Der Benutzer ist Mitglied einer Gruppe, welche vom Administrator so eingestellt wurde, dass deren Mitglieder nicht ignoriert werden können.');

// create
define('_CONTACTLISTCONFIRMREQU',                       'Nach Abschicken dieses Formulars wird der andere Benutzer eine Nachricht erhalten, dass er die Anfrage bearbeiten soll. Sobald die Kontaktanfrage akzeptiert oder abgelehnt wurde, wirst Du informiert.');
define('_CONTACTLISTREQUESTTEXT',                       'Grund für die Kontaktaufnahme');
define('_CONTACTLISTNOTADDYOURSELF',                    'Du kannst dich nicht selbst als Kontakt hinzufügen.');
define('_CONTACTLISTUNAMEINVALID',                      'Es wurde kein gültiger Benutzername übermittelt');
define('_CONTACTLISTDUPLICATEREQUEST',                  'Der übermittelte Benutzer steht bereits auf deiner Kontaktliste. Evtl. abgelehnte Kontakte müssen zuerst gelöscht werden, bevor eine neue Anfrage durchgeführt werden kann.');
define('_CONTACTLISTREQUESTSENT',                       'Die Kontaktanfrage wurde an den anderen Benutzer übermittelt');
define('_CONTACTLISTBUDDYADDED',                        'Kontakt hinzugefügt');
define('_CONTACTLISTUSERIGNORESYOU',                    'Nicht gut... Dein potentieller Buddy hat dich auf seiner Ignorier-Liste - daher war eine Kontaktanfrage nicht möglich');

// emails
define('_CONTACTLISTREQUESTCANCELLED',					'Eine Kontaktanfrage wurde wieder zurückgezogen');
define('_CONTACTLISTHASCANCELLEDYOURREQUESTAT',			'hat seine Kontaktanfrage wieder zurückgezogen auf folgender Seite');
define('_CONTACTLISTUNCONFIRMSUBJECT',                  'Du wurdest als neuer Kontakt hinzugefügt');
define('_CONTACTLISTCONFIRMSUBJECT',                    'Ein Benutzer will dich als Kontakt hinzufügen');
define('_CONTACTLISTBUDDYSUSPENDEDYOU',                 'Einer deiner Kontakte hat die Verbindung aufgehoben');
define('_CONTACTLISTREQUESTREJECTED',                   'Eine Kontaktanfrage wurde abgelehnt');
define('_CONTACTLISTDEARUSER',                          'Liebe(r)');
define('_CONTACTLISTTHEUSER',                           'der Benutzer');
define('_CONTACTLISTADDEDYOUASBUDDY',                   'hat Dich als Kontakt auf %sitename% hinzugefügt. Somit seid ihr jetzt beide in euren Kontaktlisten verknüpft.');
define('_CONTACTLISTATTENTION',                         'Achtung');
define('_CONTACTLISTAUTOMATICCONFIRM',                  'Diese Anfrage wurde automatisch angenommen, da Du selbst erst kürzlich eine Kontaktanfrage an den anderen Benutzer gestellt hast, welche in der Zwischenzeit noch nicht gelöscht wurde.');
define('_CONTACTBUDDYMANAGEMENT',                       'Verwaltung der Kontaktliste');
define('_CONTACTLISTDONOTREPLY',                        'Bitte auf diese E-Mail nicht antworten. Diese E-Mail wurde automatisch aufgrund Deiner Mitgliedschaft auf obiger Seite an Dich verschickt. Wenn es Fragen wegen der Kontaktaufnahme, einer Ablehnung oder einer Aufhebung eines Kontaktes geht, wende Dich bitte an den oben angegebenen Benutzer. In Problemfällen wende dich einfach an den Administrator der obigen Internetseite.');
define('_CONTACTLISTWANTSTOADDYOUAT',                   'will dich als neuen Kontakt auf %sitename% hinzufügen.');
define('_CONTACTLISTREJECTORACCEPT',                    'Bitte akzeptiere oder lehne die Anfrage ab.');
define('_CONTACTLISTHASREJECTEDYOURREQUESTAT',          'hat deinen Kontaktwunsch auf %sitename% leider abgelehnt.');
define('_CONTACTLISTHASACCEPTEDYOURREQUESTAT',          'hat deinen Kontaktwunsch auf %sitename% angenommen.');
define('_CONTACTLISTSUSPENDEDYOUAT',                    'hat dich als Kontakt auf %sitename% aufgehoben und von seiner Kontaktliste entfernt.');

// edit
define('_CONTACTLISTEDITINFO',                          'Kontaktdetails bearbeiten');
define('_CONTACTLISTUPDATEINFO',                        'Kontaktdetails aktualisieren');
define('_CONTACTLISTBUDDYNOTFOUND',                     'Es trat ein Fehler beim Laden des Kontaktes auf');
define('_CONTACTLISTFOREIGNBUDDY',                      'Du kannst nur deine eigenen Kontakt bearbeiten.');
define('_CONTACTLISTBUDDYUPDATED',                      'Kontaktdetails wurden aktualisiert');
define('_CONTACTLISTBUDDYUPDATEFAILED',                 'Beim Aktualisieren der Kontaktdetails trat ein Fehler auf');

// myprofile plugin and display-function
define('_CONTACTLISTTABTITLE',                          'Kontakte');
define('_CONTACTLISTMYBUDDIES',                         'Kontakte von');
define('_CONTACTLISTTHEUSERHAS',                        'Der Benutzer hat');
define('_CONTACTLISTBUDDIES',                           'Kontakte');
define('_CONTACTLISTLISTNOTPUBLIC',                     'Die Liste des Benutzers ist für dich nicht sichtbar');

// Login
define('_CONTACTLISTLOGIN',                             'Anmeldung');
define('_CONTACTLISTLOGINFAILED',                       'Login ist fehlgeschlagen!');
define('_CONTACTLISTLOGINNEEDED',                       'Für den Aufruf dieser Seite ist eine Anmeldung notwendig.');
define('_CONTACTLISTNICKNAME',                          'Benutzername');
define('_CONTACTLISTPASSWORD',                          'Passwort');
define('_CONTACTLISTREMEMBERME',                        'Login speichern');
