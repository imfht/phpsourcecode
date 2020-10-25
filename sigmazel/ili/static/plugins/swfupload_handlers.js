var swfupload_params = {'limit':0, 'uploaded':0};

function getFileExt(filename) {
	return filename.substr(filename.toLowerCase().lastIndexOf('.') + 1);
}

function formatFilePath(serverData){
	var pathArray = serverData.split('|');
	var ext = getFileExt(pathArray[0]);
	var filepath = pathArray[0] + (pathArray[2] == 0 ? '' : '.t.' + ext);
	if(filepath.indexOf('http://') != -1) return filepath;
	else return 'attachment/' + filepath;
}

function fileQueueError(file, errorCode, message) {
	try {
		var imageName = "error.gif";
		var errorName = "";
		
		if (errorCode === SWFUpload.QUEUE_ERROR.QUEUE_LIMIT_EXCEEDED) {
			errorName = swfupload.lang.limit;
		}

		if (errorName !== "") {
			alert(errorName);
			return;
		}
		
		switch (errorCode) {
			case SWFUpload.QUEUE_ERROR.ZERO_BYTE_FILE:
				imageName = "zerobyte.gif";
				break;
			case SWFUpload.QUEUE_ERROR.FILE_EXCEEDS_SIZE_LIMIT:
				imageName = "toobig.gif";
				break;
			case SWFUpload.QUEUE_ERROR.ZERO_BYTE_FILE:
			case SWFUpload.QUEUE_ERROR.INVALID_FILETYPE:
			default:
				alert(message);
				break;
		}
		
		addImage("static/images/swfupload/" + imageName);
	} catch (ex) {}
}

function fileDialogComplete(numFilesSelected, numFilesQueued) {
	try {
		if (numFilesQueued > 0) {
			this.setPostParams(swfupload_params);
			this.startUpload();
		}
	} catch (ex) {}
}

function uploadProgress(file, bytesLoaded) {
	try {
		var percent = Math.ceil((bytesLoaded / file.size) * 100);

		var progress = new FileProgress(file,  this.customSettings.upload_target);
		progress.setProgress(percent);
		if (percent === 100) {
			progress.setStatus(swfupload.lang.show);
			progress.toggleCancel(false, this);
		} else {
			progress.setStatus(swfupload.lang.uploading);
			progress.toggleCancel(true, this);
		}
	} catch (ex) {}
}

function uploadComplete(file) {
	try {
		if (this.getStats().files_queued > 0) {
			this.setPostParams(swfupload_params);
			this.startUpload();
		} else {
			var progress = new FileProgress(file,  this.customSettings.upload_target);
			progress.setComplete();
			progress.setStatus(swfupload.lang.over);
			progress.toggleCancel(false);
		}
	} catch (ex) {}
}

function uploadError(file, errorCode, message) {
	var imageName =  "error.gif";
	var progress;
	try {
		switch (errorCode) {
		case SWFUpload.UPLOAD_ERROR.FILE_CANCELLED:
			try {
				progress = new FileProgress(file,  this.customSettings.upload_target);
				progress.setCancelled();
				progress.setStatus("Cancelled");
				progress.toggleCancel(false);
			}
			catch (ex1) {}
			break;
		case SWFUpload.UPLOAD_ERROR.UPLOAD_STOPPED:
			try {
				progress = new FileProgress(file,  this.customSettings.upload_target);
				progress.setCancelled();
				progress.setStatus(swfupload.lang.cancel);
				progress.toggleCancel(true);
			}
			catch (ex2) {}
		case SWFUpload.UPLOAD_ERROR.UPLOAD_LIMIT_EXCEEDED:
			imageName = "uploadlimit.gif";
			break;
		default:
			alert(message);
			break;
		}
		
		addImage("static/images/swfupload/" + imageName);
	} catch (ex3) {}
}

