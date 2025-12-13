<?php
namespace Bga\Games\Pyramido\Domain;
/**
 *------
 * Pyramido implementation unit tests : © Marcel van Nieuwenhoven marcel.eindhoven@hotmail.com
 *
 */

include_once(__DIR__.'/../../vendor/autoload.php');
use PHPUnit\Framework\TestCase;

include_once(__DIR__.'/../../export/modules/php/Domain/StageTilePosition.php');

include_once(__DIR__.'/../_ide_helper.php');
use Bga\Games\FrameworkInterfaces;

class StageTilePositionTest extends TestCase{
    /** 5x4 tiles, Last coordinates are the coordinates of the bottom right tile */
    const FIRST_STAGE_BOUNDING_BOX = [10, 10, 18, 16];
    const STAGE_1_TOP_LEFT = [10,10];
    const STAGE_1_BOTTOM_RIGHT_44 = [16,16];
    const STAGE_1_BOTTOM_RIGHT_54 = [18,16];
    const STAGE_4_LEFT = [13,13];
    const STAGE_4_RIGHT = [15,13];
    const STAGE_3_TOP_LEFT_DOMINO = [[12,12], [14,12]];
    const STAGE_3_BOTTOM_RIGHT_DOMINO = [[14,14], [16,14]];

    protected ?StageTilePosition $sut = null;

    protected function setUp(): void {
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('position_key')]
    public function test_position_key($horizontal, $vertical, $expected_key_value) {
        // Arrange
        $sut = StageTilePosition::create($horizontal, $vertical);

        // Act
        $key_value = $sut->key();

        // Assert
        $this->assertEquals($expected_key_value, $key_value);

    }
    static public function position_key(): array {
        return [
            [0, 0, 0],
            [1, 0, 1],
            [0, 1, 100],
            [5, 3, 305],
            [20, 20, 2020],
        ];
    }

    public function test_neighbours() {
        // Arrange
        $position = [5, 11];
        $expected_positions = $this->convert_into_Tiles([[7,11], [3,11], [5,13], [5,9]]);

        $sut = StageTilePosition::create($position[0], $position[1]);

        // Act
        $positions =$sut->get_neighbours();

        // Assert
        $this->assertEqualsCanonicalizing($expected_positions, $positions);
    }

    protected function convert_into_Tiles($positions): array {
        $tile_positions = [];
        foreach($positions as $position) {
            $tile_positions[] = StageTilePosition::create($position[0], $position[1]);
        }
        return $tile_positions;
    }
    protected function convert_into_Tile($position): array {
        return ['horizontal' => $position[0], 'vertical' => $position[1]];
    }
}
?>
