import env from './env'

const DEV_URL = 'http://lucms.test/'
const PRO_URL = 'https://lucms.com/'

export default env === 'development' ? DEV_URL : PRO_URL
