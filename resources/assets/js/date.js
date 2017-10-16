Date.prototype.fixZero = function(s) {
    s = String(s);
    return '0'.substr(0, 2 - s.length) + s;
}

Date.prototype.toDate = function(){
    return this.getFullYear() + '-' + this.fixZero(this.getMonth() + 1) + '-' + this.fixZero(this.getDate());
}

Date.prototype.toTime = function() {
    let h = this.getHours()
    if(this.time12) {
        if(h > 12) h = '下午' + (h - 12)
    } else
        h = this.fixZero(h)
    return h + ':' + this.fixZero(this.getMinutes()) + ':' + this.fixZero(this.getSeconds());
}

Date.prototype.toDateTime = function() {
    return this.toDate() + ' ' + this.toTime();
}
