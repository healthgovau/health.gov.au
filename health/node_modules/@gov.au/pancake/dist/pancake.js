#!/usr/bin/env node


/***************************************************************************************************************************************************************
 *
 * Checking peerDependencies for conflicts
 * This tool was built to make working with npm and the front end easy and seamless.
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
exports.Batter = exports.Semver = exports.CopyFile = exports.ReadFile = exports.WriteFile = exports.CreateDir = exports.GetFolders = exports.Settings = exports.GetModules = exports.CheckModules = exports.ParseArgs = exports.Loading = exports.Style = exports.Log = exports.Spawning = exports.Size = exports.Cwd = exports.CheckNPM = exports.ExitHandler = undefined;

var _stringify = require('babel-runtime/core-js/json/stringify');

var _stringify2 = _interopRequireDefault(_stringify);

var _promise = require('babel-runtime/core-js/promise');

var _promise2 = _interopRequireDefault(_promise);

var _path = require('path');

var _path2 = _interopRequireDefault(_path);

var _files = require('./files');

var _helpers = require('./helpers');

var _logging = require('./logging');

var _parseArguments = require('./parse-arguments');

var _conflicts = require('./conflicts');

var _modules = require('./modules');

var _settings = require('./settings');

var _semver = require('./semver-5-3-0');

var _semver2 = _interopRequireDefault(_semver);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

//--------------------------------------------------------------------------------------------------------------------------------------------------------------
// Using this file to export the reusable items
//--------------------------------------------------------------------------------------------------------------------------------------------------------------
exports.ExitHandler = _helpers.ExitHandler;
exports.CheckNPM = _helpers.CheckNPM;
exports.Cwd = _helpers.Cwd;
exports.Size = _helpers.Size;
exports.Spawning = _helpers.Spawning;
exports.Log = _logging.Log;
exports.Style = _logging.Style;
exports.Loading = _logging.Loading;
exports.ParseArgs = _parseArguments.ParseArgs;
exports.CheckModules = _conflicts.CheckModules;
exports.GetModules = _modules.GetModules;
exports.Settings = _settings.Settings;
exports.GetFolders = _files.GetFolders;
exports.CreateDir = _files.CreateDir;
exports.WriteFile = _files.WriteFile;
exports.ReadFile = _files.ReadFile;
exports.CopyFile = _files.CopyFile;
exports.Semver = _semver2.default;

//--------------------------------------------------------------------------------------------------------------------------------------------------------------
// Get batter object
//--------------------------------------------------------------------------------------------------------------------------------------------------------------
/**
 * Running all the important bits to get us the data we need to run Pancake programmatically
 *
 * @param  {array} argv     - The arguments passed to node
 *
 * @return {Promise object} - The data object of the pancake modules
 */

var Batter = exports.Batter = function Batter() {
	var argv = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : process.argv;

	var pkg = require(_path2.default.normalize(__dirname + '/../package.json'));

	// Check npm version
	var npmVersion = (0, _helpers.CheckNPM)();

	//npm 3 and higher is required as below will install dependencies inside each module folder
	if (!npmVersion) {
		_logging.Log.error('Pancake only works with npm 3 and later.');
		_logging.Log.space();
		process.exit(1);
	}

	// Get global settings
	var SETTINGS = _settings.Settings.GetGlobal();

	// Parsing cli arguments
	var ARGS = (0, _parseArguments.ParseArgs)(SETTINGS, argv);

	//arg overwrites
	SETTINGS.npmOrg = ARGS.org;
	SETTINGS.plugins = ARGS.plugins;
	SETTINGS.ignorePlugins = ARGS.ignorePlugins;

	// Finding the current working directory
	var pkgPath = (0, _helpers.Cwd)(ARGS.cwd);

	// Get local settings
	var SETTINGSlocal = _settings.Settings.GetLocal(pkgPath);

	// Get all modules data
	return new _promise2.default(function (resolve, reject) {

		(0, _modules.GetModules)(pkgPath, SETTINGS.npmOrg).catch(function (error) {
			reject('Reading all package.json files bumped into an error: ' + error);
			reject(error);
		}).then(function (allModules) {
			//once we got all the content from all package.json files
			_logging.Log.verbose('Gathered all modules:\n' + _logging.Style.yellow((0, _stringify2.default)(allModules)));

			if (allModules.length > 0) {
				var conflicts = (0, _conflicts.CheckModules)(allModules); //check for conflicts

				if (conflicts.conflicts) {
					reject(conflicts);
				} else {
					resolve({
						version: pkg.version,
						modules: allModules,
						settings: SETTINGSlocal,
						globalSettings: SETTINGS,
						cwd: pkgPath
					});
				}
			} else {
				resolve({
					version: pkg.version,
					modules: allModules,
					settings: SETTINGSlocal,
					globalSettings: SETTINGS,
					cwd: pkgPath
				});
			}
		});
	});
};