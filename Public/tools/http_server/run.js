/*
 * @Author: 0ivi0
 * @Date:   2017-05-24 10:16:06
 * @Last Modified by:   0ivi0
 * @Last Modified time: 2017-05-24 12:13:30
 */

// var http = require('http');

// http.createServer(function(req, res) {
//   res.writeHead(200, {'Content-Type': 'text/plain'});
//   res.end('Hello World');
// }).listen(5000);

var http = require('http');
var url  = require('url');
var fs   = require('fs');
var mine = require('./mine').types;
var path = require('path');

var PORT = 3000;
var ROOT_PATH = '.';
// var ROOT_PATH = '../../';

var server = http.createServer(function(request, response) {
    var pathname = url.parse(request.url).pathname;
    if (pathname.charAt(pathname.length - 1) === "/") {
        pathname += "index.html"; //指定为默认网页
    }
    var realPath = path.join(ROOT_PATH, pathname);
    var ext = path.extname(realPath);
    ext = ext ? ext.slice(1) : 'unknown';
    fs.exists(realPath, function(exists) {
        if (!exists) {
            response.writeHead(404, {
                'Content-Type': 'text/plain'
            });

            response.write("This request URL " + pathname + " was not found on this server.");
            response.end();
        } else {
            fs.readFile(realPath, "binary", function(err, file) {
                if (err) {
                    response.writeHead(500, {
                        'Content-Type': 'text/plain'
                    });
                    response.end(err);
                } else {
                    var contentType = mine[ext] || "text/plain";
                    response.writeHead(200, {
                        'Content-Type': contentType
                    });
                    response.write(file, "binary");
                    response.end();
                }
            });
        }
    });
});
server.listen(PORT);
console.log("Server runing at port: " + PORT + ".");
