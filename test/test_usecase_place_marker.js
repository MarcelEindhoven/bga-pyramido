var assert = require('assert');
var sinon = require('sinon');

var sut_module = require('../export/modules/javascript/usecase_place_marker.js');

describe('Use case choose place marker', function () {
    beforeEach(function() {
        market = {subscribe_to_quarry: sinon.spy(), unsubscribe: sinon.spy(),};
        canvas = {add: sinon.spy(), remove: sinon.spy(), };
        ui = {paint: sinon.spy(), };
        sut = new sut_module({ui: ui, pyramid: canvas});
        subscribe = sinon.spy();
    });
    describe('Subscribe', function () {
        beforeEach(function() {
            callback_object = {
                marker_selected: sinon.spy(),
            };
        });
        function act() {
            sut.subscribe(callback_object, 'marker_selected');
        };
        it('subscribes to the market', function () {
            // Arrange
            
            // Act
            act();
            // Assert
        });
    });
    describe('Unsubscribe', function () {
        beforeEach(function() {
            callback_object = {
                marker_selected: sinon.spy(),
            };
            sut.subscribe(callback_object, 'marker_selected');
        });
        function arrange() {
        };
        function act() {
            sut.unsubscribe();
        };
        it('unsubscribes from market', function () {
            // Arrange
            // Act
            act();
            // Assert
        });
    });
});
