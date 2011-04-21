
	+-----------------------------+
	| xPandMenu MULTI-LEVEL class |
	+-----------------------------+
	| Patrick Brosset             |
	| patrickbrosset@gmail.com    |
	+-----------------------------+
	| 02/2005                     |
	+-----------------------------+


	General
	-------
	
	The xPandMenu PHP class can help you create complex menus for your webpages.
	Items organised in the menu are displayed in a TreeView structure which
	resembles the MS Windows OS's directory explorer.
	
	The usage principle is simple:
	You first create a root object which will be the `base` of your menu.
	You then append `child` nodes to this root and `child` nodes to these nodes as well.
	The menu can have as many sub-levels as needed.
	
	
	Example
	-------
	
	This example shows how you can organise your items on your website using this PHP class.
	
	+ Home page
	+ About me
		+ my CV
		+ contact details
			+ email
			+ phone
	+ Photos
		+ Travels
		+ Parties
			+ my birthday
			+ ski trip
		+ Friends
	+ News
		+ PHP Classes
			+ XpandMenu class available on PHPClasses.org
	
	
	Benefits
	--------
	
	+ Each item in your list that contains sub-items can be collapsed / expanded by clicking on it.
	+ Each item in your list can be given a 2-states image that will be displayed next to the item
		and be swapped wheter the node is open or closed
	+ The entire menu-look can be highly changed through CSS
	+ The HTML code generated for the menu is only made of <UL></UL> lists
	+ The state of the menu can be saved from page to page
	+ The generated code may be saved once and for all for future use
	
	
	Requirements
	------------
	
	The menu's collapse/expand facility is done with Javascript. The client must have javascript turned-on to
	use the menu.
	The class was developed using PHP v 4.0.1
	
	
	Hoe to use the class?
	---------------------
	
	Please have a look through these 4 example files as they currently represent the only documentation available.
	I'll try to document the class itself soon.
	
	> example_simple.php
		Simple and basic example on how to use the main functions of the class
		You should be able to get started with this, although it is recommended to have a look at the other
		examples as well in order to use more interesting features
	
	> example_complex.php
		Still simple example on how to build a menu, but the menu is a lot more complex this time and 
		includes several sub-levels
		
	> example_SaveState.php
		This example builds the same menu as the previous example file but lets you save the state of your menu:
		When a user goes to another page which also displays the menu, you might want to display the menu as
		it was when the user left the previous page, that is, with expanded or collapsed nodes as they were.
		In this example, you learn how to simply call 2 Javascript functions that will save the state of the menu
		inside a cookie and then restore it from that same cookie.
		Warming: the user must allow cookies on his browser to ba able to use this feature.
	
	> example_reuseHTML.php
		In this example you also build the same complex tree but you are taught how to save the generated code
		in a file!
		Why? In some cases, your menu might not need to be dynimically generated every time, for instance if the
		menu is used to display links to your website's pages, these links might never change at all.
		The example helps you build a page that will:
		- generate the menu if it had not been generated before
		- save the generated code inside a file
		- and restore the menu from that file if it is found 
		