export function get(name) {
    let result = document.cookie.match("(^|[^;]+)\\s*" + name + "\\s*=\\s*([^;]+)")
    return result ? result.pop() : '';
}

export function set(cookieKey, cookieValue, expirationDays) {
    let expiryDate = '';

    if(expirationDays) {
        const date = new Date();
        date.setTime(`${date.getTime()}${(expirationDays || 30 * 24 * 60 * 60 * 1000)}`);
        expiryDate = `; expiryDate=" ${date.toUTCString()}`;
    }

    document.cookie = `${cookieKey}=${cookieValue || ''}${expiryDate}; path=/`;
}

export function update() {
    console.log('updateCookie');
}

export function remove(name) {
    setCookie(name, '', -1);
}

export default { get, set, update, remove }