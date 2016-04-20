/*
* This is an empty js file provided to extend the Request Rules JavaScript library.
* JavaScript functions added here is available to the pages where Request Rules are 
* triggered; typically Request Details and TableComponent pages. 
*
* NOTE: If you would be adding to this file edit 'requestRulesJSFunctions.html'
		and add the usage of the function(s). This would enable the user configuring
		the rules to have access to newly added function via the help file.
*/

/*  The following are examples of how the JS functions should be modelled. */

/**
*
* Example # 1: If the function would work on Result Field(s), then the 
* last parameter should be an array. In this array you would have a list
* of Result Field token that you can use in your JavaScript function.
* The following is the structure of such a function.
 
* function JSFuncName(Param1, Param2, Param3, ArrayOfResultFieldTokens[]) {
*    //The body of the function.
* }
*
*/

/**
*
* Example # 2: If the function would not work on Result Field(s); typically 
* in informational alerts; then it is optional to have the last parameter defined
* and used as an Array. 
*
* The following is the structure of such a function.
* 
* function JSFuncName(Param1, Param2, Param3) {
*    //The body of the function.
* }
*
*/

// Special function to hide the "Change" button when the request type id is editable by user.
function setRequestTypeEditable(editFlag) {
   if ( document.getElementById("DRIVEN_REQUEST_TYPE_ID").parentNode.parentNode.children[2].innerText == "Change" ) {
      if (editFlag)
         document.getElementById("DRIVEN_REQUEST_TYPE_ID").parentNode.parentNode.children[2].style.display="";
      else
         document.getElementById("DRIVEN_REQUEST_TYPE_ID").parentNode.parentNode.children[2].style.display="none";
   }
}

// Special function to get the request_id as rules "On Load" can't get it (PPM bug ???).
function getRequestId() {
  alert( document.getElementById("REQUEST_ID").value);
}

// To hide action buttons.
// Number 0 is the first button.
function hideButton(buttonNumber) {
	if (buttonNumber < 0) return;
	document.getElementById("SDB0_" + buttonNumber).parentNode.parentNode.parentNode.parentNode.parentNode.style.display="none";
}

// Get information about the action button the user clicked.
function whichButton() {
    alert(document["rdf"]['STEP_NAME'].value);    
/*
    alert(document["rdf"]['STEP_TRANSACTION_ID'].value);
    alert(document["rdf"]['WF_EVENT'].value);    
    alert(document["rdf"]['RESULT_VALUE'].value);    
    alert(document["rdf"]['VALIDATION_ID'].value);    
    alert(document["rdf"]['STATUS'].value);    
    alert(document["rdf"]['STEP_TYPE_CODE'].value);    
    alert(document["rdf"]['APPROVALS_REQUIRED_CODE'].value);    
    alert(document["rdf"]['STEP_NAME'].value);    
    alert(document["rdf"]['WORKFLOW_STEP_ID'].value);    
    alert(document["rdf"]['WORKFLOW_COMMAND_ID'].value);
    alert(document["rdf"]['DESTINATION_PAGE'].value);
*/
}

// Test function. The PPM doc explains how to get the same without user specific java rule (see DemandCG.pdf document at page 179).
function checkDate1PostDate2(date1,date2, message) {
/*
	Check that date 1 is greater or equal than Date 2
	If not, then display message and stop processing
*/
var now = new Date();
var month=now.getMonth()+1;
var currentDate= now.getYear() + '-' + ((month < 10 ) ? '0'+ month : month) + '-' + ((now.getDate()<10) ? '0' + now.getDate() : now.getDate()) + ' ' + now.getHours() + ':'+ now.getMinutes() + ':' + now.getSeconds();
var inputDate=date1;
var inputDate1=inputDate.replace('-',',');
inputDate1=inputDate1.replace('-',',');
inputDate1=inputDate1.replace(' ',',');
inputDate1=inputDate1.replace(':',',');
inputDate1=inputDate1.replace(':',',');
alert ('inputDate <'+ date1 +'> => InputDate1<'+ inputDate1 +'>');
var firstDateSeconds=Date.parse(date1);
alert ('Date 1 <'+ date1 +' => '+ firstDateSeconds +' seconds since 1970, date2 <'+ date2 +'> - Message <'+ message +'>');
showMessage("From ShowMessage => " + currentDate,false);
//	alert(message);
//	this.CONTINUE_PROCESSING_RULE = false;

}

