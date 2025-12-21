<?php
namespace Bga\Games\Pyramido\Domain;
/**
 *------
 * Pyramido implementation unit tests : © Marcel van Nieuwenhoven marcel.eindhoven@hotmail.com
 *
 */

include_once(__DIR__.'/../../vendor/autoload.php');
use PHPUnit\Framework\TestCase;

include_once(__DIR__.'/../../export/modules/php/Domain/Stage.php');

include_once(__DIR__.'/../_ide_helper.php');
use Bga\Games\FrameworkInterfaces;

class FirstStageTilePositionsTest extends TestCase{
    /** 5x4 tiles, Last coordinates are the coordinates of the bottom right tile */
    const FIRST_STAGE_BOUNDING_BOX = [10, 10, 18, 16];
    const STAGE_1_TOP_LEFT = [10,10];
    const STAGE_1_BOTTOM_RIGHT_44 = [16,16];
    const STAGE_1_BOTTOM_RIGHT_54 = [18,16];
    const STAGE_4_LEFT = [13,13];
    const STAGE_4_RIGHT = [15,13];
    const STAGE_3_TOP_LEFT_DOMINO = [[12,12], [14,12]];
    const STAGE_3_BOTTOM_RIGHT_DOMINO = [[14,14], [16,14]];

    protected ?FirstStageTilePositions $sut = null;

    protected function setUp(): void {
    }

    public function test_first_stage_dominoes_no_tile() {
        // Arrange
        $sut = $this->create_first_stage([]);

        // Act
        $dominoes = $sut->create_candidate_dominoes();

        // Assert
        $this->assertEquals(2, count($dominoes));
        $this->assertEqualsCanonicalizing($this->convert_into_DominoHorizontalVerticals([
            [[10,10], [12,10]],
            [[10,10], [10,12]],
        ]), $dominoes);
    }

    public function test_first_stage_get_candidate_dominoes_no_tile() {
        // Arrange
        $sut = $this->create_first_stage([]);

        // Act
        $dominoes = $sut->get_candidate_dominoes();

        // Assert
        $this->assertEquals(4, count($dominoes));
    }

    public function test_first_stage_get_candidate_dominoes_twin_tile() {
        // Arrange
        $sut = $this->create_first_stage([FirstStageTilePositionsTest::STAGE_1_TOP_LEFT, [12, 10]]);

        // Act
        $dominoes = $sut->get_candidate_dominoes();

        // Assert
        $this->assertEquals(4 * 2 * 2 * 2, count($dominoes));
    }

