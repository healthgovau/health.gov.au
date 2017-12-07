/***************************************************************************************************************************************************************
 *
 * Parse the arguments of the cli
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
// import Path from 'path';
// import Fs from 'fs';


//--------------------------------------------------------------------------------------------------------------------------------------------------------------
// Included modules
//--------------------------------------------------------------------------------------------------------------------------------------------------------------

Object.defineProperty(exports, "__esModule", {
	value: true
});
exports.ParseArgs = undefined;

var _stringify = require('babel-runtime/core-js/json/stringify');

var _stringify2 = _interopRequireDefault(_stringify);

var _toConsumableArray2 = require('babel-runtime/helpers/toConsumableArray');

var _toConsumableArray3 = _interopRequireDefault(_toConsumableArray2);

var _typeof2 = require('babel-runtime/helpers/typeof');

var _typeof3 = _interopRequireDefault(_typeof2);

var _keys = require('babel-runtime/core-js/object/keys');

var _keys2 = _interopRequireDefault(_keys);

var _getIterator2 = require('babel-runtime/core-js/get-iterator');

var _getIterator3 = _interopRequireDefault(_getIterator2);

var _logging = require('./logging');

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

//--------------------------------------------------------------------------------------------------------------------------------------------------------------
// Default export
//--------------------------------------------------------------------------------------------------------------------------------------------------------------
/**
 * Parsing arguments coming form the cli
 *
 * @param  {object} SETTINGS - The global settings object
 * @param  {array}  args     - The arguments passed to the program, defaults to process.argv
 *
 * @return {object}          - The defaults merged with the parsed arguments
 */
var ParseArgs = exports.ParseArgs = function ParseArgs(SETTINGS) {
	var args = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : process.argv;

	_logging.Log.verbose('Cli arguments: ' + _logging.Style.yellow(args.slice(2).join(', ')));

	var allowed = { //all allowed commands
		'--version': { //the long version of the argument
			name: 'version', //the name of the argument to map to it’s defaults
			short: '-V', //the shortcut of this argument
			options: 0 },
		'--verbose': {
			name: 'verbose',
			short: '-v',
			options: 0
		},
		'--nosave': {
			name: 'nosave',
			short: '-n',
			options: 0
		},
		'--set': {
			name: 'set',
			short: '-s',
			options: 2
		},
		'--org': {
			name: 'org',
			short: '-o',
			options: 1
		},
		'--json': {
			name: 'json',
			short: '-j',
			options: 1
		},
		'--noplugins': {
			name: 'plugins',
			short: '-p',
			options: 0
		},
		'--ignore': {
			name: 'ignorePlugins',
			short: '-i',
			options: 1
		},
		'--help': {
			name: 'help',
			short: '-h',
			options: 0
		}
	};

	var defaults = { //we need to return these
		cwd: undefined,
		version: false,
		verbose: false,
		nosave: false,
		set: [],
		org: SETTINGS.npmOrg,
		json: SETTINGS.json,
		plugins: true,
		ignorePlugins: [],
		help: false
	};

	var index = 2; //the first two arguments are always the path to node and the path to this app

	if (args.length > 2) {
		//if there are even any arguments passed

		//optional argument in first place for cwd overwrite
		if (!args[2].startsWith('-')) {
			defaults.cwd = args[2];

			index = 3; //move right along
		}

		//now parse each argument
		for (index; index < args.length; index++) {
			var arg = args[index];

			//maybe we are using the shortcut?
			if (allowed[arg] === undefined) {
				var _iteratorNormalCompletion = true;
				var _didIteratorError = false;
				var _iteratorError = undefined;

				try {
					for (var _iterator = (0, _getIterator3.default)((0, _keys2.default)(allowed)), _step; !(_iteratorNormalCompletion = (_step = _iterator.next()).done); _iteratorNormalCompletion = true) {
						var key = _step.value;

						if (allowed[key].short === arg) {
							arg = key; //use long version from here on out
							break;
						}
					}
				} catch (err) {
					_didIteratorError = true;
					_iteratorError = err;
				} finally {
					try {
						if (!_iteratorNormalCompletion && _iterator.return) {
							_iterator.return();
						}
					} finally {
						if (_didIteratorError) {
							throw _iteratorError;
						}
					}
				}

				;
			}

			//have we found this argument now?
			if (allowed[arg] === undefined) {
				_logging.Log.error('There is no such option as ' + _logging.Style.yellow(arg));
				_logging.Log.error('The available options are:\n            ' + _logging.Style.yellow((0, _keys2.default)(allowed).join(', ')));
			} else {
				//argument found
				var command = allowed[arg];

				if (command.options > 0) {
					//flag with options
					for (var i = 0; i < command.options; i++) {
						//iterating over the options by moving along in the process.argv array
						index++;

						if (args[index] === undefined) {
							_logging.Log.error('There are some missings options in the commande ' + _logging.Style.yellow(arg));
						} else {
							if ((0, _typeof3.default)(defaults[command.name]) === 'object') {
								//the defaults make this as an object/array
								if (args[index].includes(',')) {
									var _defaults$command$nam;

									//the passing argument includes a comma so we split it
									(_defaults$command$nam = defaults[command.name]).push.apply(_defaults$command$nam, (0, _toConsumableArray3.default)(args[index].split(','))); //adding to defaults
								} else {
									defaults[command.name].push(args[index]); //adding to defaults
								}
							}

							if (typeof defaults[command.name] === 'string') {
								//the defaults mark this as a string
								defaults[command.name] = args[index]; //set in defaults
							}
						}
					}
				} else {
					//boolean flag without options
					defaults[command.name] = !defaults[command.name]; //invert the default
				}
			}
		}
	}

	_logging.Log.verbose('Parsed arguments:\n' + _logging.Style.yellow((0, _stringify2.default)(defaults)));

	return defaults;
};