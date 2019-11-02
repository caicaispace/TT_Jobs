/**
 * 二进制处理库
 */

define(function () {
    /**
     * 写入二进制位
     * @param  {object} data 需要解析的数据
     * @param  {object} mark 位标记
     * @return {int}      返回处理后的数据
     */
    function write(data, mark) {
        var i = 0;
        for (var k in data) {
            if (data[k]) i |= (1 << mark[k]);
        }
        return i;
    }

    /**
     * 读取二进制位信息
     * @param  {int} data 需要提取的数据
     * @param  {object} mark 位标记
     * @return {object}      返回可读数据
     */
    function read(data, mark) {
        var t = {};
        data = parseInt(data);
        for (var k in mark) {
            t[k] = data & (1 << mark[k]) ? 1 : 0;
        }
        return t;
    }

    return {
        write: write,
        read: read,
    };
});