// To know any request token value.
function getRequestField(token) {
  element = getTokenElement(token, RuleUtil.formName);
  // We may use the "decode" function to extract the hidden or the visible value ???)
  alert(element.getValue());
}

// setRequestField(token, valueCS) sample parameters:
// token as "REQ.DESCRIPTION",      valueCS as "my description" (description is a text field)
// token as "REQ.PRIORITY_CODE",    valueCS as "Low"            (priority is a dropdown list and "Low" is the visible value)
// token as "REQ.APPLICATION_CODE", valueCS as "AP00ZZZZ"       (priority is an autocomplete list and "AP00ZZZZ" is the hidden value for the visible application "TL DJIBOUTI")
function setRequestField(token, valueCS) {
  // This only works if the token is part of the rule dependencies
  RuleUtil.setField(token, valueCS);
}




//
//  T O T A L
//
//  FSC - February 2009
//

//
// The rule dependencies doesn't have the choice "IS NOT NULL"
// These functions are the copy of the standard functions, but with the test "IS NOT NULL"
//

/**
*  Set field(s) to required / not required status based on the required parameter value.
*	 required: true or false
*	 tokenArray: Field(s) this setting need to be applied for
*/
function setNotNullFieldRequired(required, tokenArray) {
	var domElement;
	for (counter = 0; counter < tokenArray.length; counter++) {
			// Get the element
		domElement = getTokenElement(tokenArray[counter][0], RuleUtil.formName);
			
			//Check if this element is present in the DOM and apply the required only if it is
		if (domElement != null && domElement != "undefined") {
			//Check if this element "IS NOT NULL"
			if (decode(domElement.getValue(),true)) {
				// Set the Required/Not Required status
				RuleUtil.setRequired(tokenArray[counter][0], required);
			}
		}
	}
}
/**
*	 Set field(s) to editable / not editable based on the flag
*  flag: true or false
*  tokenArray: Field(s) this setting need to be applied for
*
*/
function setNotNullFieldEditable(flag, tokenArray) {
	for (counter = 0; counter < tokenArray.length; counter++) {
		//Check if this element "IS NOT NULL"
		if (decode(getTokenElement(tokenArray[counter][0], RuleUtil.formName).getValue(),true)) {
			RuleUtil.setEditable(tokenArray[counter][0], flag);
		}
	}
}
/*
*  Set field(s) to visible or not visible 
*  flag: true = show field, false = hide field
*  tokenArray: Field(s) this setting need to be applied for
*/
function setNotNullFieldVisible(flag, tokenArray) {
	for (counter = 0; counter < tokenArray.length; counter++) {
		//Check if this element "IS NOT NULL"
		if (decode(getTokenElement(tokenArray[counter][0], RuleUtil.formName).getValue(),true)) {
			RuleUtil.setVisible(flag, tokenArray[counter][0]);
		}
	}
}
/*
*  Set field(s) style to the style class specified
*  clsName: The class name of the style
*  tokenArray: Field(s) this setting need to be applied for
*/
function setNotNullFieldStyle(clsName, tokenArray) {
	var tokenName;
	for (counter = 0; counter < tokenArray.length; counter++) {
	 	tokenName = tokenArray[counter][0];
	 	if(tokenArray[counter][1]) {
		 	tokenName = tokenArray[counter][0] + "AC_TF";
	 	}
		//Check if this element "IS NOT NULL"
		if (decode(getTokenElement(tokenName, RuleUtil.formName).getValue(),true)) {
			RuleUtil.setStyle(clsName, tokenName);
		}
	}
}

/*
*
* Hide the "Make a Copy" button - Works on IE and Firefox (but spacer not hidden on Firefox)
*
*/
function hideCopyButton() {
  var pageRefs,i;
  // Get array of <a> links
  pageRefs=document.getElementsByTagName('a');
  for(i in pageRefs)
  {
    // Look for the link which calls the request copy
    if(pageRefs[i].href == "javascript:copyRequest(true);") {
    	// Hide the parent (span) of the parent (td) of the link
    	pageRefs[i].parentNode.parentNode.style.display="none";
    	// Check if the sibbling button exists and if it is the "delete" button
    	if (pageRefs[i].parentNode.parentNode.nextSibling.nextSibling.firstChild && 
    	    pageRefs[i].parentNode.parentNode.nextSibling.nextSibling.firstChild.firstChild.href == "javascript:deleteRequest();")
    	  // Hide the blank spacer, so the "delete" button is well left aligned
    	  pageRefs[i].parentNode.parentNode.nextSibling.style.display="none";
    }
  }
}


