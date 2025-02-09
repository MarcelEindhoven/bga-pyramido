var assert = require('assert');
var sinon = require('sinon');

var sut_module = require('../export/modules/javascript/market.js');

describe('Market', function () {
    beforeEach(function() {
        dojo = {connect:sinon.spy(), addClass:sinon.spy(), removeClass:sinon.spy(), };
        dependencies = {dojo: dojo, stocks:{'next0': sinon.spy(), 'quarry0': sinon.spy(), 'quarry1': sinon.spy()}};
        sut = new sut_module(dependencies);
        callback_object = {
            tile_placed: sinon.spy(),
        };

    });
    describe('Constructor', function () {
        it('subscribes to dojo from the start', function () {
            // Arrange
            // Act
            // Assert
            sinon.assert.callCount(dojo.connect, Object.keys(dependencies.stocks).length);
        });
        it('connects to dojo with the correct parameters', function () {
            // Arrange
            // Act
            // Assert
            assert.equal(dojo.connect.getCall(0).args[0], dependencies.stocks['next0']);
            assert.equal(dojo.connect.getCall(0).args[1], 'onChangeSelection');
            assert.equal(dojo.connect.getCall(0).args[2], sut);
            assert.equal(dojo.connect.getCall(0).args[3], 'domino_selected');
        });
    });
    describe('Selectable', function () {
        beforeEach(function() {
            callback_object = {
                quarry_selected: sinon.spy(),
            };
        });
        function act_default() {
            sut.subscribe_to_quarry(callback_object, 'quarry_selected');
        };
        it('makes quarry dominoes selectable when subscribed to quarry', function () {
            // Arrange
            // Act
            act_default();
            // Assert
            sinon.assert.callCount(dojo.addClass, 2);
        });
        it('uses the correct arguments for addClass', function () {
            // Arrange
            // Act
            act_default();
            // Assert
            assert.equal(dojo.addClass.getCall(1).args[0], 'quarry1');
            assert.equal(dojo.addClass.getCall(1).args[1], 'selectable');
        });
    });
    describe('Unsubscribe', function () {
        beforeEach(function() {
            callback_object = {
                quarry_selected: sinon.spy(),
            };
        });
        function arrange_default() {
            sut.subscribe_to_quarry(callback_object, 'quarry_selected');
        };
        function act_default() {
            sut.unsubscribe();
        };
        it('make unselectables when unsubscribe is called', function () {
            // Arrange
            arrange_default();
            // Act
            act_default();
            // Assert
            sinon.assert.callCount(dojo.removeClass, 2);
            assert.equal(dojo.removeClass.getCall(1).args[0], 'quarry1');
            assert.equal(dojo.removeClass.getCall(1).args[1], 'selectable');
        });
        it('removes subscription when unsubscribe is called', function () {
            // Arrange
            arrange_default();
            // Act
            act_default();
            selected_element_id = 'quarry1';
            sut.domino_selected(selected_element_id);
            // Assert
            sinon.assert.callCount(callback_object.quarry_selected, 0);
        });
    });
    describe('Domino selected', function () {
        beforeEach(function() {
            callback_object = {
                quarry_selected: sinon.spy(),
            };
        });
        function arrange_default() {
            sut.subscribe_to_quarry(callback_object, 'quarry_selected');
        };
        function act_default() {
            selected_element_id = 'quarry1';
            sut.domino_selected(selected_element_id);
        };
        it('does nothing when not subscribed', function () {
            // Arrange
            // Act
            act_default();
            // Assert
            sinon.assert.callCount(callback_object.quarry_selected, 0);
            sinon.assert.callCount(dojo.removeClass, 0);
        });
        it('calls subscriber when subscribed', function () {
            // Arrange
            arrange_default();
            // Act
            act_default();
            // Assert
            sinon.assert.callCount(callback_object.quarry_selected, 1);
        });
        it('calls subscriber with stock when subscribed', function () {
            // Arrange
            arrange_default();
            // Act
            act_default();
            // Assert
            assert.equal(callback_object.quarry_selected.getCall(0).args[0], dependencies.stocks['quarry1']);
        });
        it('does not make unselectable when subscriber is called', function () {
            // Arrange
            arrange_default();
            // Act
            act_default();
            // Assert
            sinon.assert.callCount(dojo.removeClass, 0);
        });
        it('does not remove subscription when subscriber is called', function () {
            // Arrange
            arrange_default();
            // Act
            act_default();
            act_default();
            // Assert
            sinon.assert.callCount(callback_object.quarry_selected, 2);
        });
    });
});
