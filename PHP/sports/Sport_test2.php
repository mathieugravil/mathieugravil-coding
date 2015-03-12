<HTML>
   <HEAD>
         <meta http-equiv="content-type" content="text/html; charset=iso-8859-1">
	
          <link rel="stylesheet" href="style.css" type="text/css">

   </HEAD>
 <style type="text/css">
	/*###### Bouton gauche des mois ######*/ 
	.MonthLeft{
		width:14px;
		height:50px;
		background:url('images/static.png') -112px -250px;
		position:absolute;
		left:-2px;
		top:0px;
	}
	.MonthLeftOver{
		width:14px;
		height:50px;
		background:url('images/static.png') -126px -250px;
		position:absolute;
		left:-2px;
		top:0px;
	}
	.MonthLeftClick{
		width:14px;
		height:50px;
		background:url('images/static.png') -140px -250px;
		position:absolute;
		left:-2px;
		top:0px;
	}
	/*###### Bouton droit des mois ######*/ 
	.MonthRight{
		width:14px;
		height:50px;
		background:url('images/static.png') -154px -250px;
		position:absolute;
		right:-2px;
		top:0px;
	}
	.MonthRightOver{
		width:14px;
		height:50px;
		background:url('images/static.png') -168px -250px;
		position:absolute;
		right:-2px;
		top:0px;
	}
	.MonthRightClick{
		width:14px;
		height:50px;
		background:url('images/static.png') -182px -250px;
		position:absolute;
		right:-2px;
		top:0px;
	}
	
	/*###### Bouton haut des ann�es ######*/ 
	
	.YearTop{
		width:14px;
		height:25px;
		background:url('images/static.png') -196px -250px;
		position:absolute;
		right:-2px;
		top:0px;
	}
	.YearTopOver{
		width:14px;
		height:25px;
		background:url('images/static.png') -210px -250px;
		position:absolute;
		right:-2px;
		top:0px;		
	}
	.YearTopClick{
		width:14px;
		height:25px;
		background:url('images/static.png') -224px -250px;
		position:absolute;
		right:-2px;
		top:0px;		
	}
	/*###### Bouton bas des ann�es ######*/ 
	
	.YearBottom{
		width:14px;
		height:25px;
		background:url('images/static.png') -196px -275px;
		position:absolute;
		right:-2px;
		bottom:0px;		
	}
	.YearBottomOver{
		width:14px;
		height:25px;
		background:url('images/static.png') -210px -275px;
		position:absolute;
		right:-2px;
		bottom:0px;			
	}
	.YearBottomClick{
		width:14px;
		height:25px;
		background:url('images/static.png') -224px -275px;
		position:absolute;
		right:-2px;
		bottom:0px;			
	}
	/*###### conteneur principal ######*/ 
	.calendar{
		width:300px;
		height:250px;
		background:url('images/static.png') no-repeat;
		position:absolute;
		left:400px;
		font-weight:bold;
		font-family:Tahoma,"Lucida Grande",Verdana,Arial,Helvetica,sans-serif;
		font-size:11px;
		text-align:center;
	}
	
	.contentMonth{
		width:130px;
		height:50px;
		background:url('images/static.png') -100px -300px repeat-x;
		position:absolute;
		left:85px;
		top:5px;
	}
	.pMonth{
		width:130px;
		height:50px;
		line-height:50px;
		display:block;
	}
	.contentDay{
		width:56px;
		height:50px;
		line-height:25px;
		text-align:center;
		background:url('images/static.png') 0px -250px;
		position:absolute;
		left:15px;
		top:5px;
	}
	.contentYear{
		width:56px;
		height:50px;
		background:url('images/static.png') -56px -250px;
		position:absolute;
		left:230px;
		top:5px;
	}
	.pYear{
		width:42px;
		height:50px;
		line-height:50px;
		display:block;
	}
	.contentListDay{
		width:290px;
		height:155px;
		overflow:hidden;
		position:absolute;
		left:5px;
		top:90px;

	}
	.contentListDay ul{
		width:100%;
		height:100%;
		position:absolute;
		margin:0px;
		padding:2px 0px 0px 1px;
	}
	.dayCurrent{
		width:41px;
		height:25px;
		line-height:25px;
		display:block;
		float:left;
		text-align:center;
		color:#000000;
		font-weight:bold;
		background:url('images/static.png') -41px -352px;
	}
	.liOut{
		width:41px;
		height:25px;
		line-height:25px;
		display:block;
		float:left;
		text-align:center;
		color:#000000;
		font-weight:bold;
		background:url('images/static.png') 0px -352px;
		cursor:pointer;
	}
	.liHover{
		width:41px;
		height:25px;
		line-height:25px;
		display:block;
		float:left;
		text-align:center;
		color:#000000;
		font-weight:bold;
		background:url('images/static.png') -41px -352px;
		cursor:pointer;
	}
	.liInactive{
		width:41px;
		height:25px;
		line-height:25px;
		display:block;
		float:left;
		text-align:center;
		color:#000000;
		font-weight:bold;
		background:url('images/static.png') -82px -352px;
	}
	.contentNameDay{
		width:290px;
		height:27px;
		line-height:27px;
		overflow:hidden;
		position:absolute;
		left:5px;
		top:63px;
		padding:0px;
		margin:0px;
		list-style:none;
	}
	
	.contentNameDay li{
		width:41px;
		display:block;
		float:left;
		text-align:center;
		color:#000000;
		font-weight:bold;
	}

	.bugFrame{
		position:absolute;
		top:0px;
		left:0px;
		background:url('images/static.png') no-repeat;
		z-index:0;
		width:100%;
		height:100%;
		border:0px;
	}