//
//  T O T A L
//
//  FSC - September 2009
//

//
//  To show a message if the field is not null, as the "IS NOT NULL" check is missing today in the workbench
//
function showMessageNotNull(message, sourceToken, continueProcessing) {
     if (decode(getTokenElement(sourceToken, RuleUtil.formName).getValue(),true)) {
        alert(message);
        this.CONTINUE_PROCESSING_RULE = continueProcessing;
     }
}

//
//  To display a confirmation message with "OK" and "Cancel" buttons
//
//  This function is for a "before transition" event
//
function confirmProcessing(button, message) {
    if (document["rdf"]['STEP_NAME'].value == button) {
       if (!confirm(message)) {
          // Warning: this cancels the workflow action, and also the request save.
          // It is recommanded to warn the user about this if he selects "Cancel" message button.
          this.CONTINUE_PROCESSING_RULE = false;
       }
   }
}


//  03/08/2010 - evolution request #108101
//  To display a confirmation message with "OK" and "Cancel" buttons before next step of workflow
//
//  This function is for a "before transition" event
//
function confirmProcessingNextStep(message) {
	// if a message has already been displayed to user, do not display this one
    if (this.CONTINUE_PROCESSING_RULE == true) {
		// display confirmation message
		if (!confirm(message)) { // if user clicks on "Cancel" button
			  this.CONTINUE_PROCESSING_RULE = false;
		}
	}
}

//
//  To check a mandatory field
//
//  This function is for a "before transition" event
//
function checkProcessing(button, message, sourceToken) {
    if (document["rdf"]['STEP_NAME'].value == button) {
       if (!decode(getTokenElement(sourceToken, RuleUtil.formName).getValue(),true)) {
          alert(message);
          this.CONTINUE_PROCESSING_RULE = false;
       }
   }
}

//
//  To check a field which must remain empty
//
//  This function is for a "before transition" event
//
function checkProcessingEmpty(button, message, sourceToken) {
    if (document["rdf"]['STEP_NAME'].value == button) {
       if (decode(getTokenElement(sourceToken, RuleUtil.formName).getValue(),true)) {
          alert(message);
          this.CONTINUE_PROCESSING_RULE = false;
       }
   }
}

//
//  To check a mandatory field when clicking a given button
//  And check that the field remains empty when clicking another one
//
//  This function is for a "before transition" event
//
function checkMandatoryFieldCondition(button, mandatoryMessage, emptyMessage, sourceToken) {
    if (document["rdf"]['STEP_NAME'].value == button) {
       if (!decode(getTokenElement(sourceToken, RuleUtil.formName).getValue(),true)) {
          alert(mandatoryMessage);
          this.CONTINUE_PROCESSING_RULE = false;
       }
   } else {
       if (decode(getTokenElement(sourceToken, RuleUtil.formName).getValue(),true)) {
          alert(emptyMessage);
          this.CONTINUE_PROCESSING_RULE = false;
       }
   }
}


//
//  T O T A L
//
//  FSC - October 2009
//

//
//  To check a mandatory field
//
function checkMandatoryField(message, sourceToken) {
     if (!decode(getTokenElement(sourceToken, RuleUtil.formName).getValue(),true)) {
        alert(message);
        this.CONTINUE_PROCESSING_RULE = false;
     }
}

// This function is only a turn around for writable dates due to a PPM bug
// We should not need this function that we call on field change to check writable dates
// This only work with DATES which are WRITABLE
// TEST POUR LES DATES EN ECRITURE SEULEMENT
function checkDateToReset(sourceToken) {
	  // This check only works for writable dates
	  if (!document.getElementById(sourceToken).value) {
		    // Reset the field
		    // This only works if the sourceToken is part of the rule dependencies
		    setRequestField(sourceToken, "");
    }	
}


//
//  T O T A L
//
//  FSC - November 2009
//


//
// F O N C T I O N   D E   C O N V E R S I O N   D E   C A R A C T E R E S   H T M L
//

/* Copied from standard PPM JavaScript CompValidation.js */

