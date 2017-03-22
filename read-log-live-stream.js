const fs   = require('fs');
const path = require('path');

const log_path      = path.join(__dirname + '/storage/logs');
const watched_files = [];

fs.watch(log_path, (eventType, filename) => {
    // let is_err_log = filename.includes('error');
    let is_err_log = true;
    let watched    = watched_files.includes(filename);

    let should_watch = is_err_log && !watched
    
    if (should_watch) {
    	let full_file_path = path.join(log_path, filename);
        live_stream_log(full_file_path);
    }
});

function live_stream_log(filename){
	//Update watched_files
	watched_files.push(filename);

	fs.watchFile(filename, (curr, prev) => {
	    let position = prev.size;
	    let length   = curr.size - prev.size;
	    
	    fs.open(filename, 'r', function(status, fd) {
	        if (status) {
	            console.log(status.message);
	            return;
	        }
	        var buffer = new Buffer(length);
	        // fs.read(fd, buffer, offset, length, position, callback);
	        fs.read(fd, buffer, 0, length, position, function(err, num) {
	            // console.log(buffer.toString('utf8', 0, num));
	            let message = buffer.toString('utf8', 0, num);
	            let msg_datetime = message.substr(0, 21);
	            let msg_info     = message.substr(21);
	            // console.log('\x1b[32m%s\x1b[0m', msg_datetime, msg_info);
	            console.log('\x1b[32m%s\x1b[0m', msg_datetime);
	            console.log(msg_info);
	        });
	    });
	});
}





/**
 * Working example on read file at certain buffer position
 */
// var fs = require('fs');

// fs.open('D:\\work-station\\laravel-reservation\\storage\\logs\\error-2017-03-21.log', 'r', function(status, fd) {
//     if (status) {
//         console.log(status.message);
//         return;
//     }
//     var buffer = new Buffer(200);
//     // fs.read(fd, buffer, offset, length, position, callback);
//     fs.read(fd, buffer, 0, 185, 185, function(err, num) {
//         console.log(buffer.toString('utf8', 0, num));
//     });
// });