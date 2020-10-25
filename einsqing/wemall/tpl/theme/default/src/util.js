import axios from "axios";
import Vue from "vue";

export function getFile(file) {
	if(file){
		return axios.defaults.baseURL + `/public/uploads/${file.savepath}${file.savename}`;
	}
	return '';
}