/**
 * Taken from ExpressUtils.  We need to do this on the Javascript side because
 * javascript doesn't understand the HTML escaped characters (argh).
 */
function deEscapeFromHTML(value) {
    var undef;
    if(value == undef || value == "") {
        return "";
    }
    value = value.replace(/&lt;/gi, "<");
    value = value.replace(/&gt;/gi, ">");
    value = value.replace(/&quot;/gi, "\"");
    // de-escape both 039 and 39 for right now
    // until I can make sure we only use 39
    value = value.replace(/&#039;/gi, "'");
    value = value.replace(/&#39;/gi, "'");
    value = value.replace(/&amp;/gi, "&");

    return deEscapeForJavaScript(value);
}



//
// F O N C T I O N   D E   R E T O U R   A J A X   ( E X E M P L E )
//

function getAnswer() {
	switch(ajax.readyState){
 		case 0:
		case 1:
			//ouverture de la communication
		break;
		case 2:
			//envoi de la requ�te
		break;
		case 3:
			//r�ception des donn�es
		break;
		case 4:
			//donn�es arriv�es
			//ajax.status contient 200, 404, ...
			//ajax.statusText contient OK, NOT FOUND, ...
			var reponseTexte= ajax.responseText;
			//traitement
			///...
		break;	
	}
}

//
// F O N C T I O N   A J A X   D E D I E E   A   L ' A P P E L   D U   W E B   S E R V I C E
//

function ajaxPost(webServiceUrl, soapMessage) {
    var xhr_object = null;
    if(window.ActiveXObject) // Test IE first!
      xhr_object = new ActiveXObject("Microsoft.XMLHTTP");
    else if(window.XMLHttpRequest) // FIREFOX
      xhr_object = new XMLHttpRequest();
    else
      return(false);
    // "false" means "synchrone", "true" means "asynchrone"
    xhr_object.open("POST", webServiceUrl, false);
    
/* d�claration de la fonction de retour directement dans le code :
    xhr_object.onreadystatechange=function() {
       if (xhr_object.readyState==4) {
          alert(xhr_object.responseText)
       }
    }
*/
/* d�claration de la fonction de retour : attention cette fonction doit pointer sur le m�me objet "xhr_object" (� d�clarer avant la fonction et pas dedans)
    xhr_object.onreadystatechange=getAnswer;
*/
    xhr_object.setRequestHeader("MessageType", "CALL")
    xhr_object.setRequestHeader("SOAPAction", "http://tempuri.org/GetTopicUrl"); 
    xhr_object.setRequestHeader("Content-Type", "text/xml")
    
    xhr_object.send(soapMessage);
    
/* Answer is catched directly here after when no "onreadystatechange" specified */
    // readyState as 0 : non initialise; 1 : connexion etablie; 2 : requete recue; 3 : reponse en cours; 4 : termine.
		// readyState as 0 : null; 1 : ouverture de la communication; 2 : envoi de la requ�te; 3 : r�ception des donn�es; 4 : donn�es arriv�es
//alert(xhr_object.readyState);
    if(xhr_object.readyState == 4) {
//alert(xhr_object.status);
//alert(xhr_object.responseText);
      if (xhr_object.status == 200)
    	  return(xhr_object.responseText);
    	else {
    	  alert("Problem retrieving XML data");
    	  return(null);
    	}
    } else return(null);
}


//
// U I   R U L E   A P P E L E E   P O U R   A F F I C H E R   L E   L I E N   D U   F O R U M
//

function checkForum(forumId) {
	// V�rifie le num�ro car le web service plante si le num�ro est absent
	if (!forumId)
	  // D�clare un num�ro bidon
	  forumId = "-999";
	  
  var l_hostname = window.location.hostname;
  var l_port = window.location.port;
  var wsURL;

  if (l_hostname == "dsiomgq01.trd.total.com" && !l_port)
	  // D�clare l'URL du service en DEV
	  var wsURL = "http://rmfrdefa501.fr.rm.corp.local:10000/yaf/Service/CheckIDForum.asmx";
  else if ( (l_hostname == "dsiomgq01.trd.total.com" || l_hostname == "dsiomgq01.rm.corp.local" ) && l_port == 8481 )
    // D�clare l'URL du service en Qualification
    wsURL = "http://rmfrdefa504:10001/yaf/Service/CheckIDForum.asmx";
  else if (l_hostname == "dsiomgi01.trd.total.com" || l_hostname == "dsiomgi01.rm.corp.local")
    // D�clare l'URL du service en Pre-production (int�gration)
    wsURL = "http://rsf-pprod.rm.corp.local/yaf/Service/CheckIDForum.asmx";
  else if (l_hostname == "dsiomgp01.trd.total.com" || l_hostname == "omega.rm.corp.local" || l_hostname == "rsf-gdd.rm.corp.local")
    // D�clare l'URL du service en Production
    wsURL = "http://rsf.rm.corp.local/yaf/Service/CheckIDForum.asmx";
  else
    // Point to production by defaultD�clare l'URL du service en Production
    wsURL = "http://rsf.rm.corp.local/yaf/Service/CheckIDForum.asmx";

//alert(l_hostname);
//alert(wsURL);
	
	// Appel du web service
	var wsAnswer = ajaxPost(wsURL,'<?xml version="1.0" encoding="UTF-8"?><soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/"><soapenv:Header /><soapenv:Body><GetTopicUrl xmlns="http://tempuri.org/"><topicID>' + forumId + '</topicID></GetTopicUrl></soapenv:Body></soapenv:Envelope>');

// test code
//setRequestField("REQD.P.RSF_URL_FORUM", "http://www.google.fr".length + "." + ("Forum topic #" + forumId).length + "." + "http://www.google.fr" + "." + "Forum topic #" + forumId);
//return;

// test code 2
//wsAnswer = '<?xml version="1.0" encoding="utf-8"?><soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema"><soap:Body><GetTopicUrlResponse xmlns="http://tempuri.org/"><GetTopicUrlResult>http://rmfrdefa501.fr.rm.corp.local:10000/yaf/default.aspx?g=posts&amp;t=2</GetTopicUrlResult></GetTopicUrlResponse></soap:Body></soap:Envelope>';

  // Check if the answer exists
	if (wsAnswer) {
    // Translate HMTH characters
    wsAnswer = deEscapeFromHTML(wsAnswer);
  	// Look for the answer
  	var pos1 = wsAnswer.indexOf("GetTopicUrlResult");
	  if (pos1 > 0) {
		  // Look for the answer content
		  pos1 = wsAnswer.indexOf("<GetTopicUrlResult>");
  		if (pos1 > 0) {
  			// Get the first search string length
  			var length1 = "<GetTopicUrlResult>".length;
	  		// Look for the content end
	  		var pos2 = wsAnswer.indexOf("</GetTopicUrlResult>");
	  		// Get the forum URL
	  		var forumUrl = wsAnswer.substring(pos1 + length1, pos2);
	  		// Get the URL title
	  		var forumTitle = "Forum topic #" + forumId;
        // Set the value with different code and description [code length . meaning length . code . meaning]
	  		setRequestField("REQD.P.RSF_URL_FORUM", forumUrl.length + "." + forumTitle.length + "." + forumUrl + "." + forumTitle);
		  } else {
		  	alert("Forum Id does not exist");
	  		setRequestField("REQD.P.RSF_URL_FORUM", "0.22..Waiting the forum link");
		  }
	  } else alert("Forum web service not available");
	} else alert("Web service with unkown error");
}


//
// Show/Hide table component tokens (I.E. only)
//
function setTableVisible(flagTable, flagSection, tokenArray) {
	for (counter = 0; counter < tokenArray.length; counter++) {
    var tcomp = document.getElementById("DIV_EC_REQUEST_TC_" + tokenArray[counter][0]);
    if (tcomp) {
      if (!flagSection) {
        // Manage table component itself
        if (!flagTable) {
          // Hide table
          if (tcomp.parentNode.parentNode.parentNode)
            tcomp.parentNode.parentNode.parentNode.style.display = "none";
        }
        // Hide table component parent section
        tcomp.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.previousSibling.style.display = "none";
      } else {
        // Show table component parent section
        tcomp.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.previousSibling.style.display = "";
        // Manage table component itself
        if (!flagTable) {
          // Hide table
          if (tcomp.parentNode.parentNode.parentNode)
            tcomp.parentNode.parentNode.parentNode.style.display = "none";
        } else {
          // Show table
          if (tcomp.parentNode.parentNode.parentNode)
            tcomp.parentNode.parentNode.parentNode.style.display = "";
        }
      }
    }
	}
}

