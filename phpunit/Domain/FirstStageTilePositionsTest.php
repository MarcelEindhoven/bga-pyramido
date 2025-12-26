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

    #[\PHPUnit\Framework\Attributes\DataProvider('is_valid')]
    public function test_is_valid($tile_positions, $expected_is_valid) {
        // Arrange
        $sut = $this->create_first_stage($tile_positions);

        // Act
        $is_valid = $sut->is_valid();
        print_r($sut->get_bounding_box());

        // Assert
        $this->assertEquals($expected_is_valid, $is_valid);
    }
    static public function is_valid(): array {
        return [
            [[FirstStageTilePositionsTest::STAGE_1_TOP_LEFT, FirstStageTilePositionsTest::STAGE_1_BOTTOM_RIGHT_54],
                true,],
            [[FirstStageTilePositionsTest::STAGE_1_TOP_LEFT, [18,18]],
                false,],
            [[FirstStageTilePositionsTest::STAGE_1_TOP_LEFT, [10,20]],
                false,],
            [[FirstStageTilePositionsTest::STAGE_1_TOP_LEFT, [20,10]],
                false,],
            [[
                                                                     [14, 8], [16,8],
             FirstStageTilePositionsTest::STAGE_1_TOP_LEFT, [12,10], [14,10], [16,10], [18,10],
                                                                                       [18,12],],
                true,],
        ];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('bounding_box')]
    public function test_bounding_box($tile_positions, $expected_bounding_box) {
        // Arrange
        $sut = $this->create_first_stage($tile_positions);

        // Act
        $bounding_box = $sut->get_bounding_box();

        // Assert
        $this->assertEquals($expected_bounding_box, $bounding_box);
    }
    static public function bounding_box(): array {
        return [
            [
                [FirstStageTilePositionsTest::STAGE_1_TOP_LEFT, FirstStageTilePositionsTest::STAGE_1_BOTTOM_RIGHT_44],
                [10, 10, 16, 16],
            ],
        ];
    }

    public function test_dominoes_no_tile() {
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

    public function test_get_candidate_dominoes_no_tile() {
        // Arrange
        $sut = $this->create_first_stage([]);

        // Act
        $dominoes = $sut->get_candidate_dominoes();

        // Assert
        $this->assertEquals(4, count($dominoes));
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('create_touching_dominoes')]
    public function test_create_touching_dominoes(array $tile_positions, array $tile, array $expected_dominoes) {
        // Arrange
        $sut = $this->create_first_stage($tile_positions);

        // Act
        $dominoes = $sut->create_touching_dominoes($this->convert_into_tile($tile));

        // Assert
        $this->assertEqualsCanonicalizing($this->convert_into_DominoHorizontalVerticals($expected_dominoes), $dominoes);
    }
    static public function create_touching_dominoes(): array {
        return[
            [
                [[10, 10], [12, 10], [14, 10], [16, 10]],
                [10, 10],
                [[[8,8], [10,8]], [[10,8], [12,8]],
                 [[6, 10], [8, 10]], [[12, 10], [14, 10]],
                 [[8,12], [10,12]], [[10,12], [12,12]], 
                 [[10,6], [10,8]], [[10,12], [10,14]],
                 [[8,8], [8,10]], [[8,10], [8,12]], [[12,8], [12,10]], [[12,10], [12,12]]],
                [                       [14,8], [16,8],
                    [10, 10], [12, 10], [14, 10], [16, 10]],
                [10, 10],
                [[[8,8], [10,8]], [[10,8], [12,8]],
                 [[12, 10], [14, 10]],
                 [[8,12], [10,12]], [[10,12], [12,12]], 
                 [[10,6], [10,8]], [[10,12], [10,14]],
                 [[8,8], [8,10]], [[8,10], [8,12]], [[12,8], [12,10]], [[12,10], [12,12]]],
            ]
        ];
    }

    public function test_create_multiple_candidate_dominoes() {
        // Arrange
        $sut = $this->create_first_stage([
                                                                     [14, 8], [16,8],
            FirstStageTilePositionsTest::STAGE_1_TOP_LEFT, [12, 10], [14, 10], [16, 10],
        ]);

        // Act
        $dominoes = $sut->create_multiple_candidate_dominoes();

        // Assert
        $this->assertEquals(23, count($dominoes));
    }

    public function test_get_candidate_dominoes_twin_tile() {
        // Arrange
        $sut = $this->create_first_stage([FirstStageTilePositionsTest::STAGE_1_TOP_LEFT, [12, 10]]);

        // Act
        $dominoes = $sut->get_candidate_dominoes();

        // Assert
        $this->assertEquals(4 * 2 * 2 * 2, count($dominoes));
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

    protected function create_first_stage($tile_positions) {
        return FirstStageTilePositions::create_and_fill($this->convert_into_tiles($tile_positions));
    }

    protected function convert_into_DominoHorizontalVerticals($dominoe_coordinates): array {
        $dominoes = [];
        foreach($dominoe_coordinates as $dominoe_coordinate) {
            $dominoes[] = DominoHorizontalVertical::create_from_coordinates($dominoe_coordinate[0], $dominoe_coordinate[1]);
        }
        return $dominoes;
    }
    protected function convert_into_tiles($coordinates): array {
        $tile_positions = [];
        foreach($coordinates as $coordinate) {
            $tile_positions[] = StageTilePosition::create($coordinate[0], $coordinate[1]);
        }
        return $tile_positions;
    }
    protected function convert_into_tile($coordinates): array {
        return ['horizontal' => $coordinates[0], 'vertical' => $coordinates[1]];
    }
}
?>
