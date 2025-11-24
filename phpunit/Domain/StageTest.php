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

    public function test_create_stage_domino() {
        // Arrange
        $horizontal1 = 5;
        $vertical1 = 7;
        $horizontal2 = 5;
        $vertical2 = 9;

        // Act
        $sut = StageDomino::create_from_coordinates([$horizontal1, $vertical1], [$horizontal2, $vertical2]);

        // Assert
        $this->assertEquals($horizontal1, $sut[0]['horizontal']);
        $this->assertEquals($vertical1, $sut[0]['vertical']);
        $this->assertEquals($horizontal2, $sut[1]['horizontal']);
        $this->assertEquals($vertical2, $sut[1]['vertical']);
    }

    public function test_create_horizontal_dominoes() {
        // Arrange
        $stage = 3;
        $horizontal1 = 3;
        $vertical1 = 7;
        $horizontal2 = 5;
        $vertical2 = 7;
        $sut = StageDomino::create_from_coordinates([$horizontal1, $vertical1], [$horizontal2, $vertical2]);

        // Act
        $dominoes = $sut->create_horizontal_dominoes($stage);

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
        $sut = StageDomino::create_from_coordinates([$horizontal1, $vertical1], [$horizontal2, $vertical2]);

        // Act
        $dominoes = $sut->create_vertical_dominoes($stage);

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

    #[\PHPUnit\Framework\Attributes\DataProvider('is_position_occupied')]
    public function test_is_position_occupied($tile_positions, $position, $expected_result) {
        // Arrange
        $this->sut = StageTilePositions::create($this->convert_into_Tiles($tile_positions));

        // Act
        $is_position_occupied =$this->sut->is_position_occupied($this->convert_into_Tile($position));

        // Assert
        $this->assertEquals($expected_result, $is_position_occupied);
    }
    static public function is_position_occupied(): array {
        return [
            [[], [10, 10], false],
            [[[10,10]], [10, 10], true],
            [[[5,5], [10,10]], [7, 7], false],
            [[[5,5], [10,10]], [5, 5], true],
        ];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('higher_border')]
    public function test_higher_border($stage, $occupied_positions, $unoccupied_positions) {
        // Arrange

        // Act
        $sut = $this->create_higher_stage($stage, [], StageTest::FIRST_STAGE_BOUNDING_BOX);

        // Assert
        foreach($this->convert_into_Tiles($unoccupied_positions) as $position)
            $this->assertFalse($sut->is_position_occupied($position));
        foreach($this->convert_into_Tiles($occupied_positions) as $position)
            $this->assertTrue($sut->is_position_occupied($position));
    }
    static public function higher_border(): array {
        return [
            [4, [[11,11], [13,11], [15,11], [17,11], [11,13], [11,15], [17,13], [17,15], [13,15], [15,15]], [StageTest::STAGE_4_LEFT, StageTest::STAGE_4_RIGHT]],
            [2, [[9,9], [13,9], [15,9], [17,9], [9,13], [19,17], [9, 17]], [[13,13], [15,13], [11,11], [17,11], [11,15], [17,15]]],
        ];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('higher_empty_spaces')]
    public function test_higher_empty_spaces($stage, $tile_positions, $candidate_domino, $expected_result) {
        // Arrange
        $sut = $this->create_higher_stage($stage, $tile_positions, StageTest::FIRST_STAGE_BOUNDING_BOX);

        // Act
        $are_empty_spaces_inevitable = $sut->are_empty_spaces_inevitable($this->convert_into_Tiles($candidate_domino));

        // Assert
        $this->assertEquals($expected_result, $are_empty_spaces_inevitable);
    }
    static public function higher_empty_spaces(): array {
        return [
            [4, [], [StageTest::STAGE_4_LEFT, StageTest::STAGE_4_RIGHT], false],
            [3, [], StageTest::STAGE_3_TOP_LEFT_DOMINO, false],
            [3, StageTest::STAGE_3_BOTTOM_RIGHT_DOMINO, StageTest::STAGE_3_TOP_LEFT_DOMINO, true],
        ];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('higher_create_candidate_dominoes')]
    public function test_higher_create_candidate_dominoes($stage, $expected_horizontal_candidates, $expected_vertical_candidates) {
        // Arrange
        $sut = $this->create_higher_stage($stage, [], StageTest::FIRST_STAGE_BOUNDING_BOX);

        // Act
        $horizontal_candidate_dominoes = $sut->create_horizontal_candidate_dominoes();
        $vertical_candidate_dominoes = $sut->create_vertical_candidate_dominoes();

        // Assert
        $this->assertEquals($this->convert_into_StageDominos($expected_horizontal_candidates), $horizontal_candidate_dominoes);
        $this->assertEquals($this->convert_into_StageDominos($expected_vertical_candidates), $vertical_candidate_dominoes);
    }
    static public function higher_create_candidate_dominoes(): array {
        return [
            [4, [[StageTest::STAGE_4_LEFT, StageTest::STAGE_4_RIGHT]], []],
            [3, [StageTest::STAGE_3_TOP_LEFT_DOMINO, [[14, 12], [16, 12]],
             [[12, 14], [14, 14]], StageTest::STAGE_3_BOTTOM_RIGHT_DOMINO], 
             [[[12,12], [12,14]], [[14,12], [14,14]], [[16,12], [16,14]]]
        ],
        ];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('higher_get_free_contiguous_area')]
    public function test_higher_get_free_contiguous_area($stage, $tile_positions, $first_free_position, $size_expected_result) {
        // Arrange
        $sut = $this->create_higher_stage($stage, $tile_positions, StageTest::FIRST_STAGE_BOUNDING_BOX);

        // Act
        $free_contiguous_area = $sut->get_free_contiguous_area($this->convert_into_Tile($first_free_position));

        // Assert
        $this->assertEquals($size_expected_result, count($free_contiguous_area));
    }
    static public function higher_get_free_contiguous_area(): array {
        return [
            [4, [StageTest::STAGE_4_LEFT], StageTest::STAGE_4_LEFT, 0],
            [4, [], StageTest::STAGE_4_LEFT, 2],
            [4, [StageTest::STAGE_4_LEFT], StageTest::STAGE_4_RIGHT, 1],
        ];
    }
    protected function create_higher_stage($stage, $tile_positions, $bounding_box_first_stage) {
        return HigherStageTilePositions::create_and_fill($stage, $this->convert_into_Tiles($tile_positions), $bounding_box_first_stage);
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('first_stage_border')]
    public function test_first_stage_border($tile_positions, $occupied_positions, $unoccupied_positions) {
        // Arrange

        // Act
        $sut = $this->create_first_stage($tile_positions);

        // Assert
        foreach($this->convert_into_Tiles($unoccupied_positions) as $position)
            $this->assertFalse($sut->is_position_occupied($position));
        foreach($this->convert_into_Tiles($occupied_positions) as $position)
            $this->assertTrue($sut->is_position_occupied($position));
    }
    static public function first_stage_border(): array {
        return [
            [
                [StageTest::STAGE_1_TOP_LEFT, StageTest::STAGE_1_BOTTOM_RIGHT_44],
                [[10, 10],],
                [[8, 10], [10, 8]],
            ],
        ];
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
            [StageTest::STAGE_3_BOTTOM_RIGHT_DOMINO, StageTest::STAGE_3_TOP_LEFT_DOMINO, false],
            [[[10,10], [16, 16]], [[16,18], [14,18]], false], // opposite 4x4 corners
            [[[10,10], [16, 16]], [[12,18], [14,18]], true], // opposite 4x4 corners
            [[[10,16], [12, 16], [14, 16], [16, 16], [14, 14], [16, 14], [12, 12], [14, 12]], [[14,10], [16,10]], true],
            [[[10,16], [12, 16], [14, 16], [16, 16], [14, 14], [16, 14], [12, 12], [14, 12]], [[14,10], [12,10]], false],
        ];
    }

    protected function create_first_stage($tile_positions) {
        return FirstStageTilePositions::create_and_fill($this->convert_into_Tiles($tile_positions));
    }
    static public function first_stage_bounding_box(): array {
        return [
            [
                [StageTest::STAGE_1_TOP_LEFT, StageTest::STAGE_1_BOTTOM_RIGHT_44],
                [10, 10, 16, 16],
            ],
        ];
    }

    protected function convert_into_StageDominos($dominoe_coordinates): array {
        $dominoes = [];
        foreach($dominoe_coordinates as $dominoe_coordinate) {
            $dominoes[] = StageDomino::create_from_coordinates($dominoe_coordinate[0], $dominoe_coordinate[1]);
        }
        return $dominoes;
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