function fadeIn(element, opacity) {
	var reduceOpacityBy = 5;
	var rate = 30;
	
	if (opacity < 100) {
		opacity += reduceOpacityBy;
		if (opacity > 100) {
			opacity = 100;
		}

		if (element.filters) {
			try {
				element.filters.item("DXImageTransform.Microsoft.Alpha").opacity = opacity;
			} catch (e) {
				element.style.filter = 'progid:DXImageTransform.Microsoft.Alpha(opacity=' + opacity + ')';
			}
		} else {
			element.style.opacity = opacity / 100;
		}
	}

	if (opacity < 100) {
		setTimeout(function () {
			fadeIn(element, opacity);
		}, rate);
	}
}

function FileProgress(file, targetID) {
	this.fileProgressID = targetID + "Progress";

	this.fileProgressWrapper = document.getElementById(this.fileProgressID);
	if (!this.fileProgressWrapper) {
		this.fileProgressWrapper = document.createElement("div");
		this.fileProgressWrapper.className = "progressWrapper";
		this.fileProgressWrapper.id = this.fileProgressID;

		this.fileProgressElement = document.createElement("div");
		this.fileProgressElement.className = "progressContainer";

		var progressCancel = document.createElement("a");
		progressCancel.className = "progressCancel";
		progressCancel.href = "#";
		progressCancel.style.visibility = "hidden";
		progressCancel.appendChild(document.createTextNode(" "));

		var progressText = document.createElement("div");
		progressText.className = "progressName";
		progressText.appendChild(document.createTextNode(file.name));

		var progressBar = document.createElement("div");
		progressBar.className = "progressBarInProgress";

		var progressStatus = document.createElement("div");
		progressStatus.className = "progressBarStatus";
		progressStatus.innerHTML = "&nbsp;";

		this.fileProgressElement.appendChild(progressCancel);
		this.fileProgressElement.appendChild(progressText);
		this.fileProgressElement.appendChild(progressStatus);
		this.fileProgressElement.appendChild(progressBar);

		this.fileProgressWrapper.appendChild(this.fileProgressElement);

		document.getElementById(targetID).appendChild(this.fileProgressWrapper);
		fadeIn(this.fileProgressWrapper, 0);

	} else {
		this.fileProgressElement = this.fileProgressWrapper.firstChild;
		this.fileProgressElement.childNodes[1].firstChild.nodeValue = file.name;
	}

	this.height = this.fileProgressWrapper.offsetHeight;

}
FileProgress.prototype.setProgress = function (percentage) {
	this.fileProgressElement.className = "progressContainer green";
	this.fileProgressElement.childNodes[3].className = "progressBarInProgress";
	this.fileProgressElement.childNodes[3].style.width = percentage + "%";
};
FileProgress.prototype.setComplete = function () {
	this.fileProgressElement.className = "progressContainer blue";
	this.fileProgressElement.childNodes[3].className = "progressBarComplete";
	this.fileProgressElement.childNodes[3].style.width = "";

};
FileProgress.prototype.setError = function () {
	this.fileProgressElement.className = "progressContainer red";
	this.fileProgressElement.childNodes[3].className = "progressBarError";
	this.fileProgressElement.childNodes[3].style.width = "";

};
FileProgress.prototype.setCancelled = function () {
	this.fileProgressElement.className = "progressContainer";
	this.fileProgressElement.childNodes[3].className = "progressBarError";
	this.fileProgressElement.childNodes[3].style.width = "";

};
FileProgress.prototype.setStatus = function (status) {
	this.fileProgressElement.childNodes[2].innerHTML = status;
};

FileProgress.prototype.toggleCancel = function (show, swfuploadInstance) {
	this.fileProgressElement.childNodes[0].style.visibility = show ? "visible" : "hidden";
	if (swfuploadInstance) {
		var fileID = this.fileProgressID;
		this.fileProgressElement.childNodes[0].onclick = function () {
			swfuploadInstance.cancelUpload(fileID);
			return false;
		};
	}
};
