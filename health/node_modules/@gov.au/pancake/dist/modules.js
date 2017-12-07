/***************************************************************************************************************************************************************
 *
 * Read all pancake packages
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
exports.GetPlugins = exports.GetModules = exports.ReadModule = undefined;

var _keys = require('babel-runtime/core-js/object/keys');

var _keys2 = _interopRequireDefault(_keys);

var _stringify = require('babel-runtime/core-js/json/stringify');

var _stringify2 = _interopRequireDefault(_stringify);

var _typeof2 = require('babel-runtime/helpers/typeof');

var _typeof3 = _interopRequireDefault(_typeof2);

var _promise = require('babel-runtime/core-js/promise');

var _promise2 = _interopRequireDefault(_promise);

var _path = require('path');

var _path2 = _interopRequireDefault(_path);

var _fs = require('fs');

var _fs2 = _interopRequireDefault(_fs);

var _logging = require('./logging');

var _files = require('./files');

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

//--------------------------------------------------------------------------------------------------------------------------------------------------------------
// Default export
//--------------------------------------------------------------------------------------------------------------------------------------------------------------
/**
 * Reading and parsing a package.json file of a module
 *
 * @param  {string}  pkgPath - The path to the folder the package.json is in (omitting package.json)
 *
 * @return {promise object}  - Returns a promise and some of the data of the package.json
 */


//--------------------------------------------------------------------------------------------------------------------------------------------------------------
// Included modules
//--------------------------------------------------------------------------------------------------------------------------------------------------------------
var ReadModule = exports.ReadModule = function ReadModule(pkgPath) {
	var thisPath = _path2.default.normalize(pkgPath + '/package.json');

	_logging.Log.verbose('Reading ' + _logging.Style.yellow(thisPath));

	return new _promise2.default(function (resolve, reject) {
		_fs2.default.readFile(thisPath, 'utf8', function (error, data) {
			if (error) {
				_logging.Log.verbose('No package.json found in ' + _logging.Style.yellow(thisPath)); //folders like .bin and .staging won’t have package.json inside

				resolve(null);
			} else {

				var packageJson = JSON.parse(data); //parse the package.json

				if ((0, _typeof3.default)(packageJson.pancake) === 'object') {
					//is this a pancake module?
					_logging.Log.verbose(_logging.Style.green('✔') + ' Identified ' + _logging.Style.yellow(packageJson.name) + ' as pancake module');

					//we only want a subset
					var miniPackage = {
						name: packageJson.name,
						version: packageJson.version,
						peerDependencies: packageJson.peerDependencies,
						pancake: packageJson.pancake,
						path: pkgPath
					};

					resolve(miniPackage);
				} else {
					resolve(null); //non-pancake packages get null so we can identify them later and filter them out
				}
			}
		});
	});
};

/**
 * Get an object of all pancake modules inside a specified folder
 *
 * @param  {string}  pkgPath - The path that includes your node_module folder
 * @param  {string}  npmOrg  - The npmOrg scope
 *
 * @return {promise object}  - A promise.all that resolves when all package.jsons have been read
 */
var GetModules = exports.GetModules = function GetModules(pkgPath) {
	var npmOrg = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : '';

	if (typeof pkgPath !== 'string' || pkgPath.length <= 0) {
		_logging.Log.error('GetPackages only takes a valid path. You passed [type: ' + _logging.Style.yellow(typeof pkgPath === 'undefined' ? 'undefined' : (0, _typeof3.default)(pkgPath)) + '] "' + _logging.Style.yellow(pkgPath) + '"');
	}

	pkgPath = _path2.default.normalize(pkgPath + '/node_modules/' + npmOrg + '/'); //we add our npm org scope to the path to make this more effective

	_logging.Log.verbose('Looking for pancake modules in: ' + _logging.Style.yellow(pkgPath));

	var allModules = (0, _files.GetFolders)(pkgPath); //all folders inside the selected path

	if (allModules !== undefined && allModules.length > 0) {
		_logging.Log.verbose('Found the following module folders:\n' + _logging.Style.yellow(allModules.join('\n')));

		var allPackages = allModules.map(function (pkg) {
			return ReadModule(pkg).catch(function (error) {
				_logging.Log.error(error);

				process.exit(1);
			});
		}); //read all packages and save the promise return

		return _promise2.default.all(allPackages).then(function (packages) {
			//chaining the promise
			return packages.filter(function (p) {
				return p !== null;
			}); //making sure packages not identified as a pancake module don’t leave a trace in the returned array
		});
	} else {
		return _promise2.default.resolve([]); //no pancake modules found at all
	}
};

/**
 * Generate an object from the allModules object to filter out all plugins requested by all modules
 *
 * @param  {object} allModules - The object off all modules from GetModules()
 *
 * @return {array}             - An array of all plugins
 */
var GetPlugins = exports.GetPlugins = function GetPlugins(allModules) {
	var plugins = {};

	allModules.map(function (module) {
		if (module.pancake === undefined) {
			//so we can pass the error message on to next block
			module.pancake = {};
		}

		if (module.pancake['pancake-module'] === undefined) {
			_logging.Log.error('The data passed to GetPlugins is missing the "' + _logging.Style.yellow('pancake-module') + '" object.');

			return false;
		}

		module.pancake['pancake-module'].plugins.map(function (plugin) {
			plugins[plugin] = 'yay!'; //we make them objects to filter out duplicates :)
		});
	});

	_logging.Log.verbose('Found the following plugins ' + _logging.Style.yellow((0, _stringify2.default)((0, _keys2.default)(plugins))));

	return (0, _keys2.default)(plugins);
};