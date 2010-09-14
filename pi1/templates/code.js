
function showPersonSearch(kindOfPerson) {
	var iframe = document.getElementById(kindOfPerson+'Search');
	iframe.style.height = "300px";
	iframe.style.width = "400px";
	iframe.style.visibility='visible';
}

function hidePersonSearch(kindOfPerson) {
	var iframe = document.getElementById(kindOfPerson+'Search');
	iframe.style.height = "0px";
	iframe.style.width = "0px";
	iframe.style.visibility='hidden';
}

function searchPerson(kindOfPerson) {
	var usedUids = document.getElementById(kindOfPerson+'Uids');
	var uids = parent.document.getElementById(kindOfPerson+'Ids');
	usedUids.value = uids.value;
	document.getElementById(kindOfPerson+'SearchForm').submit();
}

function addPerson(uid,name,firstname,kindOfPerson,editUrl) {
	var ids = document.getElementById(kindOfPerson+'Ids');
	var tmp = ids.value.split(",");
	tmp.push(uid);
	ids.value = tmp.join(",");
	var box = document.getElementById(kindOfPerson+'Container');
	var tmpl = document.getElementById(kindOfPerson+'Template').innerHTML;
	tmpl = tmpl.replace('###UID###',uid);
	tmpl = tmpl.replace('###UID###',uid);
	tmpl = tmpl.replace('###UID###',uid);
	tmpl = tmpl.replace('###NAME###',name);
	tmpl = tmpl.replace('###FIRSTNAME###',firstname);
	tmpl = tmpl.replace('###NAME###',name);
	tmpl = tmpl.replace('###FIRSTNAME###',firstname);
	tmpl = tmpl.replace('###editUrl###',editUrl);
	box.innerHTML += tmpl;
	parent.hidePersonSearch(kindOfPerson);
}

function removePerson(uid,kindOfPerson,name) {
		// only ask for confirmation if author is supposed to be removed
	if ((kindOfPerson == 'publisher') || (confirm('Sind Sie sicher, dass Sie '+name+' entfernen möchten?'))) {
		var ids = document.getElementById(kindOfPerson+'Ids');
		var tmp = ids.value.split(",");
		var tmp2 = new Array();
		// loop over authorids and remove selected
		for (i = 0; i < tmp.length; i++) {
			if (tmp[i] != uid) {
				tmp2.push(tmp[i]);
			}
		}
		// add uids to input
		ids.value = tmp2.join(",");
		document.getElementById(kindOfPerson+'_'+uid).innerHTML = "";
		var box = document.getElementById(kindOfPerson+'Container');
		// if apperance of author-list changes, make changes accordingly
		box.innerHTML = box.innerHTML.replace('<p id="'+kindOfPerson+'_'+uid+'"></p>','');
	}
}

function removeImage(image) {
	var images = document.getElementById('images');
	var tmp = images.value.split(",");
	var tmp2 = new Array();
	// loop over authorids and remove selected
	for (i = 0; i < tmp.length; i++) {
		if (tmp[i] != image) {
			tmp2.push(tmp[i]);
		}
	}
	// add uids to input
	images.value = tmp2.join(",");
	document.getElementById(image).innerHTML = "";
}

function removeQualification(uid) {
	if (confirm('Wenn Sie die Abschlussarbeit löschen erscheint Sie bei keiner Person mehr.')) {
		var f = document.getElementById('removequalificationgeneralForm');
		f.action = window.location;
		var ih = document.getElementById('removeUid');
		ih.value=uid;
		f.submit();
	}
}

function editStudent(uid,editUrl) {
	var iframe = document.getElementById('studentEdit');
	iframe.style.height = "300px";
	iframe.style.width = "400px";
	//iframe.src = 'http://histsem.unibas.ch/nc/seminar/personen/person-details/abschlussarbeiten-verwalten/abschlussarbeiten-bearbeiten/?tx_x4equalification_pi2%5Baction%5D=editStudent&tx_x4equalification_pi2%5BeditUid%5D='+uid+'&type=7645';
	iframe.src = editUrl;
	iframe.style.visibility='visible';
}

function updateStudent(uid,editUrl) {
	var stud = parent.document.getElementById('student_'+uid);

	if ((stud != null) && (stud.innerHTML != '')) {
		var tmpl = parent.document.getElementById('studentTemplate').innerHTML;
		tmpl = tmpl.replace('###UID###',uid);
		tmpl = tmpl.replace('###UID###',uid);
		tmpl = tmpl.replace('###UID###',uid);
		tmpl = tmpl.replace('###NAME###',document.forms['studentEditSearchForm'].elements['tx_x4equalificationgeneral_pi2[stud_lastname]'].value);
		tmpl = tmpl.replace('###FIRSTNAME###',document.forms['studentEditSearchForm'].elements['tx_x4equalificationgeneral_pi2[stud_firstname]'].value);
		tmpl = tmpl.replace('###NAME###',document.forms['studentEditSearchForm'].elements['tx_x4equalificationgeneral_pi2[stud_lastname]'].value);
		tmpl = tmpl.replace('###FIRSTNAME###',document.forms['studentEditSearchForm'].elements['tx_x4equalificationgeneral_pi2[stud_firstname]'].value);
		tmpl = tmpl.replace('###editUrl###',editUrl);
		stud.innerHTML = tmpl;

	}
}

function hideStudentEdit(uid) {
	var iframe = parent.document.getElementById('studentEdit');

	if (iframe == null) {
		iframe = document.getElementById('studentEdit');
	}



	iframe.style.height = "0px";
	iframe.style.width = "0px";
	iframe.style.visibility='hidden';
	iframe.src = '';
	//document.getElementById('studentSearch').src = document.getElementById('studentSearch').src;
}