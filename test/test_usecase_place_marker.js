var assert = require('assert');
var sinon = require('sinon');

var sut_module = require('../export/modules/javascript/usecase_place_marker.js');

describe('Use case choose place marker', function () {
    beforeEach(function() {
        tile = {subscribe: sinon.spy(), unsubscribe: sinon.spy(),};
        canvas = {add: sinon.spy(), remove: sinon.spy(), get:sinon.stub().returns(tile), };
        ui = {paint: sinon.spy(), };
        sut = new sut_module({ui: ui, pyramid: canvas});
        subscribe = sinon.spy();
    });
    describe('set_candidate_tile_specifications', function () {
        function act(candidate_tile_specifications) {
            sut.set_candidate_tile_specifications(candidate_tile_specifications);
        };
        it('retrieves tile', function () {
            // Arrange
            // Act
            act([{unique_id: 3}]);
            // Assert
            assert.equal(canvas.get.getCall(0).args[0], 3);
        });
        it('retrieves tiles', function () {
            // Arrange
            // Act
            act([{unique_id: 3}, {unique_id: 5}]);
            // Assert
            sinon.assert.callCount(canvas.get, 2);
        });
    });
    describe('Subscribe', function () {
        beforeEach(function() {
            callback_object = {
                marker_selected: sinon.spy(),
            };
        });
        function arrange(candidate_tile_specifications) {
            sut.set_candidate_tile_specifications(candidate_tile_specifications);
        };
        function act() {
            sut.subscribe(callback_object, 'marker_selected');
        };
        it('subscribes to the tile', function () {
            // Arrange
            arrange([{unique_id: 3},]);
            // Act
            act();
            // Assert
            assert.equal(tile.subscribe.getCall(0).args[0], callback_object);
            assert.equal(tile.subscribe.getCall(0).args[1], 'marker_selected');
        });
        it('subscribes to the tile', function () {
            // Arrange
            arrange([{unique_id: 3}, {unique_id: 5}]);
            // Act
            act();
            // Assert
            sinon.assert.callCount(tile.subscribe, 2);
        });
    });
    describe('Unsubscribe', function () {
        beforeEach(function() {
            callback_object = {
                marker_selected: sinon.spy(),
            };
        });
        function arrange(candidate_tile_specifications) {
            sut.set_candidate_tile_specifications(candidate_tile_specifications);
            sut.subscribe(callback_object, 'marker_selected');
        };
        function act() {
            sut.unsubscribe();
        };
        it('unsubscribes to the tile', function () {
            // Arrange
            arrange([{unique_id: 3},]);
            // Act
            act();
            // Assert
            assert.equal(tile.unsubscribe.getCall(0).args[0], callback_object);
            assert.equal(tile.unsubscribe.getCall(0).args[1], 'marker_selected');
        });
        it('unsubscribes to the tile', function () {
            // Arrange
            arrange([{unique_id: 3}, {unique_id: 5}]);
            // Act
            act();
            // Assert
            sinon.assert.callCount(tile.unsubscribe, 2);
        });
    });
});
