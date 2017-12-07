/***************************************************************************************************************************************************************
 *
 * Helper methods
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
// import { Log, Style } from '@gov.au/pancake';


//--------------------------------------------------------------------------------------------------------------------------------------------------------------
// Default export
//--------------------------------------------------------------------------------------------------------------------------------------------------------------
/**
 * Remove duplicated lines
 *
 * @param  {string} content - A bunch of lines that COULD have duplicates
 *
 * @return {string}         - Removed duplicate lines
 */

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.StripDuplicateLines = undefined;

var _set = require('babel-runtime/core-js/set');

var _set2 = _interopRequireDefault(_set);

var _toConsumableArray2 = require('babel-runtime/helpers/toConsumableArray');

var _toConsumableArray3 = _interopRequireDefault(_toConsumableArray2);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

var StripDuplicateLines = exports.StripDuplicateLines = function StripDuplicateLines(content) {
  var lines = content.split('\n'); //split into each line

  if (lines[lines.length - 1] === '') {
    //remove empty line at the end
    lines.pop();
  }

  var sortedLines = [].concat((0, _toConsumableArray3.default)(new _set2.default(lines))); //make each line unique

  return sortedLines.join('\n');
};