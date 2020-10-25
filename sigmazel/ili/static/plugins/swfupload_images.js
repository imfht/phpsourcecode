function uploadSuccess(file, serverData) {
	try {
		var progress = new FileProgress(file,  this.customSettings.upload_target);
		var maxdisplayorder = 0;
		$('#tbl_body_column input').each(function(index, item){
			if(item.name.indexOf('ImageDisplayOrder') != -1 && item.value) {
				maxdisplayorder = item.value.isInt() ? item.value - 0 : 1;
			}
		});
		
		maxdisplayorder++;
		
		if (serverData.substring(0, 7) === "FILEID:") {
			addImage(formatFilePath(serverData.substring(7)), serverData, maxdisplayorder < 1 ? $('#tbl_body_column textarea').length : maxdisplayorder);
			
			progress.setStatus(swfupload.lang.end);
			progress.toggleCancel(false);
		} else {
			progress.setStatus(swfupload.lang.error);
			progress.toggleCancel(false);
			alert(serverData);
		}
	} catch (ex) {}
}


function removeImage(link){
	$(link).parent().parent().remove();
	checkImages();
}

function setUploadImageCount(count){
    swfupload_params.uploaded = count;
}

function checkImages(){
	var image_areas = $('#tbl_body_column :text').length;
	if(image_areas < 2) $('#tbl_no_record').show();
	else $('#tbl_no_record').hide();
	
	setUploadImageCount(image_areas - 1);
}

function addImage(src) {
	var filepaths = arguments.length > 1 ? arguments[1] : '';
	var imagecount = arguments.length > 2 ? arguments[2] : 0;
	
	var tbody = $('#tbl_body_column').get(0);
	var tempInsertRow = tbody.rows[tbody.rows.length - 1];
	var tempNewRow = tempInsertRow.cloneNode(true);
	tempNewRow.style.display = '';
	
	
	var images = tempNewRow.getElementsByTagName('img');
	var inputs = tempNewRow.getElementsByTagName('input');
	var spans = tempNewRow.getElementsByTagName('span');
	
	for(var i = 0; i < inputs.length; i++){
		var curinput = inputs[i];
		if(curinput.name == 'hdnImagePath[]') curinput.value = filepaths;
		else if(curinput.name == 'txtImageDisplayOrder[]') curinput.value = imagecount + '';
	}
	
	var tmparr = filepaths.split('|');
	var imagName = tmparr.length > 1 ? tmparr[1] : '';
	imagName += tmparr.length > 4 ? '<br/>' + tmparr[3] + 'px * ' + tmparr[4] + 'px' : '';
	
	$(spans).html(imagName);
	
	var newImg = images[0];
	newImg.style.margin = "5px";
	newImg.className = 'image';
	
	if (newImg.filters) {
		try {
			newImg.filters.item("DXImageTransform.Microsoft.Alpha").opacity = 0;
		} catch (e) {
			newImg.style.filter = 'progid:DXImageTransform.Microsoft.Alpha(opacity=' + 0 + ')';
		}
	} else {
		newImg.style.opacity = 0;
	}
	
	newImg.onload = function () {
		fadeIn(newImg, 0);
	};
	
	newImg.src = src;
	
	tbody.insertBefore(tempNewRow, tbody.rows[tbody.rows.length - 1]);
	checkImages();
}