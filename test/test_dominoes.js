var assert = require('assert');
var sinon = require('sinon');

var sut_module = require('../export/modules/javascript/dominoes.js');

class Document {
    getElementById(element_id) {return {insertAdjacentHTML: sinon.spy(),};}
}
class Animation {
    play () {}
}
describe('Dominoes', function () {
    beforeEach(function() {
        dojo = {style:sinon.spy(), connect:sinon.spy(), destroy:sinon.spy(), addClass:sinon.spy(), removeClass:sinon.spy(), };
        document = new Document();
        game = {get_element:sinon.stub().returns(44), slideToObjectPos:sinon.stub().returns (new Animation ()) ,};
        dependencies = {dojo: dojo, document: document, game:game, };
        sut = new sut_module(dependencies);
        domino_specification = {id: 0, stage: 0, horizontal: 10,vertical: 12, rotation: 0};
        element_id = 'HTML element ID ';
        x = 12;
        y = 33;

    });
    describe('Create domino from specification', function () {
        it('copies the input parameters', function () {
            // Arrange
            domino_specification.test_parameter = dojo;
            // Act
            domino = sut.create_domino_from(domino_specification);
            // Assert
            assert.equal(domino.test_parameter, domino_specification.test_parameter);
        });
        it('creates unique ID', function () {
            // Arrange
            domino_specification.id = 33;
            // Act
            domino = sut.create_domino_from(domino_specification);
            // Assert
            assert.equal(domino.unique_id, 'domino-33');
        });
    });
    describe('Subscribe', function () {
        beforeEach(function() {
            callback_object = {
                domino_selected: sinon.spy(),
            };
            domino = sut.create_domino_from(domino_specification);
        });
        function act() {
            domino.subscribe(callback_object, 'domino_selected');
        };
        it('connects for onclick, onmouseover, onmouseout', function () {
            // Arrange
            
            // Act
            act();
            // Assert
            sinon.assert.callCount(dojo.connect, 3);
            assert.equal(dojo.connect.getCall(0).args[0], 44);
        });
    });
    describe('Cleanup', function () {
        it('uses the framework to destroy the UI token', function () {
            // Arrange
            domino_specification.id = 33;
            domino = sut.create_domino_from(domino_specification);
            // Act
            domino.destroy_canvas_token();
            // Assert
            assert.equal(dojo.destroy.getCall(0).args[0], 'domino-33');
        });
    });
    describe('Bounding box', function () {
        function act_default(rotation) {
            domino_specification.rotation = rotation;
            domino = sut.create_domino_from(domino_specification);
            // Act
            return domino.get_bounding_box();
        }
        it('returns bounding box for rotation 0', function () {
            // Arrange
            // Act
            bounding_box = act_default(0);
            // Assert
            assert.equal(bounding_box.horizontal_min, 10 - 2);
            assert.equal(bounding_box.vertical_min, 12 - 1);
            assert.equal(bounding_box.horizontal_max, 10 + 2);
            assert.equal(bounding_box.vertical_max, 12 + 1);
        });
        it('returns bounding box for rotation 1', function () {
            // Arrange
            // Act
            bounding_box = act_default(1);
            // Assert
            assert.equal(bounding_box.horizontal_min, 10 - 2);
            assert.equal(bounding_box.vertical_min, 12 - 1);
            assert.equal(bounding_box.horizontal_max, 10 + 0);
            assert.equal(bounding_box.vertical_max, 12 + 3);
        });
        it('returns bounding box for rotation 2', function () {
            // Arrange
            // Act
            bounding_box = act_default(2);
            // Assert
            assert.equal(bounding_box.horizontal_min, 10 - 4);
            assert.equal(bounding_box.vertical_min, 12 - 1);
            assert.equal(bounding_box.horizontal_max, 10 + 0);
            assert.equal(bounding_box.vertical_max, 12 + 1);
        });
        it('returns bounding box for rotation 3', function () {
            // Arrange
            // Act
            bounding_box = act_default(3);
            // Assert
            assert.equal(bounding_box.horizontal_min, 10 - 2);
            assert.equal(bounding_box.vertical_min, 12 - 3);
            assert.equal(bounding_box.horizontal_max, 10 + 0);
            assert.equal(bounding_box.vertical_max, 12 + 1);
        });
        it('returns updated bounding box after changing properties', function () {
            // Arrange
            // Act
            bounding_box = act_default(2);
            domino.horizontal = 16;
            domino.vertical = 6;
            domino.rotation = 3;
            // Act
            bounding_box = domino.get_bounding_box();
            // Assert
            assert.equal(bounding_box.horizontal_min, 16 - 2);
            assert.equal(bounding_box.vertical_min, 6 - 3);
            assert.equal(bounding_box.horizontal_max, 16 + 0);
            assert.equal(bounding_box.vertical_max, 6 + 1);
        });
    });
});
