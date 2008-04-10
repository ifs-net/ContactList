<?php
// menu
define('_CONTACTLISTCREATEBUDDY',	                	'Add new buddy');
define('_CONTACTLISTSHOWBUDDIES',	                	'Show my buddylist');
define('_CONTACTLISTSHOWIGNORELIST',              		'Show my ignorelist');
define('_CONTACTLISTUSERPREFERENCES',					'My preferences');

// user preferences
define('_CONTACTLISTUSERPREFS',							'My preferences');
define('_CONTACTLISTMYPUBLICSTATE',						'Buddy list privacy settings');
define('_CONTACTLISTUPDATEPREFS',						'Update my preferences');
define('_CONTACTLISTPRIVACYNOBODY',						'Nobody can see my buddy list');
define('_CONTACTLISTPRIVACYBUDDIES',					'My buddies can see my buddy list');
define('_CONTACTLISTPRIVACYMEMBERS',					'All registered users can see my buddy list');
define('_CONTACTLISTPREFSUPDATEERROR',					'An error occured while updating your user preferences');
define('_CONTACTLISTPREFSUPDATED',						'User preferences updated successfully');

// main
define('_CONTACTLISTMYCONTACTS',	                 	'My contacts');
define('_CONTACTLISTADDBUDDY',			                'Add new contact');
define('_CONTACTLISTTOCONFIRM',	                    	'These request are awaiting your confirmation');
define('_CONTACTLISTUNAME',				                'Username');
define('_CONTACTLISTCOMMENTS',			                'Comments');
define('_CONTACTLISTSTATE',		 	                    'State');
define('_CONTACTLISTACTIONS',			           		'Action');
define('_CONTACTLISTPUBCOMMENT',	                 	'Public comment');
define('_CONTACTLISTPRVCOMMENT',	                	'Private comment');
define('_CONTACTLISTBUDDYUNAME',	                 	'User to add as buddy');
define('_CONTACTLISTEDIT',			                   	'Edit');
define('_CONTACTLISTADD',			                      'add as buddy');
define('_CONTACTLISTNOREQUTEXT',	                 	'no reason given');
define('_CONTACTLISTREQUTEXT',	                   	'Reason');
define('_CONTACTLISTREQUESTDATE',	                	'Request\'s date');
define('_CONTACTLISTDECLINE',		                   	'Reject request');
define('_CONTACTLISTCONFIRM',		                   	'Accept request');
define('_CONTACTLISTPENDING',                   		'pending');
define('_CONTACTLISTCONFIRMED',		                 	'confirmed');
define('_CONTACTLISTSUSPENDED',		                 	'suspended');
define('_CONTACTLISTREQUESTDECLINED',	              'Request rejected');
define('_CONTACTLISTREQUESTACCEPTED',	              'Buddy accepted');
define('_CONTACTLISTREQUESTDECLINEERR',	            'Error rejecting request');
define('_CONTACTLISTREQUESTACCERR',	              	'Error accepting request');
define('_CONTACTLISTNA',				                    'n/a');
define('_CONTACTLISTSUSPEND',			                  'Suspend');
define('_CONTACTLISTDELETE',			                  'Delete');
define('_CONTACTLISTREALLYSUSPEND',		              'Should the connection between you and this buddy really be suspended? Please note: the buddy will be informed about this.');
define('_CONTACTLISTSUSPENDERROR',	               	'An error occurred while trying to suspend the buddy');
define('_CONTACTLISTBUDDYSUSPENDED',              	'The connection to this buddy was suspended successfully');

define('_CONTACTLISTFILTERBUDDIESALL',	            'All contacts');
define('_CONTACTLISTFILTERBUDDIESCONFIRM',	        'Confirmed buddies');
define('_CONTACTLISTFILTERYOURBUDDIESREJECT',		    'Rejected contacts');
define('_CONTACTLISTFILTERYOURBUDDIESSUSPEND',	    'Suspended contacts');

define('_CONTACTLISTYOURBUDDIES',	                  'Your contacts');
define('_CONTACTLISTYOURBUDDIESCONFIRM',	         	'Your confirmed buddies');
define('_CONTACTLISTYOURBUDDIESREJECT',		          'Your rejected contacts');
define('_CONTACTLISTYOURBUDDIESSUSPEND',	         	'Your suspended contacts');
define('_CONTACTLISTAMOUNTCONFIRM',	              	'Number of confirmed contacts');
define('_CONTACTLISTAMOUNTREJECTED',	             	'Number of rejected contacts');
define('_CONTACTLISTAMOUNTSUSPENDED',	            	'Number of suspended contacts');
define('_CONTACTLISTBIRTHDAY',		                	'Birthday');
define('_CONTACTLISTSENDPM',		                  	'Send message');
define('_CONTACTLISTSHOWCONTACTS',	             		'Show user\'s buddies');
define('_CONTACTLISTONLINE',			                  'online');
define('_CONTACTLISTOF',		                      	'of');

