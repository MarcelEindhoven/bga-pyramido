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
        dojo = {style:sinon.spy(), addClass:sinon.spy(), removeClass:sinon.spy(), };
        document = new Document();
        game = {slideToObjectPos:sinon.stub().returns (new Animation ()) ,};
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
            assert.equal(bounding_box.horizontal_min, 10);
            assert.equal(bounding_box.vertical_min, 12);
            assert.equal(bounding_box.horizontal_max, 14);
            assert.equal(bounding_box.vertical_max, 14);
        });
        it('returns bounding box for rotation 1', function () {
            // Arrange
            // Act
            bounding_box = act_default(1);
            // Assert
            assert.equal(bounding_box.horizontal_min, 10);
            assert.equal(bounding_box.vertical_min, 12);
            assert.equal(bounding_box.horizontal_max, 12);
            assert.equal(bounding_box.vertical_max, 16);
        });
        it('returns bounding box for rotation 2', function () {
            // Arrange
            // Act
            bounding_box = act_default(2);
            // Assert
            assert.equal(bounding_box.horizontal_min, 8);
            assert.equal(bounding_box.vertical_min, 12);
            assert.equal(bounding_box.horizontal_max, 12);
            assert.equal(bounding_box.vertical_max, 14);
        });
        it('returns bounding box for rotation 3', function () {
            // Arrange
            // Act
            bounding_box = act_default(3);
            // Assert
            assert.equal(bounding_box.horizontal_min, 10);
            assert.equal(bounding_box.vertical_min, 10);
            assert.equal(bounding_box.horizontal_max, 12);
            assert.equal(bounding_box.vertical_max, 14);
        });
    });
});
