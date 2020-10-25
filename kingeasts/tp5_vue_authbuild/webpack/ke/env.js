const fs = require('fs')
const path = require('path')

class env
{
    constructor(filename) {
        let data = fs.readFileSync(filename, 'utf-8')
        data = data.split("\r\n").filter(str => {
            str = str.trim()
            return str !== '' && str.substr(0, 1) !== '#'
        })
        this.data = {}
        data.map(item => {
            let tmp = item.split('=')
            let value
            switch (tmp[1].trim()) {
                case 'true':
                    value = true
                    break
                case 'false':
                    value = false
                    break
                case '1':
                    value = true
                    break
                case '0':
                    value = false
                    break
                default:
                    value = tmp[1].trim()
                    break
            }
            this.data[tmp[0].trim()] = value
        })

    }

    get (key) {
        return this.data[key]
    }

}

module.exports = env
