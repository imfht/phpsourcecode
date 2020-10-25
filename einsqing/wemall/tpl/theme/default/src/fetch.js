import axios from "axios";

axios.defaults.baseURL = baseUrl;
axios.defaults.headers.common['Authorization'] = '';

export default function fetch(url, params) {

    axios.defaults.headers.common['Authorization'] = localStorage.token;

    if (params) {
        return axios.post(url, params).catch(function(error) {
	        if (error.response) {
	            if (error.response.status == 401) {
	            	location.href = '#login';
	            }
	        } else {
	            console.log('Error', error.message);
	        }
	    });
    }
    return axios.get(url).catch(function(error) {
        if (error.response) {
            if (error.response.status == 401) {
            	location.href = '#login';
            }
        } else {
            console.log('Error', error.message);
        }
    });
}
