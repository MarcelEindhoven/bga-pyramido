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

class StageTest extends TestCase{
    /** 5x4 tiles, Last coordinates are the coordinates of the bottom right tile */
    const FIRST_STAGE_BOUNDING_BOX = [10, 10, 18, 16];
    const STAGE_1_TOP_LEFT = [10,10];
    const STAGE_1_BOTTOM_RIGHT_44 = [16,16];
    const STAGE_1_BOTTOM_RIGHT_54 = [18,16];
    const STAGE_4_LEFT = [13,13];
    const STAGE_4_RIGHT = [15,13];
    const STAGE_3_TOP_LEFT_DOMINO = [[12,12], [14,12]];
    const STAGE_3_BOTTOM_RIGHT_DOMINO = [[14,14], [16,14]];

    protected ?StageTilePositions $sut = null;

    protected function setUp(): void {
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('create_candidate_dominoes')]
    public function test_create_candidate_dominoes($stage, $expected_horizontal_candidates, $expected_vertical_candidates) {
        // Arrange
        $expected_candidates = array_merge($expected_horizontal_candidates, $expected_vertical_candidates);
        $sut = $this->create_higher_stage($stage, [], StageTest::FIRST_STAGE_BOUNDING_BOX);

        // Act
        $candidate_dominoes = $sut->create_candidate_dominoes();

        // Assert
        $this->assertEquals($this->convert_into_DominoHorizontalVerticals($expected_candidates), $candidate_dominoes);
    }
    static public function create_candidate_dominoes(): array {
        return [
            [4, [[StageTest::STAGE_4_LEFT, StageTest::STAGE_4_RIGHT]], []],
            [3, [StageTest::STAGE_3_TOP_LEFT_DOMINO, [[14, 12], [16, 12]],
             [[12, 14], [14, 14]], StageTest::STAGE_3_BOTTOM_RIGHT_DOMINO], 
             [[[12,12], [12,14]], [[14,12], [14,14]], [[16,12], [16,14]]]
        ],
        ];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('get_free_contiguous_area')]
    public function test_get_free_contiguous_area($stage, $tile_positions, $first_free_position, $size_expected_result) {
        // Arrange
        $sut = $this->create_higher_stage($stage, $tile_positions, StageTest::FIRST_STAGE_BOUNDING_BOX);

        // Act
        $free_contiguous_area = $sut->get_free_contiguous_area($this->convert_into_Tile($first_free_position));

        // Assert
        $this->assertEquals($size_expected_result, count($free_contiguous_area));
    }
    static public function get_free_contiguous_area(): array {
        return [
            [4, [StageTest::STAGE_4_LEFT], StageTest::STAGE_4_LEFT, 0],
            [4, [], StageTest::STAGE_4_LEFT, 2],
            [4, [StageTest::STAGE_4_LEFT], StageTest::STAGE_4_RIGHT, 1],
        ];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('is_valid')]
    public function test_is_valid($stage, $tile_positions, $expected_result) {
        // Arrange
        $sut = $this->create_higher_stage($stage, $tile_positions, StageTest::FIRST_STAGE_BOUNDING_BOX);

        // Act
        $result = $sut->is_valid();

        // Assert
        $this->assertEquals($expected_result, $result);
    }
    static public function is_valid(): array {
        return [
            [4, [StageTest::STAGE_4_LEFT, StageTest::STAGE_4_RIGHT], true],
        ];
    }
    protected function create_higher_stage($stage, $tile_positions, $bounding_box_first_stage) {
        return BoundedStageTilePositions::create_and_fill($stage, $this->convert_into_Tiles($tile_positions), $bounding_box_first_stage);
    }

    static public function first_stage_bounding_box(): array {
        return [
            [
                [StageTest::STAGE_1_TOP_LEFT, StageTest::STAGE_1_BOTTOM_RIGHT_44],
                [10, 10, 16, 16],
            ],
        ];
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
