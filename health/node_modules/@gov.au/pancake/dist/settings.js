/***************************************************************************************************************************************************************
 *
 * Get and set global/local settings
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
exports.Settings = undefined;

var _promise = require('babel-runtime/core-js/promise');

var _promise2 = _interopRequireDefault(_promise);

var _toConsumableArray2 = require('babel-runtime/helpers/toConsumableArray');

var _toConsumableArray3 = _interopRequireDefault(_toConsumableArray2);

var _typeof2 = require('babel-runtime/helpers/typeof');

var _typeof3 = _interopRequireDefault(_typeof2);

var _assign = require('babel-runtime/core-js/object/assign');

var _assign2 = _interopRequireDefault(_assign);

var _stringify = require('babel-runtime/core-js/json/stringify');

var _stringify2 = _interopRequireDefault(_stringify);

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
 * Finding the right folder in which to run npm prefix
 *
 * @return {string} - The absolute path to the folder of your host package.json
 */


//--------------------------------------------------------------------------------------------------------------------------------------------------------------
// Included modules
//--------------------------------------------------------------------------------------------------------------------------------------------------------------
var Settings = exports.Settings = {
	/**
  * Getting global setting from the settings.json file
  *
  * @return {object} - The settings object
  */
	GetGlobal: function GetGlobal() {
		var root = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : __dirname;

		_logging.Log.verbose('Getting global settings from ' + _logging.Style.yellow(_path2.default.normalize(root + '/../settings.json')));

		var SETTINGS = {};

		try {
			SETTINGS = JSON.parse(_fs2.default.readFileSync(_path2.default.normalize(root + '/../settings.json'), 'utf8'));
		} catch (error) {
			_logging.Log.error('Couldn\u2019t read global settings :(');

			_logging.Log.space();
			process.exit(1);
		}

		_logging.Log.verbose(_logging.Style.yellow((0, _stringify2.default)(SETTINGS)));

		return SETTINGS;
	},

	/**
  * Getting local setting from the host package.json file
  *
  * @param  {string} cwd - The path to our host package.json
  *
  * @return {object}     - The settings object
  */
	GetLocal: function GetLocal(cwd) {
		_logging.Log.verbose('Getting local settings');

		var SETTINGS = {};

		try {
			SETTINGS = JSON.parse(_fs2.default.readFileSync(_path2.default.normalize(cwd + '/package.json'), 'utf8'));
		} catch (error) {
			_logging.Log.error('Couldn\u2019t read local settings :(');
			_logging.Log.error('Make sure you have a package.json file availabe in the root of your project.');
			_logging.Log.error(error);

			_logging.Log.space();
			process.exit(1);
		}

		if (SETTINGS.pancake === undefined) {
			SETTINGS.pancake = {};
		}

		//default settings
		var defaultSettings = {
			'auto-save': true,
			plugins: true,
			ignore: []
		};

		//merging default settings with local package.json
		SETTINGS.pancake = (0, _assign2.default)(defaultSettings, SETTINGS.pancake);

		_logging.Log.verbose(_logging.Style.yellow((0, _stringify2.default)(SETTINGS.pancake)));

		return SETTINGS.pancake;
	},

	/**
  * Writing new global settings to the settings.json file
  *
  * @param  {object} SETTINGS - The settings object so we don’t have to read it twice
  * @param  {array}  items    - The setting to be changed, first item is the name and the following the values
  *
  * @return {object}          - The settings object with the new setting
  */
	SetGlobal: function SetGlobal(root, SETTINGS) {
		for (var _len = arguments.length, items = Array(_len > 2 ? _len - 2 : 0), _key = 2; _key < _len; _key++) {
			items[_key - 2] = arguments[_key];
		}

		_logging.Log.info('PANCAKE SAVING DEFAULT SETTING');

		var setting = items[0];
		var value = items.splice(1);

		if (SETTINGS[setting] !== undefined) {
			//setting new value
			if ((0, _typeof3.default)(SETTINGS[setting]) === 'object') {
				var _SETTINGS$setting;

				(_SETTINGS$setting = SETTINGS[setting]).push.apply(_SETTINGS$setting, (0, _toConsumableArray3.default)(value));
			}

			if (typeof SETTINGS[setting] === 'boolean') {
				SETTINGS[setting] = value === "true";
			}

			if (typeof SETTINGS[setting] === 'string') {
				SETTINGS[setting] = value.toString();
			}

			try {
				_fs2.default.writeFileSync(_path2.default.normalize(root + '/../settings.json'), (0, _stringify2.default)(SETTINGS, null, '\t'), 'utf8');

				_logging.Log.ok('Value for ' + _logging.Style.yellow(setting) + ' saved');
			} catch (error) {
				_logging.Log.error('Saving settings failed');
				_logging.Log.error(error);
			}
		} else {
			_logging.Log.error('Setting ' + _logging.Style.yellow(setting) + ' not found');
		}
	},

	/**
  * Writing local settings to the package.json file
  *
  * @param  {object} SETTINGS - The settings object to be written
  * @param  {string} pkgPath  - The path to the package.json file
  *
  * @return {Promise object}  - The settings object with the new setting
  */
	SetLocal: function SetLocal(SETTINGS, pkgPath) {
		_logging.Log.info('PANCAKE SAVING LOCAL SETTINGS');

		return new _promise2.default(function (resolve, reject) {

			var PackagePath = _path2.default.normalize(pkgPath + '/package.json');
			var PKGsource = void 0;
			var PKG = void 0;

			try {
				PKGsource = _fs2.default.readFileSync(PackagePath, 'utf8');
				PKG = JSON.parse(PKGsource);

				_logging.Log.verbose('Read settings at ' + _logging.Style.yellow(PackagePath));
			} catch (error) {
				_logging.Log.verbose('No package.json found at ' + _logging.Style.yellow(PackagePath));
			}

			//only save stuff if we have a package.json file to write to
			if (PKGsource.length > 0) {

				//detect indentation
				var _isSpaces = void 0;

				var indentation = 2; //default indentation even though you all should be using tabs for indentation!
				try {
					var PKGlines = PKGsource.split('\n');
					_isSpaces = PKGlines[1].startsWith('  ');
				} catch (error) {
					_isSpaces = true; //buuuhhhhhh 👎
				}

				if (!_isSpaces) {
					indentation = '\t'; //here we go!
				}

				PKG.pancake = SETTINGS; //set our settings

				(0, _files.WriteFile)(PackagePath, (0, _stringify2.default)(PKG, null, indentation)) //write to package.json
				.catch(function (error) {
					_logging.Log.error(error);

					reject(error);
				}).then(function (written) {
					resolve(SETTINGS);
				});
			}
		});
	}
};