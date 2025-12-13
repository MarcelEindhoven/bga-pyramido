<?php
namespace Bga\Games\Pyramido\Domain;
/**
 *------
 * Pyramido implementation unit tests : © Marcel van Nieuwenhoven marcel.eindhoven@hotmail.com
 *
 */

include_once(__DIR__.'/../../vendor/autoload.php');
use PHPUnit\Framework\TestCase;

include_once(__DIR__.'/../../export/modules/php/Domain/DominoHorizontalVertical.php');

include_once(__DIR__.'/../_ide_helper.php');
use Bga\Games\FrameworkInterfaces;

class DominoHorizontalVerticalTest extends TestCase{
    /** 5x4 tiles, Last coordinates are the coordinates of the bottom right tile */
    const FIRST_STAGE_BOUNDING_BOX = [10, 10, 18, 16];
    const STAGE_1_TOP_LEFT = [10,10];
    const STAGE_1_BOTTOM_RIGHT_44 = [16,16];
    const STAGE_1_BOTTOM_RIGHT_54 = [18,16];
    const STAGE_4_LEFT = [13,13];
    const STAGE_4_RIGHT = [15,13];
    const STAGE_3_TOP_LEFT_DOMINO = [[12,12], [14,12]];
    const STAGE_3_BOTTOM_RIGHT_DOMINO = [[14,14], [16,14]];

    protected ?DominoHorizontalVertical $sut = null;

    protected function setUp(): void {
    }

    public function test_create_stage_domino() {
        // Arrange
        $horizontal1 = 5;
        $vertical1 = 7;
        $horizontal2 = 5;
        $vertical2 = 9;

        // Act
        $sut = DominoHorizontalVertical::create_from_coordinates([$horizontal1, $vertical1], [$horizontal2, $vertical2]);

        // Assert
        $this->assertEquals($horizontal1, $sut[0]['horizontal']);
        $this->assertEquals($vertical1, $sut[0]['vertical']);
        $this->assertEquals($horizontal2, $sut[1]['horizontal']);
        $this->assertEquals($vertical2, $sut[1]['vertical']);
    }

    public function test_key_not_swapped() {
        // Arrange
        $horizontal1 = 5;
        $vertical1 = 7;
        $horizontal2 = 5;
        $vertical2 = 9;
        $sut = DominoHorizontalVertical::create_from_coordinates([$horizontal1, $vertical1], [$horizontal2, $vertical2]);
        $expected_key = StageTilePosition::create($horizontal1, $vertical1)->key() +
                        StageTilePosition::FACTOR_HORIZONTAL * StageTilePosition::FACTOR_HORIZONTAL * 
                        StageTilePosition::create($horizontal2, $vertical2)->key();

        // Act
        $key = $sut->key();

        // Assert
        $this->assertEquals($expected_key, $key);
    }

    public function test_key_swapped() {
        // Arrange
        $horizontal1 = 5;
        $vertical1 = 7;
        $horizontal2 = 5;
        $vertical2 = 9;
        $sut = DominoHorizontalVertical::create_from_coordinates([$horizontal2, $vertical2], [$horizontal1, $vertical1]);
        $expected_key = StageTilePosition::create($horizontal1, $vertical1)->key() +
                        StageTilePosition::FACTOR_HORIZONTAL * StageTilePosition::FACTOR_HORIZONTAL * 
                        StageTilePosition::create($horizontal2, $vertical2)->key();

        // Act
        $key = $sut->key();

        // Assert
        $this->assertEquals($expected_key, $key);
    }

    public function test_create_horizontal_dominoes() {
        // Arrange
        $stage = 3;
        $horizontal1 = 3;
        $vertical1 = 7;
        $horizontal2 = 5;
        $vertical2 = 7;
        $sut = DominoHorizontalVertical::create_from_coordinates([$horizontal1, $vertical1], [$horizontal2, $vertical2]);

        // Act
        $dominoes = $sut->create_dominoes_with_rotation($stage);

        // Assert
        $this->assertEquals($horizontal1, $dominoes[0]['horizontal']);
        $this->assertEquals($vertical1, $dominoes[0]['vertical']);
        $this->assertEquals($stage, $dominoes[0]['stage']);
        $this->assertEquals(0, $dominoes[0]['rotation']);

        $this->assertEquals($horizontal2, $dominoes[1]['horizontal']);
        $this->assertEquals($vertical2, $dominoes[1]['vertical']);
        $this->assertEquals(2, $dominoes[1]['rotation']);
    }

    public function test_create_vertical_dominoes() {
        // Arrange
        $stage = 2;
        $horizontal1 = 5;
        $vertical1 = 7;
        $horizontal2 = 5;
        $vertical2 = 9;
        $sut = DominoHorizontalVertical::create_from_coordinates([$horizontal1, $vertical1], [$horizontal2, $vertical2]);

        // Act
        $dominoes = $sut->create_dominoes_with_rotation($stage);

        // Assert
        $this->assertEquals($horizontal1, $dominoes[0]['horizontal']);
        $this->assertEquals($vertical1, $dominoes[0]['vertical']);
        $this->assertEquals($stage, $dominoes[0]['stage']);
        $this->assertEquals(1, $dominoes[0]['rotation']);

        $this->assertEquals($horizontal2, $dominoes[1]['horizontal']);
        $this->assertEquals($vertical2, $dominoes[1]['vertical']);
        $this->assertEquals($stage, $dominoes[1]['stage']);
        $this->assertEquals(3, $dominoes[1]['rotation']);
    }
}
?>
