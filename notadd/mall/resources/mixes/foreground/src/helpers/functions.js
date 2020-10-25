function preserveCamelCase(str) {
    let data = str;
    let isLastCharLower = false;
    let isLastCharUpper = false;
    let isLastLastCharUpper = false;

    for (let i = 0; i < data.length; i++) {
        const c = data[i];

        if (isLastCharLower && /[a-zA-Z]/.test(c) && c.toUpperCase() === c) {
            data = `${data.substr(0, i)}-${data.substr(i)}`;
            isLastCharLower = false;
            isLastLastCharUpper = isLastCharUpper;
            isLastCharUpper = true;
            i += 1;
        } else if (isLastCharUpper && isLastLastCharUpper && /[a-zA-Z]/.test(c) && c.toLowerCase() === c) {
            data = `${data.substr(0, i - 1)}-${data.substr(i - 1)}`;
            isLastLastCharUpper = isLastCharUpper;
            isLastCharUpper = false;
            isLastCharLower = true;
        } else {
            isLastCharLower = c.toLowerCase() === c;
            isLastLastCharUpper = isLastCharUpper;
            isLastCharUpper = c.toUpperCase() === c;
        }
    }

    return data;
}

export function decamelize(str, sep) {
    if (typeof str !== 'string') {
        throw new TypeError('Expected a string');
    }

    const split = typeof sep === 'undefined' ? '_' : sep;

    return str
        .replace(/([a-z\d])([A-Z])/g, `$1${split}$2`)
        .replace(/([A-Z]+)([A-Z][a-z\d]+)/g, `$1${split}$2`)
        .toLowerCase();
}

export function camelcase(str, ...args) {
    let data;
    if (args.length > 1) {
        data = Array.from(args)
            .map(x => x.trim())
            .filter(x => x.length)
            .join('-');
    } else {
        data = str.trim();
    }

    if (str.length === 0) {
        return '';
    }

    if (str.length === 1) {
        return str.toLowerCase();
    }

    if (/^[a-z0-9]+$/.test(str)) {
        return str;
    }

    const hasUpperCase = str !== str.toLowerCase();

    if (hasUpperCase) {
        data = preserveCamelCase(str);
    }

    return data
        .replace(/^[_.\- ]+/, '')
        .toLowerCase()
        .replace(/[_.\- ]+(\w|$)/g, (m, p1) => p1.toUpperCase());
}