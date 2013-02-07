ContactList
===========

## About the module

ContactList (initial release was published in 2008) is a zikula module that allows the management of buddy lists and ignore lists and provides a visualisation of a friend-of-a-friend (FOAF) function.
It is customizable and can be combined with other social network modules.

The module is very reliable and is run by many communities that handle the buddy and ignore functions for thousands of users.

## Module ready for Zikula 1.3.x

The module is ready to be used with Zikula 1.3.x.
Feel free to test it and track your issues in the project's issue tracker.
Some functions that depend on other modules that are not yet ready for Zikula 1.3.x will follow later in additional releases.

## Available Translations

At the moment these translations are available
- English
- German

## Integrate ContactList into other Zikula modules!

Use the functions ContactList provides in your modules. Possible use-cases:
- Use the ignore list for messaging modules
- Use the ignore and buddy list for calendars
- Use the ignore list and buddy list for online lists
- Use the ignore list for forum software
- and so on... you'll see ContactList provices some basically 
  buddy functions for every module!
  
	The API is easy to use. the public function isIgnored and isBuddy gives you the values you need to integrate ContactLists data into your module. 
	For details see modules/ContactList/docs/developers.txt

## Modules that support ContactList

At the moment these modules support ContactList
- Communicator (Messaging module)
- Dizkus (Forum module)
- InterCom (Messaging module)
- MyProfile (Profile module)
- maybe more modules will follow...

## Changelog

Please take a look at the following file:
modules/ContactList/docs/changelog.txt