// ignore
define('_CONTACTLISTIGNORELISTMANAGEMENT',          'Ignore-list management');
define('_CONTACTLISTIGNOREUNAME',		                'User you want to ignore');
define('_CONTACTLISTIGNOREEXPLAIN',	              	'You can add users here you want to ignore. Ignoring means protection from emails etc.');
define('_CONTACTLISTADDUSER',			                  'Add user');
define('_CONTACTLISTUSERNOTFOUND',              		'User could not be found');
define('_CONTACTLISTDONOTIGNOREYOURSELF',           'I know, some people do not like themself - but you really cannot ignore you own username :-)');
define('_CONTACTLISTIGNOREDUSERADDED',            	'User was set to your ignore list successfully');
define('_CONTACTLISTNOIGNOREDUSERS',	              'You do not have any ignored users on your ignore list');
define('_CONTACTLISTIGNORELIST',		                'The following users are ignored actually');
define('_CONTACTLISTREMOVEIGNOREDUSER',	            'Remove from ignore list');
define('_CONTACTLISTUSERUPDATEERROR',	              'Updating user information failed');
define('_CONTACTLISTUSERNOLONGERIGNORED',           'User removed from ignore list');

// create
define('_CONTACTLISTCONFIRMREQU',	                	'After submitting this form a buddy request will be send to the new buddy. He then can accept or reject your request. You can send some text with the request for the new buddy');
define('_CONTACTLISTREQUESTTEXT',		                'Reason for buddy request');
define('_CONTACTLISTNOTADDYOURSELF',	              'You can not add yourself as buddy. Go out and find some friends :-)');
define('_CONTACTLISTUNAMEINVALID',		              'You have to submit a valid username');
define('_CONTACTLISTDUPLICATEREQUEST',            	'You can not make multiple requests. The user is your buddy or has already recieved a buddy request from you. Go out and find more friends instead :-)');
define('_CONTACTLISTREQUESTSENT',		                'Buddy request sent to the user');
define('_CONTACTLISTBUDDYADDED',		                'Buddy added');

// emails
define('_CONTACTLISTUNCONFIRMSUBJECT',	            'You where added as buddy');
define('_CONTACTLISTCONFIRMSUBJECT',	              'A user wants to add you as a buddy');
define('_CONTACTLISTBUDDYSUSPENDEDYOU',           	'You were suspended as buddy');
define('_CONTACTLISTREQUESTREJECTED',	              'A user has rejected your buddy request');
define('_CONTACTLISTDEARUSER',			                'Dear');
define('_CONTACTLISTTHEUSER',			                  'the user');
define('_CONTACTLISTADDEDYOUASBUDDY',	              'has added you as a buddy. This means you are friends now and your accounts got linked on the website');
define('_CONTACTLISTATTENTION',			                'Attention');
define('_CONTACTLISTAUTOMATICCONFIRM',            	'This request was automatically accepted because some times ago you wanted the other person to be your buddy');
define('_CONTACTBUDDYMANAGEMENT',		                'To access your buddy management visit the url');
define('_CONTACTLISTDONOTREPLY',		                'Please do not reply to this email. This email was sent automatically. For further questions ask the user who added / rejected / accepted / suspended you as a buddy or contact the site administrator otherwise');
define('_CONTACTLISTWANTSTOADDYOUAT',             	'wants to add you as new buddy at');
define('_CONTACTLISTREJECTORACCEPT',	              'Please reject or accept the request');
define('_CONTACTLISTHASREJECTEDYOURREQUESTAT',    	'has rejected your buddy request at');
define('_CONTACTLISTHASACCEPTEDYOURREQUESTAT',	    'has accepted your buddy request at');
define('_CONTACTLISTSUSPENDEDYOUAT',	              'suspended you as buddy');

// edit
define('_CONTACTLISTEDITINFO',			                'Edit contact information');
define('_CONTACTLISTUPDATEINFO',		                'Update information');
define('_CONTACTLISTBUDDYNOTFOUND',	              	'Error while trying to fetch buddy information');
define('_CONTACTLISTFOREIGNBUDDY',	               	'You can only edit your own buddies! Go out and find own friends :-)');
define('_CONTACTLISTBUDDYUPDATED',	               	'Buddy information was updated successfully');
define('_CONTACTLISTBUDDYUPDATEFAILED',	            'Updating buddy information failed');

// myprofile plugin and display-function
define('_CONTACTLISTTABTITLE',			                'User\'s buddies');
define('_CONTACTLISTMYBUDDIES',		                 	'User\'s buddy list');
define('_CONTACTLISTTHEUSERHAS',		                'The user has');
define('_CONTACTLISTBUDDIES',			                  'Buddies');
define('_CONTACTLISTLISTNOTPUBLIC',					'The user\'s buddy list is not visible for you');