    public function test_first_stage_dominoes_single_tile() {
        // Arrange
        $sut = $this->create_first_stage([FirstStageTilePositionsTest::STAGE_1_TOP_LEFT]);

        // Act
        $dominoes = $sut->create_candidate_dominoes();

        // Assert
        $this->assertEquals(12, count($dominoes));
        $this->assertEqualsCanonicalizing($this->convert_into_DominoHorizontalVerticals([
            [[8,8], [10,8]], // Horizontal dominoes
            [[10,8], [12,8]], 
            [[6,10], [8,10]], 
            [[12,10], [14,10]], 
            [[8,12], [10,12]],
            [[10,12], [12,12]],
            [[8,8], [8,10]], // Vertical dominoes
            [[8,10], [8,12]], 
            [[10,8], [10,6]], 
            [[10,12], [10,14]], 
            [[12,10], [12,8]], 
            [[12,12], [12,10]],
        ]), $dominoes);
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('first_stage_bounding_box')]
    public function test_first_stage_bounding_box($tile_positions, $expected_bounding_box) {
        // Arrange
        $sut = $this->create_first_stage($tile_positions);

        // Act
        $bounding_box = $sut->get_bounding_box();

        // Assert
        $this->assertEquals($expected_bounding_box, $bounding_box);
    }
    static public function first_stage_bounding_box(): array {
        return [
            [
                [FirstStageTilePositionsTest::STAGE_1_TOP_LEFT, FirstStageTilePositionsTest::STAGE_1_BOTTOM_RIGHT_44],
                [10, 10, 16, 16],
            ],
        ];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('first_empty_spaces')]
    public function test_first_empty_spaces($tile_positions, $candidate_domino, $expected_result) {
        // Arrange
        $sut = $this->create_first_stage($tile_positions);

        // Act
        $are_empty_spaces_inevitable = $sut->are_empty_spaces_inevitable($this->convert_into_Tiles($candidate_domino));

        // Assert
        $this->assertEquals($expected_result, $are_empty_spaces_inevitable);
    }
    static public function first_empty_spaces(): array {
        return [
            [FirstStageTilePositionsTest::STAGE_3_BOTTOM_RIGHT_DOMINO, FirstStageTilePositionsTest::STAGE_3_TOP_LEFT_DOMINO, false],
            [[[10,10], [16, 16]], [[16,18], [14,18]], false], // opposite 4x4 corners
            [[[10,10], [16, 16]], [[12,18], [14,18]], true], // opposite 4x4 corners
            [   [           [12, 12], [14, 12],
                                        [14, 14], [16, 14], 
                    [10,16], [12, 16], [14, 16], [16, 16], 
                ], [[14,10], [16,10]], true],
            [   [           [12, 12], [14, 12],
                                        [14, 14], [16, 14], 
                    [10,16], [12, 16], [14, 16], [16, 16], 
                ], [[12,18], [14,18]], true],
            [[[10,16], [12, 16], [14, 16], [16, 16], [14, 14], [16, 14], [12, 12], [14, 12]], [[14,10], [12,10]], false],
        ];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('all_bounding_boxes')]
    public function test_all_bounding_boxes_none($tile_positions, $bounding_boxes_expected) {
        // Arrange
        $sut = $this->create_first_stage($tile_positions);

        // Act
        $all_bounding_boxes = $sut->get_all_bounding_boxes();
        print_r($all_bounding_boxes);

        // Assert
        $this->assertEquals($bounding_boxes_expected, $all_bounding_boxes);
    }
    static public function all_bounding_boxes(): array {
        return[
            [[[10, 10], [18, 18]], []],
            [[[10, 10], [18, 16]], [[10, 10, 18, 16]]],
            [[[10, 10], [16, 18]], [[10, 10, 16, 18]]],
        ];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('are_empty_spaces_inevitable_for_neighbour')]
    public function test_are_empty_spaces_inevitable_for_neighbour($tile_positions, $neighbour_coordinates, $expected_result) {
        // Arrange
        $sut = $this->create_first_stage($tile_positions);
        $neighbour = StageTilePosition::create_from_coordinates($neighbour_coordinates);

        // Act
        $are_empty_spaces_inevitable = $sut->are_empty_spaces_inevitable_for_neighbour($neighbour);

        // Assert
        $this->assertEquals($expected_result, $are_empty_spaces_inevitable);
    }
    static public function are_empty_spaces_inevitable_for_neighbour(): array {
        return [
            [[[10,10], [12,10], [14,10], [16,10],
            [10,12],                 [16,12],
            [10,14],                 [16,14],
            [10,16], [12,16], [14,16], [16,16]], [10, 8], false],
            [[[10,16], [12,16], [14,16], [16,16]], [10, 16], false],
            [[[10,10], [18,16]], [20, 16], false],
        ];
    }

    protected function create_first_stage($tile_positions) {
        return FirstStageTilePositions::create_and_fill($this->convert_into_Tiles($tile_positions));
    }

    protected function convert_into_DominoHorizontalVerticals($dominoe_coordinates): array {
        $dominoes = [];
        foreach($dominoe_coordinates as $dominoe_coordinate) {
            $dominoes[] = DominoHorizontalVertical::create_from_coordinates($dominoe_coordinate[0], $dominoe_coordinate[1]);
        }
        return $dominoes;
    }
    protected function convert_into_Tiles($coordinates): array {
        $tile_positions = [];
        foreach($coordinates as $coordinate) {
            $tile_positions[] = StageTilePosition::create($coordinate[0], $coordinate[1]);
        }
        return $tile_positions;
    }
    protected function convert_into_Tile($coordinates): array {
        return ['horizontal' => $coordinates[0], 'vertical' => $coordinates[1]];
    }
}
?>
