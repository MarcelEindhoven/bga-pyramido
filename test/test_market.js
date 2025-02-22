var assert = require('assert');
var sinon = require('sinon');

var sut_module = require('../export/modules/javascript/market.js');

insertAdjacentHTML = sinon.spy();

class Document {
    getElementById(element_id) {return {insertAdjacentHTML: insertAdjacentHTML,};}
}

create = sinon.spy();
addItemType = sinon.spy();
count = sinon.spy();
class Stock {
    create(game, element, width, height) {
        this.element = element;
        create(game, element, width, height);
    }
    addItemType(game, element, width, height) {addItemType(game, element, width, height);}
    addToStockWithId(domino_id, card_id) {}
    count() {
        count(this.element);
        if (this.element == 'quarry-1')
            return 0;
        return 1;
    }
}

create_domino_from = sinon.spy();
class DominoFactory {
    create_domino_from(domino_specification) {
        create_domino_from(domino_specification);
        return domino_specification;
    }
}
describe('Market', function () {
    beforeEach(function() {
        game = {get_element:sinon.stub().returnsArg(0), };
        dojo = {connect:sinon.spy(), addClass:sinon.spy(), removeClass:sinon.spy(), };
        document = new Document();
        factory = new DominoFactory();
        dependencies = {dojo: dojo, };
        setup_dependencies = {dojo: dojo, document: document, stock_class: Stock, game: game, domino_factory: factory, };
        sut = new sut_module(dependencies);
        callback_object = {
            tile_placed: sinon.spy(),
        };

    });
    describe('Setup', function () {
        beforeEach(function() {
            gamedatas= {quarry: {}, next: {},};
        });
        function act() {
            sut.setup(setup_dependencies, gamedatas);
        }
        it('inserts HTML', function () {
            // Arrange
            // Act
            act();
            // Assert
            sinon.assert.callCount(insertAdjacentHTML, 1 + 3 + 4);
        });
        it('subscribes to dojo', function () {
            // Arrange
            // gamedatas.next = {2: {index: 2}, };
            // Act
            act();
            // Assert
            sinon.assert.callCount(dojo.connect, 3 + 4);
            // sinon.assert.callCount(dojo.connect, Object.keys(gamedatas.next).length);
        });
        it('connects to dojo with the correct parameters', function () {
            // Arrange
            // Act
            act();
            // Assert
            assert.equal(dojo.connect.getCall(0).args[1], 'onChangeSelection');
            assert.equal(dojo.connect.getCall(0).args[2], sut);
            assert.equal(dojo.connect.getCall(0).args[3], 'domino_selected');
        });
    });
    describe('Constructor', function () {
        it('does not subscribe0 to dojo', function () {
            // Arrange
            // Act
            // Assert
            sinon.assert.callCount(dojo.connect, 0);
        });
    });
    describe('Get missing quarry index', function () {
        function arrange() {
            gamedatas= {quarry: {}, next: {},};
            sut.setup(setup_dependencies, gamedatas);
        }
        function act() {
            return sut.get_missing_index();
        };
        it('checks count of each stock and returns index for empty count', function () {
            // Arrange
            arrange();
            // Act
            $index = act();
            // Assert
            assert.equal($index, 'quarry-1');
        });
    });
    describe('Selectable', function () {
        beforeEach(function() {
            callback_object = {
                quarry_selected: sinon.spy(),
            };
        });
        function arrange() {
            gamedatas= {quarry: {}, next: {},};
            sut.setup(setup_dependencies, gamedatas);
        }
        function act() {
            sut.subscribe_to_quarry(callback_object, 'quarry_selected');
        };
        it('makes quarry dominoes selectable when subscribed to quarry', function () {
            // Arrange
            arrange();
            // Act
            act();
            // Assert
            sinon.assert.callCount(dojo.addClass, 3);
        });
        it('uses the correct arguments for addClass', function () {
            // Arrange
            arrange();
            // Act
            act();
            // Assert
            assert.equal(dojo.addClass.getCall(1).args[0], 'quarry-2');
            assert.equal(dojo.addClass.getCall(1).args[1], 'selectable');
        });
    });
    describe('Unsubscribe', function () {
        beforeEach(function() {
            callback_object = {
                quarry_selected: sinon.spy(),
            };
            gamedatas= {quarry: {}, next: {},};
            sut.setup(setup_dependencies, gamedatas);
        });
        function arrange() {
            sut.subscribe_to_quarry(callback_object, 'quarry_selected');
        };
        function act() {
            sut.unsubscribe();
        };
        it('make unselectables when unsubscribe is called', function () {
            // Arrange
            arrange();
            // Act
            act();
            // Assert
            sinon.assert.callCount(dojo.removeClass, 3);
            assert.equal(dojo.removeClass.getCall(1).args[0], 'quarry-2');
            assert.equal(dojo.removeClass.getCall(1).args[1], 'selectable');
        });
        it('removes subscription when unsubscribe is called', function () {
            // Arrange
            arrange();
            // Act
            act();
            selected_element_id = 'quarry-1';
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
            gamedatas= {quarry: {}, next: {1: {element_id: 'quarry-1'},},};
            sut.setup(setup_dependencies, gamedatas);
        });
        function arrange() {
            sut.subscribe_to_quarry(callback_object, 'quarry_selected');
        };
        function act() {
            selected_element_id = 'quarry-1';
            sut.domino_selected(selected_element_id);
        };
        it('does nothing when not subscribed', function () {
            // Arrange
            // Act
            act();
            // Assert
            sinon.assert.callCount(callback_object.quarry_selected, 0);
            sinon.assert.callCount(dojo.removeClass, 0);
        });
        it('calls subscriber when subscribed', function () {
            // Arrange
            arrange();
            // Act
            act();
            // Assert
            sinon.assert.callCount(callback_object.quarry_selected, 1);
        });
        it('calls subscriber with domino when subscribed', function () {
            // Arrange
            arrange();
            // Act
            act();
            // Assert
            assert.equal(callback_object.quarry_selected.getCall(0).args[0], gamedatas.next[1]);
        });
        it('does not make unselectable when subscriber is called', function () {
            // Arrange
            arrange();
            // Act
            act();
            // Assert
            sinon.assert.callCount(dojo.removeClass, 0);
        });
        it('does not remove subscription when subscriber is called', function () {
            // Arrange
            arrange();
            // Act
            act();
            act();
            // Assert
            sinon.assert.callCount(callback_object.quarry_selected, 2);
        });
    });
});
