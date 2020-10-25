export default {
    url(path = '') {
        return process.env.VUE_APP_API_URL + path;
    },
};
