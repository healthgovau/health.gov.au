/***************************************************************************************************************************************************************
 *
 * Install and run plugins
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
exports.RunPlugins = exports.InstallPlugins = undefined;

var _toConsumableArray2 = require('babel-runtime/helpers/toConsumableArray');

var _toConsumableArray3 = _interopRequireDefault(_toConsumableArray2);

var _promise = require('babel-runtime/core-js/promise');

var _promise2 = _interopRequireDefault(_promise);

var _path = require('path');

var _path2 = _interopRequireDefault(_path);

var _logging = require('./logging');

var _helpers = require('./helpers');

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

//--------------------------------------------------------------------------------------------------------------------------------------------------------------
// Default export
//--------------------------------------------------------------------------------------------------------------------------------------------------------------
/**
 * Check if plugins exist and install if not
 *
 * @param  {array}  plugins  - An array of plugin names
 * @param  {string} cwd      - The path to our working directory
 *
 * @return {promise object}  - Return an object listing plugins installed and plugins found
 */

// import Fs from 'fs';


//--------------------------------------------------------------------------------------------------------------------------------------------------------------
// Included modules
//--------------------------------------------------------------------------------------------------------------------------------------------------------------
var InstallPlugins = exports.InstallPlugins = function InstallPlugins(plugins, cwd) {
	var result = {
		found: [],
		installing: []
	};

	var output = false; //switch output of child process to stdout

	return new _promise2.default(function (resolve, reject) {

		//go through all plugins
		plugins.map(function (plugin) {

			try {
				require(_path2.default.normalize(cwd + '/node_modules/' + plugin));

				result.found.push(plugin);
			} catch (error) {
				result.installing.push(plugin);
			}
		});

		if (result.installing.length > 0) {
			_logging.Log.info('INSTALLING ' + result.installing.join(', '));

			//get the config so we can return them to what they were
			var cacheLockStale = _helpers.Spawning.sync('npm', ['config', 'get', 'cache-lock-stale']).stdout.toString().trim();
			var cacheLockWait = _helpers.Spawning.sync('npm', ['config', 'get', 'cache-lock-wait']).stdout.toString().trim();

			_logging.Log.verbose('Npm config was cache-lock-stale: ' + _logging.Style.yellow(cacheLockStale) + ' cache-lock-wait: ' + _logging.Style.yellow(cacheLockWait));

			//setting new config for just this install to not wait too long for the lockfiles
			_helpers.Spawning.sync('npm', ['config', 'set', 'cache-lock-stale', '10']);
			_helpers.Spawning.sync('npm', ['config', 'set', 'cache-lock-wait', '10']);

			//checking if we got yarn installed
			// const command = Spawning.sync( 'yarn', [ '--version' ] );
			// const hasYarn = command.stdout && command.stdout.toString().trim() ? true : false;
			var hasYarn = false; //disabled yarn as it has some issues

			if (!output) {
				_logging.Loading.start(); //waiting with loading to after the blocking child processes
			}

			_logging.Log.verbose('Yarn ' + _logging.Style.yellow(hasYarn ? 'was' : 'was not') + ' detected');

			var installing = void 0; //for spawning our install process

			if (output) {
				_logging.Loading.stop();
				_logging.Log.space();
			}

			//options for our child process
			var spawnOpt = { cwd: cwd };
			if (output) {
				spawnOpt = { cwd: cwd, stdio: 'inherit' };
			}

			//installing modules
			if (hasYarn) {
				_helpers.Spawning.async('yarn', ['add'].concat((0, _toConsumableArray3.default)(result.installing)), spawnOpt).catch(function (error) {
					_logging.Loading.stop();

					//return npm config to what it was before
					_helpers.Spawning.sync('npm', ['config', 'set', 'cache-lock-stale', cacheLockStale]);
					_helpers.Spawning.sync('npm', ['config', 'set', 'cache-lock-wait', cacheLockWait]);

					_logging.Log.error('Installing plugins failed');
					reject(error);
				}).then(function (data) {
					//return npm config to what it was before
					_helpers.Spawning.sync('npm', ['config', 'set', 'cache-lock-stale', cacheLockStale]);
					_helpers.Spawning.sync('npm', ['config', 'set', 'cache-lock-wait', cacheLockWait]);

					resolve(result);
				});
			} else {
				_helpers.Spawning.async('npm', ['install', '--no-progress', '--save'].concat((0, _toConsumableArray3.default)(result.installing)), spawnOpt).catch(function (error) {
					_logging.Loading.stop();

					//return npm config to what it was before
					_helpers.Spawning.sync('npm', ['config', 'set', 'cache-lock-stale', cacheLockStale]);
					_helpers.Spawning.sync('npm', ['config', 'set', 'cache-lock-wait', cacheLockWait]);

					_logging.Log.error('Installing plugins failed');
					reject(error);
				}).then(function (data) {
					if (output) {
						_logging.Log.space();
					}

					//return npm config to what it was before
					_helpers.Spawning.sync('npm', ['config', 'set', 'cache-lock-stale', cacheLockStale]);
					_helpers.Spawning.sync('npm', ['config', 'set', 'cache-lock-wait', cacheLockWait]);

					resolve(result);
				});
			}
		} else {
			resolve(result);
		}
	});
};

/**
 * Run a bunch of plugins
 *
 * @param  {string} version       - The version of mother pancake
 * @param  {array}  plugins       - An array of plugin names
 * @param  {string} cwd           - The path to our working directory
 * @param  {array}  allModules    - An array of all modules to be passed to plugin
 * @param  {object} SETTINGSlocal - The object of our local settings
 * @param  {object} SETTINGS      - The global settings object
 *
 * @return {promise object}       - Pass on what the plugins returned
 */
var RunPlugins = exports.RunPlugins = function RunPlugins(version, plugins, cwd, allModules, SETTINGSlocal, SETTINGS) {

	_logging.Loading.stop();

	var plugin = void 0;
	var running = [];

	return new _promise2.default(function (resolve, reject) {

		//go through all plugins
		var allPlugins = plugins.map(function (plugin) {
			_logging.Log.info('ADDING TOPPINGS TO YOUR PANCAKE VIA ' + plugin);

			plugin = require(_path2.default.normalize(cwd + '/node_modules/' + plugin));

			return plugin.pancake(version, allModules, SETTINGSlocal, SETTINGS, cwd) //run ’em
			.catch(function (error) {
				_logging.Log.error(error);

				process.exit(1);
			});
		});

		_promise2.default.all(allPlugins).catch(function (error) {
			_logging.Log.error(error);

			process.exit(1);
		}).then(function (data) {
			_logging.Loading.start();

			return resolve(data); //resolve only after all plugins have run
		});
	});
};