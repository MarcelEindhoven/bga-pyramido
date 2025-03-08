var assert = require('assert');
var sinon = require('sinon');

var sut_module = require('../export/modules/javascript/usecase_place_domino.js');

class DominoFactoryx {
    create_domino_from(domino_specification) {
        create_domino_fromx(domino_specification);
        domino = {};
        for (var property in domino_specification) {
            domino[property] = domino_specification[property];
        }
        domino.create_canvas_token = create_canvas_token;
        domino.destroy_canvas_token = destroy_canvas_token;
        domino.subscribe = subscribe;
        return domino;
    }
}

describe('Use case choose place domino', function () {
    beforeEach(function() {
        market = {subscribe_to_quarry: sinon.spy(), unsubscribe: sinon.spy(),};
        canvas = {add: sinon.spy(), remove: sinon.spy(), };
        ui = {paint: sinon.spy(), };
        sut = new sut_module({ui: ui, market: market, pyramid: canvas, domino_factory: new DominoFactoryx()});
        stock = {control_name: "quarry-2"};

        create_domino_fromx = sinon.spy();
        create_canvas_token = sinon.spy();
        destroy_canvas_token = sinon.spy();
        subscribe = sinon.spy();
    });
    describe('Subscribe', function () {
        beforeEach(function() {
            callback_object = {
                domino_selected: sinon.spy(),
            };
        });
        function act() {
            sut.subscribe(callback_object, 'domino_selected');
        };
        it('subscribes to the market', function () {
            // Arrange
            
            // Act
            act();
            // Assert
            sinon.assert.callCount(market.subscribe_to_quarry, 1);
            assert.equal(market.subscribe_to_quarry.getCall(0).args[0], sut);
            assert.equal(market.subscribe_to_quarry.getCall(0).args[1], 'quarry_selected');
        });
    });
    describe('Quarry selected', function () {
        beforeEach(function() {
            callback_object = {
                domino_selected: sinon.spy(),
            };
            sut.subscribe(callback_object, 'domino_selected');
        });
        function act() {
            domino = {id: 33, unique_id: 'domino'};
            sut.quarry_selected(domino);
        };
        it('does not call create_domino_from without positions', function () {
            // Arrange
            sut.set_candidate_positions([]);
            // Act
            act();
            // Assert
            sinon.assert.callCount(create_domino_fromx, 0);
        });
        it('destroys previous candidates when new domino selected', function () {
            // Arrange
            sut.set_candidate_positions([
                {horizontal: 10, vertical: 10, rotation: 0},
                {horizontal: 14, vertical: 10, rotation: 0},
            ]);
            // Act
            act();
            act();
            // Assert
            sinon.assert.callCount(destroy_canvas_token, 2);
        });
        it('calls create_domino_from', function () {
            // Arrange
            sut.set_candidate_positions([
                {horizontal: 10, vertical: 10, rotation: 0},
                {horizontal: 14, vertical: 10, rotation: 0},
            ]);
            // Act
            act();
            // Assert
            sinon.assert.callCount(create_domino_fromx, 2);
        });
        it('calls create_domino_from with domino', function () {
            // Arrange
            sut.set_candidate_positions([{horizontal: 10, vertical: 10, rotation: 0},]);
            // Act
            act();
            // Assert
            assert.equal(create_domino_fromx.getCall(0).args[0].unique_id, 'domino');
        });
        it('calls canvas add with domino with unique id', function () {
            // Arrange
            sut.set_candidate_positions([{horizontal: 10, vertical: 11, rotation: 0},]);
            // Act
            act();
            // Assert
            candidate_domino = canvas.add.getCall(0).args[0];
            assert.equal(candidate_domino.unique_id, 'domino1011');
        });
        it('calls canvas add with domino with position', function () {
            // Arrange
            sut.set_candidate_positions([{horizontal: 14, vertical: 11, rotation: 0},]);
            // Act
            act();
            // Assert
            candidate_domino = canvas.add.getCall(0).args[0];
            assert.equal(candidate_domino.horizontal, 14);
            assert.equal(candidate_domino.vertical, 11);
            assert.equal(candidate_domino.rotation, 0);
        });
        it('calls canvas add with highest stage', function () {
            // Arrange
            sut.set_candidate_positions([{horizontal: 14, vertical: 11, rotation: 0},]);
            // Act
            act();
            // Assert
            candidate_domino = canvas.add.getCall(0).args[0];
            assert.ok(candidate_domino.stage >= 5);
        });
        it('only for matching rotation', function () {
            // Arrange
            sut.set_candidate_positions([
                {horizontal: 10, vertical: 11, rotation: 0},
                {horizontal: 14, vertical: 14, rotation: 1},
            ]);
            // Act
            sut.rotate();
            act();
            // Assert
            sinon.assert.callCount(create_domino_fromx, 1);
            sinon.assert.callCount(canvas.add, 1);

            candidate_domino = canvas.add.getCall(0).args[0];
            assert.equal(candidate_domino.unique_id, 'domino1414');
        });
    });
    describe('Rotate', function () {
        beforeEach(function() {
            callback_object = {
                domino_selected: sinon.spy(),
            };
            sut.subscribe(callback_object, 'domino_selected');
        });
        function arrange_selected_domino() {
            sut.subscribe(callback_object, 'domino_selected');
            domino = {id: 33, unique_id: 'domino'};
            sut.quarry_selected(domino);
        };
        function act() {
            sut.rotate();
        };
        it('calls paint if domino has been selected', function () {
            // Arrange
            domino = {id: 33, unique_id: 'domino'};
            sut.quarry_selected(domino);
            // Act
            act();
            // Assert
            sinon.assert.callCount(ui.paint, 1 + 1);
        });
        it('does not create dominoes if no domino was selected', function () {
            // Arrange
            sut.set_candidate_positions([
                {horizontal: 12, vertical: 13, rotation: 1},
            ]);
            // Act
            act();
            // Assert
            sinon.assert.callCount(canvas.add, 0);
        });
        it('creates domino if domino was selected', function () {
            // Arrange
            sut.set_candidate_positions([
                {horizontal: 12, vertical: 11, rotation: 1},
                {horizontal: 10, vertical: 11, rotation: 1},
            ]);
            arrange_selected_domino();
            // Act
            act();
            // Assert
            sinon.assert.callCount(canvas.add, 2);
        });
        it('destroys existing candidate dominoes', function () {
            // Arrange
            sut.set_candidate_positions([
                {horizontal: 11, vertical: 11, rotation: 0},
                {horizontal: 10, vertical: 11, rotation: 0},
            ]);
            arrange_selected_domino();
            // Act
            act();
            // Assert
            sinon.assert.callCount(canvas.remove, 2);
            sinon.assert.callCount(destroy_canvas_token, 2);
        });
        it('removes existing candidate dominoes', function () {
            // Arrange
            sut.set_candidate_positions([
                {horizontal: 11, vertical: 11, rotation: 0},
                {horizontal: 11, vertical: 14, rotation: 0},
                {horizontal: 11, vertical: 15, rotation: 0},
                {horizontal: 11, vertical: 18, rotation: 0},
                {horizontal: 11, vertical: 19, rotation: 0},
            ]);
            arrange_selected_domino();
            // Act
            act();
            act();
            // Assert
            sinon.assert.callCount(canvas.remove, 5);
        });
    });
    describe('placement selected', function () {
        beforeEach(function() {
            callback_object = {
                domino_selected: sinon.spy(),
            };
            sut.subscribe(callback_object, 'domino_selected');
        });
        function act() {
            domino = 'domino';
            sut.placement_selected(domino);
        };
        it('calls subscriber', function () {
            // Arrange
            // Act
            act();
            // Assert
            sinon.assert.callCount(callback_object.domino_selected, 1);
        });
        it('calls subscriber with domino', function () {
            // Arrange
            // Act
            act();
            // Assert
            assert.equal(callback_object.domino_selected.getCall(0).args[0], domino);
        });
    });
    describe('Unsubscribe', function () {
        beforeEach(function() {
            callback_object = {
                domino_selected: sinon.spy(),
            };
            sut.subscribe(callback_object, 'domino_selected');
            domino = {id: 33, unique_id: 'domino'};
            sut.quarry_selected(domino);
        });
        function arrange() {
            domino = {id: 33, unique_id: 'domino'};
            sut.quarry_selected(domino);
        };
        function act() {
            sut.unsubscribe();
        };
        it('unsubscribes from market', function () {
            // Arrange
            // Act
            act();
            // Assert
            sinon.assert.callCount(market.unsubscribe, 1);
        });
        it('destroys previous candidates', function () {
            // Arrange
            sut.set_candidate_positions([
                {horizontal: 10, vertical: 10, rotation: 0},
                {horizontal: 11, vertical: 10, rotation: 0},
            ]);
            arrange();
            // Act
            act();
            // Assert
            sinon.assert.callCount(destroy_canvas_token, 2);
        });
    });
});
