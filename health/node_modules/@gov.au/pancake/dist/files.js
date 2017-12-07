/***************************************************************************************************************************************************************
 *
 * Handel files, read, write, check
 *
 * @repo    - https://github.com/govau/pancake
 * @author  - Dominik Wilkowski
 * @license - https://raw.githubusercontent.com/govau/pancake/master/LICENSE (MIT)
 *
 **************************************************************************************************************************************************************/

'use strict';

//--------------------------------------------------------------------------------------------------------------------------------------------------------------
// Dependencies
//--------------------------------------------------------------------------------------------------------------------------------------------------------------

Object.defineProperty(exports, "__esModule", {
	value: true
});
exports.CopyFile = exports.ReadFile = exports.WriteFile = exports.CreateDir = exports.GetFolders = undefined;

var _stringify = require('babel-runtime/core-js/json/stringify');

var _stringify2 = _interopRequireDefault(_stringify);

var _promise = require('babel-runtime/core-js/promise');

var _promise2 = _interopRequireDefault(_promise);

var _path = require('path');

var _path2 = _interopRequireDefault(_path);

var _fs = require('fs');

var _fs2 = _interopRequireDefault(_fs);

var _logging = require('./logging');

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

//--------------------------------------------------------------------------------------------------------------------------------------------------------------
// Get all folders inside a folder
//--------------------------------------------------------------------------------------------------------------------------------------------------------------
/**
 * Get all folders within a given path
 *
 * @param  {string}  thisPath - The path that contains the desired folders
 *
 * @return {array}            - An array of paths to each folder
 */
var GetFolders = exports.GetFolders = function GetFolders(thisPath) {
	_logging.Log.verbose('Looking for folders in ' + _logging.Style.yellow(thisPath));

	try {
		return _fs2.default.readdirSync(_path2.default.normalize(thisPath)) //read the folders content
		.filter(function (thisFile) {
			return _fs2.default.statSync(_path2.default.normalize(thisPath + '/' + thisFile)).isDirectory();
		} //only return directories
		).map(function (path) {
			return _path2.default.normalize(thisPath + '/' + path);
		}); //return with path
	} catch (error) {
		_logging.Log.verbose(_logging.Style.yellow(thisPath) + ' not found');
		// Log.error( error );

		return [];
	}
};

//--------------------------------------------------------------------------------------------------------------------------------------------------------------
// Create all folders in a path
//--------------------------------------------------------------------------------------------------------------------------------------------------------------
/**
 * Create a path if it doesn’t exist
 *
 * @param  {string}  dir      - The path to be checked and created if not found
 *
 * @return {string}           - The path that was just worked at
 */


//--------------------------------------------------------------------------------------------------------------------------------------------------------------
// Included modules
//--------------------------------------------------------------------------------------------------------------------------------------------------------------
var CreateDir = exports.CreateDir = function CreateDir(dir) {
	_logging.Log.verbose('Creating path ' + _logging.Style.yellow(dir));

	var splitPath = dir.split(_path2.default.sep);

	splitPath.reduce(function (path, subPath) {
		var currentPath = void 0;

		if (/^win/.test(process.platform) && path === '') {
			// when using windows (post truth) at beginning of the path
			path = './'; // we add the prefix to make sure it works on windows (yuck)
		}

		if (subPath != '.') {
			currentPath = _path2.default.normalize(path + '/' + subPath);

			_logging.Log.verbose('Checking if ' + _logging.Style.yellow(currentPath) + ' exists');

			if (!_fs2.default.existsSync(currentPath)) {
				try {
					_fs2.default.mkdirSync(currentPath);

					_logging.Log.verbose('Successfully ' + _logging.Style.yellow(currentPath) + ' created');
				} catch (error) {
					_logging.Log.error('Pancake was unable to create the folder ' + _logging.Style.yellow(currentPath) + ' for path ' + _logging.Style.yellow(dir));
					_logging.Log.error(error);

					process.exit(1);
				}
			}
		} else {
			currentPath = subPath;
		}

		return currentPath;
	}, '');

	return splitPath.join(_path2.default.sep);
};

//--------------------------------------------------------------------------------------------------------------------------------------------------------------
// Write file
//--------------------------------------------------------------------------------------------------------------------------------------------------------------
/**
 * Promisified writing a file
 *
 * @param  {string} location - The location the file should be written to
 * @param  {string} content  - The content of the file
 *
 * @return {promise object}  - Boolean true for 👍 || string error for 👎
 */
var WriteFile = exports.WriteFile = function WriteFile(location, content) {
	CreateDir(_path2.default.dirname(location));

	return new _promise2.default(function (resolve, reject) {
		_fs2.default.writeFile(location, content, 'utf8', function (error) {
			if (error) {
				_logging.Log.error('Writing file failed for ' + _logging.Style.yellow(location));
				_logging.Log.error((0, _stringify2.default)(error));

				reject(error);
			} else {
				_logging.Log.verbose('Successfully written ' + _logging.Style.yellow(location));

				resolve(true);
			}
		});
	});
};

//--------------------------------------------------------------------------------------------------------------------------------------------------------------
// Read file
//--------------------------------------------------------------------------------------------------------------------------------------------------------------
/**
 * Promisified reading a file
 *
 * @param  {string} location - The location of the file to be read
 *
 * @return {promise object}  - The content of the file
 */
var ReadFile = exports.ReadFile = function ReadFile(location) {
	return new _promise2.default(function (resolve, reject) {
		_fs2.default.readFile(location, 'utf8', function (error, content) {
			if (error) {
				_logging.Log.error('Reading file failed for ' + _logging.Style.yellow(location));
				_logging.Log.error((0, _stringify2.default)(error));

				reject(error);
			} else {
				_logging.Log.verbose('Successfully read ' + _logging.Style.yellow(location));

				resolve(content);
			}
		});
	});
};

//--------------------------------------------------------------------------------------------------------------------------------------------------------------
// Copy file
//--------------------------------------------------------------------------------------------------------------------------------------------------------------
/**
 * Copy a file to another location
 *
 * @param  {string} fromFile - The path to the source file
 * @param  {string} toFile   - The path to the destination
 *
 * @return {promise object}  - The content of the file
 */
var CopyFile = exports.CopyFile = function CopyFile(fromFile, toFile) {
	CreateDir(_path2.default.dirname(location));

	return new _promise2.default(function (resolve, reject) {
		var writeStream = _fs2.default.createWriteStream(toFile).on('error', handleError).on('finish', function () {
			_logging.Log.verbose('Successfully copied ' + _logging.Style.yellow(toFile));

			resolve();
		});

		var readStream = _fs2.default.createReadStream(fromFile).on('error', handleError).pipe(writeStream);

		function handleError(error) {
			_logging.Log.error('Copying file failed for ' + _logging.Style.yellow(location));
			_logging.Log.error((0, _stringify2.default)(error));

			readStream.destroy();
			writeStream.end();
			reject(error);
		}
	});
};