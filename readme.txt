Recent:

	OO
		group
			createGroup etc.
			
		chat

	caching
		buddylist
		friends you may know
		youtube titles
		
	security
	
	Bugs
		Session login prob
		
	
		
	
	Welcome message
		Add sentence with calender after calender has been added to "the dock"
		
		klieines s schriftart scvheiße
		
		"An Element contains files and links which are connected with each other. They are listed in the filesystem to folders.<br><i><b>For example</b> you could create the image-element "My Nice Holiday In Nepal" and upload all your holiday pictures in it."
	

Files and the use of them

	PHP
	inc/config.php		//mysql server confid
	inc/functions.php 	//collection of all functions used by the universeOS usually included in every file
	
	doit.php			//one of the biggest problems, the doit.php is used to show everything that diddn't fit
						//elsewhere. Now we have the mess. It is seperated by a big switch case which separes it
						//into > 30 actions (e.g "addFolder", "addElement", "deleteFolder", "addGrouo")
						
						//all the actions will be seperated in located in /actions/folders or actions/groups etc.
						
	guest.php 			//contains view for not registered user
						
	profile.php			//userprofile
	group.php			//group profile
	
	
	SEO/FB etc...
	openFileFromlink.php//opens js openFile() function to open a file if universeOS.org?file=xy is called
	out/				//in this folder is everything
	
	
	JavaScript
	inc/functions.js	//all the js stuff
	
	CSS
	inc/style.css		//all the css stuff
	
Will be deleted
	function showActivity
	function checkAuthorisation
	