</style>
<script type="text/javascript">
	var calendarElement, calendarDestruct = false, preventDouble = true;
	document.onclick = function(e){
		var source=window.event?window.event.srcElement:e.target;
		if(!source.calendrier && calendarDestruct && preventDouble){
			calendarDestruct = false;
			calendarElement.calendarActive = false;
			while (document.getElementById('Calendrier').childNodes.length>0) {
				document.getElementById('Calendrier').removeChild(document.getElementById('Calendrier').firstChild);
			}
			document.body.removeChild(document.getElementById('Calendrier'));
		}
		else if(!preventDouble){preventDouble = true}
	}
	function calendar(element){
		var regTest = /Debut|Fin$/;
		if(regTest.test(element.id)){
			this.allowFullMonth = true;
			this.destinations = [element.id.replace(regTest, 'Debut'), element.id.replace(regTest, 'Fin')];
		}
		if(document.getElementById('Calendrier') && element != calendarElement){
			while (document.getElementById('Calendrier').childNodes.length>0) {
				document.getElementById('Calendrier').removeChild(document.getElementById('Calendrier').firstChild);
			}
			document.body.removeChild(document.getElementById('Calendrier'));
			calendarElement.calendarActive = false;
			preventDouble = false;
		}
		else{preventDouble = true;}
		calendarElement = element;
		if(!element.calendarActive){
		//Propri�t� de la date ( ann�e , mois etc ... )
		this.monthCurrent = null;
		this.yearCurrent = null;
		this.dayCurrent = null;
		this.dateCurrent = null;
		//Le timer pour les effet ( fade in ^^ )
		this.timer = null;
		/*###### Objet composant le calendrier ######*/
		// la div principale
		this.calendar = null;
		
		this.bugFrame = null;
		//div contenant les mois ainsi que les deux boutons suivant et pr�c�dent
		this.contentMonth = null;
		this.currMonth = null;
		this.pMonth = null;
		this.MonthLeft = null;
		this.MonthRight = null;
		
		//Div contenant l'ann�e ainsi que les deux boutons
		this.contentYear = null;
		this.pYear = null;
		this.YearTop = null;
		this.YearBottom = null;
		
		//Div contenant le nom des jours
		this.contentNameDay = null;
		
		//Div contenant la liste des jours
		this.contentListDay = null;
		
		/*###### FIN des Objet du calendrier ######*/
		
		//Liste des dates courantes
		this.from = null;
		//Liste des dates suivantes
		this.to = null;
		
		this.opacite = 0 ;
		this.direction = null;
		//Variable permettant de mettre a  jour le header + slide
		this.inMove = false;
		//Tableau d'�l�ment a d�plac�
		this.elementToSlide = new Array();
		//Index de l'�l�ment en cours
		this.currentIndex = 0;
		//Param�tre pour lancement automatique
		this.timePause = 0 ; //permet de d�finir le temps de pause entre deux slide
		this.auto = false ; //Permet d'activer ou non le slide automatique
	
		//Input sur lequel on a cliqu�
		this.element = (element) ? element: null;
		element.calendarActive = true;
		//Tableaux contenant le nom des mois et jours
		this.monthListName = new Array('Janvier', 'F�vrier', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Ao�t', 'Septembre', 'Octobre', 'Novembre', 'D�cembre');
		this.dayListName = new Array('Lu','Ma','Me','Je','Ve','Sa','Di');
		this.dayFullName = new Array('Lun','Mar','Mer','Jeu','Ven','Sam','Dim');
			
		this.IsIE=!!document.all;
		
		this.init();
		}
	}
	
	calendar.prototype.init = function (){
		var me = this;
		//On cr�er une div principale
		this.calendar = this.newElement({"typeElement":"div","classeCss":"calendar","parent":null});
		this.calendar.id = 'Calendrier';
		//Pour combler un bug ie , on doit ajouter les filtres d'opacit�
		//Ajout du filtre
	      if(this.IsIE)
	      {
	        this.calendar.style.filter='alpha(opacity=0)';
	        this.calendar.filters[0].opacity=0;
	      }
	      else
	      {
	        this.calendar.style.opacity='0';
	      }
		//Cr�ation d'une frame pour combler un bug li� aux liste sous ie
		this.bugFrame = this.newElement({"typeElement":"iframe","classeCss":"bugFrame","parent":this.calendar});
		//Cr�ation d'une divContenant le fond  pour combler un bug sous ie
		var temp = this.newElement({"typeElement":"div","classeCss":"bugFrame","parent":this.calendar});
		//Cr�ation des contenants ( mois , ann�e , jours , listes jours etc ... )

		this.contentDay = this.newElement({"typeElement":"div","classeCss":"contentDay","parent":this.calendar});
		this.contentMonth = this.newElement({"typeElement":"div","classeCss":"contentMonth","parent":this.calendar});
		this.currMonth = this.newElement({"typeElement":"div","classeCss":"currMonth","parent":this.contentMonth});
		this.pMonth = this.newElement({"typeElement":"span","classeCss":"pMonth","parent":this.currMonth});
		this.contentYear = this.newElement({"typeElement":"div","classeCss":"contentYear","parent":this.calendar});
		this.pYear = this.newElement({"typeElement":"span","classeCss":"pYear","parent":this.contentYear});
		this.contentNameDay = this.newElement({"typeElement":"ul","classeCss":"contentNameDay","parent":this.calendar});
		this.contentListDay = this.newElement({"typeElement":"div","classeCss":"contentListDay","parent":this.calendar});
		
		//Ajout des �l�ments dans les conteneurs ( bouton + initialisation des dates )
		this.MonthLeft = this.newElement({"typeElement":"div","classeCss":"MonthLeft","parent":this.contentMonth});
		this.MonthRight = this.newElement({"typeElement":"div","classeCss":"MonthRight","parent":this.contentMonth});
		//Ajout des �v�nements sur les div
		this.MonthLeft.onclick = function(){me.updateMonthBefNexCur("before");me.SlideToRight();return false};
		this.MonthRight.onclick = function(){me.updateMonthBefNexCur("next");me.SlideToLeft();return false};
		if(this.allowFullMonth){
			this.currMonth.style.cursor = 'pointer';
			this.currMonth.onclick = function(){
				document.getElementById(me.destinations[0]).value = "1/"+ ((me.monthCurrent+1 == 13) ? 1:me.monthCurrent+1)+"/"+me.yearCurrent;
				document.getElementById(me.destinations[1]).value = me.day_number[me.monthCurrent]+ '/' + ((me.monthCurrent+1 == 13) ? 1:me.monthCurrent+1)+"/"+me.yearCurrent;
				calendarDestruct = false;
				calendarElement.calendarActive = false;
				while (document.getElementById('Calendrier').childNodes.length>0) {
					document.getElementById('Calendrier').removeChild(document.getElementById('Calendrier').firstChild);
				}
				document.body.removeChild(document.getElementById('Calendrier'));
			}
		}
		
		this.YearTop = this.newElement({"typeElement":"div","classeCss":"YearTop","parent":this.contentYear});
		this.YearBottom = this.newElement({"typeElement":"div","classeCss":"YearBottom","parent":this.contentYear});
		
		this.YearTop.onclick = function(){me.updateYearBefNexCur("next");me.SlideToTop();return false};
		this.YearBottom.onclick = function(){me.updateYearBefNexCur("before");me.SlideToBottom();return false};
		if(this.allowFullMonth){
			this.pYear.style.cursor = 'pointer';
			this.pYear.onclick = function(){
				document.getElementById(me.destinations[0]).value = "1/1/"+me.yearCurrent;
				document.getElementById(me.destinations[1]).value = "31/12/"+me.yearCurrent;
				calendarDestruct = false;
				calendarElement.calendarActive = false;
				while (document.getElementById('Calendrier').childNodes.length>0) {
					document.getElementById('Calendrier').removeChild(document.getElementById('Calendrier').firstChild);
				}
				document.body.removeChild(document.getElementById('Calendrier'));
			}
		}
		
		//Ajout des �v�nements li�s au survol et appuis de la souris sur les �l�ments;
		this.MonthLeft.onmouseover = function(){this.className = "MonthLeftOver"};
		this.MonthLeft.onmouseout = function(){this.className = "MonthLeft"};
		this.MonthLeft.onmousedown = function(){this.className = "MonthLeftClick"};
		this.MonthLeft.onmouseup = function(){this.className = "MonthLeftOver"};
		
		this.MonthRight.onmouseover = function(){this.className = "MonthRightOver"};
		this.MonthRight.onmouseout = function(){this.className = "MonthRight"};
		this.MonthRight.onmousedown = function(){this.className = "MonthRightClick"};
		this.MonthRight.onmouseup = function(){this.className = "MonthRightOver"};
		
		this.YearTop.onmouseover = function(){this.className = "YearTopOver"};
		this.YearTop.onmouseout = function(){this.className = "YearTop"};
		this.YearTop.onmousedown = function(){this.className = "YearTopClick"};
		this.YearTop.onmouseup = function(){this.className = "YearTopOver"};
		
		this.YearBottom.onmouseover = function(){this.className = "YearBottomOver"};
		this.YearBottom.onmouseout = function(){this.className = "YearBottom"};
		this.YearBottom.onmousedown = function(){this.className = "YearBottomClick"};
		this.YearBottom.onmouseup = function(){this.className = "YearBottomOver"};
		
		//R�cup�ration de la date du champs sinon date par d�faut
		
		//Si l'�l�ment sur lequel on a cliquez n'est pas vide on extrait la date
		if(this.element != null && this.element.value != ""){
			var reg=new RegExp("/", "g");
			var dateOfField = this.element.value;
			var dateExplode = dateOfField.split(reg);
			this.dateCurrent = this.getDateCurrent(dateExplode[0], dateExplode[1] - 1,dateExplode[2]);
		}
		else{
			this.dateCurrent = this.getDateCurrent();
		}
		
		//R�cup�ration de la date du champs , sinon cr�ation d'une nouvelle;
		this.monthCurrent = this.dateCurrent.getMonth();
		this.yearCurrent = this.dateCurrent.getFullYear();
		this.dayCurrent = this.dateCurrent.getDate();
		
		//Cr�ation du mois courant
		this.from = this.createContentDay(0,"left");
		this.createMonth({"CurrentDay":this.dayCurrent,"CurrentMonth":this.monthCurrent,"CurrentYear":this.yearCurrent,"conteneur":this.from});
		//Cr�ation de la div qui d�filera  On le remplira au moment ou on en aura besoins
		this.to = this.createContentDay(parseInt(this.calendar.offsetWidth),"left");
		this.createMonth({"CurrentDay":this.dayCurrent,"CurrentMonth":this.monthCurrent,"CurrentYear":this.yearCurrent,"conteneur":this.to});
		
		//On ajoute les �l�ments souhait�s ( ici un tableau )  on peut utiliser la m�thode AddElement pour ajouter un seul �l�ment. on peut ajouter un id ou directement l'�l�ment ;-)
		this.AddElements(Array(this.from,this.to));
		
		//Cr�ation de l'entete
		this.createHeader();
		this.updateDateHeader();
		
		//Positionnement du calendrier
		this.getPosition();
		
		//Apparition
		this.fadePic(0,true);
	}
	
	calendar.prototype.getDateCurrent = function (day,month,year){
			
			//Aujourd'hui si month et year ne sont pas renseign�s
			if(year == null || month == null){
				return (new Date());
			}
			
			else{
				//Cr�ation d'une date en fonction de celle pass�e en param�tre
				return (new Date(year, month , day));
			}
	}
	
	calendar.prototype.newElement = function (parameter){
		var typeElement = parameter['typeElement'];
		var classToAffect = parameter['classeCss'];
		var parent = parameter['parent'];
		
		var newElement = document.createElement(typeElement);
		newElement.className = classToAffect;
		newElement.calendrier = true;
		if(parent == null){
			document.body.appendChild(newElement);
		}
		else{
			parent.appendChild(newElement);
		}
		return newElement;
	}

	calendar.prototype.createMonth = function(parameter){
		//R�cup�ration des param�tres
		var CurrentDay = parameter["CurrentDay"];
		var CurrentMonth = parameter["CurrentMonth"];
		var CurrentYear = parameter["CurrentYear"];
		var conteneur = parameter["conteneur"];
		
		//On commence par d�truire toute les date du conteneur :)
		/*for(var i = 0 , l = conteneur.childNodes.length; i < l;i++ ){
			conteneur.removeChild(conteneur.childNodes[i]);
		}*/
		while (conteneur.childNodes.length>0) {
			conteneur.removeChild(conteneur.firstChild);
		}
		//conteneur.innerHTML = '';
		
		//Appel de la m�thode getDateCurrent retournant la date courante ou la date pass� en param�tre
		var dateCurrent = this.getDateCurrent(CurrentDay,CurrentMonth,CurrentYear);
		
		//Mois actuel
		var monthCurrent = dateCurrent.getMonth()
		
		//Ann�e actuelle
		var yearCurrent = dateCurrent.getFullYear();
		
		//Jours actuel
		var dayCurrent = dateCurrent.getDate();
		
		// On r�cup�re le premier jour de la semaine du mois
		var dateTemp = new Date(yearCurrent, monthCurrent,1);
		
		//test pour v�rifier quel jour �tait le premier du mois par rapport a la semaine
		this.current_day_since_start_week = (( dateTemp.getDay()== 0 ) ? 6 : dateTemp.getDay() - 1);
		
		//On initialise le nombre de jour par mois et on v�rifis si l'on est au mois de f�vrier
		var nbJoursfevrier = (yearCurrent % 4) == 0 ? 29 : 28;
		//Initialisation du tableau indiquant le nombre de jours par mois
		this.day_number = new Array(31,nbJoursfevrier,31,30,31,30,31,31,30,31,30,31);
		
		//On commence par ajouter les nombre de jours du mois pr�c�dent
		
		//Calcul des date en fonction du moi pr�c�dent
		
		var dayBeforeMonth = ((this.day_number[((monthCurrent == 0) ? 11:monthCurrent-1)]) - this.current_day_since_start_week)+1;
	
		for(i  = dayBeforeMonth ; i <= (this.day_number[((monthCurrent == 0) ? 11:monthCurrent-1)]) ; i ++){
			
			this.createDayInContent(i,false,false,conteneur);
		}
		
		//On remplit le calendrier avec le nombre de jour, en remplissant les premiers jours par des champs vides
		for(var nbjours = 0 ; nbjours < (this.day_number[monthCurrent] + this.current_day_since_start_week) ; nbjours++){
		//et enfin on ajoute les dates au calendrier
		//Pour g�rer les jours "vide" et �viter de faire une boucle on v�rifit que le nombre de jours corespond bien au
		//nombre de jour du mois
			if(nbjours < this.day_number[monthCurrent]){
				if(dayCurrent == (nbjours+1)){
					this.createDayInContent(nbjours+1,true,true,conteneur);
				}
				else{
					this.createDayInContent(nbjours+1,false,true,conteneur);
				}
			}
		}
		
		//Calcul des date en fonction du moi suivant
		var nbCelRest = 42 - (this.day_number[monthCurrent]+this.current_day_since_start_week);
		
		for(i  = 0 ; i <  nbCelRest ; i ++){
			
			this.createDayInContent(i+1,false,false,conteneur);
		}

	}
	
	calendar.prototype.createDayInContent = function (dateDay,CurrentDay,active,conteneur){
		var me = this;
		//Cr�ation d'un li comprenant un noeud texte avec la date du jour
		var liDay = document.createElement("li");
		liDay.calendrier = true;
		var TextContent = document.createTextNode(dateDay);
		//Pour �viter les if else ....
		liDay.className = (CurrentDay) ? "dayCurrent":"liOut";
		liDay.className = (!active) ? "liInactive":liDay.className;
		liDay.appendChild(TextContent);
		//Ajout du survol :)
		if(active){
			liDay.onmouseover = function(){this.className = (this.className == "dayCurrent") ? this.className : "liHover";};
			liDay.onmouseout = function(){this.className = (this.className == "dayCurrent") ? this.className : "liOut";};
			liDay.onclick = function(){me.dayCurrent = this.innerHTML ; me.fillField()};
		}
		//Ajout de l'�l�ment dans la liste
		conteneur.appendChild(liDay);
	}
	
	calendar.prototype.createContentDay = function (positionTo,position){
		//Cr�ation d'un li comprenant un noeud texte avec la date du jour
		var ulDays = document.createElement("ul");
		ulDays.calendrier = true;
		ulDays.className = "dayCal";
		
		if(position != "top"){
			if(positionTo != null){ulDays.style.left = positionTo + "px";}
			ulDays.style.top = 0 + "px";
		}
		else{
			if(positionTo != null){ulDays.style.top = positionTo + "px";}
			ulDays.style.left = 0 + "px";
		}
		this.contentListDay.appendChild(ulDays);
		return ulDays;
	}
	
	calendar.prototype.createCalendar = function (){
		//Cr�ation d'un li comprenant un noeud texte avec la date du jour
		var divContent = document.createElement("div");
		divContent.calendrier = true;
		divContent.className = "calendrier";
		document.body.appendChild(divContent);
		return divContent;
	}
	
	calendar.prototype.createHeader = function(){

		//Ajout des jours
		for(var i = 0 , l = this.dayListName.length ; i < l ; i++){
			var liDayTemp = document.createElement("li");
			liDayTemp.calendrier = true;
			TextContent = document.createTextNode(this.dayListName[i]);
			liDayTemp.appendChild(TextContent);
			//Ajout du jour dans la liste
			this.contentNameDay.appendChild(liDayTemp);
		}
	}
	
	calendar.prototype.updateDateHeader = function(){
		var me = this ;
		//On commence par d�truire tous les enfants des mois et ann�es
		while (this.pMonth.childNodes.length>0) {
			this.pMonth.removeChild(this.pMonth.firstChild);
		}
		
		while (this.pYear.childNodes.length>0) {
			this.pYear.removeChild(this.pYear.firstChild);
		}
		
		while (this.contentDay.childNodes.length>0) {
			this.contentDay.removeChild(this.contentDay.firstChild);
		}
		
		//Ajout de la date du jour
		var nomDuJour =  this.dayFullName[((this.dateCurrent.getDay()-1) == -1) ? 6 :(this.dateCurrent.getDay()-1)];
		var TextContent = document.createTextNode(nomDuJour);
		this.contentDay.appendChild(TextContent);
		var retourLigne = document.createElement("br");
		this.contentDay.appendChild(retourLigne);
		TextContent = document.createTextNode(this.dayCurrent);
		this.contentDay.appendChild(TextContent);
		
		
		//Ajout du mois 
		TextContent = document.createTextNode(this.monthListName[(this.monthCurrent == 12) ? 0:this.monthCurrent]);
		this.pMonth.appendChild(TextContent);
		
		//Ajout de l'ann�e 
		TextContent = document.createTextNode(this.yearCurrent);
		this.pYear.appendChild(TextContent);
	}
	
	calendar.prototype.updateMonthBefNexCur = function(direction){
			
			if(!this.inMove){
				if(this.timer == null){
					if(direction == "next"){
						this.updateDate("next");
						this.direction = "left";
						//on le remplit
						this.createMonth({"CurrentDay":this.dayCurrent,"CurrentMonth":this.monthCurrent,"CurrentYear":this.yearCurrent,"conteneur":this.to});
					}
					else if(direction == "before"){
						this.updateDate("before");
						this.direction = "right";
						this.createMonth({"CurrentDay":this.dayCurrent,"CurrentMonth":this.monthCurrent,"CurrentYear":this.yearCurrent,"conteneur":this.to});
						
					}
				}
				//On positionne la div
				this.Positionne();
			}
	}
	
	calendar.prototype.updateYearBefNexCur = function(direction){
			if(!this.inMove){
				if(this.timer == null){
					if(direction == "next"){
						this.yearCurrent++;
						this.direction = "top";
						//on le remplit
						this.createMonth({"CurrentDay":this.dayCurrent,"CurrentMonth":this.monthCurrent,"CurrentYear":this.yearCurrent,"conteneur":this.to});
					}
					else if(direction == "before"){
						this.yearCurrent--;
						this.direction = "bottom";
						this.createMonth({"CurrentDay":this.dayCurrent,"CurrentMonth":this.monthCurrent,"CurrentYear":this.yearCurrent,"conteneur":this.to});
						
					}
				}
				//Mise a jour de la date courante : 
				this.dateCurrent = new Date(this.yearCurrent, this.monthCurrent,this.dayCurrent);
				this.dateCurrent.setDate(this.dayCurrent);
				this.updateDateHeader();
				this.Positionne();
			}
	}
	
	calendar.prototype.updateDate = function(direction){
		if(this.timer == null){
			if(direction == "before"){
			//on calcul les dates suivante et pr�c�dente
				if(this.monthCurrent == 0){
					this.monthCurrent = 11;
				}
				else{
					this.monthCurrent = this.monthCurrent - 1 ;
				}
				this.yearCurrent = (this.monthCurrent == 11 ) ? this.yearCurrent - 1:this.yearCurrent;
			}
			else{
			//On r�cup�re le mois actuel puis on v�rifit que l'on est pas en janvier sinon on ajoute une ann�e
				if(this.monthCurrent == 11){
					this.monthCurrent = 0;
			
				}
				else{
					this.monthCurrent =this.monthCurrent + 1;
				}
				this.yearCurrent = (this.monthCurrent == 0) ?  this.yearCurrent+1:this.yearCurrent;
			}
			
			//Mise a jour de la date courante : 
			this.dateCurrent = new Date(this.yearCurrent, this.monthCurrent,this.dayCurrent);
			this.dateCurrent.setDate(this.dayCurrent);
			this.updateDateHeader();
		}
	}
	
	//Fonction permettant de trouver la position de l'�l�ment ( input ) pour pouvoir positioner le calendrier
	calendar.prototype.getPosition = function() {
	var tmpLeft = this.element.offsetLeft;
	var tmpTop = this.element.offsetTop;
	var MyParent = this.element.offsetParent;
	while(MyParent) {
		tmpLeft += MyParent.offsetLeft;
		tmpTop += MyParent.offsetTop;
		MyParent = MyParent.offsetParent;
	}
		this.calendar.style.left = tmpLeft + "px";
		this.calendar.style.top = tmpTop +  this.element.offsetHeight + 2 +"px";
	}
	
	calendar.prototype.fillField = function(){
//		this.element.value = this.dayCurrent+"/"+ ((this.monthCurrent+1 == 13) ? 1:this.monthCurrent+1)+"/"+this.yearCurrent;
		this.element.value = this.yearCurrent+"/"+ ((this.monthCurrent+1 == 13) ? 1:this.monthCurrent+1)+"/"+this.dayCurrent;
		//On d�truit le calendrier;
		while (this.calendar.childNodes.length>0) {
			this.calendar.removeChild(this.calendar.firstChild);
		}
		document.body.removeChild(this.calendar);
		this.element.calendarActive = false;
		calendarDestruct = false;
	}
	/*##########################################################
	############  METHODES PERMETTANT DE SCROLLER LES DATES  ##############
	##########################################################*/
	//Permet de r�cup�rer un �l�ment par id
	calendar.prototype.$ = function(element){
		return document.getElementById(element);
	};
	
	//M�thode permettant de lancer les animations si en auto :)
	calendar.prototype.go = function(){
		if(this.auto){
			switch (this.direction ){
				case 'left':
					this.SlideToLeft();
				break;
				case 'right':
					this.SlideToRight();
				break;
				case 'top':
					this.SlideToTop();
				break;
				case 'bottom':
					this.SlideToBottom();
				break;
			}
		}
	}
	
	//M�thode permettant d'ajouter un �l�ment
	calendar.prototype.AddElement = function(element){
		if(typeof(element) == "string"){
			this.elementToSlide.push(this.$(element));
		}
		else if(typeof(element) == "object"){
			this.elementToSlide.push(element);
		}
	}
	
	//M�thode permettant d'ajouter plusieurs �l�ment d'un coup
	calendar.prototype.AddElements = function (elements){
		for(var i = 0 , l = elements.length; i < l ;i++){
			this.AddElement(elements[i]);
		}
	}
	
	//M�thode permettant de d�placer les �l�ments vers la gauche
	calendar.prototype.SlideToLeft = function(){
		if((this.direction == null || this.direction == 'left') && this.opacite >= 100){
			var me = this ;
			//On v�rifit la direction pour initialiser le positionnement
			if(this.direction != 'left'){
					this.direction = 'left';
					if(this.timer == null){
						this.Positionne();
					}
			}
			else if(this.direction == 'left' && this.auto && this.timer == null){
				this.Positionne();
			}
			
			if(this.timer != null){
				clearTimeout(this.timer);
				this.timer = null;
			}
			//Si le timer n'est pas finit on d�truit l'ancienne div
			if(parseInt(this.from.style.left) == Number.NaN || (parseInt(this.from.parentNode.offsetWidth) + parseInt(this.from.style.left))> 0){
				this.from.style.left = parseInt(this.from.style.left) - 15 + "px";
				this.to.style.left  =parseInt(this.to.style.left) - 15 + "px";
				this.inMove = true;
				this.timer = setTimeout(function(){me.SlideToLeft()},25);
				
			}
			else{
				clearTimeout(this.timer);
				this.timer = null;
				this.currentIndex = (this.currentIndex == (this.elementToSlide.length-1)) ? 0:this.currentIndex + 1;
				this.Positionne();
				this.direction = null;
				this.inMove = false;
			}
		}
	};
	
	//M�thode permettant de d�placer les �l�ments vers la droite
	calendar.prototype.SlideToRight = function(){
		var me = this ;
		if((this.direction == null || this.direction == 'right') && this.opacite >= 100){
				if(this.direction != 'right'){
					this.direction = 'right';
					if(this.timer == null){
						this.Positionne();
					}
				}
				else if(this.direction == 'right' && this.auto && this.timer == null){
					this.Positionne();
				}
				
				if(this.timer != null){
					clearTimeout(this.timer);
					this.timer = null;
				}
				//Si le timer n'est pas finit on d�truit l'ancienne div
				if(parseInt(this.from.style.left) == Number.NaN ||  parseInt(this.from.style.left) < parseInt(this.from.parentNode.offsetWidth)){
					this.from.style.left = parseInt(this.from.style.left) + 15 + "px";
					this.to.style.left  =parseInt(this.to.style.left) + 15 + "px";
					this.inMove = true;
					this.timer = setTimeout(function(){me.SlideToRight()},25);
				}
				else{
					clearTimeout(this.timer);
					this.timer = null;
					this.currentIndex = (this.currentIndex == 0) ? this.elementToSlide.length-1:this.currentIndex - 1;
					this.Positionne();
					this.direction = null;
					this.inMove = false;
				}
		}
		

	};
	
	//M�thode permettant de d�placer les �l�ments vers la gauche
	calendar.prototype.SlideToTop = function(){
		var me = this ;
		if((this.direction == null || this.direction == 'top') && this.opacite >= 100){
			//On v�rifit la direction pour initialiser le positionnement
			if(this.direction != 'top'){
					this.direction = 'top';
					if(this.timer == null){
						this.Positionne();
					}
			}
			if(this.timer != null){
				clearTimeout(this.timer);
				this.timer = null;
			}
			//Si le timer n'est pas finit on d�truit l'ancienne div
			if(parseInt(this.from.style.top) == Number.NaN || (parseInt(this.from.style.top) > - parseInt(this.from.parentNode.offsetHeight))){
				this.from.style.top = parseInt(this.from.style.top) - 15 + "px";
				this.to.style.top  =parseInt(this.to.style.top) - 15 + "px";
				this.inMove = true;
				this.timer = setTimeout(function(){me.SlideToTop()},25);
			}
			else{
				clearTimeout(this.timer);
				this.timer = null;
				this.currentIndex = (this.currentIndex == 0) ? this.elementToSlide.length-1:this.currentIndex - 1;
				this.Positionne();					
				this.direction = null;
				this.inMove = false;
			}
		}
	};
	
	//M�thode permettant de d�placer les �l�ments vers le bas
	calendar.prototype.SlideToBottom = function(){
		var me = this 
		if((this.direction == null || this.direction == 'bottom') && this.opacite >= 100){
			//On v�rifit la direction pour initialiser le positionnement
			if(this.direction != 'bottom'){
					this.direction = 'bottom';
					if(this.timer == null){
						this.Positionne();
					}
			}
			if(this.timer != null){
				clearTimeout(this.timer);
				this.timer = null;
			}
			//Si le timer n'est pas finit on d�truit l'ancienne div
			if(parseInt(this.from.style.top) == Number.NaN || parseInt(this.from.style.top) < parseInt(this.from.parentNode.offsetHeight)){
				this.from.style.top = parseInt(this.from.style.top) + 15 + "px";
				this.to.style.top  =parseInt(this.to.style.top) + 15 + "px";
				this.inMove = true;
				this.timer = setTimeout(function(){me.SlideToBottom()},25);
			}
			else{
				clearTimeout(this.timer);
				this.timer = null;
				this.currentIndex = (this.currentIndex == this.elementToSlide.length-1) ? 0:this.currentIndex + 1;
				this.Positionne();
				this.direction = null;
				this.inMove = false;
			}
		}
	};
	
	//Fonction initialisant le tableau en positionnant tous les �l�ments :)
	calendar.prototype.Positionne = function(){
		if(this.direction == 'left'){
			//On v�rifit que l'on est pas a la fin sinon le premier devient le dernier
			if(this.currentIndex == this.elementToSlide.length-1){
				//r�cup�ration des �l�ments : 
				this.from = this.elementToSlide[this.currentIndex];
				this.to = this.elementToSlide[0]; //Premier �l�ment
			}
			else{
				this.from = this.elementToSlide[this.currentIndex];
				this.to = this.elementToSlide[this.currentIndex + 1];
			}
				this.from.style.display = "block" ;
				this.from.style.left = 0 + "px";
				this.to.style.left = this.from.parentNode.offsetWidth + "px";
				this.to.style.display = "block";
				//Posionement vertical
				this.to.style.top = 0 + "px";
				this.from.style.top = 0 + "px" ;
		}
		else if(this.direction == 'right'){
			if(this.currentIndex == 0){
				this.from = this.elementToSlide[this.currentIndex];
				this.to = this.elementToSlide[this.elementToSlide.length-1]; // dernier �l�ment
			}
			else{
				this.from = this.elementToSlide[this.currentIndex];
				this.to = this.elementToSlide[this.currentIndex-1];
			}
			this.from.style.display = "block" ;
			this.from.style.left = 0 + "px";
			this.to.style.left = - (this.from.parentNode.offsetWidth )+ "px";
			this.to.style.display = "block";
			//Posionement vertical
			this.to.style.top = 0 + "px";
			this.from.style.top = 0 + "px" ;
		}
		else if(this.direction == 'bottom'){
			if(this.currentIndex == this.elementToSlide.length-1){
				this.from = this.elementToSlide[this.currentIndex];
				this.to = this.elementToSlide[0]; // dernier �l�ment
			}
			else{
				this.from = this.elementToSlide[this.currentIndex];
				this.to = this.elementToSlide[this.currentIndex+1];
			}
			this.from.style.display = "block" ;
			this.from.style.top = 0 + "px";
			this.to.style.top = - (this.from.parentNode.offsetHeight )+ "px";
			this.to.style.display = "block";
			//Posionement horizontal
			this.to.style.left = 0 + "px";
			this.from.style.left = 0 + "px" ;
		}
		else if(this.direction == 'top'){
			if(this.currentIndex == 0){
				this.from = this.elementToSlide[this.currentIndex];
				this.to = this.elementToSlide[this.elementToSlide.length-1]; // dernier �l�ment
			}
			else{
				this.from = this.elementToSlide[this.currentIndex];
				this.to = this.elementToSlide[this.currentIndex-1];
			}
			this.from.style.display = "block" ;
			this.from.style.top = 0 + "px";
			this.to.style.top = (this.from.parentNode.offsetHeight )+ "px";
			this.to.style.display = "block";
			//Posionement horizontal
			this.to.style.left = 0 + "px";
			this.from.style.left = 0 + "px" ;
		}
	};

	calendar.prototype.fadePic = function (current,up){
		this.calendar.style.display = "block";
		this.opacite = current ;
		this.up = up ;
		
		if (this.opacite< 100 && this.up){
			this.opacite+=3;
			this.IsIE?this.calendar.filters[0].opacity=this.opacite:this.calendar.style.opacity=this.opacite/100;
			var me = this;
			this.timer = setTimeout(function(){me.fadePic(me.opacite,true)},25);
		}
		else{
			clearTimeout(this.timer);
			this.timer = null;
			this.up = false;
			calendarDestruct = true;
		}
	}
	</script>
	   	
 

<?php
define('PUN_ROOT', './');
require PUN_ROOT.'config2.php';
require PUN_ROOT.'include/fonctions.php';
include "../libchart/libchart/classes/libchart.php";
include("../lib/pChart/pData.class");
 include("../lib/pChart/pChart.class");
$start='1981/06/14';
$end=date("Y/m/d");

if (isset($_POST['start']))
{
	if ($_POST['start'] != '')
	{
		$start=$_POST['start'];
	}
	else{
		$start='1981/06/14';
		}
}

if (isset($_POST['end']))
{
	if ($_POST['end'] != '')
	{
		$end=$_POST['end'];
	}
	else{
		$end=date("Y/m/d");
		}
}


echo "<H1>Statistique sur la p�riode du $start au  $end </H1>" ;

// Width of the chart
$width = 200;
$chart_cal_h_sp = new HorizontalBarChart(500, 300);
$dataSet_cal_h_sp = new XYDataSet();




/*
echo "db_host: $db_host";
echo "db_username: $db_username";
echo "db_name: $db_name";
echo 'Version PHP courante : ' . phpversion();
*/
   /* Connecting, selecting database */
 
$link=connect_db($db_host, $db_username, $db_password, $db_name);
	
	
	$query = "select sport_name, SEC_TO_TIME(sum(TIME_TO_SEC(duration))) as \"temps pass�\" , 
sum(calories) as \"Calories d�pens�es\" ,
sum(distance) /1000 as \"distance(km)\" , 
format(3600*sum(calories)/sum(TIME_TO_SEC(duration)) , 2) as \"Calorie/heure\"
FROM seances, sport_type
WHERE seances.sport_id=sport_type.sport_id
AND date <= '$end'
AND date >= '$start'
GROUP BY sport_name
;";
$result = mysql_query($query) or die("La requ�te a echou�e");


	
$query2 = "select  date_format(date,'%d/%m/%y' ),
sum(TIME_TO_SEC(below)) as \"Temps en dessous\", 
sum(TIME_TO_SEC(in_zone)) as \"Temps dans la zone\", 
sum(TIME_TO_SEC(above)) as \"Temps au dessus\"
FROM seances, sport_type
WHERE seances.sport_id=sport_type.sport_id
AND date <= '$end'
AND date >= '$start'
GROUP BY date;
;";
     $result2 = mysql_query($query2) or die("La requ�te a echou�e");

$query3 = "SELECT  SEC_TO_TIME(sum(TIME_TO_SEC(duration))) as \"temps pass�\" ,
 sum(calories) as \"Calories d�pens�es\" , 
sum(distance) /1000 as \"distance(km)\", 
format(3600*sum(calories)/sum(TIME_TO_SEC(duration)),2) as \"Calorie/heure\" ,
 min(date) as \"Start\", max(date) as \"End\" , count(distinct(date)) as \"nb days\" ,
format(sum(calories)/count(distinct(date)),2) as \"Cal/day\",
format(sum(distance) /1000/count(distinct(date)),2) as \"km/day\", 
SEC_TO_TIME(sum(TIME_TO_SEC(duration))/count(distinct(date)))as \"duration/day\",
datediff(max(date), min(date)) as \"nb days eff\" ,
format(sum(calories)/datediff(max(date), min(date)),2) as \"Cal/dayeff\",
format(sum(distance) /1000/datediff(max(date), min(date)),2) as \"km/dayeff\", 
SEC_TO_TIME(sum(TIME_TO_SEC(duration))/datediff(max(date), min(date)))as \"duration/dayeff\"
from seances
WHERE date <= '$end'
AND date >= '$start'
;";
 $result3 = mysql_query($query3) or die("La requ�te a echou�e");    

$query4="select  
SEC_TO_TIME(sum(TIME_TO_SEC(below))) as \"Temps en dessous\", 
SEC_TO_TIME(sum(TIME_TO_SEC(in_zone))) as \"Temps dans la zone\", 
SEC_TO_TIME(sum(TIME_TO_SEC(above))) as \"Temps au dessus\"
FROM seances
WHERE date <= '$end'
AND date >= '$start';";
$result4 = mysql_query($query4) or die("La requ�te 4 a echou�e");


$query5="select sport_name,
sum(TIME_TO_SEC(below)) as \"Temps en dessous\", 
sum(TIME_TO_SEC(in_zone)) as \"Temps dans la zone\", 
sum(TIME_TO_SEC(above)) as \"Temps au dessus\"
FROM seances, sport_type
WHERE seances.sport_id=sport_type.sport_id
AND date <= '$end'
AND date >= '$start'
GROUP BY sport_name 
order by \"Temps en dessous\",  \"Temps dans la zone\", \"Temps au dessus\" desc;";
$result5 = mysql_query($query5) or die("La requ�te 5 a echou�e");

$query6="
SELECT A.date, format(sum(A.product)/B.total_duration,0) as barycenter, B.max
from
(SELECT date, average*TIME_TO_SEC(duration) as product
from seances) A, 
(SELECT date, sum(TIME_TO_SEC(duration)) as total_duration, max(maximum) as max
from seances group by date) B
WHERE 
A.date = B.date
AND A.date <= '$end'
AND A.date >= '$start'
group by date 
order by date
;";
$result6 = mysql_query($query6) or die("La requ�te 6 a echou�e");

$query70="
select distinct(date)  
FROM seances
WHERE date <= '$end'
AND date >= '$start'
ORDER BY  date;
";
$result70 = mysql_query($query70) or die("La requ�te 70 a echou�e");

$query71="select distinct(seances.sport_id), sport_name  FROM seances,  sport_type WHERE seances.sport_id=sport_type.sport_id
and Distance != 0 
AND date <= '$end'
AND date >= '$start';";
$result71 = mysql_query($query71) or die("La requ�te 71 a echou�e");




//=====================Begin result 4==================================================

$row4 = mysql_fetch_array($result4, MYSQL_NUM);
         	$labelsz=$labelsz."Time below";
         	$datasz=$datasz.$row4[0];
		$labelsz=$labelsz."*";
         	$datasz=$datasz."*";
		$labelsz=$labelsz."Time in zone";
         	$datasz=$datasz.$row4[1];
		$labelsz=$labelsz."*";
		$datasz=$datasz."*";
		$labelsz=$labelsz."Time above";
         	$datasz=$datasz.$row4[2];

//=====================End result 4==================================================


//=====================Begin result 2==================================================
$DataSet = new pData;

while ($row2 = mysql_fetch_array($result2, MYSQL_NUM))
         {
$DataSet->AddPoint($row2[0],"abscisse");
$DataSet->AddPoint($row2[1],"below");
 $DataSet->AddPoint($row2[2],"in_zone");
 $DataSet->AddPoint($row2[3],"above");
}

$DataSet->AddSerie("below");
$DataSet->AddSerie("in_zone");
$DataSet->AddSerie("above");

 $DataSet->SetAbsciseLabelSerie("abscisse");
 $DataSet->SetSerieName("Temps en dessous","below");
 $DataSet->SetSerieName("Temps dans la zone","in_zone");
 $DataSet->SetSerieName("Temps au dessus","above");
$DataSet->SetYAxisFormat("time"); 

 // Initialise the graph
 $Test = new pChart(1000,450);
 $Test->setFontProperties("../lib/Fonts/tahoma.ttf",8);
 $Test->setGraphArea(80,10,1000,340);
 //$Test->drawRoundedRectangle(0,0,1000,450,5,255,255,255);
 //$Test->drawRoundedRectangle(0,0,1000,350,5,260,260,260);
 $Test->drawGraphArea(255,255,255,TRUE);
 $Test->drawScale($DataSet->GetData(),$DataSet->GetDataDescription(),SCALE_ADDALL,150,150,150,TRUE,90,2,TRUE);
 $Test->drawGrid(10,TRUE,230,230,230,50);

 // Draw the 0 line
 $Test->setFontProperties("../lib/Fonts/tahoma.ttf",6);
 $Test->drawTreshold(0,143,55,72,TRUE,TRUE);

 // Draw the bar graph
 $Test->drawStackedBarGraph($DataSet->GetData(),$DataSet->GetDataDescription(),100);

 // Finish the graph
 $Test->setFontProperties("../lib/Fonts/tahoma.ttf",8);
 $Test->drawLegend(0,0,$DataSet->GetDataDescription(),255,255,255);
 $Test->setFontProperties("../lib/Fonts/tahoma.ttf",10);
 $Test->drawTitle(50,22,"Evolution dans le temps de la r�partition du temps dans chaque zone",50,50,50,585);


 $Test->Render("generated/example20.png");
//===========End Result 2==========================
//========================Begin result 5===============================================
$DataSet5 = new pData;
while ($row5 = mysql_fetch_array($result5, MYSQL_NUM))
         {
$DataSet5->AddPoint($row5[0],"sport");
$DataSet5->AddPoint($row5[1],"below");
 $DataSet5->AddPoint($row5[2],"in_zone");
 $DataSet5->AddPoint($row5[3],"above");
//echo " $row5[0]\t$row5[1]\t$row5[2]\t$row5[3]\n";
}


$DataSet5->AddSerie("below");
$DataSet5->AddSerie("in_zone");
$DataSet5->AddSerie("above");

 $DataSet5->SetAbsciseLabelSerie("sport");
 $DataSet5->SetSerieName("Temps en dessous","below");
 $DataSet5->SetSerieName("Temps dans la zone","in_zone");
 $DataSet5->SetSerieName("Temps au dessus","above");
$DataSet5->SetYAxisFormat("time"); 

 // Initialise the graph
 $Test5 = new pChart(700,330);

 $Test5->setFontProperties("../lib/Fonts/tahoma.ttf",8);
 $Test5->setGraphArea(60,10,680,270);
 $Test5->drawRoundedRectangle(7,7,693,293,5,240,240,240);
 $Test5->drawRoundedRectangle(5,5,695,295,5,230,230,230);
 $Test5->drawGraphArea(255,255,255,TRUE);
 $Test5->drawScale($DataSet5->GetData(),$DataSet5->GetDataDescription(),SCALE_ADDALL,150,150,150,TRUE,0,2,TRUE);
 $Test5->drawGrid(10,TRUE,230,230,230,50);

 // Draw the 0 line
 $Test5->setFontProperties("../lib/Fonts/tahoma.ttf",6);
 $Test5->drawTreshold(0,143,55,72,TRUE,TRUE);

 // Draw the bar graph
 $Test5->drawStackedBarGraph($DataSet5->GetData(),$DataSet5->GetDataDescription(),TRUE);

 // Finish the graph
 $Test5->setFontProperties("../lib/Fonts/tahoma.ttf",8);
 $Test5->drawLegend(0,0,$DataSet5->GetDataDescription(),255,255,255);
 $Test5->setFontProperties("../lib/Fonts/tahoma.ttf",10);
 $Test5->drawTitle(50,22,"R�partition du temps dans chaque zone par sport",50,50,50,585);


 $Test5->Render("generated/sportzone_sport.png");
//=============End result 5========================

//====================Begin result 6=========================
$DataSet6 = new pData;
while ($row6 = mysql_fetch_array($result6, MYSQL_NUM))
         {
$DataSet6->AddPoint($row6[0],"date");
$DataSet6->AddPoint($row6[1],"Fmoy");
$DataSet6->AddPoint($row6[2],"Fmax");
//echo " $row6[0]\t$row6[1]\t$row6[2]\n";
}
$DataSet6->AddSerie("Fmoy");
$DataSet6->AddSerie("Fmax");
$DataSet6->SetAbsciseLabelSerie("date");
 $DataSet6->SetSerieName("FC Moy","Fmoy");
 $DataSet6->SetSerieName("FC Max","Fmax");

 // Initialise the graph
$Test6 = new pChart(1000,330);
 $Test6->setFontProperties("../lib/Fonts/tahoma.ttf",8);
 $Test6->setGraphArea(60,10,980,260);
 //$Test6->drawRoundedRectangle(7,7,993,293,5,240,240,240);
 //$Test6->drawRoundedRectangle(5,5,995,295,5,230,230,230);
 $Test6->drawGraphArea(255,255,255,TRUE);
 $Test6->drawScale($DataSet6->GetData(),$DataSet6->GetDataDescription(),SCALE_NORMAL,150,150,150,TRUE,90,2,TRUE);
 $Test6->drawGrid(10,TRUE,230,230,230,50);

 // Draw the 0 line
 $Test6->setFontProperties("../lib/Fonts/tahoma.ttf",6);
 $Test6->drawTreshold(0,143,55,72,TRUE,TRUE);
// Draw the line graph  
 $Test6->drawLineGraph($DataSet6->GetData(),$DataSet6->GetDataDescription());     
 $Test6->drawPlotGraph($DataSet6->GetData(),$DataSet6->GetDataDescription(),3,2,255,255,255);     
   
 // Finish the graph     
 $Test6->setFontProperties("../lib/Fonts/tahoma.ttf",8);     
 $Test6->drawLegend(0,0,$DataSet6->GetDataDescription(),255,255,255);     
 $Test6->setFontProperties("../lib/Fonts/tahoma.ttf",10);     
 $Test6->drawTitle(60,22,"Evolution des Fr�quence cardiaque",50,50,50,585);     
 $Test6->Render("generated/FC.png");        
     
//============================End result 6=========================================
//====================Begin result 7=========================
$DataSet7 = new pData;
$DataSet8 = new pData;
while ($row70 = mysql_fetch_array($result70, MYSQL_NUM))
         {
	$DataSet7->AddPoint($row70[0],"date");
	$DataSet8->AddPoint($row70[0],"date");
/*	echo " <br>$row7[0]\t $row7[1]\t$row7[2]\t$row6[3]\n";
	$DataSet7->AddPoint($row7[2],$row71[0]." Vmoy");
	$DataSet7->AddPoint($row7[3],$row71[0]." Vmax");
*/
	$result71 = mysql_query($query71) or die("La requ�te 71 a echou�e");
	while ($row71 = mysql_fetch_array($result71, MYSQL_NUM))
	        {
	//		echo "<br> $row7[0]\t $row7[1] != $row71[0] \t 0 \t 0 \n";
		$query7="select  date,sport_name,format(avg(Vaverage),2) as \"Vitesse moyenne\",
 			format(max(Vmaximum),2) as \"Vitesse max\", 
			format(sum(distance )/1000, 2) as \"Distance\" 
			FROM seances, sport_type
			WHERE seances.sport_id=sport_type.sport_id
			and Distance != 0
			and date = '$row70[0]'
			and sport_type.sport_id ='$row71[0]' 
			GROUP BY  date, sport_name;";
			$result7 = mysql_query($query7) or die("La requ�te $query7 a echou�e");
			if (mysql_num_rows($result7)== 1 )
			{
				while ($row7 = mysql_fetch_array($result7, MYSQL_NUM))
				{
				$DataSet7->AddPoint($row7[2],$row7[1]." Vmoy");
				$DataSet7->AddPoint($row7[3],$row7[1]." Vmax");
				$DataSet8->AddPoint($row7[4],$row7[1]." Distance(km)");
			//	echo "<br> $row70[0]\t $row7[1]  \t $row7[2] \t $row7[3] \n";
				}
			}else{
			$DataSet7->AddPoint( 0 , $row71[1]." Vmoy");
			$DataSet7->AddPoint(0 , $row71[1]." Vmax");
			$DataSet8->AddPoint( 0 ,$row71[1]." Distance(km)");
			//echo "<br> $row70[0]\t $row71[1]  \t 0 \t 0 \n";
			}
		}
	}
$result71 = mysql_query($query71) or die("La requ�te 71 a echou�e");
while ($row71 = mysql_fetch_array($result71, MYSQL_NUM))
         {
$DataSet7->AddSerie($row71[1]." Vmoy" );
$DataSet7->AddSerie($row71[1]." Vmax" );
$DataSet8->AddSerie($row71[1]." Distance(km)" );
// $DataSet7->SetSerieName("","Fmax");
}

$DataSet7->SetAbsciseLabelSerie("date");

 // Initialise the graph
$Test7 = new pChart(1000,330);
 $Test7->setFontProperties("../lib/Fonts/tahoma.ttf",8);
 $Test7->setGraphArea(60,10,980,260);
 //$Test7->drawRoundedRectangle(7,7,993,293,5,240,240,240);
 //$Test7->drawRoundedRectangle(5,5,995,295,5,230,230,230);
 $Test7->drawGraphArea(255,255,255,TRUE);
 $Test7->drawScale($DataSet7->GetData(),$DataSet7->GetDataDescription(),SCALE_NORMAL,150,150,150,TRUE,90,2,TRUE);
 $Test7->drawGrid(10,TRUE,230,230,230,50);

 // Draw the 0 line
 $Test7->setFontProperties("../lib/Fonts/tahoma.ttf",6);
 $Test7->drawTreshold(0,143,55,72,TRUE,TRUE);
// Draw the line graph  
 $Test7->drawLineGraph($DataSet7->GetData(),$DataSet7->GetDataDescription());     
 $Test7->drawPlotGraph($DataSet7->GetData(),$DataSet7->GetDataDescription(),3,2,255,255,255);     
   
 // Finish the graph     
 $Test7->setFontProperties("../lib/Fonts/tahoma.ttf",8);     
 $Test7->drawLegend(0,0,$DataSet7->GetDataDescription(),255,255,255);     
 $Test7->setFontProperties("../lib/Fonts/tahoma.ttf",10);     
 $Test7->drawTitle(60,10,"Vitesse",50,50,50,585);     
 $Test7->Render("generated/vitesse.png");        
  

//==== Begin graph 8 ======
$DataSet8->SetAbsciseLabelSerie("date");

 // Initialise the graph
$Test8 = new pChart(1000,330);
 $Test8->setFontProperties("../lib/Fonts/tahoma.ttf",8);
 $Test8->setGraphArea(60,10,980,260);
 //$Test7->drawRoundedRectangle(7,7,993,293,5,240,240,240);
 //$Test7->drawRoundedRectangle(5,5,995,295,5,230,230,230);
 $Test8->drawGraphArea(255,255,255,TRUE);
 $Test8->drawScale($DataSet8->GetData(),$DataSet8->GetDataDescription(),SCALE_NORMAL,150,150,150,TRUE,90,2,TRUE);
 $Test8->drawGrid(10,TRUE,230,230,230,50);

 // Draw the 0 line
 $Test8->setFontProperties("../lib/Fonts/tahoma.ttf",6);
 $Test8->drawTreshold(0,143,55,72,TRUE,TRUE);
// Draw the line graph  
 $Test8->drawLineGraph($DataSet8->GetData(),$DataSet8->GetDataDescription());     
 $Test8->drawPlotGraph($DataSet8->GetData(),$DataSet8->GetDataDescription(),3,2,255,255,255);     
   
 // Finish the graph     
 $Test8->setFontProperties("../lib/Fonts/tahoma.ttf",8);     
 $Test8->drawLegend(0,0,$DataSet8->GetDataDescription(),255,255,255);     
 $Test8->setFontProperties("../lib/Fonts/tahoma.ttf",10);     
 $Test8->drawTitle(60,10,"Distance (km)",50,50,50,585);     
 $Test8->Render("generated/distance.png");        
//====  End graph 8 ==== 
//============================End result 7=========================================

while ($row3 = mysql_fetch_array($result3, MYSQL_NUM))
         {
print"<form action=\"Sport_test2.php\" method=\"post\">";
printf("<table border=2>\n
<TR>\n
<TD> First day </TD>\n<TD><Input name=\"start\" type=\"date\"  onclick=\"new calendar(this);\" value=\"%s\" size=\"8\"/> </TD>\n<TD> Last day </TD>\n<TD> <Input name=\"end\" type=\"date\"  onclick=\"new calendar(this);\" value=\"%s\" size=\"8\"/></TD>\n <TD><INPUT TYPE=\"SUBMIT\" VALUE=\"Report\"/></TD>\n
</TR>\n
</table></form>\n",$row3[4] ,$row3[5]);

 /* Printing results in HTML */
	
$i=0;

print"<table border=2>\n";
print"<TR>
<TD>Sport</TD>
<TD>temps pass�</TD>
<TD>Calories d�pens�es</TD>
<TD>distance(km)</TD>
<TD>Calorie/heure</TD>
</TR>";
      while ($row = mysql_fetch_array($result, MYSQL_NUM))
         {
		
	  printf("<TR> 
<TD> %s </TD> 
<TD>%s </TD> 
<TD>%s </TD> 
<TD>%s </TD> 
<TD>%s </TD>
</TR>",  $row[0] , $row[1] ,  $row[2], $row[3], $row[4] );
 
if ($i>0)
         	{
         	$labelt=$labelt."*";
         	$datat=$datat."*";
		$labelc=$labelc."*";
         	$datac=$datac."*";
		$labeld=$labeld."*";
         	$datad=$datad."*";
         	}
         //	printf("<TR><TD>%s</TD><TD>%s</TD></TR>", $row[0],$row[1] );
         	$labelt=$labelt.$row[0];
         	$datat=$datat.$row[1];
		$labelc=$labelc.$row[0];
         	$datac=$datac.$row[2];
		$labeld=$labeld.$row[0];
         	$datad=$datad.$row[3];

		$dataSet_cal_h_sp->addPoint(new Point($row[0], $row[4]));

         	$i++;	  



	   $nb=$nb+1;  
	 }

printf("<TR> 
<TD>TOTAL </TD> 
<TD>%s </TD> 
<TD>%s </TD> 
<TD>%s </TD> 
<TD>%s </TD>
</TR></table>\n",  $row3[0] , $row3[1] ,  $row3[2], $row3[3]);
 



printf("
<table border=2>\n
<TR>
<TD> Nb days </TD><TD> %s </TD><TD> Nb days of activity </TD><TD> %s </TD>
</TR>
<TR>
<TD> Cal/day </TD><TD> %s </TD><TD> Cal/day of activity </TD><TD> %s </TD>
</TR>
<TR>
<TD> km/day </TD><TD> %s </TD><TD> km/day of activity </TD><TD> %s </TD>
</TR>
<TR>
<TD> Duration/day </TD><TD> %s </TD><TD> Duration/day of activity </TD><TD> %s </TD>
</TR></table>\n
", $row3[10], $row3[6],  $row3[11] ,  $row3[7],$row3[12], $row3[8] , $row3[13], $row3[9]);

}



 

	 
$chart_cal_h_sp->setDataSet($dataSet_cal_h_sp);
	$chart_cal_h_sp->setTitle("Calories/h");
	$chart_cal_h_sp->render("generated/cal_h_sp.png");
 /* Free resultset */
 mysql_free_result($result);
 mysql_free_result($result2);
 mysql_free_result($result3);
 mysql_free_result($result4);
 mysql_free_result($result5);
 mysql_free_result($result6);
 mysql_free_result($result7);
 mysql_free_result($result71);
 /* Closing connection */
  mysql_close($link);

	


echo"
<table>
<tr><td> Sport par temps pass� </td><td> Sport par calories d�pens�es</td></tr>
<tr>
<td>
";
printf("<img src=\"include/chart.php?label=%s&data=%s\"></img>\n",$labelt, $datat);
echo" </td>
<td>";
printf("<img src=\"include/chart.php?label=%s&data=%s\"></img>\n",$labelc, $datac);
echo "</td>
</tr>
<tr> <td> Sport par distance parcourue </td><td> Calories /h par sport </td>
</tr>
<tr>
<td>";

printf("<img src=\"include/chart.php?label=%s&data=%s\"></img>\n",$labeld, $datad);
echo "</td>
<td>";
printf("<img src=\"generated/cal_h_sp.png\"></img>");
echo "
</tr>
</table>
<table>
<tr><td> Repartition in sport zone </td></tr>
<tr>
<td>
";
printf("<img src=\"include/chart.php?label=%s&data=%s\"></img>\n",$labelsz, $datasz);
echo" </td>
<td>";
printf("<img src=\"generated/sportzone_sport.png\"></img>");
echo "</td>
</tr>
<tr>
<td>
</td>
</tr>
</table>";
echo"
<table>
<tr>
<td>";
printf("<img src=\"generated/example20.png\"></img>");
echo "
</td>
</tr><tr><td>";
printf("<img src=\"generated/FC.png\"></img>");
echo"
</td></tr>
<tr>
<td>";
printf("<img src=\"generated/vitesse.png\"></img>");
echo"
</td></tr>
<tr>
<td>";
printf("<img src=\"generated/distance.png\"></img>");

echo"</td></tr></table>";

?>

	   </BODY>
 </HTML>
