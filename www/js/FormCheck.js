
var FormValidation = function () {

    this.obj = null;
    this.minLength = 0;
    this.maxLength = 0;
    this.msgStr = "";
    this.chkType = "";

    this.setData = function (_obj, _msgStr, _min, _max, _type) {

        this.obj = _obj;
        this.msgStr = _msgStr || "형식이 올바르지 않습니다.";
        this.minLength = _min;
        this.maxLength = _max;
        this.chkType = _type || "";
    };

    this.chkValue = function (_obj, _msgStr, _min, _max, _type) {

        this.setData(_obj, _msgStr, _min, _max, _type)

        if (!this.chkLength()) return false;
        if (!this.chkValueType()) return false;
        //this.setData();
        return true;

    };

    this.chkLength = function (_min, _max) {
        return ((this.minLength > 0 && this.obj.value.trim().length < this.minLength) || (this.maxLength > 0 && this.obj.value.trim().length > this.maxLength)) ?
            this.callMessage() : true;
    };

    this.chkValueType = function () {
        //log(this.obj.value.isRegexp(this.chkType) + "," + this.chkType);
        return (this.chkType && !this.obj.value.isRegexp(this.chkType)) ?
            this.callMessage() : true;
    };

    this.chkCompare = function () {
    }

    this.callMessage = function () {

        alert(this.msgStr);
        if (this.obj.focus)
            this.obj.focus();

        return false;
    };

    var chkText = function (_obj) {
    };

    var chkSelect = function () {
    };

    var chkRadio = function () {
    };

    var chkCheckBoc = function () {
    };

};
